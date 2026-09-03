<?php
if (!defined('ABSPATH')) {
    exit;
}

$tags = get_the_tags();
?>
<div class="post-card-meta">
    <p class="post-card-meta__line">
        <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
        <span><?php echo esc_html(get_reading_time(get_the_content()) . ' мин чтения'); ?></span>
    </p>
    <?php if ($tags && !is_wp_error($tags)) : ?>
        <p class="post-card-meta__tags">
            <?php foreach ($tags as $tag) : ?>
                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-card-meta__tag">#<?php echo esc_html($tag->name); ?></a>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>
</div>
