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
        // ── Group sweeps (query several types at once) ──
        'all_food'    => ['label' => '★ All food & drink',   'tags' => [['amenity', 'restaurant'], ['amenity', 'cafe'], ['amenity', 'fast_food'], ['amenity', 'pub'], ['amenity', 'bar']]],
        'all_trades'  => ['label' => '★ All trades',         'tags' => [['craft', 'plumber'], ['craft', 'electrician'], ['craft', 'builder'], ['craft', 'carpenter'], ['craft', 'painter'], ['craft', 'roofer']]],
        'all_beauty'  => ['label' => '★ All hair & beauty',  'tags' => [['shop', 'hairdresser'], ['shop', 'beauty'], ['shop', 'nails']]],
        // ── Single types ──
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

        $phone = trim($t['phone'] ?? ($t['contact:phone'] ?? ''));

        // Social presence (no website, since we've filtered those out) = a business
        // that's actively trying to be found online but has no site = hottest lead.
        $fb = trim($t['contact:facebook']  ?? ($t['facebook']  ?? ''));
        $ig = trim($t['contact:instagram'] ?? ($t['instagram'] ?? ''));
        if ($fb !== '') {
            $social_url = preg_match('#^https?://#i', $fb) ? $fb : 'https://facebook.com/' . ltrim($fb, '/');
            $social_net = 'Facebook';
        } elseif ($ig !== '') {
            $social_url = preg_match('#^https?://#i', $ig) ? $ig : 'https://instagram.com/' . ltrim($ig, '/');
            $social_net = 'Instagram';
        } else {
            $social_url = '';
            $social_net = '';
        }
        $has_social = $social_url !== '';

        // Lead score: social-but-no-website is the strongest signal, phone is actionable.
        $signals = [];
        $score   = 0;
        if ($has_social)     { $score += 2; $signals[] = 'On ' . $social_net . ' · no website'; }
        if ($phone !== '')   { $score += 1; $signals[] = 'Has phone'; }

        $items[] = [
            'name'       => $name,
            'phone'      => $phone,
            'address'    => dpowered_finder_address($t),
            'category'   => $cats[$cat_key]['label'],
            'maps'       => ($lat && $lon) ? "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lon}#map=18/{$lat}/{$lon}" : '',
            'social'     => $social_url,
            'social_net' => $social_net,
            'hot'        => $has_social,
            'score'      => $score,
            'signals'    => $signals,
            'known'      => isset($existing[$key]),
        ];
        if (count($items) >= 250) break;
    }

    // Hottest first (social-no-site, then phone), then alphabetical.
    usort($items, function ($a, $b) {
        if ($a['score'] !== $b['score']) return $b['score'] - $a['score'];
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
        $social   = esc_url_raw($it['social'] ?? '');
        $note     = 'No website found via Map Finder.';
        if ($category) $note .= ' Type: ' . $category . '.';
        if ($address)  $note .= ' Address: ' . $address . '.';
        if ($social)   $note .= ' Active on social (no site): ' . $social;

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

// ── Saved searches (team "territories") ──────────────────────────────────────
// A reusable area+type+radius combo. Shared across the team like leads/meetings.

function dpowered_register_saved_searches() {
    register_post_type('dpowered_search', [
        'labels'          => ['name' => 'Saved Searches', 'singular_name' => 'Saved Search'],
        'public'          => false,
        'show_ui'         => false,
        'supports'        => ['title', 'author'],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'dpowered_register_saved_searches');

/** All saved searches (shared), newest first, with owner + decoded params. */
function dpowered_get_saved_searches() {
    $posts = get_posts([
        'post_type'      => 'dpowered_search',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    $out = [];
    foreach ($posts as $p) {
        $params = json_decode((string) get_post_meta($p->ID, '_search_params', true), true);
        $owner  = (int) $p->post_author;
        $u      = $owner ? get_userdata($owner) : null;
        $out[] = [
            'id'         => (int) $p->ID,
            'label'      => $p->post_title,
            'params'     => is_array($params) ? $params : [],
            'owner'      => $owner,
            'owner_name' => $u ? $u->display_name : '',
        ];
    }
    return $out;
}

function dpowered_ajax_save_search() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $area     = sanitize_text_field(wp_unslash($_POST['area'] ?? ''));
    $category = sanitize_key($_POST['category'] ?? '');
    $radius   = sanitize_text_field(wp_unslash($_POST['radius'] ?? '3'));
    $label    = sanitize_text_field(wp_unslash($_POST['label'] ?? ''));

    $cats = dpowered_finder_categories();
    if ($area === '' || !isset($cats[$category])) {
        wp_send_json_error(['msg' => 'Run a search first, then save it.'], 400);
    }
    if ($label === '') {
        $label = $cats[$category]['label'] . ' · ' . $area;
    }

    $id = wp_insert_post([
        'post_type'   => 'dpowered_search',
        'post_status' => 'publish',
        'post_title'  => $label,
        'post_author' => get_current_user_id(),
    ], true);
    if (is_wp_error($id)) wp_send_json_error(['msg' => 'Could not save.'], 500);

    update_post_meta($id, '_search_params', wp_json_encode([
        'source'   => 'map',
        'area'     => $area,
        'category' => $category,
        'radius'   => $radius,
    ]));

    wp_send_json_success(['searches' => dpowered_get_saved_searches()]);
}
add_action('wp_ajax_dpowered_save_search', 'dpowered_ajax_save_search');

function dpowered_ajax_delete_search() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $id = absint($_POST['search_id'] ?? 0);
    $p  = get_post($id);
    if (!$p || $p->post_type !== 'dpowered_search') wp_send_json_error(['msg' => 'Not found.'], 404);
    if ((int) $p->post_author !== get_current_user_id() && !current_user_can('manage_options')) {
        wp_send_json_error(['msg' => 'Only the owner can delete this.'], 403);
    }

    wp_delete_post($id, true);
    wp_send_json_success(['searches' => dpowered_get_saved_searches()]);
}
add_action('wp_ajax_dpowered_delete_search', 'dpowered_ajax_delete_search');
