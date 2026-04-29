<?php
session_start();
include("koneksi.php");
include("tanggal.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. Ambil Total Produksi dalam 1 Bulan Terakhir
$query_total_bulan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(total_telur) as total 
    FROM `produksi_telur` 
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
"));

// 2. Ambil Total Telur Saat Ini (Sebelumnya Telur Baik)
$query_telur_saat_ini = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(jumlah_telur_saat_ini) as total 
    FROM `detail_produksi_telur`
"));

// 3. Query Riwayat Produksi (Tanpa kolom rusak)
$query_riwayat = mysqli_query($conn, "
    SELECT 
        pt.id_produksi,
        pt.tanggal,
        pt.total_telur,
        p.nama_petugas,
        COALESCE(SUM(dpt.jumlah_telur_saat_ini), 0) as total_saat_ini
    FROM produksi_telur pt
    LEFT JOIN detail_produksi_telur dpt ON pt.id_produksi = dpt.id_produksi
    LEFT JOIN petugas p ON pt.id_petugas = p.id_petugas
    GROUP BY pt.id_produksi
    ORDER BY pt.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produksi Telur - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
    <div class="container">
        <?php $active = 'produksi'; include("sidebar.php"); ?>
        <main>
            <h1>Produksi Telur</h1>
            <p>Riwayat produksi telur harian dari kandang</p>

            <a href="produksi.php" class="btn-tambah">+ Tambah Data</a>

            <div class="card-container">
                <div class="card">
                    <p>Total Produksi <br>(30 Hari Terakhir)</p>
                    <h2><?= number_format($query_total_bulan['total'] ?? 0, 1); ?></h2>
                    <span>Kg</span>
                </div>
                
                <div class="card">
                    <p>Telur Saat Ini</p>
                    <h2 class="hijau"><?= number_format($query_telur_saat_ini['total'] ?? 0, 1); ?></h2>
                    <span>Kg</span>
                </div>
            </div>

            <div class="table-box">
                <h3>Riwayat Produksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Total Produksi (Kg)</th>
                            <th>Telur Saat Ini (Kg)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query_riwayat)) { ?>
                            <tr>
                                <td><?= tanggal_indo($row['tanggal']); ?></td>
                                <td><?= $row['nama_petugas'] ?? '-'; ?></td>
                                <td><strong><?= number_format($row['total_telur'], 1); ?></strong></td>
                                <td class="hijau"><?= number_format($row['total_saat_ini'], 1); ?></td>
                                <td>
                                    <a href="hapus_produksi.php?id=<?= $row['id_produksi']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>