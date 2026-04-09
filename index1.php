<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


$query_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$data_ayam = mysqli_fetch_assoc($query_ayam);

// 2. Ambil Total Blok Kandang
$query_blok = mysqli_query($conn, "SELECT COUNT(id_blok_kandang) as total FROM blok_kandang");
$data_blok = mysqli_fetch_assoc($query_blok);

// 3. Ambil Total Produksi Telur (Keseluruhan)
$query_telur = mysqli_query($conn, "SELECT SUM(total_telur) as total FROM produksi_telur");
$data_telur = mysqli_fetch_assoc($query_telur);

// 4. Ambil 5 Transaksi Terakhir
$query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY tanggal_transaksi DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peternakan Ayam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-summary { border-radius: 15px; border: none; transition: 0.3s; }
        .card-summary:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Sistem Peternakan Ayam</a>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Dashboard Utama</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-summary bg-primary text-white p-3">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Total Ayam</h6>
                    <h2 class="display-6 fw-bold"><?= number_format($data_ayam['total'] ?? 0); ?> Ekor</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary bg-success text-white p-3">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Total Blok Kandang</h6>
                    <h2 class="display-6 fw-bold"><?= $data_blok['total']; ?> Blok</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-summary bg-warning text-dark p-3">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Total Produksi Telur</h6>
                    <h2 class="display-6 fw-bold"><?= number_format($data_telur['total'] ?? 0); ?> Butir</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transaksi Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>ID Transaksi</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query_transaksi)): ?>
                            <tr>
                                <td><?= $row['tanggal_transaksi']; ?></td>
                                <td>#<?= $row['id_transaksi']; ?></td>
                                <td>
                                    <span class="badge <?= $row['jenis_transaksi'] == 'pemasukan' ? 'bg-info' : 'bg-danger'; ?>">
                                        <?= ucfirst($row['jenis_transaksi']); ?>
                                    </span>
                                </td>
                                <td><a href="#" class="btn btn-sm btn-outline-secondary">Detail</a></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="list-group shadow-sm">
                <a href="#" class="list-group-item list-group-item-action active">Navigasi Cepat</a>
                <a href="#" class="list-group-item list-group-item-action">Manajemen Blok Kandang</a>
                <a href="#" class="list-group-item list-group-item-action">Input Produksi Telur</a>
                <a href="#" class="list-group-item list-group-item-action">Jadwal Vaksinasi</a>
                <a href="#" class="list-group-item list-group-item-action text-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>