<?php
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
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Inter+Tight:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('dpowered-style', get_template_directory_uri() . '/assets/css/style.css', [], filemtime(get_template_directory() . '/assets/css/style.css'));
    wp_enqueue_script('three-js', 'https://unpkg.com/three@0.160.0/build/three.min.js', [], '0.160.0', true);
    wp_enqueue_script('dpowered-script', get_template_directory_uri() . '/assets/js/main.js', ['three-js'], filemtime(get_template_directory() . '/assets/js/main.js'), true);
}
add_action('wp_enqueue_scripts', 'dpowered_enqueue');

function dpowered_serve_favicon_ico() {
    $favicon = get_template_directory() . '/assets/images/favicon.ico';

    if (!file_exists($favicon)) {
        return;
    }

    header('Content-Type: image/x-icon');
    header('Content-Length: ' . filesize($favicon));
    readfile($favicon);
    exit;
}
add_action('do_faviconico', 'dpowered_serve_favicon_ico');

// ── REVIEWS CUSTOM POST TYPE ─────────────────────────────────────────────────

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
            <td>
                <input type="text" id="review_company" name="review_company"
                    value="<?php echo esc_attr($company); ?>"
                    class="regular-text"
                    placeholder="e.g. Owner, Smith's Plumbing">
            </td>
        </tr>
        <tr>
            <th><label for="review_rating">Star Rating</label></th>
            <td>
                <select id="review_rating" name="review_rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>>
                        <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                    </option>
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

    if (isset($_POST['review_company'])) {
        update_post_meta($post_id, '_review_company', sanitize_text_field($_POST['review_company']));
    }
    if (isset($_POST['review_rating'])) {
        update_post_meta($post_id, '_review_rating', min(5, max(1, absint($_POST['review_rating']))));
    }
}
add_action('save_post_review', 'dpowered_save_review_meta');

// ── PORTFOLIO CUSTOM POST TYPE ───────────────────────────────────────────────

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
            'remove_featured_image' => 'Remove homepage screenshot',
            'use_featured_image' => 'Use as homepage screenshot',
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
    <p style="margin-bottom:12px;color:#555;max-width:760px">
        Add a project by setting the <strong>title</strong> to the business/project name, writing a short summary in the editor, and using <strong>Homepage Screenshot</strong> in the sidebar to upload a picture of the front page. That screenshot and the details below appear automatically on the Portfolio page.
    </p>
    <style>
        .dpowered-star-rating {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 6px;
        }
        .dpowered-star-rating input {
            position: absolute;
            opacity: 0;
        }
        .dpowered-star-rating label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 11px;
            border: 1px solid #c3c4c7;
            border-radius: 999px;
            background: #fff;
            cursor: pointer;
            font-weight: 600;
        }
        .dpowered-star-rating label span {
            color: #dba617;
            letter-spacing: 1px;
        }
        .dpowered-star-rating input:checked + label {
            border-color: #2271b1;
            background: #f0f6fc;
            box-shadow: 0 0 0 1px #2271b1;
        }
        .dpowered-field-help {
            margin: 6px 0 0;
            color: #646970;
        }
    </style>
    <table class="form-table">
        <tr>
            <th><label for="project_client">Client / Business Name</label></th>
            <td><input type="text" id="project_client" name="project_client" value="<?php echo esc_attr($client); ?>" class="regular-text" placeholder="e.g. Smith Plumbing"></td>
        </tr>
        <tr>
            <th><label for="project_service">Project Type</label></th>
            <td><input type="text" id="project_service" name="project_service" value="<?php echo esc_attr($service); ?>" class="regular-text" placeholder="e.g. Website redesign, local SEO, care plan"></td>
        </tr>
        <tr>
            <th><label for="project_year">Launch Year</label></th>
            <td><input type="number" id="project_year" name="project_year" value="<?php echo esc_attr($year); ?>" class="small-text" min="2000" max="2100" placeholder="<?php echo esc_attr(date('Y')); ?>"></td>
        </tr>
        <tr>
            <th><label for="project_url">Live Website URL</label></th>
            <td>
                <input type="url" id="project_url" name="project_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://clientsite.com">
                <p class="dpowered-field-help">This becomes the visible clickable website link on the Portfolio page.</p>
            </td>
        </tr>
        <tr>
            <th><label for="project_review">Client Review Quote</label></th>
            <td><textarea id="project_review" name="project_review" rows="3" class="large-text"><?php echo esc_textarea($review); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="project_reviewer_name">Reviewer Name</label></th>
            <td><input type="text" id="project_reviewer_name" name="project_reviewer_name" value="<?php echo esc_attr($rev_name); ?>" class="regular-text" placeholder="e.g. Sarah Khan"></td>
        </tr>
        <tr>
            <th><label for="project_reviewer_role">Reviewer Role / Company</label></th>
            <td><input type="text" id="project_reviewer_role" name="project_reviewer_role" value="<?php echo esc_attr($rev_role); ?>" class="regular-text" placeholder="e.g. Director, SK Consultancy"></td>
        </tr>
        <tr>
            <th><label for="project_rating">Star Rating</label></th>
            <td>
                <div class="dpowered-star-rating" id="project_rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="project_rating_<?php echo $i; ?>" name="project_rating" value="<?php echo $i; ?>" <?php checked((int) $rating, $i); ?>>
                        <label for="project_rating_<?php echo $i; ?>">
                            <span aria-hidden="true"><?php echo str_repeat('&#9733;', $i); ?></span>
                            <?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?>
                        </label>
                    <?php endfor; ?>
                </div>
                <p class="dpowered-field-help">Pick the stars here. They only show on the card when you add a client review quote.</p>
            </td>
        </tr>
    </table>
    <?php
}

