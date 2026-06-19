<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-logo footer-logo" aria-label="DPowered.online — Home">
                <svg width="32" viewBox="0 0 200 220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <defs>
                        <linearGradient id="dp-grad-f" x1="0" y1="0" x2="0.7" y2="1">
                            <stop offset="0%"   stop-color="#1A4DFF"/>
                            <stop offset="100%" stop-color="#06133D"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#dp-grad-f)" fill-rule="evenodd" d="M 16 16 L 100 16 A 84 84 0 0 1 100 184 L 16 184 Z"/>
                    <g fill="#F8F7F4" fill-rule="evenodd">
                        <path d="M 56 50 L 100 50 A 36 36 0 0 1 100 122 L 80 122 L 80 210 L 56 210 Z M 80 72 L 100 72 A 14 14 0 0 1 100 100 L 80 100 Z"/>
                        <rect x="0" y="142" width="200" height="6"/>
                    </g>
                </svg>
                <span>DPowered<span class="logo-dot">.</span>online</span>
            </a>
            <p>We build websites that grow your business.<br>Modern, fast, and built to convert.</p>
            <div class="footer-socials">
                <a href="https://www.instagram.com/dpowered_/" target="_blank" rel="noopener noreferrer" aria-label="DPowered on Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
                <a href="https://www.tiktok.com/@dpowered" target="_blank" rel="noopener noreferrer" aria-label="DPowered on TikTok">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                </a>
                <a href="https://www.threads.com/@dpowered_" target="_blank" rel="noopener noreferrer" aria-label="DPowered on Threads" class="footer-socials-threads">
                    @
                </a>
                <a href="https://www.facebook.com/profile.php?id=61589007017371" target="_blank" rel="noopener noreferrer" aria-label="DPowered on Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-links">
            <h4>Pages</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
                <li><a href="<?php echo esc_url(home_url('/portfolio')); ?>">Portfolio</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                <li><a href="<?php echo esc_url(home_url('/reviews')); ?>">Reviews</a></li>
                <li><a href="<?php echo esc_url(home_url('/pricing')); ?>">Pricing</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
            </ul>
        </div>
        <div class="footer-services">
            <h4>Services</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">New Websites</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Website Redesigns</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Monthly Care Plans</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">WordPress Training</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Website Care</a></li>
            </ul>
        </div>
        <div class="footer-contact-col">
            <h4>Get In Touch</h4>
            <p>Ready to power up your online presence?</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">Start a Project</a>
        </div>
    </div>
    <div class="footer-wordmark" aria-hidden="true">
        <span>DPowered<span class="logo-dot">.</span></span>
    </div>
    <div class="footer-bottom">
        <?php
        $dp_wa_url   = function_exists('dpowered_work_area_url') ? dpowered_work_area_url() : home_url('/work-area');
        $dp_wa_label = (is_user_logged_in() && function_exists('dpowered_user_can_leads') && dpowered_user_can_leads()) ? 'Work Area' : 'Team Login';
        ?>
        <p>&copy; <?php echo date('Y'); ?> DPowered.online &mdash; All rights reserved. <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a> &middot; <a href="<?php echo esc_url(home_url('/terms')); ?>">Terms</a> &middot; <a href="<?php echo esc_url($dp_wa_url); ?>"><?php echo esc_html($dp_wa_label); ?></a></p>
        <span class="footer-made"><span class="footer-clock-dot" aria-hidden="true"></span>Built in Liverpool, UK &middot; <span id="liveClock">--:--:--</span></span>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
