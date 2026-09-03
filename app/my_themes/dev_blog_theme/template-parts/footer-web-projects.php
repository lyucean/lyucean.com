<?php
/**
 * Блок «Мои проекты» в футере.
 *
 * @package dev_blog_theme
 */

if (! defined('ABSPATH')) {
    exit;
}

$footer_web_projects = [
    [
        'url'  => 'https://haccpro.ru',
        'host' => 'haccpro.ru',
        'desc' => 'SaaS по ХАССП полного цикла.',
    ],
    [
        'url'  => 'https://logtail.ru',
        'host' => 'logtail.ru',
        'desc' => 'Логи приложений: приём, поиск, realtime.',
    ],
    [
        'url'  => 'https://sovpadem.ru',
        'host' => 'sovpadem.ru',
        'desc' => 'Тесты предпочтений для пар.',
    ],
    [
        'url'  => 'https://abxtest.com',
        'host' => 'abxtest.com',
        'desc' => 'ABX-тесты аудио против аудиофилии.',
    ],
];
?>
<section class="footer-web-projects" aria-labelledby="footer-web-projects-heading">
    <div class="footer-web-projects__panel">
        <h2 id="footer-web-projects-heading" class="footer-web-projects__heading">
            <?php echo esc_html('Мои проекты'); ?>
        </h2>
        <ul class="footer-web-projects__grid">
            <?php foreach ($footer_web_projects as $project) : ?>
                <li class="footer-web-projects__item">
                    <a class="footer-web-projects__link"
                       href="<?php echo esc_url($project['url']); ?>"
                       rel="noopener noreferrer"
                       target="_blank">
                        <span class="footer-web-projects__host-row">
                            <span class="footer-web-projects__host"><?php echo esc_html($project['host']); ?></span>
                            <i class="bi bi-arrow-up-right footer-web-projects__ext" aria-hidden="true"></i>
                        </span>
                        <span class="footer-web-projects__desc"><?php echo esc_html($project['desc']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
