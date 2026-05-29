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
$errors  = [];
$sukses  = '';

$stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmtUser->execute([$id_user]);
$dataUser = $stmtUser->fetch();

$stmtToko = $pdo->prepare("SELECT * FROM toko WHERE user_id = ? LIMIT 1");
$stmtToko->execute([$id_user]);
$toko = $stmtToko->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'simpan_profil') {
        $nama            = trim($_POST['nama'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password_baru   = trim($_POST['password_baru'] ?? '');
        $konfirmasi_pass = trim($_POST['konfirmasi_pass'] ?? '');

        if ($nama === '')  $errors['nama']  = 'Nama tidak boleh kosong.';
        if ($email === '') $errors['email'] = 'Email tidak boleh kosong.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Format email tidak valid.';

        if ($email !== $dataUser['email']) {
            $cekEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $cekEmail->execute([$email, $id_user]);
            if ($cekEmail->fetch()) $errors['email'] = 'Email sudah digunakan akun lain.';
        }

        if ($password_baru !== '') {
            if (strlen($password_baru) < 6) {
                $errors['password_baru'] = 'Password minimal 6 karakter.';
            } elseif ($password_baru !== $konfirmasi_pass) {
                $errors['konfirmasi_pass'] = 'Konfirmasi password tidak cocok.';
            }
        }

        if (empty($errors)) {
            if ($password_baru !== '') {
                $hash = password_hash($password_baru, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?")
                    ->execute([$nama, $email, $hash, $id_user]);
            } else {
                $pdo->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?")
                    ->execute([$nama, $email, $id_user]);
            }
            $_SESSION['nama'] = $nama;
            $sukses = 'profil';
            $stmtUser->execute([$id_user]);
            $dataUser = $stmtUser->fetch();
        }
    }

    if ($aksi === 'simpan_toko') {
        $nama_toko = trim($_POST['nama_toko'] ?? '');
        $kategori  = trim($_POST['kategori'] ?? '');
        $alamat    = trim($_POST['alamat'] ?? '');
        $no_wa     = trim($_POST['no_wa'] ?? '');
        $deskripsi = trim($_POST['deskripsi_toko'] ?? '');

        if ($nama_toko === '') $errors['nama_toko'] = 'Nama toko tidak boleh kosong.';

        if (empty($errors)) {
            if ($toko) {
                $pdo->prepare("UPDATE toko SET nama_toko = ?, kategori = ?, alamat = ?, no_wa = ?, deskripsi = ? WHERE user_id = ?")
                    ->execute([$nama_toko, $kategori, $alamat, $no_wa, $deskripsi, $id_user]);
            } else {
                $pdo->prepare("INSERT INTO toko (user_id, nama_toko, kategori, alamat, no_wa, deskripsi, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'aktif', NOW())")
                    ->execute([$id_user, $nama_toko, $kategori, $alamat, $no_wa, $deskripsi]);
            }
            $sukses = 'toko';
            $stmtToko->execute([$id_user]);
            $toko = $stmtToko->fetch();
        }
    }
}

$stmtKat      = $pdo->query("SELECT nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKat->fetchAll();

require_once '../views/profil/edit-profil-pemilik.php';
