<?php
require_once get_template_directory() . '/inc/leads.php';

function dpowered_ensure_terms_page() {
    $existing = get_page_by_path('terms');
    if ($existing) {
        update_post_meta($existing->ID, '_wp_page_template', 'page-terms.php');
        return $existing->ID;
    }
    $id = wp_insert_post([
        'post_title'   => 'Terms & Client Agreement',
        'post_name'    => 'terms',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
        'meta_input'   => ['_wp_page_template' => 'page-terms.php'],
    ]);
    return ($id && !is_wp_error($id)) ? $id : 0;
}
add_action('after_switch_theme', 'dpowered_ensure_terms_page');

function dpowered_maybe_create_terms_page() {
    if (get_option('dpowered_terms_page_done')) return;
    dpowered_ensure_terms_page();
    update_option('dpowered_terms_page_done', 1);
}
add_action('init', 'dpowered_maybe_create_terms_page');

function dpowered_check_icon() {
    return '<svg class="check-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
}
function dpowered_x_icon() {
    return '<svg class="x-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
}

function dpowered_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    register_nav_menus(['primary' => 'Primary Menu']);
}
add_action('after_setup_theme', 'dpowered_setup');

function dpowered_enqueue() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('dpowered-v2-style', get_template_directory_uri() . '/assets/css/style.css', [], filemtime(get_template_directory() . '/assets/css/style.css'));
    wp_enqueue_script('dpowered-v2-script', get_template_directory_uri() . '/assets/js/main.js', [], filemtime(get_template_directory() . '/assets/js/main.js'), true);
}
add_action('wp_enqueue_scripts', 'dpowered_enqueue');

function dpowered_serve_favicon_ico() {
    $favicon = get_template_directory() . '/assets/images/favicon.ico';
    if (!file_exists($favicon)) return;
    header('Content-Type: image/x-icon');
    header('Content-Length: ' . filesize($favicon));
    readfile($favicon);
    exit;
}
add_action('do_faviconico', 'dpowered_serve_favicon_ico');

