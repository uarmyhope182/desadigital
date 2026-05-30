<?php
$page_title = 'Manajemen Users';
$activePage = 'users';
$is_admin_page = true;

require_once __DIR__ . '/../config/db.php';
require_admin();

$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

$flash = get_flash('success');
$errors = get_flash('errors') ?? [];

try {
    $stmt = $pdo->prepare('SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id DESC');
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching users: ' . $e->getMessage());
    $users = [];
}

$editingUser = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT id, username, email, full_name, role FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $editingUser = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Manajemen Users -->
<!-- Background Layers -->
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
    --page-peach-dark: #F0B885;
    --page-sky: #B9DCFF;
    --page-text-dark: #1A1512;
    --page-text-soft: #2C241E;
    --page-text-light: #3A3028;
    --page-border: #E8E0D5;
    --page-shadow: rgba(58, 49, 43, 0.05);
    --page-shadow-hover: rgba(58, 49, 43, 0.08);
    --page-error-bg: #FFF5F5;
    --page-error-border: #E8C5C5;
    --page-error-text: #8B5E5E;
    --page-success-bg: #F0F7ED;
}

/* Page Background */
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

/* Section Header */
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

/* Cards */
.sasi-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(156, 175, 136, 0.2);
    border-radius: 24px;
    box-shadow: 0 4px 12px var(--page-shadow);
    transition: all 0.3s ease;
    overflow: hidden;
}

.sasi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px var(--page-shadow-hover);
    border-color: var(--page-sage-light);
}

