<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    // Ambil dan amankan input
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $total_uang = mysqli_real_escape_string($conn, $_POST['jumlah']);

    // Ambil id_petugas dari session
    $id_petugas = $_SESSION['id_petugas'] ?? 1; 

    // 1. Simpan ke tabel induk (transaksi)
    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'Pengeluaran')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 2. Simpan ke tabel anak (pengeluaran)
        $query_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, keterangan, total_uang) 
                              VALUES ('$id_transaksi_baru', '$keterangan', '$total_uang')";
        
        if (mysqli_query($conn, $query_pengeluaran)) {
            echo "<script>alert('Data pengeluaran berhasil disimpan!'); window.location='menu_transaksi.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error pengeluaran: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Error transaksi: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pengeluaran</title>
    <link rel="stylesheet" href="form.css"> 
</head>
<body>

    <div class="modal-card">
        <h2>Catat Pengeluaran</h2>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah (Rp)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah" name="jumlah" placeholder="100000" required>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Pembelian Pakan, Obat, dll" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal" style="display: flex; justify-content: center; align-items: center;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>

        </form>
    </div>

</body>
</html>