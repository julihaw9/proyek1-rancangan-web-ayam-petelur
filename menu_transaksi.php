<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 1. Ambil Total Pemasukan
$query_pemasukan = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE jenis='Pemasukan'");
$row_pemasukan = mysqli_fetch_assoc($query_pemasukan);
$total_pemasukan = $row_pemasukan['total'] ?? 0;

// 2. Ambil Total Pengeluaran
$query_pengeluaran = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE jenis='Pengeluaran'");
$row_pengeluaran = mysqli_fetch_assoc($query_pengeluaran);
$total_pengeluaran = $row_pengeluaran['total'] ?? 0;

// 3. Hitung Profit
$profit = $total_pemasukan - $total_pengeluaran;

// 4. Ambil Riwayat Transaksi (Limit 5 untuk Dashboard)
$query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY tanggal_transaksi DESC LIMIT 5");


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Keuangan - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>

<div class="container">
    <aside>
        <h3>Prima Farm</h3>
        <hr>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu_inventori.php">Inventori</a></li>
                <li><a href="menu_produksi.php">Produksi</a></li>
                <li><a href="menu_transaksikeuangan.php" class="active">Transaksi Keuangan</a></li>
                <li><a href="menu_jadwalvaksinasi.php">Jadwal Vaksinasi</a></li>
                <li><a href="pengaturan.php">Pengaturan</a></li>
            </ul>
        </nav>
        <a href="index.html" class="logout-button">Logout</a>
    </aside>

    <main>
        <h1>Analisis Keuangan</h1>
        <p>Catat transaksi dan pantau profit</p>

        <div class="btn-group">
            <a href="revisicatatanpenjualan.html" class="btn-hijau">+ Tambah Data Penjualan</a>
            <a href="catatpengeluaran.html" class="btn-merah">+ Tambah Data Pengeluaran</a>
        </div>

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

        <div class="table-box">
            <h3>Riwayat Transaksi Terakhir</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Total Uang</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($query_transaksi)): ?>
                    <tr>
                        <td><?= date('d F Y', strtotime($row['tanggal_transaksi'])) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td class="<?= strtolower($row['jenis']) == 'pemasukan' ? 'hijau' : 'merah' ?>">
                            <?= $row['jenis'] ?>
                        </td>
                        <td><?= $row['keterangan'] ?></td>
                        <td>
                            <a href="hapus_transaksi.php?id=<?= $row['id_transaksi'] ?>" onclick="return confirm('Yakin hapus?')">🗑</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>