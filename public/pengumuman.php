<?php
$page_title = 'Pengumuman Desa';
$activePage = 'profil';
$is_admin_page = false;

require_once __DIR__ . '/../config/db.php';

$announcementId = (int) get_get('id');
$currentAnnouncement = null;
$announcements = [];

try {
    if ($announcementId > 0) {
        $stmt = $pdo->prepare('SELECT id, judul, isi, created_at FROM pengumuman WHERE id = :id AND is_published = 1 LIMIT 1');
        $stmt->execute(['id' => $announcementId]);
        $currentAnnouncement = $stmt->fetch();
    }

    $stmt = $pdo->prepare('SELECT id, judul, isi, created_at FROM pengumuman WHERE is_published = 1 ORDER BY created_at DESC LIMIT 10');
    $stmt->execute();
    $announcements = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Error fetching pengumuman public detail: ' . $e->getMessage());
    $announcements = [];
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Pengumuman Desa -->
<!-- Background Layers -->
<div class="ghibli-bg-sky"></div>
<div class="ghibli-bg-rice-fields"></div>
<div class="ghibli-bg-atmosphere"></div>

<!-- Floating Clouds -->
<img src="../assets/images/cloud_1.png" alt="" class="ghibli-cloud-1">
<img src="../assets/images/cloud_4.png" alt="" class="ghibli-cloud-2">
<img src="../assets/images/cloud_1.png" alt="" class="ghibli-cloud-3">

<!-- Floating Leaves -->
<img src="../assets/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-1">
<img src="../assets/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-2">
<img src="../assets/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-3">
<img src="../assets/images/18-floating-leaves-close.png" alt="" class="ghibli-leaf-4">

<!-- Ghibli Characters Atmosphere -->
<img src="../assets/images/totoro.png" alt="Totoro" class="ghibli-totoro">
<img src="../assets/images/jiji.png" alt="Jiji" class="ghibli-jiji">
<img src="../assets/images/susuwatari-smile.png" alt="Susuwatari" class="ghibli-susuwatari-1">
<img src="../assets/images/little-totoro.png" alt="Little Totoro" class="ghibli-little-totoro">

<div class="ghibli-content-wrapper">

<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="ghibli-header-wrapper">
                <div class="header-left">
                    <div class="section-badge">WARTA KELURAHAN</div>
                    <h1 class="header-title">Informasi Publik & Warta Desa</h1>
                    <p class="header-subtitle">Buka pengumuman lengkap untuk mengetahui isi pemberitahuan resmi Kelurahan Sasi</p>
                </div>
                <div class="header-right">
                    <a href="<?= PUBLIC_URL ?>/profil-desa.php" class="btn-back">
                        <span class="back-arrow">←</span>
                        Kembali ke Profil Desa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Announcement Detail -->
    <?php if ($announcementId > 0 && $currentAnnouncement): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="ghibli-detail-card">
                    <div class="detail-header">
                        <div class="detail-date">
                            <span class="date-day"><?= date('d', strtotime($currentAnnouncement['created_at'])) ?></span>
                            <span class="date-month"><?= date('M Y', strtotime($currentAnnouncement['created_at'])) ?></span>
                        </div>
                        <div class="detail-badge">PENGUMUMAN RESMI</div>
                    </div>
                    <h2 class="detail-title"><?= h($currentAnnouncement['judul']) ?></h2>
                    <div class="detail-divider"></div>
                    <div class="detail-content">
                        <?= nl2br(h($currentAnnouncement['isi'])) ?>
                    </div>
                    <div class="detail-footer">
                        <div class="footer-decoration">
                            <img src="../assets/images/divider-squiggly-line.png" alt="" class="divider-line">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($announcementId > 0): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="ghibli-not-found">
                    <div class="not-found-icon">📄</div>
                    <h3 class="not-found-title">Pengumuman Tidak Ditemukan</h3>
                    <p class="not-found-text">Pengumuman yang Anda cari tidak tersedia atau belum dipublikasikan.</p>
                    <a href="<?= PUBLIC_URL ?>/pengumuman.php" class="btn-back-home">Lihat Semua Pengumuman</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Archive Section -->
    <div class="row">
        <div class="col-12">
            <div class="archive-header">
                <div class="archive-icon">
                    <div class="icon-bg">📰</div>
                </div>
                <div>
                    <h3 class="archive-title">Arsip Pengumuman</h3>
                    <p class="archive-subtitle">Kumpulan warta dan informasi resmi Kelurahan Sasi</p>
                </div>
            </div>

            <?php if (empty($announcements)): ?>
                <div class="ghibli-empty-archive">
                    <p>Belum ada pengumuman yang diterbitkan untuk saat ini.</p>
                    <p class="small">Silakan cek kembali di lain waktu untuk informasi terbaru dari Kelurahan Sasi.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($announcements as $news): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="ghibli-archive-card">
                                <div class="card-badge">PENGUMUMAN</div>
                                <div class="card-date">
                                    <span class="date-icon">📅</span>
                                    <span class="date-text"><?= date('d M Y', strtotime($news['created_at'])) ?></span>
                                </div>
                                <h4 class="card-title"><?= h($news['judul']) ?></h4>
                                <p class="card-excerpt">
                                    <?= h(substr(strip_tags($news['isi']), 0, 120)) ?><?= strlen(strip_tags($news['isi'])) > 120 ? '...' : '' ?>
                                </p>
                                <a href="<?= PUBLIC_URL ?>/pengumuman.php?id=<?= $news['id'] ?>" class="card-read-more">
                                    <span>Baca Selengkapnya</span>
                                    <span class="more-arrow">→</span>
                                </a>
                                <div class="card-decoration">
                                    <img src="../assets/images/susuwatari-cute.png" alt="" class="decoration-soot">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer Landscape -->
<div class="ghibli-footer-landscape">
    <img src="../assets/images/05-sunset-footer-landscape.png" alt="Sunset in Sasi village" class="footer-sunset-image">
</div>

</div>

<style>
/* Root Variables - Bright Rustic Village Interface */
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
    --ghibli-shadow-hover: rgba(58, 49, 43, 0.12);
}

