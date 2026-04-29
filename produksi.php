<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur = mysqli_real_escape_string($conn, $_POST['total_telur']);
    

    $id_petugas  = $_SESSION['id_petugas'];

    
    $query_produksi = "INSERT INTO produksi_telur (id_petugas, tanggal, total_telur) 
                       VALUES ('$id_petugas', '$tanggal', '$total_telur')";
    
    if (mysqli_query($conn, $query_produksi)) {
        $id_produksi = mysqli_insert_id($conn);
        
        $query_detail = "INSERT INTO detail_produksi_telur (id_produksi, jumlah_telur_baik) 
                         VALUES ('$id_produksi', '$telur_baik')";
        
        if (mysqli_query($conn, $query_detail)) {
            echo "<script>alert('Data produksi berhasil dicatat!'); window.location='menu_produksi.php';</script>";
        } else {
            echo "Error Detail: " . mysqli_error($conn);
        }
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
                <label for="total_telur">Total Telur (Butir/Kg)</label>
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