-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 31, 2026 at 08:20 AM
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
  `nama_blok` varchar(100) DEFAULT NULL,
  `kapasitas_per_blok` int DEFAULT NULL,
  `total_ayam` int DEFAULT NULL,
  `tanggal_pembelian_ayam` date DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blok_kandang`
--

INSERT INTO `blok_kandang` (`id_blok_kandang`, `id_petugas`, `nama_blok`, `kapasitas_per_blok`, `total_ayam`, `tanggal_pembelian_ayam`, `keterangan`) VALUES
(1, NULL, NULL, 42, 40, '2026-01-10', NULL),
(2, NULL, NULL, 42, 40, '2026-01-12', NULL),
(3, NULL, NULL, 42, 42, '2026-02-01', NULL),
(4, NULL, '', 42, 42, '2026-02-15', 'pembersihan'),
(5, NULL, '', 42, 50, '2026-03-01', 'beli baru'),
(6, NULL, 'afkir', 42, 0, '2024-06-06', 'jual'),
(7, NULL, 'Blok A', 42, 42, '2026-05-22', NULL),
(8, NULL, 'Blok 7', 42, 0, '2026-05-22', 'Flu burung'),
(10, NULL, 'Blok C', 42, 42, '2026-05-30', NULL);

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
(3, 3, '2026-05-10', 1, 'Jadwal Vaksin Gumboro', '2026-05-21'),
(4, 4, '2026-05-12', 0, 'Jadwal Vaksin AI Rutin', NULL),
(8, 1, '2026-05-13', 0, 'vaksin AQ', NULL),
(9, 2, '2026-05-13', 0, 'vaksin AQ', NULL),
(10, 3, '2026-05-13', 0, 'vaksin AQ', NULL),
(12, 2, '2026-05-20', 0, 'vaksin', NULL),
(18, 1, '2026-05-22', 0, 'vaksin', NULL),
(19, 1, '2026-06-04', 0, 'vaksin flu', NULL),
(20, 2, '2026-06-04', 0, 'vaksin flu', NULL);

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
(21, 30, 6, 2, 'jual ke warung', 133999.00),
(22, 34, 6, 40, 'afkir', 2000000.00),
(23, 35, 1, 2, 'mati', 0.00);

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
  `total_uang` decimal(15,2) DEFAULT NULL,
  `jumlah` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `id_transaksi`, `keterangan`, `total_uang`, `jumlah`) VALUES
(1, 12, 'Beli Pakan Ayam 10 Karung', 2500000.00, 0),
(2, 12, 'Biaya Listrik dan Air April', 500000.00, 0),
(3, 12, 'Beli Vitamin dan Suplemen', 350000.00, 0),
(4, 12, 'Perbaikan Atap Kandang B', 1200000.00, 0),
(7, 33, 'beli baru', 2940000.00, 42),
(8, 36, 'beli baru', 3750000.00, 50);

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
(5, 'daitriwibowo2007@gmail.com', 'DA\'I TRI WIBOWO', '827ccb0eea8a706c4c34a16891f84e7b');

-- --------------------------------------------------------

--
-- Table structure for table `produksi_telur`
--

CREATE TABLE `produksi_telur` (
  `id_produksi` int NOT NULL,
  `id_petugas` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total_telur` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produksi_telur`
--

INSERT INTO `produksi_telur` (`id_produksi`, `id_petugas`, `tanggal`, `total_telur`) VALUES
(1, NULL, '2026-04-25', 50),
(2, NULL, '2026-04-26', 48),
(3, NULL, '2026-04-27', 52),
(6, NULL, '2026-04-29', 50),
(9, NULL, '2026-05-21', 56),
(13, NULL, '2026-05-22', 20.8),
(14, NULL, '2026-05-22', 40),
(16, NULL, '2026-05-30', 16),
(17, NULL, '2026-05-30', 16),
(18, NULL, '2026-05-30', 15);

--
-- Triggers `produksi_telur`
--
DELIMITER $$
CREATE TRIGGER `setelah_insert_produksi` AFTER INSERT ON `produksi_telur` FOR EACH ROW BEGIN
    INSERT INTO rekap_produksi_telur (tanggal, total_telur_harian, jumlah_inputan)
    VALUES (NEW.tanggal, NEW.total_telur, 1)
    ON DUPLICATE KEY UPDATE 
        total_telur_harian = total_telur_harian + NEW.total_telur,
        jumlah_inputan = jumlah_inputan + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rekap_produksi_telur`
--

