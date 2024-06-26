<?php
/*
Template Name: Test page 
*/
get_header();
global $wpdb;


$sql_test = "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_type = 'product'";

$results = $wpdb->get_results( $sql_test );

if ( $results ) {
    foreach ( $results as $post ) {
       
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $post_type = $post->post_type;
        echo "Post ID: $post_id, Title: $post_title, Type: $post_type <br>";
    }
} else {
    echo "No posts found";
}

get_footer();
?>
