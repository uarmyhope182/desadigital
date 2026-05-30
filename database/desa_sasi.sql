-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Bulan Mei 2026 pada 06.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `desa_sasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `antrian_notifikasi`
--

CREATE TABLE `antrian_notifikasi` (
  `id` int(11) NOT NULL,
  `channel` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `recipient_name` varchar(150) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_surat`
--

CREATE TABLE `jenis_surat` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `persyaratan` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_surat`
--

INSERT INTO `jenis_surat` (`id`, `kode`, `nama`, `deskripsi`, `persyaratan`, `is_active`) VALUES
(1, 'SKTM', 'Surat Keterangan Tidak Mampu', 'Surat keterangan untuk warga yang tidak mampu secara ekonomi. Digunakan untuk keperluan bantuan sosial, beasiswa, atau bantuan kesehatan. Surat ini menjadi bukti bahwa pemohon tergolong dalam keluarga prasejahtera dan berhak menerima bantuan dari pemerintah.', 'KTP, KK, Surat Permohonan', 1),
(2, 'SKU', 'Surat Keterangan Usaha', 'Surat keterangan untuk legalisasi usaha mikro, kecil, dan menengah. Digunakan untuk keperluan perizinan usaha, pembuatan NPWP, pembukaan rekening bank, atau pengajuan kredit usaha. Surat ini menerangkan bahwa pemohon benar-benar memiliki usaha yang berjalan.', 'KTP, KK, Surat Permohonan', 1),
(3, 'SKP', 'Surat Keterangan Pindah', 'Surat keterangan untuk keperluan pindah domisili antar wilayah. Digunakan untuk administrasi perpindahan penduduk, perubahan data di Dukcapil, atau keperluan sekolah anak di daerah tujuan. Surat ini menjadi syarat administrasi kepindahan warga.', 'KTP, KK, Formulir Pindah', 1),
(4, 'SKB', 'Surat Keterangan Belum Menikah', 'Surat keterangan yang menyatakan bahwa seseorang belum pernah menikah secara sah. Digunakan untuk keperluan lamaran pekerjaan, pendaftaran nikah di KUA, atau pengurusan dokumen keimigrasian. Surat ini diterbitkan berdasarkan data kependudukan yang tersedia.', 'KTP, KK, Surat Pernyataan', 1),
(5, 'SKD', 'Surat Keterangan Domisili', 'Surat keterangan tempat tinggal yang digunakan untuk berbagai keperluan administrasi. Diperlukan untuk pembuatan KTP, pembukaan rekening bank, pendaftaran sekolah, atau pengurusan dokumen lainnya. Surat ini menerangkan alamat domisili pemohon yang tercatat di kelurahan.', 'KTP, KK', 1),
(6, 'SKKB', 'Surat Keterangan Kehilangan Barang', 'Surat keterangan untuk kehilangan barang atau dokumen penting. Digunakan sebagai bukti pendukung untuk pembuatan dokumen pengganti (KTP, SIM, KK, Akta Kelahiran, Ijazah, dll) di instansi terkait. Surat ini menerangkan bahwa pemohon benar-benar mengalami kehilangan.', 'KTP,KK', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penduduk`
--

