<?php
/**
 * Leads / team work area.
 *
 * A lightweight in-house CRM. Sales-team users log in from the site and manage
 * leads on the front-end work-area page. Website contact-form submissions are
 * captured automatically as new leads. All data lives in this WP install.
 */

if (!defined('ABSPATH')) exit;

// ── Reference data ───────────────────────────────────────────────────────────

function dpowered_lead_statuses() {
    return [
        'new'        => 'New',
        'called'     => 'Called',
        'interested' => 'Interested',
        'quoted'     => 'Quoted',
        'won'        => 'Won',
        'dead'       => 'Dead',
    ];
}

function dpowered_lead_sources() {
    return [
        'website'   => 'Website',
        'cold-call' => 'Cold call',
        'referral'  => 'Referral',
        'other'     => 'Other',
    ];
}

// ── Access control ───────────────────────────────────────────────────────────

/** True if the given (or current) user may use the work area. */
function dpowered_user_can_leads($user_id = null) {
    $user = $user_id ? get_userdata($user_id) : wp_get_current_user();
    if (!$user || !$user->exists()) return false;
    return user_can($user, 'manage_options') || in_array('sales_team', (array) $user->roles, true);
}

function dpowered_register_sales_role() {
    if (!get_role('sales_team')) {
        add_role('sales_team', 'Sales Team', ['read' => true]);
    }
}
add_action('init', 'dpowered_register_sales_role');

/** Keep sales-only users out of wp-admin; send them to the work area instead. */
function dpowered_block_sales_admin() {
    if (wp_doing_ajax() || !is_user_logged_in()) return;
    $user = wp_get_current_user();
    if (in_array('sales_team', (array) $user->roles, true) && !user_can($user, 'manage_options')) {
        wp_safe_redirect(dpowered_work_area_url());
        exit;
    }
}
add_action('admin_init', 'dpowered_block_sales_admin');

function dpowered_sales_hide_admin_bar($show) {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('sales_team', (array) $user->roles, true) && !user_can($user, 'manage_options')) {
            return false;
        }
    }
    return $show;
}
add_filter('show_admin_bar', 'dpowered_sales_hide_admin_bar');

function dpowered_sales_login_redirect($redirect_to, $requested, $user) {
    if ($user instanceof WP_User && dpowered_user_can_leads($user->ID)) {
        return dpowered_work_area_url();
    }
    return $redirect_to;
}
add_filter('login_redirect', 'dpowered_sales_login_redirect', 10, 3);

// ── Work-area page ───────────────────────────────────────────────────────────

/** Find or create the work-area page and return its URL. */
function dpowered_work_area_url() {
    $id = (int) get_option('dpowered_work_area_page_id');
    if ($id && get_post_status($id) === 'publish') {
        return get_permalink($id);
    }
    $id = dpowered_ensure_work_area_page();
    return $id ? get_permalink($id) : home_url('/work-area');
}

function dpowered_ensure_work_area_page() {
    $existing = get_page_by_path('work-area');
    if ($existing) {
        update_post_meta($existing->ID, '_wp_page_template', 'page-work-area.php');
        update_option('dpowered_work_area_page_id', $existing->ID);
        return $existing->ID;
    }
    $id = wp_insert_post([
        'post_title'   => 'Team Work Area',
        'post_name'    => 'work-area',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
        'meta_input'   => ['_wp_page_template' => 'page-work-area.php'],
    ]);
    if ($id && !is_wp_error($id)) {
        update_option('dpowered_work_area_page_id', $id);
        return $id;
    }
    return 0;
}
add_action('after_switch_theme', 'dpowered_ensure_work_area_page');

// ── Lead custom post type ────────────────────────────────────────────────────

