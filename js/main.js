// Логика для переключения отзывов
document.addEventListener('DOMContentLoaded', function() {
    let currentReview = 0; // Текущий индекс отзыва, начинается с 0
    const reviews = document.querySelectorAll('.block-5__review'); // Все элементы отзывов с классом block-5__review
    const totalReviews = reviews.length; // Общее количество отзывов

    function showReview(index) { // Показывает отзыв по переданному индексу
        reviews.forEach((review, i) => { // Перебирает все отзывы
            review.style.opacity = '0'; // Скрывает все отзывы
            if (i === index) { // Если индекс совпадает с текущим
                review.style.opacity = '1'; // Показывает нужный отзыв
            }
        });
    }

    function nextReview() { // Переключает на следующий отзыв
        currentReview = (currentReview + 1) % totalReviews; // Увеличивает индекс, возвращается к 0 при достижении конца
        showReview(currentReview); // Показывает следующий отзыв
    }

    if (totalReviews > 0) {
        setInterval(nextReview, 5000); // Запускает переключение отзывов каждые 5 секунд
        showReview(currentReview); // Показывает первый отзыв при загрузке страницы
    } else {
        console.warn('Отзывы не найдены. Проверьте класс .block-5__review');
    }

    // Логика для иконки пользователя
    const userIcon = document.getElementById('userIcon');
    const authModal = document.getElementById('authModal');

    // Перенаправление или открытие модального окна по клику на иконку пользователя
    if (userIcon && authModal) {
        userIcon.addEventListener('click', function(e) {
            e.preventDefault();
            if (isUserLoggedIn) { // Используем переданную переменную
                window.location.href = 'account.php';
            } else {
                authModal.style.display = 'flex';
            }
        });
    }
});