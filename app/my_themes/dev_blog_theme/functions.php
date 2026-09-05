<?php
// Используется для добавления функций и подключения стилей/скриптов.

/**
 * Версия для query string у style.css: максимальный mtime среди ключевых файлов темы.
 * Иначе при правках только PHP (футер, шаблоны) номер не менялся бы, и CDN/браузер отдавали бы старый CSS.
 */
function dev_blog_theme_get_asset_version() {
    $dir = get_stylesheet_directory();
    $paths = array(
        $dir . '/style.css',
        $dir . '/functions.php',
        $dir . '/footer.php',
        $dir . '/header.php',
        $dir . '/template-parts/footer-web-projects.php',
        $dir . '/template-parts/front-content.php',
        $dir . '/template-parts/front-tags.php',
        $dir . '/front-page.php',
    );
    $max = 0;
    foreach ($paths as $path) {
        if (is_readable($path)) {
            $max = max($max, (int) filemtime($path));
        }
    }
    return $max > 0 ? date('Ymd.His', $max) : '1.0';
}

function dev_blog_theme_file_version($relative) {
    $path = get_stylesheet_directory() . $relative;
    return is_readable($path) ? (string) filemtime($path) : '1';
}

// Подключаем стили и скрипты
function dev_blog_theme_enqueue_styles() {
    $uri = get_template_directory_uri();

    wp_enqueue_style(
        'dev-blog-poppins',
        $uri . '/vendor/fonts/poppins/poppins.css',
        array(),
        dev_blog_theme_file_version('/vendor/fonts/poppins/poppins.css')
    );
    wp_enqueue_style(
        'bootstrap-css',
        $uri . '/vendor/bootstrap/bootstrap.min.css',
        array(),
        '5.3.3'
    );
    wp_enqueue_style(
        'bootstrap-icons',
        $uri . '/vendor/bootstrap-icons/bootstrap-icons.min.css',
        array(),
        '1.11.3'
    );
    wp_enqueue_script(
        'bootstrap',
        $uri . '/vendor/bootstrap/bootstrap.bundle.min.js',
        array(),
        '5.3.3',
        true
    );

    $version = dev_blog_theme_get_asset_version();
    wp_enqueue_style('dev_blog_theme-style', get_stylesheet_uri(), array('bootstrap-css'), $version);
}
add_action('wp_enqueue_scripts', 'dev_blog_theme_enqueue_styles');

function dev_blog_enqueue_view_beacon() {
    if (!is_singular('post') || is_preview() || is_user_logged_in()) {
        return;
    }

    $post_id = (int) get_queried_object_id();
    if ($post_id < 1) {
        return;
    }

    wp_enqueue_script(
        'ly-post-views',
        get_template_directory_uri() . '/js/post-views.js',
        array(),
        dev_blog_theme_file_version('/js/post-views.js'),
        true
    );
    wp_localize_script('ly-post-views', 'lyPostViews', array(
        'url' => rest_url('lyucean/v1/views/' . $post_id),
    ));
}
add_action('wp_enqueue_scripts', 'dev_blog_enqueue_view_beacon');

/**
 * Обложка карточки: 768px (medium_large), не large/1024.
 * Первые две в цикле без lazy: они в первом экране.
 */
function dev_blog_the_card_thumbnail($index = 1, $class = 'post-card__img') {
    if (!has_post_thumbnail()) {
        return;
    }

    $eager = (int) $index <= 2;
    $attrs = array(
        'class'         => $class,
        'alt'           => get_the_title(),
        'decoding'      => 'async',
        'sizes'         => '(min-width: 768px) 50vw, 100vw',
        'loading'       => $eager ? 'eager' : 'lazy',
    );
    if ($eager) {
        $attrs['fetchpriority'] = 'high';
    }

    $attachment_id = get_post_thumbnail_id();
    $srcset_parts = array();
    foreach (array('medium', 'medium_large') as $size_name) {
        $sized = wp_get_attachment_image_src($attachment_id, $size_name);
        if (!$sized || empty($sized[0]) || (int) $sized[1] > 768) {
            continue;
        }
        $srcset_parts[(int) $sized[1]] = $sized[0] . ' ' . (int) $sized[1] . 'w';
    }
    if ($srcset_parts) {
        ksort($srcset_parts, SORT_NUMERIC);
        $attrs['srcset'] = implode(', ', $srcset_parts);
    }

    the_post_thumbnail('medium_large', $attrs);
}

