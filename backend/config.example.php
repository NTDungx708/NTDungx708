<?php
/**
 * backend/config.example.php — MẪU cấu hình cho hosting
 *
 * 1. Copy file này thành backend/config.local.php (trên máy chủ hosting).
 * 2. Điền thông tin MySQL lấy từ trang quản lý hosting (mục "MySQL Databases").
 * 3. KHÔNG đưa config.local.php lên GitHub hay gửi cho người khác.
 */

return [
    'db' => [
        'driver'   => 'mysql',
        'host'     => 'sqlXXX.example-host.com', // MySQL Hostname (KHÔNG phải localhost trên nhiều hosting miễn phí)
        'port'     => 3306,
        'dbname'   => 'if0_00000000_onexpizza',  // Tên CSDL đầy đủ, kể cả tiền tố
        'username' => 'if0_00000000',            // MySQL Username
        'password' => 'MAT_KHAU_MYSQL_CUA_BAN',  // MySQL Password
    ],
];
