<?php
$conn = mysqli_connect("localhost", "root", "", "peternakan_ayam");

if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $kandang = $_POST['kandang'];
    $id_petugas = 1; 

    $query_jadwal = "INSERT INTO jadwal_vaksinasi (id_petugas, id_blok_kandang, jadwal_vaksinasi) 
                     VALUES ('$id_petugas', '$kandang', '$tanggal')";
    
    if (mysqli_query($conn, $query_jadwal)) {
        $last_id = mysqli_insert_id($conn);

        $query_status = "INSERT INTO status_vaksinasi (id_jadwal_vaksinasi, tanggal_vaksinasi, status_vaksinasi) 
                         VALUES ('$last_id', '$tanggal', 'Mendatang')";
        
        mysqli_query($conn, $query_status);

        header("Location: menu_jadwalvaksinasi.php");
        exit();
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
                <label for="tanggal">Tanggal Vaksinasi</label>
                <input type="date" id="tanggal" name="tanggal" required>
            </div>

            <div class="form-group">
                <label for="kandang">ID Blok Kandang</label>
                <div class="input-wrapper">
                    <input type="text" id="kandang" name="kandang" placeholder="Contoh: BK01" required>
                </div>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn btn-batal" onclick="location.href='menu_jadwalvaksinasi.php';">Batal</button>
                <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
            </div>
        </div>
    </form>
</body>
</html>