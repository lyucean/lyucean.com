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

            <!-- Поиск (скрыт на мобильных) -->
            <div class="col d-none d-md-block">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <form class="d-flex" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 opacity-50">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input class="form-control border-start-0 ps-0 opacity-50"
                                       type="search"
                                       placeholder=" Поищем?"
                                       aria-label="Search"
                                       name="s"
                                       value="<?php echo get_search_query(); ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Иконка поиска (видна только на мобильных) и переключатель темы -->
            <div class="col-auto">
                <div class="d-flex align-items-center gap-4">
                    <div class="d-md-none">
                        <i class="bi bi-search opacity-50" id="mobile-search-icon"></i>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-sun-fill opacity-50"></i>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="theme-toggle">
                        </div>
                        <i class="bi bi-moon-fill opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Мобильная строка поиска -->
    <div class="mobile-search-bar d-md-none" style="display: none;">
        <div class="container-xl py-3">
            <form class="d-flex gap-2" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input class="form-control"
                       type="search"
                       placeholder="Поищем?"
                       aria-label="Search"
                       name="s"
                       value="<?php echo get_search_query(); ?>">
                <button class="btn btn-primary" type="submit">Найти</button>
            </form>
        </div>
    </div>
</header>






<!-- Контейнер для основного контента -->
<main class="container-xl py-4">
