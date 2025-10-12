<div class="feedback-post mb-4">
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
                <button id="feedback-yesBtn" class="btn btn-success me-2">
                    Да
                </button>
                <button id="feedback-noBtn" class="btn btn-danger">
                    Нет
                </button>
            </div>
            <!-- Сообщение после выбора -->
            <p id="feedback-message" class="mt-3 text-secondary"></p>
        </div>
    </div>
</div>