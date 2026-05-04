<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. Hitung Total Ayam
$query_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$data_ayam = mysqli_fetch_assoc($query_ayam);

// 2. Hitung Total Kandang
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
    <title>Dashboard Inventori - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>

    <div class="container">
        <?php $active = 'inventori'; ?>
        <?php include("sidebar.php"); ?>

        <main>
            <h1>Inventori Ayam</h1>
            <p>Kelola data populasi ayam per blok kandang</p>

            <div class="top-bar">
                <a href="inventori.php" class="btn-tambah">+ Tambah Data</a>
            </div>

            <div class="card-container">
                <div class="card">
                    <p>Total Populasi Ayam</p>
                    <h2><?php echo number_format($data_ayam['total'] ?? 0); ?> <span style="font-size: 14px; color: #666;">Ekor</span></h2>
                </div>

                <div class="card">
                    <p>Total Blok Kandang</p>
                    <h2><?php echo $data_kandang['totalkandang'] ?? 0; ?> <span style="font-size: 14px; color: #666;">Blok</span></h2>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_riwayat) > 0) { 
                            while ($row = mysqli_fetch_assoc($query_riwayat)) {
                                // Hitung umur
                                $tgl_beli = new DateTime($row['tanggal_pembelian_ayam']);
                                $sekarang = new DateTime();
                                $selisih = $sekarang->diff($tgl_beli);
                                $minggu_sejak_beli = floor($selisih->days / 7);
                                $total_umur_minggu = $minggu_sejak_beli + 18; // Asumsi beli umur 18 mgg

                                if ($total_umur_minggu > 90) {
                                    $status = "Afkir"; $class = "afkir";
                                } else {
                                    $status = "Produktif"; $class = "produktif";
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
                                    <td>
                                        <a href="catatan_penjualan_ayam.php?id_blok=<?php echo $row['id_blok_kandang']; ?>" class="btn-aksi btn-jual">Jual Ayam</a>
                                        
                                        <a href="hapus_blok.php?id=<?php echo $row['id_blok_kandang']; ?>" 
                                           class="btn-aksi btn-hapus" 
                                           onclick="return confirm('Yakin ingin menghapus Blok <?php echo $row['id_blok_kandang']; ?>? Pastikan ayam sudah terjual habis.')">Hapus</a>
                                    </td>
                                </tr>
                        <?php } 
                        } else { ?>
                            <tr><td colspan="5" style="text-align: center;">Belum ada data.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>