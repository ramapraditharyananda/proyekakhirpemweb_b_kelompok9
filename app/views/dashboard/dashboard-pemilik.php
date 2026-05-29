<?php
/** @var string $sapaan */
/** @var string $namaUser */
/** @var array|null $toko */
/** @var int   $jumlahTotal */
/** @var int   $jumlahTayang */
/** @var int   $jumlahMenunggu */
/** @var int   $jumlahDitolak */
/** @var array $produkTerbaru */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pemilik - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="DashboardPemilikController.php" class="adm-nav-link active">Dashboard</a>
        <a href="TambahProdukController.php" class="adm-nav-link">Tambah Produk</a>
        <a href="StatusProdukController.php" class="adm-nav-link">Status Produk</a>
        <a href="EditProfilPemilikController.php" class="adm-nav-link">Edit Profil</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2><?= htmlspecialchars($sapaan) ?>, <?= htmlspecialchars($namaUser) ?></h2>
          <p>Selamat datang di dashboard pengelolaan toko Anda di UMKMify.</p>
        </div>
        <a href="EditProfilPemilikController.php" class="owner-avatar-btn" title="Edit Profil">
          <div class="owner-avatar"><?= strtoupper(mb_substr($namaUser, 0, 1)) ?></div>
        </a>
      </header>

      <?php if ($toko): ?>
      <div class="toko-info-banner">
        <div class="toko-info-left">
          <div class="toko-info-ikon">🏪</div>
          <div>
            <div class="toko-info-nama"><?= htmlspecialchars($toko['nama_toko'] ?? '-') ?></div>
            <div class="toko-info-sub">
              <?= !empty($toko['alamat']) ? htmlspecialchars($toko['alamat']) : 'Alamat belum diisi' ?>
              <?php if (!empty($toko['no_wa'])): ?>
                &nbsp;·&nbsp; WA: <?= htmlspecialchars($toko['no_wa']) ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <a href="EditProfilPemilikController.php" class="toko-info-edit">Edit Toko</a>
      </div>
      <?php endif; ?>

      <div class="adm-stats">
        <div class="adm-stat adm-stat-purple">
          <div class="adm-stat-label">Total Produk</div>
          <div class="adm-stat-val adm-stat-gold"><?= $jumlahTotal ?></div>
        </div>
        <div class="adm-stat">
          <div class="adm-stat-label">Produk Tayang</div>
          <div class="adm-stat-val" style="color:#27ae60;"><?= $jumlahTayang ?></div>
        </div>
        <div class="adm-stat">
          <div class="adm-stat-label">Menunggu Persetujuan</div>
          <div class="adm-stat-val" style="color:#e67e22;"><?= $jumlahMenunggu ?></div>
        </div>
        <div class="adm-stat">
          <div class="adm-stat-label">Produk Ditolak</div>
          <div class="adm-stat-val" style="color:#c0392b;"><?= $jumlahDitolak ?></div>
        </div>
      </div>

      <div class="dash-section-header">
        <h3 class="dash-section-title">Produk Terbaru</h3>
        <a href="StatusProdukController.php" class="dash-section-link">Lihat Semua →</a>
      </div>

      <?php if (empty($produkTerbaru)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">📦</div>
        <div class="empty-state-title">Belum ada produk</div>
        <div class="empty-state-desc">Mulai promosikan produk UMKM Anda sekarang!</div>
        <a href="TambahProdukController.php" class="adm-btn-tambah" style="text-decoration:none;display:inline-block;margin-top:12px;">+ Tambah Produk Pertama</a>
      </div>
      <?php else: ?>
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Ketersediaan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produkTerbaru as $p): ?>
            <tr>
              <td>
                <div class="adm-prod-cell">
                  <?php if (!empty($p['foto'])): ?>
                  <img src="../../images/<?= htmlspecialchars($p['foto']) ?>" class="adm-prod-img" alt="<?= htmlspecialchars($p['nama']) ?>">
                  <?php else: ?>
                  <div class="adm-prod-img" style="background:#e4e2f4;display:flex;align-items:center;justify-content:center;font-size:22px;">📦</div>
                  <?php endif; ?>
                  <div>
                    <div class="adm-prod-name"><?= htmlspecialchars($p['nama']) ?></div>
                    <div class="adm-prod-sub"><?= htmlspecialchars(mb_strimwidth($p['deskripsi'] ?? '', 0, 36, '...')) ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></td>
              <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
              <td>
                <?php $stok = $p['stok'] ?? 'tersedia'; ?>
                <span class="adm-pill <?= $stok === 'tersedia' ? 'adm-pill-acc' : 'adm-pill-tolak' ?>">
                  <?= $stok === 'tersedia' ? 'Tersedia' : 'Habis' ?>
                </span>
              </td>
              <td>
                <?php
                $st = $p['status'];
                if ($st === 'disetujui' || $st === 'tayang') echo '<span class="adm-pill adm-pill-acc">Tayang</span>';
                elseif ($st === 'pending') echo '<span class="adm-pill adm-pill-pending">Menunggu</span>';
                elseif ($st === 'ditolak') echo '<span class="adm-pill adm-pill-tolak">Ditolak</span>';
                else echo '<span class="adm-pill">' . htmlspecialchars($st) . '</span>';
                ?>
              </td>
              <td>
                <div class="adm-act-btns">
                  <button class="adm-btn-edit" onclick="bukaModalStok(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nama'])) ?>', '<?= $stok ?>')">Stok</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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
            <input type="checkbox" id="toggleStokDash" onchange="labelStokDash(this)">
            <span class="toggle-slider"></span>
          </label>
          <span id="labelStokDash">Tersedia</span>
        </div>
      </div>
      <form method="POST" action="DashboardPemilikController.php">
        <input type="hidden" name="aksi" value="ubah_stok">
        <input type="hidden" name="id_produk" id="inputIdProdukStok" value="">
        <input type="hidden" name="stok" id="inputNilaiStok" value="tersedia">
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
