<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_admin();

$action = $_POST['action'] ?? '';

function valid_nik($nik) {
    return preg_match('/^[0-9]{16}$/', $nik);
}

if ($action === 'create') {
    $nik = sanitize_string($_POST['nik'] ?? '');
    $full_name = sanitize_string($_POST['full_name'] ?? '');
    $tempat_lahir = sanitize_string($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = sanitize_string($_POST['tanggal_lahir'] ?? '');
    $gender = sanitize_string($_POST['gender'] ?? '');
    $pekerjaan = sanitize_string($_POST['pekerjaan'] ?? '');
    $address = sanitize_string($_POST['address'] ?? '');
    $rt = sanitize_string($_POST['rt'] ?? '');
    $rw = sanitize_string($_POST['rw'] ?? '');
    $dusun = sanitize_string($_POST['dusun'] ?? '');
    $no_hp = sanitize_string($_POST['no_hp'] ?? '');

    $errors = [];
    if (!valid_nik($nik)) $errors[] = 'NIK harus 16 digit.';
    if (strlen($full_name) < 3) $errors[] = 'Nama minimal 3 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/penduduk.php?action=add');
        exit;
    }

// CSRF check if token present
$csrf = $_POST['csrf_token'] ?? null;
if ($csrf !== null && !verify_csrf_token($csrf)) {
    set_flash('errors', ['Token CSRF tidak valid.']);
    header('Location: ' . ADMIN_URL . '/penduduk.php?action=add');
    exit;
}

    // Check duplicate NIK
    $stmt = $pdo->prepare('SELECT id FROM penduduk WHERE nik = :nik LIMIT 1');
    $stmt->execute([':nik' => $nik]);
    if ($stmt->fetch()) {
        set_flash('errors', ['NIK sudah terdaftar.']);
        header('Location: ' . ADMIN_URL . '/penduduk.php?action=add');
        exit;
    }

    $insert = $pdo->prepare('INSERT INTO penduduk (nik, full_name, tempat_lahir, tanggal_lahir, gender, pekerjaan, address, rt, rw, dusun, no_hp, created_at, updated_at) VALUES (:nik, :full_name, :tempat_lahir, :tanggal_lahir, :gender, :pekerjaan, :address, :rt, :rw, :dusun, :no_hp, NOW(), NOW())');
    $insert->execute([
        ':nik' => $nik,
        ':full_name' => $full_name,
        ':tempat_lahir' => $tempat_lahir,
        ':tanggal_lahir' => $tanggal_lahir,
        ':gender' => $gender,
        ':pekerjaan' => $pekerjaan,
        ':address' => $address,
        ':rt' => $rt,
        ':rw' => $rw,
        ':dusun' => $dusun,
        ':no_hp' => $no_hp,
    ]);

    set_flash('success', 'Data penduduk berhasil ditambahkan.');
    header('Location: ' . ADMIN_URL . '/penduduk.php');
    exit;
}

if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $nik = sanitize_string($_POST['nik'] ?? '');
    $full_name = sanitize_string($_POST['full_name'] ?? '');
    $tempat_lahir = sanitize_string($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = sanitize_string($_POST['tanggal_lahir'] ?? '');
    $gender = sanitize_string($_POST['gender'] ?? '');
    $pekerjaan = sanitize_string($_POST['pekerjaan'] ?? '');
    $address = sanitize_string($_POST['address'] ?? '');
    $rt = sanitize_string($_POST['rt'] ?? '');
    $rw = sanitize_string($_POST['rw'] ?? '');
    $dusun = sanitize_string($_POST['dusun'] ?? '');
    $no_hp = sanitize_string($_POST['no_hp'] ?? '');

    $errors = [];
    if ($id <= 0) $errors[] = 'ID tidak valid.';
    if (!valid_nik($nik)) $errors[] = 'NIK harus 16 digit.';
    if (strlen($full_name) < 3) $errors[] = 'Nama minimal 3 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/penduduk.php?action=edit&id=' . $id);
        exit;
    }

    // Check duplicate NIK for other records
    $stmt = $pdo->prepare('SELECT id FROM penduduk WHERE nik = :nik AND id != :id LIMIT 1');
    $stmt->execute([':nik' => $nik, ':id' => $id]);
    if ($stmt->fetch()) {
        set_flash('errors', ['NIK sudah terdaftar pada data lain.']);
        header('Location: ' . ADMIN_URL . '/penduduk.php?action=edit&id=' . $id);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE penduduk SET nik = :nik, full_name = :full_name, tempat_lahir = :tempat_lahir, tanggal_lahir = :tanggal_lahir, gender = :gender, pekerjaan = :pekerjaan, address = :address, rt = :rt, rw = :rw, dusun = :dusun, no_hp = :no_hp, updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        ':nik' => $nik,
        ':full_name' => $full_name,
        ':tempat_lahir' => $tempat_lahir,
        ':tanggal_lahir' => $tanggal_lahir,
        ':gender' => $gender,
        ':pekerjaan' => $pekerjaan,
        ':address' => $address,
        ':rt' => $rt,
        ':rw' => $rw,
        ':dusun' => $dusun,
        ':no_hp' => $no_hp,
        ':id' => $id,
    ]);

    set_flash('success', 'Perubahan berhasil disimpan.');
    header('Location: ' . ADMIN_URL . '/penduduk.php');
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('errors', ['ID tidak valid.']);
        header('Location: ' . ADMIN_URL . '/penduduk.php');
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM penduduk WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash('success', 'Data penduduk berhasil dihapus.');
    header('Location: ' . ADMIN_URL . '/penduduk.php');
    exit;
}

http_response_code(400);
die('Aksi tidak dikenali');
