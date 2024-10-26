<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Telegram Settings
 *
 * The html markup for the Telegram settings tab.
 *
 * @package Social Auto Poster
 * @since 3.4.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_tele_posting;

// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['tele_posting_cats'] ) ? $wpw_auto_poster_options['tele_posting_cats']: 'exclude';

// telegram posting class
$teleposting = $wpw_auto_poster_tele_posting;

$tele_wp_pretty_url = '';
if( !empty($wpw_auto_poster_options['tele_wp_pretty_url']) ) {
	$tele_wp_pretty_url = ' checked="checked"';
}

$tele_selected_shortner = isset( $wpw_auto_poster_options['tele_url_shortener'] ) ? $wpw_auto_poster_options['tele_url_shortener'] : '';

$tele_wp_pretty_url_css = ( $tele_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css': ' ba_wp_pretty_url_css_hide';

$tele_url_shortener = $model->wpw_auto_poster_get_shortner_list();

$tele_postImg = ( isset( $wpw_auto_poster_options['tele_post_image'] ) ) ? $wpw_auto_poster_options['tele_post_image'] : '';

$tele_postImgCap = ( isset( $wpw_auto_poster_options['tele_post_img_caption'] ) ) ? $wpw_auto_poster_options['tele_post_img_caption'] : '';

$tele_template_text = ( !empty($wpw_auto_poster_options['tele_global_message_template']) ) ? $wpw_auto_poster_options['tele_global_message_template'] : '';

$tele_custom_msg_options = isset( $wpw_auto_poster_options['tele_custom_msg_options'] ) ? $wpw_auto_poster_options['tele_custom_msg_options'] : 'global_msg';

if( $tele_custom_msg_options == 'global_msg') {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "global_msg_style_hide";
	$post_msg_style = "";
}

// Get telegram keys
$telegram_keys = isset( $wpw_auto_poster_options['telegram_keys'] ) ? $wpw_auto_poster_options['telegram_keys'] : array(); ?>

<!-- beginning of the linkedin general settings meta box -->
<div id="wpw-auto-poster-telegram-general" class="post-box-container">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div id="telegram_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'Telegram General Settings', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
				<?php if (version_compare(PHP_VERSION, '7.0.0', '<')) { ?>
							<div class="wpw-auto-poster-error">
                                <ul>
                                    <li><?php esc_html_e( 'Telegram requires PHP version 7.0 or higher, Please upgrade your PHP version to 7.0 or higher.', 'wpwautoposter' ); ?></li>
                                </ul>								
							</div>
				<?php } ?>	
					<table class="form-table"><tbody>
						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[enable_telegram]">
								<?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input name="wpw_auto_poster_options[enable_telegram]" id="wpw_auto_poster_options[enable_telegram]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_telegram'] ) ) { checked( '1', $wpw_auto_poster_options['enable_telegram'] ); } ?> />
								<p><small><?php esc_html_e( 'Check this box, if you want to automatically post your new content to Telegram.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[enable_telegram_for]">
								<?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<ul>
								<?php 
									$all_types = get_post_types( array( 'public' => true ), 'objects');
									$all_types = is_array( $all_types ) ? $all_types : array();
									
									$prevent_meta = array();
									if( !empty( $wpw_auto_poster_options['enable_telegram_for'] ) ) {
										$prevent_meta = $wpw_auto_poster_options['enable_telegram_for'];
									}
													
									$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

									$tele_post_type_tags = array();
									if( !empty( $wpw_auto_poster_options['tele_post_type_tags'] ) ) {
										$tele_post_type_tags = $wpw_auto_poster_options['tele_post_type_tags'];
									}

									$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

									$tele_post_type_cats = array();
									if( !empty( $wpw_auto_poster_options['tele_post_type_cats'] ) ) {
										$tele_post_type_cats = $wpw_auto_poster_options['tele_post_type_cats'];
									}

									// Get saved categories for linkedin to exclude from posting
									if( !empty( $wpw_auto_poster_options['tele_exclude_cats'] ) ) {
										$tele_exclude_cats = $wpw_auto_poster_options['tele_exclude_cats'];
									} 
												
									foreach( $all_types as $type ) {	

										if( !is_object( $type ) ) continue;															
										if( isset( $type->labels ) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
							            } else {
							            	$label = $type->name;
							            }

										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

										$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : ''; ?>
													
										<li class="wpw-auto-poster-prevent-types">
											<input type="checkbox" id="wpw_auto_posting_telegram_prevent_<?php echo $type->name; ?>" name="wpw_auto_poster_options[enable_telegram_for][]" value="<?php echo $type->name; ?>" <?php echo $selected; ?>/>					
											<label for="wpw_auto_posting_telegram_prevent_<?php echo $type->name; ?>"><?php echo $label; ?></label>
										</li>
									
									<?php } ?>
								</ul>
								<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to Telegram when they get published.', 'wpwautoposter' ); ?></small></p>  
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"> <label for="wpw_auto_poster_options[tele_post_type_tags][]">
								<?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<select name="wpw_auto_poster_options[tele_post_type_tags][]" id="wpw_auto_poster_options[tele_post_type_tags]" class="tele_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
									<?php
									foreach( $all_types as $type ) {
										
										if( !is_object( $type ) ) continue;

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
							                	if( !empty($tele_post_type_tags[$type->name]) ) {
							                		$selected = ( in_array($taxonomy->name, $tele_post_type_tags[$type->name]) ) ? 'selected="selected"' : '';
							                	}

							                    if( is_object($taxonomy) && $taxonomy->hierarchical != 1 ) {
							                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
							                    }
							                }
							                echo '</optgroup>';
							            }
									} ?>
								</select>
								<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL)."/ajax-loader.gif";?>"/></div>
								<p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[tele_post_type_cats][]">
								<?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<select name="wpw_auto_poster_options[tele_post_type_cats][]" id="wpw_auto_poster_options[tele_post_type_cats]" class="tele_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
							                	if( isset($tele_post_type_cats[$type->name]) && !empty($tele_post_type_cats[$type->name]) ) {
							                		$selected = ( in_array( $taxonomy->name, $tele_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
							                	}

							                    if( is_object($taxonomy) && $taxonomy->hierarchical == 1 ) {
							                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
							                    }
							                }
							                echo '</optgroup>';
							            }
									} ?>
								</select>
								<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL)."/ajax-loader.gif";?>"/></div>
								<p><small><?php esc_html_e( 'Select the Categories for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashcats}.', 'wpwautoposter' );?></b></small></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label for="wpw_auto_poster_options[tele_exclude_cats][]">
								<?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td class="wpw-auto-poster-select">
								<div class="wpw-auto-poster-cats-option">
									<input name="wpw_auto_poster_options[tele_posting_cats]" id="tele_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
									<label for="tele_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
									<input name="wpw_auto_poster_options[tele_posting_cats]" id="tele_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
									<label for="tele_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
								</div>
								<select name="wpw_auto_poster_options[tele_exclude_cats][]" id="wpw_auto_poster_options[tele_exclude_cats]" class="tele_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">

								<?php

									$tele_exclude_cats_selected_values = !empty($wpw_auto_poster_options['tele_exclude_cats']) ? $wpw_auto_poster_options['tele_exclude_cats'] : array();
									$selected = 'selected="selected"';

									if(!empty($tele_exclude_cats_selected_values)) {

										foreach ($tele_exclude_cats_selected_values as $post_type => $post_data) {
											
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
							<th scope="row"><label for="wpw_auto_poster_options[tele_url_shortener]">
								<?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?> 
							</label></th>
							<td>
								<select name="wpw_auto_poster_options[tele_url_shortener]" id="wpw_auto_poster_options[tele_url_shortener]" class="tele_url_shortener" data-content='tele'>
									<?php
									foreach( $tele_url_shortener as $key => $option ) { ?>
										<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $tele_selected_shortner, $key ); ?>>
											<?php echo $option; ?>
										</option>
									<?php
									} ?>
								</select>
								<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<tr id="row-tele-wp-pretty-url" valign="top" class="<?php print $tele_wp_pretty_url_css;?>">
							<th scope="row"><label for="wpw_auto_poster_options[tele_wp_pretty_url]">
								<?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="checkbox" name="wpw_auto_poster_options[tele_wp_pretty_url]" id="wpw_auto_poster_options[tele_wp_pretty_url]" class="tele_wp_pretty_url" data-content='tele' value="yes" <?php print $tele_wp_pretty_url;?>>
								<p><small><?php esc_html_e( 'Check this box if you want to use pretty permalink. i.e. http://example.com/test-post/. (Not Recommnended).', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<?php
						$class = '';
                        if( $tele_selected_shortner != 'bitly' ) {
                            $class = 'ba_wp_pretty_url_css_hide';
                        }

                        $shortest_class = '';
                        if( $tele_selected_shortner != 'shorte.st' ) {
                            $shortest_class = 'ba_wp_pretty_url_css_hide';
                        }

                        $bitly_access_token = isset( $wpw_auto_poster_options['tele_bitly_access_token'] ) ? $wpw_auto_poster_options['tele_bitly_access_token'] : ''; ?>

                        <tr valign="top" class="tele_setting_input_bitly <?php echo $class; ?>">
                            <th scope="row">
                                <label for="wpw_auto_poster_options[tele_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
                            </th>
                            <td>
                                <input type="text" name="wpw_auto_poster_options[tele_bitly_access_token]" id="wpw_auto_poster_options[tele_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr($bitly_access_token); ?>" class="large-text">
                            </td>
                        </tr>

                        <?php
                        $shortest_api_token = isset( $wpw_auto_poster_options['tele_shortest_api_token'] ) ? $wpw_auto_poster_options['tele_shortest_api_token'] : ''; ?>

                        <tr valign="top" class="tele_setting_input_shortest <?php echo $shortest_class; ?>">
                            <th scope="row">
                                <label for="wpw_auto_poster_options[tele_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
                            </th>
                            <td>
                                <input type="text" name="wpw_auto_poster_options[tele_shortest_api_token]" id="wpw_auto_poster_options[tele_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr($shortest_api_token); ?>" class="large-text">
                            </td>
                        </tr>

						<?php
						echo apply_filters ( 
							'wpweb_tele_settings_submit_button', 
							'<tr valign="top">
								<td colspan="2">
									<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
							</tr>'
						); ?>
					</tbody></table>

				</div><!-- /.inside -->
			</div><!-- /#telegram_general -->
		</div>
	</div><!-- /.metabox-holder -->
</div><!-- /#wpw-auto-poster-telegram-general -->

<!-- beginning of the telegram api settings meta box -->
<div id="wpw-auto-poster-telegram-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="telegram_api" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'Telegram API Settings', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table wpw-auto-poster-telegram-settings"><tbody>
						<tr valign="top">
							<td width="100%">
								<div class="wpw-auto-poster-error info">
									<ul>
										<li><?php print esc_html__( 'Before you can start publishing your content to Telegram you need to create a Telegram Bot.', 'wpwautoposter' ); ?>
											<?php printf( esc_html__('You can get a step by step tutorial on how to create a Telegram Bot on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="https://docs.Michael Tallada/social-network-integration/telegram/" target="_blank">', '</a>' ); ?>
										</li>
									</ul>
								</div>
							</td>
						</tr>

						<tr><td colspan="3">
						<table class="wpw-auto-poster-form-table-resposive">

						<thead><tr valign="top" class="wpw-multiple-button">
							<td scope="row">
								<strong><label for="wpw_auto_poster_options[telegram_keys][0][boat]"><?php esc_html_e( 'Telegram Bot Name', 'wpwautoposter' ); ?></label></strong>
							</td>
							<td scope="row">
								<strong><label for="wpw_auto_poster_options[telegram_keys][0][token]"><?php esc_html_e( 'Telegram Token', 'wpwautoposter' ); ?></label></strong>
							</td>
							<td scope="row"></td>
						</tr></thead>

						<tbody>
						<?php
						if( !empty( $telegram_keys ) ) {
							foreach( $telegram_keys as $telegram_key => $telegram_value ) {
								if( !isset($telegram_key) ) $telegram_key = "0";

								// Don't disply delete link for first row
								$telegram_delete_class = empty( $telegram_key ) ? '' : ' wpw-auto-poster-display-inline '; ?>

								<tr valign="top" class="wpw-auto-poster-telegram-account-details" data-row-id="<?php echo $telegram_key; ?>">
									<td scope="row" width="25%" data-label="<?php esc_html_e( 'Telegram Bot Name', 'wpwautoposter' ); ?>">
										<input type="text" name="wpw_auto_poster_options[telegram_keys][<?php echo $telegram_key; ?>][boat]" value="<?php echo $model->wpw_auto_poster_escape_attr( $telegram_value['boat'] ); ?>" class="large-text wpw-auto-poster-telegram-boat" />
										<p><small><?php esc_html_e( 'Enter Telegram Bot Name.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="40%" data-label="<?php esc_html_e( 'Telegram Token', 'wpwautoposter' ); ?>">
										<input type="text" name="wpw_auto_poster_options[telegram_keys][<?php echo $telegram_key; ?>][token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $telegram_value['token'] ); ?>" class="large-text wpw-auto-poster-telegram-token" />
										<p><small><?php esc_html_e( 'Enter Telegram Token.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="150px">
										<a href="javascript:void(0);" class="wpw-auto-poster-delete-tele-account wpw-auto-poster-telegram-remove <?php echo $telegram_delete_class; ?>" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
									</td>
								</tr>
							<?php
							}
						} else { ?>
							<tr valign="top" class="wpw-auto-poster-telegram-account-details" data-row-id="0>">
								<td scope="row" width="25%" data-label="<?php esc_html_e( 'Telegram Bot Name', 'wpwautoposter' ); ?>">
									<input type="text" name="wpw_auto_poster_options[telegram_keys][0][boat]" value="" class="large-text wpw-auto-poster-telegram-boat" />
									<p><small><?php esc_html_e( 'Enter Telegram Boat Name.', 'wpwautoposter' ); ?></small></p>  
								</td>
								<td scope="row" width="40%" data-label="<?php esc_html_e( 'Telegram Token', 'wpwautoposter' ); ?>">
									<input type="text" name="wpw_auto_poster_options[telegram_keys][0][token]" value="" class="large-text wpw-auto-poster-telegram-token" />
									<p><small><?php esc_html_e( 'Enter Telegram Token.', 'wpwautoposter' ); ?></small></p>  
								</td>
								<td scope="row" width="50px">
									<a href="javascript:void(0);" class="wpw-auto-poster-delete-tele-account wpw-auto-poster-telegram-remove" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
								</td>
							</tr>
						<?php
						} ?>
						</tbody></table>
						</td></tr>
						
						<?php $tele_account_button = apply_filters( 'wpweb_tele_account_button', true ); ?>
                   		<?php if( $tele_account_button ){ ?>
							<tr>
								<td colspan="2" width="68.4%">
									<a class='wpw-auto-poster-add-more-tele-account button' href='javascript:void(0);'><?php esc_html_e( 'Add more', 'wpwautoposter' ); ?></a>
								</td>
								<td scope="row"></td>
							</tr>


							<?php
							echo apply_filters ( 
								'wpweb_telegram_settings_submit_button', 
								'<tr valign="top">
									<td colspan="4">
										<input type="submit" value="' . esc_html__( 'Refresh Data & Save', 'wpwautoposter' ) . '" id="wpw_auto_poster_tele_refresh_submit" name="wpw_auto_poster_flush_tele_data" class="button wpw_auto_poster_flush_tele_data">
									</td>
								</tr>'
							); ?>
						<?php } ?>
					</tbody></table>
				
				</div><!-- .inside -->
			</div><!-- #telegram_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-telegram-api -->
<!-- end of the linkedin api settings meta box -->

<!-- beginning of the grant extended permission meta box -->
<div id="wpw-auto-poster-autopost-telegram" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="autopost_telegram" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>

				<h3 class="hndle"><span class='wpw_common_verticle_align'>
					<?php esc_html_e( 'Autopost to Telegram', 'wpwautoposter' ); ?>
				</span></h3>

				<div class="inside">
					<table class="form-table"><tbody>
						<tr valign="top"> 
							<th scope="row"><label for="wpw_auto_poster_options[prevent_post_tele_metabox]">
								<?php
								esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input name="wpw_auto_poster_options[prevent_post_tele_metabox]" id="wpw_auto_poster_options[prevent_post_tele_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_tele_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_tele_metabox'] ); } ?> />
								<p><small><?php esc_html_e( 'If you check this box, then it will hide meta settings from individual posts.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>

						<?php 
						$types = get_post_types( array( 'public'=>true ), 'objects' );
						$types = is_array( $types ) ? $types : array(); ?>

						<tr valign="top">
							<th scope="row"><label>
								<?php
								esc_html_e( 'Map Autopost Location : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<?php
								// Get telegram all account
								$tele_all_chat = wpw_auto_poster_get_tele_chats();
								
								foreach( $types as $type ) {

									if( !is_object( $type ) ) continue;

									if( isset( $type->labels ) ) {
										$label = $type->labels->name ? $type->labels->name : $type->name;
						            } else {
						            	$label = $type->name;
						            }

						            if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media

						            // telegram post message type
									$tele_msgtype = !empty( $wpw_auto_poster_options['tele_type_'.$type->name.'_msgtype'] ) ? $wpw_auto_poster_options['tele_type_'.$type->name.'_msgtype'] : '';

									// telegram post chats
									$tele_chats = array();
									if( isset($wpw_auto_poster_options['tele_type_'.$type->name.'_chats']) ) {
										$tele_chats = ( array ) $wpw_auto_poster_options['tele_type_'.$type->name.'_chats'];
									} ?>

									<div class="wpw-auto-poster-fb-types-wrap">
										<div class="wpw-auto-poster-fb-types-label">
											<?php
											printf( esc_html__('Autopost %s to Telegram as', 'wpwautoposter'), $label );  ?>
										</div><!--.wpw-auto-poster-li-types-label-->

										<div class="wpw-auto-poster-tele-msgtype wpw-auto-poster-fb-type">
											<select name="wpw_auto_poster_options[tele_type_<?php echo $type->name; ?>_msgtype]" data-type="<?php echo $type->name; ?>" class="wpw-auto-poster-tele-msgtype">
												<?php
												$teleTypes = array(
													'text' => esc_html__( 'Text Message', 'wpwautoposter' ),
													'photo' => esc_html__( 'Image Post', 'wpwautoposter' ),
												);

												foreach( $teleTypes as $key => $teleType ) {
												 	echo '<option value="' . $key . '" ' . selected( $key, $tele_msgtype, false ) . '>' . $teleType . '</option>';
												} ?>
											</select>
										</div>

										<div class="wpw-auto-poster-fb-user-label">
											<?php
											esc_html_e( 'To the Chats', 'wpwautoposter' ); ?>
										</div>

										<div class="wpw-auto-poster-tele-chats wpw-auto-poster-fb-users-acc">
											<select name="wpw_auto_poster_options[tele_type_<?php echo $type->name; ?>_chats][]" multiple="multiple" class="wpw-auto-poster-tele-chats-select">
												<?php
												if( !empty($tele_all_chat) ) {
													foreach( $tele_all_chat as $key => $bots ) {

														if( empty($bots['chats']) || ! is_array($bots['chats']) ) continue;

														echo '<optgroup label="' . $bots['boat'] . '">';

														foreach( $bots['chats'] as $ckey => $chat ) {

															if( empty($chat['id']) ) continue;

															$chatVal = $bots['token'] . '|' . $chat['id'];

															$chTitle = isset( $chat['title'] ) ? $chat['title'] : '';
															if( empty($chTitle) && !empty($chat['name']) ) {
																$chTitle = $chat['name'];
															}

															echo '<option value="' . $chatVal . '" ' . selected( in_array($chatVal, $tele_chats), true, false ) . '>' . $chTitle . '</option>';
														}

														echo '</optgroup>';
													}
												} ?>
											</select>
										</div>
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
								<input id="tele_custom_global_msg" type="radio" name="wpw_auto_poster_options[tele_custom_msg_options]" value="global_msg" <?php checked($tele_custom_msg_options, 'global_msg', true);?> class="custom_msg_options">
								<label for="tele_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
                                
                                <input id="tele_custom_post_msg" type="radio" name="wpw_auto_poster_options[tele_custom_msg_options]" value="post_msg" <?php checked($tele_custom_msg_options, 'post_msg', true);?> class="custom_msg_options">
                                <label for="tele_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
							</td>	
						</tr>

						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_tele_post_image">
								<?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_options[tele_post_image]" id="wpw_auto_poster_options_tele_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $tele_postImg ); ?>">
								<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
								<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the Telegram chats.', 'wpwautoposter' ); ?></small></p>
							</td>
						</tr>
						<tr valign="top"  class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options_tele_post_img_caption">
								<?php esc_html_e( 'Image Caption : ', 'wpwautoposter' ); ?>
							</label></th>
							<td>
								<input type="text" name="wpw_auto_poster_options[tele_post_img_caption]" id="wpw_auto_poster_options_tele_post_img_caption" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $tele_postImgCap ); ?>">
								<p><small class="wpw-sap-custom-caption"><?php esc_html_e( 'Here you can enter default caption which will be used for the chat post. You can use following template tags within the caption message:', 'wpwautoposter' );

								$tele_cap_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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

					            print $tele_cap_str; ?>
							</td>
						</tr>
						<tr valign="top" class="global_msg_tr <?php echo $global_msg_style; ?>">
							<th scope="row"><label for="wpw_auto_poster_options[tele_global_message_template]">
								<?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?>
							</label></th>
							<td class="form-table-td">
								<textarea type="text" name="wpw_auto_poster_options[tele_global_message_template]" id="wpw_auto_poster_options[tele_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $tele_template_text ); ?></textarea>

								<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
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
				            	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>").
				            	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>");
					            print $tele_template_str; ?>
								</small></p>
							</td>
						</tr>

						<tr id="custom_post_type_templates_tele" class="post_msg_tr <?php echo $post_msg_style; ?>">
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

						            $postImg = ( isset( $wpw_auto_poster_options['tele_post_image_'.$type->name] ) ) ? $wpw_auto_poster_options['tele_post_image_'.$type->name] : '';

						            $postImgCap = ( isset( $wpw_auto_poster_options['tele_post_img_caption_'.$type->name] ) ) ? $wpw_auto_poster_options['tele_post_img_caption_'.$type->name] : '';

						            $postMsg = ( isset( $wpw_auto_poster_options['tele_global_message_template_'.$type->name] ) ) ? $wpw_auto_poster_options['tele_global_message_template_'.$type->name] : ''; ?>

						            <table id="tabs-<?php echo $type->name; ?>">
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_poster_options_tele_post_image_<?php echo $type->name; ?>"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
											</th>
											<td>
												<input type="text" name="wpw_auto_poster_options[tele_post_image_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_tele_post_image_<?php echo $type->name; ?>" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $postImg ); ?>">
												<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
												<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the Telegram chats.', 'wpwautoposter' ); ?></small></p>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="wpw_auto_poster_options_tele_post_img_caption_<?php echo $type->name; ?>">
												<?php esc_html_e( 'Image Caption : ', 'wpwautoposter' ); ?>
											</label></th>
											<td>
												<input type="text" name="wpw_auto_poster_options[tele_post_img_caption_<?php echo $type->name; ?>]" id="wpw_auto_poster_options_tele_post_img_caption_<?php echo $type->name; ?>" class="large-text" value="<?php echo $model->wpw_auto_poster_escape_attr( $postImgCap ); ?>">

												<p><small class="wpw-sap-custom-caption"><?php esc_html_e( 'Here you can enter a image caption which will be used for the chat image. You can use following template tags within the caption message:', 'wpwautoposter' ); ?>
												<?php
									            print $tele_cap_str; ?>
												</small></p>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<label for="wpw_auto_posting_tele_custom_msg_<?php echo $type->name; ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
											</th>
											<td class="form-table-td">
												<textarea type="text" name="wpw_auto_poster_options[tele_global_message_template_<?php echo $type->name; ?>]" id="wpw_auto_posting_tele_custom_msg_<?php echo $type->name; ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $postMsg ); ?></textarea>

												<p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
													<?php 
													$tele_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('display the first name.', 'wpwautoposter') .
										            '<br /><b><code>{last_name}</code></b> - ' . esc_html__('display the last name.', 'wpwautoposter') .
										            '<br /><b><code>{title}</code></b> - ' . esc_html__('display the default post title.', 'wpwautoposter') .
										            '<br /><b><code>{link}</code></b> - ' . esc_html__('display the default post link.', 'wpwautoposter') .
										            '<br /><b><code>{full_author}</code></b> - ' . esc_html__('display the full author name.', 'wpwautoposter') .
										            '<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('display the nickname of author.', 'wpwautoposter') .
										            '<br /><b><code>{post_type}</code></b> - ' . esc_html__(' display the post type.', 'wpwautoposter') .
										            '<br /><b><code>{sitename}</code></b> - ' . esc_html__('display the name of your site.', 'wpwautoposter') .
										            '<br /><b><code>{excerpt}</code></b> - ' . esc_html__('display the post excerpt.', 'wpwautoposter').
										            '<br /><b><code>{hashtags}</code></b> - ' . esc_html__('display the post tags as hashtags.', 'wpwautoposter').
										            '<br /><b><code>{hashcats}</code></b> - ' . esc_html__('display the post categories as hashtags.', 'wpwautoposter').
										            '<br /><b><code>{content}</code></b> - ' . esc_html__('display the post content.', 'wpwautoposter').
									            	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('display the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>").
									            	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>");
										            print $tele_template_str; ?>
													</small></p>
											</td>
										</tr>
									</table>
						       <?php } ?>
						    </th>
						</tr>
							
						<?php
						echo apply_filters ( 
							'wpweb_tele_settings_submit_button', 
							'<tr valign="top">
								<td colspan="2">
									<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
							</tr>'
						); ?>
					</tbody></table>

				</div><!-- .inside -->
			</div><!-- #tele_autopost -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-telegram-grant-permission -->
<!-- end of the auto poster meta box -->