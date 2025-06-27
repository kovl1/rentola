<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение файла базы данных
include 'auth.php'; // Подключение файла авторизации (содержит логику авторизации и регистрации)

// Обработка формы обратной связи без перенаправления
$feedback_error = ''; // Переменная для хранения ошибки
$feedback_success = ''; // Переменная для хранения сообщения об успехе
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['fio']); // Экранирование данных ФИО
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone'] ?? ''); // Экранирование номера телефона
    $mail = mysqli_real_escape_string($conn, $_POST['email']); // Экранирование email
    $question = mysqli_real_escape_string($conn, $_POST['message']); // Экранирование текста вопроса
    $date_of_creation = date('Y-m-d H:i:s'); // Текущая дата и время
    $id_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; // ID пользователя из сессии

    // Проверка на основе pattern
    if (empty($mail)) {
        $feedback_error = 'Поле почты обязательно для заполнения'; // Ошибка при пустом email
    } elseif (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $mail)) {
        $feedback_error = 'Некорректный формат email (например, example@domain.com)'; // Ошибка при неверном формате email
    } else {
        // Корректная обработка NULL в SQL
        $id_user_sql = $id_user !== null ? $id_user : 'NULL'; // Подготовка ID пользователя для SQL

        $sql = "INSERT INTO feedback (Full_name, Phone_number, Mail, Question, Date_of_creation, ID_User) 
                VALUES ('$full_name', '$phone_number', '$mail', '$question', '$date_of_creation', $id_user_sql)"; // SQL-запрос для вставки данных
        if (mysqli_query($conn, $sql)) {
            $feedback_success = 'Ваш вопрос успешно отправлен!'; // Успешное выполнение запроса
        } else {
            $feedback_error = 'Ошибка при отправке запроса: ' . mysqli_error($conn); // Ошибка при выполнении запроса
        }
    }
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null; // Получение имени пользователя из сессии
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0; // Проверка статуса администратора
$show_signup = isset($_SESSION['show_signup']) ? $_SESSION['show_signup'] : false; // Флаг отображения формы регистрации
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : []; // Данные формы из сессии
unset($_SESSION['show_signup']); // Очистка флага регистрации
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Настройка адаптивности -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/contacts.css"> <!-- Подключение стилей для страницы контактов -->
    <link rel="stylesheet" href="css/auth.css"> <!-- Подключение стилей для авторизации -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Контакты - Рентола</title> <!-- Заголовок страницы -->
