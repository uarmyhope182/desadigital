<?php
$page_title = 'Data Penduduk';
$activePage = 'penduduk';
$is_admin_page = true;

require_once __DIR__ . '/../config/db.php';
require_admin();

$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);
$search = $_GET['search'] ?? '';

$flash = get_flash('success');
$errors = get_flash('errors') ?? [];

try {
    if (!empty($search)) {
        $stmt = $pdo->prepare('SELECT id, nik, full_name, tempat_lahir, tanggal_lahir, gender, pekerjaan, address, rt, rw, dusun, no_hp, created_at FROM penduduk WHERE nik LIKE :search OR full_name LIKE :search ORDER BY id DESC');
        $stmt->execute([':search' => "%$search%"]);
    } else {
        $stmt = $pdo->prepare('SELECT id, nik, full_name, tempat_lahir, tanggal_lahir, gender, pekerjaan, address, rt, rw, dusun, no_hp, created_at FROM penduduk ORDER BY id DESC');
        $stmt->execute();
    }
    $rows = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching penduduk: ' . $e->getMessage());
    $rows = [];
}

$editing = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT id, nik, full_name, tempat_lahir, tanggal_lahir, gender, pekerjaan, address, rt, rw, dusun, no_hp FROM penduduk WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $editing = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Bricolage+Grotesque:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:root {
    --page-bg-cream: #FDFBF7;
    --page-bg-beige: #FAF6F0;
    --page-sage: #9CAF88;
    --page-sage-dark: #7A8F64;
    --page-sage-light: #B5C4A3;
    --page-sky: #B9DCFF;
    --page-text-dark: #1A1512;
    --page-text-soft: #2C241E;
    --page-border: #E8E0D5;
    --page-shadow: rgba(58, 49, 43, 0.08);
}

body {
    background: linear-gradient(135deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
    min-height: 100vh;
}

.page-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, var(--page-sky) 0%, #D4E8FF 50%, var(--page-bg-beige) 100%);
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
    opacity: 0.08;
    z-index: 0;
    pointer-events: none;
}

