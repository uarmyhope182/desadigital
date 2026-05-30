<?php
$page_title = 'Profil Kelurahan Sasi';
$activePage = 'profil';
$is_admin_page = false;

require_once __DIR__ . '/../config/db.php';

try {
    $statistik_rows = $pdo->query('SELECT stat_key, stat_value, stat_label FROM statistik_desa ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching statistik desa: ' . $e->getMessage());
    $statistik_rows = [];
}

// Struktur Organisasi dengan nama baru
$struktur_organisasi = [
    ['nama' => 'Nila Ananta', 'jabatan' => 'Lurah', 'deskripsi_tugas' => 'Memimpin jalannya pemerintahan kelurahan', 'urutan' => 1],
    ['nama' => 'Dativa Virjinia', 'jabatan' => 'Sekretaris Lurah', 'deskripsi_tugas' => 'Mengelola administrasi kesekretariatan', 'urutan' => 2],
    ['nama' => 'Aurelia Marchanda', 'jabatan' => 'Kepala Seksi Pemerintahan', 'deskripsi_tugas' => 'Mengelola urusan pemerintahan dan pelayanan', 'urutan' => 3],
    ['nama' => 'Sesilia Vienna', 'jabatan' => 'Kepala Seksi Kesejahteraan', 'deskripsi_tugas' => 'Mengelola program kesejahteraan masyarakat', 'urutan' => 4],
    ['nama' => 'Marsyanda Tara', 'jabatan' => 'Kepala Seksi Pembangunan', 'deskripsi_tugas' => 'Mengelola program pembangunan', 'urutan' => 5],
];

$statistik = array_column($statistik_rows, null, 'stat_key');

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Profil Desa -->
<div class="ghibli-bg-sky"></div>
<div class="ghibli-bg-rice-fields"></div>
<div class="ghibli-bg-atmosphere"></div>

<!-- Floating Leaves -->
<div class="floating-leaves">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="leaf leaf-1" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="leaf leaf-2" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="leaf leaf-3" loading="lazy">
    <img src="<?= ASSETS_URL ?>/images/18-floating-leaves-close.png" alt="" class="leaf leaf-4" loading="lazy">
</div>

