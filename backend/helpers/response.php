<?php
/**
 * backend/helpers/response.php — Helper phản hồi JSON & CORS
 */

function sendCorsHeaders($config = null) {
    if (!$config) {
        $config = require __DIR__ . '/../config.php';
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = $config['cors']['allowed_origins'] ?? [];

    if ($origin !== '' && (in_array('*', $allowed, true) || in_array($origin, $allowed, true))) {
        header("Access-Control-Allow-Origin: $origin");
        header("Vary: Origin");
    }
    header("Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Admin-Token");
    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function jsonResponse($success, $data = null, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    $payload = [
        'success'   => (bool)$success,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function jsonError($message, $statusCode = 400, $errors = null) {
    http_response_code($statusCode);
    $payload = [
        'success'   => false,
        'message'   => $message,
        'errors'    => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
