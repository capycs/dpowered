// Scroll progress bar
const scrollProgress = document.getElementById('scrollProgress');
if (scrollProgress) {
    function updateProgress() {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        scrollProgress.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
}

// Count-up animation for hero stats
(function () {
    const statsEl = document.querySelector('.hero-stats');
    if (!statsEl) return;
    const countEls = statsEl.querySelectorAll('[data-count]');
    if (!countEls.length) return;
    const observer = new IntersectionObserver((entries) => {
        if (!entries[0].isIntersecting) return;
        observer.disconnect();
        countEls.forEach(el => {
            const target   = parseInt(el.dataset.count, 10);
            const suffix   = el.dataset.suffix || '';
            const duration = 1600;
            const t0       = performance.now();
            function step(now) {
                const p = Math.min((now - t0) / duration, 1);
                const e = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(e * target) + suffix;
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }, { threshold: 0.7 });
    observer.observe(statsEl);
})();

// Sticky nav + hide-on-scroll-down for mobile
const header = document.getElementById('site-header');
let _lastY = window.scrollY;
let _navTick = false;

function _updateNav() {
    const y = window.scrollY;
    header.classList.toggle('scrolled', y > 40);
    if (window.innerWidth <= 900 && !(navLinks && navLinks.classList.contains('open'))) {
        if (y > _lastY + 4 && y > 100) {
            header.classList.add('nav-hidden');
        } else if (y < _lastY - 4) {
            header.classList.remove('nav-hidden');
        }
    } else {
        header.classList.remove('nav-hidden');
    }
    _lastY = y;
    _navTick = false;
}

window.addEventListener('scroll', () => {
    if (!_navTick) { requestAnimationFrame(_updateNav); _navTick = true; }
}, { passive: true });

// Mobile nav toggle
const navToggle  = document.getElementById('navToggle');
const navLinks   = document.getElementById('navLinks');
const navOverlay = document.getElementById('navOverlay');

// Teleport navLinks out of the header to escape its transform stacking context.
// The header has transform:translateX(-50%) which makes position:fixed children
// position relative to the header, not the viewport — panel ends up off-screen.
if (navLinks) {
    const navOriginalParent  = navLinks.parentElement;
    const navOriginalSibling = navLinks.nextSibling;

    function placeNav() {
        if (window.innerWidth <= 900) {
            if (navLinks.parentElement !== document.body) {
                document.body.appendChild(navLinks);
            }
        } else {
            if (navLinks.parentElement === document.body) {
                navOriginalParent.insertBefore(navLinks, navOriginalSibling);
            }
        }
    }

    placeNav();
    window.addEventListener('resize', placeNav, { passive: true });
}

function closeNav() {
    if (!navLinks) return;
    navLinks.classList.remove('open');
    if (navToggle) {
        navToggle.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
    }
    if (navOverlay) navOverlay.classList.remove('open');
    document.body.style.overflow = '';
}

if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
        const isOpen = navLinks.classList.toggle('open');
        navToggle.classList.toggle('is-open', isOpen);
        navToggle.setAttribute('aria-expanded', String(isOpen));
        if (navOverlay) navOverlay.classList.toggle('open', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });
    navLinks.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', closeNav);
    });
    if (navOverlay) navOverlay.addEventListener('click', closeNav);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeNav();
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) closeNav();
    }, { passive: true });
}

