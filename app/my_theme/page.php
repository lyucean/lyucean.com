<?php get_header(); ?>

<div class="container-fluid page">
    <div class="row">
        <!-- Правая боковая панель -->
        <aside class="col-lg-1 d-none d-lg-block">
            <!-- Здесь можно добавить виджеты -->
        </aside>
        <!-- Основное содержимое -->
        <main class="col-12 col-lg-10 px-lg-5">
            <!-- Если нет изображения, показываем обычный заголовок -->
            <?php if (!has_post_thumbnail()) : ?>
                <div class="row">
                    <h1 class="h1 fw-bold text-center mb-3"><?php the_title(); ?></h1>
                </div>
            <?php endif; ?>
            <article class="rounded mb-4">
                <!-- Шапка страницы с изображением -->
                <div class="card border-0">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="header-image-container rounded-top">
                            <?php the_post_thumbnail('large', ['class' => 'card-img rounded-top']); ?>
                            <div class="header-blur-overlay"></div>
                            <div class="card-img-overlay d-flex flex-column justify-content-end">
                                <h1 class="card-title text-white text-center mb-3"><?php the_title(); ?></h1>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Содержимое страницы -->
                <div class="p-0 p-sm-3 p-md-4">
                    <div class="article-content">
                        <?php the_content(); ?>
                    </div>
                </div>

            </article>
        </main>
    </div>
</div>

<?php get_footer(); ?>
