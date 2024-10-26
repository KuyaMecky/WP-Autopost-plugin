<?php
// Exit if accessed directly
if ( !defined('ABSPATH') ) exit;

/**
 * Settings Page
 *
 * The code for the plugins main settings page
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_message_stack,
		$wpw_auto_poster_fb_posting, $wpw_auto_poster_li_posting, $wpw_auto_poster_tb_posting, $wpw_auto_poster_ba_posting,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_medium_posting;

//model class
$model = $wpw_auto_poster_model;

//message stack class
$message = $wpw_auto_poster_message_stack;

//facebook posting class
$fbposting = $wpw_auto_poster_fb_posting;

//linkedin posting class
$liposting = $wpw_auto_poster_li_posting;

//tumblr posting class
$tbposting = $wpw_auto_poster_tb_posting;

//BufferApp posting class
$baposting = $wpw_auto_poster_ba_posting;

//GMB posting class
$gmbposting = $wpw_auto_poster_gmb_postings;

//Reddit posting class
$redditposting = $wpw_auto_poster_reddit_postings;

//Reddit posting class
$mediumposting = $wpw_auto_poster_medium_posting;
?>

<div class="wrap">

	<!-- Social Auto Poster logo -->
	<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="<?php esc_html_e( 'Logo', 'wpwautoposter' ) ?>" />

	<!-- plugin name -->
	<h2><?php esc_attr_e( 'Social Auto Poster Settings', 'wpwautoposter' ); ?></h2><br />

	<!-- settings reset -->
	<?php
	if( isset( $_POST['wpw_auto_posting_reset_settings'] ) && $_POST['wpw_auto_posting_reset_settings'] == esc_html__( 'Reset All Settings', 'wpwautoposter' ) ) {

		// Plugin install setup function file
		require_once( WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-setup-functions.php' );

		$fbposting->wpw_auto_poster_fb_reset_session(); //Facebook session reset
		$liposting->wpw_auto_poster_li_reset_session(); //Linkedin session reset
		$tbposting->wpw_auto_poster_tb_reset_session(); //Tumblr session reset
		$gmbposting->wpw_auto_poster_gmb_reset_session(); // GMB session reset
		$redditposting->wpw_auto_poster_reddit_reset_session(); // Reddit session reset
		$mediumposting->wpw_auto_poster_medium_reset_session();// Medium session reset

		//delete auto poster options
		delete_option('wpw_auto_poster_options');

		//deleter facebook session data
		delete_option('wpw_auto_poster_fb_sess_data');

		//delete linkedin session data
		delete_option('wpw_auto_poster_li_sess_data');

		//delete tumblr session data
		delete_option('wpw_auto_poster_tb_sess_data');

		//delete bufferapp session data
		delete_option('wpw_auto_poster_ba_sess_data');

		//delete twitter account data
		delete_option('wpw_auto_poster_tw_account_details');

		//delete pinterest session data
		delete_option('wpw_auto_poster_pin_sess_data');

		//delete gmb option data
		delete_option('wpw_auto_poster_gmb_sess_data');

		//delete reddit option data
		delete_option('wpw_auto_poster_reddit_sess_data');

		//delete medium option data
		delete_option('wpw_auto_poster_medium_sess_data');

		//delete set option data
		delete_option('wpw_auto_poster_set_option');

		// set default settings
		wpw_auto_posting_default_settings();

		echo '<div id="message" class="updated fade wpw_auto_posting_reposter_reset_settings"><p><strong>' . esc_html__( 'All Settings Reset Successfully.', 'wpwautoposter' ) . '</strong></p></div>';
	}

	// settings updated message
	if( isset( $_GET['settings-updated'] ) ) {
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Changes Saved.', 'wpwautoposter' ) . '</strong></p></div>';
	}

	echo apply_filters(
	 'wpweb_fb_settings_submit_button',
	 '<form method="post" action="" class="wpw-auto-poster-posting-reset-setting-form-main">
			<div class="wpw-auto-poster-posting-reset-setting">
				<input type="submit" class="button-primary wpw-auto-poster-reset-button" id="wpw_auto_posting_reset_settings" name="wpw_auto_posting_reset_settings" value="' . esc_html__( 'Reset All Settings', 'wpwautoposter' ) . '" />
			 </div>
		</form>'
	); ?>

	<!-- beginning of the plugin options form -->
	<form id="wpw_auto_poster_setting" method="post" action="options.php">
		<?php settings_fields( 'wpw_auto_poster_plugin_options' ); ?>
		<?php $wpw_auto_poster_options = get_option( 'wpw_auto_poster_options' ); ?>

		<!-- beginning of the left meta box section -->
		<div class="content">
			<?php
			/**
			 * Settings Boxes
			 * Including all the different settings boxes for the plugin options.
			 *
			 * @package Social Auto Poster
			 * @since 1.0.0
			 */
			$selected_tab = 'general';
			if( $message->size( 'poster-selected-tab' ) > 0 ) { //make tab selected
				$selected_tab = $message->messages[0]['text'];
				$message->remove_session( 'poster-selected-tab' );
			} ?>

			<h2 class="nav-tab-wrapper wpw-auto-poster-h2">
				<?php do_action( 'wpw_auto_poster_settings_panel_tab', $selected_tab ) ?>
			</h2><!--nav-tab-wrapper-->

			<input type="hidden" id="wpw_auto_poster_selected_tab" name="wpw_auto_poster_options[selected_tab]" value="<?php echo esc_attr($selected_tab);?>"/>

			<div class="wpw-auto-poster-content">
				<?php
				do_action( 'wpw_auto_poster_settings_panel_tab_content', $selected_tab ) ?>
			</div>
		</div><!-- .content -->
	</form><!-- end of plugin options form -->
</div><!-- .wrap -->
