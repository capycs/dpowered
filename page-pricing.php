<?php
/* Template Name: Pricing Page */
get_header(); ?>

<section class="inner-hero" id="main-content">
    <div class="hero-bg">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
    </div>
    <div class="container inner-hero-content">
        <span class="section-tag">Transparent Pricing</span>
        <h1>Simple Prices,<br><span class="gradient-text">No Surprises</span></h1>
        <p>One-off website builds and flexible monthly plans — know exactly what you're paying before you commit.</p>
    </div>
</section>

<!-- ── WEBSITE PACKAGES ──────────────────────────────── -->
<section class="section pricing-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">One-Off Website Builds</span>
            <h2>Get Your Business<br>Online From £399</h2>
            <p>A professional website built for you, delivered in days — then it's yours, forever.</p>
        </div>

        <div class="pricing-grid">

            <!-- Launch -->
            <div class="pricing-card reveal">
                <div class="pricing-tier">Launch</div>
                <div class="pricing-price"><span class="pricing-currency">£</span>399</div>
                <p class="pricing-desc">For new businesses that need a clean, professional online presence — fast and affordable.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Up to 5 pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Mobile-responsive design</li>
                    <li><?php echo dpowered_check_icon(); ?>Contact form included</li>
                    <li><?php echo dpowered_check_icon(); ?>Basic SEO setup</li>
                    <li><?php echo dpowered_check_icon(); ?>Google Maps integration</li>
                    <li><?php echo dpowered_check_icon(); ?>1 revision round</li>
                    <li><?php echo dpowered_check_icon(); ?>7-day delivery</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-outline btn-full">Get Started</a>
            </div>

            <!-- Growth -->
            <div class="pricing-card featured reveal">
                <div class="pricing-badge">Most Popular</div>
                <div class="pricing-tier">Growth</div>
                <div class="pricing-price"><span class="pricing-currency">£</span>599</div>
                <p class="pricing-desc">For businesses ready to grow — more pages, better SEO, and a site built to bring in customers.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Up to 8 pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Everything in Launch</li>
                    <li><?php echo dpowered_check_icon(); ?>Blog / news section</li>
                    <li><?php echo dpowered_check_icon(); ?>Google Analytics setup</li>
                    <li><?php echo dpowered_check_icon(); ?>Advanced on-page SEO</li>
                    <li><?php echo dpowered_check_icon(); ?>Social media integration</li>
                    <li><?php echo dpowered_check_icon(); ?>2 revision rounds</li>
                    <li><?php echo dpowered_check_icon(); ?>10-day delivery</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Get Started</a>
            </div>

            <!-- Pro -->
            <div class="pricing-card reveal">
                <div class="pricing-tier">Pro</div>
                <div class="pricing-price"><span class="pricing-currency">£</span>1,199</div>
                <p class="pricing-desc">For businesses that need something more — a larger, feature-rich website built to handle anything you throw at it.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Unlimited pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Everything in Growth</li>
                    <li><?php echo dpowered_check_icon(); ?>Custom design &amp; animations</li>
                    <li><?php echo dpowered_check_icon(); ?>Advanced forms &amp; integrations</li>
                    <li><?php echo dpowered_check_icon(); ?>WordPress training session</li>
                    <li><?php echo dpowered_check_icon(); ?>Priority support</li>
                    <li><?php echo dpowered_check_icon(); ?>3 revision rounds</li>
                    <li><?php echo dpowered_check_icon(); ?>14-day delivery</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-outline btn-full">Get Started</a>
            </div>

        </div>
    </div>
</section>

<!-- ── MONTHLY CARE PLANS ────────────────────────────── -->
<section class="section plans-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Monthly Care Plans</span>
            <h2>We Keep Your Site<br>Running Perfectly</h2>
            <p>You focus on your business — we handle the website. Security, updates, improvements, and results, every month.</p>
        </div>

        <div class="plans-grid plans-grid--two">

            <!-- Basic -->
            <div class="plan-card reveal">
                <div class="plan-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="plan-tier">Basic Maintenance</div>
                <div class="plan-price"><span class="pricing-currency">£</span>39<span class="pricing-per">/mo</span></div>
                <p class="pricing-desc">Keep your site secure and up to date — the essentials taken care of every month.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Plugin, theme &amp; CMS updates</li>
                    <li><?php echo dpowered_check_icon(); ?>Security monitoring</li>
                    <li><?php echo dpowered_check_icon(); ?>Regular backups</li>
                    <li><?php echo dpowered_check_icon(); ?>Small bug fixes</li>
                    <li><?php echo dpowered_check_icon(); ?>Minor edits (up to 30 mins/month)</li>
                </ul>
                <p class="plan-note">Up to 30 minutes of edits per month — text changes, image swaps, small tweaks.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-outline btn-full">Add to My Site</a>
            </div>

            <!-- Growth -->
            <div class="plan-card featured reveal">
                <div class="pricing-badge">Best Value</div>
                <div class="plan-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <div class="plan-tier">Growth Plan</div>
                <div class="plan-price"><span class="pricing-currency">£</span>89<span class="pricing-per">/mo</span></div>
                <p class="pricing-desc">Your site continuously improves every month — more content, better SEO, measurable results.</p>
                <ul class="pricing-features">
                    <li><?php echo dpowered_check_icon(); ?>Everything in Basic</li>
                    <li><?php echo dpowered_check_icon(); ?>1–2 content updates/month</li>
                    <li><?php echo dpowered_check_icon(); ?>New sections or pages</li>
                    <li><?php echo dpowered_check_icon(); ?>Basic SEO improvements</li>
                    <li><?php echo dpowered_check_icon(); ?>Speed &amp; performance checks</li>
                    <li><?php echo dpowered_check_icon(); ?>Monthly report (traffic &amp; performance)</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full">Add to My Site</a>
            </div>

        </div>
    </div>
