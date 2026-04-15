<?php
session_start();

include("koneksi.php");

// proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
}

// total ayam
$query_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$data_ayam = mysqli_fetch_assoc($query_ayam);

$query_kandang = mysqli_query($conn, "SELECT ROUND(SUM(kapasitas_per_blok)/42) as totalkandang FROM blok_kandang");
$data_kandang = mysqli_fetch_assoc($query_kandang);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
                    <li><a href="menu_inventori.php" class="active">Inventori</a></li>
                    <li><a href="menu_produksi.php">Produksi</a></li>
                    <li><a href="menu_transaksi.php">Transaksi Keuangan</a></li>
                    <li><a href="menu_jadwalvaksinasi.php">Jadwal Vaksinasi</a></li>
                    <li><a href="pengaturan.php">Pengaturan</a></li>
                </ul>
            </nav>

            <a href="logout.php" class="logout-button">Logout</a>
        </aside>

        <main>

            <h1>Inventori Ayam</h1>
            <p>Kelola data populasi ayam</p>

            <div class="top-bar">
                <input type="text" placeholder="Cari kandang atau jenis ayam...">
                <a href="Inventori.html" class="btn-tambah">+ Tambah Data</a>
            </div>

            <div class="card-container">

                <div class="card">
                    <p>Total ayam</p>
                    <h2><?php echo $data_ayam['total']; ?></h2>
                </div>

                <div class="card">
                    <p>Total Kandang</p>
                    <h2><?php echo $data_kandang['totalkandang']; ?></h2>
                </div>

            </div>

            <div class="table-box">

                <h3>Riwayat</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Kandang</th>
                            <th>Jumlah</th>
                            <th>Umur (Minggu)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>Kandang A</td>
                            <td>1,250 ekor</td>
                            <td>24 Minggu</td>
                            <td class="status">Produktif</td>
                            <td>🗑</td>
                        </tr>

                        <tr>
                            <td>Kandang B</td>
                            <td>1,480 ekor</td>
                            <td>18 Minggu</td>
                            <td class="status">Produktif</td>
                            <td>🗑</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </main>
    </div>

</body>

</html>