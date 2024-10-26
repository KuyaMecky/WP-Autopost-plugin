<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Post Type Functions
 *
 * Handles all custom post types
 * 
 * @package Social Auto Poster
 * @since 1.4.0
 */

/**
 * Setup Social Posting Logs Post PostTypes
 *
 * Registers the social posting logs post posttypes
 * 
 * @package Social Auto Poster
 * @since 1.4.0
 */
function wpw_auto_poster_register_post_types() {
	
	//social posing logs - post type
	$social_posting_logs_labels = array(
		'name'				=> esc_html__( 'Social Posing Logs','wpwautoposter' ),
		'singular_name' 	=> esc_html__( 'Social Posing Log','wpwautoposter' ),
		'add_new' 			=> esc_html__( 'Add New','wpwautoposter' ),
		'add_new_item' 		=> esc_html__( 'Add New Social Posing Log','wpwautoposter' ),
		'edit_item' 		=> esc_html__( 'Edit Social Posing Log','wpwautoposter' ),
		'new_item' 			=> esc_html__( 'New Social Posing Log','wpwautoposter' ),
		'all_items' 		=> esc_html__( 'All Social Posing Logs','wpwautoposter' ),
		'view_item' 		=> esc_html__( 'View Social Posing Log','wpwautoposter' ),
		'search_items' 		=> esc_html__( 'Search Social Posing Log','wpwautoposter' ),
		'not_found' 		=> esc_html__( 'No social posing logs found','wpwautoposter' ),
		'not_found_in_trash'=> esc_html__( 'No social posing logs found in Trash','wpwautoposter' ),
		'parent_item_colon' => '',
		'menu_name' 		=> esc_html__( 'Social Posing Logs','wpwautoposter' ),
	);

	$social_posting_logs_args = array(
		'labels'			=> $social_posting_logs_labels,
		'public'			=> false,
		'query_var'			=> false,
		'rewrite'			=> false,
		'capability_type'	=> WPW_AUTO_POSTER_LOGS_POST_TYPE,
		'hierarchical'		=> false,
		'supports'			=> array( 'title' )
	); 
	
	//register social posing logs post type
	register_post_type( WPW_AUTO_POSTER_LOGS_POST_TYPE, $social_posting_logs_args );

	// Quick Share - post type
	$social_quick_share_labels = array(
		'name'				=> esc_html__( 'Quick Share', 'wpwautoposter' ),
		'singular_name' 	=> esc_html__( 'Quick Share', 'wpwautoposter' ),
		'add_new' 			=> esc_html__( 'Add New', 'wpwautoposter' ),
		'add_new_item' 		=> esc_html__( 'Add New Quick Share Post', 'wpwautoposter' ),
		'edit_item' 		=> esc_html__( 'Edit Quick Share Post', 'wpwautoposter' ),
		'new_item' 			=> esc_html__( 'New Quick Share Post', 'wpwautoposter' ),
		'all_items' 		=> esc_html__( 'All Quick Share Post', 'wpwautoposter' ),
		'view_item' 		=> esc_html__( 'View Quick Share Post', 'wpwautoposter' ),
		'search_items' 		=> esc_html__( 'Search Quick Share Post', 'wpwautoposter' ),
		'not_found' 		=> esc_html__( 'No quick share post found','wpwautoposter' ),
		'not_found_in_trash'=> esc_html__( 'No quick share post found in Trash','wpwautoposter' ),
		'parent_item_colon' => '',
		'menu_name' 		=> esc_html__( 'Quick Share Post', 'wpwautoposter' ),
	);

	$quick_share_logs_args = array(
		'labels'			=> $social_quick_share_labels,
		'public'			=> false,
		'query_var'			=> false,
		'rewrite'			=> false,
		'capability_type'	=> WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE,
		'hierarchical'		=> false,
		'supports'			=> array( 'title', 'editor', 'thumbnail' )
	); 
	
	// register quick share post type
	register_post_type( WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE, $quick_share_logs_args );
}

//register custom post type
// we need to keep priority 100, because we need to execute this init action after all other init action called.
add_action( 'init', 'wpw_auto_poster_register_post_types', 100 );