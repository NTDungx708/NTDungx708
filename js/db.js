/**
 * js/db.js — One x Pizza
 * Hệ thống kết nối API Backend PHP & MySQL chính thức
 * Hỗ trợ đồng bộ hóa liên thiết bị và quản trị bảo mật phía máy chủ
 */

const DB_KEYS = {
  LEGACY_BACKUP: 'oxp_reservations_legacy_backup',
  LOCAL_BACKUP: 'oxp_reservations_client_backup',
  ADMIN_TOKEN: 'oxp_admin_token',
  ADMIN_PROFILE: 'oxp_admin_profile'
};

// ── Bảo toàn dữ liệu cũ trong LocalStorage (Chuyển tiếp an toàn) ──
function dbInit() {
  try {
    const oldReservations = localStorage.getItem('oxp_reservations');
    if (oldReservations && !localStorage.getItem(DB_KEYS.LEGACY_BACKUP)) {
      // Lưu lại bản sao lưu an toàn của dữ liệu cũ, không xóa âm thầm
      localStorage.setItem(DB_KEYS.LEGACY_BACKUP, oldReservations);
    }
  } catch (e) {
    console.warn('LocalStorage backup note:', e);
  }
}

// ── API: Đặt bàn khách hàng (Lưu trực tiếp vào CSDL máy chủ) ──
async function apiSaveReservation(data) {
  const url = `${API_CONFIG.baseUrl}/reservations.php`;

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(data)
  });

  const resJson = await response.json();

  if (!response.ok || !resJson.success) {
    const errorMsg = resJson.message || 'Không thể gửi yêu cầu đặt bàn.';
    const err = new Error(errorMsg);
    err.errors = resJson.errors || {};
    throw err;
  }

  // Lưu 1 bản ghi cục bộ làm client backup (để khách tiện tra cứu lịch sử trên máy mình nếu cần)
  try {
    const backups = JSON.parse(localStorage.getItem(DB_KEYS.LOCAL_BACKUP) || '[]');
    backups.unshift(resJson.data);
    localStorage.setItem(DB_KEYS.LOCAL_BACKUP, JSON.stringify(backups.slice(0, 10)));
  } catch (e) {
    // bỏ qua lỗi storage
  }

  return resJson.data;
}

// ── API Quản trị: Lấy danh sách đặt bàn từ CSDL ──
async function apiGetReservations(filters = {}) {
  const token = adminGetToken();
  if (!token) {
    throw new Error('Yêu cầu đăng nhập quản trị viên.');
  }

  const queryParams = new URLSearchParams();
  if (filters.status && filters.status !== 'all') {
    queryParams.set('status', filters.status);
  }
  if (filters.date) {
    queryParams.set('date', filters.date);
  }
  if (filters.search) {
    queryParams.set('search', filters.search);
  }

  const queryString = queryParams.toString() ? `?${queryParams.toString()}` : '';
  const url = `${API_CONFIG.baseUrl}/reservations.php${queryString}`;

  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`, 'X-Admin-Token': token,
      'Accept': 'application/json'
    }
  });

  const resJson = await response.json();

  if (response.status === 401) {
    adminClearSession();
    throw new Error('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
  }

  if (!response.ok || !resJson.success) {
    throw new Error(resJson.message || 'Không thể tải danh sách đặt bàn từ máy chủ.');
  }

  return resJson.data || [];
}

// ── API Quản trị: Cập nhật trạng thái đặt bàn ──
async function apiUpdateReservationStatus(id, newStatus, adminNote = '') {
  const token = adminGetToken();
  if (!token) {
    throw new Error('Yêu cầu đăng nhập quản trị viên.');
  }

  const url = `${API_CONFIG.baseUrl}/reservations.php?action=update&id=${encodeURIComponent(id)}`;

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`, 'X-Admin-Token': token,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      status: newStatus,
      adminNote: adminNote
    })
  });

  const resJson = await response.json();

  if (response.status === 401) {
    adminClearSession();
    throw new Error('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
  }

  if (!response.ok || !resJson.success) {
    throw new Error(resJson.message || 'Cập nhật trạng thái thất bại.');
  }

  return resJson.data;
}

