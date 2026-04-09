<?php
    $petugas = "CREATE TABLE petugas (
    id_petugas INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50),
    nama_petugas VARCHAR(50),
    password VARCHAR(50)
    );"
    $blok_kandang = "CREATE TABLE blok_kandang (
    id_blok_kandang INT PRIMARY KEY AUTO_INCREMENT,
    id_petugas INT,
    kapasitas_per_blok INT,
    tanggal_pembelian_ayam DATE,
    total_ayam INT,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
    );"
    $jadwal_produksi = "CREATE TABLE jadwal_vaksinasi (
    id_jadwal_vaksinasi INT PRIMARY KEY AUTO_INCREMENT,
    id_petugas INT,
    id_blok_kandang INT,
    jadwal_vaksinasi DATE,
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas),
    FOREIGN KEY (id_blok_kandang) REFERENCES blok_kandang(id_blok_kandang)
    );"
?>