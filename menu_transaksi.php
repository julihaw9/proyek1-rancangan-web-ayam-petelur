<?php
session_start();
include("koneksi.php");
include("tanggal.php");

// proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

//
// ✅ TOTAL PEMASUKAN
//
$query_pemasukan = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(total_uang),0) FROM pemasukan_ayam) +
        (SELECT COALESCE(SUM(total_uang),0) FROM pemasukan_telur)
    AS total
");
$data_pemasukan = mysqli_fetch_assoc($query_pemasukan);
$total_pemasukan = $data_pemasukan['total'];

//
// ✅ TOTAL PENGELUARAN
//
$query_pengeluaran = mysqli_query($conn, "
    SELECT COALESCE(SUM(total_uang),0) AS total FROM pengeluaran
");
$data_pengeluaran = mysqli_fetch_assoc($query_pengeluaran);
$total_pengeluaran = $data_pengeluaran['total'];

//
// ✅ RIWAYAT TRANSAKSI (UNION + SUMBER)
//
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

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        pt.jumlah_telur AS jumlah,
        pt.total_uang,
        'Pemasukan' AS jenis,
        'Telur' AS sumber,
        pt.keterangan,
        t.id_transaksi
    FROM pemasukan_telur pt
    JOIN transaksi t ON pt.id_transaksi = t.id_transaksi

    UNION ALL

    SELECT 
        t.tanggal_transaksi,
        '-' AS jumlah,
        p.total_uang,
        'Pengeluaran' AS jenis,
        '-' AS sumber,
        p.keterangan,
        t.id_transaksi
    FROM pengeluaran p
    JOIN transaksi t ON p.id_transaksi = t.id_transaksi

    ORDER BY tanggal_transaksi DESC
    LIMIT 5
");

$profit = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Analisis Keuangan</title>
    <link rel="stylesheet" href="menu.css">
</head>

<body>

    <div class="container">

        <?php $active = 'transaksi'; ?>
        <?php include("sidebar.php"); ?>

        <main>

            <h1>Analisis Keuangan</h1>
            <p>Catat transaksi dan pantau profit</p>

            <div class="btn-group">
                <a href="revisicatatanpenjualan.html" class="btn-hijau">+ Tambah Data Penjualan</a>
                <a href="catatpengeluaran.html" class="btn-merah">+ Tambah Data Pengeluaran</a>
            </div>

            <!-- CARD -->
            <div class="card-container">

                <div class="card">
                    <p>Total Profit</p>
                    <h2 style="color: <?= $profit >= 0 ? '#2ecc71' : '#e74c3c' ?>;">
                        Rp <?= number_format($profit, 0, ',', '.') ?>
                    </h2>
                </div>

                <div class="card">
                    <p>Total Pendapatan</p>
                    <h2>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>

                <div class="card">
                    <p>Total Biaya</p>
                    <h2>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>

            </div>

            <!-- TABEL -->
            <div class="table-box">

                <h3>Riwayat Transaksi Terakhir</h3>

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

                                    <td>
                                        <?= is_numeric($row['jumlah']) ? $row['jumlah'] : '-' ?>
                                    </td>

                                    <td>Rp <?= number_format($row['total_uang'], 0, ',', '.') ?></td>

                                    <td class="<?= ($row['jenis'] == 'Pemasukan') ? 'hijau' : 'merah' ?>">
                                        <?= $row['jenis'] ?>
                                    </td>

                                    <td><?= $row['sumber'] ?></td>

                                    <td><?= htmlspecialchars($row['keterangan']); ?></td>

                                    <td>
                                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>&jenis=transaksi&sumber=<?= $row['sumber'] ?>"
                                            onclick="return confirm('Yakin hapus?')">🗑</a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Belum ada transaksi</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>

        </main>

    </div>

</body>

</html>