</head>
<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__header">
                <div class="header__logo">
                    <img src="img/logo/logo.png" alt="Лого Рентола" class="header__logo-img"> <!-- Логотип сайта -->
                </div>
                <nav class="header__nav">
                    <a href="index.php" class="header__link">ГЛАВНАЯ</a>
                    <a href="catalog.php" class="header__link">КАТАЛОГ ДОМОВ</a>
                    <a href="services.php" class="header__link">УСЛУГИ</a>
                    <a href="contacts.php" class="header__link">КОНТАКТЫ</a>
                    <?php if ($is_admin): ?>
                        <a href="admin-panel.php" class="header__link">АДМИН-ПАНЕЛЬ</a> <!-- Ссылка на админ-панель для администраторов -->
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
            <h1 class="catalog__title">КОНТАКТЫ</h1> <!-- Заголовок секции -->
        </header>

        <div class="block-1">
            <div class="block-1-info">
                <h2 class="block-1__title">ЗВОНИТЕ НАМ</h2> <!-- Заголовок для контактных телефонов -->
                <div class="block-1__phone">
                    <p class="block-1__phonenum">+7 (987) 654-32-10</p> <!-- Первый номер телефона -->
                    <p class="block-1__phonenum">+7 (999) 999-99-99</p> <!-- Второй номер телефона -->
                </div>
                <h2 class="block-1__title">ПИШИТЕ НАМ</h2> <!-- Заголовок для email -->
                <p class="block-1__email">Rentolahotel@mail.ru</p> <!-- Email для связи -->
            </div>
            <div class="block-1-form">
                <h2 class="block-1-form__title">Обратная связь</h2> <!-- Заголовок формы обратной связи -->
                <form action="" method="POST" class="block-1-form__container"> <!-- Форма обратной связи -->
                    <div class="block-1-form__left">
                        <input type="text" name="fio" class="block-1-form__input" placeholder="ФИО*" required> <!-- Поле для ввода ФИО -->
                        <input type="tel" name="phone" class="block-1-form__input" placeholder="Номер телефона"> <!-- Поле для ввода телефона -->
                        <input type="email" name="email" class="block-1-form__input" placeholder="Почта*" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email (например, example@domain.com)"> <!-- Поле для ввода email -->
                    </div>
                    <div class="block-1-form__right">
                        <textarea name="message" class="block-1-form__textarea" placeholder="Ваш вопрос*" required></textarea> <!-- Поле для ввода вопроса -->
                        <button type="submit" name="submit" class="block-1-form__button">Отправить</button> <!-- Кнопка отправки формы -->
                    </div>
                </form>
                <?php if (!empty($feedback_error)): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($feedback_error); ?></p> <!-- Вывод сообщения об ошибке -->
                <?php endif; ?>
                <?php if (!empty($feedback_success)): ?>
                    <p style="color: green;"><?php echo htmlspecialchars($feedback_success); ?></p> <!-- Вывод сообщения об успехе -->
                <?php endif; ?>
            </div>
        </div>
    </div>
        
    <footer class="footer">
        <div class="footer__about">
            <h3 class="footer__title">О нас</h3> <!-- Заголовок секции -->
            <p class="footer__text">Гостиница в республике Карелия</p>
            <p class="footer__text">Рентола</p>
            <p class="footer__text">ул. Горького, д. 2</p>
            <p class="footer__text">г. Сортавала</p>
        </div>
        <div class="footer__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="footer__logo-img"> <!-- Логотип в футере -->
            <p class="footer__copyright">Рентола 2025®</p> <!-- Копирайт -->
        </div>
        <div class="footer__contact">
            <h3 class="footer__title">Контакты</h3> <!-- Заголовок секции -->
            <p class="footer__text">+7 (987) 654-32-10</p> <!-- Телефон -->
            <p class="footer__text">Rentolahotel@mail.ru</p> <!-- Email -->
        </div>
    </footer>

    <div class="modal" id="authModal" <?php echo (isset($_SESSION['error']) || $show_signup) ? 'style="display: flex;"' : 'style="display: none;"'; ?>> <!-- Модальное окно авторизации -->
        <div class="modal-content">
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p> <!-- Вывод сообщения об ошибке -->
            <?php endif; ?>
            <form action="contacts.php" method="POST" class="form form_signin" <?php echo !$show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма входа -->
                <h3 class="form__title">Вход в Рентола</h3> <!-- Заголовок формы -->
                <input type="text" name="username" class="form__input" placeholder="Логин" required> <!-- Поле для логина -->
                <input type="password" name="password" class="form__input" placeholder="Пароль" required> <!-- Поле для пароля -->
                <button type="submit" name="signin" class="form__btn">Войти</button> <!-- Кнопка отправки формы -->
                <div class="switch-form">
                    <a href="#" id="switchToRegister">Нет аккаунта? Зарегистрироваться</a> <!-- Ссылка для переключения на регистрацию -->
                </div>
            </form>
            <form action="contacts.php" method="POST" class="form form_signup" <?php echo $show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма регистрации -->
                <h3 class="form__title">Регистрация в Рентола</h3> <!-- Заголовок формы -->
                <input type="text" name="username" class="form__input" placeholder="Логин*" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required title="Логин (буквы, цифры, подчеркивания)" pattern="[a-zA-Z0-9_]+"> <!-- Поле для логина -->
                <input type="text" name="name" class="form__input" placeholder="Ваше имя* (Иван)" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required title="Имя (только буквы и пробелы)" pattern="[а-яА-ЯёЁa-zA-Z\s]+"> <!-- Поле для имени -->
                <input type="email" name="email" class="form__input" placeholder="Почта (example@mail.com)" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email (например, example@domain.com)"> <!-- Поле для email -->
                <input type="tel" name="phone" class="form__input" placeholder="Номер телефона* (+7 965 434 11 08)" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required title="Формат: +7XXXXXXXXXX" pattern="\+7\d{10}"> <!-- Поле для телефона -->
                <input type="password" name="password" class="form__input" placeholder="Пароль*" value="<?php echo htmlspecialchars($form_data['password'] ?? ''); ?>" required title="Введите пароль"> <!-- Поле для пароля -->
                <button type="submit" name="signup" class="form__btn">Зарегистрироваться</button> <!-- Кнопка отправки формы -->
                <div class="switch-form">
                    <a href="#" id="switchToLogin">Уже есть аккаунт? Войти</a> <!-- Ссылка для переключения на вход -->
                </div>
            </form>
            <?php if ($user_name): ?>
                <a href="#" id="logoutLink" class="form__btn" style="display: block; margin-top: 10px;">Выйти</a> <!-- Кнопка выхода из аккаунта -->
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.isAuthenticated = <?php echo json_encode($user_name !== null); ?>; // Передача состояния авторизации в JavaScript
        console.log('window.isAuthenticated:', window.isAuthenticated); // Вывод состояния авторизации в консоль для отладки
    </script>
    <script src="js/main.js"></script> <!-- Основной JavaScript -->
    <script src="js/auth.js"></script> <!-- Подключение скрипта для обработки авторизации -->
</body>
</html>