<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    // Ambil data array dari select multiple
    $id_blok_array = $_POST['id_blok_kandang'];
    $tanggal = mysqli_real_escape_string($conn, $_POST['jadwal_vaksinasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $berhasil = 0;

    // Lakukan perulangan untuk setiap blok yang dipilih
    foreach ($id_blok_array as $id_blok) {
        $id_blok = mysqli_real_escape_string($conn, $id_blok);

        $sql = "INSERT INTO jadwal_vaksinasi (id_blok_kandang, jadwal, status, keterangan) 
                VALUES ('$id_blok', '$tanggal', 0, '$keterangan')";

        if (mysqli_query($conn, $sql)) {
            $berhasil++;
        }
    }

    if ($berhasil > 0) {
        echo "<script>alert('$berhasil Jadwal Berhasil Disimpan'); window.location='menu_jadwalvaksinasi.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Jadwal Vaksinasi</title>
    <link rel="stylesheet" href="form.css">
</head>

<body>

    <form action="" method="POST">
        <div class="modal-card">
            <h2>Tambah Jadwal Vaksinasi</h2>
            <div class="form-group">
                <label for="id_blok_kandang">Pilih Batch (Blok Kandang)</label>
                <div class="input-wrapper">
                    <select name="id_blok_kandang[]" id="id_blok_kandang" multiple required style="height: 100px;">
                        <?php
                        $q_blok = mysqli_query($conn, "SELECT * FROM blok_kandang");
                        while ($b = mysqli_fetch_assoc($q_blok)):
                            ?>
                            <option value="<?= $b['id_blok_kandang']; ?>">
                                Blok <?= $b['id_blok_kandang']; ?> (Tersisa: <?= $b['total_ayam']; ?> Ekor)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <small>*Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</small>
            </div>

            <div class="form-group">
                <label>Tanggal Vaksinasi:</label>
                <input type="date" name="jadwal_vaksinasi" required>
            </div>

            <div class="form-group">
                <label>Keterangan (Jenis Vaksin/Catatan):</label>
                <input type="text" name="keterangan" placeholder="Contoh: Vaksin ND-IB Dosis 1" required>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal"
                    onclick="location.href='menu_jadwalvaksinasi.php';">Batal</button>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>
        </div>
    </form>

</body>

</html>