// Scroll story
(function () {
    const track = document.querySelector('.story-track');
    if (!track) return;

    const canvasChapters = track.querySelectorAll('.canvas-chapter');
    const textChapters   = track.querySelectorAll('.story-chapter');
    const bgNum          = track.querySelector('.story-bg-num');
    const storyNum       = track.querySelector('.story-num');
    const dots           = track.querySelectorAll('.story-dot');
    const progressFill   = track.querySelector('.story-progress-fill');
    const scrollCue      = document.getElementById('storyScrollCue');
    const ringArc        = document.getElementById('story-ring-arc');
    const stickyEl       = track.querySelector('.story-sticky');
    const swipeHint      = track.querySelector('.story-swipe-hint');
    const cnavPrev       = track.querySelector('.story-cnav-prev');
    const cnavNext       = track.querySelector('.story-cnav-next');
    const cnavPips       = track.querySelectorAll('.story-cnav-pip');
    const RING_FULL      = 603;
    const RING_TARGET    = 90;
    const N = textChapters.length;
    let current = -1;

    const isMobile = () => window.innerWidth <= 600;
    if (!N || !stickyEl) return;

    function easeOut(t) { return 1 - Math.pow(1 - t, 2); }

    function setChapter(idx) {
        if (idx === current) return;
        const label = String(idx + 1).padStart(2, '0');
        canvasChapters.forEach((el, i) => el.classList.toggle('active', i === idx));
        textChapters.forEach((el, i)   => el.classList.toggle('active', i === idx));
        dots.forEach((el, i)           => el.classList.toggle('active', i === idx));
        if (bgNum)    bgNum.textContent    = label;
        if (storyNum) storyNum.textContent = label;
        if (scrollCue && idx > 0) scrollCue.classList.add('hidden');
        current = idx;
    }

    // Ring arc animation (scroll-driven on desktop, step-driven on mobile)
    function setRingProgress(p) {
        if (!ringArc) return;
        ringArc.style.strokeDashoffset = RING_FULL - (RING_FULL - RING_TARGET) * easeOut(p);
    }

    // Scroll-driven (desktop)
    function updateStory() {
        if (isMobile()) return;
        const rect       = track.getBoundingClientRect();
        const scrolled   = -rect.top;
        const scrollable = rect.height - window.innerHeight;
        if (scrollable <= 0) return;

        const progress = Math.max(0, Math.min(1, scrolled / scrollable));
        const idx      = Math.min(Math.floor(progress * N), N - 1);

        setRingProgress(Math.max(0, Math.min(1, progress * N)));
        if (progressFill) progressFill.style.width = (progress * 100) + '%';
        setChapter(idx);
    }

    window.addEventListener('scroll', updateStory, { passive: true });
    updateStory();

    function syncCnav(idx) {
        if (cnavPrev) cnavPrev.disabled = idx === 0;
        if (cnavNext) cnavNext.disabled = idx === N - 1;
        cnavPips.forEach((p, i) => p.classList.toggle('active', i === idx));
    }

    // Carousel (mobile) — buttons + swipe + dot tap, no scroll required
    function goTo(idx) {
        idx = Math.max(0, Math.min(N - 1, idx));
        syncCnav(idx);
        setRingProgress((idx + 1) / N);
        if (progressFill) progressFill.style.width = ((idx / (N - 1)) * 100) + '%';
        setChapter(idx);
        if (swipeHint) swipeHint.classList.add('hidden');
    }

    // Dot tap navigation
    dots.forEach((dot, i) => {
        dot.style.cursor = 'pointer';
        dot.addEventListener('click', () => { if (isMobile()) goTo(i); });
    });

    // Canvas nav buttons
    if (cnavPrev) cnavPrev.addEventListener('click', () => goTo((current < 0 ? 0 : current) - 1));
    if (cnavNext) cnavNext.addEventListener('click', () => goTo((current < 0 ? 0 : current) + 1));

    // Swipe navigation — locks direction early to prevent diagonal scroll bleed
    let _tx = 0, _ty = 0, _horizontal = null;
    stickyEl.addEventListener('touchstart', e => {
        _tx = e.touches[0].clientX;
        _ty = e.touches[0].clientY;
        _horizontal = null;
    }, { passive: true });

    stickyEl.addEventListener('touchmove', e => {
        if (_horizontal === null) {
            const dx = Math.abs(e.touches[0].clientX - _tx);
            const dy = Math.abs(e.touches[0].clientY - _ty);
            if (dx > 8 || dy > 8) _horizontal = dx > dy;
        }
        if (_horizontal && isMobile()) e.preventDefault();
    }, { passive: false });

    stickyEl.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - _tx;
        const dy = e.changedTouches[0].clientY - _ty;
        _horizontal = null;
        if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy) * 1.2) return;
        const dir = dx < 0 ? 1 : -1;
        if (isMobile()) {
            goTo((current < 0 ? 0 : current) + dir);
        } else {
            const target   = Math.max(0, Math.min(N - 1, (current < 0 ? 0 : current) + dir));
            const scrollable = track.offsetHeight - window.innerHeight;
            window.scrollTo({ top: track.offsetTop + ((target + 0.5) / N) * scrollable, behavior: 'smooth' });
        }
    }, { passive: true });

    // Init mobile on load and resize
    function initMobile() {
        if (isMobile()) {
            goTo(Math.max(current, 0));
            return;
        }
        updateStory();
    }
    initMobile();
    window.addEventListener('resize', initMobile, { passive: true });
})();

