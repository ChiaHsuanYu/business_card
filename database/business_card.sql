-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 
-- 伺服器版本： 8.0.17
-- PHP 版本： 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `business_card`
--
CREATE DATABASE IF NOT EXISTS `business_card` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `business_card`;

-- --------------------------------------------------------

--
-- 資料表結構 `industry`
--

CREATE TABLE `industry` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `industryCategoryId` int(11) NOT NULL COMMENT '產業類別ID(FK:industryCategory->Id)',
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '產業名稱',
  `CreateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `industry`
--

INSERT INTO `industry` (`Id`, `industryCategoryId`, `Name`, `CreateTime`) VALUES
(1, 1, '農業', '2022-04-12 17:56:04'),
(2, 1, '漁業', '2022-04-12 17:56:09'),
(3, 2, '餐飲業', '2022-04-12 17:56:25');

-- --------------------------------------------------------

--
-- 資料表結構 `industry_category`
--

CREATE TABLE `industry_category` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '產業類別名稱',
  `CreateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `industry_category`
--

INSERT INTO `industry_category` (`Id`, `Name`, `CreateTime`) VALUES
(1, '農林漁牧業', '2022-04-12 17:55:17'),
(2, '服務業', '2022-04-12 17:55:17');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Account` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '手機號碼',
  `Password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '密碼(MD5 hash)',
  `Verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手機號碼驗證狀態(0:未驗證,1:已驗證)	',
  `VerifyCode` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '驗證碼',
  `SuperID` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'super ID',
  `Name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `Avatar` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '個人頭像(ImgUUIDName圖片檔名)',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間',
  `Token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Token',
  `TokenCreateTime` datetime DEFAULT NULL COMMENT 'Token建立時間',
  `TokenUpdateTime` datetime DEFAULT NULL COMMENT 'Token更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`Id`, `Account`, `Password`, `Verify`, `VerifyCode`, `SuperID`, `Name`, `Avatar`, `isDeleted`, `CreateTime`, `ModifiedTime`, `DeleteTime`, `Token`, `TokenCreateTime`, `TokenUpdateTime`) VALUES
(1, '0912323062', 'e10adc3949ba59abbe56e057f20f883e', 1, '433124', NULL, NULL, NULL, 0, '2022-04-11 13:17:34', '2022-04-12 10:55:54', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJhY2NvdW50IjoiMDkxMjMyMzA2MiIsInRpbWVTdGFtcCI6IjIwMjItMDQtMTIgMTA6NTU6NTQifQ.2ijUY3rgw9qk6pZBukeK8HfBNHVi5zfsy_xodKGbql0', '2022-04-12 10:55:54', '2022-04-12 10:55:54');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `industry`
--
ALTER TABLE `industry`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `industryCategoryId` (`industryCategoryId`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `industry_category`
--
ALTER TABLE `industry_category`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Cellphone` (`Account`),
  ADD KEY `Password` (`Password`),
  ADD KEY `Verify` (`Verify`),
  ADD KEY `VerifyCode` (`VerifyCode`),
  ADD KEY `SuperID` (`SuperID`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Token` (`Token`),
  ADD KEY `TokenCreateTime` (`TokenCreateTime`),
  ADD KEY `TokenUpdateTime` (`TokenUpdateTime`),
  ADD KEY `Id` (`Id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `industry`
--
ALTER TABLE `industry`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `industry_category`
--
ALTER TABLE `industry_category`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
