<?php
// page.php
// Modèle par défaut pour les pages (avec sidebar)
get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <div class="page-header">
                        <h1><?php the_title(); ?></h1>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
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

        <?php get_sidebar(); ?>
    </div>
</div>

<?php
get_footer();
