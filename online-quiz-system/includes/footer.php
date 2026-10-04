    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="container footer-inner">
            <div class="footer-left">
                <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</p>
            </div>
            <nav class="footer-links" aria-label="Footer navigation">
                <a href="<?= e(BASE_URL) ?>/privacy.php">Privacy Policy</a>
                <a href="<?= e(BASE_URL) ?>/terms.php">Terms and Conditions</a>
                <a href="mailto:<?= e(CONTACT_EMAIL) ?>">Contact</a>
            </nav>
        </div>
    </footer>

    <script src="<?= e(BASE_URL) ?>/assets/js/main.js"></script>
</body>
</html>
