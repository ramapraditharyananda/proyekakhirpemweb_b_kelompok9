<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: LoginController.php");
    exit();
}
require_once '../models/koneksi.php';

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: DaftarPenggunaController.php?hapus=sukses");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role   = isset($_GET['role'])   ? trim($_GET['role'])   : '';

$sql    = "SELECT id, nama, email, role, created_at FROM users WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (nama LIKE ? OR email LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($role !== '') {
    $sql .= " AND role = ?";
    $params[] = $role;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$penggunaList = $stmt->fetchAll();

function rolePill($role) {
    if ($role === 'admin')   return '<span class="adm-pill" style="background:#eeedf8;color:var(--purple);">Admin</span>';
    if ($role === 'pemilik') return '<span class="adm-pill adm-pill-acc">Pemilik UMKM</span>';
    return '<span class="adm-pill adm-pill-pending">Pengunjung</span>';
}

require_once '../views/pengguna/daftar-pengguna.php';