function dpowered_register_reviews() {
    register_post_type('review', [
        'labels' => [
            'name'               => 'Reviews',
            'singular_name'      => 'Review',
            'add_new'            => 'Add New Review',
            'add_new_item'       => 'Add New Review',
            'edit_item'          => 'Edit Review',
            'new_item'           => 'New Review',
            'view_item'          => 'View Review',
            'search_items'       => 'Search Reviews',
            'not_found'          => 'No reviews found',
            'not_found_in_trash' => 'No reviews found in trash',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'supports'        => ['title', 'editor'],
        'menu_icon'       => 'dashicons-star-filled',
        'capability_type' => 'post',
    ]);
}
add_action('init', 'dpowered_register_reviews');

function dpowered_review_meta_box() {
    add_meta_box('dpowered_review_details', 'Review Details', 'dpowered_review_meta_box_html', 'review', 'normal', 'high');
}
add_action('add_meta_boxes', 'dpowered_review_meta_box');

function dpowered_review_meta_box_html($post) {
    wp_nonce_field('dpowered_review_meta', 'review_meta_nonce');
    $company = get_post_meta($post->ID, '_review_company', true);
    $rating  = get_post_meta($post->ID, '_review_rating', true) ?: 5;
    ?>
    <p style="margin-bottom:12px;color:#555">
        Enter the <strong>reviewer's name</strong> as the post title above.
        Write the <strong>review quote</strong> in the content editor below.
    </p>
    <table class="form-table">
        <tr>
            <th><label for="review_company">Company / Role</label></th>
            <td><input type="text" id="review_company" name="review_company" value="<?php echo esc_attr($company); ?>" class="regular-text" placeholder="e.g. Owner, Smith's Plumbing"></td>
        </tr>
        <tr>
            <th><label for="review_rating">Star Rating</label></th>
            <td>
                <select id="review_rating" name="review_rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function dpowered_save_review_meta($post_id) {
    if (!isset($_POST['review_meta_nonce']) || !wp_verify_nonce($_POST['review_meta_nonce'], 'dpowered_review_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['review_company'])) update_post_meta($post_id, '_review_company', sanitize_text_field($_POST['review_company']));
    if (isset($_POST['review_rating']))  update_post_meta($post_id, '_review_rating', min(5, max(1, absint($_POST['review_rating']))));
}
add_action('save_post_review', 'dpowered_save_review_meta');

function dpowered_register_portfolio() {
    register_post_type('project', [
        'labels' => [
            'name'               => 'Portfolio',
            'singular_name'      => 'Project',
            'add_new'            => 'Add New Project',
            'add_new_item'       => 'Add New Project',
            'edit_item'          => 'Edit Project',
            'new_item'           => 'New Project',
            'view_item'          => 'View Project',
            'not_found'          => 'No projects found',
            'not_found_in_trash' => 'No projects found in trash',
            'featured_image'     => 'Homepage Screenshot',
            'set_featured_image' => 'Upload homepage screenshot',
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'show_in_rest'    => true,
        'supports'        => ['title', 'editor', 'thumbnail'],
        'menu_icon'       => 'dashicons-portfolio',
        'capability_type' => 'post',
    ]);
}
add_action('init', 'dpowered_register_portfolio');

function dpowered_project_meta_box() {
    add_meta_box('dpowered_project_details', 'Project Details', 'dpowered_project_meta_box_html', 'project', 'normal', 'high');
}
add_action('add_meta_boxes', 'dpowered_project_meta_box');

function dpowered_project_meta_box_html($post) {
    wp_nonce_field('dpowered_project_meta', 'project_meta_nonce');
    $url      = get_post_meta($post->ID, '_project_url', true);
    $client   = get_post_meta($post->ID, '_project_client', true);
    $service  = get_post_meta($post->ID, '_project_service', true);
    $year     = get_post_meta($post->ID, '_project_year', true);
    $review   = get_post_meta($post->ID, '_project_review', true);
    $rev_name = get_post_meta($post->ID, '_project_reviewer_name', true);
    $rev_role = get_post_meta($post->ID, '_project_reviewer_role', true);
    $rating   = get_post_meta($post->ID, '_project_rating', true) ?: 5;
    ?>
    <table class="form-table">
        <tr><th><label for="project_client">Client / Business Name</label></th><td><input type="text" id="project_client" name="project_client" value="<?php echo esc_attr($client); ?>" class="regular-text"></td></tr>
        <tr><th><label for="project_service">Project Type</label></th><td><input type="text" id="project_service" name="project_service" value="<?php echo esc_attr($service); ?>" class="regular-text"></td></tr>
        <tr><th><label for="project_year">Launch Year</label></th><td><input type="number" id="project_year" name="project_year" value="<?php echo esc_attr($year); ?>" class="small-text" min="2000" max="2100"></td></tr>
        <tr><th><label for="project_url">Live Website URL</label></th><td><input type="url" id="project_url" name="project_url" value="<?php echo esc_attr($url); ?>" class="regular-text"></td></tr>
        <tr><th><label for="project_review">Client Review Quote</label></th><td><textarea id="project_review" name="project_review" rows="3" class="large-text"><?php echo esc_textarea($review); ?></textarea></td></tr>
        <tr><th><label for="project_reviewer_name">Reviewer Name</label></th><td><input type="text" id="project_reviewer_name" name="project_reviewer_name" value="<?php echo esc_attr($rev_name); ?>" class="regular-text"></td></tr>
        <tr><th><label for="project_reviewer_role">Reviewer Role / Company</label></th><td><input type="text" id="project_reviewer_role" name="project_reviewer_role" value="<?php echo esc_attr($rev_role); ?>" class="regular-text"></td></tr>
        <tr>
            <th><label>Star Rating</label></th>
            <td>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <label style="margin-right:12px"><input type="radio" name="project_rating" value="<?php echo $i; ?>" <?php checked((int)$rating, $i); ?>> <?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?></label>
                <?php endfor; ?>
            </td>
        </tr>
    </table>
    <?php
}

function dpowered_save_project_meta($post_id) {
    if (!isset($_POST['project_meta_nonce']) || !wp_verify_nonce($_POST['project_meta_nonce'], 'dpowered_project_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['project_url']))           update_post_meta($post_id, '_project_url',           esc_url_raw($_POST['project_url']));
    if (isset($_POST['project_client']))        update_post_meta($post_id, '_project_client',        sanitize_text_field($_POST['project_client']));
    if (isset($_POST['project_service']))       update_post_meta($post_id, '_project_service',       sanitize_text_field($_POST['project_service']));
    if (isset($_POST['project_year']))          update_post_meta($post_id, '_project_year',          absint($_POST['project_year']) ?: '');
    if (isset($_POST['project_review']))        update_post_meta($post_id, '_project_review',        sanitize_textarea_field($_POST['project_review']));
    if (isset($_POST['project_reviewer_name'])) update_post_meta($post_id, '_project_reviewer_name', sanitize_text_field($_POST['project_reviewer_name']));
    if (isset($_POST['project_reviewer_role'])) update_post_meta($post_id, '_project_reviewer_role', sanitize_text_field($_POST['project_reviewer_role']));
    if (isset($_POST['project_rating']))        update_post_meta($post_id, '_project_rating',        min(5, max(1, absint($_POST['project_rating']))));
}
add_action('save_post_project', 'dpowered_save_project_meta');

function dpowered_project_admin_columns($columns) {
    return ['cb' => $columns['cb'] ?? '', 'project_screenshot' => 'Screenshot', 'title' => 'Project', 'project_type' => 'Type', 'project_url' => 'Live URL', 'date' => $columns['date'] ?? 'Date'];
}
add_filter('manage_project_posts_columns', 'dpowered_project_admin_columns');

function dpowered_project_admin_column_content($column, $post_id) {
    if ($column === 'project_screenshot') {
        echo has_post_thumbnail($post_id) ? get_the_post_thumbnail($post_id, [90, 54], ['style' => 'width:90px;height:54px;object-fit:cover;object-position:top;border-radius:4px']) : '<span style="color:#777">No screenshot</span>';
    }
    if ($column === 'project_type') echo esc_html(get_post_meta($post_id, '_project_service', true) ?: '—');
    if ($column === 'project_url') {
        $url = get_post_meta($post_id, '_project_url', true);
        echo $url ? '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html(untrailingslashit(preg_replace('#^https?://#', '', $url))) . '</a>' : '<span style="color:#777">No URL</span>';
    }
}
add_action('manage_project_posts_custom_column', 'dpowered_project_admin_column_content', 10, 2);

function dpowered_handle_contact_form() {
    if (!isset($_POST['dpowered_contact_submit'])) return;
    $referer = wp_get_referer() ?: home_url('/contact');
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'dpowered_contact')) {
        wp_redirect(add_query_arg('form_error', 'security', $referer)); exit;
    }

    if (!empty($_POST['contact_website'])) {
        wp_redirect(add_query_arg('sent', '1', $referer)); exit;
    }

    $form_time = (int) ($_POST['contact_time'] ?? 0);
    if ($form_time > 0 && (time() - $form_time) < 3) {
        wp_redirect(add_query_arg('sent', '1', $referer)); exit;
    }

    $raw_name  = wp_unslash($_POST['contact_name']  ?? '');
    $raw_phone = wp_unslash($_POST['contact_phone'] ?? '');
    if (preg_match('#https?://#i', $raw_name . $raw_phone)) {
        wp_redirect(add_query_arg('sent', '1', $referer)); exit;
    }

    $name     = sanitize_text_field($_POST['contact_name'] ?? '');
    $email    = sanitize_email($_POST['contact_email'] ?? '');
    $phone    = sanitize_text_field($_POST['contact_phone'] ?? '');
    $business = sanitize_text_field($_POST['contact_business'] ?? '');
    $service  = sanitize_text_field($_POST['contact_service'] ?? '');
    $message  = sanitize_textarea_field($_POST['contact_message'] ?? '');
    if (empty($name) || empty($email) || empty($message) || !is_email($email)) {
        wp_redirect(add_query_arg('form_error', 'validation', $referer)); exit;
    }

    if (function_exists('dpowered_insert_lead')) {
        dpowered_insert_lead([
            'business' => $business,
            'contact'  => $name,
            'phone'    => $phone,
            'email'    => $email,
            'status'   => 'new',
            'source'   => 'website',
            'notes'    => $service ? ('Interested in: ' . $service . "\n\n" . $message) : $message,
        ]);
    }
    $to      = 'support@dpowered.online';
    $subject = "New Quote Request from {$name}";
    $body    = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Inter,Arial,sans-serif;background:#f4f4f5;margin:0;padding:0}.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)}.hdr{background:#0C0C0C;padding:24px 32px}.hdr h1{color:#fff;font-size:17px;margin:0;font-weight:600}.hdr span{color:#1A4DFF}.bod{padding:28px 32px}.row{margin-bottom:18px}.lbl{font-size:11px;font-weight:700;text-transform:uppercase;color:#888;letter-spacing:.06em;margin-bottom:4px}.val{font-size:15px;color:#111}.msg{background:#f4f4f5;border-radius:6px;padding:14px 16px;color:#333;white-space:pre-wrap;font-size:14px;line-height:1.6}.ftr{padding:14px 32px;background:#f4f4f5;font-size:12px;color:#999;border-top:1px solid #e5e5e5}a{color:#1A4DFF}</style></head><body><div class="wrap"><div class="hdr"><h1>DPowered<span>.</span>online &mdash; New Quote Request</h1></div><div class="bod"><div class="row"><div class="lbl">Name</div><div class="val">' . esc_html($name) . '</div></div><div class="row"><div class="lbl">Email</div><div class="val"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></div></div>'
    . ($phone    ? '<div class="row"><div class="lbl">Phone</div><div class="val">' . esc_html($phone) . '</div></div>' : '')
    . ($business ? '<div class="row"><div class="lbl">Business</div><div class="val">' . esc_html($business) . '</div></div>' : '')
    . ($service  ? '<div class="row"><div class="lbl">Service</div><div class="val">' . esc_html($service) . '</div></div>' : '')
    . '<div class="row"><div class="lbl">Message</div><div class="msg">' . esc_html($message) . '</div></div></div><div class="ftr">Sent via DPowered.online contact form</div></div></body></html>';
    $headers = ["Content-Type: text/html; charset=UTF-8", "Reply-To: {$name} <{$email}>"];
    if (wp_mail($to, $subject, $body, $headers)) {
        wp_redirect(add_query_arg('sent', '1', $referer));
    } else {
        wp_redirect(add_query_arg('form_error', 'send', $referer));
    }
    exit;
}
add_action('template_redirect', 'dpowered_handle_contact_form');

function dpowered_handle_review_submission() {
    if (!isset($_POST['dpowered_review_submit'])) return;
    $referer = wp_get_referer() ?: home_url('/reviews');
    if (!isset($_POST['review_nonce']) || !wp_verify_nonce($_POST['review_nonce'], 'dpowered_submit_review')) {
        wp_redirect(add_query_arg('review_error', 'security', $referer)); exit;
    }

    if (!empty($_POST['review_website'])) {
        wp_redirect(add_query_arg('review_sent', '1', $referer)); exit;
    }
    $review_time = (int) ($_POST['review_time'] ?? 0);
    if ($review_time > 0 && (time() - $review_time) < 3) {
        wp_redirect(add_query_arg('review_sent', '1', $referer)); exit;
    }
    $raw_reviewer_name = wp_unslash($_POST['reviewer_name'] ?? '');
    if (preg_match('#https?://#i', $raw_reviewer_name)) {
        wp_redirect(add_query_arg('review_sent', '1', $referer)); exit;
    }

    $name    = sanitize_text_field($_POST['reviewer_name'] ?? '');
    $company = sanitize_text_field($_POST['reviewer_company'] ?? '');
    $rating  = min(5, max(1, absint($_POST['reviewer_rating'] ?? 5)));
    $text    = sanitize_textarea_field($_POST['reviewer_text'] ?? '');
    if (empty($name) || empty($text)) {
        wp_redirect(add_query_arg('review_error', 'validation', $referer)); exit;
    }
    $post_id = wp_insert_post(['post_title' => $name, 'post_content' => $text, 'post_type' => 'review', 'post_status' => 'pending']);
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_review_company', $company);
        update_post_meta($post_id, '_review_rating', $rating);
        wp_mail('support@dpowered.online', "New Review from {$name} — Awaiting Approval", "From: {$name}\nRating: {$rating}/5\n\n{$text}\n\nApprove: " . admin_url('edit.php?post_status=pending&post_type=review'), ['Content-Type: text/plain; charset=UTF-8']);
        wp_redirect(add_query_arg('review_sent', '1', $referer));
    } else {
        wp_redirect(add_query_arg('review_error', 'save', $referer));
    }
    exit;
}
add_action('template_redirect', 'dpowered_handle_review_submission');
