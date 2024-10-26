<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Tumblr Settings
 *
 * The html markup for the Tumblr settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_tb_posting;

//model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['tb_posting_cats'] ) ? $wpw_auto_poster_options['tb_posting_cats']: 'exclude';

//tumblr posting class
$tbposting = $wpw_auto_poster_tb_posting;

// get all post methods
$wall_post_methods = $model->wpw_auto_poster_get_tb_posting_method();

$tumblr_keys = isset( $wpw_auto_poster_options['tumblr_keys'] ) ? $wpw_auto_poster_options['tumblr_keys'] : array();

// Getting tumblr app grant data
$wpw_auto_poster_tb_sess_data = get_option( 'wpw_auto_poster_tb_sess_data' ); 

$tb_wp_pretty_url = ( !empty( $wpw_auto_poster_options['tb_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['tb_wp_pretty_url'] : '';

$tb_selected_shortner = isset( $wpw_auto_poster_options['tb_url_shortener'] ) ? $wpw_auto_poster_options['tb_url_shortener'] : '';

$tb_wp_pretty_url = !empty( $tb_wp_pretty_url ) ? ' checked="checked"' : '';
$tb_wp_pretty_url_css = ( $tb_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css': ' ba_wp_pretty_url_css_hide';

// get url shortner service list array 
$tb_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$tb_exclude_cats = array();

$tb_custom_msg_options = isset( $wpw_auto_poster_options['tb_custom_msg_options'] ) ? $wpw_auto_poster_options['tb_custom_msg_options'] : 'global_msg';

if( $tb_custom_msg_options == 'global_msg') {
	$post_msg_style = "global_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
}

?>

<!-- beginning of the tumblr general settings meta box -->
<div id="wpw-auto-poster-tumblr-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="tumblr_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
								
					<h3 class="hndle">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'Tumblr General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_tumblr]"><?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input name="wpw_auto_poster_options[enable_tumblr]" id="wpw_auto_poster_options[enable_tumblr]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_tumblr'] ) ) { checked( '1', $wpw_auto_poster_options['enable_tumblr'] ); } ?> />
										<p><small><?php esc_html_e( 'Check this box, if you want to autopost your content to Tumblr.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>	

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_tumblr_for]"><?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<ul>
										<?php
										
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();

											if( !empty( $wpw_auto_poster_options['enable_tumblr_for'] ) ) {
												$prevent_meta = $wpw_auto_poster_options['enable_tumblr_for'];
											} else {
												$prevent_meta = '';
											}
															
											$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

											if( !empty( $wpw_auto_poster_options['tb_post_type_tags'] ) ) {
												$tb_post_type_tags = $wpw_auto_poster_options['tb_post_type_tags'];
											} else {
												$tb_post_type_tags = array();
											}

											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

											if( !empty( $wpw_auto_poster_options['tb_post_type_cats'] ) ) {
												$tb_post_type_cats = $wpw_auto_poster_options['tb_post_type_cats'];
											} else {
												$tb_post_type_cats = array();
											}

											// Get saved categories for tumblr to exclude from posting
											if( !empty( $wpw_auto_poster_options['tb_exclude_cats'] ) ) {
												$tb_exclude_cats = $wpw_auto_poster_options['tb_exclude_cats'];
											}
														
											foreach ( $all_types as $type ) {	
															
												if ( !is_object( $type ) ) continue;

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
												<input type="checkbox" id="wpw_auto_posting_tumblr_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_tumblr_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo esc_attr($selected); ?>/>
																						
												<label for="wpw_auto_posting_tumblr_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_attr($label); ?></label>
											</li>
											
											<?php	} ?>
										</ul>
										<p><small><?php esc_html_e( 'Check each of the post types you want to automatically post to Tumblr when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_post_type_tags][]"><?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[tb_post_type_tags][]" id="wpw_auto_poster_options[tb_post_type_tags]" class="tb_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if( !empty( $static_post_type_arr[$type->name] ) && $static_post_type_arr[$type->name] != $taxonomy->name){
                             										continue;
                    										}
										                	if(isset($tb_post_type_tags[$type->name]) && !empty($tb_post_type_tags[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $tb_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

										                        echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL)."/ajax-loader.gif";?>"/></div>
										<p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_post_type_cats][]"><?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[tb_post_type_cats][]" id="wpw_auto_poster_options[tb_post_type_cats]" class="tb_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if(isset($tb_post_type_cats[$type->name]) && !empty($tb_post_type_cats[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $tb_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

										                        echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
										<p><small><?php esc_html_e( 'Select the Categories for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_exclude_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<div class="wpw-auto-poster-cats-option">
											<input name="wpw_auto_poster_options[tb_posting_cats]" id="tb_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
											<label for="tb_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
											<input name="wpw_auto_poster_options[tb_posting_cats]" id="tb_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
											<label for="tb_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
										</div>
										<select name="wpw_auto_poster_options[tb_exclude_cats][]" id="wpw_auto_poster_options[tb_exclude_cats]" class="tb_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">
											
										<?php

											$tb_exclude_cats_selected_values = !empty($wpw_auto_poster_options['tb_exclude_cats']) ? $wpw_auto_poster_options['tb_exclude_cats'] : array();
											$selected = 'selected="selected"';

											if(!empty($tb_exclude_cats_selected_values)) {

												foreach ($tb_exclude_cats_selected_values as $post_type => $post_data) {
													
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

								<?php 
								$tumblrcontent = isset( $wpw_auto_poster_options['tumblr_content_type'] ) && !empty( $wpw_auto_poster_options['tumblr_content_type'] ) 
															? $wpw_auto_poster_options['tumblr_content_type'] : ''; ?>
								<tr>
									<th scope="row">
										<label for="wpw_auto_poster_options[tumblr_content_type]"><?php esc_html_e( 'Post Content : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input type="radio" id="tumblr_content_type_full" name="wpw_auto_poster_options[tumblr_content_type]" <?php if( empty( $tumblrcontent ) )  { checked ( '', $tumblrcontent, true ); } ?> value=""/>
										<label for="tumblr_content_type_full" class="wpw-auto-poster-label"><?php esc_html_e( 'Full', 'wpwautoposter' );?></label>
										
										<input type="radio" id="tumblr_content_type_snippets" name="wpw_auto_poster_options[tumblr_content_type]" <?php checked ( '1', $tumblrcontent, true );?> value="1"/>
										<label for="tumblr_content_type_snippets" class="wpw-auto-poster-label"><?php esc_html_e( 'Snippets', 'wpwautoposter' );?></label>
										<p><small><?php esc_html_e( 'Choose whether you want to post the full content or just a snippet to your Tumblr page. if you choose snippets, the plugin will post the first 200 characters from your post. You always have the ability to customize that within the meta box.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_url_shortener]"><?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[tb_url_shortener]" id="wpw_auto_poster_options[tb_url_shortener]" class="tb_url_shortener" data-content='tb'>
											<?php
											foreach( $tb_url_shortener as $key => $option ) { ?>
												<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $tb_selected_shortner, $key ); ?>>
													<?php echo esc_html($option); ?>
												</option>
												<?php
											} ?>
										</select>
										<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								
								<tr id="row-tb-wp-pretty-url" valign="top" class="<?php print esc_attr($tb_wp_pretty_url_css);?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_wp_pretty_url]"><?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<input type="checkbox" name="wpw_auto_poster_options[tb_wp_pretty_url]" id="wpw_auto_poster_options[tb_wp_pretty_url]" class="tb_wp_pretty_url" data-content='tb' value="yes" <?php print esc_attr($tb_wp_pretty_url);?>>
										<p><small><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></small></p>
									</td>
								</tr>

								<?php	        
								if( $tb_selected_shortner == 'bitly' ) {
									$class = '';
								} else {
									$class = 'post_msg_style_hide';
								}
								
								if( $tb_selected_shortner == 'shorte.st' ) {
									$shortest_class = '';
								} else {
									$shortest_class = 'post_msg_style_hide';
								} ?>
								
								<tr valign="top" class="tb_setting_input_bitly <?php echo esc_attr($class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_bitly_access_token]"><?php esc_html_e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[tb_bitly_access_token]" id="wpw_auto_poster_options[tb_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tb_bitly_access_token'] ); ?>" class="large-text">
									</td>
								</tr>
								
								<tr valign="top" class="tb_setting_input_shortest <?php echo esc_attr($shortest_class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[tb_shortest_api_token]"><?php esc_html_e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[tb_shortest_api_token]" id="wpw_auto_poster_options[tb_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tb_shortest_api_token'] ); ?>" class="large-text">
									</td>
								</tr>

								<?php
								echo apply_filters ( 
									'wpweb_fb_settings_submit_button', 
									'<tr valign="top">
										<td colspan="2">
											<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
									</tr>'
								); ?>
							</tbody>
						</table>
										
					</div><!-- .inside -->
									
			</div><!-- #tumblr_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-tumblr-general -->
<!-- end of the tumblr general settings meta box -->

<!-- beginning of the tumblr api settings meta box -->
<div id="wpw-auto-poster-tumblr-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="twitter_api" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw-sap-buffer-app-settings'>
					<?php esc_html_e( 'Tumblr API Settings', 'wpwautoposter' ); ?>
				</span></h3>
								
				<div class="inside">
					<table class="form-table wpw-auto-poster-tumblr-settings">
						<tbody>
							<tr valign="top">
								<td colspan="4">
									<div class="wpw-auto-poster-error info">
                                        <ul>
                                            <li>
                                                <?php esc_html_e( 'Before you can start publishing your content to Tumblr you need to create a Tumblr Application.', 'wpwautoposter' ); ?>
                                                <?php printf( esc_html__('You can get a step by step tutorial on how to create a Tumblr Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="'.esc_url('https://docs.Michael Tallada/social-network-integration/tumblr/').'" target="_blank">', '</a>' ); ?>

                                            </li>
                                        </ul>
                                    </div>  
								</td>
							</tr>

							<tr>
								<th scope="row"><label>
									<?php esc_html_e( 'Allowing permissions : ', 'wpwautoposter' ); ?>
								</label></th>
								<td colspan="3">
									<p><?php esc_html_e( 'Posting content to your chosen Tumblr personal account requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.', 'wpwautoposter' ); ?></p>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<p class="wpw-auto-poster-info-box"><?php printf(esc_html__( '%s Note: %s Please note the Tumblr App, Tumblr profile or page and the user who authorizes the app MUST belong to the same Tumblr account. So please make sure you are logged in to Tumblr as the same user who created the app.', 'wpwautoposter' ), "<b>", "</b>"
								); ?></p>
								</td>
							</tr>

							<tr><td class="no-padding" colspan="4">
                                <table class="wpw-auto-poster-form-table-resposive">
                                    <thead><tr valign="top">
									<td scope="row" width="30%"><strong>
										<label for="wpw_auto_poster_options[tumblr_keys][0][consumer_key]"><?php esc_html_e( 'Tumblr OAuth Consumer Key', 'wpwautoposter' ); ?></label>
									</strong></td>
									<td scope="row" width="30%"><strong>
										<label for="wpw_auto_poster_options[tumblr_keys][0][consumer_secret]"><?php esc_html_e( 'Tumblr Secret Key', 'wpwautoposter' ); ?></label>
									</strong></td>
									<td scope="row" width="30%"><strong>
										<label><?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?></label>
									</strong></td>
									<td></td>
								</tr>
							</thead>

							<tbody>
								<?php
								if( !empty($tumblr_keys) ) {
									foreach( $tumblr_keys as $tumblr_key => $tumblr_value ) {
										
										if( !isset($tumblr_key) ) {
											$tumblr_key = "0";
										}

										// Don't disply delete link for first row
										$tumblr_delete_class = empty( $tumblr_key ) ? '' : ' wpw-auto-poster-display-inline '; ?>

									<tr valign="top" class="wpw-auto-poster-tumblr-account-details" data-row-id="<?php echo esc_attr($tumblr_key); ?>">
										<td scope="row" width="30%" data-label="<?php esc_html_e( 'Tumblr OAuth Consumer Key', 'wpwautoposter' ); ?>">
											<input type="text" name="wpw_auto_poster_options[tumblr_keys][<?php echo esc_attr($tumblr_key); ?>][consumer_key]" value="<?php echo $model->wpw_auto_poster_escape_attr( $tumblr_value['consumer_key']); ?>" class="large-text wpw-auto-poster-tumblr-app-id" />
											<p><small><?php esc_html_e( 'Enter Tumblr App Consumer Key.', 'wpwautoposter' ); ?></small></p>  
										</td>
										<td scope="row" width="30%" data-label="<?php esc_html_e( 'Tumblr Secret Key', 'wpwautoposter' ); ?>">
											<input type="text" name="wpw_auto_poster_options[tumblr_keys][<?php echo esc_attr($tumblr_key); ?>][consumer_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr( $tumblr_value['consumer_secret'] ); ?>" class="large-text wpw-auto-poster-tumblr-app-secret" />
											<p><small><?php esc_html_e( 'Enter Tumblr Consumer Secret.', 'wpwautoposter' ); ?></small></p>  
										</td>
										
										<td scope="row" width="30%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>">
											<?php
											if( !empty($tumblr_value['consumer_key']) && !empty($tumblr_value['consumer_secret']) && !empty($wpw_auto_poster_tb_sess_data[ $tumblr_value['consumer_key'] ]) )  {
												 
												echo '<p>' . esc_html__( 'You already granted extended permissions.', 'wpwautoposter' ) . '</p>';	
												echo apply_filters ( 'wpweb_tb_settings_reset_session', sprintf(
													 esc_html__( "%s Reset User Session %s", 'wpwautoposter' ), 
													 "<a href='".add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'tb_reset_user' => '1', 'wpw_tb_app' => $tumblr_value['consumer_key'] ), admin_url( 'admin.php' ) )."'>",
													 "</a>"
													 ) );
											} elseif( !empty($tumblr_value['consumer_key']) && !empty($tumblr_value['consumer_secret']) ) {
												echo '<p><a href="' . esc_url($tbposting->wpw_auto_poster_get_tb_login_url( $tumblr_value['consumer_key'] )) . '">' . esc_html__( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
											} ?>
										</td>
										<td>
											<a href="javascript:void(0);" class="wpw-auto-poster-delete-tb-account wpw-auto-poster-tumblr-remove <?php echo esc_attr($tumblr_delete_class); ?>" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
										</td>    
									</tr>
									<?php 
									}
								} else { ?>
									<tr valign="top" class="wpw-auto-poster-tumblr-account-details" data-row-id="<?php echo empty($tumblr_key) ? '': esc_attr($tumblr_key); ?>">
										<td scope="row" width="30%"  data-label="<?php esc_html_e( 'Tumblr OAuth Consumer Key', 'wpwautoposter' ); ?>">
											<input type="text" name="wpw_auto_poster_options[tumblr_keys][0][consumer_key]" value="" class="large-text wpw-auto-poster-tumblr-app-id" />
											<p><small><?php esc_html_e( 'Enter Tumblr App Consumer Key.', 'wpwautoposter' ); ?></small></p>  
										</td>
										<td scope="row" width="30%" data-label="<?php esc_html_e( 'Tumblr Secret Key', 'wpwautoposter' ); ?>">
											<input type="text" name="wpw_auto_poster_options[tumblr_keys][0][consumer_secret]" value="" class="large-text wpw-auto-poster-tumblr-app-secret" />
											<p><small><?php esc_html_e( 'Enter Tumblr App Consumer Secret.', 'wpwautoposter' ); ?></small></p>  
										</td>
										<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>"></td>
										<td>
											<a href="javascript:void(0);" class="wpw-auto-poster-delete-tb-account wpw-auto-poster-tumblr-remove" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
										</td>
									</tr>
								<?php } ?>

								</tbody>
							</table></td></tr>
						
							<?php $tumblr_api_add_more_button = apply_filters( 'wpweb_tumblr_api_add_more_button', true ); ?>
                    		<?php if( $tumblr_api_add_more_button ){ ?>
								<tr>
									<td colspan="4">
										<a class='wpw-auto-poster-add-more-tb-account button' href='javascript:void(0);'><?php esc_html_e( 'Add more', 'wpwautoposter' ); ?></a>
									</td>
								</tr>
							<?php } ?>
							
							<?php
							echo apply_filters ( 
								'wpweb_fb_settings_submit_button', 
								'<tr valign="top">
									<td colspan="4">
										<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
									</td>
								</tr>'
							); ?>
							
						</tbody>
					</table>

				</div><!-- .inside -->
			</div><!-- #tumblr_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-tumblr-api -->
<!-- end of the tumblr api settings meta box -->


<!-- beginning of the autopost meta box -->
<div id="wpw-auto-poster-autopost" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="autopost" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
				<h3 class="hndle">
					<span class='wpw-sap-buffer-app-settings'><?php esc_html_e( 'Autopost to Tumblr', 'wpwautoposter' ); ?></span>
				</h3>
								
				<div class="inside">
					<table class="form-table">
						<tbody>

							<tr valign="top">
								<th scope="row">
									<label for="wpw_auto_poster_options[prevent_post_tb_metabox]"><?php esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input name="wpw_auto_poster_options[prevent_post_tb_metabox]" id="wpw_auto_poster_options[prevent_post_tb_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_tb_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_tb_metabox'] ); } ?> />
									<p><small><?php esc_html_e( 'Check this box to hide meta settings for Tumblr from individual posts.', 'wpwautoposter' ); ?></small></p>
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
										
										// Getting all tumblr account
										$tb_accounts = wpw_auto_poster_get_tb_accounts();
										
										foreach( $types as $type ) {
											
											if( !is_object( $type ) ) continue;
											
												if( isset( $wpw_auto_poster_options['tb_type_' . $type->name . '_method'] ) ) {
													$wpw_auto_poster_tb_type_method = $wpw_auto_poster_options['tb_type_' . $type->name . '_method'];	
												} else {
													$wpw_auto_poster_tb_type_method = '';
												}

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
															esc_html_e( ' to Tumblr', 'wpwautoposter' ); 
													?>
												</div><!--.wpw-auto-poster-fb-types-label-->

												
												<div class="wpw-auto-poster-fb-type">
															<select name="wpw_auto_poster_options[tb_type_<?php echo esc_attr($type->name); ?>_method]" id="wpw_auto_poster_tb_type_post_method">
														
															<?php
																foreach ( $wall_post_methods as $method_key => $method_value ) {
																	echo '<option value="' . esc_attr($method_key) . '" ' . selected( $wpw_auto_poster_tb_type_method, $method_key, false ) . '>' . esc_html($method_value) . '</option>';
																}
															?>
															</select>
														</div>
												
												<div class="wpw-auto-poster-fb-user-label">
													<?php esc_html_e( 'of this user', 'wpwautoposter' ); ?>(<?php esc_html_e( 's', 'wpwautoposter' );?>)
												</div><!--.wpw-auto-poster-fb-user-label-->
												<div class="wpw-auto-poster-fb-users-acc">
													<?php
														if( isset( $wpw_auto_poster_options['tb_type_'.$type->name.'_user'] ) ) {
															$wpw_auto_poster_tb_type_user = $wpw_auto_poster_options['tb_type_'.$type->name.'_user'];	 
														} else {
															$wpw_auto_poster_tb_type_user = '';
														}
														
														$wpw_auto_poster_tb_type_user = ( array ) $wpw_auto_poster_tb_type_user;
													?>
													
													<select name="wpw_auto_poster_options[tb_type_<?php echo esc_attr($type->name); ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
														<?php
														if( !empty($tb_accounts) && is_array($tb_accounts) ) {
															
															foreach( $tb_accounts as $aid => $aval ) {
																
																if( is_array( $aval ) ) {

																	$tb_app_data 	= isset( $wpw_auto_poster_tb_sess_data[$aid] ) ? $wpw_auto_poster_tb_sess_data[$aid] : array();
															?>
																	
																	<?php foreach ( $aval as $aval_key => $aval_data ) { ?>
																		<option value="<?php echo esc_attr($aval_key); ?>" <?php selected( in_array( $aval_key, $wpw_auto_poster_tb_type_user ), true, true ); ?> ><?php echo esc_attr($aval_data); ?></option>
																	<?php } ?>
																																		
														<?php	} else { ?>
																		<option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $wpw_auto_poster_tb_type_user ), true, true ); ?> ><?php echo esc_attr($aval); ?></option>
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

							<tr valign="top" class="">
								<th scope="row">
									<label><?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input id="tb_custom_global_msg" type="radio" name="wpw_auto_poster_options[tb_custom_msg_options]" value="global_msg" <?php checked($tb_custom_msg_options, 'global_msg', true);?> class="custom_msg_options">
									<label for="tb_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
                                    
                                    <input id="tb_custom_post_msg" type="radio" name="wpw_auto_poster_options[tb_custom_msg_options]" value="post_msg" <?php checked($tb_custom_msg_options, 'post_msg', true);?> class="custom_msg_options">
                                    <label for="tb_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
								</td>	
							</tr>

							<tr valign="top" class="wpw_sap_tb_post_img global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
								<th scope="row">
									<label for="wpw_auto_poster_options[tb_custom_img]"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input type="text" value="<?php echo !empty( $wpw_auto_poster_options['tb_custom_img'] ) ? $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tb_custom_img'] ) : ''; ?>" name="wpw_auto_poster_options[tb_custom_img]" id="wpw_auto_poster_options_tb_custom_img" class="large-text wpw-auto-poster-img-field">
									<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
									<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the tumblr post.', 'wpwautoposter' ); ?></small></p>
								</td>	
							</tr>

							<tr valign="top" class="global_title_tr <?php echo esc_attr($global_title_style); ?>">									
								<th scope="row">
									<label for="wpw_auto_poster_options[tb_global_title_template]"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
								</th>
								<td class="form-table-td">
								<?php
									$tb_template_title_text = !empty( $wpw_auto_poster_options['tb_global_title_template'] ) ? $wpw_auto_poster_options['tb_global_title_template']: ''; ?>					

									<input type="text" name="wpw_auto_poster_options[tb_global_title_template]" id="wpw_auto_poster_options[tb_global_title_template]" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $tb_template_title_text ); ?>">
								</td>	
								
							</tr>
							<tr valign="top">									
								<th scope="row"></th>
								<td class="global_msg_td">
									<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter' ); ?>
									<?php 
									$tb_template_title_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
						            print $tb_template_title_str;
						            ?>
									</small></p>
								</td>			
							</tr>

							<tr valign="top" class="global_msg_tr custom_message_template <?php echo esc_attr($global_msg_style); ?>">									
								<th scope="row">
									<label for="wpw_auto_poster_options[tb_global_message_template]"><?php esc_html_e( 'Custom Message :', 'wpwautoposter' ); ?></label>
								</th>
								<td class="form-table-td">
									<?php
									$tb_global_message_template = !empty( $wpw_auto_poster_options['tb_global_message_template'] ) ? $wpw_auto_poster_options['tb_global_message_template']: ''; ?>

									<textarea type="text" name="wpw_auto_poster_options[tb_global_message_template]" id="wpw_auto_poster_options[tb_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $tb_global_message_template ); ?></textarea>
								</td>	
							</tr>

							<tr id="custom_post_type_templates_tb" class="post_msg_tr <?php echo esc_attr($post_msg_style); ?>">
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
									    <li><a href="#tabs-<?php echo esc_attr($type->name); ?>"><?php echo esc_html($label); ?></a></li>
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
											
										$wpw_auto_poster_options['tb_global_message_template_'.$type->name] = ( isset( $wpw_auto_poster_options['tb_global_message_template_'.$type->name] ) ) ? $wpw_auto_poster_options['tb_global_message_template_'.$type->name] : '';

	                                    $wpw_auto_poster_options['tb_custom_img_'.$type->name] = ( isset( $wpw_auto_poster_options['tb_custom_img_'.$type->name] ) ) ? $wpw_auto_poster_options['tb_custom_img_'.$type->name] : '';
								  	?>
								  	<table id="tabs-<?php echo esc_attr($type->name); ?>">
										<tr valign="top" class="wpw_sap_tb_post_img post_msg_tr">
											<th scope="row">
												<label for="wpw_auto_poster_options_tb_custom_img_<?php echo esc_attr($type->name); ?>"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tb_custom_img_'.$type->name] ); ?>" name="wpw_auto_poster_options[tb_custom_img_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_tb_custom_img_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
												<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the tumblr post.', 'wpwautoposter' ); ?></small></p>
											</td>	
										</tr>
														
										<tr valign="top" class="custom_message_template post_msg_tr">

											<th scope="row">
												<label for="wpw_auto_posting_tb_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message ', 'wpwautoposter'); ?>: </label>
											</th>

											<td class="form-table-td">
												<textarea type="text" name="wpw_auto_poster_options[tb_global_message_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_tb_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tb_global_message_template_'.$type->name] ); ?></textarea>
											</td>	
										</tr>
									</table>	
								<?php } ?>
								</th>
							</tr>	

							<tr valign="top" class="custom_message_template post_msg_tr global_msg_tr">									
								<th scope="row"></th>
								<td class="global_msg_td">
									<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter default message template which will be used for the posting on Tumblr. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
									<?php 
									$tb_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
								  '<br /><b><code>{content-digits}</code></b> - ' . sprintf(
									  esc_html__('displays the post content with define number of digits in template tag. %sE.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'),
									  "<b>","</b>"
								  ).
						            '<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
									  esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'),
									  "<b>","</b>"
								  );
						            print $tb_template_str;
						            ?>
									</small></p>
								</td>	
							</tr>

							<?php
								echo apply_filters ( 
													 'wpweb_fb_settings_submit_button', 
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
			</div><!-- #autopost -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-autopost -->
<!-- end of the autopost meta box -->