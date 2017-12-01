-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Ноя 29 2017 г., 02:38
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
('http://mycodebusters.com/aw-cpanel/dashboard.php', '59JgzcxpDy2AS782fpYjOtUubSjtlFZN2zBpp2eb', 'AgqdweGrCLtfTpjPoX4aGIBa', 'qs9UgP0OmX3ciDeNvwuKht5QO4Euyj56tllkY3O1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
