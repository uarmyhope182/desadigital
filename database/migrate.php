<?php
// Simple migration runner. Runs CREATE TABLE if not exists for required tables.
// Usage: php database/migrate.php

require_once __DIR__ . '/../config/db.php';

/** @var PDO $pdo */
if (!isset($pdo) || !$pdo instanceof PDO) {
    // Try to include db config which should set $pdo
    throw new RuntimeException('PDO $pdo not available from config/db.php');
}

// Basic migrations (id, timestamps basic)
$queries = [
    // users
    "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(150) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // jenis_surat
    "CREATE TABLE IF NOT EXISTS jenis_surat (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        kode VARCHAR(50) NOT NULL,
        nama VARCHAR(150) NOT NULL,
        persyaratan TEXT,
        deskripsi TEXT,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // penduduk
    "CREATE TABLE IF NOT EXISTS penduduk (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        nik VARCHAR(20) NOT NULL UNIQUE,
        nama VARCHAR(150) NOT NULL,
        tempat_lahir VARCHAR(100),
        tanggal_lahir DATE,
        jenis_kelamin VARCHAR(20),
        alamat TEXT,
        rt VARCHAR(10),
        rw VARCHAR(10),
        agama VARCHAR(50),
        pekerjaan VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // pengajuan_surat
    "CREATE TABLE IF NOT EXISTS pengajuan_surat (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        penduduk_id INTEGER NOT NULL,
        jenis_surat_id INTEGER NOT NULL,
        tujuan VARCHAR(255),
        tanggal_pengajuan DATE,
        status VARCHAR(50) DEFAULT 'pending',
        note TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (penduduk_id) REFERENCES penduduk(id) ON DELETE CASCADE,
        FOREIGN KEY (jenis_surat_id) REFERENCES jenis_surat(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // pengumuman
    "CREATE TABLE IF NOT EXISTS pengumuman (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        published_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        echo "OK: executed migration\n";
    } catch (PDOException $e) {
        echo "Error executing migration: " . $e->getMessage() . "\n";
    }
}

