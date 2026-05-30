<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_admin();

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $username = sanitize_string($_POST['username'] ?? '');
    $full_name = sanitize_string($_POST['full_name'] ?? '');
    $email = sanitize_string($_POST['email'] ?? '');
    $role = sanitize_string($_POST['role'] ?? 'operator');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';

        if (!empty($errors)) {
            set_flash('errors', $errors);
            header('Location: ' . ADMIN_URL . '/users.php?action=add');
            exit;
        }

    // CSRF check if token provided
    $csrf = $_POST['csrf_token'] ?? null;
    if ($csrf !== null && !verify_csrf_token($csrf)) {
        set_flash('errors', ['Token CSRF tidak valid.']);
        header('Location: ' . ADMIN_URL . '/users.php?action=add');
        exit;
    }

    // Check unique username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        set_flash('errors', ['Username sudah digunakan.']);
        header('Location: ' . ADMIN_URL . '/users.php?action=add');
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare('INSERT INTO users (username, full_name, email, password_hash, role, created_at) VALUES (:username, :full_name, :email, :password_hash, :role, NOW())');
    $insert->execute([
        ':username' => $username,
        ':full_name' => $full_name,
        ':email' => $email,
        ':password_hash' => $hash,
        ':role' => $role
    ]);

    set_flash('success', 'User berhasil dibuat.');
    header('Location: ' . ADMIN_URL . '/users.php');
    exit;
}

if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $username = sanitize_string($_POST['username'] ?? '');
    $full_name = sanitize_string($_POST['full_name'] ?? '');
    $email = sanitize_string($_POST['email'] ?? '');
    $role = sanitize_string($_POST['role'] ?? 'operator');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if ($id <= 0) $errors[] = 'ID pengguna tidak valid.';
    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';

    if (!empty($errors)) {
        set_flash('errors', $errors);
        header('Location: ' . ADMIN_URL . '/users.php?action=edit&id=' . $id);
        exit;
    }

    // If password provided, update it
    if (!empty($password)) {
        if (strlen($password) < 8) {
            set_flash('errors', ['Password minimal 8 karakter.']);
            header('Location: ' . ADMIN_URL . '/users.php?action=edit&id=' . $id);
            exit;
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET username = :username, full_name = :full_name, email = :email, password_hash = :password_hash, role = :role WHERE id = :id');
        $stmt->execute([
            ':username' => $username,
            ':full_name' => $full_name,
            ':email' => $email,
            ':password_hash' => $hash,
            ':role' => $role,
            ':id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = :username, full_name = :full_name, email = :email, role = :role WHERE id = :id');
        $stmt->execute([
            ':username' => $username,
            ':full_name' => $full_name,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);
    }

    set_flash('success', 'Perubahan berhasil disimpan.');
    header('Location: ' . ADMIN_URL . '/users.php');
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        set_flash('errors', ['ID pengguna tidak valid.']);
        header('Location: ' . ADMIN_URL . '/users.php');
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash('success', 'User berhasil dihapus.');
    header('Location: ' . ADMIN_URL . '/users.php');
    exit;
}

http_response_code(400);
die('Aksi tidak dikenali');
