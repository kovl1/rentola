<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение файла базы данных
include 'auth.php'; // Подключение файла авторизации

// Получение данных о домах с учетом типа дома
$sql_houses = "SELECT h.ID_House, h.Hause_name, h.House_photo, h.Adult_price, h.Child_price, t.Type_of_house, h.ID_Type_of_house 
               FROM house_data h 
               LEFT JOIN types_of_house t ON h.ID_Type_of_house = t.ID_Type_of_house 
               WHERE h.Available = 1";
$result_houses = mysqli_query($conn, $sql_houses);
if ($result_houses === false) {
    die("Ошибка запроса: " . mysqli_error($conn) . " (Запрос: $sql_houses)"); // Вывод ошибки при сбое запроса
}
$total_houses = mysqli_num_rows(mysqli_query($conn, "SELECT ID_House FROM house_data")); // Общее количество домов
$displayed_houses = mysqli_num_rows($result_houses); // Количество отображаемых доступных домов
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Адаптивность -->
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0"> <!-- Отключение кеширования -->
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&family=Tahoma&family=Halant&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/catalog.css"> <!-- Основной CSS для каталога -->
    <link rel="stylesheet" href="css/auth.css"> <!-- Стили для авторизации -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Каталог домов - Рентола</title> <!-- Заголовок страницы -->
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header__header">
                <div class="header__logo">
                    <img src="img/logo/logo.png" alt="Лого Рентола" class="header__logo-img"> <!-- Логотип -->
                </div>
                <nav class="header__nav">
                    <a href="index.php" class="header__link">ГЛАВНАЯ</a>
                    <a href="catalog.php" class="header__link">КАТАЛОГ ДОМОВ</a>
                    <a href="services.php" class="header__link">УСЛУГИ</a>
                    <a href="contacts.php" class="header__link">КОНТАКТЫ</a>
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
            <h1 class="catalog__title">КАТАЛОГ ДОМОВ</h1> <!-- Заголовок каталога -->
        </div>

        <div class="block-1-content-wrapper">
            <div class="block-1__content">
                <h2 class="content__subtitle">Варианты размещения: <?php echo $displayed_houses; ?> из <?php echo $total_houses; ?></h2> <!-- Подзаголовок с количеством домов -->
                <div class="houses-grid">
                    <?php
                    if ($result_houses && mysqli_num_rows($result_houses) > 0) {
                        while ($house = mysqli_fetch_assoc($result_houses)) {
                            $house_name = htmlspecialchars($house['Hause_name']); // Экранирование имени дома
                            $house_photo = !empty($house['House_photo']) ? htmlspecialchars($house['House_photo']) : 'img/house-photo.png'; // Фото дома с запасным изображением
                            $adult_price = (float)$house['Adult_price']; // Цена за взрослого
                            // Определение вместимости на основе ID_Type_of_house
                            $capacity = 1;
                            switch ($house['ID_Type_of_house']) {
                                case 2: $capacity = 2; break;
                                case 3: $capacity = 4; break;
                                case 4: $capacity = 6; break;
                            }
                            $total_price = $adult_price * $capacity; // Общая цена за всех людей
                            ?>
                            <div class="house-card">
                                <img src="<?php echo $house_photo; ?>" alt="<?php echo $house_name; ?>" class="house-card__image"> <!-- Изображение дома -->
                                <h3 class="house-card__title"><?php echo $house_name; ?></h3> <!-- Название дома -->
                                <p class="house-card__description">Гостевой домик расположен на южном берегу озера, в окружении чарующих пейзажей Карелии. Уютная атмосфера, просторный гостинный зал с камином и встроенная сауна...</p> <!-- Описание -->
                                <p class="house-card__price"><?php echo number_format($total_price, 0, '', ''); ?>р.<span class="price-unit">/сутки</span></p> <!-- Цена -->
                                <p class="house-card__capacity">Максимум: <?php echo $capacity; ?> человек<?php echo $capacity == 1 ? '' : 'а'; ?></p> <!-- Вместимость -->
                                <a href="house.php?id=<?php echo $house['ID_House']; ?>" class="house-card__button">УЗНАТЬ БОЛЬШЕ</a> <!-- Кнопка деталей -->
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>Нет доступных домов.</p>'; // Сообщение, если домов нет
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer__about">
            <h3 class="footer__title">О нас</h3> <!-- Заголовок -->
            <p class="footer__text">Гостиница в республике Карелии</p>
            <p class="footer__text">Рентола</p>
            <p class="footer__text">ул. Горького, д. 2</p>
            <p class="footer__text">г. Сортавала</p>
        </div>
        <div class="footer__logo">
            <img src="img/logo/logo.png" alt="Лого Рентола" class="footer__logo-img"> <!-- Логотип -->
            <p class="footer__copyright">Рентола 2025®</p> <!-- Копирайт -->
        </div>
        <div class="footer__contact">
            <h3 class="footer__title">Контакты</h3> <!-- Заголовок -->
            <p class="footer__text">+7 (987) 654-32-10</p> <!-- Телефон -->
            <p class="footer__text">Rentolahotel@mail.ru</p> <!-- Email -->
        </div>
    </footer>

    <div class="modal" id="authModal" <?php echo (isset($_SESSION['error']) || $show_signup) ? 'style="display: flex;"' : ''; ?>> <!-- Модальное окно -->
        <div class="modal-content">
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p> <!-- Вывод ошибки -->
            <?php endif; ?>
            <form action="catalog.php" method="POST" class="form form_signin" <?php echo !$show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма входа -->
                <h3 class="form__title">Вход в Рентола</h3>
                <input type="text" name="username" class="form__input" placeholder="Логин" required>
                <input type="password" name="password" class="form__input" placeholder="Пароль" required>
                <button type="submit" name="signin" class="form__btn">Войти</button>
                <div class="switch-form">
                    <a href="#" id="switchToRegister">Нет аккаунта? Зарегистрироваться</a> <!-- Переключение на регистрацию -->
                </div>
            </form>
            <form action="catalog.php" method="POST" class="form form_signup" <?php echo $show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма регистрации -->
                <h3 class="form__title">Регистрация в Рентола</h3>
                <input type="text" name="username" class="form__input" placeholder="Логин*" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required title="Логин (буквы, цифры, подчеркивания)" pattern="[a-zA-Z0-9_]+">
                <input type="text" name="name" class="form__input" placeholder="Ваше имя* (Иван)" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required title="Имя (только буквы и пробелы)" pattern="[а-яА-ЯёЁa-zA-Z\s]+">
                <input type="email" name="email" class="form__input" placeholder="Почта (example@mail.com)" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email (например, example@domain.com)">
                <input type="tel" name="phone" class="form__input" placeholder="Номер телефона* (+7 965 434 11 08)" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required title="Формат: +7XXXXXXXXXX" pattern="\+7\d{10}">
                <input type="password" name="password" class="form__input" placeholder="Пароль*" value="<?php echo htmlspecialchars($form_data['password'] ?? ''); ?>" required title="Введите пароль">
                <button type="submit" name="signup" class="form__btn">Зарегистрироваться</button>
                <div class="switch-form">
                    <a href="#" id="switchToLogin">Уже есть аккаунт? Войти</a> <!-- Переключение на вход -->
                </div>
            </form>
        </div>
    </div>
    <script src="js/main.js"></script> <!-- Основной JavaScript -->
    <script src="js/auth.js"></script> <!-- Основной JavaScript для авторизации -->
</body>
</html>