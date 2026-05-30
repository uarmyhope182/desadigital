<?php
require_once __DIR__ . '/../config/db.php';

$page_title = $page_title ?? 'Kelurahan Sasi';
$activePage = $activePage ?? 'home';
$is_admin = $is_admin_page ?? false;
$is_login_page = $activePage === 'login';
$show_admin_layout = $is_admin && !$is_login_page;
$current_user = is_logged_in() ? current_user() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Kelurahan Sasi - Layanan Online dan Informasi Kelurahan">
    <meta name="theme-color" content="#627e3f">
    <title><?= h($page_title) ?> - Kelurahan Sasi</title>
    
    <!-- Bright Rustic Village Interface Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700;800&family=Bricolage+Grotesque:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="<?= ASSETS_URL ?>/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/custom.css?v=<?= filemtime(__DIR__ . '/../assets/css/custom.css') ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/ghibli-animations.css?v=<?= filemtime(__DIR__ . '/../assets/css/ghibli-animations.css') ?>">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/_overrides.css?v=<?= filemtime(__DIR__ . '/../assets/css/_overrides.css') ?>">
    
    <style>
        /* ============================================
           BRIGHT RUSTIC VILLAGE INTERFACE
           Studio Ghibli Style - Global Header & Navbar
           ============================================ */

        /* CSS Root Variables */
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
            --ghibli-sky-light: #D4E8FF;
            --ghibli-text-dark: #2F2F2F;
            --ghibli-text-soft: #3A312B;
            --ghibli-text-light: #444444;
            --ghibli-border: #E8E0D5;
            --ghibli-border-soft: #F0E8DD;
            --ghibli-shadow: rgba(58, 49, 43, 0.06);
            --ghibli-shadow-hover: rgba(58, 49, 43, 0.1);
            --ghibli-shadow-soft: rgba(58, 49, 43, 0.04);
            --ghibli-error: #D4A5A5;
            --ghibli-error-soft: #E8C5C5;
            --ghibli-success: #A8C3A0;
            --ghibli-warning: #F2D0A4;
            --ghibli-transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
        }
        
        /* Base Body Styles - dark text on soft background */
        body {
            background: var(--ghibli-beige);
            color: var(--ghibli-text-soft);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            position: relative;
            min-height: 100vh;
            line-height: 1.5;
            font-weight: 400;
        }
        
        /* Atmospheric Background Layers - fixed, does not move on scroll */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                180deg,
                var(--ghibli-sky) 0%,
                var(--ghibli-sky-light) 35%,
                var(--ghibli-beige) 70%,
                var(--ghibli-cream) 100%
            );
            z-index: -3;
            pointer-events: none;
        }
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle at 10% 20%, rgba(156, 175, 136, 0.03) 0%, transparent 50%);
            z-index: -2;
            pointer-events: none;
        }
        
        /* Floating Clouds Decoration - stay in place, animate locally */
        .ghibli-cloud-decor {
            position: fixed;
            pointer-events: none;
            z-index: -1;
            opacity: 0.35;
        }
        
        .ghibli-cloud-1 {
            top: 80px;
            left: -50px;
            width: 180px;
            animation: floatCloudLocal 45s ease-in-out infinite;
        }
        
        .ghibli-cloud-2 {
            bottom: 100px;
            right: -30px;
            width: 140px;
            animation: floatCloudLocalReverse 38s ease-in-out infinite;
        }
        
        .ghibli-cloud-3 {
            top: 200px;
            right: 15%;
            width: 100px;
            animation: floatCloudLocalMedium 30s ease-in-out infinite;
        }
        
        @keyframes floatCloudLocal {
            0%, 100% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(25px) translateY(10px); }
        }
        
        @keyframes floatCloudLocalReverse {
            0%, 100% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(-20px) translateY(8px); }
        }
        
        @keyframes floatCloudLocalMedium {
            0%, 100% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(15px) translateY(-8px); }
        }
        
        /* ============================================
           NAVBAR - BRIGHT RUSTIC STYLE
           ============================================ */
        .ghibli-navbar {
            background: rgba(253, 251, 247, 0.96) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--ghibli-border);
            padding: 1rem 0 !important;
            transition: var(--ghibli-transition);
            box-shadow: 0 2px 12px var(--ghibli-shadow);
        }
        
        .navbar-brand {
            font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.6rem !important;
            letter-spacing: -0.02em;
            color: var(--ghibli-text-dark) !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--ghibli-transition);
        }
        
        .navbar-brand:hover {
            transform: translateY(-1px);
        }
        
        .navbar-brand i {
            color: var(--ghibli-sage);
            font-size: 1.7rem !important;
            transition: var(--ghibli-transition);
        }
        
        .navbar-brand:hover i {
            color: var(--ghibli-sage-dark);
            transform: scale(1.02);
        }
        
        @media (min-width: 992px) {
            .navbar-collapse {
                display: flex !important;
                flex-basis: auto;
            }
            
            .navbar-nav-center-wrapper {
                flex: 1;
                display: flex;
                justify-content: center;
            }
            
            .navbar-nav-center-wrapper .navbar-nav {
                gap: 1.25rem !important;
            }
        }
        
        .ghibli-navbar .nav-link {
            color: var(--ghibli-text-light) !important;
            font-weight: 500;
            font-size: 1rem !important;
            padding: 0.6rem 1.25rem !important;
            border-radius: 60px;
            transition: var(--ghibli-transition);
            font-family: 'Poppins', sans-serif;
            margin: 0 6px;
        }
        
        .ghibli-navbar .nav-link i {
            display: none;
        }
        
        .ghibli-navbar .nav-link:hover {
            color: var(--ghibli-sage-dark) !important;
            background-color: rgba(156, 175, 136, 0.1);
            transform: translateY(-1px);
        }
        
        .ghibli-navbar .nav-link.active {
            color: #FFFFFF !important;
            background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
            box-shadow: 0 3px 10px rgba(156, 175, 136, 0.35);
        }
        
        .ghibli-navbar { 
            overflow: visible; 
            z-index: 1200 !important;
            position: relative !important;
        }
        .navbar-right-icons, .dropdown { overflow: visible; }
        .dropdown-menu { z-index: 1200; }
        
        .btn-icon-only {
            border-radius: 50% !important;
            width: 42px;
            height: 42px;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 1.5px solid var(--ghibli-border);
            background: rgba(253, 251, 247, 0.9);
            color: var(--ghibli-text-dark);
            transition: var(--ghibli-transition);
            margin-left: 0.5rem;
        }
        
        .btn-icon-only i {
            margin: 0 !important;
            font-size: 1.2rem;
            color: var(--ghibli-text-dark);
        }
        
        .btn-icon-only:hover {
            background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
            color: #FFFFFF;
            border-color: var(--ghibli-sage);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(156, 175, 136, 0.3);
        }
        
        .btn-icon-only:hover i {
            color: #FFFFFF;
        }
        
        .dropdown-toggle-icon-only {
            background: transparent;
            border: none;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--ghibli-transition);
            color: var(--ghibli-text-dark);
            cursor: pointer;
        }
        
        .dropdown-toggle-icon-only i {
            font-size: 1.3rem;
            color: var(--ghibli-text-dark);
        }
        
        .dropdown-toggle-icon-only:hover {
            background: rgba(156, 175, 136, 0.1);
            transform: translateY(-1px);
        }
        
        .dropdown-toggle-icon-only:hover i {
            color: var(--ghibli-sage-dark);
        }
        
        .dropdown-toggle-icon-only::after {
            display: none;
        }
        
        .dropdown-menu {
            background: var(--ghibli-cream);
            border: 1px solid var(--ghibli-border);
            border-radius: 20px;
            box-shadow: 0 12px 28px var(--ghibli-shadow-hover);
            padding: 0.5rem;
            backdrop-filter: blur(8px);
            margin-top: 0.5rem;
            position: absolute;
            right: 0;
            left: auto;
            display: none;
        }
        
        .dropdown-menu.show {
            display: block !important;
            z-index: 1200;
        }
        
        .navbar-right-icons .dropdown-menu {
            min-width: 200px;
        }
        
        .dropdown-item {
            border-radius: 14px;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            color: var(--ghibli-text-soft);
            transition: var(--ghibli-transition);
            font-family: 'Inter', sans-serif;
        }
        
        .dropdown-item i {
            width: 1.25rem;
            color: var(--ghibli-sage);
            transition: var(--ghibli-transition);
            margin-right: 0.5rem;
        }
        
        .dropdown-item:hover {
            background-color: rgba(156, 175, 136, 0.1);
            color: var(--ghibli-sage-dark);
            transform: translateX(2px);
        }
        
        .dropdown-item:hover i {
            color: var(--ghibli-sage-dark);
        }
        
        .dropdown-item.text-danger:hover {
            background-color: rgba(212, 165, 165, 0.12);
            color: var(--ghibli-error) !important;
        }
        
        .dropdown-item.text-danger:hover i {
            color: var(--ghibli-error);
        }
        
        .dropdown-divider {
            border-top-color: var(--ghibli-border);
            margin: 0.4rem 0;
        }
        
        .navbar-right-icons {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            position: relative;
            z-index: 1200;
        }
        
        .ghibli-navbar .dropdown {
            position: relative;
            z-index: 1200;
        }
        
        .ghibli-navbar .dropdown-menu.show {
            position: absolute;
            z-index: 10000 !important;
        }
        
        .admin-layout,
        .dashboard-container,
        .page-container {
            overflow: visible !important;
            z-index: auto !important;
        }
        
        /* ============================================
           BOTTOM NAVIGATION - MOBILE
           ============================================ */
        .ghibli-bottom-nav {
            position: fixed;
            bottom: 16px;
            left: 16px;
            right: 16px;
            background: rgba(253, 251, 247, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--ghibli-border);
            border-radius: 60px;
            box-shadow: 0 8px 24px var(--ghibli-shadow-hover);
            z-index: 1030;
            height: 62px;
            display: none;
            align-items: center;
            justify-content: space-around;
            padding: 0 6px;
        }
        
        .ghibli-bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--ghibli-text-light) !important;
            font-size: 0.65rem;
            font-weight: 500;
            transition: var(--ghibli-transition);
            flex: 1;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            border-radius: 50px;
            gap: 2px;
        }
        
        .ghibli-bottom-nav-item i {
            font-size: 1.25rem;
            transition: var(--ghibli-transition);
            color: var(--ghibli-text-light);
        }
        
        .ghibli-bottom-nav-item span {
            font-size: 0.6rem;
            letter-spacing: 0.3px;
        }
        
        .ghibli-bottom-nav-item.active {
            color: var(--ghibli-sage-dark) !important;
            background: linear-gradient(135deg, rgba(156, 175, 136, 0.12) 0%, rgba(156, 175, 136, 0.06) 100%);
        }
        
        .ghibli-bottom-nav-item.active i {
            color: var(--ghibli-sage) !important;
            transform: translateY(-1px);
        }
        
        .ghibli-bottom-nav-item:active {
            transform: scale(0.96);
        }
        
        .main-content-wrapper {
            min-height: calc(100vh - 70px);
            position: relative;
            z-index: 1;
        }
        
        .admin-layout {
            display: flex;
            width: 100%;
            min-height: calc(100vh - 64px);
            background: transparent;
        }
        
        .admin-main {
            flex: 1;
            background: transparent;
            min-height: calc(100vh - 64px);
            transition: var(--ghibli-transition);
        }
        
        .table {
            background: var(--ghibli-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--ghibli-border);
            box-shadow: 0 2px 8px var(--ghibli-shadow-soft);
        }
        
        .table > :not(caption) > * > * {
            background-color: transparent;
            border-bottom-color: var(--ghibli-border);
        }
        
        .table thead th {
            background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
            color: #FFFFFF;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            border-bottom: none;
            padding: 14px 16px;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
        }
        
        .table thead th:first-child {
            border-radius: 20px 0 0 0;
        }
        
        .table thead th:last-child {
            border-radius: 0 20px 0 0;
        }
        
        .table tbody td {
            padding: 12px 16px;
            border-bottom-color: var(--ghibli-border);
            font-size: 0.85rem;
            color: var(--ghibli-text-light);
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: var(--ghibli-transition);
        }
        
        .table tbody tr:hover {
            background: rgba(156, 175, 136, 0.05);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .card-ghibli {
            background: var(--ghibli-cream);
            border: 1px solid var(--ghibli-border);
            border-radius: 28px;
            box-shadow: 0 6px 18px var(--ghibli-shadow);
            transition: var(--ghibli-transition);
            overflow: hidden;
        }
        
        .card-ghibli:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px var(--ghibli-shadow-hover);
            border-color: var(--ghibli-sage-light);
        }
        
        .card-ghibli .card-header {
            background: rgba(156, 175, 136, 0.08);
            border-bottom: 1px solid var(--ghibli-border);
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }
        
        .card-ghibli .card-body {
            padding: 1.5rem;
        }
        
        .btn-ghibli-primary {
            background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
            border: none;
            border-radius: 40px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            color: white;
            transition: var(--ghibli-transition);
            box-shadow: 0 2px 6px rgba(156, 175, 136, 0.3);
        }
        
        .btn-ghibli-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(156, 175, 136, 0.4);
            color: white;
        }
        
        .btn-ghibli-secondary {
            background: transparent;
            border: 1.5px solid var(--ghibli-sage);
            border-radius: 40px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            color: var(--ghibli-sage-dark);
            transition: var(--ghibli-transition);
        }
        
        .btn-ghibli-secondary:hover {
            background: var(--ghibli-sage);
            color: white;
            transform: translateY(-2px);
        }
        
        .form-control, .form-select {
            background: var(--ghibli-warm);
            border: 1px solid var(--ghibli-border);
            border-radius: 16px;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            color: var(--ghibli-text-soft);
            transition: var(--ghibli-transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--ghibli-sage);
            box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
            background: var(--ghibli-cream);
            outline: none;
        }
        
        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--ghibli-text-dark);
            margin-bottom: 0.5rem;
            font-family: 'Poppins', sans-serif;
        }
        
        .app-login-page {
            min-height: 100vh;
            background: linear-gradient(145deg, var(--ghibli-sky) 0%, var(--ghibli-beige) 100%);
            position: relative;
        }
        
        .app-login-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('<?= ASSETS_URL ?>/images/rice-fields.png');
            background-repeat: repeat;
            background-size: 200px;
            opacity: 0.06;
            pointer-events: none;
        }
        
        .modal-content {
            background: var(--ghibli-cream);
            border: 1px solid var(--ghibli-border);
            border-radius: 28px;
            box-shadow: 0 20px 40px var(--ghibli-shadow-hover);
        }
        
        .modal-header {
            border-bottom-color: var(--ghibli-border);
            background: rgba(156, 175, 136, 0.05);
            border-radius: 28px 28px 0 0;
            padding: 1.25rem 1.5rem;
        }
        
        .modal-footer {
            border-top-color: var(--ghibli-border);
            padding: 1rem 1.5rem;
        }
        
        .alert {
            border-radius: 20px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .alert-success {
            background: rgba(168, 195, 160, 0.15);
            border-left: 4px solid var(--ghibli-success);
            color: var(--ghibli-text-soft);
        }
        
        .alert-danger {
            background: rgba(212, 165, 165, 0.12);
            border-left: 4px solid var(--ghibli-error);
            color: var(--ghibli-text-soft);
        }
        
        .alert-warning {
            background: rgba(242, 208, 164, 0.15);
            border-left: 4px solid var(--ghibli-warning);
            color: var(--ghibli-text-soft);
        }
        
        .pagination {
            gap: 0.25rem;
        }
        
        .page-link {
            background: var(--ghibli-cream);
            border: 1px solid var(--ghibli-border);
            border-radius: 12px !important;
            color: var(--ghibli-text-soft);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--ghibli-transition);
        }
        
        .page-link:hover {
            background: var(--ghibli-sage-light);
            border-color: var(--ghibli-sage-light);
            color: white;
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--ghibli-sage) 0%, var(--ghibli-sage-dark) 100%);
            border-color: var(--ghibli-sage);
            color: white;
        }
        
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 90px !important;
            }
            
            .ghibli-bottom-nav {
                display: flex;
            }
            
            .ghibli-desktop-menu {
                display: none !important;
            }
            
            .ghibli-navbar .navbar-brand {
                font-size: 1.3rem !important;
            }
            
            .admin-main {
                padding: 1rem !important;
            }
        }
        
        @media (max-width: 768px) {
            .card-ghibli .card-body {
                padding: 1rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 10px 12px;
            }
        }
        
        @media (max-width: 576px) {
            .ghibli-bottom-nav {
                bottom: 12px;
                left: 12px;
                right: 12px;
                height: 56px;
            }
            
            .ghibli-bottom-nav-item i {
                font-size: 1.1rem;
            }
            
            .ghibli-bottom-nav-item span {
                font-size: 0.55rem;
            }
        }
        
        @media (max-width: 480px) {
            .btn-icon-only {
                width: 36px;
                height: 36px;
            }
        }

        @media (max-width: 1199.98px) {
            .admin-layout {
                flex-direction: column;
            }
            
            .sidebar,
            .admin-sidebar {
                flex: 0 0 100%;
                width: 100%;
                min-height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid var(--ghibli-border);
            }
            
            .admin-main {
                padding: 1rem !important;
            }
        }

        @media (max-width: 991.98px) {
            .ghibli-navbar .nav-link {
                padding: 0.55rem 0.9rem !important;
                font-size: 0.95rem !important;
            }
            
            .navbar-brand {
                font-size: 1.2rem !important;
            }
        }

        @media (max-width: 767.98px) {
            .card-ghibli .card-body,
            .card .card-body {
                padding: 1rem;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .ghibli-navbar {
                padding: 0.8rem 0.75rem !important;
            }
        }

        @media (max-width: 576px) {
            .sidebar,
            .admin-sidebar {
                border-bottom: 1px solid var(--ghibli-border);
            }

            .card,
            .card-ghibli,
            .table-responsive,
            .modal-content {
                width: 100%;
                max-width: 100%;
            }
        }

        img,
        svg,
        iframe,
        video,
        embed,
        object {
            max-width: 100%;
            height: auto;
        }

        table {
            width: 100%;
            min-width: 0;
            table-layout: auto;
        }

        td,
        th {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .page-container,
        .main-content-wrapper,
        .admin-main {
            width: 100%;
            box-sizing: border-box;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--ghibli-beige);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--ghibli-sage-light);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--ghibli-sage);
        }
        
        ::selection {
            background: rgba(156, 175, 136, 0.25);
            color: var(--ghibli-text-dark);
        }
        
        .text-ghibli-sage {
            color: var(--ghibli-sage) !important;
        }
        
        .bg-ghibli-warm {
            background: var(--ghibli-warm) !important;
        }
        
        .border-ghibli {
            border-color: var(--ghibli-border) !important;
        }
        
        .shadow-ghibli {
            box-shadow: 0 8px 20px var(--ghibli-shadow) !important;
        }
        
        html {
            scroll-behavior: smooth;
        }

        a,
        a:hover,
        a:focus,
        a:active,
        .nav-link,
        .nav-link:hover,
        .nav-link:focus,
        .nav-link:active,
        .dropdown-item,
        .dropdown-item:hover,
        .sidebar-nav-link,
        .sidebar-nav-link:hover,
        .sidebar-nav-link:focus,
        .card-read-more,
        .card-read-more:hover,
        .btn-link,
        .btn-link:hover,
        .text-decoration-none,
        *[class*="read-more"],
        *[class*="nav-link"] {
            text-decoration: none !important;
        }

        .sidebar-offcanvas-body .sidebar-nav-link::after {
            display: none !important;
            content: none !important;
        }

        .section-header,
        .section-header::after,
        .section-header::before,
        .ghibli-section-header::after,
        .ghibli-section-header::before,
        .card-header::after,
        .table-card-header::after,
        .detail-header::after,
        .sidebar-category::after,
        .sidebar-category::before,
        .footer-title::after,
        .ghibli-hero__divider,
        .detail-divider,
        .timeline-divider {
            border-bottom: none !important;
            border-top: none !important;
        }

        .sidebar-divider,
        .footer-divider,
        .ghibli-divider,
        .form-divider {
            border-bottom: none !important;
        }

        .section-header {
            position: relative;
        }

        .section-header::after {
            display: none !important;
        }

        .ghibli-card:hover,
        .stat-card:hover,
        .table-card:hover {
            transform: translateY(-3px);
        }

        * {
            text-decoration: none !important;
        }

        a:link,
        a:visited,
        a:hover,
        a:active {
            text-decoration: none !important;
        }

        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        .dashboard-title,
        .card-title,
        .modal-title,
        .offcanvas-title,
        .footer-title,
        .brand-name {
            border-bottom: none !important;
        }

        .status-badge,
        .status-badge:hover,
        .status-badge:focus {
            text-decoration: none !important;
        }
    </style>
