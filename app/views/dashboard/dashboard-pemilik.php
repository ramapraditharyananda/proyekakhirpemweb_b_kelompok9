<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: ../auth/login.php");
    exit();
}
require_once '../../../app/models/koneksi.php';

$id_user = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'ubah_stok') {
    $id_produk = (int)($_POST['id_produk'] ?? 0);
    $stok_baru = ($_POST['stok'] === 'tersedia') ? 'tersedia' : 'habis';
    $stmt = $pdo->prepare("UPDATE produk SET stok = ? WHERE id = ? AND toko_id IN (SELECT id FROM toko WHERE user_id = ?)");
    $stmt->execute([$stok_baru, $id_produk, $id_user]);
    header("Location: dashboard-pemilik.php");
    exit;
}

$stmtToko = $pdo->prepare("SELECT * FROM toko WHERE user_id = ? LIMIT 1");
$stmtToko->execute([$id_user]);
$toko = $stmtToko->fetch();
$toko_id = $toko['id'] ?? 0;

$stmtProduk = $pdo->prepare("
    SELECT p.*, k.nama AS nama_kategori, k.ikon AS ikon_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    WHERE p.toko_id = ?
    ORDER BY p.created_at DESC
");
$stmtProduk->execute([$toko_id]);
$produkList = $stmtProduk->fetchAll();

$jumlahTayang   = 0;
$jumlahMenunggu = 0;
$jumlahDitolak  = 0;
foreach ($produkList as $p) {
    if ($p['status'] === 'disetujui') $jumlahTayang++;
    if ($p['status'] === 'pending')   $jumlahMenunggu++;
    if ($p['status'] === 'ditolak')   $jumlahDitolak++;
}

$produkJson = json_encode(array_values($produkList), JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pemilik - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../../css/style.css">
</head>
<body class="body-admin">
  <div class="adm-layout">
    <aside class="adm-sidebar">
      <div class="adm-logo-text">UMKM<span>ify</span></div>
      <nav class="adm-menu">
        <a href="dashboard-pemilik.php" class="adm-nav-link active">Dashboard</a>
        <a href="../produk/tambah-produk.php" class="adm-nav-link">Tambah Produk</a>
      </nav>
      <a href="../auth/login.php" class="adm-logout">Keluar Sesi</a>
    </aside>
    <main class="adm-main" style="display:flex;flex-direction:column;">
      <header class="adm-header">
        <h2>Dashboard Pemilik UMKM</h2>
        <p>Kelola toko dan produk Anda di platform UMKMify.</p>
      </header>
      <div class="adm-stats">
        <div class="adm-stat adm-stat-purple">
          <div class="adm-stat-label">Produk Tayang</div>
          <div class="adm-stat-val adm-stat-gold"><?= $jumlahTayang ?></div>
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
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="font-size:16px;font-weight:800;color:var(--text-dark);">Produk Saya</h3>
        <a href="../produk/tambah-produk.php" class="adm-btn-tambah" style="text-decoration:none;padding:10px 18px;border-radius:12px;font-size:13px;">+ Tambah Produk</a>
      </div>
      <div class="adm-grid" id="gridProduk"></div>
      <footer class="adm-footer" style="margin-top:auto;">
        <p>© 2026 UMKMify. Platform Digital UMKM Lokal Lampung.</p>
      </footer>
    </main>
  </div>

  <div class="modal-overlay" id="modalDetail" onclick="tutupModalDetail(event)">
    <div class="modal-box" style="max-width:500px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="modal-title" style="margin-bottom:0;" id="modalNama">—</div>
        <button onclick="document.getElementById('modalDetail').classList.remove('show')"
          style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-light);">✕</button>
      </div>
      <div style="width:100%;height:180px;border-radius:14px;overflow:hidden;margin-bottom:18px;background:#f0eff8;">
        <img id="modalGambar" src="" alt="" style="width:100%;height:100%;object-fit:cover;">
      </div>
      <div style="margin-bottom:18px;">
        <div class="detail-row">
          <span class="lbl">Kategori</span>
          <span class="val" id="modalKategori">—</span>
        </div>
        <div class="detail-row">
          <span class="lbl">Harga</span>
          <span class="val" id="modalHarga">—</span>
        </div>
        <div class="detail-row">
          <span class="lbl">Status Tayang</span>
          <span class="val" id="modalStatusTayang">—</span>
        </div>
        <div class="detail-row" style="align-items:flex-start;">
          <span class="lbl">Deskripsi</span>
          <span class="val" id="modalDeskripsi" style="max-width:60%;line-height:1.5;font-weight:500;">—</span>
        </div>
      </div>
      <div style="margin-bottom:20px;">
        <div style="font-size:12px;font-weight:700;color:var(--text-mid);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Status Ketersediaan</div>
        <div class="stok-toggle">
          <label class="toggle-switch">
            <input type="checkbox" id="toggleStok" onchange="ubahStatusStok(this)">
            <span class="toggle-slider"></span>
          </label>
          <span id="labelStok">Tersedia</span>
        </div>
      </div>
      <div style="display:flex;gap:10px;">
        <button onclick="document.getElementById('modalDetail').classList.remove('show')" class="prd-btn-cancel" style="flex:1;">Tutup</button>
        <button onclick="simpanStatusStok()" class="prd-btn-submit" style="flex:1;">Simpan Perubahan</button>
      </div>
    </div>
  </div>

  <script>
    var dataProduk = <?= $produkJson ?>;
  </script>
  <script src="../../../js/app.js"></script>
</body>
</html>
