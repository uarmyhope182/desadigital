<?php
require_once __DIR__ . '/config/db.php';

// WhatsApp API configuration
$whatsappApiUrl = getenv('WHATSAPP_API_URL') ?: 'https://api.wablas.com/v2/send-message';
$whatsappApiToken = getenv('WHATSAPP_API_TOKEN') ?: 'YOUR_WABLAS_API_TOKEN_HERE';

require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kelola_permohonan.php');
    exit;
}
$submission_id = intval($_POST['submission_id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$catatan = trim($_POST['catatan'] ?? '');
$allowed = ['pending', 'approved', 'rejected'];
if ($submission_id <= 0 || !in_array($status, $allowed, true)) {
    set_flash('status_updated', 'Permintaan tidak valid.');
    header('Location: kelola_permohonan.php');
    exit;
}
$stmt = $pdo->prepare('SELECT status, nomor_surat, no_hp, nama_lengkap, tracking_id FROM pengajuan_surat WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $submission_id]);
$submission = $stmt->fetch();
if (!$submission) {
    set_flash('status_updated', 'Permohonan tidak ditemukan.');
    header('Location: kelola_permohonan.php');
    exit;
}
$update = 'UPDATE pengajuan_surat SET status = :status, catatan = :catatan, diproses_oleh = :user_id, diproses_pada = NOW()';
$params = [
    ':status' => $status,
    ':catatan' => $catatan,
    ':user_id' => $_SESSION['user_id'],
    ':id' => $submission_id,
];
if ($status === 'approved' && empty($submission['nomor_surat'])) {
    $nomorSurat = 'SASI/' . date('Ymd') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $update .= ', nomor_surat = :nomor_surat, tgl_surat = NOW()';
    $params[':nomor_surat'] = $nomorSurat;
}
$update .= ' WHERE id = :id';
$pdo->prepare($update)->execute($params);

if ($status === 'approved' && $submission['status'] !== 'approved') {
    $whatsappPhone = normalize_whatsapp_number($submission['no_hp']);
    $message = sprintf(
        'Surat Anda sudah siap! Silakan datang ke Kantor Kelurahan Sasi dengan membawa KTP asli. Tracking ID: %s',
        $submission['tracking_id']
    );
    if ($whatsappPhone !== null) {
        $sent = send_whatsapp_notification($whatsappApiUrl, $whatsappApiToken, $whatsappPhone, $message);
        if (!$sent) {
            queue_whatsapp_notification($pdo, $whatsappPhone, $message, $submission['nama_lengkap']);
        }
    } else {
        error_log('Invalid WhatsApp number for submission ID ' . $submission_id . ': ' . $submission['no_hp']);
    }
}

set_flash('status_updated', 'Status permohonan berhasil diperbarui.');
header('Location: kelola_permohonan.php');
exit;
function normalize_whatsapp_number(string $phone): ?string
{
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits === '') {
        return null;
    }
    if (preg_match('/^0(\d+)$/', $digits, $matches)) {
        return '62' . $matches[1];
    }
    if (preg_match('/^8\d+$/', $digits)) {
        return '62' . $digits;
    }
    if (preg_match('/^62\d+$/', $digits)) {
        return $digits;
    }
    return null;
}

function send_whatsapp_notification(string $url, string $token, string $phone, string $message): bool
{
    if (empty($token) || strpos($token, 'YOUR_') === 0) {
        error_log('WhatsApp API token not configured.');
        return false;
    }

    $payload = json_encode([
        'phone' => $phone,
        'message' => $message,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        error_log('WhatsApp API request failed: ' . $error);
        return false;
    }

    $data = json_decode($response, true);
    if ($httpStatus >= 200 && $httpStatus < 300 && isset($data['success']) && $data['success'] === true) {
        return true;
    }

    error_log('WhatsApp API response error: HTTP ' . $httpStatus . ' - ' . $response);
    return false;
}

function queue_whatsapp_notification(PDO $pdo, string $phone, string $message, string $name = ''): void
{
    try {
        $stmt = $pdo->prepare('INSERT INTO notifications_queue (channel, destination, message, recipient_name, status, created_at) VALUES (:channel, :destination, :message, :name, :status, NOW())');
        $stmt->execute([
            ':channel' => 'whatsapp',
            ':destination' => $phone,
            ':message' => $message,
            ':name' => $name,
            ':status' => 'pending',
        ]);
    } catch (Exception $e) {
        error_log('Failed to queue WhatsApp notification: ' . $e->getMessage());
        $logMessage = sprintf("[WA QUEUE FAIL] %s | to: %s | msg: %s\n", date('Y-m-d H:i:s'), $phone, $message);
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'whatsapp_notifications.log';
        @file_put_contents($tempFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}