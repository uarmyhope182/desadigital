<?php
$page_title = 'Lacak Permohonan';
$activePage = 'lacak';
$is_admin_page = false;

require_once __DIR__ . '/../config/db.php';

$trackingId = sanitize_string($_GET['tracking_id'] ?? '');
$errors = [];
$result = null;
$history = [];

if ($trackingId !== '') {
    if (!preg_match('/^[A-Za-z0-9\-]{5,50}$/', $trackingId)) {
        $errors[] = 'Tracking ID tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare('
                SELECT ps.*, js.nama AS jenis_surat 
                FROM pengajuan_surat ps 
                JOIN jenis_surat js ON ps.jenis_surat_id = js.id 
                WHERE ps.tracking_id = :tracking_id 
                LIMIT 1
            ');
            $stmt->execute([':tracking_id' => $trackingId]);
            $result = $stmt->fetch();

            if ($result) {
                $historyStmt = $pdo->prepare('
                    SELECT status_lama, status_baru, keterangan, created_at 
                    FROM riwayat_tracking 
                    WHERE pengajuan_id = :pengajuan_id 
                    ORDER BY created_at DESC
                ');
                $historyStmt->execute([':pengajuan_id' => $result['id']]);
                $history = $historyStmt->fetchAll();
            } else {
                $errors[] = 'Tracking ID tidak ditemukan dalam sistem.';
            }
        } catch (Exception $e) {
            error_log('Error querying tracking: ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan saat mencari data. Silakan coba lagi.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Ghibli Theme - Lacak Permohonan -->
<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>
<div class="page-bg-atmosphere"></div>

<style>
/* ============================================
   GHIBLI THEME - LACAK PERMOHONAN
   ============================================ */

:root {
    --ghibli-cream: #FDFBF7;
    --ghibli-beige: #FAF6F0;
    --ghibli-warm: #FFF8EF;
    --ghibli-sage: #9CAF88;
    --ghibli-sage-dark: #7A8F64;
    --ghibli-sage-light: #B5C4A3;
    --ghibli-peach: #F6C7A1;
    --ghibli-peach-dark: #F0B885;
    --ghibli-sky: #B9DCFF;
    --ghibli-text-dark: #1A1512;
    --ghibli-text-soft: #2C241E;
    --ghibli-text-light: #3A3028;
    --ghibli-border: #E8E0D5;
    --ghibli-shadow: rgba(58, 49, 43, 0.05);
    --ghibli-shadow-hover: rgba(58, 49, 43, 0.1);
    --status-pending: #F6C7A1;
    --status-processing: #B9DCFF;
    --status-approved: #9CAF88;
    --status-rejected: #E8C5C5;
}

/* Page Background */
.page-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, var(--ghibli-sky) 0%, #D4E8FF 50%, var(--ghibli-beige) 100%);
    z-index: 0;
    pointer-events: none;
}

.page-bg-rice-fields {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/rice-fields.png') center/cover no-repeat;
    opacity: 0.06;
    z-index: 0;
    pointer-events: none;
}

.page-bg-atmosphere {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/06-atmospheric-elements.png') center bottom/cover no-repeat;
    opacity: 0.08;
    mix-blend-mode: soft-light;
    z-index: 0;
    pointer-events: none;
}

/* Main Container */
.page-container {
    position: relative;
    z-index: 2;
    padding: 1.5rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section */
.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
}

.page-header p {
    color: var(--ghibli-text-soft);
    font-size: 0.9rem;
}

/* Main Card */
.ghibli-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 28px;
    border: 1px solid rgba(156, 175, 136, 0.2);
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 24px var(--ghibli-shadow);
}

.ghibli-card:hover {
    box-shadow: 0 12px 32px var(--ghibli-shadow-hover);
}

.card-header-custom {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--ghibli-border);
    background: rgba(156, 175, 136, 0.05);
}

.card-header-custom h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-header-custom h3 i {
    color: var(--ghibli-sage);
    font-size: 1.3rem;
}

.card-header-custom p {
    color: var(--ghibli-text-soft);
    font-size: 0.85rem;
    margin-bottom: 0;
}

.card-body-custom {
    padding: 2rem;
}

/* Tracking Form */
.tracking-form {
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.tracking-input {
    flex: 1;
    padding: 0.85rem 1.25rem;
    font-size: 0.9rem;
    border: 1.5px solid var(--ghibli-border);
    border-radius: 50px;
    background: white;
    color: var(--ghibli-text-dark);
    transition: all 0.2s ease;
    font-family: inherit;
}

.tracking-input:focus {
    border-color: var(--ghibli-sage);
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
    outline: none;
}

.btn-track {
    background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 0.85rem 2rem;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-track:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

/* Error Alert */
.alert-error {
    background: #FFF5F5;
    border-left: 4px solid #E8C5C5;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error i {
    color: #C58B8B;
    font-size: 1.2rem;
}

.alert-error p {
    color: #8B5E5E;
    margin: 0;
    font-size: 0.85rem;
}

/* Success Banner */
.success-banner {
    background: #F0F7ED;
    border: 1px solid var(--ghibli-sage-light);
    border-radius: 20px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.success-banner i {
    font-size: 2rem;
    color: var(--ghibli-sage);
}

.success-banner h5 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ghibli-sage-dark);
    margin-bottom: 0.25rem;
}

.success-banner p {
    font-size: 0.85rem;
    color: var(--ghibli-text-soft);
    margin-bottom: 0;
}

/* Section Title */
.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--ghibli-sage-light);
    display: inline-block;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-card {
    background: rgba(156, 175, 136, 0.06);
    border-radius: 16px;
    padding: 1rem;
}

.info-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--ghibli-text-soft);
    opacity: 0.7;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ghibli-text-dark);
    margin: 0;
}

