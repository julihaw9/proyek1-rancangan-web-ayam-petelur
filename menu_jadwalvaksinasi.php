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

$query_mendatang = "
    SELECT id_jadwal_vaksinasi, id_blok_kandang, jadwal, keterangan 
    FROM jadwal_vaksinasi 
    WHERE status = 0 AND jadwal >= CURDATE()
    ORDER BY jadwal ASC
";
$result_mendatang = mysqli_query($conn, $query_mendatang);

$sql_cleanup = "DELETE FROM jadwal_vaksinasi 
                WHERE status = 1 
                AND tgl_selesai <= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
mysqli_query($conn, $sql_cleanup);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Vaksinasi Ayam</title>
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="vaksinasi.css">
    <style>
        .batalkan {
            display: inline-block;
            background-color: red;
            color: white;
            padding: 5px 15px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
        }

        .sls {
            display: inline-block;
            background-color: green;
            color: white;
            padding: 5px 15px;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
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

            <a href="jadwalvaksinasi.php" class="btn-tambah">+Tambah Jadwal</a>
            <div class="vaksinasi">
                <div class="warning-box">
                    <h3>Pengingat Vaksinasi Mendesak!</h3>
                    <?php
                    $ada_peringatan = false;
                    foreach ($data_jadwal as $j):
                        // Jika status 0 (belum) dan jadwal dalam rentang 0-3 hari
                        if ($j['selisih'] >= 0 && $j['selisih'] <= 3 && $j['status'] == 0):
                            $ada_peringatan = true;
                            ?>
                            <div class="warning-card">
                                <strong style="color: #856404;">
                                    <?= ($j['selisih'] == 0) ? "HARI INI" : $j['selisih'] . " HARI LAGI"; ?>
                                </strong>
                                <p>Blok Kandang:
                                    <?= $j['id_blok_kandang']; ?> | Tanggal:
                                    <?= date('d M Y', strtotime($j['jadwal'])); ?>
                                </p>
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
                        <h2>
                            <?= $jadwal_mendatang ?>
                        </h2>
                    </div>
                    <div class="card">
                        <p>Selesai</p>
                        <h2 class="hijau">
                            <?= $selesai ?>
                        </h2>
                    </div>
                    <div class="card">
                        <p>Terlewat</p>
                        <h2 class="merah">
                            <?= $perlu_perhatian ?>
                        </h2>
                    </div>
                </div>
                <section class="list-container">

                    <div class="table-box">
                        <h3>Riwayat Vaksinasi (Selesai)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal Selesai</th>
                                    <th>Blok Kandang</th>
                                    <th>Vaksin / Keterangan</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result_mendatang) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_mendatang)): ?>
                                        <tr>
                                            <td>
                                                <?= date('d M Y', strtotime($row['jadwal'])); ?>
                                            </td>
                                            <td>
                                                <span>Blok
                                                    <?= $row['id_blok_kandang']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $row['keterangan']; ?>
                                            </td>
                                            <td>
                                                <a class="batalkan"
                                                    href="proses_batalkan.php?id=<?= $row['id_jadwal_vaksinasi']; ?>"
                                                    onclick="return confirm('Batalkan jadwal ini?')">
                                                    Batalkan
                                                </a>
                                                <a class="sls"
                                                    href="proses_selesai.php?id=<?= $row['id_jadwal_vaksinasi']; ?>"
                                                    onclick="return confirm('Tandai jadwal ini sebagai SELESAI?')">
                                                    Selesai
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Tidak ada
                                            jadwal
                                            vaksinasi dalam waktu dekat.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </section>

            </div>

        </main>
    </div>

</body>

</html>