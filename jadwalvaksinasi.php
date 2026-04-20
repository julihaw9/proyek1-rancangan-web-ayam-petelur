<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $id_petugas = mysqli_real_escape_string($conn, $_POST['id_petugas']);
    $id_blok    = mysqli_real_escape_string($conn, $_POST['id_blok_kandang']);
    $tanggal    = mysqli_real_escape_string($conn, $_POST['jadwal_vaksinasi']);

    // Gunakan 'id_blok_kandang' atau 'id_blok_kandnag' sesuai database kamu
    $sql = "INSERT INTO jadwal_vaksinasi (id_petugas, id_blok_kandang, jadwal_vaksinasi) 
            VALUES ('$id_petugas', '$id_blok', '$tanggal')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data Berhasil Disimpan'); window.location='menu_jadwalvaksinasi.php';</script>";
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
                <label>Petugas Pelaksana:</label>
                <select name="id_petugas" required>
                    <option value="">-- Pilih Petugas --</option>
                    <?php
                    $q_petugas = mysqli_query($conn, "SELECT * FROM petugas");
                    while ($p = mysqli_fetch_assoc($q_petugas)) {
                        echo "<option value='{$p['id_petugas']}'>{$p['nama_petugas']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Blok Kandang:</label>
                <select name="id_blok_kandang" required>
                    <option value="">-- Pilih Blok --</option>
                    <?php
                    $q_blok = mysqli_query($conn, "SELECT * FROM blok_kandang");
                    while ($b = mysqli_fetch_assoc($q_blok)) {
                        echo "<option value='{$b['id_blok_kandang']}'>Blok {$b['id_blok_kandang']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Vaksinasi:</label>
                <input type="date" name="jadwal_vaksinasi" required>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="location.href='menu_jadwalvaksinasi.php';">Batal</button>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>
        </div>
    </form>

</body>
</html>