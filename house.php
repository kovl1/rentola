<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение базы данных
include 'auth.php'; // Подключение авторизации

// Определение переменных из сессии
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null; // Имя пользователя
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0; // Статус админа
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // ID пользователя или сотрудника

// Получение данных о доме
$house_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // ID дома из URL
if ($house_id <= 0) {
    die("Неверный ID дома."); // Ошибка при некорректном ID
}

$sql_house = "SELECT h.*, t.Type_of_house 
              FROM house_data h 
              LEFT JOIN types_of_house t ON h.ID_Type_of_house = t.ID_Type_of_house 
              WHERE h.ID_House = ?"; // Запрос данных дома
$stmt = mysqli_prepare($conn, $sql_house);
if ($stmt === false) {
    die("Ошибка подготовки запроса: " . mysqli_error($conn)); // Ошибка подготовки запроса
}
mysqli_stmt_bind_param($stmt, "i", $house_id); // Привязка ID дома
mysqli_stmt_execute($stmt); // Выполнение запроса
$result_house = mysqli_stmt_get_result($stmt); // Результат запроса

if ($result_house === false) {
    die("Ошибка выполнения запроса: " . mysqli_error($conn)); // Ошибка выполнения запроса
}

$house = mysqli_fetch_assoc($result_house); // Данные дома

if (!$house) {
    die("Дом не найден."); // Ошибка, если дом не существует
}

// Определение вместимости на основе ID_Type_of_house
$capacity = 1; // По умолчанию 1 человек (одноместный)
$max_adults = 1; // Максимум взрослых
$max_children = 0; // Максимум детей
if (isset($house['ID_Type_of_house'])) {
    switch ($house['ID_Type_of_house']) {
        case 2: // Двухместный
            $capacity = 2;
            $max_adults = 2;
            $max_children = 1;
            break;
        case 3: // Четырёхместный
            $capacity = 4;
            $max_adults = 4;
            $max_children = 2;
            break;
        case 4: // Шестиместный
            $capacity = 6;
            $max_adults = 6;
            $max_children = 2;
            break;
        default: // Одноместный (1)
            $capacity = 1;
            $max_adults = 1;
            $max_children = 0;
    }
}

