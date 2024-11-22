<?php
// Проверяем, зарегистрировано ли меню
if (has_nav_menu('sidebar-menu')) : ?>
    <!-- Десктопное меню -->
    <div class="front-menu-desktop">
        <nav class="nav flex-column sidebar-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'sidebar-menu',
                'container'      => false,
                'menu_class'     => 'nav flex-column',
                'fallback_cb'    => false,
                'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
                'depth'          => 1,
                'walker'         => new Bootstrap_5_Nav_Walker()
            ));
            ?>
        </nav>
    </div>
<?php endif; ?>
