jQuery(document).ready(function ($) {
    const postId = $('#postIdHidden').val();
    let selectedFeedbackType = '';

    // Фразы для "Да", "Нет" и "Комментарий"
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

    const commentMessages = [
        "Спасибо за комментарий! Очень ценю твоё мнение! 💬",
        "Отлично! Твои мысли помогут улучшить статьи! ✨",
        "Спасибо, что поделился! Это важно для меня! 🙏",
        "Твой комментарий очень ценен! Спасибо! 💎",
        "Спасибо за обратную связь! Буду работать над улучшениями! 🚀"
    ];

    // Обработка клика по кнопке "Да" или "Нет" - сразу открываем модалку, отправляем ответ в фоне
    $('#feedback-yesBtn, #feedback-noBtn').on('click', function () {
        selectedFeedbackType = $(this).data('feedback');
        $('#selectedFeedback').val(selectedFeedbackType);
        
        // Обновляем заголовок модалки в зависимости от типа
        let modalTitle = '';
        if (selectedFeedbackType === 'yes') {
            modalTitle = 'Статья была полезна! 💚';
        } else if (selectedFeedbackType === 'no') {
            modalTitle = 'Статья не зашла 😔';
        }
        $('#feedbackModalLabel').text(modalTitle);
        
        // Очищаем форму
        $('#feedbackComment').val('');
        $('#feedbackModalMessage').addClass('d-none').removeClass('alert-success alert-danger');
        
        // Открываем модалку сразу
        $('#feedbackModal').modal('show');
        
        // Отправляем ответ асинхронно в фоне (не ждем ответа)
        sendFeedbackOnly(postId, selectedFeedbackType);
    });

    // Обработка клика по кнопке "Комментарий" - сразу открываем модалку
    $('#feedback-commentBtn').on('click', function () {
        selectedFeedbackType = 'comment';
        $('#selectedFeedback').val(selectedFeedbackType);
        
        // Обновляем заголовок модалки
        $('#feedbackModalLabel').text('Оставить комментарий 💬');
        
        // Очищаем форму при открытии
        $('#feedbackComment').val('');
        $('#feedbackModalMessage').addClass('d-none').removeClass('alert-success alert-danger');
        
        // Открываем модалку
        $('#feedbackModal').modal('show');
    });

    // Обработка отправки формы в модалке
    $('#sendFeedbackBtn').on('click', function () {
        const comment = $('#feedbackComment').val().trim();
        const messageDiv = $('#feedbackModalMessage');
        const sendBtn = $(this);

        // Если это "Да" или "Нет" - отправляем только комментарий (ответ уже отправлен)
        // Если это "Комментарий" - отправляем тип и комментарий вместе
        if (selectedFeedbackType === 'yes' || selectedFeedbackType === 'no') {
            sendCommentOnly(postId, comment, messageDiv, sendBtn);
        } else if (selectedFeedbackType === 'comment') {
            sendFeedback(postId, selectedFeedbackType, comment, messageDiv, sendBtn);
        }
    });

    // Обработка Enter+Ctrl/Cmd для отправки
    $('#feedbackComment').on('keydown', function (e) {
        if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            $('#sendFeedbackBtn').click();
        }
    });

    // Очистка формы при закрытии модалки
    $('#feedbackModal').on('hidden.bs.modal', function () {
        $('#feedbackComment').val('');
        $('#feedbackModalMessage').addClass('d-none').removeClass('alert-success alert-danger');
        $('#sendFeedbackBtn').prop('disabled', false).text('Отправить');
        
        // Если ответ уже был отправлен ("Да"/"Нет"), скрываем кнопки
        if (feedbackSent && (selectedFeedbackType === 'yes' || selectedFeedbackType === 'no')) {
            hideFeedbackButtons();
            feedbackSent = false; // Сбрасываем флаг
        }
    });

    // Флаг, что ответ уже отправлен (для "Да"/"Нет")
    let feedbackSent = false;

    // Функция отправки только ответа (Да/Нет) без комментария - асинхронно в фоне
    function sendFeedbackOnly(postId, feedback) {
        // Отправляем запрос асинхронно, не блокируя UI
        $.ajax({
            url: feedbackData.ajax_url,
            type: 'POST',
            data: {
                action: 'save_feedback',
                post_id: postId,
                feedback: feedback,
                comment: '' // Без комментария
            },
            success: function (response) {
                if (response.success) {
                    feedbackSent = true; // Помечаем, что ответ отправлен
                } else {
                    // Тихо логируем ошибку, не показываем пользователю
                    console.error('Ошибка отправки feedback:', response.data);
                }
            },
            error: function (xhr, status, error) {
                // Тихо логируем ошибку, не показываем пользователю
                console.error('Ошибка отправки feedback:', error);
            }
        });
    }

    // Функция отправки только комментария (ответ уже отправлен)
    function sendCommentOnly(postId, comment, messageDiv, sendBtn) {
        sendBtn.prop('disabled', true).text('Отправка...');
        messageDiv.addClass('d-none');

        // Если комментарий пустой, просто закрываем модалку
        if (!comment) {
            $('#feedbackModal').modal('hide');
            hideFeedbackButtons();
            return;
        }

        $.ajax({
            url: feedbackData.ajax_url,
            type: 'POST',
            data: {
                action: 'save_feedback_comment',
                post_id: postId,
                comment: comment
            },
            success: function (response) {
                if (response.success) {
                    messageDiv.removeClass('d-none alert-danger').addClass('alert-success');
                    messageDiv.text('Спасибо за комментарий!');

                    setTimeout(function () {
                        $('#feedbackModal').modal('hide');
                        hideFeedbackButtons();
                    }, 1500);
                } else {
                    messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                    messageDiv.text(response.data || 'Произошла ошибка. Попробуйте еще раз.');
                    sendBtn.prop('disabled', false).text('Отправить');
                }
            },
            error: function () {
                messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                messageDiv.text('Произошла ошибка. Попробуйте еще раз.');
                sendBtn.prop('disabled', false).text('Отправить');
            }
        });
    }

    // Функция скрытия кнопок обратной связи
    function hideFeedbackButtons() {
        // Выбираем случайную фразу в зависимости от ответа
        let randomMessage;
        if (selectedFeedbackType === 'yes') {
            randomMessage = yesMessages[Math.floor(Math.random() * yesMessages.length)];
        } else if (selectedFeedbackType === 'no') {
            randomMessage = noMessages[Math.floor(Math.random() * noMessages.length)];
        } else {
            randomMessage = commentMessages[Math.floor(Math.random() * commentMessages.length)];
        }
        
        $('#feedback-message').text(randomMessage);
        $('#feedback-yesBtn').hide();
        $('#feedback-noBtn').hide();
        $('#feedback-commentBtn').hide();
        $('#feedback-title').hide();
    }

    // Функция отправки данных на сервер (для типа "comment" или общая)
    function sendFeedback(postId, feedback, comment, messageDiv, sendBtn) {
        sendBtn.prop('disabled', true).text('Отправка...');
        messageDiv.addClass('d-none');

        $.ajax({
            url: feedbackData.ajax_url,
            type: 'POST',
            data: {
                action: 'save_feedback',
                post_id: postId,
                feedback: feedback,
                comment: comment
            },
            success: function (response) {
                if (response.success) {
                    // Выбираем случайную фразу в зависимости от ответа
                    let randomMessage;
                    if (feedback === 'yes') {
                        randomMessage = yesMessages[Math.floor(Math.random() * yesMessages.length)];
                    } else if (feedback === 'no') {
                        randomMessage = noMessages[Math.floor(Math.random() * noMessages.length)];
                    } else {
                        randomMessage = commentMessages[Math.floor(Math.random() * commentMessages.length)];
                    }

                    // Показываем сообщение об успехе в модалке
                    messageDiv.removeClass('d-none alert-danger').addClass('alert-success');
                    messageDiv.text('Спасибо! Твой ответ отправлен.');

                    // Закрываем модалку через 1.5 секунды
                    setTimeout(function () {
                        $('#feedbackModal').modal('hide');
                        hideFeedbackButtons();
                    }, 1500);
                } else {
                    messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                    messageDiv.text(response.data || 'Произошла ошибка. Попробуйте еще раз.');
                    sendBtn.prop('disabled', false).text('Отправить');
                }
            },
            error: function () {
                messageDiv.removeClass('d-none alert-success').addClass('alert-danger');
                messageDiv.text('Произошла ошибка. Попробуйте еще раз.');
                sendBtn.prop('disabled', false).text('Отправить');
            }
        });
    }
});