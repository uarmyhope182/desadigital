<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$activePage = $activePage ?? '';
$current_user = current_user();

// Evaluasi status peran pengguna aktif - menggunakan role dari user
$user_role = $current_user['role'] ?? '';
$is_admin_user = ($user_role === 'admin');
$is_resident_user = ($user_role === 'resident' || $user_role === 'warga');

/**
 * Array Sumber Data Menu Tunggal (Sasi Unified Sidebar Menu)
 */
$sasi_menu_items = [
    // === MAIN SEGMENT (Accessible by Admin & Resident) ===
    [
        'category'    => 'Menu Utama',
        'title'       => 'Dashboard',
        'url'         => ADMIN_URL . '/dashboard.php',
        'icon'        => 'bi-speedometer2',
        'icon_color'  => '#9CAF88',
        'active_key'  => 'dashboard',
        'role_target' => 'all',
    ],

    // === RESIDENT SEGMENT ===
    [
        'category'    => 'Layanan Mandiri',
        'title'       => 'Permohonan Saya',
        'url'         => ADMIN_URL . '/permohonan_saya.php',
        'icon'        => 'bi-file-earmark-person',
        'icon_color'  => '#F6C7A1',
        'active_key'  => 'permohonan_saya',
        'role_target' => 'resident',
    ],
    [
        'category'    => 'Layanan Mandiri',
        'title'       => 'Ajukan Surat Baru',
        'url'         => PUBLIC_URL . '/layanan_surat.php',
        'icon'        => 'bi-file-earmark-plus',
        'icon_color'  => '#F0B885',
        'active_key'  => 'ajukan_surat',
        'role_target' => 'resident',
    ],

    // === ADMIN SEGMENT ===
    [
        'category'    => 'Kelola Berkas',
        'title'       => 'Kelola Permohonan',
        'url'         => ADMIN_URL . '/kelola_permohonan.php',
        'icon'        => 'bi-file-earmark-check',
        'icon_color'  => '#A8C3A0',
        'active_key'  => 'kelola_permohonan',
        'role_target' => 'admin',
    ],
    [
        'category'    => 'Kelola Data',
        'title'       => 'Data Penduduk',
        'url'         => ADMIN_URL . '/penduduk.php',
        'icon'        => 'bi-person-badge',
        'icon_color'  => '#B5C4A3',
        'active_key'  => 'penduduk',
        'role_target' => 'admin',
    ],
    [
        'category'    => 'Kelola Data',
        'title'       => 'Jenis Surat',
        'url'         => ADMIN_URL . '/jenis_surat.php',
        'icon'        => 'bi-file-text',
        'icon_color'  => '#9CAF88',
        'active_key'  => 'jenis_surat',
        'role_target' => 'admin',
    ],
    [
        'category'    => 'Sistem Informasi',
        'title'       => 'Manajemen User',
        'url'         => ADMIN_URL . '/users.php',
        'icon'        => 'bi-people',
        'icon_color'  => '#D4E8FF',
        'active_key'  => 'users',
        'role_target' => 'admin',
    ],
    [
        'category'    => 'Sistem Informasi',
        'title'       => 'Kelola Pengumuman',
        'url'         => ADMIN_URL . '/pengumuman.php',
        'icon'        => 'bi-megaphone',
        'icon_color'  => '#F6C7A1',
        'active_key'  => 'pengumuman',
        'role_target' => 'admin',
    ],
    [
        'category'    => 'Sistem Informasi',
        'title'       => 'Laporan & Grafik',
        'url'         => ADMIN_URL . '/laporan.php',
        'icon'        => 'bi-bar-chart-line',
        'icon_color'  => '#E8C5C5',
        'active_key'  => 'laporan',
        'role_target' => 'admin',
    ],

    // === ACCOUNT SETTINGS SEGMENT ===
    [
        'category'    => 'Konfigurasi Akun',
        'title'       => 'Profil & Keamanan',
        'url'         => ADMIN_URL . '/profile.php',
        'icon'        => 'bi-person-fill-gear',
        'icon_color'  => '#9CAF88',
        'active_key'  => 'profile',
        'role_target' => 'all',
    ],
    [
        'category'    => 'Konfigurasi Akun',
        'title'       => 'Website Publik',
        'url'         => PUBLIC_URL . '/index.php',
        'icon'        => 'bi-globe2',
        'icon_color'  => '#7A8F64',
        'active_key'  => 'website',
        'role_target' => 'all',
        'target'      => '_blank',
    ],
    [
        'category'    => 'Konfigurasi Akun',
        'title'       => 'Keluar Aplikasi',
        'url'         => ADMIN_URL . '/logout.php',
        'icon'        => 'bi-box-arrow-right',
        'icon_color'  => '#D4A5A5',
        'active_key'  => 'logout',
        'role_target' => 'all',
        'class'       => 'text-danger',
        'onclick'     => "return confirm('Yakin ingin mengakhiri sesi login saat ini?')",
    ],
];

