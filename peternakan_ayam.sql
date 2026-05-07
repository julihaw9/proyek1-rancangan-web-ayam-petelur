-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 07, 2026 at 01:43 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blok_kandang`
--

INSERT INTO `blok_kandang` (`id_blok_kandang`, `id_petugas`, `kapasitas_per_blok`, `total_ayam`, `tanggal_pembelian_ayam`) VALUES
(1, 1, 42, 40, '2026-01-10'),
(2, 1, 42, 40, '2026-01-12'),
(3, 1, 42, 42, '2026-02-01'),
(4, 1, 42, 42, '2026-02-15'),
(5, 1, 42, 42, '2026-03-01'),
(6, 3, 42, 40, '2024-06-06');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_vaksinasi`
--

CREATE TABLE `jadwal_vaksinasi` (
  `id_jadwal_vaksinasi` int NOT NULL,
  `id_blok_kandang` int NOT NULL,
  `jadwal` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `keterangan` varchar(255) DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_vaksinasi`
--

INSERT INTO `jadwal_vaksinasi` (`id_jadwal_vaksinasi`, `id_blok_kandang`, `jadwal`, `status`, `keterangan`, `tgl_selesai`) VALUES
(3, 3, '2026-05-10', 0, 'Jadwal Vaksin Gumboro', NULL),
(4, 4, '2026-05-12', 0, 'Jadwal Vaksin AI Rutin', NULL),
(8, 1, '2026-05-13', 0, 'vaksin AQ', NULL),
(9, 2, '2026-05-13', 0, 'vaksin AQ', NULL),
(10, 3, '2026-05-13', 0, 'vaksin AQ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan_ayam`
--

CREATE TABLE `pemasukan_ayam` (
  `id_pemasukan_ayam` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_blok_kandang` int DEFAULT NULL,
  `jumlah_ayam` int DEFAULT NULL,
  `keterangan` text,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemasukan_ayam`
--

INSERT INTO `pemasukan_ayam` (`id_pemasukan_ayam`, `id_transaksi`, `id_blok_kandang`, `jumlah_ayam`, `keterangan`, `total_uang`) VALUES
(6, 11, 1, 100, 'Penambahan bibit ayam layer', 1500000.00),
(7, 11, 3, 50, 'Penambahan ayam pengganti', 750000.00),
(8, 11, 5, 200, 'Restock kandang E', 3000000.00),
(9, 11, 2, 20, 'Koreksi jumlah ayam', 300000.00),
(10, 11, 4, 10, 'Ayam sampel baru', 150000.00),
(11, 11, 1, 100, 'Penambahan bibit ayam layer', 1500000.00),
(12, 11, 3, 50, 'Penambahan ayam pengganti', 750000.00),
(13, 11, 5, 200, 'Restock kandang E', 3000000.00),
(14, 11, 2, 20, 'Koreksi jumlah ayam', 300000.00),
(15, 11, 4, 10, 'Ayam sampel baru', 150000.00),
(16, 20, 1, 1, 'afkir', 35000.00),
(17, 24, 1, 4, 'afkir', 175000.00),
(18, 25, 1, 2, 'afkir', 70000.00),
(19, 27, 1, 2, 'afkir', 70000.00),
(20, 28, 2, 2, 'afkir', 69997.00),
(21, 30, 6, 2, 'jual ke warung', 133999.00);

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
(1, 11, 1, 45, 'Penjualan ke agen A', 1125000.00),
(2, 11, 2, 40, 'Penjualan ke pasar lokal', 1000000.00),
(3, 11, 3, 50, 'Penjualan ke langganan tetap', 1250000.00),
(4, 11, NULL, 40, 'Penjualan grosir', 1000000.00),
(5, 11, NULL, 50, 'Penjualan eceran', 1250000.00),
(6, 11, 1, 45, 'Penjualan ke agen A', 1125000.00),
(7, 11, 2, 40, 'Penjualan ke pasar lokal', 1000000.00),
(8, 11, 3, 50, 'Penjualan ke langganan tetap', 1250000.00),
(9, 11, NULL, 40, 'Penjualan grosir', 1000000.00),
(10, 11, NULL, 50, 'Penjualan eceran', 1250000.00),
(11, 14, NULL, 200, 'jualan', 5499999.00);

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `keterangan` text,
  `total_uang` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `id_transaksi`, `keterangan`, `total_uang`) VALUES
(1, 12, 'Beli Pakan Ayam 10 Karung', 2500000.00),
(2, 12, 'Biaya Listrik dan Air April', 500000.00),
(3, 12, 'Beli Vitamin dan Suplemen', 350000.00),
(4, 12, 'Perbaikan Atap Kandang B', 1200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nama_petugas` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `email`, `nama_petugas`, `password`) VALUES
(1, 'pet@gmail.com', 'Uus Antonius', '123'),
(2, 'petugas@gmail.com', 'Uus Antonius', '123'),
(3, 'daitriwibowo2007@gmail.com', 'dai', '890'),
(4, 'risol29@gmail.com', 'juli', '123');

-- --------------------------------------------------------

--
-- Table structure for table `produksi_telur`
--

CREATE TABLE `produksi_telur` (
  `id_produksi` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total_telur` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produksi_telur`
--

INSERT INTO `produksi_telur` (`id_produksi`, `id_petugas`, `tanggal`, `total_telur`) VALUES
(1, 1, '2026-04-25', 50),
(2, 1, '2026-04-26', 48),
(3, 1, '2026-04-27', 52),
(6, 1, '2026-04-29', 50);

-- --------------------------------------------------------

--
-- Table structure for table `telur_terjual`
--

CREATE TABLE `telur_terjual` (
  `id_jual` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `jumlah_telur` int NOT NULL,
  `waktu_input` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text,
  `total_uang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `telur_terjual`
--

INSERT INTO `telur_terjual` (`id_jual`, `id_transaksi`, `jumlah_telur`, `waktu_input`, `keterangan`, `total_uang`) VALUES
(4, 11, 50, '2026-04-29 14:54:42', 'Penjualan grosir ke pasar harian', 750000),
(5, 11, 10, '2026-04-29 14:54:42', 'Penjualan eceran warga sekitar', 160000),
(6, 11, 25, '2026-04-29 14:54:42', 'Pesanan warung makan Berkah', 400000),
(7, 23, 10, '2026-04-29 21:50:24', 'agen a', 250000),
(8, 29, 340, '2026-05-07 01:15:38', 'toko dai', 400000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `jenis_transaksi` enum('pemasukan','pengeluaran') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_petugas`, `tanggal_transaksi`, `jenis_transaksi`) VALUES
(11, 1, '2026-04-01', 'pemasukan'),
(12, 1, '2026-04-05', 'pengeluaran'),
(14, 1, '2026-03-18', 'pemasukan'),
(15, 1, '2026-04-29', 'pemasukan'),
(16, 1, '2026-04-29', 'pemasukan'),
(17, 1, '2026-04-29', 'pemasukan'),
(18, 1, '2026-04-29', 'pemasukan'),
(20, 1, '2026-04-29', 'pemasukan'),
(21, 1, '2026-04-29', 'pemasukan'),
(22, 1, '2026-04-29', 'pemasukan'),
(23, 1, '2026-04-29', 'pemasukan'),
(24, 1, '2026-04-30', 'pemasukan'),
(25, 3, '2026-05-06', 'pemasukan'),
(26, 3, '2026-05-06', 'pemasukan'),
(27, 3, '2026-05-06', 'pemasukan'),
(28, 3, '2026-05-06', 'pemasukan'),
(29, 3, '2026-05-07', 'pemasukan'),
(30, 3, '2026-05-07', 'pemasukan');

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
-- Indexes for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD PRIMARY KEY (`id_jadwal_vaksinasi`),
  ADD KEY `id_blok_kandang` (`id_blok_kandang`);

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
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produksi` (`id_produksi`);

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
-- Indexes for table `telur_terjual`
--
ALTER TABLE `telur_terjual`
  ADD PRIMARY KEY (`id_jual`),
  ADD KEY `id_transaksi` (`id_transaksi`);

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
  MODIFY `id_blok_kandang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  MODIFY `id_jadwal_vaksinasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  MODIFY `id_pemasukan_ayam` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  MODIFY `id_pemasukan_telur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  MODIFY `id_produksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `telur_terjual`
--
ALTER TABLE `telur_terjual`
  MODIFY `id_jual` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blok_kandang`
--
ALTER TABLE `blok_kandang`
  ADD CONSTRAINT `blok_kandang_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL;

--
-- Constraints for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  ADD CONSTRAINT `jadwal_vaksinasi_ibfk_1` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`) ON DELETE CASCADE;

--
-- Constraints for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  ADD CONSTRAINT `pemasukan_ayam_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemasukan_ayam_ibfk_2` FOREIGN KEY (`id_blok_kandang`) REFERENCES `blok_kandang` (`id_blok_kandang`) ON DELETE SET NULL;

--
-- Constraints for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  ADD CONSTRAINT `pemasukan_telur_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemasukan_telur_ibfk_2` FOREIGN KEY (`id_produksi`) REFERENCES `produksi_telur` (`id_produksi`) ON DELETE SET NULL;

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `pengeluaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  ADD CONSTRAINT `produksi_telur_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL;

--
-- Constraints for table `telur_terjual`
--
ALTER TABLE `telur_terjual`
  ADD CONSTRAINT `telur_terjual_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
