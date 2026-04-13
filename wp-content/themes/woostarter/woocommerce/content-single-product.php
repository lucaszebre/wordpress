<?php
/**
 * Template for displaying product content in the single-product.php template.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}
?>

<div class="single-product-wrapper">
    <div class="container">
        <div class="product-layout">
            <div class="product-gallery">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('large');
                } else {
                    echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr(get_the_title()) . '">';
                }
                ?>
            </div>

            <div class="product-summary">
                <h1 class="product-title"><?php the_title(); ?></h1>

                <?php if (function_exists('themeesgi_is_new_product') && themeesgi_is_new_product($product)) : ?>
                    <p class="manga-single-badge"><strong>🆕 Nouveau manga</strong></p>
                <?php endif; ?>

                <div class="product-price">
                    <?php if ($product) : ?>
                        <?php echo wp_kses_post($product->get_price_html()); ?>
                    <?php endif; ?>
                </div>

                <div class="product-short-desc">
                    <?php echo wp_kses_post($product ? $product->get_short_description() : ''); ?>
                </div>

                <?php if ($product && $product->is_type('simple') && $product->is_purchasable()) : ?>
                    <?php woocommerce_template_single_add_to_cart(); ?>
                <?php endif; ?>

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
                            <span><strong><?php esc_html_e('Catégories :', 'themesgi'); ?></strong> <?php echo wp_kses_post($categories); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-tabs" style="margin-top: 3rem;">
            <?php woocommerce_output_product_data_tabs(); ?>
        </div>

        <?php woocommerce_output_related_products(); ?>
    </div>
</div>
