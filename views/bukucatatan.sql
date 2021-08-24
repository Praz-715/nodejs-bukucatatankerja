-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2021 at 03:21 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bukucatatan`
--

-- --------------------------------------------------------

--
-- Table structure for table `harian`
--

CREATE TABLE `harian` (
  `id` int(11) NOT NULL,
  `lokasi` varchar(5) NOT NULL,
  `tipeaset` varchar(20) NOT NULL,
  `sn` varchar(100) NOT NULL,
  `status` varchar(12) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `upload` datetime NOT NULL DEFAULT current_timestamp(),
  `diperbarui` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `harian`
--

-- INSERT INTO `harian` (`id`, `lokasi`, `tipeaset`, `sn`, `status`, `keterangan`, `upload`, `diperbarui`) VALUES
-- (2, 'ass', 'cpu fleg', 'cfg/12/12/13', 'rusak', 'inian rusaka', '2021-03-25 21:02:31', '2021-03-25 14:03:09'),
-- (4, 'ASS', 'CPU QUICK', 'CQS/121/21/12', 'IR', '12v, 9v, 5v, MAX232, ', '2021-03-26 21:03:51', '2021-03-26 14:03:51'),
-- (5, 'MGM', 'CPU Q3', 'Q3/12/12/12', 'IR', '6N137, SDCARD, CMOS', '2021-03-26 21:07:05', '2021-03-26 14:07:05'),
-- (6, 'QWE', 'CPU ZYREX', 'SECP 1212', 'SUPPLIER', 'mb rusak, supplier WHS', '2021-03-26 21:08:06', '2021-03-26 14:08:06'),
-- (7, 'QWE', 'CPU Q3', 'Q3/121212', 'RUSAK', 'MB Rusak, ', '2021-03-26 21:08:45', '2021-03-26 14:08:45'),
-- (8, 'SDF', 'CPU QUICK', 'CQS/121212', 'OK', 'Instal OK, ', '2021-03-26 21:09:55', '2021-03-26 14:09:55'),
-- (9, 'MGM', 'CPU QUICK', 'CQS/12/34/21', 'RUSAK', 'MB Rusak, ', '2021-03-27 19:08:48', '2021-03-27 12:08:48'),
-- (10, 'ASS', 'CPU ZYREX', 'SECP 1233', 'IR', 'mbh81, psu, iocard, ', '2021-03-27 20:32:30', '2021-03-27 13:32:30'),
-- (11, 'QWE', 'CPU ZYREX', 'SECP 1343', 'SUPPLIER', 'Claim MB, supplier ZYREX', '2021-03-28 19:28:43', '2021-03-28 12:28:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `harian`
--
ALTER TABLE `harian`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `harian`
--
ALTER TABLE `harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
