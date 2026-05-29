<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        if ($nama === '') {
            header("Location: PengaturanKategoriController.php?error=Nama+kategori+tidak+boleh+kosong");
            exit;
        }
        $pdo->prepare("INSERT INTO kategori (nama) VALUES (?)")->execute([$nama]);
        header("Location: PengaturanKategoriController.php");
        exit;
    }

    if ($aksi === 'edit') {
        $id   = (int)($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        if ($nama === '') {
            header("Location: PengaturanKategoriController.php?error=Nama+kategori+tidak+boleh+kosong");
            exit;
        }
        $pdo->prepare("UPDATE kategori SET nama = ? WHERE id = ?")->execute([$nama, $id]);
        header("Location: PengaturanKategoriController.php");
        exit;
    }
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM kategori WHERE id = ?")->execute([$id]);
    header("Location: PengaturanKategoriController.php");
    exit;
}

$stmt = $pdo->query("
    SELECT k.id, k.nama, COUNT(p.id) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.kategori_id = k.id
    GROUP BY k.id, k.nama
    ORDER BY k.id ASC
");
$kategoriList = $stmt->fetchAll();

require_once '../views/pengguna/pengaturan-kategori.php';