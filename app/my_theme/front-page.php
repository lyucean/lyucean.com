<?php get_header(); ?>

<div class="front-page container-xl">
    <div class="row g-4">
        <!-- Левая колонка с меню -->
        <div class="col-md-2 d-none d-md-block">
            <?php get_template_part('template-parts/front', 'menu'); ?>
        </div>

        <!-- Правая колонка с контентом -->
        <div class="col-12 col-md-9 mt-1">
            <?php get_template_part('template-parts/front', 'content'); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
