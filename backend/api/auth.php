<?php
/**
 * backend/api/auth.php — API Xác thực Quản trị viên (Login / Logout / Me)
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth_check.php';
require_once __DIR__ . '/../db.php';

$config = require __DIR__ . '/../config.php';
sendCorsHeaders($config);

$action = $_GET['action'] ?? 'login';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = Database::getConnection();

    // ─────────────────────────────────────────────────────────────
    // 1. ĐĂNG NHẬP (POST /backend/api/auth.php?action=login)
    // ─────────────────────────────────────────────────────────────
    if ($action === 'login') {
        if ($method !== 'POST') {
            jsonError('Phương thức không hợp lệ. Yêu cầu POST.', 405);
        }

        $rawInput = file_get_contents('php://input');
        $body = json_decode($rawInput, true);
        if (!is_array($body)) {
            $body = $_POST;
        }

        $username = trim($body['username'] ?? '');
        $password = (string)($body['password'] ?? '');

        if ($username === '' || $password === '') {
            jsonError('Vui lòng nhập tên đăng nhập và mật khẩu.', 400);
        }

        // Truy vấn tài khoản admin trong CSDL
        $stmt = $pdo->prepare("
            SELECT id, username, password_hash, full_name, role 
            FROM oxp_admins 
            WHERE username = ? 
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Kiểm tra mật khẩu băm với Bcrypt
        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            jsonError('Tên đăng nhập hoặc mật khẩu không chính xác.', 401);
        }

        // Sinh token ngẫu nhiên bảo mật (64 ký tự hex)
        $token = bin2hex(random_bytes(32));
        $lifetime = (int)($config['security']['token_lifetime'] ?? 86400);
        $expiresAt = date('Y-m-d H:i:s', time() + $lifetime);
        $now = date('Y-m-d H:i:s');

        // Lưu token và thời điểm đăng nhập vào cơ sở dữ liệu
        $updateStmt = $pdo->prepare("
            UPDATE oxp_admins 
            SET token = ?, token_expires_at = ?, last_login = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$token, $expiresAt, $now, $admin['id']]);

        jsonResponse(true, [
            'token'      => $token,
            'username'   => $admin['username'],
            'full_name'  => $admin['full_name'],
            'role'       => $admin['role'],
            'expires_at' => $expiresAt
        ], 'Đăng nhập thành công.');
    }

    // ─────────────────────────────────────────────────────────────
    // 2. ĐĂNG XUẤT (POST /backend/api/auth.php?action=logout)
    // ─────────────────────────────────────────────────────────────
    elseif ($action === 'logout') {
        $admin = requireAdminAuth();

        // Hủy token trong CSDL
        $stmt = $pdo->prepare("UPDATE oxp_admins SET token = NULL, token_expires_at = NULL WHERE id = ?");
        $stmt->execute([$admin['id']]);

        jsonResponse(true, null, 'Đã đăng xuất thành công.');
    }

    // ─────────────────────────────────────────────────────────────
    // 3. KIỂM TRA PHIÊN (GET /backend/api/auth.php?action=me)
    // ─────────────────────────────────────────────────────────────
    elseif ($action === 'me') {
        $admin = requireAdminAuth();
        jsonResponse(true, [
            'id'        => $admin['id'],
            'username'  => $admin['username'],
            'full_name' => $admin['full_name'],
            'role'      => $admin['role']
        ], 'Phiên đăng nhập hợp lệ.');
    }

    else {
        jsonError("Hành động '$action' không được hỗ trợ.", 404);
    }

} catch (Exception $e) {
    jsonError('Lỗi máy chủ nội bộ: ' . $e->getMessage(), 500);
}
