<?php
if (!isset($pdo) || !isset($DB_NAME)) {
    return;
}

function schema_columns(string $table): array
{
    global $pdo, $DB_NAME;
    static $cache = [];
    $key = $table;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    $stmt = $pdo->prepare('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table');
    $stmt->execute([':schema' => $DB_NAME, ':table' => $table]);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $cache[$key] = $cols;
    return $cols;
}

function schema_has_column(string $table, string $column): bool
{
    $cols = schema_columns($table);
    return in_array($column, $cols, true);
}

function select_columns_for(string $table, array $preferred): string
{
    $exists = schema_columns($table);
    $chosen = [];
    foreach ($preferred as $col) {
        if (in_array($col, $exists, true)) {
            $chosen[] = $col;
        }
    }
    if (empty($chosen)) {
        return '*';
    }
    return implode(', ', $chosen);
}

function select_columns_with_aliases(string $table, array $mapping): string
{
    $exists = schema_columns($table);
    $parts = [];
    foreach ($mapping as $appKey => $candidates) {
        foreach ($candidates as $candidate) {
            if (in_array($candidate, $exists, true)) {
                $parts[] = sprintf('%s AS %s', $candidate, $appKey);
                continue 2;
            }
        }
    }
    if (empty($parts)) {
        return '*';
    }
    return implode(', ', $parts);
}

$SCHEMA_COMPAT = [
    'jenis_surat_has_kode' => schema_has_column('jenis_surat', 'kode'),
    'pengajuan_has_nomor_surat' => schema_has_column('pengajuan_surat', 'nomor_surat'),
    'pengumuman_has_is_published' => schema_has_column('pengumuman', 'is_published'),
];