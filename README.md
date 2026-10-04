# ONE × PIZZA — Website & hệ thống quản trị đặt bàn

> Đồ án môn Thiết kế & Triển khai Website.
> **Địa chỉ nhà hàng:** 79 Hồ Tùng Mậu, Hà Nội · **Hotline:** 0869 564 200 · **Email:** 79hotungmau@gmail.com

---

## 1. Công nghệ thực dùng

| Thành phần | Công nghệ |
|---|---|
| Giao diện | HTML5, CSS3 thuần, JavaScript (Fetch API) |
| Backend | **PHP 7.4+ / 8.x** (API trả JSON, PDO) |
| Cơ sở dữ liệu | **MySQL / MariaDB** |
| Bảo mật | Mật khẩu băm bcrypt, token phiên lưu ở CSDL (hết hạn 8 giờ), prepared statements, kiểm tra dữ liệu phía server |

Dữ liệu đặt bàn được lưu ở **MySQL trên máy chủ**, không lưu ở `localStorage`. Vì vậy quản trị viên đăng nhập trên thiết bị khác vẫn xem được đơn.
Website **cần máy chủ chạy PHP + MySQL**. Mở trực tiếp file `.html` (file://) hoặc đưa lên hosting chỉ có web tĩnh (GitHub Pages, Netlify) thì đặt bàn/quản trị sẽ **không hoạt động**.

---

## 2. Cấu trúc dữ liệu

**`oxp_admins`**: `id`, `username` (duy nhất), `password_hash` (bcrypt), `full_name`, `role`, `token`, `token_expires_at`, `created_at`, `last_login`

**`oxp_reservations`**: `id`, `reference_code` (duy nhất, dạng `OXP-YYYYMMDD-NNN`), `customer_name`, `phone`, `booking_date`, `booking_time`, `party_size` (1–30), `note`, `status` (`pending` / `confirmed` / `cancelled` / `completed`), `admin_note`, `created_at`, `updated_at`

Chi tiết: [`backend/schema_mysql.sql`](backend/schema_mysql.sql)

**Quy tắc chuyển trạng thái** (kiểm tra ở server):

| Từ | Được chuyển sang |
|---|---|
| Chờ xác nhận | Đã xác nhận · Đã hủy (từ chối) |
| Đã xác nhận | Hoàn thành · Đã hủy |
| Đã hủy | Chờ xác nhận (mở lại) |
| Hoàn thành | — (kết thúc) |

**Kiểm tra khi đặt bàn** (cả trình duyệt lẫn server): họ tên 2–100 ký tự; SĐT di động Việt Nam 10 số; ngày từ hôm nay đến 60 ngày tới; giờ phải thuộc khung giờ nhận khách và chưa qua (nếu đặt cho hôm nay); 1–30 khách; ghi chú ≤ 500 ký tự.

---

## 3. Chạy trên máy cá nhân (XAMPP / Laragon)

1. Copy thư mục dự án vào `C:\xampp\htdocs\tmu`.
2. Mở XAMPP Control Panel → **Start** Apache và MySQL.
3. Mặc định [`backend/config.php`](backend/config.php) dùng `root` / mật khẩu rỗng / CSDL `onexpizza`, đúng với XAMPP. Nếu MySQL của bạn khác, tạo `backend/config.local.php` theo mẫu [`config.example.php`](backend/config.example.php).
4. Mở `http://localhost/tmu/backend/setup.php` → **tự đặt** tên đăng nhập và mật khẩu quản trị → bấm **Cài đặt**.
   - Script tự tạo CSDL (nếu chưa có), tạo bảng, tạo tài khoản admin, thêm 4 đơn mẫu (tùy chọn).
   - Sau khi cài xong, trang setup **tự khóa** (file `backend/db/installed.lock`).
5. Khách đặt bàn: `http://localhost/tmu/dat-ban.html` · Quản trị: `http://localhost/tmu/admin/`

Không có XAMPP mà có PHP trong PATH: chạy `start-server.bat` (PHP built-in server cổng 8000, MySQL vẫn phải bật riêng).

---

## 4. Đưa lên hosting để người khác xem (PHP + MySQL)

Dùng được với hosting PHP có MySQL bất kỳ (cPanel, InfinityFree, v.v.). Các bước chung:

1. **Tạo CSDL MySQL** trong trang quản lý hosting. Ghi lại 4 thông tin: *MySQL hostname*, *tên CSDL* (thường có tiền tố, ví dụ `if0_12345678_onexpizza`), *username*, *password*.
2. **Upload** nội dung file `dist/onexpizza-upload.zip` (giải nén) vào thư mục web gốc (`htdocs` hoặc `public_html`). Thư mục gốc phải chứa trực tiếp `index.html`, `admin/`, `backend/`…
3. Trên hosting, **tạo file `backend/config.local.php`** (copy từ `backend/config.example.php`) và điền 4 thông tin ở bước 1.
4. Mở `https://<tên-miền-của-bạn>/backend/setup.php` → đặt tài khoản và mật khẩu admin → **Cài đặt**.
   Nếu trang báo không ghi được file khóa, hãy **xóa `backend/setup.php`** trên hosting.
5. Gửi link `https://<tên-miền-của-bạn>/` cho người khác. Trang quản trị: `/admin/`.

> [!IMPORTANT]
> - Không đưa `backend/config.local.php` lên GitHub hay gửi cho người khác, vì file này chứa mật khẩu MySQL.
> - Mật khẩu admin chỉ do người cài đặt tự nhập ở `setup.php`. Mã nguồn và README không chứa mật khẩu nào.

---

## 5. Kịch bản kiểm thử hai thiết bị

1. **Điện thoại (khách):** mở `/dat-ban.html` → điền form → gửi. Màn hình hiện mã `OXP-…` với trạng thái **Chờ nhà hàng duyệt** (chưa phải xác nhận giữ bàn).
2. **Máy tính (quản trị):** mở `/admin/` → đăng nhập → đơn vừa gửi xuất hiện ở trạng thái **Chờ xác nhận**. Thử tìm theo mã/SĐT, lọc theo ngày.
3. Chuyển sang **Đã xác nhận** → bấm **Làm mới** / F5.
4. Mở trình duyệt khác hoặc tab ẩn danh, đăng nhập lại: đơn vẫn ở trạng thái **Đã xác nhận**, có thời điểm tạo và thời điểm cập nhật.

---

## 6. Cấu trúc thư mục

```
tmu/
├── index.html, gioi-thieu.html, thuc-don.html, dat-ban.html, lien-he.html, 404.html
├── admin/index.html          # Đăng nhập (xác thực ở server)
├── admin/dashboard.html      # Danh sách, chi tiết, tìm kiếm/lọc, đổi trạng thái
├── css/style.css
├── js/data.js                # Cấu hình nhà hàng, thực đơn, đường dẫn API
├── js/db.js                  # Gọi API backend + tiện ích hiển thị
├── backend/
│   ├── config.php            # Cấu hình mặc định (XAMPP)
│   ├── config.example.php    # Mẫu cho config.local.php (hosting)
│   ├── db.php                # Kết nối PDO, sinh mã đặt bàn
│   ├── schema_mysql.sql      # Cấu trúc bảng
│   ├── setup.php             # Cài đặt 1 lần, tự khóa
│   ├── api/auth.php          # Đăng nhập / đăng xuất / kiểm tra phiên
│   ├── api/reservations.php  # Tạo đơn (công khai) · xem/cập nhật (cần đăng nhập)
│   ├── api/stats.php         # Thống kê (cần đăng nhập)
│   └── helpers/              # Phản hồi JSON, kiểm tra dữ liệu, xác thực token
└── .htaccess                 # Chặn liệt kê thư mục, chặn file nội bộ
```

---

## 7. Chuyển tiếp dữ liệu cũ

- Đơn đặt bàn cũ trong `localStorage` (bản demo trước) **không bị xóa**. Lần đầu mở site, `js/db.js` sao chép chúng sang khóa `oxp_reservations_legacy_backup` trong chính trình duyệt đó.
- 4 đơn mẫu trong `js/data.js` được `setup.php` thêm vào MySQL nếu bạn chọn (chỉ khi bảng còn trống).
- **Chưa có** chức năng tự động đẩy đơn cũ từ `localStorage` lên MySQL.

---

## 8. Hạn chế hiện tại

- Chưa có giao diện đổi mật khẩu / quản lý nhiều tài khoản admin. Muốn đổi thì xóa dòng trong `oxp_admins` bằng phpMyAdmin, xóa `installed.lock`, rồi chạy lại `setup.php`.
- Chưa giới hạn số lần đăng nhập sai và chưa chống spam form đặt bàn.
- Token admin được lưu trong `localStorage` của trình duyệt quản trị. Server vẫn kiểm tra token ở mọi API quản trị.
