<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed.');
}

$action = $_POST['action'] ?? 'submit';
$errors = [];

if ($action === 'submit') {
    $nik = sanitize_string($_POST['nik'] ?? '');
    $nama_lengkap = sanitize_string($_POST['nama_lengkap'] ?? '');
    $alamat = sanitize_string($_POST['alamat'] ?? '');
    $rt = sanitize_string($_POST['rt'] ?? '');
    $rw = sanitize_string($_POST['rw'] ?? '');
    $dusun = sanitize_string($_POST['dusun'] ?? '');
    $no_hp = sanitize_string($_POST['no_hp'] ?? '');
    $jenis_surat_id = (int) ($_POST['jenis_surat_id'] ?? 0);
    $keperluan = sanitize_string($_POST['keperluan'] ?? '');

    $formData = compact('nik', 'nama_lengkap', 'alamat', 'rt', 'rw', 'dusun', 'no_hp', 'jenis_surat_id', 'keperluan');
    $_SESSION['form_data'] = $formData;

    $nikError = validate_nik($nik);
    if ($nikError) {
        $errors[] = $nikError;
    }

    if (empty($nama_lengkap) || strlen($nama_lengkap) < 3) {
        $errors[] = 'Nama lengkap wajib diisi minimal 3 karakter.';
    }

    if (!preg_match('/^[A-Za-z\s\.\']+$/', $nama_lengkap)) {
        $errors[] = 'Nama hanya boleh berisi huruf dan spasi.';
    }

    if (empty($alamat) || strlen($alamat) < 5) {
        $errors[] = 'Alamat wajib diisi minimal 5 karakter.';
    }

    $phoneError = validate_phone($no_hp);
    if ($phoneError) {
        $errors[] = $phoneError;
    }

    if ($jenis_surat_id <= 0) {
        $errors[] = 'Pilih jenis surat yang valid.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM jenis_surat WHERE id = :id AND is_active = 1');
        $stmt->execute([':id' => $jenis_surat_id]);
        if (!$stmt->fetch()) {
            $errors[] = 'Jenis surat yang dipilih tidak tersedia.';
        }
    }

    $file_ktp = $_FILES['file_ktp'] ?? null;
    $file_kk = $_FILES['file_kk'] ?? null;

    if (!$file_ktp || $file_ktp['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File KTP wajib diunggah.';
    } else {
        $fileError = validate_upload_file($file_ktp);
        if ($fileError) {
            $errors[] = $fileError;
        }
    }

    if (!$file_kk || $file_kk['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File KK wajib diunggah.';
    } else {
        $fileError = validate_upload_file($file_kk);
        if ($fileError) {
            $errors[] = $fileError;
        }
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: ' . PUBLIC_URL . '/layanan_surat.php');
        exit;
    }

    $stmt = $pdo->prepare('
        SELECT id FROM pengajuan_surat 
        WHERE nik = :nik AND status IN ("pending", "approved")
        LIMIT 1
    ');
    $stmt->execute([':nik' => $nik]);
    if ($stmt->fetch()) {
        $_SESSION['form_errors'] = ['Anda sudah memiliki permohonan yang sedang diproses.'];
        header('Location: ' . PUBLIC_URL . '/layanan_surat.php');
        exit;
    }

    $pendudukStmt = $pdo->prepare('SELECT id FROM penduduk WHERE nik = :nik LIMIT 1');
    $pendudukStmt->execute([':nik' => $nik]);
    if (!$pendudukStmt->fetch()) {
        $insertPenduduk = $pdo->prepare('INSERT INTO penduduk (nik, full_name, address, rt, rw, dusun, no_hp, created_at, updated_at) VALUES (:nik, :full_name, :address, :rt, :rw, :dusun, :no_hp, NOW(), NOW())');
        $insertPenduduk->execute([
            ':nik' => $nik,
            ':full_name' => $nama_lengkap,
            ':address' => $alamat,
            ':rt' => $rt ?: null,
            ':rw' => $rw ?: null,
            ':dusun' => $dusun ?: null,
            ':no_hp' => $no_hp ?: null,
        ]);
    } else {
        $updatePenduduk = $pdo->prepare('UPDATE penduduk SET full_name = :full_name, address = :address, rt = :rt, rw = :rw, dusun = :dusun, no_hp = :no_hp, updated_at = NOW() WHERE nik = :nik');
        $updatePenduduk->execute([
            ':full_name' => $nama_lengkap,
            ':address' => $alamat,
            ':rt' => $rt ?: null,
            ':rw' => $rw ?: null,
            ':dusun' => $dusun ?: null,
            ':no_hp' => $no_hp ?: null,
            ':nik' => $nik,
        ]);
    }

    // Ensure upload folder exists and is writable
    if (!is_dir(UPLOAD_PATH)) {
        if (!mkdir(UPLOAD_PATH, 0755, true) && !is_dir(UPLOAD_PATH)) {
            $_SESSION['form_errors'] = ['Folder upload tidak tersedia dan tidak dapat dibuat.'];
            $_SESSION['debug_error'] = 'Failed to create upload dir: ' . UPLOAD_PATH;
            header('Location: ' . PUBLIC_URL . '/layanan_surat.php');
            exit;
        }
    }
    if (!is_writable(UPLOAD_PATH)) {
        $_SESSION['form_errors'] = ['Folder upload tidak dapat ditulis oleh server.'];
        $_SESSION['debug_error'] = 'Upload folder not writable: ' . UPLOAD_PATH;
        header('Location: ' . PUBLIC_URL . '/layanan_surat.php');
        exit;
    }

    $uploadedFiles = [];

    try {
        // generate tracking id (may throw)
        $tracking_id = generate_tracking_id($pdo);

        // upload files (upload_file will throw on failure)
        $file_ktp_name = upload_file($file_ktp, $tracking_id, 'ktp');
        $uploadedFiles[] = $file_ktp_name;

        $file_kk_name = upload_file($file_kk, $tracking_id, 'kk');
        $uploadedFiles[] = $file_kk_name;

        $supportsRtRwDusun = schema_has_column('pengajuan_surat', 'rt') && schema_has_column('pengajuan_surat', 'rw') && schema_has_column('pengajuan_surat', 'dusun');
        $columns = ['tracking_id', 'nik', 'nama_lengkap', 'alamat'];
        $placeholders = [':tracking_id', ':nik', ':nama_lengkap', ':alamat'];
        $params = [
            ':tracking_id' => $tracking_id,
            ':nik' => $nik,
            ':nama_lengkap' => $nama_lengkap,
            ':alamat' => $alamat,
        ];

        if ($supportsRtRwDusun) {
            $columns = array_merge($columns, ['rt', 'rw', 'dusun']);
            $placeholders = array_merge($placeholders, [':rt', ':rw', ':dusun']);
            $params[':rt'] = $rt;
            $params[':rw'] = $rw;
            $params[':dusun'] = $dusun;
        } else {
            $columns[] = 'data_tambahan';
            $placeholders[] = ':data_tambahan';
            $extraData = array_filter([
                'rt' => $rt,
                'rw' => $rw,
                'dusun' => $dusun,
            ], fn($value) => $value !== '');
            $params[':data_tambahan'] = empty($extraData) ? null : json_encode($extraData, JSON_UNESCAPED_UNICODE);
        }

        $columns = array_merge($columns, ['no_hp', 'jenis_surat_id', 'keperluan', 'file_ktp', 'file_kk', 'status', 'created_at']);
        $placeholders = array_merge($placeholders, [':no_hp', ':jenis_surat_id', ':keperluan', ':file_ktp', ':file_kk', '"pending"', 'NOW()']);
        $params[':no_hp'] = $no_hp;
        $params[':jenis_surat_id'] = $jenis_surat_id;
        $params[':keperluan'] = $keperluan;
        $params[':file_ktp'] = $file_ktp_name;
        $params[':file_kk'] = $file_kk_name;

        $insertSql = sprintf(
            'INSERT INTO pengajuan_surat (%s) VALUES (%s)',
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $pdo->prepare($insertSql);
        $result = $stmt->execute($params);

        if ($result) {
            $pengajuan_id = $pdo->lastInsertId();

            $historyStmt = $pdo->prepare('
                INSERT INTO riwayat_tracking (pengajuan_id, status_lama, status_baru, keterangan, created_at)
                VALUES (:id, "BARU", "pending", "Permohonan diterima oleh sistem", NOW())
            ');
            $historyStmt->execute([':id' => $pengajuan_id]);

            unset($_SESSION['form_data']);
            set_flash('success', 'Permohonan berhasil dikirim! Tracking ID Anda: ' . h($tracking_id));

            header('Location: ' . PUBLIC_URL . '/lacak_permohonan.php?tracking_id=' . urlencode($tracking_id));
            exit;
        } else {
            throw new Exception('Gagal menyimpan permohonan ke database.');
        }
    } catch (Throwable $e) {
        // Remove any files that were already uploaded when the DB insert failed
        foreach ($uploadedFiles as $uploadedFile) {
            $uploadedPath = UPLOAD_PATH . '/' . $uploadedFile;
            if (is_file($uploadedPath)) {
                @unlink($uploadedPath);
            }
        }

        error_log('Error during surat submission: ' . $e->getMessage() . " in " . $e->getFile() . ':' . $e->getLine());
        $_SESSION['form_errors'] = ['Terjadi kesalahan pada server. Silakan coba lagi nanti.'];
        // store debug info in session (remove in production)
        $_SESSION['debug_error'] = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        header('Location: ' . PUBLIC_URL . '/layanan_surat.php');
        exit;
    }
}

http_response_code(400);
die('Invalid action.');

function validate_upload_file($file) {
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return 'File terlalu besar. Maksimal 2MB.';
    }

    if ($file['size'] < 1024) {
        return 'File terlalu kecil atau kosong.';
    }

    $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau PDF.';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($mimeType, $allowedMimes)) {
        return 'Tipe file tidak valid. Hanya JPG, PNG, atau PDF yang diterima.';
    }

    return null;
}

function upload_file($file, $tracking_id, $type) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = sprintf('%s_%s_%d.%s', $tracking_id, $type, time(), $ext);
    $filepath = UPLOAD_PATH . '/' . $filename;
    // Validate uploaded file temp
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new Exception('Temporary file tidak valid untuk upload: ' . $type);
    }

    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        $err = error_get_last();
        $msg = 'Gagal upload file: ' . $type . '. ' . ($err['message'] ?? 'unknown');
        throw new Exception($msg);
    }

    @chmod($filepath, 0644);
    return $filename;
}
