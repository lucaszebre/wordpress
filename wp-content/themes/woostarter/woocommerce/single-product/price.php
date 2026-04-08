<?php
// woocommerce/single-product/price.php
// Surcharge de l'affichage du prix sur la page produit
// Affiche un badge avec le pourcentage de réduction si en promo

defined('ABSPATH') || exit;

global $product;

if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}
?>

<div class="product-price">
    <?php if ($product) : ?>
        <?php if ($product->is_on_sale()) : ?>
            <!-- Prix en promo : on affiche le prix barré + le nouveau prix + le % de réduction -->
            <del><?php echo wc_price($product->get_regular_price()); ?></del>
            <ins><?php echo wc_price($product->get_sale_price()); ?></ins>
            <span style="display:inline-block; background: var(--couleur-surlignage); color:#fff; font-size:0.75rem; padding:2px 8px; border-radius:4px; margin-left:0.5rem; font-weight:600;">
                <?php
                // Calcul du pourcentage de réduction
                $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                echo '-' . $percentage . '%';
                ?>
            </span>
        <?php else : ?>
            <span><?php echo $product->get_price_html(); ?></span>
        <?php endif; ?>
    <?php endif; ?>
</div>
