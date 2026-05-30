<?php
$page_title = 'Pengajuan Surat Online';
$activePage = 'layanan';
$is_admin_page = false;

require_once __DIR__ . '/../config/db.php';

$successMessage = get_flash('success');
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
$debugError = $_SESSION['debug_error'] ?? null;
unset($_SESSION['form_errors'], $_SESSION['form_data'], $_SESSION['debug_error']);

$jenis_list = $pdo->query('
    SELECT id, kode, nama, deskripsi, persyaratan 
    FROM jenis_surat 
    WHERE is_active = 1 
    ORDER BY nama
')->fetchAll();

$pekerjaan_list = [
    'Belum/Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'Pegawai Negeri Sipil (PNS)',
    'TNI/Polri', 'Karyawan Swasta', 'Wiraswasta/Wirausaha', 'Pedagang', 'Buruh',
    'Guru/Dosen', 'Dokter/Tenaga Kesehatan', 'Perawat/Bidan', 'Pensiunan', 'Ibu Rumah Tangga',
    'Pelajar/Mahasiswa', 'Pengacara/Notaris', 'Seniman/Budayawan', 'Sopir', 'Montir/Mekanik',
    'Tukang Kayu', 'Tukang Batu', 'Arsitek', 'Akuntan', 'Konsultan', 'Marketing/Sales',
    'Administrasi/Staff', 'Manajer', 'Direktur', 'Lainnya'
];

function old($key, $data) {
    return h($data[$key] ?? '');
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Bright Rustic Village Interface - Layanan Surat -->
<div class="ghibli-bg-sky"></div>
<div class="ghibli-bg-rice-fields"></div>
<div class="ghibli-bg-atmosphere"></div>

<!-- Decorative Characters -->
<div class="decor-characters">
    <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="" class="decor-susuwatari" loading="lazy">
</div>

<div class="ghibli-content-wrapper">

<div class="container py-5">
    <!-- Header dengan Ilustrasi -->
    <div class="form-header text-center mb-5">
        <div class="header-illustration">
            <img src="<?= ASSETS_URL ?>/images/village-boy.png" alt="" class="header-img">
        </div>
        <h1 class="form-header-title">Pengajuan Surat Online</h1>
        <p class="form-header-subtitle">Ajukan permohonan surat dengan mudah, cepat, dan tanpa antri</p>
        <div class="header-divider">
            <img src="<?= ASSETS_URL ?>/images/divider-squiggly-line.png" alt="">
        </div>
    </div>

    <!-- Success Alert -->
    <?php if ($successMessage): ?>
        <div class="success-popup">
            <div class="success-icon">✓</div>
            <div class="success-content">
                <h4>Permohonan Berhasil!</h4>
                <p><?= h($successMessage) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Error Alerts -->
    <?php if (!empty($formErrors)): ?>
        <div class="error-popup">
            <div class="error-icon">!</div>
            <div class="error-content">
                <h4>Periksa Kembali</h4>
                <ul>
                    <?php foreach ($formErrors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content dengan Sidebar Kiri (Progress Vertikal) dan Form Kanan -->
    <div class="two-column-layout">
        <!-- Sidebar Kiri - Progress Steps Vertikal dengan Garis -->
        <div class="progress-sidebar">
            <div class="vertical-progress">
                <div class="vertical-steps-container">
                    <!-- Step 1 -->
                    <div class="vertical-step-wrapper">
                        <div class="vertical-step active" data-step="1">
                            <div class="vertical-step-number">1</div>
                            <div class="vertical-step-content">
                                <div class="vertical-step-label">Data Pribadi</div>
                                <div class="vertical-step-desc">Isi informasi diri Anda</div>
                            </div>
                        </div>
                        <div class="vertical-line"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="vertical-step-wrapper">
                        <div class="vertical-step" data-step="2">
                            <div class="vertical-step-number">2</div>
                            <div class="vertical-step-content">
                                <div class="vertical-step-label">Detail Surat</div>
                                <div class="vertical-step-desc">Pilih jenis dan keperluan</div>
                            </div>
                        </div>
                        <div class="vertical-line"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="vertical-step-wrapper">
                        <div class="vertical-step" data-step="3">
                            <div class="vertical-step-number">3</div>
                            <div class="vertical-step-content">
                                <div class="vertical-step-label">Upload Berkas</div>
                                <div class="vertical-step-desc">Lampirkan dokumen</div>
                            </div>
                        </div>
                        <div class="vertical-line"></div>
                    </div>

                    <!-- Step 4 -->
                    <div class="vertical-step-wrapper">
                        <div class="vertical-step" data-step="4">
                            <div class="vertical-step-number">4</div>
                            <div class="vertical-step-content">
                                <div class="vertical-step-label">Konfirmasi</div>
                                <div class="vertical-step-desc">Periksa dan kirim</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="ghibli-form-card">
            <div class="form-card-body">
                <div id="nik-status-message" class="status-message"></div>
                
                <form action="<?= PROSES_URL ?>/surat.php" method="post" enctype="multipart/form-data" id="suratForm">
                    <input type="hidden" name="action" value="submit">

                    <!-- STEP 1: Data Pribadi - Susuwatari Cute -->
                    <div class="form-step active" id="step1">
                        <div class="step-header">
                            <div class="step-icon">
                                <img src="<?= ASSETS_URL ?>/images/susuwatari-cute.png" alt="Susuwatari Cute" onerror="this.src='<?= ASSETS_URL ?>/images/susuwatari.png'">
                            </div>
                            <h3>Data Pribadi</h3>
                            <p>Isi data diri Anda dengan lengkap</p>
                        </div>

                        <div class="step-content">
                            <div class="form-group nik-group">
                                <label class="form-label">NIK <span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-qr-code-scan"></i>
                                    <input type="text" id="nik" name="nik" class="ghibli-input" maxlength="16" value="<?= old('nik', $formData) ?>" required 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16); checkNIK(this.value);"
                                           placeholder="Masukkan 16 digit NIK">
                                </div>
                                <small class="form-hint">Data akan terisi otomatis jika NIK terdaftar</small>
                                <div id="nik-loading" class="loading-spinner"><i class="bi bi-hourglass-split"></i> Mengecek data...</div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="ghibli-input" value="<?= old('nama_lengkap', $formData) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="ghibli-input" value="<?= old('tempat_lahir', $formData) ?>">
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="ghibli-input" value="<?= old('tanggal_lahir', $formData) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select id="gender" name="gender" class="ghibli-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki" <?= old('gender', $formData) === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="Perempuan" <?= old('gender', $formData) === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Pekerjaan</label>
                                <select id="pekerjaan" name="pekerjaan" class="ghibli-select">
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <?php foreach ($pekerjaan_list as $pekerjaan): ?>
                                        <option value="<?= h($pekerjaan) ?>" <?= old('pekerjaan', $formData) === $pekerjaan ? 'selected' : '' ?>><?= h($pekerjaan) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Alamat KTP <span class="required">*</span></label>
                                <textarea id="alamat" name="alamat" class="ghibli-input" rows="2" required><?= old('alamat', $formData) ?></textarea>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-4">
                                    <label class="form-label">RT</label>
                                    <input type="text" id="rt" name="rt" class="ghibli-input" maxlength="2" value="<?= old('rt', $formData) ?>">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">RW</label>
                                    <input type="text" id="rw" name="rw" class="ghibli-input" maxlength="2" value="<?= old('rw', $formData) ?>">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Dusun</label>
                                    <input type="text" id="dusun" name="dusun" class="ghibli-input" value="<?= old('dusun', $formData) ?>">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">No. WhatsApp <span class="required">*</span></label>
                                <div class="input-with-icon">
                                    <i class="bi bi-whatsapp"></i>
                                    <input type="tel" id="no_hp" name="no_hp" class="ghibli-input" value="<?= old('no_hp', $formData) ?>" required 
                                           placeholder="+6281234567890"
                                           oninput="formatWhatsappNumber(this)">
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-next" onclick="nextStep(2)">Lanjut ke Detail Surat <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2: Detail Surat - Susuwatari Smile -->
                    <div class="form-step" id="step2">
                        <div class="step-header">
                            <div class="step-icon">
                                <img src="<?= ASSETS_URL ?>/images/susuwatari-smile.png" alt="Susuwatari Smile" onerror="this.src='<?= ASSETS_URL ?>/images/susuwatari.png'">
                            </div>
                            <h3>Detail Permohonan</h3>
                            <p>Pilih jenis surat dan tulis keperluan</p>
                        </div>

                        <div class="step-content">
                            <div class="form-group">
                                <label class="form-label">Jenis Surat <span class="required">*</span></label>
                                <select name="jenis_surat_id" id="jenis_surat_id" class="ghibli-select" required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    <?php foreach ($jenis_list as $jenis): ?>
                                        <option value="<?= $jenis['id'] ?>" <?= (isset($formData['jenis_surat_id']) && $formData['jenis_surat_id'] == $jenis['id']) ? 'selected' : '' ?>>
                                            <?= h($jenis['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label class="form-label">Keperluan Pengajuan</label>
                                <textarea name="keperluan" class="ghibli-input" rows="4" placeholder="Tulis maksud dan tujuan pengajuan surat..."><?= old('keperluan', $formData) ?></textarea>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="prevStep(1)"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn-next" onclick="nextStep(3)">Lanjut ke Upload <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- STEP 3: Upload Berkas - Susuwatari Scare -->
                    <div class="form-step" id="step3">
                        <div class="step-header">
                            <div class="step-icon">
                                <img src="<?= ASSETS_URL ?>/images/susuwatari-scare.png" alt="Susuwatari Scare" onerror="this.src='<?= ASSETS_URL ?>/images/susuwatari.png'">
                            </div>
                            <h3>Upload Berkas</h3>
                            <p>Unggah dokumen pendukung</p>
                        </div>

                        <div class="step-content">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="upload-card" data-for="file_ktp">
                                        <input type="file" id="file_ktp" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <div class="upload-preview">
                                            <div class="upload-icon-large">
                                                <i class="bi bi-card-image"></i>
                                            </div>
                                            <p>Scan / Foto KTP</p>
                                            <small>Klik untuk upload</small>
                                            <span class="file-name"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="upload-card" data-for="file_kk">
                                        <input type="file" id="file_kk" name="file_kk" accept=".jpg,.jpeg,.png,.pdf" required>
                                        <div class="upload-preview">
                                            <div class="upload-icon-large">
                                                <i class="bi bi-files"></i>
                                            </div>
                                            <p>Scan / Foto KK</p>
                                            <small>Klik untuk upload</small>
                                            <span class="file-name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="upload-info"><i class="bi bi-info-circle"></i> Format: JPG, PNG, PDF | Maks 2MB</p>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="prevStep(2)"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn-next" onclick="nextStep(4)">Review & Kirim <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- STEP 4: Konfirmasi & Preview - Susuwatari Default -->
                    <div class="form-step" id="step4">
                        <div class="step-header">
                            <div class="step-icon">
                                <img src="<?= ASSETS_URL ?>/images/susuwatari.png" alt="Susuwatari" onerror="this.src='<?= ASSETS_URL ?>/images/susuwatari.png'">
                            </div>
                            <h3>Konfirmasi Data</h3>
                            <p>Periksa kembali data Anda sebelum mengirim</p>
                        </div>

                        <div class="step-content">
                            <div class="preview-card">
                                <h4>Data Pribadi</h4>
                                <div class="preview-grid">
                                    <div><strong>NIK:</strong> <span id="preview_nik">-</span></div>
                                    <div><strong>Nama:</strong> <span id="preview_nama">-</span></div>
                                    <div><strong>Tempat Lahir:</strong> <span id="preview_tempat">-</span></div>
                                    <div><strong>Tgl Lahir:</strong> <span id="preview_tgl">-</span></div>
                                    <div><strong>Jenis Kelamin:</strong> <span id="preview_gender">-</span></div>
                                    <div><strong>Pekerjaan:</strong> <span id="preview_pekerjaan">-</span></div>
                                    <div><strong>Alamat:</strong> <span id="preview_alamat">-</span></div>
                                    <div><strong>RT/RW:</strong> <span id="preview_rtrw">-</span></div>
                                    <div><strong>Dusun:</strong> <span id="preview_dusun">-</span></div>
                                    <div><strong>No HP:</strong> <span id="preview_nohp">-</span></div>
                                </div>
                            </div>

                            <div class="preview-card mt-3">
                                <h4>Detail Permohonan</h4>
                                <div class="preview-grid">
                                    <div><strong>Jenis Surat:</strong> <span id="preview_jenis">-</span></div>
                                    <div><strong>Keperluan:</strong> <span id="preview_keperluan">-</span></div>
                                </div>
                            </div>

                            <div class="preview-card mt-3">
                                <h4>Berkas yang Diupload</h4>
                                <div class="preview-grid">
                                    <div><strong>KTP:</strong> <span id="preview_ktp">-</span></div>
                                    <div><strong>KK:</strong> <span id="preview_kk">-</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn-prev" onclick="prevStep(3)"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="submit" class="btn-submit"><i class="bi bi-send"></i> Kirim Permohonan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>

<style>
/* ============================================
   TWO COLUMN LAYOUT - VERTICAL PROGRESS WITH LINE
   ============================================ */

.two-column-layout {
    display: flex;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Progress Sidebar - Vertical Style dengan Garis */
.progress-sidebar {
    width: 280px;
    flex-shrink: 0;
}

.vertical-progress {
    background: rgba(253, 251, 247, 0.9);
    backdrop-filter: blur(8px);
    border-radius: 28px;
    padding: 1.5rem 1rem;
    border: 1px solid rgba(156, 175, 136, 0.2);
    position: sticky;
    top: 20px;
}

.vertical-steps-container {
    display: flex;
    flex-direction: column;
    position: relative;
}

.vertical-step-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
}

.vertical-step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 20px;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
    z-index: 2;
    background: transparent;
}

.vertical-step:hover {
    background: rgba(156, 175, 136, 0.08);
}

.vertical-step.active {
    background: linear-gradient(135deg, rgba(156, 175, 136, 0.12), rgba(246, 199, 161, 0.08));
}

.vertical-step.completed .vertical-step-number {
    background: #7A8F64;
    color: white;
}

.vertical-step-number {
    width: 40px;
    height: 40px;
    background: #E8E0D5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #5C4A3A;
    transition: all 0.3s;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
}

.vertical-step.active .vertical-step-number {
    background: #9CAF88;
    color: white;
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.3);
}

.vertical-step-content {
    flex: 1;
}

.vertical-step-label {
    font-weight: 700;
    font-size: 0.85rem;
    color: #1A1512;
    margin-bottom: 0.2rem;
}

.vertical-step.active .vertical-step-label {
    color: #7A8F64;
}

.vertical-step-desc {
    font-size: 0.65rem;
    color: #5C4A3A;
}

.vertical-step.active .vertical-step-desc {
    color: #2C241E;
}

/* Vertical Line Styling */
.vertical-line {
    width: 2px;
    height: 30px;
    background: linear-gradient(180deg, #E8E0D5, #D5CCBF);
    margin-left: 39px;
    margin-top: -5px;
    margin-bottom: -5px;
    transition: all 0.3s;
    position: relative;
    z-index: 1;
}

/* Garis yang sudah terlewati (completed) akan berubah warna */
.vertical-step.completed ~ .vertical-line,
.vertical-step-wrapper.completed .vertical-line {
    background: linear-gradient(180deg, #9CAF88, #7A8F64);
}

/* Wrapper completed styling */
.vertical-step-wrapper.completed .vertical-line {
    background: linear-gradient(180deg, #9CAF88, #7A8F64);
}

/* Main Form Card */
.ghibli-form-card {
    flex: 1;
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border-radius: 32px;
    box-shadow: 0 20px 40px rgba(26, 21, 18, 0.08);
    border: 1px solid rgba(156, 175, 136, 0.2);
    overflow: hidden;
}

.form-card-body {
    padding: 2rem;
}

/* Decorative Characters */
.decor-characters {
    position: fixed;
    pointer-events: none;
    z-index: 1;
}

.decor-susuwatari {
    position: fixed;
    bottom: 30px;
    right: 40px;
    width: 30px;
    opacity: 0.25;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.form-header {
    position: relative;
}

.header-illustration {
    margin-bottom: 1rem;
}

.header-illustration img {
    width: 70px;
    opacity: 0.6;
}

.form-header-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.5rem;
}

.form-header-subtitle {
    color: #2C241E;
    font-size: 0.9rem;
}

.header-divider {
    width: 80px;
    margin: 1rem auto;
}

.header-divider img {
    width: 100%;
    opacity: 0.5;
}

/* Form Steps */
.form-step {
    display: none;
    animation: fadeIn 0.5s ease;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-header {
    text-align: center;
    margin-bottom: 2rem;
}

.step-icon {
    width: 80px;
    height: 80px;
    background: rgba(156, 175, 136, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    transition: all 0.3s ease;
    animation: gentleFloat 3s ease-in-out infinite;
}

@keyframes gentleFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.step-icon img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    opacity: 0.8;
    transition: transform 0.3s ease;
}

.step-icon:hover {
    background: rgba(156, 175, 136, 0.2);
    transform: scale(1.05);
    animation: none;
}

.step-icon:hover img {
    transform: scale(1.1);
}

.step-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.25rem;
}

.step-header p {
    color: #5C4A3A;
    font-size: 0.85rem;
}

/* Step Actions */
.step-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #E8E0D5;
}

.btn-next, .btn-prev, .btn-submit {
    padding: 0.7rem 1.8rem;
    border-radius: 40px;
    font-weight: 600;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
}

.btn-next {
    background: #9CAF88;
    color: white;
}

.btn-next:hover {
    background: #7A8F64;
    transform: translateX(3px);
}

.btn-prev {
    background: transparent;
    border: 1.5px solid #E8E0D5;
    color: #1A1512;
}

.btn-prev:hover {
    border-color: #9CAF88;
    transform: translateX(-3px);
}

.btn-submit {
    background: linear-gradient(135deg, #9CAF88, #7A8F64);
    color: white;
    box-shadow: 0 4px 15px rgba(156, 175, 136, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(156, 175, 136, 0.4);
}

/* Input dengan Icon */
.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CAF88;
    z-index: 1;
}

.input-with-icon input {
    padding-left: 45px;
}

/* Upload Card */
.upload-card {
    position: relative;
    border: 2px dashed #E8E0D5;
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s;
    background: rgba(253, 251, 247, 0.8);
}

.upload-card:hover {
    border-color: #9CAF88;
    background: rgba(156, 175, 136, 0.05);
}

.upload-card input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.upload-preview {
    padding: 2rem 1rem;
    text-align: center;
}

.upload-icon-large i {
    font-size: 2rem;
    color: #9CAF88;
    margin-bottom: 0.5rem;
}

.upload-preview p {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #1A1512;
}

.upload-preview small {
    font-size: 0.7rem;
    color: #5C4A3A;
}

.file-name {
    display: block;
    font-size: 0.65rem;
    color: #9CAF88;
    margin-top: 0.5rem;
    word-break: break-all;
}

.upload-info {
    font-size: 0.7rem;
    color: #5C4A3A;
    margin-top: 1rem;
    text-align: center;
}

/* Preview Card */
.preview-card {
    background: rgba(156, 175, 136, 0.05);
    border-radius: 20px;
    padding: 1.25rem;
}

.preview-card h4 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #7A8F64;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #E8E0D5;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.preview-grid div {
    font-size: 0.8rem;
}

.preview-grid strong {
    color: #1A1512;
}

.preview-grid span {
    color: #2C241E;
}

/* Form Fields */
.ghibli-input, .ghibli-select {
    background: white;
    border: 1.5px solid #E8E0D5;
    border-radius: 14px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    width: 100%;
    transition: all 0.3s;
}

.ghibli-input:focus, .ghibli-select:focus {
    border-color: #9CAF88;
    outline: none;
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
}

.form-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #1A1512;
    margin-bottom: 0.4rem;
    display: block;
}

.required {
    color: #C58B8B;
}

.form-hint {
    font-size: 0.65rem;
    color: #5C4A3A;
    display: block;
    margin-top: 0.25rem;
}

.loading-spinner {
    display: none;
    font-size: 0.7rem;
    color: #9CAF88;
    margin-top: 0.25rem;
}

.status-message {
    margin-bottom: 1rem;
}

/* Success/Error Popup */
.success-popup, .error-popup {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 16px;
    margin-bottom: 1.5rem;
}

.success-popup {
    background: #F0F7ED;
    border-left: 4px solid #9CAF88;
}

.error-popup {
    background: #FFF5F5;
    border-left: 4px solid #E8C5C5;
}

.success-icon, .error-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
}

.success-icon {
    background: #9CAF88;
    color: white;
}

.error-icon {
    background: #E8C5C5;
    color: #C58B8B;
}

.success-popup h4, .error-popup h4 {
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
}

.success-popup p, .error-popup p {
    font-size: 0.75rem;
    margin: 0;
}

.error-popup ul {
    margin: 0.3rem 0 0 1rem;
    font-size: 0.7rem;
}

/* Responsive */
@media (max-width: 768px) {
    .two-column-layout {
        flex-direction: column;
    }
    
    .progress-sidebar {
        width: 100%;
    }
    
    .vertical-progress {
        padding: 1rem;
    }
    
    .vertical-steps-container {
        flex-direction: row;
        justify-content: space-around;
        flex-wrap: wrap;
    }
    
    .vertical-step-wrapper {
        flex: 1;
        align-items: center;
    }
    
    .vertical-step {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0.5rem;
    }
    
    .vertical-step-desc {
        display: none;
    }
    
    .vertical-step-label {
        font-size: 0.7rem;
    }
    
    .vertical-line {
        display: none;
    }
    
    .form-card-body {
        padding: 1.25rem;
    }
    
    .preview-grid {
        grid-template-columns: 1fr;
    }
    
    .form-header-title {
        font-size: 1.5rem;
    }
    
    .decor-susuwatari {
        display: none;
    }
    
    .step-icon {
        width: 60px;
        height: 60px;
    }
    
    .step-icon img {
        width: 35px;
        height: 35px;
    }
}
</style>

<script>
let currentStep = 1;
const totalSteps = 4;

function updateProgress() {
    // Update vertical steps
    document.querySelectorAll('.vertical-step').forEach((step, index) => {
        const stepNum = index + 1;
        const wrapper = step.closest('.vertical-step-wrapper');
        
        if (stepNum < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
            if (wrapper) wrapper.classList.add('completed');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
            if (wrapper) wrapper.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
            if (wrapper) wrapper.classList.remove('completed');
        }
    });
}

function nextStep(step) {
    document.getElementById(`step${currentStep}`).classList.remove('active');
    currentStep = step;
    document.getElementById(`step${currentStep}`).classList.add('active');
    updateProgress();
    updatePreview();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    document.getElementById(`step${currentStep}`).classList.remove('active');
    currentStep = step;
    document.getElementById(`step${currentStep}`).classList.add('active');
    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Click on vertical step to navigate
document.querySelectorAll('.vertical-step').forEach(step => {
    step.addEventListener('click', () => {
        const stepNum = parseInt(step.dataset.step);
        if (stepNum && stepNum !== currentStep) {
            document.getElementById(`step${currentStep}`).classList.remove('active');
            currentStep = stepNum;
            document.getElementById(`step${currentStep}`).classList.add('active');
            updateProgress();
            updatePreview();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});

function updatePreview() {
    document.getElementById('preview_nik').innerText = document.getElementById('nik')?.value || '-';
    document.getElementById('preview_nama').innerText = document.getElementById('nama_lengkap')?.value || '-';
    document.getElementById('preview_tempat').innerText = document.getElementById('tempat_lahir')?.value || '-';
    document.getElementById('preview_tgl').innerText = document.getElementById('tanggal_lahir')?.value || '-';
    document.getElementById('preview_gender').innerText = document.getElementById('gender')?.options[document.getElementById('gender')?.selectedIndex]?.text || '-';
    document.getElementById('preview_pekerjaan').innerText = document.getElementById('pekerjaan')?.options[document.getElementById('pekerjaan')?.selectedIndex]?.text || '-';
    document.getElementById('preview_alamat').innerText = document.getElementById('alamat')?.value || '-';
    document.getElementById('preview_rtrw').innerText = `${document.getElementById('rt')?.value || '-'}/${document.getElementById('rw')?.value || '-'}`;
    document.getElementById('preview_dusun').innerText = document.getElementById('dusun')?.value || '-';
    document.getElementById('preview_nohp').innerText = document.getElementById('no_hp')?.value || '-';
    
    const jenisSelect = document.getElementById('jenis_surat_id');
    const jenisText = jenisSelect?.options[jenisSelect.selectedIndex]?.text || '-';
    document.getElementById('preview_jenis').innerText = jenisText;
    document.getElementById('preview_keperluan').innerText = document.getElementById('keperluan')?.value || '-';
}

// File upload preview
document.querySelectorAll('.upload-card').forEach(card => {
    const input = card.querySelector('input');
    const preview = card.querySelector('.upload-preview');
    const fileNameSpan = card.querySelector('.file-name');
    
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            if (fileNameSpan) fileNameSpan.innerText = fileName;
            preview.style.background = 'rgba(156, 175, 136, 0.1)';
        } else {
            if (fileNameSpan) fileNameSpan.innerText = '';
            preview.style.background = '';
        }
        updatePreview();
        document.getElementById('preview_ktp').innerText = document.getElementById('file_ktp')?.files[0]?.name || '-';
        document.getElementById('preview_kk').innerText = document.getElementById('file_kk')?.files[0]?.name || '-';
    });
});

function formatWhatsappNumber(input) {
    let value = input.value;
    const digits = value.replace(/[^0-9]/g, '');
    if (value.startsWith('+')) {
        if (value.startsWith('+62')) value = '+62' + digits.slice(2, 13);
        else if (digits.startsWith('62')) value = '+62' + digits.slice(2, 13);
        else if (digits.startsWith('0')) value = '+62' + digits.slice(1, 12);
        else value = '+62' + digits.slice(0, 11);
    } else {
        if (digits.startsWith('62')) value = '+62' + digits.slice(2, 13);
        else if (digits.startsWith('0')) value = '+62' + digits.slice(1, 12);
        else value = '+62' + digits.slice(0, 11);
    }
    input.value = value;
}

let nikCheckTimeout = null;

function checkNIK(nik) {
    const loadingEl = document.getElementById('nik-loading');
    if (nik.length !== 16) {
        if (loadingEl) loadingEl.style.display = 'none';
        return;
    }
    if (nikCheckTimeout) clearTimeout(nikCheckTimeout);
    nikCheckTimeout = setTimeout(function() {
        if (loadingEl) loadingEl.style.display = 'block';
        fetch('<?= PROSES_URL ?>/check_nik.php?nik=' + encodeURIComponent(nik))
            .then(response => response.json())
            .then(data => {
                if (loadingEl) loadingEl.style.display = 'none';
                if (data.found) {
                    document.getElementById('nama_lengkap').value = data.nama_lengkap || '';
                    document.getElementById('tempat_lahir').value = data.tempat_lahir || '';
                    document.getElementById('tanggal_lahir').value = data.tanggal_lahir || '';
                    document.getElementById('gender').value = data.gender || '';
                    const pekerjaanSelect = document.getElementById('pekerjaan');
                    if (pekerjaanSelect && data.pekerjaan) {
                        for(let i = 0; i < pekerjaanSelect.options.length; i++) {
                            if(pekerjaanSelect.options[i].value === data.pekerjaan) {
                                pekerjaanSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                    document.getElementById('alamat').value = data.alamat || '';
                    document.getElementById('rt').value = data.rt || '';
                    document.getElementById('rw').value = data.rw || '';
                    document.getElementById('dusun').value = data.dusun || '';
                    document.getElementById('no_hp').value = data.no_hp ? (data.no_hp.startsWith('+') ? data.no_hp : '+62' + data.no_hp.replace(/^0+/, '')) : '';
                    
                    document.getElementById('nama_lengkap').readOnly = true;
                    document.getElementById('tempat_lahir').readOnly = true;
                    document.getElementById('tanggal_lahir').readOnly = true;
                    document.getElementById('gender').disabled = true;
                    document.getElementById('pekerjaan').disabled = true;
                    document.getElementById('alamat').readOnly = true;
                    document.getElementById('rt').readOnly = true;
                    document.getElementById('rw').readOnly = true;
                    document.getElementById('dusun').readOnly = true;
                    
                    updatePreview();
                } else {
                    document.getElementById('nama_lengkap').readOnly = false;
                    document.getElementById('tempat_lahir').readOnly = false;
                    document.getElementById('tanggal_lahir').readOnly = false;
                    document.getElementById('gender').disabled = false;
                    document.getElementById('pekerjaan').disabled = false;
                    document.getElementById('alamat').readOnly = false;
                    document.getElementById('rt').readOnly = false;
                    document.getElementById('rw').readOnly = false;
                    document.getElementById('dusun').readOnly = false;
                }
            })
            .catch(error => console.error('Error:', error));
    }, 500);
}

function resetForm() {
    document.getElementById('suratForm').reset();
    document.getElementById('nama_lengkap').readOnly = false;
    document.getElementById('tempat_lahir').readOnly = false;
    document.getElementById('tanggal_lahir').readOnly = false;
    document.getElementById('gender').disabled = false;
    document.getElementById('pekerjaan').disabled = false;
    document.getElementById('alamat').readOnly = false;
    document.getElementById('rt').readOnly = false;
    document.getElementById('rw').readOnly = false;
    document.getElementById('dusun').readOnly = false;
}

document.getElementById('suratForm')?.addEventListener('submit', function(e) {
    const nik = document.querySelector('[name="nik"]').value;
    if (!/^\d{16}$/.test(nik)) {
        e.preventDefault();
        alert('NIK harus 16 digit angka');
        return false;
    }
    const noHp = document.querySelector('[name="no_hp"]').value;
    if (!/^\+62\d{11,13}$/.test(noHp)) {
        e.preventDefault();
        alert('No WhatsApp harus +62 dan 11-13 digit');
        return false;
    }
});

updateProgress();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>