<?php
// archive.php
// Affiche les archives (catégories, tags, dates...)
get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <!-- Titre de l'archive -->
            <div class="archive-header">
                <?php the_archive_title('<h1>', '</h1>'); ?>
                <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
            </div>

            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article <?php post_class('post-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('themesgi-post-card'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-card-body">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <div class="post-meta">
                                    <span><?php echo get_the_date(); ?></span>
                                </div>
                                <div class="post-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size'  => 2,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <?php get_template_part('template-parts/content', 'none'); ?>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</div>

<?php
get_footer();
