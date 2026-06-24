<?php get_header(); ?>

<section class="v2-hero" id="main-content" aria-label="DPowered.online, web design agency">

    <div class="v2-hero-body">
        <div class="v2-hero-main">
            <div class="v2-hero-eyebrow" aria-hidden="true">
                <span class="v2-eyebrow-line"></span>
                <span>UK web design, <b>built to last</b></span>
            </div>
            <div class="v2-hero-display" aria-label="You don't need an agency. You need a website.">
                <div class="v2-hl v2-hl-1" aria-hidden="true">You don't need an</div>
                <div class="v2-hl v2-hl-2" aria-hidden="true">
                    <span class="v2-strike-wrap" id="agencyStrike">agency.<svg class="v2-strike-svg" viewBox="0 0 500 28" preserveAspectRatio="none" aria-hidden="true"><path class="v2-strike-path" d="M6,20 C60,6 160,24 280,14 C360,8 430,18 494,10"/></svg></span>
                </div>
                <div class="v2-hl v2-hl-3" aria-hidden="true">You need a</div>
                <div class="v2-hl v2-hl-4" aria-hidden="true">
                    <span class="v2-word-reveal v2-blue">website.</span>
                </div>
            </div>
        </div>

        <div class="v2-hero-side">
            <p class="v2-hero-sub">We're builders, not pitchers. Two weeks from kickoff to launch, fixed scope, fixed price, and real code you own.</p>

            <div class="v2-hero-feats">
                <?php
                $hero_feats = ['Live in two weeks, not two months', 'Real code you own, not rented', 'Edit it yourself, anytime'];
                foreach ($hero_feats as $feat):
                ?>
                <div class="v2-feat"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg><?php echo esc_html($feat); ?></div>
                <?php endforeach; ?>
            </div>

            <div class="v2-hero-ctas">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="v2-btn-primary mag-btn">
                    Get a free quote
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-btn-ghost">See what we do</a>
            </div>
        </div>
    </div>

</section>

<section class="v2-services-strip" aria-label="Our services">
    <div class="container">
        <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-svc-card mag-btn">
            <span class="v2-svc-num">01</span>
            <div class="v2-svc-body">
                <span class="v2-svc-roll"><span class="v2-svc-roll-track"><strong>New Websites</strong><em aria-hidden="true">New Websites</em></span></span>
                <span>Built from scratch, fast</span>
            </div>
            <svg class="v2-svc-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
        <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-svc-card mag-btn">
            <span class="v2-svc-num">02</span>
            <div class="v2-svc-body">
                <span class="v2-svc-roll"><span class="v2-svc-roll-track"><strong>Website Redesigns</strong><em aria-hidden="true">Website Redesigns</em></span></span>
                <span>Old site, new life</span>
            </div>
            <svg class="v2-svc-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
        <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-svc-card mag-btn">
            <span class="v2-svc-num">03</span>
            <div class="v2-svc-body">
                <span class="v2-svc-roll"><span class="v2-svc-roll-track"><strong>Monthly Care Plans</strong><em aria-hidden="true">Monthly Care Plans</em></span></span>
                <span>Updates &amp; maintenance</span>
            </div>
            <svg class="v2-svc-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
        <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-svc-card mag-btn">
            <span class="v2-svc-num">04</span>
            <div class="v2-svc-body">
                <span class="v2-svc-roll"><span class="v2-svc-roll-track"><strong>WordPress Training</strong><em aria-hidden="true">WordPress Training</em></span></span>
                <span>Edit it yourself</span>
            </div>
            <svg class="v2-svc-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
        </a>
    </div>
</section>

<section class="v2-stats-band" aria-label="Performance stats">
    <div class="container">
        <div class="v2-stats-grid">
            <div class="v2-stat">
                <div class="v2-stat-label">Turnaround</div>
                <div class="v2-stat-val"><span class="v2-stat-num">7–14</span><span class="v2-stat-unit"> days</span></div>
                <p class="v2-stat-desc">Brief to live in two weeks, not two months.</p>
            </div>
            <div class="v2-stat">
                <div class="v2-stat-label">Uptime</div>
                <div class="v2-stat-val"><span class="v2-stat-num">99.9</span><span class="v2-stat-unit">%</span></div>
                <p class="v2-stat-desc">Solid hosting that stays up when it matters.</p>
            </div>
            <div class="v2-stat">
                <div class="v2-stat-label">Page load</div>
                <div class="v2-stat-val"><span class="v2-stat-num">&lt;500</span><span class="v2-stat-unit">ms</span></div>
                <p class="v2-stat-desc">Lean builds that load fast on every device.</p>
            </div>
            <div class="v2-stat">
                <div class="v2-stat-label">SSL + CDN</div>
                <div class="v2-stat-val"><span class="v2-stat-num">100</span><span class="v2-stat-unit">%</span></div>
                <p class="v2-stat-desc">Secure padlock and speed on every build.</p>
            </div>
        </div>
    </div>
