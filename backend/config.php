<?php
/**
 * backend/config.php — ONE × PIZZA
 * Cấu hình mặc định (dùng cho XAMPP/Laragon trên máy cá nhân).
 *
 * KHI ĐƯA LÊN HOSTING: KHÔNG sửa file này. Hãy tạo file backend/config.local.php
 * (copy từ config.example.php) và điền thông tin MySQL do hosting cấp.
 * config.local.php ghi đè các giá trị bên dưới và không nằm trong gói upload/mã nguồn.
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');

$cfg = [
    'db' => [
        'driver'      => getenv('DB_DRIVER') ?: 'mysql',
        'host'        => getenv('DB_HOST')   ?: '127.0.0.1',
        'port'        => (int)(getenv('DB_PORT') ?: 3306),
        'dbname'      => getenv('DB_NAME')   ?: 'onexpizza',
        'charset'     => 'utf8mb4',
        'username'    => getenv('DB_USER')   ?: 'root',
        'password'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'sqlite_path' => __DIR__ . '/db/onexpizza.sqlite',
    ],
    'security' => [
        'token_lifetime' => 28800, // phiên đăng nhập quản trị: 8 giờ
    ],
    'cors' => [
        // Frontend và API cùng tên miền nên không cần mở CORS rộng.
        // Chỉ thêm origin cụ thể nếu frontend đặt ở tên miền khác.
        'allowed_origins' => [],
    ],
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $override = require $local;
    if (is_array($override)) {
        $cfg = array_replace_recursive($cfg, $override);
    }
}

return $cfg;
