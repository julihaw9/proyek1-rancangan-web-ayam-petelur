<?php
// Set zona waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

session_start();
include("koneksi.php");
include("tanggal.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. LOGIKA FILTER PERIODE
$periode = $_GET['periode'] ?? 'bulanan'; // Default ke bulanan
$filter_periode_prod = "";
$where_transaksi = "";
$label_periode = "";

switch ($periode) {
    case 'harian':
        $filter_periode_prod = "pt.tanggal = CURDATE()";
        $where_transaksi = "WHERE t.tanggal_transaksi = CURDATE()";
        $label_periode = "Hari Ini";
        break;
    case 'mingguan':
        $filter_periode_prod = "pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $where_transaksi = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label_periode = "7 Hari Terakhir";
        break;
    case 'bulanan':
        $filter_periode_prod = "pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $where_transaksi = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $label_periode = "30 Hari Terakhir";
        break;
    default:
        $filter_periode_prod = "";
        $where_transaksi = "";
        $label_periode = "Semua Waktu";
        break;
}

// Klausa WHERE untuk Query Summary
$where_produksi = $filter_periode_prod ? "WHERE " . $filter_periode_prod : "";

// 2. QUERY SUMMARY
$query_total_prod = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(total_telur) as total 
    FROM `produksi_telur` pt
    $where_produksi
"));

$query_total_jual = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(jumlah_telur) as total_terjual 
    FROM `telur_terjual` tt
    JOIN transaksi t ON tt.id_transaksi = t.id_transaksi
    $where_transaksi
"));

$query_total_jual_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(jumlah_telur) as total_terjual 
    FROM `telur_terjual` tt
    JOIN transaksi t ON tt.id_transaksi = t.id_transaksi
    WHERE t.tanggal_transaksi = CURDATE()
"));


// 3. LOGIKA PENCARIAN BARU (Menghubungkan ke tabel pemasukan_telur via Subquery)
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$search_cond = "";

if ($keyword !== '') {
    $search_cond = "(
        p.nama_petugas LIKE '%$keyword%' 
        OR pt.total_telur LIKE '%$keyword%'
        OR pt.id_produksi IN (
            SELECT id_produksi 
            FROM pemasukan_telur 
            WHERE keterangan LIKE '%$keyword%'
               OR jumlah_telur LIKE '%$keyword%'
        )
    )";
}

// Menggabungkan Filter Periode & Pencarian khusus untuk Tabel Riwayat
$conditions = [];
if ($filter_periode_prod) {
    $conditions[] = $filter_periode_prod;
}
if ($search_cond) {
    $conditions[] = $search_cond;
}

$where_riwayat = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";