/* Buttons */
.btn-sasi-primary {
    background-color: var(--page-sage);
    color: #FFFFFF;
    border: none;
    border-radius: 40px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.btn-sasi-primary:hover {
    background-color: var(--page-sage-dark);
    transform: translateY(-1px);
    color: white;
}

.btn-sasi-outline {
    background: transparent;
    color: #1A1512;
    border: 1.5px solid var(--page-border);
    border-radius: 40px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-sasi-outline:hover {
    background: rgba(156, 175, 136, 0.1);
    border-color: var(--page-sage);
    color: #7A8F64;
}

.btn-action {
    border-radius: 50%;
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    margin: 0 2px;
}

.btn-edit {
    background-color: rgba(156, 175, 136, 0.2);
    color: #5C4A3A;
}

.btn-edit:hover {
    background-color: var(--page-sage);
    color: #FFFFFF;
    transform: scale(1.05);
}

.btn-delete {
    background-color: rgba(212, 165, 165, 0.2);
    color: #C58B8B;
}

.btn-delete:hover {
    background-color: #C58B8B;
    color: #FFFFFF;
    transform: scale(1.05);
}

/* Table Styles */
.sasi-table {
    width: 100%;
}

.sasi-table thead tr {
    background: rgba(156, 175, 136, 0.08);
}

.sasi-table th {
    padding: 0.85rem 1.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #5C4A3A;
    border-bottom: 1px solid var(--page-border);
}

.sasi-table td {
    padding: 0.85rem 1.25rem;
    font-size: 0.85rem;
    color: #2C241E;
    font-weight: 500;
    border-bottom: 1px solid var(--page-border);
    vertical-align: middle;
}

.sasi-table tbody tr:hover {
    background: rgba(156, 175, 136, 0.04);
}

.sasi-table tbody tr:last-child td {
    border-bottom: none;
}

/* Username Code */
.username-code {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    font-weight: 700;
    color: #1A1512;
    background: linear-gradient(135deg, rgba(156, 175, 136, 0.12), rgba(246, 199, 161, 0.06));
    padding: 0.3rem 0.7rem;
    border-radius: 10px;
    letter-spacing: 0.4px;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

/* Role Badges */
.role-badge {
    display: inline-block;
    padding: 0.3rem 0.9rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.role-admin {
    background-color: var(--page-sage);
    color: #FFFFFF;
}

.role-operator {
    background-color: var(--page-peach);
    color: #1A1512;
}

/* Form Controls */
.sasi-form-control {
    background-color: #FFFFFF;
    border: 1.5px solid var(--page-border);
    border-radius: 16px;
    padding: 0.75rem 1rem;
    color: #1A1512;
    font-weight: 500;
    transition: all 0.2s ease;
    width: 100%;
    font-family: inherit;
}

.sasi-form-control:focus {
    border-color: var(--page-sage);
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
    outline: none;
}

select.sasi-form-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23444444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    appearance: none;
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
    font-weight: 500;
}

/* Alert Messages */
.alert-success {
    background-color: var(--page-success-bg);
    border-left: 4px solid var(--page-sage);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.alert-error {
    background-color: var(--page-error-bg);
    border-left: 4px solid var(--page-error-border);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.alert-success .alert-icon {
    background: var(--page-sage);
    color: white;
}

.alert-error .alert-icon {
    background: var(--page-error-border);
    color: #8B5E5E;
}

.alert-content {
    flex: 1;
    margin-left: 0.75rem;
}

.alert-title {
    font-weight: 700;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    color: #1A1512;
}

.alert-message {
    font-size: 0.8rem;
    color: #2C241E;
    font-weight: 500;
}

.error-list {
    margin: 0.5rem 0 0 1rem;
    padding: 0;
    font-size: 0.75rem;
    color: #8B5E5E;
    font-weight: 500;
}

/* Empty State */
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

/* Modal Styles - Fixed */
.modal {
    z-index: 1060;
}

.modal-backdrop {
    z-index: 1050;
}

.modal-dialog {
    margin: 1.75rem auto;
    pointer-events: auto;
}

.modal.fade .modal-dialog {
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translate(0, 0);
}

.sasi-modal-content {
    border-radius: 28px;
    border: 1px solid var(--page-border);
    overflow: hidden;
    background: var(--page-bg-cream);
}

.sasi-modal-header {
    border-bottom: 1px solid var(--page-border);
    padding: 1.25rem 1.75rem;
    background: rgba(156, 175, 136, 0.05);
}

.sasi-modal-header .modal-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1A1512;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sasi-modal-body {
    padding: 1.75rem;
}

.sasi-modal-footer {
    border-top: 1px solid var(--page-border);
    padding: 1rem 1.75rem;
    background: rgba(156, 175, 136, 0.02);
}

/* Responsive */
@media (max-width: 992px) {
    .page-container {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1rem 1.25rem;
    }
    
    .page-title {
        font-size: 1.35rem;
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
    
    .sasi-table th,
    .sasi-table td {
        padding: 0.6rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .username-code {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
    }
    
    .role-badge {
        padding: 0.2rem 0.6rem;
        font-size: 0.65rem;
    }
    
    .btn-action {
        width: 30px;
        height: 30px;
    }
    
    .sasi-modal-header {
        padding: 1rem;
    }
    
    .sasi-modal-body {
        padding: 1rem;
    }
    
    .sasi-modal-footer {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 576px) {
    .btn-action {
        width: 28px;
        height: 28px;
    }
    
    .btn-action i {
        font-size: 0.75rem;
    }
}
</style>

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Akun Pengguna</h1>
            <p class="page-subtitle">Kelola dan tinjau hak akses akun admin kelurahan serta operator</p>
        </div>
        <button type="button" class="btn-sasi-primary" onclick="openAddModal()">
            <i class="bi bi-plus-lg"></i>
            Tambah Pengguna
        </button>
    </div>

    <!-- Success Alert -->
    <?php if ($flash): ?>
        <div class="alert-success">
            <div style="display: flex; align-items: center;">
                <div class="alert-icon">✓</div>
                <div class="alert-content">
                    <div class="alert-title">Berhasil</div>
                    <div class="alert-message"><?= h($flash) ?></div>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" onclick="this.parentElement.style.display='none'"></button>
        </div>
    <?php endif; ?>

    <!-- Error Alert -->
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <div style="display: flex; align-items: flex-start;">
                <div class="alert-icon">!</div>
                <div class="alert-content">
                    <div class="alert-title">Kendala Teknis</div>
                    <ul class="error-list">
                        <?php foreach ($errors as $err): ?>
                            <li><?= h($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" onclick="this.parentElement.style.display='none'"></button>
        </div>
    <?php endif; ?>

    <!-- Main Table Card -->
    <div class="sasi-card">
        <div class="table-responsive">
            <table class="sasi-table table-compact admin-table">
                <thead>
                    <tr>
                        <th class="priority-high" style="width: 120px;">Username</th>
                        <th class="priority-high">Nama Lengkap</th>
                        <th class="priority-high">Email</th>
                        <th class="priority-high" style="width: 100px;">Peran</th>
                        <th class="priority-low col-hidden-mobile" style="width: 110px;">Dibuat</th>
                        <th class="priority-high" style="width: 90px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td data-label="Username"><code class="username-code"><?= h($u['username']) ?></code></td>
                            <td data-label="Nama Lengkap" class="fw-semibold" style="color: #1A1512;"><?= h($u['full_name']) ?></td>
                            <td data-label="Email" style="color: #2C241E;"><?= h($u['email']) ?></td>
                            <td data-label="Peran">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="role-badge role-admin">Admin</span>
                                <?php else: ?>
                                    <span class="role-badge role-operator">Operator</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Dibuat" class="col-hidden-mobile priority-low" style="color: #2C241E; font-weight: 500;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                            <td data-label="Aksi" class="text-center table-actions">
                                <button type="button" class="btn-action btn-edit" onclick='openEditModal(<?= json_encode($u) ?>)' title="Ubah data">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <?php if ($u['username'] !== 'admin'): ?>
                                    <form action="<?= PROSES_URL ?>/users_crud.php" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                        <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn-action btn-delete" title="Hapus data">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-people"></i>
                                <p>Belum ada data akun pengguna terdaftar</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--page-bg-cream); border-radius: 28px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div class="sasi-modal-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h5 class="modal-title">
                <i class="bi bi-person-plus-fill" style="color: var(--page-sage);"></i>
                Tambah Pengguna Baru
            </h5>
            <button type="button" class="btn-close shadow-none" onclick="closeAddModal()"></button>
        </div>
        <div class="sasi-modal-body">
            <form id="addUserForm" action="<?= PROSES_URL ?>/users_crud.php" method="post">
                <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                <input type="hidden" name="action" value="create">
                
                <div class="mb-3">
                    <label class="form-label-custom">Nama Pengguna <span class="required">*</span></label>
                    <input type="text" name="username" class="sasi-form-control" required placeholder="Username login">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="full_name" class="sasi-form-control" required placeholder="Nama lengkap staf">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" class="sasi-form-control" required placeholder="staf@kelurahansasi.id">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Tingkat Wewenang <span class="required">*</span></label>
                    <select name="role" class="sasi-form-control">
                        <option value="operator">Operator Kelurahan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Kata Sandi <span class="required">*</span></label>
                    <input type="password" name="password" class="sasi-form-control" required minlength="8" placeholder="Minimal 8 karakter">
                </div>

                <div class="sasi-modal-footer" style="margin-top: 1rem; padding: 0; border: none; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn-sasi-outline" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn-sasi-primary">Daftarkan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--page-bg-cream); border-radius: 28px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div class="sasi-modal-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h5 class="modal-title">
                <i class="bi bi-pencil-square" style="color: var(--page-sage);"></i>
                Ubah Akun Pengguna
            </h5>
            <button type="button" class="btn-close shadow-none" onclick="closeEditModal()"></button>
        </div>
        <div class="sasi-modal-body">
            <form id="editUserForm" action="<?= PROSES_URL ?>/users_crud.php" method="post">
                <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="mb-3">
                    <label class="form-label-custom">Nama Pengguna <span class="required">*</span></label>
                    <input type="text" name="username" id="edit_username" class="sasi-form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="full_name" id="edit_full_name" class="sasi-form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" id="edit_email" class="sasi-form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Tingkat Wewenang <span class="required">*</span></label>
                    <select name="role" id="edit_role" class="sasi-form-control">
                        <option value="operator">Operator Kelurahan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Kata Sandi <span style="font-weight: normal; font-size: 0.7rem;">(opsional)</span></label>
                    <input type="password" name="password" class="sasi-form-control" minlength="8" placeholder="Kosongkan jika tidak ingin diubah">
                    <div class="form-hint">Kosongkan jika tidak ingin mengubah kata sandi</div>
                </div>

                <div class="sasi-modal-footer" style="margin-top: 1rem; padding: 0; border: none; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn-sasi-outline" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-sasi-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addUserModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addUserModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('addUserForm').reset();
}

function openEditModal(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('editUserModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editUserModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('editUserForm').reset();
}

// Close modal when clicking outside
document.getElementById('addUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>