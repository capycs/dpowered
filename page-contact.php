<?php
/* Template Name: Contact Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
    </div>
    <div class="container inner-hero-content">
        <span class="section-tag">Let's Talk</span>
        <h1>Get a <span class="gradient-text">Free Quote</span></h1>
        <p>Tell us about your project and we'll get back to you as soon as possible.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info reveal">
                <h2>Get In Touch</h2>
                <p>Whether you need a new website, a redesign, or ongoing help with your existing site — we're here. Fill in the form and we'll get back to you quickly with a free, no-obligation quote.</p>
                <div class="contact-points">
                    <div class="contact-point">
                        <div class="contact-point-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <strong>Email</strong>
                            <span>support@dpowered.online</span>
                        </div>
                    </div>
                    <div class="contact-point">
                        <div class="contact-point-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                        </div>
                        <div>
                            <strong>Phone</strong>
                            <span><a href="tel:07359240068">07359 240068</a></span>
                        </div>
                    </div>
                    <div class="contact-point">
                        <div class="contact-point-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <strong>Response Time</strong>
                            <span>Usually within 24 hours</span>
                        </div>
                    </div>
                    <div class="contact-point">
                        <div class="contact-point-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <strong>Location</strong>
                            <span>UK — Working Remotely</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contact-form-wrap reveal">
                <?php if (isset($_GET['sent'])): ?>
                <div class="form-notice form-success" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <p><strong>Message sent!</strong> We'll get back to you within 24 hours.</p>
                </div>
                <?php elseif (isset($_GET['form_error'])): ?>
                <div class="form-notice form-error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p>
                        <?php
                        $err = $_GET['form_error'];
                        if ($err === 'validation') {
                            echo '<strong>Please check your details.</strong> Name, a valid email, and a message are required.';
                        } elseif ($err === 'send') {
                            echo '<strong>Something went wrong.</strong> Please email us directly at <a href="mailto:support@dpowered.online">support@dpowered.online</a>.';
                        } else {
                            echo '<strong>Security check failed.</strong> Please refresh the page and try again.';
                        }
                        ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if (!isset($_GET['sent'])): ?>
                <form class="contact-form" method="post" action="">
                    <?php wp_nonce_field('dpowered_contact', 'contact_nonce'); ?>
                    <input type="hidden" name="dpowered_contact_submit" value="1">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact-name">Your Name <span aria-hidden="true">*</span></label>
                            <input type="text" id="contact-name" name="contact_name" placeholder="John Smith" autocomplete="name" required aria-required="true">
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email Address <span aria-hidden="true">*</span></label>
                            <input type="email" id="contact-email" name="contact_email" placeholder="john@yourcompany.com" autocomplete="email" required aria-required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact-business">Business Name</label>
                        <input type="text" id="contact-business" name="contact_business" placeholder="Your Business Ltd" autocomplete="organization">
                    </div>
                    <div class="form-group">
                        <label>What are you interested in? <span style="font-weight:400;color:var(--text-muted);font-size:0.82rem">(select all that apply)</span></label>
                        <div class="pkg-picker" role="group" aria-label="Select packages">
                            <input type="hidden" id="contact-service" name="contact_service" value="">

                            <p class="pkg-group-label">Website Builds</p>
                            <div class="pkg-grid pkg-grid--3">
                                <button type="button" class="pkg-card" data-value="launch">
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Launch</span>
                                    <span class="pkg-price">£399</span>
                                    <span class="pkg-detail">Up to 5 pages</span>
                                </button>
                                <button type="button" class="pkg-card" data-value="growth-site">
                                    <span class="pkg-badge-sm">Popular</span>
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Growth</span>
                                    <span class="pkg-price">£599</span>
                                    <span class="pkg-detail">Up to 8 pages</span>
                                </button>
                                <button type="button" class="pkg-card" data-value="pro">
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Pro</span>
                                    <span class="pkg-price">£1,199</span>
                                    <span class="pkg-detail">Unlimited pages</span>
                                </button>
                            </div>

                            <p class="pkg-group-label">Monthly Plans</p>
                            <div class="pkg-grid pkg-grid--2">
                                <button type="button" class="pkg-card" data-value="basic-maintenance">
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Basic Maintenance</span>
                                    <span class="pkg-price">£39<span class="pkg-per">/mo</span></span>
                                    <span class="pkg-detail">Security &amp; updates</span>
                                </button>
                                <button type="button" class="pkg-card" data-value="growth-plan">
                                    <span class="pkg-badge-sm">Best Value</span>
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Growth Plan</span>
                                    <span class="pkg-price">£89<span class="pkg-per">/mo</span></span>
                                    <span class="pkg-detail">Content + SEO monthly</span>
                                </button>
                            </div>

                            <p class="pkg-group-label">All-In-One</p>
                            <div class="pkg-grid pkg-grid--2">
                                <button type="button" class="pkg-card" data-value="bundle">
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">The Bundle</span>
                                    <span class="pkg-price">£399 <span style="color:var(--text-muted);font-size:0.85rem;font-weight:500">+</span> £49<span class="pkg-per">/mo</span></span>
                                    <span class="pkg-detail">Build + hosting + support</span>
                                </button>
                                <button type="button" class="pkg-card" data-value="not-sure">
                                    <span class="pkg-check" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <span class="pkg-name">Not Sure</span>
                                    <span class="pkg-price" style="font-size:0.9rem;color:var(--text-secondary)">Help me decide</span>
                                    <span class="pkg-detail">We'll find the best fit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Tell us about your project <span aria-hidden="true">*</span></label>
                        <textarea id="contact-message" name="contact_message" rows="5" placeholder="Describe what you're looking for, any deadlines, budget range, etc." required aria-required="true"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-full">Send Message →</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
