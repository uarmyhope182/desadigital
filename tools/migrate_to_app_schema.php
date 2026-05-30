<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/compat.php';

$changes = [];

function ensure_column(PDO $pdo, $table, $col, $definition) {
    global $changes;
    if (!schema_has_column($table, $col)) {
        $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $col, $definition);
        $pdo->exec($sql);
        $changes[] = $sql;
    }
}

try {
    // jenis_surat: map code->kode, name->nama, description->deskripsi, requirements->persyaratan
    ensure_column($pdo, 'jenis_surat', 'kode', "VARCHAR(20) UNIQUE AFTER id");
    ensure_column($pdo, 'jenis_surat', 'nama', "VARCHAR(100) AFTER kode");
    ensure_column($pdo, 'jenis_surat', 'deskripsi', "TEXT AFTER nama");
    ensure_column($pdo, 'jenis_surat', 'persyaratan', "TEXT AFTER deskripsi");
    // penduduk: add rt, rw, no_hp if missing
    ensure_column($pdo, 'penduduk', 'rt', "VARCHAR(10) DEFAULT NULL AFTER address");
    ensure_column($pdo, 'penduduk', 'rw', "VARCHAR(10) DEFAULT NULL AFTER rt");
    ensure_column($pdo, 'penduduk', 'no_hp', "VARCHAR(20) DEFAULT NULL AFTER rw");
    // pengajuan_surat: add RT/RW/Dusun and file paths to match app expectations
    ensure_column($pdo, 'pengajuan_surat', 'rt', "VARCHAR(10) DEFAULT NULL AFTER alamat");
    ensure_column($pdo, 'pengajuan_surat', 'rw', "VARCHAR(10) DEFAULT NULL AFTER rt");
    ensure_column($pdo, 'pengajuan_surat', 'dusun', "VARCHAR(100) DEFAULT NULL AFTER rw");
    ensure_column($pdo, 'pengajuan_surat', 'file_ktp', "VARCHAR(255) DEFAULT NULL AFTER keperluan");
    ensure_column($pdo, 'pengajuan_surat', 'file_kk', "VARCHAR(255) DEFAULT NULL AFTER file_ktp");

    // Copy values where possible
    // jenis_surat
    if (schema_has_column('jenis_surat','code') && schema_has_column('jenis_surat','kode')) {
        $pdo->exec("UPDATE jenis_surat SET kode = code WHERE (kode IS NULL OR kode = '') AND code IS NOT NULL");
    }
    if (schema_has_column('jenis_surat','name') && schema_has_column('jenis_surat','nama')) {
        $pdo->exec("UPDATE jenis_surat SET nama = name WHERE (nama IS NULL OR nama = '') AND name IS NOT NULL");
    }
    if (schema_has_column('jenis_surat','description') && schema_has_column('jenis_surat','deskripsi')) {
        $pdo->exec("UPDATE jenis_surat SET deskripsi = description WHERE (deskripsi IS NULL OR deskripsi = '') AND description IS NOT NULL");
    }
    if (schema_has_column('jenis_surat','requirements') && schema_has_column('jenis_surat','persyaratan')) {
        $pdo->exec("UPDATE jenis_surat SET persyaratan = requirements WHERE (persyaratan IS NULL OR persyaratan = '') AND requirements IS NOT NULL");
    }

    // penduduk: split rt_rw into rt and rw if needed
    if (schema_has_column('penduduk','rt_rw') && (schema_has_column('penduduk','rt') || schema_has_column('penduduk','rw'))) {
        $stmt = $pdo->query("SELECT id, rt_rw FROM penduduk WHERE rt_rw IS NOT NULL AND rt_rw <> ''");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parts = preg_split('/\D+/', $row['rt_rw'], -1, PREG_SPLIT_NO_EMPTY);
            $rt = $parts[0] ?? null;
            $rw = $parts[1] ?? null;
            $update = $pdo->prepare('UPDATE penduduk SET rt = :rt, rw = :rw WHERE id = :id');
            $update->execute([':rt' => $rt, ':rw' => $rw, ':id' => $row['id']]);
        }
    }

    // pengajuan_surat: if file_path exists and file_ktp/file_kk missing, set file_ktp = file_path
    if (schema_has_column('pengajuan_surat','file_path') && schema_has_column('pengajuan_surat','file_ktp')) {
        $pdo->exec("UPDATE pengajuan_surat SET file_ktp = file_path WHERE (file_ktp IS NULL OR file_ktp = '') AND file_path IS NOT NULL");
    }

    echo "OK\n";
    if ($changes) {
        echo "Applied changes:\n" . implode("\n", $changes) . "\n";
    } else {
        echo "No schema changes needed.\n";
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
