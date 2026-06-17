<?php
/**
 * Баннер CIO в стиле бокового меню (block-menu-desktop).
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="services-cta block-menu-desktop" aria-label="Консалтинг CIO">
    <nav class="nav flex-column flex-md-row flex-md-wrap align-items-md-center sidebar-nav services-cta__nav rounded-3">
        <span class="nav-link services-cta__intro">
            <span class="services-cta__copy">
                <span class="services-cta__status" aria-hidden="true">
                    <span class="visually-hidden">Сейчас есть слот</span>
                </span>
                <span class="services-cta__title">Открыт к проектам</span>
                <span class="services-cta__desc">CIO · стратегия · команда · продукт с нуля</span>
            </span>
        </span>
        <button type="button"
                class="nav-link services-cta__open-btn"
                data-bs-toggle="modal"
                data-bs-target="#servicesCtaModal">
            <i class="bi bi-chat-left-text" aria-hidden="true"></i>
            Обсудить задачу
        </button>
    </nav>
</section>
<?php dev_blog_render_services_cta_modal(); ?>
