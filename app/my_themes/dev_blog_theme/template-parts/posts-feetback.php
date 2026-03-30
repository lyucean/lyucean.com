<div class="feedback-post pt-5 pb-4">
    <!-- Скрытое поле для передачи post_id -->
    <input type="hidden" id="postIdHidden" value="<?php echo get_the_ID(); ?>">

    <div class="container">
        <div class="row justify-content-center align-items-center text-center">
            <!-- Вопрос -->
            <div id="feedback-title" class="col-12 col-lg-auto mb-3 mb-lg-0">
                <h2>Эта статья была полезна?</h2>
            </div>
            <!-- Кнопки выбора -->
            <div class="col-12 col-lg-auto">
                <button id="feedback-yesBtn" class="btn btn-success me-2" data-feedback="yes">
                    Да
                </button>
                <button id="feedback-noBtn" class="btn btn-danger me-2" data-feedback="no">
                    Нет
                </button>
                <button id="feedback-commentBtn" class="btn btn-primary" data-feedback="comment">
                    Комментарий
                </button>
            </div>
            <!-- Сообщение после выбора -->
            <p id="feedback-message" class="mt-3 text-secondary"></p>
        </div>
    </div>
</div>

<!-- Модальное окно для комментария -->
<div class="modal fade feedback-modal" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Помоги мне стать лучше</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="feedback-modal-intro">Что тебе не хватило в статье? На что ты бы хотел обратить внимание? У меня уже 14&nbsp;000 посещений — и я хочу знать тебя, мой читатель, ближе.</p>
                <form id="feedbackForm">
                    <input type="hidden" id="selectedFeedback" value="">
                    <div class="mb-3">
                        <label for="feedbackComment" class="form-label">Твой комментарий</label>
                        <textarea class="form-control" id="feedbackComment" rows="4" placeholder="Напиши, что на душе (необязательно)"></textarea>
                        <small class="form-text text-muted">Можешь просто нажать «Отправить»</small>
                    </div>
                    <div id="feedbackModalMessage" class="alert d-none" role="alert"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="sendFeedbackBtn">Отправить</button>
            </div>
        </div>
    </div>
</div>