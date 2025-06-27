document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0]; // Получает текущую дату в формате YYYY-MM-DD
    const tomorrow = new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().split('T')[0]; // Получает завтрашнюю дату

    const checkinDateHidden = document.getElementById('checkinDateHidden');
    const checkoutDateHidden = document.getElementById('checkoutDateHidden');
    checkinDateHidden.min = today;
    checkoutDateHidden.min = tomorrow;

    document.getElementById('checkinDate').addEventListener('click', function() {
        checkinDateHidden.style.display = 'block';
        checkinDateHidden.focus();
    });

    document.getElementById('checkoutDate').addEventListener('click', function() {
        checkoutDateHidden.style.display = 'block';
        checkoutDateHidden.focus();
    });

    checkinDateHidden.onchange = function() {
        const selectedDate = checkinDateHidden.value;
        if (selectedDate) {
            document.getElementById('checkinDate').value = selectedDate;
            checkoutDateHidden.min = new Date(new Date(selectedDate).setDate(new Date(selectedDate).getDate() + 1)).toISOString().split('T')[0];
            checkinDateHidden.style.display = 'none';
        }
    };

    checkoutDateHidden.onchange = function() {
        const selectedDate = checkoutDateHidden.value;
        if (selectedDate) {
            document.getElementById('checkoutDate').value = selectedDate;
            checkoutDateHidden.style.display = 'none';
        }
    };

    [checkinDateHidden, checkoutDateHidden].forEach(datePicker => {
        datePicker.addEventListener('blur', function() {
            if (!this.value) {
                this.style.display = 'none';
            }
        });
    });

    document.getElementById('bookingTrigger').addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Booking trigger clicked, window.isAuthenticated:', window.isAuthenticated);
        if (typeof window.isAuthenticated === 'undefined' || !window.isAuthenticated) {
            document.getElementById('authModal').style.display = 'flex';
        } else {
            document.getElementById('bookingModal').style.display = 'flex';
        }
    });

    // Закрытие обоих модальных окон при клике вне их
    window.addEventListener('click', (e) => {
        const bookingModal = document.getElementById('bookingModal');
        const authModal = document.getElementById('authModal');
        if (e.target === bookingModal) {
            bookingModal.style.display = 'none';
            checkinDateHidden.style.display = 'none';
            checkoutDateHidden.style.display = 'none';
        }
        if (e.target === authModal) {
            authModal.style.display = 'none';
        }
    });
});