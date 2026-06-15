<?php
/**
 * Lead Finder — free "map scraper" for the team work area.
 *
 * Finds local businesses that DON'T have a website (ideal DPowered prospects)
 * using only free, no-key, open data:
 *   • Nominatim  — geocode the search area (place name / town / postcode) → lat,lon
 *   • Overpass   — query OpenStreetMap for businesses near that point, filtered
 *                  server-side to those with no `website` / `contact:website` tag.
 *
 * Results can be ticked and imported straight into the Leads list as new leads
 * (source = "Map finder"). All requests run server-side so no API key, billing
 * account or browser CORS is involved — it's genuinely free.
 *
 * Trade-off vs Google: OSM coverage is thinner, and "no website tag" can mean
 * "nobody mapped it" rather than "definitely no site" — so treat hits as
 * prospects to verify, not gospel.
 */

if (!defined('ABSPATH')) exit;

// ── Categories → OpenStreetMap tag filters ───────────────────────────────────
// Each category maps to one or more [key, value] pairs. An empty value means
// "key exists with any value" (e.g. any shop).

function dpowered_finder_categories() {
    return [
        'restaurant'  => ['label' => 'Restaurants',          'tags' => [['amenity', 'restaurant']]],
        'cafe'        => ['label' => 'Cafés',                'tags' => [['amenity', 'cafe']]],
        'takeaway'    => ['label' => 'Takeaways / fast food','tags' => [['amenity', 'fast_food']]],
        'pub_bar'     => ['label' => 'Pubs & bars',          'tags' => [['amenity', 'pub'], ['amenity', 'bar']]],
        'hairdresser' => ['label' => 'Hairdressers / barbers','tags' => [['shop', 'hairdresser']]],
        'beauty'      => ['label' => 'Beauty & nail salons', 'tags' => [['shop', 'beauty'], ['shop', 'nails']]],
        'car_repair'  => ['label' => 'Garages / car repair', 'tags' => [['shop', 'car_repair']]],
        'plumber'     => ['label' => 'Plumbers',             'tags' => [['craft', 'plumber']]],
        'electrician' => ['label' => 'Electricians',         'tags' => [['craft', 'electrician']]],
        'builder'     => ['label' => 'Builders / joiners',   'tags' => [['craft', 'builder'], ['craft', 'carpenter']]],
        'dentist'     => ['label' => 'Dentists',             'tags' => [['amenity', 'dentist'], ['healthcare', 'dentist']]],
        'gym'         => ['label' => 'Gyms / fitness',       'tags' => [['leisure', 'fitness_centre']]],
        'florist'     => ['label' => 'Florists',             'tags' => [['shop', 'florist']]],
        'butcher'     => ['label' => 'Butchers',             'tags' => [['shop', 'butcher']]],
        'shop'        => ['label' => 'Any shop / retail',    'tags' => [['shop', '']]],
    ];
}

/** Allowed search radii (label => metres). */
function dpowered_finder_radii() {
    return ['1' => 1000, '2' => 2000, '3' => 3000, '5' => 5000, '10' => 10000];
}

// ── Helpers ──────────────────────────────────────────────────────────────────

/** Geocode a free-text area to [lat, lon, label] using Nominatim. */
function dpowered_finder_geocode($query) {
    $url = add_query_arg([
        'q'            => $query,
        'format'       => 'json',
        'limit'        => 1,
        'countrycodes' => 'gb', // DPowered is UK-focused; improves accuracy for towns/postcodes
        'addressdetails' => 0,
    ], 'https://nominatim.openstreetmap.org/search');

    $res = wp_remote_get($url, [
        'timeout' => 15,
        'headers' => ['User-Agent' => 'DPoweredWorkArea/1.0 (joshyplunkett@gmail.com)'],
    ]);
    if (is_wp_error($res)) return null;

    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (empty($body) || !isset($body[0]['lat'], $body[0]['lon'])) return null;

    return [
        'lat'   => (float) $body[0]['lat'],
        'lon'   => (float) $body[0]['lon'],
        'label' => (string) ($body[0]['display_name'] ?? $query),
    ];
}

/** Build an Overpass QL query for a set of tag pairs around a point. */
function dpowered_finder_overpass_ql($tags, $lat, $lon, $radius_m) {
    $around = sprintf('(around:%d,%.6f,%.6f)', (int) $radius_m, $lat, $lon);
    // Exclude anything that already advertises a website — that's the whole point.
    $no_site = '[!"website"][!"contact:website"][!"url"]';
    $lines = '';
    foreach ($tags as $pair) {
        list($k, $v) = $pair;
        $sel = $v === '' ? '["' . $k . '"]' : '["' . $k . '"="' . $v . '"]';
        foreach (['node', 'way'] as $type) {
            $lines .= "  {$type}{$sel}{$no_site}{$around};\n";
        }
    }
    return "[out:json][timeout:25];\n(\n{$lines});\nout center tags 250;";
}

/** Compose a single-line address from OSM addr:* tags. */
function dpowered_finder_address($t) {
    $parts = [];
    $line1 = trim(($t['addr:housenumber'] ?? '') . ' ' . ($t['addr:street'] ?? ''));
    if ($line1) $parts[] = $line1;
    if (!empty($t['addr:city']))     $parts[] = $t['addr:city'];
    if (!empty($t['addr:postcode'])) $parts[] = $t['addr:postcode'];
    return implode(', ', $parts);
}

// ── AJAX: search ─────────────────────────────────────────────────────────────

