-- Sample data for testing PDF generation
-- Adjust jenis_surat_id to match your jenis_surat table (1..5)
-- Run: mysql -u root -p desa_sasi < tools/sample_pengajuan_test.sql

START TRANSACTION;

-- Insert sample penduduk (if not exists)
INSERT INTO penduduk (nik, nama, alamat, rt, rw, dusun, no_hp, created_at, updated_at)
SELECT * FROM (SELECT '1234567890123456' AS nik, 'Budi Santoso' AS nama, 'Jl. Merdeka No.24' AS alamat, '01' AS rt, '02' AS rw, 'Dusun A' AS dusun, '081234567890' AS no_hp, NOW() AS created_at, NOW() AS updated_at) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM penduduk WHERE nik = '1234567890123456');

-- Insert sample pengajuan_surat
INSERT INTO pengajuan_surat (tracking_id, nik, nama_lengkap, alamat, no_hp, keperluan, jenis_surat_id, status, created_at)
VALUES ('SASI-TEST-00001', '1234567890123456', 'Budi Santoso', 'Jl. Merdeka No.24', '081234567890', 'Surat Domisili untuk pendaftaran sekolah', 1, 'pending', NOW());

COMMIT;

-- After loading this, open admin->Kelola Permohonan, setujui permohonan dengan Tracking ID 'SASI-TEST-00001', lalu klik Cetak.
