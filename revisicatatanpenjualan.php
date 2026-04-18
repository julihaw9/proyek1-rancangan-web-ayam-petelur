<?php
$conn = mysqli_connect("localhost", "root", "", "peternakan_ayam");

if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah_telur = $_POST['jumlah_telur'];
    $harga = $_POST['harga'];
    $total_uang = $jumlah_telur * $harga;
    $id_petugas = 1235; 

    // Langkah 1: Input ke tabel transaksi
    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'pemasukan')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        $id_transaksi_baru = mysqli_insert_id($conn);

        // Langkah 2: Input ke tabel pemasukan_telur
        $query_pemasukan = "INSERT INTO pemasukan_telur (id_transaksi, jumlah_telur, keterangan, total_uang) 
                            VALUES ('$id_transaksi_baru', '$jumlah_telur', 'Penjualan Telur', '$total_uang')";
        
        if (mysqli_query($conn, $query_pemasukan)) {
            header("Location: menu_transaksikeuangan.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulir Catat Penjualan</title>
  <link rel="stylesheet" href="form.css">
</head>
<body>

  <div class="modal-card">
    <h2>Catat Penjualan</h2>

    <form id="formPenjualan" action="" method="POST">
      
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required value="2026-02-26">
      </div>

      <div class="form-group">
        <label for="jumlah_telur">Jumlah Telur (kg)</label>
        <input type="number" id="jumlah_telur" name="jumlah_telur" placeholder="Contoh: 16" step="0.1" required>
      </div>

      <div class="form-group">
        <label for="harga">Harga per Butir (Rp)</label>
        <input type="number" id="harga" name="harga" placeholder="Contoh: 16000" required>
      </div>

      <div class="action-buttons">
        <button type="button" class="btn btn-batal" onclick="window.location.href='menu_transaksikeuangan.php';">Batal</button>
        <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
      </div>

    </form>
  </div>
</body>
</html>