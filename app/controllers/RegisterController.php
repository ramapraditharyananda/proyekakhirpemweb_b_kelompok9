<?php
session_start();
require_once "../models/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama     = trim($_POST["nama"] ?? '');
    $email    = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $konfirm  = trim($_POST["konfirmasi"] ?? '');
    $role     = trim($_POST["role"] ?? '');

    if (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif ($password !== $konfirm) {
        $error = "Password tidak cocok.";
    } elseif (!in_array($role, ['pengunjung', 'pemilik'])) {
        $error = "Pilih peran terlebih dahulu.";
    } else {
        $cek = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nama, $email, $hash, $role])) {
                header("Location: LoginController.php?success=Registrasi berhasil, silakan login");
                exit();
            } else {
                $error = "Gagal mendaftar, coba lagi.";
            }
        }
    }
}

require_once "../views/auth/register.php";