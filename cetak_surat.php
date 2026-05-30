<?php
require_once __DIR__ . '/config/db.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: kelola_permohonan.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT ps.*, js.nama AS jenis_surat, js.kode AS jenis_code,
           p.full_name AS penduduk_name, p.address AS penduduk_address,
           p.tempat_lahir, p.tanggal_lahir, p.gender, p.pekerjaan,
           p.rt, p.rw, p.dusun
    FROM pengajuan_surat ps 
    JOIN jenis_surat js ON ps.jenis_surat_id = js.id 
    LEFT JOIN penduduk p ON ps.nik = p.nik 
    WHERE ps.id = :id LIMIT 1
');
$stmt->execute([':id' => $id]);
$submission = $stmt->fetch();

if (!$submission || $submission['status'] !== 'approved') {
    header('Location: kelola_permohonan.php');
    exit;
}

$page_title = 'Preview Surat - ' . htmlspecialchars($submission['jenis_surat']);
$activePage = 'kelola';
$is_admin_page = true;

include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-0">Preview Surat</h3>
            <p class="text-muted mb-0"><?= htmlspecialchars($submission['jenis_surat']) ?> - <?= htmlspecialchars($submission['nama_lengkap']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="generate_pdf.php?id=<?= $id ?>" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>Cetak PDF
            </a>
            <a href="kelola_permohonan.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-5" style="background: #fff;">
            <!-- Kop Surat -->
            <div class="text-center mb-4">
                <h4 class="mb-0 fw-bold">PEMERINTAH KELURAHAN SASI</h4>
                <p class="mb-0">Kecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, NTT</p>
                <p class="mb-0">Jl. Merdeka No. 24 Kefamenanu</p>
                <hr class="my-3">
            </div>

            <!-- Judul Surat -->
            <?php
            $kode = strtoupper($submission['jenis_code'] ?? '');
            $judul = '';
            if ($kode === 'SKTM') $judul = 'SURAT KETERANGAN TIDAK MAMPU';
            elseif ($kode === 'SKU') $judul = 'SURAT KETERANGAN USAHA';
            elseif ($kode === 'SKP') $judul = 'SURAT KETERANGAN PINDAH';
            elseif ($kode === 'SKB') $judul = 'SURAT KETERANGAN BELUM MENIKAH';
            else $judul = 'SURAT KETERANGAN DOMISILI';
            ?>
            <div class="text-center mb-4">
                <h5 class="fw-bold"><?= $judul ?></h5>
                <p>Nomor: <strong><?= htmlspecialchars($submission['nomor_surat'] ?? 'Belum tersedia') ?></strong></p>
            </div>

            <!-- Isi Surat -->
            <p>Yang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, menerangkan dengan sesungguhnya bahwa:</p>

            <table class="table table-borderless" style="width: 100%; margin-bottom: 20px;">
                <tr><td style="width: 30%;">Nama Lengkap</td><td style="width: 5%;">:</td><td><?= htmlspecialchars($submission['nama_lengkap']) ?></td></tr>
                <tr><td>NIK</td><td>:</td><td><?= htmlspecialchars($submission['nik']) ?></td></tr>
                <?php if (!empty($submission['tempat_lahir'])): ?>
                <tr><td>Tempat, Tgl Lahir</td><td>:</td><td><?= htmlspecialchars($submission['tempat_lahir']) ?>, <?= date('d F Y', strtotime($submission['tanggal_lahir'])) ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($submission['gender'])): ?>
                <tr><td>Jenis Kelamin</td><td>:</td><td><?= htmlspecialchars($submission['gender']) ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($submission['pekerjaan'])): ?>
                <tr><td>Pekerjaan</td><td>:</td><td><?= htmlspecialchars($submission['pekerjaan']) ?></td></tr>
                <?php endif; ?>
                <tr><td>Alamat</td><td>:</td><td><?= nl2br(htmlspecialchars($submission['alamat'])) ?></td></tr>
            </table>

            <!-- Tambahan sesuai jenis surat -->
            <?php if ($kode === 'SKU'): ?>
                <p>Bahwa yang bersangkutan benar-benar memiliki dan menjalankan usaha:</p>
                <table class="table table-borderless" style="width: 100%; margin-bottom: 20px;">
                    <tr><td style="width: 30%;">Nama Usaha</td><td style="width: 5%;">:</td><td><?= htmlspecialchars($submission['keperluan'] ?? '-') ?></td></tr>
                    <tr><td>Bidang Usaha</td><td>:</td><td><?= htmlspecialchars($submission['keperluan'] ?? '-') ?></td></tr>
                    <tr><td>Alamat Usaha</td><td>:</td><td><?= nl2br(htmlspecialchars($submission['alamat'])) ?></td></tr>
                </table>
            <?php endif; ?>

            <?php if ($kode === 'SKP'): ?>
                <p>Bahwa yang bersangkutan akan pindah dengan keterangan sebagai berikut:</p>
                <table class="table table-borderless" style="width: 100%; margin-bottom: 20px;">
                    <tr><td style="width: 30%;">Alamat Tujuan</td><td style="width: 5%;">:</td><td><?= nl2br(htmlspecialchars($submission['keperluan'] ?? '-')) ?></td></tr>
                    <tr><td>Alasan Pindah</td><td>:</td><td><?= nl2br(htmlspecialchars($submission['catatan'] ?? '-')) ?></td></tr>
                </table>
            <?php endif; ?>

            <?php if ($kode === 'SKB'): ?>
                <p>Bahwa yang bersangkutan belum pernah menikah dan statusnya tercatat sebagai lajang sesuai data kependudukan.</p>
            <?php endif; ?>

            <?php if ($kode === 'SKTM'): ?>
                <p>Bahwa yang bersangkutan termasuk dalam keluarga tidak mampu secara ekonomi.</p>
            <?php endif; ?>

            <p>Surat ini dibuat untuk keperluan: <?= nl2br(htmlspecialchars($submission['keperluan'] ?? '-')) ?></p>
            <p>Demikian surat keterangan ini dibuat agar dapat digunakan sebagaimana mestinya.</p>

            <!-- Tanda Tangan -->
            <div class="mt-5" style="text-align: right;">
                <p>Kefamenanu, <?= date('d F Y') ?></p>
                <div style="margin-top: 50px;">
                    <p class="mb-0">Kepala Kelurahan Sasi</p>
                    <p class="mt-4"><strong>(_____________________)</strong></p>
                    <p class="mt-2">NIP. -</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-5 pt-3 text-center">
                <hr>
                <p class="text-muted small mb-0">Dokumen ini dicetak dari sistem informasi kelurahan. Surat asli harus ditandatangani basah oleh Kepala Kelurahan.</p>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .btn, .navbar, footer, .sidebar, .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    body {
        background: white;
    }
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>