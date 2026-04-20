<?php
$conn = mysqli_connect("localhost", "root", "", "peternakan_ayam");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $total_telur = $_POST['total_telur'];
    $telur_rusak = $_POST['telur_rusak'];
    $telur_baik = $total_telur - $telur_rusak; 
    $id_petugas = 1235; 

    $query_produksi = "INSERT INTO produksi_telur (id_petugas, tanggal, total_telur) 
                       VALUES ('$id_petugas', '$tanggal', '$total_telur')";
    
    if (mysqli_query($conn, $query_produksi)) {
        $id_produksi = mysqli_insert_id($conn);
        
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
  <title>Formulir Catat Produksi Telur</title>
  <link rel="stylesheet" href="form.css">
</head>
<body>

  <div class="modal-card">
    <h2>Catat Produksi Telur</h2>

    <form id="formProduksi" action="" method="POST">
      
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
      </div>

      <div class="form-group">
        <label for="total_telur">Total Telur (Butir)</label>
        <input type="number" id="total_telur" name="total_telur" placeholder="Contoh: 1500" required>
      </div>

      <div class="form-group">
        <label for="telur_rusak">Telur Rusak (Butir)</label>
        <input type="number" id="telur_rusak" name="telur_rusak" placeholder="Contoh: 30" required>
      </div>

      <div class="action-buttons">
        <button type="button" class="btn btn-batal" onclick="window.location.href='menu_produksi.php';">Batal</button>
        <button type="submit" class="btn btn-simpan">Simpan</button>
      </div>

    </form>
  </div>
</body>
</html>