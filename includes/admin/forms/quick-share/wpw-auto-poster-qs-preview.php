<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Quick share preview
 *
 * @package Social Auto Poster
 * @since 3.9.2
 */
global $wpw_auto_poster_model, $wpw_auto_poster_options;

// model class
$model = $wpw_auto_poster_model;
$prefix = WPW_AUTO_POSTER_META_PREFIX;
$id = ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) ? stripslashes_deep($_GET['id']) : '';

if( empty( $id ) ){
	return false;
}
$post = get_post($id);

//get posting date/time
$format 			   = get_option( 'date_format' ).' '.get_option('time_format') ;
$publication_timestamp = get_the_date($format, $id);
$date = $post->post_date;
$networks = get_post_meta( $id ,$prefix . 'enable_socials',true);
$message  = $post->post_title;
$image    = get_post_meta( $id, $prefix . 'share_image', true );
$link 	  = get_post_meta( $id, $prefix . 'share_link', true ); ?>

<div class="wpw-auto-poster-qs-add-new form-wrap wpw-auto-poster-card">
	<div class="qs-heading"><h3><?php esc_html_e( 'Preview', 'wpwautoposter' ); ?></h3><a class="back-button" href="?page=wpw-auto-poster-quick-share"><?php esc_html_e( 'Back', 'wpwautoposter' ); ?></a></div>
	
	<?php
	if( !empty( $networks ) ) { 
		foreach( $networks as $key => $network ) {
			$args = array(
				'posts_per_page'		=> '-1',
				'wpw_auto_poster_list'	=> true,
				'post_parent'	=> $id,
			);
			$args['meta_query']	= array(
				array(
					'key' => $prefix . 'social_type',
					'value' => $network,
				)
			);
			$logdata = $model->wpw_auto_poster_get_posting_logs_data( $args );
			$logs 	 = $logdata['data'];
			if( !empty($logs) ) {
				foreach( $logs as $lg_key => $log) {
					$user_details = get_post_meta( $log['ID'], $prefix.'user_details', true );
					//get posting details from meta
	 				$posting_logs = get_post_meta( $log['ID'], $prefix.'posting_logs', true );
	 				$image = (isset( $posting_logs['image'] ) && !empty( $posting_logs['image'] ) ) ? $posting_logs['image'] : '';
	 				// check if tumblr
	 				if( $network == 'tb' && empty($image) ) {
	 					$image = !empty( $posting_logs['source'] ) ? $posting_logs['source'] : '';
	 				}
	 				$message = (isset( $posting_logs['title'] ) && !empty( $posting_logs['title'] ) ) ? $posting_logs['title'] : '';
	 				$message = ( empty( $message ) && isset( $posting_logs['description'] ) ) ? $posting_logs['description'] : $message; 
	 				$link = (isset( $posting_logs['link'] ) && !empty( $posting_logs['link'] ) ) ? $posting_logs['link'] : '';
	 				// get posting link
	 				$post_link = '#';
	 				if( $network != 'yt' && $network != 'tele' ) {
						$post_link = wpw_auto_poster_get_post_link( $network, $user_details );
					}
					if( $network == 'li' ) {
						$post_link = 'https://www.linkedin.com'.$post_link;
					} ?>

					<div class="wpw-auto-poster-quick-share-privew sap-quick-privew-<?php print $network; ?>">
			            <div class="wpw-auto-poster-quick-share-privew-header">
				            <div class="sap-quick-post-privew-header-h2">
				                <div class="left-head">
				                	<a href="<?php echo $post_link;?>" target="_blank"><?php echo $model->wpw_auto_poster_get_social_type_name($network);?>
				                		<p class="sap-quick-post-privew-date"><?php echo $publication_timestamp;?></p>
				                    </a>
				                </div>
				                <div class="sap-quick-status success">Published</div>
				            </div>
			            </div>
			            <div class="wpw-auto-poster-quick-share-privew-content">
			            	<a href="<?php echo $post_link;?>" target="_blank">
								<?php
								if( !empty($image) && $network != 'yt' ) { ?>
									<div class="image"><img src="<?php echo $image;?>"></div>
								<?php }

								if( !empty($message) && $network != 'yt' ) { ?>
									<p class="title"><?php echo $message;?></p>
								<?php }

								if( !empty($link) && $network != 'yt' ) { ?>
									<p class="link"><?php echo $link;?></p>
								<?php } ?>
			            	</a>

			            	<?php
			            	$user_details['display_name'] = !empty( $user_details['display_name'] ) ? $user_details['display_name'] : '';
			            	if( $network == 'tele'){
			            		$user_details['display_name'] = $user_details['telegram_chatid'];
			            	} else if( $network == 'wp'){
			            		$user_details['display_name'] = $user_details['site_name'];
			            	} ?>

			            	<p class="log success"><?php echo esc_html__( 'Posted on ', 'wpwautoposter' ).$user_details['display_name'].' '.esc_html__( 'Successfully', 'wpwautoposter' );?></p>
			            </div>
			        </div>
	    		<?php
				} // log loop
			}  else {
				$is_error	= get_post_meta($id, $prefix . $network .'_post_status',true);
				$schdule	= get_post_meta($id, $prefix . 'schedule_wallpost', true);
				$schdule	= !empty( $schdule ) ? $schdule : array();

				if( $is_error == 'error'){ ?>
					<div class="wpw-auto-poster-quick-share-privew sap-quick-privew-<?php print $network; ?>">
				            <div class="wpw-auto-poster-quick-share-privew-header">
					            <div class="sap-quick-post-privew-header-h2">
					                <div class="left-head">
					                	<a href="#" target="_blank"><?php echo $model->wpw_auto_poster_get_social_type_name($network);?>
					                		<p class="sap-quick-post-privew-date"><?php echo $publication_timestamp;?></p>
					                    </a>
					                </div>
					                <div class="sap-quick-status sap-error"><?php print esc_html__( 'Error', 'wpwautoposter' );?></div>
					            </div>
				            </div>
				            <div class="wpw-auto-poster-quick-share-privew-content">
				            	<a href="#" target="_blank">
				            	<?php if( !empty($image) && $network != 'yt' ) {?>
				            		<div class="image"><img src="<?php echo $image;?>"></div>
				            	<?php } ?>
				            	<?php if( !empty($message) && $network != 'yt' ) {?>
				            		<p class="title"><?php echo $message;?></p>
				            	<?php } ?>
				            	<?php if( !empty($link) && $network != 'yt' ) {?>
				            		<p class="link"><?php echo $link;?></p>
				            	<?php } ?>
				            	</a>
				            	<p class="log error"><?php echo get_post_meta($id, $prefix . $network .'_error', true ); ?></p>
				            </div>
				        </div>
				<?php
				} else{
					$full_network = $model->wpw_auto_poster_get_social_type_name($network);
					$full_network = str_replace( ' ', '', $full_network );
					
					if( in_array(strtolower($full_network), $schdule) ){ ?>
						<div class="wpw-auto-poster-quick-share-privew sap-quick-privew-<?php print $network; ?>">
					            <div class="wpw-auto-poster-quick-share-privew-header">
						            <div class="sap-quick-post-privew-header-h2">
						                <div class="left-head">
						                	<a href="#" target="_blank"><?php echo $model->wpw_auto_poster_get_social_type_name($network);?>
						                		<p class="sap-quick-post-privew-date"><?php echo $publication_timestamp;?></p>
						                    </a>
						                </div>
						                <div class="sap-quick-status success"><?php print esc_html__( 'Schedule', 'wpwautoposter' );?></div>
						            </div>
					            </div>
					            <div class="wpw-auto-poster-quick-share-privew-content">
					            	<a href="#" target="_blank">
					            	<?php if( !empty($image) && $network != 'yt' ) {?>
					            		<div class="image"><img src="<?php echo $image;?>"></div>
					            	<?php } ?>
					            	<?php if( !empty($message) && $network != 'yt' ) {?>
					            		<p class="title"><?php echo $message;?></p>
					            	<?php } ?>
					            	<?php if( !empty($link) && $network != 'yt' ) {?>
					            		<p class="link"><?php echo $link;?></p>
					            	<?php } ?>
					            	</a>
					            </div>
					        </div>
						<?php
					}
				}

			}
		}
	} 
	?>    
</div>