<?php get_header(); ?>

<div class="container-fluid single-post">
    <div class="row">
        <!-- Правая боковая панель -->
        <aside class="col-lg-1 d-none d-lg-block">
            <!-- Здесь можно добавить виджеты -->
        </aside>
        <!-- Основное содержимое -->
        <main class="col-12 col-lg-10 px-lg-5">
            <article class="rounded mb-4"><!-- Убрали класс bg-white -->

                <!-- Шапка статьи с изображением -->
                <div class="card border-0">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="header-image-container rounded-top">
                            <?php the_post_thumbnail('large', ['class' => 'card-img rounded-top']); ?>
                            <div class="header-blur-overlay"></div>
                            <div class="card-img-overlay d-flex flex-column justify-content-end">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="text-white-50"><?php echo get_the_date(); ?></span>
                                    <?php
                                    $tags = get_the_tags();
                                    if ($tags) :
                                        foreach ($tags as $tag) : ?>
                                            <a href="<?php echo get_tag_link($tag->term_id); ?>"
                                               class="text-decoration-none">
                                                <span class="badge bg-light bg-opacity-10 text-white fw-light">
                                                    <span class="text-danger">#</span><?php echo $tag->name; ?>
                                                </span>
                                            </a>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Если нет изображения, показываем обычный заголовок -->
                        <div class="p-4">
                            <h1 class="h2 fw-bold text-center mb-3"><?php the_title(); ?></h1>
                            <div class="text-center mb-4">
                                <div class="d-inline-flex flex-wrap align-items-center">
                                    <span class="text-secondary fs-6 me-2"><?php echo get_the_date(); ?></span>
                                    <?php
                                    if ($tags) :
                                        foreach ($tags as $tag) : ?>
                                            <a href="<?php echo get_tag_link($tag->term_id); ?>"
                                               class="tag-link text-decoration-none">
                                            <span class="badge bg-opacity-10 text-secondary fs-6 fw-light">
                                                <span class="text-danger">#</span><?php echo $tag->name; ?>
                                            </span>
                                            </a>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Содержимое статьи -->
                <div class="p-0 p-sm-3 p-md-4">
                    <div class="article-content">

                        <h1 class=" "><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>
        </main>
    </div>
</div>

<?php get_footer(); ?>
