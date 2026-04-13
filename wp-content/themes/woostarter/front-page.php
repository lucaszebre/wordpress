<?php
// front-page.php
// Page d'accueil du site (utilisé quand on a une page statique en page d'accueil)
get_header();
?>

<!-- Section Hero - bannière principale -->
<section class="hero">
    <div class="container">
        <h1><?php esc_html_e('Bienvenue sur themeESGI', 'themesgi'); ?></h1>
        <p><?php esc_html_e('Découvrez notre collection de produits premium. Design moderne et qualité exceptionnelle.', 'themesgi'); ?></p>
        <div>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-primary">
                <?php esc_html_e('Voir la boutique', 'themesgi'); ?>
            </a>
            <a href="#featured-products" class="btn btn-outline">
                <?php esc_html_e('Produits en vedette', 'themesgi'); ?>
            </a>
        </div>
    </div>
</section>

<?php if (class_exists('WooCommerce')) : ?>
<!-- Section produits en vedette -->
<section class="featured-products" id="featured-products">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('Produits en vedette', 'themesgi'); ?></h2>
        <div class="products-grid">
            <?php
            // On cherche d'abord les produits "mis en avant"
            $featured = new WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    ],
                ],
            ]);

            if ($featured->have_posts()) :
                while ($featured->have_posts()) : $featured->the_post();
                    get_template_part('template-parts/content', 'product');
                endwhile;
                wp_reset_postdata();
            else :
                // Si pas de produits en vedette, on affiche les derniers produits
                $fallback = new WP_Query([
                    'post_type'      => 'product',
                    'posts_per_page' => 8,
                ]);
                if ($fallback->have_posts()) :
                    while ($fallback->have_posts()) : $fallback->the_post();
                        get_template_part('template-parts/content', 'product');
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p style="text-align:center; grid-column: 1/-1; color: #6c757d;">';
                    esc_html_e('Aucun produit trouvé. Ajoutez des produits via WooCommerce pour les voir ici.', 'themesgi');
                    echo '</p>';
                endif;
            endif;
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section CTA newsletter -->
<section class="cta-section">
    <div class="container">
        <h2><?php esc_html_e('Restez informé', 'themesgi'); ?></h2>
        <p><?php esc_html_e('Inscrivez-vous à notre newsletter et recevez 10% sur votre première commande.', 'themesgi'); ?></p>
        <a href="#" class="btn btn-primary"><?php esc_html_e('S\'inscrire', 'themesgi'); ?></a>
    </div>
</section>

<?php
get_footer();