// Ensure commonly used columns exist (useful when upgrading an older schema)
function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column");
    $stmt->execute([':table' => $table, ':column' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

$alterations = [
    'users' => [
        'password' => "ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL",
        'name' => "ALTER TABLE users ADD COLUMN name VARCHAR(150) NOT NULL",
        'role' => "ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'staff'",
        'created_at' => "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        'full_name' => "ALTER TABLE users ADD COLUMN full_name VARCHAR(150)",
        'email' => "ALTER TABLE users ADD COLUMN email VARCHAR(150)",
    ],
    'penduduk' => [
        'nik' => "ALTER TABLE penduduk ADD COLUMN nik VARCHAR(20) NOT NULL UNIQUE",
        'nama' => "ALTER TABLE penduduk ADD COLUMN nama VARCHAR(150) NOT NULL",
        'tempat_lahir' => "ALTER TABLE penduduk ADD COLUMN tempat_lahir VARCHAR(100)",
        'tanggal_lahir' => "ALTER TABLE penduduk ADD COLUMN tanggal_lahir DATE",
        'jenis_kelamin' => "ALTER TABLE penduduk ADD COLUMN jenis_kelamin VARCHAR(20)",
        'alamat' => "ALTER TABLE penduduk ADD COLUMN alamat TEXT",
        'rt' => "ALTER TABLE penduduk ADD COLUMN rt VARCHAR(10)",
        'rw' => "ALTER TABLE penduduk ADD COLUMN rw VARCHAR(10)",
        'agama' => "ALTER TABLE penduduk ADD COLUMN agama VARCHAR(50)",
        'pekerjaan' => "ALTER TABLE penduduk ADD COLUMN pekerjaan VARCHAR(100)",
        'created_at' => "ALTER TABLE penduduk ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        // Friendly alternative column names used in app
        'full_name' => "ALTER TABLE penduduk ADD COLUMN full_name VARCHAR(150)",
        'gender' => "ALTER TABLE penduduk ADD COLUMN gender VARCHAR(30)",
        'address' => "ALTER TABLE penduduk ADD COLUMN address TEXT",
        'no_hp' => "ALTER TABLE penduduk ADD COLUMN no_hp VARCHAR(30)",
        'dusun' => "ALTER TABLE penduduk ADD COLUMN dusun VARCHAR(100)",
    ],
    'pengajuan_surat' => [
        'penduduk_id' => "ALTER TABLE pengajuan_surat ADD COLUMN penduduk_id INTEGER NOT NULL",
        'jenis_surat_id' => "ALTER TABLE pengajuan_surat ADD COLUMN jenis_surat_id INTEGER NOT NULL",
        'tujuan' => "ALTER TABLE pengajuan_surat ADD COLUMN tujuan VARCHAR(255)",
        'tanggal_pengajuan' => "ALTER TABLE pengajuan_surat ADD COLUMN tanggal_pengajuan DATE",
        'status' => "ALTER TABLE pengajuan_surat ADD COLUMN status VARCHAR(50) DEFAULT 'pending'",
        'note' => "ALTER TABLE pengajuan_surat ADD COLUMN note TEXT",
        'created_at' => "ALTER TABLE pengajuan_surat ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        'tracking_id' => "ALTER TABLE pengajuan_surat ADD COLUMN tracking_id VARCHAR(100)",
        'nama_lengkap' => "ALTER TABLE pengajuan_surat ADD COLUMN nama_lengkap VARCHAR(150)",
        'diproses_pada' => "ALTER TABLE pengajuan_surat ADD COLUMN diproses_pada DATETIME NULL",
    ],
];

foreach ($alterations as $table => $cols) {
    foreach ($cols as $col => $sql) {
        try {
            if (!columnExists($pdo, $table, $col)) {
                $pdo->exec($sql);
                echo "OK: altered {$table} add {$col}\n";
            }
        } catch (PDOException $e) {
            echo "Error altering table {$table}: " . $e->getMessage() . "\n";
        }
    }
}

// Create helper function generate_tracking_id if some schema/trigger depends on it
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = :name AND ROUTINE_TYPE='FUNCTION'");
    $stmt->execute([':name' => 'generate_tracking_id']);
    if ((int)$stmt->fetchColumn() === 0) {
        // Create a simple deterministic function that returns a short tracking id.
        $sql = "CREATE FUNCTION generate_tracking_id() RETURNS VARCHAR(32) DETERMINISTIC
        BEGIN
            RETURN CONCAT('TRK', LPAD(FLOOR(RAND()*99999999),8,'0'));
        END";
        try {
            $pdo->exec($sql);
            echo "OK: created function generate_tracking_id\n";
        } catch (PDOException $e) {
            echo "Error creating function generate_tracking_id: " . $e->getMessage() . "\n";
        }
    }
} catch (PDOException $e) {
    // ignore
}

// Synchronize alternative column names to keep older/newer code compatible
try {
    // full_name <- nama
    if (columnExists($pdo, 'penduduk', 'nama') && columnExists($pdo, 'penduduk', 'full_name')) {
        $pdo->exec("UPDATE penduduk SET full_name = nama WHERE (full_name IS NULL OR full_name = '') AND (nama IS NOT NULL AND nama != '')");
        echo "OK: synced penduduk.full_name from nama\n";
    }

    // gender <- jenis_kelamin
    if (columnExists($pdo, 'penduduk', 'jenis_kelamin') && columnExists($pdo, 'penduduk', 'gender')) {
        $pdo->exec("UPDATE penduduk SET gender = jenis_kelamin WHERE (gender IS NULL OR gender = '') AND (jenis_kelamin IS NOT NULL AND jenis_kelamin != '')");
        echo "OK: synced penduduk.gender from jenis_kelamin\n";
    }

    // address <- alamat
    if (columnExists($pdo, 'penduduk', 'alamat') && columnExists($pdo, 'penduduk', 'address')) {
        $pdo->exec("UPDATE penduduk SET address = alamat WHERE (address IS NULL OR address = '') AND (alamat IS NOT NULL AND alamat != '')");
        echo "OK: synced penduduk.address from alamat\n";
    }

    // dusun if empty keep existing
    if (columnExists($pdo, 'penduduk', 'dusun')) {
        echo "OK: ensured penduduk.dusun exists\n";
    }
} catch (PDOException $e) {
    // Non-fatal
}

echo "Migrations finished.\n";
