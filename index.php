<?php
session_start(); // Инициализация сессии
include_once 'db.php'; // Подключение файла базы данных
include 'auth.php'; // Подключение файла авторизации
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"> <!-- Установка кодировки -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Адаптивность -->
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&family=Inter:wght@100..900&display=swap" rel="stylesheet"> <!-- Подключение шрифтов -->
    <link rel="stylesheet" href="css/main.css"> <!-- Основной CSS -->
    <link rel="stylesheet" href="css/auth.css"> <!-- Стили для авторизации -->
    <link rel="shortcut icon" href="img/logo/icon-logo.png"> <!-- Иконка сайта -->
    <title>Рентола</title> <!-- Заголовок страницы -->
    <script>
        const isUserLoggedIn = <?php echo json_encode(!!$user_name); ?>; // Передача статуса авторизации
    </script>
</head>
<body>
    <div class="wrapper">
        <header class="header">
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
        </header>

        <div class="block-1">
            <div class="block-1__content"> 
                <h1 class="block-1__title">Рентола</h1> <!-- Главный заголовок -->
                <img src="img/Patterns/big-pattern.png" alt="Узор" class="block-1__pattern"> <!-- Декоративный узор -->
                <p class="block-1__subtitle">ГОСТЕВЫЕ ДОМА В КАРЕЛИИ</p> <!-- Подзаголовок -->
                <a href="catalog.php" class="block-1__btn">В КАТАЛОГ</a> <!-- Кнопка каталога -->
                <img src="img/arrow.png" alt="Стрелка вниз" class="block-1__arrow"> <!-- Стрелка -->
            </div>
        </div>

        <div class="block-2">
            <img src="img/Patterns/pattern.png" alt="Узор 2" class="block-2__pattern-top"> <!-- Верхний узор -->
            <div class="block-2__content">
                <div>
                    <div>
                        <h2 class="block-2__title">Сезоны</h2> <!-- Заголовок секции -->
                        <p class="block-2__text">В любое время года Вы найдете развлечение по душе, отдыхая в загородном доме. 
                            В летний сезон хороши водные прогулки и активные игры, осенью приятно прогуляться по лесу или сходить в баню и рыбалка ждут Вас круглый год!</p>
                    </div>
                    <div class="block-2__separator"></div> <!-- Разделитель -->
                    <div>
                        <h2 class="block-2__title">Стоимость</h2> <!-- Заголовок секции -->
                        <p class="block-2__text">Стоимость аренды коттеджа зависит от интересующих Вас дат. 
                            Как правило, во время каникул и в праздничные дни цена отличается от стандартной. 
                            Узнайте более подробную информацию и не забудьте проверить акции и спецпредложения!</p>
                    </div>
                    <div class="block-2__separator"></div> <!-- Разделитель -->
                    <div>
                        <h2 class="block-2__title">Акции</h2> <!-- Заголовок секции -->
                        <p class="block-2__text">Мы предоставляем специальные цены для больших компаний и молодых семей, а также сезонные скидки. 
                            Все акции и спецпредложения Вы можете увидеть на нашем сайте. Отдых в загородном доме - не только отдых, а забота о здоровье!</p>
                    </div>
                </div>
            </div>
            <img src="img/Patterns/pattern.png" alt="Узор 2" class="block-2__pattern-bottom"> <!-- Нижний узор -->
        </div>

        <div class="block-3">
            <h2 class="block-3__header">КАРЕЛЬСКАЯ КУХНЯ</h2> <!-- Заголовок секции -->
            <p class="block-3__subheader">традиционные блюда карелов в ресторане Rentola</p> <!-- Подзаголовок -->
            <div class="block-3__container">
                <img src="img/kitchen.png" alt="Карельская кухня" class="block-3__image"> <!-- Изображение -->
                <div class="block-3__text-content">
                    <h3 class="block-3__title">ПРОСТО, НО СО ВКУСОМ</h3> <!-- Заголовок текста -->
                    <p class="block-3__description">Приглашаем вас попробовать поистине карельские блюда, приготовленные по всем традициям местного народа. В нашем колоритном ресторане можно не только вкусно перекусить, но и замечательно провести время в кругу близких и друзей. Здесь каждый найдёт чем себя удивить и порадовать!</p>
                    <p class="block-3__chefoffer">Наш шеф-повар рад предложить:</p>
                    <ul class="block-3__points">
                        <li>Традиционные блюда карельской кухни.</li>
                        <li>Комплексное питание: завтраки, обеды, ужины.</li>
                        <li>Вкуснейшие десерты.</li>
                        <li>Алкогольные и безалкогольные напитки местного производства.</li>
                    </ul>
                    <div class="block-3__contact">
                        <p class="block-3__phone">+7 (999) 999-99-99</p> <!-- Телефон -->
                        <div class="block-3__hours">
                            <img src="img/mini-icons/clock-icon.png" alt="Иконка часов" class="block-3__hours-icon"> <!-- Иконка часов -->
                            <p class="block-3__hours-text">9.00-22.00</p> <!-- Время работы -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="block-4">
            <img src="img/Patterns/pattern.png" alt="Узор 4" class="block-4__pattern-top"> <!-- Верхний узор -->
            <h2 class="block-4__title">УСЛУГИ И СЕРВИС</h2> <!-- Заголовок секции -->
            <p class="block-4__text">мы постараемся сделать ваше пребывание максимально комфортным и насыщенным</p>
            <div class="block-4__icons">
                <img src="img/services-icons/icon-bike.png" alt="Велосипед" class="block-4__icon"> <!-- Иконка -->
                <img src="img/services-icons/icon-fish.png" alt="Рыбалка" class="block-4__icon"> <!-- Иконка -->
                <img src="img/services-icons/icon-rowing.png" alt="Гребля" class="block-4__icon"> <!-- Иконка -->
                <img src="img/services-icons/icon-jet-ski.png" alt="Гидроцикл" class="block-4__icon"> <!-- Иконка -->
                <img src="img/services-icons/icon-sauna.png" alt="Баня" class="block-4__icon"> <!-- Иконка -->
                <img src="img/services-icons/icon-quad.png" alt="Квадроцикл" class="block-4__icon"> <!-- Иконка -->
            </div>
            <a href="services.php" class="block-4__btn">СМОТРЕТЬ ВСЕ УСЛУГИ</a> <!-- Кнопка услуг -->
        </div>

        <div class="block-5">
            <h2 class="block-5__title">ПОСЛЕДНИЕ ОТЗЫВЫ</h2> <!-- Заголовок секции -->
            <div class="block-5__reviews">
                <div class="block-5__review">
                    <p class="block-5__text"><img src="img/mini-icons/quote-icon.png" alt="Иконка цитаты" class="block-5__quote-icon"> Уютный дом со всеми удобствами. 
                        Отдыхали четверо взрослых и четверо детей. Отличный вид на озеро. Все отделано натуральным деревом, здорово. 
                        Дети были в восторге над помещением под крышей с кроватью, там только и сидели))) 
                        Маленькому ребенку (9 месяцев) дополнительно поставили кроватку, чем маму очень порадовали. Большое спасибо за гостеприимство!!!!</p>
                    <p class="block-5__author">— Александр</p> <!-- Автор отзыва -->
                </div>
                <div class="block-5__review">
                    <p class="block-5__text"><img src="img/mini-icons/quote-icon.png" alt="Иконка цитаты" class="block-5__quote-icon"> Отдыхали в августе в доме ЛЕСНОЕ ОЗЕРО-4. 
                        Дом отличный, очень все продумано, удобно, красиво. Для спокойного и уединенного отдыха - идеальное место. Спасибо.</p>
                    <p class="block-5__author">— Елена</p> <!-- Автор отзыва -->
                </div>
                <div class="block-5__review">
                    <p class="block-5__text"><img src="img/mini-icons/quote-icon.png" alt="Иконка цитаты" class="block-5__quote-icon"> Это чудесное место для отдыха, очень красивое и уютное. 
                        Все сделано со вкусом и заботой о гостях. Есть все необходимое для проживания и отдыха - полностью оборудованная кухня, принадлежности для барбекю, отличная баня, лодки, катамараны. 
                        Особенно нас покорил панорамный вид на озеро!</p>
                    <p class="block-5__author">— Дмитрий</p> <!-- Автор отзыва -->
                </div>
                <div class="block-5__review">
                    <p class="block-5__text"><img src="img/mini-icons/quote-icon.png" alt="Иконка цитаты" class="block-5__quote-icon"> Огромное спасибо. Все было просто на 10 баллов. 
                        Очень комфортный дом. Минусы отсутствуют. Природа Карелии влюбляли в себя постоянно. Прекрасное озеро, дивный лес с огромным количеством ягод и грибов. 
                        Всем советую этот прекрасный уголок.</p>
                    <p class="block-5__author">— Марина</p> <!-- Автор отзыва -->
                </div>
                <div class="block-5__review">
                    <p class="block-5__text"><img src="img/mini-icons/quote-icon.png" alt="Иконка цитаты" class="block-5__quote-icon"> Мы бронировали домик с сауной на 4 человек +2 ребёнка на выходные!
                        Замечательное место, очень красивая природа, новый обустроенный дом. Перед домом терраса и полянка. Собственный спуск к озеру. Дом оборудован всем необходимым. 
                        Очень понравилось!</p>
                    <p class="block-5__author">— Игорь</p> <!-- Автор отзыва -->
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="footer__about">
            <h3 class="footer__title">О нас</h3> <!-- Заголовок -->
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
            <h3 class="footer__title">Контакты</h3> <!-- Заголовок -->
            <p class="footer__text">+7 (987) 654-32-10</p> <!-- Телефон -->
            <p class="footer__text">Rentolahotel@mail.ru</p> <!-- Email -->
        </div>
    </footer>

    <div class="modal" id="authModal" <?php echo (isset($_SESSION['error']) || $show_signup) ? 'style="display: flex;"' : ''; ?>> <!-- Модальное окно -->
        <div class="modal-content">
            <?php if (isset($_SESSION['error'])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p> <!-- Вывод ошибки -->
            <?php endif; ?>
            <form action="index.php" method="POST" class="form form_signin" <?php echo !$show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма входа -->
                <h3 class="form__title">Вход в Рентола</h3>
                <input type="text" name="username" class="form__input" placeholder="Логин*" required>
                <input type="password" name="password" class="form__input" placeholder="Пароль*" required pattern="[a-zA-Z0-9]+">
                <button type="submit" name="signin" class="form__btn">Войти</button>
                <div class="switch-form">
                    <a href="#" id="switchToRegister">Нет аккаунта? Зарегистрироваться</a> <!-- Переключение на регистрацию -->
                </div>
            </form>
            <form action="index.php" method="POST" class="form form_signup" <?php echo $show_signup ? '' : 'style="display: none;"'; ?>> <!-- Форма регистрации -->
                <h3 class="form__title">Регистрация в Рентола</h3>
                <input type="text" name="username" class="form__input" placeholder="Логин*" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required title="Логин (буквы, цифры, подчеркивания)" pattern="[a-zA-Z0-9_]+">
                <input type="text" name="name" class="form__input" placeholder="Ваше имя* (Иван)" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                <input type="email" name="email" class="form__input" placeholder="Почта (example@mail.com)" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Введите корректный email (например, example@domain.com)">
                <input type="tel" name="phone" class="form__input" placeholder="Номер телефона* (+7 987 654 32 10)" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required title="Формат: +7XXXXXXXXXX" pattern="\+7\d{10}">
                <input type="password" name="password" class="form__input" placeholder="Пароль*" value="<?php echo htmlspecialchars($form_data['password'] ?? ''); ?>" required title="Пароль (только буквы, цифры или их комбинация)" pattern="[a-zA-Z0-9]+">
                <button type="submit" name="signup" class="form__btn">Зарегистрироваться</button>
                <div class="switch-form">
                    <a href="#" id="switchToLogin">Уже есть аккаунт? Войти</a> <!-- Переключение на вход -->
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script> <!-- Основной JavaScript -->
    <script src="js/auth.js"></script> <!-- Скрипт авторизации -->
</body>
</html>