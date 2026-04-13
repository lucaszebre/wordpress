<?php
defined( 'ABSPATH' ) || exit;

class ST_Rest_API {

	private const NS = 'store-temoignages/v1';

	public static function register(): void {
		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
	}

	public static function register_routes(): void {
		register_rest_route( self::NS, '/temoignages', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ __CLASS__, 'get_list' ],
				'permission_callback' => '__return_true',
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ __CLASS__, 'create' ],
				'permission_callback' => '__return_true',
			],
		] );

		register_rest_route( self::NS, '/temoignages/stats', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_stats' ],
			'permission_callback' => '__return_true',
		] );

		register_rest_route( self::NS, '/temoignages/(?P<id>[\d]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ __CLASS__, 'get_single' ],
			'permission_callback' => '__return_true',
		] );
	}

	public static function get_list( WP_REST_Request $req ): WP_REST_Response {
		$per_page  = (int) ( $req->get_param( 'per_page' ) ?: 10 );
		$page      = (int) ( $req->get_param( 'page' ) ?: 1 );
		$note_min  = (int) ( $req->get_param( 'note_min' ) ?: 0 );
		$categorie = sanitize_text_field( $req->get_param( 'categorie' ) ?: '' );

		$args = [
			'post_type'      => 'temoignage',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
		];

		if ( $note_min > 1 ) {
			$args['meta_query'] = [ [ 'key' => '_st_note', 'value' => $note_min, 'compare' => '>=', 'type' => 'NUMERIC' ] ];
		}
		if ( $categorie ) {
			$args['tax_query'] = [ [ 'taxonomy' => 'categorie_temoignage', 'field' => 'slug', 'terms' => $categorie ] ];
		}

		$query = new WP_Query( $args );
		$data  = array_map( [ __CLASS__, 'format' ], $query->posts );

		$response = new WP_REST_Response( $data, 200 );
		$response->header( 'X-WP-Total',      (string) $query->found_posts );
		$response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );
		return $response;
	}

	public static function get_stats(): WP_REST_Response {
		global $wpdb;

		$total = (int) wp_count_posts( 'temoignage' )->publish;

		$avg = $wpdb->get_var( $wpdb->prepare(
			"SELECT AVG(CAST(pm.meta_value AS DECIMAL(3,1)))
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			 WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status = 'publish'",
			'_st_note', 'temoignage'
		) );

		$repartition = [];
		for ( $i = 1; $i <= 5; $i++ ) {
			$repartition[ $i ] = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_type = %s AND p.post_status = 'publish'",
				'_st_note', (string) $i, 'temoignage'
			) );
		}

		return new WP_REST_Response( [
			'total'        => $total,
			'note_moyenne' => $avg ? round( (float) $avg, 2 ) : null,
			'repartition'  => $repartition,
		], 200 );
	}

	public static function get_single( WP_REST_Request $req ): WP_REST_Response|WP_Error {
		$post = get_post( (int) $req->get_param( 'id' ) );
		if ( ! $post || 'temoignage' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_Error( 'not_found', __( 'Témoignage introuvable.', 'store-temoignages' ), [ 'status' => 404 ] );
		}
		return new WP_REST_Response( self::format( $post ), 200 );
	}

	public static function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
		$titre   = sanitize_text_field( $req->get_param( 'titre' ) ?? '' );
		$contenu = sanitize_textarea_field( $req->get_param( 'contenu' ) ?? '' );

		if ( ! $titre || ! $contenu ) {
			return new WP_Error( 'missing_fields', __( 'Titre et contenu requis.', 'store-temoignages' ), [ 'status' => 400 ] );
		}

		$id = wp_insert_post( [
			'post_title'   => $titre,
			'post_content' => $contenu,
			'post_type'    => 'temoignage',
			'post_status'  => 'pending',
		], true );

		if ( is_wp_error( $id ) ) {
			return new WP_Error( 'insert_failed', $id->get_error_message(), [ 'status' => 500 ] );
		}

		$note   = min( 5, max( 1, (int) ( $req->get_param( 'note' ) ?: 5 ) ) );
		$auteur = sanitize_text_field( $req->get_param( 'auteur' ) ?: '' );
		$ville  = sanitize_text_field( $req->get_param( 'ville' ) ?: '' );

		update_post_meta( $id, '_st_note',    $note );
		update_post_meta( $id, '_st_auteur',  $auteur );
		update_post_meta( $id, '_st_ville',   $ville );
		update_post_meta( $id, '_st_verifie', '0' );

		return new WP_REST_Response( [
			'message' => __( 'Témoignage soumis, en attente de modération.', 'store-temoignages' ),
			'id'      => $id,
		], 201 );
	}

	private static function format( WP_Post $post ): array {
		$cats = wp_get_post_terms( $post->ID, 'categorie_temoignage', [ 'fields' => 'names' ] );
		return [
			'id'         => $post->ID,
			'titre'      => get_the_title( $post ),
			'contenu'    => wp_trim_words( $post->post_content, 30 ),
			'auteur'     => get_post_meta( $post->ID, '_st_auteur', true ) ?: null,
			'ville'      => get_post_meta( $post->ID, '_st_ville', true ) ?: null,
			'note'       => (int) get_post_meta( $post->ID, '_st_note', true ) ?: null,
			'verifie'    => '1' === get_post_meta( $post->ID, '_st_verifie', true ),
			'categories' => is_wp_error( $cats ) ? [] : $cats,
			'date'       => get_the_date( 'c', $post ),
			'lien'       => get_permalink( $post ),
		];
	}
}
