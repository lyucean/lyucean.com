</main>
<?php wp_footer(); ?>
<footer class="site-footer">
    <div class="container-xl site-footer__inner">

        <?php get_template_part('template-parts/footer', 'web-projects'); ?>

        <div class="footer-bar">
            <p class="footer-copyright">
                © Валентин Панченко · <?php echo esc_html(get_deployment_version()); ?>
            </p>
            <nav class="footer-social" aria-label="Контакты">
                <a href="https://t.me/lyucean"
                   class="footer-social__link"
                   target="_blank"
                   rel="noopener noreferrer">telegram</a>
                <a href="https://www.instagram.com/lyucean"
                   class="footer-social__link"
                   target="_blank"
                   rel="noopener noreferrer">instagram</a>
                <a href="mailto:lyucean@gmail.com"
                   class="footer-social__link">email</a>
                <a href="<?php bloginfo('rss2_url'); ?>"
                   class="footer-social__link"
                   target="_blank"
                   rel="noopener noreferrer">rss</a>
            </nav>
        </div>

    </div>
</footer>
</body>
</html>
