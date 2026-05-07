<?php
session_start();
include("koneksi.php");
include("tanggal.php");

mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. TANGKAP FILTER PERIODE
$periode = $_GET['periode'] ?? 'semua'; // Default 'semua'
$where_clause = "";

switch ($periode) {
    case 'harian':
        $where_clause = "WHERE DATE(t.tanggal_transaksi) = CURDATE()";
        $label_periode = "Hari Ini";
        break;
    case 'mingguan':
        $where_clause = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label_periode = "7 Hari Terakhir";
        break;
    case 'bulanan':
        $where_clause = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $label_periode = "30 Hari Terakhir";
        break;
    default:
        $where_clause = "WHERE 1=1"; // Semua data
        $label_periode = "Semua Waktu";
        break;
}

// 2. SUMMARY KEUANGAN (Disesuaikan dengan Periode)
// Kita harus join ke tabel transaksi karena filter tanggal ada di sana
$query_summary = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(pa.total_uang), 0) FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause) AS total_ayam,
        (SELECT COALESCE(SUM(pt.total_uang), 0) FROM telur_terjual pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause) AS total_telur,
        (SELECT COALESCE(SUM(p.total_uang), 0) FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause) AS total_pengeluaran
");
$res_sum = mysqli_fetch_assoc($query_summary);

$total_pemasukan = $res_sum['total_ayam'] + $res_sum['total_telur'];
$total_pengeluaran = $res_sum['total_pengeluaran'];
$profit = $total_pemasukan - $total_pengeluaran;

// 3. RIWAYAT TRANSAKSI (Disesuaikan dengan Periode)
$sql_history = "
    SELECT t.tanggal_transaksi, pa.jumlah_ayam AS jumlah, pa.total_uang, 'Pemasukan' AS jenis, 'Ayam' AS sumber, pa.keterangan, t.id_transaksi
    FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause
    UNION ALL
    SELECT t.tanggal_transaksi, pt.jumlah_telur AS jumlah, pt.total_uang, 'Pemasukan' AS jenis, 'Telur' AS sumber, pt.keterangan, t.id_transaksi
    FROM telur_terjual pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause
    UNION ALL
    SELECT t.tanggal_transaksi, p.jumlah AS jumlah, p.total_uang, 'Pengeluaran' AS jenis, 'Pengeluaran' AS sumber, p.keterangan, t.id_transaksi
    FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause
    ORDER BY tanggal_transaksi DESC
";

$query_transaksi = mysqli_query($conn, $sql_history);

if (!$query_transaksi) {
    die("Kesalahan Query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Keuangan</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Tambahan style untuk form filter */
        .filter-container { margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-container select { padding: 8px; border-radius: 4px; border: 1px solid #ddd; }
        .filter-container label { font-weight: bold; margin-right: 10px; }
        .filter-container .btn-group a { display: flex;}
    </style>
</head>
<body>

    <div class="container">
        <?php $active = 'transaksi'; include("sidebar.php"); ?>

        <main>
            <h1>Analisis Keuangan</h1>
            <p>Laporan berdasarkan periode: <strong><?= $label_periode ?></strong></p>

            <!-- FORM FILTER PERIODE -->
            <div class="filter-container">
                <form method="GET" action="">
                    <label for="periode">Pilih Periode:</label>
                    <select name="periode" id="periode" onchange="this.form.submit()">
                        <option value="semua" <?= $periode == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
                        <option value="harian" <?= $periode == 'harian' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="mingguan" <?= $periode == 'mingguan' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="bulanan" <?= $periode == 'bulanan' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                    </select>
                </form>
                <div class="btn-group">
                <a href="revisicatatanpenjualan.php" class="btn-hijau">+ Tambah Jual Telur</a>
                <a href="catatan_penjualan_ayam.php" class="btn-hijau">+ Tambah Jual Ayam</a>
                <a href="catatpengeluaran.php" class="btn-merah">+ Tambah Pengeluaran</a>
                <a href="cetak_laporan.php?periode=<?= $periode ?>" target="_blank" class="btn-biru">Cetak PDF</a>
                </div>
            </div>

            

            <div class="card-container">
                <div class="card">
                    <p>Total Pendapatan (<?= $label_periode ?>)</p>
                    <h2>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>
                <div class="card">
                    <p>Total Biaya (<?= $label_periode ?>)</p>
                    <h2 class="merah">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>
                <div class="card">
                    <p>Total Profit (<?= $label_periode ?>)</p>
                    <h2 class="<?= $profit >= 0 ? 'hijau' : 'merah' ?>">
                        Rp <?= number_format($profit, 0, ',', '.') ?>
                    </h2>
                </div>
            </div>

            <div class="table-box">
                <h3>Riwayat Transaksi - <?= $label_periode ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Total Uang</th>
                            <th>Jenis</th>
                            <th>Sumber</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_transaksi) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_transaksi)): ?>
                                <tr>
                                    <td><?= tanggal_indo($row['tanggal_transaksi']); ?></td>
                                    <td><?= ($row['jumlah'] != '-' && $row['jumlah'] != null) ? number_format((float)$row['jumlah'], 0, ',', '.') : '-' ?></td>
                                    <td><strong>Rp <?= number_format((float)($row['total_uang'] ?? 0), 0, ',', '.') ?></strong></td>
                                    <td class="<?= ($row['jenis'] == 'Pemasukan') ? 'hijau' : 'merah' ?>"><?= $row['jenis'] ?></td>
                                    <td><?= $row['sumber'] ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                    <td>
                                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>&sumber=<?= strtolower($row['sumber']) ?>"
                                           onclick="return confirm('Yakin hapus?')" class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;">Tidak ada transaksi pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>