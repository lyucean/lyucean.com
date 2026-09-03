<?php
/**
 * Облако тегов в сайдбаре: топ-15 по числу постов.
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}

$tags = function_exists('dev_blog_get_popular_tags')
    ? dev_blog_get_popular_tags(15)
    : get_tags([
        'orderby' => 'count',
        'order'   => 'DESC',
        'number'  => 15,
        'hide_empty' => true,
    ]);

if (empty($tags) || is_wp_error($tags)) {
    return;
}
?>
<nav class="front-tags" aria-label="Теги">
    <ul class="front-tags__list">
        <?php foreach ($tags as $tag) : ?>
            <li class="front-tags__item">
                <a class="front-tags__link" href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>">#<?php echo esc_html(mb_strtolower($tag->name)); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
