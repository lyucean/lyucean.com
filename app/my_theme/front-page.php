<?php get_header(); ?>

<div class="container-xl">
    <div class="row g-4">
        <!-- Левая колонка с меню -->
        <div class="col-md-2">
            <?php get_template_part('template-parts/front', 'menu'); ?>
        </div>

        <!-- Правая колонка с контентом -->
        <div class="col-12 col-md-9">
            <?php get_template_part('template-parts/front', 'content'); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
