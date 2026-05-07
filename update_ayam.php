<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$query_blok = "SELECT id_blok_kandang, total_ayam FROM blok_kandang";
$daftar_blok = mysqli_query($conn, $query_blok);

if (isset($_POST['simpan'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $id_blok_kandang = mysqli_real_escape_string($conn, $_POST['id_blok_kandang']);
    $jumlah_jual = (int)$_POST['jumlah_ayam'];
    $total_uang = (int)$_POST['total_uang'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $id_petugas = $_SESSION['id_petugas'];

    if (empty($id_blok_kandang)) {
        echo "<script>alert('Pilih Blok Kandang!'); window.history.back();</script>";
        exit;
    }

    // MULAI TRANSACTION (Agar jika satu gagal, semua batal)
    mysqli_begin_transaction($conn);

    try {
        // 1. Cek stok ayam saat ini
        $cek_stok = mysqli_query($conn, "SELECT total_ayam FROM blok_kandang WHERE id_blok_kandang = '$id_blok_kandang'");
        $data_stok = mysqli_fetch_assoc($cek_stok);
        
        if ($data_stok['total_ayam'] < $jumlah_jual) {
            throw new Exception("Stok ayam di blok ini tidak cukup! (Sisa: " . $data_stok['total_ayam'] . ")");
        }

        // 2. Input ke tabel TRANSAKSI
        $q_transaksi = "INSERT INTO transaksi (tanggal_transaksi, jenis_transaksi) VALUES ('$tanggal', 'Pemasukan')";
        mysqli_query($conn, $q_transaksi);
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 3. Input ke tabel PEMASUKAN_AYAM
        $q_pemasukan = "INSERT INTO pemasukan_ayam (id_transaksi, id_blok_kandang, jumlah_ayam, total_uang, keterangan) 
                        VALUES ('$id_transaksi_baru', '$id_blok_kandang', '$jumlah_jual', '$total_uang', '$keterangan')";
        mysqli_query($conn, $q_pemasukan);

        // 4. UPDATE STOK AYAM (Dikurangi)
        $q_update_stok = "UPDATE blok_kandang SET 
                          total_ayam = total_ayam - $jumlah_jual, 
                          id_petugas = '$id_petugas' 
                          WHERE id_blok_kandang = '$id_blok_kandang'";
        mysqli_query($conn, $q_update_stok);

        mysqli_commit($conn);
        echo "<script>alert('Penjualan Berhasil & Stok Ayam Berkurang!'); window.location='menu_transaksi.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_msg = $e->getMessage();
        echo "<script>alert('Gagal: $error_msg'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catat Penjualan Ayam</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="catatan.css">
</head>
<body>
    <div class="modal-card">
        <h2>Catat Penjualan Ayam (Afkir/Lainnya)</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label>Tanggal Transaksi</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Pilih Blok Kandang</label>
                <select name="id_blok_kandang" required>
                    <option value="">-- Pilih Blok --</option>
                    <?php 
                    $id_terpilih = $_GET['id_blok'] ?? '';
                    while ($row = mysqli_fetch_assoc($daftar_blok)): 
                        $selected = ($id_terpilih == $row['id_blok_kandang']) ? 'selected' : '';
                    ?>
                        <option value="<?= $row['id_blok_kandang'] ?>" <?= $selected ?>>
                            Blok <?= $row['id_blok_kandang'] ?> (Sisa: <?= $row['total_ayam'] ?> Ekor)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Ayam yang Dijual (Ekor)</label>
                <input type="number" name="jumlah_ayam" placeholder="Contoh: 10" required>
            </div>

            <div class="form-group">
                <label>Total Uang yang Diterima (Rp)</label>
                <input type="number" name="total_uang" placeholder="Contoh: 500000" required>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan & Update Stok</button>
            </div>
        </form>
    </div>
</body>
</html>