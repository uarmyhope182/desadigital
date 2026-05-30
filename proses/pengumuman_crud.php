<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_admin();

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $judul = sanitize_string($_POST['judul'] ?? '');
    $isi = sanitize_string($_POST['isi'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $errors = [];
    if (strlen($judul) < 3) $errors[] = 'Judul minimal 3 karakter.';
    if (strlen($isi) < 10) $errors[] = 'Isi minimal 10 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/pengumuman.php?action=add');
        exit;
    }

// CSRF check if token present
$csrf = $_POST['csrf_token'] ?? null;
if ($csrf !== null && !verify_csrf_token($csrf)) {
    set_flash('errors', ['Token CSRF tidak valid.']);
    header('Location: ' . ADMIN_URL . '/pengumuman.php?action=add');
    exit;
}

    $stmt = $pdo->prepare('INSERT INTO pengumuman (judul, isi, is_published, created_at) VALUES (:judul, :isi, :is_published, NOW())');
    $stmt->execute([':judul'=>$judul, ':isi'=>$isi, ':is_published'=>$is_published]);

    set_flash('success', 'Pengumuman berhasil dibuat.');
    header('Location: ' . ADMIN_URL . '/pengumuman.php');
    exit;
}

if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $judul = sanitize_string($_POST['judul'] ?? '');
    $isi = sanitize_string($_POST['isi'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $errors = [];
    if ($id <= 0) $errors[] = 'ID tidak valid.';
    if (strlen($judul) < 3) $errors[] = 'Judul minimal 3 karakter.';
    if (strlen($isi) < 10) $errors[] = 'Isi minimal 10 karakter.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/pengumuman.php?action=edit&id=' . $id);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE pengumuman SET judul = :judul, isi = :isi, is_published = :is_published WHERE id = :id');
    $stmt->execute([':judul'=>$judul, ':isi'=>$isi, ':is_published'=>$is_published, ':id'=>$id]);

    set_flash('success', 'Perubahan berhasil disimpan.');
    header('Location: ' . ADMIN_URL . '/pengumuman.php');
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('errors', ['ID tidak valid.']);
        header('Location: ' . ADMIN_URL . '/pengumuman.php');
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM pengumuman WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash('success', 'Pengumuman berhasil dihapus.');
    header('Location: ' . ADMIN_URL . '/pengumuman.php');
    exit;
}

http_response_code(400);
die('Aksi tidak dikenali');
