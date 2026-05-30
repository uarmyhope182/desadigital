<?php
$page_title = 'Manajemen Jenis Surat';
$activePage = 'jenis_surat';
$is_admin_page = true;

require_once __DIR__ . '/../config/db.php';
require_admin();

$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

$flash = get_flash('success');
$errors = get_flash('errors') ?? [];

// Query tanpa created_at karena kolom tersebut tidak ada di tabel jenis_surat
try {
    $stmt = $pdo->prepare('SELECT id, kode, nama, persyaratan, deskripsi, is_active FROM jenis_surat ORDER BY id DESC');
    $stmt->execute();
    $items = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching jenis_surat: ' . $e->getMessage());
    $items = [];
}

$editing = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT id, kode, nama, persyaratan, deskripsi, is_active FROM jenis_surat WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $editing = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Bricolage+Grotesque:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --page-sage: #9CAF88;
    --page-sage-dark: #7A8F64;
    --page-sage-light: #B5C4A3;
    --page-border: #E8E0D5;
    --page-bg-cream: #FDFBF7;
    --page-bg-beige: #FAF6F0;
    --page-text-dark: #1A1512;
    --page-text-soft: #2C241E;
    --page-shadow: rgba(58, 49, 43, 0.05);
    --page-shadow-hover: rgba(58, 49, 43, 0.1);
    --status-active: #9CAF88;
    --status-inactive: #E8E0D5;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
    min-height: 100vh;
}

.page-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
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

.header-title h1 {
    font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--page-text-dark);
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.header-title p {
    color: var(--page-text-soft);
    font-size: 0.85rem;
    margin-bottom: 0;
    font-weight: 500;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(156, 175, 136, 0.12);
    padding: 0.5rem 1rem;
    border-radius: 40px;
}

.stats-badge i {
    color: var(--page-sage-dark);
    font-size: 1rem;
}

.stats-badge span {
    font-weight: 700;
    color: var(--page-text-dark);
}

