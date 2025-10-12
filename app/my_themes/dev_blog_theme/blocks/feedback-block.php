<?php
// Регистрация страницы настроек
function register_telegram_settings_page() {
    add_menu_page(
        'Настройки Telegram', // Название страницы
        'Telegram',           // Название меню
        'manage_options',     // Уровень доступа
        'telegram-settings',  // Уникальный slug страницы
        'render_telegram_settings_page', // Функция для отображения страницы
        'dashicons-email-alt', // Иконка меню
        100                    // Позиция в меню
    );
}
add_action('admin_menu', 'register_telegram_settings_page');

// Отображение страницы настроек
function render_telegram_settings_page() {
    ?>
    <div class="wrap">
        <h1>Настройки Telegram</h1>
        <form method="post" action="options.php">
            <?php
            // Регистрируем настройки
            settings_fields('telegram_settings_group');
            // Выводим настройки
            do_settings_sections('telegram-settings');
            // Кнопка сохранения
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Регистрация настроек
function register_telegram_settings() {
    // Регистрируем группу настроек
    register_setting('telegram_settings_group', 'telegram_alert_token');
    register_setting('telegram_settings_group', 'telegram_alert_chat_id');

    // Добавляем секцию настроек
    add_settings_section(
        'telegram_settings_section',
        'Основные настройки Telegram',
        function () {
            echo '<p>Введите данные для подключения к Telegram.</p>';
        },
        'telegram-settings'
    );

    // Поле для токена бота
    add_settings_field(
        'telegram_alert_token',
        'Telegram Bot Token',
        function () {
            $value = get_option('telegram_alert_token', '');
            echo '<input type="text" name="telegram_alert_token" value="' . esc_attr($value) . '" class="regular-text">';
        },
        'telegram-settings',
        'telegram_settings_section'
    );

    // Поле для ID чата
    add_settings_field(
        'telegram_alert_chat_id',
        'Telegram Chat ID',
        function () {
            $value = get_option('telegram_alert_chat_id', '');
            echo '<input type="text" name="telegram_alert_chat_id" value="' . esc_attr($value) . '" class="regular-text">';
        },
        'telegram-settings',
        'telegram_settings_section'
    );
}
add_action('admin_init', 'register_telegram_settings');

// Обработчик AJAX для сохранения ответа
function save_feedback() {
    // Проверяем, что запрос пришел через AJAX
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    if (!isset($_POST['feedback']) || !in_array($_POST['feedback'], ['yes', 'no'])) {
        wp_send_json_error('Invalid feedback');
    }

    $post_id = intval($_POST['post_id']);
    $feedback = sanitize_text_field($_POST['feedback']);

    // Ключи для хранения данных
    $yes_count_key = 'feedback_yes_count';
    $no_count_key = 'feedback_no_count';

    // Увеличиваем счетчик "Да" или "Нет"
    if ($feedback === 'yes') {
        $current_yes_count = get_post_meta($post_id, $yes_count_key, true) ?: 0;
        $new_yes_count = $current_yes_count + 1;
        update_post_meta($post_id, $yes_count_key, $new_yes_count);
    } else {
        $current_no_count = get_post_meta($post_id, $no_count_key, true) ?: 0;
        $new_no_count = $current_no_count + 1;
        update_post_meta($post_id, $no_count_key, $new_no_count);
    }

    // Получаем общее количество ответов
    $total_yes = get_post_meta($post_id, $yes_count_key, true) ?: 0;
    $total_no = get_post_meta($post_id, $no_count_key, true) ?: 0;
    $total_feedback = $total_yes + $total_no;

    // Получаем IP-адрес пользователя
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Отправляем уведомление в Telegram
    $post_title = get_the_title($post_id);
    $post_url = get_permalink($post_id);
    $feedback_text = $feedback === 'yes' ? 'Да' : 'Нет';
    send_telegram_feedback($post_title, $post_url, $feedback_text, $total_feedback, $user_ip);

    // Возвращаем сообщение
    $message = $feedback === 'yes'
            ? "Спасибо за ваш ответ! Мы рады, что статья была полезна."
            : "Спасибо за ваш ответ! Мы учтем ваши замечания.";

    wp_send_json_success([
            'message' => $message
    ]);
}
add_action('wp_ajax_save_feedback', 'save_feedback');
add_action('wp_ajax_nopriv_save_feedback', 'save_feedback'); // Для неавторизованных пользователей

// Функция для отправки уведомлений в Telegram
function send_telegram_feedback($post_title, $post_url, $feedback, $total_feedback, $user_ip) {
    // Получаем токен бота и ID чата из настроек WordPress
    $telegram_token = get_option('telegram_alert_token', null);
    $chat_id = get_option('telegram_alert_chat_id', null);

    // Проверяем, что переменные заданы
    if (!$telegram_token || !$chat_id) {
        error_log("Telegram: не удалось отправить уведомление, так как токен или chat_id отсутствуют.");
        return;
    }

    // Формируем сообщение для Telegram
    $message = "📢 *Новый ответ на статью!*\n\n";
    $message .= "Статья: [{$post_title}]({$post_url})\n";
    $message .= "Ответ: *{$feedback}*\n";
    $message .= "Общее количество ответов: *{$total_feedback}*";
    $message .= "IP пользователя: *{$user_ip}*";

    // URL для отправки сообщения через Telegram Bot API
    $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

    // Параметры запроса
    $args = [
            'body' => [
                    'chat_id' => $chat_id,
                    'text' => $message,
                    'parse_mode' => 'Markdown', // Используем Markdown для форматирования
            ],
    ];

    // Отправляем запрос
    $response = wp_remote_post($url, $args);
}

// Подключение скрипта для работы блока "Эта статья была полезна?"
function enqueue_feedback_script() {
    wp_enqueue_script('feedback-script', get_template_directory_uri() . '/blocks/feedback-block.js', array('jquery'), null, true);

    // Передаем данные в скрипт
    wp_localize_script('feedback-script', 'feedbackData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_feedback_script');