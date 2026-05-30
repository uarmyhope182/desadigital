<?php
// Fill missing no_hp in penduduk with synthetic but unique numbers.
require_once __DIR__ . '/../config/db.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    echo "PDO not available\n";
    exit(1);
}

try {
    // Update empty no_hp dengan nomor unik
    $sql = "UPDATE penduduk SET no_hp = CONCAT('0812', LPAD(id, 8, '0')) WHERE no_hp IS NULL OR TRIM(no_hp) = '' OR no_hp = '-'";
    $rows = $pdo->exec($sql);
    echo "Updated rows (no_hp): " . ($rows === false ? 0 : $rows) . "\n";
    
    // Update empty full_name jika ada
    $sql2 = "UPDATE penduduk SET full_name = nama WHERE (full_name IS NULL OR full_name = '') AND nama IS NOT NULL";
    $rows2 = $pdo->exec($sql2);
    echo "Updated rows (full_name): " . ($rows2 === false ? 0 : $rows2) . "\n";
    
    // Update empty gender
    $sql3 = "UPDATE penduduk SET gender = jenis_kelamin WHERE (gender IS NULL OR gender = '') AND jenis_kelamin IS NOT NULL";
    $rows3 = $pdo->exec($sql3);
    echo "Updated rows (gender): " . ($rows3 === false ? 0 : $rows3) . "\n";
    
    // Update empty address
    $sql4 = "UPDATE penduduk SET address = alamat WHERE (address IS NULL OR address = '') AND alamat IS NOT NULL";
    $rows4 = $pdo->exec($sql4);
    echo "Updated rows (address): " . ($rows4 === false ? 0 : $rows4) . "\n";
    
    // Update pengajuan_surat nama_lengkap
    $sql5 = "UPDATE pengajuan_surat ps 
             JOIN penduduk p ON ps.nik = p.nik 
             SET ps.nama_lengkap = p.full_name 
             WHERE ps.nama_lengkap IS NULL OR ps.nama_lengkap = ''";
    $rows5 = $pdo->exec($sql5);
    echo "Updated rows (pengajuan_surat.nama_lengkap): " . ($rows5 === false ? 0 : $rows5) . "\n";
    
    // Update pengajuan_surat dengan tracking_id jika kosong
    $sql6 = "UPDATE pengajuan_surat 
             SET tracking_id = CONCAT('TRK', DATE_FORMAT(created_at, '%Y%m%d'), LPAD(id, 4, '0')) 
             WHERE tracking_id IS NULL OR tracking_id = ''";
    $rows6 = $pdo->exec($sql6);
    echo "Updated rows (tracking_id): " . ($rows6 === false ? 0 : $rows6) . "\n";

    // Count remaining empty
    $count = (int) $pdo->query("SELECT COUNT(*) FROM penduduk WHERE no_hp IS NULL OR TRIM(no_hp) = '' OR no_hp = '-'")->fetchColumn();
    echo "Remaining empty no_hp: {$count}\n";
    
    $count2 = (int) $pdo->query("SELECT COUNT(*) FROM penduduk WHERE full_name IS NULL OR full_name = ''")->fetchColumn();
    echo "Remaining empty full_name: {$count2}\n";
    
    $count3 = (int) $pdo->query("SELECT COUNT(*) FROM pengajuan_surat WHERE nama_lengkap IS NULL OR nama_lengkap = ''")->fetchColumn();
    echo "Remaining empty pengajuan_surat.nama_lengkap: {$count3}\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}