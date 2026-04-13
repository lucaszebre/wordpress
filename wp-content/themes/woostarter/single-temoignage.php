<?php
// single-temoignage.php
// Affiche un témoignage individuel
get_header();
?>

<div class="container">
    <div class="content-area full-width">
        <div class="main-content">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php
                $note    = (int) get_post_meta( get_the_ID(), '_st_note', true );
                $auteur  = get_post_meta( get_the_ID(), '_st_auteur', true ) ?: __( 'Anonyme', 'themesgi' );
                $ville   = get_post_meta( get_the_ID(), '_st_ville', true );
                $verifie = get_post_meta( get_the_ID(), '_st_verifie', true );
                $produit_id = (int) get_post_meta( get_the_ID(), '_st_produit_id', true );
                ?>

                <article class="st-card st-single" itemscope itemtype="https://schema.org/Review">

                    <?php if ( $note ) : ?>
                        <div class="st-stars" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                            <meta itemprop="ratingValue" content="<?php echo esc_attr( $note ); ?>" />
                            <meta itemprop="bestRating" content="5" />
                            <?php
                            echo str_repeat( '<span class="st-star st-star--on">★</span>', $note );
                            echo str_repeat( '<span class="st-star st-star--off">★</span>', 5 - $note );
                            ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="st-titre" itemprop="name"><?php the_title(); ?></h1>

                    <blockquote class="st-contenu" itemprop="reviewBody">
                        <?php the_content(); ?>
                    </blockquote>

                    <footer class="st-meta" itemprop="author" itemscope itemtype="https://schema.org/Person">
                        <strong class="st-auteur" itemprop="name"><?php echo esc_html( $auteur ); ?></strong>
                        <?php if ( $ville ) : ?>
                            <span class="st-ville">· <?php echo esc_html( $ville ); ?></span>
                        <?php endif; ?>
                        <?php if ( $verifie ) : ?>
                            <span class="st-verifie">✔ <?php esc_html_e( 'Achat vérifié', 'themesgi' ); ?></span>
                        <?php endif; ?>
                    </footer>

                    <?php if ( $produit_id && function_exists( 'wc_get_product' ) ) :
                        $produit = wc_get_product( $produit_id );
                        if ( $produit ) : ?>
                            <div class="st-produit-lie">
                                <span><?php esc_html_e( 'Produit concerné :', 'themesgi' ); ?></span>
                                <a href="<?php echo esc_url( get_permalink( $produit_id ) ); ?>">
                                    <?php echo esc_html( $produit->get_name() ); ?>
                                </a>
                            </div>
                        <?php endif;
                    endif; ?>

                </article>

                <div style="margin-top: 2rem;">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'temoignage' ) ); ?>" class="btn btn-outline">
                        &laquo; <?php esc_html_e( 'Tous les témoignages', 'themesgi' ); ?>
                    </a>
                </div>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