function dpowered_save_project_meta($post_id) {
    if (!isset($_POST['project_meta_nonce']) || !wp_verify_nonce($_POST['project_meta_nonce'], 'dpowered_project_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['project_url'])) {
        update_post_meta($post_id, '_project_url', esc_url_raw($_POST['project_url']));
    }
    if (isset($_POST['project_client'])) {
        update_post_meta($post_id, '_project_client', sanitize_text_field($_POST['project_client']));
    }
    if (isset($_POST['project_service'])) {
        update_post_meta($post_id, '_project_service', sanitize_text_field($_POST['project_service']));
    }
    if (isset($_POST['project_year'])) {
        $year = absint($_POST['project_year']);
        update_post_meta($post_id, '_project_year', $year ? min(2100, max(2000, $year)) : '');
    }
    if (isset($_POST['project_review'])) {
        update_post_meta($post_id, '_project_review', sanitize_textarea_field($_POST['project_review']));
    }
    if (isset($_POST['project_reviewer_name'])) {
        update_post_meta($post_id, '_project_reviewer_name', sanitize_text_field($_POST['project_reviewer_name']));
    }
    if (isset($_POST['project_reviewer_role'])) {
        update_post_meta($post_id, '_project_reviewer_role', sanitize_text_field($_POST['project_reviewer_role']));
    }
    if (isset($_POST['project_rating'])) {
        update_post_meta($post_id, '_project_rating', min(5, max(1, absint($_POST['project_rating']))));
    }
}
add_action('save_post_project', 'dpowered_save_project_meta');

function dpowered_project_title_placeholder($title, $post) {
    if ($post && $post->post_type === 'project') {
        return 'Business or project name';
    }

    return $title;
}
add_filter('enter_title_here', 'dpowered_project_title_placeholder', 10, 2);

function dpowered_project_admin_columns($columns) {
    $new = [];
    $new['cb'] = $columns['cb'] ?? '';
    $new['project_screenshot'] = 'Screenshot';
    $new['title'] = 'Project';
    $new['project_type'] = 'Type';
    $new['project_url'] = 'Live URL';
    $new['date'] = $columns['date'] ?? 'Date';
    return $new;
}
add_filter('manage_project_posts_columns', 'dpowered_project_admin_columns');

function dpowered_project_admin_column_content($column, $post_id) {
    if ($column === 'project_screenshot') {
        if (has_post_thumbnail($post_id)) {
            echo get_the_post_thumbnail($post_id, [90, 54], ['style' => 'width:90px;height:54px;object-fit:cover;object-position:top;border-radius:4px']);
        } else {
            echo '<span style="color:#777">No screenshot</span>';
        }
    }
    if ($column === 'project_type') {
        echo esc_html(get_post_meta($post_id, '_project_service', true) ?: '—');
    }
    if ($column === 'project_url') {
        $url = get_post_meta($post_id, '_project_url', true);
        if ($url) {
            $display_url = preg_replace('#^https?://#', '', $url);
            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" style="display:block;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' . esc_html(untrailingslashit($display_url)) . '</a>';
        } else {
            echo '<span style="color:#777">No URL</span>';
        }
    }
}
add_action('manage_project_posts_custom_column', 'dpowered_project_admin_column_content', 10, 2);

// ── CONTACT FORM PROCESSING ──────────────────────────────────────────────────

function dpowered_handle_contact_form() {
    if (!isset($_POST['dpowered_contact_submit'])) return;

    $referer = wp_get_referer() ?: home_url('/contact');

    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'dpowered_contact')) {
        wp_redirect(add_query_arg('form_error', 'security', $referer));
        exit;
    }

    $name     = sanitize_text_field($_POST['contact_name'] ?? '');
    $email    = sanitize_email($_POST['contact_email'] ?? '');
    $phone    = sanitize_text_field($_POST['contact_phone'] ?? '');
    $business = sanitize_text_field($_POST['contact_business'] ?? '');
    $service  = sanitize_text_field($_POST['contact_service'] ?? '');
    $message  = sanitize_textarea_field($_POST['contact_message'] ?? '');

    if (empty($name) || empty($email) || empty($message) || !is_email($email)) {
        wp_redirect(add_query_arg('form_error', 'validation', $referer));
        exit;
    }

    $to      = 'support@dpowered.online';
    $subject = "New Quote Request from {$name}";
    $body = '<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