// Package picker — multiple selection toggle
(function () {
    const picker = document.querySelector('.pkg-picker');
    if (!picker) return;
    const hidden = picker.querySelector('input[type="hidden"]');
    picker.querySelectorAll('.pkg-card').forEach(card => {
        card.addEventListener('click', () => {
            card.classList.toggle('selected');
            const vals = [...picker.querySelectorAll('.pkg-card.selected')].map(c => c.dataset.value);
            if (hidden) hidden.value = vals.join(',');
        });
    });
})();

// Scroll reveal
const reveals = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), Math.min(i * 90, 400));
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });
reveals.forEach(el => revealObserver.observe(el));

// Homepage scroll choreography
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const sectionSelectors = [
        '.platforms-section',
        '.service-strip',
        '.process-section',
        '.perf-section',
        '.scroll-story',
        '.testimonials-section',
        '.faq-section',
        '.cta-section'
    ];
    const itemSelectors = [
        '.platforms-label',
        '.sstrip-item',
        '.section-header',
        '.process-step',
        '.process-connector',
        '.perf-text',
        '.perf-stat',
        '.perf-terminal',
        '.story-card',
        '.story-meta',
        '.testimonial-card',
        '.faq-header',
        '.faq-item',
        '.cta-inner'
    ];

    const sections = document.querySelectorAll(sectionSelectors.join(','));
    if (!sections.length) return;

    document.body.classList.add('motion-ready');

    sections.forEach(section => {
        section.classList.add('motion-section');

        const items = section.matches('.service-strip')
            ? section.querySelectorAll('.sstrip-item')
            : section.querySelectorAll(itemSelectors.join(','));

        items.forEach((item, index) => {
            item.classList.add('motion-item');
            item.style.setProperty('--motion-i', index);
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('motion-in');
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -14% 0px',
        threshold: 0.12
    });

    sections.forEach(section => observer.observe(section));
})();

// Smooth anchor scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href === '#') return;
        try {
            const target = document.querySelector(href);
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        } catch (_) {}
    });
});

// Sticky CTA — appears after scrolling past the hero
const stickyCta  = document.getElementById('stickyCta');
const heroEl     = document.querySelector('.hero-v2, .hero');
if (stickyCta) {
    const threshold = heroEl ? heroEl.offsetHeight * 0.6 : 400;
    const onScroll  = () => stickyCta.classList.toggle('visible', window.scrollY > threshold);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Active nav link indicator
(function () {
    const path = window.location.pathname.replace(/\/$/, '') || '/';
    document.querySelectorAll('.nav-links a:not(.nav-cta)').forEach(a => {
        const href = a.getAttribute('href');
        if (!href) return;
        try {
            const aPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
            if (aPath === path) a.classList.add('nav-active');
        } catch (e) {}
    });
})();

// FAQ accordion
document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
        const item    = btn.closest('.faq-item');
        const answer  = item.querySelector('.faq-answer');
        const isOpen  = item.classList.contains('open');

        document.querySelectorAll('.faq-item.open').forEach(open => {
            open.classList.remove('open');
            open.querySelector('.faq-answer').style.maxHeight = null;
            open.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        });

        if (!isOpen) {
            item.classList.add('open');
            answer.style.maxHeight = answer.scrollHeight + 'px';
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});

