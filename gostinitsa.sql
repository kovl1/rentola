-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 24 2025 г., 08:52
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `gostinitsa`
--

-- --------------------------------------------------------

--
-- Структура таблицы `booking`
--

CREATE TABLE `booking` (
  `ID_Booking` int(11) NOT NULL,
  `Arrival_date` date NOT NULL,
  `Deporture_date` date NOT NULL,
  `Number_of_adoults` int(11) NOT NULL,
  `Number_of_children` int(11) NOT NULL,
  `Final_price` decimal(10,2) NOT NULL,
  `Date_of_proceccing` date NOT NULL,
  `ID_User` int(11) DEFAULT NULL,
  `ID_Staff` int(11) DEFAULT NULL,
  `ID_House` int(11) NOT NULL,
  `ID_Status_of_booking` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `booking`
--

INSERT INTO `booking` (`ID_Booking`, `Arrival_date`, `Deporture_date`, `Number_of_adoults`, `Number_of_children`, `Final_price`, `Date_of_proceccing`, `ID_User`, `ID_Staff`, `ID_House`, `ID_Status_of_booking`) VALUES
(1, '2025-06-21', '2025-06-23', 1, 0, 3000.00, '2025-06-20', 1, 1, 1, 1),
(2, '2025-06-21', '2025-06-24', 1, 1, 4500.00, '2025-06-20', 2, 1, 2, 1),
(4, '2025-06-22', '2025-06-24', 1, 1, 4500.00, '2025-06-22', 3, NULL, 2, 3),
(7, '2025-06-22', '2025-06-28', 1, 0, 3000.00, '2025-06-22', 4, 1, 4, 4),
(8, '2025-06-28', '2025-06-30', 1, 0, 3000.00, '2025-06-22', 3, NULL, 4, 1),
(9, '2025-06-27', '2025-06-30', 1, 0, 3000.00, '2025-06-22', 4, 4, 8, 4),
(10, '2025-06-27', '2025-06-30', 1, 0, 3000.00, '2025-06-22', 3, NULL, 8, 1),
(11, '2025-06-27', '2025-06-29', 1, 0, 3000.00, '2025-06-24', 3, NULL, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `feedback`
--

CREATE TABLE `feedback` (
  `ID_Feedback` int(11) NOT NULL,
  `Full_name` varchar(100) NOT NULL,
  `Phone_number` varchar(20) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Question` text NOT NULL,
  `Date_of_creation` datetime NOT NULL,
  `ID_User` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `feedback`
--

INSERT INTO `feedback` (`ID_Feedback`, `Full_name`, `Phone_number`, `Mail`, `Question`, `Date_of_creation`, `ID_User`) VALUES
(1, 'Семёнов Семён Семёнович', '+75839281295', 'sema08@mail.ru', 'У вас есть зона для шашлыков?', '2025-06-20 13:14:51', NULL),
(2, 'Янов Ян Янович', '+72349850945', 'yan99@mail.ru', 'Можно отменить заказ?', '2025-06-20 13:14:51', NULL),
(3, 'test', '', 'test@test.ru', 'сообщение сообщение', '2025-06-22 11:58:37', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `house_data`
--

CREATE TABLE `house_data` (
  `ID_House` int(11) NOT NULL,
  `Hause_name` varchar(100) NOT NULL,
  `House_photo` varchar(255) NOT NULL,
  `House_number` int(11) NOT NULL,
  `Zone_number` int(11) NOT NULL,
  `Available` tinyint(1) NOT NULL,
  `Adult_price` decimal(10,2) NOT NULL,
  `Child_price` decimal(10,2) NOT NULL,
  `ID_Type_of_house` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `house_data`
--

INSERT INTO `house_data` (`ID_House`, `Hause_name`, `House_photo`, `House_number`, `Zone_number`, `Available`, `Adult_price`, `Child_price`, `ID_Type_of_house`) VALUES
(1, 'ЛЕСНОЕ ОЗЕРО-1', 'uploads/house-13.png', 1, 1, 1, 3000.00, 1500.00, 1),
(2, 'ЛЕСНОЕ ОЗЕРО-2', 'uploads/house-1.png', 2, 1, 1, 3000.00, 1500.00, 2),
(3, 'ЛЕСНОЕ ОЗЕРО-3', 'uploads/house-5.png', 3, 1, 1, 3000.00, 1500.00, 3),
(4, 'ХАТКА БОБЕРА-1', 'uploads/house-7.png', 4, 2, 1, 3000.00, 1500.00, 4),
(5, 'ХАТКА БОБЕРА-2', 'uploads/house-7.png', 5, 2, 1, 3000.00, 1500.00, 2),
(6, 'ХАТКА БОБЕРА-3', 'uploads/house-6.png', 6, 3, 1, 5000.00, 1500.00, 3),
(7, 'ХВОЙНОЕ ГНЕЗДО-1', 'uploads/house-2.png', 7, 3, 1, 3000.00, 1500.00, 1),
(8, 'ХВОЙНОЕ ГНЕЗДО-2', 'uploads/house-10.png', 8, 3, 1, 3000.00, 1500.00, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `staff`
--

CREATE TABLE `staff` (
  `ID_Staff` int(11) NOT NULL,
  `Login` varchar(50) NOT NULL,
  `Full_name` varchar(100) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `staff`
--

INSERT INTO `staff` (`ID_Staff`, `Login`, `Full_name`, `Password`) VALUES
(1, 'admin', 'Чехов Антон Павлович', '1'),
(2, 'boss', 'Иванов Иван Иванович', '123'),
(4, 'Richard', 'Richard', 'Richard');

-- --------------------------------------------------------

--
-- Структура таблицы `status_of_booking`
--

CREATE TABLE `status_of_booking` (
  `ID_Status_of_booking` int(11) NOT NULL,
  `Status_of_booking` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `status_of_booking`
--

INSERT INTO `status_of_booking` (`ID_Status_of_booking`, `Status_of_booking`) VALUES
(1, 'Рассматривается'),
(2, 'Подтверждён'),
(3, 'Отменён'),
(4, 'Заморожен');

-- --------------------------------------------------------

--
-- Структура таблицы `types_of_house`
--

CREATE TABLE `types_of_house` (
  `ID_Type_of_house` int(11) NOT NULL,
  `Type_of_house` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `types_of_house`
--

INSERT INTO `types_of_house` (`ID_Type_of_house`, `Type_of_house`) VALUES
(1, 'одноместный'),
(2, 'двухместный'),
(3, 'четырёхместный'),
(4, 'шестиместный');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `ID_User` int(11) NOT NULL,
  `Login` varchar(50) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Phone_number` varchar(20) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`ID_User`, `Login`, `Name`, `Mail`, `Phone_number`, `Password`) VALUES
(1, 'corysander', 'Александр', 'alexander97@mail.com', '+79876543210', '123'),
(2, 'qwerty', 'Владимир', 'vladimir@mail.ru', '+70123456789', '123'),
(3, '1', 'Дмитрий', '1@mail.com', '+7 965 434 11 08', '1'),
(4, '2', 'Максим', 'max@mail.com', '+7 965 434 11 08', '2'),
(7, 'DrGEvil', 'Григорий', 'gevil@mail.com', '+79999999999', '123');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`ID_Booking`),
  ADD KEY `ID_User` (`ID_User`),
  ADD KEY `ID_Staff` (`ID_Staff`),
  ADD KEY `ID_House` (`ID_House`),
  ADD KEY `ID_Status_of_booking` (`ID_Status_of_booking`);

--
-- Индексы таблицы `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`ID_Feedback`),
  ADD KEY `ID_User` (`ID_User`);

--
-- Индексы таблицы `house_data`
--
ALTER TABLE `house_data`
  ADD PRIMARY KEY (`ID_House`),
  ADD KEY `ID_Type_of_house` (`ID_Type_of_house`);

--
-- Индексы таблицы `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`ID_Staff`);