.main-container {
    position: relative;
    z-index: 2;
    padding: 1.5rem 2rem 2rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
    background: rgba(253, 251, 247, 0.8);
    backdrop-filter: blur(10px);
    padding: 1.25rem 1.75rem;
    border-radius: 28px;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.page-title {
    font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.page-subtitle {
    color: #2C241E;
    font-size: 0.85rem;
    margin-bottom: 0;
    font-weight: 500;
}

/* PERBAIKAN SEARCH SECTION - UKURAN PROPORSIONAL */
.search-section {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 60px;
    padding: 0.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.search-section form {
    display: flex;
    gap: 0.5rem;
    width: 100%;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 200px;
    border: none;
    padding: 0.7rem 1.2rem;
    border-radius: 50px;
    background: transparent;
    font-size: 0.85rem;
    color: #1A1512;
    border: 1.5px solid transparent;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    border-color: var(--page-sage);
    background: rgba(156, 175, 136, 0.05);
}

.search-input::placeholder {
    color: #5C4A3A;
    opacity: 0.6;
}

.search-btn {
    background: linear-gradient(135deg, var(--page-sage) 0%, var(--page-sage-dark) 100%);
    border: none;
    border-radius: 50px;
    padding: 0.7rem 1.5rem;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.btn-reset {
    background: transparent;
    border: 1.5px solid var(--page-border);
    border-radius: 50px;
    padding: 0.7rem 1.5rem;
    color: var(--page-text-dark);
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-reset:hover {
    border-color: var(--page-sage);
    background: rgba(156, 175, 136, 0.1);
    color: var(--page-sage-dark);
}

.btn-primary-custom {
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    color: white;
    border: none;
    border-radius: 40px;
    padding: 0.6rem 1.3rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
    color: white;
}

.btn-outline-custom {
    background: transparent;
    border: 1.5px solid var(--page-border);
    border-radius: 40px;
    padding: 0.6rem 1.3rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #1A1512;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-outline-custom:hover {
    border-color: var(--page-sage);
    background: rgba(156, 175, 136, 0.1);
    color: var(--page-sage-dark);
}

/* PERBAIKAN UTAMA TABEL */
.table-container {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: 1px solid rgba(156, 175, 136, 0.2);
    position: relative;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.admin-table {
    width: 100%;
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

.admin-table thead th {
    background: rgba(156, 175, 136, 0.08);
    padding: 0.85rem 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #5C4A3A;
    border-bottom: 2px solid var(--page-border);
    white-space: nowrap;
    text-align: left;
    vertical-align: middle;
}

.admin-table thead th.text-center {
    text-align: center;
}

.admin-table tbody tr {
    transition: background-color 0.2s ease;
}

.admin-table tbody tr:hover {
    background: rgba(156, 175, 136, 0.05);
}

.admin-table tbody td {
    padding: 0.85rem 1rem;
    font-size: 0.8rem;
    color: #2C241E;
    font-weight: 500;
    border-bottom: 1px solid var(--page-border);
    vertical-align: middle;
    background-color: transparent;
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

/* PERBAIKAN LEBAR KOLOM - LEBIH PROPORSIONAL */
.col-nik { width: 14%; min-width: 140px; }
.col-nama { width: 18%; min-width: 160px; }
.col-gender { width: 10%; min-width: 100px; }
.col-tempat-lahir { width: 10%; min-width: 110px; }
.col-tgl-lahir { width: 10%; min-width: 100px; }
.col-pekerjaan { width: 10%; min-width: 100px; }
.col-alamat { width: 14%; min-width: 160px; }
.col-rt-rw { width: 6%; min-width: 70px; }
.col-no-hp { width: 8%; min-width: 100px; }
.col-aksi { width: 8%; min-width: 80px; text-align: center; }

/* Styling untuk konten tabel */
.nik-badge {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: 0.7rem;
    font-weight: 700;
    color: #1A1512;
    background: rgba(156, 175, 136, 0.15);
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}

.gender-male {
    background: rgba(156, 175, 136, 0.15);
    color: #1A1512;
}

.gender-female {
    background: rgba(246, 199, 161, 0.2);
    color: #C58B8B;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    margin: 0 2px;
}

.btn-edit-table {
    background: rgba(156, 175, 136, 0.2);
    color: #5C4A3A;
}

.btn-edit-table:hover {
    background: var(--page-sage);
    color: white;
    transform: scale(1.05);
}

.btn-delete-table {
    background: rgba(212, 165, 165, 0.2);
    color: #C58B8B;
}

.btn-delete-table:hover {
    background: #C58B8B;
    color: white;
    transform: scale(1.05);
}

.table-actions {
    white-space: nowrap;
    text-align: center;
}

.alert-custom {
    background: white;
    border-radius: 20px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
}

.alert-success {
    border-left-color: var(--page-sage);
    background: #F0F7ED;
}

.alert-error {
    border-left-color: #E8C5C5;
    background: #FFF5F5;
}

.empty-state {
    text-align: center;
    padding: 2.5rem;
    color: #2C241E;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    display: block;
    opacity: 0.4;
}

.empty-state p {
    font-size: 0.85rem;
    margin: 0;
    font-weight: 500;
}

.stats-footer {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 60px;
    display: inline-flex;
    justify-content: center;
    gap: 2rem;
    font-size: 0.75rem;
    color: #2C241E;
    font-weight: 500;
}

/* MODAL FLOATING FORM */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-container {
    background: #FDFBF7;
    border-radius: 32px;
    width: 90%;
    max-width: 550px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 30px 50px rgba(0, 0, 0, 0.3);
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.modal-overlay.active .modal-container {
    transform: scale(1);
}

.modal-header {
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    padding: 1.25rem 1.5rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
    border-radius: 32px 32px 0 0;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.modal-body {
    padding: 1.75rem;
}

.form-group {
    margin-bottom: 1.2rem;
}

.form-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.4rem;
    display: block;
}

.form-control, .form-select {
    background: white;
    border: 1.5px solid var(--page-border);
    border-radius: 14px;
    padding: 0.7rem 1rem;
    font-size: 0.85rem;
    color: #1A1512;
    width: 100%;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: var(--page-sage);
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
    outline: none;
}

.form-hint {
    font-size: 0.65rem;
    color: #2C241E;
    opacity: 0.7;
    margin-top: 0.25rem;
    font-weight: 500;
}

.modal-footer {
    padding: 1.25rem 1.75rem;
    border-top: 1px solid var(--page-border);
    background: #FDFBF7;
    border-radius: 0 0 32px 32px;
}

.btn-modal-save {
    width: 100%;
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    color: white;
    border: none;
    border-radius: 40px;
    padding: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-modal-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.d-flex {
    display: flex;
}

.gap-2 {
    gap: 0.5rem;
}

.flex-wrap {
    flex-wrap: wrap;
}

.text-center {
    text-align: center;
}

.d-inline {
    display: inline;
}

.mb-0 {
    margin-bottom: 0;
}

.mt-2 {
    margin-top: 0.5rem;
}

.me-1 {
    margin-right: 0.25rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .admin-table {
        min-width: 1000px;
    }
}

@media (max-width: 992px) {
    .admin-table {
        min-width: 900px;
    }
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .page-subtitle {
        font-size: 0.75rem;
    }
    
    .search-section form {
        flex-direction: column;
    }
    
    .search-input {
        width: 100%;
        min-width: auto;
    }
    
    .search-btn, .btn-reset {
        width: 100%;
        justify-content: center;
    }
    
    .modal-container {
        width: 95%;
    }
    
    .modal-body {
        padding: 1.25rem;
    }
    
    .stats-footer {
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
    }
    
    .admin-table {
        min-width: 800px;
    }
}

@media (max-width: 576px) {
    .main-container {
        padding: 0.75rem;
    }
    
    .page-header {
        padding: 0.875rem;
    }
    
    .page-title {
        font-size: 1.1rem;
    }
    
    .btn-primary-custom, .btn-outline-custom {
        width: 100%;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .admin-table thead th,
    .admin-table tbody td {
        padding: 0.6rem 0.75rem;
        font-size: 0.7rem;
    }
    
    .admin-table {
        min-width: 700px;
    }
    
    .nik-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
    }
    
    .gender-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.5rem;
    }
    
    .btn-icon {
        width: 28px;
        height: 28px;
    }
    
    .btn-icon i {
        font-size: 0.75rem;
    }
    
    .stat-card {
        padding: 0.875rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
}

@media (max-width: 380px) {
    .admin-table thead th,
    .admin-table tbody td {
        padding: 0.5rem 0.6rem;
        font-size: 0.65rem;
    }
    
    .admin-table {
        min-width: 600px;
    }
    
    .nik-badge {
        font-size: 0.55rem;
        padding: 0.1rem 0.35rem;
    }
    
    .gender-badge {
        font-size: 0.55rem;
        padding: 0.1rem 0.4rem;
    }
    
    .btn-icon {
        width: 26px;
        height: 26px;
    }
    
    .btn-icon i {
        font-size: 0.65rem;
    }
}
</style>

<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>

<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Penduduk</h1>
            <p class="page-subtitle">Kelola data demografi sipil masyarakat Kelurahan Sasi</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button onclick="openAddModal()" class="btn-primary-custom">
                <i class="bi bi-plus-lg"></i> Tambah Penduduk
            </button>
            <a href="?action=import" class="btn-outline-custom">
                <i class="bi bi-file-earmark-excel-fill"></i> Impor CSV
            </a>
        </div>
    </div>

    <!-- PERBAIKAN SEARCH SECTION -->
    <div class="search-section">
        <form method="get">
            <input type="text" name="search" class="search-input" placeholder="Cari berdasarkan NIK atau Nama lengkap..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn">
                <i class="bi bi-search"></i> Cari
            </button>
            <?php if ($search): ?>
                <a href="<?= ADMIN_URL ?>/penduduk.php" class="btn-reset">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Alert -->
    <?php if ($flash): ?>
        <div class="alert-custom alert-success">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-custom alert-error">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2" style="margin-left: 1.5rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- PERBAIKAN TABEL DATA PENDUDUK - DENGAN CLASS KOLOM -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-nik">NIK</th>
                        <th class="col-nama">Nama Lengkap</th>
                        <th class="col-gender">Jenis Kelamin</th>
                        <th class="col-tempat-lahir">Tempat Lahir</th>
                        <th class="col-tgl-lahir">Tanggal Lahir</th>
                        <th class="col-pekerjaan">Pekerjaan</th>
                        <th class="col-alamat">Alamat</th>
                        <th class="col-rt-rw">RT/RW</th>
                        <th class="col-no-hp">No. HP</th>
                        <th class="col-aksi text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td data-label="NIK">
                                <span class="nik-badge"><?= htmlspecialchars($r['nik']) ?></span>
                            </td>
                            <td data-label="Nama Lengkap">
                                <strong style="color: #1A1512;"><?= htmlspecialchars($r['full_name']) ?></strong>
                            </td>
                            <td data-label="Jenis Kelamin">
                                <?php if ($r['gender'] == 'Laki-laki'): ?>
                                    <span class="gender-badge gender-male">
                                        <i class="bi bi-gender-male"></i> Laki-laki
                                    </span>
                                <?php elseif ($r['gender'] == 'Perempuan'): ?>
                                    <span class="gender-badge gender-female">
                                        <i class="bi bi-gender-female"></i> Perempuan
                                    </span>
                                <?php else: ?>
                                    <span style="color: #A0A0A0; font-size: 0.7rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Tempat Lahir">
                                <?= htmlspecialchars($r['tempat_lahir'] ?? '-') ?>
                            </td>
                            <td data-label="Tanggal Lahir">
                                <?= $r['tanggal_lahir'] ? date('d/m/Y', strtotime($r['tanggal_lahir'])) : '-' ?>
                            </td>
                            <td data-label="Pekerjaan">
                                <?= htmlspecialchars($r['pekerjaan'] ?? '-') ?>
                            </td>
                            <td data-label="Alamat">
                                <?= htmlspecialchars(substr($r['address'], 0, 35)) ?>
                                <?= strlen($r['address'] ?? '') > 35 ? '...' : '' ?>
                            </td>
                            <td data-label="RT/RW">
                                <strong style="color: #1A1512;"><?= htmlspecialchars($r['rt']) ?>/<?= htmlspecialchars($r['rw']) ?></strong>
                            </td>
                            <td data-label="No. HP">
                                <?= htmlspecialchars($r['no_hp'] ?? '-') ?>
                            </td>
                            <td data-label="Aksi" class="table-actions">
                                <button onclick='editData(<?= json_encode($r) ?>);' class="btn-icon btn-edit-table" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="<?= PROSES_URL ?>/penduduk_crud.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn-icon btn-delete-table" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="10" class="empty-state">
                                <i class="bi bi-people"></i>
                                <p><?= $search ? 'Data tidak ditemukan' : 'Belum ada data penduduk' ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stats Footer -->
    <div class="text-center mt-4">
        <div class="stats-footer">
            <span><i class="bi bi-check-circle-fill" style="color: #9CAF88;"></i> Total: <?= count($rows) ?> penduduk</span>
            <span><i class="bi bi-gender-male" style="color: #7A8F64;"></i> Laki-laki: <?= count(array_filter($rows, fn($i) => $i['gender'] == 'Laki-laki')) ?></span>
            <span><i class="bi bi-gender-female" style="color: #C58B8B;"></i> Perempuan: <?= count(array_filter($rows, fn($i) => $i['gender'] == 'Perempuan')) ?></span>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENDUDUK (sama seperti sebelumnya) -->
<div id="addModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Data Penduduk</span>
            </h3>
            <button class="modal-close" onclick="closeAddModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="addForm" action="<?= PROSES_URL ?>/penduduk_crud.php" method="post">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="create">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">NIK <span class="required" style="color: #C58B8B;">*</span></label>
                    <input type="text" id="add_nik" name="nik" class="form-control" required pattern="[0-9]{16}" maxlength="16" placeholder="16 digit angka NIK">
                    <div class="form-hint">16 digit angka sesuai KTP</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required" style="color: #C58B8B;">*</span></label>
                    <input type="text" id="add_full_name" name="full_name" class="form-control" required placeholder="Nama lengkap sesuai KTP">
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="add_tempat_lahir" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="add_tanggal_lahir" name="tanggal_lahir" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select id="add_gender" name="gender" class="form-select">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" id="add_pekerjaan" name="pekerjaan" class="form-control" placeholder="Jenis pekerjaan">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Tinggal</label>
                    <textarea id="add_address" name="address" class="form-control" rows="2" placeholder="Nama jalan, blok, dusun..."></textarea>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">RT</label>
                            <input type="text" id="add_rt" name="rt" class="form-control" placeholder="001">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">RW</label>
                            <input type="text" id="add_rw" name="rw" class="form-control" placeholder="002">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">Dusun</label>
                            <input type="text" id="add_dusun" name="dusun" class="form-control" placeholder="Nama dusun">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="tel" id="add_no_hp" name="no_hp" class="form-control" placeholder="0812XXXXXXXX">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save">
                    <i class="bi bi-save me-2"></i>Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PENDUDUK -->
<div id="editModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="bi bi-pencil-fill"></i>
                <span>Ubah Data Penduduk</span>
            </h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="editForm" action="<?= PROSES_URL ?>/penduduk_crud.php" method="post">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">NIK <span class="required" style="color: #C58B8B;">*</span></label>
                    <input type="text" id="edit_nik" name="nik" class="form-control" required pattern="[0-9]{16}" maxlength="16" placeholder="16 digit angka NIK">
                    <div class="form-hint">16 digit angka sesuai KTP</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required" style="color: #C58B8B;">*</span></label>
                    <input type="text" id="edit_full_name" name="full_name" class="form-control" required placeholder="Nama lengkap sesuai KTP">
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select id="edit_gender" name="gender" class="form-select">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" id="edit_pekerjaan" name="pekerjaan" class="form-control" placeholder="Jenis pekerjaan">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Tinggal</label>
                    <textarea id="edit_address" name="address" class="form-control" rows="2" placeholder="Nama jalan, blok, dusun..."></textarea>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">RT</label>
                            <input type="text" id="edit_rt" name="rt" class="form-control" placeholder="001">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">RW</label>
                            <input type="text" id="edit_rw" name="rw" class="form-control" placeholder="002">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label">Dusun</label>
                            <input type="text" id="edit_dusun" name="dusun" class="form-control" placeholder="Nama dusun">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="tel" id="edit_no_hp" name="no_hp" class="form-control" placeholder="0812XXXXXXXX">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save">
                    <i class="bi bi-save me-2"></i>Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    const modal = document.getElementById('addModal');
    document.getElementById('add_nik').value = '';
    document.getElementById('add_full_name').value = '';
    document.getElementById('add_tempat_lahir').value = '';
    document.getElementById('add_tanggal_lahir').value = '';
    document.getElementById('add_gender').value = '';
    document.getElementById('add_pekerjaan').value = '';
    document.getElementById('add_address').value = '';
    document.getElementById('add_rt').value = '';
    document.getElementById('add_rw').value = '';
    document.getElementById('add_dusun').value = '';
    document.getElementById('add_no_hp').value = '';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function openEditModal(data) {
    const modal = document.getElementById('editModal');
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_nik').value = data.nik || '';
    document.getElementById('edit_full_name').value = data.full_name || '';
    document.getElementById('edit_tempat_lahir').value = data.tempat_lahir || '';
    document.getElementById('edit_tanggal_lahir').value = data.tanggal_lahir || '';
    document.getElementById('edit_gender').value = data.gender || '';
    document.getElementById('edit_pekerjaan').value = data.pekerjaan || '';
    document.getElementById('edit_address').value = data.address || '';
    document.getElementById('edit_rt').value = data.rt || '';
    document.getElementById('edit_rw').value = data.rw || '';
    document.getElementById('edit_dusun').value = data.dusun || '';
    document.getElementById('edit_no_hp').value = data.no_hp || '';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function editData(data) {
    openEditModal(data);
}

// Close modals on overlay click
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// Auto open from URL
<?php if ($action === 'add'): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openAddModal();
        const url = new URL(window.location.href);
        url.searchParams.delete('action');
        window.history.replaceState({}, '', url);
    });
<?php elseif ($action === 'edit' && $editing): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openEditModal(<?= json_encode($editing) ?>);
        const url = new URL(window.location.href);
        url.searchParams.delete('action');
        url.searchParams.delete('id');
        window.history.replaceState({}, '', url);
    });
<?php endif; ?>
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>