<?php
// Exit if accessed directly
if( ! defined('ABSPATH') ) exit;

/**
 * Quick Share Page
 * The code for the plugins quick share functionality
 *
 * @package Social Auto Poster
 * @since 3.9.2
 */

global $wpw_auto_poster_model;
// model class
$model = $wpw_auto_poster_model;
?>

<div class="wrap">

	<!-- Social Auto Poster logo -->
	<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="<?php esc_html_e( 'Logo', 'wpwautoposter' ) ?>" />
		
	<!-- plugin name -->
	<h2><?php esc_html_e( 'Quick Share', 'wpwautoposter' ); ?></h2><br />

	<?php
	// check messages
	if( !empty($_GET['message']) && $_GET['message'] == '3' ) {
		echo '<div class="error fade" id="wpw-qs-error"><p><strong>' . 
			esc_html__( 'Please enter message.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '4' ) {
		echo '<div class="error fade" id="wpw-qs-success"><p><strong>' . 
			esc_html__( 'Please activate at least one social network.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '5' ) {
		echo '<div class="error fade" id="wpw-qs-success"><p><strong>' . 
			esc_html__( 'Link is requires when link posting is enabled.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '6' ) {
		echo '<div class="error fade" id="wpw-qs-success"><p><strong>' . 
			esc_html__( 'Image is requires when image posting is enabled.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '7' ) {
		// no message here, it will print from social links
	} else if( !empty($_GET['message']) && $_GET['message'] == '8' ) {
		echo '<div class="updated fade" id="wpw-qs-success"><p><strong>' . 
			esc_html__( 'Post deleted successfully.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '9' ) {
		$networks = isset( $_GET['network'] ) ? stripslashes_deep($_GET['network']) : '';
		$network_arr = explode(',', $networks);
		$network_name = array();
		if( !empty( $network_arr ) ){
			foreach ( $network_arr as $key => $network_val ) {
				$network_name[] = $model->wpw_auto_poster_get_social_type_name($network_val);
			}
		}

		echo '<div class="error fade" id="wpw-qs-error"><p><strong>' . 
		esc_html__( 'Please select the account for the network ', 'wpwautoposter' ) . implode(', ', $network_name) . '</strong></p></div>';
	} else if( !empty($_GET['message']) && $_GET['message'] == '10' ) {
		echo '<div class="error fade" id="wpw-qs-success"><p><strong>' . 
			esc_html__( 'Please enter valid link.', 'wpwautoposter' ) .
		'</strong></p></div>';
	} ?>

	<div class="wpw-auto-poster-quick-share-wrap">
		<div id="col-container" class="wp-clearfix">
			<div id="col-left"><div class="col-wrap">
				<?php
				if( isset($_GET['action']) && $_GET['action'] == 'preview' ){
					include_once( WPW_AUTO_POSTER_ADMIN . '/forms/quick-share/wpw-auto-poster-qs-preview.php' );
				} else{
					include_once( WPW_AUTO_POSTER_ADMIN . '/forms/quick-share/wpw-auto-poster-qs-add-new-form.php' );
				}
			?>
			</div></div>
			<div id="col-right"><div class="col-wrap">
				<?php
				include_once( WPW_AUTO_POSTER_ADMIN . '/forms/quick-share/wpw-auto-poster-qs-post-list.php' ); ?>
			</div>
		</div>
	</div>
</div>