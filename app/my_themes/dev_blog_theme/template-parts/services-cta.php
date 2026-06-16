<?php
/**
 * Баннер: проекты и консалтинг.
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<section class="services-cta" aria-label="Проекты и консалтинг">
    <div class="services-cta__panel rounded-3">
        <div class="services-cta__inner">
            <div class="services-cta__body">
                <p class="services-cta__eyebrow">Сейчас есть слот</p>
                <h2 class="services-cta__title">IT-аудит, консалтинг, разработка под ключ</h2>
                <p class="services-cta__text">
                    12+ лет CIO: IT-функция и цифровой продукт с нуля - стратегия, команда, архитектура, масштабирование.
                    FMCG · e-commerce · SaaS · удалённо.
                </p>
            </div>
            <div class="services-cta__actions">
                <button type="button"
                        class="btn btn-primary services-cta__btn services-cta__open-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#servicesCtaModal">
                    Оставить заявку
                </button>
                <p class="services-cta__hint mb-0">
                    Свяжусь сам · телефон, Telegram или почта
                </p>
            </div>
        </div>
    </div>
</section>
<?php dev_blog_render_services_cta_modal(); ?>
