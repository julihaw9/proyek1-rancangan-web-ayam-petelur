<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. TANGKAP FILTER STATUS POPULASI
$status_filter = $_GET['status_filter'] ?? 'semua'; // Default ke semua status

// 2. QUERY SUMMARY CARD (Tetap menghitung total keseluruhan)
$query_ayam = mysqli_query($conn, "SELECT SUM(total_ayam) as total FROM blok_kandang");
$data_ayam = mysqli_fetch_assoc($query_ayam);

$query_kandang = mysqli_query($conn, "SELECT COUNT(id_blok_kandang) as totalkandang FROM blok_kandang");
$data_kandang = mysqli_fetch_assoc($query_kandang);

// Ambil asumsi produktif (pembelian kurang dari sama dengan 72 minggu yang lalu)
// Umur beli 18 minggu + 72 minggu di kandang = 90 minggu batas produktif
$query_produktif = mysqli_query($conn, "SELECT SUM(total_ayam) as total_produktif FROM blok_kandang 
                                        WHERE tanggal_pembelian_ayam >= DATE_SUB(CURDATE(), INTERVAL 72 WEEK)");
$data_produktif = mysqli_fetch_assoc($query_produktif);
$total_ayam_produktif = $data_produktif['total_produktif'] ?? 0;


// 3. LOGIKA FILTER UNTUK QUERY TABEL RIWAYAT
$where_riwayat = "";
if ($status_filter == 'produktif') {
    $where_riwayat = "WHERE tanggal_pembelian_ayam >= DATE_SUB(CURDATE(), INTERVAL 72 WEEK)";
} elseif ($status_filter == 'afkir') {
    $where_riwayat = "WHERE tanggal_pembelian_ayam < DATE_SUB(CURDATE(), INTERVAL 72 WEEK)";
}

$query_riwayat = mysqli_query($conn, "
    SELECT 
        id_blok_kandang,
        total_ayam,
        kapasitas_per_blok,
        tanggal_pembelian_ayam
    FROM blok_kandang
    $where_riwayat
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
    <style>
        /* ACTION BAR: TOMBOL BERADA DI SEBELAH KANAN ATAS */
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-group-side {
            display: flex;
            gap: 10px;
            align-items: center; /* Memastikan semua elemen di dalamnya sejajar secara vertikal */
        }

        .btn-group-side .btn-tambah, 
        .btn-group-side .btn-update {
            display: inline-flex;  /* Menggunakan flex agar icon dan teks sejajar sempurna */
            align-items: center;
            justify-content: center;
            gap: 6px;              /* Jarak antara ikon rotasi dengan teks */
            padding: 10px 18px;    
            height: 42px;          /* Mengunci tinggi tombol agar sama rata (Presisi) */
            box-sizing: border-box;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            white-space: nowrap;
            color: white;
            transition: opacity 0.2s;
        }

        .btn-group-side .btn-tambah { 
            background-color: #28a745; 
        }
        
        .btn-group-side .btn-update { 
            background-color: #007bff; 
            margin-top: 10px;
        }
        
        .btn-group-side .btn-tambah:hover, 
        .btn-group-side .btn-update:hover { 
            opacity: 0.9; 
        }
        
        /* Modifikasi ukuran entitas simbol rotasi agar seimbang dengan teks */
        .btn-update span.icon-rotasi {
            font-size: 18px;
            line-height: 1;
            display: inline-block;
        }

        /* === Penyesuaian Komponen Card dan Filter === */
        .card-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            align-items: stretch; /* Tinggi card & filter otomatis setara */
            flex-wrap: wrap;
        }

        .card, .filter-card-custom {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            min-width: 250px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .filter-card-custom {
            flex: 1.2;
            min-width: 300px;
        }

        /* Gaya Tombol Kapsul Radio Kustom (Oranye Prima Farm) */
        .radio-tile-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
        }

        .radio-wrapper {
            position: relative;
        }

        .radio-wrapper input[type="radio"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
            margin: 0;
        }

        .radio-label {
            display: inline-block;
            padding: 10px 15px;
            background: #f3f4f6;
            color: #4b5563;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            border: 1.5px solid transparent;
            transition: all 0.2s ease;
        }

        .radio-wrapper input[type="radio"]:checked + .radio-label {
            background-color: #f0861c;
            color: white;
            box-shadow: 0 4px 6px rgba(240, 134, 28, 0.2);
        }

        .radio-wrapper input[type="radio"]:hover:not(:checked) + .radio-label {
            background-color: #e5e7eb;
        }

        /* === Desain Badge Status Pada Tabel === */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .status-badge.produktif {
            background-color: #e6f4ea;
            color: #137333;
        }

        .status-badge.afkir {
            background-color: #fce8e6;
            color: #c5221f;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php $active = 'inventori'; include("sidebar.php"); ?>

        <main>
            <h1>Inventori Ayam</h1>
            <p>Kelola data populasi ayam per blok kandang</p>

            <div class="top-action-bar">
                <div class="btn-group-side">
                    <a href="inventori.php" class="btn-tambah">+ Tambah Data Blok</a>
                    <a href="update_ayam.php" class="btn-update"><span class="icon-rotasi">&#8635;</span> Update Data Ayam</a>
                </div>
            </div>

            <div class="card-container">
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Populasi Ayam</p>
                    <h2 style="margin: 15px 0 0 0;"><?php echo number_format($data_ayam['total'] ?? 0); ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Ekor</span></h2>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Blok Kandang</p>
                    <h2 style="margin: 15px 0 0 0;"><?php echo $data_kandang['totalkandang'] ?? 0; ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Blok</span></h2>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Ayam Produktif</p>
                    <h2 style="margin: 15px 0 0 0;" class="hijau"><?php echo number_format($total_ayam_produktif); ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Ekor</span></h2>
                </div>

                <div class="filter-card-custom">
                    <p style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #111;">Filter Status Kandang:</p>
                    <form method="GET" action="">
                        <div class="radio-tile-group">
                            <div class="radio-wrapper">
                                <input type="radio" name="status_filter" value="semua" id="semua" onchange="this.form.submit()" <?= $status_filter == 'semua' ? 'checked' : '' ?>>
                                <label for="semua" class="radio-label">Semua Blok</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="status_filter" value="produktif" id="produktif" onchange="this.form.submit()" <?= $status_filter == 'produktif' ? 'checked' : '' ?>>
                                <label for="produktif" class="radio-label">Produktif</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="status_filter" value="afkir" id="afkir" onchange="this.form.submit()" <?= $status_filter == 'afkir' ? 'checked' : '' ?>>
                                <label for="afkir" class="radio-label">Afkir</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-box">
                <h3>Daftar Blok Kandang (<?= ucfirst($status_filter) ?>)</h3>
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
                                    <td><strong><?php echo number_format($row['total_ayam']); ?></strong> ekor</td>
                                    <td><?php echo $total_umur_minggu; ?> Minggu</td>
                                    <td>
                                        <span class="status-badge <?php echo $class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="hapus_blok.php?id=<?php echo $row['id_blok_kandang']; ?>" 
                                           class="btn-aksi btn-hapus" 
                                           onclick="return confirm('Yakin ingin menghapus Blok <?php echo $row['id_blok_kandang']; ?>? Pastikan ayam sudah terjual habis.')">Hapus</a>
                                    </td>
                                </tr>
                        <?php } 
                        } else { ?>
                            <tr><td colspan="5" style="text-align: center;">Tidak ada data blok dengan status ini.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>