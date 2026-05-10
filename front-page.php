<?php get_header(); ?>

<!-- HERO -->
<section class="hero-v2" id="main-content" aria-label="Welcome to DPowered.online">

    <!-- Meta bar: DELIVERY / STUDIO / LOCAL TIME / STATUS -->
    <div class="hero-meta-bar">
        <div class="hero-meta-inner">
            <div class="hmb-col">
                <span class="hmb-label">DELIVERY</span>
                <span class="hmb-val">7&ndash;14 <span class="hmb-dim">day turnaround</span></span>
            </div>
            <div class="hmb-col">
                <span class="hmb-label">STUDIO</span>
                <span class="hmb-val">Manchester, UK <span class="hmb-dim">/ remote-first</span></span>
            </div>
            <div class="hmb-col">
                <span class="hmb-label">LOCAL TIME</span>
                <span class="hmb-val" id="hero-clock">--:--</span>
            </div>
            <div class="hmb-col">
                <span class="hmb-label">STATUS</span>
                <span class="hmb-val">
                    <span class="hmb-status-dot" aria-hidden="true"></span>
                    Accepting projects
                </span>
            </div>
        </div>
    </div>

    <!-- Hero body -->
    <div class="hero-v2-body">

        <div class="hero-quip" role="note">
            <span class="hero-quip-blip" aria-hidden="true"></span>
            <span class="hero-quip-text">Your competitors have a terrible website. Let&rsquo;s keep it that way.</span>
        </div>

        <!-- 12-column display grid -->
        <div class="hero-v2-grid">

            <!-- Headline — full 12 cols -->
            <div class="hero-v2-headline">
                <h1 class="hero-display">
                    <span class="screen-reader-text">DPowered makes your website, business, brand, bookings, and growth look unfairly good.</span>
                    <span class="hd-visual" aria-hidden="true">
                        <span class="hd-l1">DPowered makes</span>
                            <span class="hd-l2">
                                <span class="hd-your">your</span>
                                <span class="hd-word-reel" data-reel-words="website,business,brand,bookings,growth,comeback">
                                    <span class="hd-word-stage">
                                        <span class="hd-word-current hd-blue">website</span>
                                    </span>
                                    <span class="hd-word-sizer hd-blue" aria-hidden="true">website</span>
                                </span>
                            </span>
                        </span>
                        <span class="hd-l3">look unfairly good.</span>
                    </span>
                </h1>
            </div>

            <!-- CTA — cols 1–4 -->
            <div class="hero-v2-cta">
                <div class="hero-v2-cta-row">
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="cta-v2-primary">
                        Get a free quote
                        <span class="cta-v2-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/services')); ?>" class="cta-v2-secondary">
                        See what we do
                    </a>
                </div>
                <p class="hero-v2-note">&mdash; FREE 30-MIN STRATEGY CALL &middot; NO OBLIGATION</p>
            </div>

            <!-- Sub text — cols 5–10 -->
            <div class="hero-v2-sub">
                <p>DPowered.online is a small UK studio building <strong>fast, honest websites</strong> for trades, shops, and restaurants &mdash; sites that load quickly, rank locally, and turn browsers into bookings. <strong>7&ndash;14 day turnaround.</strong> You own everything.</p>
            </div>

        </div>
    </div>

</section>

