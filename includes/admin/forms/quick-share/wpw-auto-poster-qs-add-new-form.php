<?php
// Exit if accessed directly
if( ! defined('ABSPATH') ) exit;

/**
 * Add New Form
 * Add new quick share form
 *
 * @package Social Auto Poster
 * @since 3.9.2
 */

global $wpw_auto_poster_model, $wpw_auto_poster_options, $wpw_auto_poster_li_posting, $wpw_auto_poster_wp_posting;

// model class
$model = $wpw_auto_poster_model;

// posting class
$liposting = $wpw_auto_poster_li_posting;
$wpposting = $wpw_auto_poster_wp_posting; 

$prefix    = WPW_AUTO_POSTER_META_PREFIX;
$postdata = array();
$postdata = get_transient( 'wpw_auto_poster_quick_share_post_data' );
delete_transient( 'wpw_auto_poster_quick_share_post_data' );
?>
<form method="post" action="" class="validate">
	<div class="wpw-auto-poster-qs-add-new form-wrap wpw-auto-poster-card">
		<h1><?php esc_html_e( 'Quick Share', 'wpwautoposter' ); ?></h1>
		<?php
		wp_nonce_field( 'wpw_auto_poster_save_quick_share', 'quick_share_save' ); ?>
		<div class="form-field wpw-auto-poster-uploader-container">
			<div class="wpw-auto-poster-uploader-wrape">
				<div class="wpw-auto-poster-uploader">
					<label class="wpw-auto-poster-upload-wrap image-uploader" for="wpw-qs-img-uploader">
						<span class="dashicons dashicons-format-image"></span>
						<input type="hidden" value="<?php echo isset($postdata['qs_image']['id']) ? esc_attr($postdata['qs_image']['id']) : ''; ?>" name="qs_image[id]" id="wpw-qs-img-id" />
						<input type="hidden" value="<?php echo isset($postdata['qs_image']['url']) ? esc_attr($postdata['qs_image']['url']) : ''; ?>" name="qs_image[url]" id="wpw-qs-img" class="wpw-auto-poster-img-field width-73" />
						<input id="wpw-qs-img-uploader" type="button" class="button-secondary wpw-auto-poster-quick-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' ); ?>" />
					</label>
					<p><?php esc_html_e( 'Upload the post image here.', 'wpwautoposter' ); ?></p>
				</div>
			</div>
			<div class="wpw-auto-poster-uploader-wrape">
				<div class="wpw-auto-poster-uploader">
					<label class="wpw-auto-poster-upload-wrap video-uploader" for="wpw-qs-video-uploader">
						<span class="dashicons dashicons-format-video"></span>
						<input type="hidden" name="qs_video[id]" id="wpw-qs-vid-id" value="<?php echo isset($postdata['qs_video']['id']) ? esc_attr($postdata['qs_video']['id']) : ''; ?>" />
						<input type="hidden" value="<?php echo isset($postdata['qs_video']['url']) ? esc_url($postdata['qs_video']['url']) : ''; ?>" name="qs_video[url]" id="wpw-qs-vid" class="wpw-auto-poster-img-field width-73" />
						<input id="wpw-qs-video-uploader" type="button" class="button-secondary wpw-auto-poster-quick-uploader-button video" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Video','wpwautoposter' ); ?>" />
					</label>
					<p><?php esc_html_e( 'Video will only post for the YouTube.', 'wpwautoposter' ); ?></p>
				</div>
			</div>	
		</div>	

		<div class="form-field form-required wpw-qs-message-field">
			<label for="wpw-qs-msg"><?php esc_html_e( 'Message*', 'wpwautoposter' ); ?></label>
			<label class="wpw-qs-character"><?php esc_html_e( 'Characters count:', 'wpwautoposter' ); ?><span>0</span></label>
			<textarea name="qs_message" id="wpw-qs-msg" aria-required="true" rows="3" placeholder="<?php esc_html_e( 'Enter your message', 'wpwautoposter' ); ?>"><?php if( isset($postdata['qs_message']) ) echo sanitize_textarea_field( $postdata['qs_message'] ); ?></textarea>
		</div>

		<div class="form-field">
			<label for="wpw-qs-link"><?php esc_html_e( 'Link', 'wpwautoposter' ); ?></label>
			<input name="qs_link" id="wpw-qs-link" type="text" value="<?php echo isset($postdata['qs_link']) ? esc_url($postdata['qs_link']) : ''; ?>" placeholder="<?php esc_html_e('Example: https://example.com', 'wpwautoposter'); ?>" />
			<p><?php esc_html_e( 'Here you can enter a link which will be used for the wall post.', 'wpwautoposter' ); ?></p>
		</div>

		<div class="form-field">
			<label for="wpw-qs-schedule"><?php esc_html_e( 'Schedule', 'wpwautoposter' ); ?></label>
			<input name="qs_schedule" id="wpw-qs-schedule" type="text" value="<?php echo isset($postdata['qs_schedule']) ? esc_attr($postdata['qs_schedule']) : ''; ?>" />
			<span class="wpw-qs-icon wpw-qs-calander dashicons dashicons-calendar-alt" for="wpw-qs-schedule"></span>
			<span class="wpw-qs-icon wpw-qs-reset-schedule dashicons dashicons-dismiss"></span>
			<p><?php esc_html_e( 'Select the date and time if you want to schedule the post. ', 'wpwautoposter' ); ?></p>
		</div>
	</div>

	<div class="wpw-auto-poster-qs-add-new form-wrap wpw-auto-poster-card">
		<h1><?php esc_html_e( 'Networks', 'wpwautoposter' ); ?></h1>

		<div class="form-field">
			<?php
			$socials = $model->wpw_auto_poster_get_social_type_name();
			if( isset( $socials['ins'] ) ){
				unset($socials['ins']);
			}
			
			if( isset( $socials['ba'] ) ){
				unset($socials['ba']);
			}

			$selected_social = isset( $postdata['enable_socials'] ) ? $postdata['enable_socials'] : array();

			foreach( $socials as $key => $social ) { ?>
				<div class="wpw-auto-poster-qs-social-field wpw-auto-poster-panel">
					<label class="wpw-auto-poster-panel-title" for="wpw-enable-social-<?php printf( $key ); ?>">
						<span class="wpw-qs-social-icons <?php print strtolower($social);?>"></span>
						<span><?php printf( $social ); ?></span>
						<div class="wpw-auto-poster-switch">
							<input type="checkbox" id="wpw-enable-social-<?php printf( $key ); ?>" name="enable_socials[]" value="<?php printf( $key ); ?>" <?php checked(in_array($key, $selected_social), true); ?> />
							<label for="wpw-enable-social-<?php printf( $key ); ?>"></label>
						</div>
					</label>

					<div class="wpw-auto-poster-panel-content" <?php if(in_array($key , $selected_social)) echo "style='display:block;'"; ?>>
					<?php
						$options = array();
						$posting_type = array();
						switch( $key ) {
							case 'fb': // Get facebook account details
								$options = wpw_auto_poster_get_fb_accounts('all_accounts');
								if( !empty($options) ) {
									foreach( $options as $fb_user_key => $fb_user ) {
										$temp_check = explode('|', $fb_user_key);
										if( isset($temp_check[0]) && isset($temp_check[1]) && $temp_check[0] == $temp_check[1] ) {
											unset($options[$fb_user_key]);
										}
									}
								}
								$posting_type = array(
									'key' => $prefix.'fb_share_posting_type',
									'label' => esc_html__( 'Share type', 'wpwautoposter' ),
									'options' => array(
										'link_posting' => esc_html__( 'Link posting', 'wpwautoposter' ),
										'image_posting' => esc_html__( 'Image posting', 'wpwautoposter' ),
									)
								);
							break;
							case 'tw': // Get twitter account details
								$options = get_option( 'wpw_auto_poster_tw_account_details', array() );
							break;
							case 'li': // Get linkedin account details
								$options = $liposting->wpw_auto_poster_get_profiles_data();
							break;
							case 'tb': // Get tumbler account details
								$options = wpw_auto_poster_get_tb_accounts();
								$posting_type = array(
									'key' => $prefix.'tb_posting_type',
									'label' => esc_html__( 'Posting Type', 'wpwautoposter' ),
									'options' => array(
										'photo' => esc_html__( 'As Photo', 'wpwautoposter' ),
										'text' => esc_html__( 'As Text', 'wpwautoposter' ),
										'link' => esc_html__( 'As Link', 'wpwautoposter' ),
									)
								);
							break;
							case 'yt': // Get youtube account details
								$yt_sess_data = get_option('wpw_auto_poster_yt_sess_data');
								if( !empty($yt_sess_data) ) {
									foreach( $yt_sess_data as $keys => $yt_account ) {
										$options[$yt_account['wpw_auto_poster_yt_cache']['id']] = trim( $yt_account['wpw_auto_poster_yt_cache']['id'] );
									}
								}
							break;
							case 'pin': // Get pinterest account details
								$options = wpw_auto_poster_get_pin_accounts('all_accounts');
							break;
							case 'gmb': // Get googlemybusiness account details
								$options = wpw_auto_poster_get_gmb_accounts_location();
								$posting_type = array(
									'key' => $prefix.'gmb_add_buttons',
									'label' => esc_html__( 'Button type', 'wpwautoposter' ),
									'options' => array(
										'LEARN_MORE' => esc_html__( 'Learn more', 'wpwautoposter' ),
										'BOOK' => esc_html__( 'Book', 'wpwautoposter' ),
										'ORDER' => esc_html__( 'Order online', 'wpwautoposter' ),
										'SHOP' => esc_html__( 'Buy', 'wpwautoposter' ),
										'SIGN_UP' => esc_html__( 'Sign up', 'wpwautoposter' ),
										'CALL' => esc_html__( 'Call', 'wpwautoposter' ),
									)
								);
							break;
							case 'reddit': // Get reddit account details
								$options = wpw_auto_poster_get_reddit_accounts_with_subreddits();
								$posting_type = array(
									'key' => $prefix.'reddit_posting_type',
									'label' => esc_html__( 'Posting Type', 'wpwautoposter' ),
									'options' => array(
										'self' => esc_html__( 'Text', 'wpwautoposter' ),
										'link' => esc_html__( 'Link', 'wpwautoposter' ),
										'image' => esc_html__( 'Photo', 'wpwautoposter' ),
									)
								);
							break;
							case 'tele': // Get telegram account details
								$tele_bots = wpw_auto_poster_get_tele_chats();
								if( !empty($tele_bots) ) {
									foreach( $tele_bots as $bkey => $bot ) {

										if( empty($bot['chats']) || ! is_array($bot['chats']) ) continue;

										foreach( $bot['chats'] as $ckey => $chat ) {
											if( empty($chat['id']) ) continue;

											$chTitle = isset( $chat['title'] ) ? $chat['title'] : '';
											if( empty($chTitle) && !empty($chat['name']) ) {
												$chTitle = $chat['name'];
											}

											$options[$bot['token'] . '|' . $ckey] = $bot['boat'] . ' | ' . $chTitle;
										}
									}
								}
								$posting_type = array(
									'key' => $prefix.'tele_post_msgtype',
									'label' => esc_html__( 'Posting Type', 'wpwautoposter' ),
									'options' => array(
										'text' => esc_html__( 'Text Message', 'wpwautoposter' ),
										'photo' => esc_html__( 'Image Post', 'wpwautoposter' ),
									)
								);
							break;
							case 'medium':
								$options = wpw_auto_poster_get_medium_accounts_with_publications();
							break;
							case 'wp':
								$wpAllSites = get_option( 'wpw_auto_poster_wordpress_sites', array() );

								foreach( $wpAllSites as $sitekey => $site ) {

									$site['password'] = base64_decode( $site['password'] );
									$postTypes = $wpposting->wpw_auto_poster_get_site_post_types( $site );


									if( ! $postTypes || empty($postTypes) ) continue;

									foreach( $postTypes as $postType ) {
										$options[ $sitekey . ':' . $postType['name'] ] = $site['name'] . ' - ' . $postType['label'];
									}
								}
							break;
							case 'insta': // Get instagram account details
								$options = wpw_auto_poster_get_insta_accounts('all_accounts');
								if( !empty($options) ) {
									foreach( $options as $insta_user_key => $insta_user ) {
										$temp_check = explode('|', $insta_user_key);

										if( isset($temp_check[0]) && isset($temp_check[1]) && $temp_check[0] == $temp_check[1] ) {
											unset($options[$insta_user_key]);
										}
									}
								}
							break;
						} 
					?>

					<div class="wpw-qs-fields">
						<label class="wpw-qs-social-<?php printf( $key ); ?> wpw-qs-social-label">
							<?php esc_html_e( 'Select Accounts', 'wpwautoposter' ); ?>
						</label>

						<?php if (!empty($key) && $key == 'reddit') {  ?>
							<select name="qs_accounts_<?php printf( $key ); ?>[]" id="wpw-qs-social-<?php printf( $key ); ?>" class="wpw-qs-account-select" multiple>	
								<?php if (!empty($options) && is_array($options))  {
									$selected = isset($postdata['qs_accounts_'.$key]) ? $postdata['qs_accounts_'.$key] : array();
									foreach($options as $aval_key => $aval_data) {
										$main_account_details = explode('|', $aval_data['main-account']);
										$main_account_name = !empty( $main_account_details[1] ) ? $main_account_details[1] : '';		 
								 ?>
								 <optgroup label="<?php echo esc_attr($main_account_name); ?>" >
								 		<option value="<?php echo esc_attr($aval_data['main-account']); ?>" <?php selected(in_array($aval_data['main-account'] , $selected), true, false ); ?> ><?php echo esc_attr($main_account_name); ?></option>		
								 		<?php if (!empty($aval_data['subreddits']) && is_array($aval_data['subreddits'])) { 
								 			foreach($aval_data['subreddits'] as $sr_key => $sr_data) { ?>
								 				<option value="<?php echo esc_attr($sr_key); ?>" <?php selected(in_array($sr_key, $selected), true, false ); ?> ><?php echo esc_attr($sr_data); ?></option>
								 			<?php }
								 		} 
								 		?>
								 </optgroup>	
								<?php }
									}	    
								?>	
							</select>	
						<?php } else { ?>	

						<select name="qs_accounts_<?php printf( $key ); ?>[]" id="wpw-qs-social-<?php printf( $key ); ?>" class="wpw-qs-account-select" multiple>

							<?php
							$selected = isset($postdata['qs_accounts_'.$key]) ? $postdata['qs_accounts_'.$key] : array();

							foreach( $options as $keyv => $option ) {
								echo '<option value="' . esc_attr($keyv) . '" ' . selected( in_array($keyv, $selected), true, false ) . '>' . esc_html($option) . '</option>';
							} ?>
						</select>
					    <?php } ?>
					</div>

					<?php
						if( "tw" == $key ){
							echo "<b>Tweet can contain up to 280 characters or Unicode glyphs.</b>";
						}
					?>

					<?php if( !empty( $posting_type ) ) { ?>
						<div class="wpw-qs-fields">
							<label class="wpw-qs-social-label <?php printf( $posting_type['key'] ); ?>"><?php echo esc_html( $posting_type['label'] ); ?></label>
							<select name="<?php print $posting_type['key']; ?>" id="<?php print $posting_type['key']; ?>" class="wpw-qs-posting-type" >
								<?php
								$selected = isset($postdata[$posting_type['key']]) ? $postdata[$posting_type['key']] : '';

								foreach( $posting_type['options'] as $op_key => $op_value ) {
									echo '<option value="'.esc_attr($op_key).'" ' . selected( $op_key, $selected, false ) . '>'.esc_html($op_value).'</option>';
								} ?>
							</select>
						</div>
					<?php } ?>

					</div>
				</div>
			<?php } ?>
		</div>

		<?php echo apply_filters ( 'wpweb_auto_poster_save_settings', get_submit_button( __( 'Publish Post', 'wpwautoposter' ), 'primary wpw-auto-poster-post-save', 'save' ) ); ?>
	</div>
</form>