.tracking-id {
    font-family: 'Courier New', monospace;
    background: rgba(156, 175, 136, 0.1);
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    display: inline-block;
}

/* Details Card */
.details-card {
    background: rgba(156, 175, 136, 0.05);
    border-radius: 20px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--ghibli-text-soft);
    opacity: 0.7;
}

.detail-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--ghibli-text-dark);
}

.detail-value.highlight {
    color: var(--ghibli-sage-dark);
    font-weight: 700;
}

/* Status Badge */
.status-container {
    margin-bottom: 1.5rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending {
    background: var(--status-pending);
    color: #4A3A2A;
}

.status-processing {
    background: var(--status-processing);
    color: #2C5F6E;
}

.status-approved {
    background: var(--status-approved);
    color: white;
}

.status-rejected {
    background: var(--status-rejected);
    color: #6B4A4A;
}

/* Notes Card */
.notes-card {
    background: rgba(246, 199, 161, 0.12);
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.notes-card h6 {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
}

.notes-card p {
    font-size: 0.85rem;
    color: var(--ghibli-text-soft);
    margin-bottom: 0;
    line-height: 1.5;
}

/* Print Button */
.btn-print {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--ghibli-peach);
    color: var(--ghibli-text-dark);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    margin-bottom: 1.5rem;
}

.btn-print:hover {
    background: var(--ghibli-peach-dark);
    transform: translateY(-2px);
    text-decoration: none;
    color: var(--ghibli-text-dark);
}

/* Divider */
.divider {
    height: 1px;
    background: linear-gradient(90deg, var(--ghibli-border), transparent);
    margin: 1.5rem 0;
}

/* Empty History */
.empty-history {
    text-align: center;
    padding: 2rem;
    background: rgba(156, 175, 136, 0.05);
    border-radius: 20px;
}

.empty-history p {
    color: var(--ghibli-text-soft);
    margin: 0;
    font-size: 0.85rem;
}

/* Timeline */
.timeline {
    margin-top: 1rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.25rem;
    position: relative;
}

.timeline-marker {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 30px;
}

.marker-dot {
    width: 12px;
    height: 12px;
    background: var(--ghibli-sage);
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px var(--ghibli-sage-light);
    z-index: 2;
}

.marker-line {
    width: 2px;
    flex: 1;
    background: var(--ghibli-border);
    margin-top: 8px;
    min-height: 20px;
}

.timeline-content {
    flex: 1;
    padding-bottom: 0.5rem;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.timeline-status {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin: 0;
}

.timeline-date {
    font-size: 0.7rem;
    color: var(--ghibli-text-soft);
    opacity: 0.6;
}

.timeline-desc {
    font-size: 0.8rem;
    color: var(--ghibli-text-soft);
    margin: 0;
    line-height: 1.4;
}

/* Sidebar Cards */
.sidebar-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    border: 1px solid rgba(156, 175, 136, 0.2);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.sidebar-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--ghibli-shadow-hover);
}

