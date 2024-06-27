<?php 

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    header('location:/');
    die();
}


global $wpdb ,$table_prefix;
$wp_emp = $table_prefix.'emp';
$qdrop ="DROP TABLE `$wp_emp`;";
$wpdb->query($qdrop);