function dev_blog_the_hero_thumbnail($class = 'post-hero__img') {
    if (!has_post_thumbnail()) {
        return;
    }

    the_post_thumbnail('large', array(
        'class'         => $class,
        'alt'           => get_the_title(),
        'loading'       => 'eager',
        'fetchpriority' => 'high',
        'decoding'      => 'async',
    ));
}

// Поддержка миниатюр. Нужен т.к. я планирую использовать миниатюры для постов/страниц
add_theme_support('post-thumbnails');

// Позволяет пользователям менять шапку сайта через админку
add_theme_support('custom-header');

// Подключаем скрипт для переключения тем
function enqueue_theme_scripts() {
    wp_enqueue_script('theme-toggle', get_template_directory_uri() . '/js/theme-toggle.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_scripts');

function theme_enqueue_scripts() {
    // Подключаем скрипт для мобильной строки поиска
    wp_enqueue_script('mobile-search', get_template_directory_uri() . '/js/mobile-search.js', array(), '1.0.0', true);
    // Подключаем скрипт промотки страницы вверх
    wp_enqueue_script('scroll-top', get_template_directory_uri() . '/js/scroll-top.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');

// Регистрация меню. Нужно только если у вас будет навигационное меню, которое можно редактировать через админку
function register_theme_menus() {
    register_nav_menus(array(
        'sidebar-menu' => __('Боковое меню', 'dev_blog_theme'),
        'additional-menu' => __('Дополнительное меню', 'dev_blog_theme'),
        'block-menu' => __('Блок меню', 'dev_blog_theme'),
    ));
}
add_action('init', 'register_theme_menus');

/**
 * Топ тегов для сайдбара (по числу постов).
 *
 * @param int $limit
 * @return WP_Term[]
 */
function dev_blog_get_popular_tags($limit = 15) {
    $limit = max(1, (int) $limit);
    $cache_key = 'dev_blog_popular_tags_' . $limit;
    $cached = get_transient($cache_key);
    if (is_array($cached)) {
        return $cached;
    }

    $tags = get_tags([
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => $limit,
        'hide_empty' => true,
    ]);

    if (is_wp_error($tags) || empty($tags)) {
        $tags = [];
    }

    set_transient($cache_key, $tags, DAY_IN_SECONDS);
    return $tags;
}

function dev_blog_flush_popular_tags_cache() {
    for ($i = 1; $i <= 50; $i++) {
        delete_transient('dev_blog_popular_tags_' . $i);
    }
}
add_action('save_post', 'dev_blog_flush_popular_tags_cache');
add_action('deleted_post', 'dev_blog_flush_popular_tags_cache');
add_action('set_object_terms', 'dev_blog_flush_popular_tags_cache');
add_action('edited_post_tag', 'dev_blog_flush_popular_tags_cache');
add_action('created_post_tag', 'dev_blog_flush_popular_tags_cache');
add_action('delete_post_tag', 'dev_blog_flush_popular_tags_cache');

/**
 * Связанные статьи: пересечение тегов, без RAND() на отдаче страницы.
 * Счёт = число общих тегов. Два и больше общих: эти посты идут первыми.
 * Результат лежит в post meta и живёт сутки, потом пересчитывается лениво.
 */
function dev_blog_published_post_ids(array $exclude = []) {
    static $all = null;
    if ($all === null) {
        $all = get_posts([
            'post_type'              => 'post',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
        $all = array_map('intval', (array) $all);
    }

    if (empty($exclude)) {
        return $all;
    }

    return array_values(array_diff($all, array_map('intval', $exclude)));
}

function dev_blog_pick_random_post_ids($limit, array $exclude = []) {
    $ids = dev_blog_published_post_ids($exclude);
    if (empty($ids)) {
        return [];
    }
    shuffle($ids);
    return array_slice($ids, 0, max(1, (int) $limit));
}

function dev_blog_pick_weighted_ids(array $scores, $limit) {
    $picked = [];
    $pool = $scores;
    $limit = min(max(0, (int) $limit), count($pool));

    for ($i = 0; $i < $limit; $i++) {
        $total = (int) array_sum($pool);
        if ($total < 1) {
            break;
        }
        $roll = wp_rand(1, $total);
        $acc = 0;
        foreach ($pool as $id => $weight) {
            $acc += (int) $weight;
            if ($roll <= $acc) {
                $picked[] = (int) $id;
                unset($pool[$id]);
                break;
            }
        }
    }

    return $picked;
}

function dev_blog_compute_related_ids($post_id, $limit = 2) {
    $post_id = (int) $post_id;
    $limit = max(1, (int) $limit);
    $exclude = $post_id > 0 ? [$post_id] : [];

    $tag_ids = [];
    if ($post_id > 0) {
        $tags = get_the_tags($post_id);
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $tag_ids[] = (int) $tag->term_id;
            }
        }
    }

    if (empty($tag_ids)) {
        return dev_blog_pick_random_post_ids($limit, $exclude);
    }

    $scores = [];
    foreach ($tag_ids as $tag_id) {
        $object_ids = get_objects_in_term($tag_id, 'post_tag');
        if (is_wp_error($object_ids) || empty($object_ids)) {
            continue;
        }
        foreach ($object_ids as $oid) {
            $oid = (int) $oid;
            if ($oid === $post_id) {
                continue;
            }
            $scores[$oid] = ($scores[$oid] ?? 0) + 1;
        }
    }

    $published = array_flip(dev_blog_published_post_ids($exclude));
    foreach ($scores as $oid => $score) {
        if (!isset($published[$oid])) {
            unset($scores[$oid]);
        }
    }

    $must = [];
    $maybe = [];
    foreach ($scores as $oid => $score) {
        if ($score >= 2) {
            $must[$oid] = (int) $score;
        } elseif ($score > 0) {
            $maybe[$oid] = (int) $score;
        }
    }

    $picked = [];
    if ($must) {
        $picked = dev_blog_pick_weighted_ids($must, $limit);
    }
    $need = $limit - count($picked);
    if ($need > 0 && $maybe) {
        $picked = array_merge($picked, dev_blog_pick_weighted_ids($maybe, $need));
    }
    $need = $limit - count($picked);
    if ($need > 0) {
        $picked = array_merge(
            $picked,
            dev_blog_pick_random_post_ids($need, array_merge($exclude, $picked))
        );
    }

    return array_values(array_unique($picked));
}

function dev_blog_get_related_ids($post_id = 0, $limit = 2) {
    $post_id = (int) $post_id;
    $limit = max(1, (int) $limit);

    if ($post_id <= 0) {
        return dev_blog_pick_random_post_ids($limit);
    }

    $cached = get_post_meta($post_id, '_ly_related_ids', true);
    $computed_at = (int) get_post_meta($post_id, '_ly_related_at', true);
    $fresh = is_array($cached)
        && !empty($cached)
        && $computed_at > 0
        && (time() - $computed_at) < DAY_IN_SECONDS;

    if ($fresh) {
        return array_slice(array_values(array_map('intval', $cached)), 0, $limit);
    }

    $ids = dev_blog_compute_related_ids($post_id, $limit);
    update_post_meta($post_id, '_ly_related_ids', $ids);
    update_post_meta($post_id, '_ly_related_at', time());
    return $ids;
}

function dev_blog_flush_related_cache($post_id = 0) {
    $post_id = (int) $post_id;
    if ($post_id <= 0 || get_post_type($post_id) !== 'post') {
        return;
    }
    delete_post_meta($post_id, '_ly_related_ids');
    delete_post_meta($post_id, '_ly_related_at');
}

function dev_blog_flush_related_cache_on_save($post_id) {
    if (wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }
    dev_blog_flush_related_cache($post_id);
}

add_action('save_post', 'dev_blog_flush_related_cache_on_save');
add_action('deleted_post', 'dev_blog_flush_related_cache');
add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy === 'post_tag') {
        dev_blog_flush_related_cache((int) $object_id);
    }
}, 10, 4);

