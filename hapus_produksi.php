<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Mengecek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    // Sanitasi ID agar aman
    $id_produksi = mysqli_real_escape_string($conn, $_GET['id']);

    // Perintah SQL untuk menghapus data berdasarkan id_produksi
    $query = "DELETE FROM produksi_telur WHERE id_produksi = '$id_produksi'";

    // Menjalankan query
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Data produksi beserta detailnya berhasil dihapus!');
                window.location='menu_produksi.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($conn) . "');
                window.location='menu_produksi.php';
              </script>";
    }
} else {
    // Jika file diakses langsung tanpa membawa ID, kembalikan ke halaman utama
    header("Location: menu_produksi.php");
    exit;
}
?>