/**
 * Fungsi Pengolah Filter Menu & Generator HTML
 */
if (!function_exists('render_sasi_sidebar_by_role')) {
    function render_sasi_sidebar_by_role($menu_items, $activePage, $is_admin_user) {
        $current_category = '';
        $is_first_category = true;

        foreach ($menu_items as $item) {
            $target = $item['role_target'];

            // Filter menu visibility based on user role
            if ($target === 'admin' && !$is_admin_user) {
                continue; 
            }
            if ($target === 'resident' && $is_admin_user) {
                continue; 
            }

            // Category separator
            if ($current_category !== $item['category']) {
                $current_category = $item['category'];
                if (!$is_first_category) {
                    echo '<div class="sidebar-divider"></div>';
                }
                echo '<div class="sidebar-category">' . htmlspecialchars($current_category) . '</div>';
                $is_first_category = false;
            }

            $activeClass = ($activePage === $item['active_key']) ? 'active' : '';
            $customClass = $item['class'] ?? '';
            $targetAttr  = isset($item['target']) ? ' target="' . htmlspecialchars($item['target']) . '"' : '';
            $clickAttr   = isset($item['onclick']) ? ' onclick="' . htmlspecialchars($item['onclick']) . '"' : '';
            $iconColor   = $item['icon_color'] ?? '#9CAF88';

            echo '<a href="' . htmlspecialchars($item['url']) . '" class="sidebar-nav-link ' . $activeClass . ' ' . $customClass . '"' . $targetAttr . $clickAttr . '>';
            echo '<i class="bi ' . htmlspecialchars($item['icon']) . '" style="color: ' . $iconColor . ';"></i>';
            echo '<span>' . htmlspecialchars($item['title']) . '</span>';
            echo '</a>';
        }
    }
}
?>

<!-- Bright Rustic Village Interface - Sidebar Styles -->
<style>
/* ============================================
   BRIGHT RUSTIC VILLAGE INTERFACE
   Sidebar Layout - Wider & Clean
   ============================================ */

:root {
    --sidebar-bg: #FDFBF7;
    --sidebar-border: #E8E0D5;
    --sidebar-sage: #9CAF88;
    --sidebar-sage-dark: #7A8F64;
    --sidebar-sage-light: #B5C4A3;
    --sidebar-peach: #F6C7A1;
    --sidebar-text-dark: #1A1512;
    --sidebar-text-soft: #2C241E;
    --sidebar-hover-bg: rgba(156, 175, 136, 0.08);
    --sidebar-active-bg: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    --sidebar-active-text: #FFFFFF;
    --sidebar-error: #D4A5A5;
    --sidebar-error-dark: #A07070;
    --sidebar-shadow: rgba(58, 49, 43, 0.06);
    --sidebar-transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hide hamburger button in sidebar */
.btn-sidebar-hamburger {
    display: none !important;
}

/* ============================================
   OFFCANVAS SIDEBAR - WIDER (280px)
   ============================================ */
.sidebar-offcanvas {
    background: var(--sidebar-bg);
    border-right: 1px solid var(--sidebar-border);
    width: 280px !important;
    box-shadow: 4px 0 20px var(--sidebar-shadow);
}

/* Header - Compact */
.sidebar-offcanvas-header {
    border-bottom: 1px solid var(--sidebar-border);
    padding: 1rem 1.25rem;
    background: rgba(156, 175, 136, 0.02);
}

.sidebar-offcanvas-header .offcanvas-title {
    font-family: 'Bricolage Grotesque', 'Poppins', sans-serif;
    font-weight: 700;
    color: var(--sidebar-text-dark);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.1rem;
}

.sidebar-offcanvas-header .offcanvas-title i {
    font-size: 1.3rem;
    color: var(--sidebar-sage);
}

.sidebar-offcanvas-header .btn-close {
    background: rgba(156, 175, 136, 0.1);
    border-radius: 50%;
    padding: 6px;
    transition: var(--sidebar-transition);
}

.sidebar-offcanvas-header .btn-close:hover {
    background: rgba(156, 175, 136, 0.25);
    transform: rotate(90deg);
}

/* User Profile Section */
.sidebar-user-profile {
    padding: 0.85rem 1.25rem;
    margin: 0.75rem 1rem;
    background: rgba(156, 175, 136, 0.08);
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: var(--sidebar-transition);
}

.sidebar-user-profile:hover {
    background: rgba(156, 175, 136, 0.12);
    transform: translateX(2px);
}

.profile-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--sidebar-sage) 0%, var(--sidebar-peach) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(156, 175, 136, 0.2);
}

