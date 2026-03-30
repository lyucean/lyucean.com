jQuery(document).ready(function ($) {
    const postId = $('#postIdHidden').val();
    let selectedFeedbackType = '';

    // Фразы для "Да", "Нет" и "Комментарий"
    const yesMessages = [
        "О, живой человек! Спасибо, что не бот! 🤖",
        "Твой клик повысил мою селф-эстим на 0.00001%. Продолжай! 📈",
        "Спасибо! Теперь я знаю, что пишу не только для поисковых роботов! 🕷️",
        "Ещё один плюсик в статистику! Скоро соберу все лайки мира! 🎯",
        "Окей, отметка 'полезно' получена. Иду пить кофе с чувством выполненного долга! ☕",
        "Спасибо! Моя мама сказала, что я молодец, когда прочитает эту статистику! 👩",
        "Твоё мнение записано в базу данных. Скоро появится в отчёте для инвесторов! 📊",
        "Ещё один довольный читатель — ещё один шаг к мировому господству! 🌍",
        "Спасибо! Теперь у меня есть доказательство, что статьи читают живые люди! 👤",
        "О, так ты читаешь мои тексты? Значит, я не просто пишу в пустоту! ✍️",
        "Великолепно! Ещё один плюсик в моём резюме 'автор полезного контента'! 📄",
        "Спасибо! Теперь у меня есть метрика для презентации перед собой! 📊",
        "Твой апрув повышает мою уверенность. Скоро напишу книгу! 📚",
        "Окей, зафиксировал. Твоё мнение теперь часть моей цифровой идентичности! 🆔",
        "Спасибо! Каждый лайк — это шаг от 'начинающий блогер' к 'влиятельный автор'! 🚀",
        "Великолепно! Ещё одна точка данных подтверждает, что я не зря трачу время! ⏱️",
        "Твой фидбэк — это как успешный деплой в продакшн. Радуюсь! 🎉",
        "Спасибо! Теперь у меня есть доказательство, что я не пишу полную ахинею! ✅",
        "О, позитивный отклик! Иду отмечать в календаре: сегодня хороший день! 📅",
        "Твоя оценка повысила мой рейтинг в собственных глазах. Спасибо! ⭐"
    ];

    const noMessages = [
        "Окей, не зашло. Пойду думать о смысле жизни и переписывать статьи... 🤔",
        "Понял, принял, записал на салфетке. Буду работать над собой! 📝",
        "Ну ок, не прокатило. Зато теперь знаю, что не надо делать! 🚫",
        "Честность — это круто! Спасибо, что не молчишь, как остальные 99%! 💬",
        "Принял к сведению. Пойду переосмысливать свой жизненный выбор... 🤷",
        "Ок, критику принял. Теперь буду работать над статьями как проклятый! 💻",
        "Спасибо за фидбэк! Без таких как ты я бы застрял в эхо-камере! 🔊",
        "Понял тебя, братан. Учту на будущее. Может быть. 🤷‍♂️",
        "Не зашло — окей, не смертельно. Продолжаю эксперименты! 🧪",
        "Спасибо за честность! Критика — это как коммит без сообщения: полезно, но больно! 😅",
        "Окей, не зашло. Пойду в рефлексию и переосмысление контент-стратегии! 🔄",
        "Понял, принял к сведению. Критика — это как код-ревью: больно, но помогает! 👨‍💻",
        "Ну ок, не прокатило. Зато теперь есть данные для A/B тестирования! 🧪",
        "Честность ценится! Спасибо, что не прошёл мимо, как другие 95%! 📊",
        "Принял фидбэк. Иду делать ретрак-статью и исправлять ошибки! 🔧",
        "Ок, не зашло. Значит, нужно итеративно улучшать контент! 🔁",
        "Спасибо! Твоя критика — это как баг-репорт: неприятно, но необходимо! 🐛",
        "Понял тебя. Негативный фидбэк — это тоже фидбэк. Учту! 📉",
        "Не зашло? Окей, значит нужно делать pivot в контент-стратегии! 🔀",
        "Спасибо за честность! Отрицательный результат — это тоже результат! 📉"
    ];

    const commentMessages = [
        "Комментарий принят! Иду обрабатывать твои мысли в async режиме! ⚙️",
        "Спасибо! Твой фидбэк добавлен в очередь на обработку. Обработаю в free time! 📥",
        "Фидбэк получен, обработан, сохранён в БД. Скоро прочитаю и подумаю над ответом! 💾",
        "Окей, комментарий записан. Иду делать вид, что я его прочитал и обдумал! 🧠",
        "Спасибо! Твоё мнение теперь часть моего датасета. Буду анализировать! 📊",
        "Комментарий зафиксирован! Иду обрабатывать в фоне, как хороший асинхронный код! 🔄",
        "Спасибо! Твои мысли добавлены в бэклог. Скоро возьму в работу! 📋",
        "Фидбэк получен и поставлен в приоритетную очередь. Обработаю ASAP! ⏰",
        "Окей, комментарий сохранён. Теперь у меня есть невыполненная задача на вечер! ✅",
        "Спасибо! Твой текст добавлен в мою коллекцию 'что думают читатели'! 📚",
        "Комментарий принят к обработке! Иду делать code review твоего мнения! 👀",
        "Фидбэк получен! Твои слова теперь в моей БД, буду делать запросы к ним! 🗄️",
        "Спасибо! Твоё мнение сохранено в кэше моей памяти. Скоро прочитаю! 🧠",
        "Комментарий залогирован! Иду обрабатывать его как важный event в системе! 📝",
        "Окей, фидбэк добавлен в pipeline. Скоро пройдёт через мои нейронные сети! 🧬"
    ];

    // Обработка клика по кнопке "Да" или "Нет" - сразу открываем модалку, отправляем ответ в фоне
    $('#feedback-yesBtn, #feedback-noBtn').on('click', function () {
        selectedFeedbackType = $(this).data('feedback');
        $('#selectedFeedback').val(selectedFeedbackType);
        
        // Обновляем заголовок модалки в зависимости от типа
        let modalTitle = 'Помоги мне стать лучше';
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
        $('#feedbackModalLabel').text('Помоги мне стать лучше');
        
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