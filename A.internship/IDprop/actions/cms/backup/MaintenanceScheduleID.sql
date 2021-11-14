-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2020 at 08:32 AM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `letfaster_screening`
--

-- --------------------------------------------------------

--
-- Table structure for table `MaintenanceScheduleID`
--

CREATE TABLE `MaintenanceScheduleID` (
  `ID` int(9) UNSIGNED NOT NULL,
  `SupplierOrders_ID` int(9) UNSIGNED NOT NULL,
  `Status` enum('Unscheduled','Scheduled','In Progress','Order Delayed','Cancelled','Completed') CHARACTER SET latin1 NOT NULL,
  `OverBudget` enum('1','0') CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `MaintenanceScheduleID`
--
ALTER TABLE `MaintenanceScheduleID`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Maintenance_RequestID` (`SupplierOrders_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `MaintenanceScheduleID`
--
ALTER TABLE `MaintenanceScheduleID`
  MODIFY `ID` int(9) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `MaintenanceScheduleID`
--
ALTER TABLE `MaintenanceScheduleID`
  ADD CONSTRAINT `MaintenanceScheduleID_ibfk_1` FOREIGN KEY (`SupplierOrders_ID`) REFERENCES `SupplierOrdersID` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
