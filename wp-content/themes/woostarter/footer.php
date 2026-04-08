</main>

<!-- Footer du site -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-widgets">
            <!-- Colonne 1 : À propos -->
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <h3 class="widget-title"><?php esc_html_e('À propos', 'themesgi'); ?></h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem;">
                        <?php esc_html_e('themeESGI — Un thème e-commerce moderne construit avec WordPress et WooCommerce dans le cadre du projet ESGI.', 'themesgi'); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Colonne 2 : Liens rapides -->
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                <?php else : ?>
                    <h3 class="widget-title"><?php esc_html_e('Liens rapides', 'themesgi'); ?></h3>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => '',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ]);
                    ?>
                <?php endif; ?>
            </div>

            <!-- Colonne 3 : Contact -->
            <div class="footer-col">
                <?php if (is_active_sidebar('footer-3')) : ?>
                    <?php dynamic_sidebar('footer-3'); ?>
                <?php else : ?>
                    <h3 class="widget-title"><?php esc_html_e('Contact', 'themesgi'); ?></h3>
                    <ul>
                        <li><a href="mailto:contact@themesgi.fr">contact@themesgi.fr</a></li>
                        <li><a href="tel:+33123456789">+33 1 23 45 67 89</a></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> — <?php esc_html_e('Tous droits réservés.', 'themesgi'); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
