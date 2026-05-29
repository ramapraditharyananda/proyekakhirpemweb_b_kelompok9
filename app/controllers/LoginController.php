<?php
session_start();
require_once "../models/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["nama"]    = $user["nama"];
        $_SESSION["email"]   = $user["email"];
        $_SESSION["role"]    = $user["role"];

        if ($user["role"] === "pemilik") {
            $stmtToko = $pdo->prepare("SELECT id FROM toko WHERE user_id = ? LIMIT 1");
            $stmtToko->execute([$user["id"]]);
            $toko = $stmtToko->fetch();
            if ($toko) {
                $_SESSION["toko_id"] = $toko["id"];
            }
        }

        if ($user["role"] === "admin") {
            header("Location: DashboardAdminController.php");
        } elseif ($user["role"] === "pemilik") {
            header("Location: DashboardPemilikController.php");
        } else {
            header("Location: DashboardPengunjungController.php");
        }
        exit();
    } else {
        $error = "Email atau password salah.";
    }
}

require_once "../views/auth/login.php";