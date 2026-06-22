<?php
/* Template Name: Reviews Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <span class="section-tag">Client Stories</span>
        <h1>What our clients <span class="gradient-text">say about us</span></h1>
        <p>Real feedback from real businesses we've helped get online.</p>
    </div>
</section>

<section class="section testimonials-section">
    <div class="container">
        <?php
        $reviews = new WP_Query([
            'post_type'      => 'review',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ]);

        if ($reviews->have_posts()):
        ?>
        <div class="testimonials-grid">
            <?php while ($reviews->have_posts()): $reviews->the_post();
                $company    = get_post_meta(get_the_ID(), '_review_company', true);
                $rating     = get_post_meta(get_the_ID(), '_review_rating', true) ?: 5;
                $name       = get_the_title();
                $name_parts = array_slice(explode(' ', trim($name)), 0, 2);
                $initials   = '';
                foreach ($name_parts as $part) {
                    if (!empty($part)) $initials .= strtoupper($part[0]);
                }
            ?>
            <div class="testimonial-card reveal">
                <div class="testimonial-stars" aria-label="<?php echo esc_attr($rating); ?> out of 5 stars">
                    <?php for ($i = 0; $i < $rating; $i++): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text">&ldquo;<?php echo esc_html(wp_strip_all_tags(get_the_content())); ?>&rdquo;</p>
                <div class="testimonial-author">
                    <div class="author-avatar" aria-hidden="true"><?php echo esc_html($initials); ?></div>
                    <div>
                        <strong><?php echo esc_html($name); ?></strong>
                        <?php if ($company): ?><span><?php echo esc_html($company); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php else: ?>
        <div class="reviews-empty reveal">
            <div class="reviews-empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <h2>Reviews coming soon</h2>
            <p>We're just getting started, and client reviews will appear here as our portfolio grows. In the meantime, get in touch and let's talk about your project.</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Get a quote</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section review-submit-section" style="background:var(--bg-alt)">
    <div class="container">
        <div class="review-submit-inner">

            <div class="review-submit-header reveal">
                <span class="section-tag">Share Your Experience</span>
                <h2>Leave a review</h2>
                <p>Had a great experience with DPowered.online? We'd love to hear from you, and so would future clients.</p>
            </div>

            <?php if (isset($_GET['review_sent']) && $_GET['review_sent'] === '1'): ?>
            <div class="review-success reveal">
                <div class="review-success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h3>Thank you!</h3>
                <p>Your review has been received. We'll publish it shortly after a quick check, and we really appreciate you taking the time.</p>
            </div>
            <?php else: ?>
            <form class="review-form reveal" method="post" action="">
                <?php wp_nonce_field('dpowered_submit_review', 'review_nonce'); ?>
                <input type="hidden" name="review_time" value="<?php echo esc_attr(time()); ?>">

                <div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
                    <label for="review_website">Website (leave blank)</label>
                    <input type="text" id="review_website" name="review_website" value="" autocomplete="off" tabindex="-1">
                </div>

                <?php if (isset($_GET['review_error'])): ?>
                <div class="form-notice form-notice--error">
                    Something went wrong. Please check your details and try again, or <a href="<?php echo esc_url(home_url('/contact')); ?>">contact us directly</a>.
                </div>
                <?php endif; ?>

                <div class="review-form-grid">
                    <div class="form-group">
                        <label for="reviewer_name">Your Name <span aria-hidden="true">*</span></label>
                        <input type="text" id="reviewer_name" name="reviewer_name" required placeholder="Sarah Khan" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="reviewer_company">Business / Role <span class="form-optional">(optional)</span></label>
                        <input type="text" id="reviewer_company" name="reviewer_company" placeholder="Owner, Smith's Plumbing" autocomplete="organization">
                    </div>
                </div>

                <div class="form-group">
                    <label>Your Rating <span aria-hidden="true">*</span></label>
                    <div class="star-rating-input" role="radiogroup" aria-label="Star rating">
                        <input type="radio" id="star5" name="reviewer_rating" value="5" checked>
                        <label for="star5" title="5 stars">★</label>
                        <input type="radio" id="star4" name="reviewer_rating" value="4">
                        <label for="star4" title="4 stars">★</label>
                        <input type="radio" id="star3" name="reviewer_rating" value="3">
                        <label for="star3" title="3 stars">★</label>
                        <input type="radio" id="star2" name="reviewer_rating" value="2">
                        <label for="star2" title="2 stars">★</label>
                        <input type="radio" id="star1" name="reviewer_rating" value="1">
                        <label for="star1" title="1 star">★</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reviewer_text">Your Review <span aria-hidden="true">*</span></label>
                    <textarea id="reviewer_text" name="reviewer_text" rows="5" required placeholder="Tell us about your experience: what did we build for you, and how has it helped your business?"></textarea>
                </div>

                <button type="submit" name="dpowered_review_submit" value="1" class="btn btn-primary btn-lg">
                    Submit Review
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <p class="review-form-note">Reviews are checked before publishing, usually within 24 hours.</p>
            </form>
            <?php endif; ?>

        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <span class="section-tag">Ready To Start?</span>
            <h2>Let's build something<br><span class="gradient-text">amazing together</span></h2>
            <p>Join our growing list of happy clients. Get in touch for a free, no-obligation quote.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Get a free quote</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
