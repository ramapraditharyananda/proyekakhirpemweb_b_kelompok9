<?php
/** @var string $sukses */
/** @var array  $errors */
/** @var array  $dataUser */
/** @var array  $toko */
/** @var array  $kategoriList */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profil - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardPemilikController.php" class="adm-nav-link">Dashboard</a>
        <a href="TambahProdukController.php" class="adm-nav-link">Tambah Produk</a>
        <a href="StatusProdukController.php" class="adm-nav-link">Status Produk</a>
        <a href="EditProfilPemilikController.php" class="adm-nav-link active">Edit Profil</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Edit Profil</h2>
          <p>Perbarui informasi akun dan data toko Anda.</p>
        </div>
      </header>

      <?php if ($sukses === 'profil'): ?>
      <div class="notif-sukses">Profil akun berhasil diperbarui.</div>
      <?php elseif ($sukses === 'toko'): ?>
      <div class="notif-sukses">Informasi toko berhasil diperbarui.</div>
      <?php endif; ?>

      <div class="profil-grid">

        <div class="adm-form-card">
          <div class="adm-form-title" style="margin-top:20px;">Informasi Akun</div>
          <form method="POST" action="EditProfilPemilikController.php">
            <input type="hidden" name="aksi" value="simpan_profil">
            <div class="prd-form-grid">
              <div class="prd-form-full">
                <label class="prd-label" for="nama">Nama Lengkap <span style="color:#e74c3c;">*</span></label>
                <input type="text" id="nama" name="nama" class="prd-input"
                  value="<?= htmlspecialchars($_POST['nama'] ?? $dataUser['nama'] ?? '') ?>"
                  placeholder="Nama lengkap Anda">
                <?php if (!empty($errors['nama'])): ?>
                <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['nama']) ?></div>
                <?php endif; ?>
              </div>
              <div class="prd-form-full">
                <label class="prd-label" for="email">Email <span style="color:#e74c3c;">*</span></label>
                <input type="email" id="email" name="email" class="prd-input"
                  value="<?= htmlspecialchars($_POST['email'] ?? $dataUser['email'] ?? '') ?>"
                  placeholder="email@contoh.com">
                <?php if (!empty($errors['email'])): ?>
                <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
              </div>
              <div>
                <label class="prd-label" for="password_baru">Password Baru <span style="color:var(--text-light);font-weight:500;">(opsional)</span></label>
                <input type="password" id="password_baru" name="password_baru" class="prd-input"
                  placeholder="Kosongkan jika tidak ingin ubah">
                <?php if (!empty($errors['password_baru'])): ?>
                <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['password_baru']) ?></div>
                <?php endif; ?>
              </div>
              <div>
                <label class="prd-label" for="konfirmasi_pass">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_pass" name="konfirmasi_pass" class="prd-input"
                  placeholder="Ulangi password baru">
                <?php if (!empty($errors['konfirmasi_pass'])): ?>
                <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['konfirmasi_pass']) ?></div>
                <?php endif; ?>
              </div>
            </div>
            <div class="prd-form-actions">
              <button type="submit" class="prd-btn-submit">Simpan Profil</button>
            </div>
          </form>
        </div>

        <div class="adm-form-card">
          <div class="adm-form-title">Informasi Toko</div>
          <p style="font-size:13px;color:var(--text-light);margin-bottom:16px;">
            Info toko ini akan ditampilkan kepada pengunjung di halaman produk Anda.
          </p>
          <form method="POST" action="EditProfilPemilikController.php">
            <input type="hidden" name="aksi" value="simpan_toko">
            <div class="prd-form-grid">
              <div class="prd-form-full">
                <label class="prd-label" for="nama_toko">Nama Toko <span style="color:#e74c3c;">*</span></label>
                <input type="text" id="nama_toko" name="nama_toko" class="prd-input"
                  value="<?= htmlspecialchars($_POST['nama_toko'] ?? $toko['nama_toko'] ?? '') ?>"
                  placeholder="Contoh: Toko Dodol Bu Sari">
                <?php if (!empty($errors['nama_toko'])): ?>
                <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['nama_toko']) ?></div>
                <?php endif; ?>
              </div>
              <div>
                <label class="prd-label" for="kategori_toko">Kategori Utama</label>
                <select id="kategori_toko" name="kategori" class="prd-select">
                  <option value="">Pilih Kategori</option>
                  <?php foreach ($kategoriList as $kat): ?>
                  <option value="<?= htmlspecialchars($kat['nama']) ?>"
                    <?= (($_POST['kategori'] ?? $toko['kategori'] ?? '') === $kat['nama']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat['nama']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="prd-label" for="no_wa">Nomor WhatsApp</label>
                <input type="text" id="no_wa" name="no_wa" class="prd-input"
                  value="<?= htmlspecialchars($_POST['no_wa'] ?? $toko['no_wa'] ?? '') ?>"
                  placeholder="Contoh: 08123456789">
              </div>
              <div class="prd-form-full">
                <label class="prd-label" for="alamat">Alamat Toko</label>
                <input type="text" id="alamat" name="alamat" class="prd-input"
                  value="<?= htmlspecialchars($_POST['alamat'] ?? $toko['alamat'] ?? '') ?>"
                  placeholder="Contoh: Jl. Melati No. 5, Bandar Lampung">
              </div>
              <div class="prd-form-full">
                <label class="prd-label" for="deskripsi_toko">Deskripsi Toko</label>
                <textarea id="deskripsi_toko" name="deskripsi_toko" class="prd-textarea"
                  placeholder="Ceritakan sedikit tentang toko Anda..."><?= htmlspecialchars($_POST['deskripsi_toko'] ?? $toko['deskripsi'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="prd-form-actions">
              <button type="submit" class="prd-btn-submit">Simpan Info Toko</button>
            </div>
          </form>
        </div>

      </div>

      <footer class="adm-footer" style="margin-top:auto;">
        <p>© 2026 UMKMify. Platform Digital Promosi UMKM Lokal.</p>
      </footer>
    </main>
  </div>
  <script src="../../js/app.js"></script>
</body>
</html>