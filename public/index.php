<?php
$page_title = 'Beranda';
$activePage = 'home';
$is_admin_page = false;

require_once __DIR__ . '/../config/db.php';

if (get_get('logout') === '1') {
    logout_user();
    header('Location: ' . PUBLIC_URL . '/index.php?logout=success');
    exit;
}

$current_user = is_logged_in() ? current_user() : null;

$total_pengajuan = (int) $pdo->query('SELECT COUNT(*) FROM pengajuan_surat')->fetchColumn();
$total_penduduk = (int) $pdo->query('SELECT COUNT(*) FROM penduduk')->fetchColumn();
$total_jenis = (int) $pdo->query('SELECT COUNT(*) FROM jenis_surat WHERE is_active = 1')->fetchColumn();
$active_pengumuman = (int) $pdo->query('SELECT COUNT(*) FROM pengumuman WHERE is_published = 1')->fetchColumn();

$services = $pdo->query('
    SELECT id, kode, nama, deskripsi 
    FROM jenis_surat 
    WHERE is_active = 1 
    ORDER BY id 
    LIMIT 4
')->fetchAll();

$announcements = $pdo->query('
    SELECT id, judul, isi, created_at 
    FROM pengumuman 
    WHERE is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 3
')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Beranda -->
<!-- Modified: Fixed contrast issues -->

<!-- Background Layers - Fixed, does not move on scroll -->
<div class="ghibli-bg-layer-sky"></div>
<div class="ghibli-bg-layer-scene"></div>
<div class="ghibli-bg-layer-atmosphere"></div>

<!-- Decorative Elements - Fixed position, animate in place -->
<div class="ghibli-decor-fixed">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-1" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-2" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-3" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-4" loading="lazy">
</div>

<div class="ghibli-content-wrapper">

<?php if (get_get('logout') === 'success'): ?>
<div class="container mt-4">
    <div class="ghibli-alert-success">
        <div class="alert-body d-flex align-items-center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="#9CAF88"/>
            </svg>
            <span style="font-weight: 600; color: #2A241F;">Anda telah berhasil keluar dari sistem.</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Hero Section - Village Landscape -->
<section class="ghibli-hero-section">
    <div class="ghibli-hero-background" style="background-image: url('<?= ASSETS_URL ?>/images/01-hero-main-background.png');">
        <div class="ghibli-hero-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="ghibli-title-large">Kelurahan Sasi</h1>
                    <p class="ghibli-hero-text mx-auto">
                        Layanan administrasi kelurahan yang hangat, transparan, dan mudah dijangkau dari rumah Anda. 
                        Ajukan permohonan surat kini lebih bersahabat tanpa perlu antre panjang.
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <?php if ($current_user): ?>
                            <a href="<?= ADMIN_URL ?>/dashboard.php" class="ghibli-btn-primary">
                                Masuk Portal Layanan
                            </a>
                            <a href="<?= ADMIN_URL ?>/profile.php" class="ghibli-btn-secondary">
                                Pengaturan Profil
                            </a>
                        <?php else: ?>
                            <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="ghibli-btn-primary">
                                Ajukan Surat
                            </a>
                            <a href="<?= PUBLIC_URL ?>/lacak_permohonan.php" class="ghibli-btn-outline-light">
                                Lacak Permohonan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik Layanan Section -->
<section class="ghibli-stats-section">
    <div class="container">
        <h2 class="ghibli-section-title">Statistik Pelayanan</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="ghibli-card ghibli-card-stat">
                    <div class="card-body py-4">
                        <div class="ghibli-stat-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4h16v16H4V4z" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                <path d="M8 8h8M8 12h6M8 16h4" stroke="#9CAF88" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="ghibli-stat-number count-up" data-target="<?= $total_pengajuan ?>"><?= number_format($total_pengajuan) ?></h3>
                        <p class="ghibli-stat-label">Total Permohonan</p>
                        <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="ghibli-susuwatari-stat" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ghibli-card ghibli-card-stat">
                    <div class="card-body py-4">
                        <div class="ghibli-stat-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="8" r="4" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                <path d="M5 20v-2a7 7 0 0114 0v2" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                            </svg>
                        </div>
                        <h3 class="ghibli-stat-number count-up" data-target="<?= $total_penduduk ?>"><?= number_format($total_penduduk) ?></h3>
                        <p class="ghibli-stat-label">Data Penduduk</p>
                        <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="ghibli-susuwatari-stat" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ghibli-card ghibli-card-stat">
                    <div class="card-body py-4">
                        <div class="ghibli-stat-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="4" y="4" width="16" height="16" rx="2" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                <path d="M8 8h8M8 12h6M8 16h4" stroke="#9CAF88" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="ghibli-stat-number count-up" data-target="<?= $total_jenis ?>"><?= number_format($total_jenis) ?></h3>
                        <p class="ghibli-stat-label">Jenis Surat Aktif</p>
                        <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="ghibli-susuwatari-stat" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ghibli-card ghibli-card-stat">
                    <div class="card-body py-4">
                        <div class="ghibli-stat-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 8C18 4.686 15.314 2 12 2S6 4.686 6 8c0 4 6 11 6 11s6-7 6-11z" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                <circle cx="12" cy="8" r="2" fill="#9CAF88"/>
                            </svg>
                        </div>
                        <h3 class="ghibli-stat-number count-up" data-target="<?= $active_pengumuman ?>"><?= number_format($active_pengumuman) ?></h3>
                        <p class="ghibli-stat-label">Pengumuman Aktif</p>
                        <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="ghibli-susuwatari-stat" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Surat Section -->
<section class="ghibli-services-section">
    <div class="container">
        <div class="ghibli-section-header">
            <div>
                <h2 class="ghibli-section-title">Layanan Surat</h2>
                <p class="ghibli-section-subtitle">Ajukan dokumen administrasi secara digital dengan cepat</p>
            </div>
            <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="ghibli-btn-outline-primary">
                Lihat Semua Layanan
            </a>
        </div>
        
        <?php if (empty($services)): ?>
            <div class="ghibli-empty-state">
                <p class="mb-0">Belum ada layanan surat yang tersedia.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="ghibli-card ghibli-card-service h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-3">
                                    <div class="ghibli-service-icon">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                            <path d="M14 2v6h6" stroke="#9CAF88" stroke-width="1.5" fill="none"/>
                                            <path d="M16 13H8M16 17H8M10 9H8" stroke="#9CAF88" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <h5 class="ghibli-service-title"><?= h($service['nama']) ?></h5>
                                <p class="ghibli-service-desc"><?= h($service['deskripsi'] ?? 'Layanan terintegrasi di Kelurahan Sasi.') ?></p>
                                
                                <div class="mt-auto pt-2">
                                    <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="ghibli-btn-outline-primary w-100 text-center d-block">
                                        Ajukan Layanan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Pengumuman Terbaru Section -->
<section class="ghibli-announcement-section">
    <div class="container">
        <div class="ghibli-section-header">
            <div>
                <h2 class="ghibli-section-title">Warta Kelurahan</h2>
                <p class="ghibli-section-subtitle">Ikuti perkembangan dan pengumuman terbaru dari desa kita</p>
            </div>
            <a href="<?= PUBLIC_URL ?>/profil-desa.php" class="ghibli-btn-outline-primary">
                Buka Arsip Pengumuman
            </a>
        </div>
        
        <?php if (empty($announcements)): ?>
            <div class="ghibli-empty-state">
                <p class="mb-0">Belum ada pengumuman untuk saat ini.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($announcements as $news): ?>
                    <div class="col-md-4">
                        <div class="ghibli-card ghibli-card-announcement h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="mb-2">
                                    <span class="ghibli-badge">Pengumuman</span>
                                </div>
                                <h5 class="ghibli-announcement-title"><?= h($news['judul']) ?></h5>
                                <p class="ghibli-announcement-desc"><?= h(substr($news['isi'], 0, 110)) ?>...</p>
                                <a href="<?= PUBLIC_URL ?>/pengumuman.php?id=<?= $news['id'] ?>" class="btn ghibli-btn-outline-primary">Baca selengkapnya</a>
                                <div class="mt-auto pt-3">
                                    <div class="ghibli-divider"></div>
                                    <div class="ghibli-date-meta">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#5C4A3A" stroke-width="1.5" fill="none"/>
                                            <path d="M8 2v4M16 2v4M3 10h18" stroke="#5C4A3A" stroke-width="1.5"/>
                                        </svg>
                                        <span><?= date('d M Y', strtotime($news['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer Call to Action Section - WITH SAWAH BACKGROUND IMAGE -->
<section class="ghibli-cta-section" style="background-image: url('<?= ASSETS_URL ?>/images/sawah-lanscape.png'); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.55) 100%);"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <h2 class="ghibli-cta-title" style="color: #FFFFFF !important; font-weight: 700 !important;">Butuh Dokumen Pendukung Administrasi?</h2>
        <p class="ghibli-cta-text" style="color: #FFFFFF !important; font-weight: 600 !important;">
            Proses verifikasi yang mudah, aman, dan langsung dipantau secara real-time. Ajukan permohonan surat Anda sekarang!
        </p>
        
        <?php if ($current_user): ?>
            <a href="<?= ADMIN_URL ?>/dashboard.php" class="ghibli-btn-light" style="background-color: #FFFFFF !important; color: #1A4A1A !important; font-weight: 700 !important;">
                Masuk Ke Dasbor Mandiri Anda
            </a>
        <?php else: ?>
            <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="ghibli-btn-light" style="background-color: #FFFFFF !important; color: #1A4A1A !important; font-weight: 700 !important;">
                Mulai Ajukan Permohonan
            </a>
        <?php endif; ?>
    </div>
</section>

</div>

<style>
/* Ghibli Theme CSS for index.php - MODIFIED: FIXED CONTRAST */
/* Darker text for better readability */

:root {
    --ghibli-sky: #B9DCFF;
    --ghibli-sunset: #F6C7A1;
    --ghibli-rice: #9CAF88;
    --ghibli-text-dark: #1A1A1A;
    --ghibli-text-soft: #2A241F;
    --ghibli-text-medium: #333333;
    --ghibli-text-light-brown: #5C4A3A;
    --ghibli-card-bg: #FDFBF7;
    --ghibli-primary: #9CAF88;
    --ghibli-primary-dark: #7A8F64;
    --ghibli-accent: #F6C7A1;
    --ghibli-border: #E8E0D5;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: #FAF6F0;
    color: var(--ghibli-text-dark);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.5;
}

.ghibli-bg-layer-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
    z-index: 0;
    pointer-events: none;
}

.ghibli-bg-layer-scene {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/rice-fields.png') center/cover no-repeat;
    opacity: 0.12;
    z-index: 0;
    pointer-events: none;
}

.ghibli-bg-layer-atmosphere {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/06-atmospheric-elements.png') center bottom/cover no-repeat;
    opacity: 0.2;
    mix-blend-mode: soft-light;
    z-index: 0;
    pointer-events: none;
}

.ghibli-decor-fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

@keyframes fallLeaf1 {
    0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
}

@keyframes fallLeaf2 {
    0% { transform: translateY(-10vh) rotate(0deg) scaleX(-1); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(110vh) rotate(320deg); opacity: 0; }
}

@keyframes fallLeaf3 {
    0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(110vh) rotate(400deg); opacity: 0; }
}

@keyframes fallLeaf4 {
    0% { transform: translateY(-10vh) rotate(0deg) scaleX(-1); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(110vh) rotate(280deg); opacity: 0; }
}

.ghibli-leaf-1 {
    position: absolute;
    top: -10%;
    left: 10%;
    width: 30px;
    animation: fallLeaf1 8s ease-in-out infinite;
    animation-delay: 0s;
}

.ghibli-leaf-2 {
    position: absolute;
    top: -10%;
    left: 30%;
    width: 25px;
    animation: fallLeaf2 10s ease-in-out infinite;
    animation-delay: 2s;
}

.ghibli-leaf-3 {
    position: absolute;
    top: -10%;
    left: 60%;
    width: 35px;
    animation: fallLeaf3 9s ease-in-out infinite;
    animation-delay: 4s;
}

.ghibli-leaf-4 {
    position: absolute;
    top: -10%;
    left: 85%;
    width: 28px;
    animation: fallLeaf4 12s ease-in-out infinite;
    animation-delay: 1s;
}

@keyframes susuwatariRun {
    0%, 100% { transform: translateX(0) translateY(0); }
    50% { transform: translateX(5px) translateY(-2px); }
}

.ghibli-susuwatari-stat {
    position: absolute;
    bottom: -6px;
    right: -6px;
    width: 28px;
    animation: susuwatariRun 2s ease-in-out infinite;
    pointer-events: none;
}

.ghibli-content-wrapper {
    position: relative;
    z-index: 2;
}

.ghibli-hero-section {
    position: relative;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

.ghibli-hero-background {
    position: relative;
    background-size: cover;
    background-position: center 30%;
    background-repeat: no-repeat;
    min-height: 550px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ghibli-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.45) 100%);
    z-index: 1;
}

.ghibli-hero-background .container {
    position: relative;
    z-index: 2;
}

.ghibli-title-large {
    font-family: 'Bricolage Grotesque', 'Poppins', serif;
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
    color: #FFFFFF;
}

.ghibli-hero-text {
    font-size: 1.15rem;
    margin-bottom: 2rem;
    color: #FFFFFF;
    font-weight: 600;
    max-width: 600px;
}

.ghibli-card {
    background-color: #FDFBF7;
    border: 1px solid var(--ghibli-border);
    border-radius: 24px;
    box-shadow: 0 4px 12px rgba(58, 49, 43, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.ghibli-card:hover {
    transform: translateY(-4px);
    border-color: #9CAF88;
    box-shadow: 0 12px 28px rgba(58, 49, 43, 0.08);
}

.ghibli-card-stat {
    text-align: center;
}

.ghibli-btn-primary {
    background-color: #9CAF88;
    color: #FFFFFF;
    border-radius: 40px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.ghibli-btn-primary:hover {
    background-color: #7A8F64;
    transform: translateY(-2px);
    color: #FFFFFF;
}

.ghibli-btn-secondary {
    background-color: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(4px);
    border: 1.5px solid rgba(255, 255, 255, 0.6);
    color: #FFFFFF;
    border-radius: 40px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.ghibli-btn-secondary:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: #FFFFFF;
}

.ghibli-btn-outline-primary {
    background-color: transparent;
    border: 1.5px solid #C4B8A8;
    color: #2A241F;
    border-radius: 40px;
    padding: 8px 20px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.ghibli-btn-outline-primary:hover {
    background-color: rgba(156, 175, 136, 0.1);
    border-color: #9CAF88;
    color: #1A1A1A;
}

.ghibli-btn-outline-light {
    background-color: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(4px);
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    color: #FFFFFF;
    border-radius: 40px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.ghibli-btn-outline-light:hover {
    transform: translateY(-2px);
    background-color: rgba(255, 255, 255, 0.25);
}

.ghibli-btn-light {
    background-color: #FFFFFF;
    color: #7A8F64;
    border-radius: 40px;
    padding: 12px 28px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.ghibli-btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -6px rgba(156, 175, 136, 0.3);
    color: #7A8F64;
}

.ghibli-stats-section {
    padding: 60px 0;
    background-color: rgba(250, 246, 240, 0.6);
}

.ghibli-services-section {
    padding: 60px 0;
}

.ghibli-announcement-section {
    padding: 60px 0;
    background-color: rgba(250, 246, 240, 0.4);
}

.ghibli-cta-section {
    padding: 60px 0;
    text-align: center;
}

.ghibli-section-title {
    font-family: 'Bricolage Grotesque', 'Poppins', serif;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #1A1A1A;
}

.ghibli-section-subtitle {
    font-size: 0.9rem;
    color: #333333;
    margin-bottom: 0;
}

.ghibli-section-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
}

.ghibli-stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border-radius: 50%;
    background-color: rgba(156, 175, 136, 0.12);
    margin-bottom: 1rem;
}

.ghibli-service-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border-radius: 50%;
    background-color: rgba(156, 175, 136, 0.1);
}

.ghibli-stat-number {
    font-family: 'Bricolage Grotesque', 'Poppins', serif;
    font-weight: 800;
    font-size: 2rem;
    margin-bottom: 0.25rem;
    color: #1A1A1A;
}

.ghibli-stat-label {
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0;
    color: #333333;
}

.ghibli-service-title {
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    color: #1A1A1A;
}

.ghibli-service-desc {
    font-size: 0.85rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    color: #333333;
}

.ghibli-badge {
    background-color: rgba(156, 175, 136, 0.15);
    color: #7A8F64;
    border-radius: 40px;
    padding: 0.25rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.ghibli-announcement-title {
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #1A1A1A;
}

.ghibli-announcement-desc {
    font-size: 0.85rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    color: #333333;
}

.ghibli-divider {
    border-top: 2px dashed #E8E0D5;
    margin: 1rem 0 0.75rem;
}

.ghibli-date-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #5C4A3A;
}

.ghibli-cta-title {
    font-family: 'Bricolage Grotesque', 'Poppins', serif;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #FFFFFF;
}

.ghibli-cta-text {
    font-size: 1rem;
    max-width: 550px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 1.75rem;
    opacity: 0.95;
    color: #FFFFFF;
}

.ghibli-alert-success {
    background-color: #FDFBF7;
    border-radius: 24px;
    border-left: 4px solid #9CAF88;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    padding: 0.75rem 1rem;
}

.ghibli-empty-state {
    background-color: #FDFBF7;
    border-radius: 24px;
    text-align: center;
    padding: 2rem;
    border: 1px solid #E8E0D5;
    color: #333333;
}

@media (max-width: 768px) {
    .ghibli-title-large { font-size: 2rem; }
    .ghibli-hero-background { min-height: 450px; }
    .ghibli-hero-text { font-size: 1rem; }
    .ghibli-stats-section, .ghibli-services-section, .ghibli-announcement-section, .ghibli-cta-section { padding: 40px 0; }
    .ghibli-section-title { font-size: 1.5rem; }
    .ghibli-cta-title { font-size: 1.5rem; }
    .ghibli-leaf-1, .ghibli-leaf-2, .ghibli-leaf-3, .ghibli-leaf-4 {
        display: none;
    }
}

@media (max-width: 576px) {
    .ghibli-title-large { font-size: 1.75rem; }
    .ghibli-hero-background { min-height: 400px; }
    .ghibli-stat-number { font-size: 1.5rem; }
}
</style>

<script>
function initCountUp() {
    const countElements = document.querySelectorAll('.count-up');
    if (!countElements.length) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target'), 10);
                if (isNaN(target)) return;
                
                let current = 0;
                const duration = 1500;
                const step = Math.ceil(target / (duration / 16));
                const interval = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        el.textContent = target.toLocaleString();
                        clearInterval(interval);
                    } else {
                        el.textContent = current.toLocaleString();
                    }
                }, 16);
                
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.3 });
    
    countElements.forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', function() {
    initCountUp();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>