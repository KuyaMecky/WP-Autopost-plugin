<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Plugin Loaded
 *
 * Add metabox fields in plugin loaded action.
 *
 * @package Social Auto Poster
 * @since 1.6.2
 */
function wpw_auto_poster_add_meta_boxes() {

	global $wpw_auto_poster_model, $wpw_auto_poster_options;
	$curr_post_type = wpw_auto_poster_get_current_post_type();
	$curr_post_type = !empty( $curr_post_type ) ? $curr_post_type : '';


	// check if all metaboxes are prevented and hidden then no metabox to create
	if( isset($wpw_auto_poster_options['prevent_post_metabox']) && $wpw_auto_poster_options['prevent_post_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_tw_metabox']) && $wpw_auto_poster_options['prevent_post_tw_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_li_metabox']) && $wpw_auto_poster_options['prevent_post_li_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_tb_metabox']) && $wpw_auto_poster_options['prevent_post_tb_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_yt_metabox']) && $wpw_auto_poster_options['prevent_post_yt_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_pin_metabox']) && $wpw_auto_poster_options['prevent_post_pin_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_gmb_metabox']) && $wpw_auto_poster_options['prevent_post_gmb_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_reddit_metabox']) && $wpw_auto_poster_options['prevent_post_reddit_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_tele_metabox']) && $wpw_auto_poster_options['prevent_post_tele_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_medium_metabox']) && $wpw_auto_poster_options['prevent_post_medium_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_post_wp_metabox']) && $wpw_auto_poster_options['prevent_post_wp_metabox'] == '1' &&
		isset($wpw_auto_poster_options['prevent_insta_post_metabox']) && $wpw_auto_poster_options['prevent_insta_post_metabox'] == '1'
	 ) {
		return false;
	}

	//include extended metabox class to user in poster plugin
	require_once( WPW_AUTO_POSTER_META_DIR . '/class-wpw-auto-poster-meta.php' );

	//model class
	$model = $wpw_auto_poster_model;

	/*
	 * prefix of meta keys, optional
	 * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
	 *  you also can make prefix empty to disable it
	 */
	$prefix = WPW_AUTO_POSTER_META_PREFIX;

	/*
	 * configure your meta box
	 */
	$config1 = array(
		'id' => 'wpw_auto_poster_meta', // meta box id, unique per meta box
		'title' => esc_html__( 'Social Auto Poster Settings', 'wpwautoposter' ), // meta box title
		'pages' => 'all', //insert meta in custom post type
		'context' => 'normal', // where the meta box appear: normal (default), advanced, side; optional
		'priority' => 'high', // order of meta box: high (default), low; optional
		'fields' => array(), // list of meta fields (can be added by field arrays)
		'local_images' => false, // Use local or hosted images (meta box images for add/remove)
	);

	$poster_meta = new Wpw_Auto_Poster_Social_Meta_Box( $config1 );

	//Active first tab by default
	$defaulttabon = true;

	/**
	 * Facebook Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_metabox']) || empty($wpw_auto_poster_options['prevent_post_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-facebook-tab.php';
	}

	/**
	 * Twitter Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_tw_metabox']) || empty($wpw_auto_poster_options['prevent_post_tw_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-twitter-tab.php';
	}

	/**
	 * LinkedIn Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_li_metabox']) || empty($wpw_auto_poster_options['prevent_post_li_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-linkedin-tab.php';
	}

	/**
	 * Tumblr Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_tb_metabox']) || empty($wpw_auto_poster_options['prevent_post_tb_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-tumblr-tab.php';
	}

	/**
	 * Youtube Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 2.6.0
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_yt_metabox']) || empty($wpw_auto_poster_options['prevent_post_yt_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-youtube-tab.php';
	}

	/**
	 * Pinterest Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 2.6.0
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_pin_metabox']) || empty($wpw_auto_poster_options['prevent_post_pin_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-pinterest-tab.php';
	}

	/**
	 * Instagram Tab Starts
	 * @since 2.6.0
	 * */
	do_action( 'wpw_auto_poster_tab_add_meta_boxes_after_ba', $poster_meta );

	/**
	 * Google My Business Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 2.6.0
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_gmb_metabox']) || empty($wpw_auto_poster_options['prevent_post_gmb_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-googlemybusiness-tab.php';
	}

	/**
	 * Reddit Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_reddit_metabox']) || empty($wpw_auto_poster_options['prevent_post_reddit_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-reddit-tab.php';
	}

	/**
	 * Telegram Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_tele_metabox']) || empty($wpw_auto_poster_options['prevent_post_tele_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-telegram-tab.php';
	}

	/**
	 * Medium Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 3.8.2
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_medium_metabox']) || empty($wpw_auto_poster_options['prevent_post_medium_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-medium-tab.php';
	}

	/**
	 * Instagram Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 2.8.0
	 */
	if( ! isset($wpw_auto_poster_options['prevent_insta_post_metabox']) || empty($wpw_auto_poster_options['prevent_insta_post_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-instagram-tab.php';
	}
	
	/**
	 * WordPress Tab Metaboxes
	 * check if not allowed for individual post in settings page
	 * @since 2.8.0
	 */
	if( ! isset($wpw_auto_poster_options['prevent_post_wp_metabox']) || empty($wpw_auto_poster_options['prevent_post_wp_metabox']) ) {
		include WPW_AUTO_POSTER_META_DIR . '/tabs/wpw-auto-poster-wordpress-tab.php';
	}

	

	// action for after metaboxes
	do_action( 'wpw_auto_poster_tab_after_meta_boxes', $poster_meta );

	if( $defaulttabon ) { // Check no active tab
		//meta settings are not available
		$poster_meta->addParagraph($prefix . 'no_meta_settings', array('value' => esc_html__('There is no meta settings allowed to be set for individual posts from global setting.', 'wpwautoposter')));
	}

	/*
	 * Don't Forget to Close up the meta box decleration
	 */
	//Finish Meta Box Decleration
	$poster_meta->Finish();
}


// add action to add custom meta box in custom post
add_action( 'load-post.php', 'wpw_auto_poster_add_meta_boxes' );
add_action( 'load-post-new.php', 'wpw_auto_poster_add_meta_boxes' );
