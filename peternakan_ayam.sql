-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 22, 2026 at 05:10 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

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
-- Table structure for table `blok_kandang`
--

CREATE TABLE `blok_kandang` (
  `id_blok_kandang` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `kapasitas_per_blok` int DEFAULT NULL,
  `total_ayam` int DEFAULT NULL,
  `tanggal_pembelian_ayam` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blok_kandang`
--

INSERT INTO `blok_kandang` (`id_blok_kandang`, `id_petugas`, `kapasitas_per_blok`, `total_ayam`, `tanggal_pembelian_ayam`) VALUES
(1, 1235, 42, 42, '2026-01-01'),
(2, 1235, 42, 42, '2026-04-16'),
(3, 1235, 42, 42, '2026-04-16'),
(4, 1235, 42, 42, '2026-04-16'),
(5, 1235, 42, 42, '2026-04-17'),
(6, 1235, 42, 42, '2026-04-17'),
(7, 1235, 42, 42, '2026-04-17'),
(8, 1235, 42, 42, '2026-04-18'),
(9, 1235, 42, 42, '2026-04-19'),
(10, 1235, 42, 42, '2026-04-19'),
(1021, 1235, 42, 42, '2026-04-22');

-- --------------------------------------------------------

--
-- Table structure for table `detail_produksi_telur`
--

CREATE TABLE `detail_produksi_telur` (
  `id_detail_produksi` int NOT NULL,
  `id_produksi` int NOT NULL,
  `jumlah_telur_baik` int NOT NULL,
  `jumlah_telur_rusak` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_produksi_telur`
--

INSERT INTO `detail_produksi_telur` (`id_detail_produksi`, `id_produksi`, `jumlah_telur_baik`, `jumlah_telur_rusak`) VALUES
(1, 1011, 11, 1),
(2, 1012, 19, 2),
(3, 1013, 22, 12);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_vaksinasi`
--

CREATE TABLE `jadwal_vaksinasi` (
  `id_jadwal_vaksinasi` int NOT NULL,
  `id_blok_kandang` int NOT NULL,
  `jadwal` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_vaksinasi`
--

INSERT INTO `jadwal_vaksinasi` (`id_jadwal_vaksinasi`, `id_blok_kandang`, `jadwal`, `status`, `keterangan`) VALUES
(1, 1, '2026-04-20', 1, 'Vaksin ND-IB dosis pertama selesai'),
(2, 1, '2026-05-15', 0, 'Rencana vaksin Gumboro pertama'),
(3, 2, '2026-04-22', 1, 'Vaksin AI (Flu Burung) besok pagi'),
(4, 3, '2026-04-18', 1, 'Vaksin rutin bulanan blok 3 aman'),
(5, 4, '2026-06-01', 1, 'Persiapan vaksinasi booster'),
(6, 1, '2026-04-22', 0, 'vaksin 1/3 blok'),
(7, 10, '2026-04-21', 1, '1/2 blok'),
(8, 4, '2026-04-24', 0, 'vaksin');

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan_ayam`
--

CREATE TABLE `pemasukan_ayam` (
  `id_pemasukan_ayam` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_blok_kandang` int DEFAULT NULL,
  `jumlah_ayam` int DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemasukan_ayam`
--

INSERT INTO `pemasukan_ayam` (`id_pemasukan_ayam`, `id_transaksi`, `id_blok_kandang`, `jumlah_ayam`, `keterangan`, `total_uang`) VALUES
(1, 4002, 1, 2, 'afkir', 70000.00),
(3, 4002, 2, 50, 'Penjualan ayam afkir', 2500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan_telur`
--

CREATE TABLE `pemasukan_telur` (
  `id_pemasukan_telur` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_produksi` int DEFAULT NULL,
  `jumlah_telur` int DEFAULT NULL,
  `keterangan` text,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemasukan_telur`
--

INSERT INTO `pemasukan_telur` (`id_pemasukan_telur`, `id_transaksi`, `id_produksi`, `jumlah_telur`, `keterangan`, `total_uang`) VALUES
(1, 4002, 1011, 1000, 'Penjualan telur harian', 1500000.00),
(2, 4005, NULL, 16, 'Penjualan Telur', 0.00),
(6, 4009, 1, 16, 'warung', 400000.00),
(7, 4010, 1013, 10, 'warung', 250000.00),
(8, 4011, 1013, 10, 'warung', 250000.00),
(9, 4012, 1013, 10, 'warung', 240000.00),
(10, 4013, 1013, 10, 'warung', 240000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `id_transaksi`, `keterangan`, `total_uang`) VALUES
(5001, 4001, 'pakan', 360000.00),
(5002, 4001, 'vitamin', 40000.00),
(5003, 4004, 'vaksin', 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_petugas` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `email`, `nama_petugas`, `password`) VALUES
(1235, 'uus@gmail.com', 'uus', '456');

-- --------------------------------------------------------

--
-- Table structure for table `produksi_telur`
--

CREATE TABLE `produksi_telur` (
  `id_produksi` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total_telur` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produksi_telur`
--

INSERT INTO `produksi_telur` (`id_produksi`, `id_petugas`, `tanggal`, `total_telur`) VALUES
(1, 1235, '2026-02-26', 100),
(7, 1235, '2026-02-26', 10),
(1010, 1235, '2026-03-23', 256),
(1011, 1235, '2026-04-22', 12),
(1012, 1235, '2026-04-21', 21),
(1013, 1235, '2026-04-21', 34);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `jenis_transaksi` enum('pemasukan','pengeluaran') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_petugas`, `tanggal_transaksi`, `jenis_transaksi`) VALUES
(1, 1, '2026-02-26', 'pemasukan'),
(4001, 1235, '2026-03-15', 'pengeluaran'),
(4002, 1235, '2026-03-16', 'pemasukan'),
(4003, 1, '2026-04-22', 'pengeluaran'),
(4004, 1, '2026-04-22', 'pengeluaran'),
(4005, 1235, '2026-02-22', 'pemasukan'),
(4006, 1, '2026-04-22', 'pemasukan'),
(4007, 1, '2026-04-22', 'pemasukan'),
(4008, 1, '2026-04-22', 'pemasukan'),
(4009, 1235, '2026-04-22', 'pemasukan'),
(4010, 1235, '2026-04-22', 'pemasukan'),
(4011, 1235, '2026-04-22', 'pemasukan'),
(4012, 1235, '2026-04-22', 'pemasukan'),
(4013, 1235, '2026-04-22', 'pemasukan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blok_kandang`
--
ALTER TABLE `blok_kandang`
  ADD PRIMARY KEY (`id_blok_kandang`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  ADD PRIMARY KEY (`id_detail_produksi`),
  ADD KEY `fk_produksi` (`id_produksi`);

--
-- Indexes for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD PRIMARY KEY (`id_jadwal_vaksinasi`),
  ADD KEY `fk_blok_kandang` (`id_blok_kandang`);

--
-- Indexes for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  ADD PRIMARY KEY (`id_pemasukan_ayam`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_blok_kandang` (`id_blok_kandang`);

--
-- Indexes for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  ADD PRIMARY KEY (`id_pemasukan_telur`),
  ADD KEY `fk_transaksi_pemasukan` (`id_transaksi`),
  ADD KEY `fk_produksi_pemasukan` (`id_produksi`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indexes for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD PRIMARY KEY (`id_produksi`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blok_kandang`
--
ALTER TABLE `blok_kandang`
  MODIFY `id_blok_kandang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1022;

--
-- AUTO_INCREMENT for table `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  MODIFY `id_detail_produksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  MODIFY `id_jadwal_vaksinasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  MODIFY `id_pemasukan_ayam` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  MODIFY `id_pemasukan_telur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5004;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1236;

--
-- AUTO_INCREMENT for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  MODIFY `id_produksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1014;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4014;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blok_kandang`
--
ALTER TABLE `blok_kandang`
  ADD CONSTRAINT `blok_kandang_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);

--
-- Constraints for table `detail_produksi_telur`
--
ALTER TABLE `detail_produksi_telur`
  ADD CONSTRAINT `fk_produksi` FOREIGN KEY (`id_produksi`) REFERENCES `produksi_telur` (`id_produksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD CONSTRAINT `fk_blok_kandang` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  ADD CONSTRAINT `pemasukan_ayam_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `pemasukan_ayam_ibfk_2` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`);

--
-- Constraints for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  ADD CONSTRAINT `fk_produksi_pemasukan` FOREIGN KEY (`id_produksi`) REFERENCES `produksi_telur` (`id_produksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_pemasukan` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `pengeluaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`);

--
-- Constraints for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD CONSTRAINT `produksi_telur_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