</head>
<?php
$request_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$script_name = basename($request_path);
$folder_name = basename(dirname($request_path));
$page_key = trim($folder_name . '/' . $script_name, '/');
$bg_map = [
    'public/index.php' => 'bg-image',
    'public/profil_desa.php' => 'bg-image',
    'public/layanan_surat.php' => 'bg-cream',
    'public/permohonan_saya.php' => 'bg-cream',
    'public/pengumuman.php' => 'bg-cream',
    'public/lacak_permohonan.php' => 'bg-cream',
    'admin/dashboard.php' => 'bg-cream',
    'admin/penduduk.php' => 'bg-cream',
    'admin/jenis_surat.php' => 'bg-cream',
    'admin/kelola_permohonan.php' => 'bg-cream',
    'admin/users.php' => 'bg-cream',
    'admin/pengumuman.php' => 'bg-cream',
    'admin/laporan.php' => 'bg-cream',
    'admin/profile.php' => 'bg-cream',
    'admin/login.php' => 'bg-cream',
];
$body_class = $bg_map[$page_key] ?? '';
$page_class = 'page-' . preg_replace('/[^a-z0-9\-]+/i', '-', str_replace('.php', '', $script_name));
$body_classes = array_filter([$body_class, $page_class]);
if (!empty($body_classes)) {
    echo '<body class="' . implode(' ', $body_classes) . '">';
} else {
    echo '<body>';
}
?>

