<?php
header('Content-Type: application/json'); // Установка заголовка для ответа в формате JSON

include_once 'db.php'; // Подключение файла базы данных

$response = ['error' => '', 'results' => []]; // Инициализация массива для ответа API

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['view'])) { // Проверка POST-запроса и наличия параметра view
    $view = mysqli_real_escape_string($conn, $_POST['view']); // Экранирование имени представления
    $view_name = str_replace('view', '', $view); // Извлечение номера представления

    if (!preg_match('/^view[1-9][0-9]*$/', $view)) { // Валидация имени представления
        $response['error'] = 'Недопустимое имя представления';
        echo json_encode($response);
        exit;
    }

    // Проверка существования представления
    $sql_check = "SHOW TABLES LIKE '$view'";
    $result_check = mysqli_query($conn, $sql_check);
    if (!$result_check || mysqli_num_rows($result_check) == 0) {
        $response['error'] = "Представление $view не найдено в базе данных"; // Ошибка, если представление не существует
        error_log("View $view not found at " . date('Y-m-d H:i:s')); // Логирование
        echo json_encode($response);
        exit;
    }

    // Выполнение запроса к представлению
    $sql = "SELECT * FROM $view";
    $result = mysqli_query($conn, $sql); // Выполнение запроса
    if ($result) { // Проверка успешности запроса
        while ($row = mysqli_fetch_assoc($result)) {
            $response['results'][] = $row; // Добавление строк из представления в массив результатов
        }
        if (empty($response['results'])) {
            $response['error'] = "Нет данных в представлении $view"; // Ошибка, если представление пустое
        }
    } else {
        $response['error'] = 'Ошибка SQL: ' . mysqli_error($conn); // Ошибка при выполнении SQL-запроса
        error_log("SQL Error for $view: " . mysqli_error($conn)); // Логирование ошибки
    }
} else {
    $response['error'] = 'Неверный запрос'; // Ошибка при неверном методе запроса или отсутствии параметра view
}

echo json_encode($response); // Вывод ответа в формате JSON
?>