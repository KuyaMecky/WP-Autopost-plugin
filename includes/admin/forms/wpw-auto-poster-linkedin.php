<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * LinkedIn Settings
 *
 * The html markup for the LinkedIn settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_li_posting;

//model class
$model = $wpw_auto_poster_model;


$linkedin_auth_options = !empty($wpw_auto_poster_options['linkedin_auth_options']) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';

$graph_style = "";
$rest_style = "";
$app_method_style = "";
if( $linkedin_auth_options == 'graph') {
	$app_method_style = "repost_ba_global_message_template_hide";
	$rest_style = "repost_ba_global_message_template_hide";
} else {
	$graph_style = "repost_ba_global_message_template_hide";
	$rest_style = "repost_ba_global_message_template_hide";
}

$cat_posts_type = !empty( $wpw_auto_poster_options['li_posting_cats'] ) ? $wpw_auto_poster_options['li_posting_cats']: 'exclude';

//linkedin posting class
$liposting = $wpw_auto_poster_li_posting;

$linkedin_keys = isset( $wpw_auto_poster_options['linkedin_keys'] ) ? $wpw_auto_poster_options['linkedin_keys'] : array();

$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' ); // Getting linkedin app grant data

$li_wp_pretty_url = ( !empty( $wpw_auto_poster_options['li_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['li_wp_pretty_url'] : '';

$li_wp_pretty_url = !empty( $li_wp_pretty_url ) ? ' checked="checked"' : '';

$li_company = ( !empty( $wpw_auto_poster_options['li_company'] ) ) ? $wpw_auto_poster_options['li_company'] : '';

$li_company = !empty( $li_company ) ? ' checked="checked"' : '';

$li_selected_shortner = isset( $wpw_auto_poster_options['li_url_shortener'] ) ? $wpw_auto_poster_options['li_url_shortener'] : '';

$li_wp_pretty_url_css = ( $li_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css': ' ba_wp_pretty_url_css_hide';

// get url shortner service list array 
$li_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$li_exclude_cats = array();

$li_template_text = ( !empty( $wpw_auto_poster_options['li_global_message_template'] ) ) ? $wpw_auto_poster_options['li_global_message_template'] : '';

$li_template_title_text = ( !empty( $wpw_auto_poster_options['li_global_title_template'] ) ) ? $wpw_auto_poster_options['li_global_title_template'] : '';

$li_custom_msg_options = isset( $wpw_auto_poster_options['li_custom_msg_options'] ) ? $wpw_auto_poster_options['li_custom_msg_options'] : 'global_msg';

if( $li_custom_msg_options == 'global_msg') {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
}

// Check if site is ssl enabled, if not than set error message.
$error_msgs = array();

if (!is_ssl()) {
   
   $error_msgs[] = sprintf( esc_html__( 'Linkedin requires %sSSL%s for posting.', 'wpwautoposter' ), '<b>', '</b>' );
   $readonly = 'readonly';
}

$li_app_method = $wpw_auto_poster_li_posting->wpw_auto_poster_get_profiles_data();

 ?>

<!-- beginning of the linkedin general settings meta box -->
<div id="wpw-auto-poster-linkedin-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="linkedin_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
								
					<h3 class="hndle">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'LinkedIn General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
						<?php if(!empty($error_msgs)) { ?>
							<div class="wpw-auto-poster-error">
                                <ul>
                                    <?php foreach ( $error_msgs as $error_msg ) { ?>
                                        <li><?php echo $error_msg;?></li>
                                    <?php } ?>
                                </ul>								
							</div>
						<?php } ?>	
						<table class="form-table">											
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_linkedin]"><?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input name="wpw_auto_poster_options[enable_linkedin]" id="wpw_auto_poster_options[enable_linkedin]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_linkedin'] ) ) { checked( '1', $wpw_auto_poster_options['enable_linkedin'] ); } ?> />
										<p><small><?php esc_html_e( 'Check this box, if you want to automatically post your new content to LinkedIn.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>	

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_linkedin_for]"><?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<ul>
										<?php 
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();
											
											if( !empty( $wpw_auto_poster_options['enable_linkedin_for'] ) ) {
												$prevent_meta = $wpw_auto_poster_options['enable_linkedin_for'];
											} else {
												$prevent_meta = '';
											}
															
											$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

											if( !empty( $wpw_auto_poster_options['li_post_type_tags'] ) ) {
												$li_post_type_tags = $wpw_auto_poster_options['li_post_type_tags'];
											} else {
												$li_post_type_tags = array();
											}

											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

											if( !empty( $wpw_auto_poster_options['li_post_type_cats'] ) ) {
												$li_post_type_cats = $wpw_auto_poster_options['li_post_type_cats'];
											} else {
												$li_post_type_cats = array();
											}

											// Get saved categories for linkedin to exclude from posting
											if( !empty( $wpw_auto_poster_options['li_exclude_cats'] ) ) {
												$li_exclude_cats = $wpw_auto_poster_options['li_exclude_cats'];
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
													<input type="checkbox" id="wpw_auto_posting_linkedin_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_linkedin_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo esc_attr($selected); ?>/>
																						
												<label for="wpw_auto_posting_linkedin_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_html($label); ?></label>
											</li>
											
											<?php } ?>
										</ul>
										<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to LinkedIn when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>


								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_post_type_tags][]"><?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[li_post_type_tags][]" id="wpw_auto_poster_options[li_post_type_tags]" class="li_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if(isset($li_post_type_tags[$type->name]) && !empty($li_post_type_tags[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $li_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
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
										<label for="wpw_auto_poster_options[li_post_type_cats][]"><?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[li_post_type_cats][]" id="wpw_auto_poster_options[li_post_type_cats]" class="li_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if(isset($li_post_type_cats[$type->name]) && !empty($li_post_type_cats[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $li_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
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
										<label for="wpw_auto_poster_options[li_exclude_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<div class="wpw-auto-poster-cats-option">
											<input name="wpw_auto_poster_options[li_posting_cats]" id="li_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
											<label for="li_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
											<input name="wpw_auto_poster_options[li_posting_cats]" id="li_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
											<label for="li_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
										</div>
										<select name="wpw_auto_poster_options[li_exclude_cats][]" id="wpw_auto_poster_options[li_exclude_cats]" class="li_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">
											
										<?php

											$li_exclude_cats_selected_values = !empty($wpw_auto_poster_options['li_exclude_cats']) ? $wpw_auto_poster_options['li_exclude_cats'] : array();
											$selected = 'selected="selected"';

											if(!empty($li_exclude_cats_selected_values)) {
											
												foreach ($li_exclude_cats_selected_values as $post_type => $post_data) {
													
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

								<tr id="" valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_company]"><?php esc_html_e( 'Enable Company Pages : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<input type="checkbox" name="wpw_auto_poster_options[li_company]" id="wpw_auto_poster_options[li_company]" class="li_company" data-content='li' value="yes" <?php print esc_attr($li_company);?>>
										<p><small><?php  esc_html_e( 'Check this box if you want to post to your company pages.'); ?></small></p>
										
									
									</td>
								</tr>
								<tr>
									<td colspan="2"><p class="wpw-auto-poster-info-box">
										<?php
										printf( esc_html__( '%1$s You must have %2$sapproved company account%3$s and your App with these permissions %4$s and %5$s', 'wpwautoposter' ),'<b>Note: </b>','<b>','</b>', '<b>{rw_organization_admin}</b>', '<b>{w_organization_social}</b>' ); ?></p></td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_url_shortener]"><?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[li_url_shortener]" id="wpw_auto_poster_options[li_url_shortener]" class="li_url_shortener" data-content='li'>
											<?php
																
												foreach ( $li_url_shortener as $key => $option ) {											
													?>
													<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $li_selected_shortner, $key ); ?>>
														<?php echo esc_html($option); ?>
													</option>
													<?php
												}															
											?> 														
										</select>
										<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								

								<tr id="row-li-wp-pretty-url" valign="top" class="<?php print esc_attr($li_wp_pretty_url_css);?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_wp_pretty_url]"><?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<input type="checkbox" name="wpw_auto_poster_options[li_wp_pretty_url]" id="wpw_auto_poster_options[li_wp_pretty_url]" class="li_wp_pretty_url" data-content='li' value="yes" <?php print esc_attr($li_wp_pretty_url);?>>
										<p><small><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></small></p>
									</td>
								</tr>

								<?php
								if( $li_selected_shortner == 'bitly' ) {
									$class = '';
								} else {
									$class = 'ba_wp_pretty_url_css_hide';
								}
								
								if( $li_selected_shortner == 'shorte.st' ) {
									$shortest_class = '';
								} else {
									$shortest_class = 'ba_wp_pretty_url_css_hide';
								} ?>
								
								<tr valign="top" class="li_setting_input_bitly <?php echo esc_attr($class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_bitly_access_token]"><?php esc_html_e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[li_bitly_access_token]" id="wpw_auto_poster_options[li_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_bitly_access_token'] ); ?>" class="large-text">
									</td>
								</tr>
								
								<tr valign="top" class="li_setting_input_shortest <?php echo esc_attr($shortest_class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_shortest_api_token]"><?php esc_html_e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[li_shortest_api_token]" id="wpw_auto_poster_options[li_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_shortest_api_token'] ); ?>" class="large-text">
									</td>
								</tr>
								<?php
									echo apply_filters ( 
														 'wpweb_li_settings_submit_button', 
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
									
			</div><!-- #linkedin_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkedin-general -->
<!-- end of the linkedin general settings meta box -->

<!-- beginning of the linkedin api settings meta box -->
<div id="wpw-auto-poster-linkedin-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="linkedin_api" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'LinkedIn API Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
						<table id="linkedin-api-options" class="form-table wpw-auto-poster-linkedin-api-options wpw-auto-poster-linkedin-api-options_inline_width">
	                        <tr>
	                            <th>
	                                <?php
	                                esc_html_e('Authentication : ', 'wpwautoposter'); ?>
	                            </th>
	                            <td>
	                                <input id="linkedin_app_method" type="radio" name="wpw_auto_poster_options[linkedin_auth_options]" value="appmethod" <?php checked($linkedin_auth_options, 'appmethod', true); ?>><label for="linkedin_app_method" class="wpw-auto-poster-label"><?php esc_html_e('LinkedIn APP Method', 'wpwautoposter'); ?></label>
	                            </td>

	                            <td>
	                                <input id="linkedin_graph_api" type="radio" name="wpw_auto_poster_options[linkedin_auth_options]" value="graph" <?php checked($linkedin_auth_options, 'graph', true); ?>><label for="linkedin_graph_api" class="wpw-auto-poster-label"><?php esc_html_e('LinkedIn Graph API', 'wpwautoposter'); ?></label>
	                            </td>
	                        </tr>
	                    </table>	

						<table id="linkedin-graph-api" class="form-table wpw-auto-poster-linkedin-settings <?php print esc_attr($graph_style); ?>">											
							<tbody>				
								<tr valign="top">
									<th scope="row"><label>
										<?php esc_html_e( 'App Settings : ', 'wpwautoposter' ); ?>
									</label></th>
									<td colspan="3">
										<p><?php esc_html_e( 'Before you can start publishing your content to LinkedIn you need to create a LinkedIn Application.', 'wpwautoposter' ); ?></p>
										<p><?php printf( esc_html__('You can get a step by step tutorial on how to create a LinkedIn Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="'.esc_url('https://docs.Michael Tallada/social-network-integration/linkedin/').'" target="_blank">', '</a>' ); ?></p> 
									</td>
								</tr>
								
								<tr>
									<th scope="row"><label>
										<?php esc_html_e( 'Allowing permissions : ', 'wpwautoposter' ); ?>
									</label></td>
									<td colspan="3">
										<p><?php esc_html_e( 'Posting content to your chosen LinkedIn personal account requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.' ); ?></p>
									</td>
								</tr> 
								
								<tr>
									<td colspan="4">
										<p class="wpw-auto-poster-info-box"><?php printf(esc_html__( '%s Note: %s Please note the LinkedIn App, LinkedIn profile or page and the user who authorizes the app MUST belong to the same LinkedIn account. So please make sure you are logged in to LinkedIn as the same user who created the app.', 'wpwautoposter' ), "<b>", "</b>"
									); ?></p>
									</td>
								</tr>

								<tr><td class="no-padding" colspan="5">
	                                <table class="wpw-auto-poster-form-table-resposive">
	                                    <thead><tr valign="top">
										<td scope="row"><strong>
											<label for="wpw_auto_poster_options[linkedin_keys][0][app_id]"><?php esc_html_e( 'App ID/API Key', 'wpwautoposter' ); ?></label>
										</strong></td>
										<td scope="row"><strong>
											<label for="wpw_auto_poster_options[linkedin_keys][0][app_secret]"><?php esc_html_e( 'App Secret', 'wpwautoposter' ); ?></label>
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
									if( !empty($linkedin_keys) ) {
										foreach( $linkedin_keys as $linkedin_key => $linkedin_value ) {
											
											if( !isset($linkedin_key) ) {
												$linkedin_key = "0";
											}

											// Don't disply delete link for first row
											$linkedin_delete_class = empty( $linkedin_key ) ? '' : ' wpw-auto-poster-display-inline '; ?>

											<tr valign="top" class="wpw-auto-poster-linkedin-account-details" data-row-id="<?php echo esc_attr($linkedin_key); ?>">
												<td scope="row" width="25%" data-label="<?php esc_html_e( 'Linkedin App ID/API Key', 'wpwautoposter' ); ?>">
													<input type="text" name="wpw_auto_poster_options[linkedin_keys][<?php echo esc_attr($linkedin_key); ?>][app_id]" value="<?php echo $model->wpw_auto_poster_escape_attr( $linkedin_value['app_id'] ); ?>" class="large-text wpw-auto-poster-linkedin-app-id" />
													<p><small><?php esc_html_e( 'Enter Linkedin App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td scope="row" width="25%" data-label="<?php esc_html_e( 'Linkedin App Secret', 'wpwautoposter' ); ?>">
													<input type="text" name="wpw_auto_poster_options[linkedin_keys][<?php echo esc_attr($linkedin_key); ?>][app_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr( $linkedin_value['app_secret'] ); ?>" class="large-text wpw-auto-poster-linkedin-app-secret" />
													<p><small><?php esc_html_e( 'Enter Linkedin App Secret.', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td scope="row" width="25%" valign="top" data-label="<?php esc_html_e( 'Valid OAuth redirect URIs', 'wpwautoposter' ); ?>">
			                                        <?php
			                                        $site_url =  site_url().'/';                                        
			                                        $valid_auto_redirect_url = add_query_arg( array('wpwautoposter' => 'linkedin', 'wpw_li_app_id' => esc_attr(stripslashes($linkedin_value['app_id'])) ), $site_url ); ?>
			                                        <input class="li-oauth-url" id="li-oauth-url-<?php print esc_attr($linkedin_value['app_id']);?>" type="text" value="<?php echo esc_attr($valid_auto_redirect_url); ?>" size="30" readonly/><button type="button" data-appid="<?php print esc_attr($linkedin_value['app_id']);?>" class="button copy-clipboard"><?php esc_html_e('Copy', 'wpwautoposter'); ?></button>
			                                        <p><small><?php esc_html_e('Copy and paste it to Valid OAuth redirect URIs in linkedin apps.', 'wpwautoposter'); ?></small></p>
			                                    </td>
												<td scope="row" width="25%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>">
													<?php
													if( !empty($linkedin_value['app_id']) && !empty($linkedin_value['app_secret']) && !empty($wpw_auto_poster_li_sess_data[ $linkedin_value['app_id'] ]) )  {
														
														echo '<p>' . esc_html__( 'You already granted extended permissions.', 'wpwautoposter' ) . '</p>';	
														echo apply_filters ( 'wpweb_li_settings_reset_session', sprintf(
															 esc_html__( "%s Reset User Session %s", 'wpwautoposter' ), 
															 "<a href='".add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'li_reset_user' => '1', 'wpw_li_app' => $linkedin_value['app_id'] ), admin_url( 'admin.php' ) )."'>",
															 "</a>"
														 ) );
													} elseif( !empty($linkedin_value['app_id']) && !empty($linkedin_value['app_secret']) ) {
														echo '<p><a href="' . esc_url($liposting->wpw_auto_poster_get_li_login_url( $linkedin_value['app_id'] )) . '">' . esc_html__( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
													} ?>
												</td>
												<td>
													<a href="javascript:void(0);" class="wpw-auto-poster-delete-li-account wpw-auto-poster-linkedin-remove <?php echo esc_attr($linkedin_delete_class); ?>" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
												</td>
											</tr>
										<?php 
										}
									} else { ?>
										<tr valign="top" class="wpw-auto-poster-linkedin-account-details" data-row-id="<?php echo (empty($linkedin_key) ? '': $linkedin_key); ?>">
											<td scope="row" width="30%" data-label="<?php esc_html_e( 'Linkedin App ID/API Key', 'wpwautoposter' ); ?>">
												<input type="text" name="wpw_auto_poster_options[linkedin_keys][0][app_id]" value="" class="large-text wpw-auto-poster-linkedin-app-id" />
												<p><small><?php esc_html_e( 'Enter Linkedin App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
											</td>
											<td scope="row" width="30%" data-label="<?php esc_html_e( 'Linkedin App Secret', 'wpwautoposter' ); ?>">
												<input type="text" name="wpw_auto_poster_options[linkedin_keys][0][app_secret]" value="" class="large-text wpw-auto-poster-linkedin-app-secret" />
												<p><small><?php esc_html_e( 'Enter Linkedin App Secret.', 'wpwautoposter' ); ?></small></p>  
											</td>
											<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>"></td>
											<td>
												<a href="javascript:void(0);" class="wpw-auto-poster-delete-li-account wpw-auto-poster-linkedin-remove" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							
								<?php $linkedin_graph_api = apply_filters( 'wpweb_linkedin_graph_api_add_more_button', true ); ?>
                        		<?php if( $linkedin_graph_api ){ ?>
									<tr>
										<td colspan="4">
											<a class='wpw-auto-poster-add-more-li-account button' href='javascript:void(0);'><?php esc_html_e( 'Add more', 'wpwautoposter' ); ?></a>
										</td>
									</tr>
								<?php } ?>
								
								<?php
								echo apply_filters ( 
									'wpweb_li_settings_submit_button', 
									'<tr valign="top">
										<td colspan="4">
											<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
									</tr>'
								); ?>
							</tbody>
						</table>
						
						<?php $linkedin_app_method = apply_filters( 'wpweb_linkedin_app_method', true ); ?>
                        <?php if( $linkedin_app_method ){ ?>
							<table id="linkedin-app-method" class="form-table wpw-auto-poster-linkedin-settings wpw-auto-poster-linkedin-custom-settings <?php print esc_attr($app_method_style); ?> <?php echo!empty($li_app_method && $linkedin_auth_options == 'appmethod') ? 'wpw-auto-poster-linkedin-after-custom-app-added' : '' ?>">
								<tbody>
									<tr valign="top" class="wpw-auto-poster-linkedin-account-details-custom-method <?php echo!empty($li_app_method && $linkedin_auth_options == 'appmethod') ? 'wpw-auto-poster-linkedin-custom-app-added' : '' ?>" data-row-id="0">
										<td scope="row" class="row-btn" colspan="3" style="text-align: center;">
									<?php
									echo '<p><a href="' . $liposting->wpw_auto_poster_get_li_app_method_login_url() . '">' . esc_html__('Add LinkedIn Account', 'wpwautoposter') . '</a></p>';
									?>
										</td>
									</tr>

									<?php
									if( !empty($li_app_method) && $linkedin_auth_options == 'appmethod' ) { ?>
										<tr>
											<td colspan="3">
												<table class="wpw-auto-poster-table-resposive">
													<thead><tr valign="top">
														<td><strong>
															<?php esc_html_e('User ID', 'wpwautoposter'); ?>
														</strong></td>
														<td scope="row"><strong>
															<?php esc_html_e('Account Name', 'wpwautoposter'); ?>
														</strong></td>
														<td class="width-16"><strong>
															<?php esc_html_e('Action', 'wpwautoposter'); ?>
														</strong></td>
													</tr></thead>

													<tbody>
													<?php
													$i = 0;

													foreach( $li_app_method as $linkedin_app_key => $linkedin_app_value ) {
														
														$profile_data = explode(':|:',$linkedin_app_key);
														
														if($profile_data[0] != 'user'){
															continue;
														}

														$li_user_data = $linkedin_app_value; ?>

														<tr valign="top" class="wpw-auto-poster-linkedin-post-data">
															<td scope="row" width="33%" data-label="<?php esc_html_e('User ID', 'wpwautoposter'); ?>"><?php print esc_html( $profile_data[1] ); ?>
															</td>
															
															<td scope="row" width="33%" data-label="<?php esc_html_e('Account Name', 'wpwautoposter'); ?>"><?php print esc_html($li_user_data); ?></td>
															
															<td scope="row" width="15%" valign="top" class="wpw-grant-reset-data wpw-delete-fb-app-method width-16" data-label="<?php esc_html_e('Action', 'wpwautoposter'); ?>">
																<?php
																echo apply_filters('wpweb_li_settings_reset_session', sprintf(
																	esc_html__("%s Delete Account %s", 'wpwautoposter'), "<a class='wpw-auto-poster-linkedin-app-delete-link' href='" . add_query_arg(array('page' => 'wpw-auto-poster-settings', 'li_reset_user' => '1', 'wpw_li_app' => $profile_data[2], 'li_delet_user' => '1#wpw-auto-poster-tab-linkedin'), admin_url('admin.php')) . "'>", "</a>"
																	)
																); ?>
															</td>
														</tr>

														<?php
														$i++;
													}
													echo "</tbody>
												</table>
											</td>
										</tr>";
									}

									echo apply_filters(
									'wpweb_fb_settings_submit_button', '<tr valign="top">
										<td colspan="4">
										<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
									</tr>'
								);
								?>
								</tbody>
							</table>
						<?php } ?>		
					</div><!-- .inside -->
							
			</div><!-- #linkedin_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkedin-api -->
<!-- end of the linkedin api settings meta box -->


<!-- beginning of the grant extended permission meta box -->
<div id="wpw-auto-poster-linkein-grant-permission" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="grant_permission" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
				<h3 class="hndle">
					<span class='wpw_common_verticle_align'><?php esc_html_e( 'Autopost to LinkedIn', 'wpwautoposter' ); ?></span>
				</h3>
								
				<div class="inside">
					<table class="form-table">
						<tbody>
							
							<tr valign="top"> 
								<th scope="row">
									<label for="wpw_auto_poster_options[prevent_post_li_metabox]"><?php esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?></label>
								</th>									
								<td>
									<input name="wpw_auto_poster_options[prevent_post_li_metabox]" id="wpw_auto_poster_options[prevent_post_li_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_li_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_li_metabox'] ); } ?> />
									<p><small><?php esc_html_e( 'If you check this box, then it will hide meta settings for linkedin from individual posts.', 'wpwautoposter' ); ?></small></p>
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
								<td><?php
									
									foreach( $types as $type ) {
										
										if( !is_object( $type ) ) continue;
										
										if( isset( $type->labels ) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
							            }
							            else {
							            	$label = $type->name;
							            }
										
										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
										
										//Get linkedin Profiles Data
										$li_profile_data	= $liposting->wpw_auto_poster_get_profiles_data();

										//Initilize profile
										$wpw_auto_poster_li_profile	= array();
										if( isset( $wpw_auto_poster_options['li_type_'.$type->name.'_profile'] ) ) {
											
											$wpw_auto_poster_li_profile = ( array ) $wpw_auto_poster_options['li_type_'.$type->name.'_profile'];
										}
										
										?>
										
										<div class="wpw-auto-poster-fb-types-wrap">
											<div class="wpw-auto-poster-fb-types-label"><?php	
												echo esc_html__( 'Autopost', 'wpwautoposter' ) . ' ' . esc_html($label) . esc_html__( ' to Linkedin', 'wpwautoposter' );?>
											</div><!--.wpw-auto-poster-li-types-label-->
											
											<div class="wpw-auto-poster-fb-type">
												<select name="wpw_auto_poster_options[li_type_<?php echo esc_attr($type->name); ?>_profile][]" multiple="multiple" class="wpw-auto-poster-users-acc-select"><?php
													
													if( !empty( $li_profile_data ) ) {
														foreach ( $li_profile_data as $profile_id => $profile_name ) {?>
															
															<option value="<?php echo esc_attr($profile_id);?>" <?php selected( in_array( $profile_id, $wpw_auto_poster_li_profile ), true, true );?>><?php echo esc_html($profile_name);?></option><?php
														}
													}?>
													
												</select>
											</div><!--.wpw-auto-poster-fb-type-->
										</div><!--.wpw-auto-poster-fb-types-wrap--><?php
									}?>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label><?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input id="li_custom_global_msg" type="radio" name="wpw_auto_poster_options[li_custom_msg_options]" value="global_msg" <?php checked($li_custom_msg_options, 'global_msg', true);?> class="custom_msg_options">
									<label for="li_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
                                    
                                    <input id="li_custom_post_msg" type="radio" name="wpw_auto_poster_options[li_custom_msg_options]" value="post_msg" <?php checked($li_custom_msg_options, 'post_msg', true);?> class="custom_msg_options">
                                    <label for="li_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
								</td>	
							</tr>
							
							<tr valign="top"  class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
								<th scope="row">
									<label for="wpw_auto_poster_options_li_post_image"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<?php
									$li_post_image = isset( $wpw_auto_poster_options['li_post_image'] ) ? $wpw_auto_poster_options['li_post_image'] : ''; ?>

									<input type="text" name="wpw_auto_poster_options[li_post_image]" id="wpw_auto_poster_options_li_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $li_post_image ); ?>">
									<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
									<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the LinkedIn wall post.', 'wpwautoposter' ); ?></small></p>
								</td>	
							</tr>

							<tr valign="top" class="global_title_tr <?php echo esc_attr($global_title_style); ?>">									
								<th scope="row">
									<label for="wpw_auto_poster_options[li_global_title_template]"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
								</th>
								<td class="form-table-td">
									<input type="text" name="wpw_auto_poster_options[li_global_title_template]" id="wpw_auto_poster_options[li_global_title_template]" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $li_template_title_text ); ?>">
								</td>	
								
							</tr>
							<tr valign="top">									
								<th scope="row"></th>
								<td class="global_msg_td">
									<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter a custom title which will be used for the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter' ); ?>
									<?php 
									$li_template_title_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
						            print $li_template_title_str;
						            ?>
									</small></p>
								</td>			
							</tr>		



							<tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">									
								<th scope="row">
									<label for="wpw_auto_poster_options[li_global_message_template]"><?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?></label>
								</th>
								<td class="form-table-td">
									<textarea type="text" name="wpw_auto_poster_options[li_global_message_template]" id="wpw_auto_poster_options[li_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $li_template_text ); ?></textarea>
								</td>	
								
							</tr>
							
							<tr id="custom_post_type_templates_li" class="post_msg_tr <?php echo esc_attr($post_msg_style); ?>">
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
											
										$wpw_auto_poster_options['li_global_message_template_'.$type->name] = ( isset( $wpw_auto_poster_options['li_global_message_template_'.$type->name] ) ) ? $wpw_auto_poster_options['li_global_message_template_'.$type->name] : '';

										$wpw_auto_poster_options['li_post_image_'.$type->name] = ( isset( $wpw_auto_poster_options['li_post_image_'.$type->name] ) ) ? $wpw_auto_poster_options['li_post_image_'.$type->name] : '';
										?>
										<table id="tabs-<?php echo esc_attr($type->name); ?>">
											<tr valign="top">
												<th scope="row">
													<label for="wpw_auto_poster_options_li_post_image_<?php echo esc_attr($type->name); ?>"><?php esc_html_e( 'Post Image:', 'wpwautoposter' ); ?></label>
												</th>
												<td>
													<input type="text" name="wpw_auto_poster_options[li_post_image_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_li_post_image_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_post_image_'.$type->name] ); ?>">
													<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
													<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the LinkedIn wall post.', 'wpwautoposter' ); ?></small></p>
												</td>	
											</tr>

											<tr valign="top">

												<th scope="row">
													<label for="wpw_auto_posting_li_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message ', 'wpwautoposter'); ?>: </label>
												</th>

												<td class="form-table-td">
													<textarea type="text" name="wpw_auto_poster_options[li_global_message_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_li_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_global_message_template_'.$type->name] ); ?></textarea>
												</td>	
											</tr>
										</table>	
									<?php } ?>
								</th>
							</tr>	

							<tr valign="top">									
								<th scope="row"></th>
								<td class="global_msg_td">
									<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
									<?php 
									$li_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
						            print $li_template_str;
						            ?>
									</small></p>
								</td>			
							</tr>
							<?php
							echo apply_filters ( 
												 'wpweb_li_settings_submit_button', 
												 '<tr valign="top">
														<td colspan="2">
															<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
														</td>
													</tr>'
												);?>
						</tbody>
					</table>
				</div><!-- .inside -->
			</div><!-- #grant_permissions -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkein-grant-permission -->
<!-- end of the grant extended permissions meta box -->