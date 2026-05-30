<?php
$page_title = 'Kelola Permohonan';
$activePage = 'kelola_permohonan';
$is_admin_page = true;

require_once __DIR__ . '/../config/db.php';
require_admin();

$filter = $_GET['status'] ?? 'all';

try {
    $query = '
        SELECT ps.*, js.nama AS jenis_surat, p.no_hp 
        FROM pengajuan_surat ps 
        JOIN jenis_surat js ON ps.jenis_surat_id = js.id 
        LEFT JOIN penduduk p ON ps.nik = p.nik
    ';
    
    if ($filter !== 'all') {
        $query .= ' WHERE ps.status = :status';
        $stmt = $pdo->prepare($query . ' ORDER BY ps.created_at DESC');
        $stmt->execute([':status' => $filter]);
    } else {
        $stmt = $pdo->prepare($query . ' ORDER BY ps.created_at DESC');
        $stmt->execute();
    }
    $rows = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching permohonan: ' . $e->getMessage());
    $rows = [];
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Ghibli Theme - Kelola Permohonan -->
<!-- Background Layers -->
<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>
<div class="page-bg-atmosphere"></div>

<style>
/* ============================================
   GHIBLI ADMIN THEME - KELOLA PERMOHONAN
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
    --ghibli-sky-dark: #9DC8F0;
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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
    min-height: 100vh;
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
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.3"><rect x="0" y="0" width="20" height="100" fill="%239CAF88"/><rect x="25" y="0" width="20" height="100" fill="%239CAF88"/><rect x="50" y="0" width="20" height="100" fill="%239CAF88"/><rect x="75" y="0" width="20" height="100" fill="%239CAF88"/></svg>');
    background-repeat: repeat;
    background-size: 40px 100%;
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
    background: radial-gradient(circle at 20% 40%, rgba(156, 175, 136, 0.05) 0%, transparent 60%);
    z-index: 0;
    pointer-events: none;
}

/* Main Container */
.page-container {
    position: relative;
    z-index: 2;
    padding: 1.5rem 2rem 2rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
    background: rgba(253, 251, 247, 0.85);
    backdrop-filter: blur(10px);
    padding: 1.25rem 1.75rem;
    border-radius: 28px;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.page-title-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-icon {
    width: 50px;
    height: 50px;
    background: rgba(156, 175, 136, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-icon i {
    font-size: 1.5rem;
    color: var(--ghibli-sage);
}

.page-title {
    font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.page-subtitle {
    color: var(--ghibli-text-soft);
    font-size: 0.85rem;
    margin-bottom: 0;
    font-weight: 500;
}

/* PERBAIKAN FILTER FORM - UKURAN TIDAK TERLALU LEBAR */
.filter-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    background-color: #FFFFFF;
    border: 1.5px solid var(--ghibli-border);
    border-radius: 40px;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    color: var(--ghibli-text-dark);
    font-weight: 500;
    cursor: pointer;
    width: 160px;
    transition: all 0.2s ease;
}

.filter-select:focus {
    border-color: var(--ghibli-sage);
    outline: none;
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
}

.filter-btn, .filter-reset {
    border-radius: 40px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 600;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.filter-btn {
    background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
    border: none;
    color: #FFFFFF;
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.filter-reset {
    background-color: transparent;
    border: 1.5px solid var(--ghibli-border);
    color: var(--ghibli-text-dark);
}

.filter-reset:hover {
    border-color: var(--ghibli-sage);
    background: rgba(156, 175, 136, 0.1);
    color: var(--ghibli-sage-dark);
}

/* Stats Cards */
.stats-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.stat-card {
    flex: 1;
    min-width: 140px;
    background: rgba(253, 251, 247, 0.9);
    backdrop-filter: blur(8px);
    border-radius: 24px;
    padding: 1rem 1.25rem;
    border: 1px solid rgba(156, 175, 136, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--ghibli-sage);
}

.stat-card:hover {
    transform: translateY(-3px);
    border-color: var(--ghibli-sage-light);
    box-shadow: 0 12px 28px var(--ghibli-shadow-hover);
}

.stat-card-processing::before { background: var(--status-processing); }
.stat-card-approved::before { background: var(--status-approved); }
.stat-card-rejected::before { background: var(--status-rejected); }
.stat-card-total::before { background: var(--ghibli-sage); }

.stat-icon {
    width: 40px;
    height: 40px;
    background: rgba(156, 175, 136, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.stat-icon i {
    font-size: 1.2rem;
    color: var(--ghibli-sage);
}

.stat-number {
    font-family: 'Bricolage Grotesque', monospace;
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--ghibli-text-dark);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.65rem;
    color: var(--ghibli-text-soft);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 700;
}

/* PERBAIKAN UTAMA TABEL */
.ghibli-table-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border-radius: 28px;
    box-shadow: 0 8px 24px var(--ghibli-shadow);
    border: 1px solid rgba(156, 175, 136, 0.2);
    overflow: hidden;
    transition: all 0.3s ease;
}

.ghibli-table-card:hover {
    box-shadow: 0 12px 32px var(--ghibli-shadow-hover);
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.admin-table {
    width: 100%;
    min-width: 1000px;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: auto;
}

.admin-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background: rgba(253, 251, 247, 0.98);
}

.admin-table thead tr {
    background: rgba(156, 175, 136, 0.06);
}

.admin-table th {
    padding: 1rem 1.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #5C4A3A;
    border-bottom: 2px solid var(--ghibli-border);
    white-space: nowrap;
    text-align: left;
    vertical-align: middle;
}

.admin-table th.text-center {
    text-align: center;
}

.admin-table td {
    padding: 1rem 1.25rem;
    font-size: 0.85rem;
    color: var(--ghibli-text-soft);
    font-weight: 500;
    border-bottom: 1px solid var(--ghibli-border);
    vertical-align: middle;
    background-color: transparent;
}

.admin-table tbody tr {
    transition: background-color 0.2s ease;
}

.admin-table tbody tr:hover {
    background: rgba(156, 175, 136, 0.04);
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

/* Column width management */
.col-tracking {
    width: 16%;
    min-width: 150px;
}

.col-nama {
    width: 18%;
    min-width: 140px;
}

.col-jenis {
    width: 15%;
    min-width: 130px;
}

.col-hp {
    width: 12%;
    min-width: 110px;
}

.col-status {
    width: 12%;
    min-width: 105px;
}

.col-tanggal {
    width: 12%;
    min-width: 110px;
}

.col-aksi {
    width: 15%;
    min-width: 140px;
}

.table-actions {
    white-space: nowrap;
    text-align: center;
}

/* Tracking Code */
.tracking-code {
    font-family: 'Courier New', 'Fira Code', monospace;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    background: linear-gradient(135deg, rgba(156, 175, 136, 0.12), rgba(246, 199, 161, 0.06));
    padding: 0.3rem 0.7rem;
    border-radius: 10px;
    display: inline-block;
    letter-spacing: 0.4px;
    border: 1px solid rgba(156, 175, 136, 0.2);
    white-space: nowrap;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.9rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-pending {
    background-color: var(--status-pending);
    color: #4A3A2A;
}

.status-approved {
    background-color: var(--status-approved);
    color: #FFFFFF;
}

.status-rejected {
    background-color: var(--status-rejected);
    color: #6B4A4A;
}

.status-processing {
    background-color: var(--status-processing);
    color: #2C5F6E;
}

/* Action Buttons */
.btn-action {
    border-radius: 50% !important;
    width: 34px;
    height: 34px;
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    border: none;
    cursor: pointer;
    margin: 0 2px;
    text-decoration: none;
}

.btn-view {
    background-color: rgba(156, 175, 136, 0.15);
    color: #5C4A3A;
}

.btn-view:hover {
    background-color: var(--ghibli-sage);
    color: #FFFFFF;
    transform: scale(1.08);
}

.btn-approve {
    background-color: var(--status-approved);
    color: #FFFFFF;
}

.btn-approve:hover {
    background-color: var(--ghibli-sage-dark);
    transform: scale(1.08);
}

.btn-reject {
    background-color: var(--status-rejected);
    color: #8B5E5E;
}

.btn-reject:hover {
    background-color: #D4A5A5;
    color: #FFFFFF;
    transform: scale(1.08);
}

.btn-print {
    background-color: var(--ghibli-peach);
    color: #2C241E;
}

.btn-print:hover {
    background-color: var(--ghibli-peach-dark);
    transform: scale(1.08);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--ghibli-text-soft);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    opacity: 0.4;
    color: var(--ghibli-sage);
}

.empty-state p {
    font-size: 0.9rem;
    margin: 0;
    font-weight: 500;
}

/* Modal Styles */
.ghibli-modal-content {
    border-radius: 28px !important;
    border: 1px solid var(--ghibli-border) !important;
    overflow: hidden;
    background: var(--ghibli-cream) !important;
}

.ghibli-modal-header {
    border-bottom: 1px solid var(--ghibli-border);
    padding: 1.25rem 1.75rem;
    background: rgba(156, 175, 136, 0.05);
}

.ghibli-modal-footer {
    border-top: 1px solid var(--ghibli-border);
    padding: 1rem 1.75rem;
    background: rgba(156, 175, 136, 0.03);
}

.modal-divider {
    height: 1px;
    background: repeating-linear-gradient(90deg, var(--ghibli-border), var(--ghibli-border) 8px, transparent 8px, transparent 16px);
    margin: 1.25rem 0;
}

.form-label-custom {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modal-info-text {
    background: rgba(156, 175, 136, 0.08);
    padding: 0.75rem 1rem;
    border-radius: 16px;
    font-size: 0.85rem;
    color: var(--ghibli-text-dark);
}

.btn-modal-secondary {
    background: transparent;
    border: 1.5px solid var(--ghibli-border);
    border-radius: 40px;
    padding: 0.5rem 1.5rem;
    color: var(--ghibli-text-dark);
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-modal-secondary:hover {
    border-color: var(--ghibli-sage);
    background: rgba(156, 175, 136, 0.1);
    color: var(--ghibli-sage-dark);
}

.btn-modal-primary {
    background: linear-gradient(135deg, var(--ghibli-sage), var(--ghibli-sage-dark));
    border: none;
    border-radius: 40px;
    padding: 0.5rem 1.75rem;
    color: #FFFFFF;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-modal-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.btn-modal-danger {
    background-color: #C58B8B;
    border: none;
    border-radius: 40px;
    padding: 0.5rem 1.75rem;
    color: #FFFFFF;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-modal-danger:hover {
    background-color: #B57777;
    transform: translateY(-1px);
}

.fw-semibold {
    font-weight: 600;
}

.text-center {
    text-align: center;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .admin-table {
        min-width: 900px;
    }
}

@media (max-width: 992px) {
    .page-container {
        padding: 1rem;
    }
    .admin-table {
        min-width: 800px;
    }
}

@media (max-width: 768px) {
    .page-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .page-subtitle {
        font-size: 0.75rem;
    }
    
    .stats-row {
        flex-direction: column;
    }
    
    .stat-card {
        min-width: auto;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
    }
    
    .tracking-code {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
    }
    
    .status-badge {
        padding: 0.2rem 0.7rem;
        font-size: 0.65rem;
        gap: 0.25rem;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
    }
    
    .btn-action i {
        font-size: 0.75rem;
    }
    
    .filter-select {
        width: 100%;
        min-width: auto;
    }
    
    .filter-form {
        width: 100%;
    }
    
    .filter-btn, .filter-reset {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .page-container {
        padding: 0.75rem;
    }
    
    .page-header {
        padding: 1rem;
    }
    
    .page-title-wrapper {
        gap: 0.75rem;
    }
    
    .page-icon {
        width: 40px;
        height: 40px;
    }
    
    .page-icon i {
        font-size: 1.2rem;
    }
    
    .page-title {
        font-size: 1.1rem;
    }
    
    .page-subtitle {
        font-size: 0.7rem;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .filter-select {
        width: 100%;
    }
    
    .filter-btn, .filter-reset {
        width: 100%;
        justify-content: center;
    }
    
    .stat-card {
        padding: 0.875rem 1rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-icon {
        width: 35px;
        height: 35px;
    }
    
    .stat-icon i {
        font-size: 1rem;
    }
    
    .stat-label {
        font-size: 0.6rem;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 0.6rem 0.75rem;
        font-size: 0.7rem;
    }
    
    .admin-table {
        min-width: 700px;
    }
    
    .tracking-code {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
    }
    
    .status-badge {
        padding: 0.15rem 0.5rem;
        font-size: 0.6rem;
        gap: 0.2rem;
    }
    
    .status-badge i {
        font-size: 0.55rem;
    }
    
    .btn-action {
        width: 28px;
        height: 28px;
    }
    
    .btn-action i {
        font-size: 0.7rem;
    }
    
    .empty-state {
        padding: 1.5rem;
    }
    
    .empty-state i {
        font-size: 2rem;
    }
    
    .empty-state p {
        font-size: 0.8rem;
    }
}

@media (max-width: 380px) {
    .admin-table th,
    .admin-table td {
        padding: 0.5rem 0.6rem;
        font-size: 0.65rem;
    }
    
    .admin-table {
        min-width: 600px;
    }
    
    .tracking-code {
        font-size: 0.55rem;
        padding: 0.1rem 0.35rem;
    }
    
    .status-badge {
        font-size: 0.55rem;
        padding: 0.1rem 0.4rem;
    }
    
    .btn-action {
        width: 26px;
        height: 26px;
    }
    
    .btn-action i {
        font-size: 0.65rem;
    }
}
</style>

<?php
// Hitung statistik
$total_pending = count(array_filter($rows, fn($r) => $r['status'] === 'pending'));
$total_processing = count(array_filter($rows, fn($r) => $r['status'] === 'processing'));
$total_approved = count(array_filter($rows, fn($r) => $r['status'] === 'approved'));
$total_rejected = count(array_filter($rows, fn($r) => $r['status'] === 'rejected'));
?>

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="page-title-wrapper">
            <div class="page-icon">
                <i class="bi bi-files-alt"></i>
            </div>
            <div>
                <h1 class="page-title">Kelola Permohonan Surat</h1>
                <p class="page-subtitle">Verifikasi, setujui, atau tolak permohonan dokumen administrasi warga</p>
            </div>
        </div>
        <form method="get" class="filter-form">
            <select name="status" class="filter-select">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                <option value="processing" <?= $filter === 'processing' ? 'selected' : '' ?>>Diproses</option>
                <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
            </select>
            <button type="submit" class="filter-btn">
                <i class="bi bi-funnel-fill"></i> Filter
            </button>
            <?php if ($filter !== 'all'): ?>
                <a href="?status=all" class="filter-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-row">
        <div class="stat-card stat-card-processing">
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-number"><?= $total_pending + $total_processing ?></div>
            <div class="stat-label">Dalam Proses</div>
        </div>
        <div class="stat-card stat-card-approved">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-number"><?= $total_approved ?></div>
            <div class="stat-label">Disetujui</div>
        </div>
        <div class="stat-card stat-card-rejected">
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-number"><?= $total_rejected ?></div>
            <div class="stat-label">Ditolak</div>
        </div>
        <div class="stat-card stat-card-total">
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-number"><?= count($rows) ?></div>
            <div class="stat-label">Total Permohonan</div>
        </div>
    </div>

    <!-- PERBAIKAN STRUKTUR TABEL UTAMA -->
    <div class="ghibli-table-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-tracking">Tracking ID</th>
                        <th class="col-nama">Nama Pemohon</th>
                        <th class="col-jenis">Jenis Surat</th>
                        <th class="col-hp">No. HP</th>
                        <th class="col-status text-center">Status</th>
                        <th class="col-tanggal">Tanggal</th>
                        <th class="col-aksi text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td data-label="Tracking ID">
                                <code class="tracking-code"><?= htmlspecialchars($r['tracking_id']) ?></code>
                            </td>
                            <td data-label="Nama Pemohon" class="fw-semibold" style="color: var(--ghibli-text-dark);">
                                <?= htmlspecialchars($r['nama_lengkap']) ?>
                            </td>
                            <td data-label="Jenis Surat" style="color: var(--ghibli-text-dark);">
                                <?= htmlspecialchars($r['jenis_surat']) ?>
                            </td>
                            <td data-label="No. HP" style="font-family: monospace; font-weight: 500;">
                                <?= htmlspecialchars($r['no_hp'] ?? '-') ?>
                            </td>
                            <td data-label="Status" class="text-center">
                                <?php if ($r['status'] === 'approved'): ?>
                                    <span class="status-badge status-approved">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui
                                    </span>
                                <?php elseif ($r['status'] === 'rejected'): ?>
                                    <span class="status-badge status-rejected">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                <?php elseif ($r['status'] === 'processing'): ?>
                                    <span class="status-badge status-processing">
                                        <i class="bi bi-hourglass-split"></i> Diproses
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">
                                        <i class="bi bi-clock-fill"></i> Menunggu
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Tanggal" style="font-weight: 500;">
                                <?= date('d M Y', strtotime($r['created_at'])) ?>
                            </td>
                            <td data-label="Aksi" class="table-actions">
                                <!-- Detail Button -->
                                <button class="btn-action btn-view" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                    data-id="<?= $r['id'] ?>"
                                    data-tracking="<?= htmlspecialchars($r['tracking_id']) ?>"
                                    data-nama="<?= htmlspecialchars($r['nama_lengkap']) ?>"
                                    data-nik="<?= htmlspecialchars($r['nik']) ?>"
                                    data-alamat="<?= htmlspecialchars($r['alamat']) ?>"
                                    data-jenis="<?= htmlspecialchars($r['jenis_surat']) ?>"
                                    data-keperluan="<?= htmlspecialchars($r['keperluan']) ?>"
                                    data-status="<?= $r['status'] ?>"
                                    data-catatan="<?= htmlspecialchars($r['catatan'] ?? '-') ?>"
                                    data-file-ktp="<?= htmlspecialchars($r['file_ktp']) ?>"
                                    title="Lihat Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                <!-- Print PDF Button -->
                                <?php if ($r['status'] === 'approved' && !empty($r['nomor_surat'])): ?>
                                    <a href="<?= BASE_URL ?>/generate_pdf.php?id=<?= $r['id'] ?>" target="_blank" class="btn-action btn-print" title="Cetak Surat PDF">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- Approve Button -->
                                <?php if ($r['status'] === 'pending'): ?>
                                    <button class="btn-action btn-approve" data-bs-toggle="modal" data-bs-target="#approveModal" data-id="<?= $r['id'] ?>" title="Setujui Berkas">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php endif; ?>

                                <!-- Reject Button -->
                                <?php if ($r['status'] === 'pending'): ?>
                                    <button class="btn-action btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?= $r['id'] ?>" title="Tolak Berkas">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada permohonan surat masuk dalam antrean</p>
                                <p style="font-size: 0.7rem; margin-top: 0.5rem;">Tenang saja, seperti suasana pedesaan yang damai...</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Permohonan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content ghibli-modal-content">
            <div class="modal-header ghibli-modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--ghibli-text-dark);">
                    <i class="bi bi-file-text-fill me-2" style="color: var(--ghibli-sage);"></i>Detail Permohonan
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-upc-scan me-1"></i> Tracking ID
                            </div>
                            <div class="tracking-code" id="detail-tracking" style="font-size: 0.9rem; display: inline-block;"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-person me-1"></i> Nama Pemohon
                            </div>
                            <div class="fw-semibold" id="detail-nama" style="color: var(--ghibli-text-dark); font-size: 1rem;"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-qr-code me-1"></i> NIK Pemohon
                            </div>
                            <div id="detail-nik" style="font-family: monospace; color: var(--ghibli-text-soft);"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-house me-1"></i> Alamat Pemohon
                            </div>
                            <div id="detail-alamat" style="color: var(--ghibli-text-soft);"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-envelope me-1"></i> Jenis Layanan Surat
                            </div>
                            <div class="fw-semibold" id="detail-jenis" style="color: var(--ghibli-sage-dark);"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-pencil me-1"></i> Keperluan Berkas
                            </div>
                            <div id="detail-keperluan" style="color: var(--ghibli-text-soft); background: rgba(156, 175, 136, 0.05); padding: 0.5rem; border-radius: 12px;"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-info-circle me-1"></i> Status Pengajuan
                            </div>
                            <div id="detail-status"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label-custom">
                                <i class="bi bi-chat me-1"></i> Catatan Tambahan
                            </div>
                            <div id="detail-catatan" style="color: var(--ghibli-text-soft);"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-divider"></div>
                <div>
                    <div class="form-label-custom">
                        <i class="bi bi-image me-1"></i> Unggahan Dokumen KTP
                    </div>
                    <div id="detail-file"></div>
                </div>
            </div>
            <div class="modal-footer ghibli-modal-footer">
                <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setujui -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= PROSES_URL ?>/permohonan_process.php">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="id" id="approve-id">
            <div class="modal-content ghibli-modal-content">
                <div class="modal-header ghibli-modal-header" style="background: rgba(156, 175, 136, 0.1);">
                    <h5 class="modal-title fw-bold" style="color: var(--ghibli-sage-dark);">
                        <i class="bi bi-check-circle-fill me-2"></i>Setujui Berkas Permohonan
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="modal-info-text mb-3">
                        <i class="bi bi-question-circle me-2"></i>
                        Apakah Anda yakin seluruh berkas pendukung sudah sesuai untuk disetujui?
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom">
                            <i class="bi bi-pencil-square me-1"></i> Tambahkan Catatan atau Nomor Surat (opsional)
                        </label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Nomor Surat: 140/02/VIII/2026. Silakan diambil di kantor..." style="background-color: #FFFFFF; border: 1.5px solid var(--ghibli-border); border-radius: 16px; padding: 0.7rem; width: 100%; color: var(--ghibli-text-dark);"></textarea>
                    </div>
                </div>
                <div class="modal-footer ghibli-modal-footer">
                    <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn-modal-primary">
                        <i class="bi bi-check-lg me-1"></i> Ya, Setujui Berkas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= PROSES_URL ?>/permohonan_process.php">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id" id="reject-id">
            <div class="modal-content ghibli-modal-content">
                <div class="modal-header ghibli-modal-header" style="background: rgba(212, 165, 165, 0.1);">
                    <h5 class="modal-title fw-bold" style="color: #6B4A4A;">
                        <i class="bi bi-x-circle-fill me-2"></i>Tolak Berkas Permohonan
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="modal-info-text mb-3" style="background: rgba(197, 139, 139, 0.08);">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Apakah Anda yakin menolak pengajuan ini? Tindakan ini tidak dapat dibatalkan.
                    </div>
                    <div class="mb-2">
                        <label class="form-label-custom">
                            <i class="bi bi-chat-left-text me-1"></i> Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan" class="form-control" rows="3" required placeholder="Contoh: Lampiran Kartu Keluarga tidak terbaca / buram. Silakan unggah kembali." style="background-color: #FFFFFF; border: 1.5px solid var(--ghibli-border); border-radius: 16px; padding: 0.7rem; width: 100%; color: var(--ghibli-text-dark);"></textarea>
                    </div>
                </div>
                <div class="modal-footer ghibli-modal-footer">
                    <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn-modal-danger">
                        <i class="bi bi-x-lg me-1"></i> Ya, Tolak Berkas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Modal Detail Handler
document.querySelectorAll('[data-bs-target="#detailModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('detail-tracking').innerText = this.dataset.tracking;
        document.getElementById('detail-nama').innerText = this.dataset.nama;
        document.getElementById('detail-nik').innerText = this.dataset.nik;
        document.getElementById('detail-alamat').innerText = this.dataset.alamat || '-';
        document.getElementById('detail-jenis').innerText = this.dataset.jenis;
        document.getElementById('detail-keperluan').innerText = this.dataset.keperluan || '-';
        document.getElementById('detail-catatan').innerText = this.dataset.catatan;
        
        let status = this.dataset.status;
        let statusHtml = '';
        if (status === 'approved') {
            statusHtml = '<span class="status-badge status-approved"><i class="bi bi-check-circle-fill"></i> Disetujui</span>';
        } else if (status === 'rejected') {
            statusHtml = '<span class="status-badge status-rejected"><i class="bi bi-x-circle-fill"></i> Ditolak</span>';
        } else if (status === 'processing') {
            statusHtml = '<span class="status-badge status-processing"><i class="bi bi-hourglass-split"></i> Diproses</span>';
        } else {
            statusHtml = '<span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Menunggu Verifikasi</span>';
        }
        document.getElementById('detail-status').innerHTML = statusHtml;
        
        let fileUrl = this.dataset.fileKtp;
        if (fileUrl && fileUrl !== '') {
            const uploadUrl = '<?= ASSETS_URL ?>/uploads/' + encodeURIComponent(fileUrl);
            document.getElementById('detail-file').innerHTML = `<a href="${uploadUrl}" target="_blank" class="btn-modal-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; text-decoration: none;"><i class="bi bi-file-earmark-image"></i> Lihat Berkas KTP</a>`;
        } else {
            document.getElementById('detail-file').innerHTML = '<span style="color: var(--ghibli-text-soft); opacity: 0.6;"><i class="bi bi-folder2-open"></i> Tidak ada lampiran file</span>';
        }
    });
});

// Modal Approve Handler
document.querySelectorAll('[data-bs-target="#approveModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('approve-id').value = this.dataset.id;
    });
});

// Modal Reject Handler
document.querySelectorAll('[data-bs-target="#rejectModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('reject-id').value = this.dataset.id;
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>