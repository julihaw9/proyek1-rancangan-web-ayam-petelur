<?php
session_start();
include("koneksi.php");
include("tanggal.php");

mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. TANGKAP FILTER PERIODE
$periode = $_GET['periode'] ?? 'semua'; // Default 'semua'
$where_clause = "";
$label_periode = "";

switch ($periode) {
    case 'harian':
        $where_clause = "WHERE DATE(t.tanggal_transaksi) = CURDATE()";
        $label_periode = "Hari Ini";
        break;
    case 'mingguan':
        $where_clause = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $label_periode = "7 Hari Terakhir";
        break;
    case 'bulanan':
        $where_clause = "WHERE t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $label_periode = "30 Hari Terakhir";
        break;
    default:
        $where_clause = "WHERE 1=1"; // Semua data
        $label_periode = "Semua Waktu";
        break;
}

// 2. TANGKAP KEYWORD PENCARIAN
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$search_clause = "";

if ($keyword !== '') {
    // Mencari berdasarkan keterangan, sumber (Ayam/Telur/Pengeluaran), atau jenis (Pemasukan/Pengeluaran)
    $search_clause = " WHERE keterangan LIKE '%$keyword%' 
                       OR sumber LIKE '%$keyword%' 
                       OR jenis LIKE '%$keyword%' ";
}

// 3. SUMMARY KEUANGAN (Disesuaikan dengan Periode)
$query_summary = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(pa.total_uang), 0) FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause) AS total_ayam,
        (SELECT COALESCE(SUM(pt.total_uang), 0) FROM telur_terjual pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause) AS total_telur,
        (SELECT COALESCE(SUM(p.total_uang), 0) FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause) AS total_pengeluaran
");
$res_sum = mysqli_fetch_assoc($query_summary);

$total_pemasukan = $res_sum['total_ayam'] + $res_sum['total_telur'];
$total_pengeluaran = $res_sum['total_pengeluaran'];
$profit = $total_pemasukan - $total_pengeluaran;

// 4. RIWAYAT TRANSAKSI (Disesuaikan dengan Periode & Keyword Pencarian)
$sql_history = "
    SELECT * FROM (
        SELECT t.tanggal_transaksi, pa.jumlah_ayam AS jumlah, pa.total_uang, 'Pemasukan' AS jenis, 'Ayam' AS sumber, pa.keterangan, t.id_transaksi
        FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause
        UNION ALL
        SELECT t.tanggal_transaksi, pt.jumlah_telur AS jumlah, pt.total_uang, 'Pemasukan' AS jenis, 'Telur' AS sumber, pt.keterangan, t.id_transaksi
        FROM telur_terjual pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause
        UNION ALL
        SELECT t.tanggal_transaksi, p.jumlah AS jumlah, p.total_uang, 'Pengeluaran' AS jenis, 'Pengeluaran' AS sumber, p.keterangan, t.id_transaksi
        FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause
    ) AS gabungan
    $search_clause
    ORDER BY tanggal_transaksi DESC
";

$query_transaksi = mysqli_query($conn, $sql_history);

