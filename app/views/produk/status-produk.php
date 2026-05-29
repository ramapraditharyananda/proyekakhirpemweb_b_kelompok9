<?php
/** @var string $filterStatus */
/** @var string $filterSearch */
/** @var array  $produkList */
/** @var int    $jumlahTayang */
/** @var int    $jumlahMenunggu */
/** @var int    $jumlahDitolak */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Produk - UMKMify</title>
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
        <a href="StatusProdukController.php" class="adm-nav-link active">Status Produk</a>
        <a href="EditProfilPemilikController.php" class="adm-nav-link">Edit Profil</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Status Produk Saya</h2>
          <p>Pantau semua produk yang telah Anda ajukan ke admin.</p>
        </div>
        <a href="TambahProdukController.php" class="adm-btn-tambah" style="text-decoration:none;white-space:nowrap;">+ Tambah Produk</a>
      </header>

      <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'sukses'): ?>
      <div class="notif-sukses">Produk berhasil dihapus.</div>
      <?php endif; ?>

      <div class="status-filter-tabs">
        <a href="StatusProdukController.php" class="status-tab <?= $filterStatus === '' ? 'active' : '' ?>">Semua (<?= count($produkList) ?>)</a>
        <a href="StatusProdukController.php?status=tayang" class="status-tab status-tab-acc <?= $filterStatus === 'tayang' ? 'active' : '' ?>">Tayang (<?= $jumlahTayang ?>)</a>
        <a href="StatusProdukController.php?status=pending" class="status-tab status-tab-pending <?= $filterStatus === 'pending' ? 'active' : '' ?>">Menunggu (<?= $jumlahMenunggu ?>)</a>
        <a href="StatusProdukController.php?status=ditolak" class="status-tab status-tab-tolak <?= $filterStatus === 'ditolak' ? 'active' : '' ?>">Ditolak (<?= $jumlahDitolak ?>)</a>
      </div>

      <div class="adm-toolbar" style="margin-top:0;">
        <form method="GET" action="StatusProdukController.php" style="display:contents;">
          <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
          <div class="adm-search-wrap">
            <input type="text" class="adm-search" name="search"
              placeholder="Cari nama produk..."
              value="<?= htmlspecialchars($filterSearch) ?>">
          </div>
          <button type="submit" class="adm-btn-acc" style="padding:8px 16px;font-size:13px;">Cari</button>
        </form>
      </div>

      <?php if (empty($produkList)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <div class="empty-state-title">Belum ada produk</div>
        <div class="empty-state-desc">
          <?= $filterStatus !== '' ? 'Tidak ada produk dengan status ini.' : 'Anda belum mengajukan produk apapun.' ?>
        </div>
        <a href="TambahProdukController.php" class="adm-btn-tambah" style="text-decoration:none;display:inline-block;margin-top:12px;">+ Tambah Produk</a>
      </div>
      <?php else: ?>
      <div class="produk-status-grid">
        <?php foreach ($produkList as $p):
          $stok      = $p['stok'] ?? 'tersedia';
          $st        = $p['status'];
          $isPending = ($st === 'pending');
          $isTayang  = ($st === 'disetujui' || $st === 'tayang');
          $isDitolak = ($st === 'ditolak');
        ?>
        <div class="ps-card<?= $isDitolak ? ' ps-card-ditolak' : ($isPending ? ' ps-card-pending' : '') ?>">
          <div class="ps-card-img-wrap">
            <?php if (!empty($p['foto'])): ?>
              <img src="../../images/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" class="ps-card-img">
            <?php else: ?>
              <div class="ps-card-img-placeholder">📦</div>
            <?php endif; ?>
            <div class="ps-card-status-badge <?= $isTayang ? 'badge-acc' : ($isPending ? 'badge-pending' : 'badge-tolak') ?>">
              <?= $isTayang ? 'Tayang' : ($isPending ? 'Menunggu' : 'Ditolak') ?>
            </div>
          </div>
          <div class="ps-card-body">
            <div class="ps-card-nama"><?= htmlspecialchars($p['nama']) ?></div>
            <div class="ps-card-kategori"><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></div>
            <div class="ps-card-harga">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
            <?php if ($isDitolak): ?>
            <div class="ps-card-info ps-card-info-tolak">⚠️ Produk ini ditolak admin. Perbarui dan ajukan kembali.</div>
            <?php elseif ($isPending): ?>
            <div class="ps-card-info ps-card-info-pending">⏳ Sedang ditinjau oleh admin.</div>
            <?php endif; ?>
            <div class="ps-card-meta">
              <span class="adm-pill <?= $stok === 'tersedia' ? 'adm-pill-acc' : 'adm-pill-tolak' ?>">
                <?= $stok === 'tersedia' ? 'Tersedia' : 'Habis' ?>
              </span>
              <span class="ps-card-tgl"><?= date('d M Y', strtotime($p['created_at'])) ?></span>
            </div>
            <div class="ps-card-actions">
              <button class="adm-btn-edit" onclick="bukaModalStok(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nama'])) ?>', '<?= $stok ?>')">
                Ubah Stok
              </button>
              <form method="POST" action="StatusProdukController.php" style="display:inline;" onsubmit="return confirm('Yakin hapus produk ini?')">
                <input type="hidden" name="aksi" value="hapus_produk">
                <input type="hidden" name="id_produk" value="<?= $p['id'] ?>">
                <button type="submit" class="adm-btn-hapus">Hapus</button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <footer class="adm-footer" style="margin-top:auto;">
        <p>© 2026 UMKMify. Platform Digital Promosi UMKM Lokal.</p>
      </footer>
    </main>
  </div>

  <div class="modal-overlay" id="modalStok" onclick="tutupModalStok(event)">
    <div class="modal-box" style="max-width:420px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="modal-title" style="margin-bottom:0;" id="modalStokNama">—</div>
        <button onclick="document.getElementById('modalStok').classList.remove('show')"
          style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-light);">✕</button>
      </div>
      <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:600;color:var(--text-mid);margin-bottom:10px;">Status Ketersediaan Stok</div>
        <div class="stok-toggle">
          <label class="toggle-switch">
            <input type="checkbox" id="toggleStokStatus" onchange="handleStokChange(this)">
            <span class="toggle-slider"></span>
          </label>
          <span id="labelStokStatus">Tersedia</span>
        </div>
      </div>
      <form method="POST" action="StatusProdukController.php">
        <input type="hidden" name="aksi" value="ubah_stok">
        <input type="hidden" name="id_produk" id="inputIdProduk" value="">
        <input type="hidden" name="stok" id="inputStokVal" value="tersedia">
        <div style="display:flex;gap:10px;">
          <button type="button" onclick="document.getElementById('modalStok').classList.remove('show')" class="prd-btn-cancel" style="flex:1;">Batal</button>
          <button type="submit" class="prd-btn-submit" style="flex:1;">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../../js/app.js"></script>
</body>
</html>
