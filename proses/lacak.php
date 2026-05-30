<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lacak_permohonan.php');
    exit;
}
$tracking_id = trim($_POST['tracking_id'] ?? '');
if ($tracking_id === '') {
    header('Location: lacak_permohonan.php');
    exit;
}
header('Location: lacak_permohonan.php?tracking_id=' . urlencode($tracking_id));
exit;
