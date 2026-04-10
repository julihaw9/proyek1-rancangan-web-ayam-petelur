<?php
session_start();
include ("koneksi.php");

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
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="menu_inventori.php">Inventori</a></li>
            <li><a href="menu_produksi.php">Produksi</a></li>
            <li><a href="menu_transaksi.php">Transaksi Keuangan</a></li>
            <li><a href="menu_jadwalvaksinasi.php">Jadwal Vaksinasi</a></li>
            <li><a href="pengaturan.php">Pengaturan</a></li>
        </ul>
    </nav>

    <a href="logout.php" class="logout-button">Logout</a>
</aside>

<main>

<h1>Dashboard</h1>
<p>Halo, <?= $_SESSION['nama']; ?> 👋</p>
<p>Ringkasan data peternakan ayam petelur</p>

<div class="card-container">

    <div class="card">
        <p>Total ayam</p>
        <h2 class="card-number"><?= $total_ayam ?></h2>
        <span>Ekor ayam</span>
    </div>

    <div class="card">
        <p>Produksi Hari ini</p>
        <h2 class="card-number"><?= $total_telur ?></h2>
        <span>Butir telur</span>
    </div>

</div>

<div class="chart-box">
    <div class="chart-header">
        <h3>Trend Profit Bulanan</h3>
    </div>
</div>

</main>

</div>

</body>
</html>