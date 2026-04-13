<?php
/**
 * The Template for displaying product archives, including the main shop page.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_main_content');
?>

<div class="custom-shop-wrapper">
    <header class="custom-shop-banner">
        <p class="custom-shop-banner__eyebrow">Catalogue cohérent et organisé</p>
        <h1 class="custom-shop-banner__title"><?php esc_html_e('Nos Mangas', 'themeesgi'); ?></h1>
        <p class="custom-shop-banner__description">
            <?php esc_html_e('Découvrez une sélection de mangas classée par collections, prix et nouveautés.', 'themeesgi'); ?>
        </p>
        <p class="custom-shop-banner__shipping">
            <strong><?php esc_html_e('Livraison gratuite à partir de 50€ d\'achat', 'themeesgi'); ?></strong>
        </p>
    </header>

    <?php
    
    do_action('woocommerce_before_shop_loop');

    if (woocommerce_product_loop()) {
        woocommerce_product_loop_start();

        if (wc_get_loop_prop('total')) {
            while (have_posts()) {
                the_post();

                /**
                 * Hook: woocommerce_shop_loop.
                 */
                do_action('woocommerce_shop_loop');

                wc_get_template_part('content', 'product');
            }
        }

        woocommerce_product_loop_end();

        do_action('woocommerce_after_shop_loop');
    } else {
        do_action('woocommerce_no_products_found');
    }
    ?>

</div>

<?php
do_action('woocommerce_after_main_content');

do_action('woocommerce_sidebar');

get_footer('shop');