<div class="ghibli-content-wrapper">

    <!-- Hero Section -->
    <section class="ghibli-profile-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="ghibli-card-hero">
                        <div class="hero-badge">
                            <img src="<?= ASSETS_URL ?>/images/totoro.png" alt="Totoro" class="badge-icon-img">
                            <span>Selamat Datang</span>
                        </div>
                        <h1 class="hero-title">Profil <span class="highlight">Kelurahan Sasi</span></h1>
                        <div class="hero-divider">
                            <img src="<?= ASSETS_URL ?>/images/divider-squiggly-line.png" alt="">
                        </div>
                        <p class="hero-description">
                            Kecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, Provinsi Nusa Tenggara Timur.
                        </p>
                        <p class="hero-text">
                            Kelurahan Sasi adalah kampung yang ramah, kreatif, dan berorientasi pada pelayanan publik berkualitas.
                            Menyatu dengan alam, masyarakat kami tumbuh bersama dalam semangat gotong royong dan inovasi digital.
                        </p>
                        <div class="hero-buttons">
                            <a href="<?= PUBLIC_URL ?>/layanan_surat.php" class="btn-primary">
                                <i class="bi bi-envelope-paper"></i> Ajukan Surat
                            </a>
                            <a href="<?= PUBLIC_URL ?>/lacak_permohonan.php" class="btn-outline">
                                <i class="bi bi-compass"></i> Lacak Permohonan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-wrapper">
                        <img src="<?= ASSETS_URL ?>/images/village-center.png" alt="Kelurahan Sasi" class="hero-image" loading="lazy">
                        <div class="floating-card floating-card-1">
                            <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="float-icon">
                            <span>+<?= number_format($statistik['total_penduduk']['stat_value'] ?? 5000) ?></span>
                            <small>Warga</small>
                        </div>
                        <div class="floating-card floating-card-2">
                            <img src="<?= ASSETS_URL ?>/images/susuwatari-smile.png" alt="" class="float-icon">
                            <span><?= $statistik['total_rt']['stat_value'] ?? 12 ?> RT</span>
                            <small>Wilayah</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sejarah & Kontak Section -->
    <section class="ghibli-section info-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="glass-card history-card">
                        <div class="card-icon">
                            <img src="<?= ASSETS_URL ?>/images/little-totoro.png" alt="Sejarah">
                        </div>
                        <h2 class="card-title">Sejarah Singkat</h2>
                        <p class="card-text">
                            Kelurahan Sasi berkembang sebagai pusat pelayanan masyarakat yang memadukan citra pedesaan autentik
                            dengan pelayanan modern. Dengan latar budaya lokal dan lingkungan alam yang asri, Kelurahan Sasi selalu
                            menjaga tradisi sambil beradaptasi dengan kebutuhan masyarakat masa kini.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="glass-card contact-card">
                        <div class="card-icon">
                            <img src="<?= ASSETS_URL ?>/images/jiji.png" alt="Kontak">
                        </div>
                        <h2 class="card-title">Kontak & Lokasi</h2>
                        <div class="contact-items">
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-building"></i></div>
                                <div class="contact-detail">
                                    <strong>Alamat Kantor</strong>
                                    <p>Jl. Merdeka No. 24, Kelurahan Sasi, Kota Kefamenanu</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                                <div class="contact-detail">
                                    <strong>Telepon</strong>
                                    <p>(0380) 123456</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                                <div class="contact-detail">
                                    <strong>Email</strong>
                                    <p>info@kelurahansasi.id</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                                <div class="contact-detail">
                                    <strong>Jam Layanan</strong>
                                    <p>Senin - Jumat, 08:00 - 15:00 WITA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Section -->
    <section class="ghibli-section stats-section">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-badge">
                    <span>Data Terkini</span>
                </div>
                <h2 class="section-title">Statistik <span class="highlight">Kelurahan Sasi</span></h2>
                <p class="section-subtitle">Gambaran demografi dan struktur wilayah</p>
            </div>
            <div class="row g-4">
                <?php
                $stat_order = [
                    'total_penduduk' => ['icon' => 'bi-people-fill', 'color' => '#9CAF88', 'unit' => ''],
                    'luas_wilayah' => ['icon' => 'bi-map-fill', 'color' => '#F6C7A1', 'unit' => ' Ha'],
                    'total_rw' => ['icon' => 'bi-diagram-3-fill', 'color' => '#B9DCFF', 'unit' => ' RW'],
                    'total_rt' => ['icon' => 'bi-house-fill', 'color' => '#E8C5C5', 'unit' => ' RT'],
                    'laki_laki' => ['icon' => 'bi-gender-male', 'color' => '#7A8F64', 'unit' => ' Jiwa'],
                    'perempuan' => ['icon' => 'bi-gender-female', 'color' => '#F0B885', 'unit' => ' Jiwa'],
                ];
                foreach ($stat_order as $stat_key => $config):
                    $item = $statistik[$stat_key] ?? null;
                    if (!$item) continue;
                    $value = preg_replace('/[^0-9]/', '', $item['stat_value']);
                    if ($value === '') $value = $item['stat_value'];
                ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div class="stat-icon" style="background: <?= $config['color'] ?>20;">
                                    <i class="bi <?= $config['icon'] ?>" style="color: <?= $config['color'] ?>;"></i>
                                </div>
                                <div class="stat-value">
                                    <span class="counter" data-target="<?= is_numeric($value) ? $value : 0 ?>">0</span>
                                    <span class="stat-unit"><?= $config['unit'] ?></span>
                                </div>
                                <div class="stat-label"><?= htmlspecialchars($item['stat_label']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section class="ghibli-section vision-mission-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="vision-card">
                        <div class="card-header-vm">
                            <div class="icon-wrapper-img">
                                <img src="<?= ASSETS_URL ?>/images/totoro.png" alt="Visi">
                            </div>
                            <h2>Visi</h2>
                        </div>
                        <p class="vision-text">
                            "Menjadi Kelurahan yang mandiri, inklusif, dan unggul dalam pelayanan publik dengan sentuhan kearifan lokal dan teknologi tepat guna."
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-card">
                        <div class="card-header-vm">
                            <div class="icon-wrapper-img">
                                <img src="<?= ASSETS_URL ?>/images/village-girl.png" alt="Misi">
                            </div>
                            <h2>Misi</h2>
                        </div>
                        <ul class="mission-list">
                            <li><span class="mission-number">01</span><span class="mission-text">Meningkatkan kualitas pelayanan publik yang cepat, ramah, dan transparan.</span></li>
                            <li><span class="mission-number">02</span><span class="mission-text">Mendorong partisipasi masyarakat dalam pembangunan sosial dan ekonomi.</span></li>
                            <li><span class="mission-number">03</span><span class="mission-text">Mewujudkan lingkungan hidup yang nyaman dan lestari.</span></li>
                            <li><span class="mission-number">04</span><span class="mission-text">Memperkuat tata kelola pemerintahan berbasis nilai budaya setempat.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Struktur Organisasi - Dengan Nama Baru & Gambar yang Sesuai -->
    <section class="ghibli-section organization-section">
        <div class="container">
            <div class="section-header text-center">
                <div class="section-badge">
                    <span>Struktur Organisasi</span>
                </div>
                <h2 class="section-title">Bagan <span class="highlight">Struktur Kelurahan</span></h2>
                <p class="section-subtitle">Hierarki kepemimpinan Kelurahan Sasi</p>
            </div>

            <div class="org-chart">
                <!-- ===== LEVEL 1: LURAH (Nila Ananta) ===== -->
                <div class="org-node level-1">
                    <div class="org-card leader">
                        <div class="org-avatar">
                            <img src="<?= ASSETS_URL ?>/images/village-girl.png" alt="Lurah">
                        </div>
                        <h4>Nila Ananta</h4>
                        <p>Lurah</p>
                    </div>
                </div>

                <!-- Garis Vertikal ke bawah -->
                <div class="org-line-vertical"></div>

                <!-- Garis Horizontal Cabang -->
                <div class="org-line-horizontal">
                    <div class="line-left"></div>
                    <div class="line-center"><div class="dot"></div></div>
                    <div class="line-right"></div>
                </div>

                <!-- ===== LEVEL 2: SEKRETARIS & KASI ===== -->
                <div class="org-level-2">
                    <!-- SEKRETARIS (KIRI) - Dativa Virjinia -->
                    <div class="org-branch left">
                        <div class="org-line-vertical small"></div>
                        <div class="org-card sekretaris">
                            <div class="org-avatar">
                                <img src="<?= ASSETS_URL ?>/images/character.png" alt="Sekretaris">
                            </div>
                            <h4>Dativa Virjinia</h4>
                            <p>Sekretaris Lurah</p>
                        </div>
                    </div>

                    <!-- KASI (KANAN) - 3 Kasi -->
                    <div class="org-branch right">
                        <div class="org-line-vertical small"></div>
                        <div class="org-kasi-group">
                            <!-- Aurelia Marchanda - Kasi Pemerintahan -->
                            <div class="org-card kasi">
                                <div class="org-avatar">
                                    <img src="<?= ASSETS_URL ?>/images/character_1.png" alt="Kasi Pemerintahan">
                                </div>
                                <h4>Aurelia Marchanda</h4>
                                <p>Kepala Seksi Pemerintahan</p>
                            </div>
                            <!-- Sesilia Vienna - Kasi Kesejahteraan -->
                            <div class="org-card kasi">
                                <div class="org-avatar">
                                    <img src="<?= ASSETS_URL ?>/images/village-girll.png" alt="Kasi Kesejahteraan">
                                </div>
                                <h4>Sesilia Vienna</h4>
                                <p>Kepala Seksi Kesejahteraan</p>
                            </div>
                            <!-- Marsyanda Tara - Kasi Pembangunan -->
                            <div class="org-card kasi">
                                <div class="org-avatar">
                                    <img src="<?= ASSETS_URL ?>/images/23-woman-harvest-basket.png" alt="Kasi Pembangunan">
                                </div>
                                <h4>Marsyanda Tara</h4>
                                <p>Kepala Seksi Pembangunan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
/* ============================================
   BRIGHT RUSTIC VILLAGE INTERFACE
   ============================================ */

:root {
    --primary: #9CAF88;
    --primary-dark: #7A8F64;
    --primary-light: #B5C4A3;
    --accent: #F6C7A1;
    --text-dark: #2F2F2F;
    --text-soft: #3A312B;
    --text-light: #444444;
    --bg-card: rgba(253, 251, 247, 0.92);
    --shadow: 0 10px 30px rgba(58, 49, 43, 0.08);
    --shadow-hover: 0 20px 40px rgba(58, 49, 43, 0.12);
    --transition: all 0.3s ease;
}

/* Background Layers */
.ghibli-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, #B9DCFF 0%, #D4E8FF 40%, #FAF6F0 100%);
    z-index: 0;
    pointer-events: none;
}

