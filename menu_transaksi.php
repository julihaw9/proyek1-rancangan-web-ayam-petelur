<?php
// Set zona waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

session_start();
include("koneksi.php");
include("tanggal.php");

mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. TANGKAP FILTER PERIODE, TANGGAL, DAN PENCARIAN
$periode = $_GET['periode'] ?? 'semua';
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$keyword = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');

$where_clause = "WHERE 1=1";
$label_periode = "Semua Waktu";

// Logika Filter Rentang Tanggal Manual atau Kapsul Periode
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $where_clause = "WHERE t.tanggal_transaksi BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    $label_periode = "Periode " . tanggal_indo($tgl_awal) . " s/d " . tanggal_indo($tgl_akhir);
    $periode = 'custom'; 
} else {
    switch ($periode) {
        case 'harian':
            // PERBAIKAN: Spasi setelah WHERE aman, fungsi DATE() dipanggil murni tanpa t. di depannya
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
            $where_clause = "WHERE 1=1";
            $label_periode = "Semua Waktu";
            break;
    }
}

// 2. SUMMARY KEUANGAN (Menyesuaikan dengan nama tabel riil database: pemasukan_telur)
$query_summary = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(pa.total_uang), 0) FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause) AS total_ayam,
        (SELECT COALESCE(SUM(pt.total_uang), 0) FROM pemasukan_telur pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause) AS total_telur,
        (SELECT COALESCE(SUM(p.total_uang), 0) FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause) AS total_pengeluaran
");
$res_sum = mysqli_fetch_assoc($query_summary);

$total_pemasukan = $res_sum['total_ayam'] + $res_sum['total_telur'];
$total_pengeluaran = $res_sum['total_pengeluaran'];
$profit = $total_pemasukan - $total_pengeluaran;

// 3. RIWAYAT TRANSAKSI DENGAN SUB-QUERY SEARCH KEYWORD
$search_query_ayam = "";
$search_query_telur = "";
$search_query_pengeluaran = "";

if (!empty($keyword)) {
    $search_query_ayam = " AND (pa.keterangan LIKE '%$keyword%' OR 'Ayam' LIKE '%$keyword%')";
    $search_query_telur = " AND (pt.keterangan LIKE '%$keyword%' OR 'Telur' LIKE '%$keyword%')";
    $search_query_pengeluaran = " AND (p.keterangan LIKE '%$keyword%' OR 'Pengeluaran' LIKE '%$keyword%')";
    $label_periode .= " (Hasil Pencarian: '$keyword')";
}

