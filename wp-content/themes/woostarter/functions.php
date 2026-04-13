<?php
// themeESGI - functions.php
// Fonctions principales du thème WordPress + WooCommerce
// Projet ESGI

defined('ABSPATH') || exit;

define('THEMEESGI_VERSION', '1.0.0');
define('THEMEESGI_DIR', get_template_directory());
define('THEMEESGI_URI', get_template_directory_uri());


// --- CONFIGURATION DU THÈME ---
function themeesgi_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');

    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    add_image_size('themeesgi-hero', 1200, 600, true);
    add_image_size('themeesgi-card', 400, 400, true);
    add_image_size('themeesgi-post-card', 600, 375, true);

    register_nav_menus([
        'primary'  => __('Menu principal', 'themeesgi'),
        'footer'   => __('Menu footer', 'themeesgi'),
    ]);

    load_theme_textdomain('themeesgi', THEMEESGI_DIR . '/languages');
}
add_action('after_setup_theme', 'themeesgi_setup');


// --- STYLES ET SCRIPTS ---
function themeesgi_enqueue(): void {
    wp_enqueue_style(
        'themeesgi-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'themeesgi-style',
        get_stylesheet_uri(),
        ['themeesgi-google-fonts'],
        THEMEESGI_VERSION
    );

    wp_enqueue_script(
        'themeesgi-main',
        THEMEESGI_URI . '/assets/js/main.js',
        [],
        THEMEESGI_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'themeesgi_enqueue');


// --- ZONES DE WIDGETS ---
function themeesgi_widgets_init(): void {
    register_sidebar([
        'name'          => __('Footer Colonne 1', 'themeesgi'),
        'id'            => 'footer-1',
        'description'   => __('Première colonne du footer.', 'themeesgi'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Footer Colonne 2', 'themeesgi'),
        'id'            => 'footer-2',
        'description'   => __('Deuxième colonne du footer.', 'themeesgi'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Footer Colonne 3', 'themeesgi'),
        'id'            => 'footer-3',
        'description'   => __('Troisième colonne du footer.', 'themeesgi'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);

    register_sidebar([
        'name'          => __('Sidebar', 'themeesgi'),
        'id'            => 'sidebar-1',
        'description'   => __('Zone de widgets principale.', 'themeesgi'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'themeesgi_widgets_init');


// --- HOOK PERSONNALISÉ : après le header ---
function themeesgi_after_header(): void {
    do_action('themeesgi_after_header');
}


// --- BANNIÈRE PROMO ---
function themeesgi_render_promo_banner(): void {
    echo '<div class="promo-banner">';
    echo esc_html__('Livraison gratuite à partir de 50€ — Commandez maintenant !', 'themeesgi');
    echo '</div>';
}
add_action('themeesgi_after_header', 'themeesgi_render_promo_banner');


// --- WOOCOMMERCE : BADGES, TITRES ET MESSAGES ---
function themeesgi_is_new_product($product = null, int $days = 30): bool {
    if (!class_exists('WooCommerce')) {
        return false;
    }

    if (!$product instanceof WC_Product) {
        $product = wc_get_product(get_the_ID());
    }

    if (!$product) {
        return false;
    }

    $date_created = $product->get_date_created();
    if (!$date_created) {
        return false;
    }

    return (time() - $date_created->getTimestamp()) <= ($days * DAY_IN_SECONDS);
}

function themeesgi_render_shop_title($page_title) {
    if (function_exists('is_shop') && is_shop()) {
        return 'Nos Mangas';
    }

    return $page_title;
}
add_filter('woocommerce_page_title', 'themeesgi_render_shop_title', 10, 1);

function themeesgi_loop_new_badge(): void {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    if (themeesgi_is_new_product($product)) {
        echo '<span class="manga-card__badge" aria-label="Produit récent">🆕 Nouveau manga</span>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'themeesgi_loop_new_badge', 8);

function themeesgi_single_new_badge(): void {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    if (themeesgi_is_new_product($product)) {
        echo '<p class="manga-single-badge"><strong>🆕 Nouveau manga</strong></p>';
    }
}
add_action('woocommerce_single_product_summary', 'themeesgi_single_new_badge', 4);

function themeesgi_customize_shop_loop(): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
}
add_action('wp', 'themeesgi_customize_shop_loop');

function themeesgi_loop_categories(): void {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    $category_list = wc_get_product_category_list($product->get_id(), ', ');
    if (empty($category_list)) {
        return;
    }

    echo '<p class="manga-card__categories">Catégories : ' . wp_kses_post($category_list) . '</p>';
}
add_action('woocommerce_after_shop_loop_item_title', 'themeesgi_loop_categories', 15);

function themeesgi_loop_excerpt(): void {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    $short_description = wp_strip_all_tags($product->get_short_description());
    if (empty($short_description)) {
        return;
    }

    echo '<p class="manga-card__excerpt">' . esc_html(wp_trim_words($short_description, 16, '...')) . '</p>';
}
add_action('woocommerce_after_shop_loop_item_title', 'themeesgi_loop_excerpt', 16);

function themeesgi_loop_add_to_cart_args($args, $product) {
    if (!function_exists('is_shop') || !(is_shop() || is_product_taxonomy())) {
        return $args;
    }

    $existing_class = isset($args['class']) ? $args['class'] : 'button';
    $args['class'] = trim($existing_class . ' manga-add-to-cart manga-add-to-cart--primary');

    return $args;
}
add_filter('woocommerce_loop_add_to_cart_args', 'themeesgi_loop_add_to_cart_args', 10, 2);

function themeesgi_add_to_cart_text($text, $product) {
    if (!function_exists('is_shop') || !(is_shop() || is_product_taxonomy())) {
        return $text;
    }

    if ($product instanceof WC_Product && $product->is_purchasable() && $product->is_in_stock()) {
        return 'Ajouter au panier';
    }

    return $text;
}
add_filter('woocommerce_product_add_to_cart_text', 'themeesgi_add_to_cart_text', 10, 2);

function themeesgi_thankyou_message($text, $order) {
    if (!$order) {
        return $text;
    }

    return 'Merci pour votre commande chez Manga store ! On espère vous revoir bientôt.';
}
add_filter('woocommerce_thankyou_order_received_text', 'themeesgi_thankyou_message', 10, 2);


// --- BOUTON PANIER PERSONNALISÉ ---
function themeesgi_custom_add_to_cart_button($button, $product, $args): string {
    $button = sprintf(
        '<a href="%s" data-quantity="%s" class="%s add-to-bag" %s>%s</a>',
        esc_url($product->add_to_cart_url()),
        esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
        esc_attr(isset($args['class']) ? $args['class'] : 'button'),
        isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
        esc_html__('Ajouter au panier', 'themeesgi')
    );
    return $button;
}
add_filter('woocommerce_loop_add_to_cart_link', 'themeesgi_custom_add_to_cart_button', 10, 3);


// --- WRAPPER WOOCOMMERCE ---
function themeesgi_woocommerce_wrapper_before(): void {
    echo '<div class="container"><div class="content-area full-width"><div class="main-content">';
}

function themeesgi_woocommerce_wrapper_after(): void {
    echo '</div></div></div>';
}

remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'themeesgi_woocommerce_wrapper_before');
add_action('woocommerce_after_main_content', 'themeesgi_woocommerce_wrapper_after');
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);


// --- AJAX : mise à jour du compteur panier ---
function themeesgi_cart_fragment($fragments): array {
    ob_start();
    ?>
    <span class="count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-icon .count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'themeesgi_cart_fragment');


// --- CUSTOMIZER ---
function themeesgi_customize_register($wp_customize): void {
    $wp_customize->add_section('themeesgi_theme_options', [
        'title'    => __('Options du thème', 'themeesgi'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('themeesgi_promo_text', [
        'default'           => 'Livraison gratuite à partir de 50€',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('themeesgi_promo_text', [
        'label'   => __('Texte bannière promo', 'themeesgi'),
        'section' => 'themeesgi_theme_options',
        'type'    => 'text',
    ]);
}
add_action('customize_register', 'themeesgi_customize_register');


// --- HELPERS ---
function themeesgi_cart_url(): string {
    if (class_exists('WooCommerce')) {
        return wc_get_cart_url();
    }
    return '#';
}

function themeesgi_cart_count(): int {
    if (class_exists('WooCommerce')) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}