.profile-avatar i {
    font-size: 1.2rem;
    color: #FFFFFF;
}

.profile-name {
    font-family: 'Poppins', sans-serif;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--sidebar-text-dark);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* SIDEBAR BODY */
.sidebar-offcanvas-body {
    padding: 0.5rem 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: calc(100vh - 70px);
}

/* Category Menu */
.sidebar-category {
    font-family: 'Poppins', sans-serif;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #5C4A3A;
    padding: 0.75rem 1.25rem 0.4rem 1.25rem;
    text-transform: uppercase;
}

/* Navigation Links */
.sidebar-offcanvas-body .sidebar-nav-link {
    font-family: 'Inter', 'Poppins', sans-serif;
    color: var(--sidebar-text-soft);
    font-weight: 500;
    padding: 0.6rem 1.25rem;
    margin: 0.1rem 0.8rem;
    border-radius: 32px;
    transition: var(--sidebar-transition);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.85rem;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    position: relative;
}

.sidebar-offcanvas-body .sidebar-nav-link i {
    font-size: 1.1rem;
    transition: var(--sidebar-transition);
    width: 24px;
    text-align: center;
    flex-shrink: 0;
}

.sidebar-offcanvas-body .sidebar-nav-link span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hover State */
.sidebar-offcanvas-body .sidebar-nav-link:hover {
    background: var(--sidebar-hover-bg);
    color: var(--sidebar-text-dark);
    transform: translateX(4px);
}

.sidebar-offcanvas-body .sidebar-nav-link:hover i {
    transform: scale(1.05);
    color: var(--sidebar-sage-dark) !important;
}

/* Active State */
.sidebar-offcanvas-body .sidebar-nav-link.active {
    background: var(--sidebar-active-bg);
    color: var(--sidebar-active-text);
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(156, 175, 136, 0.3);
}

.sidebar-offcanvas-body .sidebar-nav-link.active i {
    color: var(--sidebar-active-text) !important;
}

/* Danger Link (Logout) */
.sidebar-offcanvas-body .sidebar-nav-link.text-danger {
    color: var(--sidebar-error-dark);
}

.sidebar-offcanvas-body .sidebar-nav-link.text-danger i {
    color: var(--sidebar-error);
}

.sidebar-offcanvas-body .sidebar-nav-link.text-danger:hover {
    background: rgba(212, 165, 165, 0.12);
    color: #8B5E5E;
}

.sidebar-offcanvas-body .sidebar-nav-link.text-danger:hover i {
    color: #C58B8B !important;
}

/* Divider */
.sidebar-divider {
    border-top: 1px dashed var(--sidebar-border);
    margin: 0.5rem 1.25rem;
}

/* Sidebar Decoration - Totoro */
.sidebar-decoration {
    text-align: center;
    padding: 0.75rem 0;
    opacity: 0.35;
    pointer-events: none;
    margin-top: auto;
}

.sidebar-decoration img {
    width: 55px;
    height: auto;
    transition: transform 0.3s ease;
}

.sidebar-decoration:hover img {
    transform: scale(1.05);
}

@keyframes sidebarTotoroBreathe {
    0%, 100% { transform: scale(1) translateY(0); }
    50% { transform: scale(1.03) translateY(-3px); }
}

.totoro-breathe-sidebar {
    animation: sidebarTotoroBreathe 4s ease-in-out infinite;
}

