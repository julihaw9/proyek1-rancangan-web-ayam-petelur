<?php
session_start();
include("koneksi.php");

// Proteksi halaman login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Mengambil daftar blok kandang untuk pilihan (Dropdown)
// Ini menghindari error Foreign Key saat memasukkan id_blok_kandang
$query_blok = "SELECT id_blok_kandang FROM blok_kandang";
$daftar_blok = mysqli_query($conn, $query_blok);

if (isset($_POST['simpan'])) {
    // 1. Ambil dan amankan input dari form
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $id_blok_kandang = mysqli_real_escape_string($conn, $_POST['id_blok_kandang']);
    $jumlah_ayam = mysqli_real_escape_string($conn, $_POST['jumlah_ayam']);
    $total_uang = mysqli_real_escape_string($conn, $_POST['total_uang']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // 2. ID Petugas (Ambil dari session atau gunakan default 1235 seperti di foto)
    $id_petugas = $_SESSION['id_petugas'] ?? 1235;

    // 3. INSERT ke tabel induk (transaksi)
    // Menambahkan keterangan 'Penjualan Ayam' untuk membedakan dengan telur
    $query_transaksi = "INSERT INTO transaksi (id_petugas, tanggal_transaksi, jenis_transaksi) 
                        VALUES ('$id_petugas', '$tanggal', 'pemasukan')";

    if (mysqli_query($conn, $query_transaksi)) {
        // Ambil ID Transaksi yang baru saja terbuat
        $id_transaksi_baru = mysqli_insert_id($conn);

        // 4. INSERT ke tabel anak (pemasukan_ayam)
        $query_pemasukan = "INSERT INTO pemasukan_ayam (id_transaksi, id_blok_kandang, jumlah_ayam, keterangan, total_uang) 
                            VALUES ('$id_transaksi_baru', '$id_blok_kandang', '$jumlah_ayam', '$keterangan', '$total_uang')";

        if (mysqli_query($conn, $query_pemasukan)) {
            echo "<script>
                    alert('Data Penjualan Ayam Berhasil Disimpan!'); 
                    window.location='menu_transaksi.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Gagal simpan ke pemasukan_ayam: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Gagal simpan ke transaksi: " . mysqli_error($conn) . "');</script>";
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
    <style>
        /* Tambahan styling sedikit untuk select agar konsisten dengan desainmu */
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="modal-card">
        <h2>Catat Penjualan Ayam (Afkir/Lainnya)</h2>

        <form action="" method="POST">

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi</label>
                <div class="input-wrapper">
                    <input type="date" id="tanggal" name="tanggal" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="id_blok_kandang">Blok Kandang (Asal Ayam)</label>
                <div class="input-wrapper">
                    <select name="id_blok_kandang" id="id_blok_kandang" required>
                        <option value="">-- Pilih Blok Kandang --</option>
                        <?php
                        // Pastikan query blok kandang berhasil sebelum di-looping
                        if ($daftar_blok) {
                            while ($row = mysqli_fetch_assoc($daftar_blok)):
                                ?>
                                <option value="<?= $row['id_blok_kandang'] ?>">
                                    Blok Kandang ID: <?= $row['id_blok_kandang'] ?>
                                </option>
                                $id_terpilih = $_GET['id_blok'] ?? '';
                                <option value="<?= $row['id_blok_kandang'] ?>" <?= ($id_terpilih == $row['id_blok_kandang']) ? 'selected' : '' ?>>
                                    Blok Kandang ID: <?= $row['id_blok_kandang'] ?>
                                <?php
                            endwhile;
                        } else {
                            echo '<option value="">Gagal memuat blok kandang</option>';
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
                    <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Penjualan ayam afkir"
                        required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_transaksi.php" class="btn btn-batal"
                    style="text-decoration:none; display:flex; align-items:center; justify-content:center;">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan Data</button>
            </div>

        </form>
    </div>

</body>

</html>