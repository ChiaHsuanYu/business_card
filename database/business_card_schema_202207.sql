-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2022-07-25 14:35:52
-- 伺服器版本： 10.4.22-MariaDB
-- PHP 版本： 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫: `business_card`
--
CREATE DATABASE IF NOT EXISTS `business_card` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `business_card`;

-- --------------------------------------------------------

--
-- 資料表結構 `avatar`
--

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE `avatar` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `ImageURL` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '圖片檔名',
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '主題名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `bc_record`
--

DROP TABLE IF EXISTS `bc_record`;
CREATE TABLE `bc_record` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者id(FK:Users->Id)',
  `Scan_userId` int(11) NOT NULL COMMENT '瀏覽的userId',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `ScanTime` datetime NOT NULL COMMENT '瀏覽時間',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者id(FK:Users->Id)',
  `Order` text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'company_name,company_logo,company_industryId,company_position,company_aboutus,company_phone,company_address,company_email,company_gui,company_social' COMMENT '欄位順序(以逗號分隔)',
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

DROP TABLE IF EXISTS `country_code`;
CREATE TABLE `country_code` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Country` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '國家名稱',
  `Code` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '國碼',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `industry`
--

DROP TABLE IF EXISTS `industry`;
CREATE TABLE `industry` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '產業名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `mgt_users`
--

DROP TABLE IF EXISTS `mgt_users`;
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

-- --------------------------------------------------------

--
-- 資料表結構 `sms_log`
--

DROP TABLE IF EXISTS `sms_log`;
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

DROP TABLE IF EXISTS `social`;
CREATE TABLE `social` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '圖片檔名',
  `Name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '社群名稱',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `subject`
--

DROP TABLE IF EXISTS `subject`;
CREATE TABLE `subject` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `TemplateId` int(11) NOT NULL COMMENT '模板元件ID',
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

-- --------------------------------------------------------

--
-- 資料表結構 `sys_msg`
--

DROP TABLE IF EXISTS `sys_msg`;
CREATE TABLE `sys_msg` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '系統通知標題',
  `Msg` text COLLATE utf8_unicode_ci NOT NULL COMMENT '系統通知訊息',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `template`
--

DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `Template` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '模板名稱',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者ID',
  `Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '裝置host',
  `Device` tinyint(1) NOT NULL COMMENT '裝置類型(0:電腦,1:行動裝置)',
  `Token` varchar(350) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Token',
  `TokenCreateTime` datetime NOT NULL COMMENT 'Token建立時間',
  `TokenUpdateTime` datetime NOT NULL COMMENT 'Token更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

DROP TABLE IF EXISTS `users`;
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
  `Order` text COLLATE utf8_unicode_ci DEFAULT 'personal_superID,personal_name,personal_nickname,personal_avatar,personal_phone,personal_email,personal_social' COMMENT '欄位順序(以逗號分隔)',
  `CompanyOrder` text COLLATE utf8_unicode_ci NOT NULL COMMENT '公司資訊順序(以ID紀錄，逗號分隔)',
  `Avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '個人頭像(ImgUUIDName圖片檔名)',
  `SubjectId` int(11) NOT NULL DEFAULT 1 COMMENT '主題ID(FK:subject->Id)',
  `Identity` tinyint(1) NOT NULL DEFAULT 0 COMMENT '身分-0:一般用戶,1:系統管理人員',
  `isOpenAI` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否開啟AI推薦-0:否;1:是',
  `isPublic` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公開-0:否;1:是',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否刪除-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `user_collect`
--

DROP TABLE IF EXISTS `user_collect`;
CREATE TABLE `user_collect` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `UserId` int(11) NOT NULL COMMENT '使用者id(FK:Users->Id)',
  `Collect_userId` int(11) NOT NULL COMMENT '收藏的userId',
  `isCollected` tinyint(1) NOT NULL DEFAULT 1 COMMENT '收藏狀態-0:拒絕,1:接受,2:待確認',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間',
  `DeleteTime` datetime DEFAULT NULL COMMENT '刪除時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `user_msg_state`
--

DROP TABLE IF EXISTS `user_msg_state`;
CREATE TABLE `user_msg_state` (
  `Id` int(11) NOT NULL COMMENT 'ID',
  `MsgId` int(11) NOT NULL COMMENT '系統通知訊息ID',
  `UserId` int(11) NOT NULL COMMENT '使用者ID',
  `isReaded` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已讀-0:否,1:是',
  `CreateTime` datetime NOT NULL DEFAULT current_timestamp() COMMENT '建立時間',
  `ModifiedTime` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT '修改時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- 資料表索引 `bc_record`
--
ALTER TABLE `bc_record`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `Scan_userId` (`Scan_userId`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `ScanTime` (`ScanTime`),
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
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `TemplateId` (`TemplateId`);

--
-- 資料表索引 `sys_msg`
--
ALTER TABLE `sys_msg`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Title` (`Title`),
  ADD KEY `Msg` (`Msg`(1024)),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `isDeleted` (`isDeleted`),
  ADD KEY `Id` (`Id`);

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
  ADD KEY `Line_access_token` (`Line_access_token`),
  ADD KEY `isOpenAI` (`isOpenAI`),
  ADD KEY `isPublic` (`isPublic`);

--
-- 資料表索引 `user_collect`
--
ALTER TABLE `user_collect`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `Collect_userId` (`Collect_userId`),
  ADD KEY `isCollected` (`isCollected`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 資料表索引 `user_msg_state`
--
ALTER TABLE `user_msg_state`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `MsgId` (`MsgId`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `isReaded` (`isReaded`),
  ADD KEY `CreateTime` (`CreateTime`),
  ADD KEY `Id` (`Id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `avatar`
--
ALTER TABLE `avatar`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `bc_record`
--
ALTER TABLE `bc_record`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `company`
--
ALTER TABLE `company`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `country_code`
--
ALTER TABLE `country_code`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `industry`
--
ALTER TABLE `industry`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `mgt_users`
--
ALTER TABLE `mgt_users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `sms_log`
--
ALTER TABLE `sms_log`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `social`
--
ALTER TABLE `social`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `subject`
--
ALTER TABLE `subject`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `template`
--
ALTER TABLE `template`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

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

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_collect`
--
ALTER TABLE `user_collect`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_msg_state`
--
ALTER TABLE `user_msg_state`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
