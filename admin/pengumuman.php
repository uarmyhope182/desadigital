<?php
$page_title = 'Pengumuman';
$activePage = 'pengumuman';
$is_admin_page = true;

require_once __DIR__ . '/../config/db.php';
require_admin();

$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

$flash = get_flash('success');
$errors = get_flash('errors') ?? [];

try {
    $stmt = $pdo->prepare('SELECT id, judul, isi, gambar, is_published, created_at FROM pengumuman ORDER BY id DESC');
    $stmt->execute();
    $items = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching pengumuman: ' . $e->getMessage());
    $items = [];
}

$editing = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT id, judul, isi, gambar, is_published FROM pengumuman WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $editing = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Manajemen Pengumuman -->
<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>
<div class="page-bg-atmosphere"></div>

<style>
/* Bright Rustic Village Interface - Variables */
:root {
    --page-bg-cream: #FDFBF7;
    --page-bg-beige: #FAF6F0;
    --page-bg-warm: #FFF8EF;
    --page-sage: #9CAF88;
    --page-sage-dark: #7A8F64;
    --page-sage-light: #B5C4A3;
    --page-peach: #F6C7A1;
    --page-sky: #B9DCFF;
    --page-text-dark: #1A1512;
    --page-text-soft: #2C241E;
    --page-text-light: #3A3028;
    --page-border: #E8E0D5;
    --page-shadow: rgba(58, 49, 43, 0.04);
    --page-shadow-hover: rgba(58, 49, 43, 0.08);
    --page-success-bg: #F0F7ED;
    --page-error-bg: #FFF5F5;
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

.page-container {
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
    background: rgba(253, 251, 247, 0.85);
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

.btn-primary-custom {
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    color: white;
    border: none;
    border-radius: 40px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
    color: white;
}

.btn-outline-custom {
    background: transparent;
    color: #1A1512;
    border: 1.5px solid var(--page-border);
    border-radius: 40px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s;
    cursor: pointer;
}

.btn-outline-custom:hover {
    background: rgba(156, 175, 136, 0.1);
    border-color: var(--page-sage);
    color: #7A8F64;
}

.btn-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    margin: 0 2px;
}

.btn-edit {
    background: rgba(156, 175, 136, 0.2);
    color: #5C4A3A;
}

.btn-edit:hover {
    background: var(--page-sage);
    color: white;
    transform: scale(1.05);
}

.btn-delete {
    background: rgba(212, 165, 165, 0.2);
    color: #C58B8B;
}

.btn-delete:hover {
    background: #C58B8B;
    color: white;
    transform: scale(1.05);
}

/* PERBAIKAN UTAMA TABEL */
.table-container {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border-radius: 24px;
    overflow: hidden;
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

.admin-table thead tr {
    background: rgba(156, 175, 136, 0.08);
}

.admin-table th {
    padding: 0.85rem 1.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #5C4A3A;
    border-bottom: 2px solid var(--page-border);
    text-align: left;
    vertical-align: middle;
    white-space: nowrap;
}

.admin-table th.text-center {
    text-align: center;
}

.admin-table td {
    padding: 0.85rem 1.25rem;
    font-size: 0.85rem;
    color: #2C241E;
    font-weight: 500;
    border-bottom: 1px solid var(--page-border);
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
.col-judul {
    width: 22%;
    min-width: 200px;
}

.col-ringkasan {
    width: 45%;
    min-width: 250px;
}

.col-status {
    width: 10%;
    min-width: 90px;
}

.col-tanggal {
    width: 12%;
    min-width: 110px;
}

.col-aksi {
    width: 11%;
    min-width: 100px;
}

.table-actions {
    white-space: nowrap;
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.85rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-published {
    background: var(--page-sage);
    color: white;
}

.status-draft {
    background: var(--page-border);
    color: #2C241E;
}

.alert-success {
    background: var(--page-success-bg);
    border-left: 4px solid var(--page-sage);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.alert-error {
    background: var(--page-error-bg);
    border-left: 4px solid #E8C5C5;
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.btn-close {
    background: transparent;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.2s;
}

.btn-close:hover {
    opacity: 1;
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

/* Modal Styles */
.modal-custom {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-custom.active {
    display: flex;
}

.modal-custom-content {
    background: var(--page-bg-cream);
    border-radius: 28px;
    width: 90%;
    max-width: 600px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal-custom-header {
    border-bottom: 1px solid var(--page-border);
    padding: 1rem 1.5rem;
    background: rgba(156, 175, 136, 0.05);
    flex-shrink: 0;
}

.modal-custom-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #1A1512;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-custom-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.modal-custom-footer {
    border-top: 1px solid var(--page-border);
    padding: 1rem 1.5rem;
    flex-shrink: 0;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background: var(--page-bg-cream);
}

/* Custom scrollbar */
.modal-custom-body::-webkit-scrollbar {
    width: 6px;
}

.modal-custom-body::-webkit-scrollbar-track {
    background: var(--page-border);
    border-radius: 10px;
}

.modal-custom-body::-webkit-scrollbar-thumb {
    background: var(--page-sage);
    border-radius: 10px;
}

.form-label-custom {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.5rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.required {
    color: #C58B8B;
}

.form-hint {
    font-size: 0.65rem;
    color: #2C241E;
    opacity: 0.7;
    margin-top: 0.25rem;
}

.sasi-form-control {
    background: white;
    border: 1.5px solid var(--page-border);
    border-radius: 16px;
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    color: #1A1512;
    width: 100%;
    transition: all 0.2s;
    font-family: inherit;
}

.sasi-form-control:focus {
    border-color: var(--page-sage);
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
    outline: none;
}

textarea.sasi-form-control {
    resize: vertical;
    min-height: 100px;
}

.switch-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 1rem 0;
    padding: 0.75rem;
    background: rgba(156, 175, 136, 0.06);
    border-radius: 16px;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-check-input {
    width: 2.5em;
    height: 1.25em;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--page-sage);
    border-color: var(--page-sage);
}

.form-check-label {
    color: #1A1512;
    font-weight: 600;
    cursor: pointer;
}

.mb-3 {
    margin-bottom: 1rem;
}

.d-inline {
    display: inline;
}

.fw-semibold {
    font-weight: 600;
}

.text-center {
    text-align: center;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .page-container {
        padding: 1.25rem;
    }
    .admin-table {
        min-width: 700px;
    }
}

@media (max-width: 768px) {
    .page-container {
        padding: 1rem;
    }
    .page-title {
        font-size: 1.25rem;
    }
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .admin-table th,
    .admin-table td {
        padding: 0.6rem 0.75rem;
        font-size: 0.75rem;
    }
    .modal-custom-body {
        padding: 1rem;
    }
    .modal-custom-header {
        padding: 0.85rem 1rem;
    }
    .modal-custom-footer {
        padding: 0.85rem 1rem;
    }
    .status-badge {
        padding: 0.2rem 0.7rem;
        font-size: 0.65rem;
    }
    .btn-icon {
        width: 30px;
        height: 30px;
    }
    .btn-icon i {
        font-size: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-container {
        padding: 0.75rem;
    }
    .page-header {
        padding: 1rem;
    }
    .page-title {
        font-size: 1.1rem;
    }
    .page-subtitle {
        font-size: 0.75rem;
    }
    .btn-primary-custom {
        width: 100%;
        justify-content: center;
    }
    .admin-table th,
    .admin-table td {
        padding: 0.5rem 0.6rem;
        font-size: 0.7rem;
    }
    .admin-table {
        min-width: 600px;
    }
    .status-badge {
        padding: 0.15rem 0.5rem;
        font-size: 0.6rem;
    }
    .btn-icon {
        width: 28px;
        height: 28px;
    }
    .modal-custom-content {
        width: 95%;
        max-height: 90vh;
    }
    .modal-custom-header h5 {
        font-size: 1rem;
    }
    .form-label-custom {
        font-size: 0.7rem;
    }
    .sasi-form-control {
        padding: 0.6rem 0.85rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 380px) {
    .admin-table th,
    .admin-table td {
        padding: 0.4rem 0.5rem;
        font-size: 0.65rem;
    }
    .status-badge {
        font-size: 0.55rem;
        padding: 0.1rem 0.4rem;
    }
    .btn-icon {
        width: 26px;
        height: 26px;
    }
}
</style>

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Pengumuman</h1>
            <p class="page-subtitle">Publikasikan informasi resmi, berita kelurahan, atau imbauan bagi masyarakat</p>
        </div>
        <button class="btn-primary-custom" onclick="openAddModal()">
            <i class="bi bi-plus-lg"></i> Tambah Pengumuman
        </button>
    </div>

    <!-- Success Alert -->
    <?php if ($flash): ?>
        <div class="alert-success">
            <div>
                <i class="bi bi-check-circle-fill" style="color: #9CAF88;"></i>
                <strong>Berhasil</strong> - <?= htmlspecialchars($flash) ?>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Error Alert -->
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <div>
                <i class="bi bi-exclamation-triangle-fill" style="color: #C58B8B;"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <!-- PERBAIKAN STRUKTUR TABEL PENGUMUMAN -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="col-judul">Judul Pengumuman</th>
                        <th class="col-ringkasan">Ringkasan Informasi</th>
                        <th class="col-status text-center">Status</th>
                        <th class="col-tanggal">Tanggal Dibuat</th>
                        <th class="col-aksi text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td data-label="Judul Pengumuman">
                                <strong style="color: #1A1512;"><?= htmlspecialchars($it['judul']) ?></strong>
                            </td>
                            <td data-label="Ringkasan Informasi" style="color: #2C241E;">
                                <?php 
                                $ringkasan = strip_tags($it['isi']);
                                echo htmlspecialchars(substr($ringkasan, 0, 120));
                                echo strlen($ringkasan) > 120 ? '...' : '';
                                ?>
                            </td>
                            <td data-label="Status" class="text-center">
                                <?php if ($it['is_published']): ?>
                                    <span class="status-badge status-published">Terbit</span>
                                <?php else: ?>
                                    <span class="status-badge status-draft">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Tanggal Dibuat" style="color: #2C241E;">
                                <?= date('d M Y', strtotime($it['created_at'])) ?>
                            </td>
                            <td data-label="Aksi" class="table-actions">
                                <button type="button" class="btn-icon btn-edit" 
                                    data-id="<?= $it['id'] ?>"
                                    data-judul="<?= htmlspecialchars($it['judul']) ?>"
                                    data-isi="<?= htmlspecialchars($it['isi']) ?>"
                                    data-published="<?= $it['is_published'] ?>"
                                    title="Ubah pengumuman">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="<?= PROSES_URL ?>/pengumuman_crud.php" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?');">
                                    <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $it['id'] ?>">
                                    <button type="submit" class="btn-icon btn-delete" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-megaphone"></i>
                                <p>Belum ada pengumuman yang diterbitkan</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div id="addModal" class="modal-custom">
    <div class="modal-custom-content">
        <div class="modal-custom-header">
            <h5>
                <i class="bi bi-plus-circle-fill" style="color: var(--page-sage);"></i>
                Tambah Pengumuman
            </h5>
        </div>
        <div class="modal-custom-body">
            <form id="addForm" action="<?= PROSES_URL ?>/pengumuman_crud.php" method="post" enctype="multipart/form-data">
                <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                <input type="hidden" name="action" value="create">
                
                <div class="mb-3">
                    <label class="form-label-custom">Judul <span class="required">*</span></label>
                    <input type="text" name="judul" id="add_judul" class="sasi-form-control" required placeholder="Masukkan judul pengumuman">
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Isi <span class="required">*</span></label>
                    <textarea name="isi" id="add_isi" class="sasi-form-control" rows="6" required placeholder="Tulis isi pengumuman..."></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Gambar <span style="font-weight: normal; font-size: 0.7rem;">(opsional)</span></label>
                    <input type="file" name="gambar" class="sasi-form-control" accept="image/jpeg,image/png,image/jpg">
                    <div class="form-hint">Format: JPG, JPEG, PNG. Maksimal 2MB</div>
                </div>
                
                <div class="switch-container">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_is_published" name="is_published" value="1" checked>
                        <label class="form-check-label" for="add_is_published">Terbitkan sekarang ke website publik</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-custom-footer">
            <button type="button" class="btn-outline-custom" onclick="closeAddModal()">Batal</button>
            <button type="button" class="btn-primary-custom" onclick="document.getElementById('addForm').submit()">Simpan</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="editModal" class="modal-custom">
    <div class="modal-custom-content">
        <div class="modal-custom-header">
            <h5>
                <i class="bi bi-pencil-square" style="color: var(--page-sage);"></i>
                Edit Pengumuman
            </h5>
        </div>
        <div class="modal-custom-body">
            <form id="editForm" action="<?= PROSES_URL ?>/pengumuman_crud.php" method="post" enctype="multipart/form-data">
                <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="mb-3">
                    <label class="form-label-custom">Judul <span class="required">*</span></label>
                    <input type="text" name="judul" id="edit_judul" class="sasi-form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Isi <span class="required">*</span></label>
                    <textarea name="isi" id="edit_isi" class="sasi-form-control" rows="6" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Ganti Gambar <span style="font-weight: normal; font-size: 0.7rem;">(opsional)</span></label>
                    <input type="file" name="gambar" class="sasi-form-control" accept="image/jpeg,image/png,image/jpg">
                    <div class="form-hint">Kosongkan jika tidak ingin mengubah gambar</div>
                </div>
                
                <div class="switch-container">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_published" name="is_published" value="1">
                        <label class="form-check-label" for="edit_is_published">Terbitkan ke publik</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-custom-footer">
            <button type="button" class="btn-outline-custom" onclick="closeEditModal()">Batal</button>
            <button type="button" class="btn-primary-custom" onclick="document.getElementById('editForm').submit()">Simpan Perubahan</button>
        </div>
    </div>
</div>

<script>
// Fungsi helper untuk htmlspecialchars di JavaScript
function htmlEscape(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Modal elements
const addModal = document.getElementById('addModal');
const editModal = document.getElementById('editModal');

// Open Add Modal
function openAddModal() {
    addModal.classList.add('active');
    document.body.style.overflow = 'hidden';
    // Reset form
    document.getElementById('add_judul').value = '';
    document.getElementById('add_isi').value = '';
    document.getElementById('add_is_published').checked = true;
}

// Close Add Modal
function closeAddModal() {
    addModal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Open Edit Modal
function openEditModal(id, judul, isi, published) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_isi').value = isi;
    document.getElementById('edit_is_published').checked = (published == 1);
    editModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close Edit Modal
function closeEditModal() {
    editModal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Event listeners for edit buttons
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const judul = this.dataset.judul;
        const isi = this.dataset.isi;
        const published = this.dataset.published;
        
        openEditModal(id, judul, isi, published);
    });
});

// Close modal when clicking outside
addModal.addEventListener('click', function(e) {
    if (e.target === addModal) {
        closeAddModal();
    }
});

editModal.addEventListener('click', function(e) {
    if (e.target === editModal) {
        closeEditModal();
    }
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (addModal.classList.contains('active')) {
            closeAddModal();
        }
        if (editModal.classList.contains('active')) {
            closeEditModal();
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>