<?php if ($is_login_page): ?>
    <div class="app-login-page">
        <div class="ghibli-cloud-decor ghibli-cloud-1">
            <svg width="180" height="100" viewBox="0 0 180 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M30 80 Q35 45 60 45 Q70 25 95 25 Q120 25 130 45 Q155 45 160 65 Q165 85 150 85 L30 85 Z" fill="#FFFFFF" fill-opacity="0.6"/>
                <path d="M50 75 Q55 55 70 55 Q80 45 95 45 Q110 45 115 55 Q130 55 130 68 Q130 80 115 80 L50 80 Z" fill="#FFFFFF" fill-opacity="0.4"/>
            </svg>
        </div>
        <div class="ghibli-cloud-decor ghibli-cloud-2">
            <svg width="140" height="80" viewBox="0 0 140 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 65 Q25 40 45 40 Q55 25 75 25 Q95 25 105 40 Q125 40 130 55 Q135 70 120 70 L20 70 Z" fill="#FFFFFF" fill-opacity="0.5"/>
            </svg>
        </div>
<?php elseif (!$show_admin_layout): ?>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top ghibli-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= PUBLIC_URL ?>/index.php">
                <i class="bi bi-flower1"></i>
                <span>Kelurahan Sasi</span>
            </a>

            <div class="collapse navbar-collapse" id="navbarMain">
                <div class="navbar-nav-center-wrapper">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="<?= PUBLIC_URL ?>/index.php">
                                Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'profil' ? 'active' : '' ?>" href="<?= PUBLIC_URL ?>/profil_desa.php">
                                Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'layanan' ? 'active' : '' ?>" href="<?= PUBLIC_URL ?>/layanan_surat.php">
                                Layanan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'lacak' ? 'active' : '' ?>" href="<?= PUBLIC_URL ?>/lacak_permohonan.php">
                                Lacak
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="navbar-right-icons">
                <?php if ($current_user): ?>
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle-icon-only" data-bs-toggle="dropdown" aria-expanded="false" title="Akun Saya">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= ADMIN_URL ?>/profile.php">
                                    <i class="bi bi-gear"></i>Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= PUBLIC_URL ?>/index.php?logout=1" onclick="return confirm('Yakin ingin keluar dari sistem?')">
                                    <i class="bi bi-box-arrow-right"></i>Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= ADMIN_URL ?>/login.php" class="btn-icon-only" title="Masuk">
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <nav class="ghibli-bottom-nav">
        <a href="<?= PUBLIC_URL ?>/index.php" class="ghibli-bottom-nav-item <?= $activePage === 'home' ? 'active' : '' ?>">
            <i class="bi <?= $activePage === 'home' ? 'bi-house-door-fill' : 'bi-house-door' ?>"></i>
            <span>Beranda</span>
        </a>
        <a href="<?= PUBLIC_URL ?>/profil_desa.php" class="ghibli-bottom-nav-item <?= $activePage === 'profil' ? 'active' : '' ?>">
            <i class="bi <?= $activePage === 'profil' ? 'bi-building-fill' : 'bi-building' ?>"></i>
            <span>Profil</span>
        </a>
        <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="ghibli-bottom-nav-item <?= $activePage === 'layanan' ? 'active' : '' ?>">
            <i class="bi <?= $activePage === 'layanan' ? 'bi-file-earmark-text-fill' : 'bi-file-earmark-text' ?>"></i>
            <span>Layanan</span>
        </a>
        <a href="<?= PUBLIC_URL ?>/lacak_permohonan.php" class="ghibli-bottom-nav-item <?= $activePage === 'lacak' ? 'active' : '' ?>">
            <i class="bi <?= $activePage === 'lacak' ? 'bi-compass-fill' : 'bi-compass' ?>"></i>
            <span>Lacak</span>
        </a>
    </nav>

    <main class="main-content-wrapper">
