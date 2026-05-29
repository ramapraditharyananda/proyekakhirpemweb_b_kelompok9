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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'ubah_stok') {
    $id_produk = (int)($_POST['id_produk'] ?? 0);
    $stok_baru = ($_POST['stok'] === 'tersedia') ? 'tersedia' : 'habis';
    $stmt = $pdo->prepare("UPDATE produk SET stok = ? WHERE id = ? AND toko_id IN (SELECT id FROM toko WHERE user_id = ?)");
    $stmt->execute([$stok_baru, $id_produk, $id_user]);
    header("Location: DashboardPemilikController.php");
    exit;
}

$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmtUser->execute([$id_user]);
$dataUser = $stmtUser->fetch();
$namaUser = $dataUser['nama'] ?? 'Pemilik';

$stmtToko = $pdo->prepare("SELECT * FROM toko WHERE user_id = ? LIMIT 1");
$stmtToko->execute([$id_user]);
$toko = $stmtToko->fetch();
$toko_id = $toko['id'] ?? 0;

$stmtProduk = $pdo->prepare("
    SELECT p.*, k.nama AS nama_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.toko_id = ?
    ORDER BY p.created_at DESC
");
$stmtProduk->execute([$toko_id]);
$produkList = $stmtProduk->fetchAll();

$jumlahTayang   = 0;
$jumlahMenunggu = 0;
$jumlahDitolak  = 0;
foreach ($produkList as $p) {
    if ($p['status'] === 'disetujui' || $p['status'] === 'tayang') $jumlahTayang++;
    if ($p['status'] === 'pending')  $jumlahMenunggu++;
    if ($p['status'] === 'ditolak')  $jumlahDitolak++;
}
$jumlahTotal = count($produkList);

$produkTerbaru = array_slice($produkList, 0, 5);
$produkJson    = json_encode(array_values($produkList), JSON_UNESCAPED_UNICODE);

$jamSekarang = (int)date('H');
if ($jamSekarang >= 5 && $jamSekarang < 12)       $sapaan = 'Selamat Pagi';
elseif ($jamSekarang >= 12 && $jamSekarang < 15)  $sapaan = 'Selamat Siang';
elseif ($jamSekarang >= 15 && $jamSekarang < 19)  $sapaan = 'Selamat Sore';
else                                               $sapaan = 'Selamat Malam';

require_once '../views/dashboard/dashboard-pemilik.php';