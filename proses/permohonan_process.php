<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_admin();

$action = $_POST['action'] ?? '';

if (!in_array($action, ['approve','reject'])) {
    http_response_code(400);
    die('Aksi tidak dikenali');
}

$id = (int) ($_POST['id'] ?? 0);
$catatan = sanitize_string($_POST['catatan'] ?? '');

// CSRF optional verification
$csrf = $_POST['csrf_token'] ?? null;
if ($csrf !== null && !verify_csrf_token($csrf)) {
    set_flash('errors', ['Token CSRF tidak valid.']);
    header('Location: ' . ADMIN_URL . '/kelola_permohonan.php');
    exit;
}

if ($id <= 0) {
    set_flash('errors', ['ID permohonan tidak valid.']);
    header('Location: ' . ADMIN_URL . '/kelola_permohonan.php');
    exit;
}

try {
    $pdo->beginTransaction();

    if ($action === 'approve') {
        // Fetch submission for notification
        $sel = $pdo->prepare('SELECT * FROM pengajuan_surat WHERE id = :id LIMIT 1');
        $sel->execute([':id' => $id]);
        $submission = $sel->fetch();

        // Generate nomor_surat and set tgl_surat when approving
        if (empty($submission['nomor_surat'])) {
            // generate next nomor per month/year using helper from validation.php
            $nomor = generate_nomor_surat($pdo);
            $updateStmt = $pdo->prepare('UPDATE pengajuan_surat SET status = :status, catatan = :catatan, nomor_surat = :nomor_surat, tgl_surat = NOW(), diproses_pada = NOW(), diproses_oleh = :user WHERE id = :id');
            $updateStmt->execute([':status' => 'approved', ':catatan' => $catatan, ':nomor_surat' => $nomor, ':user' => current_user()['id'] ?? null, ':id' => $id]);
            $submission['nomor_surat'] = $nomor;
        } else {
            $stmt = $pdo->prepare('UPDATE pengajuan_surat SET status = :status, catatan = :catatan, diproses_pada = NOW(), diproses_oleh = :user WHERE id = :id');
            $stmt->execute([':status' => 'approved', ':catatan' => $catatan, ':user' => current_user()['id'] ?? null, ':id' => $id]);
        }

        // Log history
        $hist = $pdo->prepare('INSERT INTO riwayat_tracking (pengajuan_id, status_lama, status_baru, keterangan, created_at) VALUES (:pid, :old, :new, :ket, NOW())');
        $hist->execute([':pid'=>$id, ':old'=>'pending', ':new'=>'approved', ':ket'=>$catatan]);

        set_flash('success', 'Permohonan disetujui.');

        // Send notification email if available
        $recipient = $submission['email'] ?? null;
        if ($recipient) {
            $autoload = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($autoload)) {
                require_once $autoload;
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->setFrom('no-reply@kelurahansasi.local', 'Kelurahan Sasi');
                    $mail->addAddress($recipient);
                    $mail->Subject = 'Status Permohonan Anda: Disetujui';
                    $body = "Permohonan dengan Tracking ID {$submission['tracking_id']} telah disetujui.\nCatatan: {$catatan}";
                    $mail->Body = $body;
                    $mail->send();
                } catch (Exception $e) {
                    error_log('Mail send error: ' . $e->getMessage());
                }
            }
        }
    } else {
        // Reject
        $stmt = $pdo->prepare('UPDATE pengajuan_surat SET status = :status, catatan = :catatan WHERE id = :id');
        $stmt->execute([':status' => 'rejected', ':catatan' => $catatan, ':id' => $id]);

        $hist = $pdo->prepare('INSERT INTO riwayat_tracking (pengajuan_id, status_lama, status_baru, keterangan, created_at) VALUES (:pid, :old, :new, :ket, NOW())');
        $hist->execute([':pid'=>$id, ':old'=>'pending', ':new'=>'rejected', ':ket'=>$catatan]);

        set_flash('success', 'Permohonan ditolak.');

        // Send notification email if available
        $recipient = $submission['email'] ?? null;
        if ($recipient) {
            $autoload = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($autoload)) {
                require_once $autoload;
                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->setFrom('no-reply@kelurahansasi.local', 'Kelurahan Sasi');
                    $mail->addAddress($recipient);
                    $mail->Subject = 'Status Permohonan Anda: Ditolak';
                    $body = "Permohonan dengan Tracking ID {$submission['tracking_id']} telah ditolak.\nAlasan: {$catatan}";
                    $mail->Body = $body;
                    $mail->send();
                } catch (Exception $e) {
                    error_log('Mail send error: ' . $e->getMessage());
                }
            }
        }
    }

    $pdo->commit();
    header('Location: ' . ADMIN_URL . '/kelola_permohonan.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Error processing permohonan: ' . $e->getMessage());
    set_flash('errors', ['Terjadi kesalahan saat memproses permohonan.']);
    header('Location: ' . ADMIN_URL . '/kelola_permohonan.php');
    exit;
}
