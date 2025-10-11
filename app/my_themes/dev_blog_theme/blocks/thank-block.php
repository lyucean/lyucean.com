<?php
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

    // Возвращаем новое значение
    wp_send_json_success([
        'count' => $new_count,
        'message' => "Спасибо, что поддержал(а)! За эту статью сказали спасибо уже {$new_count} раз."
    ]);
}
add_action('wp_ajax_thank_you', 'handle_thank_you');
add_action('wp_ajax_nopriv_thank_you', 'handle_thank_you'); // Для неавторизованных пользователей

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