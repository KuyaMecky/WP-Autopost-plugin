<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * WordPress Settings
 *
 * The html markup for the WordPress settings tab.
 *
 * @package Social Auto Poster
 * @since 3.4.1
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_wp_posting;

// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['wp_posting_cats'] ) ? $wpw_auto_poster_options['wp_posting_cats']: 'exclude';

// wordpress posting class
$wpposting = $wpw_auto_poster_wp_posting;

$wp_wp_pretty_url = (!empty($wpw_auto_poster_options['wp_wp_pretty_url']) ) ? $wpw_auto_poster_options['wp_wp_pretty_url'] : '';

$wp_wp_pretty_url = !empty($wp_wp_pretty_url) ? ' checked="checked"' : '';


$wp_selected_shortner = isset($wpw_auto_poster_options['wp_url_shortener']) ? $wpw_auto_poster_options['wp_url_shortener'] : '';

$wp_wp_pretty_url_css = ( $wp_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css' : ' ba_wp_pretty_url_css_hide';


$wp_url_shortener = $model->wpw_auto_poster_get_shortner_list();

$wp_postTitle = ( isset($wpw_auto_poster_options['wp_global_title']) ) ? $wpw_auto_poster_options['wp_global_title'] : '';

$wp_postImg = ( isset($wpw_auto_poster_options['wp_post_image']) ) ? $wpw_auto_poster_options['wp_post_image'] : '';

$wp_template_text = ( !empty($wpw_auto_poster_options['wp_global_message_template']) ) ? $wpw_auto_poster_options['wp_global_message_template'] : '';

$wp_custom_msg_options = isset( $wpw_auto_poster_options['wp_custom_msg_options'] ) ? $wpw_auto_poster_options['wp_custom_msg_options'] : 'global_msg';

if( $wp_custom_msg_options == 'global_msg') {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
}

// Get wordpress sites
$wordpress_sites = isset( $wpw_auto_poster_options['wordpress_sites'] ) ? $wpw_auto_poster_options['wordpress_sites'] : array(); ?>

<!-- beginning of the wordpress general settings meta box -->
<div id="wpw-auto-poster-wordpress-general" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="wordpress_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'WordPress General Settings', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table"><tbody>
						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[enable_wordpress]">
								<?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input name="wpw_auto_poster_options[enable_wordpress]" id="wpw_auto_poster_options[enable_wordpress]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_wordpress'] ) ) { checked( '1', $wpw_auto_poster_options['enable_wordpress'] ); } ?> />
								<p><small><?php esc_html_e( 'Check this box, if you want to automatically post your new content to another WordPress websites.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[enable_wordpress_for]">
								<?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<ul>
								<?php 
									$all_types = get_post_types( array( 'public' => true ), 'objects');
									$all_types = is_array( $all_types ) ? $all_types : array();
									
									$prevent_meta = array();
									if( !empty( $wpw_auto_poster_options['enable_wordpress_for'] ) ) {
										$prevent_meta = $wpw_auto_poster_options['enable_wordpress_for'];
									}
													
									$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

									$wp_post_type_tags = array();
									if( !empty($wpw_auto_poster_options['wp_post_type_tags']) ) {
										$wp_post_type_tags = $wpw_auto_poster_options['wp_post_type_tags'];
									}

									$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

									$wp_post_type_cats = array();
									if( !empty( $wpw_auto_poster_options['wp_post_type_cats'] ) ) {
										$wp_post_type_cats = $wpw_auto_poster_options['wp_post_type_cats'];
									}

									// Get saved categories for linkedin to exclude from posting
									if( !empty( $wpw_auto_poster_options['wp_exclude_cats'] ) ) {
										$wp_exclude_cats = $wpw_auto_poster_options['wp_exclude_cats'];
									} 
												
									foreach( $all_types as $type ) {

										if( !is_object($type) ) continue;

										if( isset($type->labels) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
							            } else {
							            	$label = $type->name;
							            }

										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

										$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : ''; ?>
													
										<li class="wpw-auto-poster-prevent-types">
											<input type="checkbox" id="wpw_auto_posting_wordpress_prevent_<?php echo $type->name; ?>" name="wpw_auto_poster_options[enable_wordpress_for][]" value="<?php echo $type->name; ?>" <?php echo $selected; ?>/>					
											<label for="wpw_auto_posting_wordpress_prevent_<?php echo $type->name; ?>"><?php echo $label; ?></label>
										</li>
									<?php } ?>
								</ul>
								<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to WordPress when they get published.', 'wpwautoposter' ); ?></small></p>  
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"> <label for="wpw_auto_poster_options[wp_post_type_tags][]">
								<?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<select name="wpw_auto_poster_options[wp_post_type_tags][]" id="wpw_auto_poster_options[wp_post_type_tags]" class="wp_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
									<?php
									foreach( $all_types as $type ) {
										
										if( !is_object($type) ) continue;

										if( in_array($type->name, $prevent_meta) ) {
											if( isset($type->labels) ) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
								            } else {
								            	$label = $type->name;
								            }

											if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
											$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
        							
        									echo '<optgroup label="'.$label.'">';
							                // Loop on all taxonomies
							                foreach( $all_taxonomies as $taxonomy ) {
							                	$selected = '';
							                	if( !empty( $static_post_type_arr[$type->name] ) && $static_post_type_arr[$type->name] != $taxonomy->name){
                 										continue;
        										}
							                	if( !empty($wp_post_type_tags[$type->name]) ) {
							                		$selected = ( in_array( $taxonomy->name, $wp_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
							                	}

							                    if( is_object($taxonomy) && $taxonomy->hierarchical != 1 ) {
							                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
							                    }
							                }
							                echo '</optgroup>';
							            }
									} ?>
								</select>
								<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
								<p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[wp_post_type_cats][]">
								<?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<select name="wpw_auto_poster_options[wp_post_type_cats][]" id="wpw_auto_poster_options[wp_post_type_cats]" class="wp_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
									<?php
									foreach( $all_types as $type ) {	
										
										if( !is_object($type) ) continue;	

										if( in_array($type->name, $prevent_meta) ) {
											if( isset($type->labels) ) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
								            } else {
								            	$label = $type->name;
								            }

											if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

											$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
        							
        									echo '<optgroup label="'.$label.'">';
							                // Loop on all taxonomies
							                foreach( $all_taxonomies as $taxonomy ) {

							                	$selected = '';
							                	if( isset($wp_post_type_cats[$type->name]) && !empty($wp_post_type_cats[$type->name]) ) {
							                		$selected = ( in_array( $taxonomy->name, $wp_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
							                	}

							                    if( is_object($taxonomy) && $taxonomy->hierarchical == 1 ) {
							                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
							                    }
							                }
							                echo '</optgroup>';
							            }
									} ?>
								</select>
								<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
								<p><small><?php esc_html_e( 'Select the Categories for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashcats}.', 'wpwautoposter' );?></b></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[wp_exclude_cats][]">
								<?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<div class="wpw-auto-poster-cats-option">
									<input name="wpw_auto_poster_options[wp_posting_cats]" id="wp_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
									<label for="wp_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
									<input name="wpw_auto_poster_options[wp_posting_cats]" id="wp_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
									<label for="wp_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
								</div>
								<select name="wpw_auto_poster_options[wp_exclude_cats][]" id="wpw_auto_poster_options[wp_exclude_cats]" class="wp_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">
								<?php

										$wp_exclude_cats_selected_values = !empty($wpw_auto_poster_options['wp_exclude_cats']) ? $wpw_auto_poster_options['wp_exclude_cats'] : array();
										$selected = 'selected="selected"';

										if(!empty($wp_exclude_cats_selected_values)) {

											foreach ($wp_exclude_cats_selected_values as $post_type => $post_data) {
												
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
							<th scope="row"><label for="wpw_auto_poster_options[wp_url_shortener]">
								<?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td>
								<select name="wpw_auto_poster_options[wp_url_shortener]" id="wpw_auto_poster_options[wp_url_shortener]" class="wp_url_shortener" data-content='wp'>
									<?php
									foreach( $wp_url_shortener as $key => $option ) { ?>
										<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $wp_selected_shortner, $key ); ?>>
											<?php echo $option; ?>
										</option>
									<?php
									} ?>
								</select>
								<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr id="row-wp-wp-pretty-url" valign="top" class="<?php print $wp_wp_pretty_url_css;?>">
							<th scope="row"><label for="wpw_auto_poster_options[wp_wp_pretty_url]">
								<?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="checkbox" name="wpw_auto_poster_options[wp_wp_pretty_url]" id="wpw_auto_poster_options[wp_wp_pretty_url]" class="wp_wp_pretty_url" data-content='wp' value="yes" <?php print $wp_wp_pretty_url;?>>
								<p><small><?php esc_html_e( 'Check this box if you want to use pretty permalink. i.e. http://example.com/test-post/. (Not Recommnended).', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<?php
						$class = '';
                        if( $wp_selected_shortner != 'bitly' ) {
                            $class = ' ba_wp_pretty_url_css_hide';
                        }

                        $shortest_class = '';
                        if( $wp_selected_shortner != 'shorte.st' ) {
                            $shortest_class = 'ba_wp_pretty_url_css_hide';
                        } ?>

                        <tr valign="top" class="wp_setting_input_bitly <?php echo $class; ?>">
                            <th scope="row">
                                <label for="wpw_auto_poster_options[wp_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
                            </th>
                            <td>
                                <input type="text" name="wpw_auto_poster_options[wp_bitly_access_token]" id="wpw_auto_poster_options[wp_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['wp_bitly_access_token']); ?>" class="large-text">
                            </td>
                        </tr>
                        <tr valign="top" class="wp_setting_input_shortest <?php echo $shortest_class; ?>">
                            <th scope="row">
                                <label for="wpw_auto_poster_options[wp_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
                            </th>
                            <td>
                                <input type="text" name="wpw_auto_poster_options[wp_shortest_api_token]" id="wpw_auto_poster_options[wp_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['wp_shortest_api_token']); ?>" class="large-text">
                            </td>
                        </tr>

                        <?php
						echo apply_filters ( 
							'wpweb_wp_settings_submit_button', 
							'<tr valign="top">
								<td colspan="2">
									<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
							</tr>'
						); ?>
					</tbody></table>
				</div><!-- /.inside -->
			</div><!-- /#wordpress_general -->
		</div>
	</div><!-- /.metabox-holder -->
</div><!-- /#wpw-auto-poster-wordpress-general -->

<!-- beginning of the wordpress api settings meta box -->
<div id="wpw-auto-poster-wordpress-websites" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="wordpress_api" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'Another WordPress Websites Settings', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table wpw-auto-poster-wordpress-settings"><tbody>
						<?php
						// Get wordpress all websites
						$wpAllSites = get_option( 'wpw_auto_poster_wordpress_sites', array() );
						if( !empty($wpAllSites) ) { ?>
							<tr><td class="no-padding" colspan="5">
								<table class="child-table wpw-auto-poster-table-resposive">
								<thead><tr>
									<td width="18%"><strong><?php esc_html_e('Website Name', 'wpwautoposter');?></strong></td>
									<td width="28%"><strong><?php esc_html_e('Website URL', 'wpwautoposter');?></strong></td>
									<td width="17%"><strong><?php esc_html_e('Website Username', 'wpwautoposter');?></strong></td>
									<td width="17%"><strong><?php esc_html_e('Action', 'wpwautopo*ster');?></strong></td>
								</tr></thead>

								<tbody>
								<?php
								foreach( $wpAllSites as $key => $Site ) {

									$reset_url = add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'remove_wp_website' => '1', 'wpw_wp_index' => $key ), admin_url( 'admin.php' ) ); ?>

									<tr>
										<td data-label="<?php esc_html_e('Website Name', 'wpwautoposter'); ?>"><?php print stripslashes($Site['name']); ?></td>
										<td data-label="<?php esc_html_e('Website URL', 'wpwautoposter'); ?>"><?php print $Site['url']; ?></td>
										<td data-label="<?php esc_html_e('Website Username', 'wpwautoposter'); ?>"><?php print $Site['username']; ?></td>
										<td data-label="<?php esc_html_e('Action', 'wpwautoposter'); ?>"><a class="wpw-auto-post-danger" href="<?php print esc_url($reset_url); ?>"><?php esc_html_e('Delete Website', 'wpwautoposter');?></a></td>
									</tr>
							<?php } ?>
							</tbody></table></td></tr>
						<?php
						} ?>
					</tbody></table>

                    <table class="form-table wpw-auto-poster-form-table-resposive">
						<thead><tr valign="top">
							<tr valign="top">
								<td scope="row" class="wpw-wp-site-cell"><strong>
									<label for="wpw_auto_poster_wordpress_site_name"><?php esc_html_e( 'Website Name', 'wpwautoposter' ); ?></label>
								</strong></td>
								<td scope="row" class="wpw-wp-site-url"><strong>
									<label for="wpw_auto_poster_wordpress_site_url"><?php esc_html_e( 'Website URL', 'wpwautoposter' ); ?></label>
								</strong></td>
								<td scope="row" class="wpw-wp-site-uname"><strong>
									<label for="wpw_auto_poster_wordpress_site_username"><?php esc_html_e( 'Website Username', 'wpwautoposter' ); ?></label>
								</strong></td>
								<td scope="row" class="wpw-wp-site-pass"><strong>
									<label for="wpw_auto_poster_wordpress_site_password"><?php esc_html_e( 'Website Password', 'wpwautoposter' ); ?></label>
								</strong></td>
								<td scope="row"></td>
							</tr>
						</tr></thead>

						<tbody>
						<tr valign="top">
							<td scope="row" data-label="<?php esc_html_e( 'Website Name', 'wpwautoposter' ); ?>">
								<input type="text" id="wpw_auto_poster_wordpress_site_name" name="wpw_auto_poster_options_wordpress_site_name" value="" class="large-text wpw-auto-poster-wordpress_site_name" />
								<p><small>
									<?php esc_html_e( 'Enter WordPress Site Name.', 'wpwautoposter' ) ?>
								</small></p>
							</td>
							<td scope="row" data-label="<?php esc_html_e( 'Website URL', 'wpwautoposter' ); ?>">
								<input type="text" id="wpw_auto_poster_wordpress_site_url" name="wpw_auto_poster_options_wordpress_site_url" value="" class="large-text wpw-auto-poster-wordpress_site_url" />
								<p><small>
									<?php esc_html_e( 'Enter WordPress Site URL.', 'wpwautoposter' ) ?>
								</small></p>
							</td>
							<td scope="row" data-label="<?php esc_html_e( 'Website Username', 'wpwautoposter' ); ?>">
								<input type="text" id="wpw_auto_poster_wordpress_site_username" autocomplete="username" name="wpw_auto_poster_options_wordpress_site_username" value="" class="large-text wpw-auto-poster-wordpress_site_username" />
								<p><small>
									<?php esc_html_e( 'Enter WordPress Site Username.', 'wpwautoposter' ) ?>
								</small></p>
							</td>
							<td scope="row" data-label="<?php esc_html_e( 'Website Password', 'wpwautoposter' ); ?>">
								<input type="password" id="wpw_auto_poster_wordpress_site_password" autocomplete="current-password" name="wpw_auto_poster_options_wordpress_site_password" value="" class="large-text wpw-auto-poster-wordpress_site_password" />
								<p><small>
									<?php esc_html_e( 'Enter WordPress Site Password.', 'wpwautoposter' ) ?>
								</small></p>
							</td>
							<?php $wordpress_account_button = apply_filters( 'wpweb_wordpress_account_button', true ); ?>
               				<?php if( $wordpress_account_button ){ ?>
								<td scope="row">
									<button type="button" id="add-wordpress-website" class="add-wp-website button-primary"><?php esc_html_e( 'Add WordPress Site', 'wpwautoposter' );?></button>
									<span class="wpw-validate-token-loader ba_wp_pretty_url_css_hide"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></span>
								</td>
							<?php } ?>
						</tr>
						<tr class="wpw-message-row"><td id="wp-website-add-result" colspan="5"></td></tr>
						</tbody>
					</table>
				
				</div><!-- .inside -->
			</div><!-- #wordpress_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-wordpress-api -->
<!-- end of the wordpress api settings meta box -->

<!-- beginning of the grant extended permission meta box -->
<div id="wpw-auto-poster-autopost-wordpress" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="autopost_wordpress" class="postbox">
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'Autopost to WordPress Website', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table"><tbody>
						<tr valign="top"> 
							<th scope="row"><label for="wpw_auto_poster_options[prevent_post_wp_metabox]">
								<?php
								esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input name="wpw_auto_poster_options[prevent_post_wp_metabox]" id="wpw_auto_poster_options[prevent_post_wp_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_wp_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_wp_metabox'] ); } ?> />
								<p><small><?php esc_html_e( 'If you check this box, then it will hide meta settings from individual posts.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<?php 
						$types = get_post_types( array( 'public'=>true ), 'objects' );
						$types = is_array( $types ) ? $types : array(); ?>

						<tr valign="top">
							<th scope="row"><label>
								<?php
								esc_html_e( 'Map WordPress post types : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<?php
								$mapTypes = get_option( 'wpw_auto_poster_wordpress_mapped_posttypes' );

								foreach( $types as $type ) {

									if( !is_object( $type ) ) continue;

									if( isset( $type->labels ) ) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
						            } else {
						            	$label = $type->name;
						            }

						            if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

									// wordpress post chats
									$selectedSites = array();
									if( isset($wpw_auto_poster_options['wp_type_'.$type->name.'_sites']) ) {
										$selectedSites = ( array ) $wpw_auto_poster_options['wp_type_'.$type->name.'_sites'];
									}

									$thisSites = !empty( $mapTypes[$type->name] ) ? $mapTypes[$type->name] : array(); ?>

									<div class="wpw-auto-poster-fb-types-wrap">
										<div class="wpw-auto-poster-fb-types-label">
											<?php
											printf( esc_html__('Autopost %s to WordPress Websites', 'wpwautoposter'), $label );  ?>
										</div><!--.wpw-auto-poster-li-types-label-->

										<div class="wpw-auto-poster-wp-sites wpw-auto-poster-fb-users-acc" style="<?php if( empty($thisSites) ) echo 'display:none;'; ?>">
											<select name="wpw_auto_poster_options[wp_type_<?php echo $type->name; ?>_sites][]" multiple="multiple" class="wpw-auto-poster-wp-sites-select">
												<?php
												if( !empty($thisSites) ) {
													foreach( $thisSites as $site ) {

														$siteArr = explode( ':', $site );
														$siteKey = isset($siteArr[0]) ? $siteArr[0] : '';
														$siteType = isset($siteArr[1]) ? $siteArr[1] : '';

														$siteName = isset( $wpAllSites[$siteKey]['name'] ) ? $wpAllSites[$siteKey]['name'] : '';

														$siteName .= ' - ' . $siteType;

														echo '<option value="' . esc_attr( $site ) . '" ' . selected( true, true, false ) . '>' . esc_html( stripslashes( $siteName ) ) . '</option>';
													}
												} ?>
											</select>
										</div>
										
										<?php $map_post_types_button = apply_filters( 'wpweb_map_post_types_button', true ); ?>
                   						<?php if( $map_post_types_button ){ ?>
											<a href="#" class="wordpress-map-post-types button-primary" data-post-type="<?php echo $type->name; ?>" data-val='<?php echo json_encode($thisSites); ?>'>
												<?php
												esc_html_e( 'Map Post Types', 'wpwautoposter' ); ?>
											</a>
										<?php } ?>
									</div>
								<?php
								} // End foreach ?>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label>
								<?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
							</label></th>
							<td>
								<input id="wp_custom_global_cnt" type="radio" name="wpw_auto_poster_options[wp_custom_msg_options]" value="global_msg" <?php checked($wp_custom_msg_options, 'global_msg', true);?> class="custom_cnt_options">
								<label for="wp_custom_global_cnt" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>

								<input id="wp_custom_post_cnt" type="radio" name="wpw_auto_poster_options[wp_custom_msg_options]" value="post_msg" <?php checked($wp_custom_msg_options, 'post_msg', true);?> class="custom_cnt_options">
								<label for="wp_custom_post_cnt" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
							</td>
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_wp_global_title">
								<?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_options[wp_global_title]" id="wpw_auto_poster_options_wp_global_title" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $wp_postTitle ); ?>" />
								<p><small><?php esc_html_e( 'Here you can enter a default post title which will be used for the WordPress Websites.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_wp_post_image">
								<?php esc_html_e( 'Post Default Image : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_options[wp_post_image]" id="wpw_auto_poster_options_wp_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $wp_postImg ); ?>">
								<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
								<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the WordPress Websites.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top" class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options[wp_global_message_template]">
								<?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?>
							</label></th>
							<td class="form-table-td">
								<textarea type="text" name="wpw_auto_poster_options[wp_global_message_template]" id="wpw_auto_poster_options[wp_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wp_template_text ); ?></textarea>
							</td>
						</tr>

						<tr id="custom_post_type_templates_wp" class="post_msg_tr <?php echo $post_msg_style; ?>">
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

										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media ?>
										<li><a href="#tabs-<?php echo $type->name; ?>"><?php echo $label; ?></a></li>
									<?php } ?>
								</ul>

								<?php 
								foreach( $all_types as $type ) {

									if( !is_object( $type ) ) continue;

									if( isset( $type->labels ) ) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
									} else {
										$label = $type->name;
									}

									if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

									$postTitle = ( isset( $wpw_auto_poster_options['wp_post_title_'.$type->name] ) ) ? $wpw_auto_poster_options['wp_post_title_'.$type->name] : '';

									$postImg = ( isset( $wpw_auto_poster_options['wp_post_image_'.$type->name] ) ) ? $wpw_auto_poster_options['wp_post_image_'.$type->name] : '';

									$postMsg = ( isset( $wpw_auto_poster_options['wp_post_cnt_template_'.$type->name] ) ) ? $wpw_auto_poster_options['wp_post_cnt_template_'.$type->name] : ''; ?>

									<table id="tabs-<?php echo $type->name; ?>">
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_wp_post_title_<?php echo $type->name; ?>"><?php esc_html_e( 'Custom Title:', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_options[wp_post_title_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_wp_post_title_<?php echo $type->name; ?>" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $postTitle ); ?>">
												<p><small><?php esc_html_e( 'Here you can enter a default post title which will be used for the WordPress Websites.', 'wpwautoposter' ); ?></small></p>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_wp_post_image_<?php echo $type->name; ?>"><?php esc_html_e( 'Post Image:', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_options[wp_post_image_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_wp_post_image_<?php echo $type->name; ?>" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $postImg ); ?>">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
												<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the WordPress websites.', 'wpwautoposter' ); ?></small></p>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_posting_wp_post_custom_cnt_<?php echo $type->name; ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
											</th>
											<td class="form-table-td">
												<textarea type="text" name="wpw_auto_poster_options[wp_post_cnt_template_<?php echo $type->name; ?>]" id="wpw_auto_posting_wp_post_custom_cnt_<?php echo $type->name; ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $postMsg ); ?></textarea>
											</td>
										</tr>

									</table>
								<?php } ?>
							</th>
						</tr>

						<tr valign="top">
							<th scope="row"></th>
							<td class="global_msg_td">
								<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter default message which will be used for the WordPress website post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
								<?php 
								$wp_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
					            print $wp_template_str; ?>
								</small></p>
							</td>
						</tr>

						<?php
						echo apply_filters ( 
							'wpweb_wp_settings_submit_button', 
							'<tr valign="top">
								<td colspan="2">
									<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
							</tr>'
						); ?>
					</tbody></table>

				</div><!-- .inside -->
			</div><!-- #wp_autopost -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-wordpress-grant-permission -->
<!-- end of the auto poster meta box -->

<div class="wpw-auto-poster-popup-content wp-map-post-types-popup">
	<div class="wpw-auto-poster-header">
		<div class="wpw-auto-poster-header-title">
			<?php
			esc_html_e( 'Map Post Types', 'wpwautoposter' ) ?>
		</div>
		<div class="wpw-auto-poster-popup-close"><a href="javascript:void(0);" class="wpw-auto-poster-close-button">&times;</a></div>
	</div>
	<div class="wpw-auto-poster-popup">

		<input type="hidden" class="mapped-post" value="" />

		<div class="wp-map-pt-row table-header">
			<div class="wpmptr-name"><strong>
				<?php esc_html_e( 'Website Name', 'wpwautoposter' ) ?>
			</strong></div>
			<div class="wpmptr-post-types"><strong>
				<?php esc_html_e( 'Post Types', 'wpwautoposter' ) ?>
			</strong></div>
		</div>

		<?php
		if( !empty($wpAllSites) ) {
			foreach( $wpAllSites as $sitekey => $site ) {

				$site['password'] = base64_decode( $site['password'] );
				$postTypes = $wpposting->wpw_auto_poster_get_site_post_types( $site );

				if( ! $postTypes || empty($postTypes) ) continue; ?>

				<div class="wp-map-pt-row">
					<div class="wpmptr-name">
						<span><?php echo stripslashes( $site['name'] ); ?></span><br />
						<code><?php echo esc_url( $site['url'] ); ?></code>
					</div>
					<div class="wpmptr-post-types">
						<select class="post-types" data-site-key="<?php echo esc_attr($sitekey); ?>">
							<option value=""><?php esc_html_e( '-- Select Post Type --', 'wpwautoposter' ); ?></option>
							<?php
							foreach( $postTypes as $key => $postType ) {
								echo '<option value="' . esc_attr($postType['name']) . '">' . esc_html__($postType['label']) . '</option>';
							} ?>
						</select>
					</div>
				</div>
			<?php
			}
		} ?>

		<div class="wp-map-submit">
			<img class="ajax-loader" src="<?php echo WPW_AUTO_POSTER_IMG_URL; ?>/ajax-loader.gif" alt="Loading ..." />

			<button type="button" class="button wp-map-submit-btn" disabled="disabled"><?php esc_html_e( 'Save Changes', 'wpwautoposter' ); ?></button>
		</div>
	</div>
</div>
<div class="wpw-auto-poster-popup-overlay"></div>