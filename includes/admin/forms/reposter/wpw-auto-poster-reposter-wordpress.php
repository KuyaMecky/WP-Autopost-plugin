<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * WordPress Reposter Settings
 *
 * The html markup for the WordPress settings tab.
 *
 * @package Social Auto Poster
 * @since 3.4.1
 */

global $wpw_auto_poster_reposter_options, $wpw_auto_poster_model;

//model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_reposter_options['wp_posting_cats'] ) ? $wpw_auto_poster_reposter_options['wp_posting_cats']: 'include';

$wp_exclude_cats = array();

// Get saved categories for li to exclude from posting
if( !empty($wpw_auto_poster_reposter_options['wp_post_type_cats']) ) {
	$wp_exclude_cats = $wpw_auto_poster_reposter_options['wp_post_type_cats'];
}

$wp_last_posted_page = ( !empty($wpw_auto_poster_reposter_options['wp_last_posted_page']) ) ? $wpw_auto_poster_reposter_options['wp_last_posted_page'] : '1';

$exludes_post_ids = !empty( $wpw_auto_poster_reposter_options['wp_post_ids_exclude']) ? $wpw_auto_poster_reposter_options['wp_post_ids_exclude'] : '';

$wp_postImg = ( isset( $wpw_auto_poster_reposter_options['wp_post_image'] ) ) ? $wpw_auto_poster_reposter_options['wp_post_image'] : '';

$wp_postTitle = ( isset( $wpw_auto_poster_reposter_options['wp_global_title'] ) ) ? $wpw_auto_poster_reposter_options['wp_global_title'] : '';

$wp_global_message_template = isset( $wpw_auto_poster_reposter_options['wp_global_message_template'] ) ? $wpw_auto_poster_reposter_options['wp_global_message_template'] : '';

$repost_wp_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_wp_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_wp_custom_msg_options'] : 'global_msg';

if( $repost_wp_custom_msg_options == 'global_msg' ) {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
} ?>

