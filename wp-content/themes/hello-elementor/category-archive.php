<?php get_header(); ?>

<div class="category-archive">
    <h1><?php single_cat_title(); ?></h1>
    <?php if (have_posts()) : ?>
        <div class="posts-list">
            <?php while (have_posts()) : the_post(); ?>
                <div class="post-item">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p>No posts found in this category.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
