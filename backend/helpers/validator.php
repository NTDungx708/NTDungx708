<?php
/**
 * backend/helpers/validator.php — Kiểm tra dữ liệu đầu vào
 */

class Validator {
    public static function validateReservationInput($data) {
        $errors = [];

        // 1. Họ và tên
        $name = trim($data['customer_name'] ?? $data['customerName'] ?? '');
        if ($name === '') {
            $errors['customer_name'] = 'Vui lòng nhập họ và tên.';
        } elseif (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 100) {
            $errors['customer_name'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
        }

        // 2. Số điện thoại (chuẩn Việt Nam 10 chữ số, đầu 0)
        $phone = trim($data['phone'] ?? '');
        $phoneClean = preg_replace('/[\s\-\.]/', '', $phone);
        if ($phoneClean === '') {
            $errors['phone'] = 'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^(03|05|07|08|09)\d{8}$/', $phoneClean)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (cần 10 chữ số, ví dụ 0912345678).';
        }

        // 3. Ngày đặt bàn (không được ở quá khứ)
        $date = trim($data['booking_date'] ?? $data['bookingDate'] ?? '');
        if ($date === '') {
            $errors['booking_date'] = 'Vui lòng chọn ngày đặt bàn.';
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $errors['booking_date'] = 'Định dạng ngày không hợp lệ (YYYY-MM-DD).';
            } else {
                $today = new DateTime('today');
                $maxDay = (new DateTime('today'))->modify('+60 days');
                if ($d < $today) {
                    $errors['booking_date'] = 'Ngày đặt bàn không thể là ngày trong quá khứ.';
                } elseif ($d > $maxDay) {
                    $errors['booking_date'] = 'Chỉ nhận đặt bàn trong vòng 60 ngày tới.';
                }
            }
        }

        // 4. Giờ đến: phải là một khung giờ nhà hàng nhận khách (giống danh sách trong dat-ban.html)
        $allowedTimes = ['10:00','11:00','11:30','12:00','12:30','13:00',
                         '17:00','17:30','18:00','18:30','19:00','19:30',
                         '20:00','20:30','21:00'];
        $time = trim($data['booking_time'] ?? $data['bookingTime'] ?? '');
        if ($time === '') {
            $errors['booking_time'] = 'Vui lòng chọn giờ đến.';
        } elseif (!in_array($time, $allowedTimes, true)) {
            $errors['booking_time'] = 'Giờ đến không nằm trong khung giờ nhận đặt bàn.';
        } elseif (empty($errors['booking_date']) && $date === date('Y-m-d') && $time <= date('H:i')) {
            $errors['booking_time'] = 'Giờ đến hôm nay đã qua, vui lòng chọn giờ muộn hơn.';
        }

        // 5. Số khách
        $partySize = (int)($data['party_size'] ?? $data['partySize'] ?? 0);
        if ($partySize < 1 || $partySize > 30) {
            $errors['party_size'] = 'Số lượng khách phải từ 1 đến 30 người.';
        }

        // 6. Ghi chú
        $note = trim($data['note'] ?? '');
        if (mb_strlen($note, 'UTF-8') > 500) {
            $errors['note'] = 'Ghi chú tối đa 500 ký tự.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'clean' => [
                // Lưu nguyên văn; giao diện quản trị tự escape khi hiển thị (escHtml)
                'customer_name' => $name,
                'phone'         => $phoneClean,
                'booking_date'  => $date,
                'booking_time'  => $time,
                'party_size'    => $partySize,
                'note'          => $note,
            ]
        ];
    }

    public static function validateStatusTransition($currentStatus, $newStatus) {
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($newStatus, $validStatuses, true)) {
            return [
                'valid' => false,
                'message' => "Trạng thái '$newStatus' không hợp lệ."
            ];
        }

        if ($currentStatus === $newStatus) {
            return ['valid' => true];
        }

        // Ma trận chuyển đổi trạng thái hợp lệ:
        // pending -> confirmed, cancelled
        // confirmed -> completed, cancelled
        // cancelled -> completed (không cho phép), pending (không khuyến khích)
        // completed -> kết thúc
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'cancelled' => ['pending'], // Cho phép mở lại nếu khách gọi lại xác nhận
            'completed' => []           // Đã phục vụ xong, không sửa lại
        ];

        if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus], true)) {
            $statusLabels = [
                'pending'   => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'cancelled' => 'Đã hủy',
                'completed' => 'Hoàn thành'
            ];
            $currentLabel = $statusLabels[$currentStatus] ?? $currentStatus;
            $newLabel = $statusLabels[$newStatus] ?? $newStatus;
            return [
                'valid' => false,
                'message' => "Không thể chuyển trạng thái từ '$currentLabel' sang '$newLabel'."
            ];
        }

        return ['valid' => true];
    }
}
