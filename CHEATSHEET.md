# WordPress Theme Development - Cheatsheet
## Projet ESGI - themeESGI

---

## 1. STRUCTURE D'UN THÈME WORDPRESS

```
mon-theme/
├── style.css          ← Obligatoire : en-tête du thème + styles
├── index.php          ← Obligatoire : template par défaut
├── functions.php      ← Fonctions, hooks, configuration du thème
├── header.php         ← Partial : en-tête du site (<head> + nav)
├── footer.php         ← Partial : pied de page
├── sidebar.php        ← Partial : barre latérale
├── front-page.php     ← Template : page d'accueil statique
├── home.php           ← Template : page du blog
├── single.php         ← Template : article seul
├── page.php           ← Template : page par défaut
├── archive.php        ← Template : archives (catégories, tags, dates)
├── search.php         ← Template : résultats de recherche
├── 404.php            ← Template : page non trouvée
├── comments.php       ← Template : section commentaires
├── screenshot.png     ← Image d'aperçu (1200x900px recommandé)
│
├── template-parts/    ← Partials réutilisables
│   ├── content.php
│   ├── content-none.php
│   └── content-product.php
│
├── page-templates/    ← Templates de page personnalisés
│   └── full-width.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
└── woocommerce/       ← Surcharge des templates WooCommerce
    ├── single-product.php
    └── single-product/
        ├── price.php
        └── short-description.php
```

---

## 2. EN-TÊTE DU style.css (OBLIGATOIRE)

```css
/*
Theme Name: Mon Theme        ← Nom du thème (obligatoire)
Author: Lucas                ← Auteur
Description: Description...  ← Description
Version: 1.0.0               ← Version
Requires PHP: 8.0
License: GNU General Public License v2 or later
Text Domain: mon-theme       ← Pour la traduction (i18n)
Tags: e-commerce, woocommerce
*/
```

---

## 3. HIÉRARCHIE DES TEMPLATES

WordPress cherche les templates dans cet ordre pour chaque type de page :

**Page d'accueil :** front-page.php → home.php → index.php

**Article seul :** single-{post-type}.php → single.php → index.php

**Page :** page-{slug}.php → page-{id}.php → page.php → index.php

**Archive (catégorie) :** category-{slug}.php → category-{id}.php → category.php → archive.php → index.php

**Archive (tag) :** tag-{slug}.php → tag-{id}.php → tag.php → archive.php → index.php

**Résultat de recherche :** search.php → index.php

**404 :** 404.php → index.php

---

## 4. FUNCTIONS.PHP - CONFIGURATION

### Setup du thème

```php
// Dans functions.php - le hook 'after_setup_theme' est appelé
// quand le thème est chargé par WordPress
function mon_theme_setup(): void {
    // Support du titre dans la balise <title>
    add_theme_support('title-tag');

    // Images mises en avant (thumbnails)
    add_theme_support('post-thumbnails');

    // HTML5 pour les formulaires et listes
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);

    // Support WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Tailles d'images personnalisées
    // add_image_size(nom, largeur, hauteur, crop)
    add_image_size('mon-hero', 1200, 600, true);
    add_image_size('mon-card', 400, 400, true);

    // Enregistrer des emplacements de menu
    register_nav_menus([
        'primary' => __('Menu principal', 'mon-theme'),
        'footer'  => __('Menu footer', 'mon-theme'),
    ]);

    // Traduction
    load_theme_textdomain('mon-theme', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'mon_theme_setup');
```

---

### Charger les styles et scripts

```php
function mon_theme_enqueue(): void {
    // wp_enqueue_style(handle, src, dependencies, version, media)
    wp_enqueue_style(
        'mon-theme-style',          // identifiant unique
        get_stylesheet_uri(),       // URL du style.css
        [],                         // dépendances
        '1.0.0'                     // version
    );

    // wp_enqueue_script(handle, src, dependencies, version, in_footer)
    wp_enqueue_script(
        'mon-theme-js',
        get_template_directory_uri() . '/assets/js/main.js',
        [],        // dépendances (ex: ['jquery'])
        '1.0.0',
        true       // charger dans le footer (avant </body>)
    );
}
add_action('wp_enqueue_scripts', 'mon_theme_enqueue');
```

---

### Zones de widgets (sidebars)

```php
function mon_theme_widgets(): void {
    register_sidebar([
        'name'          => __('Ma Sidebar', 'mon-theme'),
        'id'            => 'sidebar-1',           // ID unique
        'description'   => __('Description...', 'mon-theme'),
        'before_widget' => '<div class="widget">', // HTML avant chaque widget
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'mon_theme_widgets');
```

---

## 5. TEMPLATE TAGS (Fonctions de template)

### Dans la boucle (The Loop)

