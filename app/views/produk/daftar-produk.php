<?php
/** @var string $search */
/** @var string $status */
/** @var string $kategori */
/** @var array  $produkList */
/** @var array  $kategoriList */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardAdminController.php" class="adm-nav-link">Dashboard</a>
        <a href="PersetujuanProdukController.php" class="adm-nav-link">Persetujuan Produk</a>
        <a href="DaftarProdukController.php" class="adm-nav-link active">Daftar Produk</a>
        <a href="DaftarUmkmController.php" class="adm-nav-link">Daftar UMKM</a>
        <a href="DaftarPenggunaController.php" class="adm-nav-link">Daftar Pengguna</a>
        <a href="PengaturanKategoriController.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Daftar Produk</h2>
          <p>Semua produk yang telah tayang di platform UMKMify.</p>
        </div>
      </header>

      <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'sukses'): ?>
      <div style="background:#d4edda;color:#155724;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:14px;">
        Produk berhasil dihapus.
      </div>
      <?php endif; ?>

      <div class="adm-toolbar">
        <form method="GET" action="DaftarProdukController.php" style="display:contents;">
          <div class="adm-search-wrap">
            <input type="text" class="adm-search" name="search" id="searchProduk"
              placeholder="Cari nama produk atau UMKM..."
              value="<?= htmlspecialchars($search) ?>">
          </div>
          <select class="adm-filter" name="kategori" id="filterKategori" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoriList as $kat): ?>
              <option value="<?= htmlspecialchars($kat['nama']) ?>"
                <?= $kategori === $kat['nama'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($kat['nama']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select class="adm-filter" name="status" id="filterStatus" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="disetujui" <?= $status === 'disetujui' ? 'selected' : '' ?>>Tayang</option>
            <option value="pending"   <?= $status === 'pending'   ? 'selected' : '' ?>>Menunggu ACC</option>
            <option value="ditolak"   <?= $status === 'ditolak'   ? 'selected' : '' ?>>Ditolak</option>
          </select>
          <button type="submit" class="adm-btn-acc" style="padding:8px 16px;font-size:13px;">Cari</button>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" id="tabelProduk">
          <thead>
            <tr>
              <th>Produk</th>
              <th>UMKM</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($produkList)): ?>
            <tr>
              <td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">
                Tidak ada produk ditemukan.
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($produkList as $p): ?>
            <tr
              data-nama="<?= htmlspecialchars($p['nama']) ?>"
              data-umkm="<?= htmlspecialchars($p['nama_toko'] ?? '') ?>"
              data-kategori="<?= htmlspecialchars($p['kategori_nama'] ?? '') ?>"
              data-status="<?= htmlspecialchars(statusLabel($p['status'])) ?>">
              <td>
                <div class="adm-prod-cell">
                  <?php if (!empty($p['foto'])): ?>
                  <img src="../../images/<?= htmlspecialchars($p['foto']) ?>" class="adm-prod-img" alt="<?= htmlspecialchars($p['nama']) ?>">
                  <?php else: ?>
                  <div class="adm-prod-img" style="background:#e4e2f4;display:flex;align-items:center;justify-content:center;font-size:22px;">📦</div>
                  <?php endif; ?>
                  <div>
                    <div class="adm-prod-name"><?= htmlspecialchars($p['nama']) ?></div>
                    <div class="adm-prod-sub"><?= htmlspecialchars(mb_strimwidth($p['deskripsi'] ?? '', 0, 40, '...')) ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($p['nama_toko'] ?? '-') ?></td>
              <td><?= htmlspecialchars($p['kategori_nama'] ?? '-') ?></td>
              <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
              <td><?= statusPill($p['status']) ?></td>
              <td>
                <div class="adm-act-btns">
                  <a href="HapusProdukController.php?id=<?= $p['id'] ?>" class="adm-btn-hapus"
                     onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <footer class="adm-footer">
        <p>© 2026 UMKMify. Platform Digital UMKM Lokal Lampung.</p>
      </footer>
    </main>
  </div>
  <script src="../../js/app.js"></script>
</body>
</html>
