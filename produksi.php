<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_telur = mysqli_real_escape_string($conn, $_POST['total_telur']);
    $id_petugas  = $_SESSION['id_petugas'] ?? 1;

    mysqli_begin_transaction($conn);

    try {
        $query_produksi = "INSERT INTO produksi_telur (id_petugas, tanggal, total_telur) 
                           VALUES ('$id_petugas', '$tanggal', '$total_telur')";
        mysqli_query($conn, $query_produksi);
        
        $query_rekap = "INSERT INTO rekap_produksi_telur (tanggal, total_telur_harian, jumlah_inputan) 
                        VALUES ('$tanggal', '$total_telur', 1)
                        ON DUPLICATE KEY UPDATE 
                            total_telur_harian = total_telur_harian + VALUES(total_telur_harian),
                            jumlah_inputan = jumlah_inputan + 1";
        mysqli_query($conn, $query_rekap);
        
        $keterangan_stok = "Penambahan otomatis dari input produksi tanggal " . $tanggal;
        $query_stok = "UPDATE stok_gudang 
                       SET total_stok_telur = total_stok_telur + '$total_telur',
                           keterangan_update = '$keterangan_stok'
                       WHERE id = 1";
        mysqli_query($conn, $query_stok);

        mysqli_commit($conn);

        echo "<script>
                alert('Data Produksi Berhasil Disimpan dan Stok Gudang Telah Diperbarui!'); 
                window.location='menu_produksi.php';
              </script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>
                alert('Gagal menyimpan data! Terjadi kesalahan pada sistem database.'); 
                window.location='menu_produksi.php';
              </script>";
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
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="total_telur">Total Telur (Kg)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_telur" name="total_telur" placeholder="Contoh: 150.5" step="0.1" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_produksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

</body>
</html>