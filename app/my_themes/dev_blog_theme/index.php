<?php get_header(); ?>

<div class="container py-4">
    <div class="row">
        <main class="col-lg-8">
            <?php if (have_posts()) : ?>
                <!-- Заголовок для архивных страниц -->
                <?php if (is_archive()) : ?>
                    <h1 class="archive-title mb-4">
                        <?php the_archive_title(); ?>
                    </h1>
                <?php endif; ?>

                <!-- Сетка записей -->
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="col">
                            <article class="card h-100">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="card-img-top-link">
                                        <?php the_post_thumbnail('medium', ['class' => 'card-img-top']); ?>
                                    </a>
                                <?php endif; ?>

                                <div class="card-body">
                                    <h2 class="card-title h5">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>

                                    <div class="card-text">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo get_the_date(); ?>

                                        <?php if (has_category()) : ?>
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-folder"></i>
                                            <?php the_category(', '); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Пагинация -->
                <div class="pagination-wrapper mt-4">
                    <?php
                    echo paginate_links([
                        'prev_text' => '<i class="fas fa-arrow-left"></i> Назад',
                        'next_text' => 'Вперед <i class="fas fa-arrow-right"></i>',
                        'class' => 'pagination-link'
                    ]);
                    ?>
                </div>

            <?php else : ?>
                <div class="alert alert-info">
                    <p class="mb-0">Записей пока нет.</p>
                </div>

                <?php get_template_part('template-parts/posts', 'related', [
                    'title' => 'Возможно, вас заинтересуют эти статьи:'
                ]); ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php get_footer(); ?>