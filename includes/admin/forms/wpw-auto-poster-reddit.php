<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
exit;

/**
* Reddit Settings
*
* The html markup for the Reddit settings tab.
*
* @package Social Auto Poster
* @since 1.0.0
*/

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_reddit_postings;


// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty($wpw_auto_poster_options['reddit_posting_cats']) ? $wpw_auto_poster_options['reddit_posting_cats'] : 'exclude';

// Reddit posting class
$redditposting = $wpw_auto_poster_reddit_postings;


$wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data'); // Getting reddit app grant data

$reddit_wp_pretty_url = (!empty($wpw_auto_poster_options['reddit_wp_pretty_url']) ) ? $wpw_auto_poster_options['reddit_wp_pretty_url'] : '';

$reddit_wp_pretty_url = !empty($reddit_wp_pretty_url) ? ' checked="checked"' : '';

$selected_shortner = isset($wpw_auto_poster_options['reddit_url_shortener']) ? $wpw_auto_poster_options['reddit_url_shortener'] : '';

$reddit_wp_pretty_url_css = ( $selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css' : ' ba_wp_pretty_url_css_hide';

// get url shortner service list array 
$reddit_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$reddit_exclude_cats = array();

$reddit_custom_msg_options = isset($wpw_auto_poster_options['reddit_custom_msg_options']) ? $wpw_auto_poster_options['reddit_custom_msg_options'] : 'global_msg';

$reddit_template_text = (!empty($wpw_auto_poster_options['reddit_global_message_template']) ) ? $wpw_auto_poster_options['reddit_global_message_template'] : '';


if ($reddit_custom_msg_options == 'global_msg') {
$post_msg_style = "post_msg_style_hide";
$global_msg_style = "";
} else {
$global_msg_style = "global_msg_style_hide";
$post_msg_style = "";
}

// Getting Reddit All Accounts 
$reddit_accounts = wpw_auto_poster_get_reddit_accounts();
$subreddits_accounts = wpw_auto_poster_get_reddit_accounts_with_subreddits();   
// get all post methods
$wall_post_methods = $model->wpw_auto_poster_get_reddit_posting_method();




?>
<!-- beginning of the reddit general settings meta box -->
<div id="wpw-auto-poster-rd-general" class="post-box-container">
<div class="metabox-holder">	
<div class="meta-box-sortables ui-sortable">
	<div id="reddit_general" class="postbox">	
		<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

		<h3 class="hndle">
			<span class='wpw-sap-reddit-app-settings'><?php esc_html_e('Reddit General Settings', 'wpwautoposter'); ?></span>
		</h3>
		<div class="inside">
			<table class="form-table">											
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="wpw_auto_poster_options[enable_reddit]"><?php esc_html_e('Enable Autoposting : ', 'wpwautoposter'); ?></label>
						</th>
						<td>
							<input name="wpw_auto_poster_options[enable_reddit]" id="wpw_auto_poster_options[enable_reddit]" type="checkbox" value="1" <?php
							if (isset($wpw_auto_poster_options['enable_reddit'])) {
								checked('1', $wpw_auto_poster_options['enable_reddit']);
							}
							?> />
							<p><small><?php esc_html_e('Check this box, if you want to automatically post your new content to Reddit.', 'wpwautoposter'); ?></small></p>
						</td>
					</tr>	
					<tr valign="top">
						<th scope="row">
							<label for="wpw_auto_poster_options[enable_reddit_for]"><?php esc_html_e('Enable Autoposting for : ', 'wpwautoposter'); ?></label>
						</th>
						<td>
							<ul>
								<?php
								$all_types = get_post_types(array('public' => true), 'objects');
								$all_types = is_array($all_types) ? $all_types : array();

								if (!empty($wpw_auto_poster_options['enable_reddit_for'])) {
									$prevent_meta = $wpw_auto_poster_options['enable_reddit_for'];
								} else {
									$prevent_meta = array();
								}

								if (!empty($wpw_auto_poster_options['reddit_post_type_tags'])) {
									$reddit_post_type_tags = $wpw_auto_poster_options['reddit_post_type_tags'];
								} else {
									$reddit_post_type_tags = array();
								}

							
								$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

								if (!empty($wpw_auto_poster_options['reddit_post_type_cats'])) {
									$reddit_post_type_cats = $wpw_auto_poster_options['reddit_post_type_cats'];
								} else {
									$reddit_post_type_cats = array();
								}

								// Get saved categories for fb to exclude from posting
								if (!empty($wpw_auto_poster_options['reddit_exclude_cats'])) {
									$reddit_exclude_cats = $wpw_auto_poster_options['reddit_exclude_cats'];
								}

								foreach ($all_types as $type) {

									if (!is_object($type))
										continue;
									if (isset($type->labels)) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
									} else {
										$label = $type->name;
									}

									if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
										continue; // skip media
									$selected = ( in_array($type->name, $prevent_meta) ) ? 'checked="checked"' : '';
									?>

									<li class="wpw-auto-poster-prevent-types">
										<input type="checkbox" id="wpw_auto_posting_reddit_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_reddit_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo $selected; ?>/>

										<label for="wpw_auto_posting_reddit_prevent_<?php echo $type->name; ?>"><?php echo esc_attr($label); ?></label>
									</li>
								<?php } ?>
							</ul>	
							<p><small><?php esc_html_e('Check each of the post types that you want to post automatically to Reddit when they get published.', 'wpwautoposter'); ?></small></p>  
						</td>	
					</tr> 
					<tr valign="top">
						<th scope="row">
							<label for="wpw_auto_poster_options[reddit_post_type_tags][]"><?php esc_html_e('Select Tags for hashtags : ', 'wpwautoposter'); ?></label> 
						</th>
						<td class="wpw-auto-poster-select">
							<select name="wpw_auto_poster_options[reddit_post_type_tags][]" id="wpw_auto_poster_options[reddit_post_type_tags]" class="rd_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
								<?php
								foreach ($all_types as $type) {

									if (!is_object($type))
										continue;

									if (in_array($type->name, $prevent_meta)) {

										if (isset($type->labels)) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
										} else {
											$label = $type->name;
										}

										if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
												continue; // skip media
											$all_taxonomies = get_object_taxonomies($type->name, 'objects');

											echo '<optgroup label="' . esc_attr($label) . '">';
											// Loop on all taxonomies
											foreach ($all_taxonomies as $taxonomy) {

												$selected = '';
												if (!empty($static_post_type_arr[$type->name]) && $static_post_type_arr[$type->name] != $taxonomy->name) {
													continue;
												}
												if (isset($reddit_post_type_tags[$type->name]) && !empty($reddit_post_type_tags[$type->name])) {
													$selected = ( in_array($taxonomy->name,$reddit_post_type_tags[$type->name]) ) ? 'selected="selected"' : '';
												}
												if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

													echo '<option value="' . esc_attr($type->name . '|' . $taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy->label) . '</option>';

												}
											}
											echo '</optgroup>';
										}
									}
									?>
								</select>
								<div class="wpw-ajax-loader">
									<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif"; ?>"/>
								</div>
								<p><small><?php esc_html_e('Select the Tags for each post type that you want to post as ', 'wpwautoposter'); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter'); ?></b></small></p>
							</td>
						</tr>    
						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_post_type_cats][]"><?php esc_html_e('Select Categories for hashtags : ', 'wpwautoposter'); ?></label> 
							</th>
							<td class="wpw-auto-poster-select">
								<select name="wpw_auto_poster_options[reddit_post_type_cats][]" id="wpw_auto_poster_options[reddit_post_type_cats]" class="rd_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
									<?php
									foreach ($all_types as $type) {

										if (!is_object($type))
											continue;

										if (in_array($type->name, $prevent_meta)) {
											if (isset($type->labels)) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
											} else {
												$label = $type->name;
											}

											if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
												continue; // skip media
											$all_taxonomies = get_object_taxonomies($type->name, 'objects');

											echo '<optgroup label="' . esc_attr($label) . '">';
											// Loop on all taxonomies
											foreach ($all_taxonomies as $taxonomy) {

												$selected = '';
												if (isset($reddit_post_type_cats[$type->name]) && !empty($reddit_post_type_cats[$type->name])) {
													$selected = ( in_array($taxonomy->name,$reddit_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
												}
												if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

													echo '<option value="' . esc_attr($type->name . "|" . $taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy->label) . '</option>';

												}
											}
											echo '</optgroup>';
										}
									}
									?>
								</select>
								<div class="wpw-ajax-loader">
									<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif"; ?>"/>
								</div>
								<p><small><?php esc_html_e('Select the Categories for each post type that you want to post as ', 'wpwautoposter'); ?><b><?php esc_html_e('{hashcats}', 'wpwautoposter'); ?></b></small></p>
							</td>
						</tr>   
						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_exclude_cats][]"><?php esc_html_e('Select Taxonomies : ', 'wpwautoposter'); ?></label>
							</th>
							<td class="wpw-auto-poster-select">
								<div class="wpw-auto-poster-cats-option">
									<input name="wpw_auto_poster_options[reddit_posting_cats]" id="reddit_cats_include" type="radio" value="include" <?php checked('include', $cat_posts_type); ?> />
									<label for="reddit_cats_include"><?php esc_html_e('Include ( Post only with )', 'wpwautoposter'); ?></label>
									<input name="wpw_auto_poster_options[reddit_posting_cats]" id="reddit_cats_exclude" type="radio" value="exclude" <?php checked('exclude', $cat_posts_type); ?> />
									<label for="reddit_cats_exclude"><?php esc_html_e('Exclude ( Do not post )', 'wpwautoposter'); ?></label>
								</div>
								<select name="wpw_auto_poster_options[reddit_exclude_cats][]" id="wpw_auto_poster_options[reddit_exclude_cats]" class="rb_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">

								<?php

									$rd_exclude_cats_selected_values = !empty($wpw_auto_poster_options['reddit_exclude_cats']) ? $wpw_auto_poster_options['reddit_exclude_cats'] : array();
									$selected = 'selected="selected"';

									if(!empty($rd_exclude_cats_selected_values)) {

										foreach ($rd_exclude_cats_selected_values as $post_type => $post_data) {
											
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
								<p><small><?php esc_html_e('Select the Taxonomies for each post type that you want to include or exclude for posting.', 'wpwautoposter'); ?></small></p>
							</td>
						</tr>	
						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_url_shortener]"><?php esc_html_e('URL Shortener : ', 'wpwautoposter'); ?></label> 
							</th>
							<td>
								<select name="wpw_auto_poster_options[reddit_url_shortener]" id="wpw_auto_poster_options[reddit_url_shortener]" class="rd_url_shortener" data-content='rd'>
									<?php
									foreach ($reddit_url_shortener as $key => $option) {
										?>
										<option value="<?php echo $model->wpw_auto_poster_escape_attr($key); ?>" <?php selected($selected_shortner, $key); ?>>
											<?php echo $option; ?>
										</option>
										<?php
									}
									?>
								</select>
								<p><small><?php esc_html_e('Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter'); ?></small></p>
							</td>
						</tr>
						<tr id="row-rd-wp-pretty-url" valign="top" class="<?php print $reddit_wp_pretty_url_css; ?>">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_wp_pretty_url]"><?php esc_html_e('Pretty permalink URL : ', 'wpwautoposter'); ?></label> 
							</th>
							<td>
								<input type="checkbox" name="wpw_auto_poster_options[reddit_wp_pretty_url]" id="wpw_auto_poster_options[reddit_wp_pretty_url]" class="rd_wp_pretty_url" data-content='rd' value="yes" <?php print esc_attr($reddit_wp_pretty_url); ?>>
								<p><small><?php esc_html_e('Check this box if you want to use pretty permalink. i.e. http://example.com/test-post/. (Not Recommnended).', 'wpwautoposter'); ?></small></p>
							</td>
						</tr>
						<?php
						if ($selected_shortner == 'bitly') {
							$class = '';
						} else {
							$class = ' ba_wp_pretty_url_css_hide';
						}

						if ($selected_shortner == 'shorte.st') {
							$shortest_class = '';
						} else {
							$shortest_class = 'ba_wp_pretty_url_css_hide';
						}
						?>

						<tr valign="top" class="rd_setting_input_bitly <?php echo $class; ?>">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
							</th>
							<td>
								<?php
								if(!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['reddit_bitly_access_token'] != ''){
									$redd_bit_token = $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['reddit_bitly_access_token']);
								}else{
									$redd_bit_token = '';
								}
								?>
								<input type="text" name="wpw_auto_poster_options[reddit_bitly_access_token]" id="wpw_auto_poster_options[reddit_bitly_access_token]" value="<?php echo $redd_bit_token; ?>" class="large-text">
							</td>
						</tr>

						<tr valign="top" class="rd_setting_input_shortest <?php echo $shortest_class; ?>">
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
							</th>
							<td>
								<?php 

								if(!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['reddit_shortest_api_token'] != ''){
									$redd_short_token = $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['reddit_shortest_api_token']);
								}else{
									$redd_short_token = '';
								}
								?>
								<input type="text" name="wpw_auto_poster_options[reddit_shortest_api_token]" id="wpw_auto_poster_options[reddit_shortest_api_token]" value="<?php echo $redd_short_token; ?>" class="large-text">
							</td>
						</tr>
						<?php
						echo apply_filters(
							'wpweb_reddit_settings_submit_button', '<tr valign="top">
							<td colspan="2">
							<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '"  name="wpw_auto_poster_set_submit" class="button-primary">
							</td>
							</tr>'
						);
						?>
					</tbody>
				</table>	  				
			</div>	
		</div>
	</div>