/* Sidebar Footer */
.sidebar-footer-text {
    text-align: center;
    padding: 0.75rem 1rem;
    font-size: 0.55rem;
    color: #5C4A3A;
    font-weight: 500;
    border-top: 1px solid var(--sidebar-border);
    margin-top: 0.5rem;
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */

/* Tablet */
@media (max-width: 992px) {
    .sidebar-offcanvas {
        width: 260px !important;
    }
}

@media (max-width: 768px) {
    .sidebar-offcanvas {
        width: 280px !important;
    }
    
    .sidebar-offcanvas-header {
        padding: 0.85rem 1rem;
    }
    
    .sidebar-offcanvas-header .offcanvas-title {
        font-size: 1rem;
    }
    
    .sidebar-offcanvas-header .offcanvas-title i {
        font-size: 1.1rem;
    }
    
    .sidebar-offcanvas-body .sidebar-nav-link {
        padding: 0.5rem 1rem;
        margin: 0.1rem 0.6rem;
        font-size: 0.8rem;
        gap: 0.6rem;
    }
    
    .sidebar-offcanvas-body .sidebar-nav-link i {
        font-size: 1rem;
        width: 22px;
    }
    
    .sidebar-category {
        padding: 0.6rem 1rem 0.3rem 1rem;
        font-size: 0.55rem;
    }
    
    .sidebar-user-profile {
        padding: 0.7rem 1rem;
        margin: 0.5rem 0.8rem;
    }
    
    .profile-avatar {
        width: 35px;
        height: 35px;
    }
    
    .profile-avatar i {
        font-size: 1rem;
    }
    
    .profile-name {
        font-size: 0.8rem;
    }
    
    .sidebar-decoration img {
        width: 45px;
    }
    
    .sidebar-divider {
        margin: 0.4rem 1rem;
    }
}

/* Mobile */
@media (max-width: 576px) {
    .sidebar-offcanvas {
        width: 270px !important;
    }
    
    .sidebar-user-profile {
        padding: 0.6rem 0.9rem;
        margin: 0.4rem 0.7rem;
    }
    
    .profile-avatar {
        width: 32px;
        height: 32px;
    }
    
    .profile-name {
        font-size: 0.75rem;
    }
    
    .sidebar-offcanvas-body .sidebar-nav-link {
        padding: 0.45rem 0.9rem;
        font-size: 0.75rem;
    }
    
    .sidebar-category {
        font-size: 0.5rem;
        padding: 0.5rem 0.9rem 0.25rem 0.9rem;
    }
    
    .sidebar-decoration {
        display: none;
    }
    
    .sidebar-footer-text {
        font-size: 0.5rem;
        padding: 0.6rem;
    }
}

/* Landscape mode untuk mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .sidebar-offcanvas-body {
        min-height: auto;
        max-height: 85vh;
        overflow-y: auto;
    }
    
    .sidebar-decoration {
        display: none;
    }
}

/* Untuk layar yang sangat kecil */
@media (max-width: 380px) {
    .sidebar-offcanvas {
        width: 260px !important;
    }
    
    .sidebar-user-profile {
        margin: 0.3rem 0.6rem;
    }
    
    .profile-name {
        font-size: 0.7rem;
    }
}

/* Print styles */
@media print {
    .sidebar-offcanvas {
        display: none !important;
    }
}
</style>

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="sasiAdminSidebar" aria-labelledby="sasiAdminSidebarLabel">
    <div class="offcanvas-header sidebar-offcanvas-header">
        <h5 class="offcanvas-title" id="sasiAdminSidebarLabel">
            <i class="bi bi-flower1"></i>
            <span>Kelurahan Sasi</span>
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body sidebar-offcanvas-body">
        <!-- User Profile Section -->
        <div class="sidebar-user-profile">
            <div class="profile-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="profile-name" title="<?= h($current_user['full_name'] ?? 'Admin User') ?>">
                <?= h($current_user['full_name'] ?? 'Admin User') ?>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="nav flex-column">
            <?php render_sasi_sidebar_by_role($sasi_menu_items, $activePage, $is_admin_user); ?>
        </nav>
        
        <!-- Ghibli Decoration - Totoro -->
        <div class="sidebar-decoration">
            <img src="<?= ASSETS_URL ?>/images/totoro.png" alt="Totoro" class="totoro-breathe-sidebar" loading="lazy" onerror="this.style.display='none'">
        </div>
        
        <!-- Sidebar Footer -->
        <div class="sidebar-footer-text">
            <span>© Sasi Kelurahan</span>
        </div>
    </div>
</div>