<?php get_header(); ?>

<div class="container-xl">
    <div class="row g-4">
        <!-- Левая колонка с меню -->
        <div class="col-md-3 d-none d-md-block">
            <div class="position-sticky" style="top: 1rem;">
                <nav class="nav flex-column sidebar-nav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'sidebar-menu',
                        'container'      => false,
                        'menu_class'     => 'nav flex-column gap-1',
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                        'depth'          => 1,
                        'walker'         => new Bootstrap_5_Nav_Walker()
                    ));
                    ?>
                </nav>
            </div>
        </div>

        <!-- Правая колонка с контентом -->
        <div class="col-12 col-md-9">
            <main>
                <?php
                if (have_posts()) :
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content', get_post_type());
                    endwhile;

                    the_posts_pagination();
                else :
                    get_template_part('template-parts/content', 'none');
                endif;
                ?>
            </main>
        </div>
    </div>
</div>

<?php get_footer(); ?>
