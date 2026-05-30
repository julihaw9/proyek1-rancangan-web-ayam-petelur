<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$query_blok = "SELECT id_blok_kandang, nama_blok, total_ayam, keterangan FROM blok_kandang";
$daftar_blok = mysqli_query($conn, $query_blok);

if (isset($_POST['simpan'])) {
    $id_blok_kandang      = mysqli_real_escape_string($conn, $_POST['id_blok_kandang'] ?? '');
    $nama_blok_baru       = mysqli_real_escape_string($conn, $_POST['nama_blok'] ?? '');
    $keterangan_blok_baru = mysqli_real_escape_string($conn, $_POST['keterangan_blok'] ?? ''); 
    $tanggal              = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
    $total_biaya          = (int) ($_POST['total_uang'] ?? 0);
    $keterangan_transaksi = mysqli_real_escape_string($conn, $_POST['keterangan_transaksi'] ?? ''); 
    $id_petugas           = $_SESSION['id_petugas'];

    if (empty($id_blok_kandang)) {
        echo "<script>alert('Pilih Blok Kandang!'); window.history.back();</script>";
        exit;
    }

    $query_cek = "SELECT nama_blok, total_ayam, keterangan FROM blok_kandang WHERE id_blok_kandang = '$id_blok_kandang'";
    $hasil_cek = mysqli_query($conn, $query_cek);
    $data_lama = mysqli_fetch_assoc($hasil_cek);

    if (isset($_POST['jumlah_ayam']) && $_POST['jumlah_ayam'] !== '' && (int)$_POST['jumlah_ayam'] > 0) {
        $jumlah_baru = (int)$_POST['jumlah_ayam'];
        $buat_transaksi = true; 
    } else {
        $jumlah_baru = (int)$data_lama['total_ayam']; 
        $buat_transaksi = false; 
    }

    if (empty($nama_blok_baru)) {
        $nama_blok_baru = $data_lama['nama_blok'];
    }

    if (empty($keterangan_blok_baru)) {
        $keterangan_blok_baru = $data_lama['keterangan'];
    }

    mysqli_begin_transaction($conn);

    try {
        if ($buat_transaksi) {
            $q_transaksi = "INSERT INTO transaksi (tanggal_transaksi, jenis_transaksi) VALUES ('$tanggal', 'Pengeluaran')";
            mysqli_query($conn, $q_transaksi);
            $id_transaksi_baru = mysqli_insert_id($conn);

            $q_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, keterangan, total_uang, jumlah) 
                              VALUES ('$id_transaksi_baru', '$keterangan_transaksi', '$total_biaya', '$jumlah_baru')";
            mysqli_query($conn, $q_pengeluaran);
        }

        $q_update_stok = "UPDATE blok_kandang SET 
                          nama_blok = '$nama_blok_baru',
                          keterangan = '$keterangan_blok_baru',
                          total_ayam = $jumlah_baru, 
                          id_petugas = '$id_petugas' 
                          WHERE id_blok_kandang = '$id_blok_kandang'";
        mysqli_query($conn, $q_update_stok);

        mysqli_commit($conn);
        echo "<script>alert('Data berhasil diperbarui!'); window.location='menu_inventori.php';</script>";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Ayam & Blok</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Update Ayam & Blok</h2>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="id_blok_kandang">Pilih Blok Kandang</label>
                <div class="input-wrapper">
                    <select name="id_blok_kandang" id="id_blok_kandang" required>
                        <option value="">-- Pilih Blok --</option>
                        <?php while ($row = mysqli_fetch_assoc($daftar_blok)): ?>
                            <option value="<?= $row['id_blok_kandang'] ?>">
                                Blok <?= $row['id_blok_kandang'] ?> - <?= htmlspecialchars($row['nama_blok'] ?? '') ?> (Stok: <?= $row['total_ayam'] ?> Ekor)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="nama_blok">Nama Blok Baru</label>
                <div class="input-wrapper">
                    <input type="text" id="nama_blok" name="nama_blok" placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan_blok">Keterangan / Kondisi Blok</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan_blok" name="keterangan_blok" placeholder="Contoh: Tahap Karantina, Pembersihan, dll.">
                </div>
            </div>

            <hr style="margin: 25px 0; border: 0; border-top: 1px dashed #cbd5e1;">
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px; line-height: 1.4; text-align: center;">
                *Isi bagian finansial di bawah ini <strong>hanya jika</strong> jumlah ayam berubah atau ada pengadaan barang baru.
            </p>

            <div class="form-group">
                <label for="tanggal">Tanggal Masuk / Beli</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_ayam">Jumlah Ayam Baru (Ekor)</label>
                <div class="input-wrapper">
                    <input type="number" id="jumlah_ayam" name="jumlah_ayam" placeholder="Kosongkan jika tidak ada perubahan">
                </div>
            </div>

            <div class="form-group">
                <label for="total_uang">Total Biaya Pembelian (Rp)</label>
                <div class="input-wrapper">
                    <input type="number" id="total_uang" name="total_uang" placeholder="Contoh: 2000000">
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan_transaksi">Keterangan Transaksi Finansial</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan_transaksi" name="keterangan_transaksi" placeholder="Contoh: Pembelian bibit DOC ayam tambahan">
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_inventori.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Perubahan</button>
            </div>

        </form>
    </div>

</body>
</html>