</div>               
</div>
<!-- end of the reddit general settings meta box -->	
<!-- beginning of the autopost to reddit meta box -->
<div id="wpw-auto-poster-rd-api" class="post-box-container">
<div class="metabox-holder">	
	<div class="meta-box-sortables ui-sortable">
		<div id="rd_api" class="postbox">	
			<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

			<h3 class="hndle">
				<span class='wpw-sap-rd-app-settings'><?php esc_html_e('Reddit API Settings', 'wpwautoposter'); ?></span>
			</h3>
			<div class="inside">
				<table class="form-table wpw-auto-poster-rd-settings">								
					<tbody>		
						<?php $wpweb_reddit_settings_submit_button = apply_filters( 'wpweb_reddit_settings_submit_button', true ); ?>
						<?php if( $wpweb_reddit_settings_submit_button ){ ?>
							<tr valign="top" class="wpw-auto-poster-facebook-account-details-custom-method <?php echo!empty($reddit_accounts) ? 'wpw-auto-poster-facebook-custom-app-added' : '' ?>"   data-row-id="">
								<td scope="row" class="row-btn" colspan="3">
									<?php
									echo '<p><a href="' . $redditposting->wpw_auto_poster_get_rd_app_method_login_url() . '">' . esc_html__('Add Reddit Account', 'wpwautoposter') . '</a></p>';
									?>
								</td>
							</tr>
						<?php } ?>
						
						<?php if (!empty($reddit_accounts)) { ?>
							<tr>
								<td colspan="3">
									<table class="child-table wpw-auto-poster-table-resposive">
										<thead><tr valign="top">
											<td><strong>
												<?php esc_html_e('User ID', 'wpwautoposter'); ?>
											</strong></td>
											<td><strong>
												<?php esc_html_e('Account Name', 'wpwautoposter'); ?>
											</strong></td>
											<td class="width-16"><strong>
												<?php esc_html_e('Action', 'wpwautoposter'); ?>
											</strong></td>
										</tr></thead>

										<tbody>
											<?php
											foreach( $reddit_accounts as $aid => $aval ) {
										//if( !is_array($aval) ) continue;

												

												$reset_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'reddit_reset_user' => '1', 'wpw_reddit_userid' => $aid), admin_url('admin.php')); ?>

												<tr valign="top" class="wpw-auto-poster-facebook-post-data">
													<td scope="row" width="33.33%" data-label="<?php esc_html_e('User ID', 'wpwautoposter'); ?>"><?php print $aid; ?></td>

													<td scope="row" width="33.33%" data-label="<?php esc_html_e('Account Name', 'wpwautoposter'); ?>"><?php
													print $aval; ?></td>

													<td scope="row" width="33.33%" class="wpw-grant-reset-data wpw-delete-fb-app-method width-16" data-label="<?php esc_html_e('Action', 'wpwautoposter'); ?>">
														<a class='wpw-auto-poster-reddit-app-delete-link' href="<?php print esc_url($reset_url); ?>"><?php esc_html_e('Delete Account', 'wpwautoposter'); ?></a>
													</td>
												</tr>
									<?php }  // End of foreach  
									?>
								</tbody></table>
							</td>
						</tr>
					<?php } ?>	
					
					<?php
					echo apply_filters(
						'wpweb_reddit_settings_submit_button', '<tr valign="top">
						<td colspan="4">
						<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
						</td>
						</tr>'
					);
					?>
				</tbody>	
			</table>
		</div>        
	</div>
