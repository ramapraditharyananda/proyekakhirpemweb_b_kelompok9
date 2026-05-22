<?php
session_start();
require_once '../../../app/models/koneksi.php';

$namaUser = $_SESSION['nama'] ?? 'Pengunjung';
$inisial   = strtoupper(substr($namaUser, 0, 1));

$stmtTayang = $pdo->query("SELECT COUNT(*) AS total FROM produk WHERE status = 'disetujui'");
$totalTayang = $stmtTayang->fetch()['total'];

$stmtUMKM = $pdo->query("SELECT COUNT(*) AS total FROM toko");
$totalUMKM = $stmtUMKM->fetch()['total'];

$stmtKategori = $pdo->query("SELECT COUNT(*) AS total FROM kategori");
$totalKategori = $stmtKategori->fetch()['total'];

$stmtProduk = $pdo->query("
    SELECT p.id, p.nama, p.harga, p.deskripsi, p.foto,
           k.nama AS kategori,
           t.nama_toko, t.no_wa, t.alamat AS lokasi
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    LEFT JOIN toko t ON p.toko_id = t.id
    WHERE p.status = 'disetujui'
    ORDER BY p.created_at DESC
");
$produkList = $stmtProduk->fetchAll();

$stmtToko = $pdo->query("
    SELECT t.id, t.nama_toko, t.kategori, t.alamat, t.deskripsi, t.status,
           u.nama AS pemilik,
           COUNT(p.id) AS jumlah_produk
    FROM toko t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN produk p ON p.toko_id = t.id AND p.status = 'disetujui'
    GROUP BY t.id
    ORDER BY t.created_at DESC
");
$tokoList = $stmtToko->fetchAll();

$stmtKatList = $pdo->query("SELECT * FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKatList->fetchAll();

$produkJson   = json_encode(array_values($produkList), JSON_UNESCAPED_UNICODE);
$tokoJson     = json_encode(array_values($tokoList), JSON_UNESCAPED_UNICODE);
$kategoriJson = json_encode(array_values($kategoriList), JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jelajah Produk - UMKMify</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../../css/style.css">
</head>
<body>

  <nav class="nav">
    <span class="nav-logo-text">UMKM<span>ify</span></span>
    <div class="nav-links">
      <a href="#produk" class="active">Produk</a>
      <a href="#umkm">UMKM</a>
    </div>
    <div class="nav-right">
      <button class="nav-wishlist-btn" onclick="toggleWishlist()" title="Wishlist saya">
        ❤️
        <span class="nav-wishlist-count" id="wishlistCount">0</span>
      </button>
      <div class="nav-user">
        <div class="nav-avatar"><?= htmlspecialchars($inisial) ?></div>
        <span><?= htmlspecialchars($namaUser) ?></span>
      </div>
    </div>
  </nav>

  <div class="hero">
    <h1>Produk Lokal <span>Lampung</span></h1>
    <p>Temukan ribuan produk UMKM unggulan dari seluruh penjuru Lampung.</p>
    <div class="hero-search">
      <input type="text" id="searchInput" placeholder="Cari produk atau nama toko..." oninput="filterProduk()">
      <button onclick="filterProduk()">🔍 Cari</button>
    </div>
    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-val"><?= $totalTayang ?></div>
        <div class="hero-stat-label">Produk Tayang</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val"><?= $totalUMKM ?></div>
        <div class="hero-stat-label">UMKM Terdaftar</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-val"><?= $totalKategori ?></div>
        <div class="hero-stat-label">Kategori</div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="filter-bar" id="produk">
      <div class="filter-chips">
        <button class="filter-chip active" onclick="setFilter(this, '')">Semua</button>
        <?php foreach ($kategoriList as $kat): ?>
          <button class="filter-chip" onclick="setFilter(this, '<?= htmlspecialchars($kat['nama']) ?>')"><?= htmlspecialchars($kat['ikon'] ?? '') ?> <?= htmlspecialchars($kat['nama']) ?></button>
        <?php endforeach; ?>
      </div>
      <select class="sort-select" onchange="sortProduk(this.value)">
        <option value="">Urutkan</option>
        <option value="harga-asc">Harga Terendah</option>
        <option value="harga-desc">Harga Tertinggi</option>
        <option value="nama-asc">Nama A-Z</option>
      </select>
    </div>

    <div class="produk-grid" id="produkGrid">
      <div class="no-results" id="noResults">
        <div class="no-results-icon">🔍</div>
        <h3>Produk tidak ditemukan</h3>
        <p>Coba kata kunci lain atau ubah filter kategori.</p>
      </div>
    </div>

    <h2 class="section-title" id="umkm">UMKM Terdaftar</h2>
    <div class="umkm-grid" id="umkmGrid"></div>
  </div>

  <footer class="user-footer">
    <p>© 2026 <strong>UMKMify</strong>. Platform Digital UMKM Lokal Lampung.</p>
  </footer>

  <div class="modal-overlay" id="modalDetail" onclick="closeModalOutside(event, 'modalDetail')">
    <div class="modal-detail" id="modalDetailBox">
      <img class="modal-detail-img" id="mdImg" src="" alt="">
      <div class="modal-detail-body">
        <div class="modal-detail-top">
          <div>
            <div class="modal-detail-cat" id="mdKat"></div>
            <div class="modal-detail-nama" id="mdNama"></div>
            <div class="modal-detail-toko" id="mdToko"></div>
          </div>
          <button class="modal-close" onclick="closeModal('modalDetail')">✕</button>
        </div>
        <div class="modal-detail-price" id="mdHarga"></div>
        <div class="modal-detail-desc" id="mdDesc"></div>
        <div class="modal-detail-info">
          <div class="modal-info-item">
            <div class="modal-info-label">Kategori</div>
            <div class="modal-info-val" id="mdKatInfo"></div>
          </div>
          <div class="modal-info-item">
            <div class="modal-info-label">Nama Toko</div>
            <div class="modal-info-val" id="mdTokoInfo"></div>
          </div>
          <div class="modal-info-item">
            <div class="modal-info-label">Lokasi</div>
            <div class="modal-info-val" id="mdLokasi"></div>
          </div>
          <div class="modal-info-item">
            <div class="modal-info-label">Ketersediaan</div>
            <div class="modal-info-val" style="color:#1a7a35;">✓ Tersedia</div>
          </div>
        </div>
        <hr class="modal-detail-divider">
        <div class="modal-actions">
          <a class="btn-wa" id="mdWaLink" href="#" target="_blank">
            <span>💬</span> Hubungi via WhatsApp
          </a>
          <button class="btn-wishlist-modal" id="mdWishlistBtn" onclick="toggleWishlistFromModal()">
            <span id="mdWishlistIcon">🤍</span> Simpan
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modalReport" onclick="closeModalOutside(event, 'modalReport')">
    <div class="modal-report">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div class="modal-report-title">Laporkan UMKM</div>
          <div class="modal-report-sub" id="reportUmkmName"></div>
        </div>
        <button class="modal-close" onclick="closeModal('modalReport')">✕</button>
      </div>
      <p style="font-size:13px;color:var(--text-light);margin-bottom:16px;">Pilih alasan pelaporan:</p>
      <div class="report-options" id="reportOptions">
        <label class="report-option" onclick="selectReport(this)">
          <input type="radio" name="reportReason" value="penipuan">
          <div>
            <div class="report-option-label">⚠️ Dugaan Penipuan</div>
            <div style="font-size:11px;color:var(--text-light);margin-top:2px;">Produk tidak sesuai atau pembayaran bermasalah</div>
          </div>
        </label>
        <label class="report-option" onclick="selectReport(this)">
          <input type="radio" name="reportReason" value="konten">
          <div>
            <div class="report-option-label">🚫 Konten Tidak Sesuai</div>
            <div style="font-size:11px;color:var(--text-light);margin-top:2px;">Foto atau deskripsi produk menyesatkan</div>
          </div>
        </label>
        <label class="report-option" onclick="selectReport(this)">
          <input type="radio" name="reportReason" value="spam">
          <div>
            <div class="report-option-label">📢 Spam / Promosi Berlebihan</div>
            <div style="font-size:11px;color:var(--text-light);margin-top:2px;">Mengirim pesan promosi yang mengganggu</div>
          </div>
        </label>
        <label class="report-option" onclick="selectReport(this)">
          <input type="radio" name="reportReason" value="lain">
          <div>
            <div class="report-option-label">📝 Alasan Lain</div>
            <div style="font-size:11px;color:var(--text-light);margin-top:2px;">Ceritakan masalah yang Anda alami</div>
          </div>
        </label>
      </div>
      <textarea class="report-textarea" id="reportKeterangan" placeholder="Tambahkan keterangan (opsional)..."></textarea>
      <div class="report-actions">
        <button class="btn-report-cancel" onclick="closeModal('modalReport')">Batal</button>
        <button class="btn-report-submit" onclick="submitReport()">Kirim Laporan</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modalUmkm" onclick="closeModalOutside(event, 'modalUmkm')">
    <div class="modal-umkm">
      <div class="modal-umkm-header">
        <div class="modal-umkm-icon" id="muIcon">🏪</div>
        <div>
          <div class="modal-umkm-name" id="muNama"></div>
          <div class="modal-umkm-cat" id="muKat"></div>
        </div>
        <button class="modal-close" onclick="closeModal('modalUmkm')" style="margin-left:auto;">✕</button>
      </div>
      <div class="modal-umkm-info-grid">
        <div class="modal-umkm-info-item">
          <div class="modal-info-label">Pemilik</div>
          <div class="modal-info-val" id="muPemilik"></div>
        </div>
        <div class="modal-umkm-info-item">
          <div class="modal-info-label">Lokasi</div>
          <div class="modal-info-val" id="muLokasi"></div>
        </div>
        <div class="modal-umkm-info-item">
          <div class="modal-info-label">Jumlah Produk</div>
          <div class="modal-info-val" id="muJumlah"></div>
        </div>
        <div class="modal-umkm-info-item">
          <div class="modal-info-label">Status</div>
          <div class="modal-info-val" id="muStatus"></div>
        </div>
      </div>
      <div class="modal-umkm-produk-section">
        <div class="modal-umkm-produk-title">Produk dari toko ini</div>
        <div class="umkm-produk-list" id="muProdukList"></div>
      </div>
      <div class="modal-umkm-actions">
        <button class="btn-contact-umkm" onclick="alert('Menghubungi via WhatsApp...')">💬 Hubungi Toko</button>
        <button class="btn-laporkan-umkm" onclick="openReportFromUmkm()">🚩 Laporkan</button>
      </div>
    </div>
  </div>

  <div class="wishlist-panel" id="wishlistPanel">
    <div class="wishlist-header">
      <div class="wishlist-title">❤️ Wishlist Saya</div>
      <button class="wishlist-close" onclick="toggleWishlist()">✕</button>
    </div>
    <div class="wishlist-body" id="wishlistBody">
      <div class="wishlist-empty" id="wishlistEmpty">
        <div class="wishlist-empty-icon">🤍</div>
        <p>Belum ada produk yang disimpan.<br>Klik ❤️ pada produk favorit Anda!</p>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    var produkData   = <?= $produkJson ?>;
    var tokoData     = <?= $tokoJson ?>;
    var kategoriData = <?= $kategoriJson ?>;
  </script>
  <script src="../../../js/app.js"></script>
</body>
</html>
