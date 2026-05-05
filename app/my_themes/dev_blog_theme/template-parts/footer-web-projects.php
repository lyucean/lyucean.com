<?php
/**
 * Блок «Мои проекты» в футере: карточки ссылок на внешние проекты.
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}

$footer_web_projects = [
    [
        'url'  => 'https://abxtest.com',
        'host' => 'abxtest.com',
        'desc' => 'ABX-тесты аудио: лечит аудиофилию на ранних стадиях.',
    ],
    [
        'url'  => 'https://haccpro.ru',
        'host' => 'haccpro.ru',
        'desc' => 'SaaS по ХАССП: первая в России платформа для ХАССП полного цикла.',
    ],
    [
        'url'  => 'https://sovpadem.ru',
        'host' => 'sovpadem.ru',
        'desc' => 'Тесты предпочтений для пар: проходите вместе, смотрите, где совпали.',
    ],
    [
        'url'  => 'https://logtail.ru',
        'host' => 'logtail.ru',
        'desc' => 'Logtail - логи приложений в одном месте: приём, хранение, поиск в реальном времени.',
    ],
];
?>
<!-- dev_blog_theme: footer-web-projects (единый источник блока «Мои проекты») -->
<section class="footer-web-projects mb-4 mb-md-5" aria-labelledby="footer-web-projects-heading">
    <div class="footer-web-projects__panel mx-auto rounded-3 px-3 px-md-4 py-4">
        <h2 id="footer-web-projects-heading" class="footer-web-projects__heading">
            <?php echo esc_html('Мои проекты'); ?>
        </h2>
        <div class="row g-3 g-md-4">
            <?php foreach ($footer_web_projects as $project) : ?>
                <div class="col-12 col-md-6">
                    <div class="footer-web-projects__card card h-100 border-0 p-4 position-relative bg-body">
                        <a
                            href="<?php echo esc_url($project['url']); ?>"
                            class="footer-web-projects__card-link stretched-link text-decoration-none"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <?php echo esc_html($project['host']); ?>
                        </a>
                        <p class="footer-web-projects__card-desc mb-0">
                            <?php echo esc_html($project['desc']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