</div>
</div>           
</div>


<!-- beginning of the autopost to reddit meta box -->
<div id="wpw-auto-poster-autopost-rd" class="post-box-container">
<div class="metabox-holder">	
	<div class="meta-box-sortables ui-sortable">
		<div id="autopost_rd" class="postbox">	
			<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

			<h3 class="hndle">
				<span class='wpw-sap-reddit-app-settings'><?php esc_html_e('Autopost to Reddit', 'wpwautoposter'); ?></span>
			</h3>

			<div class="inside">
				<table class="form-table">											
					<tbody>
						<tr valign="top"> 
							<th scope="row">
								<label for="wpw_auto_poster_options[prevent_post_reddit_metabox]"><?php esc_html_e('Do not allow individual posts : ', 'wpwautoposter'); ?></label>
							</th>									
							<td>
								<input name="wpw_auto_poster_options[prevent_post_reddit_metabox]" id="wpw_auto_poster_options[prevent_post_reddit_metabox]" type="checkbox" value="1" <?php
								if (isset($wpw_auto_poster_options['prevent_post_reddit_metabox'])) {
									checked('1', $wpw_auto_poster_options['prevent_post_reddit_metabox']);
								}
								?> />
								<p><small><?php esc_html_e('If you check this box, then it will hide meta settings from individual posts.', 'wpwautoposter'); ?></small></p>
							</td>	
						</tr>

						<?php
						$wpw_auto_poster_reddit_user = array();

						$types = get_post_types(array('public' => true), 'objects');
						$types = is_array($types) ? $types : array();
						?>
						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e('Map Autopost Location : ', 'wpwautoposter'); ?></label>
							</th>
							<td>

								<?php
								foreach ($types as $type) {

									if (!is_object($type))
										continue;

									if (isset($wpw_auto_poster_options['reddit_type_' . $type->name . '_method'])) {
										$wpw_auto_poster_reddit_type_method = $wpw_auto_poster_options['reddit_type_' . $type->name . '_method'];
									} else {
										$wpw_auto_poster_reddit_type_method = '';
									}

									if (isset($type->labels)) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
									} else {
										$label = $type->name;
									}

									if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
										continue; // skip media
									?>
									<div class="wpw-auto-poster-fb-types-wrap">
										<div class="wpw-auto-poster-fb-types-label">
											<?php
											esc_html_e('Autopost', 'wpwautoposter');
											echo ' ' . $label;
											esc_html_e(' to Reddit', 'wpwautoposter');
											?>
										</div><!--.wpw-auto-poster-fb-types-label-->

										<div class="wpw-auto-poster-fb-type">
											<select name="wpw_auto_poster_options[reddit_type_<?php echo esc_attr($type->name); ?>_method]" id="wpw_auto_poster_reddit_type_post_method">
												
												<?php
												foreach ( $wall_post_methods as $method_key => $method_value ) {
													echo '<option value="' . esc_attr($method_key) . '" ' . selected( $wpw_auto_poster_reddit_type_method, $method_key, false ) . '>' . esc_html($method_value) . '</option>';
												}
												?>
											</select>
										</div>

										<div class="wpw-auto-poster-fb-user-label">
											<?php esc_html_e('of this user', 'wpwautoposter'); ?>(<?php esc_html_e('s', 'wpwautoposter'); ?>)
										</div><!--.wpw-auto-poster-fb-user-label-->
										<div class="wpw-auto-poster-fb-users-acc">
											<?php
											if (isset($wpw_auto_poster_options['reddit_type_' . $type->name . '_user'])) {
												$wpw_auto_poster_reddit_user = $wpw_auto_poster_options['reddit_type_' . $type->name . '_user'];
											} else {
												$wpw_auto_poster_reddit_user = '';
											}

											$wpw_auto_poster_reddit_user = (array) $wpw_auto_poster_reddit_user;
											?>

											<select name="wpw_auto_poster_options[reddit_type_<?php echo $type->name; ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
												<?php
												if(!empty($subreddits_accounts) && is_array($subreddits_accounts)) {
													foreach($subreddits_accounts as $aval_key => $aval_data) {
															$main_account_details = explode('|', $aval_data['main-account']);
															$main_account_name = !empty( $main_account_details[1] ) ? $main_account_details[1] : '';	
														?>											
														<optgroup label="<?php echo esc_attr($main_account_name); ?>" >
															<option value="<?php echo esc_attr($aval_data['main-account']); ?>" <?php selected(in_array($aval_data['main-account'] , $wpw_auto_poster_reddit_user), true, true ); ?> ><?php echo esc_attr($main_account_name); ?></option>
															<?php if (!empty($aval_data['subreddits']) && is_array($aval_data['subreddits'])) { 
																foreach($aval_data['subreddits'] as $sr_key => $sr_data) { ?>
																	<option value="<?php echo esc_attr($sr_key); ?>" <?php selected(in_array($sr_key, $wpw_auto_poster_reddit_user), true, true ); ?> ><?php echo esc_attr($sr_data); ?></option>
															<?php }
															  } 
															?>	
														</optgroup>
														<?php
													}	
												} 
												
												?>
											</select>
										</div><!--.wpw-auto-poster-reddit-users-acc-->
									</div><!--.wpw-auto-poster-reddit-types-wrap-->
								<?php } ?>

							</td>
						</tr> 

						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e('Posting Format Options : ', 'wpwautoposter'); ?></label>
							</th>
							<td>
								<input id="rd_custom_global_msg" type="radio" name="wpw_auto_poster_options[reddit_custom_msg_options]" value="global_msg" <?php checked($reddit_custom_msg_options, 'global_msg', true); ?> class="custom_msg_options">
								<label for="reddit_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e('Global', 'wpwautoposter'); ?></label>

								<input id="rd_custom_post_msg" type="radio" name="wpw_auto_poster_options[reddit_custom_msg_options]" value="post_msg" <?php checked($reddit_custom_msg_options, 'post_msg', true); ?> class="custom_msg_options">
								<label for="rd_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e('Individual Post Type Message', 'wpwautoposter'); ?></label>
							</td>	
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row">
								<label for="wpw_auto_poster_options_reddit_post_image"><?php esc_html_e('Post Image : ', 'wpwautoposter'); ?></label>
							</th>
							<td>
								<input type="text" name="wpw_auto_poster_options[reddit_post_image]" id="wpw_auto_poster_options_rd_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo!empty($wpw_auto_poster_options['reddit_post_image']) ? $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['reddit_post_image']) : ''; ?>">
								<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
								<p><small><?php echo sprintf(esc_html__('Here you can upload a default image which will be used for the Reddit posting.', 'wpwautoposter'),"<b>","</b>"); ?></small></p>
							</td>	
						</tr>

						<tr valign="top" class="global_title_rd <?php echo esc_attr($global_title_style); ?>">									
								<th scope="row">
									<label for="wpw_auto_poster_options[rd_global_title_template]"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
								</th>
								<td class="form-table-td">
								<?php 				
									$rd_template_title_text = ( !empty( $wpw_auto_poster_options['rd_global_title_template'] ) ) ? $wpw_auto_poster_options['rd_global_title_template'] : '';
								?>
									<input type="text" name="wpw_auto_poster_options[rd_global_title_template]" id="wpw_auto_poster_options[rd_global_title_template]" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $rd_template_title_text ); ?>">
								</td>	
								
							</tr>
							<tr valign="top">									
								<th scope="row"></th>
								<td class="global_msg_td">
									<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter' ); ?>
									<?php 
									$rd_template_title_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
						            print $rd_template_title_str;
						            ?>
									</small></p>
								</td>			
							</tr>						


						<tr valign="top" class="global_msg_tr <?php echo $global_msg_style; ?>">									
							<th scope="row">
								<label for="wpw_auto_poster_options[reddit_global_message_template]"><?php esc_html_e('Custom Message : ', 'wpwautoposter'); ?></label>
							</th>
							<td class="form-table-td">
								<textarea type="text" name="wpw_auto_poster_options[reddit_global_message_template]" id="wpw_auto_poster_options[reddit_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr($reddit_template_text); ?></textarea>
							</td>	

						</tr>

						<tr id="custom_post_type_templates_rd" class="post_msg_tr <?php echo $post_msg_style; ?>">
							<th colspan="2" class="form-table-td">
								<ul>
									<?php
									$all_types = get_post_types(array('public' => true), 'objects');
									$all_types = is_array($all_types) ? $all_types : array();

									foreach ($all_types as $type) {

										if (!is_object($type))
											continue;
										if (isset($type->labels)) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
										} else {
											$label = $type->name;
										}

										if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
											continue; // skip media
										?>
										<li><a href="#tabs-<?php echo $type->name; ?>"><?php echo $label; ?></a></li>
									<?php } ?>

								</ul>
								<?php
								foreach ($all_types as $type) {

									if (!is_object($type))
										continue;
									if (isset($type->labels)) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
									} else {
										$label = $type->name;
									}

									if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
										continue; // skip media
									$postImg = ( isset($wpw_auto_poster_options['reddit_post_image_' . $type->name]) ) ? $wpw_auto_poster_options['reddit_post_image_' . $type->name] : '';

									$postMsg = ( isset($wpw_auto_poster_options['reddit_global_message_template_' . $type->name]) ) ? $wpw_auto_poster_options['reddit_global_message_template_' . $type->name] : '';
									?>
									<table id="tabs-<?php echo $type->name; ?>">
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_reddit_post_image_<?php echo $type->name; ?>"><?php esc_html_e('Post Image : ', 'wpwautoposter'); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_options[reddit_post_image_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_reddit_post_image_<?php echo $type->name; ?>" class="large-text wpw-auto-poster-img-field" value="<?php echo $postImg; ?>">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
												<p><small><?php esc_html_e('Here you can upload a default image which will be used for the Reddit post.', 'wpwautoposter'); ?></small></p>
											</td>	
										</tr>

										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_posting_reddit_custom_msg_<?php echo $type->name; ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
											</th>

											<td class="form-table-td">
												<textarea type="text" name="wpw_auto_poster_options[reddit_global_message_template_<?php echo $type->name; ?>]" id="wpw_auto_posting_reddit_custom_msg_<?php echo $type->name; ?>" class="large-text"><?php echo $postMsg; ?></textarea>
											</td>	
										</tr>
									</table>	
								<?php } ?>
							</th>
						</tr>	

						<tr valign="top">									
							<th scope="row"></th>
							<td class="global_msg_td">
								<p><small class="wpw-sap-custom-message"><?php esc_html_e('Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter'); ?>
								<?php
								$li_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
								'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
								'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
								'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
								'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
								'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
								'<br /><b><code>{post_type}</code></b> - ' . esc_html__(' displays the post type.', 'wpwautoposter') .
								'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
								'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
								'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
								'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
								'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
								'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>") .
								'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>");
								print $li_template_str;
								?>
							</small></p>
						</td>			
					</tr>

					<?php
					echo apply_filters(
						'wpweb_reddit_settings_submit_button', '<tr valign="top">
						<td colspan="2">
						<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
						</td>
						</tr>'
					);
					?>
				</tbody>
			</table>

		</div><!-- .inside -->
	</div><!-- #autopost_reddit -->
</div><!-- .meta-box-sortables ui-sortable -->
</div><!-- .metabox-holder -->
</div><!-- #ps-poster-autopost-reddit -->
<!-- end of the autopost to reddit meta box -->







