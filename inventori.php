<?php
// Konfigurasi Koneksi
$host = "localhost";
$user = "root"; // Sesuaikan username database kamu
$pass = "";     // Sesuaikan password database kamu
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek apakah tombol simpan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kandang = $_POST['nama_kandang'];
    $jumlah_ayam  = $_POST['jumlah_ayam'];
    $umur         = $_POST['umur'];
    $status       = $_POST['status'];

    // Query Insert
    $sql = "INSERT INTO ayam (nama_kandang, jumlah_ayam, umur, status) 
            VALUES ('$nama_kandang', '$jumlah_ayam', '$umur', '$status')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data berhasil disimpan!');
                window.location.href='menu_inventori.html';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulir Tambah Data Ayam</title>
  <link rel="stylesheet" href="form.css">
</head>
<body>

  <div class="modal-card">
    <h2>Tambah Data Ayam</h2>

    <form id="formTambahAyam" action="menu_inventori.html" method="POST">
      
      <div class="form-group">
        <label for="nama_kandang">Nama Kandang</label>
        <input type="text" id="nama_kandang" name="nama_kandang" placeholder="Contoh: Kandang E" required>
      </div>

      <div class="form-group">
        <label for="jumlah_ayam">Jumlah Ayam</label>
        <input type="number" id="jumlah_ayam" name="jumlah_ayam" placeholder="300" required>
      </div>

      <div class="form-group">
        <label for="umur">Umur (Minggu)</label>
        <input type="number" id="umur" name="umur" placeholder="20" required>
      </div>

      <div class="form-group">
        <label for="status">Status</label>
        <input type="text" id="status" name="status" placeholder="Produktif" required>
      </div>

      <div class="action-buttons">
        <button type="button" class="btn btn-batal" onclick="window.location.href='menu_inventori.html';">Batal</button>
        <button type="submit" class="btn btn-simpan" onclick="window.location.href='menu_inventori.html';">Simpan</button>
      </div>

    </form>
  </div>

</body>
</html>