// Bootstrap 5 Nav Walker для меню
class Bootstrap_5_Nav_Walker extends Walker_Nav_Menu {
    // Массив с иконками Bootstrap для каждого пункта меню
    private $menu_icons = [
        'Главная' => 'bi bi-house',
        'Менеджмент' => 'bi bi-graph-up-arrow',
        'DevOps' => 'bi bi-hdd-network',
        'Карьера' => 'bi bi-ladder',
        'Ответы' => 'bi bi-question-circle',
        'Личное' => 'bi bi-person',
        'Ресурсы' => 'bi bi-book',
        'Инструменты' => 'bi bi-tools',
        'Проекты' => 'bi bi-kanban',
        'Письма' => 'bi bi-bell',
        'Обо мне' => 'bi bi-person-vcard',
        'Процессы' => 'bi bi-diagram-3',
        'Разработка' => 'bi bi-code-square',
        'Мысли' => 'bi bi-lightbulb',
        'Практика' => 'bi bi-clipboard-check',
        'Менторинг' => 'bi bi-mortarboard',
        'Автор' => 'bi bi-pen',
        'Скорая помощь' => 'bi bi-fire',
        'ИТ Скорая' => 'bi bi-activity',
        'Мои правила' => 'bi bi-shield-check',
        'Мои принципы' => 'bi bi-shield-check',
        'ITIL 4' => 'bi bi-layers-half',
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
        // Получаем иконку для текущего пункта меню из массива
        // Если иконка не найдена, используем универсальную иконку 'bi bi-chevron-right'
        $icon_class = $this->menu_icons[$item->title] ?? 'bi bi-chevron-right';

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

function get_post_views($post_id) {
    return (string) get_unique_post_views($post_id);
}

function get_unique_post_views($post_id) {
    $counted = (int) get_post_meta($post_id, 'post_views_count', true);
    $legacy  = (int) get_post_meta($post_id, 'unique_post_views', true);
    return max($counted, $legacy);
}

function dev_blog_rest_increment_views(WP_REST_Request $request) {
    $id = (int) $request['id'];
    $post = get_post($id);
    if (!$post || $post->post_type !== 'post' || $post->post_status !== 'publish') {
        return new WP_Error('ly_bad_post', 'Not found', array('status' => 404));
    }

    $count = (int) get_post_meta($id, 'post_views_count', true);
    $count++;
    update_post_meta($id, 'post_views_count', $count);

    return array(
        'ok'    => true,
        'views' => $count,
    );
}

function dev_blog_register_views_route() {
    register_rest_route('lyucean/v1', '/views/(?P<id>\d+)', array(
        'methods'             => 'POST',
        'callback'            => 'dev_blog_rest_increment_views',
        'permission_callback' => '__return_true',
        'args'                => array(
            'id' => array(
                'required' => true,
                'type'     => 'integer',
            ),
        ),
    ));
}
add_action('rest_api_init', 'dev_blog_register_views_route');

// Функция для получения SVG-паттерна
function get_random_pattern() {
    return '<svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="tech-pattern" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse">
                <!-- Сетка -->
                <path d="M 0 0 L 50 0 M 0 10 L 50 10 M 0 20 L 50 20 M 0 30 L 50 30 M 0 40 L 50 40 M 0 50 L 50 50" 
                      stroke="#6c757d" stroke-width="0.5" fill="none"/>
                <path d="M 0 0 L 0 50 M 10 0 L 10 50 M 20 0 L 20 50 M 30 0 L 10 50 M 20 0 L 20 50 M 30 0 L 30 50 M 40 0 L0 L 50 50" 
                      stroke="#6c757d" stroke-width="0.5" fill="none"/>
                
                <!-- IT символы -->
                <text x="5" y="15" font-family="monospace" font-size="8" fill="#dee2e6">no</text>
                <text x="20" y="15" font-family="monospace" font-size="8" fill="#dee2e6">image</text>
                <text x="15" y="35" font-family="monospace" font-size="4" fill="#dee2e6"> 10 50 M 20 0 L</text>
                <text x="5" y="45" font-family="monospace" font-size="4" fill="#dee2e6">M 20 0 L 20 50 M 30 0 L </text>
                
                <!-- Круги -->
                <circle cx="25" cy="25" r="20" stroke="#6c757d" stroke-width="0.5" fill="none"/>
                <circle cx="25" cy="25" r="15" stroke="#6c757d" stroke-width="0.5" fill="none"/>
                
                <!-- Линии -->
                <path d="M 0 0 L 50 50 M 50 0 L 0 50" stroke="#6c757d" stroke-width="0.5" fill="none"/>
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="#495057"/>
        <rect width="100%" height="100%" fill="url(#tech-pattern)"/>
    </svg>';
}

function dev_blog_compute_reading_time_for_post($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return 1;
    }

    $raw = (string) $post->post_content;
    $text = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags($raw)));
    $word_count = ($text === '') ? 0 : count(explode(' ', $text));
    $reading_time = (int) ceil($word_count / 150);

    $img_tags = preg_match_all('/<img\b/i', $raw);
    $wp_images = substr_count($raw, '<!-- wp:image');
    $image_count = max((int) $img_tags, (int) $wp_images);

    return max(1, (int) ceil($reading_time + $image_count * 0.5));
}

