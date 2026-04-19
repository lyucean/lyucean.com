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
        <?php dev_blog_render_telegram_test_notice(); ?>
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
        <hr>
        <h2 class="title">Проверка доставки</h2>
        <p>Отправка через cURL (не WordPress HTTP API). Используются сохранённые токен, Chat ID и прокси.</p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="telegram_send_test_message">
            <?php wp_nonce_field( 'telegram_send_test_message' ); ?>
            <?php submit_button( 'Отправить тестовое сообщение', 'secondary', 'submit', false ); ?>
        </form>
    </div>
    <?php
}

function dev_blog_render_telegram_test_notice() {
    $key  = 'telegram_test_notice_' . get_current_user_id();
    $data = get_transient( $key );
    if ( ! is_array( $data ) || empty( $data['type'] ) || empty( $data['message'] ) ) {
        return;
    }
    delete_transient( $key );
    $class = 'success' === $data['type'] ? 'notice-success' : 'notice-error';
    printf(
        '<div class="notice %1$s is-dismissible"><p>%2$s</p></div>',
        esc_attr( $class ),
        esc_html( $data['message'] )
    );
}

function dev_blog_handle_telegram_send_test_message() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Недостаточно прав.', 'dev_blog_theme' ), '', 403 );
    }
    check_admin_referer( 'telegram_send_test_message' );

    $token   = trim( (string) get_option( 'telegram_alert_token', '' ) );
    $chat_id = trim( (string) get_option( 'telegram_alert_chat_id', '' ) );
    $redirect = admin_url( 'admin.php?page=telegram-settings' );
    $notice_key = 'telegram_test_notice_' . get_current_user_id();

    if ( '' === $token || '' === $chat_id ) {
        set_transient(
            $notice_key,
            array(
                'type'    => 'error',
                'message' => 'Сначала сохраните Telegram Bot Token и Telegram Chat ID.',
            ),
            60
        );
        wp_safe_redirect( $redirect );
        exit;
    }

    $text = sprintf(
        'Тест: уведомления с сайта «%s» доходят. Время: %s',
        get_bloginfo( 'name' ),
        wp_date( 'Y-m-d H:i:s' )
    );

    $url    = 'https://api.telegram.org/bot' . $token . '/sendMessage';
    $fields = array(
        'chat_id' => $chat_id,
        'text'    => $text,
    );

    $r = dev_blog_telegram_curl_post( $url, $fields );

    if ( $r['curl_errno'] !== 0 ) {
        set_transient(
            $notice_key,
            array(
                'type'    => 'error',
                'message' => 'cURL: ' . $r['curl_error'],
            ),
            60
        );
        wp_safe_redirect( $redirect );
        exit;
    }

    $json = json_decode( $r['body'], true );
    if ( 200 === $r['http_code'] && is_array( $json ) && ! empty( $json['ok'] ) ) {
        set_transient(
            $notice_key,
            array(
                'type'    => 'success',
                'message' => 'Тестовое сообщение отправлено.',
            ),
            60
        );
    } else {
        $detail = '';
        if ( is_array( $json ) && isset( $json['description'] ) ) {
            $detail = (string) $json['description'];
        } elseif ( $r['body'] !== '' ) {
            $detail = wp_strip_all_tags( substr( $r['body'], 0, 200 ) );
        } else {
            $detail = 'HTTP ' . (string) $r['http_code'];
        }
        set_transient(
            $notice_key,
            array(
                'type'    => 'error',
                'message' => 'Telegram API: ' . $detail,
            ),
            60
        );
    }

    wp_safe_redirect( $redirect );
    exit;
}
add_action( 'admin_post_telegram_send_test_message', 'dev_blog_handle_telegram_send_test_message' );

// Регистрация настроек
function register_telegram_settings() {
    // Регистрируем группу настроек
    register_setting('telegram_settings_group', 'telegram_alert_token');
    register_setting('telegram_settings_group', 'telegram_alert_chat_id');
    register_setting(
        'telegram_settings_group',
        'telegram_http_proxy',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'dev_blog_sanitize_telegram_http_proxy',
            'default'           => '',
        )
    );

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

    add_settings_section(
        'telegram_proxy_section',
        'Прокси для Telegram Bot API',
        function () {
            echo '<p>Только для исходящих запросов к <code>api.telegram.org</code> (cURL). Если поле пустое, можно задать переменную окружения <code>TELEGRAM_HTTP_PROXY</code>.</p>';
        },
        'telegram-settings'
    );

    add_settings_field(
        'telegram_http_proxy',
        'HTTP(S) прокси',
        function () {
            $value = get_option( 'telegram_http_proxy', '' );
            echo '<input type="text" name="telegram_http_proxy" value="' . esc_attr( $value ) . '" class="large-text" autocomplete="off" placeholder="http://хост:3128">';
            echo '<p class="description">Пример: <code>http://77.246.111.170:3128</code> или <code>http://логин:пароль@хост:3128</code>. Для SOCKS5: <code>socks5h://...</code> при поддержке cURL.</p>';
        },
        'telegram-settings',
        'telegram_proxy_section'
    );
}
add_action('admin_init', 'register_telegram_settings');

