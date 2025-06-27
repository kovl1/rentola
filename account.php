<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение базы данных

// Проверка авторизации пользователя
if (!isset($_SESSION['user_name'])) {
    header('Location: index.php'); // Перенаправление на главную, если пользователь не авторизован
    exit();
}

// Получение данных пользователя
$user_login = $_SESSION['user_login']; // Логин текущего пользователя
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0; // Статус администратора

// Запрос данных из таблицы Users
$sql = "SELECT * FROM Users WHERE Login = '$user_login'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result); // Данные пользователя
    $_SESSION['user_data'] = $user_data; // Сохранение данных в сессии
} else {
    // Запрос данных из таблицы Staff
    $sql = "SELECT * FROM Staff WHERE Login = '$user_login'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result); // Данные сотрудника
        $_SESSION['user_data'] = $user_data; // Сохранение данных в сессии
    } else {
        header('Location: index.php'); // Перенаправление, если данные не найдены
        exit();
    }
}

// Обработка отмены заказа (только для не-админов)
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id']) && !$is_admin) {
    $booking_id = (int)$_POST['booking_id']; // ID бронирования
    $user_id = $user_data['ID_User']; // ID пользователя
    $sql_update = "UPDATE booking SET ID_Status_of_booking = 3 WHERE ID_Booking = ? AND ID_User = ? AND ID_Status_of_booking = 1"; // Обновление статуса на "Отменён"
    $stmt_update = mysqli_prepare($conn, $sql_update);
    if ($stmt_update === false) {
        die("Ошибка подготовки запроса: " . mysqli_error($conn)); // Ошибка подготовки запроса
    }
    mysqli_stmt_bind_param($stmt_update, "ii", $booking_id, $user_id); // Привязка параметров
    mysqli_stmt_execute($stmt_update); // Выполнение запроса
    mysqli_stmt_close($stmt_update); // Закрытие запроса
    header('Location: account.php'); // Перенаправление после отмены
    exit();
}

