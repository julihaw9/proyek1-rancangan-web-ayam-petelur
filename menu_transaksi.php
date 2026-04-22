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

// ✅ 1. AMBIL SUMMARY KEUANGAN
$query_summary = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(total_uang), 0) FROM pemasukan_ayam) AS total_ayam,
        (SELECT COALESCE(SUM(total_uang), 0) FROM pemasukan_telur) AS total_telur,
        (SELECT COALESCE(SUM(total_uang), 0) FROM pengeluaran) AS total_pengeluaran
");
$res_sum = mysqli_fetch_assoc($query_summary);

$total_pemasukan = $res_sum['total_ayam'] + $res_sum['total_telur'];
$total_pengeluaran = $res_sum['total_pengeluaran'];
$profit = $total_pemasukan - $total_pengeluaran;

// ✅ 2. RIWAYAT TRANSAKSI (Dengan perbaikan COLLATE agar tidak error)
$query_transaksi = mysqli_query($conn, "
    SELECT 
        t.tanggal_transaksi,
        pa.jumlah_ayam AS jumlah,
        pa.total_uang,
        CAST('Pemasukan' AS CHAR) COLLATE utf8mb4_general_ci AS jenis,
        CAST('Ayam' AS CHAR) COLLATE utf8mb4_general_ci AS sumber,
        pa.keterangan COLLATE utf8mb4_general_ci AS keterangan,
        t.id_transaksi
    FROM pemasukan_ayam pa
    JOIN transaksi t ON pa.id_transaksi = t.id_transaksi

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        pt.jumlah_telur AS jumlah,
        pt.total_uang,
        CAST('Pemasukan' AS CHAR) COLLATE utf8mb4_general_ci AS jenis,
        CAST('Telur' AS CHAR) COLLATE utf8mb4_general_ci AS sumber,
        pt.keterangan COLLATE utf8mb4_general_ci AS keterangan,
        t.id_transaksi
    FROM pemasukan_telur pt
    JOIN transaksi t ON pt.id_transaksi = t.id_transaksi

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        CAST('-' AS CHAR) COLLATE utf8mb4_general_ci AS jumlah,
        p.total_uang,
        CAST('Pengeluaran' AS CHAR) COLLATE utf8mb4_general_ci AS jenis,
        CAST('-' AS CHAR) COLLATE utf8mb4_general_ci AS sumber,
        p.keterangan COLLATE utf8mb4_general_ci AS keterangan,
        t.id_transaksi
    FROM pengeluaran p
    JOIN transaksi t ON p.id_transaksi = t.id_transaksi

    ORDER BY tanggal_transaksi DESC
    LIMIT 10
");
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
                <input type="text" placeholder="Cari transaksi...">
            </div>

            <p>Pantau arus kas masuk dan keluar secara real-time.</p>

            <div class="btn-group">
                <a href="revisicatatanpenjualan.php" class="btn-hijau">+ Tambah Data Penjualan</a>
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