.sidebar-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--ghibli-border);
    background: rgba(156, 175, 136, 0.05);
}

.sidebar-card-header h5 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-card-header i {
    color: var(--ghibli-sage);
    font-size: 1.1rem;
}

.sidebar-card-body {
    padding: 1.25rem 1.5rem;
}

/* Tips List */
.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    position: relative;
    padding-left: 1.25rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
    color: var(--ghibli-text-soft);
    line-height: 1.5;
}

.tips-list li:last-child {
    margin-bottom: 0;
}

.tips-list li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: var(--ghibli-sage);
    font-weight: 700;
    font-size: 1rem;
}

/* Contact Info */
.help-text {
    font-size: 0.85rem;
    color: var(--ghibli-text-soft);
    line-height: 1.5;
    margin-bottom: 1rem;
}

.contact-info {
    background: rgba(156, 175, 136, 0.06);
    border-radius: 16px;
    padding: 0.75rem;
}

.contact-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}

.contact-row:first-child {
    padding-top: 0;
}

.contact-row:last-child {
    padding-bottom: 0;
}

.contact-icon {
    width: 28px;
    color: var(--ghibli-sage);
    font-size: 0.9rem;
}

.contact-value {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--ghibli-text-dark);
}

/* Responsive */
@media (max-width: 992px) {
    .page-container {
        padding: 1rem;
    }
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 1.4rem;
    }
    
    .card-header-custom {
        padding: 1rem 1.25rem;
    }
    
    .card-body-custom {
        padding: 1.25rem;
    }
    
    .form-group {
        flex-direction: column;
    }
    
    .btn-track {
        justify-content: center;
        width: 100%;
    }
    
    .info-grid,
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .timeline-header {
        flex-direction: column;
    }
}

@media (max-width: 576px) {
    .success-banner {
        flex-direction: column;
        text-align: center;
    }
    
    .sidebar-card-header {
        padding: 0.85rem 1rem;
    }
    
    .sidebar-card-body {
        padding: 1rem;
    }
}
</style>

