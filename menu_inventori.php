<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit; // Tambahkan exit setelah header redirect
}

// 1. Hitung Total Ayam
$query_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$data_ayam = mysqli_fetch_assoc($query_ayam);

// 2. Hitung Total Kandang (Menghitung jumlah baris/blok yang ada di database)
$query_kandang = mysqli_query($conn, "SELECT COUNT(id_blok_kandang) as totalkandang FROM blok_kandang");
$data_kandang = mysqli_fetch_assoc($query_kandang);

// 3. Ambil Riwayat Data
$query_riwayat = mysqli_query($conn, "
    SELECT 
        id_blok_kandang,
        total_ayam,
        kapasitas_per_blok,
        tanggal_pembelian_ayam
    FROM blok_kandang
    ORDER BY id_blok_kandang ASC
");

if (!$query_riwayat) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventori</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Tambahan style untuk label status agar lebih menarik */
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .produktif {
            background-color: #dcfce7;
            color: #166534;
        }

        .belum-produktif {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .afkir {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php $active = 'inventori'; ?>
        <?php include("sidebar.php"); ?>

        <main>
            <h1>Inventori Ayam</h1>
            <p>Kelola data populasi ayam per blok kandang</p>

            <div class="top-bar">
                <input type="text" placeholder="Cari nomor kandang...">
                <a href="inventori.php" class="btn-tambah">+ Tambah Data</a>
            </div>

            <div class="card-container">
                <div class="card">
                    <p>Total Populasi Ayam</p>
                    <h2><?php echo number_format($data_ayam['total'] ?? 0); ?> <span
                            style="font-size: 14px; color: #666;">Ekor</span></h2>
                </div>

                <div class="card">
                    <p>Total Blok Kandang</p>
                    <h2><?php echo $data_kandang['totalkandang'] ?? 0; ?> <span
                            style="font-size: 14px; color: #666;">Blok</span></h2>
                </div>
            </div>

            <div class="table-box">
                <h3>Riwayat & Status Produksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nomor Blok</th>
                            <th>Populasi</th>
                            <th>Umur (Minggu)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_riwayat) > 0) { ?>
                            <?php
                            while ($row = mysqli_fetch_assoc($query_riwayat)) {
                                // 1. Hitung selisih minggu sejak tanggal pembelian sampai sekarang
                                $tgl_beli = new DateTime($row['tanggal_pembelian_ayam']);
                                $sekarang = new DateTime();
                                $selisih = $sekarang->diff($tgl_beli);
                                $minggu_sejak_beli = floor($selisih->days / 7);

                                // 2. Tambahkan estimasi umur awal (Asumsi: Ayam dibeli saat umur 18 minggu)
                                $umur_awal_beli = 18;
                                $total_umur_minggu = $minggu_sejak_beli + $umur_awal_beli;

                                // 3. Tentukan Status berdasarkan total umur
                                if ($total_umur_minggu > 90) {
                                    $status = "Afkir";
                                    $class = "afkir";
                                } else {
                                    // Karena awal beli sudah produktif, maka status defaultnya adalah Produktif
                                    $status = "Produktif";
                                    $class = "produktif";
                                }
                                ?>
                                <tr>
                                    <td><strong>Blok <?php echo $row['id_blok_kandang']; ?></strong></td>
                                    <td><?php echo number_format($row['total_ayam']); ?> ekor</td>
                                    <td><?php echo $total_umur_minggu; ?> Minggu</td>
                                    <td>
                                        <span class="status-badge <?php echo $class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Belum ada data inventori.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>