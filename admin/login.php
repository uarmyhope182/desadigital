<?php
$page_title = 'Login Admin - Kelurahan Sasi';
$activePage = 'login';
$is_admin_page = true;
require_once __DIR__ . '/../config/db.php';

// Handle logout via GET parameter
if (get_get('logout') === '1') {
    logout_user();
    header('Location: ' . ADMIN_URL . '/login.php?logout=success');
    exit;
}

if (is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

$formErrors = get_flash('form_errors') ?? ($_SESSION['form_errors'] ?? []);
unset($_SESSION['form_errors']);
$successMessage = get_flash('success');
$infoMessage = get_flash('info') ?? (get_get('info') ?? null) ?? (get_get('logout') === 'success' ? 'Anda telah keluar dari sistem.' : null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kelurahan Sasi</title>
    
    <!-- Bright Rustic Village Interface Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&family=Bricolage+Grotesque:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="<?= ASSETS_URL ?>/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/vendor/bootstrap-icons/bootstrap-icons.css">
    
    <style>
        /* Bright Rustic Village Interface - Login Page */
        :root {
            --ghibli-bg-cream: #FDFBF7;
            --ghibli-bg-beige: #FAF6F0;
            --ghibli-bg-warm: #FFF8EF;
            --ghibli-sage: #9CAF88;
            --ghibli-sage-dark: #7A8F64;
            --ghibli-sage-light: #B5C4A3;
            --ghibli-peach: #F6C7A1;
            --ghibli-peach-dark: #F0B885;
            --ghibli-sky: #B9DCFF;
            --ghibli-text-dark: #3A312B;
            --ghibli-text-soft: #2F2F2F;
            --ghibli-text-light: #444444;
            --ghibli-border: #E8E0D5;
            --ghibli-shadow: rgba(58, 49, 43, 0.08);
            --ghibli-shadow-card: rgba(58, 49, 43, 0.12);
            --ghibli-error-bg: #FFF5F5;
            --ghibli-error-border: #E8C5C5;
            --ghibli-error-text: #8B5E5E;
            --ghibli-success-bg: #F0F7ED;
            --ghibli-success-border: #C5E0B4;
            --ghibli-info-bg: #F0F4FA;
            --ghibli-info-border: #C5D5E8;
        }

        body {
            background: linear-gradient(135deg, var(--ghibli-sky) 0%, #D4E8FF 50%, var(--ghibli-bg-beige) 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        /* Background Layers */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?= ASSETS_URL ?>/images/05-sky-cloud-layers.png') center top/cover no-repeat;
            opacity: 0.3;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?= ASSETS_URL ?>/images/rice-fields.png') center/cover no-repeat;
            opacity: 0.08;
            z-index: 0;
            pointer-events: none;
        }

        .font-headline {
            font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
        }

        /* Login Card - Bright Rustic Style */
        .sasi-login-card {
            border-radius: 32px !important;
            background: var(--ghibli-bg-cream) !important;
            border: 1px solid var(--ghibli-border) !important;
            box-shadow: 0 20px 40px -12px var(--ghibli-shadow-card) !important;
            backdrop-filter: blur(2px);
            position: relative;
            z-index: 2;
        }

        /* Login Icon */
        .sasi-login-icon {
            width: 80px;
            height: 80px;
            background: rgba(156, 175, 136, 0.12) !important;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            border: 2px solid var(--ghibli-sage-light);
        }

        .sasi-login-icon i {
            font-size: 2.5rem;
            color: var(--ghibli-sage) !important;
        }

        /* Form Inputs - Natural Style */
        .sasi-input-group {
            border-radius: 20px;
            overflow: hidden;
            border: 1.5px solid var(--ghibli-border);
            transition: all 0.2s ease;
            background: #FFFFFF;
        }

        .sasi-input-group:focus-within {
            border-color: var(--ghibli-sage);
            box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
        }

        .sasi-input-group-text {
            background-color: #FFFFFF !important;
            border: none !important;
            color: var(--ghibli-sage) !important;
            padding-left: 1rem !important;
            padding-right: 0.75rem !important;
        }

        .sasi-form-control {
            background-color: #FFFFFF !important;
            border: none !important;
            padding: 0.75rem 1rem !important;
            color: var(--ghibli-text-dark) !important;
            font-size: 0.95rem !important;
        }

        .sasi-form-control:focus {
            box-shadow: none !important;
            outline: none !important;
        }

        .sasi-form-control::placeholder {
            color: var(--ghibli-text-light);
            opacity: 0.5;
        }

        /* Login Button - Sage Green */
        .btn-sasi-login {
            background-color: var(--ghibli-sage) !important;
            border: none !important;
            border-radius: 40px !important;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: #FFFFFF !important;
            padding: 0.75rem 0 !important;
            transition: all 0.2s ease;
        }

        .btn-sasi-login:hover {
            transform: translateY(-2px);
            background-color: var(--ghibli-sage-dark) !important;
            box-shadow: 0 8px 20px -6px rgba(156, 175, 136, 0.4) !important;
        }

        /* Dashed Divider */
        .sasi-dashed-divider {
            border-top: 2px dashed var(--ghibli-border) !important;
            margin: 1.5rem 0;
            opacity: 0.7;
        }

        /* Alert Styles - Pastel */
        .alert-success-ghibli {
            background-color: var(--ghibli-success-bg);
            border-left: 4px solid var(--ghibli-sage);
            border-radius: 20px;
            color: var(--ghibli-text-dark);
        }

        .alert-info-ghibli {
            background-color: var(--ghibli-info-bg);
            border-left: 4px solid var(--ghibli-sage-light);
            border-radius: 20px;
            color: var(--ghibli-text-dark);
        }

        .alert-error-ghibli {
            background-color: var(--ghibli-error-bg);
            border-left: 4px solid var(--ghibli-error-border);
            border-radius: 20px;
            color: var(--ghibli-text-dark);
        }

        /* Demo Credentials Box - Small, not changing size */
        .demo-credentials {
            background: rgba(156, 175, 136, 0.08);
            border-radius: 16px;
            padding: 0.75rem;
            margin-top: 1rem;
            text-align: center;
            border: 1px solid var(--ghibli-border);
        }

        .demo-credentials p {
            font-size: 0.7rem;
            margin-bottom: 0.25rem;
            color: var(--ghibli-text-light);
            letter-spacing: 0.3px;
        }

        .demo-credentials .cred-value {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--ghibli-sage-dark);
            background: rgba(156, 175, 136, 0.12);
            padding: 0.15rem 0.4rem;
            border-radius: 8px;
            font-size: 0.7rem;
        }

        .demo-credentials .separator {
            margin: 0 0.25rem;
            color: var(--ghibli-border);
        }

        /* Floating Leaves Animation */
        .ghibli-leaf {
            position: fixed;
            background: rgba(156, 175, 136, 0.25);
            border-radius: 0 100% 0 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .leaf-1 {
            bottom: 5%;
            left: 5%;
            width: 60px;
            height: 60px;
            transform: rotate(15deg);
            animation: leafSway 8s ease-in-out infinite;
        }
        
        .leaf-2 {
            bottom: 15%;
            right: 8%;
            width: 45px;
            height: 45px;
            transform: rotate(-10deg);
            animation: leafSway 10s ease-in-out infinite;
        }
        
        .leaf-3 {
            top: 15%;
            left: 10%;
            width: 50px;
            height: 50px;
            transform: rotate(25deg);
            animation: leafSway 12s ease-in-out infinite;
        }
        
        @keyframes leafSway {
            0%, 100% { transform: rotate(15deg) translateY(0px); }
            50% { transform: rotate(25deg) translateY(-8px); }
        }

        /* Floating Clouds Animation */
        .ghibli-cloud {
            position: fixed;
            background: rgba(253, 251, 247, 0.7);
            border-radius: 100px;
            filter: blur(20px);
            pointer-events: none;
            z-index: 1;
        }
        
        .cloud-1 {
            top: 8%;
            right: -100px;
            width: 180px;
            height: 70px;
            animation: floatCloud1 30s linear infinite;
        }
        
        .cloud-2 {
            bottom: 20%;
            left: -120px;
            width: 220px;
            height: 85px;
            animation: floatCloud2 40s linear infinite;
            animation-delay: 5s;
        }
        
        @keyframes floatCloud1 {
            0% { transform: translateX(0px) translateY(0px); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translateX(-150px) translateY(-15px); opacity: 0; }
        }
        
        @keyframes floatCloud2 {
            0% { transform: translateX(0px) translateY(0px); opacity: 0; }
            10% { opacity: 0.4; }
            90% { opacity: 0.4; }
            100% { transform: translateX(200px) translateY(10px); opacity: 0; }
        }

        /* Form Label */
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ghibli-text-light);
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .ghibli-leaf, .ghibli-cloud {
                display: none;
            }
            .sasi-login-card .card-body {
                padding: 1.5rem !important;
            }
        }
    </style>
</head>
<body>
    <!-- Floating decorative leaves -->
    <div class="ghibli-leaf leaf-1"></div>
    <div class="ghibli-leaf leaf-2"></div>
    <div class="ghibli-leaf leaf-3"></div>

    <!-- Floating clouds -->
    <div class="ghibli-cloud cloud-1"></div>
    <div class="ghibli-cloud cloud-2"></div>

    <div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="card border-0 sasi-login-card">
                        <div class="card-body p-4 p-md-5">
                            <!-- Header Icon -->
                            <div class="text-center mb-4">
                                <div class="sasi-login-icon">
                                    <i class="bi bi-flower1"></i>
                                </div>
                                <h3 class="font-headline fw-bold mb-1" style="color: var(--ghibli-sage-dark);">Admin Panel</h3>
                                <p class="mb-0" style="color: var(--ghibli-text-light); font-size: 0.85rem;">Sistem Informasi Kelurahan Sasi</p>
                            </div>
                            
                            <!-- Success Alert -->
                            <?php if ($successMessage): ?>
                                <div class="alert alert-success-ghibli border-0 p-3 mb-4 d-flex align-items-center" role="alert">
                                    <i class="bi bi-check-circle-fill me-2 fs-5" style="color: var(--ghibli-sage);"></i>
                                    <span class="small fw-semibold"><?= h($successMessage) ?></span>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Info Alert / Logout -->
                            <?php if ($infoMessage): ?>
                                <div class="alert alert-info-ghibli border-0 p-3 mb-4 d-flex align-items-center" role="alert">
                                    <i class="bi bi-info-circle-fill me-2 fs-5" style="color: var(--ghibli-sage-light);"></i>
                                    <span class="small fw-semibold"><?= h($infoMessage) ?></span>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Error Alert -->
                            <?php if (!empty($formErrors)): ?>
                                <div class="alert alert-error-ghibli border-0 p-4 mb-4" role="alert">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5" style="color: var(--ghibli-error-border);"></i>
                                        <strong class="font-headline" style="color: var(--ghibli-text-dark);">Login Gagal</strong>
                                    </div>
                                    <ul class="mb-0 small ps-3" style="color: var(--ghibli-text-light);">
                                        <?php foreach ($formErrors as $error): ?>
                                            <li><?= h($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Login Form -->
                            <form action="<?= PROSES_URL ?>/user.php" method="post">
                                <input type="hidden" name="action" value="login">
                                
                                <!-- Username Field -->
                                <div class="mb-3">
                                    <label for="login_username" class="form-label-custom">Nama Pengguna</label>
                                    <div class="input-group sasi-input-group">
                                        <span class="input-group-text sasi-input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" id="login_username" class="form-control sasi-form-control" name="username" placeholder="Masukkan nama pengguna" required autofocus>
                                    </div>
                                </div>
                                
                                <!-- Password Field -->
                                <div class="mb-4">
                                    <label for="login_password" class="form-label-custom">Kata Sandi</label>
                                    <div class="input-group sasi-input-group">
                                        <span class="input-group-text sasi-input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" id="login_password" class="form-control sasi-form-control" name="password" placeholder="Masukkan kata sandi" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-sasi-login w-100 fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Aplikasi
                                </button>
                            </form>
                            
                            <!-- Demo Credentials - Username & Password -->
                            <div class="demo-credentials">
                                <p class="mb-1">Demo Akun</p>
                                <p class="mb-0">
                                    <span class="cred-value">admin</span>
                                    <span class="separator">/</span>
                                    <span class="cred-value">admin123</span>
                                </p>
                            </div>
                            
                            <div class="sasi-dashed-divider"></div>
                            
                            <!-- Help & Back Link -->
                            <div class="text-center">
                                <p class="small mb-2" style="color: var(--ghibli-text-light);">
                                    <i class="bi bi-patch-question-fill me-1" style="color: var(--ghibli-sage);"></i>
                                    Lupa kata sandi? Hubungi Staf Kelurahan.
                                </p>
                                <a href="<?= PUBLIC_URL ?>/index.php" class="text-decoration-none small fw-semibold" style="color: var(--ghibli-sage);">
                                    <i class="bi bi-arrow-left-short fs-5 align-middle"></i>
                                    Kembali ke Beranda Publik
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Copyright -->
                    <div class="text-center mt-4">
                        <p class="small mb-0" style="color: rgba(58, 49, 43, 0.6);">
                            &copy; <?= date('Y') ?> Kelurahan Sasi — Melayani dengan hati, bekerja dengan integritas
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= ASSETS_URL ?>/vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>