<?php
session_start();
include("koneksi.php");
include("tanggal.php");

// Pastikan charset koneksi seragam untuk menghindari error collation
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// SUMMARY KEUANGAN
$query_summary = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(total_uang), 0) FROM pemasukan_ayam) AS total_ayam,
        (SELECT COALESCE(SUM(total_uang), 0) FROM telur_terjual) AS total_telur,
        (SELECT COALESCE(SUM(total_uang), 0) FROM pengeluaran) AS total_pengeluaran
");
$res_sum = mysqli_fetch_assoc($query_summary);

$total_pemasukan = $res_sum['total_ayam'] + $res_sum['total_telur'];
$total_pengeluaran = $res_sum['total_pengeluaran'];
$profit = $total_pemasukan - $total_pengeluaran;

// RIWAYAT TRANSAKSI
$query_transaksi = mysqli_query($conn, "
    SELECT 
        t.tanggal_transaksi,
        pa.jumlah_ayam AS jumlah, 
        pa.total_uang,
        'Pemasukan' AS jenis,
        'Ayam' AS sumber,
        pa.keterangan,
        t.id_transaksi
    FROM pemasukan_ayam pa
    JOIN transaksi t ON pa.id_transaksi = t.id_transaksi
    WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        pt.jumlah_telur AS jumlah,
        pt.total_uang,
        'Pemasukan' AS jenis,
        'Telur' AS sumber,
        pt.keterangan,
        t.id_transaksi
    FROM telur_terjual pt
    JOIN transaksi t ON pt.id_transaksi = t.id_transaksi
    WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        '-' AS jumlah,
        p.total_uang,
        'Pengeluaran' AS jenis,
        'Umum' AS sumber,
        p.keterangan,
        t.id_transaksi
    FROM pengeluaran p
    JOIN transaksi t ON p.id_transaksi = t.id_transaksi
    WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)

    ORDER BY tanggal_transaksi DESC
    LIMIT 20
");

// Cek jika query gagal (untuk mempermudah debugging)
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
</head>

<body>

    <div class="container">

        <?php $active = 'transaksi';
        include("sidebar.php"); ?>

        <main>
            <div class="top-bar">
                <h1>Analisis Keuangan</h1><br>
            </div>

            <div class="top-bar">
                <input type="text" placeholder="Cari transaksi...">
            </div>

            

            <div class="btn-group">
                <a href="revisicatatanpenjualan.php" class="btn-hijau">+ Tambah Data Penjualan telur</a>
                <a href="catatan_penjualan_ayam.php" class="btn-hijau">+ Tambah Data Penjualan Ayam</a>
                <a href="catatpengeluaran.php" class="btn-merah">+ Tambah Data Pengeluaran</a>
            </div>

            <div class="card-container">
                <div class="card">
                    <p>Total Pendapatan</p>
                    <h2>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>

                <div class="card">
                    <p>Total Biaya</p>
                    <h2 class="merah">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>

                <div class="card">
                    <p>Total Profit</p>
                    <h2 class="<?= $profit >= 0 ? 'hijau' : 'merah' ?>">
                        Rp <?= number_format($profit, 0, ',', '.') ?>
                    </h2>
                </div>
            </div>

            <div class="table-box">
                <h3>Riwayat Transaksi Terakhir</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah (kg)</th>
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

                                    <td>
                                        <?php
                                        $jumlah = $row['jumlah'] ?? 0; // Jika null, jadikan 0
                                        echo is_numeric($jumlah) ? number_format((float) $jumlah, 0, ',', '.') : '-';
                                        ?>
                                    </td>

                                    <td>
                                        <strong>Rp <?= number_format((float) ($row['total_uang'] ?? 0), 0, ',', '.') ?></strong>
                                    </td>

                                    <td class="<?= ($row['jenis'] == 'Pemasukan') ? 'hijau' : 'merah' ?>">
                                        <?= $row['jenis'] ?>
                                    </td>

                                    <td><?= $row['sumber'] ?></td>

                                    <td><?= htmlspecialchars($row['keterangan'] ?? ''); ?></td>

                                    <td>
                                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>&sumber=<?= strtolower($row['sumber']) ?>"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada data transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>