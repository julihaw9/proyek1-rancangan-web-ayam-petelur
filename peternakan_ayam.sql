-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Apr 2026 pada 04.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peternakan_ayam`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `blok_kandang`
--

CREATE TABLE `blok_kandang` (
  `id_blok_kandang` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `kapasitas_per_blok` int(11) DEFAULT NULL,
  `total_ayam` int(11) DEFAULT NULL,
  `tanggal_pembelian_ayam` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `blok_kandang`
--

INSERT INTO `blok_kandang` (`id_blok_kandang`, `id_petugas`, `kapasitas_per_blok`, `total_ayam`, `tanggal_pembelian_ayam`) VALUES
(1011, 1235, 42, 42, '2026-01-01'),
(1012, 1235, 42, 42, '2026-01-01'),
(1013, 1235, 42, 42, '2026-01-01'),
(1014, 1235, 42, 42, '2026-01-01'),
(1015, 1235, 42, 42, '2026-01-01'),
(1016, 1235, 42, 42, '2026-01-01'),
(1017, 1235, 42, 42, '2026-01-01'),
(1018, 1235, 42, 42, '2026-01-01'),
(1019, 1235, 42, 42, '2026-01-01'),
(1020, 1235, 42, 42, '2026-01-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_produksi_telur`
--