<?php else: ?>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top ghibli-navbar">
        <div class="container-fluid px-4">
            <div class="navbar-brand d-flex align-items-center">
                <button id="sidebarToggleFlower" type="button" class="btn-icon-only me-2" title="Toggle Sidebar" aria-label="Toggle Sidebar">
                    <i class="bi bi-flower1"></i>
                </button>
                <a class="navbar-brand-link mb-0" href="<?= ADMIN_URL ?>/dashboard.php">
                    <span>Admin Sasi</span>
                </a>
            </div>
            <div class="navbar-right-icons ms-auto">
                <?php if ($current_user): ?>
                    <a href="<?= ADMIN_URL ?>/profile.php" class="nav-link" title="Ubah Password">
                        <i class="bi bi-shield-lock"></i>
                    </a>
                    <a href="<?= ADMIN_URL ?>/login.php?logout=1" class="nav-link text-danger" onclick="return confirm('Yakin ingin keluar dari sistem?')" title="Keluar">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.getElementById('sidebarToggleFlower');
        var offcanvasEl = document.getElementById('sasiAdminSidebar');
        function toggleSidebar() {
            if (!offcanvasEl) return;
            var bsOff = (window.bootstrap && window.bootstrap.Offcanvas && window.bootstrap.Offcanvas.getInstance(offcanvasEl)) || (window.bootstrap && new window.bootstrap.Offcanvas(offcanvasEl));
            if (!bsOff) return;
            if (offcanvasEl.classList.contains('show')) bsOff.hide(); else bsOff.show();
        }
        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);

        var dropdownToggles = document.querySelectorAll('.dropdown-toggle[data-bs-toggle="dropdown"]');
        dropdownToggles.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                var menu = button.nextElementSibling;
                if (!menu || !menu.classList.contains('dropdown-menu')) return;
                if (window.bootstrap && window.bootstrap.Dropdown) {
                    var dropdownInstance = window.bootstrap.Dropdown.getOrCreateInstance(button);
                    dropdownInstance.toggle();
                    return;
                }
                var isShown = menu.classList.contains('show');
                menu.classList.toggle('show', !isShown);
                button.setAttribute('aria-expanded', String(!isShown));
            });
        });

        document.addEventListener('click', function (event) {
            document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
                var button = menu.previousElementSibling;
                if (button && button.matches('[data-bs-toggle="dropdown"]') && (button.contains(event.target) || menu.contains(event.target))) {
                    return;
                }
                menu.classList.remove('show');
                if (button && button.matches('[data-bs-toggle="dropdown"]')) {
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    });
    </script>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main p-4">
<?php endif; ?>