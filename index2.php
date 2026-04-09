<?php
include 'connection.php';
date_default_timezone_set('Asia/Jakarta');

if(sesion_status() == PHP_SESSION_NONE){
	session_start();
}

if($_SESSION['status'] == 'login'){
	header('location:dashboard.php');
}


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

		<form class="form-group" action="dashboard.html" method="POST">

			<label for="email">Email</label>
			<div class="input-wrapper">
				<input type="text" id="email" placeholder="Masukkan email" required>
			</div>

			<label for="password">Password</label>
			<div class="input-wrapper">
				<input type="password" id="password" placeholder="Masukkan password" required>
			</div>

			<div class="action-buttons">
        		<button type="submit" class="btn btn-simpan">Login</button>
      		</div>

		</form>
	</div>

</body>
</html>