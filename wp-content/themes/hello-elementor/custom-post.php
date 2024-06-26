<?php
/* Template Name: Custom post page */
get_header();

// Get category ID from URL parameter
$category_id = isset($_GET['category_id']) ? absint($_GET['category_id']) : 0;

// Get all parent categories where taxonomy is 'category'
$parent_categories = get_categories(array(
    'taxonomy' => 'category',
    'parent' => 0,
    'hide_empty' => false,
));

// Get current page
$paged = max(1, get_query_var('paged'));

// Recursive function to get child categories
function get_child_categories($parent_id)
{
    global $wpdb;

    $child_categories = $wpdb->get_results($wpdb->prepare(
        "SELECT term.term_id 
        FROM {$wpdb->terms} AS term
        INNER JOIN {$wpdb->term_taxonomy} AS tax 
        ON term.term_id = tax.term_id 
        WHERE tax.parent = %d 
        AND tax.taxonomy = 'category'",
        $parent_id
    ), ARRAY_A);

    $categories = array();
    foreach ($child_categories as $child_category) {
        $categories[] = $child_category['term_id'];
        $categories = array_merge($categories, get_child_categories($child_category['term_id']));
    }

    return $categories;
}

// Get category and its subcategories
$categories = array($category_id);
$categories = array_merge($categories, get_child_categories($category_id));

// Construct WP_Query args
$query_args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'paged' => $paged,
    'posts_per_page' => 2,
    'category__in' => $categories,
    'order' => 'ASC'
);

// Execute the query
$custom_query = new WP_Query($query_args);
?>

<div id="primary" class="content-area">
    <div id="main" class="site-main" role="main">

        <!-- Dropdown to select categories -->
        <form id="category-filter" action="<?php echo esc_url(get_permalink()); ?>" method="get">
            <input type="hidden" name="page_id" value="<?php echo esc_attr(get_the_ID()); ?>">
            <select name="category_id" id="category_id">
                <option value="0">Select Category</option>
                <?php foreach ($parent_categories as $parent_cat) : ?>
                    <option value="<?php echo esc_attr($parent_cat->term_id); ?>" <?php selected($category_id, $parent_cat->term_id); ?>><?php echo esc_html($parent_cat->name); ?></option>
                    <?php
                    $child_cats = get_categories(array(
                        'taxonomy' => 'category',
                        'parent' => $parent_cat->term_id,
                        'hide_empty' => false,
                    ));
                    foreach ($child_cats as $child_cat) : ?>
                        <option value="<?php echo esc_attr($child_cat->term_id); ?>" <?php selected($category_id, $child_cat->term_id); ?>>-- <?php echo esc_html($child_cat->name); ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Display posts -->
        <div id="ajax-posts-container">
            <?php if ($custom_query->have_posts()) : ?>
                <div class="main-aja">
                    <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <div class="entry-header">
                                <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            </div>
                            <div class="image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail(); ?>
                                <?php endif; ?>
                            </div>
                            <div class="entry-content">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="container-pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $custom_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => __('← Previous', 'textdomain'),
                        'next_text' => __('Next →', 'textdomain'),
                        'type' => 'list',
                    ));
                    ?>
                </div>


            <?php else : ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>

        <?php wp_reset_postdata(); ?>

    </div>
</div>

<?php get_footer(); ?>