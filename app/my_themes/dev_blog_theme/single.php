<?php get_header(); ?>

<div class="container-fluid single-post">
    <div class="row">
        <!-- Левая боковая панель -->
        <aside class="col-lg-1 d-none d-lg-block">
            <div class="sticky-top d-flex flex-column align-items-end post-side-nav">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="post-side-nav__btn" title="На главную">
                    <i class="bi bi-house" aria-hidden="true"></i>
                    <span class="visually-hidden">На главную</span>
                </a>
                <?php
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                if (!empty($referer) && strpos($referer, parse_url(get_site_url(), PHP_URL_HOST)) !== false) :
                    ?>
                    <a href="javascript:history.back()" class="post-side-nav__btn" title="Назад">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        <span class="visually-hidden">Назад</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Основное содержимое -->
        <main class="col-12 col-lg-10">
            <article class="post-shell mb-4">
                <?php
                $tags = get_the_tags();
                $feedback_counts = dev_blog_get_feedback_display_counts(get_the_ID());
                $views = (int) get_unique_post_views(get_the_ID());
                ?>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-hero">
                        <div class="post-hero__cover">
                            <?php dev_blog_the_hero_thumbnail(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="article-content">
                    <header class="post-header">
                        <h1 class="post-header__title"><?php the_title(); ?></h1>

                        <div class="post-header__meta">
                            <div class="post-header__meta-start">
                                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                                <span><?php echo esc_html(dev_blog_get_stored_reading_time() . ' мин'); ?></span>
                            </div>
                            <?php if ($views > 0) : ?>
                                <span class="post-header__meta-end"><?php echo esc_html($views . ' чит.'); ?></span>
                            <?php endif; ?>
                            <span class="visually-hidden">
                                Полезно:
                                <span id="feedback-hero-yes"><?php echo (int) $feedback_counts['display_yes']; ?></span>
                                /
                                <span id="feedback-hero-no"><?php echo (int) $feedback_counts['display_no']; ?></span>
                            </span>
                        </div>
                    </header>

                    <?php the_content(); ?>

                    <?php if ($tags) : ?>
                        <footer class="post-tags" aria-label="Теги">
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tags__tag">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </footer>
                    <?php endif; ?>
                </div>
            </article>

            <?php get_template_part('template-parts/posts', 'feetback'); ?>
            <?php get_template_part('template-parts/posts', 'related'); ?>
        </main>

        <!-- Правая боковая панель -->
        <aside class="col-lg-1 d-none d-lg-block">
            <div class="sticky-bottom d-flex align-items-start">
                <button id="scrollTopBtn" class="post-side-nav__btn opacity-0"
                        type="button"
                        title="Наверх">
                    <i class="bi bi-arrow-up" aria-hidden="true"></i>
                    <span class="visually-hidden">Наверх</span>
                </button>
            </div>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
