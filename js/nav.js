/**
 * js/nav.js — One x Pizza
 * Injects shared header + footer, handles mobile menu, active link
 */

(function () {
  // ── Current page detection ──────────────────────────
  const path = location.pathname.replace(/\\/g, '/');
  const filename = path.split('/').pop() || 'index.html';

  function isActive(href) {
    if (href === 'index.html' || href === './') {
      return filename === 'index.html' || filename === '';
    }
    return filename === href;
  }

  function navLink(href, label) {
    const active = isActive(href) ? ' class="active"' : '';
    return `<a href="${href}"${active}>${label}</a>`;
  }

  // ── Header HTML ─────────────────────────────────────
  const headerHTML = `
<header class="site-header" id="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="index.html" class="site-logo">ONE <span>×</span> PIZZA</a>

      <nav class="nav-links" role="navigation" aria-label="Điều hướng chính">
        ${navLink('index.html',      'Trang chủ')}
        ${navLink('gioi-thieu.html', 'Giới thiệu')}
        ${navLink('thuc-don.html',   'Thực đơn')}
        ${navLink('dat-ban.html',    'Đặt bàn')}
        ${navLink('lien-he.html',    'Liên hệ')}
      </nav>

      <a href="dat-ban.html" class="btn btn-primary btn-sm nav-cta">
        🍕 Đặt bàn ngay
      </a>

      <button class="hamburger" id="hamburger"
              aria-label="Mở menu" aria-expanded="false" aria-controls="mobile-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<nav class="mobile-nav" id="mobile-nav" role="navigation" aria-label="Menu di động">
  ${navLink('index.html',      'Trang chủ')}
  ${navLink('gioi-thieu.html', 'Giới thiệu')}
  ${navLink('thuc-don.html',   'Thực đơn')}
  ${navLink('dat-ban.html',    'Đặt bàn')}
  ${navLink('lien-he.html',    'Liên hệ')}
  <a href="dat-ban.html" class="btn btn-primary" style="margin-top:12px">🍕 Đặt bàn ngay</a>
</nav>`;

  // ── Footer HTML ─────────────────────────────────────
  const footerHTML = `
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <h2>ONE <span>×</span> PIZZA</h2>
        <p>${RESTAURANT_CONFIG.tagline}<br>${RESTAURANT_CONFIG.subTagline}</p>
        <div style="display:flex;gap:10px;margin-top:16px">
          <a href="${RESTAURANT_CONFIG.social.facebook}" aria-label="Facebook"
            style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#1877F2;transition:opacity .2s" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#fff">
              <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
            </svg>
          </a>
          <a href="${RESTAURANT_CONFIG.social.instagram}" aria-label="Instagram"
            style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);transition:opacity .2s" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#fff">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
          </a>
          <a href="${RESTAURANT_CONFIG.social.zalo}" aria-label="Zalo"
            style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#0068FF;transition:opacity .2s" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="13" viewBox="0 0 52 28" fill="#fff">
              <text x="0" y="24" font-family="Arial Black,Arial" font-weight="900" font-size="28" letter-spacing="-1">Zalo</text>
            </svg>
          </a>
        </div>

      </div>

      <div class="footer-col">
        <h3>Điều hướng</h3>
        <a href="index.html">Trang chủ</a>
        <a href="gioi-thieu.html">Giới thiệu</a>
        <a href="thuc-don.html">Thực đơn</a>
        <a href="dat-ban.html">Đặt bàn</a>
        <a href="lien-he.html">Liên hệ</a>
      </div>

      <div class="footer-col">
        <h3>Giờ mở cửa</h3>
        ${RESTAURANT_CONFIG.hours.map(h =>
          `<p>${h.days}<br><strong style="color:rgba(255,255,255,.8)">${h.time}</strong></p>`
        ).join('')}
        <p style="margin-top:8px;font-size:0.8rem;opacity:.5">${RESTAURANT_CONFIG.hoursNote}</p>
      </div>

      <div class="footer-col">
        <h3>Liên hệ</h3>
        <p>📍 ${RESTAURANT_CONFIG.addressShort}</p>
        <p>📞 <a href="tel:${RESTAURANT_CONFIG.phone.replace(/\s/g,'')}">${RESTAURANT_CONFIG.phone}</a></p>
        <p>✉️ <a href="mailto:${RESTAURANT_CONFIG.email}">${RESTAURANT_CONFIG.email}</a></p>
      </div>
    </div>

    <hr class="footer-divider">

    <div class="footer-bottom">
      <p>© 2026 One x Pizza. Website đồ án môn Thiết kế & Triển khai Website.</p>
      <div>
        <a href="gioi-thieu.html">Giới thiệu</a>
        <a href="lien-he.html">Liên hệ</a>
        <a href="admin/index.html" style="opacity:.4;font-size:.75rem">Admin</a>
      </div>
    </div>
  </div>
</footer>`;

  // ── Inject ──────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    // Header
    const headerPlaceholder = document.getElementById('nav-placeholder');
    if (headerPlaceholder) {
      headerPlaceholder.outerHTML = headerHTML;
    } else {
      document.body.insertAdjacentHTML('afterbegin', headerHTML);
    }

    // Footer
    const footerPlaceholder = document.getElementById('footer-placeholder');
    if (footerPlaceholder) {
      footerPlaceholder.outerHTML = footerHTML;
    } else {
      document.body.insertAdjacentHTML('beforeend', footerHTML);
    }

    // Mobile menu toggle
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobile-nav');
    if (hamburger && mobileNav) {
      hamburger.addEventListener('click', function () {
        const open = mobileNav.classList.toggle('open');
        hamburger.classList.toggle('open', open);
        hamburger.setAttribute('aria-expanded', open);
        document.body.style.overflow = open ? 'hidden' : '';
      });

      // Close on nav link click
      mobileNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          mobileNav.classList.remove('open');
          hamburger.classList.remove('open');
          hamburger.setAttribute('aria-expanded', 'false');
          document.body.style.overflow = '';
        });
      });

      // Close on outside click
      document.addEventListener('click', function (e) {
        if (!hamburger.contains(e.target) && !mobileNav.contains(e.target)) {
          mobileNav.classList.remove('open');
          hamburger.classList.remove('open');
          hamburger.setAttribute('aria-expanded', 'false');
          document.body.style.overflow = '';
        }
      });
    }

    // Keyboard: Escape closes mobile nav
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileNav && mobileNav.classList.contains('open')) {
        mobileNav.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        hamburger.focus();
      }
    });

    // Header scroll effect
    initHeaderScroll();

    // Scroll animations
    initScrollAnimations();

    // DB init
    dbInit();
  });
})();
