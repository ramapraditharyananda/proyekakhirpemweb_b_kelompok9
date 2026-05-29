<?php
session_start();

$timeout = 300;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: LoginController.php");
    exit();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pengunjung') {
    header("Location: LoginController.php");
    exit();
}

require_once '../models/koneksi.php';

$userId    = $_SESSION['user_id'] ?? 0;
$namaUser  = $_SESSION['nama'] ?? 'Pengunjung';
$emailUser = $_SESSION['email'] ?? '';
$inisial   = strtoupper(substr($namaUser, 0, 1));

$pesan = '';
$pesanTipe = '';

function buatFotoUrl($foto)
{
    $foto = trim($foto ?? '');

    if ($foto !== '') {
        return '/umkmify/images/' . $foto;
    }

    return '/umkmify/images/UMKMify_Logo_Color.png';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit_profil') {
    $namaBaru  = trim($_POST['nama'] ?? '');
    $emailBaru = trim($_POST['email'] ?? '');
    $pwLama    = $_POST['pw_lama'] ?? '';
    $pwBaru    = $_POST['pw_baru'] ?? '';

    if ($namaBaru === '' || $emailBaru === '') {
        $pesan = 'Nama dan email tidak boleh kosong.';
        $pesanTipe = 'error';
    } else {
        $stmtCek = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmtCek->execute([$userId]);
        $userRow = $stmtCek->fetch();

        if (!$userRow) {
            $pesan = 'Pengguna tidak ditemukan.';
            $pesanTipe = 'error';
        } else {
            if ($pwBaru !== '') {
                if (!password_verify($pwLama, $userRow['password'])) {
                    $pesan = 'Password lama tidak sesuai.';
                    $pesanTipe = 'error';
                } else {
                    $hashBaru = password_hash($pwBaru, PASSWORD_DEFAULT);

                    $pdo->prepare("UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?")
                        ->execute([$namaBaru, $emailBaru, $hashBaru, $userId]);

                    $_SESSION['nama']  = $namaBaru;
                    $_SESSION['email'] = $emailBaru;

                    $namaUser  = $namaBaru;
                    $emailUser = $emailBaru;
                    $inisial   = strtoupper(substr($namaUser, 0, 1));

                    $pesan = 'Profil dan password berhasil diperbarui.';
                    $pesanTipe = 'sukses';
                }
            } else {
                $pdo->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?")
                    ->execute([$namaBaru, $emailBaru, $userId]);

                $_SESSION['nama']  = $namaBaru;
                $_SESSION['email'] = $emailBaru;

                $namaUser  = $namaBaru;
                $emailUser = $emailBaru;
                $inisial   = strtoupper(substr($namaUser, 0, 1));

                $pesan = 'Profil berhasil diperbarui.';
                $pesanTipe = 'sukses';
            }
        }
    }
}

$stmtTayang = $pdo->query("SELECT COUNT(*) AS total FROM produk WHERE status = 'tayang'");
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
    WHERE p.status = 'tayang'
    ORDER BY p.created_at DESC
");

$produkList = $stmtProduk->fetchAll();

foreach ($produkList as &$produk) {
    $produk['foto_url'] = buatFotoUrl($produk['foto'] ?? '');
}
unset($produk);

$stmtToko = $pdo->query("
    SELECT t.id, t.nama_toko, t.kategori, t.alamat, t.deskripsi, t.status, t.no_wa,
           u.nama AS pemilik,
           COUNT(p.id) AS jumlah_produk
    FROM toko t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN produk p ON p.toko_id = t.id AND p.status = 'tayang'
    GROUP BY t.id
    ORDER BY t.created_at DESC
");

$tokoList = $stmtToko->fetchAll();

$stmtKatList = $pdo->query("SELECT id, nama FROM kategori ORDER BY nama ASC");
$kategoriList = $stmtKatList->fetchAll();

$stmtProdukBaru = $pdo->query("
    SELECT p.id, p.nama, p.harga, p.foto,
           k.nama AS kategori,
           t.nama_toko
    FROM produk p
    LEFT JOIN kategori k ON p.kategori_id = k.id
    LEFT JOIN toko t ON p.toko_id = t.id
    WHERE p.status = 'tayang'
    ORDER BY p.created_at DESC
    LIMIT 2
");

$produkBaru = $stmtProdukBaru->fetchAll();

foreach ($produkBaru as &$produk) {
    $produk['foto_url'] = buatFotoUrl($produk['foto'] ?? '');
}
unset($produk);

$produkJson     = json_encode(array_values($produkList), JSON_UNESCAPED_UNICODE);
$tokoJson       = json_encode(array_values($tokoList), JSON_UNESCAPED_UNICODE);
$kategoriJson   = json_encode(array_values($kategoriList), JSON_UNESCAPED_UNICODE);
$produkBaruJson = json_encode(array_values($produkBaru), JSON_UNESCAPED_UNICODE);

require_once '../views/dashboard/dashboard_pengunjung.php';