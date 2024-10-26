<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * Medium Settings
 *
 * The html markup for the Medium settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model,$wpw_auto_poster_medium_posting;


// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty($wpw_auto_poster_options['medium_posting_cats']) ? $wpw_auto_poster_options['medium_posting_cats'] : 'exclude';

//Medium posting class
$mediumposting = $wpw_auto_poster_medium_posting;


$wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data'); // Getting medium app grant data

$medium_wp_pretty_url = (!empty($wpw_auto_poster_options['medium_wp_pretty_url']) ) ? $wpw_auto_poster_options['medium_wp_pretty_url'] : '';

$medium_wp_pretty_url = !empty($medium_wp_pretty_url) ? ' checked="checked"' : '';

$selected_shortner = isset($wpw_auto_poster_options['medium_url_shortener']) ? $wpw_auto_poster_options['medium_url_shortener'] : '';

$medium_wp_pretty_url_css = ( $selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css' : ' ba_wp_pretty_url_css_hide';

// get url shortner service list array
$medium_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$medium_exclude_cats = array();

$medium_custom_msg_options = isset($wpw_auto_poster_options['medium_custom_msg_options']) ? $wpw_auto_poster_options['medium_custom_msg_options'] : 'global_msg';

$medium_template_text = (!empty($wpw_auto_poster_options['medium_global_message_template']) ) ? $wpw_auto_poster_options['medium_global_message_template'] : '';


if ($medium_custom_msg_options == 'global_msg') {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else {
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
}

// Getting Medium All Accounts
//$medium_accounts = wpw_auto_poster_get_medium_accounts();
$medium_accounts		 = wpw_auto_poster_get_medium_accounts();
$medium_posting_accounts = wpw_auto_poster_get_medium_accounts_with_publications(); ?>

<!-- beginning of the medium general settings meta box -->
<div id="wpw-auto-poster-md-general" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="medium_general" class="postbox">
				<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>
				<h3 class="hndle">
						<span class='wpw-sap-medium-app-settings'><?php esc_html_e('Medium General Settings', 'wpwautoposter'); ?></span>
				</h3>
				<div class="inside">
						<table class="form-table">
								<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_medium]"><?php esc_html_e('Enable Autoposting : ', 'wpwautoposter'); ?></label>
									</th>
									<td>
										<input name="wpw_auto_poster_options[enable_medium]" id="wpw_auto_poster_options[enable_medium]" type="checkbox" value="1" <?php
										if (isset($wpw_auto_poster_options['enable_medium'])) {
											checked('1', $wpw_auto_poster_options['enable_medium']);
										}
										?> />
										<p><small><?php esc_html_e('Check this box, if you want to automatically post your new content to Medium.', 'wpwautoposter'); ?></small></p>
									</td>
						        </tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_medium_for]"><?php esc_html_e('Enable Autoposting for : ', 'wpwautoposter'); ?></label>
									</th>
									<td>
										<ul>
											<?php
											$all_types = get_post_types(array('public' => true), 'objects');
											$all_types = is_array($all_types) ? $all_types : array();

											if (!empty($wpw_auto_poster_options['enable_medium_for'])) {
												$prevent_meta = $wpw_auto_poster_options['enable_medium_for'];
											} else {
												$prevent_meta = array();
											}

											if (!empty($wpw_auto_poster_options['medium_post_type_tags'])) {
												$medium_post_type_tags = $wpw_auto_poster_options['medium_post_type_tags'];
											} else {
												$medium_post_type_tags = array();
											}




											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();


											if (!empty($wpw_auto_poster_options['medium_post_type_cats'])) {
												$medium_post_type_cats = $wpw_auto_poster_options['medium_post_type_cats'];
											} else {
												$medium_post_type_cats = array();
											}

											// Get saved categories for fb to exclude from posting
											if (!empty($wpw_auto_poster_options['medium_exclude_cats'])) {
												$medium_exclude_cats = $wpw_auto_poster_options['medium_exclude_cats'];
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
													<input type="checkbox" id="wpw_auto_posting_medium_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_medium_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo $selected; ?>/>

													<label for="wpw_auto_posting_medium_prevent_<?php echo $type->name; ?>"><?php echo esc_attr($label); ?></label>
												</li>
											<?php } ?>
										</ul>
										<p><small><?php esc_html_e('Check each of the post types that you want to post automatically to Medium when they get published.', 'wpwautoposter'); ?></small></p>
									</td>
                                </tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[medium_post_type_tags][]"><?php esc_html_e('Select Tags for hashtags :', 'wpwautoposter'); ?></label>
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[medium_post_type_tags][]" id="wpw_auto_poster_options[medium_post_type_tags]" class="md_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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

															if (!empty($static_post_type_arr[$type->name]) && $static_post_type_arr[$type->name] != $taxonomy->name) {
																continue;
															}
															if (isset($medium_post_type_tags) && !empty($medium_post_type_tags)) {

																$select_tag = $type->name."|".$taxonomy->name;
																$selected = ( in_array($select_tag,$medium_post_type_tags) ) ? 'selected="selected"' : '';
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
	                            		<label for="wpw_auto_poster_options[medium_post_type_cats][]"><?php esc_html_e('Select Categories for hashtags : ', 'wpwautoposter'); ?></label>
	                            	</th>
	                            	<td class="wpw-auto-poster-select">
	                            		<select name="wpw_auto_poster_options[medium_post_type_cats][]" id="wpw_auto_poster_options[medium_post_type_cats]" class="md_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
	                                                	if (isset($medium_post_type_cats) && !empty($medium_post_type_cats)) {

															$selected_cat = $type->name."|".$taxonomy->name;
	                                                		$selected = ( in_array($selected_cat,$medium_post_type_cats) ) ? 'selected="selected"' : '';
	                                                	}
	                                                	if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

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
	                                    <p><small><?php esc_html_e('Select the Categories for each post type that you want to post as ', 'wpwautoposter'); ?><b><?php esc_html_e('{hashcats}.', 'wpwautoposter'); ?></b></small></p>
	                                </td>
	                            </tr>
								<tr valign="top">
	                            	<th scope="row">
									<label for="wpw_auto_poster_options[medium_exclude_cats][]"><?php esc_html_e('Select Taxonomies : ', 'wpwautoposter'); ?></label>
	                            	</th>
	                            	<td class="wpw-auto-poster-select">
	                            		<div class="wpw-auto-poster-cats-option">
                                            <input name="wpw_auto_poster_options[medium_posting_cats]" id="medium_cats_include" type="radio" value="include" <?php checked('include', $cat_posts_type); ?> />
                                            <label for="medium_cats_include"><?php esc_html_e('Include ( Post only with )', 'wpwautoposter'); ?></label>
                                            <input name="wpw_auto_poster_options[medium_posting_cats]" id="medium_cats_exclude" type="radio" value="exclude" <?php checked('exclude', $cat_posts_type); ?> />
                                            <label for="medium_cats_exclude"><?php esc_html_e('Exclude ( Do not post )', 'wpwautoposter'); ?></label>
                                        </div>
									<select name="wpw_auto_poster_options[medium_exclude_cats][]" id="wpw_auto_poster_options[medium_exclude_cats]" class="medium_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">

									<?php

										$md_exclude_cats_selected_values = !empty($wpw_auto_poster_options['medium_exclude_cats']) ? $wpw_auto_poster_options['medium_exclude_cats'] : array();
										$selected = 'selected="selected"';

										if(!empty($md_exclude_cats_selected_values)) {

											foreach ($md_exclude_cats_selected_values as $post_type => $post_data) {

												$cat_details = explode("|",$post_data);
												$post_type = $cat_details[0];
												$cat_data = $cat_details[1];

												$term              = get_term( $cat_data );
												$get_taxonomy_data = get_taxonomy( $term->taxonomy );
												$cat_name          = $get_taxonomy_data->label." : ".$term->name;
												echo '<option value="' . esc_attr($post_type) . "|" . esc_attr($cat_data) . '" ' . esc_attr($selected) . '>' . esc_html($cat_name) . '</option>';
												
	

											}    

										}

								?>

	                            		</select>
	                            		<p><small><?php esc_html_e('Select the Taxonomies for each post type that you want to include or exclude for posting.', 'wpwautoposter'); ?></small></p>
	                            	</td>
	                            </tr>
								<tr valign="top">
	                            	<th scope="row">
	                            		<label for="wpw_auto_poster_options[medium_url_shortener]"><?php esc_html_e('URL Shortener : ', 'wpwautoposter'); ?></label>
	                            	</th>
	                            	<td>
	                            		<select name="wpw_auto_poster_options[medium_url_shortener]" id="wpw_auto_poster_options[medium_url_shortener]" class="md_url_shortener" data-content='md'>
	                            			<?php
	                            			foreach ($medium_url_shortener as $key => $option) {
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
								 <tr id="row-md-wp-pretty-url" valign="top" class="<?php print $medium_wp_pretty_url_css; ?>">
	                            	<th scope="row">
	                            		<label for="wpw_auto_poster_options[medium_wp_pretty_url]"><?php esc_html_e('Pretty permalink URL : ', 'wpwautoposter'); ?></label>
	                            	</th>
	                            	<td>
	                            		<input type="checkbox" name="wpw_auto_poster_options[medium_wp_pretty_url]" id="wpw_auto_poster_options[medium_wp_pretty_url]" class="rd_wp_pretty_url" data-content='rd' value="yes" <?php print esc_attr($medium_wp_pretty_url); ?>>
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

	                            <tr valign="top" class="md_setting_input_bitly <?php echo $class; ?>">
	                            	<th scope="row">
	                            		<label for="wpw_auto_poster_options[medium_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
	                            	</th>
	                            	<td>
	                            		<?php
	                            		if(!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['medium_bitly_access_token'] != ''){
	                            			$medd_bit_token = $wpw_auto_poster_options['medium_bitly_access_token'];
	                            		}else{
	                            			$medd_bit_token = '';
	                            		}
	                            		?>
	                            		<input type="text" name="wpw_auto_poster_options[medium_bitly_access_token]" id="medium_bitly_access_token" value="<?php echo $medd_bit_token; ?>" class="large-text"/>
	                            	</td>
	                            </tr>

	                            <tr valign="top" class="md_setting_input_shortest <?php echo $shortest_class; ?>">
	                            	<th scope="row">
	                            		<label for="wpw_auto_poster_options[medium_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
	                            	</th>
	                            	<td>
	                            		<?php

	                            		if(!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['medium_shortest_api_token'] != ''){
	                            			$medd_short_token = $wpw_auto_poster_options['medium_shortest_api_token'];
	                            		}else{
	                            			$medd_short_token = '';
	                            		}
	                            		?>
	                            		<input type="text" name="wpw_auto_poster_options[medium_shortest_api_token]" id="wpw_auto_poster_options[medium_shortest_api_token]" value="<?php echo $medd_short_token; ?>" class="large-text">
	                            	</td>
	                            </tr>

								<?php
	                            echo apply_filters(
	                            	'wpweb_medium_settings_submit_button', '<tr valign="top">
	                            	<td colspan="2">
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
	<div id="wpw-auto-poster-md-api" class="post-box-container">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="md_api" class="postbox">
    				<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>
					<h3 class="hndle">
								<span class='wpw-sap-md-app-settings'><?php esc_html_e('Medium API Settings', 'wpwautoposter'); ?></span>
					</h3>
					<div class="inside">
							<div class="wpw-auto-poster-error info"><ul><li><?php print esc_html__( 'Before adding an account, make sure that you are already logged in to your Medium account on a same browser.', 'wpwautoposter' ); ?></li></ul></div>
							<?php $md_account_button = apply_filters( 'wpweb_md_account_button', true ); ?>
                   			<?php if( $md_account_button ){ ?>
								<table class="form-table wpw-auto-poster-md-settings">
									<tbody>
										<tr valign="top" class="wpw-auto-poster-facebook-account-details-custom-method <?php echo!empty($medium_accounts) ? 'wpw-auto-poster-facebook-custom-app-added' : '' ?>"   data-row-id="">
											<td scope="row" class="row-btn" colspan="3">
												<?php
													echo '<p><a href="' . $mediumposting->wpw_auto_poster_medium_auth_url() . '">' . esc_html__('Add Medium Account', 'wpwautoposter') . '</a></p>';
												?>
											</td>
										</tr>
										<?php if (!empty($medium_accounts)) { ?>
								    	<tr>
											<td colspan="3">
												<table class="child-table wpw-auto-poster-table-resposive">
													<thead><tr valign="top">
														<td><strong>
																<?php esc_html_e('User Name', 'wpwautoposter'); ?>
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
														foreach( $medium_accounts as $aid => $aval ) {
													//if( !is_array($aval) ) continue;



															$reset_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'medium_reset_user' => '1', 'wpw_medium_userid' => $aid), admin_url('admin.php')); ?>

															<tr valign="top" class="wpw-auto-poster-facebook-post-data">
																<td scope="row" width="33.33%" data-label="<?php esc_html_e('User Name', 'wpwautoposter'); ?>"><?php print $aval['display_name']; ?></td>

																<td scope="row" width="33.33%" data-label="<?php esc_html_e('Account Name', 'wpwautoposter'); ?>"><?php
																print $aval['name']; ?></td>

																<td scope="row" width="33.33%" class="wpw-grant-reset-data wpw-delete-fb-app-method width-16" data-label="<?php esc_html_e('Action', 'wpwautoposter'); ?>">
																	<a class='wpw-auto-poster-medium-app-delete-link' href="<?php print esc_url($reset_url); ?>"><?php esc_html_e('Delete Account', 'wpwautoposter'); ?></a>
																</td>
															</tr>
												<?php }  // End of foreach  ?>

											</tbody></table>
										</td>
                                      </tr>
									<?php } ?>
									<?php
											echo apply_filters(
														'wpweb_medium_settings_submit_button', '<tr valign="top">
														<td colspan="4">
														<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
														</td>
														</tr>'
											);
                           			?>
									</tbody>
								</table>
							<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="wpw-auto-poster-autopost-md" class="post-box-container">
	<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="autopost_md" class="postbox">
					<div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>
					<h3 class="hndle">
							<span class='wpw-sap-medium-app-settings'><?php esc_html_e('Autopost to Medium', 'wpwautoposter'); ?></span>
					</h3>
					<div class="inside">
						<div class="wpw-auto-poster-error info"><ul><li><?php print esc_html__( 'You will be not able to post featured image as the Medium API doesn\'t included with "uploadImage" extended permission.', 'wpwautoposter' ); ?></li></ul></div>
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row">
											<label for="wpw_auto_poster_options[prevent_post_medium_metabox]"><?php esc_html_e('Do not allow individual posts : ', 'wpwautoposter'); ?></label>
										</th>
										<td>
											<input name="wpw_auto_poster_options[prevent_post_medium_metabox]" id="wpw_auto_poster_options[prevent_post_medium_metabox]" type="checkbox" value="1" <?php
											if (isset($wpw_auto_poster_options['prevent_post_medium_metabox'])) {
												checked('1', $wpw_auto_poster_options['prevent_post_medium_metabox']);
											}
											?> />
											<p><small><?php esc_html_e('If you check this box, then it will hide meta settings for Medium from individual posts.', 'wpwautoposter'); ?></small></p>
										</td>
									</tr>
									<?php
										$wpw_auto_poster_medium_user = array();

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
													esc_html_e(' to Medium', 'wpwautoposter');
													?>
												</div><!--.wpw-auto-poster-fb-types-label-->


												<div class="wpw-auto-poster-fb-user-label">
													<?php esc_html_e('of this user', 'wpwautoposter'); ?>(<?php esc_html_e('s', 'wpwautoposter'); ?>)
												</div><!--.wpw-auto-poster-fb-user-label-->
												<div class="wpw-auto-poster-fb-users-acc">
													<?php
													if (isset($wpw_auto_poster_options['medium_type_' . $type->name . '_user'])) {
														$wpw_auto_poster_medium_user = $wpw_auto_poster_options['medium_type_' . $type->name . '_user'];
													} else {
														$wpw_auto_poster_medium_user = '';
													}
													$wpw_auto_poster_medium_user = (array) $wpw_auto_poster_medium_user;
													?>

													<select name="wpw_auto_poster_options[medium_type_<?php echo $type->name; ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
														<?php
															if(!empty($medium_posting_accounts) && is_array($medium_posting_accounts)) {
																foreach($medium_posting_accounts as $aval_key => $aval_data) {
																	?>
															     		<option value="<?php echo esc_attr($aval_key); ?>" <?php selected(in_array($aval_key, $wpw_auto_poster_medium_user), true, true ); ?> ><?php echo esc_attr($aval_data); ?></option>
																	<?php
																}
															}
														?>
													</select>
												</div><!--.wpw-auto-poster-medium-users-acc-->
											</div><!--.wpw-auto-poster-medium-types-wrap-->
										<?php } ?>
									</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label><?php esc_html_e('Posting Format Options : ', 'wpwautoposter'); ?></label>
										</th>
										<td>
											<input id="md_custom_global_msg" type="radio" name="wpw_auto_poster_options[medium_custom_msg_options]" value="global_msg" <?php checked($medium_custom_msg_options, 'global_msg', true); ?> class="custom_msg_options">
											<label for="md_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e('Global', 'wpwautoposter'); ?></label>

											<input id="md_custom_post_msg" type="radio" name="wpw_auto_poster_options[medium_custom_msg_options]" value="post_msg" <?php checked($medium_custom_msg_options, 'post_msg', true); ?> class="custom_msg_options">
											<label for="md_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e('Individual Post Type Message', 'wpwautoposter'); ?></label>
										</td>
								    </tr>

									<tr valign="top" class="global_title_tr <?php echo esc_attr($global_title_style); ?>">									
										<th scope="row">
											<label for="wpw_auto_poster_options[medium_global_title_template]"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
										</th>
										<td class="form-table-td">
											<?php 
											$medium_template_title_text = ( !empty( $wpw_auto_poster_options['medium_global_title_template'] ) ) ? $wpw_auto_poster_options['medium_global_title_template'] : '';	
											?>
											<input type="text" name="wpw_auto_poster_options[medium_global_title_template]" id="wpw_auto_poster_options[medium_global_title_template]" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $medium_template_title_text ); ?>">
										</td>	
										
									</tr>
									<tr valign="top">									
										<th scope="row"></th>
										<td class="global_msg_td">
											<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter' ); ?>
											<?php 
											$medium_template_title_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
											'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
											'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
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
											print $medium_template_title_str;
											?>
											</small></p>
										</td>			
									</tr>

									<tr valign="top" class="global_msg_tr <?php echo $global_msg_style; ?>">
										<th scope="row">
											<label for="wpw_auto_poster_options[medium_global_message_template]"><?php esc_html_e('Custom Message : ', 'wpwautoposter'); ?></label>
										</th>
										<td class="form-table-td">
											<textarea type="text" name="wpw_auto_poster_options[medium_global_message_template]" id="wpw_auto_poster_options[medium_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr($medium_template_text); ?></textarea>
										</td>
									</tr>

									<tr id="custom_post_type_templates_md" class="post_msg_tr <?php echo $post_msg_style; ?>">
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

												$postMsg = ( isset($wpw_auto_poster_options['medium_global_message_template_' . $type->name]) ) ? $wpw_auto_poster_options['medium_global_message_template_' . $type->name] : '';
												?>
												<table id="tabs-<?php echo $type->name; ?>">
													<tr valign="top">
														<th scope="row">
															<label for="wpw_auto_posting_medium_custom_msg_<?php echo $type->name; ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
														</th>

														<td class="form-table-td">
															<textarea type="text" name="wpw_auto_poster_options[medium_global_message_template_<?php echo $type->name; ?>]" id="wpw_auto_posting_medium_custom_msg_<?php echo $type->name; ?>" class="large-text"><?php echo $postMsg; ?></textarea>
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
											'wpweb_medium_settings_submit_button', '<tr valign="top">
											<td colspan="2">
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
