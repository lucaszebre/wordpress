<?php
defined( 'ABSPATH' ) || exit;

class ST_Shortcodes {

	public static function register(): void {
		add_shortcode( 'temoignages', [ __CLASS__, 'render_temoignages' ] );
		add_shortcode( 'temoignage_note_moyenne', [ __CLASS__, 'render_note_moyenne' ] );
	}

	public static function render_temoignages( array $atts ): string {
		$settings = get_option( 'st_settings', [] );

		$atts = shortcode_atts( [
			'limit'      => $settings['nb_affichage'] ?? 6,
			'categorie'  => '',
			'note_min'   => 1,
			'layout'     => 'grid',
			'produit_id' => 0,
		], $atts, 'temoignages' );

		$args = [
			'post_type'      => 'temoignage',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $atts['limit'],
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ( ! empty( $atts['categorie'] ) ) {
			$args['tax_query'] = [ [
				'taxonomy' => 'categorie_temoignage',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['categorie'] ),
			] ];
		}

		$note_min = (int) $atts['note_min'];
		if ( $note_min > 1 ) {
			$args['meta_query'] = [ [
				'key'     => '_st_note',
				'value'   => $note_min,
				'compare' => '>=',
				'type'    => 'NUMERIC',
			] ];
		}

		$produit_id = (int) $atts['produit_id'];
		if ( $produit_id > 0 ) {
			$cond = [
				'key'     => '_st_produit_id',
				'value'   => $produit_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			];
			$args['meta_query']   = $args['meta_query'] ?? [];
			$args['meta_query'][] = $cond;
		}

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return '<p class="st-empty">' . esc_html__( 'Aucun témoignage pour le moment.', 'store-temoignages' ) . '</p>';
		}

		wp_enqueue_style( 'store-temoignages' );
		wp_enqueue_script( 'store-temoignages' );

		$layout_class = ( 'liste' === $atts['layout'] ) ? 'st-grid--liste' : 'st-grid--grid';
		$show_rating  = ! empty( ( get_option( 'st_settings', [] ) )['afficher_note'] );

		ob_start();
		?>
		<section class="st-temoignages-section">
			<div class="st-grid <?php echo esc_attr( $layout_class ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php
					$pid     = get_the_ID();
					$note    = (int) get_post_meta( $pid, '_st_note', true );
					$auteur  = get_post_meta( $pid, '_st_auteur', true ) ?: __( 'Anonyme', 'store-temoignages' );
					$ville   = get_post_meta( $pid, '_st_ville', true );
					$verifie = get_post_meta( $pid, '_st_verifie', true );
					?>
					<article class="st-card" itemscope itemtype="https://schema.org/Review">
						<?php if ( $show_rating && $note ) : ?>
							<div class="st-stars" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
								<meta itemprop="ratingValue" content="<?php echo esc_attr( $note ); ?>" />
								<meta itemprop="bestRating" content="5" />
								<?php
								echo str_repeat( '<span class="st-star st-star--on">★</span>', $note );
								echo str_repeat( '<span class="st-star st-star--off">★</span>', 5 - $note );
								?>
							</div>
						<?php endif; ?>

						<blockquote class="st-contenu" itemprop="reviewBody">
							<?php echo wp_kses_post( get_the_excerpt() ?: get_the_content() ); ?>
						</blockquote>

						<footer class="st-meta" itemprop="author" itemscope itemtype="https://schema.org/Person">
							<strong class="st-auteur" itemprop="name"><?php echo esc_html( $auteur ); ?></strong>
							<?php if ( $ville ) : ?>
								<span class="st-ville">· <?php echo esc_html( $ville ); ?></span>
							<?php endif; ?>
							<?php if ( $verifie ) : ?>
								<span class="st-verifie">✔ <?php esc_html_e( 'Vérifié', 'store-temoignages' ); ?></span>
							<?php endif; ?>
						</footer>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	public static function render_note_moyenne( array $atts ): string {
		$atts = shortcode_atts( [ 'produit_id' => 0 ], $atts, 'temoignage_note_moyenne' );

		$meta_query = [ [ 'key' => '_st_note', 'compare' => 'EXISTS' ] ];
		if ( (int) $atts['produit_id'] > 0 ) {
			$meta_query = [ [
				'key'     => '_st_produit_id',
				'value'   => (int) $atts['produit_id'],
				'compare' => '=',
				'type'    => 'NUMERIC',
			] ];
		}

		$posts = get_posts( [
			'post_type'      => 'temoignage',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => $meta_query,
		] );

		if ( empty( $posts ) ) {
			return '';
		}

		$total = 0;
		foreach ( $posts as $id ) {
			$total += (int) get_post_meta( $id, '_st_note', true );
		}

		$nb      = count( $posts );
		$moyenne = round( $total / $nb, 1 );

		wp_enqueue_style( 'store-temoignages' );

		ob_start();
		?>
		<div class="st-note-moyenne" itemscope itemtype="https://schema.org/AggregateRating">
			<meta itemprop="ratingValue" content="<?php echo esc_attr( $moyenne ); ?>" />
			<meta itemprop="reviewCount" content="<?php echo esc_attr( $nb ); ?>" />
			<span class="st-moyenne-chiffre"><?php echo esc_html( number_format( $moyenne, 1 ) ); ?></span>
			<span class="st-moyenne-etoiles">
				<?php
				$plein = (int) floor( $moyenne );
				$demi  = ( $moyenne - $plein ) >= 0.5 ? 1 : 0;
				$vide  = 5 - $plein - $demi;
				echo str_repeat( '<span class="st-star st-star--on">★</span>', $plein );
				if ( $demi ) {
					echo '<span class="st-star st-star--half">★</span>';
				}
				echo str_repeat( '<span class="st-star st-star--off">★</span>', $vide );
				?>
			</span>
			<span class="st-moyenne-nb">
				<?php printf(
					esc_html( _n( 'basé sur %d avis', 'basé sur %d avis', $nb, 'store-temoignages' ) ),
					(int) $nb
				); ?>
			</span>
		</div>
		<?php
		return ob_get_clean();
	}
}