function dpowered_register_leads() {
    register_post_type('lead', [
        'labels' => [
            'name'               => 'Leads',
            'singular_name'      => 'Lead',
            'add_new'            => 'Add New Lead',
            'add_new_item'       => 'Add New Lead',
            'edit_item'          => 'Edit Lead',
            'new_item'           => 'New Lead',
            'view_item'          => 'View Lead',
            'search_items'       => 'Search Leads',
            'not_found'          => 'No leads found',
            'not_found_in_trash' => 'No leads found in trash',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-phone',
        'menu_position'   => 26,
        'supports'        => ['title'],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'dpowered_register_leads');

// ── Workspace pages custom post type ─────────────────────────────────────────

function dpowered_register_workspace_pages() {
    register_post_type('dpowered_page', [
        'labels'          => ['name' => 'Workspace Pages', 'singular_name' => 'Workspace Page'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-welcome-write-blog',
        'menu_position'   => 27,
        'supports'        => ['title', 'editor', 'author'],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'dpowered_register_workspace_pages');

// ── Meetings custom post type ─────────────────────────────────────────────────

function dpowered_register_meetings() {
    register_post_type('dpowered_meeting', [
        'labels'          => ['name' => 'Meetings', 'singular_name' => 'Meeting'],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-calendar-alt',
        'menu_position'   => 28,
        'supports'        => ['title'],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'dpowered_register_meetings');

/** Normalised array of a meeting's data. */
function dpowered_get_meeting_data($post_id) {
    $assigned = (int) get_post_meta($post_id, '_meeting_assigned', true);
    $user     = $assigned ? get_userdata($assigned) : null;
    return [
        'id'            => (int) $post_id,
        'business'      => get_the_title($post_id),
        'contact'       => (string) get_post_meta($post_id, '_meeting_contact', true),
        'phone'         => (string) get_post_meta($post_id, '_meeting_phone',   true),
        'date'          => (string) get_post_meta($post_id, '_meeting_date',    true),
        'time'          => (string) get_post_meta($post_id, '_meeting_time',    true),
        'location'      => (string) get_post_meta($post_id, '_meeting_location', true),
        'notes'         => (string) get_post_meta($post_id, '_meeting_notes',   true),
        'status'        => get_post_meta($post_id, '_meeting_status', true) ?: 'upcoming',
        'assigned'      => $assigned,
        'assigned_name' => $user ? $user->display_name : '',
        'created'       => get_the_date('Y-m-d', $post_id),
    ];
}

/** Normalised data for a single workspace page. */
function dpowered_get_page_data($page_id) {
    $p = get_post($page_id);
    if (!$p) return null;
    $author  = (int) $p->post_author;
    $auser   = $author ? get_userdata($author) : null;
    $updated = (int) get_post_modified_time('U', true, $page_id);
    return [
        'id'            => (int) $page_id,
        'title'         => $p->post_title ?: 'Untitled',
        'content'       => $p->post_content,
        'icon'          => (string) get_post_meta($page_id, '_page_icon', true),
        'private'       => (bool)   get_post_meta($page_id, '_page_private', true),
        'author'        => $author,
        'author_name'   => $auser ? $auser->display_name : '',
        'author_avatar' => dpowered_user_avatar($author),
        'updated'       => $updated,
        'updated_human' => $updated ? human_time_diff($updated, time()) . ' ago' : '',
    ];
}

/** All workspace pages visible to a given user (filters other people's private pages). */
function dpowered_get_visible_pages($uid) {
    $is_admin = user_can($uid, 'manage_options');
    $posts    = get_posts([
        'post_type'      => 'dpowered_page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ]);
    $result = [];
    foreach ($posts as $p) {
        $private = (bool) get_post_meta($p->ID, '_page_private', true);
        if ($private && (int) $p->post_author !== $uid && !$is_admin) continue;
        $result[] = dpowered_get_page_data($p->ID);
    }
    return $result;
}

/** Normalised array of a lead's data for rendering / JSON. */
function dpowered_get_lead_data($post_id) {
    $assigned = (int) get_post_meta($post_id, '_lead_assigned', true);
    $assigned_user = $assigned ? get_userdata($assigned) : null;
    $edited_by   = (int) get_post_meta($post_id, '_lead_edited_by', true);
    $edited_at   = (int) get_post_meta($post_id, '_lead_edited_at', true);
    $edited_user = $edited_by ? get_userdata($edited_by) : null;
    return [
        'id'           => (int) $post_id,
        'business'     => get_the_title($post_id),
        'contact'      => (string) get_post_meta($post_id, '_lead_contact', true),
        'phone'        => (string) get_post_meta($post_id, '_lead_phone', true),
        'email'        => (string) get_post_meta($post_id, '_lead_email', true),
        'status'       => get_post_meta($post_id, '_lead_status', true) ?: 'new',
        'called'       => (bool) get_post_meta($post_id, '_lead_called', true),
        'offered'      => (bool) get_post_meta($post_id, '_lead_offered', true),
        'assigned'     => $assigned,
        'assigned_name'=> $assigned_user ? $assigned_user->display_name : '',
        'source'       => get_post_meta($post_id, '_lead_source', true) ?: 'cold-call',
        'date'         => get_post_meta($post_id, '_lead_date', true) ?: get_the_date('Y-m-d', $post_id),
        'callback'     => (string) get_post_meta($post_id, '_lead_callback', true),
        'private'      => (bool)   get_post_meta($post_id, '_lead_private', true),
        'notes'        => (string) get_post_meta($post_id, '_lead_notes', true),
        'created'      => get_the_date('Y-m-d', $post_id),
        'edited_by'    => $edited_by,
        'edited_by_name' => $edited_user ? $edited_user->display_name : '',
        'edited_at'    => $edited_at,
        'edited_human' => $edited_at ? human_time_diff($edited_at, current_time('timestamp')) . ' ago' : '',
    ];
}

/** Record who last touched a lead, and when. */
function dpowered_touch_lead($post_id, $uid = null) {
    $uid = $uid ?: get_current_user_id();
    update_post_meta($post_id, '_lead_edited_by', (int) $uid);
    update_post_meta($post_id, '_lead_edited_at', time());
}

/** Create a lead from an arbitrary set of fields. Returns post ID or WP_Error. */
function dpowered_insert_lead($args) {
    $a = wp_parse_args($args, [
        'business'     => '',
        'contact'      => '',
        'phone'        => '',
        'email'        => '',
        'status'       => 'new',
        'source'       => 'cold-call',
        'assigned'     => 0,
        'notes'        => '',
        'callback'     => '',
        'private'      => 0,
        'date'         => '',
    ]);

    $statuses = dpowered_lead_statuses();
    $sources  = dpowered_lead_sources();
    $status   = array_key_exists($a['status'], $statuses) ? $a['status'] : 'new';
    $source   = array_key_exists($a['source'], $sources) ? $a['source'] : 'cold-call';
    $assigned = (int) $a['assigned'];
    if ($assigned && !dpowered_user_can_leads($assigned)) $assigned = 0;

    $title = $a['business'] !== '' ? $a['business'] : ($a['contact'] !== '' ? $a['contact'] : 'New lead');

    $id = wp_insert_post([
        'post_type'   => 'lead',
        'post_status' => 'publish',
        'post_title'  => $title,
    ], true);
    if (is_wp_error($id)) return $id;

    update_post_meta($id, '_lead_contact', $a['contact']);
    update_post_meta($id, '_lead_phone', $a['phone']);
    update_post_meta($id, '_lead_email', $a['email']);
    update_post_meta($id, '_lead_status', $status);
    update_post_meta($id, '_lead_called', $status !== 'new' ? 1 : 0);
    update_post_meta($id, '_lead_offered', 0);
    update_post_meta($id, '_lead_assigned', $assigned);
    update_post_meta($id, '_lead_source', $source);
    update_post_meta($id, '_lead_notes', $a['notes']);

    $date     = (is_string($a['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $a['date'])) ? $a['date'] : current_time('Y-m-d');
    $callback = (is_string($a['callback']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $a['callback'])) ? $a['callback'] : '';
    update_post_meta($id, '_lead_date', $date);
    update_post_meta($id, '_lead_callback', $callback);
    update_post_meta($id, '_lead_private', (int) (bool) $a['private']);
    dpowered_touch_lead($id, get_current_user_id());

    return $id;
}

/** Users who can be assigned leads. */
function dpowered_lead_team_members() {
    return get_users([
        'role__in' => ['sales_team', 'administrator'],
        'orderby'  => 'display_name',
        'order'    => 'ASC',
    ]);
}

// ── Admin overview columns (so Josh can oversee in wp-admin) ──────────────────

function dpowered_lead_admin_columns($columns) {
    $new = [];
    $new['cb']            = $columns['cb'] ?? '';
    $new['title']         = 'Business';
    $new['lead_contact']  = 'Contact';
    $new['lead_phone']    = 'Phone';
    $new['lead_status']   = 'Status';
    $new['lead_assigned'] = 'Assigned';
    $new['lead_source']   = 'Source';
    $new['date']          = $columns['date'] ?? 'Date';
    return $new;
}
add_filter('manage_lead_posts_columns', 'dpowered_lead_admin_columns');

function dpowered_lead_admin_column_content($column, $post_id) {
    $statuses = dpowered_lead_statuses();
    $sources  = dpowered_lead_sources();
    switch ($column) {
        case 'lead_contact':
            echo esc_html(get_post_meta($post_id, '_lead_contact', true) ?: '—');
            break;
        case 'lead_phone':
            echo esc_html(get_post_meta($post_id, '_lead_phone', true) ?: '—');
            break;
        case 'lead_status':
            $s = get_post_meta($post_id, '_lead_status', true) ?: 'new';
            echo esc_html($statuses[$s] ?? $s);
            break;
        case 'lead_assigned':
            $uid = (int) get_post_meta($post_id, '_lead_assigned', true);
            $u   = $uid ? get_userdata($uid) : null;
            echo esc_html($u ? $u->display_name : 'Unassigned');
            break;
        case 'lead_source':
            $src = get_post_meta($post_id, '_lead_source', true) ?: 'cold-call';
            echo esc_html($sources[$src] ?? $src);
            break;
    }
}
add_action('manage_lead_posts_custom_column', 'dpowered_lead_admin_column_content', 10, 2);

// ── On-site team login ───────────────────────────────────────────────────────

function dpowered_handle_team_login() {
    if (!isset($_POST['dpowered_login_submit'])) return;

    $url = dpowered_work_area_url();

    if (!isset($_POST['dpowered_login_nonce']) || !wp_verify_nonce($_POST['dpowered_login_nonce'], 'dpowered_team_login')) {
        wp_safe_redirect(add_query_arg('login_error', 'security', $url));
        exit;
    }

    $user = wp_signon([
        'user_login'    => sanitize_text_field(wp_unslash($_POST['log'] ?? '')),
        'user_password' => (string) ($_POST['pwd'] ?? ''),
        'remember'      => !empty($_POST['rememberme']),
    ], is_ssl());

    if (is_wp_error($user)) {
        wp_safe_redirect(add_query_arg('login_error', '1', $url));
        exit;
    }

    if (!dpowered_user_can_leads($user->ID)) {
        wp_logout();
        wp_safe_redirect(add_query_arg('login_error', 'noaccess', $url));
        exit;
    }

    wp_safe_redirect($url);
    exit;
}
add_action('template_redirect', 'dpowered_handle_team_login');

// ── AJAX: update a single field ──────────────────────────────────────────────

function dpowered_ajax_update_lead() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) {
        wp_send_json_error(['msg' => 'Not allowed'], 403);
    }

    $id = absint($_POST['lead_id'] ?? 0);
    if (!$id || get_post_type($id) !== 'lead') {
        wp_send_json_error(['msg' => 'Invalid lead'], 400);
    }

    $field = sanitize_key($_POST['field'] ?? '');
    $value = wp_unslash($_POST['value'] ?? '');

    switch ($field) {
        case 'status':
            $value = array_key_exists($value, dpowered_lead_statuses()) ? $value : 'new';
            update_post_meta($id, '_lead_status', $value);
            if ($value !== 'new' && !get_post_meta($id, '_lead_called', true)) {
                update_post_meta($id, '_lead_called', 1);
            }
            break;
        case 'called':
        case 'offered':
            update_post_meta($id, '_lead_' . $field, ($value === '1' || $value === 1) ? 1 : 0);
            break;
        case 'assigned':
            $uid = absint($value);
            if ($uid && !dpowered_user_can_leads($uid)) $uid = 0;
            update_post_meta($id, '_lead_assigned', $uid);
            break;
        case 'source':
            $value = array_key_exists($value, dpowered_lead_sources()) ? $value : 'cold-call';
            update_post_meta($id, '_lead_source', $value);
            break;
        case 'contact':
        case 'phone':
            update_post_meta($id, '_lead_' . $field, sanitize_text_field($value));
            break;
        case 'email':
            update_post_meta($id, '_lead_email', sanitize_email($value));
            break;
        case 'last_contact':
            update_post_meta($id, '_lead_last_contact', sanitize_text_field($value));
            break;
        case 'date':
            $value = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value) ? $value : current_time('Y-m-d');
            update_post_meta($id, '_lead_date', $value);
            break;
        case 'notes':
            update_post_meta($id, '_lead_notes', sanitize_textarea_field($value));
            break;
        case 'business':
            wp_update_post(['ID' => $id, 'post_title' => sanitize_text_field($value)]);
            break;
        case 'callback':
            $value = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value) ? $value : '';
            update_post_meta($id, '_lead_callback', $value);
            break;
        case 'private':
            update_post_meta($id, '_lead_private', ($value === '1' || $value === 1) ? 1 : 0);
            break;
        default:
            wp_send_json_error(['msg' => 'Unknown field'], 400);
    }

    dpowered_touch_lead($id);
    wp_send_json_success(['lead' => dpowered_get_lead_data($id)]);
}
add_action('wp_ajax_dpowered_update_lead', 'dpowered_ajax_update_lead');

// ── AJAX: add a lead ─────────────────────────────────────────────────────────

function dpowered_ajax_add_lead() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) {
        wp_send_json_error(['msg' => 'Not allowed'], 403);
    }

    $business = sanitize_text_field(wp_unslash($_POST['business'] ?? ''));
    $contact  = sanitize_text_field(wp_unslash($_POST['contact'] ?? ''));
    $phone    = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));

    if ($business === '' && $contact === '' && $phone === '') {
        wp_send_json_error(['msg' => 'Enter at least a business name, contact or phone.'], 400);
    }

    $id = dpowered_insert_lead([
        'business' => $business,
        'contact'  => $contact,
        'phone'    => $phone,
        'email'    => sanitize_email(wp_unslash($_POST['email'] ?? '')),
        'status'   => sanitize_key($_POST['status'] ?? 'new'),
        'source'   => sanitize_key($_POST['source'] ?? 'cold-call'),
        'assigned' => absint($_POST['assigned'] ?? 0),
        'notes'    => sanitize_textarea_field(wp_unslash($_POST['notes'] ?? '')),
        'date'     => sanitize_text_field(wp_unslash($_POST['lead_date'] ?? '')),
        'callback' => sanitize_text_field(wp_unslash($_POST['callback'] ?? '')),
        'private'  => !empty($_POST['private']) ? 1 : 0,
    ]);

    if (is_wp_error($id)) {
        wp_send_json_error(['msg' => 'Could not save lead.'], 500);
    }

    wp_send_json_success(['lead' => dpowered_get_lead_data($id)]);
}
add_action('wp_ajax_dpowered_add_lead', 'dpowered_ajax_add_lead');

// ── AJAX: delete (trash) a lead ──────────────────────────────────────────────

function dpowered_ajax_delete_lead() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) {
        wp_send_json_error(['msg' => 'Not allowed'], 403);
    }

    $id = absint($_POST['lead_id'] ?? 0);
    if (!$id || get_post_type($id) !== 'lead') {
        wp_send_json_error(['msg' => 'Invalid lead'], 400);
    }

    wp_trash_post($id);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_delete_lead', 'dpowered_ajax_delete_lead');

// ── AJAX: workspace pages ────────────────────────────────────────────────────

function dpowered_ajax_save_page() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $uid     = get_current_user_id();
    $page_id = absint($_POST['page_id'] ?? 0);
    $title   = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    $content = wp_kses_post(wp_unslash($_POST['content'] ?? ''));
    $private = (int) !empty($_POST['private']);
    $icon    = sanitize_text_field(wp_unslash($_POST['icon'] ?? ''));

    if ($page_id) {
        $p = get_post($page_id);
        if (!$p || $p->post_type !== 'dpowered_page') wp_send_json_error(['msg' => 'Not found.'], 404);
        if ((int) $p->post_author !== $uid && !current_user_can('manage_options')) wp_send_json_error(['msg' => 'No access.'], 403);
        wp_update_post(['ID' => $page_id, 'post_title' => $title ?: 'Untitled', 'post_content' => $content]);
    } else {
        $page_id = wp_insert_post([
            'post_type'    => 'dpowered_page',
            'post_status'  => 'publish',
            'post_title'   => $title ?: 'Untitled',
            'post_content' => $content,
            'post_author'  => $uid,
        ]);
        if (is_wp_error($page_id)) wp_send_json_error(['msg' => 'Could not create page.'], 500);
    }

    update_post_meta($page_id, '_page_private', $private);
    update_post_meta($page_id, '_page_icon', $icon);
    wp_send_json_success(['page' => dpowered_get_page_data($page_id)]);
}
add_action('wp_ajax_dpowered_save_page', 'dpowered_ajax_save_page');

function dpowered_ajax_delete_page() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $uid     = get_current_user_id();
    $page_id = absint($_POST['page_id'] ?? 0);
    $p       = get_post($page_id);
    if (!$p || $p->post_type !== 'dpowered_page') wp_send_json_error(['msg' => 'Not found.'], 404);
    if ((int) $p->post_author !== $uid && !current_user_can('manage_options')) wp_send_json_error(['msg' => 'No access.'], 403);

    wp_trash_post($page_id);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_delete_page', 'dpowered_ajax_delete_page');

// ── AJAX: meetings ───────────────────────────────────────────────────────────

function dpowered_ajax_add_meeting() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $business = sanitize_text_field(wp_unslash($_POST['business'] ?? ''));
    if ($business === '') wp_send_json_error(['msg' => 'Business name is required.'], 400);

    $date = sanitize_text_field(wp_unslash($_POST['date'] ?? ''));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) wp_send_json_error(['msg' => 'Valid date required.'], 400);

    $id = wp_insert_post([
        'post_type'   => 'dpowered_meeting',
        'post_status' => 'publish',
        'post_title'  => $business,
    ]);
    if (is_wp_error($id)) wp_send_json_error(['msg' => 'Could not save.'], 500);

    $assigned = absint($_POST['assigned'] ?? 0);
    if ($assigned && !dpowered_user_can_leads($assigned)) $assigned = 0;

    update_post_meta($id, '_meeting_contact',  sanitize_text_field(wp_unslash($_POST['contact']  ?? '')));
    update_post_meta($id, '_meeting_phone',    sanitize_text_field(wp_unslash($_POST['phone']    ?? '')));
    update_post_meta($id, '_meeting_date',     $date);
    update_post_meta($id, '_meeting_time',     sanitize_text_field(wp_unslash($_POST['time']     ?? '')));
    update_post_meta($id, '_meeting_location', sanitize_text_field(wp_unslash($_POST['location'] ?? '')));
    update_post_meta($id, '_meeting_notes',    sanitize_textarea_field(wp_unslash($_POST['notes'] ?? '')));
    update_post_meta($id, '_meeting_status',   'upcoming');
    update_post_meta($id, '_meeting_assigned', $assigned);

    wp_send_json_success(['meeting' => dpowered_get_meeting_data($id)]);
}
add_action('wp_ajax_dpowered_add_meeting', 'dpowered_ajax_add_meeting');

function dpowered_ajax_update_meeting() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $id = absint($_POST['meeting_id'] ?? 0);
    if (!$id || get_post_type($id) !== 'dpowered_meeting') wp_send_json_error(['msg' => 'Invalid meeting.'], 400);

    $field = sanitize_key($_POST['field'] ?? '');
    $value = wp_unslash($_POST['value'] ?? '');

    switch ($field) {
        case 'business':
            wp_update_post(['ID' => $id, 'post_title' => sanitize_text_field($value)]);
            break;
        case 'contact':
        case 'phone':
        case 'location':
        case 'time':
            update_post_meta($id, '_meeting_' . $field, sanitize_text_field($value));
            break;
        case 'date':
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value)) wp_send_json_error(['msg' => 'Invalid date.'], 400);
            update_post_meta($id, '_meeting_date', $value);
            break;
        case 'notes':
            update_post_meta($id, '_meeting_notes', sanitize_textarea_field($value));
            break;
        case 'status':
            $allowed = ['upcoming', 'done', 'cancelled'];
            $value   = in_array($value, $allowed, true) ? $value : 'upcoming';
            update_post_meta($id, '_meeting_status', $value);
            break;
        case 'assigned':
            $uid = absint($value);
            if ($uid && !dpowered_user_can_leads($uid)) $uid = 0;
            update_post_meta($id, '_meeting_assigned', $uid);
            break;
        default:
            wp_send_json_error(['msg' => 'Unknown field.'], 400);
    }

    wp_send_json_success(['meeting' => dpowered_get_meeting_data($id)]);
}
add_action('wp_ajax_dpowered_update_meeting', 'dpowered_ajax_update_meeting');

