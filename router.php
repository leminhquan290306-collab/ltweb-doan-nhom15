<?php

$duongDan = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$duongDan = rawurldecode($duongDan ?? '');

if (
    str_starts_with($duongDan, '/storage/')
    || str_starts_with($duongDan, '/uploads/')
) {
    http_response_code(403);

    echo '<h1>403 - Forbidden</h1>';
    echo '<p>Không được phép truy cập tài nguyên này.</p>';

    exit;
}

if ($duongDan === '/') {
    require __DIR__ . '/index.php';
    exit;
}

$tep = __DIR__ . $duongDan;

if (is_file($tep)) {
    return false;
}

http_response_code(404);
require __DIR__ . '/404.php';
exit;