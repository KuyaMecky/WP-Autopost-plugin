<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Settings Hooks
 *
 * The code for the plugins main settings hooks
 *
 * @package Social Auto Poster
 * @since 2.6.9
 */

/*********************** General Settings ***************************/

if( !function_exists( 'wpw_auto_poster_reposter_general_setting_tab' ) ) {

	/**
	 * Display General Setting Tab
	 * 
	 * Handle to display general setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_general_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'general' ? ' nav-tab-active' : ''; ?>
		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-general" attr-tab="general">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/wpw-auto-poster-icon.png" width="24" height="24" alt="gn" title="<?php esc_html_e( 'General', 'wpwautoposter' ); ?>" />
		</a>

	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_general_setting_tab_content' ) ) {

	/**
	 * Display General Setting Tab Content
	 * 
	 * Handle to display general setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_general_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'general' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-general"> 
			<?php
			// General Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-general-settings.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-general-->
	<?php
	}
}

/*********************** Facebook Settings ***************************/

if( !function_exists( 'wpw_auto_poster_reposter_facebook_setting_tab' ) ) {

	/**
	 * Display Facebook Setting Tab
	 * 
	 * Handle to display facebook setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_facebook_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'facebook' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-facebook" attr-tab="facebook">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/facebook_set.png" width="24" height="24" alt="fb" title="<?php esc_html_e( 'Facebook', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_facebook_setting_tab_content' ) ) {

	/**
	 * Display Facebook Setting Tab Content
	 * 
	 * Handle to display facebook setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_facebook_setting_tab_content( $selected_tab ) {

		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'facebook' ? ' wpw-auto-poster-selected-tab' : '';
		?>
			<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-facebook"> 
					
				<?php
			
				// Facebook Settings
				include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-facebook.php' );
			
				?>
			
			</div><!--#wpw-auto-poster-tab-facebook-->
		<?php
	}
}

/*********************** Twitter Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_twitter_setting_tab' ) ) {

	/**
	 * Display Twitter Setting Tab
	 * 
	 * Handle to display twitter setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_twitter_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'twitter' ? ' nav-tab-active' : '';
		?>
			<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-twitter" attr-tab="twitter">
				<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/twitter_set.png" width="24" height="24" alt="tw" title="<?php esc_html_e( 'Twitter', 'wpwautoposter' ); ?>" />
			</a>
		<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_twitter_setting_tab_content' ) ) {

	/**
	 * Display Twitter Setting Tab Content
	 * 
	 * Handle to display twitter setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_twitter_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'twitter' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-twitter">
			<?php
			// Twitter Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-twitter.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-twitter-->
	<?php
	}
}

/*********************** LinkedIn Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_linkedin_setting_tab' ) ) {

	/**
	 * Display LinkedIn Setting Tab
	 * 
	 * Handle to display linkedin setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_linkedin_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'linkedin' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-linkedin" attr-tab="linkedin">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/linkedin_set.png" width="24" height="24" alt="li" title="<?php esc_html_e( 'LinkedIn', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_linkedin_setting_tab_content' ) ) {

	/**
	 * Display LinkedIn Setting Tab Content
	 * 
	 * Handle to display linkedin setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_linkedin_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'linkedin' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-linkedin">
			<?php
			// LinkedIn Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-linkedin.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-linkedin-->
	<?php
	}
}

/*********************** Tumblr Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_tumblr_setting_tab' ) ) {

	/**
	 * Display Tumblr Setting Tab
	 * 
	 * Handle to display tumblr setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_tumblr_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'tumblr' ? ' nav-tab-active' : ''; ?>
		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-tumblr" attr-tab="tumblr">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/tumblr_set.png" width="24" height="24" alt="tb" title="<?php esc_html_e( 'Tumblr', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_tumblr_setting_tab_content' ) ) {

	/**
	 * Display Tumblr Setting Tab Content
	 * 
	 * Handle to display tumblr setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_tumblr_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'tumblr' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-tumblr"> 
			<?php
			// Tumblr Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-tumblr.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-tumblr-->
	<?php
	}
}

/*********************** WordPress Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_wordpress_setting_tab' ) ) {

	/**
	 * Display WordPress Setting Tab
	 * 
	 * Handle to display WordPress setting tab
	 *
	 * @package Social Auto Poster
	 * @since 3.4.1
	 */
	function wpw_auto_poster_reposter_wordpress_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'wordpress' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo $selectedtab; ?>" href="#wpw-auto-poster-tab-wordpress" attr-tab="wordpress">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/wordpress_set.png" width="24" height="24" alt="wp" title="<?php esc_html_e( 'WordPress', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_wordpress_setting_tab_content' ) ) {

	/**
	 * Display WordPress Setting Tab Content
	 * 
	 * Handle to display WordPress setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 3.4.1
	 */
	function wpw_auto_poster_reposter_wordpress_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'wordpress' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo $selectedtabcontent; ?>" id="wpw-auto-poster-tab-wordpress">
			<?php
			// wordpress Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-wordpress.php' ); ?>
		</div><!-- #wpw-auto-poster-reposter-tab-wordpress -->
	<?php
	}
}

