<?php
include ("koneksi.php");

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 

    $cek_email = mysqli_query($conn, "SELECT * FROM petugas WHERE email='$email'");
    
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah digunakan, silakan gunakan email lain!');</script>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO petugas (nama_petugas, email, password) VALUES ('$nama', '$email', '$password')");

        if ($query) {
            echo "<script>
                    alert('Registrasi Berhasil! Silakan Login.');
                    window.location.href='index.php';
                  </script>";
        } else {
            echo "<script>alert('Registrasi Gagal!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Petugas</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>

    <div class="modal-card">
        <h2>REGISTRASI</h2>

        <form action="registrasi.php" method="POST">

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <div class="input-wrapper">
                    <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="Masukkan email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-simpan" name="register">Daftar</button>
            </div>
            
            <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #64748b;">
                Sudah punya akun? <a href="index.php" style="text-decoration: none; color: #f0861c; font-weight: bold;">Login di sini</a>
            </p>

        </form>
    </div>

</body>
</html>