// ── API Quản trị: Lấy số liệu thống kê từ CSDL ──
async function apiGetStats() {
  const token = adminGetToken();
  if (!token) {
    throw new Error('Yêu cầu đăng nhập quản trị viên.');
  }

  const url = `${API_CONFIG.baseUrl}/stats.php`;

  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${token}`, 'X-Admin-Token': token,
      'Accept': 'application/json'
    }
  });

  const resJson = await response.json();

  if (response.status === 401) {
    adminClearSession();
    throw new Error('Phiên làm việc đã hết hạn.');
  }

  if (!response.ok || !resJson.success) {
    throw new Error(resJson.message || 'Không thể tải số liệu thống kê.');
  }

  return resJson.data;
}

// ── API Xác thực Quản trị viên (Đăng nhập vào backend qua Bcrypt & Token) ──
async function apiAdminLogin(username, password) {
  const url = `${API_CONFIG.baseUrl}/auth.php?action=login`;

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ username, password })
  });

  const resJson = await response.json();

  if (!response.ok || !resJson.success) {
    throw new Error(resJson.message || 'Đăng nhập thất bại.');
  }

  const { token, ...profile } = resJson.data;
  localStorage.setItem(DB_KEYS.ADMIN_TOKEN, token);
  localStorage.setItem(DB_KEYS.ADMIN_PROFILE, JSON.stringify(profile));

  return resJson.data;
}

// ── API Đăng xuất Quản trị viên ──
async function apiAdminLogout() {
  const token = adminGetToken();
  if (token) {
    try {
      await fetch(`${API_CONFIG.baseUrl}/auth.php?action=logout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`, 'X-Admin-Token': token
        }
      });
    } catch (e) {
      // bỏ qua lỗi mạng khi logout
    }
  }
  adminClearSession();
}

// ── Kiểm tra token với máy chủ ──
async function apiAdminVerifySession() {
  const token = adminGetToken();
  if (!token) return false;

  try {
    const res = await fetch(`${API_CONFIG.baseUrl}/auth.php?action=me`, {
      method: 'GET',
      headers: { 'Authorization': `Bearer ${token}`, 'X-Admin-Token': token }
    });
    const data = await res.json();
    return res.ok && data.success;
  } catch (e) {
    return false;
  }
}

function adminGetToken() {
  return localStorage.getItem(DB_KEYS.ADMIN_TOKEN);
}

function adminGetProfile() {
  try {
    return JSON.parse(localStorage.getItem(DB_KEYS.ADMIN_PROFILE));
  } catch {
    return null;
  }
}

function adminIsLoggedIn() {
  return !!adminGetToken();
}

function adminClearSession() {
  localStorage.removeItem(DB_KEYS.ADMIN_TOKEN);
  localStorage.removeItem(DB_KEYS.ADMIN_PROFILE);
}

// ── Formatting Helpers (Dùng chung cho toàn bộ website) ────
function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const [y, m, d] = dateStr.split('-');
  return `${d}/${m}/${y}`;
}

function formatDateTime(isoStr) {
  if (!isoStr) return '';
  // Xử lý cả chuỗi ISO (2026-10-03T08:00:00) và MySQL DATETIME (2026-10-03 08:00:00)
  const dt = new Date(isoStr.replace(' ', 'T'));
  if (isNaN(dt.getTime())) return isoStr;
  return dt.toLocaleString('vi-VN', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}

// ── Menu Helpers (Dành cho trang Thực đơn & Trang chủ) ─────
function getMenuByCategory(categoryId) {
  if (!categoryId) return MENU_ITEMS.filter(i => i.available);
  return MENU_ITEMS.filter(i => i.categoryId === categoryId && i.available);
}

function getCategoryById(id) {
  return CATEGORIES.find(c => c.id === id);
}

function getFeaturedItems() {
  return MENU_ITEMS.filter(i => i.featured && i.available);
}

function searchMenu(query) {
  const q = query.toLowerCase().trim();
  if (!q) return MENU_ITEMS.filter(i => i.available);
  return MENU_ITEMS.filter(i =>
    i.available && (
      i.name.toLowerCase().includes(q) ||
      i.description.toLowerCase().includes(q) ||
      i.ingredients.toLowerCase().includes(q)
    )
  );
}

// ── Scroll Animation ──────────────────────────────────────
function initScrollAnimations() {
  const els = document.querySelectorAll('.fade-up');
  if (!els.length) return;
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  els.forEach(el => io.observe(el));
}

// ── Shared Header Scroll ──────────────────────────────────
function initHeaderScroll() {
  const header = document.querySelector('.site-header');
  if (!header) return;
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
}
