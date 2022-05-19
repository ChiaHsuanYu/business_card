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
-- 資料表結構 `avatar`
--

CREATE TABLE `avatar` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `ImageURL` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '圖片檔名',
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '主題名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `avatar`
--

INSERT INTO `avatar` (`Id`, `ImageURL`, `Name`, `CreateTime`) VALUES
(1, 'image1.png', '預設圖像1', '2022-04-19 11:05:15'),
(2, 'image2.png', '預設圖像2', '2022-04-19 11:05:15'),
(3, 'image3.png', '預設圖像3', '2022-04-19 11:05:15'),
(4, 'image4.png', '預設圖像4', '2022-04-19 11:05:15');

-- --------------------------------------------------------

--
-- 資料表結構 `company`
--

CREATE TABLE `company` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者id(FK:Users->Id)',
  `Order` varchar(535) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'company_name,company_logo,company_industryId,company_position,company_aboutus,company_phone,company_address,company_email,company_gui,company_social' COMMENT '欄位順序(以逗號分隔)',
  `Company` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '公司名稱',
  `Address` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '公司地址(以逗號分隔)',
  `Gui` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '統編',
  `Phone` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '電話(以逗號分隔)',
  `IndustryId` int(11) DEFAULT 1 COMMENT '產業ID(FK:industryCategory->Id)',
  `Position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '職位',
  `Aboutus` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '服務介紹',
  `Email` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email(以逗號分隔)',
  `Logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Logo圖片(ImgUUIDName圖片檔名)',
  `Social` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '社群資料(包含socialCategory社群分類、socialTitle標題、socialURL網址連結)' CHECK (json_valid(`Social`)),
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `country_code`
--