<!-- beginning of the wordpress general settings meta box -->
<div id="wpw-auto-poster-wordpress-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="wordpress_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw-sap-telegram-settings'>
					<?php esc_html_e( 'WordPress Settings', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table"><tbody>
						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[enable_telegram]"><?php esc_html_e( 'Enable Repost : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<input name="wpw_auto_poster_reposter_options[enable_wordpress]" id="wpw_auto_poster_reposter_options[enable_wordpress]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_reposter_options['enable_wordpress'] ) ) { checked( '1', $wpw_auto_poster_reposter_options['enable_wordpress'] ); } ?> />
								<p><small><?php esc_html_e( 'Check this box, if you want to automatically post your new content to WordPress.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[enable_wordpress_for]"><?php esc_html_e( 'Repost for : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<ul>
									<?php 
									$all_types = get_post_types( array( 'public' => true ), 'objects');
									$all_types = is_array( $all_types ) ? $all_types : array();

									$prevent_meta = array();
									if( !empty($wpw_auto_poster_reposter_options['enable_wordpress_for']) ) {
										$prevent_meta = $wpw_auto_poster_reposter_options['enable_wordpress_for'];
									}
													
									$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

									$tele_post_type_cats = array();
									if( !empty($wpw_auto_poster_reposter_options['wp_post_type_cats']) ) {
										$tele_post_type_cats = $wpw_auto_poster_reposter_options['wp_post_type_cats'];
									}
												
									foreach( $all_types as $type ) {
										if( !is_object($type) ) continue;	

										if( isset($type->labels) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
										} else {
											$label = $type->name;
										}

										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

										$selected = ( in_array($type->name, $prevent_meta) ) ? 'checked="checked"' : ''; ?>
													
										<li class="wpw-auto-poster-prevent-types">
											<input type="checkbox" id="wpw_auto_posting_wordpress_prevent_<?php echo $type->name; ?>" name="wpw_auto_poster_reposter_options[enable_wordpress_for][]" value="<?php echo $type->name; ?>" <?php echo $selected; ?> />

											<label for="wpw_auto_posting_wordpress_prevent_<?php echo $type->name; ?>"><?php echo $label; ?></label>
										</li>
									<?php } ?>
								</ul>
								<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to WordPress.', 'wpwautoposter' ); ?></small></p>  
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[wp_post_type_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
							</th>
							<td class="wpw-auto-poster-select">
								<div class="wpw-auto-poster-cats-option">
									<input name="wpw_auto_poster_reposter_options[wp_posting_cats]" id="wp_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
									<label for="wp_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
									<input name="wpw_auto_poster_reposter_options[wp_posting_cats]" id="wp_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
									<label for="wp_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
								</div>
								<select name="wpw_auto_poster_reposter_options[wp_post_type_cats][]" id="wpw_auto_poster_reposter_options[wp_post_type_cats]" class="wp_post_type_cats ajax-taxonomy-search wpw-auto-poster-cats-tags-select" multiple="multiple">
								<?php

									$wp_reposter_exclude_cats_selected_values = !empty($wpw_auto_poster_reposter_options['wp_post_type_cats']) ? $wpw_auto_poster_reposter_options['wp_post_type_cats'] : array();	
									$selected = 'selected="selected"';

									if(!empty($wp_reposter_exclude_cats_selected_values)) {

										foreach ($wp_reposter_exclude_cats_selected_values as $post_type => $post_data) {
											
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
								<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL)."/ajax-loader.gif";?>"/></div>
								<p><small><?php esc_html_e( 'Select the Taxonomies for each post type that you want to include or exclude for the repost.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[wp_post_ids_exclude]"><?php esc_html_e( 'Exclude Posts : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<textarea placeholder="1100,1200,1300" cols="35" id="wpw_auto_poster_reposter_options[wp_post_ids_exclude]" name="wpw_auto_poster_reposter_options[wp_post_ids_exclude]"><?php echo $exludes_post_ids; ?></textarea>
								<p><small>
									<?php esc_html_e( 'Enter the post ids seprated by comma(,) which you want to exclude for the posting.', 'wpwautoposter' ); ?>
								</small></p>
							</td>
						</tr>

						<tr valign="top" class="wpw-auto-poster-schedule-limit">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[wp_posts_limit]"><?php esc_html_e( 'Maximum Posting per schedule : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<input id="wpw_auto_poster_reposter_options[wp_posts_limit]" name="wpw_auto_poster_reposter_options[wp_posts_limit]" type="number" value="<?php echo $wpw_auto_poster_reposter_options['wp_posts_limit']; ?>" min="0" max="<?php print WPW_AUTO_POSTER_POST_LIMIT; ?>" />
								<p><small>
									<?php esc_html_e( 'Enter the maximum auto posting allowed on each schedule execution.', 'wpwautoposter' ); ?>
								</small></p>
								<br>
								<p class="wpw-auto-poster-info-box width-80"><?php print sprintf( esc_html__('%sNote:%s Maximum 10 posts per schedule allowed to avoid account blocking issue.','wpwautoposter' ), '<b>','</b>' ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label><?php esc_html_e( 'Custom Content Options : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<input id="wp_custom_global_msg" type="radio" name="wpw_auto_poster_reposter_options[repost_wp_custom_msg_options]" value="global_msg" <?php checked($repost_wp_custom_msg_options, 'global_msg', true); ?>>
								<label for="wp_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
                                
                                <input id="wp_custom_post_msg" type="radio" name="wpw_auto_poster_reposter_options[repost_wp_custom_msg_options]" value="post_msg" <?php checked($repost_wp_custom_msg_options, 'post_msg', true); ?>>
                                <label for="wp_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
							</td>
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_wp_global_title">
								<?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_reposter_options[wp_global_title]" id="wpw_auto_poster_options_wp_global_title" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $wp_postTitle ); ?>" />
								<p><small><?php esc_html_e( 'Here you can enter a default post title which will be used for the WordPress Websites.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_wp_post_image">
								<?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_reposter_options[wp_post_image]" id="wpw_auto_poster_options_wp_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $wp_postImg ); ?>">
								<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
								<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the WordPress websites.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top" class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row">
								<label for="wpw_auto_poster_reposter_options[wp_global_message_template]"><?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?></label>
							</th>
							<td>
								<textarea type="text" name="wpw_auto_poster_reposter_options[wp_global_message_template]" id="wpw_auto_poster_reposter_options[wp_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wp_global_message_template ); ?></textarea>
							</td>
						</tr>

						<tr id="custom_post_type_templates_wp" class="post_msg_tr <?php echo $post_msg_style; ?>">
							<th colspan="2">
							  	<ul>
							  		<?php
									$all_types = get_post_types( array( 'public' => true ), 'objects' );
									$all_types = is_array( $all_types ) ? $all_types : array();

									foreach( $all_types as $type ) {
										if( !is_object($type) ) continue;	

										if( isset($type->labels) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
										} else {
											$label = $type->name;
										}

										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media ?>

							    		<li><a href="#tabs-<?php echo $type->name; ?>"><?php echo ucfirst($type->name); ?></a></li>
							  		<?php } ?>
							  	</ul>

							  	<?php 
							  	foreach( $all_types as $type ) {
									if( !is_object($type) ) continue;

									if( isset($type->labels) ) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
									} else {
										$label = $type->name;
									}

									if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

									$postTitle = ( isset( $wpw_auto_poster_reposter_options['wp_post_title_'.$type->name] ) ) ? $wpw_auto_poster_reposter_options['wp_post_title_'.$type->name] : '';

									$postImg = ( isset( $wpw_auto_poster_reposter_options['wp_post_image_'.$type->name] ) ) ? $wpw_auto_poster_reposter_options['wp_post_image_'.$type->name] : '';
										
									$postCnt = ( isset( $wpw_auto_poster_reposter_options['wp_post_cnt_template_'.$type->name] ) ) ? $wpw_auto_poster_reposter_options['wp_post_cnt_template_'.$type->name] : ''; ?>

								  	<table id="tabs-<?php echo $type->name; ?>">
								  		<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_wp_post_title_<?php echo $type->name; ?>"><?php esc_html_e( 'Custom Title : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_reposter_options[wp_post_title_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_wp_post_title_<?php echo $type->name; ?>" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $postTitle ); ?>" />
												<p><small><?php esc_html_e( 'Here you can enter a default post title which will be used for the WordPress Websites.', 'wpwautoposter' ); ?></small></p>
											</td>
										</tr>
								  		<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_wp_post_image_<?php echo $type->name; ?>"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_reposter_options[wp_post_image_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_wp_post_image_<?php echo $type->name; ?>" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $postImg ); ?>">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
												<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the WordPress websites.', 'wpwautoposter' ); ?></small></p>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_posting_wp_custom_cnt_<?php echo $type->name; ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
											</th>
											<td>
												<textarea type="text" name="wpw_auto_poster_reposter_options[wp_post_cnt_template_<?php echo $type->name; ?>]" id="wpw_auto_posting_wp_custom_cnt_<?php echo $type->name; ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $postCnt ); ?></textarea>
												<p><small><?php esc_html_e('Leave it empty for global custom message.', 'wpwautoposter'); ?></small></p>
											</td>
										</tr>
									</table>
								<?php } ?>
							</th>
						</tr>

						<tr valign="top" class="global_msg_tr">
							<th scope="row"></th>
							<td class="global_msg_td">
								<p><small class="wpw-sap-wordpress-custom-content"><?php esc_html_e( 'Here you can enter default content which will be used for the website posting. Leave it empty to use the post level message. You can use following template tags within the content template:', 'wpwautoposter' ); ?>
								<?php 
								$tele_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
				            	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>").
				            	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>");
					            print $tele_template_str; ?>
								</small></p>
							</td>
						</tr>

						<?php
						echo apply_filters ( 
							'wpweb_reposter_wp_settings_submit_button', 
							'<tr valign="top">
								<td colspan="2">
									<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" id="wpw_auto_poster_reposter_set_submit" name="wpw_auto_poster_reposter_set_submit" class="button-primary">
								</td>
							</tr>'
						); ?>
					</tbody></table>
				</div><!-- .inside -->

			</div><!-- #wordpress_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-wordpress-general -->
<!-- end of the wordpress general settings meta box -->