<?php
session_start();
$timeout = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: LoginController.php");
    exit();
}
$_SESSION['last_activity'] = time();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

$stmtPengguna = $pdo->query("SELECT COUNT(*) AS total FROM users");
$totalPengguna = $stmtPengguna->fetch()['total'];

$stmtUMKM = $pdo->query("SELECT COUNT(*) AS total FROM toko");
$totalUMKM = $stmtUMKM->fetch()['total'];

$stmtTayang = $pdo->query("SELECT COUNT(*) AS total FROM produk WHERE status = 'tayang'");
$totalTayang = $stmtTayang->fetch()['total'];

$stmtMenunggu = $pdo->query("SELECT COUNT(*) AS total FROM produk WHERE status = 'pending'");
$totalMenunggu = $stmtMenunggu->fetch()['total'];

$stmtProdukTerbaru = $pdo->query("
    SELECT p.*, k.nama AS nama_kategori, t.nama_toko
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    LEFT JOIN toko t ON p.toko_id = t.id
    ORDER BY p.created_at DESC
");
$produkTerbaru = $stmtProdukTerbaru->fetchAll();

require_once '../views/dashboard/dashboard-admin.php';
