<?php
// archive-temoignage.php
// Archive de tous les témoignages
get_header();
?>

<div class="container">
    <div class="content-area full-width">
        <div class="main-content">

            <div class="archive-header">
                <h1><?php esc_html_e( 'Témoignages clients', 'themesgi' ); ?></h1>
                <?php echo do_shortcode( '[temoignage_note_moyenne]' ); ?>
            </div>

            <?php
            // Filtre par catégorie si présente dans l'URL
            $cat_slug = get_query_var( 'categorie_temoignage' );
            $categories = get_terms( [ 'taxonomy' => 'categorie_temoignage', 'hide_empty' => true ] );
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
            ?>
            <div class="st-filtres">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'temoignage' ) ); ?>"
                   class="btn <?php echo empty( $cat_slug ) ? 'btn-primary' : 'btn-outline'; ?>">
                    <?php esc_html_e( 'Tous', 'themesgi' ); ?>
                </a>
                <?php foreach ( $categories as $cat ) : ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                       class="btn <?php echo ( $cat_slug === $cat->slug ) ? 'btn-primary' : 'btn-outline'; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ( have_posts() ) : ?>
                <div class="st-grid st-grid--grid">
                    <?php while ( have_posts() ) : the_post();
                        $note    = (int) get_post_meta( get_the_ID(), '_st_note', true );
                        $auteur  = get_post_meta( get_the_ID(), '_st_auteur', true ) ?: __( 'Anonyme', 'themesgi' );
                        $ville   = get_post_meta( get_the_ID(), '_st_ville', true );
                        $verifie = get_post_meta( get_the_ID(), '_st_verifie', true );
                    ?>
                        <?php
                        $contenu_complet = get_the_content();
                        $extrait         = wp_trim_words( $contenu_complet, 20 );
                        $est_tronque     = str_word_count( strip_tags( $contenu_complet ) ) > 20;
                        ?>
                        <article class="st-card" itemscope itemtype="https://schema.org/Review">

                            <?php if ( $note ) : ?>
                                <div class="st-stars">
                                    <?php
                                    echo str_repeat( '<span class="st-star st-star--on">★</span>', $note );
                                    echo str_repeat( '<span class="st-star st-star--off">★</span>', 5 - $note );
                                    ?>
                                </div>
                            <?php endif; ?>

                            <h3 class="st-titre-card" itemprop="name">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <blockquote class="st-contenu" itemprop="reviewBody">
                                <?php echo wp_kses_post( $extrait ); ?>
                                <?php if ( $est_tronque ) : ?>
                                    <span class="st-suite">…</span>
                                <?php endif; ?>
                            </blockquote>

                            <footer class="st-meta">
                                <strong class="st-auteur"><?php echo esc_html( $auteur ); ?></strong>
                                <?php if ( $ville ) : ?>
                                    <span class="st-ville">· <?php echo esc_html( $ville ); ?></span>
                                <?php endif; ?>
                                <?php if ( $verifie ) : ?>
                                    <span class="st-verifie">✔ <?php esc_html_e( 'Vérifié', 'themesgi' ); ?></span>
                                <?php endif; ?>
                            </footer>

                            <?php if ( $est_tronque ) : ?>
                                <a href="<?php the_permalink(); ?>" class="st-lire-plus">
                                    <?php esc_html_e( 'Lire la suite →', 'themesgi' ); ?>
                                </a>
                            <?php endif; ?>

                        </article>
                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php the_posts_pagination( [
                        'mid_size'  => 2,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ] ); ?>
                </div>

            <?php else : ?>
                <p><?php esc_html_e( 'Aucun témoignage pour le moment.', 'themesgi' ); ?></p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>
