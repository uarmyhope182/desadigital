<?php
require_once __DIR__ . '/../config/db.php';
require_admin();

$page_title = 'Dashboard Admin';
$activePage = 'dashboard';
$is_admin_page = true;

// Ambil data user yang sedang login
$current_user = current_user();

// Get statistics dengan error handling
try {
    $total = (int) $pdo->query('SELECT COUNT(*) FROM pengajuan_surat')->fetchColumn();
    $pending = (int) $pdo->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'pending'")->fetchColumn();
    $approved = (int) $pdo->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'approved'")->fetchColumn();
    $rejected = (int) $pdo->query("SELECT COUNT(*) FROM pengajuan_surat WHERE status = 'rejected'")->fetchColumn();
} catch (Exception $e) {
    error_log('Error getting statistics: ' . $e->getMessage());
    $total = $pending = $approved = $rejected = 0;
}

// Month statistics
try {
    $monthStmt = $pdo->prepare('
        SELECT COUNT(*) 
        FROM pengajuan_surat 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ');
    $monthStmt->execute();
    $month = (int) $monthStmt->fetchColumn();
} catch (Exception $e) {
    $month = 0;
}

// Total users
try {
    $total_users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Exception $e) {
    $total_users = 0;
}

// Total penduduk
try {
    $total_penduduk = (int) $pdo->query('SELECT COUNT(*) FROM penduduk')->fetchColumn();
} catch (Exception $e) {
    $total_penduduk = 0;
}

// Latest submissions
try {
    $latest = $pdo->query('
        SELECT ps.id, ps.tracking_id, ps.nama_lengkap, js.nama AS jenis_surat, ps.status, ps.created_at 
        FROM pengajuan_surat ps 
        JOIN jenis_surat js ON ps.jenis_surat_id = js.id 
        ORDER BY ps.created_at DESC 
        LIMIT 10
    ')->fetchAll();
} catch (Exception $e) {
    $latest = [];
}

// Dashboard filter input
$filter_range = isset($_GET['range']) && in_array($_GET['range'], ['today', 'week', 'month']) ? $_GET['range'] : 'month';
$filter_jenis = isset($_GET['jenis_surat']) && is_numeric($_GET['jenis_surat']) ? (int) $_GET['jenis_surat'] : '';
$filterConditions = [];
$filterParams = [];

if ($filter_range === 'today') {
    $filterConditions[] = 'DATE(ps.created_at) = CURDATE()';
} elseif ($filter_range === 'week') {
    $filterConditions[] = 'YEARWEEK(ps.created_at, 1) = YEARWEEK(CURDATE(), 1)';
} else {
    $filterConditions[] = 'MONTH(ps.created_at) = MONTH(CURDATE()) AND YEAR(ps.created_at) = YEAR(CURDATE())';
}

if ($filter_jenis) {
    $filterConditions[] = 'ps.jenis_surat_id = :jenis_surat';
    $filterParams[':jenis_surat'] = $filter_jenis;
}

$dashboardFilterWhere = !empty($filterConditions) ? 'WHERE ' . implode(' AND ', $filterConditions) : '';
$lineChartWhere = 'WHERE ps.created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), "%Y-%m-01")';
if ($filter_jenis) {
    $lineChartWhere .= ' AND ps.jenis_surat_id = :jenis_surat';
}

try {
    $jenis_surat_list = $pdo->query('SELECT id, nama FROM jenis_surat ORDER BY nama')->fetchAll();
} catch (Exception $e) {
    $jenis_surat_list = [];
}

try {
    $pendingWhere = $dashboardFilterWhere;
    if (!empty($pendingWhere)) {
        $pendingWhere .= ' AND ps.status = "pending"';
    } else {
        $pendingWhere = 'WHERE ps.status = "pending"';
    }
    $pendingStmt = $pdo->prepare('SELECT COUNT(*) AS total, MIN(created_at) AS oldest FROM pengajuan_surat ps ' . $pendingWhere);
    $pendingStmt->execute($filterParams);
    $pendingDetails = $pendingStmt->fetch(PDO::FETCH_ASSOC);
    $pending_count = (int) ($pendingDetails['total'] ?? 0);
    $pending_oldest = $pendingDetails['oldest'] ? date('d M Y H:i', strtotime($pendingDetails['oldest'])) : '-';
} catch (Exception $e) {
    $pending_count = 0;
    $pending_oldest = '-';
}

try {
    $processWhere = !empty($filterConditions) ? 'WHERE ' . implode(' AND ', $filterConditions) . ' AND ps.diproses_pada IS NOT NULL' : 'WHERE ps.diproses_pada IS NOT NULL';
    $processStmt = $pdo->prepare('SELECT AVG(TIMESTAMPDIFF(SECOND, ps.created_at, ps.diproses_pada)) / 3600 AS avg_hours FROM pengajuan_surat ps ' . $processWhere);
    $processStmt->execute($filterParams);
    $avg_process_hours = round((float) $processStmt->fetchColumn(), 1);
} catch (Exception $e) {
    $avg_process_hours = 0;
}

try {
    $top5Stmt = $pdo->prepare('SELECT js.nama AS jenis, COUNT(*) AS total FROM pengajuan_surat ps JOIN jenis_surat js ON ps.jenis_surat_id = js.id ' . $dashboardFilterWhere . ' GROUP BY js.id ORDER BY total DESC LIMIT 5');
    $top5Stmt->execute($filterParams);
    $top5_types = $top5Stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $top5_types = [];
}

try {
    $statusStmt = $pdo->prepare('SELECT ps.status, COUNT(*) AS total FROM pengajuan_surat ps ' . $dashboardFilterWhere . ' GROUP BY ps.status');
    $statusStmt->execute($filterParams);
    $status_values = array_reduce($statusStmt->fetchAll(PDO::FETCH_ASSOC), function ($carry, $item) {
        $carry[$item['status']] = (int) $item['total'];
        return $carry;
    }, ['pending' => 0, 'approved' => 0, 'rejected' => 0]);
} catch (Exception $e) {
    $status_values = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
}

try {
    $monthlyStmt = $pdo->prepare('SELECT DATE_FORMAT(ps.created_at, "%Y-%m") AS ym, COUNT(*) AS total FROM pengajuan_surat ps ' . $lineChartWhere . ' GROUP BY ym ORDER BY ym ASC');
    $monthlyStmt->execute($filterParams);
    $monthly_raw = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $monthly_raw = [];
}

$month_labels = [];
for ($i = 11; $i >= 0; $i--) {
    $date = new DateTime(sprintf('-%d months', $i));
    $month_labels[] = $date->format('M Y');
}
$month_values = array_fill(0, count($month_labels), 0);
foreach ($monthly_raw as $row) {
    $monthLabel = DateTime::createFromFormat('Y-m', $row['ym'])->format('M Y');
    $index = array_search($monthLabel, $month_labels, true);
    if ($index !== false) {
        $month_values[$index] = (int) $row['total'];
    }
}

$jenis_labels = [];
$jenis_values = [];
foreach ($top5_types as $row) {
    $jenis_labels[] = $row['jenis'];
    $jenis_values[] = (int) $row['total'];
}

// Data untuk Area Chart (6 bulan terakhir)
$area_labels = array_slice($month_labels, -6);
$area_values = array_slice($month_values, -6);

// Data untuk Radar Chart (perbandingan semua jenis surat)
try {
    $allJenisStmt = $pdo->prepare('
        SELECT js.nama AS jenis, COUNT(ps.id) AS total 
        FROM jenis_surat js 
        LEFT JOIN pengajuan_surat ps ON js.id = ps.jenis_surat_id 
        GROUP BY js.id 
        ORDER BY total DESC
    ');
    $allJenisStmt->execute();
    $all_jenis = $allJenisStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $all_jenis = [];
}

$radar_labels = [];
$radar_values = [];
foreach ($all_jenis as $jenis) {
    $radar_labels[] = $jenis['jenis'];
    $radar_values[] = (int) $jenis['total'];
}

$status_labels = ['Pending', 'Approved', 'Rejected'];
$status_values_array = [
    $status_values['pending'],
    $status_values['approved'],
    $status_values['rejected'],
];

// Data untuk Grafik Perbandingan Bulanan per Status
try {
    $monthlyStatusStmt = $pdo->prepare('
        SELECT 
            DATE_FORMAT(ps.created_at, "%Y-%m") AS ym,
            SUM(CASE WHEN ps.status = "pending" THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN ps.status = "approved" THEN 1 ELSE 0 END) AS approved,
            SUM(CASE WHEN ps.status = "rejected" THEN 1 ELSE 0 END) AS rejected
        FROM pengajuan_surat ps
        WHERE ps.created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
        GROUP BY ym
        ORDER BY ym ASC
    ');
    $monthlyStatusStmt->execute();
    $monthly_status = $monthlyStatusStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $monthly_status = [];
}

$stack_labels = [];
$stack_pending = [];
$stack_approved = [];
$stack_rejected = [];
foreach ($monthly_status as $row) {
    $stack_labels[] = DateTime::createFromFormat('Y-m', $row['ym'])->format('M Y');
    $stack_pending[] = (int) $row['pending'];
    $stack_approved[] = (int) $row['approved'];
    $stack_rejected[] = (int) $row['rejected'];
}

// Format tanggal untuk header
$tanggal = new DateTime();
$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tanggal_formatted = $hari[$tanggal->format('w')] . ', ' . $tanggal->format('d') . ' ' . $bulan[$tanggal->format('n')-1] . ' ' . $tanggal->format('Y');

include __DIR__ . '/../includes/header.php';
?>

<!-- Ghibli Theme - Dashboard Admin -->
<div class="dashboard-bg-sky"></div>
<div class="dashboard-bg-rice-fields"></div>
<div class="dashboard-bg-atmosphere"></div>

<style>
/* ============================================
   GHIBLI ADMIN THEME - DASHBOARD
   ============================================ */

:root {
    --ghibli-cream: #FDFBF7;
    --ghibli-beige: #FAF6F0;
    --ghibli-sage: #9CAF88;
    --ghibli-sage-dark: #7A8F64;
    --ghibli-sage-light: #B5C4A3;
    --ghibli-peach: #F6C7A1;
    --ghibli-peach-dark: #F0B885;
    --ghibli-sky: #B9DCFF;
    --ghibli-text-dark: #1A1512;
    --ghibli-text-soft: #2C241E;
    --ghibli-border: #E8E0D5;
    --ghibli-shadow: rgba(58, 49, 43, 0.05);
    --ghibli-shadow-hover: rgba(58, 49, 43, 0.1);
    --status-pending: #F6C7A1;
    --status-processing: #B9DCFF;
    --status-approved: #9CAF88;
    --status-rejected: #E8C5C5;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #B9DCFF 0%, #D4E8FF 50%, #FAF6F0 100%);
    min-height: 100vh;
}

.dashboard-bg-sky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(180deg, var(--ghibli-sky) 0%, #D4E8FF 50%, var(--ghibli-beige) 100%);
    z-index: 0;
    pointer-events: none;
}

.dashboard-bg-rice-fields {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.3"><rect x="0" y="0" width="20" height="100" fill="%239CAF88"/><rect x="25" y="0" width="20" height="100" fill="%239CAF88"/><rect x="50" y="0" width="20" height="100" fill="%239CAF88"/><rect x="75" y="0" width="20" height="100" fill="%239CAF88"/></svg>');
    background-repeat: repeat;
    background-size: 40px 100%;
    opacity: 0.06;
    z-index: 0;
    pointer-events: none;
}

.dashboard-bg-atmosphere {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 40%, rgba(156, 175, 136, 0.05) 0%, transparent 60%);
    z-index: 0;
    pointer-events: none;
}

.dashboard-container {
    position: relative;
    z-index: 2;
    padding: 1.5rem 2rem 2rem 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
    background: rgba(253, 251, 247, 0.8);
    backdrop-filter: blur(10px);
    padding: 1.25rem 1.75rem;
    border-radius: 28px;
    border: 1px solid rgba(156, 175, 136, 0.2);
}

.greeting-text h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.greeting-text p {
    color: var(--ghibli-text-soft);
    font-size: 0.85rem;
    font-weight: 500;
}

.dashboard-date {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 1.2rem;
    background: rgba(156, 175, 136, 0.12);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ghibli-text-dark);
    border: 1px solid var(--ghibli-border);
}

/* Section Header */
.section-header {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2.5px solid var(--ghibli-sage-light);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Stat Cards */
.stat-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid var(--ghibli-border);
    border-radius: 24px;
    transition: all 0.3s ease;
    height: 100%;
    overflow: hidden;
    box-shadow: 0 4px 12px var(--ghibli-shadow);
}

.stat-card:hover {
    transform: translateY(-3px);
    border-color: var(--ghibli-sage-light);
    box-shadow: 0 12px 28px var(--ghibli-shadow-hover);
}

.stat-card-body {
    padding: 1.25rem 1.5rem;
}

.stat-card-border-sage { border-left: 4px solid var(--ghibli-sage); }
.stat-card-border-peach { border-left: 4px solid var(--ghibli-peach); }
.stat-card-border-approved { border-left: 4px solid var(--status-approved); }
.stat-card-border-rejected { border-left: 4px solid var(--status-rejected); }
.stat-card-border-neutral { border-left: 4px solid var(--ghibli-border); }

.stat-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--ghibli-text-soft);
    font-weight: 700;
    opacity: 0.8;
    margin-bottom: 0.35rem;
}