// ── Hero live clock (Europe/London — auto BST/GMT) ───────────────────────────
(function () {
    const clockEl = document.getElementById('hero-clock');
    if (!clockEl) return;
    const fmt = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/London',
        hour: '2-digit', minute: '2-digit', hour12: false,
        timeZoneName: 'short'
    });
    function tick() {
        const parts = fmt.formatToParts(new Date());
        const h  = parts.find(p => p.type === 'hour').value;
        const m  = parts.find(p => p.type === 'minute').value;
        const tz = parts.find(p => p.type === 'timeZoneName').value;
        clockEl.textContent = h + ':' + m + ' ' + tz;
    }
    tick();
    setInterval(tick, 10000);
})();

// ── CelestialSphere — Three.js WebGL shader background ───────────────────────
(function () {
    const container = document.getElementById('celestial-bg');
    if (!container || typeof THREE === 'undefined') return;

    const mobileShader = window.matchMedia('(max-width: 600px)').matches;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const touchShader  = mobileShader || window.matchMedia('(pointer: coarse)').matches;
    const HUE           = 218.0;
    const SPEED         = touchShader || reduceMotion ? 0.85 : 0.4;
    const ZOOM          = touchShader ? 1.36 : 1.2;
    const PARTICLE_SIZE = mobileShader ? 3.0 : 4.0;

    const scene    = new THREE.Scene();
    const camera   = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    const renderer = new THREE.WebGLRenderer({ antialias: !mobileShader, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, mobileShader ? 1.25 : 2));
    container.appendChild(renderer.domElement);

    const vertexShader = `
        varying vec2 vUv;
        void main() {
            vUv = uv;
            gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
        }
    `;

    const fragmentShader = `
        precision highp float;
        varying vec2 vUv;
        uniform vec2  u_resolution;
        uniform float u_time;
        uniform vec2  u_mouse;
        uniform float u_hue;
        uniform float u_zoom;
        uniform float u_particle_size;

        vec3 hsl2rgb(vec3 c) {
            vec3 rgb = clamp(abs(mod(c.x*6.0+vec3(0.0,4.0,2.0),6.0)-3.0)-1.0, 0.0, 1.0);
            return c.z * mix(vec3(1.0), rgb, c.y);
        }

        float random(vec2 st) {
            return fract(sin(dot(st.xy, vec2(12.9898,78.233))) * 43758.5453123);
        }

        float noise(vec2 st) {
            vec2 i = floor(st);
            vec2 f = fract(st);
            float a = random(i);
            float b = random(i + vec2(1.0, 0.0));
            float c = random(i + vec2(0.0, 1.0));
            float d = random(i + vec2(1.0, 1.0));
            vec2 u = f * f * (3.0 - 2.0 * f);
            return mix(a, b, u.x) + (c - a)*u.y*(1.0 - u.x) + (d - b)*u.y*u.x;
        }

        float fbm(vec2 st) {
            float v = 0.0, a = 0.5;
            for (int i = 0; i < 6; i++) { v += a * noise(st); st *= 2.0; a *= 0.5; }
            return v;
        }

        void main() {
            vec2 uv = (gl_FragCoord.xy - 0.5 * u_resolution) / min(u_resolution.y, u_resolution.x);
            uv *= u_zoom;

            vec2 mn = u_mouse / u_resolution;
            uv += (mn - 0.5) * 0.42;

            float f = fbm(uv + vec2(u_time * 0.12,  u_time * 0.055));
            float t = fbm(uv + f + vec2(u_time * 0.07, u_time * 0.032));

            float nebula = pow(t, 2.0);
            vec3 deepInk = vec3(0.012, 0.018, 0.055);
            vec3 indigo  = vec3(0.045, 0.075, 0.22);
            vec3 blue    = vec3(0.10, 0.20, 0.62);
            vec3 cyan    = vec3(0.18, 0.62, 0.78);
            vec3 violet  = vec3(0.22, 0.18, 0.54);
            vec3 color = mix(deepInk, indigo, smoothstep(0.08, 0.72, nebula));
            color += blue * pow(nebula, 1.3) * 0.58;
            color += cyan * pow(max(0.0, f - 0.34), 1.8) * 0.24;
            color += violet * pow(max(0.0, t - 0.48), 1.6) * 0.22;

            vec2 starUv = gl_FragCoord.xy / min(u_resolution.x, u_resolution.y);
            starUv = mat2(0.96, 0.18, -0.14, 1.03) * starUv;
            vec2 starGrid = starUv * 390.0;
            vec2 starCell = floor(starGrid);
            float sv = random(starCell);
            vec2 starPos = vec2(
                random(starCell + vec2(19.17, 73.31)),
                random(starCell + vec2(61.23, 11.79))
            );
            starPos = 0.12 + starPos * 0.76;
            vec2 starLocal = fract(starGrid) - starPos;
            float starRadius = mix(0.035, 0.115, random(starCell + vec2(7.13, 41.91)));
            float starMask = smoothstep(starRadius, 0.0, length(starLocal));
            float twinkle = 0.72 + 0.28 * sin(u_time * 2.0 + sv * 6.28318);
            float star = step(0.982, sv) * starMask * twinkle * u_particle_size;
            color += vec3(star * 0.86);

            gl_FragColor = vec4(color, 1.0);
        }
    `;

    const material = new THREE.ShaderMaterial({
        vertexShader,
        fragmentShader,
        uniforms: {
            u_time:          { value: 0.0 },
            u_resolution:    { value: new THREE.Vector2() },
            u_mouse:         { value: new THREE.Vector2(0, 0) },
            u_hue:           { value: HUE },
            u_zoom:          { value: ZOOM },
            u_particle_size: { value: PARTICLE_SIZE },
        },
    });

    const mesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material);
    scene.add(mesh);

    function resize() {
        const w = window.innerWidth, h = Math.max(window.innerHeight, document.documentElement.clientHeight || 0);
        renderer.setSize(w, h);
        material.uniforms.u_resolution.value.set(w, h);
        if (touchShader) material.uniforms.u_mouse.value.set(w * 0.5, h * 0.58);
    }

    window.addEventListener('resize', resize, { passive: true });
    window.addEventListener('mousemove', function (e) {
        if (touchShader) return;
        material.uniforms.u_mouse.value.set(e.clientX, window.innerHeight - e.clientY);
    }, { passive: true });

    resize();

    (function animate() {
        material.uniforms.u_time.value += 0.005 * SPEED;
        if (touchShader) {
            const t = material.uniforms.u_time.value;
            material.uniforms.u_mouse.value.set(
                window.innerWidth * (0.5 + Math.sin(t * 1.1) * 0.24),
                window.innerHeight * (0.58 + Math.cos(t * 0.78) * 0.2)
            );
        }
        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    })();
})();

