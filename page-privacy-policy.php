<?php
/* Template Name: Privacy Policy */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <span class="section-tag">Legal</span>
        <h1>Privacy <span class="gradient-text">Policy</span></h1>
        <p>How we collect, use, and protect your personal information.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="privacy-content reveal">

            <p class="privacy-updated">Last updated: <?php echo date('F Y'); ?></p>

            <h2>Who We Are</h2>
            <p>DPowered.online is a web design service based in Liverpool, UK. Our website address is <strong>https://dpowered.online</strong>. You can contact us at <a href="mailto:support@dpowered.online">support@dpowered.online</a>.</p>

            <h2>What Data We Collect</h2>
            <p>We collect information you voluntarily provide when using our contact form or review form, including:</p>
            <ul>
                <li>Your name</li>
                <li>Email address</li>
                <li>Phone number (optional)</li>
                <li>Business name (optional)</li>
                <li>Message content</li>
            </ul>
            <p>We do not collect payment information directly. Any payment processing uses a third-party provider.</p>

            <h2>How We Use Your Data</h2>
            <p>Information submitted through our contact form is used solely to respond to your enquiry. We do not sell, rent, or share your personal data with third parties for marketing purposes.</p>
            <p>Review submissions may be published on our website with your name and business/role as provided. We will contact you before publishing.</p>

            <h2>Cookies</h2>
            <p>Our website uses minimal cookies, mainly those set by WordPress for core functionality and security. We do not use tracking or advertising cookies. If you complete a contact form, WordPress may set a session cookie.</p>

            <h2>Third-Party Services</h2>
            <p>We use Google Fonts to serve typography. Google may collect anonymised data about font requests. We also use Google Analytics on some pages to understand visitor behaviour. This is anonymised and does not identify individuals.</p>

            <h2>Data Retention</h2>
            <p>Contact form submissions are retained in our email inbox for as long as reasonably necessary to fulfil your enquiry. You may request deletion at any time by emailing <a href="mailto:support@dpowered.online">support@dpowered.online</a>.</p>

            <h2>Your Rights</h2>
            <p>Under UK GDPR, you have the right to access, correct, or delete personal data we hold about you. To make a request, email us at <a href="mailto:support@dpowered.online">support@dpowered.online</a>. We will respond within 30 days.</p>

            <h2>Changes to This Policy</h2>
            <p>We may update this policy from time to time. Changes will be posted on this page with an updated date. Continued use of our website after changes are posted constitutes acceptance of the revised policy.</p>

            <h2>Contact Us</h2>
            <p>If you have any questions about this privacy policy, please contact us:</p>
            <p><strong>Email:</strong> <a href="mailto:support@dpowered.online">support@dpowered.online</a><br>
            <strong>Location:</strong> Liverpool, UK</p>

            <div class="privacy-optin" id="optin">
                <h2>Happy to be contacted?</h2>
                <p>If you'd like us to reach out about a website, tick the box and leave your details. We'll only use them to get in touch — nothing else.</p>

                <?php if (isset($_GET['optin'])): ?>
                <div class="form-notice form-success" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <p><strong>Thanks!</strong> We've got your details and will be in touch.</p>
                </div>
                <?php else: ?>
                    <?php if (isset($_GET['optin_error'])): ?>
                    <div class="form-notice form-error" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <p>
                            <?php
                            $oerr = $_GET['optin_error'];
                            if ($oerr === 'consent') {
                                echo '<strong>Please tick the box</strong> to confirm you\'re happy for us to contact you.';
                            } elseif ($oerr === 'validation') {
                                echo '<strong>Please add a contact.</strong> Leave a valid email or a phone number so we can reach you.';
                            } else {
                                echo '<strong>Security check failed.</strong> Please refresh the page and try again.';
                            }
                            ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <form class="privacy-optin-form" method="post" action="#optin">
                        <?php wp_nonce_field('dpowered_privacy', 'privacy_nonce'); ?>
                        <input type="hidden" name="dpowered_privacy_submit" value="1">
                        <input type="hidden" name="privacy_time" value="<?php echo esc_attr(time()); ?>">

                        <div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
                            <label for="privacy_website">Website (leave blank)</label>
                            <input type="text" id="privacy_website" name="privacy_website" value="" autocomplete="off" tabindex="-1">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="privacy-email">Email Address</label>
                                <input type="email" id="privacy-email" name="privacy_email" placeholder="you@yourbusiness.com" autocomplete="email">
                            </div>
                            <div class="form-group">
                                <label for="privacy-phone">Phone Number <span style="font-weight:400;color:var(--ink-2);font-size:0.82rem">(optional)</span></label>
                                <input type="tel" id="privacy-phone" name="privacy_phone" placeholder="07123 456789" autocomplete="tel" inputmode="tel">
                            </div>
                        </div>

                        <label class="privacy-consent">
                            <input type="checkbox" name="privacy_consent" value="1" required>
                            <span class="privacy-consent-box" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span class="privacy-consent-text">I agree to DPowered storing the details above so they can contact me about my enquiry.</span>
                        </label>

                        <button type="submit" class="btn btn-primary">Agree &amp; send my details &rarr;</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<style>
.privacy-optin{margin-top:3.5rem;padding:2.25rem;border:1px solid var(--line,#e5e5e5);border-radius:16px;background:var(--surface-2,#fafafa)}
.privacy-optin h2{margin-top:0}
.privacy-optin-form{margin-top:1.5rem}
.privacy-consent{display:flex;align-items:flex-start;gap:.75rem;cursor:pointer;margin:.25rem 0 1.5rem;line-height:1.5}
.privacy-consent input{position:absolute;opacity:0;width:0;height:0}
.privacy-consent-box{flex:0 0 auto;width:22px;height:22px;margin-top:1px;border:1.5px solid var(--line,#cfcfcf);border-radius:6px;background:#fff;display:flex;align-items:center;justify-content:center;transition:background .15s,border-color .15s}
.privacy-consent-box svg{opacity:0;transform:scale(.6);transition:opacity .15s,transform .15s}
.privacy-consent input:checked + .privacy-consent-box{background:var(--accent,#1A4DFF);border-color:var(--accent,#1A4DFF)}
.privacy-consent input:checked + .privacy-consent-box svg{opacity:1;transform:scale(1)}
.privacy-consent input:focus-visible + .privacy-consent-box{outline:2px solid var(--accent,#1A4DFF);outline-offset:2px}
.privacy-consent-text{font-size:.95rem;color:var(--ink-2,#555)}
</style>

<?php get_footer(); ?>
