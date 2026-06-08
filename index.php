<?php
session_start();

if (isset($_SESSION["role"])) {
    $role = $_SESSION["role"];
    if ($role === "admin") {
        header("Location: app/controllers/DashboardAdminController.php");
    } elseif ($role === "pemilik") {
        header("Location: app/controllers/DashboardPemilikController.php");
    } else {
        header("Location: app/controllers/DashboardPengunjungController.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMKMify - Platform Digital UMKM Lampung</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="index-hero">
    <div class="index-content">
      <div class="index-logo">
        UMKM<span>ify</span>
      </div>
      <p class="index-tagline">Platform Digital UMKM dan Produk Lokal Lampung.<br>Masuk atau daftar untuk melanjutkan.</p>
      <div class="index-cards">
        <a href="app/controllers/LoginController.php" class="index-card">
          <span class="index-card-icon">🔐</span>
          <span class="index-card-title">Masuk</span>
          <span class="index-card-sub">Login ke akun Anda</span>
        </a>
        <a href="app/controllers/RegisterController.php" class="index-card">
          <span class="index-card-icon">📝</span>
          <span class="index-card-title">Daftar</span>
          <span class="index-card-sub">Buat akun baru</span>
        </a>
      </div>
      <?php if (isset($_GET["error"])): ?>
      <p style="color:red;margin-top:12px;"><?= htmlspecialchars($_GET["error"]) ?></p>
      <?php endif; ?>
    </div>
  </div>
  <script src="js/app.js"></script>
</body>
</html>