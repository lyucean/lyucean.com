jQuery(document).ready(function($) {
    // Обработка отправки формы пожеланий
    $('#sendWishesBtn').on('click', function() {
        const wishesText = $('#wishesText').val().trim();
        const messageDiv = $('#wishesMessage');
        const sendBtn = $(this);
        
        // Проверка на пустое поле
        if (!wishesText) {
            messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
            messageDiv.text('Пожалуйста, введите пожелание');
            return;
        }
        
        // Блокируем кнопку на время отправки
        sendBtn.prop('disabled', true).text('Отправка...');
        messageDiv.addClass('d-none');
        
        // Отправка AJAX запроса
        $.ajax({
            url: newYearWishesData.ajax_url,
            type: 'POST',
            data: {
                action: 'send_new_year_wishes',
                wishes: wishesText
            },
            success: function(response) {
                if (response.success) {
                    // Показываем сообщение об успехе
                    messageDiv.removeClass('d-none alert-danger').addClass('alert-success');
                    messageDiv.text(response.data.message);
                    
                    // Очищаем поле ввода
                    $('#wishesText').val('');
                    
                    // Закрываем модалку через 1.5 секунды
                    setTimeout(function() {
                        $('#newYearWishesModal').modal('hide');
                        messageDiv.addClass('d-none');
                        sendBtn.prop('disabled', false).text('Отправить');
                    }, 1500);
                } else {
                    messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                    messageDiv.text(response.data || 'Произошла ошибка. Попробуйте еще раз.');
                    sendBtn.prop('disabled', false).text('Отправить');
                }
            },
            error: function() {
                messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                messageDiv.text('Произошла ошибка. Попробуйте еще раз.');
                sendBtn.prop('disabled', false).text('Отправить');
            }
        });
    });
    
    // Обработка Enter в текстовом поле (отправка формы)
    $('#wishesText').on('keydown', function(e) {
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            $('#sendWishesBtn').click();
        }
    });
    
    // Сброс формы при закрытии модалки
    $('#newYearWishesModal').on('hidden.bs.modal', function() {
        $('#wishesText').val('');
        $('#wishesMessage').addClass('d-none').removeClass('alert-success alert-danger');
        $('#sendWishesBtn').prop('disabled', false).text('Отправить');
    });
});

