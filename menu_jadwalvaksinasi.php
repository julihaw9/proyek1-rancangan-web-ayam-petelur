<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 1. TANGKAP FILTER JADWAL VIA RADIO BUTTON
$status_filter = $_GET['status_filter'] ?? 'belum'; 

// 2. QUERY UTAMA UNTUK MENGHITUNG STATISTIK (CARD OVERVIEW)
$query_sql = "
    SELECT 
        id_jadwal_vaksinasi, 
        id_blok_kandang, 
        jadwal, 
        status,
        keterangan
    FROM jadwal_vaksinasi 
    ORDER BY jadwal ASC
";
$result = mysqli_query($conn, $query_sql);

$today = date('Y-m-d');
$jadwal_mendatang = 0;
$selesai = 0;
$perlu_perhatian = 0;
$data_jadwal = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tgl_vaksin = $row['jadwal']; 
        $status = $row['status'];     

        $diff = strtotime($tgl_vaksin) - strtotime($today);
        $selisih = floor($diff / 86400);

        if ($status == 1) {
            $selesai++;
        } elseif ($selisih < 0) {
            $perlu_perhatian++;
        } else {
            $jadwal_mendatang++;
        }

        $row['selisih'] = $selisih;
        $data_jadwal[] = $row;
    }
}

// 3. LOGIKA QUERY UNTUK MENYARING DATA TABEL BERDASARKAN SELEKSI RADIO BUTTON
if ($status_filter == 'selesai') {
    $query_tabel = "SELECT * FROM jadwal_vaksinasi WHERE status = 1 ORDER BY jadwal DESC";
} elseif ($status_filter == 'terlewat') {
    $query_tabel = "SELECT * FROM jadwal_vaksinasi WHERE status = 0 AND jadwal < CURDATE() ORDER BY jadwal ASC";
} elseif ($status_filter == 'semua') {
    $query_tabel = "SELECT * FROM jadwal_vaksinasi ORDER BY jadwal DESC";
} else {
    $query_tabel = "SELECT * FROM jadwal_vaksinasi WHERE status = 0 AND jadwal >= CURDATE() ORDER BY jadwal ASC";
}
$result_tabel = mysqli_query($conn, $query_tabel);

// 4. CLEANUP DATA LAMA
$sql_cleanup = "DELETE FROM jadwal_vaksinasi 
                WHERE status = 1 
                AND jadwal <= DATE_SUB(CURDATE(), INTERVAL 45 DAY)";
$sql_cleanup_2 = "DELETE FROM jadwal_vaksinasi 
                WHERE status = 0 
                AND jadwal <= DATE_SUB(CURDATE(), INTERVAL 45 DAY)";
