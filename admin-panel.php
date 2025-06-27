<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение базы данных

if (!isset($_SESSION['user_name']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { // Проверка авторизации и прав администратора
    header('Location: index.php'); // Перенаправление на главную страницу, если не администратор
    exit();
}

$user_login = $_SESSION['user_login']; // Логин текущего пользователя
$sql = "SELECT * FROM Staff WHERE Login = '$user_login'"; // Запрос данных сотрудника
$result = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($result);
$_SESSION['user_data'] = $user_data; // Сохранение данных сотрудника в сессии

$current_section = isset($_GET['section']) ? $_GET['section'] : 'booking'; // Текущая секция (по умолчанию — бронирование)
$section_title = [
    'booking' => 'Бронирование',
    'tables' => 'Таблицы',
    'display' => 'Представления'
][$current_section] ?? 'Бронирование'; // Заголовок секции

$tables = [ // Список таблиц и их названий
    'users' => 'Пользователи',
    'staff' => 'Сотрудники',
    'house_data' => 'Данные дома',
    'booking' => 'Бронирование',
    'types_of_house' => 'Типы дома',
    'status_of_booking' => 'Статус бронирование',
    'feedback' => 'Обратная связь'
];

$id_fields = [ // Поля идентификаторов для таблиц
    'users' => 'ID_User',
    'staff' => 'ID_Staff',
    'house_data' => 'ID_House',
    'booking' => 'ID_Booking',
    'types_of_house' => 'ID_Type_of_house',
    'status_of_house' => '',
    'status_of_booking' => 'ID_Status_of_booking',
    'feedback' => 'ID_Feedback'
];

$fields = [ // Поля таблиц и их метки
    'users' => ['Login' => 'Логин', 'Name' => 'Имя', 'Mail' => 'Почта', 'Phone_number' => 'Телефон', 'Password' => 'Пароль'],
    'staff' => ['Login' => 'Логин', 'Full_name' => 'ФИО', 'Password' => 'Пароль'],
    'house_data' => ['Hause_name' => 'Название', 'House_photo' => 'Фотография', 'House_number' => 'Номер дома', 'Zone_number' => 'Номер зоны', 'Available' => 'Доступен', 'Adult_price' => 'Цена для взрослого', 'Child_price' => 'Цена для ребенка', 'ID_Type_of_house' => 'Тип дома'],
    'booking' => ['Arrival_date' => 'Дата заезда', 'Departure_date' => 'Дата отъезда', 'Number_of_adoults' => 'Кол-во взрослых', 'Number_of_children' => 'Кол-во детей', 'Final_price' => 'Итоговая цена', 'Date_of_proceccing' => 'Дата обработки', 'ID_User' => 'Пользователь', 'ID_Staff' => 'Сотрудник', 'ID_House' => 'Дом', 'ID_Status_of_booking' => 'Статус'],
    'types_of_house' => ['Type_of_house' => 'Название'],
    'status_of_booking' => ['Status_of_booking' => 'Название статуса'],
    'feedback' => ['Full_name' => 'ФИО', 'Phone_number' => 'Телефон', 'Mail' => 'Почта', 'Question' => 'Вопрос', 'Date_of_creation' => 'Дата создания', 'ID_User' => 'Пользователь']
];

$error_message = ''; // Переменная для сообщения об ошибке
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $table = $_POST['table']; // Имя таблицы
    $data = []; // Массив для данных
    $id_field = $id_fields[$table]; // Поле идентификатора

    foreach ($fields[$table] as $field => $label) { // Формирование данных для вставки
        if ($field === 'House_photo' && isset($_FILES['House_photo']) && $table === 'house_data') { // Обработка загрузки фото
            $photo = $_FILES['House_photo'];
            $target_dir = "Uploads/";
            $target_file = $target_dir . basename($photo["name"]);
            if (move_uploaded_file($photo["tmp_name"], $target_file)) {
                $data[$field] = $target_file; // Путь к загруженному файлу
            } else {
                $data[$field] = ''; // Пустое значение при ошибке загрузки
            }
        } elseif (in_array($field, ['ID_User', 'ID_Staff', 'ID_House', 'ID_Type_of_house', 'ID_Status_of_booking'])) {
            $data[$field] = $_POST[$field] ?: null; // Установка значения или NULL для внешних ключей
        } elseif ($field === 'Available') {
            $data[$field] = isset($_POST[$field]) && $_POST[$field] === '1' ? 1 : 0; // Установка доступности (1 или 0)
        } elseif (in_array($field, ['Arrival_date', 'Departure_date', 'Date_of_proceccing', 'Date_of_creation'])) {
            $data[$field] = $_POST[$field] ? date('Y-m-d H:i:s', strtotime($_POST[$field])) : date('Y-m-d H:i:s'); // Форматирование дат
        } elseif ($table === 'users' && $field === 'Phone_number') {
            $data[$field] = mysqli_real_escape_string($conn, $_POST[$field] ?? ''); // Экранирование номера телефона
        } else {
            $data[$field] = mysqli_real_escape_string($conn, $_POST[$field] ?? ''); // Экранирование остальных полей
        }
    }

    if ($table === 'staff') { // Проверка уникальности логина для таблицы staff
        $login = $data['Login'];
        $check_login_sql = "SELECT Login FROM users WHERE Login = '$login'";
        $result = mysqli_query($conn, $check_login_sql);
        if (mysqli_num_rows($result) > 0) {
            $error_message = "Ошибка: Логин '$login' уже используется пользователем. Выберите другой логин."; // Ошибка при занятом логине
        } else {
            $columns = implode(', ', array_keys($data)); // Формирование списка столбцов
            $values = implode(', ', array_map(function($value) use ($conn) {
                return $value === null ? 'NULL' : ($value === '' ? "''" : "'$value'");
            }, array_values($data)));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)"; // SQL-запрос для вставки
            mysqli_query($conn, $sql) or die(mysqli_error($conn)); // Выполнение запроса
            header("Location: admin-panel.php?section=tables&table=$table"); // Перенаправление после добавления
            exit();
        }
    } else {
        $columns = implode(', ', array_keys($data)); // Формирование списка столбцов
        $values = implode(', ', array_map(function($value) use ($conn) {
            return $value === null ? 'NULL' : ($value === '' ? "''" : "'$value'");
        }, array_values($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)"; // SQL-запрос для вставки
        mysqli_query($conn, $sql) or die(mysqli_error($conn)); // Выполнение запроса
        header("Location: admin-panel.php?section=tables&table=$table"); // Перенаправление после добавления
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') { // Обработка редактирования записи
    $table = $_POST['table']; // Имя таблицы
    $id_field = $id_fields[$table]; // Поле идентификатора
    $id = mysqli_real_escape_string($conn, $_POST[$id_field]); // Экранирование ID
    $data = []; // Массив для данных
    $all_columns = array_merge([$id_field => ''], $fields[$table]); // Все столбцы таблицы
    foreach ($all_columns as $field => $label) {
        if ($field === $id_field) continue; // Пропуск поля идентификатора
        if ($field === 'House_photo' && $table === 'house_data') { // Обработка загрузки фото
            if (isset($_FILES['House_photo']) && $_FILES['House_photo']['name']) {
                $photo = $_FILES['House_photo'];
                $target_dir = "Uploads/";
                $target_file = $target_dir . basename($photo["name"]);
                if (move_uploaded_file($photo["tmp_name"], $target_file)) {
                    $data[$field] = $target_file; // Путь к загруженному файлу
                }
            } else {
                $sql = "SELECT House_photo FROM house_data WHERE $id_field = '$id'"; // Запрос текущего фото
                $result = mysqli_query($conn, $sql);
                if ($result && $row = mysqli_fetch_assoc($result)) {
                    $data[$field] = $row['House_photo'] ?? ''; // Использование текущего фото
                }
            }
        } elseif (in_array($field, ['ID_User', 'ID_Staff', 'ID_House', 'ID_Type_of_house', 'ID_Status_of_booking'])) {
            $data[$field] = $_POST[$field] === '' ? null : ($_POST[$field] ?: null); // Установка значения или NULL для внешних ключей
        } elseif ($field === 'Available') {
            $data[$field] = isset($_POST[$field]) && $_POST[$field] === '1' ? 1 : 0; // Установка доступности
        } elseif (in_array($field, ['Arrival_date', 'Departure_date', 'Date_of_proceccing', 'Date_of_creation'])) {
            $data[$field] = $field === 'Date_of_creation' ? date('Y-m-d H:i:s') : ($_POST[$field] ? date('Y-m-d H:i:s', strtotime($_POST[$field])) : null); // Форматирование дат
        } else {
            $data[$field] = mysqli_real_escape_string($conn, $_POST[$field] ?? ''); // Экранирование остальных полей
        }
    }
    if (!empty($data)) { // Если есть данные для обновления
        $set_clause = implode(', ', array_map(function($key) use ($data, $conn) {
            return "$key = " . ($data[$key] === null ? 'NULL' : "'".mysqli_real_escape_string($conn, $data[$key])."'"); // Формирование условий обновления
        }, array_keys($data)));
        $sql = "UPDATE $table SET $set_clause WHERE $id_field = '$id'"; // SQL-запрос для обновления
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            die("Ошибка обновления: " . mysqli_error($conn)); // Вывод ошибки при неудачном обновлении
        }
    }
    header("Location: admin-panel.php?section=tables&table=$table"); // Перенаправление после редактирования
    exit();
}

$foreign_data = [ // Загрузка данных для выпадающих списков
    'ID_User' => mysqli_query($conn, "SELECT ID_User, CONCAT(Name, ' (', Login, ')') as Name FROM users") ?: [], // Пользователи
    'ID_Staff' => mysqli_query($conn, "SELECT ID_Staff, Full_name FROM staff") ?: [], // Сотрудники
    'ID_House' => mysqli_query($conn, "SELECT ID_House, Hause_name FROM house_data") ?: [], // Дома
    'ID_Type_of_house' => mysqli_query($conn, "SELECT ID_Type_of_house, Type_of_house FROM types_of_house") ?: [], // Типы домов
    'ID_Status_of_booking' => mysqli_query($conn, "SELECT ID_Status_of_booking, Status_of_booking FROM status_of_booking") ?: [] // Статусы бронирования
];

if (isset($_GET['delete']) && isset($_GET['table'])) { // Обработка удаления записи
    $table = $_GET['table']; // Имя таблицы
    $id_field = $id_fields[$table]; // Поле идентификатора
    $id = mysqli_real_escape_string($conn, $_GET['delete']); // Экранирование ID
    $sql = "DELETE FROM $table WHERE $id_field = '$id'"; // SQL-запрос для удаления
    mysqli_query($conn, $sql) or die(mysqli_error($conn)); // Выполнение запроса
    header("Location: admin-panel.php?section=tables&table=$table"); // Перенаправление после удаления
    exit();
}

// Обработка бронирований
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_section === 'booking') {
    if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) { // Отмена бронирования
        $booking_id = (int)$_POST['booking_id'];
        $sql_update = "UPDATE booking SET ID_Status_of_booking = 3 WHERE ID_Booking = ? AND ID_Status_of_booking IN (1, 4)"; // Обновление статуса на "Отменено"
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "i", $booking_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    } elseif (isset($_POST['add_booking']) && isset($_POST['booking_id'])) { // Подтверждение бронирования
        $booking_id = (int)$_POST['booking_id'];
        $sql_update = "UPDATE booking SET ID_Status_of_booking = 2 WHERE ID_Booking = ? AND ID_Status_of_booking IN (1, 4)"; // Обновление статуса на "Подтверждено"
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "i", $booking_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }
    header('Location: admin-panel.php?section=booking'); // Перенаправление после обработки бронирования
    exit();
}