// Professional word-reel animation in the front-page hero headline
(function () {
    const root = document.querySelector('.hd-word-reel[data-reel-words]');
    if (!root) return;

    const words = root.dataset.reelWords
        .split(',')
        .map(word => word.trim())
        .filter(Boolean);
    const current = root.querySelector('.hd-word-current');
    const next = root.querySelector('.hd-word-next');
    if (words.length < 2 || !current || !next) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let index = 0;
    current.textContent = words[index];
    next.textContent = words[(index + 1) % words.length];
    root.style.setProperty('--word-chars', Math.max(...words.map(word => word.length)));

    if (reduceMotion) {
        return;
    }

    let timer = null;

    function cycleWord() {
        if (root.classList.contains('is-changing')) return;

        const upcoming = (index + 1) % words.length;
        next.textContent = words[upcoming];
        root.classList.add('is-changing');

        window.setTimeout(() => {
            index = upcoming;
            current.textContent = words[index];
            next.textContent = words[(index + 1) % words.length];
            root.classList.remove('is-changing');
        }, 720);
    }

    timer = window.setInterval(cycleWord, 2650);

    window.addEventListener('pagehide', () => {
        if (timer) window.clearInterval(timer);
    }, { once: true });
})();

// Typewriter cycling animation on hero headline
(function () {
    const el = document.querySelector('.hero-title .gradient-text');
    if (!el) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const phrases = [
        'Work For You',
        'Grow Your Business',
        'Build Your Brand',
        'Drive More Sales',
        'Get You Found',
        'Bring Clients In',
        'Make You Stand Out',
    ];

    // Insert blinking cursor immediately after the gradient span
    const cursor = document.createElement('span');
    cursor.className = 'type-cursor';
    cursor.setAttribute('aria-hidden', 'true');
    el.after(cursor);

    const TYPE_MS   = 75;
    const DELETE_MS = 40;
    const PAUSE_END = 2200;  // hold after fully typed
    const PAUSE_DEL = 80;    // pause before typing next

    let idx      = 0;
    let charIdx  = phrases[0].length; // matches 'Work For You' in the HTML
    let deleting = false;

    function tick() {
        const phrase = phrases[idx];

        if (deleting) {
            charIdx--;
            el.textContent = phrase.slice(0, charIdx);

            if (charIdx === 0) {
                deleting = false;
                idx = (idx + 1) % phrases.length;
                setTimeout(tick, PAUSE_DEL);
            } else {
                setTimeout(tick, DELETE_MS);
            }
        } else {
            charIdx++;
            el.textContent = phrases[idx].slice(0, charIdx);

            if (charIdx === phrases[idx].length) {
                deleting = true;
                setTimeout(tick, PAUSE_END);
            } else {
                setTimeout(tick, TYPE_MS);
            }
        }
    }

    // Hold on first phrase for a moment, then start the loop
    setTimeout(() => { deleting = true; tick(); }, 2500);
})();

