<?php
// woocommerce/single-product.php
// Surcharge du template produit seul de WooCommerce
// Layout personnalisé : image à gauche, infos à droite

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}
?>

<div class="single-product-wrapper">
    <div class="container">
        <!-- Layout 2 colonnes -->
        <div class="product-layout">
            <!-- Colonne gauche : image du produit -->
            <div class="product-gallery">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('large');
                } else {
                    echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr(get_the_title()) . '">';
                }
                ?>
            </div>

            <!-- Colonne droite : infos produit -->
            <div class="product-summary">
                <h1 class="product-title"><?php the_title(); ?></h1>

                <!-- Prix -->
                <div class="product-price">
                    <?php if ($product) : ?>
                        <?php echo $product->get_price_html(); ?>
                    <?php endif; ?>
                </div>

                <!-- Description courte -->
                <div class="product-short-desc">
                    <?php echo wp_kses_post($product ? $product->get_short_description() : ''); ?>
                </div>

                <!-- Formulaire d'ajout au panier -->
                <?php if ($product && $product->is_type('simple') && $product->is_purchasable()) : ?>
                    <?php woocommerce_template_single_add_to_cart(); ?>
                <?php endif; ?>

                <!-- Meta : SKU, stock, catégories -->
                <div class="product-meta-info">
                    <?php if ($product) : ?>
                        <?php if ($product->get_sku()) : ?>
                            <span><strong><?php esc_html_e('Réf :', 'themesgi'); ?></strong> <?php echo esc_html($product->get_sku()); ?></span>
                        <?php endif; ?>

                        <span><strong><?php esc_html_e('Disponibilité :', 'themesgi'); ?></strong>
                            <?php if ($product->is_in_stock()) : ?>
                                <span style="color: #28a745;"><?php esc_html_e('En stock', 'themesgi'); ?></span>
                            <?php else : ?>
                                <span style="color: #dc3545;"><?php esc_html_e('Rupture de stock', 'themesgi'); ?></span>
                            <?php endif; ?>
                        </span>

                        <?php
                        $categories = wc_get_product_category_list($product->get_id(), ', ');
                        if ($categories) : ?>
                            <span><strong><?php esc_html_e('Catégories :', 'themesgi'); ?></strong> <?php echo $categories; ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Onglets du produit (description, avis, etc.) -->
        <div class="product-tabs" style="margin-top: 3rem;">
            <?php woocommerce_output_product_data_tabs(); ?>
        </div>

        <!-- Produits similaires -->
        <?php woocommerce_output_related_products(); ?>
    </div>
</div>
