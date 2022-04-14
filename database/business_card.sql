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
-- 資料表結構 `company`
--

CREATE TABLE `company` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者id(FK:Users->Id)',
  `Order` text COLLATE utf8_unicode_ci NOT NULL COMMENT '欄位順序(以逗號分隔)',
  `Company` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '公司名稱',
  `Address` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '公司地址(以逗號分隔)',
  `Gui` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '統編',
  `Phone` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '電話(以逗號分隔)',
  `IndustryId` int(11) DEFAULT NULL COMMENT '產業ID(FK:industryCategory->Id)',
  `Position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '職位',
  `Aboutus` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '服務介紹',
  `Email` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email(以逗號分隔)',
  `Logo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Logo圖片(ImgUUIDName圖片檔名)',
  `Social` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '社群資料(包含socialCategory社群分類、socialTitle標題、socialURL網址連結)' CHECK (json_valid(`Social`)),
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `company`
--

INSERT INTO `company` (`Id`, `UserId`, `Order`, `Company`, `Address`, `Gui`, `Phone`, `IndustryId`, `Position`, `Aboutus`, `Email`, `Logo`, `Social`, `isDeleted`, `CreateTime`, `ModifiedTime`, `DeleteTime`) VALUES
(1, 1, 'company_name', '坂和科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2022-04-13 12:07:38', '2022-04-13 12:06:12', '2022-04-13 12:06:12'),
(2, 1, 'company_name,company_address,company_gui,company_phone,company_industryCategory,company_position,company_aboutus,company_email,company_logo,company_social', '坂和科技有限公司', '高雄市前金區五福三路63號12樓', '987654321', '06666666,0998765431', 1, '前端工程師', '取之大眾，用之社會', 'company@gmail.com,company@gmail.com', NULL, '[{\"socialCategory\": \"facebook\",\"socialTitle\": \"我的臉書\",\"socialURL\": \"https://www.fb.com/fb\"},{\"socialCategory\": \"other\",\"socialTitle\": \"官方網站\",\"socialURL\": \"https://sakawa.sakawa.com.tw/\"}]', 0, '2022-04-13 12:07:38', '2022-04-13 13:54:59', '2022-04-13 12:06:12'),
(3, 2, 'company_name,company_address,company_gui,company_phone,company_industryCategory,company_position,company_aboutus,company_email,company_logo,company_social', '坂和科技有限公司', '高雄市前金區五福三路63號12樓', '987654321', '06666666,0998765431', 1, '前端工程師', '取之大眾，用之社會', 'company@gmail.com,company@gmail.com', NULL, '[{\"socialCategory\": \"facebook\",\"socialTitle\": \"我的臉書\",\"socialURL\": \"https://www.fb.com/fb\"},{\"socialCategory\": \"other\",\"socialTitle\": \"官方網站\",\"socialURL\": \"https://sakawa.sakawa.com.tw/\"}]', 0, '2022-04-13 12:07:38', '2022-04-13 13:54:59', '2022-04-13 12:06:12');

-- --------------------------------------------------------

--
-- 資料表結構 `industry`
--

CREATE TABLE `industry` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '產業名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `industry`
--

INSERT INTO `industry` (`Id`, `Name`, `CreateTime`) VALUES
(1, '農、林、漁、牧業', '2022-04-12 17:56:04'),
(2, '金融保險業', '2022-04-12 17:56:09'),
(3, '醫療保健業', '2022-04-12 17:56:25'),
(4, '教育業', '2022-04-14 11:24:58'),
(5, '設計藝術業', '2022-04-14 11:24:58'),
(6, '餐飲業', '2022-04-14 11:24:58'),
(7, '資訊科技業', '2022-04-14 11:24:58'),
(8, '營建工程業', '2022-04-14 11:24:58'),
(9, '製造業', '2022-04-14 11:24:58'),
(10, '服務業', '2022-04-14 11:24:58'),
(11, '不動產業', '2022-04-14 11:24:58'),
(12, '其他', '2022-04-14 11:24:58');

-- --------------------------------------------------------

--
-- 資料表結構 `social`
--

CREATE TABLE `social` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '圖片檔名',
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '社群名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `social`
--

