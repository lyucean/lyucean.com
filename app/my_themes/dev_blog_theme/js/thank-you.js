jQuery(document).ready(function ($) {
    const postId = $('#postIdHidden').val();

    // Проверяем, благодарил ли пользователь за статью
    $.ajax({
        url: thankYouData.ajax_url,
        type: 'POST',
        data: {
            action: 'check_thank_you',
            post_id: postId
        },
        success: function (response) {
            if (response.success) {
                // Если пользователь уже поблагодарил
                $('#thankYouMessage').text('Вы уже поблагодарили меня за эту статью.');
                $('#thankYouBtn').hide(); // Отключаем кнопку
            }
        }
    });

    // Обработка клика по кнопке "Спасибо"
    $('#thankYouBtn').on('click', function () {
        $.ajax({
            url: thankYouData.ajax_url,
            type: 'POST',
            data: {
                action: 'thank_you',
                post_id: postId
            },
            success: function (response) {
                if (response.success) {
                    // Обновляем сообщение и счетчик
                    $('#thankYouMessage').text(response.data.message);
                    $('#thankYouCount').text(response.data.count);
                    $('#thankYouBtn').hide(); // Отключаем кнопку
                } else {
                    alert('Ошибка: ' + response.data);
                }
            },
            error: function () {
                alert('Произошла ошибка. Попробуйте еще раз.');
            }
        });
    });
});