</section>

<!-- ── BUNDLE DEAL ───────────────────────────────────── -->
<section class="section bundle-section">
    <div class="container">
        <div class="bundle-card reveal">
            <div class="bundle-glow"></div>
            <div class="bundle-left">
                <span class="section-tag" style="margin-bottom:16px">Best Way to Start</span>
                <h2>Website + Hosting + Support<br><span class="gradient-text">All Included</span></h2>
                <p>The smartest way to get started — a lower upfront cost, with everything handled from day one. Hosting, maintenance, and support are all bundled in so you never have to worry about the technical side.</p>
                <ul class="bundle-perks">
                    <li><?php echo dpowered_check_icon(); ?>Lower upfront cost</li>
                    <li><?php echo dpowered_check_icon(); ?>Hosting included</li>
                    <li><?php echo dpowered_check_icon(); ?>Monthly maintenance included</li>
                    <li><?php echo dpowered_check_icon(); ?>Priority support included</li>
                    <li><?php echo dpowered_check_icon(); ?>Cancel anytime</li>
                </ul>
            </div>
            <div class="bundle-right">
                <div class="bundle-price-box">
                    <div class="bundle-price-row">
                        <div>
                            <span class="bundle-label">One-off build</span>
                            <div class="bundle-amount"><span class="pricing-currency">£</span>399</div>
                        </div>
                        <div class="bundle-plus">+</div>
                        <div>
                            <span class="bundle-label">Per month</span>
                            <div class="bundle-amount"><span class="pricing-currency">£</span>49<span class="pricing-per">/mo</span></div>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-full" style="margin-top:24px">Start the Bundle</a>
                    <p style="font-size:0.78rem;color:var(--text-muted);text-align:center;margin-top:12px">No hidden fees. Cancel the monthly plan anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── TRANSPARENCY ──────────────────────────────────── -->
<section class="section" style="background:var(--bg-secondary)">
    <div class="container">
        <div class="transparency-grid">

            <div class="not-included-card reveal">
                <div class="not-included-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    What's Not Included
                </div>
                <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:18px">To keep our prices fair and our quality high, the following are not covered in standard packages:</p>
                <ul class="not-included-list">
                    <li><?php echo dpowered_x_icon(); ?>Unlimited edits</li>
                    <li><?php echo dpowered_x_icon(); ?>Full redesigns (quoted separately)</li>
                    <li><?php echo dpowered_x_icon(); ?>Logo or branding work</li>
                    <li><?php echo dpowered_x_icon(); ?>Daily social media posting</li>
                    <li><?php echo dpowered_x_icon(); ?>Copywriting (we can refer you)</li>
                </ul>
                <p class="plan-note" style="margin-top:16px">Need something not listed? Just ask — we'll always give you an honest quote.</p>
            </div>

            <div class="peace-card reveal">
                <span class="section-tag" style="margin-bottom:20px">Why Choose Us</span>
                <h3 style="font-size:1.5rem;margin-bottom:24px">We Sell Peace of Mind,<br>Not Tasks</h3>
                <div class="peace-items">
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <h4>Secure &amp; Up to Date</h4>
                            <p>We keep your website secure and running — you never have to think about it.</p>
                        </div>
                    </div>
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        </div>
                        <div>
                            <h4>Continuously Improving</h4>
                            <p>We keep improving your site so it brings in more customers — not just maintaining it.</p>
                        </div>
                    </div>
                    <div class="peace-item">
                        <div class="peace-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <h4>Zero Technical Stress</h4>
                            <p>You don't have to worry about anything technical — just run your business.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── CTA ───────────────────────────────────────────── -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <div class="cta-orb"></div>
            <span class="section-tag">Ready To Get Started?</span>
            <h2>Not Sure Which Plan<br><span class="gradient-text">Is Right For You?</span></h2>
            <p>Drop us a message and we'll recommend the best option for your budget and goals — no pressure, no hard sell.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg">Talk To Us</a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View Services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
