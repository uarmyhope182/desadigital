<?php
// Runner for database seeder
// Usage: php database/seed.php

// Ensure error display for CLI
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/migrate.php';
require_once __DIR__ . '/seeders/DatabaseSeeder.php';
require_once __DIR__ . '/../config/db.php';

/** @var PDO $pdo */
if (!isset($pdo) || !$pdo instanceof PDO) {
    throw new RuntimeException('PDO $pdo not available from config/db.php');
}

$seeder = new DatabaseSeeder($pdo);
try {
    $seeder->run();
    echo "All done.\n";
} catch (Exception $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
