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
  <title>Persetujuan Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardAdminController.php" class="adm-nav-link">Dashboard</a>
        <a href="PersetujuanProdukController.php" class="adm-nav-link active">Persetujuan Produk</a>
        <a href="DaftarProdukController.php" class="adm-nav-link">Daftar Produk</a>
        <a href="DaftarUmkmController.php" class="adm-nav-link">Daftar UMKM</a>
        <a href="DaftarPenggunaController.php" class="adm-nav-link">Daftar Pengguna</a>
        <a href="PengaturanKategoriController.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Persetujuan Produk</h2>
          <p>Tinjau dan setujui produk dari pemilik UMKM.</p>
        </div>
      </header>

      <div class="adm-toolbar">
        <form method="GET" action="PersetujuanProdukController.php" style="display:contents;">
          <div class="adm-search-wrap">
            <input type="text" class="adm-search" name="search" id="searchPersetujuan"
              placeholder="Cari nama produk atau UMKM..."
              value="<?= htmlspecialchars($search) ?>">
          </div>
          <select class="adm-filter" name="kategori" id="filterKatPersetujuan" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoriList as $kat): ?>
              <option value="<?= htmlspecialchars($kat['nama']) ?>"
                <?= $kategori === $kat['nama'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($kat['nama']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select class="adm-filter" name="status" id="filterStatusPersetujuan" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="tayang"  <?= $status === 'tayang'  ? 'selected' : '' ?>>Disetujui</option>
            <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
          </select>
          <button type="submit" class="adm-btn-acc" style="padding:8px 16px;font-size:13px;">Cari</button>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Pemilik UMKM</th>
              <th>Kategori</th>
              <th>Dikirim</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tabelPersetujuan">
            <?php if (empty($produkList)): ?>
            <tr>
              <td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">
                Tidak ada data produk.
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($produkList as $p): ?>
            <tr
              data-nama="<?= htmlspecialchars($p['nama']) ?>"
              data-umkm="<?= htmlspecialchars($p['nama_toko'] ?? '') ?>"
              data-kategori="<?= htmlspecialchars($p['kategori_nama'] ?? '') ?>"
              data-status="<?= htmlspecialchars(labelStatus($p['status'])) ?>">
              <td>
                <div class="adm-prod-cell">
                  <?php if (!empty($p['foto'])): ?>
                  <img src="../../images/<?= htmlspecialchars($p['foto']) ?>" class="adm-prod-img" alt="<?= htmlspecialchars($p['nama']) ?>">
                  <?php else: ?>
                  <div class="adm-prod-img" style="background:#e4e2f4;display:flex;align-items:center;justify-content:center;font-size:22px;">📦</div>
                  <?php endif; ?>
                  <div>
                    <div class="adm-prod-name"><?= htmlspecialchars($p['nama']) ?></div>
                    <div class="adm-prod-sub">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($p['nama_toko'] ?? '-') ?></td>
              <td><?= htmlspecialchars($p['kategori_nama'] ?? '-') ?></td>
              <td><?= waktuRelatif($p['created_at']) ?></td>
              <td><?= statusPill($p['status']) ?></td>
              <td>
                <?php if ($p['status'] === 'pending'): ?>
                <div class="adm-act-btns">
                  <form method="POST" action="PersetujuanProdukController.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="aksi" value="tayang">
                    <button type="submit" class="adm-btn-acc">Setujui</button>
                  </form>
                  <form method="POST" action="PersetujuanProdukController.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="aksi" value="ditolak">
                    <button type="submit" class="adm-btn-tolak">Tolak</button>
                  </form>
                </div>
                <?php else: ?>
                <span style="color:var(--text-light);font-size:13px;">—</span>
                <?php endif; ?>
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
