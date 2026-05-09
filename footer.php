<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-logo footer-logo" aria-label="DPowered.online — Home">
                <svg width="36" viewBox="0 0 200 220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <defs>
                        <linearGradient id="dp-grad-f" x1="0" y1="0" x2="0.7" y2="1">
                            <stop offset="0%"   stop-color="#1A4DFF"/>
                            <stop offset="100%" stop-color="#06133D"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#dp-grad-f)" fill-rule="evenodd" d="M 16 16 L 100 16 A 84 84 0 0 1 100 184 L 16 184 Z"/>
                    <g fill="#060612" fill-rule="evenodd">
                        <path d="M 56 50 L 100 50 A 36 36 0 0 1 100 122 L 80 122 L 80 210 L 56 210 Z M 80 72 L 100 72 A 14 14 0 0 1 100 100 L 80 100 Z"/>
                        <rect x="0" y="142" width="200" height="6"/>
                    </g>
                </svg>
                <span>DPowered<span class="logo-dot">.</span>online</span>
            </a>
            <p>We build websites that grow your business. Modern, fast, and built to convert.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
                <li><a href="<?php echo esc_url(home_url('/portfolio')); ?>">Portfolio</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                <li><a href="<?php echo esc_url(home_url('/reviews')); ?>">Reviews</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
            </ul>
        </div>
        <div class="footer-services">
            <h4>Services</h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">New Websites</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Website Redesigns</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Ongoing Updates</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">WordPress Training</a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>">Website Care</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Get In Touch</h4>
            <p>Ready to power up your online presence?</p>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary btn-shine"><span>Start a Project</span></a>
            <div class="footer-social">
                <a href="https://www.instagram.com/dpowered_/" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="DPowered on Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    <span>@dpowered_</span>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> DPowered.online &mdash; All rights reserved. <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a></p>
        <a href="https://www.instagram.com/dpowered_/" target="_blank" rel="noopener noreferrer" class="footer-bottom-ig" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