/* Background Layers */
.ghibli-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, var(--ghibli-sky) 0%, #D4E8FF 50%, var(--ghibli-bg-beige) 100%);
    z-index: 0;
    pointer-events: none;
}

.ghibli-bg-rice-fields {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('../assets/images/rice-fields.png') center/cover no-repeat;
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
    background: url('../assets/images/06-atmospheric-elements.png') center bottom/cover no-repeat;
    opacity: 0.12;
    mix-blend-mode: soft-light;
    z-index: 0;
    pointer-events: none;
}

.ghibli-content-wrapper {
    position: relative;
    z-index: 2;
}

/* Cloud Animations */
@keyframes floatCloud {
    0% { transform: translateX(-100px) translateY(0); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateX(100vw) translateY(20px); opacity: 0; }
}

@keyframes floatCloudSlow {
    0% { transform: translateX(-100px) translateY(0); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateX(100vw) translateY(-10px); opacity: 0; }
}

@keyframes floatCloudMedium {
    0% { transform: translateX(-100px) translateY(0); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateX(100vw) translateY(15px); opacity: 0; }
}

.ghibli-cloud-1 {
    position: fixed;
    top: 10%;
    left: -100px;
    width: 120px;
    z-index: 9;
    pointer-events: none;
    opacity: 0.4;
    animation: floatCloud 25s ease-in-out infinite;
}

.ghibli-cloud-2 {
    position: fixed;
    top: 25%;
    left: -100px;
    width: 90px;
    z-index: 9;
    pointer-events: none;
    opacity: 0.3;
    animation: floatCloudSlow 35s ease-in-out infinite;
    animation-delay: 5s;
}

.ghibli-cloud-3 {
    position: fixed;
    top: 60%;
    left: -100px;
    width: 150px;
    z-index: 9;
    pointer-events: none;
    opacity: 0.3;
    animation: floatCloudMedium 45s ease-in-out infinite;
    animation-delay: 12s;
}

/* Leaf Animations */
@keyframes fallLeaf1 {
    0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
}

@keyframes fallLeaf2 {
    0% { transform: translateY(-10vh) rotate(0deg) scaleX(-1); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateY(110vh) rotate(320deg); opacity: 0; }
}

@keyframes fallLeaf3 {
    0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateY(110vh) rotate(400deg); opacity: 0; }
}

@keyframes fallLeaf4 {
    0% { transform: translateY(-10vh) rotate(0deg) scaleX(-1); opacity: 0; }
    10% { opacity: 0.4; }
    90% { opacity: 0.4; }
    100% { transform: translateY(110vh) rotate(280deg); opacity: 0; }
}

