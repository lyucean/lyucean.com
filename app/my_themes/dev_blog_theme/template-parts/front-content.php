<!-- Панель сортировки -->
<div class="sort-panel">
    <nav class="sort-nav" aria-label="Сортировка статей">
        <a href="?sort=latest" class="sort-link <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'latest') ? 'active' : ''; ?>">
            Новые
        </a>
        <a href="?sort=oldest" class="sort-link <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'active' : ''; ?>">
            Старые
        </a>
        <a href="?sort=views" class="sort-link <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'views') ? 'active' : ''; ?>">
            Популярные
        </a>
    </nav>
</div>

<?php

// Определяем текущую страницу пагинации
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Определяем параметры сортировки
$args = array(
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => $paged // Добавляем параметр paged для пагинации
);

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

switch ($sort) {
    case 'latest':
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
    case 'oldest':
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
        break;
    case 'views':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'post_views_count';
        $args['order'] = 'DESC';
        break;
    default:
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
}

// Создаем новый запрос
$query = new WP_Query($args);
?>

<!-- Основной контейнер для статей -->
<div class="articles">
    <!-- Сетка статей: 1 колонка на мобильных, 2 колонки на десктопах -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
            <div class="col">
                <article class="post-card h-100">
                    <a href="<?php the_permalink(); ?>" class="post-card__cover text-decoration-none">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('large'); ?>"
                                 class="post-card__img"
                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                 loading="lazy"
                                 decoding="async">
                        <?php else: ?>
                            <div class="placeholder-img post-card__img"
                                 style="background-image: url('data:image/svg+xml,<?php echo rawurlencode(get_random_pattern()); ?>')"
                                 role="img"
                                 aria-label="<?php echo esc_attr(get_the_title()); ?>"></div>
                        <?php endif; ?>
                    </a>

                    <div class="post-card__body">
                        <h2 class="post-card__title">
                            <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="post-card__meta">
                            <div class="post-card__meta-start">
                                <time class="post-card__date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                                <span class="post-card__read">
                                    <?php echo esc_html(get_reading_time(get_the_content()) . ' мин'); ?>
                                </span>
                            </div>
                            <?php
                            $views = (int) get_unique_post_views(get_the_ID());
                            if ($views > 0) :
                                ?>
                                <span class="post-card__views">
                                    <?php echo esc_html($views . ' чит.'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </div>
        <?php endwhile; endif; ?>

    </div>

    <!-- Навигация по страницам -->
    <nav class="pagination-wrapper">
        <div class="pagination justify-content-center mt-4" role="navigation" aria-label="Постраничная навигация">
            <?php
            echo paginate_links(array(
                'prev_text' => '<span aria-hidden="true">←</span><span class="visually-hidden">Предыдущая</span>',
                'next_text' => '<span aria-hidden="true">→</span><span class="visually-hidden">Следующая</span>',
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'type' => 'plain',
                'mid_size' => 1,
                'end_size' => 1,
            ));
            ?>
        </div>
    </nav>

</div>

<?php wp_reset_postdata(); // Сбрасываем запрос ?>
