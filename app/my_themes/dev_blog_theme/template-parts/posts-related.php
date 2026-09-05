<section class="post-after related-posts" aria-labelledby="related-posts-heading">
    <p class="related-posts__eyebrow" id="related-posts-heading">Ещё статьи</p>
    <h2 class="related-posts__heading visually-hidden">
        <?php echo esc_html($args['title'] ?? 'Если статья была полезной, загляни в мои другие статьи:'); ?>
    </h2>
    <ul class="related-posts__list">
        <?php
        $source_id = is_singular('post') ? (int) get_queried_object_id() : 0;
        $related_ids = dev_blog_get_related_ids($source_id, 2);
        $related_posts = empty($related_ids)
            ? null
            : new WP_Query([
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => count($related_ids),
                'post__in'            => $related_ids,
                'orderby'             => 'post__in',
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ]);

        if ($related_posts && $related_posts->have_posts()) :
            while ($related_posts->have_posts()) : $related_posts->the_post();
                ?>
                <li class="related-posts__item-wrap">
                    <a href="<?php the_permalink(); ?>" class="related-posts__item">
                        <div class="related-posts__thumb">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail', [
                                    'alt'     => esc_attr(get_the_title()),
                                    'loading' => 'lazy',
                                    'decoding' => 'async',
                                ]);
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
