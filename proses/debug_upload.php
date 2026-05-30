<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Debug Upload Checker for Kelurahan Sasi\n";
echo "===============================\n\n";

// 1) Check upload folder
echo "UPLOAD_PATH: " . UPLOAD_PATH . "\n";
if (is_dir(UPLOAD_PATH)) {
    echo "- Exists: YES\n";
    echo "- Writable: " . (is_writable(UPLOAD_PATH) ? 'YES' : 'NO') . "\n";
    // try creating a temporary file
    $tmp = UPLOAD_PATH . DIRECTORY_SEPARATOR . '.upload_test_' . uniqid();
    $ok = @file_put_contents($tmp, 'test');
    if ($ok !== false) {
        echo "- Can create files: YES\n";
        @unlink($tmp);
    } else {
        echo "- Can create files: NO (permission denied)\n";
    }
} else {
    echo "- Exists: NO\n";
}

// 2) Check generate_tracking_id()
try {
    $id = generate_tracking_id($pdo);
    echo "\ngenerate_tracking_id() => " . $id . "\n";
} catch (Throwable $e) {
    echo "\ngenerate_tracking_id() error: " . $e->getMessage() . "\n";
}

// 3) Check DB connection
try {
    $v = $pdo->query('SELECT 1')->fetchColumn();
    echo "\nDatabase connection: OK (SELECT 1 returned: " . var_export($v, true) . ")\n";
} catch (Throwable $e) {
    echo "\nDatabase connection error: " . $e->getMessage() . "\n";
}

echo "\nInstructions:\n";
echo "- Visit /proses/debug_upload.php in your browser.\n";
echo "- If uploads folder is missing, create it: mkdir -p " . UPLOAD_PATH . " and make it writable by the webserver.\n";
echo "- Check return values above to identify the problem.\n";

exit;
