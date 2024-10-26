<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Youtube Settings
 *
 * The html markup for the Youtube settings tab.
 *
 * @package Social Auto Poster - You Tube
 * @since 2.6.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_yt_posting;

// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['yt_posting_cats'] ) ? $wpw_auto_poster_options['yt_posting_cats']: 'exclude';

$ytposting = $wpw_auto_poster_yt_posting;

$youtube_keys = isset( $wpw_auto_poster_options['yt_keys'] ) ? $wpw_auto_poster_options['yt_keys'] : array();

$wpw_auto_poster_yt_sess_data = get_option( 'wpw_auto_poster_yt_sess_data' );

$yt_wp_pretty_url = ( !empty( $wpw_auto_poster_options['yt_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['yt_wp_pretty_url'] : '';

$yt_selected_shortner = isset( $wpw_auto_poster_options['yt_url_shortener'] ) ? $wpw_auto_poster_options['yt_url_shortener'] : '';

$yt_wp_pretty_url = !empty( $yt_wp_pretty_url ) ? ' checked="checked"' : '';
$yt_wp_pretty_url_css = ( $yt_selected_shortner == 'wordpress' ) ? ' display:table-row': ' display:none';

// get url shortner service list array 
$yt_url_shortener = $model->wpw_auto_poster_get_shortner_list();

$yt_exclude_cats = array();

$error_msgs = array();
$readonly = "";

$yt_custom_msg_options = isset( $wpw_auto_poster_options['yt_custom_msg_options'] ) ? $wpw_auto_poster_options['yt_custom_msg_options'] : 'global_msg';

if( $yt_custom_msg_options == 'global_msg') {
	$post_msg_style = "display:none";
	$global_msg_style = "";
} else{
	$global_msg_style = "display:none";
	$post_msg_style = "";
}

if(empty($youtube_keys)){
	$hide_button = "display:none";
}else{
	$hide_button = "display:block";
}
?>

<!-- beginning of the youtube general settings meta box -->
<div id="wpw-auto-poster-youtube-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="youtube_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle">
					<span class="verticle-align-top"><?php esc_html_e( 'YouTube General Settings', 'wpwautoposter' ); ?></span>
				</h3>

				<div class="inside">
				<div class="wpw-auto-poster-error info"><ul><li><?php print sprintf(esc_html__( 'Note:  Youtube data API allows only 10K units (5 videos) per day. For more details %sclick here%s.', 'wpwautoposter' ), '<a href="'.esc_url('https://developers.google.com/youtube/v3/getting-started#quota').'" target="_blank">','</a>' ); ?></li></ul></div>
					<table class="form-table">											
						<tbody>				
							<tr valign="top">
								<th scope="row">
									<label for="wpw_auto_poster_options[enable_youtube]"><?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input name="wpw_auto_poster_options[enable_youtube]" id="wpw_auto_poster_options[enable_youtube]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_youtube'] ) ) { checked( '1', $wpw_auto_poster_options['enable_youtube'] ); } ?> />
									<p><small><?php esc_html_e( 'Check this box, if you want to automatically post your new content to YouTube.', 'wpwautoposter' ); ?></small></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="wpw_auto_poster_options[enable_youtube_for]"><?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<ul>
										<?php 
										$all_types = get_post_types( array( 'public' => true ), 'objects');
										$all_types = is_array( $all_types ) ? $all_types : array();

										if( !empty( $wpw_auto_poster_options['enable_youtube_for'] ) ) {
											$prevent_meta = $wpw_auto_poster_options['enable_youtube_for'];
										} else {
											$prevent_meta = array();
										}

										if( !empty( $wpw_auto_poster_options['yt_post_type_tags'] ) ) {
											$yt_post_type_tags = $wpw_auto_poster_options['yt_post_type_tags'];
										} else {
											$yt_post_type_tags = array();
										}

										$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

										if( !empty( $wpw_auto_poster_options['yt_post_type_cats'] ) ) {
											$yt_post_type_cats = $wpw_auto_poster_options['yt_post_type_cats'];
										} else {
											$yt_post_type_cats = array();
										}

											// Get saved categories for youtube to exclude from posting
										if( !empty( $wpw_auto_poster_options['yt_exclude_cats'] ) ) {
											$yt_exclude_cats = $wpw_auto_poster_options['yt_exclude_cats'];
										} 

										foreach( $all_types as $type ) {	

											if( !is_object( $type ) ) continue;															
											if( isset( $type->labels ) ) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
											}
											else {
												$label = $type->name;
											}

												if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
												$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : '';
												?>

												<li class="wpw-auto-poster-prevent-types">
													<input type="checkbox" id="wpw_auto_posting_youtube_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_youtube_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo esc_attr($selected); ?>/>

													<label for="wpw_auto_posting_youtube_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_attr($label); ?></label>
												</li>

											<?php	} ?>
										</ul>
										<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to YouTube when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[yt_post_type_tags][]"><?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[yt_post_type_tags][]" id="wpw_auto_poster_options[yt_post_type_tags]" class="yt_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
											<?php foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;	

												if(in_array( $type->name, $prevent_meta )) {

													if( isset( $type->labels ) ) {
														$label = $type->labels->name ? $type->labels->name : $type->name;
													}
													else {
														$label = $type->name;
													}

														if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );

														echo '<optgroup label="'.esc_attr($label).'">';
										                // Loop on all taxonomies
														foreach ( $all_taxonomies as $taxonomy ) {

															$selected = '';

															if( !empty( $static_post_type_arr[$type->name] ) && $static_post_type_arr[$type->name] != $taxonomy->name){
																continue;
															}

															if(isset($yt_post_type_tags[$type->name]) && !empty($yt_post_type_tags[$type->name])) {
																$selected = ( in_array( $taxonomy->name, $yt_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
															}
															if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

																echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_attr($taxonomy->label).'</option>';
															}
														}
														echo '</optgroup>';
													}
												}?>
											</select>
											<div class="wpw-ajax-loader"><img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/ajax-loader.gif";?>"/></div>
											<p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_post_type_cats][]"><?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?></label> 
										</th>
										<td class="wpw-auto-poster-select">
											<select name="wpw_auto_poster_options[yt_post_type_cats][]" id="wpw_auto_poster_options[yt_post_type_cats]" class="yt_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
												<?php foreach( $all_types as $type ) {	

													if( !is_object( $type ) ) continue;	

													if(in_array( $type->name, $prevent_meta )) {														
														if( isset( $type->labels ) ) {
															$label = $type->labels->name ? $type->labels->name : $type->name;
														}
														else {
															$label = $type->name;
														}

														if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );

														echo '<optgroup label="'.esc_attr($label).'">';
										                // Loop on all taxonomies
														foreach ($all_taxonomies as $taxonomy){

															$selected = '';
															if(isset($yt_post_type_cats[$type->name]) && !empty($yt_post_type_cats[$type->name])) {
																$selected = ( in_array( $taxonomy->name, $yt_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
															}
															if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

																echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
															}
														}
														echo '</optgroup>';
													}
												}?>
											</select>
											<div class="wpw-ajax-loader"><img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/ajax-loader.gif";?>"/></div>
											<p><small><?php esc_html_e( 'Select the Categories for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
										</td>
									</tr>	
									<tr valign="top">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_exclude_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
										</th>
										<td class="wpw-auto-poster-select">
											<div class="wpw-auto-poster-cats-option">
												<input name="wpw_auto_poster_options[yt_posting_cats]" id="yt_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
												<label for="yt_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
												<input name="wpw_auto_poster_options[yt_posting_cats]" id="yt_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
												<label for="yt_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
											</div>
											<select name="wpw_auto_poster_options[yt_exclude_cats][]" id="wpw_auto_poster_options[yt_exclude_cats]" class="yt_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">

											<?php

												$yt_exclude_cats_selected_values = !empty($wpw_auto_poster_options['yt_exclude_cats']) ? $wpw_auto_poster_options['yt_exclude_cats'] : array();
												$selected = 'selected="selected"';

												if(!empty($yt_exclude_cats_selected_values)) {

													foreach ($yt_exclude_cats_selected_values as $post_type => $post_data) {
														
														if( !empty( $post_data ) && is_array( $post_data ) ){
															
															foreach( $post_data as $key => $cat_data ) {
																
																$term              = get_term( $cat_data );
																$get_taxonomy_data = get_taxonomy( $term->taxonomy );
																$cat_name          = $get_taxonomy_data->label." : ".$term->name;
																echo '<option value="' . esc_attr($post_type) . "|" . esc_attr($cat_data) . '" ' . esc_attr($selected) . '>' . esc_html($cat_name) . '</option>';                                                    
																
																
															}

														}

													}    

												}
											?>

											</select>
											<p><small><?php esc_html_e( 'Select the Taxonomies for each post type that you want to include or exclude for posting.', 'wpwautoposter' ); ?></small></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_url_shortener]"><?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?></label> 
										</th>
										<td>
											<select name="wpw_auto_poster_options[yt_url_shortener]" id="wpw_auto_poster_options[yt_url_shortener]" class="yt_url_shortener" data-content='yt'>
												<?php
												foreach ( $yt_url_shortener as $key => $option ) { ?>
													<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $yt_selected_shortner, $key ); ?>>
														<?php esc_html_e( $option ); ?>
													</option>
													<?php
												}
												?>
											</select>
											<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
										</td>
									</tr>



									<tr id="row-yt-wp-pretty-url" valign="top" style="<?php print esc_attr($yt_wp_pretty_url_css);?>">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_wp_pretty_url]"><?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?></label> 
										</th>
										<td>
											<input type="checkbox" name="wpw_auto_poster_options[yt_wp_pretty_url]" id="wpw_auto_poster_options[yt_wp_pretty_url]" class="yt_wp_pretty_url" data-content='yt' value="yes" <?php print esc_attr($yt_wp_pretty_url);?>>
											<p><small><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></small></p>
										</td>
									</tr>

									<?php	        
									$class = $shortest_class = ' display:none;';
									if( $yt_selected_shortner == 'bitly' ) {	        		
										$class = '';	        		
									} else if( $yt_selected_shortner == 'shorte.st' ) {
										$shortest_class = '';	        		
									} ?>

									<tr valign="top" class="yt_setting_input_bitly" style="<?php echo esc_attr($class); ?>">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_bitly_access_token]"><?php esc_html_e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
										</th>
										<td>
											<input type="text" name="wpw_auto_poster_options[yt_bitly_access_token]" id="wpw_auto_poster_options[yt_bitly_access_token]" value="<?php echo ( isset( $wpw_auto_poster_options['yt_bitly_access_token'] ) ) ? $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['yt_bitly_access_token'] ) : ''; ?>" class="large-text">
										</td>
									</tr>

									<tr valign="top" class="yt_setting_input_shortest" style="<?php echo esc_attr($shortest_class); ?>">
										<th scope="row">
											<label for="wpw_auto_poster_options[yt_shortest_api_token]"><?php esc_html_e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
										</th>
										<td>
											<input type="text" name="wpw_auto_poster_options[yt_shortest_api_token]" id="wpw_auto_poster_options[yt_shortest_api_token]" value="<?php echo ( isset( $wpw_auto_poster_options['yt_shortest_api_token'] )) ? $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['yt_shortest_api_token'] ) : ''; ?>" class="large-text">
										</td>
									</tr>

									<?php
									echo apply_filters ( 
										'wpweb_yt_settings_submit_button', 
										'<tr valign="top">
										<td colspan="2">
										<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
										</tr>'
									);
									?>
								</tbody>
							</table>

						</div><!-- .inside -->

					</div><!-- #youtube_general -->
				</div><!-- .meta-box-sortables ui-sortable -->
			</div><!-- .metabox-holder -->
		</div><!-- #wpw-auto-poster-youtube-general -->
		<!-- end of the youtube general settings meta box -->

		<!-- beginning of the youtube api settings meta box -->
		<div id="wpw-auto-poster-youtube-api" class="post-box-container">
			<div class="metabox-holder">	
				<div class="meta-box-sortables ui-sortable">
					<div id="youtube_api" class="postbox">	
						<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

						<h3 class="hndle"><span class="verticle-align-top">
							<?php esc_html_e( 'YouTube API Settings', 'wpwautoposter' ); ?>
						</span></h3>

						<div class="inside">
							<table class="form-table wpw-auto-poster-youtube-settings">
								<tbody>				
									<tr valign="top">
										<td colspan="4">
											<div class="wpw-auto-poster-error info">
		                                        <ul>
		                                            <li>
		                                                <?php esc_html_e( 'Before you can start publishing your content to YouTube you need to create Google Application.', 'wpwautoposter' ); ?>
		                                                <?php printf( esc_html__('You can get a step by step tutorial on how to create a Google Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="'.esc_url('https://docs.Michael Tallada/social-network-integration/youtube/').'" target="_blank">', '</a>' ); ?>

		                                            </li>
		                                        </ul>
		                                    </div>  
										</td>
									</tr>

									<tr><td class="no-padding" colspan="4">
		                                <table class="wpw-auto-poster-form-table-resposive">
		                                    <thead><tr valign="top">
											<td scope="row"><strong>
												<label for="wpw_auto_poster_options[yt_keys][0][app_id]"><?php _e( 'YouTube App ID/API Key', 'wpwautoposter' ); ?></label>
											</strong></td>
											<td scope="row"><strong>
												<label for="wpw_auto_poster_options[yt_keys][0][app_secret]"><?php _e( 'Youtube App Secret', 'wpwautoposter' ); ?></label>
											</strong></td>
											<td scope="row"><strong>
												<label><?php esc_html_e('Valid OAuth redirect URIs', 'wpwautoposter'); ?></label>
											</strong></td>
											<td scope="row"><strong>
												<label><?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?></label>
											</strong></td>  
											<td></td>
										</tr></thead>

										<tbody>
										<?php
										if( !empty( $youtube_keys ) ) {
											foreach ( $youtube_keys as $youtube_key => $youtube_value ) {

												// Don't disply delete link for first row
												$youtube_delete_class = empty( $youtube_key ) ? '' : ' wpw-auto-poster-display-inline '; ?>

												<tr valign="top" class="wpw-auto-poster-youtube-account-details" data-row-id="<?php echo esc_attr($youtube_key); ?>">
													<td scope="row" width="25%" data-label="<?php _e( 'YouTube App ID/API Key', 'wpwautoposter' ); ?>">
														<input type="text" name="wpw_auto_poster_options[yt_keys][<?php echo esc_attr($youtube_key); ?>][app_id]" value="<?php echo $model->wpw_auto_poster_escape_attr( $youtube_value['app_id'] ); ?>" class="large-text wpw-auto-poster-youtube-app-id" <?php echo esc_attr($readonly);?>/>
														<p><small><?php esc_html_e( 'Enter YouTube App ID / API Key', 'wpwautoposter' ); ?></small></p>  
													</td>
													<td scope="row" width="25%" data-label="<?php _e( 'Youtube App Secret', 'wpwautoposter' ); ?>">
														<input type="text" name="wpw_auto_poster_options[yt_keys][<?php echo esc_attr($youtube_key); ?>][app_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr( $youtube_value['app_secret'] ); ?>" class="large-text wpw-auto-poster-youtube-app-secret" <?php echo esc_attr($readonly);?>/>
														<p><small><?php esc_html_e( 'Enter YouTube App Secret.', 'wpwautoposter' ); ?></small></p>  
													</td>
													<td scope="row" width="25%" valign="top" data-label="<?php _e( 'Valid OAuth redirect URIs', 'wpwautoposter' ); ?>">
														<?php
														$site_url =  site_url().'/';                                        
														$valid_auto_redirect_url = add_query_arg( array( 'wpwautoposter' => 'youtube', 'wpw_yt_app_id' => esc_attr( stripslashes( $youtube_value['app_id'] ) ) ), $site_url ); ?>
														<input class="yt-oauth-url" id="yt-oauth-url-<?php print esc_attr($youtube_value['app_id']);?>" type="text" value="<?php echo esc_attr($valid_auto_redirect_url); ?>" size="30" readonly/><button type="button" data-appid="<?php print esc_attr($youtube_value['app_id']);?>" class="button yt-copy-clipboard"><?php esc_html_e('Copy', 'wpwautoposter'); ?></button>
														<p><small><?php esc_html_e('Copy and paste it to Valid OAuth redirect URIs in YouTube apps.', 'wpwautoposter'); ?></small></p>
													</td>
													<td scope="row" width="25%" valign="top" class="wpw-grant-reset-data" data-label="<?php _e( 'Allowing permissions', 'wpwautoposter' ); ?>">
														<?php
														if( !empty( $youtube_value['app_id'] ) && !empty( $youtube_value['app_secret'] ) && !empty( $wpw_auto_poster_yt_sess_data[ $youtube_value['app_id'] ] ) ) {

															echo '<p>' . esc_html__( 'You already granted extended permissions.', 'wpwautoposter' ) . '</p>';	
															echo apply_filters ( 'wpweb_yt_settings_reset_session', sprintf(
																esc_html__( "%s Reset User Session %s", 'wpwautoposter' ),
																"<a href=' " . add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'yt_reset_user' => '1', 'wpw_yt_app' => $youtube_value['app_id'] ), admin_url( 'admin.php' ) ) . "'>",
																"</a>"
															) );
														} elseif( !empty( $youtube_value['app_id'] ) && !empty( $youtube_value['app_secret'] ) ) {

															echo '<p><a href="' . esc_url( $ytposting->wpw_auto_poster_get_yt_login_url( $youtube_value['app_id'] ) ) . '">' . esc_html__( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
														} ?>
													</td>

													<td>
														<a href="javascript:void(0);" class="wpw-auto-poster-delete-yt-account wpw-auto-poster-youtube-remove <?php echo esc_attr($youtube_delete_class); ?>" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
													</td>
												</tr>
											<?php 
											}
										} else { ?>
											<tr valign="top" class="wpw-auto-poster-youtube-account-details" data-row-id="<?php echo empty($youtube_key) ? '': esc_attr($youtube_key); ?>">
												<td scope="row" width="30%" data-label="<?php _e( 'YouTube App ID/API Key', 'wpwautoposter' ); ?>">
													<input type="text" name="wpw_auto_poster_options[yt_keys][0][app_id]" value="" class="large-text wpw-auto-poster-youtube-username" />
													<p><small><?php esc_html_e( 'Enter Google App ID.', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td scope="row" width="30%" data-label="<?php _e( 'Youtube App Secret', 'wpwautoposter' ); ?>">
													<input type="password" name="wpw_auto_poster_options[yt_keys][0][app_secret]" value="" class="large-text wpw-auto-poster-youtube-password" />
													<p><small><?php esc_html_e( 'Enter Google Secret', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td style="<?php echo esc_attr($hide_button); ?>">
													<a href="javascript:void(0);" class="wpw-auto-poster-delete-yt-account wpw-auto-poster-youtube-remove" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo WPW_AUTO_POSTER_META_URL; ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
												</td>
											</tr>
										<?php } ?>
										</tbody>
									</table></td>
									</tr>

									<?php $yt_api_add_more = apply_filters( 'wpweb_yt_api_add_more_button', true ); ?>
                        			<?php if( $yt_api_add_more ){ ?>
										<tr>
											<td colspan="4">
												<a class='wpw-auto-poster-add-more-yt-account button' href='javascript:void(0);'><?php esc_html_e( 'Add Another App', 'wpwautoposter' ); ?></a>
											</td>
										</tr>
									<?php } ?>

									<?php
									echo apply_filters ( 
										'wpweb_yt_settings_submit_button', 
										'<tr valign="top">
										<td colspan="4">
										<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
										</tr>'
									);
									?>
								</tbody>
							</table>

						</div><!-- .inside -->

					</div><!-- #youtube_api -->
				</div><!-- .meta-box-sortables ui-sortable -->
			</div><!-- .metabox-holder -->
		</div><!-- #wpw-auto-poster-youtube-api -->
		<!-- end of the youtube api settings meta box -->

		<!-- beginning of the autopost to youtube meta box -->
		<div id="wpw-auto-poster-autopost-youtube" class="post-box-container">
			<div class="metabox-holder">	
				<div class="meta-box-sortables ui-sortable">
					<div id="autopost_youtube" class="postbox">	
						<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

						<h3 class="hndle">
							<span class="verticle-align-top"><?php esc_html_e( 'Autopost to YouTube', 'wpwautoposter' ); ?></span>
						</h3>

						<div class="inside">

							<table class="form-table">											
								<tbody>

									<tr valign="top"> 
										<th scope="row">
											<label for="wpw_auto_poster_options[prevent_post_yt_metabox]"><?php esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?></label>
										</th>									
										<td>
											<input name="wpw_auto_poster_options[prevent_post_yt_metabox]" id="wpw_auto_poster_options[prevent_post_yt_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_yt_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_yt_metabox'] ); } ?> />
											<p><small><?php esc_html_e( 'If you check this box, then it will hide meta settings for youtube from individual posts.', 'wpwautoposter' ); ?></small></p>
										</td>	
									</tr>

									<?php

									$types = get_post_types( array( 'public'=>true ), 'objects' );
									$types = is_array( $types ) ? $types : array();
									?>
									<tr valign="top">
										<th scope="row">
											<label><?php esc_html_e( 'Map Autopost Location : ', 'wpwautoposter' ); ?></label>
										</th>
										<td>

											<?php

												// Getting all youtube accounts
											$yt_accounts = $ytposting->wpw_auto_poster_get_profiles_data();

											foreach( $types as $type ) {

												if( !is_object( $type ) ) continue;

												if( isset( $type->labels ) ) {
													$label = $type->labels->name ? $type->labels->name : $type->name;
												}
												else {
													$label = $type->name;
												}

													if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
													?>
													<div class="wpw-auto-poster-fb-types-wrap">
														<div class="wpw-auto-poster-fb-types-label">
															<?php	esc_html_e( 'Autopost', 'wpwautoposter' ); 
															echo ' '.esc_html($label); 
															esc_html_e( ' to YouTube', 'wpwautoposter' ); 
															?>
														</div><!--.wpw-auto-poster-fb-types-label-->
														
														<div class="wpw-auto-poster-fb-user-label">
															<?php esc_html_e( 'of this user', 'wpwautoposter' ); ?>(<?php esc_html_e( 's', 'wpwautoposter' );?>)
														</div><!--.wpw-auto-poster-fb-user-label-->
														<div class="wpw-auto-poster-yt-users-acc">
															<?php
															if( isset( $wpw_auto_poster_options['yt_type_'.$type->name.'_user'] ) ) {
																$wpw_auto_poster_yt_type_user = $wpw_auto_poster_options['yt_type_'.$type->name.'_user'];	 
															} else {
																$wpw_auto_poster_yt_type_user = '';
															}

															$wpw_auto_poster_yt_type_user = ( array ) $wpw_auto_poster_yt_type_user;

															?>
															<select name="wpw_auto_poster_options[yt_type_<?php echo esc_attr($type->name); ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
																<?php
																if( !empty($yt_accounts) && is_array($yt_accounts) ) {
																	
																	foreach( $yt_accounts as $aid => $aval ) {

																		if( is_array( $aval ) ) { 
																			$value = $aval['app_id']."|".$aval['app_secret'];
																			?>
																			<option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $wpw_auto_poster_yt_type_user ), true, true ); ?>><?php echo esc_attr($aval); ?></option>
																			
																		</optgroup>

																	<?php	} else { ?>
																		<option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $wpw_auto_poster_yt_type_user ), true, true ); ?> ><?php echo esc_attr($aval); ?></option>
																	<?php 	}
																	
																	} // End of foreach
																} // End of main if
																?>
															</select>
														</div><!--.wpw-auto-poster-fb-users-acc-->
													</div><!--.wpw-auto-poster-fb-types-wrap-->
												<?php } ?>

											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
												<label><?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input id="yt_custom_global_msg" type="radio" name="wpw_auto_poster_options[yt_custom_msg_options]" value="global_msg" <?php checked($yt_custom_msg_options, 'global_msg', true);?> class="custom_msg_options">
												<label for="yt_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>

												<input id="yt_custom_post_msg" type="radio" name="wpw_auto_poster_options[yt_custom_msg_options]" value="post_msg" <?php checked($yt_custom_msg_options, 'post_msg', true);?> class="custom_msg_options">
												<label for="yt_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
											</td>	
										</tr>

										<tr valign="top" style="<?php echo esc_attr($global_msg_style); ?>" class="global_msg_tr">
											<th scope="row">
												<label for="wpw_auto_poster_options_yt_custom_img"><?php esc_html_e( 'Post Video : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<?php
												$yt_custom_img = isset( $wpw_auto_poster_options['yt_custom_img'] ) ? $wpw_auto_poster_options['yt_custom_img'] : ''; ?>
												<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $yt_custom_img ); ?>" name="wpw_auto_poster_options[yt_custom_img]" id="wpw_auto_poster_options_yt_custom_img" class="large-text wpw-auto-poster-img-field">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button youtube" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Video','wpwautoposter' );?>" />
												<p><small><?php esc_html_e( 'Here you can upload a default video which will be used for the YouTube post.', 'wpwautoposter' ); ?></small></p><br>
											</td>	
										</tr>

										<tr valign="top" class="global_title_yt <?php echo esc_attr($global_title_style); ?>">									
											<th scope="row">
												<label for="wpw_auto_poster_options[yt_global_title_template]"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
											</th>
											<td class="form-table-td">
												<?php 				
												$yt_template_title_text = ( !empty( $wpw_auto_poster_options['yt_global_title_template'] ) ) ? $wpw_auto_poster_options['yt_global_title_template'] : '';
												?>	
												<input type="text" name="wpw_auto_poster_options[yt_global_title_template]" id="wpw_auto_poster_options[yt_global_title_template]" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $yt_template_title_text ); ?>">
											</td>	
											
										</tr>
										<tr valign="top">									
											<th scope="row"></th>
											<td class="global_msg_td">
												<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter' ); ?>
												<?php 
												$yt_template_title_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
												'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
												'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
												'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
												'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
												'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
												'<br /><b><code>{post_type}</code></b> - ' . esc_html__(' displays the post type.', 'wpwautoposter') .
												'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
												'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter').
												'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter').
												'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter').
												'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter').
												'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>").
												'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>");
												print $yt_template_title_str;
												?>
												</small></p>
											</td>			
										</tr>

										<tr valign="top" style="<?php echo esc_attr($global_msg_style); ?>" class="global_msg_tr">									
											<th scope="row">
												<label for="wpw_auto_poster_options[yt_template]"><?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?></label>
											</th>
											<td class="form-table-td">
												<?php
												$yt_template = isset( $wpw_auto_poster_options['yt_template'] ) ? $wpw_auto_poster_options['yt_template'] : ''; ?>

												<textarea name="wpw_auto_poster_options[yt_template]" id="wpw_auto_poster_options[yt_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $yt_template ); ?></textarea>
											</td>	
										</tr>

										<tr id="custom_post_type_templates_yt" style="<?php echo esc_attr($post_msg_style); ?>" class="post_msg_tr">
											<th colspan="2" class="form-table-td">
												<ul>
													<?php
													$all_types = get_post_types( array( 'public' => true ), 'objects');
													$all_types = is_array( $all_types ) ? $all_types : array();

													foreach( $all_types as $type ) {	

														if( !is_object( $type ) ) continue;															
														if( isset( $type->labels ) ) {
															$label = $type->labels->name ? $type->labels->name : $type->name;
														}
														else {
															$label = $type->name;
														}

												if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
												
												?>
												<li><a href="#tabs-<?php echo esc_attr($type->name); ?>"><?php echo esc_attr($label); ?></a></li>
											<?php } ?>

										</ul>
										<?php 
										foreach( $all_types as $type ) {	

											if( !is_object( $type ) ) continue;															
											if( isset( $type->labels ) ) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
											}
											else {
												$label = $type->name;
											}

											if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

											$yt_template_name = ( isset( $wpw_auto_poster_options['yt_template_'.$type->name] ) ) ? $wpw_auto_poster_options['yt_template_'.$type->name] : '';

											$yt_custom_img = ( isset( $wpw_auto_poster_options['yt_custom_img_'.$type->name] ) ) ? $wpw_auto_poster_options['yt_custom_img_'.$type->name] : ''; ?>

											<table id="tabs-<?php echo esc_attr($type->name); ?>">
												<tr valign="top">
													<th scope="row">
														<label for="wpw_auto_poster_options_yt_custom_img_<?php echo esc_attr($type->name); ?>"><?php esc_html_e( 'Post Video :', 'wpwautoposter' ); ?></label>
													</th>
													<td>
														<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $yt_custom_img ); ?>" name="wpw_auto_poster_options[yt_custom_img_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_yt_custom_img_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field">
														<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Video', 'wpwautoposter' ); ?>" />
														<p><small><?php esc_html_e( 'Here you can upload a default video which will be used for the YouTube post.', 'wpwautoposter' ); ?></small></p><br>

													</td>	
												</tr>

												<tr valign="top">

													<th scope="row">
														<label for="wpw_auto_posting_yt_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message ', 'wpwautoposter'); ?>:</label>
													</th>

													<td class="form-table-td">
														<textarea type="text" name="wpw_auto_poster_options[yt_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_yt_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $yt_template_name ); ?></textarea>
													</td>	
												</tr>
											</table>
										<?php } ?>
									</th>
								</tr>	
								<tr valign="top">									
									<th scope="row"></th>
									<td class="global_msg_td">
										<p><small class="line-height-20"><?php esc_html_e( 'Here you can enter default description of video will be used for the YouTube posting. Leave it empty to use the post level description. You can use following template tags within the caption template:', 'wpwautoposter' ); ?>
										<?php 
										$yt_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
										'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
										'<br /><b><code>{displays_name}</code></b> - ' . esc_html__('displays the displays name.', 'wpwautoposter') .
										'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
										'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
										'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
										'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
										'<br /><b><code>{post_type}</code></b> - ' . esc_html__(' displays the post type.', 'wpwautoposter') .
										'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
										'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter').
										'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter').
										'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter').
										'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter').
										'<br /><b><code>{content-digits}</code></b> - ' .sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'),"<b>", "</b>"
									).
										'<br /><b><code>{CF-CustomFieldName}</code></b> - ' .sprintf(esc_html__('inserts the contents of the custom field with the specified name.  %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>"
									);
										print $yt_template_str;
										?>
									</small></p>
								</td>	
							</tr>

							<?php
							echo apply_filters ( 
								'wpweb_yt_settings_submit_button', 
								'<tr valign="top">
								<td colspan="2">
								<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
								</tr>'
							);
							?>
						</tbody>
					</table>

				</div><!-- .inside -->

			</div><!-- #autopost_youtube -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #ps-poster-autopost-youtube -->
<!-- end of the autopost to youtube meta box -->