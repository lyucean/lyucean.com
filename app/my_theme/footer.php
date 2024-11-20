</main>
<?php wp_footer(); ?>
<footer class="py-4 mt-5 w-100">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center gap-4">
                    <a href="https://www.instagram.com/lyucean"
                       class="btn text-secondary"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="https://t.me/lyucean"
                       class="btn text-secondary"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Telegram">
                        <i class="bi bi-telegram"></i>
                    </a>

                    <a href="mailto:lyucean@gmail.com"
                       class="btn text-secondary"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Email">
                        <i class="bi bi-envelope"></i>
                    </a>

                    <a href="<?php bloginfo('rss2_url'); ?>"
                       class="btn text-secondary"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="RSS Feed">
                        <i class="bi bi-rss"></i>
                    </a>
                </div>

                <div class="text-center mt-4">
                    <small class="text-secondary">
                        © <?php echo date('Y'); ?> Lyucean
                    </small>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
