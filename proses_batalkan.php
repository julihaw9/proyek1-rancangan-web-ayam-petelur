<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Ambil parameter status filter aktif dari URL (jika tidak ada, default ke 'belum')
    $status_filter = $_GET['status_filter'] ?? 'belum';

    // Eksekusi Hapus Data Permanen
    $sql = "DELETE FROM jadwal_vaksinasi WHERE id_jadwal_vaksinasi = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Redirect dinamis membawa kembali state tab radio button yang tadinya aktif
        echo "<script>
            alert('Vaksinasi berhasil dibatalkan dan data dihapus!');
            window.location='menu_jadwalvaksinasi.php?status_filter=" . urlencode($status_filter) . "';
          </script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: menu_jadwalvaksinasi.php");
    exit;
}
?>