</section>

<section class="v2-process section">
    <div class="container">
        <div class="v2-process-header reveal">
            <span class="section-tag">How we work</span>
            <h2 class="split-headline">Simple process, <em>powerful</em> results.</h2>
        </div>
        <div class="v2-process-steps reveal-group">
            <div class="v2-process-step">
                <div class="v2-step-num">01</div>
                <div class="v2-step-content">
                    <h3>Discovery</h3>
                    <p>Tell us about your business, your goals, and your vision. We listen first and ask the right questions to understand exactly what you need.</p>
                </div>
            </div>
            <div class="v2-process-step">
                <div class="v2-step-num">02</div>
                <div class="v2-step-content">
                    <h3>Design &amp; Build</h3>
                    <p>We craft your site with attention to every detail: beautiful design, fast performance, and all the functionality your business requires.</p>
                </div>
            </div>
            <div class="v2-process-step">
                <div class="v2-step-num">03</div>
                <div class="v2-step-content">
                    <h3>Launch &amp; Support</h3>
                    <p>We get you live and stick around to make sure everything runs smoothly. You can also edit the site yourself, no tech skills needed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="v2-statement" aria-label="Less agency theatre. More working website.">
    <div class="v2-statement-line v2-statement-line--a">Less agency <em>theatre.</em></div>
    <div class="v2-statement-line v2-statement-line--b">More <em>working</em> website.</div>
</section>

<section class="v2-why section">
    <div class="container">
        <div class="v2-why-header reveal">
            <span class="section-tag">Why DPowered.online</span>
            <h2 class="split-headline">Six reasons businesses <em>choose</em> us.</h2>
        </div>
        <div class="v2-why-grid reveal-group">
            <div class="v2-why-card v2-why-card--featured">
                <div class="v2-why-glow" aria-hidden="true"></div>
                <div class="v2-why-num">01</div>
                <h3>Live in 7–14 days</h3>
                <p>No 6-week timelines or endless back-and-forth. We move fast. Most projects go from brief to live in under two weeks, without cutting a single corner.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="v2-why-link">Get a free quote →</a>
            </div>
            <div class="v2-why-card">
                <div class="v2-why-num">02</div>
                <h3>Perfect on every screen</h3>
                <p>Over 60% of web traffic is mobile. We design for phones first, so every visitor gets a smooth experience on a phone, tablet, or desktop.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-why-link">See how we build →</a>
            </div>
            <div class="v2-why-card">
                <div class="v2-why-num">03</div>
                <h3>Found on Google</h3>
                <p>Every site ships with SEO foundations built in: semantic markup, fast load times, Google Analytics, and on-page optimisation from day one.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-why-link">View our services →</a>
            </div>
            <div class="v2-why-card">
                <div class="v2-why-num">04</div>
                <h3>You stay in control</h3>
                <p>Your site runs on WordPress, so you can update text, images, and content yourself without needing us for every small change. We hand over the keys and walk you through it.</p>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-why-link">WordPress training →</a>
            </div>
            <div class="v2-why-card">
                <div class="v2-why-num">05</div>
                <h3>Support that doesn't vanish</h3>
                <p>We don't disappear after launch. Something broken? Need a change? Just want advice? We're a message away, and we actually respond quickly.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="v2-why-link">Talk to us →</a>
            </div>
            <div class="v2-why-card v2-why-card--wide">
                <div class="v2-why-num">06</div>
                <h3>Prices you can plan around</h3>
                <p>Fixed project quotes, no hidden fees, no scope creep surprises. You know the full cost before a deposit, not after months of back-and-forth.</p>
                <a href="<?php echo esc_url(home_url('/pricing')); ?>" class="v2-why-link">See our pricing →</a>
            </div>
        </div>
    </div>
