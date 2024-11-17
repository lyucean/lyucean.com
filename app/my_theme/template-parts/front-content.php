<!-- Основной контейнер для статей -->
<div class="articles">
    <!-- Сетка статей: 1 колонка на мобильных, 2 колонки на десктопах -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <!-- Колонка для отдельной статьи -->
            <div class="col">
                <!-- Карточка статьи -->
                <article class="card h-100 border-0 shadow">
                    <!-- Изображение статьи (если есть) -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="card-img-wrapper">
                            <img src="<?php the_post_thumbnail_url('medium'); ?>"
                                 class="card-img-top"
                                 alt="<?php the_title(); ?>">
                        </div>
                    <?php endif; ?>

                    <!-- Основное содержимое карточки -->
                    <div class="card-body p-3 d-flex flex-column">
                        <!-- Заголовок статьи -->
                        <h2 class="h5 fw-bold mb-3">
                            <a href="<?php the_permalink(); ?>" class="text-body-emphasis text-decoration-none">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <!-- Нижняя часть карточки с метаданными -->
                        <div class="mt-auto">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <!-- Блок с датой и тегами -->
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <!-- Дата публикации -->
                                    <small class="text-muted"><?php echo get_the_date(); ?></small>
                                    <!-- Теги статьи -->
                                    <?php
                                    $tags = get_the_tags();
                                    if ($tags) :
                                        foreach ($tags as $tag) : ?>
                                            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="text-decoration-none">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    #<?php echo $tag->name; ?>
                                                </span>
                                            </a>
                                        <?php endforeach;
                                    endif; ?>
                                </div>

                                <!-- Время чтения -->
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i>
                                    <?php
                                    $content = get_the_content();
                                    $word_count = str_word_count(strip_tags($content));
                                    $reading_time = ceil($word_count / 50);
                                    echo $reading_time . ' мин чтения';
                                    ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        <?php endwhile; endif; ?>
    </div>

    <!-- Навигация по страницам -->
    <nav class="pagination justify-content-center mt-4">
        <?php
        echo paginate_links(array(
            'prev_text' => __('← Предыдущая'),
            'next_text' => __('Следующая →'),
            'class' => 'pagination-link',
        ));
        ?>
    </nav>
</div>
