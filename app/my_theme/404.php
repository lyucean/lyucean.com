<?php get_header(); ?>

<div class="container-fluid page">
    <div class="row">
        <!-- Основное содержимое -->
        <main class="col-12 col-lg-10 offset-lg-1 px-lg-5">
            <article class="rounded mb-4">
                <!-- Содержимое страницы -->
                <div class="article-content">
                    <div class="mb-4">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/404.gif"
                             alt="Ничего не найдено"
                             class="col-12 col-md-6 mb-4">
                    </div>


                    <h1 class="h1 fw-bold text-center mb-3">404: Похоже, вы заблудились.</h1>

                    <p class="lead mb-4 text-center">Эта страница удалена или никогда не была на этом сайте</p>

                    <div class="d-grid gap-3 col-md-8 mx-auto mb-5">
                        <a href="<?php echo home_url(); ?>" class="btn btn-link">
                            <i class="fas fa-home me-2"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </article>

            <!-- Блок с рекомендованными статьями -->
            <?php get_template_part('template-parts/posts', 'related', [
                'title' => 'Пока вы здесь, почитайте что-нибудь интересное:'
            ]); ?>

        </main>
    </div>
</div>

<?php get_footer(); ?>
