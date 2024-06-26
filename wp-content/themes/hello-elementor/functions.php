<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '3.0.2');

if (!isset($content_width)) {
	$content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup()
	{
		if (is_admin()) {
			hello_maybe_update_theme_version_in_db();
		}

		if (apply_filters('hello_elementor_register_menus', true)) {
			register_nav_menus(['menu-1' => esc_html__('Header', 'hello-elementor')]);
			register_nav_menus(['menu-2' => esc_html__('Footer', 'hello-elementor')]);
		}

		if (apply_filters('hello_elementor_post_type_support', true)) {
			add_post_type_support('page', 'excerpt');
		}

		if (apply_filters('hello_elementor_add_theme_support', true)) {
			add_theme_support('post-thumbnails');
			add_theme_support('automatic-feed-links');
			add_theme_support('title-tag');
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style('classic-editor.css');

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support('align-wide');

			/*
			 * WooCommerce.
			 */
			if (apply_filters('hello_elementor_add_woocommerce_support', true)) {
				// WooCommerce in general.
				add_theme_support('woocommerce');
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support('wc-product-gallery-zoom');
				// lightbox.
				add_theme_support('wc-product-gallery-lightbox');
				// swipe.
				add_theme_support('wc-product-gallery-slider');
			}
		}
	}
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option($theme_version_option_name);

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
		update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
	}
}

if (!function_exists('hello_elementor_display_header_footer')) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer()
	{
		$hello_elementor_header_footer = true;

		return apply_filters('hello_elementor_header_footer', $hello_elementor_header_footer);
	}
}

if (!function_exists('hello_elementor_scripts_styles')) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles()
	{
		$min_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if (apply_filters('hello_elementor_enqueue_style', true)) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (hello_elementor_display_header_footer()) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (!function_exists('hello_elementor_register_elementor_locations')) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations($elementor_theme_manager)
	{
		if (apply_filters('hello_elementor_register_elementor_locations', true)) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
	}
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (!function_exists('hello_elementor_add_description_meta_tag')) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag()
	{
		if (!apply_filters('hello_elementor_description_meta_tag', true)) {
			return;
		}

		if (!is_singular()) {
			return;
		}

		$post = get_queried_object();
		if (empty($post->post_excerpt)) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($post->post_excerpt)) . '">' . "\n";
	}
}
add_action('wp_head', 'hello_elementor_add_description_meta_tag');

// Admin notice
if (is_admin()) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if (!function_exists('hello_elementor_customizer')) {
	// Customizer controls
	function hello_elementor_customizer()
	{
		if (!is_customize_preview()) {
			return;
		}

		if (!hello_elementor_display_header_footer()) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action('init', 'hello_elementor_customizer');

if (!function_exists('hello_elementor_check_hide_title')) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title($val)
	{
		if (defined('ELEMENTOR_VERSION')) {
			$current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
			if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if (!function_exists('hello_elementor_body_open')) {
	function hello_elementor_body_open()
	{
		wp_body_open();
	}
}




/*this function use for dump and die dd */
function dd($var, $die = true)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
	if ($die) {
		die();
	}
}

//shortcode for the signup form
function signup_form_shortcode()
{
	ob_start();
	include 'signupshot.php';
	return ob_get_clean();
}
add_shortcode('signup_form', 'signup_form_shortcode');



//thank you hook
function ThankYouAction()
{
	echo 'Thank you for signing up!';
}
add_action('signup_form', 'ThankYouAction');

//add fields to signup form
function AdditionalInputField()
{

	echo  '<label> Email: </label>
		<input name="email" placeholder="email here" type="email"/>';
}
add_action('add_field', 'AdditionalInputField');



//Confirm your email 
function ConfirmEmail()
{
	echo 'Please confirm your email';
}
add_action('confirm_email', 'ConfirmEmail');


//jquery

function enqueue_ajax_pagination_script()
{
    wp_enqueue_script('custom-jquery', get_template_directory_uri() . '/custom-jquery.js', array('jquery'), null, true);
    wp_localize_script('custom-jquery', 'ajaxpagination', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_pagination_script');


function ajax_filter_posts()
{
	global $wpdb;

	if (isset($_POST['category_id'])) {
		$category_id = absint($_POST['category_id']);
	} else {
		$category_id = 0;
	}
	
	if (isset($_POST['page'])) {
		$paged = max(1, absint($_POST['page']));
	} else {
		$paged = 1;
	}
	

	// Function to get child categories
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

	$categories = array($category_id);
	$categories = array_merge($categories, get_child_categories($category_id));

	$query_args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'paged' => $paged,
		'posts_per_page' => 2,
		'category__in' => $categories,
		'order' => 'ASC'
	);

	$custom_query = new WP_Query($query_args);

	ob_start();

	if ($custom_query->have_posts()) :
		while ($custom_query->have_posts()) : $custom_query->the_post();
?>
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
<?php
		endwhile;
	else :
		echo '<p>No posts found.</p>';
	endif;

	$response = ob_get_clean();

	//pagination 
	ob_start();
	echo paginate_links(array(
		'total' => $custom_query->max_num_pages,
		'current' => max(1, $paged),
		'prev_text' => __('← Previous'),
		'next_text' => __('Next →'),
		'format' => '',
		'base' => get_permalink(),
		'add_args' => false,
		'type' => 'list',
	));

	$paginateLinks = ob_get_clean();
	wp_send_json_success(["posts" => $response, "paginate" => $paginateLinks]);

	wp_die();
}
add_action('wp_ajax_ajax_filter_posts', 'ajax_filter_posts');
add_action('wp_ajax_nopriv_ajax_filter_posts', 'ajax_filter_posts');

