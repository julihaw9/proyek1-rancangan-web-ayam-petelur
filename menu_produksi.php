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
$where_produksi = "";
$where_transaksi = "";
$label_periode = "";

switch ($periode) {
    case 'harian':
        $where_produksi = "WHERE pt.tanggal = CURDATE()";
        $where_transaksi = "WHERE t.tanggal_transaksi = CURDATE()";
        $label_periode = "Hari Ini";
        break;
    case 'mingguan':
        $where_produksi = "WHERE pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $where_transaksi = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label_periode = "7 Hari Terakhir";
        break;
    case 'bulanan':
        $where_produksi = "WHERE pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $where_transaksi = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        $label_periode = "30 Hari Terakhir";
        break;
    default:
        $where_produksi = ""; // Tampilkan semua jika tidak ada filter
        $where_transaksi = "";
        $label_periode = "Semua Waktu";
        break;
}

// 2. QUERY SUMMARY (Disesuaikan Filter)
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

// Tetap tampilkan total jual hari ini sebagai pembanding statis (Opsional)
$query_total_jual_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(jumlah_telur) as total_terjual 
    FROM `telur_terjual` tt
    JOIN transaksi t ON tt.id_transaksi = t.id_transaksi
    WHERE t.tanggal_transaksi = CURDATE()
"));

// 3. QUERY RIWAYAT (Disesuaikan Filter)
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
    $where_produksi
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
        .filter-box { margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .filter-box select { padding: 8px; border-radius: 5px; border: 1px solid #ccc; width: 200px; }
        .filter-box label { font-weight: bold; margin-right: 10px; }
    </style>
</head>

<body>
    <div class="container">
        <?php $active = 'produksi'; include("sidebar.php"); ?>
        <main>
            <h1>Produksi Telur</h1>
            <p>Laporan produksi telur: <strong><?= $label_periode ?></strong></p>

            <!-- SELECT PERIODE -->
            <div class="filter-box">
                <form method="GET" action="">
                    <label>Pilih Periode:</label>
                    <select name="periode" onchange="this.form.submit()">
                        <option value="bulanan" <?= $periode == 'bulanan' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                        <option value="harian" <?= $periode == 'harian' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="mingguan" <?= $periode == 'mingguan' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="semua" <?= $periode == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
                    </select>
                </form>
            </div>

            <a href="produksi.php" class="btn-tambah">+ Tambah Data</a>

            <div class="card-container">
                <div class="card">
                    <p>Total Produksi <br>(<?= $label_periode ?>)</p>
                    <h2><?= number_format($query_total_prod['total'] ?? 0, 1); ?></h2>
                    <span>Kg</span>
                </div>
                <div class="card">
                    <p>Total Terjual <br>(<?= $label_periode ?>)</p>
                    <h2><?= number_format($query_total_jual['total_terjual'] ?? 0, 1); ?></h2>
                    <span>Kg</span>
                </div>

                <div class="card">
                    <p>Total Terjual <br>(Hari Ini)</p>
                    <h2><?= number_format($query_total_jual_hari_ini['total_terjual'] ?? 0, 1); ?></h2>
                    <span>Kg</span>
                </div>
            </div>

            <div class="table-box">
                <h3>Riwayat Produksi - <?= $label_periode ?></h3>
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
                                    <td class="hijau">
                                        <?= number_format($row['total_terjual'] ?? 0, 1); ?>
                                    </td>
                                    <td>
                                        <a href="hapus_produksi.php?id=<?= $row['id_produksi']; ?>"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">Data tidak ditemukan untuk periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>