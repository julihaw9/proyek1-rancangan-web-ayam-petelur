<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "peternakan_ayam";

$conn = mysqli_connect($host, $user, $pass, $db);

if ($conn) {
    // echo "Koneksi berhasil"; // aktifkan kalau mau cek
} else {
    echo "Koneksi ke database gagal! <br>";
    echo "Error: " . mysqli_connect_error();
    exit;
}
?>