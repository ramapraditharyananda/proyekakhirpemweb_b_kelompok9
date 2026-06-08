<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$status   = isset($_GET['status'])   ? trim($_GET['status'])   : '';

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $pdo->prepare("DELETE FROM toko WHERE id = ?")->execute([(int)$_GET['hapus']]);
    header('Location: DaftarUmkmController.php?hapus=sukses');
    exit;
}

$sql = "
    SELECT t.id, t.nama_toko, t.kategori, t.alamat, t.no_wa, t.deskripsi, t.status, t.created_at,
           u.nama AS nama_pemilik,
           COUNT(p.id) AS jumlah_produk
    FROM toko t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN produk p ON p.toko_id = t.id AND p.status = 'tayang'
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (t.nama_toko LIKE ? OR u.nama LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($kategori !== '') {
    $sql .= " AND t.kategori = ?";
    $params[] = $kategori;
}
if ($status !== '') {
    $sql .= " AND t.status = ?";
    $params[] = $status;
}
$sql .= " GROUP BY t.id ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tokoList = $stmt->fetchAll();

$stmtKat = $pdo->query("SELECT DISTINCT kategori FROM toko WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
$kategoriList = $stmtKat->fetchAll();

function statusPill($status) {
    if ($status === 'aktif')    return '<span class="adm-pill adm-pill-acc">Aktif</span>';
    if ($status === 'nonaktif') return '<span class="adm-pill adm-pill-tolak">Nonaktif</span>';
    return '<span class="adm-pill adm-pill-pending">' . htmlspecialchars($status) . '</span>';
}

$ikonKategori = [
    'Makanan'              => '🍱',
    'Minuman'              => '🥤',
    'Fashion & Kain Tapis' => '👘',
    'Kerajinan Tangan'     => '🧺',
    'Pertanian & Rempah'   => '🌿',
];

require_once '../views/umkm/daftar-umkm.php';