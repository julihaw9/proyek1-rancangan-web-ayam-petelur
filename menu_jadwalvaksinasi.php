<?php
$conn = mysqli_connect("localhost", "root", "", "peternakan_ayam");

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM status_vaksinasi WHERE id_jadwal_vaksinasi = $id");
    mysqli_query($conn, "DELETE FROM jadwal_vaksinasi WHERE id_jadwal_vaksinasi = $id");
    header("Location: menu_jadwalvaksinasi.php");
    exit();
}

if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    $tgl_hari_ini = date('Y-m-d');
    mysqli_query($conn, "UPDATE status_vaksinasi SET status_vaksinasi='Selesai', tanggal_vaksinasi='$tgl_hari_ini' WHERE id_jadwal_vaksinasi = $id");
    header("Location: menu_jadwalvaksinasi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Vaksinasi</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
<div class="container">
    <aside>
        <h3>Prima Farm</h3>
        <hr>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="menu_inventori.php">Inventori</a></li>
                <li><a href="menu_produksi.php">Produksi</a></li>
                <li><a href="menu_transaksikeuangan.php">Transaksi Keuangan</a></li>
                <li><a href="menu_jadwalvaksinasi.php" class="active">Jadwal Vaksinasi</a></li>
                <li><a href="pengaturan.php">Pengaturan</a></li>
            </ul>
        </nav>
        <a href="index.php" class="logout-button">Logout</a>
    </aside>

    <main>
        <h1>Jadwal Vaksinasi</h1>
        <p>Pengingat dan manajemen jadwal vaksin ayam</p>
        <a href="tambah_jadwal.php" class="btn-tambah">+ Tambah Jadwal</a>

        <div class="warning-box">
            <h3>Daftar Pengingat</h3>
            <?php
            $sql = "SELECT j.id_jadwal_vaksinasi, j.id_blok_kandang, j.jadwal_vaksinasi, s.status_vaksinasi 
                    FROM jadwal_vaksinasi j
                    LEFT JOIN status_vaksinasi s ON j.id_jadwal_vaksinasi = s.id_jadwal_vaksinasi
                    ORDER BY j.jadwal_vaksinasi ASC";
            
            $query = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $tgl_tujuan = new DateTime($row['jadwal_vaksinasi']);
                    $tgl_skrg = new DateTime();
                    $diff = $tgl_skrg->diff($tgl_tujuan);
                    $sisa = $diff->format("%r%a");
            ?>
                <div class="warning-card">
                    <span class="hari">
                        <?php 
                            if ($row['status_vaksinasi'] == 'Selesai') echo "SELESAI";
                            else if ($sisa < 0) echo "TERLAMBAT";
                            else echo $sisa . " HARI LAGI";
                        ?>
                    </span>
                    <p><strong>Blok: <?= htmlspecialchars($row['id_blok_kandang']) ?></strong></p>
                    <p><?= date('d F Y', strtotime($row['jadwal_vaksinasi'])) ?></p>
                    
                    <div style="margin-top:10px;">
                        <?php if($row['status_vaksinasi'] != 'Selesai'): ?>
                            <button class="btn-hijau" onclick="location.href='?selesai=<?= $row['id_jadwal_vaksinasi'] ?>'">Tandai Selesai</button>
                        <?php endif; ?>
                        <button onclick="if(confirm('Hapus?')) location.href='?hapus=<?= $row['id_jadwal_vaksinasi'] ?>'" style="background:none; border:none; color:red; cursor:pointer; font-size:12px; margin-left:10px;">Hapus</button>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo "<p style='padding:20px; color:gray;'>Belum ada data di database.</p>";
            }
            ?>
        </div>
    </main>
</div>
</body>
</html>