document.getElementById('profile-connection').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let errorContainer = document.getElementById('form_errors');

    fetch('/feedback/', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Берём текст ответа, даже если он не JSON (тестировалось для разных кодировок)
    .then(text => {
        try {
            let data = JSON.parse(text); // Пробуем распарсить JSON
            if (data.status === "error") {
                errorContainer.innerHTML = data.message;
            } else if (data.status === "success") {
                $('#modal-overlay').addClass('visible');
                document.getElementById('clear').reset(); // Очищаем форму
            }
        } catch (error) {
            errorContainer.innerHTML = "Ошибка обработки ответа сервера";
        }
    })
    .catch(error => {
        errorContainer.innerHTML = "Ошибка отправки запроса";
    });
});

$(document).on('click', '.close-modal', function () {
    $('.modal-overlay').removeClass('visible');
});
