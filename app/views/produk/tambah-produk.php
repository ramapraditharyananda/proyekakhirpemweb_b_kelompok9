<?php
/** @var bool   $sukses */
/** @var array  $errors */
/** @var array  $kategoriList */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardPemilikController.php" class="adm-nav-link">Dashboard</a>
        <a href="TambahProdukController.php" class="adm-nav-link active">Tambah Produk</a>
        <a href="StatusProdukController.php" class="adm-nav-link">Status Produk</a>
        <a href="EditProfilPemilikController.php" class="adm-nav-link">Edit Profil</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Tambah Produk Baru</h2>
          <p>Isi detail produk untuk diajukan ke admin.</p>
        </div>
      </header>

      <?php if ($sukses): ?>
      <div class="notif-sukses">
        Produk berhasil diajukan dan sedang menunggu persetujuan admin.
      </div>
      <?php endif; ?>

      <div class="prd-notice">
        ⚠️ Produk yang Anda tambahkan akan ditinjau oleh admin terlebih dahulu sebelum ditampilkan ke pengunjung.
      </div>

      <div class="adm-form-card">
        <form id="formTambahProduk" method="POST" action="TambahProdukController.php" enctype="multipart/form-data">
          <div class="prd-form-grid">
            <div class="prd-form-full">
              <label class="prd-label">Foto Produk</label>
              <div class="prd-upload-area" onclick="document.getElementById('fotoInput').click()">
                <div class="prd-upload-icon">📷</div>
                <div class="prd-upload-text"><strong>Klik untuk unggah</strong> atau seret file ke sini</div>
                <div class="prd-upload-text" style="margin-top:4px;">PNG, JPG, WEBP — maks. 5 MB</div>
              </div>
              <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none;" onchange="previewGambar(this)">
              <?php if (!empty($errors['foto'])): ?>
              <div class="err-msg" style="display:block;"><?= htmlspecialchars($errors['foto']) ?></div>
              <?php endif; ?>
              <div id="previewWrap" style="display:none;margin-top:12px;">
                <img id="previewImg" style="max-height:160px;border-radius:12px;border:2px solid #e4e2f4;" alt="Preview">
              </div>
            </div>

            <div>
              <label class="prd-label" for="namaProduk">Nama Produk <span style="color:#e74c3c;">*</span></label>
              <input type="text" id="namaProduk" name="namaProduk" class="prd-input"
                placeholder="Contoh: Dodol Durian Khas Lampung"
                value="<?= htmlspecialchars($_POST['namaProduk'] ?? '') ?>">
              <div class="err-msg" id="errNama" <?= !empty($errors['nama']) ? 'style="display:block;"' : '' ?>>
                <?= htmlspecialchars($errors['nama'] ?? 'Nama produk wajib diisi.') ?>
              </div>
            </div>

            <div>
              <label class="prd-label" for="kategori">Kategori <span style="color:#e74c3c;">*</span></label>
              <select id="kategori" name="kategori" class="prd-select">
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= htmlspecialchars($kat['nama']) ?>"
                  <?= (($_POST['kategori'] ?? '') === $kat['nama']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($kat['nama']) ?>
                </option>
                <?php endforeach; ?>
              </select>
              <div class="err-msg" id="errKategori" <?= !empty($errors['kategori']) ? 'style="display:block;"' : '' ?>>
                <?= htmlspecialchars($errors['kategori'] ?? 'Pilih kategori produk.') ?>
              </div>
            </div>

            <div>
              <label class="prd-label" for="harga">Harga (Rp) <span style="color:#e74c3c;">*</span></label>
              <input type="number" id="harga" name="harga" class="prd-input"
                placeholder="Contoh: 35000" min="0"
                value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
              <div class="err-msg" id="errHarga" <?= !empty($errors['harga']) ? 'style="display:block;"' : '' ?>>
                <?= htmlspecialchars($errors['harga'] ?? 'Harga wajib diisi dan tidak boleh negatif.') ?>
              </div>
            </div>

            <div>
              <label class="prd-label" for="stok">Status Ketersediaan</label>
              <select id="stok" name="stok" class="prd-select">
                <option value="tersedia" <?= (($_POST['stok'] ?? 'tersedia') === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                <option value="habis"    <?= (($_POST['stok'] ?? '') === 'habis') ? 'selected' : '' ?>>Stok Habis</option>
              </select>
            </div>

            <div class="prd-form-full">
              <label class="prd-label" for="deskripsi">Deskripsi Produk <span style="color:#e74c3c;">*</span></label>
              <textarea id="deskripsi" name="deskripsi" class="prd-textarea"
                placeholder="Jelaskan produk Anda secara singkat dan menarik..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
              <div class="err-msg" id="errDeskripsi" <?= !empty($errors['deskripsi']) ? 'style="display:block;"' : '' ?>>
                <?= htmlspecialchars($errors['deskripsi'] ?? 'Deskripsi produk wajib diisi.') ?>
              </div>
            </div>
          </div>

          <div class="prd-form-actions">
            <button type="button" class="prd-btn-cancel" onclick="history.back()">Batal</button>
            <button type="submit" class="prd-btn-submit">Ajukan Produk</button>
          </div>
        </form>
      </div>

      <footer class="adm-footer" style="margin-top:auto;">
        <p>© 2026 UMKMify. Platform Digital UMKM Lokal Lampung.</p>
      </footer>
    </main>
  </div>
  <script src="../../js/app.js"></script>
</body>
</html>