.ghibli-bg-rice-fields {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/sawah-lanscape.png') center/cover no-repeat;
    opacity: 0.08;
    z-index: 0;
    pointer-events: none;
}

.ghibli-bg-atmosphere {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= ASSETS_URL ?>/images/06-atmospheric-elements.png') center bottom/cover no-repeat;
    opacity: 0.1;
    z-index: 0;
    pointer-events: none;
}

.ghibli-content-wrapper {
    position: relative;
    z-index: 2;
}

/* Floating Leaves */
.floating-leaves {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.leaf {
    position: absolute;
    width: 25px;
    height: auto;
    opacity: 0.35;
    animation: fallLeaf 12s linear infinite;
}

.leaf-1 { top: -10%; left: 10%; animation-delay: 0s; width: 20px; }
.leaf-2 { top: -10%; left: 30%; animation-delay: 3s; width: 18px; }
.leaf-3 { top: -10%; left: 60%; animation-delay: 6s; width: 25px; }
.leaf-4 { top: -10%; left: 85%; animation-delay: 9s; width: 22px; }

@keyframes fallLeaf {
    0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.35; }
    90% { opacity: 0.35; }
    100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
}

/* Hero Section */
.ghibli-profile-hero {
    padding: 50px 0 70px;
}

.ghibli-card-hero {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 32px;
    padding: 2rem;
    border: 1px solid rgba(156, 175, 136, 0.25);
    box-shadow: var(--shadow);
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(156, 175, 136, 0.12);
    padding: 0.3rem 1rem;
    border-radius: 100px;
    margin-bottom: 1.5rem;
}

