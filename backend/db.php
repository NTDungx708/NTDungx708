<?php
/**
 * backend/db.php — Kết nối Cơ sở dữ liệu qua PDO (MySQL / SQLite)
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection($config = null): PDO {
        if (self::$instance === null) {
            if (!$config) {
                $config = require __DIR__ . '/config.php';
            }

            $dbCfg = $config['db'];
            $driver = $dbCfg['driver'] ?? 'mysql';

            try {
                if ($driver === 'sqlite') {
                    $sqlitePath = $dbCfg['sqlite_path'];
                    $dir = dirname($sqlitePath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    self::$instance = new PDO("sqlite:" . $sqlitePath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$instance->exec("PRAGMA foreign_keys = ON;");
                } else {
                    // MySQL PDO connection
                    $host    = $dbCfg['host'] ?? '127.0.0.1';
                    $port    = $dbCfg['port'] ?? 3306;
                    $dbname  = $dbCfg['dbname'] ?? 'onexpizza';
                    $charset = $dbCfg['charset'] ?? 'utf8mb4';
                    $user    = $dbCfg['username'] ?? 'root';
                    $pass    = $dbCfg['password'] ?? '';

                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        // Giờ Việt Nam cho CURRENT_TIMESTAMP, kể cả khi máy chủ hosting đặt ở nước ngoài
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}, time_zone = '+07:00'"
                    ];

                    self::$instance = new PDO($dsn, $user, $pass, $options);
                }
            } catch (PDOException $e) {
                // Log lỗi an toàn, không làm lộ mật khẩu
                error_log("Database Connection Error: " . $e->getMessage());
                throw new Exception("Không thể kết nối tới cơ sở dữ liệu. Vui lòng thử lại sau.");
            }
        }

        return self::$instance;
    }

    /**
     * Tạo mã đặt bàn duy nhất: OXP-YYYYMMDD-NNN
     */
    public static function generateReferenceCode(PDO $pdo, string $bookingDate): string {
        $cleanDate = str_replace('-', '', $bookingDate);
        $prefix = "OXP-{$cleanDate}-";

        // Đếm số đơn đã có trong ngày đó để cấp số thứ tự
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM oxp_reservations WHERE reference_code LIKE ?");
        $stmt->execute([$prefix . '%']);
        $row = $stmt->fetch();
        $nextNum = ($row ? (int)$row['total'] : 0) + 1;

        $refCode = sprintf("%s%03d", $prefix, $nextNum);

        // Kiểm tra chắc chắn không trùng
        $checkStmt = $pdo->prepare("SELECT id FROM oxp_reservations WHERE reference_code = ? LIMIT 1");
        $checkStmt->execute([$refCode]);
        if ($checkStmt->fetch()) {
            // Nếu trùng do đặt cùng tích tắc, thêm hậu tố ngẫu nhiên ngắn
            $refCode = sprintf("%s%03d-%s", $prefix, $nextNum, strtoupper(substr(bin2hex(random_bytes(2)), 0, 3)));
        }

        return $refCode;
    }
}
