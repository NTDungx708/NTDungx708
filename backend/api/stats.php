<?php
/**
 * backend/api/stats.php — API Thống kê số lượng Đặt bàn theo trạng thái (Admin)
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/auth_check.php';
require_once __DIR__ . '/../db.php';

$config = require __DIR__ . '/../config.php';
sendCorsHeaders($config);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonError('Phương thức không hợp lệ. Yêu cầu GET.', 405);
}

try {
    $admin = requireAdminAuth();
    $pdo = Database::getConnection();

    $stmt = $pdo->query("
        SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed
        FROM oxp_reservations
    ");
    $stats = $stmt->fetch();

    $data = [
        'total'     => (int)($stats['total'] ?? 0),
        'pending'   => (int)($stats['pending'] ?? 0),
        'confirmed' => (int)($stats['confirmed'] ?? 0),
        'cancelled' => (int)($stats['cancelled'] ?? 0),
        'completed' => (int)($stats['completed'] ?? 0),
    ];

    jsonResponse(true, $data, 'Lấy thống kê thành công.');

} catch (Exception $e) {
    jsonError('Lỗi máy chủ nội bộ: ' . $e->getMessage(), 500);
}