function dpowered_ajax_finder_search() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    // Be a good citizen to the free Nominatim/Overpass services: light throttle.
    $uid = get_current_user_id();
    if (get_transient('dpowered_finder_wait_' . $uid)) {
        wp_send_json_error(['msg' => 'Give it a couple of seconds between searches.'], 429);
    }
    set_transient('dpowered_finder_wait_' . $uid, 1, 3);

    $area     = sanitize_text_field(wp_unslash($_POST['area'] ?? ''));
    $cat_key  = sanitize_key($_POST['category'] ?? '');
    $radius_k = sanitize_text_field(wp_unslash($_POST['radius'] ?? '3'));

    if ($area === '') wp_send_json_error(['msg' => 'Enter a town, area or postcode.'], 400);

    $cats = dpowered_finder_categories();
    if (!isset($cats[$cat_key])) wp_send_json_error(['msg' => 'Pick a business type.'], 400);

    $radii    = dpowered_finder_radii();
    $radius_m = $radii[$radius_k] ?? 3000;

    $geo = dpowered_finder_geocode($area);
    if (!$geo) wp_send_json_error(['msg' => "Couldn't find that area. Try a town name or full postcode."], 404);

    $ql  = dpowered_finder_overpass_ql($cats[$cat_key]['tags'], $geo['lat'], $geo['lon'], $radius_m);
    $res = wp_remote_post('https://overpass-api.de/api/interpreter', [
        'timeout' => 35,
        'headers' => ['User-Agent' => 'DPoweredWorkArea/1.0 (joshyplunkett@gmail.com)'],
        'body'    => ['data' => $ql],
    ]);
    if (is_wp_error($res)) {
        wp_send_json_error(['msg' => 'Map service is busy — try again in a moment.'], 502);
    }
    $data = json_decode(wp_remote_retrieve_body($res), true);
    if (!isset($data['elements'])) {
        wp_send_json_error(['msg' => 'Map service returned nothing — try again in a moment.'], 502);
    }

    // Existing lead titles (lowercased) so we can flag already-known businesses.
    $existing = [];
    foreach (get_posts(['post_type' => 'lead', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']) as $lid) {
        $existing[mb_strtolower(trim(get_the_title($lid)))] = true;
    }

    $items = [];
    $seen  = [];
    foreach ($data['elements'] as $el) {
        $t    = $el['tags'] ?? [];
        $name = trim($t['name'] ?? '');
        if ($name === '') continue;

        // Belt-and-braces: drop anything that slipped through with a site.
        if (!empty($t['website']) || !empty($t['contact:website']) || !empty($t['url'])) continue;

        $key = mb_strtolower($name);
        if (isset($seen[$key])) continue;
        $seen[$key] = true;

        $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
        $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);

        $items[] = [
            'name'     => $name,
            'phone'    => trim($t['phone'] ?? ($t['contact:phone'] ?? '')),
            'address'  => dpowered_finder_address($t),
            'category' => $cats[$cat_key]['label'],
            'maps'     => ($lat && $lon) ? "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lon}#map=18/{$lat}/{$lon}" : '',
            'known'    => isset($existing[$key]),
        ];
        if (count($items) >= 250) break;
    }

    // Businesses with a phone number first — they're the actionable ones.
    usort($items, function ($a, $b) {
        $pa = $a['phone'] !== '' ? 0 : 1;
        $pb = $b['phone'] !== '' ? 0 : 1;
        if ($pa !== $pb) return $pa - $pb;
        return strcasecmp($a['name'], $b['name']);
    });

    wp_send_json_success([
        'area'    => $geo['label'],
        'count'   => count($items),
        'results' => $items,
    ]);
}
add_action('wp_ajax_dpowered_finder_search', 'dpowered_ajax_finder_search');

// ── AJAX: import selected results as leads ───────────────────────────────────

function dpowered_ajax_finder_import() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $raw   = wp_unslash($_POST['items'] ?? '');
    $items = json_decode($raw, true);
    if (!is_array($items) || !$items) wp_send_json_error(['msg' => 'Nothing selected to import.'], 400);

    // Existing titles for dedupe.
    $existing = [];
    foreach (get_posts(['post_type' => 'lead', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids']) as $lid) {
        $existing[mb_strtolower(trim(get_the_title($lid)))] = true;
    }

    $uid     = get_current_user_id();
    $added   = 0;
    $skipped = 0;

    foreach ($items as $it) {
        $name = sanitize_text_field($it['name'] ?? '');
        if ($name === '') { $skipped++; continue; }
        $key = mb_strtolower(trim($name));
        if (isset($existing[$key])) { $skipped++; continue; }

        $address  = sanitize_text_field($it['address'] ?? '');
        $category = sanitize_text_field($it['category'] ?? '');
        $note     = 'No website found via Map Finder.';
        if ($category) $note .= ' Type: ' . $category . '.';
        if ($address)  $note .= ' Address: ' . $address . '.';

        $id = dpowered_insert_lead([
            'business' => $name,
            'phone'    => sanitize_text_field($it['phone'] ?? ''),
            'source'   => 'finder',
            'assigned' => $uid,
            'notes'    => $note,
            'date'     => current_time('Y-m-d'),
        ]);
        if (is_wp_error($id)) { $skipped++; continue; }

        $existing[$key] = true;
        $added++;
    }

    wp_send_json_success(['added' => $added, 'skipped' => $skipped]);
}
add_action('wp_ajax_dpowered_finder_import', 'dpowered_ajax_finder_import');
