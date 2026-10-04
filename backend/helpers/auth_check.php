<?php
/**
 * backend/helpers/auth_check.php — Middleware xác thực quản trị viên
 */

require_once __DIR__ . '/response.php';
require_once __DIR__ . '/../db.php';

function requireAdminAuth(): array {
    $token = null;

    // 1. Authorization: Bearer <token> (một số hosting chuyển header này sang REDIRECT_HTTP_AUTHORIZATION)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!$authHeader && function_exists('getallheaders')) {
        foreach (getallheaders() as $k => $v) {
            if (strcasecmp($k, 'Authorization') === 0) { $authHeader = $v; break; }
        }
    }

    if (preg_match('/Bearer\s+([A-Fa-f0-9]{64})/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    // 2. Dự phòng: header riêng X-Admin-Token (hosting dùng chung hay xóa header Authorization)
    if (!$token && !empty($_SERVER['HTTP_X_ADMIN_TOKEN']) && preg_match('/^[A-Fa-f0-9]{64}$/', $_SERVER['HTTP_X_ADMIN_TOKEN'])) {
        $token = $_SERVER['HTTP_X_ADMIN_TOKEN'];
    }

    if (!$token) {
        jsonError('Yêu cầu xác thực. Vui lòng đăng nhập để thực hiện thao tác quản trị.', 401);
    }

    try {
        $pdo = Database::getConnection();

        // Kiểm tra token trong bảng oxp_admins và kiểm tra thời hạn
        $stmt = $pdo->prepare("
            SELECT id, username, full_name, role, token_expires_at 
            FROM oxp_admins 
            WHERE token = ? 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $admin = $stmt->fetch();

        if (!$admin) {
            jsonError('Phiên đăng nhập không hợp lệ hoặc đã bị hủy. Vui lòng đăng nhập lại.', 401);
        }

        // Kiểm tra hạn sử dụng của token
        if (!empty($admin['token_expires_at'])) {
            $expiresAt = new DateTime($admin['token_expires_at']);
            $now = new DateTime();
            if ($now > $expiresAt) {
                jsonError('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 401);
            }
        }

        return $admin;
    } catch (Exception $e) {
        jsonError('Lỗi xác thực hệ thống: ' . $e->getMessage(), 500);
    }
}