CREATE TABLE `rekap_produksi_telur` (
  `id_rekap` int NOT NULL,
  `tanggal` date NOT NULL,
  `total_telur_harian` decimal(10,2) DEFAULT '0.00',
  `jumlah_inputan` int DEFAULT '0',
  `keterangan` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rekap_produksi_telur`
--

INSERT INTO `rekap_produksi_telur` (`id_rekap`, `tanggal`, `total_telur_harian`, `jumlah_inputan`, `keterangan`, `updated_at`) VALUES
(1, '2026-04-25', 50.00, 1, NULL, '2026-05-24 13:15:55'),
(2, '2026-04-26', 48.00, 1, NULL, '2026-05-24 13:15:55'),
(3, '2026-04-27', 52.00, 1, NULL, '2026-05-24 13:15:55'),
(4, '2026-04-29', 50.00, 1, NULL, '2026-05-24 13:15:55'),
(5, '2026-05-21', 56.00, 1, NULL, '2026-05-24 13:15:55'),
(6, '2026-05-22', 60.80, 2, NULL, '2026-05-24 13:15:55'),
(7, '2026-05-30', 136.00, 6, NULL, '2026-05-30 05:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `stok_gudang`
--

CREATE TABLE `stok_gudang` (
  `id` int NOT NULL DEFAULT '1',
  `total_stok_telur` decimal(10,2) DEFAULT '0.00',
  `keterangan_update` text,
  `terakhir_diubah` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `stok_gudang`
--

INSERT INTO `stok_gudang` (`id`, `total_stok_telur`, `keterangan_update`, `terakhir_diubah`) VALUES
(1, 319.00, 'Pengurangan otomatis dari penjualan tanggal 2026-05-30', '2026-05-30 05:53:21');

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
(8, 29, 340, '2026-05-07 01:15:38', 'toko dai', 400000),
(9, 37, 60, '2026-05-30 05:49:03', 'jual ke agen C', 1680000),
(10, 38, 10, '2026-05-30 05:53:21', 'jual ke agen C', 280000);

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
(11, NULL, '2026-04-01', 'pemasukan'),
(12, NULL, '2026-04-05', 'pengeluaran'),
(14, NULL, '2026-03-18', 'pemasukan'),
(15, NULL, '2026-04-29', 'pemasukan'),
(16, NULL, '2026-04-29', 'pemasukan'),
(17, NULL, '2026-04-29', 'pemasukan'),
(18, NULL, '2026-04-29', 'pemasukan'),
(20, NULL, '2026-04-29', 'pemasukan'),
(21, NULL, '2026-04-29', 'pemasukan'),
(22, NULL, '2026-04-29', 'pemasukan'),
(23, NULL, '2026-04-29', 'pemasukan'),
(24, NULL, '2026-04-30', 'pemasukan'),
(25, NULL, '2026-05-06', 'pemasukan'),
(26, NULL, '2026-05-06', 'pemasukan'),
(27, NULL, '2026-05-06', 'pemasukan'),
(28, NULL, '2026-05-06', 'pemasukan'),
(29, NULL, '2026-05-07', 'pemasukan'),
(30, NULL, '2026-05-07', 'pemasukan'),
(33, NULL, '2026-05-07', 'pengeluaran'),
(34, NULL, '2026-05-22', 'pemasukan'),
(35, NULL, '2026-05-22', 'pemasukan'),
(36, NULL, '2026-05-22', 'pengeluaran'),
(37, NULL, '2026-05-30', 'pemasukan'),
(38, NULL, '2026-05-30', 'pemasukan');

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
-- Indexes for table `rekap_produksi_telur`
--
ALTER TABLE `rekap_produksi_telur`
  ADD PRIMARY KEY (`id_rekap`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indexes for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id_blok_kandang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jadwal_vaksinasi`
--
ALTER TABLE `jadwal_vaksinasi`
  MODIFY `id_jadwal_vaksinasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pemasukan_ayam`
--
ALTER TABLE `pemasukan_ayam`
  MODIFY `id_pemasukan_ayam` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pemasukan_telur`
--
ALTER TABLE `pemasukan_telur`
  MODIFY `id_pemasukan_telur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produksi_telur`
--
ALTER TABLE `produksi_telur`
  MODIFY `id_produksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `rekap_produksi_telur`
--
ALTER TABLE `rekap_produksi_telur`
  MODIFY `id_rekap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `telur_terjual`
--
ALTER TABLE `telur_terjual`
  MODIFY `id_jual` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
