<?php
$servername = "localhost"; // Имя сервера базы данных
$username = "root"; // Имя пользователя базы данных
$password = ""; // Пароль пользователя базы данных
$dbname = "gostinitsa"; // Имя базы данных

$conn = mysqli_connect($servername, $username, $password, $dbname); // Установление соединения с базой данных

if (!$conn) { // Проверка успешности подключения
    die("Ошибка подключения: " . mysqli_connect_error()); // Вывод ошибки и завершение скрипта при неудачном подключении
}
?>