<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Cek apakah parameter ID ada di URL
// if (!isset($_GET['id']) || empty($_GET['id'])) {
//     echo "<script>
//             alert('ID data tidak ditemukan!'); 
//             window.location='menu_produksi.php';
//           </script>";
//     exit;
// }

// $id_produksi = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data lama berdasarkan ID untuk ditampilkan di form
$query_ambil = "SELECT * FROM produksi WHERE id_produksi = '$id_produksi'";
$result = mysqli_query($conn, $query_ambil);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan di database
if (!$data) {
    echo "<script>
            alert('Data tidak ditemukan dalam database!'); 
            window.location='menu_produksi.php';
          </script>";
    exit;
}

// Proses jika tombol Simpan Perubahan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur = mysqli_real_escape_string($conn, $_POST['total_telur']);
    $keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan']); // Tambahan catatan alasan update
    
    // Query Update Data
    // Catatan: Pastikan nama kolom 'id_produksi' sesuai dengan primary key di tabel produksi Anda
    $query_update = "UPDATE produksi SET 
                        tanggal = '$tanggal', 
                        total_telur = '$total_telur',
                        keterangan = '$keterangan' 
                     WHERE id_produksi = '$id_produksi'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Data Produksi Telur Berhasil Diperbarui!'); 
                window.location='menu_produksi.php';
              </script>";
    } else {
        echo "Error Update: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Produksi Telur - Prima Farm</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Update / Koreksi Produksi Telur</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
            Silakan ubah jumlah total telur jika terdapat telur pecah atau selisih data.
        </p>

        <form id="formUpdateProduksi" action="" method="POST">
            
            <div class="form-group">
                <label for="tanggal">Tanggal Pengambilan</label>
                <!-- Menampilkan tanggal yang sebelumnya tersimpan -->
                <input type="date" id="tanggal" name="tanggal" required value="<?= $data['tanggal'] ?>">
            </div>

            <div class="form-group">
                <label for="total_telur">Total Telur Baru (Kg)</label>
                <!-- Menampilkan total telur yang sebelumnya tersimpan -->
                <input type="number" id="total_telur" name="total_telur" placeholder="Contoh: 1450" step="0.1" required value="<?= $data['total_telur'] ?>">
            </div>

            <div class="form-group">
                <label for="keterangan">Alasan Perubahan / Catatan</label>
                <!-- Input tambahan untuk mencatat alasan (misal: "Ada yang pecah 5kg") -->
                <textarea id="keterangan" name="keterangan" placeholder="Contoh: Telur pecah di gudang sebanyak 5 Kg" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?= isset($data['keterangan']) ? $data['keterangan'] : '' ?></textarea>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="window.location.href='menu_produksi.php';">Batal</button>
                <button type="submit" class="btn btn-simpan" style="background-color: #28a745;">Simpan Perubahan</button>
            </div>

        </form>
    </div>

</body>
</html>