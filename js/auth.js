document.addEventListener('DOMContentLoaded', () => {
    /* Находим элементы модального окна и связанные с ним элементы */
    const modal = document.getElementById('authModal'); /* Модальное окно */
    const loginTrigger = document.getElementById('loginTrigger'); /* Элемент, открывающий модальное окно */
    const switchToRegister = document.getElementById('switchToRegister'); /* Ссылка для переключения на регистрацию */
    const switchToLogin = document.getElementById('switchToLogin'); /* Ссылка для переключения на вход */
    const signinForm = modal.querySelector('.form_signin'); /* Форма входа */
    const signupForm = modal.querySelector('.form_signup'); /* Форма регистрации */

    loginTrigger.addEventListener('click', (e) => {
        e.preventDefault(); /* Предотвращаем стандартное поведение ссылки */
        /* Проверяем, что клик был по блоку авторизации или иконке */
        if (e.target.closest('.header__auth') || e.target.classList.contains('header__auth-icon')) {
            modal.style.display = 'flex'; /* Показываем модальное окно как flex-контейнер */
        }
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) { /* Проверяем, что клик был именно по фону модального окна */
            modal.style.display = 'none'; /* Скрываем модальное окно */
        }
    });

    switchToRegister.addEventListener('click', (e) => {
        e.preventDefault();
        signinForm.style.display = 'none'; /* Скрываем форму входа */
        signupForm.style.display = 'block'; /* Показываем форму регистрации */
    });

    switchToLogin.addEventListener('click', (e) => {
        e.preventDefault();
        signupForm.style.display = 'none'; /* Скрываем форму регистрации */
        signinForm.style.display = 'block'; /* Показываем форму входа */
    });
});