.stat-number {
    font-family: 'Bricolage Grotesque', monospace;
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.2;
}

.stat-number-sage { color: var(--ghibli-sage-dark); }
.stat-number-peach { color: #D49C6C; }
.stat-number-approved { color: var(--ghibli-sage-dark); }
.stat-number-rejected { color: #B88686; }
.stat-number-dark { color: var(--ghibli-text-dark); }

.stat-icon {
    font-size: 1.8rem;
    opacity: 0.35;
}

/* Charts Grid - 2 kolom untuk grafik */
.charts-grid {
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(2, 1fr);
}

/* Filter Card - FULL WIDTH seperti tabel */
.filter-card-full {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid var(--ghibli-border);
    border-radius: 24px;
    overflow: hidden;
    transition: transform 0.25s ease;
    margin-top: 1.25rem;
    width: 100%;
}

.filter-card-full:hover {
    transform: translateY(-2px);
}

.filter-card-full .filter-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--ghibli-border);
    background: rgba(156, 175, 136, 0.08);
}

.filter-card-full .filter-card-header h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
}

.filter-card-full .filter-card-body {
    padding: 1.5rem;
}

.filter-card-full form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.filter-card-full .filter-group {
    flex: 1;
    min-width: 180px;
}

.filter-card-full label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--ghibli-text-dark);
    margin-bottom: 0.5rem;
    display: block;
}

