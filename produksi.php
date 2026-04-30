<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur = mysqli_real_escape_string($conn, $_POST['total_telur']);
    
    if (isset($_SESSION['id_petugas'])) {
        $id_petugas = $_SESSION['id_petugas'];
    } else {
        $id_petugas = 1;
    }

    // Query 1: Input ke tabel produksi_telur
    // Hapus tanda kutip satu ('') pada $id_petugas jika kolom di DB bertipe INT
    $query_produksi = "INSERT INTO produksi_telur (id_petugas, tanggal, total_telur) 
                       VALUES ('$id_petugas', '$tanggal', '$total_telur')";
    
    if (mysqli_query($conn, $query_produksi)) {
        echo "<script>
                alert('Data Produksi Telur Berhasil Disimpan!'); 
                window.location='menu_produksi.php';
              </script>";
    } else {
        echo "Error Produksi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Produksi Telur - Prima Farm</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Catat Produksi Telur</h2>

        <form id="formProduksi" action="" method="POST">
            
            <div class="form-group">
                <label for="tanggal">Tanggal Pengambilan</label>
                <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="total_telur">Total Telur (Kg)</label>
                <input type="number" id="total_telur" name="total_telur" placeholder="Contoh: 1500" step="0.1" required>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="window.location.href='menu_produksi.php';">Batal</button>
                <button type="submit" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

</body>
</html>