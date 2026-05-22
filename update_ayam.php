<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Ambil daftar blok untuk pilihan di <select>
$query_blok = "SELECT id_blok_kandang, nama_blok, total_ayam, keterangan FROM blok_kandang";
$daftar_blok = mysqli_query($conn, $query_blok);

if (isset($_POST['simpan'])) {
    $id_blok_kandang    = mysqli_real_escape_string($conn, $_POST['id_blok_kandang'] ?? '');
    $nama_blok_baru     = mysqli_real_escape_string($conn, $_POST['nama_blok'] ?? '');
    $keterangan_blok    = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
    $tanggal            = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
    $total_biaya        = (int) ($_POST['total_uang'] ?? 0);
    $keterangan_transaksi = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');
    $id_petugas         = $_SESSION['id_petugas'];

    if (empty($id_blok_kandang)) {
        echo "<script>alert('Pilih Blok Kandang!'); window.history.back();</script>";
        exit;
    }

    // Ambil data blok saat ini dari database untuk mendapatkan data lama jika form dikosongkan
    $query_cek = "SELECT nama_blok, total_ayam, keterangan FROM blok_kandang WHERE id_blok_kandang = '$id_blok_kandang'";
    $hasil_cek = mysqli_query($conn, $query_cek);
    $data_lama = mysqli_fetch_assoc($hasil_cek);

    // LOGIKA TOTAL AYAM (Tetap menggunakan override sesuai struktur Anda)
    if (isset($_POST['jumlah_ayam']) && $_POST['jumlah_ayam'] !== '' && (int)$_POST['jumlah_ayam'] > 0) {
        $jumlah_baru = (int)$_POST['jumlah_ayam'];
        $buat_transaksi = true; 
    } else {
        $jumlah_baru = (int)$data_lama['total_ayam']; // Tetap pakai jumlah lama jika input kosong
        $buat_transaksi = false; 
    }

    // Jika nama_blok dikosongkan di form, tetap gunakan nama_blok yang lama
    if (empty($nama_blok_baru)) {
        $nama_blok_baru = $data_lama['nama_blok'];
    }

    // Jika keterangan_blok dikosongkan di form, tetap gunakan keterangan yang lama
    if (empty($keterangan_blok)) {
        $keterangan_blok = $data_lama['keterangan'];
    }

    // MULAI TRANSACTION
    mysqli_begin_transaction($conn);

    try {
        // Input ke tabel TRANSAKSI & PENGELUARAN jika ada penambahan ayam
        if ($buat_transaksi) {
            $q_transaksi = "INSERT INTO transaksi (tanggal_transaksi, jenis_transaksi) VALUES ('$tanggal', 'Pengeluaran')";
            mysqli_query($conn, $q_transaksi);
            $id_transaksi_baru = mysqli_insert_id($conn);

            $q_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, keterangan, total_uang, jumlah) 
                              VALUES ('$id_transaksi_baru', '$keterangan_transaksi', '$total_biaya', '$jumlah_baru')";
            mysqli_query($conn, $q_pengeluaran);
        }

        // UPDATE DATA KANDANG (Menambahkan pembaruan keterangan_blok dan mempertahankan logika total_ayam)
        $q_update_stok = "UPDATE blok_kandang SET 
                          nama_blok = '$nama_blok_baru',
                          keterangan = '$keterangan_blok',
                          total_ayam = $jumlah_baru, 
                          id_petugas = '$id_petugas' 
                          WHERE id_blok_kandang = '$id_blok_kandang'";
        mysqli_query($conn, $q_update_stok);

        mysqli_commit($conn);
        echo "<script>alert('Data berhasil diperbarui!'); window.location='menu_produksi.php';</script>";

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
    <title>Update Data Ayam & Blok</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="catatan.css">
</head>

<body>
    <div class="modal-card">
        <h2>Update Data Ayam & Blok</h2>
        <form action="" method="POST">
            
            <div class="form-group">
                <label>Pilih Blok Kandang</label>
                <select name="id_blok_kandang" required>
                    <option value="">-- Pilih Blok --</option>
                    <?php while ($row = mysqli_fetch_assoc($daftar_blok)): ?>
                        <option value="<?= $row['id_blok_kandang'] ?>">
                            Blok <?= $row['id_blok_kandang'] ?> - <?= htmlspecialchars($row['nama_blok'] ?? '') ?> 
                            (Status: <?= htmlspecialchars($row['keterangan'] ?? 'Normal') ?>, Stok: <?= $row['total_ayam'] ?> Ekor)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- INPUT UNTUK NAMA BLOK -->
            <div class="form-group">
                <label>Nama Blok</label>
                <input type="text" name="nama_blok" placeholder="Contoh: Kandang A, Kandang B (Kosongkan jika tidak diubah)">
            </div>

            <!-- INPUT BARU: KETERANGAN/KONDISI BLOK (CONTOH: KARANTINA) -->
            <div class="form-group">
                <label>Keterangan/Kondisi Blok</label>
                <input type="text" name="keterangan_blok" placeholder="Contoh: Blok ini karantina, Tahap Pembersihan, dll.">
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px dashed #ccc;">
            <p style="font-size: 12px; color: #666;">*Isi bagian keuangan di bawah ini jika jumlah total ayam berubah atau ada pengeluaran pembelian baru.</p>

            <div class="form-group">
                <label>Tanggal Masuk/Beli</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label>Jumlah Ayam Baru (Ekor)</label>
                <input type="number" name="jumlah_ayam" placeholder="Kosongkan jika tidak ada perubahan jumlah ayam">
            </div>

            <div class="form-group">
                <label>Total Biaya Pembelian (Rp)</label>
                <input type="number" name="total_uang" placeholder="Contoh: 2000000">
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan Transaksi</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Detail pengeluaran finansial">
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_produksi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>

</html>