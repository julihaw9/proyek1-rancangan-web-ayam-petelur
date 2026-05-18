<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Menghapus data jadwal vaksinasi secara permanen
    $sql = "DELETE FROM jadwal_vaksinasi 
            WHERE id_jadwal_vaksinasi = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Vaksinasi berhasil dibatalkan dan data dihapus!');
                window.location='menu_jadwalvaksinasi.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: menu_jadwalvaksinasi.php");
    exit;
}
?>