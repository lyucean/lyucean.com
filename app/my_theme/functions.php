<?php
// Используется для добавления функций и подключения стилей/скриптов.

// Подключаем стили и скрипты
function my_theme_enqueue_styles() {
    // Подключаем Bootstrap
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

    // Подключаем стили темы
    wp_enqueue_style('my_theme-style', get_stylesheet_uri());

    // Подключаем скрипты
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
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

?>