--
-- Индексы таблицы `status_of_booking`
--
ALTER TABLE `status_of_booking`
  ADD PRIMARY KEY (`ID_Status_of_booking`);

--
-- Индексы таблицы `types_of_house`
--
ALTER TABLE `types_of_house`
  ADD PRIMARY KEY (`ID_Type_of_house`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_User`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `booking`
--
ALTER TABLE `booking`
  MODIFY `ID_Booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `feedback`
--
ALTER TABLE `feedback`
  MODIFY `ID_Feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `house_data`
--
ALTER TABLE `house_data`
  MODIFY `ID_House` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `staff`
--
ALTER TABLE `staff`
  MODIFY `ID_Staff` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `status_of_booking`
--
ALTER TABLE `status_of_booking`
  MODIFY `ID_Status_of_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `types_of_house`
--
ALTER TABLE `types_of_house`
  MODIFY `ID_Type_of_house` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`ID_Staff`) REFERENCES `staff` (`ID_Staff`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`ID_House`) REFERENCES `house_data` (`ID_House`),
  ADD CONSTRAINT `booking_ibfk_4` FOREIGN KEY (`ID_Status_of_booking`) REFERENCES `status_of_booking` (`ID_Status_of_booking`);

--
-- Ограничения внешнего ключа таблицы `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`);

--
-- Ограничения внешнего ключа таблицы `house_data`
--
ALTER TABLE `house_data`
  ADD CONSTRAINT `house_data_ibfk_1` FOREIGN KEY (`ID_Type_of_house`) REFERENCES `types_of_house` (`ID_Type_of_house`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
