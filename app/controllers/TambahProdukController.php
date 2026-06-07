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

if (!isset($_SESSION['toko_id'])) {
    $stmtToko = $pdo->prepare("SELECT id FROM toko WHERE user_id = ? LIMIT 1");
    $stmtToko->execute([$_SESSION['user_id']]);
    $tokoRow = $stmtToko->fetch();
    if ($tokoRow) {
        $_SESSION['toko_id'] = $tokoRow['id'];
    } else {
        $namaToko = 'Toko ' . ($_SESSION['nama'] ?? 'Pemilik');
        $stmtBuat = $pdo->prepare("INSERT INTO toko (user_id, nama_toko, kategori, alamat, no_wa, deskripsi, status, created_at) VALUES (?, ?, '', '', '', '', 'aktif', NOW())");
        $stmtBuat->execute([$_SESSION['user_id'], $namaToko]);
        $_SESSION['toko_id'] = $pdo->lastInsertId();
    }
}

$errors = [];
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['namaProduk'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $stok      = ($_POST['stok'] ?? 'tersedia') === 'habis' ? 'habis' : 'tersedia';
    $tokoId    = $_SESSION['toko_id'];

    if ($nama === '')      $errors['nama']      = 'Nama produk wajib diisi.';
    if ($kategori === '')  $errors['kategori']  = 'Pilih kategori produk.';
    if ($harga === '' || (int)$harga < 0) $errors['harga'] = 'Harga wajib diisi dan tidak boleh negatif.';
    if ($deskripsi === '') $errors['deskripsi'] = 'Deskripsi produk wajib diisi.';

    $fotoNama = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
            $errors['foto'] = 'Format gambar tidak didukung (PNG, JPG, WEBP).';
        } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            $errors['foto'] = 'Ukuran gambar maksimal 5 MB.';
        } else {
            $fotoNama = uniqid('produk_') . '.' . $ext;
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], '../../images/' . $fotoNama)) {
                $errors['foto'] = 'Gagal mengunggah gambar.';
            }
        }
    }

    if (empty($errors)) {
        $stmtKat = $pdo->prepare("SELECT id FROM kategori WHERE nama = ? LIMIT 1");
        $stmtKat->execute([$kategori]);
        $katRow     = $stmtKat->fetch();
        $kategoriId = $katRow ? $katRow['id'] : null;

        $pdo->prepare("
            INSERT INTO produk (toko_id, nama, kategori_id, harga, deskripsi, foto, ketersediaan, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ")->execute([$tokoId, $nama, $kategoriId, (int)$harga, $deskripsi, $fotoNama, $stok]);

        $sukses = true;
    }
}

$stmtKat = $pdo->query("SELECT nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKat->fetchAll();

require_once '../views/produk/tambah-produk.php';
