<?php 
/**
 * Plugin Name: My plugin 
 * Description:Tis is a test plugin.
 *  Version:1.0
 * Author: Akash
 */

if(!defined('ABSPATH')){
    header('Location:/');
    die('');
}


//this is for activationd plugin 
function my_plugin_activation(){
 global $wpdb, $table_prefix;
 $wp_emp = $table_prefix.'emp';
 $qu ="CREATE TABLE IF NOT EXISTS `$wp_emp` (`ID` INT(50) NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL , `email` VARCHAR(100) NOT NULL , `status` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;";
 $wpdb->query($qu);


//  $qu2="INSERT INTO `$wp_emp` (`name`, `email`, `status`) VALUES ('Akash', 'akash@gmail.com', 1);";

$data = array(
     'name' => 'Akash',
     'email' => 'akash@gmail.com',
    'status' => 1,
 );
 $wpdb->insert($wp_emp,$data);
}
register_activation_hook(__FILE__, 'my_plugin_activation');


//this is for deactivation plugin 
function my_plugin_deactivation(){
    global $wpdb, $table_prefix;
    $wp_emp = $table_prefix.'emp';

    $queryDeactive = "TRUNCATE `$wp_emp`";
    $wpdb->query($queryDeactive);
}
register_deactivation_hook(__FILE__, 'my_plugin_deactivation');





// this is for shortcode 
function my_shortcode_function(){
    return '<h1>This is my shortcode</h1>';
}

add_shortcode('my-short','my_shortcode_function');




//jquery function
function my_custom_scripts() {
    // Get the URL of the main.js file within your plugin
    $path = plugin_dir_url(__FILE__) . 'js/main.js';

    // Define jQuery as a dependency for your script
    $dependencies = array('jquery');

    // Get the file modification time to use as version (cache busting)
    $version = filemtime(plugin_dir_path(__FILE__) . 'js/main.js');

    // Enqueue the script with WordPress
    wp_enqueue_script('my-custom-js', $path, $dependencies, $version, true);

    wp_add_inline_script('my-custom-js','var is_login ='.is_user_logged_in().';',);
}
add_action('wp_enqueue_scripts', 'my_custom_scripts'); 
add_action('admin_enqueue_scripts', 'my_custom_scripts'); 



// How to use sql select query and  wp_query 

function getquery(){
    global $wpdb, $table_prefix;
    $wp_emp = $table_prefix.'emp';
    $query = "SELECT * FROM `$wp_emp`";
    $result= $wpdb->get_results($query);
    print_r($result);

}
add_shortcode('databasequery','getquery');