.badge-icon-img {
    width: 22px;
    height: auto;
}

.hero-badge span {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--primary-dark);
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.hero-title .highlight {
    color: var(--primary-dark);
}

.hero-divider {
    width: 70px;
    margin-bottom: 1rem;
}

.hero-divider img {
    width: 100%;
    opacity: 0.5;
}

.hero-description {
    font-size: 0.95rem;
    color: var(--text-light);
    font-weight: 500;
    margin-bottom: 1rem;
}

.hero-text {
    color: var(--text-soft);
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-primary, .btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    border-radius: 40px;
    font-weight: 600;
    transition: var(--transition);
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.btn-outline {
    background: transparent;
    border: 1.5px solid rgba(156, 175, 136, 0.4);
    color: var(--text-dark);
}

.btn-outline:hover {
    border-color: var(--primary);
    background: rgba(156, 175, 136, 0.08);
}

/* Hero Image */
.hero-image-wrapper {
    position: relative;
}

.hero-image {
    width: 100%;
    border-radius: 32px;
    box-shadow: var(--shadow);
}

.floating-card {
    position: absolute;
    background: white;
    border-radius: 16px;
    padding: 0.4rem 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.floating-card-1 { bottom: -10px; left: -10px; }
.floating-card-2 { top: -10px; right: -10px; }

.float-icon { width: 20px; height: auto; }
.floating-card span { font-size: 0.9rem; font-weight: 700; color: var(--text-dark); }
.floating-card small { font-size: 0.6rem; color: var(--text-light); }

/* Glass Card */
.glass-card {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 28px;
    padding: 1.75rem;
    border: 1px solid rgba(156, 175, 136, 0.2);
    height: 100%;
    transition: var(--transition);
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.card-icon {
    width: 55px;
    height: 55px;
    background: rgba(156, 175, 136, 0.1);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.card-icon img { width: 35px; height: auto; opacity: 0.7; }
.card-title { font-size: 1.3rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.75rem; }
.card-text { color: var(--text-soft); line-height: 1.6; }

/* Contact Items */
.contact-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.contact-item {
    display: flex;
    gap: 0.8rem;
    align-items: flex-start;
}

.contact-icon {
    width: 35px;
    height: 35px;
    background: rgba(156, 175, 136, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-icon i { font-size: 1rem; color: var(--primary); }

.contact-detail strong {
    display: block;
    color: var(--text-dark);
    font-weight: 600;
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.contact-detail p {
    color: var(--text-soft);
    margin: 0;
    font-size: 0.85rem;
}

/* Statistics */
.stats-section, .vision-mission-section, .organization-section {
    padding: 50px 0;
}

.section-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-badge {
    display: inline-block;
    background: rgba(156, 175, 136, 0.12);
    padding: 0.25rem 1rem;
    border-radius: 100px;
    margin-bottom: 0.75rem;
}

.section-badge span {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--primary-dark);
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.section-title .highlight { color: var(--primary-dark); }
.section-subtitle { color: var(--text-light); font-size: 0.85rem; }

.stat-card {
    background: var(--bg-card);
    backdrop-filter: blur(8px);
    border-radius: 24px;
    padding: 1.25rem;
    border: 1px solid rgba(156, 175, 136, 0.15);
    transition: var(--transition);
    text-align: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}

.stat-icon i { font-size: 1.5rem; }

.stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.stat-unit {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-light);
}

.stat-label {
    color: var(--text-soft);
    font-size: 0.8rem;
    font-weight: 500;
}

/* Visi Misi */
.vision-card, .mission-card {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 28px;
    padding: 1.75rem;
    border: 1px solid rgba(156, 175, 136, 0.2);
    height: 100%;
    transition: var(--transition);
}

.vision-card:hover, .mission-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.card-header-vm {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1.25rem;
}

.icon-wrapper-img {
    width: 55px;
    height: 55px;
    background: rgba(156, 175, 136, 0.12);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-wrapper-img img { width: 35px; height: auto; opacity: 0.8; }
.card-header-vm h2 { font-size: 1.3rem; font-weight: 700; color: var(--text-dark); margin: 0; }
.vision-text { font-size: 1rem; line-height: 1.7; color: var(--text-soft); font-style: italic; }

.mission-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.mission-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    padding: 0.6rem;
    background: rgba(156, 175, 136, 0.05);
    border-radius: 16px;
}

.mission-number {
    width: 30px;
    height: 30px;
    background: var(--primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.mission-text {
    color: var(--text-soft);
    line-height: 1.5;
    font-size: 0.85rem;
}

/* ============================================
   ORGANIZATION CHART
   ============================================ */

.org-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem 0;
}

/* Level 1 */
.org-node.level-1 {
    text-align: center;
    margin-bottom: 0.5rem;
}

/* Cards */
.org-card {
    background: white;
    border-radius: 24px;
    padding: 1.25rem;
    text-align: center;
    min-width: 220px;
    box-shadow: 0 8px 20px rgba(58, 49, 43, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.org-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(58, 49, 43, 0.15);
}

.org-card.leader {
    background: linear-gradient(135deg, #9CAF88 0%, #7A8F64 100%);
    border: none;
}

.org-card.leader h4, .org-card.leader p { color: white; }

.org-card.sekretaris {
    background: linear-gradient(135deg, #F6C7A1 0%, #F0B885 100%);
    border: none;
}

.org-card.sekretaris h4, .org-card.sekretaris p { color: #2F2F2F; }

.org-card.kasi {
    background: linear-gradient(135deg, #B9DCFF 0%, #9CC4E8 100%);
    border: none;
    margin-bottom: 0.75rem;
    min-width: 220px;
}

/* Kasi Group */
.org-kasi-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Avatars */
.org-avatar {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    overflow: hidden;
}

.org-avatar img { 
    width: 55px; 
    height: auto; 
    border-radius: 50%;
    object-fit: cover;
}

.org-card h4 { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
.org-card p { font-size: 0.7rem; font-weight: 500; opacity: 0.8; margin: 0; }

/* Garis - Lines */
.org-line-vertical {
    width: 2px;
    height: 35px;
    background: linear-gradient(180deg, #9CAF88, #B5C4A3);
    margin: 0.5rem auto;
}

.org-line-vertical.small { height: 20px; }

.org-line-horizontal {
    display: flex;
    align-items: center;
    width: 70%;
    max-width: 600px;
    margin: 0.5rem auto;
}

.line-left, .line-right {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, #9CAF88, #B5C4A3);
}

.line-center {
    width: 20px;
    text-align: center;
}

.line-center .dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #9CAF88;
    border-radius: 50%;
}

/* Level 2 */
.org-level-2 {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
    width: 100%;
    margin: 0.5rem 0;
}

.org-branch {
    flex: 1;
    min-width: 220px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Responsive */
@media (max-width: 992px) {
    .org-level-2 {
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
    }
    
    .org-branch {
        width: 100%;
        max-width: 300px;
    }
    
    .org-line-horizontal {
        display: none;
    }
    
    .org-line-vertical {
        display: block;
    }
}

@media (max-width: 768px) {
    .hero-title { font-size: 1.8rem; }
    .section-title { font-size: 1.5rem; }
    .ghibli-card-hero { padding: 1.5rem; }
    .floating-card { display: none; }
    .stat-value { font-size: 1.5rem; }
    .card-header-vm { flex-direction: column; text-align: center; }
    .leaf { display: none; }
    
    .org-card {
        min-width: 180px;
    }
    
    .org-card.kasi {
        min-width: 180px;
    }
    
    .org-branch {
        min-width: 180px;
    }
}

@media (max-width: 576px) {
    .hero-buttons { flex-direction: column; }
    .btn-primary, .btn-outline { justify-content: center; }
    
    .org-card {
        min-width: 160px;
        padding: 0.75rem;
    }
    
    .org-card.kasi {
        min-width: 160px;
    }
    
    .org-avatar {
        width: 50px;
        height: 50px;
    }
    
    .org-avatar img {
        width: 40px;
    }
    
    .org-card h4 {
        font-size: 0.85rem;
    }
    
    .org-card p {
        font-size: 0.6rem;
    }
}
</style>

<script>
// Counter animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200;
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-target'));
        let current = 0;
        const increment = Math.ceil(target / speed);
        
        const update = () => {
            current += increment;
            if (current >= target) {
                counter.innerText = target;
            } else {
                counter.innerText = current;
                setTimeout(update, 16);
            }
        };
        update();
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });
    
    counters.forEach(counter => observer.observe(counter));
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>