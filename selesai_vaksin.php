<?php
include("koneksi.php");

$id = $_GET['id'];

mysqli_query($conn, "
    UPDATE jadwal_vaksin 
    SET status='selesai' 
    WHERE id=$id
");

header("Location: menu_jadwalvaksinasi.php");