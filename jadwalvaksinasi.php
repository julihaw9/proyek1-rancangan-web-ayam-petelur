<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $id_blok_array = $_POST['id_blok_kandang'];
    $tanggal = mysqli_real_escape_string($conn, $_POST['jadwal_vaksinasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $berhasil = 0;

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Vaksinasi</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>Tambah Jadwal Vaksinasi</h2>

        <form action="" method="POST">

            <div class="form-group">
                <label for="id_blok_kandang">Pilih Batch (Blok Kandang)</label>
                <div class="input-wrapper">
                    <select name="id_blok_kandang[]" id="id_blok_kandang" multiple required style="height: 120px;">
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
                <small style="display: block; color: #64748b; margin-top: 6px; font-size: 12px;">*Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</small>
            </div>

            <div class="form-group">
                <label for="jadwal_vaksinasi">Tanggal Vaksinasi</label>
                <div class="input-wrapper">
                    <input type="date" id="jadwal_vaksinasi" name="jadwal_vaksinasi" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan (Jenis Vaksin/Catatan)</label>
                <div class="input-wrapper">
                    <input type="text" id="keterangan" name="keterangan" placeholder="Contoh: Vaksin ND-IB Dosis 1" required>
                </div>
            </div>

            <div class="action-buttons">
                <a href="menu_jadwalvaksinasi.php" class="btn btn-batal">Batal</a>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>

        </form>
    </div>

</body>
</html>