// Обработка бронирования
if (isset($_POST['book'])) {
    error_log("Booking attempt: " . var_export($_SESSION, true)); // Логирование сессии
    if (!$user_name || !$user_id) {
        $_SESSION['error'] = 'Необходимо авторизоваться для бронирования.'; // Ошибка неавторизованного доступа
        header('Location: house.php?id=' . $house_id);
        exit();
    }

    $checkin_date = trim(mysqli_real_escape_string($conn, $_POST['checkin_date'])); // Дата заезда
    $checkout_date = trim(mysqli_real_escape_string($conn, $_POST['checkout_date'])); // Дата выезда
    $adults = (int)$_POST['adults']; // Количество взрослых
    $children = (int)$_POST['children']; // Количество детей

    // Проверка количества гостей
    if ($adults <= 0) {
        $_SESSION['error'] = 'Количество взрослых должно быть больше 0.'; // Ошибка при нуле взрослых
    } else {
        $total_guests = $adults + $children; // Общее количество гостей
        if ($total_guests > $capacity || $adults > $max_adults || $children > $max_children) {
            $_SESSION['error'] = 'Превышено максимальное количество гостей (' . $capacity . ' человек: до ' . $max_adults . ' взрослых и ' . $max_children . ' детей).'; // Ошибка превышения
        } else {
            $booking_user_id = $is_admin ? null : $user_id; // ID_User для пользователей
            $booking_staff_id = $is_admin ? $user_id : null; // ID_Staff для админов
            $status_id = 1; // Статус "Рассматривается"
            $final_price = ($adults * $house['Adult_price']) + ($children * $house['Child_price']); // Расчёт цены

            $sql_booking = "INSERT INTO booking (Arrival_date, Deporture_date, Number_of_adoults, Number_of_children, Final_price, Date_of_proceccing, ID_User, ID_Staff, ID_House, ID_Status_of_booking) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?)"; // Запрос бронирования
            $stmt_booking = mysqli_prepare($conn, $sql_booking);
            if ($stmt_booking === false) {
                $_SESSION['error'] = 'Ошибка подготовки бронирования: ' . mysqli_error($conn); // Ошибка подготовки
                error_log("Ошибка подготовки бронирования: " . mysqli_error($conn));
            } else {
                mysqli_stmt_bind_param($stmt_booking, "ssiidiiii", $checkin_date, $checkout_date, $adults, $children, $final_price, $booking_user_id, $booking_staff_id, $house_id, $status_id); // Привязка параметров
                $result = mysqli_stmt_execute($stmt_booking); // Выполнение запроса
                if ($result) {
                    header('Location: house.php?id=' . $house_id . '&success=1'); // Успех
                    exit();
                } else {
                    $_SESSION['error'] = 'Ошибка при бронировании: ' . mysqli_error($conn); // Ошибка бронирования
                    error_log("Ошибка бронирования: " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt_booking); // Закрытие запроса
            }
        }
    }
    header('Location: house.php?id=' . $house_id); // Перенаправление при ошибке
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Адаптивность -->
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0"> <!-- Отключение кеширования -->
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/house.css"> <!-- Основной CSS -->
    <link rel="stylesheet" href="css/auth.css"> <!-- Стили для авторизации -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title><?php echo htmlspecialchars($house['Hause_name']); ?> - Рентола</title> <!-- Заголовок страницы -->
</head>
<body>
    <div class="wrapper">
        <header class="header"> <!-- Шапка сайта -->
            <div class="header__header">
                <div class="header__logo">
                    <img src="img/logo/logo.png" alt="Лого Рентола" class="header__logo-img"> <!-- Логотип -->
                </div>
                <nav class="header__nav">
                    <a href="index.php" class="header__link">ГЛАВНАЯ</a> <!-- Ссылка на главную -->
                    <a href="catalog.php" class="header__link">КАТАЛОГ ДОМОВ</a> <!-- Ссылка на каталог -->
                    <a href="services.php" class="header__link">УСЛУГИ</a> <!-- Ссылка на услуги -->
                    <a href="contacts.php" class="header__link">КОНТАКТЫ</a> <!-- Ссылка на контакты -->
                    <?php if ($is_admin): ?>
                        <a href="admin-panel.php" class="header__link">АДМИН-ПАНЕЛЬ</a> <!-- Ссылка для админов -->
                    <?php endif; ?>
                </nav>
                <div class="header__auth" id="profileLink">
                    <img src="img/mini-icons/profile-icon.png" alt="Иконка профиля" class="header__auth-icon" id="userIcon"> <!-- Иконка профиля -->
                    <?php if ($user_name): ?>
                        <a href="account.php" class="header__auth-link"><?php echo htmlspecialchars($user_name); ?></a> <!-- Имя пользователя -->
                    <?php else: ?>
                        <a href="#" class="header__auth-link" id="loginTrigger">ВОЙТИ</a> <!-- Кнопка входа -->
                    <?php endif; ?>
                </div>
            </div>
            <h1 class="house__title"><?php echo htmlspecialchars($house['Hause_name']); ?></h1> <!-- Название дома -->
        </header>

        <div class="block-1">
            <div class="block-1-content-wrapper">
                <div class="block-1-content">
                    <div class="block-1-details">
                        <img src="<?php echo !empty($house['House_photo']) ? htmlspecialchars($house['House_photo']) : 'img/house-photo.png'; ?>" alt="<?php echo htmlspecialchars($house['Hause_name']); ?>" class="block-1-details__image"> <!-- Фото дома -->
                        <div class="block-1-details__info">
                            <div class="title-wrapper">
                                <h2 class="block-1-details__title">СТОИМОСТЬ</h2> <!-- Заголовок стоимости -->
                                <div class="line"></div> <!-- Линия-разделитель -->
                            </div>
                            <div class="block-1-details__pricing">
                                <div class="pricing-item">
                                    <p>Аренда дома на (<?php echo $capacity; ?> человек<?php echo $capacity == 1 ? '' : 'а'; ?>)</p>
                                    <p class="pricing-item__price"><?php echo number_format($capacity * $house['Adult_price'], 0, '', ''); ?> <span class="pricing-unit">руб./сут</span></p> <!-- Цена за дом -->
                                </div>
                                <div class="pricing-item">
                                    <p>Аренда на 1 человека</p>
                                    <p class="pricing-item__price"><?php echo number_format($house['Adult_price'], 0, '', ''); ?> <span class="pricing-unit">руб./сут</span></p> <!-- Цена за взрослого -->
                                </div>
                                <div class="pricing-item">
                                    <p>Аренда на 1 ребёнка</p>
                                    <p class="pricing-item__price"><?php echo number_format($house['Child_price'], 0, '', ''); ?> <span class="pricing-unit">руб./сут</span></p> <!-- Цена за ребёнка -->
                                </div>
                            </div>
                            <div class="title-wrapper">
                                <h2 class="block-1-details__title">ИНФОРМАЦИЯ</h2> <!-- Заголовок информации -->
                                <div class="line"></div> <!-- Линия-разделитель -->
                            </div>
                            <p class="block-1-details__text">Проживание с животными:<br>размещение с домашними животными не допускается<br>Число возможных гостей:<br><?php echo $max_adults; ?> взросл<?php echo $max_adults == 1 ? 'ый' : 'ых'; ?><br><?php echo $max_children; ?> ребён<?php echo $max_children == 1 ? 'ок' : 'ка'; ?></p> <!-- Информация о доме -->
                            <div class="title-wrapper">
                                <h2 class="block-1-details__title">БРОНИРОВАНИЕ</h2> <!-- Заголовок бронирования -->
                                <div class="line"></div> <!-- Линия-разделитель -->
                            </div>
                            <div class="block-1-details__booking">
                                <div class="booking-item">
                                    <div class="booking-times">
                                        <div>
                                            <p>Заселение:</p>
                                            <p class="booking-item__time">15:00</p> <!-- Время заселения -->
                                        </div>
                                        <div>
                                            <p>Выселение:</p>
                                            <p class="booking-item__time">12:00</p> <!-- Время выселения -->
                                        </div>
                                    </div>
                                    <p class="booking-item__phone">+7 (987) 654-32-10</p> <!-- Контактный телефон -->
                                </div>
                                <div class="booking-contact">
                                    <a href="#" class="booking-item__button" id="bookingTrigger">ЗАБРОНИРОВАТЬ</a> <!-- Кнопка бронирования -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <footer class="footer"> <!-- Подвал сайта -->
        <div class="footer__about">
            <h3 class="footer__title">О нас</h3> <!-- Заголовок "О нас" -->
            <p class="footer__text">Гостиница в республике Карелия</p>
            <p class="footer__text">Рентола</p>
            <p class="footer__text">ул. Горького, д. 2</p>
            <p class="footer__text">г. Сортавала</p>
        </div>
        <div class="footer__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="footer__logo-img"> <!-- Логотип -->
            <p class="footer__copyright">Рентола 2025®</p> <!-- Копирайт -->
        </div>
        <div class="footer__contact">
            <h3 class="footer__title">Контакты</h3> <!-- Заголовок "Контакты" -->
            <p class="footer__text">+7 (987) 654-32-10</p> <!-- Телефон -->
            <p class="footer__text">Rentolahotel@mail.ru</p> <!-- Email -->
        </div>
    </footer>

    <div class="modal" id="bookingModal" style="display: none;"> <!-- Модальное окно бронирования -->
        <div class="modal-content">
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p> <!-- Вывод ошибки -->
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <p class="success-message">Бронирование успешно!</p> <!-- Сообщение об успехе -->
            <?php endif; ?>
            <form action="house.php?id=<?php echo $house_id; ?>" method="POST" class="form form_booking" novalidate>
                <h3 class="form__title">Бронирование</h3> <!-- Заголовок формы -->
                <input type="text" name="checkin_date" class="form__input date-input" id="checkinDate" placeholder="Дата прибытия" readonly required> <!-- Дата заезда -->
                <input type="text" name="checkout_date" class="form__input date-input" id="checkoutDate" placeholder="Дата выбытия" readonly required> <!-- Дата выезда -->
                <input type="number" name="adults" class="form__input" id="adults" placeholder="Количество взрослых" min="1" max="<?php echo $max_adults; ?>" value="1" required> <!-- Количество взрослых -->
                <input type="number" name="children" class="form__input" id="children" placeholder="Количество детей" min="0" max="<?php echo $max_children; ?>" value="0" required> <!-- Количество детей -->
                <button type="submit" name="book" class="form__btn">Забронировать</button> <!-- Кнопка отправки -->
                <div class="switch-form">
                    <a href="contacts.php" id="contactLink">Возникла проблема? Написать нам</a> <!-- Ссылка на контакты -->
                </div>
                <input type="date" id="checkinDateHidden" class="date-picker" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"> <!-- Скрытое поле для даты заезда -->
                <input type="date" id="checkoutDateHidden" class="date-picker" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"> <!-- Скрытое поле для даты выезда -->
            </form>
        </div>
    </div>

    <div class="modal" id="authModal" <?php echo (isset($_SESSION['error']) || (isset($show_signup) && $show_signup)) ? 'style="display: flex;"' : 'style="display: none;"'; ?>> <!-- Модальное окно авторизации -->
        <div class="modal-content">
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p> <!-- Вывод ошибки -->
            <?php endif; ?>
            <form action="house.php?id=<?php echo $house_id; ?>" method="POST" class="form form_signin" id="signinForm" <?php echo !isset($show_signup) || !$show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма входа -->
                <h3 class="form__title">Вход в Рентола</h3> <!-- Заголовок формы входа -->
                <input type="text" name="username" class="form__input" placeholder="Логин" required> <!-- Поле логина -->
                <input type="password" name="password" class="form__input" placeholder="Пароль" required> <!-- Поле пароля -->
                <button type="submit" name="signin" class="form__btn">Войти</button> <!-- Кнопка входа -->
                <div class="switch-form">
                    <a href="#" id="switchToRegister">Нет аккаунта? Зарегистрироваться</a> <!-- Ссылка на регистрацию -->
                </div>
            </form>
            <form action="house.php?id=<?php echo $house_id; ?>" method="POST" class="form form_signup" id="signupForm" <?php echo isset($show_signup) && $show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма регистрации -->
                <h3 class="form__title">Регистрация в Рентола</h3> <!-- Заголовок формы регистрации -->
                <input type="text" name="username" class="form__input" placeholder="Логин*" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required title="Логин (буквы, цифры, подчеркивания)" pattern="[a-zA-Z0-9_]+"> <!-- Поле логина -->
                <input type="text" name="name" class="form__input" placeholder="Ваше имя* (Иван)" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required title="Имя (только буквы и пробелы)" pattern="[а-яА-ЯёЁa-zA-Z\s]+"> <!-- Поле имени -->
                <input type="email" name="email" class="form__input" placeholder="Почта (example@mail.com)" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email"> <!-- Поле почты -->
                <input type="tel" name="phone" class="form__input" placeholder="Номер телефона* (+7 965 434 11 08)" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required title="Формат: +7XXXXXXXXXX" pattern="\+7\d{10}"> <!-- Поле телефона -->
                <input type="password" name="password" class="form__input" placeholder="Пароль*" value="<?php echo htmlspecialchars($form_data['password'] ?? ''); ?>" required title="Введите пароль"> <!-- Поле пароля -->
                <button type="submit" name="signup" class="form__btn">Зарегистрироваться</button> <!-- Кнопка регистрации -->
                <div class="switch-form">
                    <a href="#" id="switchToLogin">Уже есть аккаунт? Войти</a> <!-- Ссылка на вход -->
                </div>
            </form>
        </div>
    </div>

    <script>
        window.isAuthenticated = <?php echo json_encode($user_name !== null); ?>; // Статус авторизации
        console.log('window.isAuthenticated:', window.isAuthenticated); // Логирование статуса
    </script>
    <script src="js/main.js"></script> <!-- Основной JavaScript -->
    <script src="js/auth.js"></script> <!-- Подключение скрипта авторизации -->
    <script src="js/booking.js"></script> <!-- Подключение скрипта бронирования -->
</body>
</html>