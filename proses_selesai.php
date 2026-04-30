<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $tgl_sekarang = date("Y-m-d");

    // Update status menjadi 1 dan catat tanggal selesainya
    $sql = "UPDATE jadwal_vaksinasi 
            SET status = 1, tgl_selesai = '$tgl_sekarang' 
            WHERE id_jadwal_vaksinasi = '$id'";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>
                alert('Vaksinasi selesai! Data akan otomatis dihapus sistem dalam 7 hari.');
                window.location='menu_jadwalvaksinasi.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("Location: menu_jadwalvaksinasi.php");
    exit;
}
?>