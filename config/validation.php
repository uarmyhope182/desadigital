<?php
if (!function_exists('normalize_phone')) {
    function normalize_phone(string $value): string
    {
        return preg_replace('/\D+/', '', $value);
    }
}

if (!function_exists('validate_nik')) {
    function validate_nik(string $nik): ?string
    {
        $nik = trim($nik);
        if ($nik === '') {
            return 'NIK wajib diisi.';
        }
        if (!ctype_digit($nik)) {
            return 'NIK hanya boleh berisi angka.';
        }
        if (strlen($nik) !== 16) {
            return 'NIK harus terdiri dari tepat 16 digit angka.';
        }
        return null;
    }
}

if (!function_exists('validate_name')) {
    function validate_name(string $name): ?string
    {
        $name = trim($name);
        if ($name === '') {
            return 'Nama wajib diisi.';
        }
        if (!preg_match('/^[A-Za-z\s\.\']+$/', $name)) {
            return 'Nama hanya boleh berisi huruf, spasi, titik, dan apostrof. Tidak boleh mengandung angka.';
        }
        return null;
    }
}

if (!function_exists('validate_pekerjaan')) {
    /**
     * Validasi field pekerjaan - hanya huruf, spasi, titik, dan tanda hubung
     * 
     * @param string $pekerjaan Nilai pekerjaan
     * @param bool $required Apakah field wajib diisi
     * @return string|null Pesan error atau null jika valid
     */
    function validate_pekerjaan(string $pekerjaan, bool $required = false): ?string
    {
        $pekerjaan = trim($pekerjaan);
        
        if ($pekerjaan === '') {
            return $required ? 'Pekerjaan wajib diisi.' : null;
        }
        
        // Hanya huruf, spasi, titik, dan tanda hubung
        if (!preg_match('/^[A-Za-z\s\.\-]+$/', $pekerjaan)) {
            return 'Pekerjaan hanya boleh berisi huruf, spasi, titik, dan tanda hubung.';
        }
        
        // Minimal 2 karakter jika diisi
        if (strlen($pekerjaan) > 0 && strlen($pekerjaan) < 2) {
            return 'Pekerjaan minimal 2 karakter.';
        }
        
        // Maksimal 100 karakter
        if (strlen($pekerjaan) > 100) {
            return 'Pekerjaan maksimal 100 karakter.';
        }
        
        return null;
    }
}

if (!function_exists('validate_tempat_lahir')) {
    /**
     * Validasi field tempat lahir - hanya huruf, spasi, titik, dan tanda hubung
     * 
     * @param string $tempat_lahir Nilai tempat lahir
     * @param bool $required Apakah field wajib diisi
     * @return string|null Pesan error atau null jika valid
     */
    function validate_tempat_lahir(string $tempat_lahir, bool $required = false): ?string
    {
        $tempat_lahir = trim($tempat_lahir);
        
        if ($tempat_lahir === '') {
            return $required ? 'Tempat lahir wajib diisi.' : null;
        }
        
        // Hanya huruf, spasi, titik, dan tanda hubung
        if (!preg_match('/^[A-Za-z\s\.\-]+$/', $tempat_lahir)) {
            return 'Tempat lahir hanya boleh berisi huruf, spasi, titik, dan tanda hubung.';
        }
        
        // Minimal 2 karakter jika diisi
        if (strlen($tempat_lahir) > 0 && strlen($tempat_lahir) < 2) {
            return 'Tempat lahir minimal 2 karakter.';
        }
        
        // Maksimal 50 karakter
        if (strlen($tempat_lahir) > 50) {
            return 'Tempat lahir maksimal 50 karakter.';
        }
        
        return null;
    }
}

if (!function_exists('validate_phone')) {
    function validate_phone(string $phone, bool $required = true): ?string
    {
        $value = trim($phone);
        if ($value === '') {
            return $required ? 'Nomor HP/WhatsApp wajib diisi.' : null;
        }
        if (!preg_match('/^\+62\d{10,13}$/', $value)) {
            return 'Nomor HP/WhatsApp harus dimulai dengan +62 dan diikuti 10-13 digit angka.';
        }
        return null;
    }
}

