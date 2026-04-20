<?php
include("koneksi.php");

$id = $_GET['id'];
$jenis = $_GET['jenis'];
$sumber = $_GET['sumber'] ?? '';

if ($jenis == "transaksi") {

    if ($sumber == "Ayam") {
        mysqli_query($conn, "DELETE FROM pemasukan_ayam WHERE id_transaksi = $id");
    } elseif ($sumber == "Telur") {
        mysqli_query($conn, "DELETE FROM pemasukan_telur WHERE id_transaksi = $id");
    } elseif ($sumber == "-") {
        mysqli_query($conn, "DELETE FROM pengeluaran WHERE id_transaksi = $id");
    }

    // cek apakah masih ada data
    $cek = mysqli_query($conn, "
        SELECT id_transaksi FROM pemasukan_ayam WHERE id_transaksi=$id
        UNION
        SELECT id_transaksi FROM pemasukan_telur WHERE id_transaksi=$id
        UNION
        SELECT id_transaksi FROM pengeluaran WHERE id_transaksi=$id
    ");

    // kalau sudah kosong semua → hapus transaksi utama
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = $id");
    }

} elseif ($jenis == "kandang") {

    mysqli_query($conn, "DELETE FROM blok_kandang WHERE id_blok_kandang = $id");

} elseif ($jenis == "produksi") {

    mysqli_query($conn, "DELETE FROM produksi_telur WHERE id_produksi = $id");

}

// balik
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;