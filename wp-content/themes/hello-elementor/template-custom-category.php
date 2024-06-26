<?php
/*
Template Name: Custom category
*/

get_header();
global $wpdb;

function generate_custom_permalink($category_id)
{
    $custom_page_id = 45;
    return add_query_arg('category_id', $category_id, get_permalink($custom_page_id));
}

function display_categories_recursive($parent_id)
{
    global $wpdb;
    $child_query = $wpdb->prepare(
        "SELECT * FROM $wpdb->term_taxonomy 
        INNER JOIN $wpdb->terms
        ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
        WHERE $wpdb->term_taxonomy.taxonomy = 'category' 
        AND $wpdb->term_taxonomy.parent = %d
        ORDER BY $wpdb->terms.name",
        $parent_id
    );

    $child_result = $wpdb->get_results($child_query);

    if ($child_result) {
        echo '<ul class="child">'; 
        foreach ($child_result as $child_category) {
            echo '<li>';
            echo '<a href="' . esc_url(generate_custom_permalink($child_category->term_id)) . '">';
            echo esc_html($child_category->name);
            echo '</a>';
            display_categories_recursive($child_category->term_id); 
            echo '</li>'; 
        }
        echo '</ul>';
    }
}

// Get Parent Categories Query
$parent_query = "SELECT * FROM $wpdb->term_taxonomy 
                 INNER JOIN $wpdb->terms
                 ON $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
                 WHERE $wpdb->term_taxonomy.taxonomy = 'category' 
                 AND $wpdb->term_taxonomy.parent = 0
                 ORDER BY $wpdb->terms.name";
$parent_result = $wpdb->get_results($parent_query);

?>

<!-- Parent Categories Loop -->
<?php if ($parent_result) : ?>
    <div class="category-list">
        <?php foreach ($parent_result as $parent_category) : ?>
            <div class="parent-category">
                <a href="<?= esc_url(generate_custom_permalink($parent_category->term_id)) ?>">
                    <?= esc_html($parent_category->name) ?>
                </a>
                <br>

                <!-- Child Categories Display -->
                <?php 
                display_categories_recursive($parent_category->term_id);
                ?>

            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>No Categories found.</p>
<?php endif; ?>

<?php get_footer(); ?>