INSERT INTO `social` (`Id`, `Icon`, `Name`, `CreateTime`) VALUES
(1, 'Dribbble.svg', 'Dribbble', '2022-04-14 11:51:00'),
(2, 'Facebook.svg', 'Facebook', '2022-04-14 11:51:00'),
(3, 'Github.svg', 'Github', '2022-04-14 11:51:00'),
(4, 'Google.svg', 'Google', '2022-04-14 11:51:00'),
(5, 'Instagram.svg', 'Instagram', '2022-04-14 11:51:00'),
(6, 'Line.svg', 'Line', '2022-04-14 11:51:00'),
(7, 'LinkedIn.svg', 'LinkedIn', '2022-04-14 11:51:00'),
(8, 'Messenger.svg', 'Messenger', '2022-04-14 11:51:00'),
(9, 'Other.svg', 'Other', '2022-04-14 11:51:00'),
(10, 'Pinterest.svg', 'Pinterest', '2022-04-14 11:51:00'),
(11, 'Skype.svg', 'Skype', '2022-04-14 11:51:00'),
(12, 'Snapchat.svg', 'Snapchat', '2022-04-14 11:51:00'),
(13, 'Telegram.svg', 'Telegram', '2022-04-14 11:51:00'),
(14, 'TikTok.svg', 'TikTok', '2022-04-14 11:51:00'),
(15, 'Twitter.svg', 'Twitter', '2022-04-14 11:51:00'),
(16, 'Wechat.svg', 'Wechat', '2022-04-14 11:51:00'),
(17, 'Weibo.svg', 'Weibo', '2022-04-14 11:51:00'),
(18, 'YouTube.svg', 'YouTube', '2022-04-14 11:51:00');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Account` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '手機號碼',
  `Password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '密碼(MD5 hash)',
  `Verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '手機號碼驗證狀態(0:未驗證,1:已驗證)	',
  `VerifyCode` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '驗證碼',
  `SuperID` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'super ID',
  `Name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `Nickname` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '暱稱',
  `Phone` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '電話(以逗號分隔)',
  `Email` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email(以逗號分隔)',
  `Social` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '社群資料(包含socialCategory社群分類、socialTitle標題、socialURL網址連結)',
  `Order` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '欄位順序(以逗號分隔)',
  `CompanyOrder` text COLLATE utf8_unicode_ci NOT NULL COMMENT '公司資訊順序(以ID紀錄，逗號分隔)',
  `Avatar` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '個人頭像(ImgUUIDName圖片檔名)',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間',
  `Token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Token',
  `TokenCreateTime` datetime DEFAULT NULL COMMENT 'Token建立時間',
  `TokenUpdateTime` datetime DEFAULT NULL COMMENT 'Token更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`Id`, `Account`, `Password`, `Verify`, `VerifyCode`, `SuperID`, `Name`, `Nickname`, `Phone`, `Email`, `Social`, `Order`, `CompanyOrder`, `Avatar`, `isDeleted`, `CreateTime`, `ModifiedTime`, `DeleteTime`, `Token`, `TokenCreateTime`, `TokenUpdateTime`) VALUES
(1, '0912323062', 'e10adc3949ba59abbe56e057f20f883e', 1, '123456', 'C001', '王小明', 'Nick', '07-5882097,07123456,0912123123', '123@mail.com,test001@gmail.com', '[{\"socialCategory\": \"facebook\", \"socialTitle\": \"我的臉書\", \"socialURL\": \"https://www.fb.com/fb\"},{\"socialCategory\": \"other\", \"socialTitle\": \"官方網站\", \"socialURL\": \"https://sakawa.sakawa.com.tw/\"}]\n', 'personal_name,personal_nickname,personal_email,personal_social,personal_phone', '2,1', NULL, 0, '2022-04-11 13:17:34', '2022-04-14 17:59:00', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJhY2NvdW50IjoiMDkxMjMyMzA2MiIsInRpbWVTdGFtcCI6IjIwMjItMDQtMTQgMTc6NTg6MDgifQ.fD2wJJKEVsOBZo0kSQBa3XFEje2Mm4ljkqFQHixXnYA', '2022-04-14 17:58:08', '2022-04-14 17:59:00'),
(2, '091232302', '351523b8e6eb36ae5115205886f36f86', 1, '123456', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, '2022-04-14 17:38:30', '2022-04-14 18:53:09', NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEzIiwiYWNjb3VudCI6IjA5MTIzMjMwMiIsInRpbWVTdGFtcCI6IjIwMjItMDQtMTQgMTg6NTA6NDgifQ.U34fuaD1lXEtcZ88jYgCZj22JnPxafnxqUBgT4NWbck', '2022-04-14 18:50:48', '2022-04-14 18:53:09');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `industry`
--
ALTER TABLE `industry`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `social`
--
ALTER TABLE `social`
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
  ADD KEY `SuperID` (`SuperID`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Token` (`Token`),
  ADD KEY `TokenCreateTime` (`TokenCreateTime`),
  ADD KEY `TokenUpdateTime` (`TokenUpdateTime`),
  ADD KEY `Id` (`Id`),
  ADD KEY `Verify` (`Verify`),
  ADD KEY `VerifyCode` (`VerifyCode`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `company`
--
ALTER TABLE `company`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `industry`
--
ALTER TABLE `industry`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=13;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `social`
--
ALTER TABLE `social`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