.filter-card-full select {
    width: 100%;
    border-radius: 14px;
    border: 1.5px solid var(--ghibli-border);
    padding: 0.7rem 1rem;
    font-size: 0.85rem;
    color: var(--ghibli-text-dark);
    background: white;
    transition: all 0.2s ease;
    cursor: pointer;
}

.filter-card-full select:focus {
    border-color: var(--ghibli-sage);
    outline: none;
    box-shadow: 0 0 0 3px rgba(156, 175, 136, 0.2);
}

.filter-card-full .btn-primary {
    background: linear-gradient(135deg, var(--ghibli-sage), var(--ghibli-sage-dark));
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 700;
    padding: 0.7rem 1.5rem;
    border-radius: 40px;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.filter-card-full .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(156, 175, 136, 0.3);
}

.filter-card-full .btn-secondary {
    background: rgba(156, 175, 136, 0.08);
    color: var(--ghibli-text-dark);
    border: 1.5px solid var(--ghibli-border);
    cursor: pointer;
    font-weight: 700;
    padding: 0.7rem 1.5rem;
    border-radius: 40px;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.filter-card-full .btn-secondary:hover {
    background: rgba(156, 175, 136, 0.15);
    border-color: var(--ghibli-sage);
}

.chart-card,
.widget-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid var(--ghibli-border);
    border-radius: 24px;
    overflow: hidden;
    transition: transform 0.25s ease;
}

