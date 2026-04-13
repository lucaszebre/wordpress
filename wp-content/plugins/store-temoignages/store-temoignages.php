<?php
/**
 * Plugin Name:       Store Témoignages
 * Plugin URI:        https://github.com/esgi/wordpress
 * Description:       Gestion des témoignages clients avec intégration WooCommerce. Inclut un CPT, une taxonomie, des hooks WooCommerce, un shortcode et une API REST.
 * Version:           1.0.0
 * Author:            Groupe ESGI
 * Author URI:        #
 * Text Domain:       store-temoignages
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'ST_VERSION',     '1.0.0' );
define( 'ST_PLUGIN_FILE', __FILE__ );
define( 'ST_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'ST_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

require_once ST_PLUGIN_DIR . 'includes/class-post-types.php';
require_once ST_PLUGIN_DIR . 'includes/class-hooks.php';
require_once ST_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once ST_PLUGIN_DIR . 'includes/class-admin.php';
require_once ST_PLUGIN_DIR . 'includes/class-rest-api.php';

final class Store_Temoignages {

	private static ?Store_Temoignages $instance = null;

	public static function get_instance(): Store_Temoignages {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
		register_activation_hook( ST_PLUGIN_FILE,   [ $this, 'activate' ] );
		register_deactivation_hook( ST_PLUGIN_FILE, [ $this, 'deactivate' ] );
	}

	public function init(): void {
		ST_Post_Types::register();
		ST_Hooks::register();
		ST_Shortcodes::register();
		ST_Admin::register();
		ST_Rest_API::register();
	}

	public function activate(): void {
		if ( false === get_option( 'st_settings' ) ) {
			add_option( 'st_settings', [
				'nb_affichage'     => 6,
				'position_produit' => 'woocommerce_after_single_product_summary',
				'afficher_note'    => '1',
				'couleur_etoile'   => '#f5a623',
			] );
		}
		flush_rewrite_rules();
	}

	public function deactivate(): void {
		flush_rewrite_rules();
	}
}

Store_Temoignages::get_instance();
