<?php
/**
 * Блок «Эта статья была полезна?»
 *
 * @package dev_blog_theme
 */
?>
<section class="post-after feedback-post" aria-labelledby="feedback-title">
    <input type="hidden" id="postIdHidden" value="<?php echo (int) get_the_ID(); ?>">

    <div class="feedback-post__row">
        <h2 class="feedback-post__title" id="feedback-title">Эта статья была полезна?</h2>
        <div class="feedback-post__actions" role="group" aria-label="Оценка статьи">
            <button id="feedback-yesBtn" class="feedback-post__btn" type="button" data-feedback="yes">Да</button>
            <button id="feedback-noBtn" class="feedback-post__btn" type="button" data-feedback="no">Нет</button>
            <button id="feedback-commentBtn" class="feedback-post__btn feedback-post__btn--accent" type="button" data-feedback="comment">Комментарий</button>
        </div>
    </div>
    <p id="feedback-message" class="feedback-post__message"></p>
</section>

<div class="modal fade feedback-modal" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Помоги мне стать лучше</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="feedback-modal-intro">Что в статье было не так или не хватило? Напиши, если есть идеи. Уже больше четырнадцати тысяч заходов, и мне хочется понимать вас лучше, не по цифрам, а по делу.</p>
                <form id="feedbackForm">
                    <input type="hidden" id="selectedFeedback" value="">
                    <div class="mb-3">
                        <label for="feedbackComment" class="form-label">Твой комментарий</label>
                        <textarea class="form-control" id="feedbackComment" rows="4" placeholder="Напиши, что на душе (необязательно)"></textarea>
                        <p class="feedback-modal-hint mb-0">Можешь просто нажать «Отправить»</p>
                    </div>
                    <div id="feedbackModalMessage" class="alert d-none" role="alert"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-dark" id="sendFeedbackBtn">Отправить</button>
            </div>
        </div>
    </div>
</div>