// WebGL aurora shader background
(function () {
    if (!document.querySelector('.hero')) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const canvas = document.createElement('canvas');
    canvas.id = 'shader-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:-1;';
    document.body.insertBefore(canvas, document.body.firstChild);

    const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
    if (!gl) { canvas.remove(); return; }

    const vs = `
        attribute vec2 a_pos;
        void main() { gl_Position = vec4(a_pos, 0.0, 1.0); }
    `;

    // tanh and float for-loops are not in GLSL ES 1.00 (WebGL 1) —
    // implement tanh manually and use int loop counters.
    const fs = `
        precision highp float;
        uniform float iTime;
        uniform vec2  iResolution;

        #define NUM_OCTAVES 3

        float rand(vec2 n) {
            return fract(sin(dot(n, vec2(12.9898, 4.1414))) * 43758.5453);
        }

        float noise(vec2 p) {
            vec2 ip = floor(p);
            vec2 u  = fract(p);
            u = u * u * (3.0 - 2.0 * u);
            float res = mix(
                mix(rand(ip),                 rand(ip + vec2(1.0, 0.0)), u.x),
                mix(rand(ip + vec2(0.0, 1.0)), rand(ip + vec2(1.0, 1.0)), u.x),
                u.y);
            return res * res;
        }

        float fbm(vec2 x) {
            float v = 0.0;
            float a = 0.3;
            vec2  shift = vec2(100.0);
            mat2  rot   = mat2(cos(0.5), sin(0.5), -sin(0.5), cos(0.5));
            for (int i = 0; i < NUM_OCTAVES; i++) {
                v += a * noise(x);
                x  = rot * x * 2.0 + shift;
                a *= 0.4;
            }
            return v;
        }

        vec4 tanh4(vec4 x) {
            vec4 e2x = exp(clamp(2.0 * x, -20.0, 20.0));
            return (e2x - 1.0) / (e2x + 1.0);
        }

        void main() {
            vec2 shake = vec2(sin(iTime * 1.2) * 0.005, cos(iTime * 2.1) * 0.005);
            vec2 p = ((gl_FragCoord.xy + shake * iResolution.xy) - iResolution.xy * 0.5)
                     / iResolution.y * mat2(6.0, -4.0, 4.0, 6.0);
            vec2 v;
            vec4 o = vec4(0.0);

            float f = 2.0 + fbm(p + vec2(iTime * 5.0, 0.0)) * 0.5;

            for (int j = 0; j < 35; j++) {
                float i = float(j);
                v = p + cos(i * i + (iTime + p.x * 0.08) * 0.025 + i * vec2(13.0, 11.0)) * 3.5
                      + vec2(sin(iTime * 3.0 + i) * 0.003, cos(iTime * 3.5 - i) * 0.003);
                float tailNoise = fbm(v + vec2(iTime * 0.5, i)) * 0.3 * (1.0 - (i / 35.0));
                vec4 auroraColors = vec4(
                    0.1 + 0.3 * sin(i * 0.2 + iTime * 0.4),
                    0.3 + 0.5 * cos(i * 0.3 + iTime * 0.5),
                    0.7 + 0.3 * sin(i * 0.4 + iTime * 0.3),
                    1.0
                );
                float denom = max(length(max(v, vec2(v.x * f * 0.015, v.y * 1.5))), 0.0001);
                vec4 contrib = auroraColors * exp(sin(i * i + iTime * 0.8)) / denom;
                float thin   = smoothstep(0.0, 1.0, i / 35.0) * 0.6;
                o += contrib * (1.0 + tailNoise * 0.8) * thin;
            }

            o = tanh4(pow(max(o / 100.0, vec4(0.0)), vec4(1.6)));
            gl_FragColor = o * 1.5;
        }
    `;

    function mkShader(type, src) {
        const s = gl.createShader(type);
        gl.shaderSource(s, src);
        gl.compileShader(s);
        if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) {
            console.error('Shader compile error:', gl.getShaderInfoLog(s));
        }
        return s;
    }

    const prog = gl.createProgram();
    gl.attachShader(prog, mkShader(gl.VERTEX_SHADER, vs));
    gl.attachShader(prog, mkShader(gl.FRAGMENT_SHADER, fs));
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) {
        console.error('Shader link error:', gl.getProgramInfoLog(prog));
        canvas.remove(); return;
    }
    gl.useProgram(prog);

    const buf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 1,-1, -1,1, 1,-1, -1,1, 1,1]), gl.STATIC_DRAW);

    const aPos = gl.getAttribLocation(prog, 'a_pos');
    gl.enableVertexAttribArray(aPos);
    gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 0, 0);

    const uTime = gl.getUniformLocation(prog, 'iTime');
    const uRes  = gl.getUniformLocation(prog, 'iResolution');

    let t = 0;

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
        const w = window.innerWidth  * dpr;
        const h = window.innerHeight * dpr;
        canvas.width  = w;
        canvas.height = h;
        gl.viewport(0, 0, w, h);
        gl.uniform2f(uRes, w, h);
    }

    function render() {
        if (!document.hidden) {
            t += 0.016;
            gl.uniform1f(uTime, t);
            gl.drawArrays(gl.TRIANGLES, 0, 6);
        }
        requestAnimationFrame(render);
    }

    resize();
    render();
    window.addEventListener('resize', resize, { passive: true });
})();
