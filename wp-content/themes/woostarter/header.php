<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header du site - sticky en haut de page -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <!-- Logo / nom du site -->
            <div class="site-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php bloginfo('name'); ?>
                </a>
            </div>

            <!-- Navigation principale -->
            <nav class="main-navigation" id="main-navigation" aria-label="<?php esc_attr_e('Menu principal', 'themesgi'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

            <!-- Actions : panier + bouton menu mobile -->
            <div class="header-actions">
                <?php if (class_exists('WooCommerce')) : ?>
                    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-icon" aria-label="<?php esc_attr_e('Voir le panier', 'themesgi'); ?>">
                        &#128722;
                        <?php $count = WC()->cart->get_cart_contents_count(); ?>
                        <?php if ($count > 0) : ?>
                            <span class="count"><?php echo $count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <!-- Bouton hamburger (visible uniquement sur mobile) -->
                <button class="menu-toggle" id="menu-toggle" aria-controls="main-navigation" aria-expanded="false" aria-label="<?php esc_attr_e('Ouvrir le menu', 'themesgi'); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Bannière promo (hook personnalisé) -->
<?php themeesgi_after_header(); ?>

<main class="site-content" id="main-content">
