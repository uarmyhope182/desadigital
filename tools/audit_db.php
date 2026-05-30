<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $schema = [];
    foreach ($tables as $t) {
        $cols = $pdo->prepare('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table');
        $cols->execute([':schema' => $DB_NAME, ':table' => $t]);
        $schema[$t] = $cols->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode(['ok' => true, 'tables' => $tables, 'schema' => $schema], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
