<?php
require_once __DIR__ . '/paths.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'desa_sasi';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $DB_HOST, $DB_NAME, $DB_CHARSET);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    die('Koneksi database gagal. Silakan hubungi administrator.');
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? BASE_URL;
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function current_user(): array
{
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'created_at' => $_SESSION['created_at'] ?? null,
    ];
}

function is_admin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
    }
}

function login_user(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $user['email'] ?? null;
    $_SESSION['created_at'] = $user['created_at'] ?? null;
    $_SESSION['login_time'] = time();
}

function logout_user(): void
{
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear all session data
    $_SESSION = [];
    session_unset();

    // Delete session cookie by setting it to expire in the past
    // Try multiple paths to ensure deletion
    $cookieName = session_name();
    $expiry = time() - 3600;
    
    // Delete with common paths
    setcookie($cookieName, '', $expiry, '/');
    setcookie($cookieName, '', $expiry, '/admin');
    setcookie($cookieName, '', $expiry, BASE_URL);
    setcookie($cookieName, '', $expiry, ADMIN_URL);
    
    // Also try explicit PHPSESSID deletion
    setcookie('PHPSESSID', '', $expiry, '/');
    
    // Destroy the session on server
    session_destroy();
}

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function needs_rehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
}

function set_flash(string $key, array|string $message): void
{
    $_SESSION['flash_messages'][$key] = $message;
}

function get_flash(string $key): array|string|null
{
    if (!empty($_SESSION['flash_messages'][$key])) {
        $message = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }
    return null;
}

function has_flash(string $key): bool
{
    return !empty($_SESSION['flash_messages'][$key]);
}

function h(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function json_encode_safe($data): string
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

function get_post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function get_get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function sanitize_string(string $value): string
{
    return trim(stripslashes($value));
}

function sanitize_email(string $value): string
{
    return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
}

function generate_nomor_surat(PDO $pdo, int $jenis_surat_id = null): string
{
    $year = date('Y');
    $month = date('m');
    
    $stmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM pengajuan_surat WHERE YEAR(created_at) = :year");
    $stmt->execute([':year' => $year]);
    $nomor_urut = (int) $stmt->fetchColumn();
    
    return sprintf('470/%03d/Kel.Sasi/%s/%s', $nomor_urut, $month, $year);
}

function get_penduduk_by_nik(PDO $pdo, string $nik): ?array
{
    $stmt = $pdo->prepare('SELECT nik, full_name, address, rt, rw, dusun, no_hp FROM penduduk WHERE nik = :nik LIMIT 1');
    $stmt->execute([':nik' => $nik]);
    return $stmt->fetch() ?: null;
}

function paginate(int $total, int $page, int $per_page = 10): array
{
    $total_pages = max(1, ceil($total / $per_page));
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $per_page;
    
    return [
        'page' => $page,
        'per_page' => $per_page,
        'offset' => $offset,
        'total' => $total,
        'total_pages' => $total_pages,
        'has_prev' => $page > 1,
        'has_next' => $page < $total_pages,
        'prev_page' => $page - 1,
        'next_page' => $page + 1,
    ];
}

require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/compat.php';