body{font-family:Inter,Arial,sans-serif;background:#f4f4f5;margin:0;padding:0}
.wrap{max-width:560px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.hdr{background:#060612;padding:24px 32px}
.hdr h1{color:#fff;font-size:17px;margin:0;font-weight:600}
.hdr span{color:#00d4ff}
.bod{padding:28px 32px}
.row{margin-bottom:18px}
.lbl{font-size:11px;font-weight:700;text-transform:uppercase;color:#888;letter-spacing:.06em;margin-bottom:4px}
.val{font-size:15px;color:#111}
.msg{background:#f4f4f5;border-radius:6px;padding:14px 16px;color:#333;white-space:pre-wrap;font-size:14px;line-height:1.6}
.ftr{padding:14px 32px;background:#f4f4f5;font-size:12px;color:#999;border-top:1px solid #e5e5e5}
a{color:#1A4DFF}
</style></head><body>
<div class="wrap">
<div class="hdr"><h1>DPowered<span>.</span>online &mdash; New Quote Request</h1></div>
<div class="bod">
<div class="row"><div class="lbl">Name</div><div class="val">' . esc_html($name) . '</div></div>
<div class="row"><div class="lbl">Email</div><div class="val"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></div></div>'
. ($phone    ? '<div class="row"><div class="lbl">Phone</div><div class="val"><a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone)) . '">' . esc_html($phone) . '</a></div></div>' : '')
. ($business ? '<div class="row"><div class="lbl">Business</div><div class="val">' . esc_html($business) . '</div></div>' : '')
. ($service  ? '<div class="row"><div class="lbl">Service</div><div class="val">' . esc_html($service)  . '</div></div>' : '') .
'<div class="row"><div class="lbl">Message</div><div class="msg">' . esc_html($message) . '</div></div>
</div>
<div class="ftr">Sent via the contact form on DPowered.online</div>
</div></body></html>';
    $headers = ["Content-Type: text/html; charset=UTF-8", "Reply-To: {$name} <{$email}>"];

    if (wp_mail($to, $subject, $body, $headers)) {
        wp_redirect(add_query_arg('sent', '1', $referer));
    } else {
        wp_redirect(add_query_arg('form_error', 'send', $referer));
    }
    exit;
}
add_action('template_redirect', 'dpowered_handle_contact_form');

// ── CUSTOMER REVIEW SUBMISSION ───────────────────────────────────────────────

function dpowered_handle_review_submission() {
    if (!isset($_POST['dpowered_review_submit'])) return;

    $referer = wp_get_referer() ?: home_url('/reviews');

    if (!isset($_POST['review_nonce']) || !wp_verify_nonce($_POST['review_nonce'], 'dpowered_submit_review')) {
        wp_redirect(add_query_arg('review_error', 'security', $referer));
        exit;
    }

    $name    = sanitize_text_field($_POST['reviewer_name'] ?? '');
    $company = sanitize_text_field($_POST['reviewer_company'] ?? '');
    $rating  = min(5, max(1, absint($_POST['reviewer_rating'] ?? 5)));
    $text    = sanitize_textarea_field($_POST['reviewer_text'] ?? '');

    if (empty($name) || empty($text)) {
        wp_redirect(add_query_arg('review_error', 'validation', $referer));
        exit;
    }

    $post_id = wp_insert_post([
        'post_title'   => $name,
        'post_content' => $text,
        'post_type'    => 'review',
        'post_status'  => 'pending',
    ]);

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_review_company', $company);
        update_post_meta($post_id, '_review_rating', $rating);

        wp_mail(
            'support@dpowered.online',
            "New Review from {$name} — Awaiting Approval",
            "A new review has been submitted and is waiting for your approval.\n\nFrom: {$name}" . ($company ? " ({$company})" : '') . "\nRating: {$rating}/5\n\nReview:\n{$text}\n\nApprove it here:\n" . admin_url('edit.php?post_status=pending&post_type=review'),
            ['Content-Type: text/plain; charset=UTF-8']
        );

        wp_redirect(add_query_arg('review_sent', '1', $referer));
    } else {
        wp_redirect(add_query_arg('review_error', 'save', $referer));
    }
    exit;
}
add_action('template_redirect', 'dpowered_handle_review_submission');
