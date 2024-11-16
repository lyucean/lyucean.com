<!DOCTYPE html>
<html <?php language_attributes(); ?> lang="ru" data-bs-theme="light">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="py-3 border-bottom">
    <div class="container-xl">
        <div class="row align-items-center justify-content-between">
            <!-- Логотип -->
            <div class="col-auto">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a class="navbar-brand text-uppercase fw-bold fs-4" href="' . esc_url(home_url('/')) . '">' . get_bloginfo('name') . '</a>';
                }
                ?>
            </div>

            <!-- Поиск -->
            <div class="col-md-5 position-relative">
                <form class="d-flex" role="search">
                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input class="form-control border-start-0 ps-0" type="search" placeholder=" Поищем?" aria-label="Search">
                    </div>
                </form>
            </div>

            <!-- Переключатель темы -->
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-sun-fill text-muted"></i>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="theme-toggle">
                    </div>
                    <i class="bi bi-moon-fill text-muted"></i>
                </div>
            </div>

        </div>
    </div>
</header>

<!-- Контейнер для основного контента -->
<main class="container-xl py-4">
