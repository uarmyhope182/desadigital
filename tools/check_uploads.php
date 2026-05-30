<?php
$uploadDir = __DIR__ . '/../uploads';
$result = ['exists' => is_dir($uploadDir), 'writable' => is_writable($uploadDir), 'htaccess' => false];
if ($result['exists']) {
    $result['htaccess'] = file_exists($uploadDir . '/.htaccess');
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
