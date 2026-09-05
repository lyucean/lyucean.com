<?php get_header(); ?>

<div class="container-fluid page">
    <div class="row">
        <!-- Левая боковая панель -->
        <aside class="col-lg-1 d-none d-lg-block">
            <div class="sticky-top d-flex flex-column align-items-end post-side-nav">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="post-side-nav__btn" title="На главную">
                    <i class="bi bi-house" aria-hidden="true"></i>
                    <span class="visually-hidden">На главную</span>
                </a>
                <a href="javascript:history.back()" class="post-side-nav__btn" title="Назад">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span class="visually-hidden">Назад</span>
                </a>
            </div>
        </aside>

        <!-- Основное содержимое -->
        <main class="col-12 col-lg-10">
            <article class="post-shell mb-4">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-hero">
                        <div class="post-hero__cover post-hero__cover--page">
                            <?php dev_blog_the_hero_thumbnail(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="article-content">
                    <header class="post-header">
                        <h1 class="post-header__title"><?php the_title(); ?></h1>
                    </header>
                    <?php the_content(); ?>
                </div>
            </article>
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
