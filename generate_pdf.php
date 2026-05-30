<?php
require_once __DIR__ . '/config/db.php';

$trackingId = trim($_GET['tracking_id'] ?? '');
$submissionId = trim($_GET['id'] ?? '');

if ($trackingId === '' && $submissionId === '') {
    http_response_code(400);
    echo 'Parameter tracking_id atau id diperlukan.';
    exit;
}

if ($submissionId !== '' && ctype_digit($submissionId)) {
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
    $stmt->execute([':id' => $submissionId]);
} else {
    $stmt = $pdo->prepare('
        SELECT ps.*, js.nama AS jenis_surat, js.kode AS jenis_code,
               p.full_name AS penduduk_name, p.address AS penduduk_address,
               p.tempat_lahir, p.tanggal_lahir, p.gender, p.pekerjaan,
               p.rt, p.rw, p.dusun
        FROM pengajuan_surat ps 
        JOIN jenis_surat js ON ps.jenis_surat_id = js.id 
        LEFT JOIN penduduk p ON ps.nik = p.nik 
        WHERE ps.tracking_id = :tracking_id LIMIT 1
    ');
    $stmt->execute([':tracking_id' => $trackingId]);
}

$submission = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$submission) {
    http_response_code(404);
    echo 'Permohonan tidak ditemukan.';
    exit;
}

if ($submission['status'] !== 'approved') {
    http_response_code(403);
    echo 'Surat hanya dapat dicetak jika permohonan telah disetujui.';
    exit;
}

// Fungsi generate nomor surat jika belum ada
if (!function_exists('generate_nomor_surat')) {
    function generate_nomor_surat($pdo) {
        $year = date('Y');
        $month = date('m');
        
        $stmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM pengajuan_surat WHERE YEAR(created_at) = :year");
        $stmt->execute([':year' => $year]);
        $nomor_urut = $stmt->fetchColumn();
        
        return sprintf('470/%03d/Kel.Sasi/%s/%s', $nomor_urut, $month, $year);
    }
}

$nomorSurat = $submission['nomor_surat'];
if (empty($nomorSurat)) {
    $nomorSurat = generate_nomor_surat($pdo);
    $update = $pdo->prepare('UPDATE pengajuan_surat SET nomor_surat = :nomor_surat, tgl_surat = NOW() WHERE id = :id');
    $update->execute([':nomor_surat' => $nomorSurat, ':id' => $submission['id']]);
}

$tglSurat = $submission['tgl_surat'] ?? date('Y-m-d H:i:s');
$formattedTanggal = date('d F Y', strtotime($tglSurat));

// Ambil data tambahan dari JSON (untuk SKU, SKP, dll)
$data_tambahan = [];
if (!empty($submission['data_tambahan'])) {
    $data_tambahan = json_decode($submission['data_tambahan'], true);
}

$alamat = nl2br(htmlspecialchars($submission['alamat']));
$keperluan = nl2br(htmlspecialchars($submission['keperluan'] ?? '-'));

// Tentukan jenis surat
$jenisKode = strtoupper($submission['jenis_code'] ?? '');
$jenisNama = strtolower($submission['jenis_surat'] ?? '');

$bodyContent = '';
$letterTitle = 'SURAT KETERANGAN';
$additionalSection = '';
$purposeLabel = 'Keperluan';
$purposeValue = $keperluan;

if (!empty($submission['jenis_surat'])) {
    $rawJenis = trim(preg_replace('/\s+/', ' ', $submission['jenis_surat']));
    if (preg_match('/^\s*surat\s+/i', $rawJenis)) {
        $letterTitle = strtoupper($rawJenis);
    } else {
        $letterTitle = 'SURAT KETERANGAN ' . strtoupper($rawJenis);
    }
}

// SURAT DOMISILI (SKD)
if ($jenisKode === 'SKD' || strpos($jenisNama, 'domisili') !== false) {
    $letterTitle = 'SURAT KETERANGAN DOMISILI';
    $additionalSection = sprintf(
        '<tr><td class="label">Alamat Domisili</td><td>: </td><td>%s</td></tr>',
        $alamat
    );
    $purposeLabel = 'Tujuan';
}
// SURAT TIDAK MAMPU (SKTM)
elseif ($jenisKode === 'SKTM' || strpos($jenisNama, 'tidak mampu') !== false) {
    $letterTitle = 'SURAT KETERANGAN TIDAK MAMPU';
    $additionalSection = '<tr><td class="label">Keterangan</td><td>: </td><td>Yang bersangkutan tidak mampu secara ekonomi.</td></tr>';
    $purposeLabel = 'Tujuan Surat';
}
// SURAT USAHA (SKU)
elseif ($jenisKode === 'SKU' || strpos($jenisNama, 'usaha') !== false) {
    $letterTitle = 'SURAT KETERANGAN USAHA';
    $namaUsaha = $data_tambahan['nama_usaha'] ?? $keperluan ?? '-';
    $bidangUsaha = $data_tambahan['bidang_usaha'] ?? $keperluan ?? '-';
    $alamatUsaha = $data_tambahan['alamat_usaha'] ?? $submission['alamat'] ?? '-';
    
    $additionalSection = sprintf(
        '<tr><td class="label">Nama Usaha</td><td>: </td><td>%s</td></tr>
         <tr><td class="label">Bidang Usaha</td><td>: </td><td>%s</td></tr>
         <tr><td class="label">Alamat Usaha</td><td>: </td><td>%s</td></tr>',
        htmlspecialchars($namaUsaha),
        htmlspecialchars($bidangUsaha),
        htmlspecialchars($alamatUsaha)
    );
    $purposeLabel = 'Tujuan Surat';
}
// SURAT PINDAH (SKP)
elseif ($jenisKode === 'SKP' || strpos($jenisNama, 'pindah') !== false) {
    $letterTitle = 'SURAT KETERANGAN PINDAH';
    $alamatTujuan = $data_tambahan['alamat_tujuan'] ?? $keperluan ?? '-';
    $alasanPindah = $data_tambahan['alasan_pindah'] ?? '-';
    
    $additionalSection = sprintf(
        '<tr><td class="label">Alamat Asal</td><td>: </td><td>%s</td></tr>
         <tr><td class="label">Alamat Tujuan</td><td>: </td><td>%s</td></tr>
         <tr><td class="label">Alasan Pindah</td><td>: </td><td>%s</td></tr>',
        htmlspecialchars($submission['alamat']),
        htmlspecialchars($alamatTujuan),
        htmlspecialchars($alasanPindah)
    );
    $purposeLabel = 'Alasan';
}
// SURAT BELUM MENIKAH (SKB)
elseif ($jenisKode === 'SKB' || strpos($jenisNama, 'belum menikah') !== false) {
    $letterTitle = 'SURAT KETERANGAN BELUM MENIKAH';
    $additionalSection = '<tr><td class="label">Keterangan</td><td>: </td><td>Yang bersangkutan belum pernah menikah.</td></tr>';
    $purposeLabel = 'Tujuan Surat';
}

