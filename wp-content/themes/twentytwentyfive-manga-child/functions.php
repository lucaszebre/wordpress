<?php

add_action( 'wp_enqueue_scripts', 'twentytwentyfive_manga_child_enqueue_styles', 20 );
function twentytwentyfive_manga_child_enqueue_styles() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'twentytwentyfive-manga-child-style',
		get_stylesheet_uri(),
		array(),
		$theme_version
	);
}

add_action( 'woocommerce_before_main_content', 'twentytwentyfive_manga_top_banner', 5 );
function twentytwentyfive_manga_top_banner() {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	echo '<section class="manga-top-banner" role="note">';
	echo '<p class="manga-top-banner__eyebrow">Manga Store</p>';
	echo '<h1 class="manga-top-banner__title">Nos Mangas</h1>';
	echo '<p class="manga-top-banner__shipping"><strong>Livraison gratuite en France métropolitaine 🇫🇷</strong></p>';
	echo '</section>';
}

add_filter( 'woocommerce_page_title', 'twentytwentyfive_manga_shop_title', 10, 1 );
function twentytwentyfive_manga_shop_title( $page_title ) {
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return 'Nos Mangas';
	}

	return $page_title;
}

function twentytwentyfive_manga_is_new_product( $product = null, $days = 30 ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( ! $product instanceof WC_Product ) {
		$product = wc_get_product( get_the_ID() );
	}

	if ( ! $product ) {
		return false;
	}

	$date_created = $product->get_date_created();
	if ( ! $date_created ) {
		return false;
	}

	return ( time() - $date_created->getTimestamp() ) <= ( $days * DAY_IN_SECONDS );
}

add_action( 'woocommerce_before_shop_loop_item_title', 'twentytwentyfive_manga_loop_new_badge', 8 );
function twentytwentyfive_manga_loop_new_badge() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( twentytwentyfive_manga_is_new_product( $product ) ) {
		echo '<span class="manga-card__badge" aria-label="Produit recent">🆕 Nouveau manga</span>';
	}
}

add_action( 'woocommerce_single_product_summary', 'twentytwentyfive_manga_single_new_badge', 4 );
function twentytwentyfive_manga_single_new_badge() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( twentytwentyfive_manga_is_new_product( $product ) ) {
		echo '<p class="manga-single-badge"><strong>🆕 Nouveau manga</strong></p>';
	}
}

add_action( 'wp', 'twentytwentyfive_manga_customize_shop_loop' );
function twentytwentyfive_manga_customize_shop_loop() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}

add_action( 'woocommerce_after_shop_loop_item_title', 'twentytwentyfive_manga_loop_categories', 15 );
function twentytwentyfive_manga_loop_categories() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$category_list = wc_get_product_category_list( $product->get_id(), ', ' );
	if ( empty( $category_list ) ) {
		return;
	}

	echo '<p class="manga-card__categories">Categories: ' . wp_kses_post( $category_list ) . '</p>';
}

add_action( 'woocommerce_after_shop_loop_item_title', 'twentytwentyfive_manga_loop_excerpt', 16 );
function twentytwentyfive_manga_loop_excerpt() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$short_description = wp_strip_all_tags( $product->get_short_description() );
	if ( empty( $short_description ) ) {
		return;
	}

	echo '<p class="manga-card__excerpt">' . esc_html( wp_trim_words( $short_description, 16, '...' ) ) . '</p>';
}

add_filter( 'woocommerce_loop_add_to_cart_args', 'twentytwentyfive_manga_loop_add_to_cart_args', 10, 2 );
function twentytwentyfive_manga_loop_add_to_cart_args( $args, $product ) {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return $args;
	}

	$existing_class = isset( $args['class'] ) ? $args['class'] : 'button';
	$args['class']  = trim( $existing_class . ' manga-add-to-cart manga-add-to-cart--primary' );

	return $args;
}

add_filter( 'woocommerce_product_add_to_cart_text', 'twentytwentyfive_manga_add_to_cart_text', 10, 2 );
function twentytwentyfive_manga_add_to_cart_text( $text, $product ) {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return $text;
	}

	if ( $product instanceof WC_Product && $product->is_purchasable() && $product->is_in_stock() ) {
		return 'Ajouter au panier';
	}

	return $text;
}

add_filter( 'woocommerce_thankyou_order_received_text', 'twentytwentyfive_manga_thankyou_message', 10, 2 );
function twentytwentyfive_manga_thankyou_message( $text, $order ) {
	if ( ! $order ) {
		return $text;
	}

	return 'Merci pour votre commande chez Manga store ! On espère vous revoir bientôt.';
}
