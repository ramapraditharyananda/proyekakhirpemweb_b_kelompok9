<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="auth-page">
  <div class="page-wrapper">
    <div class="card">
      <div class="card-header">
        <div class="logo-area">
          <span class="logo-text">UMKM<span>ify</span></span>
        </div>
        <p class="site-tagline">Platform Digital UMKM dan Produk Lokal Lampung</p>
      </div>
      <div class="card-body">
        <h2 class="form-title">Selamat Datang</h2>
        <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
        <p id="notifMsg" style="display:none;color:red;font-size:13px;font-weight:600;margin-bottom:10px;"></p>
        <?php if (!empty($error)): ?>
          <p style="color:red;font-size:13px;font-weight:600;margin-bottom:10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form id="loginForm" action="LoginController.php" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password">
          </div>
          <div class="remember-row">
            <label class="remember-label">
              <input type="checkbox"> Ingat saya
            </label>
          </div>
          <button type="button" class="btn-primary" onclick="doLogin()">LOG IN</button>
        </form>
        <div class="divider"><span>atau</span></div>
        <p class="switch-link">Belum punya akun? <a href="RegisterController.php">Daftar sekarang</a></p>
      </div>
    </div>
    <p class="footer-text">© 2026 UMKMify. All rights reserved.</p>
  </div>
  <script src="../../js/app.js?v=3"></script>
</body>
</html>