// 4. QUERY RIWAYAT
$query_riwayat = mysqli_query($conn, "
    SELECT 
        pt.id_produksi,
        pt.tanggal,
        pt.total_telur,
        p.nama_petugas,
        (SELECT SUM(jumlah_telur) 
         FROM telur_terjual tt 
         JOIN transaksi t ON tt.id_transaksi = t.id_transaksi 
         WHERE t.tanggal_transaksi = pt.tanggal) as total_terjual
    FROM produksi_telur pt
    LEFT JOIN petugas p ON pt.id_petugas = p.id_petugas
    $where_riwayat
    ORDER BY pt.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produksi Telur - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* === Penyesuaian Integrasi Filter ke baris Card === */
        .card-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            align-items: stretch; /* Memastikan filter setinggi card produksi */
            flex-wrap: wrap;
        }

        .card, .filter-card-custom {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            min-width: 270px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .filter-card-custom {
            flex: 1; /* Membuat kotak filter mengisi sisa ruang yang tersedia */
            min-width: 320px; 
        }

        /* Desain Kustom Radio Button agar Selaras dengan Tema Oranye */
        .radio-tile-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: auto; /* Memaksa pilihan nempel ke bawah card */
        }

        .radio-wrapper {
            position: relative;
        }

        .radio-wrapper input[type="radio"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
            margin: 0;
        }

        .radio-label {
            display: inline-block;
            padding: 10px 16px;
            background: #f3f4f6;
            color: #4b5563;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            border: 1.5px solid transparent;
            transition: all 0.2s ease;
        }

        .radio-wrapper input[type="radio"]:checked + .radio-label {
            background-color: #f0861c;
            color: white;
            box-shadow: 0 4px 6px rgba(240, 134, 28, 0.2);
        }

        .radio-wrapper input[type="radio"]:hover:not(:checked) + .radio-label {
            background-color: #e5e7eb;
        }

        /* === Gaya Pencarian Riwayat === */
        .search-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 20px;
            width: 100%;
        }

        .search-form {
            display: flex;
            gap: 5px;
            width: 100%;
            max-width: 950px;
        }

        .input-cari {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .btn-cari {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            margin-left: 10px;
            margin-right: 10px;
            text-decoration: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .btn-cari:hover {
            background-color: #0056b3;
        }

        .btn-reset {
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php $active = 'produksi'; include("sidebar.php"); ?>
        <main>
            <h1>Produksi Telur</h1>
            <p>Laporan produksi telur: <strong><?= $label_periode ?></strong></p>

            <div class="btn-group">
                <a href="produksi.php" class="btn-tambah">+ Tambah Data</a>
                <a href="update_produksi.php" class="btn-tambah">Update Data</a>
            </div>

            <!-- CONTAINER UTAMA: CARD INFO DAN CARD FILTER DISATUKAN -->
            <div class="card-container">
                <div class="card">
                    <p style="margin: 0; font-size: 16px; color: #666;">Total Produksi<br><b>(<?= $label_periode ?>)</b></p>
                    <h2 style="margin: 15px 0 5px 0;"><?= number_format($query_total_prod['total'] ?? 0, 1); ?></h2>
                    <span style="font-weight: bold; color: #888;">Kg</span>
                </div>
                
                <div class="card">
                    <p style="margin: 0; font-size: 16px; color: #666;">Total Terjual<br><b>(<?= $label_periode ?>)</b></p>
                    <h2 style="margin: 15px 0 5px 0;" class="hijau"><?= number_format($query_total_jual['total_terjual'] ?? 0, 1); ?></h2>
                    <span style="font-weight: bold; color: #888;">Kg</span>
                </div>

                <!-- FILTER SEKARANG MENJADI BAGIAN DARI CARD -->
                <div class="filter-card-custom">
                    <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #111;">Pilih Periode Laporan:</p>
                    
                    <form method="GET" action="">
                        <div class="radio-tile-group">
                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="semua" id="semua" onchange="this.form.submit()" <?= $periode == 'semua' ? 'checked' : '' ?>>
                                <label for="semua" class="radio-label">Semua Waktu</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="harian" id="harian" onchange="this.form.submit()" <?= $periode == 'harian' ? 'checked' : '' ?>>
                                <label for="harian" class="radio-label">Hari Ini</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="mingguan" id="mingguan" onchange="this.form.submit()" <?= $periode == 'mingguan' ? 'checked' : '' ?>>
                                <label for="mingguan" class="radio-label">7 Hari</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="bulanan" id="bulanan" onchange="this.form.submit()" <?= $periode == 'bulanan' ? 'checked' : '' ?>>
                                <label for="bulanan" class="radio-label">30 Hari</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BAGIAN TABEL RIWAYAT -->
            <div class="table-box">
                <h3>Riwayat Produksi - <?= $label_periode ?></h3>
                <div class="search-wrapper">
                    <form action="" method="GET" class="search-form">
                        <input type="hidden" name="periode" value="<?= htmlspecialchars($periode) ?>">
                        <input type="text" name="keyword" class="input-cari" placeholder="Cari petugas, jumlah, atau keterangan jual..." value="<?= htmlspecialchars($keyword) ?>">
                        <button type="submit" class="btn-cari">Cari</button>
                        <?php if ($keyword !== ''): ?>
                            <a href="?periode=<?= $periode ?>" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Total Produksi (Kg)</th>
                            <th>Telur Terjual (Kg)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_riwayat)) { ?>
                                <tr>
                                    <td><?= tanggal_indo($row['tanggal']); ?></td>
                                    <td><?= $row['nama_petugas'] ?? '-'; ?></td>
                                    <td><strong><?= number_format($row['total_telur'], 1); ?></strong></td>
                                    <td class="hijau"><?= number_format($row['total_terjual'] ?? 0, 1); ?></td>
                                    <td>
                                        <a href="hapus_produksi.php?id=<?= $row['id_produksi']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Data tidak ditemukan untuk periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>