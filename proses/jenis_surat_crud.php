<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_admin();

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $kode = sanitize_string($_POST['kode'] ?? '');
    $nama = sanitize_string($_POST['nama'] ?? '');
    $persyaratan = sanitize_string($_POST['persyaratan'] ?? '');
    $deskripsi = sanitize_string($_POST['deskripsi'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if ($kode === '') $errors[] = 'Kode jenis surat wajib diisi.';
    if (strlen($nama) < 3) $errors[] = 'Nama jenis surat minimal 3 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php?action=add');
        exit;
    }

    $csrf = $_POST['csrf_token'] ?? null;
    if ($csrf !== null && !verify_csrf_token($csrf)) {
        set_flash('errors', ['Token CSRF tidak valid.']);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php?action=add');
        exit;
    }

    $duplicateCheck = $pdo->prepare('SELECT id FROM jenis_surat WHERE kode = :kode LIMIT 1');
    $duplicateCheck->execute([':kode' => $kode]);
    if ($duplicateCheck->fetch()) {
        set_flash('errors', ['Kode jenis surat sudah digunakan.']);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php?action=add');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO jenis_surat (kode, nama, persyaratan, deskripsi, is_active) VALUES (:kode, :nama, :persyaratan, :deskripsi, :is_active)');
    $stmt->execute([':kode' => $kode, ':nama' => $nama, ':persyaratan' => $persyaratan, ':deskripsi' => $deskripsi, ':is_active' => $is_active]);

    set_flash('success', 'Jenis surat berhasil dibuat.');
    header('Location: ' . ADMIN_URL . '/jenis_surat.php');
    exit;
}

if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $kode = sanitize_string($_POST['kode'] ?? '');
    $nama = sanitize_string($_POST['nama'] ?? '');
    $persyaratan = sanitize_string($_POST['persyaratan'] ?? '');
    $deskripsi = sanitize_string($_POST['deskripsi'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if ($id <= 0) $errors[] = 'ID tidak valid.';
    if ($kode === '') $errors[] = 'Kode jenis surat wajib diisi.';
    if (strlen($nama) < 3) $errors[] = 'Nama jenis surat minimal 3 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php?action=edit&id=' . $id);
        exit;
    }

    $duplicateCheck = $pdo->prepare('SELECT id FROM jenis_surat WHERE kode = :kode AND id != :id LIMIT 1');
    $duplicateCheck->execute([':kode' => $kode, ':id' => $id]);
    if ($duplicateCheck->fetch()) {
        set_flash('errors', ['Kode jenis surat sudah digunakan oleh entri lain.']);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php?action=edit&id=' . $id);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE jenis_surat SET kode = :kode, nama = :nama, persyaratan = :persyaratan, deskripsi = :deskripsi, is_active = :is_active WHERE id = :id');
    $stmt->execute([':kode' => $kode, ':nama' => $nama, ':persyaratan' => $persyaratan, ':deskripsi' => $deskripsi, ':is_active' => $is_active, ':id' => $id]);

    set_flash('success', 'Perubahan berhasil disimpan.');
    header('Location: ' . ADMIN_URL . '/jenis_surat.php');
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('errors', ['ID tidak valid.']);
        header('Location: ' . ADMIN_URL . '/jenis_surat.php');
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM jenis_surat WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash('success', 'Jenis surat berhasil dihapus.');
    header('Location: ' . ADMIN_URL . '/jenis_surat.php');
    exit;
}

http_response_code(400);
die('Aksi tidak dikenali');
