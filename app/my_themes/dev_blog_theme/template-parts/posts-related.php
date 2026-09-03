<section class="post-after related-posts" aria-labelledby="related-posts-heading">
    <p class="related-posts__eyebrow" id="related-posts-heading">Ещё статьи</p>
    <h2 class="related-posts__heading visually-hidden">
        <?php echo esc_html($args['title'] ?? 'Если статья была полезной, загляни в мои другие статьи:'); ?>
    </h2>
    <ul class="related-posts__list">
        <?php
        $random_posts = new WP_Query(array(
            'posts_per_page' => 2,
            'post_type' => 'post',
            'orderby' => 'rand',
            'post__not_in' => array(get_the_ID()),
        ));

        if ($random_posts->have_posts()) :
            while ($random_posts->have_posts()) : $random_posts->the_post();
                ?>
                <li class="related-posts__item-wrap">
                    <a href="<?php the_permalink(); ?>" class="related-posts__item">
                        <div class="related-posts__thumb">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail', ['alt' => esc_attr(get_the_title())]);
                            } else {
                                echo '<div class="related-posts__thumb-placeholder" aria-hidden="true"></div>';
                            }
                            ?>
                        </div>
                        <div class="related-posts__body">
                            <h3 class="related-posts__title"><?php the_title(); ?></h3>
                            <div class="related-posts__meta">
                                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            </div>
                        </div>
                        <i class="bi bi-arrow-right related-posts__arrow" aria-hidden="true"></i>
                    </a>
                </li>
            <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </ul>
</section>
