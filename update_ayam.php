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
    // Menggunakan null coalescing untuk menghindari Deprecated Warning
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
    $id_blok_kandang = mysqli_real_escape_string($conn, $_POST['id_blok_kandang'] ?? '');
    $jumlah_baru = (int) ($_POST['jumlah_ayam'] ?? 0);
    $total_biaya = (int) ($_POST['total_uang'] ?? 0);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? 'Pembelian Ayam Baru');
    $id_petugas = $_SESSION['id_petugas'];

    if (empty($id_blok_kandang)) {
        echo "<script>alert('Pilih Blok Kandang!'); window.history.back();</script>";
        exit;
    }

    // MULAI TRANSACTION
    mysqli_begin_transaction($conn);

    try {
        // 1. Input ke tabel TRANSAKSI (Jenis: Pengeluaran karena kita beli/bayar)
        $q_transaksi = "INSERT INTO transaksi (tanggal_transaksi, jenis_transaksi) VALUES ('$tanggal', 'Pengeluaran')";
        mysqli_query($conn, $q_transaksi);
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 2. Input ke tabel PENGELUARAN
        $q_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, keterangan, total_uang, jumlah) 
                          VALUES ('$id_transaksi_baru', '$keterangan', '$total_biaya', '$jumlah_baru')";
        mysqli_query($conn, $q_pengeluaran);

        // 3. UPDATE STOK AYAM (Logika Penambahan: total_ayam + jumlah_baru)
        // Perhatikan bagian total_ayam = total_ayam + $jumlah_baru
        $q_update_stok = "UPDATE blok_kandang SET 
                          total_ayam = $jumlah_baru, 
                          id_petugas = '$id_petugas' 
                          WHERE id_blok_kandang = '$id_blok_kandang'";
        mysqli_query($conn, $q_update_stok);

        mysqli_commit($conn);
        echo "<script>alert('Stok ayam berhasil ditambah!'); window.location='menu_produksi.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memperbarui data!'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Update Data Ayam</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="catatan.css">
</head>

<body>
    <div class="modal-card">
        <h2>Update Data Ayam</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label>Tanggal Masuk/Beli</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Pilih Blok Kandang (Tujuan)</label>
                <select name="id_blok_kandang" required>
                    <option value="">-- Pilih Blok --</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($daftar_blok)):
                        ?>
                        <option value="<?= $row['id_blok_kandang'] ?>">
                            Blok <?= $row['id_blok_kandang'] ?> (Sisa Saat Ini: <?= $row['total_ayam'] ?> Ekor)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Ayam Baru (Ekor)</label>
                <input type="number" name="jumlah_ayam" placeholder="Masukkan jumlah ayam yang dibeli" required>
            </div>

            <div class="form-group">
                <label>Total Biaya Pembelian (Rp)</label>
                <input type="number" name="total_uang" placeholder="Contoh: 2000000" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Detail penjualan" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_produksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Tambah ke Stok</button>
            </div>
        </form>
    </div>
</body>

</html>