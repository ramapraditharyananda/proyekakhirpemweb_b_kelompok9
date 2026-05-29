<?php
/** @var string $search */
/** @var string $status */
/** @var string $kategori */
/** @var array  $tokoList */
/** @var array  $kategoriList */
/** @var array  $ikonKategori */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar UMKM - UMKMify</title>
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
        <a href="DaftarProdukController.php" class="adm-nav-link">Daftar Produk</a>
        <a href="DaftarUmkmController.php" class="adm-nav-link active">Daftar UMKM</a>
        <a href="DaftarPenggunaController.php" class="adm-nav-link">Daftar Pengguna</a>
        <a href="PengaturanKategoriController.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Daftar UMKM</h2>
          <p>Kelola seluruh UMKM yang terdaftar di platform.</p>
        </div>
      </header>

      <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'sukses'): ?>
      <div style="background:#d4edda;color:#155724;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:14px;">
        UMKM berhasil dihapus.
      </div>
      <?php endif; ?>

      <div class="adm-toolbar">
        <form method="GET" action="DaftarUmkmController.php" style="display:contents;">
          <div class="adm-search-wrap">
            <input type="text" class="adm-search" name="search"
              placeholder="Cari nama toko atau pemilik..."
              value="<?= htmlspecialchars($search) ?>">
          </div>
          <select class="adm-filter" name="kategori" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoriList as $kat): ?>
            <option value="<?= htmlspecialchars($kat['kategori']) ?>"
              <?= $kategori === $kat['kategori'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($kat['kategori']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <select class="adm-filter" name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="aktif"    <?= $status === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif" <?= $status === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
          </select>
          <button type="submit" class="adm-btn-acc" style="padding:8px 16px;font-size:13px;">Cari</button>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" id="tabelUMKM">
          <thead>
            <tr>
              <th>Nama Toko</th>
              <th>Pemilik</th>
              <th>Kategori</th>
              <th>Produk Tayang</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tokoList)): ?>
            <tr>
              <td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">
                Tidak ada UMKM ditemukan.
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($tokoList as $t): ?>
            <?php $ikon = $ikonKategori[$t['kategori']] ?? '🏪'; ?>
            <tr
              data-nama="<?= htmlspecialchars($t['nama_toko']) ?>"
              data-pemilik="<?= htmlspecialchars($t['nama_pemilik'] ?? '') ?>"
              data-kategori="<?= htmlspecialchars($t['kategori'] ?? '') ?>"
              data-status="<?= htmlspecialchars(ucfirst($t['status'])) ?>"
              data-alamat="<?= htmlspecialchars($t['alamat'] ?? '') ?>"
              data-wa="<?= htmlspecialchars($t['no_wa'] ?? '') ?>"
              data-produk="<?= (int)$t['jumlah_produk'] ?>">
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <span style="font-size:24px;"><?= $ikon ?></span>
                  <div>
                    <div class="adm-prod-name"><?= htmlspecialchars($t['nama_toko']) ?></div>
                    <div class="adm-prod-sub"><?= htmlspecialchars($t['alamat'] ?? '-') ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($t['nama_pemilik'] ?? '-') ?></td>
              <td><?= htmlspecialchars($t['kategori'] ?? '-') ?></td>
              <td>
                <span class="adm-pill <?= $t['jumlah_produk'] > 0 ? 'adm-pill-acc' : 'adm-pill-pending' ?>">
                  <?= (int)$t['jumlah_produk'] ?> produk
                </span>
              </td>
              <td><?= statusPill($t['status']) ?></td>
              <td>
                <div class="adm-act-btns">
                  <button class="adm-btn-detail" onclick="lihatDetailUMKM(this)">Detail</button>
                  <a href="DaftarUmkmController.php?hapus=<?= $t['id'] ?>" class="adm-btn-hapus"
                     onclick="return confirm('Yakin hapus UMKM ini?')">Hapus</a>
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

  <div class="modal-overlay" id="modalDetailUMKM" onclick="if(event.target===this)tutupModalUMKM()">
    <div class="modal-box">
      <div class="modal-title">
        Detail UMKM
        <button class="modal-close" onclick="tutupModalUMKM()">✕</button>
      </div>
      <div class="modal-grid">
        <div class="modal-field"><div class="modal-label">Nama Toko</div><div class="modal-value" id="detailNamaToko">—</div></div>
        <div class="modal-field"><div class="modal-label">Pemilik</div><div class="modal-value" id="detailPemilik">—</div></div>
        <div class="modal-field"><div class="modal-label">Kategori</div><div class="modal-value" id="detailKategori">—</div></div>
        <div class="modal-field"><div class="modal-label">Alamat</div><div class="modal-value" id="detailAlamat">—</div></div>
        <div class="modal-field"><div class="modal-label">No. WhatsApp</div><div class="modal-value" id="detailWa">—</div></div>
        <div class="modal-field"><div class="modal-label">Status</div><div class="modal-value" id="detailStatus">—</div></div>
        <div class="modal-field"><div class="modal-label">Produk Tayang</div><div class="modal-value" id="detailProduk">—</div></div>
      </div>
      <div style="margin-top:20px;display:flex;justify-content:flex-end;">
        <button class="adm-btn-acc" onclick="tutupModalUMKM()">Tutup</button>
      </div>
    </div>
  </div>

  <script src="../../js/app.js"></script>
</body>
</html>
