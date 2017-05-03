-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Май 03 2017 г., 12:28
-- Версия сервера: 5.5.52-MariaDB
-- Версия PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `beacon`
--

-- --------------------------------------------------------

--
-- Структура таблицы `beacon`
--

DROP TABLE IF EXISTS `beacon`;
CREATE TABLE IF NOT EXISTS `beacon` (
  `id` int(11) NOT NULL,
  `imei` varchar(20) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` datetime NOT NULL,
  `timezone_type` int(11) NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `priority` int(11) NOT NULL,
  `longitude` varchar(20) NOT NULL,
  `latitude` varchar(20) NOT NULL,
  `altitude` varchar(20) NOT NULL,
  `angle` varchar(20) NOT NULL,
  `satellites` int(11) NOT NULL,
  `speed` varchar(20) NOT NULL,
  `hasGpsFix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `beacon`
--
ALTER TABLE `beacon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE `imeidate` (`imei`, `date`) USING BTREE;

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `beacon`
--
ALTER TABLE `beacon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