if (!function_exists('validate_email')) {
    function validate_email(string $email, bool $required = false): ?string
    {
        $email = trim($email);
        if ($email === '') {
            return $required ? 'Alamat email wajib diisi.' : null;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Alamat email tidak valid.';
        }
        return null;
    }
}

if (!function_exists('validate_password')) {
    function validate_password(string $password, int $minLength = 8, bool $required = true): ?string
    {
        $password = trim($password);
        if ($password === '') {
            return $required ? 'Password wajib diisi.' : null;
        }
        if (strlen($password) < $minLength) {
            return 'Password harus minimal ' . $minLength . ' karakter.';
        }
        return null;
    }
}

if (!function_exists('validate_tracking_id')) {
    function validate_tracking_id(string $trackingId): ?string
    {
        $trackingId = trim($trackingId);
        if ($trackingId === '') {
            return 'Tracking ID wajib diisi.';
        }
        if (!preg_match('/^[A-Za-z0-9\-]{5,50}$/', $trackingId)) {
            return 'Tracking ID tidak valid.';
        }
        return null;
    }
}

if (!function_exists('validate_url')) {
    function validate_url(string $value, bool $required = false): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return $required ? 'URL wajib diisi.' : null;
        }
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return 'URL tidak valid.';
        }
        return null;
    }
}

if (!function_exists('validate_date')) {
    function validate_date(string $value, bool $required = false): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return $required ? 'Tanggal wajib diisi.' : null;
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return 'Tanggal tidak valid.';
        }
        return null;
    }
}

if (!function_exists('validate_rt_rw')) {
    function validate_rt_rw(string $value, bool $required = false): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return $required ? 'RT/RW wajib diisi.' : null;
        }
        if (!ctype_digit($value)) {
            return 'RT/RW hanya boleh berisi angka.';
        }
        if (strlen($value) > 3) {
            return 'RT/RW maksimal 3 digit angka.';
        }
        return null;
    }
}

if (!function_exists('validate_dusun')) {
    function validate_dusun(string $value, bool $required = false): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return $required ? 'Dusun wajib diisi.' : null;
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $value)) {
            return 'Dusun hanya boleh berisi huruf, spasi, dan tanda hubung.';
        }
        return null;
    }
}

if (!function_exists('validate_file_upload')) {
    function validate_file_upload(array $file, array $allowedExtensions, int $maxBytes, string $label, bool $required = true): ?string
    {
        $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return $required ? sprintf('%s wajib diunggah.', $label) : null;
        }
        
        if ($errorCode !== UPLOAD_ERR_OK) {
            switch ($errorCode) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return sprintf('%s melebihi ukuran maksimal.', $label);
                default:
                    return sprintf('Terjadi masalah saat mengunggah %s.', strtolower($label));
            }
        }

        $fileName = $file['name'] ?? '';
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            return sprintf('%s harus berupa %s.', $label, implode(', ', $allowedExtensions));
        }

        if (($file['size'] ?? 0) > $maxBytes) {
            $maxMb = round($maxBytes / 1024 / 1024, 1);
            return sprintf('%s maksimal %s MB.', $label, $maxMb);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedMimeTypes = [];
        foreach ($allowedExtensions as $ext) {
            if ($ext === 'jpg' || $ext === 'jpeg') {
                $allowedMimeTypes[] = 'image/jpeg';
            }
            if ($ext === 'png') {
                $allowedMimeTypes[] = 'image/png';
            }
            if ($ext === 'webp') {
                $allowedMimeTypes[] = 'image/webp';
            }
            if ($ext === 'pdf') {
                $allowedMimeTypes[] = 'application/pdf';
            }
        }
        $allowedMimeTypes = array_unique($allowedMimeTypes);
        
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return sprintf('Format %s tidak valid.', strtolower($label));
        }

        return null;
    }
}

