<?php
/** @var int   $totalPengguna */
/** @var int   $totalUMKM */
/** @var int   $totalTayang */
/** @var int   $totalMenunggu */
/** @var array $produkTerbaru */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css?v=4">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardAdminController.php" class="adm-nav-link active">Dashboard</a>
        <a href="PersetujuanProdukController.php" class="adm-nav-link">Persetujuan Produk</a>
        <a href="DaftarProdukController.php" class="adm-nav-link">Daftar Produk</a>
        <a href="DaftarUMKMController.php" class="adm-nav-link">Daftar UMKM</a>
        <a href="DaftarPenggunaController.php" class="adm-nav-link">Daftar Pengguna</a>
        <a href="PengaturanKategoriController.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Dashboard Admin</h2>
          <p>Selamat datang kembali, Admin. Berikut ringkasan platform hari ini.</p>
        </div>
      </header>
      <div class="adm-stats">
        <div class="adm-stat adm-stat-purple adm-stat-click" onclick="location.href='DaftarPenggunaController.php'">
          <div class="adm-stat-label">Total Pengguna</div>
          <div class="adm-stat-val adm-stat-gold"><?= $totalPengguna ?></div>
        </div>
        <div class="adm-stat adm-stat-click" onclick="location.href='DaftarUMKMController.php'">
          <div class="adm-stat-label">Total UMKM</div>
          <div class="adm-stat-val adm-stat-gold"><?= $totalUMKM ?></div>
        </div>
        <div class="adm-stat adm-stat-click" onclick="location.href='DaftarProdukController.php'">
          <div class="adm-stat-label">Total Produk Tayang</div>
          <div class="adm-stat-val"><?= $totalTayang ?></div>
        </div>
        <div class="adm-stat adm-stat-click" onclick="location.href='PersetujuanProdukController.php'">
          <div class="adm-stat-label">Menunggu Persetujuan</div>
          <div class="adm-stat-val" style="color:#e67e22;"><?= $totalMenunggu ?></div>
        </div>
      </div>
      <div class="adm-section-head">Produk Terbaru</div>
      <div class="adm-grid">
        <?php if (empty($produkTerbaru)): ?>
          <p style="color:var(--text-light);font-size:14px;">Belum ada produk terbaru.</p>
        <?php else: ?>
          <?php foreach ($produkTerbaru as $p): ?>
            <div class="adm-card">
              <?php if (!empty($p['foto'])): ?>
              <img src="../../images/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>">
              <?php else: ?>
              <div style="width:100%;height:180px;background:#e4e2f4;display:flex;align-items:center;justify-content:center;font-size:40px;">📦</div>
              <?php endif; ?>
              <div class="adm-overlay">
                <div class="adm-cat-label"><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></div>
                <div class="adm-card-bottom">
                  <div>
                    <h3><?= htmlspecialchars($p['nama']) ?></h3>
                    <div class="adm-prod-sub" style="color:rgba(255,255,255,0.6);font-size:11px;"><?= htmlspecialchars($p['nama_toko'] ?? '-') ?></div>
                  </div>
                  <div class="adm-card-actions">
                    <button class="adm-btn" onclick="location.href='PersetujuanProdukController.php'">Tinjau <?php if ($p['status'] === 'pending'): ?><span class="adm-pending-count">!</span><?php endif; ?></button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <footer class="adm-footer">
        <p>© 2026 UMKMify. Platform Digital UMKM Lokal Lampung.</p>
      </footer>
    </main>
  </div>
  <script src="../../../js/app.js?v=4"></script>
</body>
</html>