function dpowered_ajax_delete_meeting() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $id = absint($_POST['meeting_id'] ?? 0);
    if (!$id || get_post_type($id) !== 'dpowered_meeting') wp_send_json_error(['msg' => 'Invalid meeting.'], 400);

    wp_trash_post($id);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_delete_meeting', 'dpowered_ajax_delete_meeting');

// ── AJAX: scratchpad ──────────────────────────────────────────────────────────

function dpowered_ajax_save_pad() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $date = sanitize_text_field(wp_unslash($_POST['date'] ?? current_time('Y-m-d')));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) wp_send_json_error(['msg' => 'Invalid date.'], 400);
    $content = sanitize_textarea_field(wp_unslash($_POST['content'] ?? ''));

    update_user_meta(get_current_user_id(), '_dpowered_pad_' . $date, $content);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_save_pad', 'dpowered_ajax_save_pad');

function dpowered_ajax_load_pad() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'No access.'], 403);

    $date = sanitize_text_field(wp_unslash($_POST['date'] ?? current_time('Y-m-d')));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) wp_send_json_error(['msg' => 'Invalid date.'], 400);

    $content = (string) get_user_meta(get_current_user_id(), '_dpowered_pad_' . $date, true);
    wp_send_json_success(['content' => $content]);
}
add_action('wp_ajax_dpowered_load_pad', 'dpowered_ajax_load_pad');

