-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Ноя 28 2017 г., 12:02
-- Версия сервера: 5.6.38-log
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mycodebu_aweber`
--

-- --------------------------------------------------------

--
-- Структура таблицы `aw_lists_config`
--

DROP TABLE IF EXISTS `aw_lists_config`;
CREATE TABLE `aw_lists_config` (
  `id` int(11) NOT NULL,
  `src_list` int(11) NOT NULL,
  `src_list_name` varchar(128) NOT NULL,
  `dest_list` int(11) NOT NULL,
  `dest_list_name` varchar(255) NOT NULL,
  `clicks_num` int(11) NOT NULL,
  `clicks_type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `updated` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `aw_users`
--

DROP TABLE IF EXISTS `aw_users`;
CREATE TABLE `aw_users` (
  `id` int(11) NOT NULL,
  `fname` varchar(128) NOT NULL,
  `lname` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `suspended` int(11) NOT NULL DEFAULT '0',
  `last_activity` varchar(255) NOT NULL DEFAULT '',
  `admin` int(11) NOT NULL DEFAULT '0',
  `top_user` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `aw_users`
--

INSERT INTO `aw_users` (`id`, `fname`, `lname`, `username`, `password`, `suspended`, `last_activity`, `admin`, `top_user`) VALUES
(1, 'Ron', 'Lang', 'roifreakadvertising@gmail.com', 'strange12', 0, '1511888215', 1, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `aw_lists_config`
--
ALTER TABLE `aw_lists_config`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `aw_users`
--
ALTER TABLE `aw_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `aw_lists_config`
--
ALTER TABLE `aw_lists_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `aw_users`
--
ALTER TABLE `aw_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
