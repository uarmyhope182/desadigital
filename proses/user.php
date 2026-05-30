<?php
require_once __DIR__ . '/../config/db.php';

// Get POST data
$action = $_POST['action'] ?? 'login';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$errors = [];

// ============================================
// LOGIN ACTION
// ============================================
if ($action === 'login') {
    // Validate input
    if ($username === '') {
        $errors[] = 'Username wajib diisi.';
    }
    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }

    // Query user with prepared statement
    $stmt = $pdo->prepare('
        SELECT id, username, full_name, role, password_hash, email, created_at 
        FROM users 
        WHERE username = :username 
        LIMIT 1
    ');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    // Verify credentials
    if (!$user || !verify_password($password, $user['password_hash'])) {
        // Intentionally vague error message for security
        $_SESSION['form_errors'] = ['Username atau password salah.'];
        error_log('Login attempt gagal untuk username: ' . $username);
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }

    // Login successful - set session and redirect
    login_user($user);
    set_flash('success', 'Selamat datang, ' . $user['full_name']);
    
    $redirect = $_SESSION['redirect_after_login'] ?? ADMIN_URL . '/dashboard.php';
    unset($_SESSION['redirect_after_login']);
    
    header('Location: ' . $redirect);
    exit;
}

// ============================================
// LOGOUT ACTION
// ============================================
if ($action === 'logout') {
    logout_user();
    set_flash('info', 'Anda telah logout.');
    header('Location: ' . PUBLIC_URL . '/index.php');
    exit;
}

// ============================================
// CHANGE PASSWORD ACTION
// ============================================
if ($action === 'change_password') {
    require_admin();
    
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = current_user()['id'];
    
    // Validation
    if ($current_password === '') {
        $errors[] = 'Password saat ini wajib diisi.';
    }
    if ($new_password === '') {
        $errors[] = 'Password baru wajib diisi.';
    }
    if ($confirm_password === '') {
        $errors[] = 'Konfirmasi password wajib diisi.';
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = 'Password baru dan konfirmasi tidak cocok.';
    }
    
    $error = validate_password($new_password);
    if ($error) {
        $errors[] = $error;
    }
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: ' . ADMIN_URL . '/profile.php');
        exit;
    }
    
    // Get current user's password hash
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id');
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
    
    if (!$user || !verify_password($current_password, $user['password_hash'])) {
        $_SESSION['form_errors'] = ['Password saat ini tidak cocok.'];
        header('Location: ' . ADMIN_URL . '/profile.php');
        exit;
    }
    
    // Update password
    $new_hash = hash_password($new_password);
    $stmt = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
    $stmt->execute([':hash' => $new_hash, ':id' => $user_id]);
    
    set_flash('success', 'Password berhasil diubah.');
    header('Location: ' . ADMIN_URL . '/profile.php');
    exit;
}

// Invalid action
http_response_code(400);
die('Invalid action.');