if (!$query_transaksi) {
    die("Kesalahan Query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Keuangan - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Kontainer Utama Tombol Tambah Data */
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Grup tombol tambah */
        .btn-group-side {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-group-side .btn-hijau,
        .btn-group-side .btn-merah {
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            white-space: nowrap;
            color: white;
            transition: opacity 0.2s;
        }

        .btn-group-side .btn-hijau { background-color: #28a745; }
        .btn-group-side .btn-merah { background-color: #dc3545; }
        .btn-group-side .btn-hijau:hover, .btn-group-side .btn-merah:hover { opacity: 0.9; }

        /* === Integrasi Filter ke baris Card === */
        .card-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            align-items: stretch; /* Membuat semua card memiliki tinggi seimbang */
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
            flex: 1.5; /* Memberikan ruang sedikit lebih lebar untuk barisan filter */
            min-width: 320px;
        }

        /* Gaya Komponen Radio Button Oranye Prima Farm */
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
            padding: 10px 14px;
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

       
        .search-wrapper {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            /* Tetap rapat kanan pada layar lebar */
            margin-bottom: 20px;
            width: 100%;
            /* Memastikan kontainer utama mengambil 100% lebar */
        }

        .search-form {
            display: flex;
            gap: 5px;
            width: 100%;
            /* Membuat form fleksibel memenuhi ruang */
            max-width: 800px;
            /* Batas maksimal lebar di layar PC agar tidak terlalu panjang, hapus baris ini jika ingin benar-benar mentok dari kiri ke kanan */
        }

        .input-cari {
            flex: 1;
            /* Ini kunci agar kolom input otomatis melebar menghabiskan sisa ruang */
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .btn-cari {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            margin-left: 5px;
            margin-right: 5px;
            text-decoration: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            white-space: nowrap;
            /* Mencegah teks tombol terlipat ke bawah */
            transition: background 0.2s;
        }

        .btn-cari:hover {
            background-color: #0056b3;
        }

        .btn-reset {
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tombol Cetak PDF diletakkan di luar form, otomatis berjarak di sebelahnya */
        .search-wrapper .btn-biru {
            padding: 10px 20px;
            margin-left: 10px;
            white-space: nowrap;
        }

        .btn-cetak {
            padding: 18px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            margin-right: 10px;
            margin-left: 10px;
            border-radius: 10px;
            font-weight: bold;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .btn-cetak:hover { background-color: #1e7e34; }
    </style>
</head>

<body>

    <div class="container">
        <?php $active = 'transaksi'; include("sidebar.php"); ?>

        <main>
            <h1>Analisis Keuangan</h1>
            <p>Laporan berdasarkan periode: <strong><?= $label_periode ?></strong></p>

            <!-- ACTION BAR: TOMBOL TAMBAH DATA -->
            <div class="top-action-bar">
                <div class="btn-group-side">
                    <a href="revisicatatanpenjualan.php" class="btn-hijau">+ Tambah Jual Telur</a>
                    <a href="catatan_penjualan_ayam.php" class="btn-hijau">+ Tambah Jual Ayam</a>
                    <a href="catatpengeluaran.php" class="btn-merah">+ Tambah Pengeluaran</a>
                </div>
            </div>

            <!-- CONTAINER UTAMA: CARD SUMMARY DAN CARD FILTER SEJAJAR -->
            <div class="card-container">
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Pendapatan<br><b>(<?= $label_periode ?>)</b></p>
                    <h2 style="margin: 15px 0 0 0;">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>
                
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Biaya<br><b>(<?= $label_periode ?>)</b></p>
                    <h2 style="margin: 15px 0 0 0;" class="merah">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>
                
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Profit<br><b>(<?= $label_periode ?>)</b></p>
                    <h2 style="margin: 15px 0 0 0;" class="<?= $profit >= 0 ? 'hijau' : 'merah' ?>">
                        Rp <?= number_format($profit, 0, ',', '.') ?>
                    </h2>
                </div>

                <!-- FILTER KINI BERADA DI DALAM KELOMPOK CARD -->
                <div class="filter-card-custom">
                    <p style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #111;">Pilih Periode:</p>
                    <form method="GET" action="">
                        <?php if ($keyword !== ''): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>

                        <div class="radio-tile-group">
                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="semua" id="semua" onchange="this.form.submit()" <?= $periode == 'semua' ? 'checked' : '' ?>>
                                <label for="semua" class="radio-label">Semua Waktu</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="harian" id="harian" onchange="this.form.submit()" <?= $periode == 'harian' ? 'checked' : '' ?>>
                                <label for="harian" class="radio-label">Hari Ini</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="mingguan" id="mingguan" onchange="this.form.submit()" <?= $periode == 'mingguan' ? 'checked' : '' ?>>
                                <label for="mingguan" class="radio-label">7 Hari</label>
                            </div>

                            <div class="radio-wrapper">
                                <input type="radio" name="periode" value="bulanan" id="bulanan" onchange="this.form.submit()" <?= $periode == 'bulanan' ? 'checked' : '' ?>>
                                <label for="bulanan" class="radio-label">30 Hari</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL RIWAYAT TRANSAKSI -->
            <div class="table-box">
                <h3>Riwayat Transaksi - <?= $label_periode ?></h3>

                <div class="search-wrapper">
                    <form action="" method="GET" class="search-form">
                        <input type="hidden" name="periode" value="<?= htmlspecialchars($periode) ?>">
                        <input type="text" name="keyword" class="input-cari" placeholder="Cari keterangan/sumber..." value="<?= htmlspecialchars($keyword) ?>">
                        <button type="submit" class="btn-cari">Cari</button>
                        <?php if ($keyword !== ''): ?>
                            <a href="?periode=<?= $periode ?>" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </form>

                    <a href="cetak_laporan.php?periode=<?= $periode ?>&keyword=<?= urlencode($keyword) ?>" target="_blank" class="btn-cetak">Cetak PDF</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Total Uang</th>
                            <th>Jenis</th>
                            <th>Sumber</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_transaksi) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_transaksi)): ?>
                                <tr>
                                    <td><?= tanggal_indo($row['tanggal_transaksi']); ?></td>
                                    <td><?= ($row['jumlah'] != '-' && $row['jumlah'] != null) ? number_format((float) $row['jumlah'], 0, ',', '.') : '-' ?></td>
                                    <td><strong>Rp <?= number_format((float) ($row['total_uang'] ?? 0), 0, ',', '.') ?></strong></td>
                                    <td class="<?= ($row['jenis'] == 'Pemasukan') ? 'hijau' : 'merah' ?>"><?= $row['jenis'] ?></td>
                                    <td><?= $row['sumber'] ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                    <td>
                                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>&sumber=<?= strtolower($row['sumber']) ?>" onclick="return confirm('Yakin hapus?')" class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Tidak ada transaksi pada periode ini atau kata kunci tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>