// Format tanggal lahir
$tglLahir = '';
if (!empty($submission['tanggal_lahir'])) {
    $tglLahir = date('d F Y', strtotime($submission['tanggal_lahir']));
}

$html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($letterTitle) . '</title>
    <style>
        @page { size: A4 portrait; margin: 3cm 3cm 3cm 3cm; }
        body { font-family: "Times New Roman", serif; font-size: 12pt; color: #000; margin: 0; padding: 0; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 1.5rem; }
        .header .kelurahan { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .header .address { font-size: 10pt; }
        .header .title { font-size: 14pt; font-weight: bold; margin: 1rem 0 0.5rem; text-transform: uppercase; }
        .nomor-surat { text-align: center; margin-bottom: 1.5rem; }
        .nomor-surat strong { font-weight: bold; }
        .table-data { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table-data td { vertical-align: top; padding: 0.25rem 0; }
        .table-data .label { width: 30%; font-weight: bold; }
        .signature { margin-top: 3rem; text-align: right; }
        .signature .name { margin-top: 2rem; text-decoration: underline; font-weight: bold; }
        hr { border: 1px solid #000; margin: 0.5rem 0; }
    </style>
</head>
<body>';

$html .= '<div class="header">';
$html .= '<div class="kelurahan">PEMERINTAH KELURAHAN SASI</div>';
$html .= '<div class="address">Kecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, NTT</div>';
$html .= '<div class="address">Jl. Merdeka No. 24 Kefamenanu</div>';
$html .= '<hr>';
$html .= '<div class="title">' . htmlspecialchars($letterTitle) . '</div>';
$html .= '</div>';

$html .= '<div class="nomor-surat">Nomor: <strong>' . htmlspecialchars($nomorSurat) . '</strong></div>';

$html .= '<p>Yang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, menerangkan dengan sesungguhnya bahwa:</p>';

$html .= '<table class="table-data">';
$html .= sprintf('<tr><td class="label">Nama Lengkap</td><td>: </td><td>%s</td></tr>', htmlspecialchars($submission['nama_lengkap']));
$html .= sprintf('<tr><td class="label">NIK</td><td>: </td><td>%s</td></tr>', htmlspecialchars($submission['nik']));

if (!empty($submission['tempat_lahir']) && !empty($tglLahir)) {
    $html .= sprintf('<tr><td class="label">Tempat, Tgl Lahir</td><td>: </td><td>%s, %s</td></tr>', htmlspecialchars($submission['tempat_lahir']), $tglLahir);
}
if (!empty($submission['gender'])) {
    $html .= sprintf('<tr><td class="label">Jenis Kelamin</td><td>: </td><td>%s</td></tr>', htmlspecialchars($submission['gender']));
}
if (!empty($submission['pekerjaan'])) {
    $html .= sprintf('<tr><td class="label">Pekerjaan</td><td>: </td><td>%s</td></tr>', htmlspecialchars($submission['pekerjaan']));
}

$html .= sprintf('<tr><td class="label">Alamat</td><td>: </td><td>%s</td></tr>', $alamat);
$html .= $additionalSection;
$html .= sprintf('<tr><td class="label">%s</td><td>: </td><td>%s</td></tr>', htmlspecialchars($purposeLabel), $purposeValue);
$html .= '</table>';

$html .= '<p style="margin-top: 1.5rem;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>';

$html .= '<div class="signature">';
$html .= '<p>Kefamenanu, ' . htmlspecialchars($formattedTanggal) . '</p>';
$html .= '<div class="name">(Nama Kepala Kelurahan)</div>';
$html .= '<div style="margin-top: 0.25rem;">NIP. -</div>';
$html .= '</div>';

$html .= '</body></html>';

// Load Dompdf
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    $autoload = __DIR__ . '/../vendor/autoload.php';
}
if (!file_exists($autoload)) {
    http_response_code(500);
    echo 'Dompdf belum terpasang. Jalankan <strong>composer require dompdf/dompdf</strong> kemudian coba lagi.';
    exit;
}

require_once $autoload;

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Times New Roman');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Surat_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', $submission['tracking_id']) . '.pdf', ['Attachment' => false]);
exit;
?>