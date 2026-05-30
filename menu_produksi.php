<?php
// Set zona waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

session_start();
include("koneksi.php");
include("tanggal.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. LOGIKA FILTER PERIODE, TANGGAL, DAN PENCARIAN
$periode = $_GET['periode'] ?? 'bulanan'; // Default ke bulanan
$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

$filter_periode_prod = "";
$filter_periode_trans = "";
$label_periode = "";

// Logika Filter Rentang Tanggal Manual atau Kapsul Periode
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $filter_periode_prod = "pt.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    $filter_periode_trans = "t.tanggal_transaksi BETWEEN '$tgl_awal' AND '$tgl_akhir'";
    $label_periode = "Periode " . tanggal_indo($tgl_awal) . " s/d " . tanggal_indo($tgl_akhir);
    $periode = 'custom';
} else {
    switch ($periode) {
        case 'harian':
            $filter_periode_prod = "DATE(pt.tanggal) = CURDATE()";
            $filter_periode_trans = "DATE(t.tanggal_transaksi) = CURDATE()";
            $label_periode = "Hari Ini";
            break;
        case 'mingguan':
            $filter_periode_prod = "pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $filter_periode_trans = "t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $label_periode = "7 Hari Terakhir";
            break;
        case 'bulanan':
            $filter_periode_prod = "pt.tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            $filter_periode_trans = "t.tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            $label_periode = "30 Hari Terakhir";
            break;
        default:
            $filter_periode_prod = "";
            $filter_periode_trans = "";
            $label_periode = "Semua Waktu";
            break;
    }
}

// Klausa WHERE Dinamis untuk Card Summary
$where_prod_card = $filter_periode_prod ? "WHERE " . $filter_periode_prod : "";
$where_trans_card = $filter_periode_trans ? "WHERE " . $filter_periode_trans : "";

// 2. QUERY SUMMARY CARD
$query_total_prod = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(pt.total_telur) as total 
    FROM `produksi_telur` pt
    $where_prod_card
"));

$query_total_jual = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(tt.jumlah_telur) as total_terjual 
    FROM `telur_terjual` tt
    JOIN transaksi t ON tt.id_transaksi = t.id_transaksi
    $where_trans_card
"));

// AMBIL DATA STOK GUDANG GLOBAL
$query_stok_gudang = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT total_stok_telur, keterangan_update 
    FROM stok_gudang 
    WHERE id = 1
"));

// 3. LOGIKA PENCARIAN RIWAYAT TABEL (Mendukung Pencarian Jumlah Angka & Desimal)
$search_cond = "";
if ($keyword !== '') {
    if (is_numeric($keyword)) {
        $search_cond = "(
            p.nama_petugas LIKE '%$keyword%' 
            OR pt.total_telur = '$keyword'
            OR pt.id_produksi IN (
                SELECT id_produksi 
                FROM pemasukan_telur 
                WHERE keterangan LIKE '%$keyword%'
                   OR jumlah_telur = '$keyword'
            )
        )";
    } else {
        $search_cond = "(
            p.nama_petugas LIKE '%$keyword%' 
            OR CAST(pt.total_telur AS CHAR) LIKE '%$keyword%'
            OR pt.id_produksi IN (
                SELECT id_produksi 
                FROM pemasukan_telur 
                WHERE keterangan LIKE '%$keyword%'
                   OR CAST(jumlah_telur AS CHAR) LIKE '%$keyword%'
            )
        )";
    }
}

// Menggabungkan Filter Periode & Pencarian khusus untuk Tabel Riwayat
$conditions = [];
if ($filter_periode_prod) { $conditions[] = $filter_periode_prod; }
if ($search_cond) { $conditions[] = $search_cond; }

