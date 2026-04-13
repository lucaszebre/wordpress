<?php
defined( 'ABSPATH' ) || exit;

class ST_Post_Types {

	public static function register(): void {
		add_action( 'init', [ __CLASS__, 'register_cpt_temoignage' ] );
		add_action( 'init', [ __CLASS__, 'register_taxonomy_categorie' ] );
		add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );
		add_action( 'save_post_temoignage', [ __CLASS__, 'save_meta_boxes' ] );
		add_filter( 'manage_temoignage_posts_columns', [ __CLASS__, 'custom_columns' ] );
		add_action( 'manage_temoignage_posts_custom_column', [ __CLASS__, 'render_custom_columns' ], 10, 2 );
	}

	public static function register_cpt_temoignage(): void {
		$labels = [
			'name'               => _x( 'Témoignages', 'post type general name', 'store-temoignages' ),
			'singular_name'      => _x( 'Témoignage', 'post type singular name', 'store-temoignages' ),
			'menu_name'          => __( 'Témoignages', 'store-temoignages' ),
			'add_new'            => __( 'Ajouter', 'store-temoignages' ),
			'add_new_item'       => __( 'Ajouter un témoignage', 'store-temoignages' ),
			'edit_item'          => __( 'Modifier le témoignage', 'store-temoignages' ),
			'new_item'           => __( 'Nouveau témoignage', 'store-temoignages' ),
			'view_item'          => __( 'Voir le témoignage', 'store-temoignages' ),
			'search_items'       => __( 'Rechercher un témoignage', 'store-temoignages' ),
			'not_found'          => __( 'Aucun témoignage trouvé.', 'store-temoignages' ),
			'not_found_in_trash' => __( 'Aucun témoignage dans la corbeille.', 'store-temoignages' ),
		];

		register_post_type( 'temoignage', [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'temoignages' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-format-quote',
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
			'show_in_rest'       => true,
			'taxonomies'         => [ 'categorie_temoignage' ],
		] );
	}

	public static function register_taxonomy_categorie(): void {
		$labels = [
			'name'              => _x( 'Catégories', 'taxonomy general name', 'store-temoignages' ),
			'singular_name'     => _x( 'Catégorie', 'taxonomy singular name', 'store-temoignages' ),
			'search_items'      => __( 'Rechercher une catégorie', 'store-temoignages' ),
			'all_items'         => __( 'Toutes les catégories', 'store-temoignages' ),
			'parent_item'       => __( 'Catégorie parente', 'store-temoignages' ),
			'parent_item_colon' => __( 'Catégorie parente :', 'store-temoignages' ),
			'edit_item'         => __( 'Modifier la catégorie', 'store-temoignages' ),
			'update_item'       => __( 'Mettre à jour', 'store-temoignages' ),
			'add_new_item'      => __( 'Ajouter une catégorie', 'store-temoignages' ),
			'new_item_name'     => __( 'Nouvelle catégorie', 'store-temoignages' ),
			'menu_name'         => __( 'Catégories', 'store-temoignages' ),
		];

		register_taxonomy( 'categorie_temoignage', [ 'temoignage' ], [
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'categorie-temoignage' ],
			'show_in_rest'      => true,
		] );
	}

	public static function add_meta_boxes(): void {
		add_meta_box(
			'st_temoignage_details',
			__( 'Détails du témoignage', 'store-temoignages' ),
			[ __CLASS__, 'render_meta_box' ],
			'temoignage',
			'normal',
			'high'
		);
	}

	public static function render_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'st_save_meta', 'st_meta_nonce' );

		$note       = (int) get_post_meta( $post->ID, '_st_note', true );
		$auteur     = get_post_meta( $post->ID, '_st_auteur', true );
		$ville      = get_post_meta( $post->ID, '_st_ville', true );
		$produit_id = (int) get_post_meta( $post->ID, '_st_produit_id', true );
		$verifie    = get_post_meta( $post->ID, '_st_verifie', true );

		$produits = [];
		if ( class_exists( 'WooCommerce' ) ) {
			$produits = wc_get_products( [ 'limit' => -1, 'status' => 'publish' ] );
		}
		?>
		<table class="form-table">
			<tr>
				<th><label for="st_auteur"><?php esc_html_e( 'Nom du client', 'store-temoignages' ); ?></label></th>
				<td><input type="text" id="st_auteur" name="st_auteur" value="<?php echo esc_attr( $auteur ); ?>" class="regular-text" placeholder="Ex : Marie D." /></td>
			</tr>
			<tr>
				<th><label for="st_ville"><?php esc_html_e( 'Ville', 'store-temoignages' ); ?></label></th>
				<td><input type="text" id="st_ville" name="st_ville" value="<?php echo esc_attr( $ville ); ?>" class="regular-text" placeholder="Ex : Paris" /></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Note (1 à 5)', 'store-temoignages' ); ?></label></th>
				<td>
					<fieldset>
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<label style="margin-right:8px">
								<input type="radio" name="st_note" value="<?php echo $i; ?>" <?php checked( $note, $i ); ?> />
								<?php echo str_repeat( '★', $i ) . str_repeat( '☆', 5 - $i ); ?>
							</label>
						<?php endfor; ?>
					</fieldset>
				</td>
			</tr>
			<?php if ( ! empty( $produits ) ) : ?>
			<tr>
				<th><label for="st_produit_id"><?php esc_html_e( 'Produit lié', 'store-temoignages' ); ?></label></th>
				<td>
					<select id="st_produit_id" name="st_produit_id">
						<option value="0"><?php esc_html_e( '— Aucun produit —', 'store-temoignages' ); ?></option>
						<?php foreach ( $produits as $produit ) : ?>
							<option value="<?php echo esc_attr( $produit->get_id() ); ?>" <?php selected( $produit_id, $produit->get_id() ); ?>>
								<?php echo esc_html( $produit->get_name() ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th><?php esc_html_e( 'Achat vérifié', 'store-temoignages' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="st_verifie" value="1" <?php checked( $verifie, '1' ); ?> />
						<?php esc_html_e( 'Marquer comme achat vérifié', 'store-temoignages' ); ?>
					</label>
				</td>
			</tr>
		</table>
		<?php
	}

	public static function save_meta_boxes( int $post_id ): void {
		if (
			! isset( $_POST['st_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['st_meta_nonce'] ) ), 'st_save_meta' )
		) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['st_note'] ) ) {
			update_post_meta( $post_id, '_st_note', min( 5, max( 1, (int) $_POST['st_note'] ) ) );
		}
		if ( isset( $_POST['st_auteur'] ) ) {
			update_post_meta( $post_id, '_st_auteur', sanitize_text_field( wp_unslash( $_POST['st_auteur'] ) ) );
		}
		if ( isset( $_POST['st_ville'] ) ) {
			update_post_meta( $post_id, '_st_ville', sanitize_text_field( wp_unslash( $_POST['st_ville'] ) ) );
		}
		if ( isset( $_POST['st_produit_id'] ) ) {
			update_post_meta( $post_id, '_st_produit_id', (int) $_POST['st_produit_id'] );
		}
		update_post_meta( $post_id, '_st_verifie', isset( $_POST['st_verifie'] ) ? '1' : '0' );
	}

	public static function custom_columns( array $columns ): array {
		$new = [];
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['st_auteur']  = __( 'Client', 'store-temoignages' );
				$new['st_note']    = __( 'Note', 'store-temoignages' );
				$new['st_verifie'] = __( 'Vérifié', 'store-temoignages' );
			}
		}
		return $new;
	}

	public static function render_custom_columns( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'st_auteur':
				$auteur = get_post_meta( $post_id, '_st_auteur', true );
				$ville  = get_post_meta( $post_id, '_st_ville', true );
				echo esc_html( $auteur ?: '—' );
				if ( $ville ) {
					echo ' <em style="color:#888">(' . esc_html( $ville ) . ')</em>';
				}
				break;
			case 'st_note':
				$note = (int) get_post_meta( $post_id, '_st_note', true );
				echo '<span style="color:#f5a623">' . str_repeat( '★', $note ) . '</span>';
				echo '<span style="color:#ddd">' . str_repeat( '★', 5 - $note ) . '</span>';
				break;
			case 'st_verifie':
				$v = get_post_meta( $post_id, '_st_verifie', true );
				echo $v ? '<span style="color:green">✔</span>' : '<span style="color:#ccc">—</span>';
				break;
		}
	}
}