mysqli_query($conn, $sql_cleanup);
mysqli_query($conn, $sql_cleanup_2);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Vaksinasi Ayam - Prima Farm</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Action Bar Kanan Atas */
        .top-action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-tambah-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            height: 42px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            box-sizing: border-box;
            transition: opacity 0.2s;
        }
        .btn-tambah-custom:hover { opacity: 0.9; }

        /* Container Pengingat Ringkas Slim */
        .warning-box {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            max-width: 920px;
        }
        .warning-box h3 {
            margin: 0 0 12px 0;
            color: #856404;
            font-size: 15px;
            font-weight: bold;
        }
        .warning-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .warning-card {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ffeeba;
        }
        .warning-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .time-badge {
            font-size: 11px;
            font-weight: bold;
            color: #b06000;
            background: #feefc3;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .time-badge.hari-ini {
            background: #fce8e6;
            color: #c5221f;
        }
        .warning-text {
            font-size: 14px;
            color: #495057;
        }

        /* Card Statistik Overview */
        .card-container {
            display: flex;
            gap: 20px;
            margin: 20px 0 25px 0;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            flex: 1;
        }

        /* Container Box Form Filter */
        .filter-container-box {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .card p { margin: 0; color: #666; font-size: 14px; }
        .card h2 { margin: 15px 0 0 0; font-size: 28px; }
        .card h2.hijau { color: #28a745; }
        .card h2.merah { color: #dc3545; }

        /* ======================================================= */
        /* PERBAIKAN: GAYA TOMBOL KAPSUL RAPAT (SAMA SEPERTI INVENTORI) */
        /* ======================================================= */
        .radio-tile-group {
            display: inline-flex; /* Membuat kontainer hanya selebar isinya */
            gap: 10px;            /* Jarak antar tombol kapsul */
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
            z-index: 2;
        }

        .radio-label {
            display: block;
            padding: 10px 20px; /* Padding sisi kiri & kanan konstan */
            background: #e5e7eb;
            color: #4b5563;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        /* Warna saat aktif (Oranye Prima Farm) */
        .radio-wrapper input[type="radio"]:checked + .radio-label {
            background-color: #f0861c;
            color: white;
            box-shadow: 0 2px 4px rgba(240, 134, 28, 0.2);
        }

        /* Efek hover opsi tidak aktif */
        .radio-wrapper input[type="radio"]:hover:not(:checked) + .radio-label {
            background-color: #d1d5db;
        }

        /* Tombol Aksi Di Dalam Tabel */
        .btn-aksi-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }
        .btn-tabel {
            padding: 6px 12px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
            transition: opacity 0.2s;
        }
        .btn-tabel:hover { opacity: 0.9; }
        .btn-tabel.batalkan { background-color: #dc3545; }
        .btn-tabel.sls { background-color: #28a745; }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.success { background-color: #e6f4ea; color: #137333; }
        .badge.danger { background-color: #fce8e6; color: #c5221f; }
        .badge.warning { background-color: #feefc3; color: #b06000; }
    </style>
</head>

<body>

    <div class="container">
        <?php $active = 'jadwal'; include("sidebar.php"); ?>

        <main>
            <h1>Jadwal Vaksinasi</h1>
            <p>Manajemen jadwal dan pengingat vaksinasi peternakan</p>

            <div class="top-action-bar">
                <a href="jadwalvaksinasi.php" class="btn-tambah-custom">+ Tambah Jadwal</a>
            </div>

            <div class="warning-box">
                <h3>Pengingat Vaksinasi Mendesak!</h3>
                <div class="warning-grid">
                    <?php
                    $ada_peringatan = false;
                    foreach ($data_jadwal as $j):
                        if ($j['selisih'] >= 0 && $j['selisih'] <= 3 && $j['status'] == 0):
                            $ada_peringatan = true;
                            $is_hari_ini = ($j['selisih'] == 0) ? 'hari-ini' : '';
                            ?>
                            <div class="warning-card">
                                <div class="warning-left">
                                    <span class="time-badge <?= $is_hari_ini ?>">
                                        <?= ($j['selisih'] == 0) ? "HARI INI" : $j['selisih'] . " HARI LAGI"; ?>
                                    </span>
                                    <span class="warning-text">
                                        Vaksinasi <strong>Blok <?= $j['id_blok_kandang']; ?></strong> &bull; Rencana: <strong><?= date('d M Y', strtotime($j['jadwal'])); ?></strong>
                                    </span>
                                </div>
                                <a href="proses_selesai.php?id=<?= $j['id_jadwal_vaksinasi']; ?>" class="btn-tabel sls">Tandai Selesai</a>
                            </div>
                            <?php
                        endif;
                    endforeach;

                    if (!$ada_peringatan) {
                        echo "<p style='margin:0; color: #6c757d; font-size: 14px;'>Tidak ada jadwal kritis/mendesak dalam 3 hari ke depan.</p>";
                    }
                    ?>
                </div>
            </div>

            <div class="card-container">
                <div class="card">
                    <p>Jadwal Mendatang</p>
                    <h2><?= $jadwal_mendatang ?></h2>
                </div>
                <div class="card">
                    <p>Selesai</p>
                    <h2 class="hijau"><?= $selesai ?></h2>
                </div>
                <div class="card">
                    <p>Terlewat</p>
                    <h2 class="merah"><?= $perlu_perhatian ?></h2>
                </div>
            </div>

            <div class="filter-container-box">
                <p style="margin: 0 0 12px 0; font-size: 14px; font-weight: bold; color: #111;">Filter Tampilan Jadwal:</p>
                <form method="GET" action="">
                    <div class="radio-tile-group">
                        <div class="radio-wrapper">
                            <input type="radio" name="status_filter" value="belum" id="belum" onchange="this.form.submit()" <?= $status_filter == 'belum' ? 'checked' : '' ?>>
                            <label for="belum" class="radio-label">Belum Selesai</label>
                        </div>

                        <div class="radio-wrapper">
                            <input type="radio" name="status_filter" value="selesai" id="selesai" onchange="this.form.submit()" <?= $status_filter == 'selesai' ? 'checked' : '' ?>>
                            <label for="selesai" class="radio-label">Selesai</label>
                        </div>

                        <div class="radio-wrapper">
                            <input type="radio" name="status_filter" value="terlewat" id="terlewat" onchange="this.form.submit()" <?= $status_filter == 'terlewat' ? 'checked' : '' ?>>
                            <label for="terlewat" class="radio-label">Terlewat</label>
                        </div>

                        <div class="radio-wrapper">
                            <input type="radio" name="status_filter" value="semua" id="semua" onchange="this.form.submit()" <?= $status_filter == 'semua' ? 'checked' : '' ?>>
                            <label for="semua" class="radio-label">Semua Jadwal</label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-box">
                <h3>
                    Daftar Vaksinasi 
                    (<?= $status_filter == 'selesai' ? 'Selesai' : ($status_filter == 'terlewat' ? 'Terlewat' : ($status_filter == 'semua' ? 'Semua Jadwal' : 'Belum Selesai')) ?>)
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Blok Kandang</th>
                            <th>Vaksin / Keterangan</th>
                            <th style="text-align: center;">Status Realisasi</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_tabel) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_tabel)): 
                                $diff = strtotime($row['jadwal']) - strtotime($today);
                                $selisih = floor($diff / 86400);
                                
                                if ($row['status'] == 1) {
                                    $badge_text = "Selesai"; $badge_class = "success";
                                } elseif ($selisih < 0) {
                                    $badge_text = "Terlewat"; $badge_class = "danger";
                                } else {
                                    $badge_text = "Terjadwal"; $badge_class = "warning";
                                }
                            ?>
                                <tr>
                                    <td><strong><?= date('d M Y', strtotime($row['jadwal'])); ?></strong></td>
                                    <td><span>Blok <?= $row['id_blok_kandang']; ?></span></td>
                                    <td><?= htmlspecialchars($row['keterangan']); ?></td>
                                    <td style="text-align: center;">
                                        <span class="badge <?= $badge_class ?>"><?= $badge_text ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($row['status'] == 0): ?>
                                            <div class="btn-aksi-group">
                                                <a class="btn-tabel batalkan"
                                                   href="proses_batalkan.php?id=<?= $row['id_jadwal_vaksinasi']; ?>"
                                                   onclick="return confirm('Batalkan jadwal ini?')">Hapus</a>
                                                <a class="btn-tabel sls"
                                                   href="proses_selesai.php?id=<?= $row['id_jadwal_vaksinasi']; ?>"
                                                   onclick="return confirm('Tandai jadwal ini sebagai SELESAI?')">Selesai</a>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #6c757d; font-size: 13px; font-style: italic;">Tidak ada aksi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #888; padding: 20px;">
                                    Tidak ada data jadwal ditemukan untuk kategori ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>