.ghibli-leaf-1 {
    position: fixed;
    top: -10%;
    left: 10%;
    width: 28px;
    z-index: 9;
    pointer-events: none;
    animation: fallLeaf1 8s ease-in-out infinite;
}

.ghibli-leaf-2 {
    position: fixed;
    top: -10%;
    left: 30%;
    width: 22px;
    z-index: 9;
    pointer-events: none;
    animation: fallLeaf2 10s ease-in-out infinite;
    animation-delay: 2s;
}

.ghibli-leaf-3 {
    position: fixed;
    top: -10%;
    left: 60%;
    width: 32px;
    z-index: 9;
    pointer-events: none;
    animation: fallLeaf3 9s ease-in-out infinite;
    animation-delay: 4s;
}

.ghibli-leaf-4 {
    position: fixed;
    top: -10%;
    left: 85%;
    width: 25px;
    z-index: 9;
    pointer-events: none;
    animation: fallLeaf4 12s ease-in-out infinite;
    animation-delay: 1s;
}

/* Character Animations */
@keyframes totoroBreathe {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

@keyframes jijiFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
}

@keyframes susuwatariRun {
    0%, 100% { transform: translateX(0) translateY(0); }
    50% { transform: translateX(4px) translateY(-2px); }
}

.ghibli-totoro {
    position: fixed;
    bottom: 15px;
    left: 15px;
    width: 90px;
    z-index: 10;
    pointer-events: none;
    opacity: 0.35;
    animation: totoroBreathe 4s ease-in-out infinite;
}

.ghibli-jiji {
    position: fixed;
    top: 80px;
    right: 20px;
    width: 45px;
    z-index: 10;
    pointer-events: none;
    opacity: 0.3;
    animation: jijiFloat 4s ease-in-out infinite;
}

.ghibli-susuwatari-1 {
    position: fixed;
    bottom: 20px;
    right: 30px;
    width: 22px;
    z-index: 10;
    pointer-events: none;
    opacity: 0.4;
    animation: susuwatariRun 3s ease-in-out infinite;
}

.ghibli-little-totoro {
    position: fixed;
    bottom: 10px;
    left: 70px;
    width: 42px;
    z-index: 10;
    pointer-events: none;
    opacity: 0.3;
    animation: totoroBreathe 3.5s ease-in-out infinite;
    animation-delay: 0.5s;
}

/* Header Section */
.ghibli-header-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.header-left {
    flex: 1;
}

.section-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--ghibli-sage);
    margin-bottom: 0.75rem;
}

.header-title {
    font-family: 'Poppins', 'Bricolage Grotesque', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.header-subtitle {
    font-size: 0.9rem;
    color: var(--ghibli-text-light);
    opacity: 0.7;
    margin-bottom: 0;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: transparent;
    border: 1.5px solid var(--ghibli-border);
    color: var(--ghibli-text-dark);
    padding: 0.6rem 1.25rem;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background-color: rgba(156, 175, 136, 0.08);
    border-color: var(--ghibli-sage);
    text-decoration: none;
    color: var(--ghibli-text-dark);
}

.back-arrow {
    font-size: 1rem;
    transition: transform 0.2s ease;
}

.btn-back:hover .back-arrow {
    transform: translateX(-3px);
}

/* Detail Card */
.ghibli-detail-card {
    background-color: var(--ghibli-bg-cream);
    border-radius: 32px;
    border: 1px solid var(--ghibli-border);
    box-shadow: 0 8px 24px var(--ghibli-shadow);
    overflow: hidden;
    padding: 2rem;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.detail-date {
    display: flex;
    align-items: baseline;
    gap: 0.25rem;
    background-color: rgba(156, 175, 136, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 40px;
}

.detail-date .date-day {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ghibli-sage-dark);
}

.detail-date .date-month {
    font-size: 0.75rem;
    color: var(--ghibli-text-light);
    text-transform: uppercase;
}

.detail-badge {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: var(--ghibli-sage);
    background-color: rgba(156, 175, 136, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 40px;
}

.detail-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 1rem;
    line-height: 1.3;
}

.detail-divider {
    width: 60px;
    height: 3px;
    background-color: var(--ghibli-peach);
    margin-bottom: 1.5rem;
    border-radius: 3px;
}

.detail-content {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--ghibli-text-light);
    white-space: pre-line;
}

.detail-footer {
    margin-top: 2rem;
    padding-top: 1rem;
    text-align: center;
}

.divider-line {
    max-width: 150px;
    opacity: 0.3;
}

/* Not Found */
.ghibli-not-found {
    text-align: center;
    padding: 3rem;
    background-color: var(--ghibli-bg-warm);
    border-radius: 32px;
    border: 1px solid var(--ghibli-border);
}

.not-found-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.not-found-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.35rem;
    font-weight: 600;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
}

