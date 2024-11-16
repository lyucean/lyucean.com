<?php
// Используется для добавления функций и подключения стилей/скриптов.

// Подключаем стили и скрипты
function my_theme_enqueue_styles() {
    // Подключаем Bootstrap
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    // Bootstrap Icons
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css');
    // Bootstrap JS
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true);

    // Подключаем стили темы
    wp_enqueue_style('my_theme-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles'); // Подключаем стили и скрипты когда WordPress загружает скрипты и стили

// Поддержка миниатюр. Нужен т.к. я планирую использовать миниатюры для постов/страниц
add_theme_support('post-thumbnails');

// Регистрация меню. Нужно только если у вас будет навигационное меню, которое можно редактировать через админку
function my_theme_register_menus() {
    register_nav_menus(array(
        'primary' => __('Меню сборку', 'my_theme'),
    ));
}
add_action('after_setup_theme', 'my_theme_register_menus');

// Позволяет пользователям менять шапку сайта через админку
add_theme_support('custom-header');

// Подключаем скрипт для переключения тем
function enqueue_theme_scripts() {
    wp_enqueue_script('theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_scripts');

// Пагинация
function bootstrap_pagination() {
    global $wp_query;

    $big = 999999999;

    return paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages,
        'type' => 'list',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'textdomain') . ' </span>',
    ));
}
?>
