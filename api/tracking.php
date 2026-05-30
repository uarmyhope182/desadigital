<?php
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json');
$action = trim($_GET['action'] ?? '');
switch ($action) {
    case 'surat':
        $result = $pdo->query('SELECT id, kode, nama FROM jenis_surat WHERE is_active = 1 ORDER BY nama')->fetchAll();
        echo json_encode(['success' => true, 'data' => $result]);
        break;
    case 'penduduk':
        $nik = trim($_GET['nik'] ?? '');
        if ($nik === '') {
            echo json_encode(['success' => false]);
            break;
        }
        $stmt = $pdo->prepare('SELECT nik, full_name, address, rt, rw, dusun, no_hp FROM penduduk WHERE nik = :nik LIMIT 1');
        $stmt->execute([':nik' => $nik]);
        $result = $stmt->fetch();
        echo json_encode(['success' => (bool) $result, 'data' => $result]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action tidak dikenali.']);
        break;
}