```php
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <h2><?php the_title(); ?></h2>           <!-- Titre du post -->
        <p><?php the_content(); ?></p>            <!-- Contenu complet -->
        <p><?php the_excerpt(); ?></p>            <!-- Extrait automatique -->
        <span><?php echo get_the_date(); ?></span><!-- Date -->
        <span><?php the_author(); ?></span>       <!-- Auteur -->
        <span><?php the_category(', '); ?></span> <!-- Catégories -->
        <a href="<?php the_permalink(); ?>">Lire</a> <!-- Lien permanent -->

        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('mon-card'); ?> <!-- Image mise en avant -->
        <?php endif; ?>
    <?php endwhile; ?>
<?php else : ?>
    <p>Aucun article trouvé.</p>
<?php endif; ?>
```

### Hors boucle

```php
get_header();           // Inclut header.php
get_footer();           // Inclut footer.php
get_sidebar();          // Inclut sidebar.php
get_template_part('template-parts/content', 'product'); // Inclut template-parts/content-product.php
wp_head();              // Hook crucial dans <head> (dans header.php)
wp_footer();            // Hook crucial avant </body> (dans footer.php)
```

### Pagination

```php
the_posts_pagination([
    'mid_size'  => 2,
    'prev_text' => '&laquo; Précédent',
    'next_text' => 'Suivant &raquo;',
]);
```

### Navigation entre articles

```php
the_post_navigation([
    'prev_text' => '&laquo; %title',
    'next_text' => '%title &raquo;',
]);
```

---

## 6. LA BOUCLE WP_QUERY (Requêtes personnalisées)

```php
// Requête personnalisée pour récupérer des produits
$args = [
    'post_type'      => 'product',         // Type de post
    'posts_per_page' => 8,                 // Nombre par page (-1 = tous)
    'orderby'        => 'date',            // Tri
    'order'          => 'DESC',            // Ordre
    'tax_query'      => [                  // Filtrer par taxonomie
        [
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
        ],
    ],
];

$ma_query = new WP_Query($args);

if ($ma_query->have_posts()) :
    while ($ma_query->have_posts()) : $ma_query->the_post();
        // Afficher les posts...
    endwhile;
    wp_reset_postdata();  // IMPORTANT : réinitialiser après une requête custom
endif;
```

---

## 7. HOOKS : ACTIONS ET FILTRES

### Actions - Exécuter du code à un moment précis

```php
// add_action(hook_name, callback, priority, args_count)

function mon_function() {
    echo '<p>Bonjour !</p>';
}
add_action('wp_footer', 'mon_function');
```

### Filtres - Modifier une valeur avant qu'elle soit utilisée

```php
// add_filter(hook_name, callback, priority, args_count)

function mon_filtre($content) {
    return '<div class="wrapper">' . $content . '</div>';
}
add_filter('the_content', 'mon_filtre');
```

### Hooks WordPress les plus utilisés

| Hook | Type | Description |
|------|------|-------------|
| `after_setup_theme` | Action | Après le chargement du thème |
| `wp_enqueue_scripts` | Action | Pour charger CSS/JS |
| `widgets_init` | Action | Pour enregistrer les widgets |
| `wp_head` | Action | Dans le `<head>` |
| `wp_footer` | Action | Avant `</body>` |
| `the_content` | Filtre | Modifier le contenu d'un post |
| `the_title` | Filtre | Modifier le titre |
| `wp_nav_menu_items` | Filtre | Modifier les items du menu |

### Créer son propre hook

```php
// Déclarer le hook (dans functions.php)
function mon_theme_after_header(): void {
    do_action('mon_theme_after_header');
}

// L'utiliser dans un template (header.php)
<?php mon_theme_after_header(); ?>

// S'y raccrocher depuis n'importe où
function ma_fonction(): void {
    echo '<div class="banner">Promo !</div>';
}
add_action('mon_theme_after_header', 'ma_fonction');
```

---

## 8. WOOCOMMERCE - INTÉGRATION AU THÈME

### Support de base

```php
// Dans after_setup_theme
add_theme_support('woocommerce');
add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');
```

### Remplacer les wrappers WooCommerce

```php
// WooCommerce entoure son contenu avec ses propres divs
// On les remplace par les nôtres pour matcher notre layout

remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', function() {
    echo '<div class="container"><div class="main-content">';
});
add_action('woocommerce_after_main_content', function() {
    echo '</div></div>';
});

// Retirer la sidebar WooCommerce (on gère la nôtre)
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
```

### Surcharger un template WooCommerce

1. Créer le dossier `woocommerce/` dans votre thème
2. Copier le fichier depuis `woocommerce/templates/...`
3. Le modifier dans votre thème

Exemple : `woocommerce/templates/single-product/price.php`
→ Copier vers : `mon-theme/woocommerce/single-product/price.php`

### Hooks WooCommerce utiles