.chart-card:hover,
.widget-card:hover {
    transform: translateY(-2px);
}

.chart-card-header,
.widget-card-header {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--ghibli-border);
    background: rgba(156, 175, 136, 0.08);
}

.chart-card-header h5,
.widget-card-header h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
}

.chart-card-body,
.widget-card-body {
    padding: 1.2rem 1.3rem;
}

.chart-canvas {
    width: 100%;
    min-height: 280px;
}

/* Widget Cards */
.widget-metric {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.widget-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--ghibli-text-dark);
}

.widget-subtitle {
    color: var(--ghibli-text-soft);
    font-size: 0.8rem;
    margin-top: 0.35rem;
}

/* Table Card */
.table-card {
    background: rgba(253, 251, 247, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid var(--ghibli-border);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 4px 12px var(--ghibli-shadow);
}

.table-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: rgba(156, 175, 136, 0.06);
    border-bottom: 1px solid var(--ghibli-border);
}

.table-card-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-outline-ghibli {
    background: transparent;
    border: 1.5px solid var(--ghibli-border);
    border-radius: 40px;
    padding: 0.35rem 1.2rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--ghibli-text-soft);
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.btn-outline-ghibli:hover {
    border-color: var(--ghibli-sage);
    background: rgba(156, 175, 136, 0.12);
    color: var(--ghibli-sage-dark);
}

