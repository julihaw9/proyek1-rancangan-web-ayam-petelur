<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 🔹 Query disesuaikan dengan database di gambar (tanpa JOIN karena status sudah ada di tabel ini)
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

// 🔹 Hitung statistik
$today = date('Y-m-d');
$jadwal_mendatang = 0;
$selesai = 0;
$perlu_perhatian = 0;
$data_jadwal = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tgl_vaksin = $row['jadwal']; // Nama kolom di gambar adalah 'jadwal'
        $status = $row['status'];     // Nama kolom di gambar adalah 'status' (0 atau 1)

        // Hitung selisih hari
        $diff = strtotime($tgl_vaksin) - strtotime($today);
        $selisih = floor($diff / 86400);

        // Pengelompokan Logika: 1 = Selesai, 0 = Belum
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

// Query untuk mengambil jadwal yang BELUM dilakukan (status = 0)
// Dan diurutkan dari tanggal paling dekat
$query_mendatang = "
    SELECT id_jadwal_vaksinasi, id_blok_kandang, jadwal, keterangan 
    FROM jadwal_vaksinasi 
    WHERE status = 0 AND jadwal >= CURDATE()
    ORDER BY jadwal ASC
";
$result_mendatang = mysqli_query($conn, $query_mendatang);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Vaksinasi Ayam</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        .warning-box {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }

        .warning-card {
            background: white;
            padding: 10px;
            margin-top: 10px;
            border-left: 5px solid #ffc107;
            border-radius: 4px;
        }

        .card-container {
            display: flex;
            gap: 20px;
        }

        .card {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .merah {
            color: red;
        }

        .hijau {
            color: green;
        }

        .btn-hijau {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php $active = 'jadwal'; ?>
        <?php include("sidebar.php"); ?>

        <main>
            <h1>Jadwal Vaksinasi</h1>
            <p>Manajemen jadwal dan pengingat vaksinasi peternakan</p>

            <a href="jadwalvaksinasi.php" class="btn-tambah"
                style="display:inline-block; margin-bottom:20px; padding:10px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">+
                Tambah Jadwal</a>

            <div class="warning-box">
                <h3>Pengingat Vaksinasi Mendesak!</h3>
                <?php
                $ada_peringatan = false;
                foreach ($data_jadwal as $j):
                    // Jika status 0 (belum) dan jadwal dalam rentang 0-2 hari
                    if ($j['selisih'] >= 0 && $j['selisih'] <= 2 && $j['status'] == 0):
                        $ada_peringatan = true;
                        ?>
                        <div class="warning-card">
                            <strong style="color: #856404;">
                                <?= ($j['selisih'] == 0) ? "HARI INI" : $j['selisih'] . " HARI LAGI"; ?>
                            </strong>
                            <p>Blok Kandang: <?= $j['id_blok_kandang']; ?> | Tanggal:
                                <?= date('d M Y', strtotime($j['jadwal'])); ?></p>
                            <a href="proses_selesai.php?id=<?= $j['id_jadwal_vaksinasi']; ?>" class="btn-hijau">Tandai
                                Selesai</a>
                        </div>
                    <?php
                    endif;
                endforeach;

                if (!$ada_peringatan)
                    echo "<p>Tidak ada jadwal mendesak saat ini.</p>";
                ?>
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
            <section class="list-container"
                style="margin-top: 30px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px; color: #333; border-left: 5px solid #EF892A; padding-left: 15px;">Daftar
                    Vaksinasi Mendatang</h3>

                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                            <th style="padding: 12px;">Tanggal</th>
                            <th style="padding: 12px;">Blok</th>
                            <th style="padding: 12px;">Vaksin / Keterangan</th>
                            <th style="padding: 12px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_mendatang) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_mendatang)): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px; font-weight: 600;">
                                        <?= date('d M Y', strtotime($row['jadwal'])); ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px;">Blok
                                            <?= $row['id_blok_kandang']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px; color: #666;">
                                        <?= $row['keterangan']; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        <a href="proses_selesai.php?id=<?= $row['id_jadwal_vaksinasi']; ?>"
                                            style="background: #28a745; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px;"
                                            onclick="return confirm('Tandai jadwal ini sebagai SELESAI?')">
                                            Selesai
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="padding: 20px; text-align: center; color: #999;">Tidak ada jadwal
                                    vaksinasi dalam waktu dekat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>

</html>