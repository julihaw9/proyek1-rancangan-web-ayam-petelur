<?php
session_start();
include("koneksi.php"); // Pastikan file koneksi.php sudah benar

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan amankan input
    $total_ayam    = mysqli_real_escape_string($conn, $_POST['jumlah_ayam']);
    $tanggal_beli  = mysqli_real_escape_string($conn, $_POST['tanggal_pembelian']);
    $kapasitas     = 42; // Sesuai data di screenshot kamu rata-rata 42
    $id_petugas    = $_SESSION['id_petugas'] ?? 1235;

    // Query Insert ke tabel blok_kandang
    $sql = "INSERT INTO blok_kandang (id_petugas, kapasitas_per_blok, total_ayam, tanggal_pembelian_ayam) 
            VALUES ('$id_petugas', '$kapasitas', '$total_ayam', '$tanggal_beli')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data blok kandang berhasil ditambahkan!');
                window.location.href='menu_inventori.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Ayam</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Tambah Blok Ayam Baru</h2>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="jumlah_ayam">Jumlah Ayam (Ekor)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah_ayam" name="jumlah_ayam" placeholder="Contoh: 42" required>
                </div>
            </div>

            <div class="form-group">
                <label for="tanggal_pembelian">Tanggal Pembelian</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_inventori.php" class="btn btn-batal" style="display: flex; justify-content: center; align-items: center; text-decoration: none;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

</body>
</html>