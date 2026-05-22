<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../auth/login.php");
    exit();
}
require_once '../../../app/models/koneksi.php';

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
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], '../../../images/' . $fotoNama)) {
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
            INSERT INTO produk (toko_id, nama, kategori_id, harga, deskripsi, foto, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
        ")->execute([$tokoId, $nama, $kategoriId, (int)$harga, $deskripsi, $fotoNama]);

        $sukses = true;
    }
}

$stmtKat = $pdo->query("SELECT nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="../dashboard/dashboard-pemilik.php" class="adm-nav-link">Dashboard</a>
        <a href="tambah-produk.php" class="adm-nav-link active">Tambah Produk</a>
      </nav>
      <a href="../auth/login.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <h2>Tambah Produk Baru</h2>
        <p>Isi detail produk untuk diajukan ke admin.</p>
      </header>

      <?php if ($sukses): ?>
      <div style="background:#d4edda;color:#155724;padding:14px 18px;border-radius:10px;margin-bottom:16px;font-size:14px;">
        Produk berhasil diajukan dan sedang menunggu persetujuan admin.
      </div>
      <?php endif; ?>

      <div class="prd-notice">
        ⚠️ Produk yang Anda tambahkan akan ditinjau oleh admin terlebih dahulu sebelum ditampilkan ke pengunjung.
      </div>
      <div class="adm-form-card">
        <form id="formTambahProduk" method="POST" action="tambah-produk.php" enctype="multipart/form-data">
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
  <script src="../../../js/app.js"></script>
</body>
</html>