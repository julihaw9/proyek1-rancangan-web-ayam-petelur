<?php
session_start();
include("koneksi.php");

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM petugas WHERE email='$email'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Lakukan verifikasi password (jika menggunakan plain teks seperti pada kode Anda)
        if ($password == $data['password']) {
            $_SESSION['login'] = true;
            
            $_SESSION['id_petugas'] = $data['id_petugas'];
            $_SESSION['nama'] = $data['nama_petugas'];

            echo "<script>
                    alert('Login berhasil!');
                    window.location.href='dashboard.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
	<link rel="stylesheet" href="form.css">
</head>

<body>

	<div class="login-container">
		<h2>LOGIN</h2>

		<form class="form-group" action="index.php" method="POST">

			<label for="email">Email</label>
			<div class="input-wrapper">
				<input type="text" id="email" name="email" placeholder="Masukkan email" required>
			</div>

			<label for="password">Password</label>
			<div class="input-wrapper">
				<input type="password" id="password" name="password" placeholder="Masukkan password" required>
			</div>

			<div class="action-buttons">
				<button type="submit" class="btn btn-simpan" name="login">Login</button>
			</div>

			<p style="text-align: center; margin-top: 15px; font-size: 14px;">
				Belum punya akun? <a href="registrasi.php"
					style="color: #4e73df; text-decoration: none; font-weight: bold;">Daftar Sekarang</a>
			</p>

		</form>
	</div>

</body>

</html>