CREATE TABLE `country_code` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Country` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '國家名稱',
  `Code` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '國碼',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `country_code`
--

INSERT INTO `country_code` (`Id`, `Country`, `Code`, `isDeleted`, `CreateTime`) VALUES
(1, '台灣', '886', 0, '2022-04-28 14:22:15'),
(2, '中國', '86', 1, '2022-04-28 14:22:15'),
(3, '南韓', '82', 1, '2022-04-28 14:22:15'),
(4, '日本', '81', 1, '2022-04-28 14:22:15'),
(5, '香港', '852', 1, '2022-04-28 14:22:15'),
(6, '澳門', '853', 1, '2022-04-28 14:22:15'),
(7, '泰國', '66', 1, '2022-04-28 14:22:15'),
(8, '馬來西亞', '60', 1, '2022-04-28 14:22:15'),
(9, '新加坡', '65', 1, '2022-04-28 14:22:15'),
(10, '菲律賓', '63', 1, '2022-04-28 14:22:15'),
(11, '印尼', '62', 1, '2022-04-28 14:22:15'),
(12, '越南', '84', 1, '2022-04-28 14:22:15'),
(13, '印度', '91', 1, '2022-04-28 14:22:15');

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
(1, '農、林、漁、牧業', '2022-04-28 14:04:25'),
(2, '礦業及土石採取業', '2022-04-28 14:04:25'),
(3, '製造業', '2022-04-28 14:04:25'),
(4, '電力及燃氣供應業', '2022-04-28 14:04:25'),
(5, '用水供應及污染整治業', '2022-04-28 14:04:25'),
(6, '營建工程業', '2022-04-28 14:04:25'),
(7, '批發及零售業', '2022-04-28 14:04:25'),
(8, '運輸及倉儲業', '2022-04-28 14:04:25'),
(9, '住宿及餐飲業', '2022-04-28 14:04:25'),
(10, '出版影音及資通訊業', '2022-04-28 14:04:25'),
(11, '金融及保險業', '2022-04-28 14:04:25'),
(12, '不動產業', '2022-04-28 14:04:25'),
(13, '專業、科學及技術服務業', '2022-04-28 14:04:25'),
(14, '公共行政及國防', '2022-04-28 14:04:25'),
(15, '教育業', '2022-04-28 14:04:25'),
(16, '醫療保健及社會工作服務業', '2022-04-28 14:04:25'),
(17, '藝術、娛樂及休閒服務業', '2022-04-28 14:04:25'),
(18, '其他服務業', '2022-04-28 14:04:25');

-- --------------------------------------------------------

--
-- 資料表結構 `mgt_login`
--

CREATE TABLE `mgt_login` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `MgtUserId` int(11) NOT NULL COMMENT '管理帳號ID',
  `Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '裝置host',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `LastTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '最新登入時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `mgt_users`
--

CREATE TABLE `mgt_users` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Account` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '帳號',
  `Password` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '密碼(MD5 hash)',
  `Name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `Phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '電話',
  `Email` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間',
  `Token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Token',
  `TokenCreateTime` datetime DEFAULT NULL COMMENT 'Token建立時間',
  `TokenUpdateTime` datetime DEFAULT NULL COMMENT 'Token更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `mgt_users`
--

INSERT INTO `mgt_users` (`Id`, `Account`, `Password`, `Name`, `Phone`, `Email`, `isDeleted`, `CreateTime`, `ModifiedTime`, `DeleteTime`, `Token`, `TokenCreateTime`, `TokenUpdateTime`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Shine', '0911222333', 'shineyu0502@gmail.com', 0, '2022-04-18 18:14:08', '2022-05-05 16:35:45', '2022-04-18 18:13:31', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJhY2NvdW50IjoiYWRtaW4iLCJ0aW1lU3RhbXAiOiIyMDIyLTA1LTA1IDE2OjM1OjQzIn0.0NhqJRTAROz1iUr6QJI_ZLUjDDLvl45cNrI0fHKIqC4', '2022-05-05 16:35:43', '2022-05-05 16:35:45'),
(2, 'test001', 'fa820cc1ad39a4e99283e9fa555035ec', '測試帳號', '0912123123', 'shineyu0502@gmail.com', 0, '2022-04-20 17:09:52', '2022-05-05 11:22:41', '2022-04-20 17:09:06', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `sms_log`
--

CREATE TABLE `sms_log` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `MobileNumber` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '手機號碼',
  `Status` tinyint(1) NOT NULL COMMENT '發送狀態(0:失敗;1:成功)',
  `Msg` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '回傳訊息',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- 資料表結構 `subject`
--

CREATE TABLE `subject` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `ImageURL` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '圖片檔名',
  `SubjectFile` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'CSS檔案',
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '主題名稱',
  `isReleased` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否發行-0:否,1:是',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `ReleaseTime` date DEFAULT NULL COMMENT '發布時間',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 傾印資料表的資料 `subject`
--

INSERT INTO `subject` (`Id`, `ImageURL`, `SubjectFile`, `Name`, `isReleased`, `isDeleted`, `ReleaseTime`, `CreateTime`, `ModifiedTime`, `DeleteTime`) VALUES
(1, 'test1.png', 'test1.css', '預設主題一', 1, 0, '2022-04-20', '2022-04-15 13:27:10', '2022-04-28 11:04:11', NULL),
(2, 'test2.jpg', 'test2.css', '預設主題二', 1, 0, '2022-04-20', '2022-04-15 13:27:10', '2022-04-28 11:04:11', NULL),
(3, 'test3.jpg', 'test3.css', '預設主題三', 1, 0, '2022-04-20', '2022-04-15 13:27:36', '2022-04-28 11:04:11', NULL),
(4, 'test4.jpg', 'test4.css', '預設主題四', 1, 0, '2022-04-20', '2022-04-18 18:59:42', '2022-04-28 11:04:11', NULL),
(5, 'test5.jpg', 'test5.css', '預設主題五', 1, 0, '2022-04-20', '2022-04-19 18:50:08', '2022-04-28 11:10:55', '2022-04-28 00:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `token`
--

CREATE TABLE `token` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者ID',
  `Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '裝置host',
  `Device` tinyint(1) NOT NULL COMMENT '裝置類型(0:電腦,1:行動裝置)',
  `Token` varchar(535) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Token',
  `TokenCreateTime` datetime NOT NULL COMMENT 'Token建立時間',
  `TokenUpdateTime` datetime NOT NULL COMMENT 'Token更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Google_uid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'google oauth ID',
  `Google_access_token` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'google access Token',
  `Facebook_uid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'facebook oauth ID',
  `Facebook_access_token` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'facebook access Token',
  `Line_uid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'line oauth ID',
  `Line_access_token` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'line access Token',
  `Account` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手機號碼',
  `Verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '手機號碼驗證狀態(0:未驗證,1:已驗證)	',
  `VerifyCode` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '驗證碼',
  `SuperID` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'super ID',
  `SMSNumber` int(11) NOT NULL DEFAULT 0 COMMENT '當日簡訊發送次數',
  `SMSTime` datetime DEFAULT NULL COMMENT '簡訊最後發送時間',
  `Name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `Nickname` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '暱稱',
  `Phone` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '電話(以逗號分隔)',
  `Email` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Email(以逗號分隔)',
  `Social` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '社群資料(包含socialCategory社群分類、socialTitle標題、socialURL網址連結)',
  `Order` varchar(535) COLLATE utf8_unicode_ci DEFAULT 'personal_superID,personal_name,personal_nickname,personal_avatar,personal_phone,personal_email,personal_social' COMMENT '欄位順序(以逗號分隔)',
  `CompanyOrder` text COLLATE utf8_unicode_ci NOT NULL COMMENT '公司資訊順序(以ID紀錄，逗號分隔)',
  `Avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '個人頭像(ImgUUIDName圖片檔名)',
  `SubjectId` int(11) NOT NULL DEFAULT 1 COMMENT '主題ID(FK:subject->Id)',
  `Identity` tinyint(1) NOT NULL DEFAULT 0 COMMENT '身分-0:一般用戶,1:系統管理人員',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 已傾印資料表的索引
--

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `Id` (`Id`),
  ADD KEY `Company` (`Company`),
  ADD KEY `IndustryId` (`IndustryId`);

--
-- 資料表索引 `country_code`
--
ALTER TABLE `country_code`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Id` (`Id`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`);

--
-- 資料表索引 `industry`
--
ALTER TABLE `industry`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `mgt_login`
--
ALTER TABLE `mgt_login`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`MgtUserId`),
  ADD KEY `Id` (`Id`),
  ADD KEY `Host` (`Host`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `LastTime` (`LastTime`);

--
-- 資料表索引 `mgt_users`
--
ALTER TABLE `mgt_users`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Account` (`Account`),
  ADD KEY `Password` (`Password`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Token` (`Token`),
  ADD KEY `TokenCreateTime` (`TokenCreateTime`),
  ADD KEY `TokenUpdateTime` (`TokenUpdateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `sms_log`
--
ALTER TABLE `sms_log`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Status` (`Status`),
  ADD KEY `Id` (`Id`),
  ADD KEY `CreateTime` (`CreateTime`);

--
-- 資料表索引 `social`
--
ALTER TABLE `social`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`),
  ADD KEY `isReleased` (`isReleased`),
  ADD KEY `isDeleted` (`isDeleted`);

--
-- 資料表索引 `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `Id` (`Id`),
  ADD KEY `Token` (`Token`),
  ADD KEY `Host` (`Host`),
  ADD KEY `Device` (`Device`),
  ADD KEY `TokenCreateTime` (`TokenCreateTime`),
  ADD KEY `TokenUpdateTime` (`TokenUpdateTime`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Cellphone` (`Account`),
  ADD KEY `SuperID` (`SuperID`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`),
  ADD KEY `Verify` (`Verify`),
  ADD KEY `VerifyCode` (`VerifyCode`),
  ADD KEY `subjectId` (`SubjectId`),
  ADD KEY `SMSNumber` (`SMSNumber`),
  ADD KEY `SMSTime` (`SMSTime`),
  ADD KEY `Identity` (`Identity`),
  ADD KEY `Google_uid` (`Google_uid`),
  ADD KEY `Google_access_token` (`Google_access_token`),
  ADD KEY `Facebook_uid` (`Facebook_uid`),
  ADD KEY `Facebook_access_token` (`Facebook_access_token`),
  ADD KEY `Line_uid` (`Line_uid`),
  ADD KEY `Line_access_token` (`Line_access_token`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `avatar`
--
ALTER TABLE `avatar`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `company`
--
ALTER TABLE `company`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `country_code`
--
ALTER TABLE `country_code`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=14;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `industry`
--
ALTER TABLE `industry`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `mgt_login`
--
ALTER TABLE `mgt_login`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `mgt_users`
--
ALTER TABLE `mgt_users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `sms_log`
--
ALTER TABLE `sms_log`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `social`
--
ALTER TABLE `social`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `subject`
--
ALTER TABLE `subject`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `token`
--
ALTER TABLE `token`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
