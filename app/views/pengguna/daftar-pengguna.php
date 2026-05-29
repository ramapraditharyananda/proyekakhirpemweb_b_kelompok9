<?php
/** @var string   $search */
/** @var string   $role */
/** @var array    $penggunaList */
/** @var callable $rolePill */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pengguna - UMKMify</title>
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
        <a href="DaftarUMKMController.php" class="adm-nav-link">Daftar UMKM</a>
        <a href="DaftarPenggunaController.php" class="adm-nav-link active">Daftar Pengguna</a>
        <a href="PengaturanKategoriController.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="LogoutController.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main">
      <header class="adm-header">
        <div class="adm-header-text">
          <h2>Daftar Pengguna</h2>
          <p>Kelola seluruh pengguna yang terdaftar di platform.</p>
        </div>
      </header>

      <?php if (isset($_GET['hapus']) && $_GET['hapus'] === 'sukses'): ?>
      <div style="background:#d4edda;color:#155724;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:14px;">
        Pengguna berhasil dihapus.
      </div>
      <?php endif; ?>

      <div class="adm-toolbar">
        <form method="GET" action="DaftarPenggunaController.php" style="display:contents;">
          <div class="adm-search-wrap">
            <input type="text" class="adm-search" name="search" id="searchPengguna"
              placeholder="Cari nama atau email..."
              value="<?= htmlspecialchars($search) ?>">
          </div>
          <select class="adm-filter" name="role" id="filterRole" onchange="this.form.submit()">
            <option value="">Semua Peran</option>
            <option value="admin"      <?= $role === 'admin'      ? 'selected' : '' ?>>Admin</option>
            <option value="pemilik"    <?= $role === 'pemilik'    ? 'selected' : '' ?>>Pemilik UMKM</option>
            <option value="pengunjung" <?= $role === 'pengunjung' ? 'selected' : '' ?>>Pengunjung</option>
          </select>
          <button type="submit" class="adm-btn-acc" style="padding:8px 16px;font-size:13px;">Cari</button>
        </form>
      </div>

      <div class="adm-table-wrap">
        <table class="adm-table" id="tabelPengguna">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Peran</th>
              <th>Terdaftar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($penggunaList)): ?>
            <tr>
              <td colspan="5" style="text-align:center;color:var(--text-light);padding:32px;">
                Tidak ada pengguna ditemukan.
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($penggunaList as $u): ?>
            <tr data-nama="<?= htmlspecialchars($u['nama']) ?>"
                data-email="<?= htmlspecialchars($u['email']) ?>"
                data-role="<?= htmlspecialchars($u['role']) ?>">
              <td><strong><?= htmlspecialchars($u['nama']) ?></strong></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= rolePill($u['role']) ?></td>
              <td style="font-size:13px;color:var(--text-light);"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <div class="adm-act-btns">
                  <?php if ($u['role'] !== 'admin'): ?>
                  <a href="DaftarPenggunaController.php?hapus=<?= $u['id'] ?>" class="adm-btn-hapus"
                     onclick="return confirm('Yakin hapus pengguna ini?')">Hapus</a>
                  <?php else: ?>
                  <span style="color:var(--text-light);font-size:13px;">—</span>
                  <?php endif; ?>
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
