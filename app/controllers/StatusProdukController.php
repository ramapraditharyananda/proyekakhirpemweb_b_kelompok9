<?php
session_start();
$timeout = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: LoginController.php");
    exit();
}
$_SESSION['last_activity'] = time();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

$id_user = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    if ($_POST['aksi'] === 'ubah_stok') {
        $id_produk = (int)($_POST['id_produk'] ?? 0);
        $stok_baru = ($_POST['stok'] === 'tersedia') ? 'tersedia' : 'habis';
        $stmt = $pdo->prepare("UPDATE produk SET ketersediaan = ? WHERE id = ? AND toko_id IN (SELECT id FROM toko WHERE user_id = ?)");
        $stmt->execute([$stok_baru, $id_produk, $id_user]);
        header("Location: StatusProdukController.php");
        exit;
    }
    if ($_POST['aksi'] === 'hapus_produk') {
        $id_produk = (int)($_POST['id_produk'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ? AND toko_id IN (SELECT id FROM toko WHERE user_id = ?)");
        $stmt->execute([$id_produk, $id_user]);
        header("Location: StatusProdukController.php?hapus=sukses");
        exit;
    }
}

$stmtToko = $pdo->prepare("SELECT * FROM toko WHERE user_id = ? LIMIT 1");
$stmtToko->execute([$id_user]);
$toko    = $stmtToko->fetch();
$toko_id = $toko['id'] ?? 0;

$filterStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
$filterSearch = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "
    SELECT p.*, k.nama AS nama_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.toko_id = ?
";
$params = [$toko_id];

if ($filterStatus !== '') {
    if ($filterStatus === 'tayang') {
        $sql .= " AND p.status = 'tayang'";
    } else {
        $sql .= " AND p.status = ?";
        $params[] = $filterStatus;
    }
}
if ($filterSearch !== '') {
    $sql .= " AND p.nama LIKE ?";
    $params[] = '%' . $filterSearch . '%';
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produkList = $stmt->fetchAll();

$jumlahTayang   = 0;
$jumlahMenunggu = 0;
$jumlahDitolak  = 0;
foreach ($produkList as $p) {
    if ($p['status'] === 'tayang') $jumlahTayang++;
    if ($p['status'] === 'pending')  $jumlahMenunggu++;
    if ($p['status'] === 'ditolak')  $jumlahDitolak++;
}

require_once '../views/produk/status-produk.php';
