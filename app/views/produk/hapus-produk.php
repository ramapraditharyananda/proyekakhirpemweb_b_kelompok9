<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
require_once '../../../app/models/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: daftar-produk.php');
    exit;
}

$id = (int)$_GET['id'];
$pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$id]);

header('Location: daftar-produk.php?hapus=sukses');
exit;
