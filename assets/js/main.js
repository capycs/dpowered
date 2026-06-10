/* DPowered V2.1 — main.js */
(function () {
  'use strict';

  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ── Strikethrough (SVG path draw) ────────────────────────────── */
  const strikeEl = document.getElementById('agencyStrike');
  if (strikeEl) {
    setTimeout(() => strikeEl.classList.add('struck'), 800);
  }

  /* ── Scroll Progress Bar ───────────────────────────────────────── */
  /* CSS scroll-driven animation handles this where supported — JS is
     only the fallback for older browsers. */
  const progressBar = document.getElementById('scrollProgress');
  const cssProgress = typeof CSS !== 'undefined' && CSS.supports && CSS.supports('animation-timeline: scroll()');
  function updateProgress() {
    if (!progressBar || cssProgress) return;
    const scrolled = window.scrollY;
    const total = document.documentElement.scrollHeight - window.innerHeight;
    progressBar.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
  }

  /* ── Nav: scroll state ─────────────────────────────────────────── */
  const siteHeader = document.querySelector('.site-header');
  function updateNav() {
    if (!siteHeader) return;
    siteHeader.classList.toggle('scrolled', window.scrollY > 40);
  }

  window.addEventListener('scroll', () => {
    updateProgress();
    updateNav();
  }, { passive: true });

  updateProgress();
  updateNav();

  /* ── Mobile Nav Toggle ─────────────────────────────────────────── */
  const navToggle  = document.getElementById('navToggle');
  const navLinks   = document.getElementById('navLinks');
  const navOverlay = document.getElementById('navOverlay');

  function openNav() {
    navLinks   && navLinks.classList.add('open');
    navOverlay && navOverlay.classList.add('open');
    navToggle  && navToggle.classList.add('active');
    navToggle  && navToggle.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    navLinks   && navLinks.classList.remove('open');
    navOverlay && navOverlay.classList.remove('open');
    navToggle  && navToggle.classList.remove('active');
    navToggle  && navToggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  const navClose = document.getElementById('navClose');

  navToggle  && navToggle.addEventListener('click', () => {
    navLinks && navLinks.classList.contains('open') ? closeNav() : openNav();
  });
  navClose   && navClose.addEventListener('click', closeNav);
  navOverlay && navOverlay.addEventListener('click', closeNav);
  navLinks   && navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', closeNav));
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && navLinks && navLinks.classList.contains('open')) closeNav();
  });

  /* ── Cursor Follower ───────────────────────────────────────────── */
  const isTouchDevice = window.matchMedia('(hover: none)').matches;
  if (!isTouchDevice && !prefersReduced) {
    const cursor = document.createElement('div');
    cursor.className = 'v2-cursor';
    document.body.appendChild(cursor);

    let mx = -100, my = -100, cx = -100, cy = -100;

    document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });

    const interactiveSelectors = 'a, button, [role="button"], input, textarea, select, .mag-btn';
    document.addEventListener('mouseover', e => {
      if (e.target.closest(interactiveSelectors)) cursor.classList.add('expanded');
    });
    document.addEventListener('mouseout', e => {
      if (e.target.closest(interactiveSelectors)) cursor.classList.remove('expanded');
    });

    function lerp(a, b, t) { return a + (b - a) * t; }
    function animateCursor() {
      cx = lerp(cx, mx, 0.15);
      cy = lerp(cy, my, 0.15);
      cursor.style.transform = `translate(${cx}px, ${cy}px) translate(-50%, -50%)`;
      requestAnimationFrame(animateCursor);
    }
    animateCursor();
  }

  /* ── Magnetic Buttons ──────────────────────────────────────────── */
  if (!prefersReduced) {
    document.querySelectorAll('.mag-btn').forEach(btn => {
      btn.addEventListener('mousemove', e => {
        const r  = btn.getBoundingClientRect();
        const dx = e.clientX - (r.left + r.width  / 2);
        const dy = e.clientY - (r.top  + r.height / 2);
        btn.style.transform = `translate(${dx * 0.28}px, ${dy * 0.28}px)`;
      });
      btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
    });
  }

  /* ── Scroll Reveal (single + group) ───────────────────────────── */
  const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-scale, .reveal-group');
  if (prefersReduced) {
    revealEls.forEach(el => el.classList.add('visible'));
  } else {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(el => revealObserver.observe(el));
  }

  /* ── Word-Split Headline Animation ────────────────────────────── */
  /* Walks child nodes so <em> serif accents survive the split — em
     words get .is-serif on the inner span. Skipped entirely under
     reduced motion (CSS shows headlines as-is). */
  function initSplitHeadlines() {
    if (prefersReduced) return;
    document.querySelectorAll('.split-headline').forEach(el => {
      const tokens = [];
      el.childNodes.forEach(node => {
        const isSerif = node.nodeType === 1 && node.tagName === 'EM';
        const text = node.textContent;
        text.trim().split(/\s+/).forEach(w => {
          if (w) tokens.push({ w, serif: isSerif });
        });
      });
      if (!tokens.length) return;
      el.innerHTML = tokens
        .map((t, i) =>
          `<span class="split-word" style="transition-delay:${(i * 0.06).toFixed(2)}s"><span class="${t.serif ? 'is-serif' : ''}">${t.w}</span></span>`)
        .join(' ');

      const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (e.isIntersecting) {
            e.target.classList.add('split-ready');
            obs.unobserve(e.target);
          }
        });
      }, { threshold: 0.2 });
      obs.observe(el);
    });
  }
  initSplitHeadlines();

  /* ── Number Scramble Counter ───────────────────────────────────── */
  const CHARS = '0123456789';

  function scrambleCount(el, target, suffix, prefix, decimals) {
    const duration   = 1400;
    const scrambleMs = 500;
    const start      = performance.now();

    function randomDigit() { return CHARS[Math.floor(Math.random() * CHARS.length)]; }

    function step(now) {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased    = 1 - Math.pow(1 - progress, 3);
      const current  = target * eased;

      if (elapsed < scrambleMs) {
        /* scramble phase — show random digits around approaching value */
        const base = current.toFixed(decimals);
        const scrambled = base.replace(/\d/g, d =>
          Math.random() > 0.4 ? randomDigit() : d
        );
        el.textContent = prefix + scrambled + suffix;
      } else {
        el.textContent = prefix + current.toFixed(decimals) + suffix;
      }

      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = prefix + target.toFixed(decimals) + suffix;
    }
    requestAnimationFrame(step);
  }

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el       = entry.target;
        const target   = parseFloat(el.dataset.target || el.textContent);
        const suffix   = el.dataset.suffix  || '';
        const prefix   = el.dataset.prefix  || '';
        const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
        if (prefersReduced) {
          el.textContent = prefix + target.toFixed(decimals) + suffix;
        } else {
          scrambleCount(el, target, suffix, prefix, decimals);
        }
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

  /* ── FAQ Accordion (grid-rows animation lives in CSS) ──────────── */
  document.querySelectorAll('.v2-faq-question').forEach(q => {
    q.addEventListener('click', () => {
      const item = q.closest('.v2-faq-item');
      if (!item) return;

      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.v2-faq-item.open').forEach(openItem => {
        openItem.classList.remove('open');
        const btn = openItem.querySelector('.v2-faq-question');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });

      if (!isOpen) {
        item.classList.add('open');
        q.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ── Package Picker (contact page) ────────────────────────────── */
  const serviceInput = document.getElementById('contact-service');
  document.querySelectorAll('.pkg-card').forEach(card => {
    card.setAttribute('aria-pressed', card.classList.contains('selected') ? 'true' : 'false');
    card.addEventListener('click', () => {
      card.classList.toggle('selected');
      card.setAttribute('aria-pressed', card.classList.contains('selected') ? 'true' : 'false');
      if (serviceInput) {
        const selected = [...document.querySelectorAll('.pkg-card.selected')]
          .map(c => c.dataset.value || '')
          .filter(Boolean)
          .join(', ');
        serviceInput.value = selected;
      }
    });
  });

  /* ── Clock (footer + anywhere #liveClock appears) ──────────────── */
  const clockEl = document.getElementById('liveClock');
  if (clockEl) {
    function updateClock() {
      clockEl.textContent = new Date().toLocaleTimeString('en-GB', {
        timeZone: 'Europe/London',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      });
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  /* ── Smooth anchor scrolling ───────────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth', block: 'start' });
      }
    });
  });

})();
