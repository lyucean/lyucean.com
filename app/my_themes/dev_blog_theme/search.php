<?php get_header(); ?>

    <div class="container-xl search-page">
        <main class="row">
            <div class="col-12">
                <?php if (have_posts()) : ?>
                <div class="row g-4">
                    <?php while (have_posts()) : the_post(); ?>

                        <div class="col-md-6 col-lg-4">
                            <!-- Карточка статьи -->
                            <article class="card h-100 border-0">
                                <!-- Обертка изображения -->
                                <div class="card-img-wrapper">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>"
                                           class="card-img-wrapper text-decoration-none">
                                            <img src="<?php the_post_thumbnail_url('large'); ?>"
                                                 class="card-img-top"
                                                 alt="<?php the_title(); ?>">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php the_permalink(); ?>"
                                           class="card-img-wrapper text-decoration-none">
                                            <div class="placeholder-img"
                                                 style="background-image: url('data:image/svg+xml,<?php echo rawurlencode(get_random_pattern()); ?>')"></div>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Основное содержимое карточки -->
                                <div class="card-body p-3 d-flex flex-column">
                                    <!-- Заголовок статьи -->
                                    <h2 class="h5 fw-bold mb-3">
                                        <a href="<?php the_permalink(); ?>"
                                           class="text-body-emphasis text-decoration-none">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>

                                    <!-- Описание статьи -->
                                    <p class="card-text mb-3">
                                        <?php
                                        // Пробуем получить мета-описание из Yoast SEO
                                        $yoast_meta = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);

                                        // Если есть мета-описание Yoast, используем его
                                        if (!empty($yoast_meta)) {
                                            echo wp_trim_words($yoast_meta, 20);
                                        } // Иначе используем стандартный excerpt
                                        else {
                                            echo wp_trim_words(get_the_excerpt(), 20);
                                        }
                                        ?>
                                    </p>

                                    <!-- Нижняя часть карточки с метаданными -->
                                    <div class="mt-auto">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                                            <!-- Блок с датой и тегами -->
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <!-- Дата публикации -->
                                                <small class="text-muted fw-light"><?php echo get_the_date(); ?></small>
                                                <!-- Теги статьи -->
                                                <?php
                                                $tags = get_the_tags();
                                                if ($tags) :
                                                    foreach ($tags as $tag) : ?>
                                                        <a href="<?php echo get_tag_link($tag->term_id); ?>"
                                                           class="text-decoration-none">
                                                        <span class="badge bg-opacity-10 text-secondary fw-light">
                                                            <span class="text-danger">#</span><?php echo $tag->name; ?>
                                                        </span>
                                                        </a>
                                                    <?php endforeach;
                                                endif; ?>
                                            </div>

                                            <!-- Время чтения -->
                                            <small class="text-muted fw-light">
                                                <i class="bi bi-clock-history"></i>
                                                <?php echo get_reading_time(get_the_content()) . ' мин чтения'; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mt-4">
                    <?php echo bootstrap_pagination(); ?>
                </div>
            </div>
            <?php else : ?>
                <main class="col-12 col-lg-10 offset-lg-1 px-lg-5">
                    <article class="rounded mb-4">
                        <div class="article-content">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/confused-travolta.gif"
                                 alt="Ничего не найдено" class="w-75 w-md-100">

                            <h1 class="text-center">Упс! Кажется, мы в тупике... 🤔</h1>

                            <p class="text-center lead">Либо ты слишком креативно написал запрос, либо я ещё не созрел на такую статью.</p>
                        </div>
                    </article>

                    <!-- Блок со случайными статьями -->
                    <?php get_template_part('template-parts/posts', 'related', [
                        'title' => 'Поиск не удался, но у меня есть кое-что интересное:'
                    ]); ?>

                </main>
            <?php endif; ?>
    </div>

<?php get_footer(); ?>