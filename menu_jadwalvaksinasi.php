<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// 🔹 Query dengan LEFT JOIN untuk mengambil status dari tabel status_vaksinasi
// j = jadwal_vaksinasi, s = status_vaksinasi
$query_sql = "
    SELECT 
        j.id_jadwal_vaksinasi, 
        j.id_petugas, 
        j.id_blok_kandang, 
        j.jadwal_vaksinasi, 
        s.status_vaksinasi 
    FROM jadwal_vaksinasi j
    LEFT JOIN status_vaksinasi s ON j.id_jadwal_vaksinasi = s.id_jadwal_vaksinasi
    ORDER BY j.jadwal_vaksinasi ASC
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
        $tgl_vaksin = $row['jadwal_vaksinasi'];
        $status = $row['status_vaksinasi'];
        
        // Hitung selisih hari
        $diff = strtotime($tgl_vaksin) - strtotime($today);
        $selisih = floor($diff / 86400);

        // Pengelompokan Logika
        if ($status == 'selesai') {
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Vaksinasi Ayam</title>
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Tambahan style singkat agar tampilan rapi */
        .warning-box { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeeba; }
        .warning-card { background: white; padding: 10px; margin-top: 10px; border-left: 5px solid #ffc107; border-radius: 4px; }
        .card-container { display: flex; gap: 20px; }
        .card { flex: 1; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .merah { color: red; }
        .hijau { color: green; }
        .btn-hijau { background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <?php $active = 'jadwal'; ?>
    <?php include("sidebar.php"); ?>

    <main>
        <h1>Jadwal Vaksinasi</h1>
        <p>Manajemen jadwal dan pengingat vaksinasi peternakan</p>

        <a href="jadwalvaksinasi.php" class="btn-tambah" style="display:inline-block; margin-bottom:20px; padding:10px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">+ Tambah Jadwal</a>

        <div class="warning-box">
            <h3>Pengingat Vaksinasi Mendesak!</h3>
            <?php 
            $ada_peringatan = false;
            foreach ($data_jadwal as $j): 
                if ($j['selisih'] >= 0 && $j['selisih'] <= 2 && $j['status_vaksinasi'] != 'selesai'): 
                    $ada_peringatan = true;
            ?>
                <div class="warning-card">
                    <strong style="color: #856404;">
                        <?= ($j['selisih'] == 0) ? "HARI INI" : $j['selisih'] . " HARI LAGI"; ?>
                    </strong>
                    <p>Blok Kandang: <?= $j['id_blok_kandang']; ?> | Tanggal: <?= date('d M Y', strtotime($j['jadwal_vaksinasi'])); ?></p>
                    <a href="proses_selesai.php?id=<?= $j['id_jadwal_vaksinasi']; ?>&tgl=<?= $j['jadwal_vaksinasi']; ?>" class="btn-hijau">Tandai Selesai</a>
                </div>
            <?php 
                endif; 
            endforeach; 

            if (!$ada_peringatan) echo "<p>Tidak ada jadwal mendesak saat ini.</p>";
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
                <p>Perlu Perhatian (Terlewat)</p>
                <h2 class="merah"><?= $perlu_perhatian ?></h2>
            </div>
        </div>
    </main>
</div>

</body>
</html>