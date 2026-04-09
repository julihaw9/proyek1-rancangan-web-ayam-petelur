<?php
$host = "localhost";
$user = "root";      // Default XAMPP
$pass = "";          // Default XAMPP kosong
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek apakah berhasil terhubung
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>