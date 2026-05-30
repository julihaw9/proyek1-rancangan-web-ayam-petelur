<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$query_blok = "SELECT id_blok_kandang FROM blok_kandang";
$daftar_blok = mysqli_query($conn, $query_blok);

if (isset($_POST['simpan'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $id_blok_kandang = mysqli_real_escape_string($conn, $_POST['id_blok_kandang']);
    $jumlah_ayam = mysqli_real_escape_string($conn, $_POST['jumlah_ayam']);
    $total_uang = mysqli_real_escape_string($conn, $_POST['total_uang']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    if (empty($id_blok_kandang)) {
        echo "<script>alert('Error: Silakan pilih Blok Kandang terlebih dahulu!'); window.history.back();</script>";
        exit;
    }

    $id_petugas = $_SESSION['id_petugas'] ?? 1;

    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'pemasukan')";

    if (mysqli_query($conn, $query_transaksi)) {
        $id_transaksi_baru = mysqli_insert_id($conn);

        $query_pemasukan = "INSERT INTO pemasukan_ayam (id_transaksi, id_blok_kandang, jumlah_ayam, keterangan, total_uang) 
                            VALUES ('$id_transaksi_baru', '$id_blok_kandang', '$jumlah_ayam', '$keterangan', '$total_uang')";

        if (mysqli_query($conn, $query_pemasukan)) {
            $query_update_stok = "UPDATE blok_kandang 
                                  SET total_ayam = total_ayam - $jumlah_ayam 
                                  WHERE id_blok_kandang = '$id_blok_kandang'";

            mysqli_query($conn, $query_update_stok);

            echo "<script>
                    alert('Data Penjualan Berhasil Disimpan!'); 
                    window.location='menu_transaksi.php';
                  </script>";
            exit();
        } else {
            mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi_baru'");
            echo "Error Pemasukan: " . mysqli_error($conn);
        }
    } else {
        echo "Error Transaksi: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Penjualan Ayam</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Catat Penjualan Ayam<br><span style="font-size: 15px; color: #64748b; font-weight: normal;">(Afkir / Lainnya)</span></h2>

        <form action="" method="POST">

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="id_blok_kandang">Pilih Batch (Blok Kandang)</label>
                <div class="input-wrapper">
                    <select name="id_blok_kandang" id="id_blok_kandang" required>
                        <option value="">-- Pilih Blok --</option>
                        <?php
                        $id_terpilih = $_GET['id_blok'] ?? '';

                        while ($row = mysqli_fetch_assoc($daftar_blok)) {
                            $selected = ($id_terpilih == $row['id_blok_kandang']) ? 'selected' : '';
                            echo "<option value='{$row['id_blok_kandang']}' $selected>Blok Kandang: {$row['id_blok_kandang']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_ayam">Jumlah Ayam (Ekor)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah_ayam" name="jumlah_ayam" placeholder="Contoh: 50" required>
                </div>
            </div>

            <div class="form-group">
                <label for="total_uang">Total Pendapatan (Rp)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_uang" name="total_uang" placeholder="Contoh: 2500000" required>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Penjualan ayam afkir" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

</body>
</html>