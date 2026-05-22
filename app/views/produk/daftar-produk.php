<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
require_once '../../../app/models/koneksi.php';

$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$status   = isset($_GET['status'])   ? trim($_GET['status'])   : '';

$sql = "
    SELECT p.id, p.nama, p.harga, p.foto, p.status, p.deskripsi,
           t.nama_toko, k.nama AS kategori_nama
    FROM produk p
    LEFT JOIN toko t ON p.toko_id = t.id
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND (p.nama LIKE ? OR t.nama_toko LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($kategori !== '') {
    $sql .= " AND k.nama = ?";
    $params[] = $kategori;
}
if ($status !== '') {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produkList = $stmt->fetchAll();

$stmtKat = $pdo->query("SELECT nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKat->fetchAll();

function statusPill($status) {
    if ($status === 'disetujui') return '<span class="adm-pill adm-pill-acc">Tayang</span>';
    if ($status === 'pending')   return '<span class="adm-pill adm-pill-pending">Menunggu ACC</span>';
    if ($status === 'ditolak')   return '<span class="adm-pill adm-pill-tolak">Ditolak</span>';
    return '<span class="adm-pill">' . htmlspecialchars($status) . '</span>';
}

function statusLabel($status) {
    if ($status === 'disetujui') return 'Tayang';
    if ($status === 'pending')   return 'Menunggu ACC';
    if ($status === 'ditolak')   return 'Ditolak';
    return $status;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="../dashboard/dashboard-admin.php" class="adm-nav-link">Dashboard</a>
        <a href="persetujuan-produk.php" class="adm-nav-link">Persetujuan Produk</a>
        <a href="daftar-produk.php" class="adm-nav-link active">Daftar Produk</a>
        <a href="../umkm/daftar-umkm.php" class="adm-nav-link">Daftar UMKM</a>
        <a href="../pengguna/daftar-pengguna.php" class="adm-nav-link">Daftar Pengguna</a>
        <a href="../pengguna/pengaturan-kategori.php" class="adm-nav-link">Pengaturan Kategori</a>
      </nav>
      <a href="../auth/login.php" class="adm-logout">Keluar Sesi</a>
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
        <form method="GET" action="daftar-produk.php" style="display:contents;">
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
                  <img src="../../../images/<?= htmlspecialchars($p['foto']) ?>" class="adm-prod-img" alt="<?= htmlspecialchars($p['nama']) ?>">
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
                  <a href="hapus-produk.php?id=<?= $p['id'] ?>" class="adm-btn-hapus"
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
  <script src="../../../js/app.js"></script>
</body>
</html>
