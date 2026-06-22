<?php
/* Template Name: Portfolio Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <span class="section-tag">Our Work</span>
        <h1>Sites we've <span class="gradient-text">built &amp; launched</span></h1>
        <p>A look at some of the businesses we've helped get online and grow.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php
        $projects = new WP_Query([
            'post_type'      => 'project',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ]);

        if ($projects->have_posts()):
        ?>
        <div class="portfolio-grid">
            <?php while ($projects->have_posts()): $projects->the_post();
                $url      = get_post_meta(get_the_ID(), '_project_url', true);
                $client   = get_post_meta(get_the_ID(), '_project_client', true);
                $service  = get_post_meta(get_the_ID(), '_project_service', true);
                $year     = get_post_meta(get_the_ID(), '_project_year', true);
                $review   = get_post_meta(get_the_ID(), '_project_review', true);
                $rev_name = get_post_meta(get_the_ID(), '_project_reviewer_name', true);
                $rev_role = get_post_meta(get_the_ID(), '_project_reviewer_role', true);
                $rating   = get_post_meta(get_the_ID(), '_project_rating', true) ?: 5;
                $name     = get_the_title();
                $desc     = wp_strip_all_tags(get_the_content());
                $display_url = $url ? untrailingslashit(preg_replace('#^https?://#', '', $url)) : '';

                $name_parts = array_slice(explode(' ', trim($rev_name ?: $name)), 0, 2);
                $initials   = '';
                foreach ($name_parts as $part) {
                    if (!empty($part)) $initials .= strtoupper($part[0]);
                }
            ?>
            <article class="project-card reveal">
                <div class="project-screenshot">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large', ['alt' => esc_attr($name) . ' website screenshot', 'loading' => 'lazy']); ?>
                    <?php else: ?>
                        <div class="project-screenshot-placeholder" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        </div>
                    <?php endif; ?>
                    <?php if ($url): ?>
                    <a href="<?php echo esc_url($url); ?>" class="project-visit" target="_blank" rel="noopener noreferrer" aria-label="Visit <?php echo esc_attr($name); ?>">
                        Visit site
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="project-body">
                    <h3 class="project-name"><?php echo esc_html($name); ?></h3>
                    <?php if ($client || $service || $year): ?>
                    <div class="project-meta">
                        <?php if ($client): ?><span><?php echo esc_html($client); ?></span><?php endif; ?>
                        <?php if ($service): ?><span><?php echo esc_html($service); ?></span><?php endif; ?>
                        <?php if ($year): ?><span><?php echo esc_html($year); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($desc): ?>
                    <p class="project-desc"><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>
                    <?php if ($url): ?>
                    <a href="<?php echo esc_url($url); ?>" class="project-site-link" target="_blank" rel="noopener noreferrer" aria-label="Open <?php echo esc_attr($name); ?> website">
                        <span><?php echo esc_html($display_url); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                    </a>
                    <?php endif; ?>
                </div>

                <?php if ($review): ?>
                <div class="project-review">
                    <div class="testimonial-stars" aria-label="<?php echo esc_attr($rating); ?> out of 5 stars">
                        <?php for ($i = 0; $i < $rating; $i++): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">&ldquo;<?php echo esc_html($review); ?>&rdquo;</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><?php echo esc_html($initials ?: '?'); ?></div>
                        <div>
                            <?php if ($rev_name): ?><strong><?php echo esc_html($rev_name); ?></strong><?php endif; ?>
                            <?php if ($rev_role): ?><span><?php echo esc_html($rev_role); ?></span><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php else: ?>
        <div class="reviews-empty reveal">
            <div class="reviews-empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            </div>
            <h2>Portfolio coming soon</h2>
            <p>We're building up our portfolio, so check back soon to see the sites we've launched for our clients.</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Get a quote</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <span class="section-tag">Ready To Start?</span>
            <h2>Want a site like these?<br><span class="gradient-text">Let's talk</span></h2>
            <p>Get in touch today for a free, no-obligation quote and we'll get started.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Get a free quote</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