.not-found-text {
    color: var(--ghibli-text-light);
    margin-bottom: 1.5rem;
}

.btn-back-home {
    display: inline-block;
    background-color: var(--ghibli-sage);
    color: #FFFFFF;
    padding: 0.6rem 1.5rem;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.btn-back-home:hover {
    background-color: var(--ghibli-sage-dark);
    transform: translateY(-2px);
    text-decoration: none;
    color: #FFFFFF;
}

/* Archive Header */
.archive-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.archive-icon .icon-bg {
    width: 48px;
    height: 48px;
    background-color: rgba(156, 175, 136, 0.12);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.archive-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.25rem;
}

.archive-subtitle {
    font-size: 0.85rem;
    color: var(--ghibli-text-light);
    opacity: 0.7;
    margin-bottom: 0;
}

/* Archive Cards */
.ghibli-archive-card {
    background-color: var(--ghibli-bg-cream);
    border-radius: 24px;
    border: 1px solid var(--ghibli-border);
    padding: 1.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
}

.ghibli-archive-card:hover {
    transform: translateY(-4px);
    border-color: var(--ghibli-sage-light);
    box-shadow: 0 12px 28px var(--ghibli-shadow-hover);
}

.card-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: var(--ghibli-sage);
    background-color: rgba(156, 175, 136, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 40px;
}

.card-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.date-icon {
    font-size: 0.75rem;
    color: var(--ghibli-sage);
}

.date-text {
    font-size: 0.7rem;
    color: var(--ghibli-text-light);
    opacity: 0.6;
}

.card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.card-excerpt {
    font-size: 0.85rem;
    line-height: 1.6;
    color: var(--ghibli-text-light);
    margin-bottom: 1.25rem;
    flex: 1;
}

.card-read-more {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ghibli-sage);
    text-decoration: none;
    transition: gap 0.2s ease;
    margin-top: auto;
}

.card-read-more:hover {
    gap: 0.75rem;
    text-decoration: none;
    color: var(--ghibli-sage-dark);
}

.more-arrow {
    transition: transform 0.2s ease;
}

.card-read-more:hover .more-arrow {
    transform: translateX(3px);
}

.card-decoration {
    position: absolute;
    bottom: 8px;
    right: 12px;
}

.decoration-soot {
    width: 24px;
    opacity: 0.25;
    animation: susuwatariRun 2.5s ease-in-out infinite;
}

/* Empty Archive */
.ghibli-empty-archive {
    text-align: center;
    padding: 3rem;
    background-color: var(--ghibli-bg-warm);
    border-radius: 24px;
    border: 1px solid var(--ghibli-border);
}

.ghibli-empty-archive p {
    margin-bottom: 0.5rem;
    color: var(--ghibli-text-light);
}

.ghibli-empty-archive p.small {
    font-size: 0.8rem;
    opacity: 0.6;
}

/* Footer Landscape */
.ghibli-footer-landscape {
    margin-top: 4rem;
    position: relative;
    z-index: 2;
    overflow: hidden;
}

.footer-sunset-image {
    width: 100%;
    height: auto;
    display: block;
    opacity: 0.6;
    margin-top: -2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .header-title {
        font-size: 1.5rem;
    }
    
    .ghibli-detail-card {
        padding: 1.25rem;
    }
    
    .detail-title {
        font-size: 1.35rem;
    }
    
    .detail-content {
        font-size: 0.9rem;
    }
    
    .archive-title {
        font-size: 1.1rem;
    }
    
    .ghibli-totoro {
        width: 60px;
    }
    
    .ghibli-jiji {
        width: 35px;
        top: 70px;
    }
    
    .ghibli-little-totoro {
        width: 30px;
        left: 50px;
    }
    
    .card-badge {
        position: relative;
        top: auto;
        right: auto;
        display: inline-block;
        margin-bottom: 0.5rem;
        width: fit-content;
    }
}

@media (max-width: 480px) {
    .ghibli-header-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .ghibli-jiji {
        display: none;
    }
    
    .ghibli-totoro {
        width: 50px;
    }
    
    .detail-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .archive-header {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>