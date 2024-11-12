<?php
// файл для отображения главной страницы
// Подключаем заголовок
get_header();
?>

    <div class="container mt-5">
        <header class="mb-4">
            <h1><?php bloginfo('name'); ?></h1>
            <p class="lead"><?php bloginfo('description'); ?></p>
        </header>

        <div class="row">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url(); ?>" class="card-img-top" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php the_title(); ?></h5>
                            <p class="card-text"><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Читать далее</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; else : ?>
                <p>Записей не найдено.</p>
            <?php endif; ?>
        </div>
    </div>

<?php
// Подключаем подвал
get_footer();