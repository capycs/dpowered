<?php
/* Template Name: About Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
    </div>
    <div class="container inner-hero-content">
        <span class="section-tag">Our Story</span>
        <h1>About <span class="gradient-text">DPowered.online</span></h1>
        <p>Built on one simple idea — every business deserves a great website.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-intro reveal">
            <div class="about-text">
                <h2>Who We Are</h2>
                <p>DPowered.online is an independent web design agency focused on one thing — building websites that small and medium businesses are genuinely proud of. Professional quality, honest pricing, and none of the big-agency runaround.</p>
                <p>We work closely with every client because we care about getting it right. You deal directly with the person building your site, from first conversation to launch day and beyond.</p>
            </div>
            <div class="about-values">
                <div class="value-card">
                    <div class="value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <h4>Purpose-Driven</h4>
                    <p>Every decision we make is focused on what's best for your business.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg>
                    </div>
                    <h4>Creative</h4>
                    <p>We bring fresh ideas and modern design to every project.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h4>Reliable</h4>
                    <p>We deliver what we promise, on time, every time.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    </div>
                    <h4>Growth-Focused</h4>
                    <p>We build sites that don't just look good — they get results.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <div class="cta-orb"></div>
            <span class="section-tag">Ready To Start?</span>
            <h2>Let's Work Together</h2>
            <p>Ready to power up your online presence? We'd love to hear about your project.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Get in Touch</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View Services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