function dev_blog_refresh_reading_time($post_id) {
    $post_id = (int) $post_id;
    if ($post_id < 1 || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    if (get_post_type($post_id) !== 'post') {
        return;
    }

    update_post_meta($post_id, '_ly_reading_time', dev_blog_compute_reading_time_for_post($post_id));
}
add_action('save_post', 'dev_blog_refresh_reading_time');

function dev_blog_get_stored_reading_time($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : (int) get_the_ID();
    if ($post_id < 1) {
        return 1;
    }

    $cached = get_post_meta($post_id, '_ly_reading_time', true);
    if ($cached !== '' && $cached !== false) {
        return max(1, (int) $cached);
    }

    $time = dev_blog_compute_reading_time_for_post($post_id);
    update_post_meta($post_id, '_ly_reading_time', $time);
    return $time;
}

function get_reading_time($content = '') {
    unset($content);
    return dev_blog_get_stored_reading_time();
}

// Функция для получения версии деплоя (дата модификации functions.php)
function get_deployment_version() {
    // Устанавливаем часовой пояс Москвы
    $moscow_timezone = new DateTimeZone('Europe/Moscow');
    
    $functions_file = get_template_directory() . '/functions.php';
    if (file_exists($functions_file)) {
        $timestamp = filemtime($functions_file);
        $date = new DateTime('@' . $timestamp);
        $date->setTimezone($moscow_timezone);
        return $date->format('d.m.Y H:i');
    }
    // fallback на текущую дату и время по московскому времени
    $date = new DateTime('now', $moscow_timezone);
    return $date->format('d.m.Y H:i');
}

add_action('init', function () {
    if (wp_next_scheduled('cleanup_viewed_ips_hook')) {
        wp_clear_scheduled_hook('cleanup_viewed_ips_hook');
    }
}, 20);

// Подключение стилей для редактора Gutenberg
function add_custom_editor_styles() {
    add_theme_support('editor-styles');
    add_editor_style([
        'vendor/bootstrap/bootstrap.min.css',
        'editor-style.css'
    ]);
}
add_action('after_setup_theme', 'add_custom_editor_styles');

// Подключим наш блок с кратким описанием статьи
require_once get_template_directory() . '/blocks/summary-block.php';

// Подключим наш блок с конвертацией в Html
require_once get_template_directory() . '/blocks/static-html-export.php';

// Подключим наш блок с разрывом страницы
require_once get_template_directory() . '/blocks/article-divider-block.php';

// Подключим спойлер в выпадающее меню форматирования
require_once get_template_directory() . '/blocks/spoiler-block.php';

// Исходящий cURL к api.telegram.org (прокси из админки или TELEGRAM_HTTP_PROXY)
require_once get_template_directory() . '/telegram-outbound.php';

// Подключим блок кнопок обратной связи
require_once get_template_directory() . '/blocks/feedback-block.php';

// Подключим новогодние фишки (закомментируйте эту строку, чтобы отключить)
// require_once get_template_directory() . '/blocks/new-year-features.php';

// Отключаем автоматическую замену дефисов на тире
function disable_wptexturize() {
    remove_filter('the_content', 'wptexturize');
    remove_filter('the_excerpt', 'wptexturize');
    remove_filter('the_title', 'wptexturize');
    remove_filter('comment_text', 'wptexturize');
    remove_filter('widget_text_content', 'wptexturize');
}
add_action('init', 'disable_wptexturize', 5); // Приоритет 5 - выполняется раньше стандартных фильтров