if (!function_exists('redirect_with_errors')) {
    function redirect_with_errors(string $location, array $errors, array $oldData = []): void
    {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $oldData;
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('redirect_with_success')) {
    function redirect_with_success(string $location, string $message): void
    {
        $_SESSION['success'] = $message;
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('generate_tracking_id')) {
    function generate_tracking_id(PDO $pdo): string
    {
        $datePart = date('Ymd');
        $basePrefix = 'SASI-' . $datePart . '-%';

        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(tracking_id, '-', -1) AS UNSIGNED)) as max_seq
                FROM pengajuan_surat
                WHERE tracking_id LIKE :prefix";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':prefix' => $basePrefix]);
        $maxSeq = (int) $stmt->fetchColumn();

        $next = $maxSeq + 1;
        return sprintf('SASI-%s-%05d', $datePart, $next);
    }
}

if (!function_exists('generate_nomor_surat')) {
    function generate_nomor_surat(PDO $pdo): string
    {
        $year = date('Y');
        $month = date('m');
        $pattern = sprintf('470/%%/Kel.Sasi/%s/%s', $month, $year);

        $stmt = $pdo->prepare('SELECT nomor_surat FROM pengajuan_surat WHERE nomor_surat LIKE :pattern');
        $stmt->execute([':pattern' => $pattern]);

        $lastNumbers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (preg_match('/^470\/([0-9]{3})\/Kel\.Sasi\/' . preg_quote($month, '/') . '\/' . preg_quote($year, '/') . '$/', $row['nomor_surat'], $matches)) {
                $lastNumbers[] = (int) $matches[1];
            }
        }

        $nextNumber = empty($lastNumbers) ? 1 : max($lastNumbers) + 1;
        return sprintf('470/%03d/Kel.Sasi/%s/%s', $nextNumber, $month, $year);
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('truncate_text')) {
    function truncate_text(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('format_date_indonesia')) {
    function format_date_indonesia(string $date, string $format = 'd M Y'): string
    {
        $timestamp = strtotime($date);
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        if ($format === 'd M Y') {
            $day = date('d', $timestamp);
            $month = $bulan[(int)date('n', $timestamp)];
            $year = date('Y', $timestamp);
            return $day . ' ' . $month . ' ' . $year;
        }
        
        return date($format, $timestamp);
    }
}

/* ============================================
   FUNGSI VALIDASI DATA PENDUDUK
   Untuk digunakan di form pengajuan surat online
   ============================================ */

if (!function_exists('get_penduduk_by_nik')) {
    /**
     * Mendapatkan data penduduk berdasarkan NIK dari database
     * 
     * @param PDO $pdo Koneksi database
     * @param string $nik NIK penduduk (16 digit)
     * @return array|null Data penduduk atau null jika tidak ditemukan
     */
    function get_penduduk_by_nik(PDO $pdo, string $nik): ?array
    {
        $nik = trim($nik);
        if (strlen($nik) !== 16) {
            return null;
        }
        
        $stmt = $pdo->prepare('SELECT nik, full_name as nama_lengkap, tempat_lahir, tanggal_lahir, gender, pekerjaan, address as alamat, rt, rw, dusun, no_hp FROM penduduk WHERE nik = :nik LIMIT 1');
        $stmt->execute([':nik' => $nik]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
}

if (!function_exists('validate_penduduk_exists')) {
    /**
     * Memvalidasi apakah NIK terdaftar di database penduduk
     * 
     * @param PDO $pdo Koneksi database
     * @param string $nik NIK penduduk
     * @return array|null Data penduduk jika ditemukan, null jika tidak
     */
    function validate_penduduk_exists(PDO $pdo, string $nik): ?array
    {
        $penduduk = get_penduduk_by_nik($pdo, $nik);
        
        if (!$penduduk) {
            return null;
        }
        
        return $penduduk;
    }
}

if (!function_exists('validate_penduduk_match')) {
    /**
     * Memvalidasi apakah NIK dan Nama sesuai dengan data di database
     * 
     * @param PDO $pdo Koneksi database
     * @param string $nik NIK penduduk
     * @param string $nama Nama penduduk
     * @return array|null Data penduduk jika valid, null jika tidak
     */
    function validate_penduduk_match(PDO $pdo, string $nik, string $nama): ?array
    {
        $nik = trim($nik);
        $nama = trim($nama);
        
        $stmt = $pdo->prepare('SELECT nik, full_name as nama_lengkap, tempat_lahir, tanggal_lahir, gender, pekerjaan, address as alamat, rt, rw, dusun, no_hp FROM penduduk WHERE nik = :nik AND full_name = :nama LIMIT 1');
        $stmt->execute([':nik' => $nik, ':nama' => $nama]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
}

if (!function_exists('auto_fill_penduduk_data')) {
    /**
     * Mengambil data penduduk untuk autofill form
     * 
     * @param PDO $pdo Koneksi database
     * @param string $nik NIK penduduk
     * @return array Data penduduk dengan format yang siap untuk form
     */
    function auto_fill_penduduk_data(PDO $pdo, string $nik): array
    {
        $penduduk = get_penduduk_by_nik($pdo, $nik);
        
        if (!$penduduk) {
            return [
                'found' => false,
                'message' => 'NIK tidak ditemukan dalam database kependudukan.'
            ];
        }
        
        return [
            'found' => true,
            'data' => [
                'nik' => $penduduk['nik'],
                'nama_lengkap' => $penduduk['nama_lengkap'],
                'tempat_lahir' => $penduduk['tempat_lahir'] ?? '',
                'tanggal_lahir' => $penduduk['tanggal_lahir'] ?? '',
                'gender' => $penduduk['gender'] ?? '',
                'pekerjaan' => $penduduk['pekerjaan'] ?? '',
                'alamat' => $penduduk['alamat'],
                'rt' => $penduduk['rt'],
                'rw' => $penduduk['rw'],
                'dusun' => $penduduk['dusun'],
                'no_hp' => $penduduk['no_hp']
            ]
        ];
    }
}

if (!function_exists('validate_phone_whatsapp')) {
    /**
     * Validasi khusus untuk nomor WhatsApp Indonesia
     * 
     * @param string $phone Nomor telepon
     * @return string|null Pesan error atau null jika valid
     */
    function validate_phone_whatsapp(string $phone): ?string
    {
        $phone = trim($phone);
        if ($phone === '') {
            return 'Nomor WhatsApp wajib diisi.';
        }
        
        // Bersihkan dari karakter non-digit
        $clean = preg_replace('/\D/', '', $phone);
        
        // Cek apakah mulai dengan 08 atau +62
        if (preg_match('/^08[0-9]{9,11}$/', $clean)) {
            return null;
        }
        
        if (preg_match('/^62[0-9]{10,12}$/', $clean)) {
            return null;
        }
        
        if (preg_match('/^\+62[0-9]{10,12}$/', $phone)) {
            return null;
        }
        
        return 'Nomor WhatsApp harus diawali dengan 08 atau +62. Contoh: 081234567890 atau +6281234567890';
    }
}

if (!function_exists('format_phone_to_international')) {
    /**
     * Memformat nomor telepon ke format internasional (+62)
     * 
     * @param string $phone Nomor telepon
     * @return string Nomor dalam format internasional
     */
    function format_phone_to_international(string $phone): string
    {
        $phone = trim($phone);
        $clean = preg_replace('/\D/', '', $phone);
        
        // Jika sudah dalam format +62
        if (strpos($phone, '+62') === 0) {
            return $phone;
        }
        
        // Jika dimulai dengan 62
        if (strpos($clean, '62') === 0) {
            return '+' . $clean;
        }
        
        // Jika dimulai dengan 08
        if (strpos($clean, '08') === 0) {
            return '+62' . substr($clean, 1);
        }
        
        // Default: tambahkan +62
        return '+62' . $clean;
    }
}

if (!function_exists('validate_gender')) {
    /**
     * Validasi jenis kelamin
     * 
     * @param string $gender Nilai gender
     * @param bool $required Apakah field wajib diisi
     * @return string|null Pesan error atau null jika valid
     */
    function validate_gender(string $gender, bool $required = false): ?string
    {
        $gender = trim($gender);
        
        if ($gender === '') {
            return $required ? 'Jenis kelamin wajib dipilih.' : null;
        }
        
        $allowed = ['Laki-laki', 'Perempuan'];
        if (!in_array($gender, $allowed)) {
            return 'Jenis kelamin tidak valid.';
        }
        
        return null;
    }
}
?>