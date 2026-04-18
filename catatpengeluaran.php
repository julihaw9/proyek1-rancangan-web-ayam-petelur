<?php
$conn = mysqli_connect("localhost", "root", "", "peternakan_ayam");

if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $total_uang = $_POST['jumlah'];
    $id_petugas = 1235; 

    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'pengeluaran')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        $id_transaksi_baru = mysqli_insert_id($conn);

        $query_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, keterangan, total_uang) 
                              VALUES ('$id_transaksi_baru', '$keterangan', '$total_uang')";
        
        if (mysqli_query($conn, $query_pengeluaran)) {
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
  <title>Formulir Catat Pengeluaran</title>
  <link rel="stylesheet" href="form.css">
</head>
<body>

  <div class="modal-card">
    <h2>Catat Pengeluaran</h2>

    <form id="formPengeluaran" action="" method="POST">
      
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" required value="2026-02-26">
      </div>

      <div class="form-group">
        <label for="kategori">Kategori</label>
        <input type="text" id="kategori" name="kategori" placeholder="Pembelian Pakan, Obat, dll" required>
      </div>

      <div class="form-group">
        <label for="jumlah">Jumlah (Rp)</label>
        <input type="number" id="jumlah" name="jumlah" placeholder="100000" required>
      </div>

      <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <input type="text" id="keterangan" name="keterangan" placeholder="Detail pengeluaran">
      </div>

      <div class="action-buttons">
        <button type="button" class="btn btn-batal" onclick="window.location.href='menu_transaksikeuangan.php';">Batal</button>
        <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
      </div>

    </form>
  </div>
</body>
</html>