// Обработчик AJAX для сохранения ответа
function save_feedback() {
    // Проверяем, что запрос пришел через AJAX
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    if (!isset($_POST['feedback']) || !in_array($_POST['feedback'], ['yes', 'no', 'comment'])) {
        wp_send_json_error('Invalid feedback');
    }

    $post_id = intval($_POST['post_id']);
    $feedback = sanitize_text_field($_POST['feedback']);
    $comment = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';

    // Ключи для хранения данных
    $yes_count_key = 'feedback_yes_count';
    $no_count_key = 'feedback_no_count';
    $comment_count_key = 'feedback_comment_count';

    // Увеличиваем счетчик в зависимости от типа ответа
    if ($feedback === 'yes') {
        $current_yes_count = get_post_meta($post_id, $yes_count_key, true) ?: 0;
        $new_yes_count = $current_yes_count + 1;
        update_post_meta($post_id, $yes_count_key, $new_yes_count);
    } else if ($feedback === 'no') {
        $current_no_count = get_post_meta($post_id, $no_count_key, true) ?: 0;
        $new_no_count = $current_no_count + 1;
        update_post_meta($post_id, $no_count_key, $new_no_count);
    } else if ($feedback === 'comment') {
        $current_comment_count = get_post_meta($post_id, $comment_count_key, true) ?: 0;
        $new_comment_count = $current_comment_count + 1;
        update_post_meta($post_id, $comment_count_key, $new_comment_count);
    }

    // Получаем общее количество ответов
    $total_yes = get_post_meta($post_id, $yes_count_key, true) ?: 0;
    $total_no = get_post_meta($post_id, $no_count_key, true) ?: 0;
    $total_comment = get_post_meta($post_id, $comment_count_key, true) ?: 0;
    $total_feedback = $total_yes + $total_no + $total_comment;

    // Получаем IP-адрес пользователя
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Получаем данные статьи
    $post_title = get_the_title($post_id);
    $post_url = get_permalink($post_id);

    // Определяем текст ответа
    $feedback_text = '';
    if ($feedback === 'yes') {
        $feedback_text = 'Да';
    } else if ($feedback === 'no') {
        $feedback_text = 'Нет';
    } else if ($feedback === 'comment') {
        $feedback_text = 'Комментарий';
    }

    // Отправляем первое сообщение в Telegram с ответом
    send_telegram_feedback($post_title, $post_url, $feedback_text, $total_feedback, $user_ip);

    // Если есть комментарий, отправляем его отдельным сообщением
    if (!empty($comment)) {
        send_telegram_comment($post_title, $post_url, $comment, $user_ip);
    }

    // Возвращаем сообщение
    wp_send_json_success([
        'message' => 'Спасибо за ваш ответ!'
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
    $message .= "Общее количество ответов: *{$total_feedback}*\n";
    $message .= "IP пользователя: *{$user_ip}*";

    // URL для отправки сообщения через Telegram Bot API
    $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

    dev_blog_telegram_curl_post(
        $url,
        array(
            'chat_id'    => $chat_id,
            'text'       => $message,
            'parse_mode' => 'Markdown',
        )
    );
}

// Обработчик AJAX для отправки только комментария (когда ответ уже отправлен)
function save_feedback_comment() {
    // Проверяем, что запрос пришел через AJAX
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    if (!isset($_POST['comment']) || empty(trim($_POST['comment']))) {
        wp_send_json_error('Comment is required');
    }

    $post_id = intval($_POST['post_id']);
    $comment = sanitize_textarea_field($_POST['comment']);

    // Получаем IP-адрес пользователя
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Получаем данные статьи
    $post_title = get_the_title($post_id);
    $post_url = get_permalink($post_id);

    // Отправляем комментарий в Telegram отдельным сообщением
    send_telegram_comment($post_title, $post_url, $comment, $user_ip);

    // Возвращаем успех
    wp_send_json_success([
        'message' => 'Комментарий отправлен'
    ]);
}
add_action('wp_ajax_save_feedback_comment', 'save_feedback_comment');
add_action('wp_ajax_nopriv_save_feedback_comment', 'save_feedback_comment'); // Для неавторизованных пользователей

// Функция для отправки комментария в Telegram отдельным сообщением
function send_telegram_comment($post_title, $post_url, $comment, $user_ip) {
    // Получаем токен бота и ID чата из настроек WordPress
    $telegram_token = get_option('telegram_alert_token', null);
    $chat_id = get_option('telegram_alert_chat_id', null);

    // Проверяем, что переменные заданы
    if (!$telegram_token || !$chat_id) {
        error_log("Telegram: не удалось отправить комментарий, так как токен или chat_id отсутствуют.");
        return;
    }

    // Формируем сообщение для Telegram
    $message = "💬 *Комментарий к статье*\n\n";
    $message .= "Статья: [{$post_title}]({$post_url})\n\n";
    $message .= "*Комментарий:*\n{$comment}\n\n";
    $message .= "IP пользователя: *{$user_ip}*";

    // URL для отправки сообщения через Telegram Bot API
    $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";

    dev_blog_telegram_curl_post(
        $url,
        array(
            'chat_id'    => $chat_id,
            'text'       => $message,
            'parse_mode' => 'Markdown',
        )
    );
}

// Подключение скрипта для работы блока "Эта статья была полезна?"
function enqueue_feedback_script() {
    // Получаем путь к файлу скрипта
    $script_path = get_template_directory() . '/blocks/feedback-block.js';
    // Получаем время последней модификации файла для версии (чтобы избежать кеширования)
    $version = file_exists($script_path) ? date('Ymd.His', filemtime($script_path)) : '1.0';
    
    wp_enqueue_script('feedback-script', get_template_directory_uri() . '/blocks/feedback-block.js', array('jquery'), $version, true);

    // Передаем данные в скрипт
    wp_localize_script('feedback-script', 'feedbackData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_feedback_script');