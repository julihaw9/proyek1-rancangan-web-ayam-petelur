<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi input agar aman dari SQL Injection
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur = mysqli_real_escape_string($conn, $_POST['total_telur']);
    $telur_rusak = mysqli_real_escape_string($conn, $_POST['telur_rusak']);
    
    // Logika perhitungan
    $telur_baik  = $total_telur - $telur_rusak; 
    
    // ID Petugas sesuai data di screenshot (1235)
    // Di masa depan, ini bisa diambil dari $_SESSION['id_petugas']
    $id_petugas  = 1235; 

    // 1. Insert ke tabel produksi_telur
    $query_produksi = "INSERT INTO produksi_telur (id_petugas, tanggal, total_telur) 
                       VALUES ('$id_petugas', '$tanggal', '$total_telur')";
    
    if (mysqli_query($conn, $query_produksi)) {
        // Mengambil ID terakhir yang baru saja diinsert
        $id_produksi = mysqli_insert_id($conn);
        
        // 2. Insert ke tabel detail_produksi_telur
        $query_detail = "INSERT INTO detail_produksi_telur (id_produksi, jumlah_telur_baik, jumlah_telur_rusak) 
                         VALUES ('$id_produksi', '$telur_baik', '$telur_rusak')";
        
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

            <div class="form-group">
                <label for="telur_rusak">Telur Rusak (Butir/Kg)</label>
                <input type="number" id="telur_rusak" name="telur_rusak" placeholder="Contoh: 30" step="0.1" required>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="window.location.href='menu_produksi.php';">Batal</button>
                <button type="submit" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

    <script>
        // Validasi sederhana agar telur rusak tidak lebih besar dari total
        document.getElementById('formProduksi').onsubmit = function() {
            const total = parseFloat(document.getElementById('total_telur').value);
            const rusak = parseFloat(document.getElementById('telur_rusak').value);
            
            if (rusak > total) {
                alert("Jumlah telur rusak tidak boleh lebih besar dari total produksi!");
                return false;
            }
            return true;
        };
    </script>
</body>
</html>