$where_riwayat = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// 4. QUERY RIWAYAT TABEL
$query_riwayat = mysqli_query($conn, "
    SELECT 
        pt.id_produksi,
        pt.tanggal,
        pt.total_telur,
        p.nama_petugas,
        (SELECT SUM(jumlah_telur) 
         FROM telur_terjual tt 
         JOIN transaksi t ON tt.id_transaksi = t.id_transaksi 
         WHERE t.tanggal_transaksi = pt.tanggal) as total_terjual
    FROM produksi_telur pt
    LEFT JOIN petugas p ON pt.id_petugas = p.id_petugas
    $where_riwayat
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
    <style>
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-group-side {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-group-side .btn-tambah, 
        .btn-group-side .btn-update {
            display: inline-flex;  
            align-items: center;
            justify-content: center;
            gap: 6px;              
            padding: 10px 18px;    
            height: 42px;          
            box-sizing: border-box;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            white-space: nowrap;
            color: white;
            transition: opacity 0.2s;
        }

        .btn-group-side .btn-tambah { background-color: #28a745; }
        .btn-group-side .btn-update { background-color: #007bff; }
        .btn-group-side .btn-tambah:hover, .btn-group-side .btn-update:hover { opacity: 0.9; }
        
        .btn-update span.icon-rotasi {
            font-size: 18px;
            line-height: 1;
            display: inline-block;
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
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .clear-filter-btn:hover {
            background-color: #cbd5e1;
        }

        /* SUSUNAN ROW CARD SUMMARY (3 Kolom Seimbang) */
        .card-container {
            width: 100%;
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* CONTAINER BARU KHUSUS UNTUK FILTER AGAR BERDIRI DI BARIS SENDIRI (100%) */
        .filter-container-row {
            width: 100%;
            margin: 10px 0 25px 0;
        }

        .filter-card-custom {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        .radio-tile-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .radio-wrapper { position: relative; }
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
            padding: 10px 20px;
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
        <?php $active = 'produksi'; include("sidebar.php"); ?>
        
        <main>
            <h1>Produksi Telur</h1>
            <p>Kelola dan analisis data aliran masuk serta keluar produksi telur</p>

            <div class="top-action-bar">
                <div class="btn-group-side">
                    <a href="produksi.php" class="btn-tambah">+ Tambah Input Harian</a>
                    <a href="update_stok_telur.php" class="btn-update"><span class="icon-rotasi">&#8635;</span> Koreksi Stok Global</a>
                </div>
            </div>

            

            <div class="card-container">
                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Stok Gudang</p>
                    <h2 style="margin: 15px 0 0 0;"><?= number_format($query_stok_gudang['total_stok_telur'] ?? 0, 1); ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Kg</span></h2>
                    <small style="color: #999; margin-top: 5px; font-size: 11px; display: block; line-height: 1.2;">Ket: <?= htmlspecialchars($query_stok_gudang['keterangan_update'] ?? '-'); ?></small>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Produksi Masuk (<?= $label_periode ?>)</p>
                    <h2 style="margin: 15px 0 0 0; color: #007bff;"><?= number_format($query_total_prod['total'] ?? 0, 1); ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Kg</span></h2>
                </div>

                <div class="card">
                    <p style="margin: 0; color: #666; font-size: 15px;">Total Telur Terjual (<?= $label_periode ?>)</p>
                    <h2 style="margin: 15px 0 0 0; color: #137333;"><?= number_format($query_total_jual['total_terjual'] ?? 0, 1); ?> <span style="font-size: 14px; color: #888; font-weight: normal;">Kg</span></h2>
                </div>
            </div>

            <div class="filter-container-row">
                <div class="filter-card-custom">
                    <p style="margin: 0 0 5px 0; font-size: 15px; font-weight: bold; color: #111;">Pilih Periode Laporan:</p>
                    <form method="GET" action="">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <input type="hidden" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal) ?>">
                        <input type="hidden" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>">
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
                        <label for="keyword">Cari Data</label>
                        <input type="text" id="keyword" name="keyword" placeholder="Cari petugas, jumlah, keterangan..." value="<?= htmlspecialchars($keyword) ?>">
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
                        <a href="menu_produksi.php" class="clear-filter-btn">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-box">
                <h3>Riwayat Produksi (<?= $label_periode ?>)</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Petugas Input</th>
                            <th>Jumlah Masuk</th>
                            <th>Telur Terjual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_riwayat) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query_riwayat)) { ?>
                                <tr>
                                    <td><strong><?= tanggal_indo($row['tanggal']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_petugas'] ?? '-'); ?></td>
                                    <td><strong><?= number_format($row['total_telur'], 1); ?></strong> Kg</td>
                                    <td style="color: #137333;"><strong><?= number_format($row['total_terjual'] ?? 0, 1); ?></strong> Kg</td>
                                    <td>
                                        <a href="hapus_produksi.php?id=<?= $row['id_produksi']; ?>" 
                                           onclick="return confirm('Yakin ingin menghapus data aliran masuk ini?')" 
                                           class="btn-aksi btn-hapus">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #666; padding: 20px;">Tidak ada data ditemukan untuk periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>