CREATE TABLE `penduduk` (
  `id` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `gender` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `dusun` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penduduk`
--

INSERT INTO `penduduk` (`id`, `nik`, `full_name`, `tempat_lahir`, `tanggal_lahir`, `gender`, `pekerjaan`, `address`, `rt`, `rw`, `dusun`, `no_hp`, `created_at`, `updated_at`) VALUES
(2, '5371010102930002', 'Siti Aminah', 'Kefamenanu', '1993-02-01', 'Perempuan', 'Ibu Rumah Tangga', 'Jl. Sudirman No. 5', '003', '001', 'Dusun Barat', '081234567891', '2026-05-23 10:57:23', '2026-05-23 10:57:23'),
(3, '5371010103950003', 'Ahmad Fauzi', 'Kupang', '1985-03-15', 'Laki-laki', 'Petani', 'Jl. Diponegoro No. 8', '002', '003', 'Dusun Timur', '081234567892', '2026-05-23 10:57:23', '2026-05-23 10:57:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_surat`
--

CREATE TABLE `pengajuan_surat` (
  `id` int(11) NOT NULL,
  `tracking_id` varchar(50) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_lengkap` varchar(120) NOT NULL,
  `alamat` text NOT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `dusun` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL,
  `jenis_surat_id` int(11) NOT NULL,
  `keperluan` text DEFAULT NULL,
  `data_tambahan` text DEFAULT NULL,
  `file_ktp` varchar(255) DEFAULT NULL,
  `file_kk` varchar(255) DEFAULT NULL,
  `nomor_surat` varchar(50) DEFAULT NULL,
  `tgl_surat` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `diproses_oleh` int(11) DEFAULT NULL,
  `diproses_pada` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger `pengajuan_surat`
--
DELIMITER $$
CREATE TRIGGER `after_update_pengajuan` AFTER UPDATE ON `pengajuan_surat` FOR EACH ROW BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO riwayat_tracking (pengajuan_id, status_lama, status_baru, keterangan, diubah_oleh)
        VALUES (NEW.id, OLD.status, NEW.status, NEW.catatan, NEW.diproses_oleh);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_pengajuan` BEFORE INSERT ON `pengajuan_surat` FOR EACH ROW BEGIN
    IF NEW.tracking_id IS NULL OR NEW.tracking_id = '' THEN
        SET NEW.tracking_id = generate_tracking_id();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `kategori` enum('umum','berita','kegiatan','pengumuman') DEFAULT 'umum',
  `gambar_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `kategori`, `gambar_url`, `is_published`, `published_at`, `dibuat_oleh`, `created_at`, `gambar`) VALUES
(1, 'Pelayanan Online Telah Tersedia', 'Kini warga dapat mengajukan surat secara online melalui website Kelurahan Sasi. Layanan ini untuk mempermudah warga dalam mengurus administrasi surat.', 'pengumuman', NULL, 1, '2026-05-23 10:57:23', NULL, '2026-05-23 10:57:23', NULL),
(2, 'Pendataan Penduduk Bulanan', 'Harap warga memperbarui data penduduk jika ada perubahan alamat atau status keluarga. Pendataan dilakukan setiap bulan.', 'pengumuman', NULL, 1, '2026-05-23 10:57:23', NULL, '2026-05-23 10:57:23', NULL),
(3, 'Program Penghijauan Lingkungan', 'Pemerintah Kelurahan Sasi mengajak seluruh warga untuk ikut serta dalam program penghijauan lingkungan.', 'kegiatan', NULL, 1, '2026-05-23 10:57:23', NULL, '2026-05-23 10:57:23', NULL),
(5, 'Jadwal Posyandu Bulan Juni 2026', 'Diberitahukan kepada seluruh warga Kelurahan Sasi, bahwa akan dilaksanakan kegiatan Posyandu rutin bulan Juni 2026.\r\n\r\nJadwal:\r\nHari/Tanggal: Sabtu, 6 Juni 2026\r\nWaktu: 08.00 - 11.00 WIB\r\nTempat: Balai Kelurahan Sasi\r\n•	Layanan yang tersedia:\r\n•	Penimbangan balita\r\n•	Pemberian vitamin A\r\n•	Imunisasi campak dan polio\r\n•	Pemeriksaan ibu hamil\r\n•	Konsultasi gizi dan ASI eksklusif\r\nBagi warga yang memiliki balita, ibu hamil, dan lansia diharapkan hadir tepat waktu. Membawa buku KIA dan kartu imunisasi.\r\nTerima kasih.', 'umum', NULL, 1, NULL, NULL, '2026-05-28 23:43:48', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_tracking`
--

CREATE TABLE `riwayat_tracking` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `status_lama` varchar(20) NOT NULL,
  `status_baru` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `diubah_oleh` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `statistik_desa`
--

CREATE TABLE `statistik_desa` (
  `id` int(11) NOT NULL,
  `stat_key` varchar(50) NOT NULL,
  `stat_value` varchar(100) NOT NULL,
  `stat_label` varchar(100) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `statistik_desa`
--

INSERT INTO `statistik_desa` (`id`, `stat_key`, `stat_value`, `stat_label`, `updated_at`) VALUES
(1, 'total_penduduk', '3481', 'Total Penduduk', '2026-05-23 10:57:23'),
(2, 'luas_wilayah', '6.00', 'Luas Wilayah (km²)', '2026-05-23 10:57:23'),
(3, 'total_rw', '8', 'Jumlah RW', '2026-05-23 10:57:23'),
(4, 'total_rt', '36', 'Jumlah RT', '2026-05-23 10:57:23'),
(5, 'laki_laki', '1762', 'Jumlah Penduduk Laki-laki', '2026-05-23 10:57:23'),
(6, 'perempuan', '1719', 'Jumlah Penduduk Perempuan', '2026-05-23 10:57:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `struktur_organisasi`
--

CREATE TABLE `struktur_organisasi` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `deskripsi_tugas` text DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `struktur_organisasi`
--

INSERT INTO `struktur_organisasi` (`id`, `nama`, `jabatan`, `deskripsi_tugas`, `urutan`, `parent_id`, `level`) VALUES
(1, 'H. Ahmad Fauzi', 'Kepala Kelurahan', 'Memimpin penyelenggaraan pemerintahan kelurahan', 1, NULL, 1),
(2, 'Budi Santoso', 'Sekretaris Kelurahan', 'Membantu kepala kelurahan dalam administrasi', 2, NULL, 1),
(3, 'Siti Aminah', 'Kaur Keuangan', 'Mengelola keuangan kelurahan', 3, NULL, 1),
(4, 'Drs. Mulyadi', 'Kaur Pembangunan', 'Mengelola pembangunan infrastruktur', 4, NULL, 1),
(5, 'Rina Wati', 'Kaur Kesra', 'Mengelola kesejahteraan masyarakat', 5, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_surat`
--

CREATE TABLE `template_surat` (
  `id` int(11) NOT NULL,
  `jenis_surat_id` int(11) NOT NULL,
  `judul_template` varchar(100) NOT NULL,
  `isi_template` text NOT NULL,
  `footer_template` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `template_surat`
--

INSERT INTO `template_surat` (`id`, `jenis_surat_id`, `judul_template`, `isi_template`, `footer_template`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'SKTM - Surat Keterangan Tidak Mampu', 'SURAT KETERANGAN TIDAK MAMPU\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan dengan sesungguhnya bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nPekerjaan        : {pekerjaan}\r\nAlamat           : {alamat}\r\n\r\nBerdasarkan hasil verifikasi dan data yang ada di kelurahan, \r\norang tersebut benar-benar tergolong dalam KELUARGA TIDAK MAMPU \r\nsecara ekonomi dan memerlukan bantuan sosial.\r\n\r\nSurat keterangan ini dibuat untuk keperluan: {keperluan}\r\n\r\nDemikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(2, 2, 'SKU - Surat Keterangan Usaha', 'SURAT KETERANGAN USAHA\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nAlamat           : {alamat}\r\n\r\nBahwa orang tersebut benar-benar memiliki dan menjalankan usaha:\r\n\r\nNama Usaha       : {nama_usaha}\r\nBidang Usaha     : {bidang_usaha}\r\nAlamat Usaha     : {alamat_usaha}\r\nMulai Berdiri    : {tahun_usaha}\r\n\r\nSurat keterangan ini dibuat untuk keperluan: {keperluan}\r\n\r\nDemikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(3, 3, 'SKP - Surat Keterangan Pindah', 'SURAT KETERANGAN PINDAH\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nPekerjaan        : {pekerjaan}\r\nAlamat Asal      : {alamat}\r\nAlamat Tujuan    : {alamat_tujuan}\r\nAlasan Pindah    : {alasan_pindah}\r\n\r\nBahwa orang tersebut benar-benar akan pindah dari wilayah \r\nKelurahan Sasi ke alamat tujuan di atas.\r\n\r\nSurat keterangan ini dibuat untuk keperluan administrasi perpindahan penduduk.\r\n\r\nDemikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(4, 4, 'SKB - Surat Keterangan Belum Menikah', 'SURAT KETERANGAN BELUM MENIKAH\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nPekerjaan        : {pekerjaan}\r\nAlamat           : {alamat}\r\n\r\nBerdasarkan data kependudukan dan keterangan dari yang bersangkutan, \r\nsampai dengan saat ini belum pernah terdaftar menikah atau terikat \r\ndalam perkawinan secara sah menurut hukum dan agama.\r\n\r\nSurat keterangan ini dibuat untuk keperluan: {keperluan}\r\n\r\nDemikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(5, 5, 'SKD - Surat Keterangan Domisili', 'SURAT KETERANGAN DOMISILI\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nPekerjaan        : {pekerjaan}\r\nAlamat Asal      : {alamat_asal}\r\nAlamat Domisili  : {alamat_domisili}\r\n\r\nBenar-benar berdomisili di wilayah Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara.\r\n\r\nSurat keterangan ini dibuat untuk keperluan: {keperluan}\r\n\r\nDemikian surat ini dibuat agar dapat digunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(6, 6, 'SKKB - Surat Keterangan Kehilangan Barang', 'SURAT KETERANGAN KEHILANGAN BARANG\r\nNomor: {nomor_surat}\r\n\r\nYang bertanda tangan di bawah ini, Kepala Kelurahan Sasi, \r\nKecamatan Kota Kefamenanu, Kabupaten Timor Tengah Utara, \r\nProvinsi Nusa Tenggara Timur, menerangkan bahwa:\r\n\r\nNama Lengkap     : {nama_lengkap}\r\nNIK              : {nik}\r\nTempat, Tgl Lahir: {tempat_lahir}, {tanggal_lahir}\r\nJenis Kelamin    : {gender}\r\nPekerjaan        : {pekerjaan}\r\nAlamat           : {alamat}\r\n\r\nBahwa yang bersangkutan benar-benar telah kehilangan barang-barang sebagai berikut:\r\n\r\n{daftar_barang_hilang}\r\n\r\nKejadian kehilangan tersebut terjadi pada:\r\nHari/Tanggal     : {hari_tanggal_kehilangan}\r\nWaktu            : {waktu_kehilangan}\r\nTempat           : {tempat_kehilangan}\r\n\r\nSurat keterangan ini dibuat berdasarkan laporan dan pengakuan dari yang bersangkutan serta diketahui oleh Ketua RT/RW setempat.\r\n\r\nDemikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.\r\n\r\nDikeluarkan di: Kefamenanu\r\nPada tanggal  : {tanggal_surat}', 'Kepala Kelurahan Sasi\r\n\r\n({nama_kepala_desa})\r\nNIP. {nip_kepala_desa}', 1, '2026-05-28 20:35:33', '2026-05-28 20:35:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL DEFAULT 'operator',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password_hash`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@kelurahan-sasi.id', 'Administrator', '$2y$12$x88gb.JStLPl48zCQbbJne.BokQwaeveg894vPSxBZQoJQQUQJlWW', 'admin', 1, NULL, '2026-05-23 10:57:22', '2026-05-24 17:48:40'),
(2, 'operator', 'operator@kelurahan-sasi.id', 'Petugas Operator', '$2y$10$c1.mMpiXTfCLMQoTxjsJkuLjY1fpukJtoH4QRQ.dBh2iskEDn/tUO', 'operator', 1, NULL, '2026-05-23 10:57:22', '2026-05-23 10:57:22'),
(3, 'karyawan', 'karyawan@kelurahansasi.id', 'Sesilia Vienna', '$2y$10$E1C3WMIOuLNLe.L3i0DlQOvB7XbVtfEyFaF9/qYFWhorumzm81Ala', 'admin', 1, NULL, '2026-05-28 23:33:05', '2026-05-28 23:33:05');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `antrian_notifikasi`
--
ALTER TABLE `antrian_notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jenis_surat`
--
ALTER TABLE `jenis_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indeks untuk tabel `penduduk`
--
ALTER TABLE `penduduk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indeks untuk tabel `pengajuan_surat`
--
ALTER TABLE `pengajuan_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD KEY `jenis_surat_id` (`jenis_surat_id`),
  ADD KEY `nik` (`nik`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `riwayat_tracking`
--
ALTER TABLE `riwayat_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `diubah_oleh` (`diubah_oleh`);

--
-- Indeks untuk tabel `statistik_desa`
--
ALTER TABLE `statistik_desa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_key` (`stat_key`);

--
-- Indeks untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `template_surat`
--
ALTER TABLE `template_surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_surat_id` (`jenis_surat_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `antrian_notifikasi`
--
ALTER TABLE `antrian_notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jenis_surat`
--
ALTER TABLE `jenis_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `penduduk`
--
ALTER TABLE `penduduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_surat`
--
ALTER TABLE `pengajuan_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `riwayat_tracking`
--
ALTER TABLE `riwayat_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `statistik_desa`
--
ALTER TABLE `statistik_desa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `template_surat`
--
ALTER TABLE `template_surat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengajuan_surat`
--
ALTER TABLE `pengajuan_surat`
  ADD CONSTRAINT `pengajuan_surat_ibfk_1` FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`id`),
  ADD CONSTRAINT `pengajuan_surat_ibfk_2` FOREIGN KEY (`nik`) REFERENCES `penduduk` (`nik`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD CONSTRAINT `pengumuman_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `riwayat_tracking`
--
ALTER TABLE `riwayat_tracking`
  ADD CONSTRAINT `riwayat_tracking_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_surat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_tracking_ibfk_2` FOREIGN KEY (`diubah_oleh`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `struktur_organisasi`
--
ALTER TABLE `struktur_organisasi`
  ADD CONSTRAINT `struktur_organisasi_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `struktur_organisasi` (`id`);

--
-- Ketidakleluasaan untuk tabel `template_surat`
--
ALTER TABLE `template_surat`
  ADD CONSTRAINT `template_surat_ibfk_1` FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