.stats-badge small {
    color: var(--page-sage-dark);
    font-weight: 500;
    font-size: 0.7rem;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    color: white;
    border: none;
    border-radius: 40px;
    padding: 0.6rem 1.5rem;
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

.btn-icon {
    width: 34px;
    height: 34px;
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

.alert-custom {
    background: white;
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
    backdrop-filter: blur(4px);
}

.alert-success {
    border-left-color: var(--page-sage);
    background: rgba(240, 247, 237, 0.95);
}

.alert-error {
    border-left-color: #E8C5C5;
    background: rgba(255, 245, 245, 0.95);
}

.alert-custom i {
    margin-right: 0.5rem;
}

.alert-custom ul {
    margin-top: 0.5rem;
    margin-bottom: 0;
    padding-left: 1.5rem;
}

/* PERBAIKAN UTAMA TABEL */
.table-container {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    overflow-x: auto;
    overflow-y: visible;
    box-shadow: 0 10px 30px var(--page-shadow);
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
    min-width: 800px;
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
    padding: 1rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
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
    background: rgba(156, 175, 136, 0.04);
}

.admin-table tbody td {
    padding: 1rem 1.25rem;
    font-size: 0.85rem;
    color: var(--page-text-soft);
    font-weight: 500;
    border-bottom: 1px solid var(--page-border);
    vertical-align: middle;
    background-color: transparent;
}

.admin-table tbody tr:last-child td {
    border-bottom: none;
}

/* Styling untuk konten tabel */
.code-badge {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--page-text-dark);
    background: linear-gradient(135deg, rgba(156, 175, 136, 0.12), rgba(246, 199, 161, 0.06));
    padding: 0.3rem 0.7rem;
    border-radius: 10px;
    letter-spacing: 0.4px;
    border: 1px solid rgba(156, 175, 136, 0.2);
    white-space: nowrap;
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.9rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-active {
    background: var(--status-active);
    color: #FFFFFF;
}

.status-inactive {
    background: var(--status-inactive);
    color: var(--page-text-soft);
}

.cell-truncate {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cell-truncate br {
    display: none;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--page-text-soft);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    opacity: 0.4;
    color: var(--page-sage);
}

.empty-state p {
    font-size: 0.9rem;
    margin: 0;
    font-weight: 500;
}

.stats-footer {
    margin-top: 1.5rem;
    padding: 0.75rem 1.5rem;
    background: rgba(253, 251, 247, 0.9);
    backdrop-filter: blur(8px);
    border-radius: 60px;
    display: inline-flex;
    justify-content: center;
    gap: 2rem;
    font-size: 0.75rem;
    color: var(--page-text-soft);
    font-weight: 500;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.stats-footer span {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

/* Modal Styles */
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
    background: var(--page-bg-cream);
    border-radius: 32px;
    width: 90%;
    max-width: 500px;
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

.modal-header h3 i {
    font-size: 1.2rem;
}

.modal-close {
    background: rgba(255, 255, 255, 0.2);
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
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-body {
    padding: 1.75rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--page-text-dark);
    margin-bottom: 0.5rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-label .required {
    color: #C58B8B;
}

.form-control {
    background: white;
    border: 1.5px solid var(--page-border);
    border-radius: 14px;
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    color: var(--page-text-dark);
    width: 100%;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    border-color: var(--page-sage);
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
    outline: none;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.form-hint {
    font-size: 0.65rem;
    color: var(--page-text-soft);
    opacity: 0.7;
    margin-top: 0.25rem;
    font-weight: 500;
}

.switch-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1.25rem 0;
}

.switch-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--page-text-dark);
    margin: 0;
}

.form-switch-custom {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 26px;
}

.form-switch-custom input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #E8E0D5;
    transition: 0.3s;
    border-radius: 34px;
}

.switch-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .switch-slider {
    background-color: var(--page-sage);
}

input:checked + .switch-slider:before {
    transform: translateX(26px);
}

.modal-footer {
    padding: 1.25rem 1.75rem;
    border-top: 1px solid var(--page-border);
    background: var(--page-bg-cream);
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
    font-size: 0.9rem;
}

.btn-modal-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.text-center {
    text-align: center;
}

.d-inline {
    display: inline;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .main-container {
        padding: 1.25rem;
    }
    
    .page-header {
        padding: 1rem 1.25rem;
    }
    
    .header-title h1 {
        font-size: 1.35rem;
    }
}

@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-title h1 {
        font-size: 1.25rem;
    }
    
    .header-title p {
        font-size: 0.75rem;
    }
    
    .header-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .stats-badge {
        padding: 0.4rem 0.8rem;
    }
    
    .stats-badge span {
        font-size: 0.9rem;
    }
    
    .admin-table thead th,
    .admin-table tbody td {
        padding: 0.75rem 1rem;
    }
    
    .code-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    
    .status-badge {
        padding: 0.2rem 0.7rem;
        font-size: 0.65rem;
    }
    
    .btn-icon {
        width: 30px;
        height: 30px;
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
        padding: 0.6rem 1rem;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 0.875rem 1rem;
    }
    
    .header-title h1 {
        font-size: 1.1rem;
    }
    
    .header-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stats-badge {
        justify-content: center;
    }
    
    .btn-primary-custom {
        justify-content: center;
        width: 100%;
    }
    
    .admin-table thead th,
    .admin-table tbody td {
        padding: 0.6rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .code-badge {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
    }
    
    .status-badge {
        padding: 0.15rem 0.5rem;
        font-size: 0.6rem;
    }
    
    .btn-icon {
        width: 28px;
        height: 28px;
    }
    
    .btn-icon i {
        font-size: 0.75rem;
    }
    
    .modal-header {
        padding: 1rem;
    }
    
    .modal-header h3 {
        font-size: 1rem;
    }
    
    .modal-close {
        width: 32px;
        height: 32px;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .modal-footer {
        padding: 1rem;
    }
    
    .form-label {
        font-size: 0.7rem;
    }
    
    .form-control {
        padding: 0.6rem 0.85rem;
        font-size: 0.8rem;
    }
    
    .stats-footer {
        font-size: 0.65rem;
        gap: 0.75rem;
    }
    
    .empty-state {
        padding: 1.5rem;
    }
    
    .empty-state i {
        font-size: 2rem;
    }
}
</style>

<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>

<div class="main-container">
    <div class="page-header">
        <div class="header-title">
            <h1>Manajemen Jenis Surat</h1>
            <p>Kelola ragam opsi surat yang terintegrasi pada formulir pendaftaran penduduk</p>
        </div>
        <div class="header-actions">
            <div class="stats-badge">
                <i class="bi bi-files"></i>
                <span><?= count($items) ?></span>
                <small>Jenis Surat Terdaftar</small>
            </div>
            <button onclick="openAddModal()" class="btn-primary-custom">
                <i class="bi bi-plus-lg"></i> Tambah Jenis Surat
            </button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert-custom alert-success">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-custom alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- PERBAIKAN STRUKTUR TABEL -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Kode Surat</th>
                        <th style="width: 18%;">Nama Jenis Surat</th>
                        <th style="width: 20%;">Persyaratan</th>
                        <th style="width: 25%;">Deskripsi</th>
                        <th style="width: 10%; text-align: center;">Status</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items) && count($items) > 0): ?>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td data-label="Kode Surat">
                                    <span class="code-badge"><?= htmlspecialchars($it['kode']) ?></span>
                                </td>
                                <td data-label="Nama Jenis Surat">
                                    <strong style="color: var(--page-text-dark);"><?= htmlspecialchars($it['nama']) ?></strong>
                                </td>
                                <td data-label="Persyaratan" class="cell-truncate">
                                    <?= htmlspecialchars(substr($it['persyaratan'] ?? '-', 0, 100)) ?>
                                    <?= (strlen($it['persyaratan'] ?? '') > 100) ? '...' : '' ?>
                                </td>
                                <td data-label="Deskripsi" class="cell-truncate">
                                    <?= htmlspecialchars(substr($it['deskripsi'] ?? '-', 0, 100)) ?>
                                    <?= (strlen($it['deskripsi'] ?? '') > 100) ? '...' : '' ?>
                                </td>
                                <td data-label="Status" style="text-align: center;">
                                    <?php if ($it['is_active']): ?>
                                        <span class="status-badge status-active">Aktif</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Aksi" style="text-align: center; white-space: nowrap;">
                                    <button onclick='editData(<?= json_encode($it) ?>)' class="btn-icon btn-edit-table" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form action="<?= PROSES_URL ?>/jenis_surat_crud.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jenis surat ini?')">
                                        <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $it['id'] ?>">
                                        <button type="submit" class="btn-icon btn-delete-table" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-files"></i>
                                <p>Belum ada jenis surat yang terdaftar.<br>Klik tombol Tambah Jenis Surat untuk memulai</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center">
        <div class="stats-footer">
            <span><i class="bi bi-check-circle-fill" style="color: #9CAF88;"></i> Total: <?= count($items) ?> jenis</span>
            <span><i class="bi bi-check-circle-fill" style="color: #7A8F64;"></i> Aktif: <?= count(array_filter($items, fn($i) => $i['is_active'])) ?></span>
            <span><i class="bi bi-check-circle-fill" style="color: #C58B8B;"></i> Nonaktif: <?= count(array_filter($items, fn($i) => !$i['is_active'])) ?></span>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div id="addModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="bi bi-file-earmark-plus-fill"></i>
                <span>Tambah Jenis Surat</span>
            </h3>
            <button class="modal-close" onclick="closeAddModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="addForm" action="<?= PROSES_URL ?>/jenis_surat_crud.php" method="post">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="create">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Identifikasi <span class="required">*</span></label>
                    <input type="text" id="add_kode" name="kode" class="form-control text-uppercase" required maxlength="20" placeholder="CONTOH: SKTM">
                    <div class="form-hint">Gunakan singkatan ringkas (SKTM, SKU, SKD, dll)</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Surat <span class="required">*</span></label>
                    <input type="text" id="add_nama" name="nama" class="form-control" required placeholder="Contoh: Surat Keterangan Tidak Mampu">
                </div>

                <div class="form-group">
                    <label class="form-label">Persyaratan</label>
                    <textarea id="add_persyaratan" name="persyaratan" class="form-control" rows="3" placeholder="Tuliskan persyaratan yang diperlukan untuk mengajukan surat ini&#10;Contoh:&#10;- Fotokopi KTP&#10;- Fotokopi KK&#10;- Surat Pengantar RT/RW"></textarea>
                    <div class="form-hint">Pisahkan setiap persyaratan dengan baris baru atau gunakan bullet point</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="add_deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi atau penjelasan tentang surat ini&#10;Contoh: Surat ini digunakan untuk keperluan administrasi kependudukan seperti..."></textarea>
                    <div class="form-hint">Jelaskan kegunaan dan fungsi dari surat ini</div>
                </div>

                <div class="switch-container">
                    <label class="switch-label">Status Aktif</label>
                    <label class="form-switch-custom">
                        <input type="checkbox" id="add_is_active" name="is_active" value="1" checked>
                        <span class="switch-slider"></span>
                    </label>
                    <span style="font-size: 0.7rem; color: var(--page-text-soft); opacity: 0.7;">Aktifkan opsi jenis surat ini</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save">
                    <i class="bi bi-save me-2"></i>Simpan Jenis Surat
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="editModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3>
                <i class="bi bi-pencil-fill"></i>
                <span>Edit Jenis Surat</span>
            </h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <form id="editForm" action="<?= PROSES_URL ?>/jenis_surat_crud.php" method="post">
            <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Identifikasi <span class="required">*</span></label>
                    <input type="text" id="edit_kode" name="kode" class="form-control text-uppercase" required maxlength="20" placeholder="CONTOH: SKTM">
                    <div class="form-hint">Gunakan singkatan ringkas (SKTM, SKU, SKD, dll)</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Surat <span class="required">*</span></label>
                    <input type="text" id="edit_nama" name="nama" class="form-control" required placeholder="Contoh: Surat Keterangan Tidak Mampu">
                </div>

                <div class="form-group">
                    <label class="form-label">Persyaratan</label>
                    <textarea id="edit_persyaratan" name="persyaratan" class="form-control" rows="3" placeholder="Tuliskan persyaratan yang diperlukan"></textarea>
                    <div class="form-hint">Pisahkan setiap persyaratan dengan baris baru</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi surat"></textarea>
                    <div class="form-hint">Jelaskan kegunaan dan fungsi surat ini</div>
                </div>

                <div class="switch-container">
                    <label class="switch-label">Status Aktif</label>
                    <label class="form-switch-custom">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                        <span class="switch-slider"></span>
                    </label>
                    <span style="font-size: 0.7rem; color: var(--page-text-soft); opacity: 0.7;">Aktifkan opsi jenis surat ini</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-modal-save">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    const modal = document.getElementById('addModal');
    document.getElementById('add_kode').value = '';
    document.getElementById('add_nama').value = '';
    document.getElementById('add_persyaratan').value = '';
    document.getElementById('add_deskripsi').value = '';
    document.getElementById('add_is_active').checked = true;
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
    document.getElementById('edit_kode').value = data.kode;
    document.getElementById('edit_nama').value = data.nama;
    document.getElementById('edit_persyaratan').value = data.persyaratan || '';
    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
    document.getElementById('edit_is_active').checked = data.is_active == 1;
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

document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

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