/* Dashboard Table */
.dashboard-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.dashboard-table {
    width: 100%;
    min-width: 650px;
    border-collapse: collapse;
    font-size: 0.85rem;
}

.dashboard-table thead {
    background: rgba(156, 175, 136, 0.05);
}

.dashboard-table th {
    padding: 1rem 1.2rem;
    font-weight: 700;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #5C4A3A;
    border-bottom: 2px solid var(--ghibli-border);
    text-align: left;
}

.dashboard-table td {
    padding: 1rem 1.2rem;
    border-bottom: 1px solid var(--ghibli-border);
    color: var(--ghibli-text-soft);
    vertical-align: middle;
}

.dashboard-table tbody tr:hover {
    background: rgba(156, 175, 136, 0.04);
}

.dashboard-table tbody tr:last-child td {
    border-bottom: none;
}

.col-tracking-id { width: 22%; }
.col-nama-pemohon { width: 28%; }
.col-jenis-surat { width: 22%; }
.col-status-table { width: 15%; text-align: center; }
.col-tanggal-table { width: 13%; }

.text-center {
    text-align: center;
}

.tracking-code {
    font-family: 'Courier New', 'Fira Code', monospace;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--ghibli-text-dark);
    background: linear-gradient(135deg, rgba(156, 175, 136, 0.12), rgba(246, 199, 161, 0.06));
    padding: 0.25rem 0.7rem;
    border-radius: 10px;
    display: inline-block;
    border: 1px solid rgba(156, 175, 136, 0.2);
    white-space: nowrap;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.3rem 1rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
}

