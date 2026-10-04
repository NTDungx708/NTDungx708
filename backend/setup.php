<?php
/**
 * backend/setup.php — Khởi tạo CSDL & tạo tài khoản quản trị đầu tiên (chạy 1 lần)
 *
 * - Không có mật khẩu nào ghi sẵn trong mã nguồn: người cài đặt tự nhập mật khẩu admin.
 * - Sau khi cài thành công, script tự khóa bằng file backend/db/installed.lock.
 *   Muốn chạy lại: xóa file installed.lock (qua File Manager / FTP).
 */

header('Content-Type: text/html; charset=utf-8');

$config   = require __DIR__ . '/config.php';
$dbCfg    = $config['db'];
$driver   = $dbCfg['driver'] ?? 'mysql';
$lockFile = __DIR__ . '/db/installed.lock';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function page_start() {
    echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><meta name='robots' content='noindex,nofollow'>
<meta name='viewport' content='width=device-width,initial-scale=1'><title>Cài đặt — One x Pizza</title>
<style>body{font-family:system-ui,sans-serif;background:#F6F0E6;padding:32px 16px;color:#1D211F;line-height:1.6}
.card{background:#fff;max-width:620px;margin:0 auto;padding:32px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08)}
h1{color:#A54030;font-size:1.5rem;margin-top:0}label{display:block;font-weight:600;margin-top:14px}
input[type=text],input[type=password]{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;box-sizing:border-box;font-size:1rem}
.ok{color:#2e7d32}.err{color:#c62828}.info{color:#1565c0}
.box{padding:12px 14px;border-radius:6px;margin:12px 0}.box.err{background:#ffebee}.box.ok{background:#e8f5e9}.box.warn{background:#fdf6e2}
button,.btn{display:inline-block;background:#A54030;color:#fff;border:0;text-decoration:none;padding:11px 22px;border-radius:6px;font-weight:600;margin-top:18px;cursor:pointer;font-size:1rem}
code{background:#f3f1ed;padding:1px 5px;border-radius:4px}</style></head><body><div class='card'>
<h1>🍕 ONE × PIZZA — Cài đặt hệ thống đặt bàn</h1>";
}
function page_end() { echo "</div></body></html>"; exit; }

page_start();

// ── 0. Đã cài rồi → khóa ─────────────────────────────────────
if (is_file($lockFile)) {
    http_response_code(403);
    echo "<div class='box warn'>🔒 Hệ thống đã được cài đặt. Trang này đã bị khóa để bảo vệ tài khoản quản trị.</div>";
    echo "<p>Nếu thật sự cần chạy lại, hãy xóa file <code>backend/db/installed.lock</code> trên máy chủ.</p>";
    echo "<a class='btn' href='../admin/index.html'>Đến trang quản trị →</a>";
    page_end();
}

$errors = [];
$old = ['username' => 'admin', 'full_name' => 'Quản trị viên', 'seed' => true];

// ── 1. Xử lý form ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $pass     = (string)($_POST['password'] ?? '');
    $pass2    = (string)($_POST['password_confirm'] ?? '');
    $seed     = !empty($_POST['seed']);
    $old = ['username' => $username, 'full_name' => $fullName, 'seed' => $seed];

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) $errors[] = 'Tên đăng nhập 3–30 ký tự, chỉ gồm chữ không dấu, số, dấu gạch dưới.';
    if ($fullName === '' || mb_strlen($fullName, 'UTF-8') > 100) $errors[] = 'Vui lòng nhập họ tên (tối đa 100 ký tự).';
    if (strlen($pass) < 8) $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự.';
    if ($pass !== $pass2) $errors[] = 'Hai lần nhập mật khẩu không khớp.';

    if (!$errors) {
        try {
            // ── Kết nối CSDL ──
            if ($driver === 'sqlite') {
                $path = $dbCfg['sqlite_path'];
                if (!is_dir(dirname($path))) mkdir(dirname($path), 0775, true);
                $pdo = new PDO('sqlite:' . $path);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec("CREATE TABLE IF NOT EXISTS oxp_admins (
                    id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL,
                    full_name TEXT NOT NULL DEFAULT 'Quản trị viên', role TEXT NOT NULL DEFAULT 'admin',
                    token TEXT, token_expires_at TEXT, created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')), last_login TEXT)");
                $pdo->exec("CREATE TABLE IF NOT EXISTS oxp_reservations (
                    id TEXT PRIMARY KEY, reference_code TEXT NOT NULL UNIQUE, customer_name TEXT NOT NULL, phone TEXT NOT NULL,
                    booking_date TEXT NOT NULL, booking_time TEXT NOT NULL, party_size INTEGER NOT NULL, note TEXT,
                    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','confirmed','cancelled','completed')),
                    admin_note TEXT, created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                    updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')))");
            } else {
                $host = $dbCfg['host']; $port = (int)$dbCfg['port']; $name = $dbCfg['dbname'];
                $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
                try {
                    // Hosting: CSDL đã được tạo sẵn trong trang quản lý → kết nối thẳng
                    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $dbCfg['username'], $dbCfg['password'], $opts);
                } catch (PDOException $e) {
                    // Máy cá nhân (XAMPP/Laragon): CSDL chưa có (lỗi 1049) → thử tự tạo
                    if (strpos($e->getMessage(), '1049') === false) throw $e;
                    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) throw new Exception('Tên CSDL không hợp lệ.');
                    $root = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $dbCfg['username'], $dbCfg['password'], $opts);
                    $root->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $dbCfg['username'], $dbCfg['password'], $opts);
                }
                // Chạy schema_mysql.sql (bỏ dòng chú thích, tách theo dấu ;)
                $sql = file_get_contents(__DIR__ . '/schema_mysql.sql');
                $sql = preg_replace('/^\s*--.*$/m', '', $sql);
                foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
                    $pdo->exec($stmt);
                }
            }
            echo "<p class='ok'>✓ Đã kết nối CSDL và tạo bảng <code>oxp_admins</code>, <code>oxp_reservations</code>.</p>";

            // ── Tài khoản quản trị ──
            $chk = $pdo->prepare('SELECT id FROM oxp_admins WHERE username = ?');
            $chk->execute([$username]);
            if ($chk->fetch()) {
                echo "<p class='info'>ℹ Tài khoản <code>" . h($username) . "</code> đã tồn tại — giữ nguyên, không đổi mật khẩu.</p>";
            } else {
                $ins = $pdo->prepare("INSERT INTO oxp_admins (username, password_hash, full_name, role) VALUES (?, ?, ?, 'admin')");
                $ins->execute([$username, password_hash($pass, PASSWORD_BCRYPT), $fullName]);
                echo "<p class='ok'>✓ Đã tạo tài khoản quản trị <code>" . h($username) . "</code> (mật khẩu được băm bcrypt, không lưu dạng rõ).</p>";
            }

            // ── Dữ liệu mẫu (chỉ khi bảng trống) ──
            $count = (int)$pdo->query('SELECT COUNT(*) FROM oxp_reservations')->fetchColumn();
            if ($seed && $count === 0) {
                $rows = [
                    ['r-seed-001','OXP-20260928-001','Nguyễn Văn Minh','0901234567','2026-10-05','19:00',4,'Bàn gần cửa sổ nếu có','confirmed','2026-09-28 10:30:00'],
                    ['r-seed-002','OXP-20260928-002','Trần Thị Lan','0912876543','2026-10-06','18:30',2,'Kỷ niệm ngày cưới, cần hỗ trợ đặt hoa','pending','2026-09-28 14:15:00'],
                    ['r-seed-003','OXP-20260929-001','Lê Hoàng Phúc','0933456789','2026-10-10','20:00',8,'','pending','2026-09-29 09:00:00'],
                    ['r-seed-004','OXP-20260927-001','Phạm Thị Thu','0888111222','2026-09-27','12:00',3,'','cancelled','2026-09-25 16:45:00'],
                ];
                $insR = $pdo->prepare('INSERT INTO oxp_reservations (id, reference_code, customer_name, phone, booking_date, booking_time, party_size, note, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
                foreach ($rows as $r) { $r[] = $r[9]; $insR->execute($r); }
                echo "<p class='ok'>✓ Đã thêm 4 đơn đặt bàn mẫu (lấy từ dữ liệu mẫu cũ trong js/data.js).</p>";
            } elseif ($count > 0) {
                echo "<p class='info'>ℹ CSDL đã có {$count} đơn đặt bàn — giữ nguyên, không ghi đè.</p>";
            }

            // ── Khóa trình cài đặt ──
            if (@file_put_contents($lockFile, 'installed ' . date('c')) !== false) {
                echo "<div class='box ok'>🔒 Trang cài đặt đã tự khóa.</div>";
            } else {
                echo "<div class='box err'>⚠️ Không ghi được file khóa <code>backend/db/installed.lock</code>. Hãy <strong>xóa file backend/setup.php</strong> trên máy chủ để không ai chạy lại được.</div>";
            }
            echo "<p><strong>🎉 Cài đặt xong.</strong></p><a class='btn' href='../admin/index.html'>Đăng nhập quản trị →</a>";
            page_end();
        } catch (Exception $e) {
            $errors[] = 'Lỗi CSDL: ' . $e->getMessage() . ' — kiểm tra lại thông tin trong backend/config.local.php.';
        }
    }
}

// ── 2. Form ───────────────────────────────────────────────────
echo "<p>Trang này chạy <strong>một lần</strong> để tạo bảng dữ liệu và tài khoản quản trị đầu tiên. Mật khẩu do bạn tự đặt, không được ghi trong mã nguồn.</p>";
echo "<p class='info'>CSDL: <code>" . h($driver) . "</code>" . ($driver === 'mysql' ? " — <code>" . h($dbCfg['username']) . "@" . h($dbCfg['host']) . "/" . h($dbCfg['dbname']) . "</code>" : '') . "</p>";
foreach ($errors as $e) echo "<div class='box err'>" . h($e) . "</div>";
?>
<form method="post" autocomplete="off">
  <label for="username">Tên đăng nhập quản trị</label>
  <input type="text" id="username" name="username" value="<?= h($old['username']) ?>" required>
  <label for="full_name">Họ tên hiển thị</label>
  <input type="text" id="full_name" name="full_name" value="<?= h($old['full_name']) ?>" required>
  <label for="password">Mật khẩu (tối thiểu 8 ký tự)</label>
  <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
  <label for="password_confirm">Nhập lại mật khẩu</label>
  <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
  <label style="font-weight:400"><input type="checkbox" name="seed" value="1" <?= $old['seed'] ? 'checked' : '' ?>> Thêm 4 đơn đặt bàn mẫu để demo (chỉ khi CSDL đang trống)</label>
  <button type="submit">Cài đặt</button>
</form>
<?php page_end();
