<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение файла базы данных
include 'auth.php'; // Подключение файла авторизации (здесь вся логика авторизации и регистрации)
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Настройка адаптивности -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/services.css"> <!-- Подключение стилей для страницы услуг -->
    <link rel="stylesheet" href="css/auth.css"> <!-- Подключение стилей для авторизации -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Услуги - Рентола</title> <!-- Заголовок страницы -->
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
            <h1 class="services__title">УСЛУГИ</h1> <!-- Заголовок секции услуг -->
        </header>

        <div class="block-1">
            <img src="img/Patterns/pattern.png" alt="Узор" class="block-1-pattern-top"> <!-- Верхний декоративный узор -->
            <div class="block-1-content-wrapper">
                <p class="block-1-intro">Отдыхайте с комфортом, пользуясь дополнительными услугами. Наша цель организовать Ваш отпуск или выходные в Карелии так, чтобы время, проведенное на отдыхе, осталось с Вами, как одно из самых насыщенных, увлекательных и запоминающихся впечатлений.</p> <!-- Вступительный текст -->
                <div class="block-1-row">
                    <img src="img/services-photo/bike.png" alt="Велосипед" class="block-1-image"> <!-- Изображение для велопроката -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">ВЕЛОПРОКАТ</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">Мимо сосен и елей!<br>Мы предлагаем: Велосипеды и байки<br>Стоимость: 1 час = 900р., 2 часа = 1500р., 3 часа = 2000р., день = 3000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
                <div class="block-1-row reverse">
                    <img src="img/services-photo/fish.png" alt="Рыбалка" class="block-1-image"> <!-- Изображение для рыбалки -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">РЫБАЛКА</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">Ловись рыбка большая или маленькая!<br>Мы предлагаем: Удочки и снасти<br>Стоимость: 1 час = 900р., 2 часа = 1500р., 3 часа = 2000р., день = 3000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
                <div class="block-1-row">
                    <img src="img/services-photo/rowing.png" alt="Гребля" class="block-1-image"> <!-- Изображение для аренды лодок -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">АРЕНДА ЛОДОК</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">Попутного ветра капитан!<br>Мы предлагаем: Моторные и вёсельные лодки<br>Стоимость: 1 час = 900р., 2 часа = 1500р., 3 часа = 2000р., день = 3000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
                <div class="block-1-row reverse">
                    <img src="img/services-photo/jet-ski.png" alt="Гидроцикл" class="block-1-image"> <!-- Изображение для аренды гидроскутеров -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">АРЕНДА ГИДРОСКУТЕРОВ</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">Не наткнись на рифы!<br>Мы предлагаем: Гидроцикл Ямаха (110 л.с.), 2 места<br>Стоимость: 30 мин = 6000р., 1 час = 10000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
                <div class="block-1-row">
                    <img src="img/services-photo/quad.png" alt="Квадроцикл" class="block-1-image"> <!-- Изображение для аренды квадроциклов -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">АРЕНДА КВАДРОЦИКЛОВ</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">По машинам!<br>Мы предлагаем: Квадроциклы<br>Стоимость: 1 час = 900р., 2 часа = 1500р., 3 часа = 2000р., день = 3000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
                <div class="block-1-row reverse">
                    <img src="img/services-photo/sauna.png" alt="Баня" class="block-1-image"> <!-- Изображение для бани -->
                    <div class="block-1-text">
                        <h3 class="block-1-title">БАНЯ</h3> <!-- Заголовок услуги -->
                        <p class="block-1-description">По машинам!<br>Мы предлагаем: Веники и температуру от 90°С до 110°С<br>Стоимость: 3 часа = 6500р., сеанс = 9000р.</p> <!-- Описание услуги -->
                    </div>
                </div>
            </div>
            <img src="img/Patterns/pattern.png" alt="Узор" class="block-1-pattern-bottom"> <!-- Нижний декоративный узор -->
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
            <form action="services.php" method="POST" class="form form_signin" <?php echo !$show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма входа -->
                <h3 class="form__title">Вход в Рентола</h3> <!-- Заголовок формы -->
                <input type="text" name="username" class="form__input" placeholder="Логин" required>
                <input type="password" name="password" class="form__input" placeholder="Пароль" required>
                <button type="submit" name="signin" class="form__btn">Войти</button> <!-- Кнопка отправки формы -->
                <div class="switch-form">
                    <a href="#" id="switchToRegister">Нет аккаунта? Зарегистрироваться</a> <!-- Ссылка для переключения на регистрацию -->
                </div>
            </form>
            <form action="services.php" method="POST" class="form form_signup" <?php echo $show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма регистрации -->
                <h3 class="form__title">Регистрация в Рентола</h3> <!-- Заголовок формы -->
                <input type="text" name="username" class="form__input" placeholder="Логин*" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required title="Логин (буквы, цифры, подчеркивания)" pattern="[a-zA-Z0-9_]+">
                <input type="text" name="name" class="form__input" placeholder="Ваше имя* (Иван)" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required title="Имя (только буквы и пробелы)" pattern="[а-яА-ЯёЁa-zA-Z\s]+">
                <input type="email" name="email" class="form__input" placeholder="Почта (example@mail.com)" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email (например, example@domain.com)">
                <input type="tel" name="phone" class="form__input" placeholder="Номер телефона* (+7 965 434 11 08)" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required title="Формат: +7XXXXXXXXXX" pattern="\+7\d{10}">
                <input type="password" name="password" class="form__input" placeholder="Пароль*" value="<?php echo htmlspecialchars($form_data['password'] ?? ''); ?>" required title="Введите пароль">
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