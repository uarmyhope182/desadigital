<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Profil & Ganti Password';
$activePage = 'profile';
$is_admin_page = true;
$current_user = current_user();

$successMessage = get_flash('success');
$infoMessage = get_flash('info');
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Profil & Ganti Password -->
<!-- Background Layers -->
<div class="page-bg-sky"></div>
<div class="page-bg-rice-fields"></div>
<div class="page-bg-atmosphere"></div>

<style>
/* Bright Rustic Village Interface - Variables */
/* DARKER TEXT FOR BETTER READABILITY */
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
    --page-info-bg: #F0F4FA;
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
    padding-left: 3rem;
    padding-right: 1.5rem;
}

/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
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
    background: var(--page-bg-cream) !important;
    border: 1px solid var(--page-border) !important;
    border-radius: 24px !important;
    box-shadow: 0 4px 12px var(--page-shadow) !important;
    transition: all 0.3s ease !important;
    overflow: hidden;
}

.sasi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px var(--page-shadow-hover) !important;
    border-color: var(--page-sage-light) !important;
}

/* Profile Avatar */
.profile-avatar {
    width: 100px;
    height: 100px;
    background: rgba(156, 175, 136, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    border: 2px solid var(--page-sage-light);
}

.profile-avatar i {
    font-size: 2.5rem;
    color: var(--page-sage);
}

/* Badges */
.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 1rem;
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
    background-color: #FFFFFF !important;
    border: 1.5px solid var(--page-border) !important;
    border-radius: 16px !important;
    padding: 0.7rem 1rem !important;
    color: #1A1512 !important;
    font-weight: 500;
    transition: all 0.2s ease;
    width: 100%;
}

.sasi-form-control:focus {
    border-color: var(--page-sage) !important;
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2) !important;
    outline: none !important;
}