// ── Conditionally enqueue work-area assets ───────────────────────────────────

function dpowered_work_area_assets() {
    if (!is_page_template('page-work-area.php')) return;

    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    wp_enqueue_style(
        'dpowered-work-area',
        $uri . '/assets/css/work-area.css',
        ['dpowered-v2-style'],
        filemtime($dir . '/assets/css/work-area.css')
    );
    wp_enqueue_script(
        'dpowered-work-area',
        $uri . '/assets/js/work-area.js',
        [],
        filemtime($dir . '/assets/js/work-area.js'),
        true
    );
    $uid = get_current_user_id();
    wp_localize_script('dpowered-work-area', 'dpoweredLeads', [
        'ajaxurl'       => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('dpowered_leads'),
        'currentUser'   => $uid,
        'currentName'   => wp_get_current_user()->display_name,
        'currentAvatar' => dpowered_user_avatar($uid),
        'colors'        => [
            '#22c55e', '#f59e0b', '#ef4444', '#a855f7', '#06b6d4',
            '#f97316', '#ec4899', '#14b8a6', '#84cc16', '#fb923c',
        ],
        'pages'         => dpowered_get_visible_pages($uid),
        'todayPad'      => (string) get_user_meta($uid, '_dpowered_pad_' . current_time('Y-m-d'), true),
    ]);
}
add_action('wp_enqueue_scripts', 'dpowered_work_area_assets');

