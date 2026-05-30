<?php
require_once __DIR__ . '/../config/db.php';

// Perform a clean logout
logout_user();

// Redirect to login page with info message via query param so we don't start a new session
$msg = rawurlencode('Anda telah logout.');
header('Location: ' . ADMIN_URL . '/login.php?info=' . $msg);
exit;