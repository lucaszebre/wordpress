<?php
defined( 'ABSPATH' ) || exit;

class ST_Hooks {

	public static function register(): void {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		self::setup_woocommerce_hooks();
	}

	public static function setup_woocommerce_hooks(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$settings = get_option( 'st_settings', [] );
		$position = $settings['position_produit'] ?? 'woocommerce_after_single_product_summary';

		add_action( $position, [ __CLASS__, 'afficher_temoignages_produit' ], 15 );

		add_action( 'woocommerce_before_shop_loop_item_title', [ __CLASS__, 'badge_produit_populaire' ], 11 );

		add_filter( 'woocommerce_product_tabs', [ __CLASS__, 'ajouter_onglet_avis' ] );

		add_filter( 'woocommerce_sale_flash', [ __CLASS__, 'personnaliser_badge_promo' ], 10, 3 );

		add_filter( 'woocommerce_catalog_orderby',           [ __CLASS__, 'ajouter_tri_avis' ] );
		add_filter( 'woocommerce_get_catalog_ordering_args', [ __CLASS__, 'appliquer_tri_avis' ] );
		add_filter( 'woocommerce_default_catalog_orderby',   [ __CLASS__, 'tri_avis_par_defaut' ] );
	}

	public static function afficher_temoignages_produit(): void {
		global $product;
		if ( ! $product ) {
			return;
		}

		$settings    = get_option( 'st_settings', [] );
		$nb          = (int) ( $settings['nb_affichage'] ?? 3 );
		$show_rating = ! empty( $settings['afficher_note'] );

		$temoignages = get_posts( [
			'post_type'      => 'temoignage',
			'posts_per_page' => $nb,
			'post_status'    => 'publish',
			'meta_query'     => [ [
				'key'     => '_st_produit_id',
				'value'   => $product->get_id(),
				'compare' => '=',
				'type'    => 'NUMERIC',
			] ],
		] );

		if ( empty( $temoignages ) ) {
			return;
		}

		echo '<section class="st-temoignages-produit">';
		echo '<h3>' . esc_html__( 'Ce que nos clients disent', 'store-temoignages' ) . '</h3>';
		echo '<div class="st-grid st-grid--grid">';

		foreach ( $temoignages as $t ) {
			$note    = (int) get_post_meta( $t->ID, '_st_note', true );
			$auteur  = get_post_meta( $t->ID, '_st_auteur', true ) ?: __( 'Anonyme', 'store-temoignages' );
			$ville   = get_post_meta( $t->ID, '_st_ville', true );
			$verifie = get_post_meta( $t->ID, '_st_verifie', true );

			echo '<div class="st-card">';

			if ( $show_rating && $note ) {
				echo '<div class="st-stars">';
				echo str_repeat( '<span class="st-star st-star--on">★</span>', $note );
				echo str_repeat( '<span class="st-star st-star--off">★</span>', 5 - $note );
				echo '</div>';
			}

			echo '<blockquote class="st-contenu">' . wp_kses_post( get_the_excerpt( $t ) ?: wp_trim_words( $t->post_content, 30 ) ) . '</blockquote>';

			echo '<footer class="st-meta">';
			echo '<strong class="st-auteur">' . esc_html( $auteur ) . '</strong>';
			if ( $ville ) {
				echo ' <span class="st-ville">· ' . esc_html( $ville ) . '</span>';
			}
			if ( $verifie ) {
				echo ' <span class="st-verifie">✔ ' . esc_html__( 'Vérifié', 'store-temoignages' ) . '</span>';
			}
			echo '</footer>';
			echo '</div>';
		}

		echo '</div></section>';
	}

	public static function badge_produit_populaire(): void {
		global $product;
		if ( ! $product ) {
			return;
		}

		$nb_avis = get_comments( [
			'post_id'  => $product->get_id(),
			'status'   => 'approve',
			'count'    => true,
		] );

		if ( $nb_avis >= 1 ) {
			echo '<span class="st-badge-populaire">' . esc_html__( 'Populaire', 'store-temoignages' ) . '</span>';
		}
	}

	public static function ajouter_onglet_avis( array $tabs ): array {
		global $product;
		if ( ! $product ) {
			return $tabs;
		}

		$q = new WP_Query( [
			'post_type'      => 'temoignage',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => [ [
				'key'     => '_st_produit_id',
				'value'   => $product->get_id(),
				'compare' => '=',
				'type'    => 'NUMERIC',
			] ],
		] );

		if ( $q->found_posts > 0 ) {
			$tabs['st_avis_clients'] = [
				'title'    => sprintf(
					_n( 'Avis client (%d)', 'Avis clients (%d)', $q->found_posts, 'store-temoignages' ),
					$q->found_posts
				),
				'priority' => 50,
				'callback' => [ __CLASS__, 'afficher_temoignages_produit' ],
			];
		}

		return $tabs;
	}

	public static function personnaliser_badge_promo( string $html, WP_Post $post, WC_Product $product ): string {
		$economie = '';

		if ( $product->get_regular_price() && $product->get_sale_price() ) {
			$pourcentage = round( ( 1 - (float) $product->get_sale_price() / (float) $product->get_regular_price() ) * 100 );
			if ( $pourcentage > 0 ) {
				$economie = ' -' . $pourcentage . '%';
			}
		}

		return '<span class="onsale st-onsale">' . esc_html__( 'Promo', 'store-temoignages' ) . esc_html( $economie ) . '</span>';
	}

	public static function tri_avis_par_defaut( string $default ): string {
		return 'popularity_reviews';
	}

	public static function ajouter_tri_avis( array $options ): array {
		unset( $options['popularity'] );
		$options['popularity_reviews'] = __( 'Tri par popularité', 'store-temoignages' );
		return $options;
	}

	public static function appliquer_tri_avis( array $args ): array {
		$orderby = sanitize_key( $_GET['orderby'] ?? 'popularity_reviews' );
		if ( 'popularity_reviews' === $orderby ) {
			$args['meta_key'] = '_wc_review_count';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
		}
		return $args;
	}

	public static function enqueue_assets(): void {
		wp_register_style(
			'store-temoignages',
			ST_PLUGIN_URL . 'assets/css/frontend.css',
			[ 'woocommerce-general' ],
			ST_VERSION
		);
		wp_register_script(
			'store-temoignages',
			ST_PLUGIN_URL . 'assets/js/frontend.js',
			[ 'jquery' ],
			ST_VERSION,
			true
		);

		if ( is_product() || is_shop() || is_product_category() ) {
			wp_enqueue_style( 'store-temoignages' );
			wp_enqueue_script( 'store-temoignages' );
		}
	}
}
