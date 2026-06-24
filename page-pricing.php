<?php
/* Template Name: Pricing Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="container inner-hero-content">
        <span class="section-tag">Transparent Pricing</span>
        <h1>Simple prices,<br><span class="gradient-text">no surprises</span></h1>
        <p>Fixed website builds, optional care plans, and clear scope before you pay. No vague agency quote games.</p>
    </div>
</section>

<section class="section narrative-section pricing-guide-section">
    <div class="container">
        <div class="pricing-guide pricing-guide--market reveal">
            <div class="pricing-guide-main">
                <span class="section-tag">Market Checked</span>
                <h2>Agency-style clarity, without agency-style guesswork.</h2>
                <p>UK web packages usually win trust by making scope obvious: pages, SEO, analytics, ownership, turnaround, and support. We keep that same clarity, then price it for local businesses that need a proper site without a massive upfront leap.</p>
            </div>
            <div class="pricing-guide-list">
                <div>
                    <strong>Fixed build price</strong>
                    <p>You know the starting cost before a call, with no mystery quote form hiding the basics.</p>
                </div>
                <div>
                    <strong>Built to be owned</strong>
                    <p>Your website is yours. We can support it monthly, but you are not trapped in a rental model.</p>
                </div>
                <div>
                    <strong>Support is optional</strong>
                    <p>Add care if you want updates, edits, and backups. Skip it if you want the site handed over.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section pricing-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">One-Off Website Builds</span>
            <h2>Choose the build<br>that <em>matches</em> the job</h2>
            <p>Most small businesses do not need a bloated agency project. They need a fast, credible website that earns trust, takes enquiries, and can grow when the business grows.</p>
        </div>

        <div class="pricing-grid">

            <div class="pricing-card pricing-card--launch reveal">
                <div class="pricing-card-top">
                    <div>
                        <div class="pricing-tier">Launch</div>
                        <p class="pricing-fit">Best for first websites, trades, local services, and simple brochure sites.</p>
                    </div>
                    <span class="pricing-speed">7 days</span>
                </div>
                <div class="pricing-price"><span class="pricing-currency">&pound;</span>399</div>
                <p class="pricing-desc">A clean business website that makes you look real, trustworthy, and easy to contact.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Up to 5 pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Mobile-responsive design</li>
                    <li><?php echo dpowered_check_icon(); ?>Contact form included</li>
                    <li><?php echo dpowered_check_icon(); ?>Basic SEO setup</li>
                    <li><?php echo dpowered_check_icon(); ?>Google Maps integration</li>
                    <li><?php echo dpowered_check_icon(); ?>1 revision round</li>
                    <li><?php echo dpowered_check_icon(); ?>You own the finished website</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Start Launch</a>
            </div>

            <div class="pricing-card pricing-card--growth featured reveal">
                <div class="v2-rain" aria-hidden="true"></div>
                <div class="pricing-badge">Most popular</div>
                <div class="pricing-card-top">
                    <div>
                        <div class="pricing-tier">Growth</div>
                        <p class="pricing-fit">Best for businesses that want search structure, content room, and stronger conversion.</p>
                    </div>
                    <span class="pricing-speed">10 days</span>
                </div>
                <div class="pricing-price"><span class="pricing-currency">&pound;</span>599</div>
                <p class="pricing-desc">For businesses ready to grow: more pages, better SEO, and a site built to bring in customers.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Up to 8 pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Everything in Launch</li>
                    <li><?php echo dpowered_check_icon(); ?>Blog / news section</li>
                    <li><?php echo dpowered_check_icon(); ?>Google Analytics setup</li>
                    <li><?php echo dpowered_check_icon(); ?>Advanced on-page SEO</li>
                    <li><?php echo dpowered_check_icon(); ?>Social media integration</li>
                    <li><?php echo dpowered_check_icon(); ?>2 revision rounds</li>
                    <li><?php echo dpowered_check_icon(); ?>You own the finished website</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Choose Growth</a>
            </div>

            <div class="pricing-card pricing-card--pro reveal">
                <div class="pricing-card-top">
                    <div>
                        <div class="pricing-tier">Pro</div>
                        <p class="pricing-fit">Best for serious service businesses, advanced forms, booking flows, and bigger sites.</p>
                    </div>
                    <span class="pricing-speed">14 days</span>
                </div>
                <div class="pricing-price"><span class="pricing-currency">&pound;</span>1,199</div>
                <p class="pricing-desc">For businesses that need something more: a larger, feature-rich website built to handle serious enquiries.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Expanded page plan</li>
                    <li><?php echo dpowered_check_icon(); ?>Everything in Growth</li>
                    <li><?php echo dpowered_check_icon(); ?>Custom design &amp; animations</li>
                    <li><?php echo dpowered_check_icon(); ?>Advanced forms &amp; integrations</li>
                    <li><?php echo dpowered_check_icon(); ?>WordPress training session</li>
                    <li><?php echo dpowered_check_icon(); ?>Priority support</li>
                    <li><?php echo dpowered_check_icon(); ?>3 revision rounds</li>
                    <li><?php echo dpowered_check_icon(); ?>You own the finished website</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Plan a Pro Build</a>
            </div>

        </div>
    </div>
</section>

<section class="section insight-section">
    <div class="container">
        <div class="pricing-reality reveal">
            <div>
                <span class="section-tag">Before You Pay</span>
                <h2>We confirm the exact scope first.</h2>
                <p>Competitors often sell packages, then quote extras after discovery. We keep the package simple, check the details, and tell you if your project needs a custom quote before anything starts.</p>
            </div>
            <ol class="pricing-steps">
                <li><span>01</span>Tell us the pages and features you need.</li>
                <li><span>02</span>We recommend Launch, Growth, Pro, or custom.</li>
                <li><span>03</span>You get a fixed scope and price in writing.</li>
            </ol>
        </div>
    </div>
</section>

<section class="section plans-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Optional Monthly Care</span>
            <h2>Keep the site <em>sharp</em><br>after it goes live</h2>
            <p>Some agencies bundle hosting and support into everything. We keep it optional: choose a one-off build, or add monthly care if you want us handling the technical side.</p>
        </div>

        <div class="plans-grid plans-grid--two">

            <div class="plan-card reveal">
                <div class="plan-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="plan-tier">Basic Maintenance</div>
                <div class="plan-price"><span class="pricing-currency">&pound;</span>49<span class="pricing-per">/mo</span></div>
                <p class="pricing-desc">For businesses that want the site kept safe, backed up, and updated.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Plugin, theme &amp; CMS updates</li>
                    <li><?php echo dpowered_check_icon(); ?>Security monitoring</li>
                    <li><?php echo dpowered_check_icon(); ?>Regular backups</li>
                    <li><?php echo dpowered_check_icon(); ?>Small bug fixes</li>
                    <li><?php echo dpowered_check_icon(); ?>Minor edits, up to 30 mins/month</li>
                </ul>
                <p class="plan-note">Good for simple sites where you want peace of mind, not constant marketing work.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Add Maintenance</a>
            </div>

            <div class="plan-card featured reveal">
                <div class="v2-rain" aria-hidden="true"></div>
                <div class="pricing-badge">Best Value</div>
                <div class="plan-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <div class="plan-tier">Growth Care</div>
                <div class="plan-price"><span class="pricing-currency">&pound;</span>99<span class="pricing-per">/mo</span></div>
                <p class="pricing-desc">For businesses that want the site improved every month, not just maintained.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Everything in Basic</li>
                    <li><?php echo dpowered_check_icon(); ?>1-2 content updates/month</li>
                    <li><?php echo dpowered_check_icon(); ?>New sections or small pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Basic SEO improvements</li>
                    <li><?php echo dpowered_check_icon(); ?>Speed &amp; performance checks</li>
                    <li><?php echo dpowered_check_icon(); ?>Monthly traffic and performance summary</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Add Growth Care</a>
            </div>

        </div>
    </div>
</section>

<section class="section bundle-section">
    <div class="container">
        <div class="bundle-card pricing-bundle-card reveal">
            <div class="v2-rain" aria-hidden="true"></div>
            <div class="bundle-left">
                <span class="section-tag" style="margin-bottom:16px">Low-Stress Route</span>
                <h2>Website + care<br><span class="gradient-text">without the big agency bill</span></h2>
                <p>If you want the site built and looked after, this is the cleanest route. Pay less upfront, launch quickly, and let us handle updates, backups, edits, and support after it goes live.</p>
                <ul class="bundle-perks">
                    <li><?php echo dpowered_check_icon(); ?>Lower upfront cost</li>
                    <li><?php echo dpowered_check_icon(); ?>Hosting guidance included</li>
                    <li><?php echo dpowered_check_icon(); ?>Maintenance included</li>
                    <li><?php echo dpowered_check_icon(); ?>Support included</li>
                    <li><?php echo dpowered_check_icon(); ?>Cancel the monthly care anytime</li>
                </ul>
            </div>
            <div class="bundle-right">
                <div class="bundle-price-box">
                    <div class="bundle-price-row">
                        <div>
                            <span class="bundle-label">Launch build</span>
                            <div class="bundle-amount"><span class="pricing-currency">&pound;</span>399</div>
                        </div>
                        <div class="bundle-plus">+</div>
                        <div>
                            <span class="bundle-label">Care from</span>
                            <div class="bundle-amount"><span class="pricing-currency">&pound;</span>49<span class="pricing-per">/mo</span></div>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full" style="margin-top:24px">Ask About the Bundle</a>
                    <p style="font-size:0.78rem;color:var(--ink-2);text-align:center;margin-top:12px">No hidden fees. Build scope confirmed before payment.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--bg-alt)">
    <div class="container">
        <div class="transparency-grid">

            <div class="not-included-card reveal">
                <div class="not-included-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    What's quoted separately
                </div>
                <p style="font-size:0.88rem;color:var(--ink-2);margin-bottom:18px">To keep the public package prices fair, these are scoped separately when needed:</p>
                <ul class="not-included-list">
                    <li><?php echo dpowered_x_icon(); ?>Logo or full branding work</li>
                    <li><?php echo dpowered_x_icon(); ?>Large copywriting projects</li>
                    <li><?php echo dpowered_x_icon(); ?>Booking/payment systems</li>
                    <li><?php echo dpowered_x_icon(); ?>Complex directories or membership areas</li>
                    <li><?php echo dpowered_x_icon(); ?>Unlimited edits after approval</li>
                </ul>
                <p class="plan-note" style="margin-top:16px">Need one of these? We can still help, we just quote it honestly instead of hiding it inside a package.</p>
            </div>

            <div class="peace-card reveal">
                <span class="section-tag" style="margin-bottom:20px">Why It Works</span>
                <h3 style="font-size:1.5rem;margin-bottom:24px">Cheap can be risky.<br>Agency can be overkill.</h3>
                <div class="peace-items">
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <h4>Clear ownership</h4>
                            <p>Your site is handed over properly, with WordPress access and no lock-in trick.</p>
                        </div>
                    </div>
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </div>
                        <div>
                            <h4>Room to grow</h4>
                            <p>Start with what you need now, then add SEO, content, pages, or support when it makes sense.</p>
                        </div>
                    </div>
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <h4>Plain English scope</h4>
                            <p>You know what is included, what costs extra, and what happens before the project starts.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <span class="section-tag">Ready To Get Started?</span>
            <h2>Not sure which plan<br><span class="gradient-text">is right for you?</span></h2>
            <p>Drop us a message and we will recommend the best option for your budget and goals. No pressure, no hard sell.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Talk to us</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
