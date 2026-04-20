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

// produksi hari ini
$query_telur = mysqli_query($conn, "
    SELECT SUM(total_telur) as total 
    FROM produksi_telur 
    WHERE tanggal = CURDATE()
");
$data_telur = mysqli_fetch_assoc($query_telur);

$total_ayam = $data_ayam['total'] ?? 0;
$total_telur = $data_telur['total'] ?? 0;
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
        <?php $active = 'dashboard'; ?>
        <?php include("sidebar.php"); ?>
        <main>
            <main>
                <h1>Dashboard</h1>
                <p>Ringkasan data peternakan ayam petelur</p>

                <div class="card-container">

                    <div class="card">
                        <p>Total ayam</p>
                        <h2 class="card-number">158</h2>
                        <span>Ekor ayam</span>
                    </div>

                    <div class="card">
                        <p>Produksi Hari ini</p>
                        <h2 class="card-number">256</h2>
                        <span>kg telur</span>
                    </div>

                </div>

                <div class="chart-box">
                    <div class="chart-header">
                        <h3>Trend Profit Bulanan</h3>
                        <a href="#">Lihat Detail →</a>
                    </div>


                </div>

            </main>



        </main>

    </div>

</body>

</html>