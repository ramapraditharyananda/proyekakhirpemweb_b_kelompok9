<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: DaftarProdukController.php');
    exit;
}

$id = (int)$_GET['id'];
$pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$id]);

header('Location: DaftarProdukController.php?hapus=sukses');
exit;
