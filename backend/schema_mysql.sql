-- ============================================================
-- ONE × PIZZA — MySQL Schema (hệ thống đặt bàn)
-- Địa chỉ nhà hàng: 79 Hồ Tùng Mậu, Hà Nội
--
-- Chạy bên trong CSDL đã chọn (không tự tạo database, vì hosting
-- dùng chung thường cấp sẵn tên CSDL và không cho CREATE DATABASE).
-- Có thể import file này bằng phpMyAdmin, hoặc để backend/setup.php tự chạy.
-- ============================================================

-- Tài khoản quản trị: mật khẩu chỉ lưu dạng băm bcrypt
CREATE TABLE IF NOT EXISTS `oxp_admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL DEFAULT 'Quản trị viên',
    `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
    `token` VARCHAR(255) NULL,
    `token_expires_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login` DATETIME NULL,
    INDEX `idx_admin_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đơn đặt bàn
CREATE TABLE IF NOT EXISTS `oxp_reservations` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `reference_code` VARCHAR(30) NOT NULL UNIQUE,
    `customer_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_time` VARCHAR(10) NOT NULL,
    `party_size` INT NOT NULL,
    `note` TEXT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `admin_note` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_res_status` (`status`),
    INDEX `idx_res_booking_date` (`booking_date`),
    INDEX `idx_res_phone` (`phone`),
    INDEX `idx_res_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