CREATE TABLE `detail_produksi_telur` (
  `id_detail_produksi` int(11) NOT NULL,
  `id_produksi` int(11) DEFAULT NULL,
  `jumlah_telur_baik` int(11) DEFAULT NULL,
  `jumlah_telur_rusak` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_produksi_telur`
--

INSERT INTO `detail_produksi_telur` (`id_detail_produksi`, `id_produksi`, `jumlah_telur_baik`, `jumlah_telur_rusak`) VALUES
(2000, 1010, 250, 6),
(2001, 1001, 250, 6),
(2002, 1002, 253, 3),
(2003, 1003, 251, 5),
(2006, 1007, 255, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_vaksinasi`
--

CREATE TABLE `jadwal_vaksinasi` (
  `id_jadwal_vaksinasi` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `id_blok_kandang` int(11) NOT NULL,
  `jadwal_vaksinasi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal_vaksinasi`
--

INSERT INTO `jadwal_vaksinasi` (`id_jadwal_vaksinasi`, `id_petugas`, `id_blok_kandang`, `jadwal_vaksinasi`) VALUES
(2010, 1235, 1020, '2026-01-01'),
(2011, 1235, 1011, '2026-01-01'),
(2012, 1235, 1012, '2026-01-01'),
(2013, 1235, 1013, '2026-01-01'),
(2014, 1235, 1014, '2026-01-01'),
(2015, 1235, 1015, '2026-01-01'),
(2016, 1235, 1016, '2026-01-01'),
(2017, 1235, 1017, '2026-01-01'),
(2018, 1235, 1018, '2026-01-01'),
(2019, 1235, 1019, '2026-01-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemasukan_ayam`
--

CREATE TABLE `pemasukan_ayam` (
  `id_pemasukan_ayam` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_blok_kandang` int(11) DEFAULT NULL,
  `jumlah_ayam` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemasukan_ayam`
--

INSERT INTO `pemasukan_ayam` (`id_pemasukan_ayam`, `id_transaksi`, `id_blok_kandang`, `jumlah_ayam`, `keterangan`, `total_uang`) VALUES
(6001, 4002, 1011, 2, 'afkir', 60000.00),
(7001, 4002, 1011, 2, 'afkir', 60000.00),
(7002, 4002, 1012, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemasukan_telur`
--

CREATE TABLE `pemasukan_telur` (
  `id_pemasukan_telur` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_produksi` int(11) DEFAULT NULL,
  `jumlah_telur` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemasukan_telur`
--

INSERT INTO `pemasukan_telur` (`id_pemasukan_telur`, `id_transaksi`, `id_produksi`, `jumlah_telur`, `keterangan`, `total_uang`) VALUES
(7001, 4002, 1002, 160, 'dijual ke warung', 280000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `id_transaksi`, `keterangan`, `total_uang`) VALUES
(5001, 4001, 'pakan', 360000.00),
(5002, 4001, 'vitamin', 40000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_petugas` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `email`, `nama_petugas`, `password`) VALUES
(1235, 'petugas@gmail.com', 'uus antonius', 'petugas123#');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produksi_telur`
--

CREATE TABLE `produksi_telur` (
  `id_produksi` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total_telur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produksi_telur`
--

INSERT INTO `produksi_telur` (`id_produksi`, `id_petugas`, `tanggal`, `total_telur`) VALUES
(1001, 1235, '2026-03-15', 256),
(1002, 1235, '2026-03-16', 256),
(1003, 1235, '2026-03-17', 256),
(1004, 1235, '2026-03-18', 256),
(1005, 1235, '2026-03-19', 256),
(1007, 1235, '2026-03-20', 256),
(1008, 1235, '2026-03-21', 256),
(1009, 1235, '2026-03-22', 256),
(1010, 1235, '2026-03-23', 256);

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_vaksinasi`
--

CREATE TABLE `status_vaksinasi` (
  `id_status_vaksinasi` int(11) NOT NULL,
  `id_jadwal_vaksinasi` int(11) DEFAULT NULL,
  `tanggal_vaksinasi` date DEFAULT NULL,
  `status_vaksinasi` enum('sudah','belum') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_vaksinasi`
--

INSERT INTO `status_vaksinasi` (`id_status_vaksinasi`, `id_jadwal_vaksinasi`, `tanggal_vaksinasi`, `status_vaksinasi`) VALUES
(3001, 2010, '2026-02-15', 'sudah'),
(3002, 2011, '2026-03-16', 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `jenis_transaksi` enum('pemasukan','pengeluaran') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_petugas`, `tanggal_transaksi`, `jenis_transaksi`) VALUES
(4001, 1235, '2026-03-15', 'pengeluaran'),
(4002, 1235, '2026-03-16', 'pemasukan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `blok_kandang`
--
ALTER TABLE `blok_kandang`
  ADD PRIMARY KEY (`id_blok_kandang`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  ADD PRIMARY KEY (`id_detail_produksi`),
  ADD KEY `id_produksi` (`id_produksi`);

--
-- Indeks untuk tabel `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD PRIMARY KEY (`id_jadwal_vaksinasi`,`id_blok_kandang`),
  ADD KEY `id_petugas` (`id_petugas`),
  ADD KEY `id_blok_kandang` (`id_blok_kandang`);

--
-- Indeks untuk tabel `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  ADD PRIMARY KEY (`id_pemasukan_ayam`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_blok_kandang` (`id_blok_kandang`);

--
-- Indeks untuk tabel `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  ADD PRIMARY KEY (`id_pemasukan_telur`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produksi` (`id_produksi`);

--
-- Indeks untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indeks untuk tabel `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD PRIMARY KEY (`id_produksi`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `status_vaksinasi`
--
ALTER TABLE `status_vaksinasi`
  ADD PRIMARY KEY (`id_status_vaksinasi`),
  ADD KEY `id_jadwal_vaksinasi` (`id_jadwal_vaksinasi`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `blok_kandang`
--
ALTER TABLE `blok_kandang`
  MODIFY `id_blok_kandang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1021;

--
-- AUTO_INCREMENT untuk tabel `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  MODIFY `id_detail_produksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2007;

--
-- AUTO_INCREMENT untuk tabel `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  MODIFY `id_jadwal_vaksinasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2020;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1236;

--
-- AUTO_INCREMENT untuk tabel `status_vaksinasi`
--
ALTER TABLE `status_vaksinasi`
  MODIFY `id_status_vaksinasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3003;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `blok_kandang`
--
ALTER TABLE `blok_kandang`
  ADD CONSTRAINT `blok_kandang_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);

--
-- Ketidakleluasaan untuk tabel `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  ADD CONSTRAINT `detail_produksi_telur_ibfk_1` FOREIGN KEY (`id_produksi`) REFERENCES `produksi_telur` (`id_produksi`);

--
-- Ketidakleluasaan untuk tabel `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD CONSTRAINT `jadwal_vaksinasi_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`),
  ADD CONSTRAINT `jadwal_vaksinasi_ibfk_2` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`);

--
-- Ketidakleluasaan untuk tabel `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  ADD CONSTRAINT `pemasukan_ayam_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `pemasukan_ayam_ibfk_2` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`);

--
-- Ketidakleluasaan untuk tabel `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  ADD CONSTRAINT `pemasukan_telur_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `pemasukan_telur_ibfk_2` FOREIGN KEY (`id_produksi`) REFERENCES `produksi_telur` (`id_produksi`);

--
-- Ketidakleluasaan untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `pengeluaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`);

--
-- Ketidakleluasaan untuk tabel `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD CONSTRAINT `produksi_telur_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);

--
-- Ketidakleluasaan untuk tabel `status_vaksinasi`
--
ALTER TABLE `status_vaksinasi`
  ADD CONSTRAINT `status_vaksinasi_ibfk_1` FOREIGN KEY (`id_jadwal_vaksinasi`) REFERENCES `jadwal_vaksinasi` (`id_jadwal_vaksinasi`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
