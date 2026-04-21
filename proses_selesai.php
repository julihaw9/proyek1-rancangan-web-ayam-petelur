<?php
session_start();
include("koneksi.php");

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Pastikan ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    // Sanitasi ID untuk keamanan
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query UPDATE: ubah status menjadi 1 (Selesai) berdasarkan ID
    $sql = "UPDATE jadwal_vaksinasi SET status = 1 WHERE id_jadwal_vaksinasi = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Jika berhasil, tampilkan notifikasi dan kembali ke halaman utama
        echo "<script>
                alert('Vaksinasi telah ditandai SELESAI!');
                window.location='menu_jadwalvaksinasi.php';
              </script>";
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error saat memperbarui data: " . mysqli_error($conn);
    }
} else {
    // Jika tidak ada ID di URL, kembalikan ke halaman utama
    header("Location: menu_jadwalvaksinasi.php");
    exit;
}
?>