<?php
// single.php
// Affiche un article seul
get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <!-- En-tête de l'article -->
                    <div class="post-header">
                        <h1><?php the_title(); ?></h1>
                        <div class="post-meta">
                            <span><?php echo get_the_date(); ?></span>
                            <span><?php the_author_posts_link(); ?></span>
                            <?php if (has_category()) : ?>
                                <span><?php the_category(', '); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Image mise en avant -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('themesgi-hero'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Contenu de l'article -->
                    <div class="post-content">
                        <?php the_content(); ?>

                        <?php
                        // Si l'article est paginé avec <!--nextpage-->
                        wp_link_pages([
                            'before' => '<div class="page-links">' . __('Pages :', 'themesgi'),
                            'after'  => '</div>',
                        ]);
                        ?>
                    </div>

                    <!-- Navigation entre les articles -->
                    <?php
                    the_post_navigation([
                        'prev_text' => '&laquo; %title',
                        'next_text' => '%title &raquo;',
                    ]);
                    ?>
                </article>

                <!-- Commentaires -->
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
