<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Proses hapus data berdasarkan id_blok_kandang
    $query = "DELETE FROM blok_kandang WHERE id_blok_kandang = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Blok $id berhasil dihapus!');
                window.location='inventori_dashboard.php'; 
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus: Data mungkin sedang digunakan di tabel lain.');
                window.location='inventori_dashboard.php';
              </script>";
    }
} else {
    header("Location: inventori_dashboard.php");
}
?>