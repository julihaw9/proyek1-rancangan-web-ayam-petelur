<?php
session_start();
include("koneksi.php");

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Gunakan mysqli_real_escape_string untuk keamanan
$id = mysqli_real_escape_string($conn, $_GET['id']);
$jenis = $_GET['jenis'] ?? 'transaksi'; // Default ke transaksi jika tidak ada
$sumber = isset($_GET['sumber']) ? strtolower($_GET['sumber']) : '';

if ($jenis == "transaksi") {

    // Logika hapus berdasarkan sumber (Ayam, Telur, atau Pengeluaran)
    if ($sumber == "ayam") {
        mysqli_query($conn, "DELETE FROM pemasukan_ayam WHERE id_transaksi = '$id'");
    } elseif ($sumber == "telur") {
        // Nama tabel disesuaikan dengan database kamu: telur_terjual
        mysqli_query($conn, "DELETE FROM telur_terjual WHERE id_transaksi = '$id'");
    } elseif ($sumber == "pengeluaran" || $sumber == "umum" || $sumber == "-") {
        // Menangani jika sumbernya adalah pengeluaran/umum
        mysqli_query($conn, "DELETE FROM pengeluaran WHERE id_transaksi = '$id'");
    }

    // Cek apakah data di tabel detail sudah benar-benar kosong
    // Gunakan nama tabel yang benar sesuai database (telur_terjual)
    $cek = mysqli_query($conn, "
        SELECT id_transaksi FROM pemasukan_ayam WHERE id_transaksi='$id'
        UNION
        SELECT id_transaksi FROM telur_terjual WHERE id_transaksi='$id'
        UNION
        SELECT id_transaksi FROM pengeluaran WHERE id_transaksi='$id'
    ");

    // Jika sudah tidak ada kaitan di tabel detail, hapus transaksi utamanya
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$id'");
    }

} elseif ($jenis == "kandang") {
    mysqli_query($conn, "DELETE FROM blok_kandang WHERE id_blok_kandang = '$id'");

} elseif ($jenis == "produksi") {
    mysqli_query($conn, "DELETE FROM produksi_telur WHERE id_produksi = '$id'");
}

// Redirect kembali ke halaman sebelumnya
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: transaksi.php");
}
exit;