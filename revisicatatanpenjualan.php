<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $tanggal_input = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jumlah_jual   = mysqli_real_escape_string($conn, $_POST['jumlah_telur']);
    $keterangan    = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $total_uang    = mysqli_real_escape_string($conn, $_POST['total_uang']);
    $id_petugas    = $_SESSION['id_petugas'] ?? 1;

    $query_cek_stok = "SELECT total_stok_telur FROM stok_gudang WHERE id = 1";
    $result_stok = mysqli_query($conn, $query_cek_stok);
    $data_stok = mysqli_fetch_assoc($result_stok);
    $stok_gudang_sekarang = $data_stok['total_stok_telur'] ?? 0;

    if ($jumlah_jual > $stok_gudang_sekarang) {
        echo "<script>
                alert('Gagal: Stok di gudang tidak cukup! Stok saat ini: " . number_format($stok_gudang_sekarang, 1) . " Kg'); 
                window.history.back();
              </script>";
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                            VALUES ('$id_petugas', '$tanggal_input', 'Pemasukan')";
        mysqli_query($conn, $query_transaksi);
        
        $id_transaksi_baru = mysqli_insert_id($conn);

        $query_jumlah_jual = "INSERT INTO telur_terjual (id_transaksi, jumlah_telur, keterangan, total_uang) 
                              VALUES ('$id_transaksi_baru', '$jumlah_jual', '$keterangan', '$total_uang')";
        mysqli_query($conn, $query_jumlah_jual);
        
        $keterangan_stok = "Pengurangan otomatis dari penjualan tanggal " . $tanggal_input;
        $query_update_gudang = "UPDATE stok_gudang 
                                SET total_stok_telur = total_stok_telur - '$jumlah_jual',
                                    keterangan_update = '$keterangan_stok'
                                WHERE id = 1";
        mysqli_query($conn, $query_update_gudang);

        $query_rekap = "INSERT INTO rekap_produksi_telur (tanggal, total_telur_harian, jumlah_inputan) 
                        VALUES ('$tanggal_input', -'$jumlah_jual', 1)
                        ON DUPLICATE KEY UPDATE 
                            total_telur_harian = total_telur_harian - VALUES(total_telur_harian)";
        mysqli_query($conn, $query_rekap);

        mysqli_commit($conn);

        echo "<script>
                alert('Data Penjualan Berhasil Disimpan dan Stok Gudang Berhasil Dikurangi!'); 
                window.location='menu_transaksi.php';
              </script>";
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>
                alert('Gagal menyimpan transaksi penjualan telur.'); 
                window.location='menu_transaksi.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Penjualan Telur</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Catat Penjualan</h2>

        <form action="" method="POST">

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_telur">Jumlah Telur Terjual (kg)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah_telur" name="jumlah_telur" placeholder="Contoh: 10" step="0.1" required>
                </div>
            </div>

            <div class="form-group">
                <label for="total_uang">Total Uang (Rp)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_uang" name="total_uang" placeholder="Contoh: 250000" required>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Detail penjualan" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Penjualan</button>
            </div>

        </form>
    </div>

</body>
</html>