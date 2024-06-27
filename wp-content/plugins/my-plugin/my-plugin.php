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







//this is for shortcode 
// function my_shortcode_function(){
//     return '<h1>This is my shortcode</h1>';
// }

// add_shortcode('my-short','my_shortcode_function');