<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
    global $post;
    $site_name = get_bloginfo('name') ?: 'DPowered.online';
    $sep       = ' — ';

    if (is_front_page()) {
        $meta_title = $site_name . $sep . 'Web Design Agency UK';
        $meta_desc  = 'DPowered.online builds modern, affordable websites for small businesses across the UK. Professional web design with a 7–14 day turnaround. Get a free quote today.';
    } elseif (is_page('portfolio') || is_page('our-work')) {
        $meta_title = 'Portfolio' . $sep . $site_name;
        $meta_desc  = 'Browse websites built and launched by DPowered.online for small businesses across the UK.';
    } elseif (is_page('pricing')) {
        $meta_title = 'Pricing' . $sep . $site_name;
        $meta_desc  = 'Simple, transparent website pricing from £399. One-off builds and monthly care plans — no hidden fees.';
    } elseif (is_page('reviews')) {
        $meta_title = 'Client Reviews' . $sep . $site_name;
        $meta_desc  = 'Real reviews from businesses that DPowered.online has helped get online and grow.';
    } elseif (is_page('contact')) {
        $meta_title = 'Get a Free Quote' . $sep . $site_name;
        $meta_desc  = 'Contact DPowered.online for a free, no-obligation web design quote. We typically respond within 24 hours.';
    } elseif (is_singular() && !empty($post)) {
        $meta_title  = get_the_title($post) . $sep . $site_name;
        $meta_desc   = has_excerpt($post) ? strip_tags(get_the_excerpt($post)) : 'DPowered.online — professional web design for small businesses.';
    } else {
        $raw_title  = wp_title('', false);
        $meta_title = ($raw_title ? trim($raw_title) . $sep : '') . $site_name;
        $meta_desc  = 'DPowered.online — modern, affordable web design for small businesses across the UK.';
    }

    $og_image = get_template_directory_uri() . '/assets/images/favicon.svg';
    if (!empty($post) && has_post_thumbnail($post)) {
        $og_image = get_the_post_thumbnail_url($post, 'large');
    }
    $canonical = is_singular() && !empty($post) ? get_permalink($post) : home_url(add_query_arg([]));
    ?>
    <title><?php echo esc_html($meta_title); ?></title>
    <meta name="description" content="<?php echo esc_attr($meta_desc); ?>">
    <link rel="canonical" href="<?php echo esc_url($canonical); ?>">
    <meta property="og:title"       content="<?php echo esc_attr($meta_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($meta_desc); ?>">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?php echo esc_url($canonical); ?>">
    <meta property="og:site_name"   content="<?php echo esc_attr($site_name); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?php echo esc_attr($meta_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($meta_desc); ?>">
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/favicon.svg" type="image/svg+xml">
    <meta name="theme-color" content="#060612">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="celestial-bg" aria-hidden="true"></div>

<div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

<a href="#main-content" class="skip-link">Skip to main content</a>

<header class="site-header" id="site-header">
    <nav class="nav-container" aria-label="Main">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-logo" aria-label="DPowered.online — Home">
            <svg width="36" viewBox="0 0 200 220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <defs>
                    <linearGradient id="dp-grad-h" x1="0" y1="0" x2="0.7" y2="1">
                        <stop offset="0%"   stop-color="#1A4DFF"/>
                        <stop offset="100%" stop-color="#06133D"/>
                    </linearGradient>
                </defs>
                <path fill="url(#dp-grad-h)" fill-rule="evenodd" d="M 16 16 L 100 16 A 84 84 0 0 1 100 184 L 16 184 Z"/>
                <g fill="#060612" fill-rule="evenodd">
                    <path d="M 56 50 L 100 50 A 36 36 0 0 1 100 122 L 80 122 L 80 210 L 56 210 Z M 80 72 L 100 72 A 14 14 0 0 1 100 100 L 80 100 Z"/>
                    <rect x="0" y="142" width="200" height="6"/>
                </g>
            </svg>
            <span>DPowered<span class="logo-dot">.</span>online</span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
            <li><a href="<?php echo esc_url(home_url('/portfolio')); ?>">Portfolio</a></li>
            <li><a href="<?php echo esc_url(home_url('/reviews')); ?>">Reviews</a></li>
            <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
            <li><a href="<?php echo esc_url(home_url('/pricing')); ?>">Pricing</a></li>
            <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="nav-cta">Get a Quote</a></li>
        </ul>
    </nav>
</header>

<div id="navOverlay" class="nav-overlay" aria-hidden="true"></div>

<a href="<?php echo esc_url(home_url('/contact')); ?>" class="sticky-cta" id="stickyCta" aria-label="Get a free quote">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <span>Get a Quote</span>
</a>