</section>

<?php
$reviews = new WP_Query(['post_type' => 'review', 'posts_per_page' => 3, 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'publish']);
if ($reviews->have_posts()):
?>
<section class="v2-reviews section">
    <div class="container">
        <div class="v2-reviews-header reveal">
            <div>
                <h2>What our clients<br>are <em>saying.</em></h2>
            </div>
            <a href="<?php echo esc_url(home_url('/reviews')); ?>" class="v2-all-reviews-link">See all reviews →</a>
        </div>
        <div class="v2-reviews-grid">
            <?php while ($reviews->have_posts()): $reviews->the_post();
                $company    = get_post_meta(get_the_ID(), '_review_company', true);
                $rating     = get_post_meta(get_the_ID(), '_review_rating', true) ?: 5;
                $name       = get_the_title();
                $name_parts = array_slice(explode(' ', trim($name)), 0, 2);
                $initials   = '';
                foreach ($name_parts as $part) { if (!empty($part)) $initials .= strtoupper($part[0]); }
            ?>
            <div class="v2-review-card reveal">
                <div class="v2-review-stars" aria-label="<?php echo esc_attr($rating); ?> out of 5 stars">
                    <?php for ($i = 0; $i < $rating; $i++): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="v2-review-text">&ldquo;<?php echo esc_html(wp_strip_all_tags(get_the_content())); ?>&rdquo;</p>
                <div class="v2-review-author">
                    <div class="v2-review-avatar" aria-hidden="true"><?php echo esc_html($initials); ?></div>
                    <div>
                        <strong><?php echo esc_html($name); ?></strong>
                        <?php if ($company): ?><span><?php echo esc_html($company); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="v2-faq section">
    <div class="container">
        <div class="v2-faq-inner">
            <div class="v2-faq-header reveal">
                <span class="section-tag">FAQ</span>
                <h2>Common <em>questions.</em></h2>
                <p>Everything you need to know before getting started.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="v2-btn-primary" style="display:inline-flex;margin-top:24px">Ask us anything →</a>
            </div>
            <div class="v2-faq-list">
                <?php
                $faqs = [
                    ['How much does a website cost?',
                     'Every project is different, so we tailor our pricing to your specific needs. We work with a range of budgets and always aim to give you the most value for your money. Get in touch for a free, no-obligation quote.'],
                    ['How long does it take to build a website?',
                     'Most projects are completed within 7 to 14 days from the point we have everything we need from you: content, images, and any brand assets. Larger projects like e-commerce stores may take a little longer.'],
                    ['Can I update the website myself after launch?',
                     'Absolutely. Every site we build runs on WordPress, which means you can log in and update text, images, and content yourself with no coding skills needed. We also walk you through how to use it at handover.'],
                    ['Do you offer support after the site goes live?',
                     'Yes. We don\'t just disappear after launch. We offer free ongoing support for any issues that arise, and we\'re always available if you need changes, updates, or just have a question.'],
                    ['What do I need to provide to get started?',
                     'Ideally: your logo, the text for your pages, any photos you want to use, and an idea of what you like. Don\'t worry if you\'re not sure, we guide you through everything.'],
                    ['How do I pay for my website?',
                     'We ask for a 50% deposit to get started, with the remaining 50% due when the site is ready to launch. We accept all major debit and credit cards via a secure payment link.'],
                ];
                foreach ($faqs as $i => [$q, $a]):
                ?>
                <div class="v2-faq-item reveal">
                    <button class="v2-faq-question" aria-expanded="false" aria-controls="v2-faq-<?php echo $i; ?>">
                        <span><?php echo esc_html($q); ?></span>
                        <svg class="v2-faq-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <div class="v2-faq-answer" id="v2-faq-<?php echo $i; ?>" role="region">
                        <p><?php echo esc_html($a); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="v2-cta-section">
    <div class="v2-cta-aurora" aria-hidden="true"></div>
    <div class="container">
        <div class="v2-cta-inner reveal">
            <h2>Let's build something<br><em>worth talking about.</em></h2>
            <p>Get in touch today and let's talk about how we can help your business grow online.</p>
            <div class="v2-cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="v2-btn-primary v2-btn-lg mag-btn">Get a Free Quote</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="v2-btn-outline-dark v2-btn-lg">View Services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
