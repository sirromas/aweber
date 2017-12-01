-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Ноя 29 2017 г., 02:24
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
-- Структура таблицы `aw_credentials`
--

DROP TABLE IF EXISTS `aw_credentials`;
CREATE TABLE IF NOT EXISTS `aw_credentials` (
  `callbackUrl` varchar(255) NOT NULL,
  `requestTokenSecret` varchar(255) NOT NULL,
  `accessToken` varchar(255) NOT NULL,
  `accessTokenSecret` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `aw_credentials`
--

INSERT INTO `aw_credentials` (`callbackUrl`, `requestTokenSecret`, `accessToken`, `accessTokenSecret`) VALUES
('', '', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `aw_links`
--

DROP TABLE IF EXISTS `aw_links`;
CREATE TABLE IF NOT EXISTS `aw_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `collection_link` varchar(255) NOT NULL,
  `self_link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `aw_lists_config`
--

DROP TABLE IF EXISTS `aw_lists_config`;
CREATE TABLE IF NOT EXISTS `aw_lists_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src_list` int(11) NOT NULL,
  `src_list_name` varchar(128) NOT NULL,
  `dest_list` int(11) NOT NULL,
  `dest_list_name` varchar(255) NOT NULL,
  `clicks_num` int(11) NOT NULL,
  `clicks_type` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `updated` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `aw_lists_config`
--

INSERT INTO `aw_lists_config` (`id`, `src_list`, `src_list_name`, `dest_list`, `dest_list_name`, `clicks_num`, `clicks_type`, `url`, `updated`) VALUES
(14, 4865768, 'Face Fears Opt ins', 4878186, 'Face Fears WARM', 3, '1', 'PGEgaHJlZj0naHR0cHM6Ly95b3V0dS5iZS8wLXdzN0hDWVdJcycgdGFyZ2V0PSdfYmxhbmsnPmh0dHBzOi8veW91dHUuYmUvMC13czdIQ1lXSXM8L2E+', '1511895397');

-- --------------------------------------------------------

--
-- Структура таблицы `aw_users`
--

DROP TABLE IF EXISTS `aw_users`;
CREATE TABLE IF NOT EXISTS `aw_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(128) NOT NULL,
  `lname` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `suspended` int(11) NOT NULL DEFAULT '0',
  `last_activity` varchar(255) NOT NULL DEFAULT '',
  `admin` int(11) NOT NULL DEFAULT '0',
  `top_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `aw_users`
--

INSERT INTO `aw_users` (`id`, `fname`, `lname`, `username`, `password`, `suspended`, `last_activity`, `admin`, `top_user`) VALUES
(1, 'Ron', 'Lang', 'roifreakadvertising@gmail.com', 'strange12', 0, '1511943662', 1, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
