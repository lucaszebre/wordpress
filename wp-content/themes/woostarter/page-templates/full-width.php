<?php
/**
 * Template Name: Pleine largeur
 * Description: Modèle de page sans sidebar, pleine largeur.
 */
get_header();
?>

<div class="container">
    <div class="content-area full-width">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <div class="page-header">
                        <h1><?php the_title(); ?></h1>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('themesgi-hero'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="page-content">
                        <?php the_content(); ?>

                        <?php
                        wp_link_pages([
                            'before' => '<div class="page-links">' . __('Pages :', 'themesgi'),
                            'after'  => '</div>',
                        ]);
                        ?>
                    </div>
                </article>

                <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php
get_footer();