/*********************** Telegram Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_telegram_setting_tab' ) ) {

	/**
	 * Display Telegram Setting Tab
	 * 
	 * Handle to display Telegram setting tab
	 *
	 * @package Social Auto Poster
	 * @since 3.4.1
	 */
	function wpw_auto_poster_reposter_telegram_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'telegram' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo $selectedtab; ?>" href="#wpw-auto-poster-tab-telegram" attr-tab="telegram">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/telegram_set.png" width="24" height="24" alt="wp" title="<?php esc_html_e( 'Telegram', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_telegram_setting_tab_content' ) ) {

	/**
	 * Display Telegram Setting Tab Content
	 * 
	 * Handle to display Telegram setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 3.7.0
	 */
	function wpw_auto_poster_reposter_telegram_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'telegram' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo $selectedtabcontent; ?>" id="wpw-auto-poster-tab-telegram">
			<?php
			// telegram Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-telegram.php' ); ?>
		</div><!-- #wpw-auto-poster-reposter-tab-telegram -->
	<?php
	}
}

/*********************** Google My Business Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_googlemybusiness_setting_tab' ) ) {

	/**
	 * Display Google My Business Setting Tab
	 * 
	 * Handle to display Google My Business setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_googlemybusiness_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'googlemybusiness' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo $selectedtab; ?>" href="#wpw-auto-poster-tab-googlemybusiness" attr-tab="googlemybusiness">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/googlemybusiness_set.png" width="24" height="24" alt="gmb" title="<?php esc_html_e( 'Google My Business', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_googlemybusiness_setting_tab_content' ) ) {

	/**
	 * Display Google My Business Setting Tab Content
	 * 
	 * Handle to display Google My Business setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_googlemybusiness_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'googlemybusiness' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo $selectedtabcontent; ?>" id="wpw-auto-poster-tab-googlemybusiness"> 
			<?php
			// Google My Business Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-googlemybusiness.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-googlemybusiness-->
	<?php
	}
}


/*********************** Reddit Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_reddit_setting_tab' ) ) {

	/**
	 * Display Reddit Setting Tab
	 * 
	 * Handle to display Reddit setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_reddit_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'reddit' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo $selectedtab; ?>" href="#wpw-auto-poster-tab-reddit" attr-tab="reddit">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/reddit_set.png" width="24" height="24" alt="gmb" title="<?php esc_html_e( 'Reddit', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_reddit_setting_tab_content' ) ) {

	/**
	 * Display Reddit Setting Tab Content
	 * 
	 * Handle to display Reddit setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_reddit_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'reddit' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo $selectedtabcontent; ?>" id="wpw-auto-poster-tab-reddit"> 
		
         <?php
			// Reddit Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-reddit.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-reddit-->
	<?php
	}
}

/*********************** Medium Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_medium_setting_tab' ) ) {

	/**
	 * Display Medium Setting Tab
	 * 
	 * Handle to display Medium setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_medium_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'medium' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo $selectedtab; ?>" href="#wpw-auto-poster-tab-medium" attr-tab="medium">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/medium_set.png" width="24" height="24" alt="md" title="<?php esc_html_e( 'Medium', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_medium_setting_tab_content' ) ) {

	/**
	 * Display Medium Setting Tab Content
	 * 
	 * Handle to display Medium setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_medium_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'medium' ? ' wpw-auto-poster-selected-tab' : ''; ?>
		<div class="wpw-auto-poster-tab-content <?php echo $selectedtabcontent; ?>" id="wpw-auto-poster-tab-medium"> 
         <?php
			// Reddit Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-medium.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-reddit-->
	<?php
	}
}

/*********************** Pinterest Settings ***************************/
if( !function_exists( 'wpw_auto_poster_reposter_pinterest_setting_tab' ) ) {

	/**
	 * Display Pinterest Setting Tab
	 * 
	 * Handle to display pinterest setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_pinterest_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'pinterest' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-pinterest" attr-tab="pinterest">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/pinterest_set.png" width="24" height="24" alt="ins" title="<?php esc_html_e( 'Pinterest', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_pinterest_setting_tab_content' ) ) {

	/**
	 * Display Pinterest Setting Tab Content
	 * 
	 * Handle to display pinterest setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_pinterest_setting_tab_content( $selected_tab ) {
	
		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'pinterest' ? ' wpw-auto-poster-selected-tab' : ''; ?>

		<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-pinterest">
			<?php
			// Pinterest Settings
			include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-pinterest.php' ); ?>
		</div><!--#wpw-auto-poster-reposter-tab-pinterest-->
	<?php
	}
}


/*********************** Instagram Settings ***************************/

if( !function_exists( 'wpw_auto_poster_reposter_instagram_setting_tab' ) ) {

	/**
	 * Display Instagram Setting Tab
	 * 
	 * Handle to display instagram setting tab
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_instagram_setting_tab( $selected_tab ) {
		
		$selectedtab = !empty( $selected_tab ) && $selected_tab == 'instagram' ? ' nav-tab-active' : ''; ?>

		<a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-instagram" attr-tab="instagram">
			<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/instagram_set.png" width="24" height="24" alt="instagram" title="<?php esc_html_e( 'Instagram', 'wpwautoposter' ); ?>" />
		</a>
	<?php
	}
}

if( !function_exists( 'wpw_auto_poster_reposter_instagram_setting_tab_content' ) ) {

	/**
	 * Display Instagram Setting Tab Content
	 * 
	 * Handle to display instagram setting tab content
	 *
	 * @package Social Auto Poster
	 * @since 2.6.9
	 */
	function wpw_auto_poster_reposter_instagram_setting_tab_content( $selected_tab ) {

		$selectedtabcontent = !empty( $selected_tab ) && $selected_tab == 'instagram' ? ' wpw-auto-poster-selected-tab' : '';
		?>
			<div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-instagram"> 
					
				<?php
			
				// Instagram Settings
				include( WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-instagram.php' );
			
				?>
			
			</div><!--#wpw-auto-poster-tab-instagram-->
		<?php
	}
}

/*********************** All Hooks Start ***************************/
// add action to add general settings tab 	-  5
// add action to add facebook settings tab 	- 10
// add action to add twitter settings tab 	- 15
// add action to add linkedin settings tab 	- 20
// add action to add tumblr settings tab 	- 25
// add action to add pinterest settings tab - 35
// add action to add gmb settings tab 		- 40
// add action to add reddit settings tab    - 99
// add action to add telegram settings tab  - 149
// add action to add wordpress settings tab	- 199
// add action to add bufferapp settings tab - 299
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_general_setting_tab', 5 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_facebook_setting_tab', 10 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_twitter_setting_tab', 15 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_linkedin_setting_tab', 20 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_tumblr_setting_tab', 25 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_pinterest_setting_tab', 35 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_googlemybusiness_setting_tab', 40 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_reddit_setting_tab', 99 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_telegram_setting_tab', 149 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_medium_setting_tab',199 );

add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_instagram_setting_tab',249);

add_action( 'wpw_auto_poster_reposter_settings_panel_tab', 'wpw_auto_poster_reposter_wordpress_setting_tab',299);


do_action( 'wpw_auto_poster_reposter_settings_panel_after_tabs' );
do_action( 'wpw_auto_poster_reposter_settings_panel_tab_after_ba' );

// add action to add general settings tab content 	-  5
// add action to add facebook settings tab content 	- 10
// add action to add twitter settings tab content 	- 15
// add action to add linkedin settings tab content 	- 20
// add action to add tumblr settings tab content 	- 25
// add action to add youtube settings tab content 	- 30
// add action to add pinterest settings tab content	- 35
// add action to add gmb settings tab content 		- 40
// add action to add reddit settings tab content    - 99
// add action to add telegram settings tab content  - 149
// add action to add wordpress settings tab content	- 199
// add action to add bufferapp settings tab content - 299
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_general_setting_tab_content', 5 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_facebook_setting_tab_content', 10 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_twitter_setting_tab_content', 15 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_linkedin_setting_tab_content', 20 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_tumblr_setting_tab_content', 25 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_pinterest_setting_tab_content', 35 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_googlemybusiness_setting_tab_content', 40 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_reddit_setting_tab_content', 99 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_telegram_setting_tab_content', 149 );
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_medium_setting_tab_content',199);
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_instagram_setting_tab_content',249);
add_action( 'wpw_auto_poster_reposter_settings_panel_tab_content', 'wpw_auto_poster_reposter_wordpress_setting_tab_content',299);


do_action( 'wpw_auto_poster_reposter_settings_panel_after_tabs_content' );
do_action( 'wpw_auto_poster_reposter_settings_panel_tab_content_after_ba' );

/*********************** All Hooks End ***************************/