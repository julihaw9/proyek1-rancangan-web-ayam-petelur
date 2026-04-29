<?php
session_start();
include("koneksi.php");

// Proteksi halaman login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    // 1. Ambil input dari form
    $tanggal_input = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah_jual = mysqli_real_escape_string($conn, $_POST['jumlah_telur']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $total_uang = mysqli_real_escape_string($conn, $_POST['total_uang']);

    // 2. Ambil ID Produksi TERBARU secara otomatis dari database
    // Ini dilakukan agar kita tidak perlu mengisi ID secara manual atau lewat dropdown
    $query_cari_id = "SELECT id_produksi FROM produksi_telur ORDER BY id_produksi DESC LIMIT 1";
    $result_id = mysqli_query($conn, $query_cari_id);
    $data_produksi = mysqli_fetch_assoc($result_id);

    if (!$data_produksi) {
        echo "<script>alert('Gagal: Tidak ada data di tabel produksi_telur. Isi data produksi dulu!'); window.history.back();</script>";
        exit;
    }

    $id_produksi_otomatis = $data_produksi['id_produksi'];
    $id_petugas = $_SESSION['id_petugas'] ?? 1; 

    // 3. Simpan ke tabel transaksi (induk)
    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal_input', 'Pemasukan')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 4. Simpan ke tabel pemasukan_telur (anak) menggunakan ID Produksi otomatis
        $query_pemasukan = "INSERT INTO pemasukan_telur (id_transaksi, id_produksi, jumlah_telur, keterangan, total_uang) 
                            VALUES ('$id_transaksi_baru', '$id_produksi_otomatis', '$jumlah_jual', '$keterangan', '$total_uang')";
        
        if (mysqli_query($conn, $query_pemasukan)) {
            echo "<script>
                    alert('Data Penjualan Berhasil Disimpan!'); 
                    window.location='menu_transaksi.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Gagal simpan ke pemasukan_telur: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal simpan ke transaksi: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Penjualan Telur</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Catat Penjualan</h2>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_telur">Jumlah Telur Terjual (kg)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah_telur" name="jumlah_telur" placeholder="Contoh: 10" step="0.1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="total_uang">Total Uang (Rp)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_uang" name="total_uang" placeholder="Contoh: 250000" required>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Detail penjualan" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Penjualan</button>
            </div>

        </form>
    </div>

</body>
</html>