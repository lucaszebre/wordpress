<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$product_link       = get_permalink( $product->get_id() );
$product_title      = $product->get_name();
$category_list      = wc_get_product_category_list( $product->get_id(), ', ' );
$short_description  = wp_strip_all_tags( $product->get_short_description() );
$short_description  = wp_trim_words( $short_description, 16, '...' );
?>
<li <?php wc_product_class( '', $product ); ?>>
	<article class="manga-card">
		<div class="manga-card__media">
			<a class="manga-card__media-link" href="<?php echo esc_url( $product_link ); ?>">
				<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			</a>
		</div>

		<div class="manga-card__content">
			<h3 class="manga-card__title">
				<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_title ); ?></a>
			</h3>

			<div class="manga-card__price">
				<?php woocommerce_template_loop_price(); ?>
			</div>

			<?php if ( ! empty( $category_list ) ) : ?>
				<p class="manga-card__categories">Categories: <?php echo wp_kses_post( $category_list ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $short_description ) ) : ?>
				<p class="manga-card__excerpt"><?php echo esc_html( $short_description ); ?></p>
			<?php endif; ?>

			<div class="manga-card__actions">
				<?php
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => 'button manga-add-to-cart manga-add-to-cart--primary',
					)
				);
				?>
			</div>
		</div>
	</article>
</li>