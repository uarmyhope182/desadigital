<?php
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json');
$nik = trim($_GET['nik'] ?? '');
if ($nik === '') {
    echo json_encode(['success' => false]);
    exit;
}
$stmt = $pdo->prepare('SELECT nik, full_name, address, no_hp FROM penduduk WHERE nik = :nik LIMIT 1');
$stmt->execute([':nik' => $nik]);
$data = $stmt->fetch();
if (!$data) {
    echo json_encode(['success' => false]);
    exit;
}
echo json_encode(['success' => true, 'data' => $data]);
