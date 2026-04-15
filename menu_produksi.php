<?php
session_start();
include("koneksi.php");


$query_telur = mysqli_query($conn, "SELECT SUM(total_telur) as total FROM `produksi_telur`");
$data_telur = mysqli_fetch_assoc($query_telur);

$query_telur_baik = mysqli_query($conn, "SELECT SUM(jumlah_telur_baik) as total FROM `detail_produksi_telur`");
$data_telur_baik = mysqli_fetch_assoc($query_telur_baik);

$query_telur_rusak = mysqli_query($conn, "SELECT SUM(jumlah_telur_rusak) as total FROM `detail_produksi_telur`");
$data_telur_rusak = mysqli_fetch_assoc($query_telur_rusak);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produksi</title>
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

            <h1>Produksi Telur</h1>
            <p>Formulir dan riwayat produksi telur harian</p>

            <a href="produksi.html" class="btn-tambah">+ Tambah Data</a>

            <div class="card-container">

                <div class="card">
                    <p>Total Produksi</p>
                    <h2><?php echo $data_telur['total']; ?></h2>
                    <span>butir telur</span>
                </div>

                <div class="card">
                    <p>Telur Baik</p>
                    <h2 class="hijau"><?php echo $data_telur_baik['total']; ?></h2>
                    <span>butir</span>
                </div>

                <div class="card merah">
                    <p>Telur Rusak</p>
                    <h2 class="merah"><?php echo $data_telur_rusak['total']; ?></h2>
                    <span>butir</span>
                </div>
            </div>
            <div class="table-box">

                <h3>Riwayat Produksi</h3>

                <table>

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kandang</th>
                            <th>Total Telur</th>
                            <th>Total Baik</th>
                            <th>Total Rusak</th>
                            <th>Total Busuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>24 Februari 2026</td>
                            <td>Kandang A</td>
                            <td>1,260</td>
                            <td class="hijau">1,220</td>
                            <td class="merah">30</td>
                            <td class="merah">10</td>
                            <td>🗑</td>
                        </tr>

                        <tr>
                            <td>24 Februari 2026</td>
                            <td>Kandang B</td>
                            <td>1,480</td>
                            <td class="hijau">1,430</td>
                            <td class="merah">40</td>
                            <td class="merah">10</td>
                            <td>🗑</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </main>
    </div>

</body>

</html>