```php
// Modifier le bouton "Add to cart"
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product, $args) {
    return '<a href="..." class="add-to-bag">Ajouter au panier</a>';
}, 10, 3);

// Mise à jour AJAX du compteur panier
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>
    <span class="count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-icon .count'] = ob_get_clean();
    return $fragments;
});

// Obtenir l'URL du panier
wc_get_cart_url();

// Obtenir l'URL de la boutique
wc_get_page_id('shop');  // ID de la page boutique
get_permalink(wc_get_page_id('shop'));  // URL de la boutique
```

### Obtenir le produit courant

```php
global $product;

// Récupérer le produit (si pas encore chargé)
if (!is_a($product, WC_Product::class)) {
    $product = wc_get_product(get_the_ID());
}

$product->get_price_html();          // HTML du prix
$product->get_price();               // Prix brut
$product->get_regular_price();       // Prix normal
$product->get_sale_price();          // Prix promo
$product->is_on_sale();              // En promo ?
$product->is_in_stock();             // En stock ?
$product->is_purchasable();          // Achetable ?
$product->is_type('simple');         // Type du produit
$product->get_sku();                 // Référence
$product->get_short_description();   // Description courte
$product->get_id();                  // ID du produit
$product->add_to_cart_url();         // URL d'ajout au panier
```

---

## 9. TEMPLATE DE PAGE PERSONNALISÉ

```php
<?php
/**
 * Template Name: Pleine largeur
 * Description: Page sans sidebar.
 */
get_header();
?>

<div class="container">
    <!-- contenu -->
</div>

<?php get_footer(); ?>
```

Le commentaire `Template Name:` permet à WordPress de le détecter.
L'utilisateur le choisit dans l'éditeur de page → "Modèle de page".

---

## 10. FONCTIONS WORDPRESS UTILES

### Sécurité / Escape

```php
esc_html($text);      // Échapper du HTML
esc_attr($text);      // Échapper un attribut HTML
esc_url($url);        // Échapper une URL
wp_kses_post($html);  // Autoriser le HTML sûr (pour le contenu)
```

### Traduction (i18n)

```php
__('Texte', 'mon-theme');       // Retourne la traduction
esc_html__('Texte', 'mon-theme'); // Retourne + escape
esc_attr__('Texte', 'mon-theme'); // Retourne + escape pour attribut
_e('Texte', 'mon-theme');       // Affiche directement (echo)
```

### URL et chemins

```php
get_template_directory();        // Chemin absolu du thème (serveur)
get_template_directory_uri();    // URL du thème
get_stylesheet_uri();            // URL du style.css
home_url('/');                   // URL de la page d'accueil
admin_url();                     // URL du back-office
```

### Menus

```php
// Afficher un menu enregistré
wp_nav_menu([
    'theme_location' => 'primary',
    'container'      => false,     // Pas de div autour
    'menu_class'     => 'nav-menu',
    'fallback_cb'    => false,     // Ne rien afficher si pas de menu
    'depth'          => 1,         // Un seul niveau
]);
```

### Sidebars / Widgets

```php
// Vérifier si une sidebar a des widgets
is_active_sidebar('sidebar-1');

// Afficher les widgets d'une sidebar
dynamic_sidebar('sidebar-1');
```

---

## 11. CUSTOMIZER (PERSONNALISATEUR)

```php
function mon_theme_customizer($wp_customize): void {
    // Ajouter une section
    $wp_customize->add_section('mon_theme_options', [
        'title'    => __('Options du thème', 'mon-theme'),
        'priority' => 30,
    ]);

    // Ajouter un setting (la valeur stockée)
    $wp_customize->add_setting('mon_theme_promo_text', [
        'default'           => 'Texte par défaut',
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    // Ajouter un contrôle (l'interface dans le customizer)
    $wp_customize->add_control('mon_theme_promo_text', [
        'label'   => __('Texte promo', 'mon-theme'),
        'section' => 'mon_theme_options',
        'type'    => 'text',  // text, textarea, select, color, etc.
    ]);
}
add_action('customize_register', 'mon_theme_customizer');

// Récupérer la valeur dans un template :
// get_theme_mod('mon_theme_promo_text', 'Valeur par défaut');
```

---

## 12. CONVENTIONS IMPORTANTES

- **Toujours** appeler `wp_head()` dans `<head>` et `wp_footer()` avant `</body>`
- **Toujours** utiliser `wp_reset_postdata()` après une `WP_Query` custom
- **Toujours** échapper les sorties : `esc_html()`, `esc_url()`, `esc_attr()`
- Le fichier `style.css` est **obligatoire** même si tout le CSS est ailleurs
- Le fichier `index.php` est **obligatoire** (template de dernier recours)
- Le text domain (ex: `'mon-theme'`) doit être le même partout
- Les templates WooCommerce se surchargent dans `mon-theme/woocommerce/`
- Les noms de fonction doivent être préfixés pour éviter les conflits (ex: `themeesgi_setup`)
