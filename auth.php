<?php
include_once 'db.php'; // Подключение базы данных

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin'])) { // Обработка входа
    $username = mysqli_real_escape_string($conn, trim($_POST['username'])); // Очистка и экранирование логина
    $password = mysqli_real_escape_string($conn, trim($_POST['password'])); // Очистка и экранирование пароля

    if (empty($username) || empty($password)) { // Проверка заполнения полей
        $_SESSION['error'] = 'Все поля обязательны для заполнения'; // Ошибка при пустых полях
    } else {
        $sql_users = "SELECT Name, ID_User FROM Users WHERE Login = ? AND Password = ?"; // Проверка учетной записи пользователя
        $stmt_users = mysqli_prepare($conn, $sql_users);
        mysqli_stmt_bind_param($stmt_users, "ss", $username, $password);
        mysqli_stmt_execute($stmt_users);
        $result_users = mysqli_stmt_get_result($stmt_users);

        if (mysqli_num_rows($result_users) == 1) { // Если пользователь найден
            $row = mysqli_fetch_assoc($result_users);
            $_SESSION['user_name'] = $row['Name']; // Сохранение имени пользователя
            $_SESSION['user_login'] = $username; // Сохранение логина
            $_SESSION['user_id'] = $row['ID_User']; // Сохранение ID пользователя
            $_SESSION['is_admin'] = 0; // Установка статуса не администратора
            $_SESSION['user_data'] = ['id' => $row['ID_User'], 'name' => $row['Name']]; // Сохранение данных пользователя
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php')); // Перенаправление с fallback
            exit();
        }

        $sql_staff = "SELECT Full_name AS Name, ID_Staff FROM Staff WHERE Login = ? AND Password = ?"; // Проверка учетной записи персонала
        $stmt_staff = mysqli_prepare($conn, $sql_staff);
        mysqli_stmt_bind_param($stmt_staff, "ss", $username, $password);
        mysqli_stmt_execute($stmt_staff);
        $result_staff = mysqli_stmt_get_result($stmt_staff);

        if (mysqli_num_rows($result_staff) == 1) { // Если сотрудник найден
            $row = mysqli_fetch_assoc($result_staff);
            $_SESSION['user_name'] = $row['Name']; // Сохранение имени сотрудника
            $_SESSION['user_login'] = $username; // Сохранение логина
            $_SESSION['user_id'] = $row['ID_Staff']; // Сохранение ID сотрудника
            $_SESSION['is_admin'] = 1; // Установка статуса администратора
            $_SESSION['user_data'] = ['id' => $row['ID_Staff'], 'name' => $row['Name']]; // Сохранение данных персонала
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php')); // Перенаправление с fallback
            exit();
        }

        $_SESSION['error'] = 'Неверный логин или пароль'; // Ошибка при неверных учетных данных
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) { // Обработка регистрации
    $username = mysqli_real_escape_string($conn, trim($_POST['username'])); // Очистка и экранирование логина
    $name = mysqli_real_escape_string($conn, trim($_POST['name'])); // Очистка и экранирование имени
    $email = mysqli_real_escape_string($conn, trim($_POST['email'])); // Очистка и экранирование email
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'])); // Очистка и экранирование телефона
    $password = mysqli_real_escape_string($conn, trim($_POST['password'])); // Очистка и экранирование пароля

    if (empty($username) || empty($name) || empty($phone) || empty($password)) { // Проверка обязательных полей
        $_SESSION['error'] = 'Все поля, отмеченные звездочкой (*), обязательны для заполнения'; // Ошибка при пустых полях
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) { // Проверка формата email
        $_SESSION['error'] = 'Некорректный формат email'; // Ошибка при неверном email
    } elseif (!preg_match('/^\+7\d{10}$/', $phone)) { // Проверка формата телефона
        $_SESSION['error'] = 'Номер телефона должен быть в формате +7XXXXXXXXXX (10 цифр)'; // Ошибка при неверном формате телефона
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) { // Проверка формата логина
        $_SESSION['error'] = 'Логин может содержать только буквы, цифры и подчеркивания'; // Ошибка при неверном формате логина
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $password)) { // Проверка формата пароля
        $_SESSION['error'] = 'Пароль должен содержать только буквы, цифры или их комбинацию'; // Ошибка при неверном формате пароля
    } else {
        // Проверка уникальности логина
        $check_sql = "SELECT ID_User FROM Users WHERE Login = ?";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $check_result = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($check_result) > 0) { // Если логин уже занят
            $_SESSION['error'] = 'Пользователь с таким логином уже существует'; // Ошибка при существующем логине
        } else {
            // Регистрация нового пользователя
            $sql = "INSERT INTO Users (Login, Name, Mail, Phone_number, Password) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $username, $name, $email, $phone, $password);
            if (mysqli_stmt_execute($stmt)) { // Если регистрация успешна
                // Получение данных нового пользователя
                $sql_user_data = "SELECT ID_User, Name FROM Users WHERE Login = ? AND Password = ?";
                $stmt_user_data = mysqli_prepare($conn, $sql_user_data);
                mysqli_stmt_bind_param($stmt_user_data, "ss", $username, $password);
                mysqli_stmt_execute($stmt_user_data);
                $result_user_data = mysqli_stmt_get_result($stmt_user_data);
                if ($result_user_data) {
                    $user_data = mysqli_fetch_assoc($result_user_data);
                    $_SESSION['user_name'] = $user_data['Name']; // Сохранение имени пользователя
                    $_SESSION['user_login'] = $username; // Сохранение логина
                    $_SESSION['user_id'] = $user_data['ID_User']; // Сохранение ID пользователя
                    $_SESSION['is_admin'] = 0; // Установка статуса не администратора
                    $_SESSION['user_data'] = $user_data; // Сохранение данных пользователя
                }
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php')); // Перенаправление с fallback
                exit();
            } else {
                $_SESSION['error'] = 'Ошибка при сохранении пользователя'; // Ошибка при выполнении SQL
            }
        }
    }
    // Сохранение данных формы при ошибке
    $_SESSION['form_data'] = ['username' => $username, 'name' => $name, 'email' => $email, 'phone' => $phone, 'password' => ''];
    $_SESSION['show_signup'] = true; // Показ формы регистрации
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null; // Получение имени пользователя
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0; // Получение статуса администратора
$show_signup = isset($_SESSION['show_signup']) ? $_SESSION['show_signup'] : false; // Проверка флага регистрации
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : []; // Получение данных формы
unset($_SESSION['show_signup']); // Удаление флага регистрации
?>