// ── AJAX: cursor presence ─────────────────────────────────────────────────────

function dpowered_ajax_cursor_push() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(null, 403);
    $uid = get_current_user_id();
    $x   = max(0,     min(1,     (float) sanitize_text_field($_POST['x'] ?? 0.5))); // fraction of content width
    $y   = max(-2000, min(60000, (float) sanitize_text_field($_POST['y'] ?? 0)));   // px down from content top
    set_transient('dpowered_cursor_' . $uid, [
        'x'    => $x,
        'y'    => $y,
        'name' => wp_get_current_user()->display_name,
        't'    => time(),
    ], 15);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_cursor_push', 'dpowered_ajax_cursor_push');

function dpowered_ajax_cursor_poll() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(null, 403);
    $uid  = get_current_user_id();
    $team = get_users(['role__in' => ['sales_team', 'administrator'], 'fields' => ['ID']]);
    $now  = time();
    $out  = [];
    foreach ($team as $member) {
        $mid = (int) $member->ID;
        if ($mid === $uid) continue;
        $data = get_transient('dpowered_cursor_' . $mid);
        if ($data && ($now - $data['t']) < 8) {
            $out[] = ['uid' => $mid, 'x' => $data['x'], 'y' => $data['y'], 'name' => $data['name']];
        }
    }
    wp_send_json_success(['cursors' => $out]);
}
add_action('wp_ajax_dpowered_cursor_poll', 'dpowered_ajax_cursor_poll');