// Получение списка заказов
if (!$is_admin) {
    $user_id = $user_data['ID_User'] ?? null; // ID пользователя
    $sql_bookings = "SELECT b.*, h.Hause_name, h.House_photo 
                     FROM booking b 
                     LEFT JOIN house_data h ON b.ID_House = h.ID_House 
                     WHERE b.ID_User = ? 
                     ORDER BY b.Date_of_proceccing DESC"; // Запрос заказов пользователя
    $stmt = mysqli_prepare($conn, $sql_bookings);
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . mysqli_error($conn)); // Ошибка подготовки запроса
    }
    mysqli_stmt_bind_param($stmt, "i", $user_id); // Привязка ID пользователя
    mysqli_stmt_execute($stmt); // Выполнение запроса
    $result_bookings = mysqli_stmt_get_result($stmt); // Результат запроса
} else {
    $sql_bookings = "SELECT b.*, h.Hause_name, h.House_photo 
                     FROM booking b 
                     LEFT JOIN house_data h ON b.ID_House = h.ID_House 
                     ORDER BY b.Date_of_proceccing DESC"; // Запрос всех заказов для админа
    $result_bookings = mysqli_query($conn, $sql_bookings);
    if ($result_bookings === false) {
        die("Ошибка запроса: " . mysqli_error($conn)); // Ошибка выполнения запроса
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Адаптивность -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/account.css"> <!-- Подключение стилей -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Личный кабинет - Рентола</title> <!-- Заголовок страницы -->
</head>
<body>
    <header class="header">
        <div class="header__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="header__logo-img"> <!-- Логотип -->
        </div>
        <nav class="header__nav">
            <a href="index.php" class="header__link header__link_type_main">ГЛАВНАЯ</a> <!-- Ссылка на главную -->
            <a href="catalog.php" class="header__link header__link_type_catalog">КАТАЛОГ ДОМОВ</a> <!-- Ссылка на каталог -->
            <a href="services.php" class="header__link header__link_type_services">УСЛУГИ</a> <!-- Ссылка на услуги -->
            <a href="contacts.php" class="header__link header__link_type_contacts">КОНТАКТЫ</a> <!-- Ссылка на контакты -->
            <?php if ($is_admin): ?>
                <a href="admin-panel.php" class="header__link header__link_type_admin">АДМИН-ПАНЕЛЬ</a> <!-- Ссылка на админ-панель для админов -->
            <?php endif; ?>
        </nav>
        <div class="header__auth">
            <img src="img/mini-icons/profile-icon.png" alt="Иконка профиля" class="header__auth-icon"> <!-- Иконка профиля -->
            <a href="#" class="header__auth-link"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a> <!-- Имя пользователя -->
        </div>
    </header>

    <div class="block-1">
        <h1 class="block-1__title">ЛИЧНЫЙ КАБИНЕТ</h1> <!-- Заголовок страницы -->
        <div class="block-1-content">
            <div class="block-1__details">
                <div class="block-1__detail">
                    <p class="block-1__detail-text">Имя пользователя:</p> <!-- Метка имени -->
                    <input class="block-1__input" type="text" value="<?php echo htmlspecialchars($user_data['Name'] ?? $user_data['Full_name'] ?? ''); ?>" readonly> <!-- Поле имени -->
                </div>
                <div class="block-1__detail">
                    <p class="block-1__detail-text">Логин:</p> <!-- Метка логина -->
                    <input class="block-1__input" type="text" value="<?php echo htmlspecialchars($user_data['Login'] ?? ''); ?>" readonly> <!-- Поле логина -->
                </div>
                <div class="block-1__detail">
                    <p class="block-1__detail-text">Пароль:</p> <!-- Метка пароля -->
                    <input class="password-1__input" type="password" value="<?php echo htmlspecialchars($user_data['Password'] ?? ''); ?>" readonly onmouseover="this.type='text'" onmouseout="this.type='password'"> <!-- Поле пароля с переключением видимости -->
                </div>
                <div class="block-1__detail">
                    <p class="block-1__detail-text">Почта:</p> <!-- Метка почты -->
                    <input class="block-1__input" type="email" value="<?php echo htmlspecialchars($user_data['Mail'] ?? ''); ?>" readonly> <!-- Поле почты -->
                </div>
                <div class="block-1__detail">
                    <p class="block-1__detail-text">Номер телефона:</p> <!-- Метка телефона -->
                    <input class="block-1__input" type="tel" value="<?php echo htmlspecialchars($user_data['Phone_number'] ?? ($is_admin ? 'Не указан' : '')); ?>" readonly> <!-- Поле телефона -->
                </div>
                <div class="block-1__detail">
                    <a href="logout.php" class="block-1__logout-btn">Выйти</a> <!-- Кнопка выхода -->
                </div>
            </div>
            <div class="block-1__orders">
                <?php
                if ($result_bookings && mysqli_num_rows($result_bookings) > 0) {
                    while ($booking = mysqli_fetch_assoc($result_bookings)) {
                        $order_number = $booking['ID_Booking'] ?? 'N/A'; // Номер заказа
                        $house_photo = !empty($booking['House_photo']) ? htmlspecialchars($booking['House_photo']) : 'img/house-photo.png'; // Фото дома
                        $user_name = htmlspecialchars($user_data['Name'] ?? $user_data['Full_name'] ?? ''); // Имя пользователя
                        $phone_number = htmlspecialchars($user_data['Phone_number'] ?? ($is_admin ? 'Не указан' : '')); // Номер телефона
                        $status_id = $booking['ID_Status_of_booking'] ?? 1; // ID статуса
                        $status_text = ($status_id == 1) ? 'Рассматривается' : (($status_id == 2) ? 'Подтверждён' : (($status_id == 3) ? 'Отменён' : 'Заморожен')); // Текст статуса
                        ?>
                        <div class="block-1__order">
                            <img src="<?php echo $house_photo; ?>" alt="Дом" class="block-1__order-image"> <!-- Изображение дома -->
                            <div class="block-1__order-info">
                                <div class="block-1__order-row">
                                    <p class="block-1__order-text block-1__order-text--order">Заказ номер: <?php echo $order_number; ?></p> <!-- Номер заказа -->
                                    <p class="block-1__order-text block-1__order-text--price">Цена: <?php echo number_format($booking['Final_price'] ?? 0, 0, '', ''); ?> руб</p> <!-- Цена -->
                                    <p class="block-1__order-text block-1__order-text--status">Статус: <?php echo $status_text; ?></p> <!-- Статус -->
                                    <p class="block-1__order-text block-1__order-text--name">Имя: <?php echo $user_name; ?></p> <!-- Имя -->
                                </div>
                                <div class="block-1__order-row">
                                    <p class="block-1__order-text block-1__order-text--checkin">Дата заезда: <?php echo htmlspecialchars($booking['Arrival_date'] ?? 'Не указана'); ?></p> <!-- Дата заезда -->
                                    <p class="block-1__order-text block-1__order-text--checkout">Дата отъезда: <?php echo htmlspecialchars($booking['Departure_date'] ?? 'Не указана'); ?></p> <!-- Дата отъезда -->
                                    <p class="block-1__order-text block-1__order-text--phone">Номер телефона: <?php echo $phone_number; ?></p> <!-- Номер телефона -->
                                </div>
                            </div>
                            <div class="block-1__order-actions">
                                <?php if (!$is_admin && $status_id == 1): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $order_number; ?>"> <!-- Скрытое поле ID брони -->
                                        <button type="submit" name="cancel_booking" class="block-1__review-btn">ОТМЕНИТЬ</button> <!-- Кнопка отмены -->
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p class="no-orders-message">У вас пока нет заказов.</p>'; // Сообщение при отсутствии заказов
                }
                ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer__about">
            <h3 class="footer__title">О нас</h3> <!-- Заголовок раздела "О нас" -->
            <p class="footer__text">Гостиница в республике Карелия</p>
            <p class="footer__text">Рентола</p>
            <p class="footer__text">ул. Горького, д. 2</p> 
            <p class="footer__text">г. Сортавала</p> 
        </div>
        <div class="footer__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="footer__logo-img"> 
            <p class="footer__copyright">Рентола 2025®</p> 
        </div>
        <div class="footer__contact">
            <h3 class="footer__title">Контакты</h3> 
            <p class="footer__text">+7 (987) 654-32-10</p>
            <p class="footer__text">Rentolahotel@mail.ru</p>
        </div>
    </footer>
</body>
</html>