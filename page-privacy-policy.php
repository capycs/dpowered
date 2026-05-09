<?php
/* Template Name: Privacy Policy */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
    </div>
    <div class="container inner-hero-content">
        <span class="section-tag">Legal</span>
        <h1>Privacy <span class="gradient-text">Policy</span></h1>
        <p>How we collect, use, and protect your personal information.</p>
    </div>
</section>

<section class="section privacy-summary-section">
    <div class="container">
        <div class="privacy-summary reveal">
            <div>
                <span class="section-tag">Plain English Version</span>
                <h2>Your details are used to reply, quote, deliver work, and process payments securely.</h2>
            </div>
            <div class="privacy-summary-points">
                <p><strong>We do not sell your data.</strong> We only use it to handle enquiries, projects, support, accounting, and site improvement.</p>
                <p><strong>Card payments stay with Stripe.</strong> We never store your full card number, CVC, or expiry date.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="privacy-layout">
            <aside class="privacy-nav reveal">
                <p class="privacy-nav-label">On this page</p>
                <ul>
                    <li><a href="#who-we-are">Who we are</a></li>
                    <li><a href="#data-we-collect">Data we collect</a></li>
                    <li><a href="#payments">Payments &amp; Stripe</a></li>
                    <li><a href="#how-we-use">How we use your data</a></li>
                    <li><a href="#data-sharing">Data sharing</a></li>
                    <li><a href="#retention">Data retention</a></li>
                    <li><a href="#your-rights">Your rights</a></li>
                    <li><a href="#cookies">Cookies</a></li>
                    <li><a href="#contact">Contact us</a></li>
                </ul>
            </aside>

            <div class="privacy-content reveal">
                <p class="privacy-updated">Last updated: <?php echo date('F j, Y'); ?></p>

                <div class="privacy-notice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <p>DPowered.online is committed to protecting your privacy in line with the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018.</p>
                </div>

                <section id="who-we-are" class="privacy-section">
                    <h2>Who we are</h2>
                    <p>DPowered.online is a web design and development service based in the United Kingdom. We are the data controller for personal information collected through this website and during the delivery of our services.</p>
                    <p>You can contact us at <a href="mailto:support@dpowered.online">support@dpowered.online</a> or by phone on <a href="tel:07359240068">07359 240068</a>.</p>
                </section>

                <section id="data-we-collect" class="privacy-section">
                    <h2>Data we collect</h2>
                    <p>We collect personal information in the following ways:</p>
                    <ul class="privacy-list">
                        <li><strong>Contact form</strong> — your name, email address, business name, and project details when you submit an enquiry.</li>
                        <li><strong>Payment</strong> — when you pay for a service, payment is processed directly by Stripe. We do not store card numbers or sensitive payment data on our servers (see the Payments &amp; Stripe section below).</li>
                        <li><strong>Communications</strong> — emails, messages, or other correspondence you send us.</li>
                        <li><strong>Website usage</strong> — basic analytics data (pages visited, approximate location, device type) to help us improve the site. This data is aggregated and not linked to you personally.</li>
                    </ul>
                </section>

                <section id="payments" class="privacy-section">
                    <h2>Payments &amp; Stripe</h2>
                    <div class="privacy-stripe-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Payments processed securely by Stripe
                    </div>
                    <p>All payment transactions are handled by <strong>Stripe, Inc.</strong>, a PCI DSS Level 1 certified payment processor — the highest level of certification available in the payments industry.</p>
                    <p><strong>What this means for you:</strong></p>
                    <ul class="privacy-list">
                        <li>Your card number, CVC, and expiry date are entered directly into Stripe's secure payment form. This data goes straight to Stripe and is never seen or stored by DPowered.online.</li>
                        <li>Stripe uses industry-standard TLS encryption and tokenises your card details so they are never transmitted in plain text.</li>
                        <li>We receive only a payment confirmation and a masked card reference (e.g. Visa ending 4242) from Stripe — never your full card details.</li>
                    </ul>
                    <p><strong>Data Stripe holds:</strong> Stripe stores your payment method details and transaction history as required to process payments and meet their legal obligations. Stripe acts as an independent data controller for payment data. You can read Stripe's privacy policy at <a href="https://stripe.com/gb/privacy" target="_blank" rel="noopener noreferrer">stripe.com/gb/privacy</a>.</p>
                    <p><strong>What we retain:</strong> We keep a record of your name, email address, invoice amount, and payment status for accounting and service delivery purposes. We do not retain any card details.</p>
                </section>

                <section id="how-we-use" class="privacy-section">
                    <h2>How we use your data</h2>
                    <p>We use your personal information for the following purposes:</p>
                    <div class="privacy-purposes">
                        <div class="privacy-purpose-item">
                            <div class="privacy-purpose-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <strong>Responding to enquiries</strong>
                                <p>To reply to your contact form submission and provide a quote.</p>
                            </div>
                        </div>
                        <div class="privacy-purpose-item">
                            <div class="privacy-purpose-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <strong>Delivering services</strong>
                                <p>To build and manage your website project and provide ongoing support.</p>
                            </div>
                        </div>
                        <div class="privacy-purpose-item">
                            <div class="privacy-purpose-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <strong>Processing payments</strong>
                                <p>To send payment requests, issue invoices, and maintain financial records as required by UK law.</p>
                            </div>
                        </div>
                        <div class="privacy-purpose-item">
                            <div class="privacy-purpose-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <strong>Legal obligations</strong>
                                <p>To comply with tax, accounting, and other legal requirements.</p>
                            </div>
                        </div>
                    </div>
                    <p>We will not use your data for marketing purposes unless you have explicitly opted in, and we will never sell your data to third parties.</p>
                </section>

                <section id="data-sharing" class="privacy-section">
                    <h2>Data sharing</h2>
                    <p>We only share your personal information with third parties where necessary:</p>
                    <ul class="privacy-list">
                        <li><strong>Stripe</strong> — to process payments securely.</li>
                        <li><strong>Email providers</strong> — contact form submissions are delivered to us via email. Our email provider may process your data as part of this delivery.</li>
                        <li><strong>Hosting providers</strong> — our website and any client websites are hosted on servers that may process data on our behalf.</li>
                        <li><strong>Legal authorities</strong> — if required by law or court order.</li>
                    </ul>
                    <p>All third-party processors are selected to ensure appropriate safeguards are in place for your data.</p>
                </section>

                <section id="retention" class="privacy-section">
                    <h2>Data retention</h2>
                    <p>We retain your personal data only for as long as necessary:</p>
                    <ul class="privacy-list">
                        <li><strong>Contact enquiries</strong> — retained for up to 2 years, or until we have responded and the matter is resolved.</li>
                        <li><strong>Client project records</strong> — retained for the duration of the working relationship, plus up to 7 years for accounting purposes (as required by HMRC).</li>
                        <li><strong>Payment records</strong> — retained for 7 years in line with UK tax law.</li>
                    </ul>
                    <p>After the applicable retention period, data is securely deleted or anonymised.</p>
                </section>

                <section id="your-rights" class="privacy-section">
                    <h2>Your rights</h2>
                    <p>Under the UK GDPR you have the following rights regarding your personal data:</p>
                    <div class="privacy-rights-grid">
                        <div class="privacy-right">
                            <strong>Right of access</strong>
                            <p>Request a copy of the personal data we hold about you.</p>
                        </div>
                        <div class="privacy-right">
                            <strong>Right to rectification</strong>
                            <p>Ask us to correct any inaccurate or incomplete information.</p>
                        </div>
                        <div class="privacy-right">
                            <strong>Right to erasure</strong>
                            <p>Request deletion of your personal data where there is no legitimate reason to continue holding it.</p>
                        </div>
                        <div class="privacy-right">
                            <strong>Right to restrict processing</strong>
                            <p>Ask us to limit how we use your data in certain circumstances.</p>
                        </div>
                        <div class="privacy-right">
                            <strong>Right to data portability</strong>
                            <p>Receive your data in a structured, machine-readable format.</p>
                        </div>
                        <div class="privacy-right">
                            <strong>Right to object</strong>
                            <p>Object to processing of your data for direct marketing or where we rely on legitimate interests.</p>
                        </div>
                    </div>
                    <p>To exercise any of these rights, contact us at <a href="mailto:support@dpowered.online">support@dpowered.online</a>. We will respond within 30 days. If you are not satisfied with our response, you have the right to complain to the Information Commissioner's Office (ICO) at <a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer">ico.org.uk</a>.</p>
                </section>

                <section id="cookies" class="privacy-section">
                    <h2>Cookies</h2>
                    <p>Our website uses only essential cookies required for the site to function (such as WordPress session cookies if you log in to our client portal). We do not use advertising or tracking cookies.</p>
                    <p>You can control cookie settings in your browser preferences. Disabling cookies may affect the functionality of certain parts of the site.</p>
                </section>

                <section id="contact" class="privacy-section">
                    <h2>Contact us</h2>
                    <p>If you have any questions about this privacy policy or how we handle your data, please get in touch:</p>
                    <div class="privacy-contact-block">
                        <div class="contact-point">
                            <div class="contact-point-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div>
                                <strong>Email</strong>
                                <span><a href="mailto:support@dpowered.online">support@dpowered.online</a></span>
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
                    </div>
                    <p>We aim to respond to all data-related requests within 30 days.</p>
                </section>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