// Menyesuaikan query kedua UNION menggunakan pemasukan_telur dan pt.jumlah_telur
$sql_history = "
    SELECT t.tanggal_transaksi, pa.jumlah_ayam AS jumlah, pa.total_uang, 'Pemasukan' AS jenis, 'Ayam' AS sumber, pa.keterangan, t.id_transaksi
    FROM pemasukan_ayam pa JOIN transaksi t ON pa.id_transaksi = t.id_transaksi $where_clause $search_query_ayam
    UNION ALL
    SELECT t.tanggal_transaksi, pt.jumlah_telur AS jumlah, pt.total_uang, 'Pemasukan' AS jenis, 'Telur' AS sumber, pt.keterangan, t.id_transaksi
    FROM pemasukan_telur pt JOIN transaksi t ON pt.id_transaksi = t.id_transaksi $where_clause $search_query_telur
    UNION ALL
    SELECT t.tanggal_transaksi, p.jumlah AS jumlah, p.total_uang, 'Pengeluaran' AS jenis, 'Pengeluaran' AS sumber, p.keterangan, t.id_transaksi
    FROM pengeluaran p JOIN transaksi t ON p.id_transaksi = t.id_transaksi $where_clause $search_query_pengeluaran
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
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 25px;
            margin-top: 10px;
        }

        .btn-group-side {
            display: flex;
            gap: 10px;
            align-items: center; 
        }

        .btn-group-side .btn-tambah, 
        .btn-group-side .btn-update,
        .btn-group-side .btn-cetak {
            display: inline-flex !important;  
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;              
            padding: 0 18px !important;    
            height: 42px !important;          
            line-height: 42px !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            text-decoration: none !important;
            border-radius: 8px !important;
            font-weight: bold !important;
            font-size: 14px !important;
            white-space: nowrap !important;
            color: white !important;
            transition: opacity 0.2s;
            border: none !important;
        }

        .btn-group-side .btn-tambah { background-color: #28a745 !important; }
        .btn-group-side .btn-update { background-color: #dc3545 !important; }
        .btn-group-side .btn-cetak { background-color: #007bff !important; }
        
        .btn-group-side .btn-tambah:hover, 
        .btn-group-side .btn-update:hover,
        .btn-group-side .btn-cetak:hover { 
            opacity: 0.85 !important; 
        }

        /* === Search & Filter Grid Bar === */
        .search-filter-wrapper {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .search-filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: flex-end;
        }

        .filter-input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-input-group label {
            font-size: 13px;
            font-weight: bold;
            color: #4b5563;
        }

        .filter-input-group input[type="text"],
        .filter-input-group input[type="date"] {
            padding: 10px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            color: #334155;
            width: 100%;
            box-sizing: border-box;
        }

        .filter-input-group input:focus {
            border-color: #f0861c;
        }

        .search-submit-btn {
            background-color: #f0861c;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
            height: 42px;
        }

        .search-submit-btn:hover {
            background-color: #d97310;
        }

        .clear-filter-btn {
            background-color: #e2e8f0;
            color: #475569;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.2s;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .clear-filter-btn:hover {
            background-color: #cbd5e1;
        }

        /* === Penyesuaian Komponen Card dan Filter === */
        .card-container {
            width: 100%;
            display: flex;
            gap: 20px;
            margin: 20px 0;
            align-items: stretch; 
            flex-wrap: wrap;
        }

        .card, .filter-card-custom {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            min-width: 240px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .filter-card-custom {
            flex: 1.4;
            min-width: 320px;
        }

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

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .status-badge.pemasukan {
            background-color: #e6f4ea;
            color: #137333;
        }

        .status-badge.pengeluaran {
            background-color: #fce8e6;
            color: #c5221f;
        }

        .btn-aksi.btn-hapus {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php $active = 'transaksi'; include("sidebar.php"); ?>

        <main>
            <h1>Analisis Keuangan</h1>
            <p>Kelola aliran kas masuk dan keluar operasional Prima Farm</p>

            <div class="top-action-bar">
                <div class="btn-group-side">
                    <a href="revisicatatanpenjualan.php" class="btn-tambah">+ Jual Telur</a>
                    <a href="catatan_penjualan_ayam.php" class="btn-tambah">+ Jual Ayam</a>
                    <a href="catatpengeluaran.php" class="btn-update">+ Pengeluaran</a>
                    <a href="cetak_laporan.php?periode=<?= $periode ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&keyword=<?= urlencode($keyword) ?>" target="_blank" class="btn-cetak">Cetak PDF</a>
                </div>
            </div>

            <div class="card-container">
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Pendapatan</p>
                    <h2 style="margin: 15px 0 0 0; color: #137333;">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Biaya</p>
                    <h2 style="margin: 15px 0 0 0; color: #c5221f;">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Profit</p>
                    <h2 style="margin: 15px 0 0 0; color: <?= $profit >= 0 ? '#007bff' : '#c5221f' ?>;">
                        Rp <?= number_format($profit, 0, ',', '.') ?>
                    </h2>
                </div>

                <div class="filter-card-custom">
                    <p style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #111;">Pilih Periode Laporan:</p>
                    <form method="GET" action="">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
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

            <div class="search-filter-wrapper">
                <form method="GET" action="" class="search-filter-form">
                    <input type="hidden" name="periode" value="<?= htmlspecialchars($periode) ?>">
                    
                    <div class="filter-input-group">
                        <label for="keyword">Cari Transaksi</label>
                        <input type="text" id="keyword" name="keyword" placeholder="Ketik keterangan / sumber..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    </div>

                    <div class="filter-input-group">
                        <label for="tgl_awal">Tanggal Awal</label>
                        <input type="date" id="tgl_awal" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal) ?>">
                    </div>

                    <div class="filter-input-group">
                        <label for="tgl_akhir">Tanggal Akhir</label>
                        <input type="date" id="tgl_akhir" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>">
                    </div>

                    <button type="submit" class="search-submit-btn">Cari</button>
                    
                    <?php if(!empty($keyword) || !empty($tgl_awal) || !empty($tgl_akhir)): ?>
                        <a href="menu_transaksi.php" class="clear-filter-btn">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-box">
                <h3>Riwayat Transaksi (<?= $label_periode ?>)</h3>
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
                            <?php while ($row = mysqli_fetch_assoc($query_transaksi)): 
                                $class_badge = (strtolower($row['jenis']) == 'pemasukan') ? 'pemasukan' : 'pengeluaran';
                            ?>
                                <tr>
                                    <td><strong><?= tanggal_indo($row['tanggal_transaksi']); ?></strong></td>
                                    <td><?= ($row['jumlah'] != '-' && $row['jumlah'] != null) ? number_format((float)$row['jumlah'], 0, ',', '.') : '-' ?></td>
                                    <td><strong>Rp <?= number_format((float)($row['total_uang'] ?? 0), 0, ',', '.') ?></strong></td>
                                    <td>
                                        <span class="status-badge <?= $class_badge ?>">
                                            <?= $row['jenis'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['sumber']) ?></td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                    <td>
                                        <a href="hapus.php?id=<?= $row['id_transaksi'] ?>&sumber=<?= strtolower($row['sumber']) ?>"
                                           onclick="return confirm('Yakin hapus transaksi ini?')" class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; color: #666; padding: 20px;">Tidak ada transaksi yang cocok atau ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>