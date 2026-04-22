<?php
session_start();
include("koneksi.php");

// Proteksi halaman login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    // 1. Ambil dan amankan input dari form
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah_telur = mysqli_real_escape_string($conn, $_POST['jumlah_telur']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $total_uang = mysqli_real_escape_string($conn, $_POST['total_uang']); // Mengambil input total uang manual

    // 2. ID Petugas (Ambil dari session atau gunakan default 1235 sesuai database kamu)
    $id_petugas = $_SESSION['id_petugas'] ?? 1235; 

    // 3. INSERT ke tabel induk (transaksi)
    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'Pemasukan')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        // Ambil ID Transaksi yang baru saja terbuat
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 4. INSERT ke tabel anak (pemasukan_telur)
        // Gunakan ID produksi yang sudah pasti ada di database kamu (misal 1013)
        $id_produksi_ref = 1013; 

        $query_pemasukan = "INSERT INTO pemasukan_telur (id_transaksi, id_produksi, jumlah_telur, keterangan, total_uang) 
                            VALUES ('$id_transaksi_baru', '$id_produksi_ref', '$jumlah_telur', '$keterangan', '$total_uang')";
        
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
                <label for="tanggal">Tanggal</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_telur">Jumlah Telur (kg)</label>
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
                <a href="menu_transaksi.php" class="btn btn-batal" style="display: flex; justify-content: center; align-items: center;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>

        </form>
    </div>

</body>
</html>