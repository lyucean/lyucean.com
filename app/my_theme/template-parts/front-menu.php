<!-- Меню -->
<div class="front-menu offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" style="width: 280px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Меню</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
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
</div>