<?php get_header(); ?>

<div class="row">
    <div class="col-12">
        <?php if (have_posts()) : ?>
            <h1 class="mb-4">
                Результаты поиска: <?php echo get_search_query(); ?>
            </h1>

            <div class="row g-4">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('medium'); ?>"
                                     class="card-img-top"
                                     alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                        <?php the_title(); ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>
                            </div>

                            <div class="card-footer text-muted">
                                <small>
                                    <?php echo get_the_date(); ?>
                                </small>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="mt-4">
                <?php
                // Пагинация
                echo bootstrap_pagination();
                ?>
            </div>

        <?php else : ?>
            <div class="alert alert-info">
                <h3>Ничего не найдено</h3>
                <p>Попробуйте изменить поисковый запрос или вернуться на <a href="<?php echo home_url(); ?>">главную страницу</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
