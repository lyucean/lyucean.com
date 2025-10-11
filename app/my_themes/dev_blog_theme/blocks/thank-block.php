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

// Обработчик AJAX для кнопки "Спасибо"
function handle_thank_you() {
    // Проверяем, что запрос пришел через AJAX
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    $post_id = intval($_POST['post_id']);
    $count_key = 'thank_you_count';
    $users_key = 'thank_you_users'; // Массив пользователей, которые сказали "Спасибо"

    // Получаем текущий массив пользователей, сказавших "Спасибо"
    $thank_you_users = get_post_meta($post_id, $users_key, true) ?: [];

    // Определяем идентификатор пользователя
    if (is_user_logged_in()) {
        $user_id = get_current_user_id(); // Для авторизованных пользователей
    } else {
        $user_id = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']); // Уникальный идентификатор для неавторизованных
    }

    // Проверяем, благодарил ли пользователь уже за статью
    if (in_array($user_id, $thank_you_users)) {
        wp_send_json_error('Вы уже поблагодарили за эту статью.');
    }

    // Добавляем пользователя в список
    $thank_you_users[] = $user_id;
    update_post_meta($post_id, $users_key, $thank_you_users);

    // Увеличиваем счетчик "Спасибо"
    $current_count = get_post_meta($post_id, $count_key, true) ?: 0;
    $new_count = $current_count + 1;
    update_post_meta($post_id, $count_key, $new_count);

    // Отправляем уведомление в Telegram
    send_telegram_notification($post_id, $new_count);

    // Возвращаем новое значение
    wp_send_json_success([
        'count' => $new_count,
        'message' => "Спасибо, что поддержал(а)! За эту статью сказали спасибо уже {$new_count} раз."
    ]);
}
add_action('wp_ajax_thank_you', 'handle_thank_you');
add_action('wp_ajax_nopriv_thank_you', 'handle_thank_you'); // Для неавторизованных пользователей

// Функция для отправки уведомлений в Telegram
function send_telegram_notification($post_id, $thank_count) {
    // Получаем токен бота и ID чата из настроек WordPress
    $telegram_token = get_option('telegram_alert_token', null);
    $chat_id = get_option('telegram_alert_chat_id', null);

    // Проверяем, что переменные заданы
    if (!$telegram_token || !$chat_id) {
        error_log("Telegram: не удалось отправить уведомление, так как токен или chat_id отсутствуют.");
        return;
    }

    // Получаем заголовок статьи
    $post_title = get_the_title($post_id);
    $post_url = get_permalink($post_id);

    // Формируем сообщение для Telegram
    $message = "❤️*Новая благодарность!* \n\n";
    $message .= "Кто-то сказал спасибо за статью: [{$post_title}]({$post_url}) \n";
    $message .= "Всего благодарностей: {$thank_count}";

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

// Подключение скрипта для работы кнопки "Спасибо"
function enqueue_thank_you_script() {
    wp_enqueue_script('thank-you-script', get_template_directory_uri() . '/js/thank-you.js', array('jquery'), null, true);

    // Передаем данные в скрипт
    wp_localize_script('thank-you-script', 'thankYouData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_thank_you_script');

add_action('wp_head', function () {
    if (is_single()) {
        echo '<script>var post_id = ' . get_the_ID() . ';</script>';
    }
});

// Обработчик AJAX для проверки, благодарил ли пользователь за статью
function check_thank_you() {
    // Проверяем, что запрос пришел через AJAX
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    $post_id = intval($_POST['post_id']);
    $users_key = 'thank_you_users';

    // Получаем текущий массив пользователей, сказавших "Спасибо"
    $thank_you_users = get_post_meta($post_id, $users_key, true) ?: [];

    // Определяем идентификатор пользователя
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    } else {
        $user_id = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    }

    // Проверяем, благодарил ли пользователь уже за статью
    if (in_array($user_id, $thank_you_users)) {
        wp_send_json_success(true);
    } else {
        wp_send_json_error(false);
    }
}
add_action('wp_ajax_check_thank_you', 'check_thank_you');
add_action('wp_ajax_nopriv_check_thank_you', 'check_thank_you'); // Для неавторизованных пользователей