// ══════════════════════════════════════════════════════════════════════════════
//  People presence · avatars · last-edited · live updates
// ══════════════════════════════════════════════════════════════════════════════

/**
 * A user's signature colour. Same palette + order as work-area.js so a person's
 * cursor, avatar fallback and presence pip all match across every screen.
 */
function dpowered_user_color($uid) {
    $palette = [
        '#22c55e', '#f59e0b', '#ef4444', '#a855f7', '#06b6d4',
        '#f97316', '#ec4899', '#14b8a6', '#84cc16', '#fb923c',
    ];
    return $palette[((int) $uid) % count($palette)];
}

/** Initials from a display name, e.g. "Sarah Jones" → "SJ". */
function dpowered_user_initials($name) {
    $parts = preg_split('/\s+/', trim((string) $name));
    $parts = array_filter($parts);
    if (!$parts) return '?';
    if (count($parts) === 1) return strtoupper(mb_substr($parts[0], 0, 1));
    $first = reset($parts);
    $last  = end($parts);
    return strtoupper(mb_substr($first, 0, 1) . mb_substr($last, 0, 1));
}

/** Avatar descriptor for a user: custom upload URL if set, else colour + initials. */
function dpowered_user_avatar($uid) {
    $uid  = (int) $uid;
    $user = $uid ? get_userdata($uid) : null;
    $name = $user ? $user->display_name : '';
    $att  = (int) get_user_meta($uid, '_dpowered_avatar', true);
    $url  = $att ? wp_get_attachment_image_url($att, 'thumbnail') : '';
    return [
        'uid'      => $uid,
        'name'     => $name,
        'url'      => $url ?: '',
        'color'    => dpowered_user_color($uid),
        'initials' => dpowered_user_initials($name),
    ];
}

/** Render an avatar chip (HTML) at a given pixel size. */
function dpowered_avatar_html($uid, $size = 32, $extra_class = '') {
    $a = dpowered_user_avatar($uid);
    $style = 'width:' . (int) $size . 'px;height:' . (int) $size . 'px;'
           . 'font-size:' . round($size * 0.4) . 'px;';
    $cls = 'wa-avatar ' . esc_attr($extra_class);
    if ($a['url']) {
        return '<span class="' . $cls . '" style="' . esc_attr($style) . '" title="' . esc_attr($a['name']) . '">'
             . '<img src="' . esc_url($a['url']) . '" alt="' . esc_attr($a['name']) . '"></span>';
    }
    $style .= 'background:' . esc_attr($a['color']) . ';';
    return '<span class="' . $cls . ' wa-avatar-initials" style="' . esc_attr($style) . '" title="' . esc_attr($a['name']) . '">'
         . esc_html($a['initials']) . '</span>';
}

