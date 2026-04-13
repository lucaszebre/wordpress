<?php
defined( 'ABSPATH' ) || exit;

class ST_Admin {

	private const OPTION_KEY = 'st_settings';
	private const MENU_SLUG  = 'store-temoignages';

	public static function register(): void {
		add_action( 'admin_menu',            [ __CLASS__, 'add_menu' ] );
		add_action( 'admin_init',            [ __CLASS__, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
		add_filter( 'plugin_action_links_store-temoignages/store-temoignages.php', [ __CLASS__, 'action_links' ] );
	}

	public static function add_menu(): void {
		add_options_page(
			__( 'Témoignages Store', 'store-temoignages' ),
			__( 'Témoignages', 'store-temoignages' ),
			'manage_options',
			self::MENU_SLUG,
			[ __CLASS__, 'render_page' ]
		);
	}

	public static function register_settings(): void {
		register_setting( self::MENU_SLUG, self::OPTION_KEY, [ 'sanitize_callback' => [ __CLASS__, 'sanitize_settings' ] ] );

		add_settings_section( 'st_affichage', __( 'Paramètres d\'affichage', 'store-temoignages' ), '__return_empty_string', self::MENU_SLUG );
		add_settings_section( 'st_woo',       __( 'Intégration WooCommerce', 'store-temoignages' ), '__return_empty_string', self::MENU_SLUG );

		add_settings_field( 'nb_affichage',    __( 'Nombre de témoignages', 'store-temoignages' ),    [ __CLASS__, 'field_nb' ],       self::MENU_SLUG, 'st_affichage' );
		add_settings_field( 'afficher_note',   __( 'Afficher les étoiles', 'store-temoignages' ),     [ __CLASS__, 'field_note' ],     self::MENU_SLUG, 'st_affichage' );
		add_settings_field( 'couleur_etoile',  __( 'Couleur des étoiles', 'store-temoignages' ),      [ __CLASS__, 'field_couleur' ],  self::MENU_SLUG, 'st_affichage' );
		add_settings_field( 'position_produit', __( 'Position sur fiche produit', 'store-temoignages' ), [ __CLASS__, 'field_position' ], self::MENU_SLUG, 'st_woo' );
	}

	public static function field_nb(): void {
		$v = (int) ( self::get()['nb_affichage'] ?? 6 );
		echo '<input type="number" min="1" max="50" name="' . esc_attr( self::OPTION_KEY ) . '[nb_affichage]" value="' . esc_attr( $v ) . '" class="small-text" />';
	}

	public static function field_note(): void {
		$v = self::get()['afficher_note'] ?? '1';
		echo '<label><input type="checkbox" name="' . esc_attr( self::OPTION_KEY ) . '[afficher_note]" value="1" ' . checked( $v, '1', false ) . ' /> ' . esc_html__( 'Oui', 'store-temoignages' ) . '</label>';
	}

	public static function field_couleur(): void {
		$v = self::get()['couleur_etoile'] ?? '#f5a623';
		echo '<input type="color" name="' . esc_attr( self::OPTION_KEY ) . '[couleur_etoile]" value="' . esc_attr( $v ) . '" />';
	}

	public static function field_position(): void {
		$v = self::get()['position_produit'] ?? 'woocommerce_after_single_product_summary';
		$options = [
			'woocommerce_before_single_product_summary' => __( 'Avant le résumé produit', 'store-temoignages' ),
			'woocommerce_after_single_product_summary'  => __( 'Après le résumé produit (recommandé)', 'store-temoignages' ),
			'woocommerce_after_single_product'          => __( 'Après le produit entier', 'store-temoignages' ),
		];
		echo '<select name="' . esc_attr( self::OPTION_KEY ) . '[position_produit]">';
		foreach ( $options as $hook => $label ) {
			echo '<option value="' . esc_attr( $hook ) . '" ' . selected( $v, $hook, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	public static function sanitize_settings( array $input ): array {
		return [
			'nb_affichage'     => min( 50, max( 1, (int) ( $input['nb_affichage'] ?? 6 ) ) ),
			'afficher_note'    => ! empty( $input['afficher_note'] ) ? '1' : '0',
			'couleur_etoile'   => sanitize_hex_color( $input['couleur_etoile'] ?? '#f5a623' ) ?: '#f5a623',
			'position_produit' => in_array( $input['position_produit'] ?? '', [
				'woocommerce_before_single_product_summary',
				'woocommerce_after_single_product_summary',
				'woocommerce_after_single_product',
			], true ) ? $input['position_produit'] : 'woocommerce_after_single_product_summary',
		];
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'store-temoignages' ) );
		}
		$nb_total = (int) wp_count_posts( 'temoignage' )->publish;
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="notice notice-info inline" style="padding:10px 16px;margin-bottom:20px">
				<strong><?php esc_html_e( 'Statistiques :', 'store-temoignages' ); ?></strong>
				<?php printf( esc_html( _n( '%d témoignage publié', '%d témoignages publiés', $nb_total, 'store-temoignages' ) ), $nb_total ); ?>
				&nbsp;— <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=temoignage' ) ); ?>"><?php esc_html_e( 'Gérer →', 'store-temoignages' ); ?></a>
			</div>

			<form method="post" action="options.php">
				<?php settings_fields( self::MENU_SLUG ); do_settings_sections( self::MENU_SLUG ); submit_button(); ?>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Shortcodes disponibles', 'store-temoignages' ); ?></h2>
			<table class="widefat striped" style="max-width:680px">
				<tbody>
					<tr><td><code>[temoignages]</code></td><td><?php esc_html_e( 'Grille de tous les témoignages', 'store-temoignages' ); ?></td></tr>
					<tr><td><code>[temoignages limit="3" categorie="livraison" note_min="4"]</code></td><td><?php esc_html_e( 'Avec filtres', 'store-temoignages' ); ?></td></tr>
					<tr><td><code>[temoignages layout="liste"]</code></td><td><?php esc_html_e( 'Affichage en liste', 'store-temoignages' ); ?></td></tr>
					<tr><td><code>[temoignage_note_moyenne]</code></td><td><?php esc_html_e( 'Note moyenne globale', 'store-temoignages' ); ?></td></tr>
					<tr><td><code>[temoignage_note_moyenne produit_id="42"]</code></td><td><?php esc_html_e( 'Note moyenne d\'un produit', 'store-temoignages' ); ?></td></tr>
				</tbody>
			</table>

			<h2 style="margin-top:24px"><?php esc_html_e( 'API REST', 'store-temoignages' ); ?></h2>
			<p><code><?php echo esc_html( rest_url( 'store-temoignages/v1/temoignages' ) ); ?></code></p>
			<p><code><?php echo esc_html( rest_url( 'store-temoignages/v1/temoignages/stats' ) ); ?></code></p>
		</div>
		<?php
	}

	public static function enqueue_admin_assets( string $hook ): void {
		if ( 'settings_page_store-temoignages' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'store-temoignages-admin', ST_PLUGIN_URL . 'assets/css/admin.css', [], ST_VERSION );
	}

	public static function action_links( array $links ): array {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=' . self::MENU_SLUG ) ) . '">' . esc_html__( 'Réglages', 'store-temoignages' ) . '</a>' );
		return $links;
	}

	private static function get(): array {
		return (array) get_option( self::OPTION_KEY, [] );
	}
}
