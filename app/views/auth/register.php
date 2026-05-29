<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - UMKMify</title>
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
        <h2 class="form-title">Buat Akun Baru</h2>
        <p class="form-subtitle">Daftarkan diri Anda untuk mulai menggunakan layanan</p>
        <p id="notifMsg" style="display:none;color:red;font-size:13px;font-weight:600;margin-bottom:10px;"></p>
        <?php if (!empty($error)): ?>
          <p style="color:red;font-size:13px;font-weight:600;margin-bottom:10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form id="registerForm" action="RegisterController.php" method="POST">
          <input type="hidden" name="role" id="hiddenRole" value="">
          <div class="role-select-area">
            <label>Daftar sebagai</label>
            <div class="role-options">
              <div class="role-option" id="role-pengunjung" onclick="pilihRole('pengunjung')">
                <span class="role-icon">🛍️</span>
                <span class="role-label">Pengunjung</span>
              </div>
              <div class="role-option" id="role-pemilik" onclick="pilihRole('pemilik')">
                <span class="role-icon">🏪</span>
                <span class="role-label">Pemilik UMKM</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            <span class="field-error-msg" id="errNama"></span>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <span class="field-error-msg" id="errEmail"></span>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Buat password">
            <span class="field-error-msg" id="errPassword"></span>
          </div>
          <div class="form-group">
            <label for="konfirmasi">Konfirmasi Password</label>
            <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password">
            <span class="field-error-msg" id="errKonfirmasi"></span>
          </div>
          <div class="terms-row">
            <label class="remember-label">
              <input type="checkbox" id="terms">
              Saya setuju dengan <a href="#" class="forgot-link">Syarat &amp; Ketentuan</a>
            </label>
          </div>
          <button type="button" class="btn-primary" onclick="doRegister()">DAFTAR SEKARANG</button>
        </form>
        <div class="divider"><span>atau</span></div>
        <p class="switch-link">Sudah punya akun? <a href="LoginController.php">Masuk di sini</a></p>
      </div>
    </div>
    <p class="footer-text">© 2026 UMKMify. All rights reserved.</p>
  </div>
  <script src="../../js/app.js?v=4"></script>
</body>
</html>