// ── Presence heartbeat ────────────────────────────────────────────────────────

/** True if a user pinged within the last 35 seconds. */
function dpowered_user_is_online($uid) {
    $seen = (int) get_transient('dpowered_seen_' . (int) $uid);
    return $seen && (time() - $seen) < 35;
}

function dpowered_ajax_presence_ping() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(null, 403);
    set_transient('dpowered_seen_' . get_current_user_id(), time(), 60);
    wp_send_json_success();
}
add_action('wp_ajax_dpowered_presence_ping', 'dpowered_ajax_presence_ping');

/** Build the team roster with live presence + avatar info. */
function dpowered_team_presence() {
    $team = dpowered_lead_team_members();
    $now  = time();
    $out  = [];
    foreach ($team as $member) {
        $uid  = (int) $member->ID;
        $seen = (int) get_transient('dpowered_seen_' . $uid);
        $a    = dpowered_user_avatar($uid);
        $out[] = [
            'uid'         => $uid,
            'name'        => $member->display_name,
            'url'         => $a['url'],
            'color'       => $a['color'],
            'initials'    => $a['initials'],
            'online'      => ($seen && ($now - $seen) < 35),
            'last_human'  => $seen ? human_time_diff($seen, $now) . ' ago' : '',
        ];
    }
    // Online first, then alphabetical.
    usort($out, function ($x, $y) {
        if ($x['online'] !== $y['online']) return $x['online'] ? -1 : 1;
        return strcasecmp($x['name'], $y['name']);
    });
    return $out;
}

function dpowered_ajax_presence_poll() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(null, 403);
    wp_send_json_success(['team' => dpowered_team_presence()]);
}
add_action('wp_ajax_dpowered_presence_poll', 'dpowered_ajax_presence_poll');

// ── Avatar upload ─────────────────────────────────────────────────────────────

function dpowered_ajax_upload_avatar() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(['msg' => 'Not allowed'], 403);
    if (empty($_FILES['avatar'])) wp_send_json_error(['msg' => 'No file received.'], 400);

    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $check = wp_check_filetype($file['name']);
    if (!in_array($file['type'], $allowed, true) || !in_array($check['type'], $allowed, true)) {
        wp_send_json_error(['msg' => 'Please upload a JPG, PNG, GIF or WebP image.'], 400);
    }
    if ($file['size'] > 4 * 1024 * 1024) {
        wp_send_json_error(['msg' => 'Image must be under 4MB.'], 400);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uid = get_current_user_id();
    $att_id = media_handle_upload('avatar', 0);
    if (is_wp_error($att_id)) {
        wp_send_json_error(['msg' => 'Upload failed. ' . $att_id->get_error_message()], 500);
    }

    // Remove the previous custom avatar so the media library doesn't fill up.
    $old = (int) get_user_meta($uid, '_dpowered_avatar', true);
    if ($old && $old !== $att_id) wp_delete_attachment($old, true);

    update_user_meta($uid, '_dpowered_avatar', $att_id);
    wp_send_json_success(['avatar' => dpowered_user_avatar($uid)]);
}
add_action('wp_ajax_dpowered_upload_avatar', 'dpowered_ajax_upload_avatar');

/** Surface the custom avatar inside wp-admin's get_avatar() too. */
function dpowered_filter_avatar_url($url, $id_or_email, $args) {
    $uid = 0;
    if (is_numeric($id_or_email)) {
        $uid = (int) $id_or_email;
    } elseif ($id_or_email instanceof WP_User) {
        $uid = (int) $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Comment) {
        $uid = (int) $id_or_email->user_id;
    } elseif (is_string($id_or_email) && ($u = get_user_by('email', $id_or_email))) {
        $uid = (int) $u->ID;
    }
    if (!$uid) return $url;
    $att = (int) get_user_meta($uid, '_dpowered_avatar', true);
    if ($att) {
        $custom = wp_get_attachment_image_url($att, 'thumbnail');
        if ($custom) return $custom;
    }
    return $url;
}
add_filter('get_avatar_url', 'dpowered_filter_avatar_url', 10, 3);

// ── Server-rendered lead row (shared by template + live updates) ───────────────

/**
 * Render one lead's <tr> pair (main row + detail row) exactly as the template
 * does, so live updates can drop in fresh markup without duplicating it in JS.
 */
