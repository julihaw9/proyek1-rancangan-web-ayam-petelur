<?php

date_default_timezone_set('Asia/Jakarta');
session_start();
include("koneksi.php");
include("tanggal.php");

if (!isset($_SESSION['login'])) {
    exit;
}

// Ambil Summary yang sama
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

// Ambil Semua Riwayat (Tanpa Limit agar laporan lengkap)
$query_transaksi = mysqli_query($conn, "
    SELECT t.tanggal_transaksi, pa.jumlah_ayam AS jumlah, pa.total_uang, 'Pemasukan' AS jenis, 'Ayam' AS sumber, pa.keterangan
    FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi
    UNION ALL
    SELECT t.tanggal_transaksi, pt.jumlah_telur AS jumlah, pt.total_uang, 'Pemasukan' AS jenis, 'Telur' AS sumber, pt.keterangan
    FROM telur_terjual pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi
    UNION ALL
    SELECT t.tanggal_transaksi, '-' AS jumlah, p.total_uang, 'Pengeluaran' AS jenis, 'Umum' AS sumber, p.keterangan
    FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi
    ORDER BY tanggal_transaksi DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - Prima Farm</title>
    <link rel="stylesheet" href="cetak.css">
</head>
<body>

    <div class="no-print" style="background: #f1f1f1; padding: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Klik untuk Print / Simpan PDF</button>
    </div>

    <div class="header">
        <h2>PRIMA FARM - LAPORAN KEUANGAN</h2>
        <p>Tanggal Cetak: <?= date('d/m/Y H:i') ?></p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Total Pendapatan:</strong> Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
            <td class="text-right"><strong>Status Profit:</strong> 
                <span class="<?= $profit >= 0 ? 'hijau' : 'merah' ?>">
                    Rp <?= number_format($profit, 0, ',', '.') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td><strong>Total Pengeluaran:</strong> Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
            <td></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Sumber</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Total Uang</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_transaksi)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_transaksi'])) ?></td>
                <td class="<?= ($row['jenis'] == 'Pemasukan') ? 'hijau' : 'merah' ?>"><?= $row['jenis'] ?></td>
                <td><?= $row['sumber'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                <td class="text-right">Rp <?= number_format($row['total_uang'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>


    <script>
        // Otomatis membuka dialog print saat halaman dimuat
        window.onload = function() {
            // window.print(); 
        };
    </script>
</body>
</html>