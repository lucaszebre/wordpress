<?php
// template-parts/content-product.php
// Carte produit affichée dans la grille de la page d'accueil
global $product;

// Récupérer le produit si pas encore chargé
if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}
?>

<article <?php post_class('product-card'); ?>>
    <!-- Image du produit -->
    <div class="product-card-image">
        <a href="<?php the_permalink(); ?>">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('themesgi-card');
            } else {
                // Image placeholder si pas de vignette
                echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr(get_the_title()) . '">';
            }
            ?>
        </a>
    </div>

    <!-- Infos du produit -->
    <div class="product-card-body">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

        <?php if ($product) : ?>
            <!-- Prix -->
            <div class="product-card-price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- Bouton ajouter au panier (uniquement pour les produits simples en stock) -->
            <?php
            if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
                printf(
                    '<a href="%s" data-quantity="1" class="button add-to-bag" data-product_id="%d" rel="nofollow">%s</a>',
                    esc_url($product->add_to_cart_url()),
                    $product->get_id(),
                    esc_html__('Ajouter au panier', 'themesgi')
                );
            }
            ?>
        <?php endif; ?>
    </div>
</article>
