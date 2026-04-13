<?php
/**
 * The template for displaying product content within loops.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}
?>

<article <?php post_class('product-card'); ?>>
    <div class="product-card-image">
        <?php if (function_exists('themeesgi_is_new_product') && themeesgi_is_new_product($product)) : ?>
            <span class="manga-card__badge" aria-label="Produit récent">🆕 Nouveau manga</span>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('themeesgi-card');
            } else {
                echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr(get_the_title()) . '">';
            }
            ?>
        </a>
    </div>

    <div class="product-card-body">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

        <?php if ($product) : ?>
            <div class="product-card-price">
                <?php echo wp_kses_post($product->get_price_html()); ?>
            </div>

            <?php
            $category_list = wc_get_product_category_list($product->get_id(), ', ');
            if ($category_list) :
            ?>
                <p class="manga-card__categories">Catégories : <?php echo wp_kses_post($category_list); ?></p>
            <?php endif; ?>

            <?php
            $short_description = wp_strip_all_tags($product->get_short_description());
            if (!empty($short_description)) :
            ?>
                <p class="manga-card__excerpt"><?php echo esc_html(wp_trim_words($short_description, 16, '...')); ?></p>
            <?php endif; ?>

            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                <?php
                printf(
                    '<a href="%s" data-quantity="1" class="button add-to-bag" data-product_id="%d" rel="nofollow">%s</a>',
                    esc_url($product->add_to_cart_url()),
                    (int) $product->get_id(),
                    esc_html__('Ajouter au panier', 'themeesgi')
                );
                ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</article>