<?php
/**
 * backend/api/reservations.php — API Quản lý Đặt bàn (Đặt bàn mới & Quản trị)
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/auth_check.php';
require_once __DIR__ . '/../db.php';

$config = require __DIR__ . '/../config.php';
sendCorsHeaders($config);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// Hosting miễn phí hay chặn PATCH/PUT → cho phép cập nhật qua POST ?action=update
$isUpdate = ($method === 'PATCH' || $method === 'PUT' || ($method === 'POST' && $action === 'update'));

try {
    $pdo = Database::getConnection();

    // ─────────────────────────────────────────────────────────────
    // 1. TẠO ĐƠN ĐẶT BÀN MỚI (POST) — Dành cho khách hàng (Public)
    // ─────────────────────────────────────────────────────────────
    if ($method === 'POST' && !$isUpdate) {
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        if (!is_array($body)) {
            $body = $_POST;
        }

        // Kiểm tra dữ liệu đầu vào
        $validation = Validator::validateReservationInput($body);
        if (!$validation['valid']) {
            jsonError('Dữ liệu đặt bàn không hợp lệ.', 422, $validation['errors']);
        }

        $clean = $validation['clean'];

        // Sinh ID định danh duy nhất cho bản ghi
        $id = 'r-' . round(microtime(true) * 1000) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);

        // Sinh mã tra cứu thân thiện duy nhất: OXP-YYYYMMDD-NNN
        $refCode = Database::generateReferenceCode($pdo, $clean['booking_date']);

        $now = date('Y-m-d H:i:s');
        $initialStatus = 'pending'; // Bắt buộc là "Chờ xác nhận"

        $stmt = $pdo->prepare("
            INSERT INTO oxp_reservations 
            (id, reference_code, customer_name, phone, booking_date, booking_time, party_size, note, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id,
            $refCode,
            $clean['customer_name'],
            $clean['phone'],
            $clean['booking_date'],
            $clean['booking_time'],
            $clean['party_size'],
            $clean['note'],
            $initialStatus,
            $now,
            $now
        ]);

        $createdReservation = [
            'id'             => $id,
            'referenceCode'  => $refCode,
            'customerName'   => $clean['customer_name'],
            'phone'          => $clean['phone'],
            'bookingDate'    => $clean['booking_date'],
            'bookingTime'    => $clean['booking_time'],
            'partySize'      => $clean['party_size'],
            'note'           => $clean['note'],
            'status'         => $initialStatus,
            'statusLabel'    => 'Chờ xác nhận',
            'createdAt'      => $now
        ];

        jsonResponse(true, $createdReservation, 'Yêu cầu đặt bàn đã được gửi tới hệ thống One x Pizza.', 201);
    }

    // ─────────────────────────────────────────────────────────────
    // 2. XEM DANH SÁCH HOẶC CHI TIẾT ĐẶT BÀN (GET) — Yêu cầu Admin
    // ─────────────────────────────────────────────────────────────
    elseif ($method === 'GET') {
        $admin = requireAdminAuth();

        $id = $_GET['id'] ?? null;

        // Chi tiết 1 đơn
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM oxp_reservations WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if (!$item) {
                jsonError("Không tìm thấy đơn đặt bàn với mã $id.", 404);
            }
            jsonResponse(true, $item, 'Lấy chi tiết đơn đặt bàn thành công.');
        }

        // Lấy danh sách có tìm kiếm và bộ lọc
        $status = $_GET['status'] ?? 'all';
        $date   = $_GET['date'] ?? '';
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT * FROM oxp_reservations WHERE 1=1";
        $params = [];

        // Lọc theo trạng thái
        if ($status !== 'all' && in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        // Lọc theo ngày đặt bàn
        if ($date !== '') {
            $sql .= " AND booking_date = ?";
            $params[] = $date;
        }

        // Tìm kiếm theo từ khóa (Mã tra cứu, Tên khách, Số điện thoại)
        if ($search !== '') {
            $sql .= " AND (reference_code LIKE ? OR customer_name LIKE ? OR phone LIKE ?)";
            $keyword = "%{$search}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $sql .= " ORDER BY created_at DESC, booking_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Chuyển đổi format camelCase để tương thích mượt mà với frontend hiện tại
        $formatted = array_map(function ($r) {
            return [
                'id'            => $r['id'],
                'referenceCode' => $r['reference_code'],
                'customerName'  => $r['customer_name'],
                'phone'         => $r['phone'],
                'bookingDate'   => $r['booking_date'],
                'bookingTime'   => $r['booking_time'],
                'partySize'     => (int)$r['party_size'],
                'note'          => $r['note'] ?? '',
                'status'        => $r['status'],
                'adminNote'     => $r['admin_note'] ?? '',
                'createdAt'     => $r['created_at'],
                'updatedAt'     => $r['updated_at'] ?? $r['created_at']
            ];
        }, $rows);

        jsonResponse(true, $formatted, 'Lấy danh sách đặt bàn thành công.');
    }

    // ─────────────────────────────────────────────────────────────
    // 3. CẬP NHẬT TRẠNG THÁI / GHI CHÚ ĐẶT BÀN (PATCH / PUT) — Admin
    // ─────────────────────────────────────────────────────────────
    elseif ($isUpdate) {
        $admin = requireAdminAuth();

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];

        $id = $_GET['id'] ?? $body['id'] ?? null;
        if (!$id) {
            jsonError('Thiếu mã ID đơn đặt bàn cần cập nhật.', 400);
        }

        // Tìm đơn hiện tại trong database
        $stmt = $pdo->prepare("SELECT * FROM oxp_reservations WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        if (!$current) {
            jsonError("Không tìm thấy đơn đặt bàn với mã $id.", 404);
        }

        $newStatus  = $body['status'] ?? $current['status'];
        $adminNote  = isset($body['adminNote']) ? trim($body['adminNote']) : (isset($body['admin_note']) ? trim($body['admin_note']) : $current['admin_note']);

        // Kiểm tra logic chuyển đổi trạng thái
        if ($newStatus !== $current['status']) {
            $transitionCheck = Validator::validateStatusTransition($current['status'], $newStatus);
            if (!$transitionCheck['valid']) {
                jsonError($transitionCheck['message'], 422);
            }
        }

        $now = date('Y-m-d H:i:s');
        $updateStmt = $pdo->prepare("
            UPDATE oxp_reservations 
            SET status = ?, admin_note = ?, updated_at = ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$newStatus, $adminNote, $now, $id]);

        jsonResponse(true, [
            'id'        => $id,
            'status'    => $newStatus,
            'adminNote' => $adminNote,
            'updatedAt' => $now
        ], "Cập nhật trạng thái đơn đặt bàn thành công.");
    }

    // ─────────────────────────────────────────────────────────────
    // 4. PHƯƠNG THỨC KHÔNG HỖ TRỢ
    // ─────────────────────────────────────────────────────────────
    else {
        jsonError("Phương thức HTTP '$method' không được hỗ trợ.", 405);
    }

} catch (Exception $e) {
    jsonError('Lỗi máy chủ nội bộ: ' . $e->getMessage(), 500);
}