.status-approved { background: var(--status-approved); color: white; }
.status-rejected { background: var(--status-rejected); color: #6B4A4A; }
.status-processing { background: var(--status-processing); color: #2C5F6E; }
.status-pending { background: var(--status-pending); color: #4A3A2A; }

.empty-state {
    text-align: center;
    padding: 2.5rem;
    color: var(--ghibli-text-soft);
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    display: block;
    opacity: 0.4;
    color: var(--ghibli-sage);
}

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.col-md-3 { flex: 1; min-width: 180px; }
.col-md-6 { flex: 1; min-width: 250px; }
.col-lg-12 { width: 100%; }

.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 2rem; }

.d-flex { display: flex; }
.gap-2 { gap: 0.5rem; }
.fw-semibold { font-weight: 600; }

/* Responsive */
@media (max-width: 1200px) {
    .charts-grid { grid-template-columns: 1fr; }
}

@media (max-width: 992px) {
    .dashboard-container { padding: 1rem; }
    .filter-card-full .filter-group { min-width: 100%; }
    .filter-card-full form { flex-direction: column; }
    .filter-card-full .btn-primary,
    .filter-card-full .btn-secondary { width: 100%; justify-content: center; }
}

@media (max-width: 768px) {
    .dashboard-header { flex-direction: column; align-items: flex-start; }
    .greeting-text h1 { font-size: 1.25rem; }
    .stat-number { font-size: 1.5rem; }
    .dashboard-table th,
    .dashboard-table td { padding: 0.75rem 1rem; font-size: 0.75rem; }
    .tracking-code { font-size: 0.65rem; padding: 0.2rem 0.5rem; }
}

@media (max-width: 576px) {
    .dashboard-container { padding: 0.75rem; }
    .dashboard-table { min-width: 580px; }
    .tracking-code { font-size: 0.6rem; padding: 0.15rem 0.45rem; }
}
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="greeting-text">
            <h1>Selamat Datang, Administrator</h1>
            <p>Melayani dengan hati, bekerja dengan integritas</p>
        </div>
        <div class="dashboard-date">
            <i class="bi bi-calendar-event"></i>
            <span><?= $tanggal_formatted ?></span>
        </div>
    </div>

    <!-- Statistik Permohonan Surat -->
    <div class="section-header">
        <i class="bi bi-envelope-paper"></i>
        Statistik Permohonan Surat
    </div>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-sage">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Total Permohonan</div>
                            <div class="stat-number stat-number-sage">
                                <span class="js-count" data-target="<?= $total ?>"><?= number_format($total) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-sage">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-peach">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Menunggu Proses</div>
                            <div class="stat-number stat-number-peach">
                                <span class="js-count" data-target="<?= $pending ?>"><?= number_format($pending) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-peach">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-approved">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Disetujui</div>
                            <div class="stat-number stat-number-approved">
                                <span class="js-count" data-target="<?= $approved ?>"><?= number_format($approved) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-sage">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-rejected">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Ditolak</div>
                            <div class="stat-number stat-number-rejected">
                                <span class="js-count" data-target="<?= $rejected ?>"><?= number_format($rejected) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-x-circle" style="color: var(--status-rejected);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analitik Permohonan - Grafik -->
    <div class="section-header">
        <i class="bi bi-bar-chart-line"></i>
        Analitik & Visualisasi Data
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="charts-grid">
                <!-- Line Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-graph-up"></i> Tren 12 Bulan Terakhir</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="lineChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <!-- Area Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-cloud-arrow-up"></i> Area Chart - 6 Bulan Terakhir</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="areaChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <!-- Stacked Bar Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-bar-chart-steps"></i> Stacked Bar - Status per Bulan</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="stackedBarChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <!-- Radar Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-radar"></i> Radar - Perbandingan Jenis Surat</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="radarChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-bar-chart"></i> Top 5 Jenis Surat</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="barChart" class="chart-canvas"></canvas>
                    </div>
                </div>

                <!-- Donut Chart -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h5><i class="bi bi-pie-chart"></i> Status Permohonan</h5>
                    </div>
                    <div class="chart-card-body">
                        <canvas id="donutChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>

            <!-- FILTER LAPORAN - FULL WIDTH (SAMA SEPERTI TABEL) -->
            <div class="filter-card-full">
                <div class="filter-card-header">
                    <h5><i class="bi bi-funnel"></i> Filter Laporan</h5>
                </div>
                <div class="filter-card-body">
                    <form method="get" id="dashboard-filter-form">
                        <div class="filter-group">
                            <label for="range">Periode Tanggal</label>
                            <select name="range" id="range">
                                <option value="today" <?= $filter_range === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                                <option value="week" <?= $filter_range === 'week' ? 'selected' : '' ?>>Minggu Ini</option>
                                <option value="month" <?= $filter_range === 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="jenis_surat">Jenis Surat</label>
                            <select name="jenis_surat" id="jenis_surat">
                                <option value="">Semua Jenis Surat</option>
                                <?php foreach ($jenis_surat_list as $jenis): ?>
                                    <option value="<?= htmlspecialchars($jenis['id']) ?>" <?= $filter_jenis === (int) $jenis['id'] ? 'selected' : '' ?>><?= htmlspecialchars($jenis['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group d-flex gap-2">
                            <button type="submit" class="btn-primary">Terapkan Filter</button>
                            <button type="button" id="filter-reset" class="btn-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget Antrean -->
    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="widget-card">
                <div class="widget-card-header">
                    <h5><i class="bi bi-clipboard-data"></i> Antrean Permohonan</h5>
                </div>
                <div class="widget-card-body">
                    <div class="widget-metric">
                        <span>
                            <span class="widget-value"><?= number_format($pending_count) ?></span>
                            <span class="widget-subtitle">Jumlah permohonan pending saat ini</span>
                        </span>
                        <i class="bi bi-clock-history" style="font-size: 2rem; color: var(--ghibli-sage);"></i>
                    </div>
                    <div class="widget-subtitle">Permohonan tertua: <strong><?= htmlspecialchars($pending_oldest) ?></strong></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="widget-card">
                <div class="widget-card-header">
                    <h5><i class="bi bi-speedometer2"></i> Rata-rata Waktu Proses</h5>
                </div>
                <div class="widget-card-body">
                    <div class="widget-metric">
                        <span>
                            <span class="widget-value"><?= number_format($avg_process_hours, 1) ?></span>
                            <span class="widget-subtitle">Jam</span>
                        </span>
                        <i class="bi bi-clock-fill" style="font-size: 2rem; color: var(--ghibli-peach);"></i>
                    </div>
                    <div class="widget-subtitle">Target: <strong>24 jam</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kelurahan -->
    <div class="section-header">
        <i class="bi bi-tree"></i>
        Statistik Kelurahan
    </div>
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-sage">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Permohonan Bulan Ini</div>
                            <div class="stat-number stat-number-sage">
                                <span class="js-count" data-target="<?= $month ?>"><?= number_format($month) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-sage">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-neutral">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Total Pengguna</div>
                            <div class="stat-number stat-number-dark">
                                <span class="js-count" data-target="<?= $total_users ?>"><?= number_format($total_users) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-people" style="color: #5C4A3A;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-approved">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Data Penduduk</div>
                            <div class="stat-number stat-number-approved">
                                <span class="js-count" data-target="<?= $total_penduduk ?>"><?= number_format($total_penduduk) ?></span>
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-sage">
                            <i class="bi bi-person-standing"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-card-border-peach">
                <div class="stat-card-body">
                    <div class="stat-info">
                        <div>
                            <div class="stat-label">Persetujuan</div>
                            <div class="stat-number stat-number-peach">
                                <span class="js-count" data-target="<?= $total > 0 ? round(($approved / $total) * 100) : 0 ?>"><?= $total > 0 ? round(($approved / $total) * 100) : 0 ?></span>%
                            </div>
                        </div>
                        <div class="stat-icon stat-icon-peach">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Permohonan Terbaru - FULL WIDTH -->
    <div class="table-card">
        <div class="table-card-header">
            <h5 class="table-card-title">
                <i class="bi bi-files"></i>
                Permohonan Terbaru
            </h5>
            <a href="<?= ADMIN_URL ?>/kelola_permohonan.php" class="btn-outline-ghibli">
                <i class="bi bi-eye"></i> Lihat Semua
            </a>
        </div>
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th class="col-tracking-id">TRACKING ID</th>
                        <th class="col-nama-pemohon">NAMA PEMOHON</th>
                        <th class="col-jenis-surat">JENIS SURAT</th>
                        <th class="col-status-table">STATUS</th>
                        <th class="col-tanggal-table">TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latest)): ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada permohonan baru</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($latest as $row): ?>
                            <tr>
                                <td><code class="tracking-code"><?= htmlspecialchars($row['tracking_id']) ?></code></td>
                                <td class="fw-semibold" style="color: var(--ghibli-text-dark);"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td style="color: var(--ghibli-text-dark);"><?= htmlspecialchars($row['jenis_surat']) ?></td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'approved'): ?>
                                        <span class="status-badge status-approved"><i class="bi bi-check-circle-fill"></i> Disetujui</span>
                                    <?php elseif ($row['status'] === 'rejected'): ?>
                                        <span class="status-badge status-rejected"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                                    <?php elseif ($row['status'] === 'processing'): ?>
                                        <span class="status-badge status-processing"><i class="bi bi-hourglass-split"></i> Diproses</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending"><i class="bi bi-clock-fill"></i> Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.75rem; white-space: nowrap;"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animasi counter
    const counters = document.querySelectorAll('.js-count');
    const speed = 200;
    
    counters.forEach(counter => {
        const updateCount = () => {
            const target = parseInt(counter.getAttribute('data-target'));
            const current = parseInt(counter.innerText.replace(/[^0-9]/g, '')) || 0;
            const increment = Math.ceil(target / speed);
            
            if (current < target) {
                counter.innerText = Math.min(current + increment, target);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        
        observer.observe(counter);
    });

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($month_labels, JSON_HEX_TAG) ?>,
            datasets: [{
                label: 'Permohonan',
                data: <?= json_encode(array_values($month_values), JSON_HEX_TAG) ?>,
                borderColor: 'rgba(154, 175, 136, 0.92)',
                backgroundColor: 'rgba(154, 175, 136, 0.18)',
                fill: true,
                tension: 0.34,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(154, 175, 136, 0.96)',
                pointBorderColor: '#FFFFFF',
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5C4A3A' } },
                y: { beginAtZero: true, ticks: { color: '#5C4A3A' }, grid: { color: 'rgba(148, 145, 130, 0.12)' } }
            }
        }
    });

    // Area Chart
    new Chart(document.getElementById('areaChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($area_labels, JSON_HEX_TAG) ?>,
            datasets: [{
                label: 'Jumlah Permohonan',
                data: <?= json_encode($area_values, JSON_HEX_TAG) ?>,
                borderColor: '#9CAF88',
                backgroundColor: 'rgba(156, 175, 136, 0.25)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#7A8F64',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { color: '#5C4A3A' } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5C4A3A' } },
                y: { beginAtZero: true, ticks: { color: '#5C4A3A' }, grid: { color: 'rgba(148, 145, 130, 0.12)' } }
            }
        }
    });

    // Stacked Bar Chart
    new Chart(document.getElementById('stackedBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($stack_labels, JSON_HEX_TAG) ?>,
            datasets: [
                {
                    label: 'Pending',
                    data: <?= json_encode($stack_pending, JSON_HEX_TAG) ?>,
                    backgroundColor: '#F6C7A1',
                    borderRadius: 4,
                },
                {
                    label: 'Approved',
                    data: <?= json_encode($stack_approved, JSON_HEX_TAG) ?>,
                    backgroundColor: '#9CAF88',
                    borderRadius: 4,
                },
                {
                    label: 'Rejected',
                    data: <?= json_encode($stack_rejected, JSON_HEX_TAG) ?>,
                    backgroundColor: '#E8C5C5',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { color: '#5C4A3A' } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { stacked: true, grid: { display: false }, ticks: { color: '#5C4A3A' } },
                y: { stacked: true, beginAtZero: true, ticks: { color: '#5C4A3A' }, grid: { color: 'rgba(148, 145, 130, 0.12)' } }
            }
        }
    });

    // Radar Chart
    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: <?= json_encode($radar_labels, JSON_HEX_TAG) ?>,
            datasets: [{
                label: 'Jumlah Permohonan',
                data: <?= json_encode($radar_values, JSON_HEX_TAG) ?>,
                backgroundColor: 'rgba(156, 175, 136, 0.25)',
                borderColor: '#9CAF88',
                borderWidth: 2,
                pointBackgroundColor: '#7A8F64',
                pointBorderColor: '#FFFFFF',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { color: '#5C4A3A' } },
                tooltip: { callbacks: { label: (context) => `${context.label}: ${context.raw} permohonan` } }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    ticks: { color: '#5C4A3A', stepSize: 1 },
                    grid: { color: 'rgba(148, 145, 130, 0.2)' },
                    pointLabels: { color: '#5C4A3A', font: { size: 10 } }
                }
            }
        }
    });

    // Bar Chart
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($jenis_labels, JSON_HEX_TAG) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode($jenis_values, JSON_HEX_TAG) ?>,
                backgroundColor: ['rgba(154, 175, 136, 0.88)', 'rgba(246, 199, 161, 0.88)', 'rgba(185, 220, 255, 0.88)', 'rgba(219, 176, 155, 0.88)', 'rgba(163, 193, 167, 0.88)'],
                borderRadius: 12,
                barThickness: 28,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5C4A3A' } },
                y: { beginAtZero: true, ticks: { color: '#5C4A3A' }, grid: { color: 'rgba(148, 145, 130, 0.12)' } }
            }
        }
    });

    // Donut Chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($status_labels, JSON_HEX_TAG) ?>,
            datasets: [{
                data: <?= json_encode($status_values_array, JSON_HEX_TAG) ?>,
                backgroundColor: ['rgba(246, 199, 161, 0.88)', 'rgba(154, 175, 136, 0.88)', 'rgba(232, 197, 197, 0.88)'],
                hoverOffset: 12,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#5C4A3A' } },
                tooltip: { callbacks: { label: (context) => `${context.label}: ${context.parsed} permohonan` } }
            }
        }
    });

    // Filter reset
    document.getElementById('filter-reset')?.addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>