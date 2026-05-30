<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Cek apakah parameter TANGGAL ada di URL (Sesuai strategi rekap harian)
if (!isset($_GET['tanggal']) || empty($_GET['tanggal'])) {
    echo "<script>
            alert('Tanggal data tidak ditemukan!'); 
            window.location='menu_produksi.php';
          </script>";
    exit;
}

$tanggal_url = mysqli_real_escape_string($conn, $_GET['tanggal']);

// Ambil data lama dari tabel REKAP berdasarkan TANGGAL untuk ditampilkan di form
$query_ambil = "SELECT * FROM rekap_produksi_telur WHERE tanggal = '$tanggal_url'";
$result = mysqli_query($conn, $query_ambil);
$data = mysqli_fetch_assoc($result);

// Jika data rekap tanggal tersebut tidak ditemukan di database
if (!$data) {
    echo "<script>
            alert('Data rekap untuk tanggal tersebut tidak ditemukan!'); 
            window.location='menu_produksi.php';
          </script>";
    exit;
}

// Proses jika tombol Simpan Perubahan ditekan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $tanggal_baru      = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur_baru  = mysqli_real_escape_string($conn, $_POST['total_telur']);
    $keterangan        = mysqli_real_escape_string($conn, $_POST['keterangan']); 
    
    // Query Update Data ke tabel rekapitasi
    $query_update = "UPDATE rekap_produksi_telur SET 
                        tanggal = '$tanggal_baru', 
                        total_telur_harian = '$total_telur_baru',
                        keterangan = '$keterangan' 
                     WHERE tanggal = '$tanggal_url'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Rekap Produksi Telur Berhasil Diperbarui!'); 
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
        <h2>Update / Koreksi Total Produksi Telur</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
            Silakan ubah total akumulasi telur pada tanggal ini jika terdapat selisih data atau koreksi global.
        </p>

        <form id="formUpdateProduksi" action="" method="POST">
            
            <div class="form-group">
                <label for="tanggal">Tanggal Produksi</label>
                <input type="date" id="tanggal" name="tanggal" required value="<?= htmlspecialchars($data['tanggal'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="total_telur">Total Telur Harian Baru (Kg)</label>
                <input type="number" id="total_telur" name="total_telur" placeholder="Contoh: 1450" step="0.01" required value="<?= htmlspecialchars($data['total_telur_harian'] ?? '0') ?>">
            </div>

            <div class="form-group">
                <label for="keterangan">Alasan Perubahan / Catatan Rekap</label>
                <textarea id="keterangan" name="keterangan" placeholder="Contoh: Penyesuaian total karena ada telur pecah harian" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="window.location.href='menu_produksi.php';">Batal</button>
                <button type="submit" class="btn btn-simpan" style="background-color: #28a745;">Simpan Perubahan</button>
            </div>

        </form>
    </div>

</body>
</html>