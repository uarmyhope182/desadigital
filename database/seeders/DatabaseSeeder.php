<?php

class DatabaseSeeder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): void
    {
        // Truncate tables first
        $this->truncateTables();

        $this->pdo->beginTransaction();
        try {
            $this->seedUsers();
            $this->seedJenisSurat();
            $this->seedPenduduk(100); // Tambah jadi 100 penduduk
            $this->seedPengajuanSurat(200); // Tambah jadi 200 pengajuan
            $this->seedPengumuman(15);

            $this->pdo->commit();
            echo "Seeding complete.\n";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function truncateTables(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $tables = ['pengumuman', 'pengajuan_surat', 'penduduk', 'jenis_surat', 'users'];
        foreach ($tables as $t) {
            $this->pdo->exec("TRUNCATE TABLE `{$t}`");
        }
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        echo "Truncated tables.\n";
    }

    private function seedUsers(): void
    {
        $users = [
            ['username' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'name' => 'Administrator', 'role' => 'admin'],
            ['username' => 'staff1', 'password' => password_hash('staff123', PASSWORD_DEFAULT), 'name' => 'Staff Satu', 'role' => 'staff'],
            ['username' => 'staff2', 'password' => password_hash('staff123', PASSWORD_DEFAULT), 'name' => 'Staff Dua', 'role' => 'staff'],
            ['username' => 'staff3', 'password' => password_hash('staff123', PASSWORD_DEFAULT), 'name' => 'Staff Tiga', 'role' => 'staff'],
        ];
        
        $hasEmail = $this->columnExists('users', 'email');
        $hasFullName = $this->columnExists('users', 'full_name');

        if ($hasEmail) {
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password, name, role, email) VALUES (:username, :password, :name, :role, :email)');
            foreach ($users as $u) {
                $u['email'] = $u['username'] . '@example.local';
                $stmt->execute($u);
            }
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO users (username, password, name, role) VALUES (:username, :password, :name, :role)');
            foreach ($users as $u) {
                $stmt->execute($u);
            }
        }
        
        // Update full_name if column exists
        if ($hasFullName) {
            $this->pdo->exec("UPDATE users SET full_name = name WHERE full_name IS NULL");
        }
        
        echo "Seeded users.\n";
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column");
        $stmt->execute([':table' => $table, ':column' => $column]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function seedJenisSurat(): void
    {
        $list = [
            ['kode' => 'SKTM', 'nama' => 'Surat Keterangan Tidak Mampu', 'persyaratan' => "- Fotokopi KTP\n- Fotokopi KK\n- Surat Pengantar RT/RW", 'deskripsi' => 'Surat keterangan untuk warga kurang mampu'],
            ['kode' => 'SKU', 'nama' => 'Surat Keterangan Usaha', 'persyaratan' => "- Fotokopi KTP\n- Fotokopi KK\n- Deskripsi usaha", 'deskripsi' => 'Surat keterangan untuk pelaku UMKM'],
            ['kode' => 'SKD', 'nama' => 'Surat Keterangan Domisili', 'persyaratan' => "- Fotokopi KTP\n- Fotokopi KK\n- Surat pengantar RT/RW", 'deskripsi' => 'Surat keterangan tempat tinggal'],
            ['kode' => 'SKCK', 'nama' => 'Surat Keterangan Catatan Kepolisian', 'persyaratan' => "- Fotokopi KTP\n- Fotokopi KK\n- Pas foto", 'deskripsi' => 'Surat pengantar untuk SKCK'],
            ['kode' => 'SKB', 'nama' => 'Surat Keterangan Belum Menikah', 'persyaratan' => "- Fotokopi KTP\n- Fotokopi KK\n- Surat pengantar RT/RW", 'deskripsi' => 'Surat keterangan status belum menikah'],
            ['kode' => 'SKKB', 'nama' => 'Surat Keterangan Kehilangan', 'persyaratan' => "- Fotokopi KTP\n- Surat keterangan kehilangan dari kepolisian", 'deskripsi' => 'Surat keterangan kehilangan dokumen'],
        ];

        $stmt = $this->pdo->prepare('INSERT INTO jenis_surat (kode, nama, persyaratan, deskripsi, is_active) VALUES (:kode, :nama, :persyaratan, :deskripsi, 1)');
        foreach ($list as $l) {
            $stmt->execute($l);
        }
        echo "Seeded jenis_surat.\n";
    }

    private function seedPenduduk(int $count = 100): void
    {
        $maleNames = ['Agus', 'Budi', 'Cahya', 'Dedi', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Indra', 'Joko', 'Andi', 'Rudi', 'Sandi', 'Tono', 'Ujang'];
        $femaleNames = ['Ayu', 'Bunga', 'Citra', 'Dewi', 'Elisa', 'Fitri', 'Gita', 'Hani', 'Intan', 'Putri', 'Sari', 'Tika', 'Wulan', 'Yuni', 'Zahra'];
        $surnames = ['Pratama','Santoso','Saputra','Wijaya','Setiawan','Irawan','Wibowo','Sari','Nugroho','Kusuma','Hidayat','Permana','Maulana'];
        $streets = ['Jl. Merdeka','Jl. Kemerdekaan','Jl. Sudirman','Jl. Raya','Jl. Pahlawan','Jl. Diponegoro','Jl. Ahmad Yani','Jl. Gatot Subroto','Jl. Teuku Umar','Jl. Hasanuddin'];
        $dusunList = ['Dusun Barat', 'Dusun Timur', 'Dusun Utara', 'Dusun Selatan', 'Dusun Tengah'];
        $agama = ['Islam','Kristen','Katolik','Hindu','Buddha'];
        $pekerjaan = ['Petani','Wiraswasta','PNS','Buruh','Pelajar','Ibu Rumah Tangga','Pedagang','Nelayan','Guru','Dokter'];

        // Check which columns exist
        $hasFullName = $this->columnExists('penduduk', 'full_name');
        $hasGender = $this->columnExists('penduduk', 'gender');
        $hasAddress = $this->columnExists('penduduk', 'address');
        $hasDusun = $this->columnExists('penduduk', 'dusun');
        $hasNoHp = $this->columnExists('penduduk', 'no_hp');

        // Build insert query dynamically
        $columns = ['nik'];
        $placeholders = [':nik'];
        
        if ($hasFullName) {
            $columns[] = 'full_name';
            $placeholders[] = ':full_name';
        }
        if ($this->columnExists('penduduk', 'nama')) {
            $columns[] = 'nama';
            $placeholders[] = ':nama';
        }
        if ($this->columnExists('penduduk', 'tempat_lahir')) {
            $columns[] = 'tempat_lahir';
            $placeholders[] = ':tempat_lahir';
        }
        if ($this->columnExists('penduduk', 'tanggal_lahir')) {
            $columns[] = 'tanggal_lahir';
            $placeholders[] = ':tanggal_lahir';
        }
        if ($hasGender) {
            $columns[] = 'gender';
            $placeholders[] = ':gender';
        }
        if ($this->columnExists('penduduk', 'jenis_kelamin')) {
            $columns[] = 'jenis_kelamin';
            $placeholders[] = ':jenis_kelamin';
        }
        if ($hasAddress) {
            $columns[] = 'address';
            $placeholders[] = ':address';
        }
        if ($this->columnExists('penduduk', 'alamat')) {
            $columns[] = 'alamat';
            $placeholders[] = ':alamat';
        }
        if ($this->columnExists('penduduk', 'rt')) {
            $columns[] = 'rt';
            $placeholders[] = ':rt';
        }
        if ($this->columnExists('penduduk', 'rw')) {
            $columns[] = 'rw';
            $placeholders[] = ':rw';
        }
        if ($hasDusun) {
            $columns[] = 'dusun';
            $placeholders[] = ':dusun';
        }
        if ($this->columnExists('penduduk', 'agama')) {
            $columns[] = 'agama';
            $placeholders[] = ':agama';
        }
        if ($this->columnExists('penduduk', 'pekerjaan')) {
            $columns[] = 'pekerjaan';
            $placeholders[] = ':pekerjaan';
        }
        if ($hasNoHp) {
            $columns[] = 'no_hp';
            $placeholders[] = ':no_hp';
        }

        $sql = 'INSERT INTO penduduk (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);

        for ($i = 0; $i < $count; $i++) {
            $isFemale = rand(0, 1) === 1;
            $firstName = $isFemale ? $femaleNames[array_rand($femaleNames)] : $maleNames[array_rand($maleNames)];
            $lastName = $surnames[array_rand($surnames)];
            $fullName = $firstName . ' ' . $lastName;
            
            $tempat = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Karangasem', 'Bangli', 'Buleleng', 'Jembrana', 'Klungkung'][array_rand(['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Karangasem', 'Bangli', 'Buleleng', 'Jembrana', 'Klungkung'])];
            
            $timestamp = mt_rand(strtotime('1940-01-01'), strtotime('2010-12-31'));
            $tanggal = date('Y-m-d', $timestamp);
            
            $nik = $this->generateNik();
            $alamat = $streets[array_rand($streets)] . ' No. ' . rand(1, 200);
            $rt = str_pad((string)rand(1, 15), 3, '0', STR_PAD_LEFT);
            $rw = str_pad((string)rand(1, 10), 3, '0', STR_PAD_LEFT);
            $dusun = $dusunList[array_rand($dusunList)];
            $no_hp = '0812' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            
            $params = [
                ':nik' => $nik,
                ':full_name' => $fullName,
                ':nama' => $fullName,
                ':tempat_lahir' => $tempat,
                ':tanggal_lahir' => $tanggal,
                ':gender' => $isFemale ? 'Perempuan' : 'Laki-laki',
                ':jenis_kelamin' => $isFemale ? 'Perempuan' : 'Laki-laki',
                ':address' => $alamat,
                ':alamat' => $alamat,
                ':rt' => $rt,
                ':rw' => $rw,
                ':dusun' => $dusun,
                ':agama' => $agama[array_rand($agama)],
                ':pekerjaan' => $pekerjaan[array_rand($pekerjaan)],
                ':no_hp' => $no_hp,
            ];
            
            $stmt->execute($params);
        }
        echo "Seeded {$count} penduduk.\n";
    }

    private function seedPengajuanSurat(int $count = 200): void
    {
        // Check available columns
        $usesPendudukId = $this->columnExists('pengajuan_surat', 'penduduk_id');
        $usesNik = $this->columnExists('pengajuan_surat', 'nik');
        $hasNamaLengkap = $this->columnExists('pengajuan_surat', 'nama_lengkap');
        $hasTrackingId = $this->columnExists('pengajuan_surat', 'tracking_id');
        $hasCreatedAt = $this->columnExists('pengajuan_surat', 'created_at');
        $hasDiprosesPada = $this->columnExists('pengajuan_surat', 'diproses_pada');

        // Fetch penduduk reference data
        $pstmt = $this->pdo->query('SELECT id, nik, full_name, nama FROM penduduk');
        $pendudukRows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($pendudukRows)) {
            echo "No penduduk data available; skipping pengajuan_surat.\n";
            return;
        }

        $jstmt = $this->pdo->query('SELECT id FROM jenis_surat');
        $jenis = $jstmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($pendudukRows) || empty($jenis)) {
            echo "No penduduk or jenis_surat available; skipping pengajuan_surat.\n";
            return;
        }

        $statuses = $this->buildStatusDistribution($count, [
            'pending' => 30,
            'approved' => 40,
            'rejected' => 20,
            'processing' => 10,
        ]);

        // Build dynamic insert query
        $columns = [];
        $placeholders = [];
        
        if ($usesPendudukId) {
            $columns[] = 'penduduk_id';
            $placeholders[] = ':penduduk_id';
        }
        if ($usesNik) {
            $columns[] = 'nik';
            $placeholders[] = ':nik';
        }
        if ($hasNamaLengkap) {
            $columns[] = 'nama_lengkap';
            $placeholders[] = ':nama_lengkap';
        }
        if ($hasTrackingId) {
            $columns[] = 'tracking_id';
            $placeholders[] = ':tracking_id';
        }
        
        // Add jenis_surat_id
        $columns[] = 'jenis_surat_id';
        $placeholders[] = ':jenis_surat_id';
        
        // Add keperluan/tujuan
        if ($this->columnExists('pengajuan_surat', 'keperluan')) {
            $columns[] = 'keperluan';
            $placeholders[] = ':keperluan';
        } elseif ($this->columnExists('pengajuan_surat', 'tujuan')) {
            $columns[] = 'tujuan';
            $placeholders[] = ':keperluan';
        }
        
        // Add alamat
        if ($this->columnExists('pengajuan_surat', 'alamat')) {
            $columns[] = 'alamat';
            $placeholders[] = ':alamat';
        }
        
        // Add status
        $columns[] = 'status';
        $placeholders[] = ':status';
        
        // Add catatan/note
        if ($this->columnExists('pengajuan_surat', 'catatan')) {
            $columns[] = 'catatan';
            $placeholders[] = ':catatan';
        } elseif ($this->columnExists('pengajuan_surat', 'note')) {
            $columns[] = 'note';
            $placeholders[] = ':catatan';
        }
        
        // Add nomor_surat
        if ($this->columnExists('pengajuan_surat', 'nomor_surat')) {
            $columns[] = 'nomor_surat';
            $placeholders[] = ':nomor_surat';
        }
        
        // Add created_at
        if ($hasCreatedAt) {
            $columns[] = 'created_at';
            $placeholders[] = ':created_at';
        }
        
        // Add tanggal_pengajuan if exists
        if ($this->columnExists('pengajuan_surat', 'tanggal_pengajuan')) {
            $columns[] = 'tanggal_pengajuan';
            $placeholders[] = ':tanggal_pengajuan';
        }
        
        // Add diproses_pada
        if ($hasDiprosesPada) {
            $columns[] = 'diproses_pada';
            $placeholders[] = ':diproses_pada';
        }

        $sql = 'INSERT INTO pengajuan_surat (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);

        $keperluanList = [
            'Pengajuan Bantuan Sosial',
            'Pendaftaran Sekolah Anak',
            'Keperluan Kerja',
            'Pengurusan Dokumen',
            'Beasiswa Pendidikan',
            'Administrasi Kependudukan',
            'Perpanjangan SKCK',
            'Pengajuan Kredit Bank',
            'Pendaftaran Nikah',
            'Pengurusan Warisan'
        ];

        for ($i = 0; $i < $count; $i++) {
            $pend = $pendudukRows[array_rand($pendudukRows)];
            $jenisId = $jenis[array_rand($jenis)];
            $status = $statuses[$i];
            
            // RANDOM DATE - Generate random date within last year
            $randomDays = rand(0, 365);
            $createdAt = date('Y-m-d H:i:s', strtotime("-$randomDays days"));
            
            // Random tanggal_pengajuan (different from created_at sometimes)
            $tanggalPengajuan = date('Y-m-d', strtotime("-$randomDays days"));
            
            // Random diproses_pada for approved/rejected/processing
            $diprosesPada = null;
            $nomorSurat = null;
            if ($status === 'approved') {
                $processDays = rand(1, 7);
                $diprosesPada = date('Y-m-d H:i:s', strtotime($createdAt . " + $processDays days"));
                $nomorSurat = '140/' . rand(100, 999) . '/IX/' . date('Y', strtotime($createdAt));
            } elseif ($status === 'rejected') {
                $processDays = rand(1, 3);
                $diprosesPada = date('Y-m-d H:i:s', strtotime($createdAt . " + $processDays days"));
            } elseif ($status === 'processing') {
                $processDays = rand(1, 2);
                $diprosesPada = date('Y-m-d H:i:s', strtotime($createdAt . " + $processDays days"));
            }
            
            // Generate tracking ID
            $trackingId = 'TRK' . date('Ymd', strtotime($createdAt)) . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            
            // Get penduduk name
            $namaLengkap = $pend['full_name'] ?? $pend['nama'] ?? 'Unknown';
            
            $params = [
                ':penduduk_id' => $pend['id'],
                ':nik' => $pend['nik'],
                ':nama_lengkap' => $namaLengkap,
                ':tracking_id' => $trackingId,
                ':jenis_surat_id' => $jenisId,
                ':keperluan' => $keperluanList[array_rand($keperluanList)] . ' - ' . $namaLengkap,
                ':alamat' => 'Jl. Contoh No. ' . rand(1, 200) . ', RT ' . rand(1, 15) . '/RW ' . rand(1, 10),
                ':status' => $status,
                ':catatan' => $status === 'rejected' ? 'Dokumen tidak lengkap, silakan lengkapi persyaratan' : null,
                ':nomor_surat' => $nomorSurat,
                ':created_at' => $createdAt,
                ':tanggal_pengajuan' => $tanggalPengajuan,
                ':diproses_pada' => $diprosesPada,
            ];
            
            $stmt->execute($params);
        }

        echo "Seeded {$count} pengajuan_surat with random dates.\n";
    }

    private function seedPengumuman(int $count = 15): void
    {
        $titleCol = $this->columnExists('pengumuman', 'title') ? 'title' : ($this->columnExists('pengumuman', 'judul') ? 'judul' : null);
        $contentCol = $this->columnExists('pengumuman', 'content') ? 'content' : ($this->columnExists('pengumuman', 'isi') ? 'isi' : null);
        $publishedCol = $this->columnExists('pengumuman', 'published_at') ? 'published_at' : ($this->columnExists('pengumuman', 'tanggal') ? 'tanggal' : null);
        $createdAtCol = $this->columnExists('pengumuman', 'created_at') ? 'created_at' : null;
        $isPublishedCol = $this->columnExists('pengumuman', 'is_published') ? 'is_published' : null;

        if (!$titleCol) {
            echo "No title column found in pengumuman; skipping.\n";
            return;
        }

        $cols = [];
        $placeholders = [];
        
        if ($titleCol) { $cols[] = $titleCol; $placeholders[] = ':title'; }
        if ($contentCol) { $cols[] = $contentCol; $placeholders[] = ':content'; }
        if ($publishedCol) { $cols[] = $publishedCol; $placeholders[] = ':published_at'; }
        if ($createdAtCol) { $cols[] = $createdAtCol; $placeholders[] = ':created_at'; }
        if ($isPublishedCol) { $cols[] = $isPublishedCol; $placeholders[] = ':is_published'; }

        $sql = 'INSERT INTO pengumuman (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);

        $titles = [
            'Pendaftaran Bantuan Sosial 2024',
            'Pelatihan UMKM Gratis Se-Desa',
            'Lomba 17 Agustus Tingkat RT/RW',
            'Penyuluhan Kesehatan Ibu dan Anak',
            'Gotong Royong Bersih Desa',
            'Pembagian BLT Tahap 3',
            'Rapat Koordinasi RT/RW Se-Desa',
            'Pendataan Ulang Penduduk',
            'Vaksinasi Booster Covid-19',
            'Bazar Murah Ramadhan',
            'Pengumuman Lelang Jabatan',
            'Peringatan Hari Kemerdekaan',
            'Sosialisasi Bahaya Narkoba',
            'Pelatihan Komputer untuk Pemuda',
            'Pembentukan Karang Taruna Baru'
        ];

        for ($i = 0; $i < $count; $i++) {
            $randomDays = rand(0, 90);
            $createdAt = date('Y-m-d H:i:s', strtotime("-$randomDays days"));
            $publishedAt = rand(0, 1) ? $createdAt : null;
            $isPublished = $publishedAt ? 1 : 0;
            
            $params = [
                ':title' => $titles[$i % count($titles)] . ' (' . date('Y', strtotime($createdAt)) . ')',
                ':content' => 'Pengumuman penting untuk seluruh warga masyarakat. ' . 
                              'Informasi selengkapnya dapat menghubungi kantor kelurahan. ' .
                              'Terima kasih atas perhatiannya.',
                ':published_at' => $publishedAt,
                ':created_at' => $createdAt,
                ':is_published' => $isPublished,
            ];
            
            $stmt->execute($params);
        }

        echo "Seeded {$count} pengumuman.\n";
    }

    // --- Helpers ---
    private function generateNik(): string
    {
        $nik = '32';
        for ($i = 0; $i < 14; $i++) {
            $nik .= (string)rand(0, 9);
        }
        return $nik;
    }

    private function buildStatusDistribution(int $count, array $percentages): array
    {
        $list = [];
        foreach ($percentages as $status => $pct) {
            $n = (int)round($count * ($pct / 100));
            for ($i = 0; $i < $n; $i++) $list[] = $status;
        }
        while (count($list) < $count) $list[] = 'pending';
        while (count($list) > $count) array_pop($list);
        shuffle($list);
        return $list;
    }
}