<?php
// woocommerce/single-product/short-description.php
// Surcharge de la description courte produit
// On l'affiche seulement si elle existe (pas de div vide)

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}

$short_description = $product ? $product->get_short_description() : '';

// On affiche que si il y a une description
if ($short_description) :
?>
<div class="product-short-desc">
    <?php echo wp_kses_post($short_description); ?>
</div>
<?php endif; ?>