<div class="page-container">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="ghibli-card">
                <div class="card-header-custom">
                    <h3>
                        <i class="bi bi-search"></i>
                        Lacak Permohonan Surat
                    </h3>
                    <p>Masukkan kode Tracking ID untuk memantau status permohonan Anda</p>
                </div>
                <div class="card-body-custom">
                    <!-- Search Form -->
                    <form method="get" action="<?= PUBLIC_URL ?>/lacak_permohonan.php" class="tracking-form">
                        <div class="form-group">
                            <input type="text" class="tracking-input" name="tracking_id" placeholder="Contoh: SASI-20260523-00001" value="<?= h($trackingId) ?>" required>
                            <button type="submit" class="btn-track">
                                <i class="bi bi-search"></i> Lacak
                            </button>
                        </div>
                    </form>

                    <!-- Error Alerts -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert-error">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <?php foreach ($errors as $error): ?>
                                    <p><?= h($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tracking Results -->
                    <?php if ($trackingId !== '' && $result): ?>
                        <div class="tracking-result">
                            <!-- Success Banner -->
                            <?php if ($result['status'] === 'approved'): ?>
                                <div class="success-banner">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div>
                                        <h5>Surat Anda Sudah Siap Diambil</h5>
                                        <p>Silakan berkunjung ke Kantor Kelurahan Sasi dengan membawa KTP asli untuk proses serah terima.</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Status Header -->
                            <h5 class="section-title">Status Berkas</h5>
                            
                            <!-- Info Cards -->
                            <div class="info-grid">
                                <div class="info-card">
                                    <span class="info-label">Tracking ID</span>
                                    <p class="info-value tracking-id"><?= h($result['tracking_id']) ?></p>
                                </div>
                                <div class="info-card">
                                    <span class="info-label">Tanggal Diajukan</span>
                                    <p class="info-value"><?= date('d M Y', strtotime($result['created_at'])) ?></p>
                                </div>
                            </div>

                            <!-- Pemohon Details -->
                            <div class="details-card">
                                <div class="details-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Pemohon</span>
                                        <span class="detail-value"><?= h($result['nama_lengkap']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">NIK</span>
                                        <span class="detail-value"><?= h($result['nik']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Jenis Surat</span>
                                        <span class="detail-value highlight"><?= h($result['jenis_surat']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">No. WhatsApp</span>
                                        <span class="detail-value"><?= h($result['no_hp'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="status-container">
                                <?php if ($result['status'] == 'pending'): ?>
                                    <div class="status-badge status-pending">
                                        <i class="bi bi-clock-fill"></i> Menunggu Verifikasi
                                    </div>
                                <?php elseif ($result['status'] == 'processing'): ?>
                                    <div class="status-badge status-processing">
                                        <i class="bi bi-hourglass-split"></i> Sedang Diproses
                                    </div>
                                <?php elseif ($result['status'] == 'approved'): ?>
                                    <div class="status-badge status-approved">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui
                                    </div>
                                <?php elseif ($result['status'] == 'rejected'): ?>
                                    <div class="status-badge status-rejected">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Catatan from Admin -->
                            <?php if (!empty($result['catatan'])): ?>
                                <div class="notes-card">
                                    <h6><i class="bi bi-chat-left-text"></i> Catatan Petugas</h6>
                                    <p><?= nl2br(h($result['catatan'])) ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Print Button -->
                            <?php if ($result['status'] == 'approved' && !empty($result['nomor_surat'])): ?>
                                <a href="<?= BASE_URL ?>/generate_pdf.php?id=<?= $result['id'] ?>" target="_blank" class="btn-print">
                                    <i class="bi bi-printer-fill"></i> Cetak Surat PDF
                                </a>
                            <?php endif; ?>

                            <!-- Timeline Divider -->
                            <div class="divider"></div>

                            <!-- History Timeline -->
                            <h5 class="section-title">Perjalanan Status Dokumen</h5>
                            
                            <?php if (empty($history)): ?>
                                <div class="empty-history">
                                    <i class="bi bi-hourglass-split" style="font-size: 2rem; opacity: 0.4; display: block; margin-bottom: 0.5rem;"></i>
                                    <p>Belum ada catatan perubahan riwayat untuk berkas ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="timeline">
                                    <?php foreach ($history as $index => $item): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker">
                                                <div class="marker-dot"></div>
                                                <?php if ($index < count($history) - 1): ?>
                                                    <div class="marker-line"></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-header">
                                                    <div class="timeline-status">
                                                        <?= h($item['status_lama']) ?> → <?= h($item['status_baru']) ?>
                                                    </div>
                                                    <div class="timeline-date">
                                                        <i class="bi bi-calendar-event"></i> <?= date('d M Y H:i', strtotime($item['created_at'])) ?>
                                                    </div>
                                                </div>
                                                <?php if (!empty($item['keterangan'])): ?>
                                                    <p class="timeline-desc"><?= h($item['keterangan']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Tips Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h5><i class="bi bi-lightbulb"></i> Tips Melacak</h5>
                </div>
                <div class="sidebar-card-body">
                    <ul class="tips-list">
                        <li>Pastikan format penulisan Tracking ID sudah tepat beserta tanda strip (-)</li>
                        <li>Gunakan fitur salin-tempel dari pesan konfirmasi pendaftaran Anda</li>
                        <li>Proses verifikasi berkas diselesaikan dalam 1-3 hari kerja</li>
                        <li>Simpan Tracking ID untuk memantau perkembangan permohonan</li>
                    </ul>
                </div>
            </div>

            <!-- Bantuan Card -->
            <div class="sidebar-card">
                <div class="sidebar-card-header">
                    <h5><i class="bi bi-headset"></i> Butuh Bantuan?</h5>
                </div>
                <div class="sidebar-card-body">
                    <p class="help-text">Jika Anda menemukan kendala atau memiliki pertanyaan, silakan hubungi kami:</p>
                    <div class="contact-info">
                        <div class="contact-row">
                            <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div class="contact-value">(0380) 123456</div>
                        </div>
                        <div class="contact-row">
                            <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                            <div class="contact-value">kelurahan@sasi.go.id</div>
                        </div>
                        <div class="contact-row">
                            <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                            <div class="contact-value">Senin-Jumat: 08.00 - 14.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>