function dpowered_render_lead_row($d, $statuses = null, $sources = null, $team = null) {
    $statuses = $statuses ?: dpowered_lead_statuses();
    $sources  = $sources  ?: dpowered_lead_sources();
    $team     = $team     ?: dpowered_lead_team_members();

    $edited = '';
    if ($d['edited_by_name'] && $d['edited_human']) {
        $edited = '<span class="wa-edited">'
                . dpowered_avatar_html($d['edited_by'], 16, 'wa-edited-avatar')
                . 'edited by ' . esc_html($d['edited_by_name']) . ' · ' . esc_html($d['edited_human'])
                . '</span>';
    }

    ob_start();
    ?>
    <tr class="wa-row<?php echo $d['private'] ? ' is-private-lead' : ''; ?>"
        data-id="<?php echo (int) $d['id']; ?>"
        data-status="<?php echo esc_attr($d['status']); ?>"
        data-assigned="<?php echo (int) $d['assigned']; ?>"
        data-date="<?php echo esc_attr($d['date']); ?>"
        data-callback="<?php echo esc_attr($d['callback']); ?>"
        data-edited="<?php echo (int) $d['edited_at']; ?>"
        data-private="<?php echo $d['private'] ? '1' : '0'; ?>">
        <td class="wa-col-toggle"><button type="button" class="wa-expand<?php echo $d['notes'] ? ' has-notes' : ''; ?>" aria-label="Toggle details">&rsaquo;</button></td>
        <td data-label="Business">
            <input type="text" class="wa-input wa-business" data-field="business" value="<?php echo esc_attr($d['business']); ?>">
            <span class="wa-meta">Added <?php echo esc_html($d['created']); ?> · <?php echo esc_html($sources[$d['source']] ?? $d['source']); ?></span>
            <?php echo $edited; ?>
            <span class="wa-callback-badge" hidden>📞 Callback</span>
        </td>
        <td data-label="Contact"><input type="text" class="wa-input" data-field="contact" value="<?php echo esc_attr($d['contact']); ?>"></td>
        <td data-label="Phone" class="wa-phone-cell">
            <input type="tel" class="wa-input" data-field="phone" value="<?php echo esc_attr($d['phone']); ?>">
            <button type="button" class="wa-call-btn" aria-label="Call this number" title="Call">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </button>
        </td>
        <td data-label="Status">
            <select class="wa-status status-<?php echo esc_attr($d['status']); ?>" data-field="status">
                <?php foreach ($statuses as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($d['status'], $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="wa-col-center" data-label="Called"><input type="checkbox" class="wa-check" data-field="called" <?php checked($d['called']); ?>></td>
        <td class="wa-col-center" data-label="Offered"><input type="checkbox" class="wa-check" data-field="offered" <?php checked($d['offered']); ?>></td>
        <td data-label="Assigned">
            <div class="wa-assigned-cell">
                <?php echo $d['assigned'] ? dpowered_avatar_html($d['assigned'], 24, 'wa-assigned-avatar') : ''; ?>
                <select class="wa-input" data-field="assigned">
                    <option value="0">Unassigned</option>
                    <?php foreach ($team as $member): ?>
                    <option value="<?php echo (int) $member->ID; ?>" <?php selected($d['assigned'], $member->ID); ?>><?php echo esc_html($member->display_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </td>
        <td class="wa-col-center wa-col-actions">
            <button type="button"
                class="wa-private-btn<?php echo $d['private'] ? ' is-private' : ''; ?>"
                data-field="private"
                aria-label="Toggle privacy"
                title="<?php echo $d['private'] ? 'Private — click to share with team' : 'Shared — click to make private'; ?>"></button>
            <button type="button" class="wa-delete" aria-label="Delete lead">&times;</button>
        </td>
    </tr>
    <tr class="wa-detail" data-id="<?php echo (int) $d['id']; ?>" hidden>
        <td colspan="9">
            <div class="wa-detail-grid">
                <label>Email
                    <input type="email" class="wa-input" data-field="email" value="<?php echo esc_attr($d['email']); ?>">
                </label>
                <label>Source
                    <select class="wa-input" data-field="source">
                        <?php foreach ($sources as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($d['source'], $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Sheet date
                    <input type="date" class="wa-input" data-field="date" value="<?php echo esc_attr($d['date']); ?>">
                </label>
                <label>Call back date
                    <input type="date" class="wa-input" data-field="callback" value="<?php echo esc_attr($d['callback']); ?>">
                </label>
                <label class="wa-detail-notes">Notes
                    <textarea class="wa-input" data-field="notes" rows="2"><?php echo esc_textarea($d['notes']); ?></textarea>
                </label>
            </div>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

// ── AJAX: live lead changes since a timestamp ──────────────────────────────────

function dpowered_ajax_leads_changes() {
    check_ajax_referer('dpowered_leads', 'nonce');
    if (!dpowered_user_can_leads()) wp_send_json_error(null, 403);

    $since       = absint($_POST['since'] ?? 0);
    $uid         = get_current_user_id();
    $is_admin    = current_user_can('manage_options');
    $statuses    = dpowered_lead_statuses();
    $sources     = dpowered_lead_sources();
    $team        = dpowered_lead_team_members();

    $leads = get_posts([
        'post_type'      => 'lead',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $ids  = [];
    $rows = [];
    foreach ($leads as $l) {
        $d = dpowered_get_lead_data($l->ID);
        // Respect privacy: leads only the owner/admin can see.
        if ($d['private'] && $d['assigned'] !== $uid && !$is_admin) continue;
        $ids[] = $d['id'];
        if ($since && $d['edited_at'] > $since) {
            $rows[] = [
                'id'     => $d['id'],
                'editor' => $d['edited_by'],
                'editor_name' => $d['edited_by_name'],
                'business'    => $d['business'],
                'html'   => dpowered_render_lead_row($d, $statuses, $sources, $team),
            ];
        }
    }

    wp_send_json_success(['now' => time(), 'ids' => $ids, 'rows' => $rows]);
}
add_action('wp_ajax_dpowered_leads_changes', 'dpowered_ajax_leads_changes');