.form-label-custom {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.5rem;
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

/* Buttons */
.btn-sasi-primary {
    background-color: var(--page-sage) !important;
    color: #FFFFFF !important;
    border: none !important;
    border-radius: 40px !important;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s ease;
}

.btn-sasi-primary:hover {
    background-color: var(--page-sage-dark) !important;
    transform: translateY(-1px);
}

.btn-sasi-outline {
    background: transparent !important;
    color: #1A1512 !important;
    border: 1.5px solid var(--page-border) !important;
    border-radius: 40px !important;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s ease;
}

.btn-sasi-outline:hover {
    background: rgba(156, 175, 136, 0.1) !important;
    border-color: var(--page-sage) !important;
    color: #7A8F64 !important;
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
    gap: 0.75rem;
}

.alert-info {
    background-color: var(--page-info-bg);
    border-left: 4px solid var(--page-sage-light);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error {
    background-color: var(--page-error-bg);
    border-left: 4px solid var(--page-error-border);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-icon {
    font-size: 1.25rem;
    font-weight: 700;
}

.alert-content {
    flex: 1;
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

/* Info Card */
.info-section {
    background: rgba(156, 175, 136, 0.08);
    border-radius: 20px;
    padding: 1.25rem;
    margin-top: 1rem;
}

.info-title {
    font-family: 'Poppins', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 1rem;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    position: relative;
    padding-left: 1.25rem;
    margin-bottom: 0.65rem;
    font-size: 0.75rem;
    color: #2C241E;
    line-height: 1.5;
    font-weight: 500;
}

.info-list li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 6px;
    width: 6px;
    height: 6px;
    background-color: var(--page-sage);
    border-radius: 50%;
}

/* Tips Card */
.tips-card {
    background: rgba(246, 199, 161, 0.12);
    border-radius: 20px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}

.tips-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.75rem;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    position: relative;
    padding-left: 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.7rem;
    color: #2C241E;
    line-height: 1.5;
    font-weight: 500;
}

.tips-list li::before {
    content: "•";
    position: absolute;
    left: 0;
    color: var(--page-sage);
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .page-container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .btn-sasi-primary, .btn-sasi-outline {
        width: 100%;
        text-align: center;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
    }
}
</style>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Profil & Pengaturan Akun</h1>
        <p class="page-subtitle">Kelola informasi administratif dan tingkat keamanan akun Anda</p>
    </div>

    <div class="row g-4">
        <!-- Left Column: Profile Summary -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="sasi-card text-center mb-4">
                <div class="card-body p-4">
                    <div class="profile-avatar">
                        <i class="bi bi-flower1"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color: #1A1512;"><?= h($current_user['full_name']) ?></h5>
                    <p class="small mb-3" style="color: #2C241E; font-weight: 500;">@<?= h($current_user['username']) ?></p>
                    <div class="d-flex justify-content-center gap-2">
                        <?php if ($current_user['role'] === 'admin'): ?>
                            <span class="role-badge role-admin">
                                <i class="bi bi-shield-fill"></i> Administrator
                            </span>
                        <?php else: ?>
                            <span class="role-badge role-operator">
                                <i class="bi bi-person-fill"></i> Operator
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Account Details Card -->
            <div class="sasi-card">
                <div class="card-header p-4 pb-0 border-0">
                    <h6 class="fw-bold mb-0" style="color: #1A1512;">
                        <i class="bi bi-info-circle-fill me-2"></i>Informasi Akun
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="mb-3">
                        <label class="small d-block mb-1" style="color: #2C241E; font-weight: 600;">Username Login</label>
                        <code class="font-monospace fw-bold" style="color: #7A8F64;"><?= h($current_user['username']) ?></code>
                    </div>
                    <div class="mb-3">
                        <label class="small d-block mb-1" style="color: #2C241E; font-weight: 600;">Alamat Email</label>
                        <p class="mb-0 fw-semibold" style="color: #1A1512;"><?= h($current_user['email']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="small d-block mb-1" style="color: #2C241E; font-weight: 600;">Hak Akses</label>
                        <p class="mb-0 fw-semibold" style="color: #1A1512;">
                            <?= $current_user['role'] === 'admin' ? 'Administrator - Akses Penuh' : 'Operator - Akses Terbatas' ?>
                        </p>
                    </div>
                    <div>
                        <label class="small d-block mb-1" style="color: #2C241E; font-weight: 600;">Terdaftar Sejak</label>
                        <p class="mb-0 fw-semibold" style="color: #1A1512;"><?= date('d M Y', strtotime($current_user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Change Password Form -->
        <div class="col-lg-8">
            <div class="sasi-card">
                <div class="card-header p-4 pb-0 border-0">
                    <h5 class="fw-bold mb-0" style="color: #1A1512;">
                        <i class="bi bi-lock-fill me-2"></i>Ubah Password Akun
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-4 small" style="color: #2C241E; font-weight: 500;">
                        Lakukan penggantian kata sandi secara berkala demi menjaga keamanan akun
                    </p>

                    <!-- Success Alert -->
                    <?php if ($successMessage): ?>
                        <div class="alert-success">
                            <div class="alert-icon">✓</div>
                            <div class="alert-content">
                                <div class="alert-title">Berhasil</div>
                                <div class="alert-message"><?= h($successMessage) ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Info Alert -->
                    <?php if ($infoMessage): ?>
                        <div class="alert-info">
                            <div class="alert-icon">ℹ</div>
                            <div class="alert-content">
                                <div class="alert-title">Informasi</div>
                                <div class="alert-message"><?= h($infoMessage) ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Alert -->
                    <?php if (!empty($formErrors)): ?>
                        <div class="alert-error">
                            <div class="alert-icon">!</div>
                            <div class="alert-content">
                                <div class="alert-title">Kendala Perubahan Password</div>
                                <ul class="error-list">
                                    <?php foreach ($formErrors as $error): ?>
                                        <li><?= h($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Change Password Form -->
                    <form action="<?= PROSES_URL ?>/user.php" method="post">
                        <?php require_once __DIR__ . '/../config/csrf.php'; echo csrf_input(); ?>
                        <input type="hidden" name="action" value="change_password">

                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label-custom">Password Saat Ini <span class="required">*</span></label>
                            <input type="password" class="sasi-form-control" id="current_password" name="current_password" placeholder="Masukkan password yang saat ini aktif" required>
                            <div class="form-hint">Konfirmasi sandi lama untuk validasi kepemilikan akun</div>
                        </div>

                        <!-- New Password & Confirm -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="new_password" class="form-label-custom">Password Baru <span class="required">*</span></label>
                                <input type="password" class="sasi-form-control" id="new_password" name="new_password" placeholder="Masukkan password baru" required minlength="8">
                                <div class="form-hint">Minimal 8 karakter</div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label-custom">Konfirmasi Password Baru <span class="required">*</span></label>
                                <input type="password" class="sasi-form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required>
                                <div class="form-hint">Isian harus sama persis</div>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="tips-card">
                            <div class="tips-title">
                                <i class="bi bi-lightbulb me-2"></i>Panduan Kata Sandi Aman
                            </div>
                            <ul class="tips-list">
                                <li>Campurkan huruf besar, huruf kecil, angka, dan simbol unik</li>
                                <li>Hindari penggunaan nama, tanggal lahir, atau kata umum yang mudah ditebak</li>
                                <li>Jangan pernah membagikan kata sandi Anda kepada orang lain</li>
                            </ul>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-sasi-primary" onclick="return confirm('Apakah Anda yakin ingin mengganti password akun Anda?')">
                                <i class="bi bi-check-circle-fill me-2"></i>Simpan Password
                            </button>
                            <button type="reset" class="btn-sasi-outline">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Isian
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Info Card -->
            <div class="info-section">
                <div class="info-title">
                    <i class="bi bi-shield-check-fill me-2"></i>Prosedur Pengamanan Sistem
                </div>
                <ul class="info-list">
                    <li>Semua kata sandi disimpan menggunakan enkripsi Bcrypt yang aman di database</li>
                    <li>Sesi login akan kedaluwarsa secara otomatis jika tidak ada aktivitas dalam waktu tertentu</li>
                    <li>Gunakan kombinasi karakter yang kuat dan unik untuk setiap akun</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>