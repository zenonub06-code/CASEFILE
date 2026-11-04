<?php
// ===== Konfigurasi koneksi database =====
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'casefile_db';

// Ubah jika folder proyek bukan "casefile" di htdocs
define('BASE', '/casefile/');

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

if (session_status() === PHP_SESSION_NONE) session_start();

// ===== Fungsi bantu =====
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Token tidak valid. Muat ulang halaman.');
}
function badge($s) {
    return '<span class="badge s-' . strtolower(str_replace(' ', '-', $s)) . '">' . e($s) . '</span>';
}
function card($r) {
    return '<a class="card" href="' . BASE . 'case_detail.php?id=' . (int)$r['id'] . '">'
        . '<div class="meta"><span class="cid">' . e($r['case_id']) . '</span>' . badge($r['status']) . '</div>'
        . '<h3>' . e($r['judul']) . '</h3><p>' . e($r['ringkasan']) . '</p>'
        . '<small>' . e($r['kategori']) . ' · ' . e($r['lokasi']) . ' · ' . e($r['tahun']) . '</small></a>';
}
