document.addEventListener('DOMContentLoaded', () => {
    const viewButtons = document.querySelectorAll('.block-1__view-btn');
    const outputDiv = document.querySelector('.block-1__view-output');
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            const view = button.getAttribute('data-view');
            const url = 'fetch_view.php';
            console.log('Fetching from:', url);
            outputDiv.innerHTML = '<p>Загрузка данных...</p>';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `view=${encodeURIComponent(view)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.error) {
                    outputDiv.innerHTML = `<p style="color: red;">Ошибка: ${data.error}</p>`;
                } else if (data.results && data.results.length > 0) {
                    let html = `<h3>Результаты ${view}</h3>`;
                    data.results.forEach((row, index) => {
                        html += '<div class="view-row">';
                        let items = [];
                        for (let key in row) {
                            items.push(`${key}: ${row[key] || 'Не указано'}`);
                        }
                        html += items.join(', ');
                        html += '</div>';
                    });
                    outputDiv.innerHTML = html;
                } else {
                    outputDiv.innerHTML = `<p>Нет данных для ${view}.</p>`;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                outputDiv.innerHTML = `<p style="color: red;">Ошибка запроса: ${error.message}</p>`;
            });
        });
    });
});