<!-- PLATFORMS MARQUEE -->
<div class="platforms-section" aria-hidden="true">
    <p class="platforms-label">Technologies &amp; Platforms We Build With</p>
    <div class="platforms-inner">
        <div class="platforms-track">
            <?php
            $platforms = [
                ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>', 'label' => 'WordPress'],
                ['icon' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>', 'label' => 'Google SEO'],
                ['icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 'label' => 'SSL Secured'],
                ['icon' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>', 'label' => 'Google Analytics'],
                ['icon' => '<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>', 'label' => 'Mobile First'],
                ['icon' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>', 'label' => 'Fast Loading'],
            ];
            // Duplicate for seamless loop
            $all = array_merge($platforms, $platforms);
            foreach ($all as $p):
            ?>
            <div class="platform-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $p['icon']; ?></svg>
                <?php echo esc_html($p['label']); ?>
            </div>
            <span class="platform-sep" aria-hidden="true">·</span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- SERVICE STRIP -->
<div class="service-strip" id="services" role="list" aria-label="Our services">
    <a href="<?php echo esc_url(home_url('/services')); ?>" class="sstrip-item" role="listitem">
        <div class="sstrip-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
        </div>
        <div class="sstrip-body">
            <strong>New Websites</strong>
            <span>Built from scratch, fast</span>
        </div>
        <svg class="sstrip-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
    <a href="<?php echo esc_url(home_url('/services')); ?>" class="sstrip-item" role="listitem">
        <div class="sstrip-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </div>
        <div class="sstrip-body">
            <strong>Website Redesigns</strong>
            <span>Old site, new life</span>
        </div>
        <svg class="sstrip-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
    <a href="<?php echo esc_url(home_url('/services')); ?>" class="sstrip-item" role="listitem">
        <div class="sstrip-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="sstrip-body">
            <strong>Monthly Care Plans</strong>
            <span>Updates &amp; maintenance</span>
        </div>
        <svg class="sstrip-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
    <a href="<?php echo esc_url(home_url('/services')); ?>" class="sstrip-item" role="listitem">
        <div class="sstrip-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="sstrip-body">
            <strong>WordPress Training</strong>
            <span>Edit it yourself</span>
        </div>
        <svg class="sstrip-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
</div>

<!-- HOW IT WORKS -->
<section class="section process-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">How We Work</span>
            <h2>Simple Process,<br>Powerful Results</h2>
            <p>We keep it straightforward so you're never left in the dark.</p>
        </div>
        <div class="process-steps">
            <div class="process-step reveal">
                <div class="step-number">01</div>
                <div class="step-content">
                    <h3>Discovery</h3>
                    <p>Tell us about your business, your goals, and your vision. We listen first and ask the right questions to understand exactly what you need.</p>
                </div>
            </div>
            <div class="process-connector"></div>
            <div class="process-step reveal">
                <div class="step-number">02</div>
                <div class="step-content">
                    <h3>Design & Build</h3>
                    <p>We craft your site with attention to every detail — beautiful design, fast performance, and all the functionality your business requires.</p>
                </div>
            </div>
            <div class="process-connector"></div>
            <div class="process-step reveal">
                <div class="step-number">03</div>
                <div class="step-content">
                    <h3>Launch & Support</h3>
                    <p>We get you live and stick around to make sure everything runs smoothly. You can also edit the site yourself — no tech skills needed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PERFORMANCE -->
<section class="section perf-section">
    <div class="container">
        <div class="perf-layout">

            <!-- Left: text + stats -->
            <div class="perf-text reveal">
                <span class="section-tag">Server Performance</span>
                <h2>Hosting Built<br><span class="gradient-text">For Speed</span></h2>
                <p>Your site lives on UK-based infrastructure engineered for sub-second load times. A global CDN, free SSL, and 99.9% uptime guarantee — so every visitor gets a fast, smooth experience from day one.</p>
                <div class="perf-stats-col">
                    <div class="perf-stat">
                        <div class="perf-stat-value">99.9%</div>
                        <div class="perf-stat-label">Uptime SLA</div>
                    </div>
                    <div class="perf-stat">
                        <div class="perf-stat-value">&lt;500ms</div>
                        <div class="perf-stat-label">Avg. Response</div>
                    </div>
                    <div class="perf-stat">
                        <div class="perf-stat-value">UK</div>
                        <div class="perf-stat-label">Based Servers</div>
                    </div>
                    <div class="perf-stat">
                        <div class="perf-stat-value">Free</div>
                        <div class="perf-stat-label">SSL + CDN</div>
                    </div>
                </div>
            </div>

            <!-- Right: rocket performance visual -->
            <div class="perf-terminal perf-rocket-wrap reveal">
                <div class="rocket-launch-card" aria-label="Fast hosting visual">
                    <div class="rocket-speed-lines" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <div class="rocket-orbit-ring ring-one" aria-hidden="true"></div>
                    <div class="rocket-orbit-ring ring-two" aria-hidden="true"></div>
                    <div class="rocket-stage" aria-hidden="true">
                        <div class="rocket-ship">
                            <svg class="rocket-svg" viewBox="0 0 160 260" role="img" aria-label="Rocket taking off">
                                <defs>
                                    <linearGradient id="rocketBodyGrad" x1="48" y1="16" x2="112" y2="170" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#f7fbff"/>
                                        <stop offset="0.52" stop-color="#a9c7ff"/>
                                        <stop offset="1" stop-color="#3156d8"/>
                                    </linearGradient>
                                    <linearGradient id="rocketNoseGrad" x1="80" y1="8" x2="80" y2="76" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#f7fdff"/>
                                        <stop offset="1" stop-color="#5ce5ff"/>
                                    </linearGradient>
                                    <linearGradient id="rocketFinGrad" x1="30" y1="128" x2="58" y2="190" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#7ea2ff"/>
                                        <stop offset="1" stop-color="#2558ff"/>
                                    </linearGradient>
                                    <linearGradient id="rocketNozzleGrad" x1="80" y1="154" x2="80" y2="190" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#3b4f87"/>
                                        <stop offset="0.58" stop-color="#18234c"/>
                                        <stop offset="1" stop-color="#070b1d"/>
                                    </linearGradient>
                                    <radialGradient id="rocketWindowGrad" cx="0.36" cy="0.28" r="0.72">
                                        <stop offset="0" stop-color="#ffffff"/>
                                        <stop offset="0.42" stop-color="#52d7f7"/>
                                        <stop offset="1" stop-color="#3156d8"/>
                                    </radialGradient>
                                    <linearGradient id="rocketFlameGrad" x1="80" y1="168" x2="80" y2="250" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#fff7ac"/>
                                        <stop offset="0.25" stop-color="#52d7f7"/>
                                        <stop offset="0.68" stop-color="#2558ff"/>
                                        <stop offset="1" stop-color="#2558ff" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <g class="rocket-svg-flame">
                                    <path d="M80 164 C58 192 58 226 80 252 C102 226 102 192 80 164Z" fill="url(#rocketFlameGrad)"/>
                                    <path d="M80 176 C69 196 70 218 80 235 C90 218 91 196 80 176Z" fill="#fff6a9" opacity="0.9"/>
                                </g>
                                <path class="rocket-svg-fin rocket-svg-fin-left" d="M52 124 C28 134 26 176 42 194 C54 182 60 158 60 136Z" fill="url(#rocketFinGrad)"/>
                                <path class="rocket-svg-fin rocket-svg-fin-right" d="M108 124 C132 134 134 176 118 194 C106 182 100 158 100 136Z" fill="url(#rocketFinGrad)"/>
                                <path class="rocket-svg-body" d="M80 8 C61 31 50 53 48 76 L48 146 C48 162 58 172 80 172 C102 172 112 162 112 146 L112 76 C110 53 99 31 80 8Z" fill="url(#rocketBodyGrad)"/>
                                <path class="rocket-svg-nose" d="M80 8 C61 31 51 53 48 76 C58 86 102 86 112 76 C109 53 99 31 80 8Z" fill="url(#rocketNoseGrad)"/>
                                <path class="rocket-svg-shade" d="M96 47 C105 76 106 118 100 150 C96 163 88 170 80 172 C102 172 112 162 112 146 L112 76 C110 53 99 31 80 8 C87 21 92 33 96 47Z" fill="#050713" opacity="0.18"/>
                                <circle class="rocket-svg-window-ring" cx="80" cy="82" r="17" fill="#101832" opacity="0.82"/>
                                <circle class="rocket-svg-window" cx="80" cy="82" r="11" fill="url(#rocketWindowGrad)"/>
                                <rect class="rocket-svg-stripe" x="56" y="128" width="48" height="10" rx="5" fill="url(#rocketFlameGrad)"/>
                                <path class="rocket-svg-nozzle" d="M62 156 H98 L91 190 Q80 198 69 190Z" fill="url(#rocketNozzleGrad)"/>
                                <path class="rocket-svg-nozzle-glow" d="M70 171 H90 L86 188 Q80 192 74 188Z" fill="#52d7f7" opacity="0.18"/>
                            </svg>
                        </div>
                        <div class="rocket-plume">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                    <div class="rocket-info">
                        <span class="rocket-kicker">Performance launch</span>
                        <strong>Loaded in 0.48s</strong>
                        <p>UK edge hosting, SSL, and CDN tuned for quick first visits.</p>
                    </div>
                    <div class="rocket-metrics">
                        <span><strong>99</strong> score</span>
                        <span><strong>&lt;500ms</strong> response</span>
                        <span><strong>99.9%</strong> uptime</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SCROLL STORY: Why DPowered -->
<section class="scroll-story" id="why-us" aria-label="Why choose DPowered.online">
    <div class="story-track">
        <div class="story-sticky">

            <!-- Left: animated visual canvas -->
            <div class="story-canvas" aria-hidden="true">
                <div class="story-bg-num">01</div>

                <!-- Ch 0: Speed / Ring -->
                <div class="canvas-chapter active" data-ch="0">
                    <svg width="240" height="240" viewBox="0 0 240 240" fill="none">
                        <defs>
                            <linearGradient id="sg-ring" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#00d4ff"/>
                                <stop offset="100%" stop-color="#1a4dff"/>
                            </linearGradient>
                            <filter id="sg-glow"><feGaussianBlur stdDeviation="4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                        </defs>
                        <circle cx="120" cy="120" r="96" stroke="rgba(255,255,255,0.05)" stroke-width="10"/>
                        <circle id="story-ring-arc" cx="120" cy="120" r="96" stroke="url(#sg-ring)" stroke-width="10"
                            stroke-linecap="round" stroke-dasharray="603" stroke-dashoffset="603"
                            transform="rotate(-90 120 120)" filter="url(#sg-glow)"/>
                        <circle cx="120" cy="120" r="72" fill="rgba(0,212,255,0.04)" stroke="rgba(0,212,255,0.08)" stroke-width="1"/>
                        <g id="ch0-label">
                            <text x="120" y="107" text-anchor="middle" font-size="52" font-weight="800" fill="white" font-family="var(--font-head)" letter-spacing="-2">7</text>
                            <text x="120" y="128" text-anchor="middle" font-size="11" fill="rgba(255,255,255,0.45)" font-family="var(--font-body)" letter-spacing="0.2em">DAYS</text>
                            <text x="120" y="148" text-anchor="middle" font-size="9.5" fill="rgba(0,212,255,0.6)" font-family="var(--font-body)" letter-spacing="0.12em">TO GO LIVE</text>
                        </g>
                    </svg>
                    <div class="canvas-stats">
                        <div class="canvas-stat"><span class="cs-val">7–14</span><span class="cs-label">Day delivery</span></div>
                        <div class="canvas-stat-div"></div>
                        <div class="canvas-stat"><span class="cs-val">100%</span><span class="cs-label">On time</span></div>
                    </div>
                </div>

                <!-- Ch 1: Mobile -->
                <div class="canvas-chapter" data-ch="1">
                    <svg width="148" height="268" viewBox="0 0 148 268" fill="none">
                        <defs>
                            <linearGradient id="sg-phone-screen" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#00d4ff" stop-opacity="0.18"/>
                                <stop offset="100%" stop-color="#1a4dff" stop-opacity="0.06"/>
                            </linearGradient>
                        </defs>
                        <!-- Phone border draws itself in -->
                        <rect id="ch1-outline" x="4" y="4" width="140" height="260" rx="24"
                            fill="rgba(255,255,255,0.035)" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"
                            stroke-dasharray="760" stroke-dashoffset="760"/>
                        <rect x="14" y="28" width="120" height="222" rx="10" fill="url(#sg-phone-screen)"/>
                        <rect x="52" y="7" width="44" height="10" rx="5" fill="rgba(0,0,0,0.5)"/>
                        <rect x="55" y="255" width="38" height="4" rx="2" fill="rgba(255,255,255,0.15)"/>
                        <!-- Screen content fades in after border draws -->
                        <g class="ph-cnt">
                            <rect x="22" y="38" width="104" height="56" rx="7" fill="rgba(0,212,255,0.12)"/>
                            <rect x="32" y="50" width="56" height="7" rx="3" fill="rgba(255,255,255,0.3)"/>
                            <rect x="32" y="62" width="80" height="5" rx="2" fill="rgba(255,255,255,0.15)"/>
                            <rect x="32" y="74" width="42" height="12" rx="4" fill="rgba(0,212,255,0.4)"/>
                            <rect x="22" y="103" width="68" height="7" rx="3" fill="rgba(255,255,255,0.18)"/>
                            <rect x="22" y="116" width="104" height="5" rx="2" fill="rgba(255,255,255,0.08)"/>
                            <rect x="22" y="126" width="88" height="5" rx="2" fill="rgba(255,255,255,0.08)"/>
                            <rect x="22" y="136" width="96" height="5" rx="2" fill="rgba(255,255,255,0.06)"/>
                            <rect x="22" y="153" width="47" height="44" rx="6" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
                            <rect x="79" y="153" width="47" height="44" rx="6" fill="rgba(0,212,255,0.07)" stroke="rgba(0,212,255,0.15)" stroke-width="1"/>
                            <rect x="22" y="222" width="104" height="1" fill="rgba(255,255,255,0.06)"/>
                            <rect x="38" y="230" width="16" height="16" rx="3" fill="rgba(0,212,255,0.3)"/>
                            <rect x="66" y="230" width="16" height="16" rx="3" fill="rgba(255,255,255,0.08)"/>
                            <rect x="94" y="230" width="16" height="16" rx="3" fill="rgba(255,255,255,0.08)"/>
                        </g>
                    </svg>
                    <div class="canvas-stats">
                        <div class="canvas-stat"><span class="cs-val">60%</span><span class="cs-label">Mobile traffic</span></div>
                        <div class="canvas-stat-div"></div>
                        <div class="canvas-stat"><span class="cs-val">360px</span><span class="cs-label">Min breakpoint</span></div>
                    </div>
                </div>

                <!-- Ch 2: SEO -->
                <div class="canvas-chapter" data-ch="2">
                    <svg width="280" height="210" viewBox="0 0 280 210" fill="none">
                        <defs>
                            <linearGradient id="sg-bar" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#00d4ff"/>
                                <stop offset="100%" stop-color="#1a4dff" stop-opacity="0.5"/>
                            </linearGradient>
                            <linearGradient id="sg-bar2" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#00d4ff" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#1a4dff" stop-opacity="0.15"/>
                            </linearGradient>
                        </defs>
                        <rect x="0" y="0" width="280" height="46" rx="11" fill="rgba(255,255,255,0.05)" stroke="rgba(0,212,255,0.25)" stroke-width="1.5"/>
                        <circle cx="28" cy="23" r="9" stroke="rgba(0,212,255,0.7)" stroke-width="1.8"/>
                        <line x1="34.5" y1="29.5" x2="42" y2="37" stroke="rgba(0,212,255,0.7)" stroke-width="1.8" stroke-linecap="round"/>
                        <rect x="56" y="18" width="90" height="10" rx="4" fill="rgba(255,255,255,0.18)"/>
                        <rect x="232" y="14" width="40" height="18" rx="5" fill="rgba(0,212,255,0.2)"/>
                        <rect x="239" y="20" width="26" height="6" rx="2" fill="rgba(0,212,255,0.6)"/>
                        <!-- Rising bars (each grows up from baseline) -->
                        <rect class="ch2-bar" x="8"   y="178" width="26" height="18"  rx="4" fill="url(#sg-bar2)" style="--ai:0"/>
                        <rect class="ch2-bar" x="44"  y="163" width="26" height="33"  rx="4" fill="url(#sg-bar2)" style="--ai:1"/>
                        <rect class="ch2-bar" x="80"  y="147" width="26" height="49"  rx="4" fill="url(#sg-bar2)" style="--ai:2"/>
                        <rect class="ch2-bar" x="116" y="128" width="26" height="68"  rx="4" fill="url(#sg-bar)"  style="--ai:3"/>
                        <rect class="ch2-bar" x="152" y="104" width="26" height="92"  rx="4" fill="url(#sg-bar)"  style="--ai:4"/>
                        <!-- Trend line draws itself after bars -->
                        <polyline id="ch2-trend" points="210,186 230,155 250,126 270,100"
                            stroke="#00d4ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            stroke-dasharray="110" stroke-dashoffset="110" fill="none"/>
                        <circle cx="270" cy="100" r="4" fill="#00d4ff"/>
                        <rect x="196" y="60" width="76" height="32" rx="8" fill="rgba(0,212,255,0.12)" stroke="rgba(0,212,255,0.3)" stroke-width="1"/>
                        <text x="234" y="81" text-anchor="middle" font-size="13" font-weight="700" fill="rgba(0,212,255,0.9)" font-family="var(--font-head)">#1 Goal</text>
                    </svg>
                    <div class="canvas-stats">
                        <div class="canvas-stat"><span class="cs-val">Built-in</span><span class="cs-label">SEO setup</span></div>
                        <div class="canvas-stat-div"></div>
                        <div class="canvas-stat"><span class="cs-val">Every</span><span class="cs-label">Site included</span></div>
                    </div>
                </div>

                <!-- Ch 3: Control / CMS -->
                <div class="canvas-chapter" data-ch="3">
                    <svg width="290" height="190" viewBox="0 0 290 190" fill="none">
                        <rect x="0" y="0" width="290" height="190" rx="12" fill="rgba(255,255,255,0.025)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
                        <rect x="0" y="0" width="290" height="30" rx="12" fill="rgba(0,212,255,0.06)"/>
                        <rect x="0" y="18" width="290" height="12" fill="rgba(0,212,255,0.06)"/>
                        <rect x="12" y="9" width="40" height="12" rx="3" fill="rgba(0,212,255,0.3)"/>
                        <rect x="62" y="11" width="28" height="8" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="98" y="11" width="28" height="8" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="134" y="11" width="28" height="8" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="0" y="30" width="52" height="160" fill="rgba(255,255,255,0.02)"/>
                        <rect x="10" y="44" width="32" height="7" rx="2" fill="rgba(0,212,255,0.35)"/>
                        <rect x="10" y="57" width="32" height="6" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="10" y="69" width="32" height="6" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="10" y="81" width="32" height="6" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="10" y="93" width="32" height="6" rx="2" fill="rgba(255,255,255,0.1)"/>
                        <rect x="64" y="42" width="130" height="11" rx="3" fill="rgba(255,255,255,0.2)"/>
                        <rect x="64" y="60" width="210" height="7" rx="2" fill="rgba(255,255,255,0.07)"/>
                        <rect x="64" y="72" width="185" height="7" rx="2" fill="rgba(255,255,255,0.07)"/>
                        <rect x="64" y="84" width="200" height="7" rx="2" fill="rgba(255,255,255,0.07)"/>
                        <rect x="64" y="96" width="170" height="7" rx="2" fill="rgba(255,255,255,0.05)"/>
                        <rect x="64" y="115" width="82" height="28" rx="6" fill="rgba(0,212,255,0.22)" stroke="rgba(0,212,255,0.45)" stroke-width="1"/>
                        <rect x="76" y="126" width="58" height="6" rx="2" fill="rgba(255,255,255,0.55)"/>
                        <!-- Saved badge pops in -->
                        <g id="ch3-saved">
                            <rect x="162" y="115" width="68" height="28" rx="6" fill="rgba(34,197,94,0.1)" stroke="rgba(34,197,94,0.35)" stroke-width="1"/>
                            <circle cx="177" cy="129" r="6" fill="none" stroke="#22c55e" stroke-width="1.5"/>
                            <polyline points="173,129 176,132 181,124" fill="none" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect x="187" y="126" width="36" height="6" rx="2" fill="rgba(34,197,94,0.5)"/>
                        </g>
                        <rect x="64" y="156" width="90" height="7" rx="2" fill="rgba(255,255,255,0.08)"/>
                        <rect x="164" y="156" width="56" height="7" rx="2" fill="rgba(255,255,255,0.05)"/>
                    </svg>
                    <div class="canvas-stats">
                        <div class="canvas-stat"><span class="cs-val">0</span><span class="cs-label">Code needed</span></div>
                        <div class="canvas-stat-div"></div>
                        <div class="canvas-stat"><span class="cs-val">Full</span><span class="cs-label">CMS access</span></div>
                    </div>
                </div>

                <!-- Ch 4: Support -->
                <div class="canvas-chapter" data-ch="4">
                    <svg width="280" height="210" viewBox="0 0 280 210" fill="none">
                        <!-- Client message slides from left -->
                        <g class="ch4-b1">
                            <rect x="0" y="8" width="168" height="56" rx="14" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                            <rect x="16" y="22" width="110" height="8" rx="3" fill="rgba(255,255,255,0.2)"/>
                            <rect x="16" y="36" width="84" height="8" rx="3" fill="rgba(255,255,255,0.1)"/>
                            <text x="9" y="83" font-size="9" fill="rgba(255,255,255,0.2)" font-family="var(--font-body)">You · just now</text>
                        </g>
                        <!-- Reply slides from right -->
                        <g class="ch4-b2">
                            <rect x="112" y="96" width="168" height="56" rx="14" fill="rgba(0,212,255,0.1)" stroke="rgba(0,212,255,0.22)" stroke-width="1"/>
                            <rect x="128" y="110" width="136" height="8" rx="3" fill="rgba(0,212,255,0.45)"/>
                            <rect x="128" y="124" width="104" height="8" rx="3" fill="rgba(0,212,255,0.25)"/>
                            <text x="271" y="172" text-anchor="end" font-size="9" fill="rgba(0,212,255,0.35)" font-family="var(--font-body)">DPowered · just now</text>
                        </g>
                        <!-- Typing indicator fades in last -->
                        <g id="ch4-typing">
                            <rect x="0" y="178" width="68" height="32" rx="14" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
                            <circle cx="18" cy="194" r="4" fill="rgba(255,255,255,0.4)"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.2s" repeatCount="indefinite" begin="0s"/></circle>
                            <circle cx="34" cy="194" r="4" fill="rgba(255,255,255,0.4)"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.2s" repeatCount="indefinite" begin="0.2s"/></circle>
                            <circle cx="50" cy="194" r="4" fill="rgba(255,255,255,0.4)"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.2s" repeatCount="indefinite" begin="0.4s"/></circle>
                        </g>
                    </svg>
                    <div class="canvas-stats">
                        <div class="canvas-stat"><span class="cs-val">Fast</span><span class="cs-label">Response time</span></div>
                        <div class="canvas-stat-div"></div>
                        <div class="canvas-stat"><span class="cs-val">Always</span><span class="cs-label">Here for you</span></div>
                    </div>
                </div>

            </div>

            <!-- Canvas nav — mobile only, sits below the animation -->
            <div class="story-cnav">
                <button class="story-cnav-btn story-cnav-prev" aria-label="Previous story">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="story-cnav-pips" aria-hidden="true">
                    <span class="story-cnav-pip active"></span>
                    <span class="story-cnav-pip"></span>
                    <span class="story-cnav-pip"></span>
                    <span class="story-cnav-pip"></span>
                    <span class="story-cnav-pip"></span>
                </div>
                <button class="story-cnav-btn story-cnav-next" aria-label="Next story">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <!-- Right: text panel -->
            <div class="story-panel">
                <div class="story-meta">
                    <span class="section-tag">Why DPowered.online</span>
                    <div class="story-counter">
                        <span class="story-num">01</span>
                        <span class="story-sep"> / </span>
                        <span class="story-total">05</span>
                    </div>
                </div>

                <div class="story-chapters-wrap">
                    <div class="story-chapter active">
                        <h2>Your Site, Live<br><span class="gradient-text">In 7–14 Days</span></h2>
                        <p>No 6-week timelines or endless back-and-forth. We move fast — most projects go from brief to live in under two weeks, without cutting a single corner.</p>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="story-link">Get a free quote <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>
                    <div class="story-chapter">
                        <h2>Looks Perfect<br><span class="gradient-text">On Every Screen</span></h2>
                        <p>Over 60% of web traffic is mobile. We design for phones first — so every visitor gets a seamless experience whether they're on a phone, tablet, or desktop.</p>
                        <a href="<?php echo esc_url(home_url('/services')); ?>" class="story-link">See how we build <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>
                    <div class="story-chapter">
                        <h2>Get Found<br><span class="gradient-text">On Google</span></h2>
                        <p>Every site ships with SEO foundations built in — semantic markup, lightning-fast load times, Google Analytics, and on-page optimisation from day one.</p>
                        <a href="<?php echo esc_url(home_url('/services')); ?>" class="story-link">View our services <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>
                    <div class="story-chapter">
                        <h2>You Stay<br><span class="gradient-text">In Control</span></h2>
                        <p>Your site runs on WordPress — update text, images, and content yourself without needing us for every small change. We hand over the keys and walk you through it.</p>
                        <a href="<?php echo esc_url(home_url('/services')); ?>" class="story-link">WordPress training <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>
                    <div class="story-chapter">
                        <h2>Support That<br><span class="gradient-text">Doesn't Vanish</span></h2>
                        <p>We don't disappear after launch. Something broken? Need a change? Just want advice? We're a message away — and we actually respond quickly.</p>
                        <a href="<?php echo esc_url(home_url('/contact')); ?>" class="story-link">Talk to us <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>
                </div>

                <div class="story-dots" aria-hidden="true">
                    <span class="story-dot active"></span>
                    <span class="story-dot"></span>
                    <span class="story-dot"></span>
                    <span class="story-dot"></span>
                    <span class="story-dot"></span>
                </div>
                <p class="story-swipe-hint" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Swipe to explore
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </p>

                <div class="story-progress-bar" aria-hidden="true">
                    <div class="story-progress-fill"></div>
                </div>
            </div>

            <!-- Scroll cue — fades out once user advances past chapter 0 -->
            <div class="story-scroll-cue" id="storyScrollCue" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                <span>Scroll</span>
            </div>

        </div>
    </div>
</section>

<!-- Back to top — inline below animation, not a floating overlay -->
<div class="story-btop-wrap">
    <button class="story-btop" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
        Back to top
    </button>
</div>

<!-- REVIEWS — pulled from WordPress admin: Reviews > Add New Review -->
<?php
$reviews = new WP_Query([
    'post_type'      => 'review',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
]);
if ($reviews->have_posts()):
?>
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Client Stories</span>
            <h2>What Our Clients<br>Are Saying</h2>
            <p>Don't just take our word for it &mdash; here's what businesses we've helped have to say.</p>
        </div>
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
        <div style="text-align:center;margin-top:40px">
            <a href="<?php echo esc_url(home_url('/reviews')); ?>" class="btn btn-outline btn-lg">See All Reviews</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<section class="section faq-section">
    <div class="container">
        <div class="faq-inner">
            <div class="faq-header reveal">
                <span class="section-tag">FAQ</span>
                <h2>Common Questions</h2>
                <p>Everything you need to know before getting started.</p>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary" style="margin-top:20px">Ask us anything →</a>
            </div>
            <div class="faq-list">
                <?php
                $faqs = [
                    ['How much does a website cost?',
                     'Every project is different, so we tailor our pricing to your specific needs. We work with a range of budgets and always aim to give you the most value for your money. Get in touch for a free, no-obligation quote.'],
                    ['How long does it take to build a website?',
                     'Most projects are completed within 7–14 days from the point we have everything we need from you — content, images, and any brand assets. Larger projects like e-commerce stores may take a little longer.'],
                    ['Can I update the website myself after launch?',
                     'Absolutely. Every site we build runs on WordPress, which means you can log in and update text, images, and content yourself — no coding skills needed. We also walk you through how to use it at handover.'],
                    ['Do you offer support after the site goes live?',
                     'Yes — we don\'t just disappear after launch. We offer free ongoing support for any issues that arise, and we\'re always available if you need changes, updates, or just have a question.'],
                    ['What do I need to provide to get started?',
                     'Ideally: your logo (or we can help create one), the text/copy for your pages, any photos you want to use, and an idea of what you like. Don\'t worry if you\'re not sure — we guide you through everything.'],
                    ['How do I pay for my website?',
                     'Once we\'ve had a chat and agreed on everything, we\'ll send you a secure payment link directly. We ask for a 50% deposit to get started, with the remaining 50% due when the site is ready to launch. We accept all major debit and credit cards.'],
                ];
                foreach ($faqs as $i => [$q, $a]):
                ?>
                <div class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-<?php echo $i; ?>">
                        <?php echo esc_html($q); ?>
                        <span class="faq-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                    <div class="faq-answer" id="faq-answer-<?php echo $i; ?>" role="region">
                        <p><?php echo esc_html($a); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <div class="cta-orb"></div>
            <span class="section-tag">Ready To Start?</span>
            <h2>Let's Build Something<br><span class="gradient-text">Amazing Together</span></h2>
            <p>Get in touch today and let's talk about how we can help your business grow online.</p>
            <div class="cta-actions">
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-lg"><span>Get a Free Quote</span></a>
                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-outline btn-lg">View Services</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
