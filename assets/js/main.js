
(function () {
  'use strict';

  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const strikeEl = document.getElementById('agencyStrike');
  if (strikeEl) {
    setTimeout(() => strikeEl.classList.add('struck'), 800);
  }

  const siteHeader = document.querySelector('.site-header');
  function updateNav() {
    if (!siteHeader) return;
    siteHeader.classList.toggle('scrolled', window.scrollY > 40);
  }

  window.addEventListener('scroll', updateNav, { passive: true });
  updateNav();

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

  if (!prefersReduced) {
    document.querySelectorAll('.mag-btn').forEach(btn => {
      btn.addEventListener('mousemove', e => {
        const r  = btn.getBoundingClientRect();
        const dx = e.clientX - (r.left + r.width  / 2);
        const dy = e.clientY - (r.top  + r.height / 2);
        btn.style.transform = `translate(${dx * 0.14}px, ${dy * 0.14}px)`;
      });
      btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
    });
  }

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

  const statsBand = document.querySelector('.v2-stats-band');
  if (statsBand) {
    if (prefersReduced) {
      statsBand.classList.add('stats-in');
    } else {
      const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('stats-in');
            statsObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.35 });
      statsObserver.observe(statsBand);
    }
  }

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
