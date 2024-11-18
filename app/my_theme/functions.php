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

// Подключаем скрипт для мобильной строки поиска
function theme_enqueue_scripts() {
    wp_enqueue_script('mobile-search', get_template_directory_uri() . '/js/mobile-search.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');



// Регистрация меню. Нужно только если у вас будет навигационное меню, которое можно редактировать через админку
function register_theme_menus() {
    register_nav_menus(array(
        'sidebar-menu' => __('Боковое меню', 'my_theme'),
    ));
}
add_action('init', 'register_theme_menus');

// Bootstrap 5 Nav Walker для меню
class Bootstrap_5_Nav_Walker extends Walker_Nav_Menu {
    // Массив с иконками Bootstrap для каждого пункта меню
    private $menu_icons = [
        'Главная' => 'bi bi-house',
        'Посты' => 'bi bi-file-text',
        'Подписаться' => 'bi bi-bell',
        'Обо мне' => 'bi bi-person',
        'Блог ИТ-директора' => 'bi bi-person-workspace',
        'Ресурсы' => 'bi bi-book',
        'Инструменты' => 'bi bi-tools',
        'Настройка Netdata' => 'bi bi-graph-up',
        'Как всё успевать?' => 'bi bi-clock',
        'ITSM' => 'bi bi-arrow-repeat',
        'Life' => 'bi bi-flower1',
        'How' => 'bi bi-lightbulb',
        'Dev' => 'bi bi-code-square',
        'Support' => 'bi bi-question-circle',
        'Win' => 'bi bi-bullseye',
        'Management' => 'bi bi-graph-up-arrow'
    ];

    function start_lvl(&$output, $depth = 0, $args = null): void
    {
        // $output - строка, куда записывается HTML-код
        // $depth - текущий уровень вложенности (0 - верхний уровень)
        // $args - аргументы, переданные в wp_nav_menu()

        $output .= "<ul class='nav flex-column'>"; // Открывающий тег для подменю
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void
    {
        // $output - строка, куда записывается HTML-код (передается по ссылке &)
        // $item - объект пункта меню, содержит такие свойства как:
        //   - $item->title (название пункта)
        //   - $item->url (ссылка)
        //   - $item->classes (массив CSS-классов)
        // $depth - уровень вложенности пункта меню (0 - верхний уровень)
        // $args - аргументы, переданные в wp_nav_menu()
        // $id - ID пункта меню

        // Получаем иконку для текущего пункта меню из массива
        $icon_class = $this->menu_icons[$item->title] ?? 'bi bi-dot';

        // Формируем HTML для пункта меню
        $output .= "<li class='nav-item'>";
        $output .= sprintf(
            "<a href='%s' class='nav-link d-flex align-items-center gap-2 py-2 px-3 rounded-3'>
                <i class='%s'></i>
                %s
            </a>",
            $item->url,
            $icon_class,
            $item->title
        );
    }
}


add_action('after_setup_theme', function() {
    // Добавляем поддержку thumbnails
    add_theme_support('post-thumbnails');

    // Добавляем свой размер изображения с высоким качеством
    add_image_size('article-thumb', 1200, 630, true);
});

// Добавляем счетчик просмотров
function set_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);

        if ($count == '') {
            delete_post_meta($post_id, 'post_views_count');
            add_post_meta($post_id, 'post_views_count', 1);
        } else {
            update_post_meta($post_id, 'post_views_count', $count + 1);
        }
    }
}
add_action('wp_head', 'set_post_views');

?>
