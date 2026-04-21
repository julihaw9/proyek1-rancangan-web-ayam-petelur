<?php
session_start();
include("koneksi.php");
include("tanggal.php");


$query_telur = mysqli_query($conn, "SELECT SUM(total_telur) as total FROM `produksi_telur`");
$data_telur = mysqli_fetch_assoc($query_telur);

$query_telur_baik = mysqli_query($conn, "SELECT SUM(jumlah_telur_baik) as total FROM `detail_produksi_telur`");
$data_telur_baik = mysqli_fetch_assoc($query_telur_baik);

$query_telur_rusak = mysqli_query($conn, "SELECT SUM(jumlah_telur_rusak) as total FROM `detail_produksi_telur`");
$data_telur_rusak = mysqli_fetch_assoc($query_telur_rusak);

$query_riwayat = mysqli_query($conn, "
    SELECT 
    pt.id_produksi,
    pt.tanggal,
    pt.total_telur,
    COALESCE(SUM(dpt.jumlah_telur_baik), 0) as total_baik,
    COALESCE(SUM(dpt.jumlah_telur_rusak), 0) as total_rusak
FROM produksi_telur pt
LEFT JOIN detail_produksi_telur dpt 
    ON pt.id_produksi = dpt.id_produksi
GROUP BY pt.id_produksi
ORDER BY pt.tanggal DESC;

");


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

        <?php $active = 'produksi'; ?>
        <?php include("sidebar.php"); ?>
        <main>

            <h1>Produksi Telur</h1>
            <p>Formulir dan riwayat produksi telur harian</p>

            <a href="produksi.html" class="btn-tambah">+ Tambah Data</a>

            <div class="card-container">

                <div class="card">
                    <p>Total Produksi</p>
                    <h2><?php echo $data_telur['total']; ?></h2>
                    <span>Kg</span>
                </div>

                <div class="card">
                    <p>Telur Baik</p>
                    <h2 class="hijau"><?php echo $data_telur_baik['total']; ?></h2>
                    <span>kg</span>
                </div>

                <div class="card merah">
                    <p>Telur Rusak</p>
                    <h2 class="merah"><?php echo $data_telur_rusak['total']; ?></h2>
                    <span>kg</span>
                </div>
            </div>
            <div class="table-box">

                <h3>Riwayat Produksi</h3>

                <table>

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total Telur</th>
                            <th>Total Baik</th>
                            <th>Total Rusak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php while ($row = mysqli_fetch_assoc($query_riwayat)) { ?>

                            <tr>
                                <td>
                                    <?php echo tanggal_indo($row['tanggal']); ?>
                                </td>
                                <td>
                                    <?php echo number_format($row['total_telur']); ?>
                                </td>
                                <td class="hijau">
                                    <?php echo number_format($row['total_baik']); ?>
                                </td>
                                <td class="merah">
                                    <?php echo number_format($row['total_rusak']); ?>
                                </td>
                                <td>
                                    <a href="hapus_produksi.php?id=<?php echo $row['id_produksi']; ?>"
                                        onclick="return confirm('Yakin ingin menghapus?')">🗑</a>
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