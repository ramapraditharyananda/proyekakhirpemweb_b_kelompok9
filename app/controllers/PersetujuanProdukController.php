<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['aksi'])) {
    $id   = (int)$_POST['id'];
    $aksi = $_POST['aksi'];
    if (in_array($aksi, ['tayang', 'ditolak', 'pending'])) {
        $pdo->prepare("UPDATE produk SET status = ? WHERE id = ?")->execute([$aksi, $id]);
    }
    header('Location: PersetujuanProdukController.php');
    exit;
}

$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$status   = isset($_GET['status'])   ? trim($_GET['status'])   : '';

$sql = "
    SELECT p.id, p.nama, p.harga, p.foto, p.status, p.created_at,
           t.nama_toko, k.nama AS kategori_nama
    FROM produk p
    LEFT JOIN toko t ON p.toko_id = t.id
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (p.nama LIKE ? OR t.nama_toko LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($kategori !== '') {
    $sql .= " AND k.nama = ?";
    $params[] = $kategori;
}
if ($status !== '') {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produkList = $stmt->fetchAll();

$stmtKat = $pdo->query("SELECT nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKat->fetchAll();

function statusPill($status) {
    if ($status === 'tayang')  return '<span class="adm-pill adm-pill-acc">Disetujui</span>';
    if ($status === 'pending') return '<span class="adm-pill adm-pill-pending">Pending</span>';
    if ($status === 'ditolak') return '<span class="adm-pill adm-pill-tolak">Ditolak</span>';
    return '<span class="adm-pill">' . htmlspecialchars($status) . '</span>';
}

function labelStatus($status) {
    if ($status === 'tayang')  return 'Disetujui';
    if ($status === 'ditolak') return 'Ditolak';
    return 'Pending';
}

function waktuRelatif($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 3600)    return floor($diff / 60) . ' menit lalu';
    if ($diff < 86400)   return floor($diff / 3600) . ' jam lalu';
    if ($diff < 2592000) return floor($diff / 86400) . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}

require_once '../views/produk/persetujuan-produk.php';