// Запрос списка бронирований
$sql_bookings = "SELECT b.*, h.Hause_name, h.House_photo, u.Name 
                 FROM booking b 
                 LEFT JOIN house_data h ON b.ID_House = h.ID_House 
                 LEFT JOIN users u ON b.ID_User = u.ID_User 
                 WHERE b.ID_Status_of_booking IN (1, 4) 
                 ORDER BY b.Date_of_proceccing DESC"; // Запрос активных бронирований
$result_bookings = mysqli_query($conn, $sql_bookings); // Выполнение запроса
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Адаптивность -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/admin-panel.css"> <!-- Подключение стилей -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Административная панель - Рентола</title> <!-- Заголовок страницы -->
</head>
<body>
    <header class="header">
        <div class="header__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="header__logo-img"> <!-- Логотип -->
        </div>
        <nav class="header__nav">
            <a href="?section=booking" class="header__link header__link_type_booking">БРОНИРОВАНИЕ</a> <!-- Ссылка на секцию бронирования -->
            <a href="?section=tables" class="header__link header__link_type_tables">ТАБЛИЦЫ</a> <!-- Ссылка на секцию таблиц -->
            <a href="?section=display" class="header__link header__link_type_display">ПРЕДСТАВЛЕНИЯ</a> <!-- Ссылка на секцию представлений -->
            <a href="account.php" class="header__link header__link_type_exit">ВЫЙТИ</a> <!-- Ссылка для выхода -->
        </nav>
    </header>

    <div class="block-1">
        <h1 class="block-1__title">АДМИНИСТРАТИВНАЯ ПАНЕЛЬ</h1> <!-- Заголовок панели -->
        <h2 class="block-1__subtitle"><?php echo htmlspecialchars($section_title); ?></h2> <!-- Подзаголовок секции -->
        <?php if ($current_section === 'tables'): ?>
            <div class="block-1__table-buttons">
                <?php foreach ($tables as $table_key => $table_name): ?>
                    <button class="block-1__table-btn <?php echo (isset($_GET['table']) && $_GET['table'] === $table_key) ? 'active' : ''; ?>" 
                            data-table="<?php echo $table_key; ?>"> <!-- Кнопка выбора таблицы -->
                        <?php echo htmlspecialchars($table_name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="block-1__content">
                <div class="block-1__main-section">
                    <div class="block-1__add-section">
                        <h3 class="block-1__form-title">Форма добавления записи</h3> <!-- Заголовок формы добавления -->
                        <form method="POST" enctype="multipart/form-data" class="block-1__add-form" id="addForm"> <!-- Форма добавления -->
                            <input type="hidden" name="action" value="add"> <!-- Скрытое поле для действия -->
                            <input type="hidden" name="table" id="addTable"> <!-- Скрытое поле для имени таблицы -->
                            <?php
                            $selected_table = isset($_GET['table']) ? $_GET['table'] : 'users'; // Выбранная таблица
                            foreach ($fields[$selected_table] as $field => $label):
                                if (in_array($field, ['ID_User', 'ID_Staff', 'ID_House', 'ID_Type_of_house', 'ID_Status_of_booking'])): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка поля -->
                                        <select name="<?php echo $field; ?>" class="block-1__form-input" required> <!-- Выпадающий список -->
                                            <option value="">Не выбрано</option>
                                            <?php
                                            $foreign_key = $field;
                                            $result = $foreign_data[$foreign_key];
                                            if ($result === false) {
                                                echo "<option value=''>Ошибка загрузки данных</option>"; // Ошибка при загрузке данных
                                            } else {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $id = $row[array_keys($row)[0]];
                                                    $name = $row[array_keys($row)[1]];
                                                    echo "<option value='$id'>$name</option>"; // Опция списка
                                                }
                                                mysqli_data_seek($foreign_data[$foreign_key], 0); // Сброс указателя результата
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php elseif ($field === 'House_photo' && $selected_table === 'house_data'): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для фото -->
                                        <input type="file" name="House_photo" class="block-1__form-input" accept="image/*" required> <!-- Поле загрузки фото -->
                                    </div>
                                <?php elseif ($field === 'Available' && $selected_table === 'house_data'): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для доступности -->
                                        <select name="Available" class="block-1__form-input" required> <!-- Выпадающий список -->
                                            <option value="1">Да</option>
                                            <option value="0">Нет</option>
                                        </select>
                                    </div>
                                <?php elseif (in_array($field, ['Arrival_date', 'Departure_date', 'Date_of_proceccing', 'Date_of_creation'])): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для даты -->
                                        <input type="date" name="<?php echo $field; ?>" class="block-1__form-input" required> <!-- Поле ввода даты -->
                                    </div>
                                <?php elseif ($field === 'Mail' && in_array($selected_table, ['users', 'feedback'])): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для email -->
                                        <input type="email" name="<?php echo $field; ?>" class="block-1__form-input" 
                                               pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
                                               title="Введите корректный email (например, example@domain.com)" required> <!-- Поле ввода email -->
                                    </div>
                                <?php elseif ($field !== 'ID_Booking' || $selected_table !== 'booking'): ?>
                                    <div class="block-1__form-group">
                                        <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для текстового поля -->
                                        <input type="text" name="<?php echo $field; ?>" class="block-1__form-input" required> <!-- Поле ввода текста -->
                                    </div>
                                <?php endif;
                            endforeach; ?>
                            <button type="submit" class="block-1__submit-btn">Добавить</button> <!-- Кнопка отправки -->
                            <?php if ($error_message && $selected_table === 'staff'): ?>
                                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div> <!-- Сообщение об ошибке -->
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="block-1__cards-section">
                        <div class="block-1__cards">
                            <?php
                            $selected_table = isset($_GET['table']) ? $_GET['table'] : 'users'; // Выбранная таблица
                            $result = mysqli_query($conn, "SELECT * FROM $selected_table"); // Запрос данных таблицы
                            if ($result === false) {
                                echo "<p style='color: red;'>Ошибка выполнения запроса: " . mysqli_error($conn) . "</p>"; // Ошибка запроса
                            } else {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)): ?>
                                        <div class="block-1__card">
                                            <?php if (in_array($selected_table, ['users', 'staff', 'house_data', 'booking', 'types_of_house', 'feedback'])): ?>
                                                <div class="block-1__card-item">
                                                    <span class="block-1__card-label">ID:</span>
                                                    <span class="block-1__card-value"><?php echo htmlspecialchars($row[$id_fields[$selected_table]] ?? 'N/A'); ?></span> <!-- ID записи -->
                                                </div>
                                            <?php endif; ?>
                                            <?php foreach ($fields[$selected_table] as $field => $label):
                                                $display_value = $row[$field] ?? 'N/A'; // Значение поля
                                                if ($field === 'ID_User' && $selected_table !== 'users') {
                                                    $user_result = mysqli_query($conn, "SELECT CONCAT(Name, ' (', Login, ')') as Name FROM users WHERE ID_User = '{$row[$field]}'");
                                                    $display_value = $user_result && mysqli_num_rows($user_result) > 0 ? mysqli_fetch_assoc($user_result)['Name'] : 'Не указан'; // Имя пользователя
                                                } elseif ($field === 'ID_Staff' && $selected_table !== 'staff') {
                                                    $staff_result = mysqli_query($conn, "SELECT Full_name FROM staff WHERE ID_Staff = '{$row[$field]}'");
                                                    $display_value = $staff_result && mysqli_num_rows($staff_result) > 0 ? mysqli_fetch_assoc($staff_result)['Full_name'] : 'Не указан'; // ФИО сотрудника
                                                } elseif ($field === 'ID_House' && $selected_table !== 'house_data') {
                                                    $house_result = mysqli_query($conn, "SELECT Hause_name FROM house_data WHERE ID_House = '{$row[$field]}'");
                                                    $display_value = $house_result && mysqli_num_rows($house_result) > 0 ? mysqli_fetch_assoc($house_result)['Hause_name'] : 'Не указан'; // Название дома
                                                } elseif ($field === 'ID_Type_of_house' && $selected_table !== 'types_of_house') {
                                                    $type_result = mysqli_query($conn, "SELECT Type_of_house FROM types_of_house WHERE ID_Type_of_house = '{$row[$field]}'");
                                                    $display_value = $type_result && mysqli_num_rows($type_result) > 0 ? mysqli_fetch_assoc($type_result)['Type_of_house'] : 'Не указан'; // Тип дома
                                                } elseif ($field === 'ID_Status_of_booking' && $selected_table !== 'status_of_booking') {
                                                    $status_result = mysqli_query($conn, "SELECT Status_of_booking FROM status_of_booking WHERE ID_Status_of_booking = '{$row[$field]}'");
                                                    $display_value = $status_result && mysqli_num_rows($status_result) > 0 ? mysqli_fetch_assoc($status_result)['Status_of_booking'] : 'Не указан'; // Статус бронирования
                                                }
                                                ?>
                                                <div class="block-1__card-item">
                                                    <span class="block-1__card-label"><?php echo htmlspecialchars($label); ?>:</span>
                                                    <span class="block-1__card-value"><?php echo htmlspecialchars($display_value); ?></span> <!-- Значение поля -->
                                                </div>
                                            <?php endforeach; ?>
                                            <div class="block-1__card-actions">
                                                <a href="?section=tables&table=<?php echo $selected_table; ?>&edit=<?php echo $row[$id_fields[$selected_table]] ?? ''; ?>" 
                                                   class="block-1__edit-btn">Изменить</a> <!-- Кнопка редактирования -->
                                                <a href="?section=tables&table=<?php echo $selected_table; ?>&delete=<?php echo $row[$id_fields[$selected_table]] ?? ''; ?>" 
                                                   class="block-1__delete-btn" onclick="return confirm('Удалить запись?')">Удалить</a> <!-- Кнопка удаления -->
                                            </div>
                                        </div>
                                    <?php endwhile;
                                } else {
                                    echo "<p>Нет записей в таблице $selected_table.</p>"; // Сообщение при пустой таблице
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="block-1__edit-section" style="display: <?php echo isset($_GET['edit']) ? 'block' : 'none'; ?>;"> <!-- Секция редактирования -->
                    <?php
                    if (isset($_GET['edit']) && isset($_GET['table'])) {
                        $table = $_GET['table']; // Имя таблицы
                        $id_field = $id_fields[$table]; // Поле идентификатора
                        $id = mysqli_real_escape_string($conn, $_GET['edit']); // Экранирование ID
                        $sql = "SELECT * FROM $table WHERE $id_field = '$id'"; // Запрос записи
                        $result = mysqli_query($conn, $sql);
                        $record = mysqli_fetch_assoc($result);
                        if ($record): ?>
                            <h3 class="block-1__form-title">Форма редактирования записи</h3> <!-- Заголовок формы редактирования -->
                            <form method="POST" enctype="multipart/form-data" class="block-1__edit-form"> <!-- Форма редактирования -->
                                <input type="hidden" name="action" value="edit"> <!-- Скрытое поле для действия -->
                                <input type="hidden" name="table" value="<?php echo $table; ?>"> <!-- Скрытое поле для таблицы -->
                                <input type="hidden" name="<?php echo $id_field; ?>" value="<?php echo $id; ?>"> <!-- Скрытое поле для ID -->
                                <?php foreach ($fields[$table] as $field => $label):
                                    if (in_array($field, ['ID_User', 'ID_Staff', 'ID_House', 'ID_Type_of_house', 'ID_Status_of_booking'])): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка поля -->
                                            <select name="<?php echo $field; ?>" class="block-1__form-input" required> <!-- Выпадающий список -->
                                                <option value="">Не выбрано</option>
                                                <?php
                                                $foreign_key = $field;
                                                $result_fk = $foreign_data[$foreign_key];
                                                if ($result_fk === false) {
                                                    echo "<option value=''>Ошибка загрузки данных</option>"; // Ошибка при загрузке данных
                                                } else {
                                                    while ($row_fk = mysqli_fetch_assoc($result_fk)) {
                                                        $fk_id = $row_fk[array_keys($row_fk)[0]];
                                                        $fk_name = $row_fk[array_keys($row_fk)[1]];
                                                        $selected = $fk_id == $record[$field] ? 'selected' : '';
                                                        echo "<option value='$fk_id' $selected>$fk_name</option>"; // Опция списка
                                                    }
                                                    mysqli_data_seek($foreign_data[$foreign_key], 0); // Сброс указателя
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php elseif ($field === 'House_photo' && $table === 'house_data'): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для фото -->
                                            <input type="file" name="House_photo" class="block-1__form-input" accept="image/*"> <!-- Поле загрузки фото -->
                                            <p>Текущая: <?php echo htmlspecialchars($record[$field]); ?></p> <!-- Текущее фото -->
                                            <input type="hidden" name="current_House_photo" value="<?php echo htmlspecialchars($record[$field] ?? ''); ?>"> <!-- Скрытое поле для текущего фото -->
                                        </div>
                                    <?php elseif ($field === 'Available' && $table === 'house_data'): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для доступности -->
                                            <select name="Available" class="block-1__form-input" required> <!-- Выпадающий список -->
                                                <option value="1" <?php echo $record[$field] == 1 ? 'selected' : ''; ?>>Да</option>
                                                <option value="0" <?php echo $record[$field] == 0 ? 'selected' : ''; ?>>Нет</option>
                                            </select>
                                        </div>
                                    <?php elseif (in_array($field, ['Arrival_date', 'Departure_date', 'Date_of_proceccing', 'Date_of_creation'])): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для даты -->
                                            <input type="date" name="<?php echo $field; ?>" class="block-1__form-input" 
                                                   value="<?php echo $record[$field] ? date('Y-m-d', strtotime($record[$field])) : ''; ?>" required> <!-- Поле ввода даты -->
                                        </div>
                                    <?php elseif ($field === 'Mail' && in_array($table, ['users', 'feedback'])): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для email -->
                                            <input type="email" name="<?php echo $field; ?>" class="block-1__form-input" 
                                                   value="<?php echo htmlspecialchars($record[$field]); ?>" 
                                                   pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
                                                   title="Введите корректный email (например, example@domain.com)" required> <!-- Поле ввода email -->
                                        </div>
                                    <?php elseif ($field !== 'ID_Booking' || $table !== 'booking'): ?>
                                        <div class="block-1__form-group">
                                            <label><?php echo htmlspecialchars($label); ?>:</label> <!-- Метка для текстового поля -->
                                            <input type="text" name="<?php echo $field; ?>" class="block-1__form-input" 
                                                   value="<?php echo htmlspecialchars($record[$field]); ?>" required> <!-- Поле ввода текста -->
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                                <button type="submit" class="block-1__submit-btn">Сохранить</button> <!-- Кнопка сохранения -->
                                <a href="?section=tables&table=<?php echo $table; ?>" class="block-1__cancel-btn">Отмена</a> <!-- Кнопка отмены -->
                            </form>
                        <?php endif;
                    }
                    ?>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() { // Инициализация скрипта после загрузки DOM
                    const tableButtons = document.querySelectorAll('.block-1__table-btn'); // Кнопки выбора таблиц
                    tableButtons.forEach(button => {
                        button.addEventListener('click', function() { // Обработчик клика по кнопке
                            const table = this.getAttribute('data-table'); // Имя таблицы
                            window.location.href = `?section=tables&table=${table}`; // Переход к выбранной таблице
                        });
                    });
                    document.getElementById('addTable').value = '<?php echo $selected_table; ?>'; // Установка значения скрытого поля
                });
            </script>
        <?php elseif ($current_section === 'booking'): ?>
            <div class="block-1__orders">
                <?php
                if ($result_bookings && mysqli_num_rows($result_bookings) > 0) {
                    while ($booking = mysqli_fetch_assoc($result_bookings)) {
                        $order_number = $booking['ID_Booking'] ?? 'N/A'; // Номер заказа
                        $house_photo = !empty($booking['House_photo']) ? htmlspecialchars($booking['House_photo']) : 'img/house-photo.png'; // Фото дома
                        $user_name = htmlspecialchars($booking['Name'] ?? 'Не указан'); // Имя пользователя
                        $phone_number = htmlspecialchars($user_data['Phone_number'] ?? ''); // Номер телефона сотрудника
                        $status_id = $booking['ID_Status_of_booking'] ?? 1; // ID статуса
                        $status_text = ($status_id == 1) ? 'Рассматривается' : 'Заморожен'; // Текст статуса
                        ?>
                        <div class="block-1__order">
                            <img src="<?php echo $house_photo; ?>" alt="Дом" class="block-1__order-image"> <!-- Изображение дома -->
                            <div class="block-1__order-info">
                                <div class="block-1__order-row">
                                    <p class="block-1__order-text block-1__order-text--order">Заказ номер: <?php echo $order_number; ?></p> <!-- Номер заказа -->
                                    <p class="block-1__order-text block-1__order-text--price">Цена: <?php echo number_format($booking['Final_price'] ?? 0, 0, '', ''); ?> руб</p> <!-- Цена -->
                                    <p class="block-1__order-text block-1__order-text--status">Статус: <?php echo $status_text; ?></p> <!-- Статус -->
                                    <p class="block-1__order-text block-1__order-text--name">Имя: <?php echo $user_name; ?></p> <!-- Имя пользователя -->
                                </div>
                                <div class="block-1__order-row">
                                    <p class="block-1__order-text block-1__order-text--checkin">Дата заезда: <?php echo htmlspecialchars($booking['Arrival_date'] ?? 'Не указана'); ?></p> <!-- Дата заезда -->
                                    <p class="block-1__order-text block-1__order-text--checkout">Дата отъезда: <?php echo htmlspecialchars($booking['Departure_date'] ?? 'Не указана'); ?></p> <!-- Дата отъезда -->
                                    <p class="block-1__order-text block-1__order-text--phone">Номер телефона: <?php echo $phone_number; ?></p> <!-- Номер телефона -->
                                </div>
                            </div>
                            <div class="block-1__order-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $order_number; ?>"> <!-- Скрытое поле ID брони -->
                                    <button type="submit" name="add_booking" class="block-1__review-btn">ДОБАВИТЬ</button> <!-- Кнопка подтверждения -->
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $order_number; ?>"> <!-- Скрытое поле ID брони -->
                                    <button type="submit" name="cancel_booking" class="block-1__review-btn">ОТМЕНИТЬ</button> <!-- Кнопка отмены -->
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>Нет заказов для обработки.</p>'; // Сообщение при отсутствии бронирований
                }
                ?>
            </div>
        <?php elseif ($current_section === 'display'): ?>
            <div class="block-1__view">
                <div class="block-1__view-controls">
                    <?php
                    $views = [
                        'view1' => 'Получить список всех гостей, зарегистрированных в гостинице',
                        'view2' => 'Получить список всех доступных одноместных номеров',
                        'view3' => 'Получить список всех бронирований для определенного гостя',
                        'view4' => 'Получить список всех бронирований для определенного номера',
                        'view5' => 'Получить список всех гостей, проживающих в определенный период времени',
                        'view6' => 'Получить список всех свободных номеров в определенный период времени',
                        'view7' => 'Получить список гостей с количеством бронирований больше заданного',
                        'view8' => 'Получить список бронирований, совершенных определенным сотрудником',
                        'view9' => 'Получить список всех бронирований с именами гостей',
                        'view10' => 'Получить список гостей на определенном этаже'
                    ];
                    $active_view = isset($_GET['view']) ? $_GET['view'] : ''; // Активное представление
                    foreach ($views as $view => $title): ?>
                        <button class="block-1__view-btn <?php echo ($active_view === $view) ? 'active' : ''; ?>" 
                                data-view="<?php echo $view; ?>" 
                                title="<?php echo $title; ?>"> <!-- Кнопка выбора представления -->
                            <?php echo $view; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="block-1__view-output"></div> <!-- Контейнер для вывода данных представлений -->
        <?php endif; ?>
    </div>

    <script src="js/admin-panel.js"></script> <!-- Подключение скрипта админ-панели -->
</body>
</html>