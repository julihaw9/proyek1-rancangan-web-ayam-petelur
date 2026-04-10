<?php
session_start(); // WAJIB
include ("koneksi.php");

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = mysqli_query($conn, "SELECT * FROM petugas WHERE email='$email'");
    $data = mysqli_fetch_assoc($query);

    // cek apakah data ada
    if ($data) {

        if ($password == $data['password']) {

            $_SESSION['login'] = true;
            $_SESSION['nama'] = $data['nama_petugas'];

            header("Location: dashboard.php");
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

		</form>
	</div>

</body>
</html>