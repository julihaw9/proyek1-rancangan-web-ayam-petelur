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
    $transaksi = "CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    id_petugas INT,
    tanggal_transaksi DATE,
    jenis_transaksi CHAR(20),
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
    );"
    $pengeluaran = "CREATE TABLE pengeluaran (
    id_pengeluaran INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    keterangan TEXT,
    total_uang INT,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi)
    );"
    $pemasukan_telur = "CREATE TABLE pemasukan_telur (
    id_pemasukan_telur INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    id_produksi INT,
    jumlah_telur INT,
    keterangan TEXT,
    total_uang INT,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_produksi) REFERENCES produksi_telur(id_produksi)
    );"
    $pemasukan_ayam = "CREATE TABLE pemasukan_ayam (
    id_pemasukan_ayam_afkir INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    id_blok_kandang INT,
    keterangan TEXT,
    jumlah_ayam INT,
    total_uang INT,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_blok_kandang) REFERENCES blok_kandang(id_blok_kandang)
    );"
?>