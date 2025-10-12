jQuery(document).ready(function ($) {
    const postId = $('#postIdHidden').val();

    // Фразы для "Да" и "Нет"
    const yesMessages = [
        "Вот это да! Ты просто огонь! 🔥",
        "Ну ты вообще красавчик (красотка), спасибо! 😎",
        "Ты сделал мой день, спасибо, бро! ❤️",
        "Вот так держать! Ты топчик! 💪",
        "Спасибо, что не прошёл мимо, ты лучший! 🌟",
        "Ты - тот самый человек, который спасает мир! 🦸‍♂️",
        "Ты просто легенда, спасибо за поддержку! 🏆",
        "С такой поддержкой я напишу книгу! 📚",
        "Ты мой герой, спасибо! 🦸‍♀️",
        "Ура! Ты сделал этот мир чуть лучше! 🎉"
    ];

    const noMessages = [
        "Ну что, не зашло? Пойду грустить в угол... 😢",
        "Окей, спасибо, я учту!",
        "Ну ты придирчив, но я ценю честность!",
        "Понял, принял, буду работать над собой! 💼",
        "Ты суров, но я услышал! Спасибо за фидбэк! ⚖️",
        "Эх,... я учту твои пожелания! 🤔",
        "Ну ты даёшь! Ладно, я услышал! 🙃",
        "Окей, я понял, шутки не зашли. Буду серьёзнее! 🤷‍♂️",
        "Спасибо за критику, бро/систерс! Я учту и запомню!",
        "Ну ты меня расстроил... Но всё равно спасибо! 🙌"
    ];

    // Обработка клика по кнопке "Да"
    $('#feedback-yesBtn').on('click', function () {
        sendFeedback(postId, 'yes');
    });

    // Обработка клика по кнопке "Нет"
    $('feedback-#noBtn').on('click', function () {
        sendFeedback(postId, 'no');
    });

    // Функция отправки данных на сервер
    function sendFeedback(postId, feedback) {
        $.ajax({
            url: feedbackData.ajax_url,
            type: 'POST',
            data: {
                action: 'save_feedback',
                post_id: postId,
                feedback: feedback
            },
            success: function (response) {
                if (response.success) {
                    // Выбираем случайную фразу в зависимости от ответа
                    const randomMessage = feedback === 'yes'
                        ? yesMessages[Math.floor(Math.random() * yesMessages.length)]
                        : noMessages[Math.floor(Math.random() * noMessages.length)];

                    // Отображаем сообщение
                    $('#feedback-message').text(randomMessage);
                    $('#feedback-yesBtn').hide();
                    $('#feedback-noBtn').hide();
                    $('#feedback-title').hide();
                } else {
                    alert('Ошибка: ' + response.data);
                }
            },
            error: function () {
                alert('Произошла ошибка. Попробуйте еще раз.');
            }
        });
    }
});