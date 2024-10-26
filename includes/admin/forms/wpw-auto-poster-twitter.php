<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Twitter Settings
 *
 * The html markup for the Twitter settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
global $wpw_auto_poster_options, $wpw_auto_poster_model;

//model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['tw_posting_cats'] ) ? $wpw_auto_poster_options['tw_posting_cats']: 'exclude';

$twitter_keys = isset($wpw_auto_poster_options['twitter_keys']) ? $wpw_auto_poster_options['twitter_keys'] : array();

$tw_wp_pretty_url = ( !empty( $wpw_auto_poster_options['tw_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['tw_wp_pretty_url'] : '';

$tw_wp_pretty_url = !empty( $tw_wp_pretty_url ) ? ' checked="checked"' : '';

$tw_selected_shortner = isset( $wpw_auto_poster_options['tw_url_shortener'] ) ? $wpw_auto_poster_options['tw_url_shortener'] : '';
$tw_wp_pretty_url_css = ( $tw_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css': ' ba_wp_pretty_url_css_hide';

$tw_custom_msg_options = isset( $wpw_auto_poster_options['tw_custom_msg_options'] ) ? $wpw_auto_poster_options['tw_custom_msg_options'] : 'global_msg';

// get url shortner service list array 
$tw_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$tw_exclude_cats = array(); ?>

<!-- beginning of the twitter general settings meta box -->
<div id="wpw-auto-poster-twitter-general" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="twitter_general" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw_common_verticle_align'><?php esc_html_e('Twitter General Settings', 'wpwautoposter'); ?></span>
                </h3>

                <div class="inside">

                    <table class="form-table">											
                        <tbody>										
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[enable_twitter]"><?php esc_html_e('Enable Autoposting : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <input name="wpw_auto_poster_options[enable_twitter]" id="wpw_auto_poster_options[enable_twitter]" type="checkbox" value="1" <?php if (isset($wpw_auto_poster_options['enable_twitter'])) {

                                            checked('1', $wpw_auto_poster_options['enable_twitter']);
                                        } ?> />
                                    <p><small><?php esc_html_e('Check this box, if you want to automatically post your new content to Twitter.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>	

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[enable_twitter_for]"><?php esc_html_e('Enable Autoposting for : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <ul>
                                        <?php
                                        $all_types = get_post_types(array('public' => true), 'objects');
                                        $all_types = is_array($all_types) ? $all_types : array();

                                        if (!empty($wpw_auto_poster_options['enable_twitter_for'])) {
                                            $prevent_meta = $wpw_auto_poster_options['enable_twitter_for'];
                                        } else {
                                            $prevent_meta = '';
                                        }

                                        $prevent_meta = is_array($prevent_meta) ? $prevent_meta : array();

                                        if( !empty( $wpw_auto_poster_options['tw_post_type_tags'] ) ) {
                                            $tw_post_type_tags = $wpw_auto_poster_options['tw_post_type_tags'];
                                        } else {
                                            $tw_post_type_tags = array();
                                        }

                                        $static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

                                        if( !empty( $wpw_auto_poster_options['tw_post_type_cats'] ) ) {
                                            $tw_post_type_cats = $wpw_auto_poster_options['tw_post_type_cats'];
                                        } else {
                                            $tw_post_type_cats = array();
                                        }

                                        // Get saved categories for twitter to exclude from posting
                                            if( !empty( $wpw_auto_poster_options['tw_exclude_cats'] ) ) {
                                                $tw_exclude_cats = $wpw_auto_poster_options['tw_exclude_cats'];
                                            }

                                        foreach ($all_types as $type) {

                                            if (!is_object($type))
                                                continue;

                                            if( isset( $type->labels ) ) {
                                                $label = $type->labels->name ? $type->labels->name : $type->name;
                                            }
                                            else {
                                                $label = $type->name;
                                            }

                                            if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
                                                continue; // skip media
                                            $selected = ( in_array($type->name, $prevent_meta) ) ? 'checked="checked"' : '';
                                            ?>

                                            <li class="wpw-auto-poster-prevent-types">
                                                <input type="checkbox" id="wpw_auto_posting_twitter_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_twitter_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo esc_attr($selected); ?>/>

                                                <label for="wpw_auto_posting_twitter_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_html($label); ?></label>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <p><small><?php esc_html_e('Check each of the post types that you want to post automatically to Twitter when they get published.', 'wpwautoposter'); ?></small></p>  
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_post_type_tags][]"><?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?></label> 
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <select name="wpw_auto_poster_options[tw_post_type_tags][]" id="wpw_auto_poster_options[tw_post_type_tags]" class="tw_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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
                                                        if(isset($tw_post_type_tags[$type->name]) && !empty($tw_post_type_tags[$type->name])) {
                                                            $selected = ( in_array( $taxonomy->name, $tw_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
                                                        }
                                                        if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

                                                            echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
                                                        }
                                                    }
                                                    echo '</optgroup>';
                                                }
                                        }?>
                                    </select>
                                    <div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
                                    <p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_post_type_cats][]"><?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?></label> 
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <select name="wpw_auto_poster_options[tw_post_type_cats][]" id="wpw_auto_poster_options[tw_post_type_cats]" class="tw_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
                                                        if(isset($tw_post_type_cats[$type->name]) && !empty($tw_post_type_cats[$type->name])) {
                                                            $selected = ( in_array( $taxonomy->name, $tw_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
                                                        }
                                                        if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

                                                            echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_attr($taxonomy->label).'</option>';
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
                                    <label for="wpw_auto_poster_options[tw_exclude_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <div class="wpw-auto-poster-cats-option">
                                        <input name="wpw_auto_poster_options[tw_posting_cats]" id="tw_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
                                        <label for="tw_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
                                        <input name="wpw_auto_poster_options[tw_posting_cats]" id="tw_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
                                        <label for="tw_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
                                    </div>
                                    <select name="wpw_auto_poster_options[tw_exclude_cats][]" id="wpw_auto_poster_options[tw_exclude_cats]" class="tw_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">
                                        
                                        <?php

                                            $tw_exclude_cats_selected_values = !empty($wpw_auto_poster_options['tw_exclude_cats']) ? $wpw_auto_poster_options['tw_exclude_cats'] : array();
                                            $selected = 'selected="selected"';

                                            if(!empty($tw_exclude_cats_selected_values)) {
                                            
                                                foreach ($tw_exclude_cats_selected_values as $post_type => $post_data) {
                                                    
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
                                    <label for="wpw_auto_poster_options[tw_url_shortener]"><?php esc_html_e('URL Shortener : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td>
                                    <select name="wpw_auto_poster_options[tw_url_shortener]" id="wpw_auto_poster_options[tw_url_shortener]" class="tw_url_shortener" data-content='tw'>
                                        <?php

                                        foreach ($tw_url_shortener as $key => $option) {
                                            ?>
                                            <option value="<?php echo $model->wpw_auto_poster_escape_attr($key); ?>" <?php selected($tw_selected_shortner, $key); ?>>
                                            <?php echo esc_html($option); ?>
                                            </option>
                                            <?php
                                        }
                                        ?> 														
                                    </select>
                                    <p><small><?php esc_html_e('Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>

                            <tr id="row-tw-wp-pretty-url" valign="top" class="<?php print esc_attr($tw_wp_pretty_url_css);?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_wp_pretty_url]"><?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?></label> 
                                </th>
                                <td>
                                    <input type="checkbox" name="wpw_auto_poster_options[tw_wp_pretty_url]" id="wpw_auto_poster_options[tw_wp_pretty_url]" class="tw_wp_pretty_url" data-content='tw' value="yes" <?php print esc_attr($tw_wp_pretty_url);?>>
                                    <p><small><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></small></p>
                                </td>
                            </tr>

                            <?php
                            if( $tw_selected_shortner == 'bitly' ) {
                                $class = '';
                            } else {
                                $class = 'ba_wp_pretty_url_css_hide';
                            }

                            if($tw_selected_shortner == 'shorte.st') {
                                $shortest_class = 'ba_wp_pretty_url_css_hide';
                            } else {
                                $shortest_class = ' ba_wp_pretty_url_css_hide';
                            }

                            $bitly_token = isset( $wpw_auto_poster_options['tw_bitly_access_token'] ) ? $wpw_auto_poster_options['tw_bitly_access_token'] : '';

                            $shortest_token = isset( $wpw_auto_poster_options['tw_shortest_api_token'] ) ? $wpw_auto_poster_options['tw_shortest_api_token'] : ''; ?>

                            <tr valign="top" class="tw_setting_input_bitly <?php echo esc_attr($class); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
                                </th>
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[tw_bitly_access_token]" id="wpw_auto_poster_options[tw_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr($bitly_token); ?>" class="large-text">
                                </td>
                            </tr>

                            <tr valign="top" class="tw_setting_input_shortest <?php echo esc_attr($shortest_class); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
                                </th>
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[tw_shortest_api_token]" id="wpw_auto_poster_options[tw_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $shortest_token ); ?>" class="large-text">
                                </td>
                            </tr>
                            <?php
                            echo apply_filters(
                                    'wpweb_fb_settings_submit_button', '<tr valign="top">
																<td colspan="2">
																	<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
                            );
                            ?>
                        </tbody>
                    </table>

                </div><!-- .inside -->

            </div><!-- #twitter_general -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-twitter-general -->
<!-- end of the twitter general settings meta box -->

<!-- beginning of the twitter api settings meta box -->
<div id="wpw-auto-poster-twitter-api" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="twitter_api" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw_common_verticle_align'><?php esc_html_e('Twitter API Settings', 'wpwautoposter'); ?></span>
                </h3>

                <div class="inside">

                    <table class="form-table wpw-auto-poster-twitter-settings">											
                        <tbody>			
                            <tr valign="top">
                                <td>
                                    <div class="wpw-auto-poster-error info">
                                        <ul>
                                            <li>
                                                <?php esc_html_e('Before you can start publishing your content to Twitter you need to create a Twitter Application.', 'wpwautoposter'); ?>
                                                <?php printf(esc_html__('You can get a step by step tutorial on how to create a Twitter Application on our %sDocumentation%s.', 'wpwautoposter'), '<a href="'.esc_url('https://docs.Michael Tallada/social-network-integration/twitter/').'" target="_blank">', '</a>'); ?>

                                            </li>
                                        </ul>
                                    </div>                                
                                </td>
                            </tr>	

                            <tr valign="top"><td scope="row" colspan="5">
                            <table class="wpw-auto-poster-form-table-resposive">
                                <thead><tr>
                                    <td><strong>
                                        <label for="wpw_auto_poster_options[twitter_consumer_key]"><?php esc_html_e('API Key', 'wpwautoposter'); ?></label>
                                    </strong></td>
                                    <td scope="row"><strong>
                                        <label for="wpw_auto_poster_options[twitter_consumer_secret]"><?php esc_html_e('API Secret', 'wpwautoposter'); ?></label>
                                    </strong></td>
                                    <td scope="row"><strong>
                                        <label for="wpw_auto_poster_options[twitter_oauth_token]"><?php esc_html_e('Access Token', 'wpwautoposter'); ?></label>
                                    </strong></td>
                                    <td scope="row"><strong>
                                        <label for="wpw_auto_poster_options[twitter_oauth_secret]"><?php esc_html_e('Access Token Secret', 'wpwautoposter'); ?></label>
                                    </strong></td>
                                    <td></td>
                                </tr></thead>

                                <tbody>
	                            <?php
	                            if( !empty($twitter_keys) ) {
	                                foreach( $twitter_keys as $twitter_key => $twitter_value ) {

	                                    // dont disply delete link for first row
	                                    $twitter_delete_class = empty($twitter_key) ? '' : ' wpw-auto-poster-display-inline ';
	                                    ?>

	                                    <tr valign="top" class="wpw-auto-poster-twitter-account-details" data-row-id="<?php echo esc_attr($twitter_key); ?>">
	                                        <td width="24%" data-label="<?php esc_html_e('API Key', 'wpwautoposter'); ?>">
	                                            <input type="text" name="wpw_auto_poster_options[twitter_keys][<?php echo esc_attr($twitter_key); ?>][consumer_key]" class="wpw-auto-poster-twitter-consumer-key" value="<?php echo $model->wpw_auto_poster_escape_attr($twitter_keys[$twitter_key]['consumer_key']); ?>" class="large-text">
	                                            <p><small><?php esc_html_e('Enter Twitter Consumer Key.', 'wpwautoposter'); ?></small></p>  
	                                        </td>
	                                        <td width="24%" data-label="<?php esc_html_e('API Secret', 'wpwautoposter'); ?>">
	                                            <input type="text" name="wpw_auto_poster_options[twitter_keys][<?php echo esc_attr($twitter_key); ?>][consumer_secret]" class="wpw-auto-poster-twitter-consumer-secret" value="<?php echo $model->wpw_auto_poster_escape_attr($twitter_keys[$twitter_key]['consumer_secret']); ?>" class="large-text">
	                                            <p><small><?php esc_html_e('Enter Twitter Consumer Secret.', 'wpwautoposter'); ?></small></p>  
	                                        </td>
	                                        <td width="24%" data-label="<?php esc_html_e('Access Token', 'wpwautoposter'); ?>">
	                                            <input type="text" name="wpw_auto_poster_options[twitter_keys][<?php echo esc_attr($twitter_key); ?>][oauth_token]" class="wpw-auto-poster-twitter-oauth-token" value="<?php echo $model->wpw_auto_poster_escape_attr($twitter_keys[$twitter_key]['oauth_token']); ?>" class="large-text">
	                                            <p><small><?php esc_html_e('Enter Twitter Access Token.', 'wpwautoposter'); ?></small></p>  
	                                        </td>
	                                        <td width="24%" data-label="<?php esc_html_e('Access Token Secret', 'wpwautoposter'); ?>">
	                                            <input type="text" name="wpw_auto_poster_options[twitter_keys][<?php echo esc_attr($twitter_key); ?>][oauth_secret]" class="wpw-auto-poster-twitter-oauth-secret" value="<?php echo $model->wpw_auto_poster_escape_attr($twitter_keys[$twitter_key]['oauth_secret']); ?>" class="large-text">
	                                            <p><small><?php esc_html_e('Enter Twitter Access Token Secret.', 'wpwautoposter'); ?></small></p>
	                                        </td>
                                            <td>
                                                <a href="javascript:void(0);" class="wpw-auto-poster-delete-account wpw-auto-poster-twitter-remove <?php echo esc_attr($twitter_delete_class); ?>" title="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"/></a>
                                            </td>
	                                    </tr>
	                                <?php
	                                }
	                            } else { ?>
	                                <tr valign="top" class="wpw-auto-poster-twitter-account-details" data-row-id="0">
	                                    <td width="24%" data-label="<?php esc_html_e('API Key', 'wpwautoposter'); ?>">
	                                        <input type="text" name="wpw_auto_poster_options[twitter_keys][0][consumer_key]" class="wpw-auto-poster-twitter-consumer-key" value="" class="large-text">
	                                        <p><small><?php esc_html_e('Enter Twitter Consumer Key.', 'wpwautoposter'); ?></small></p>  
	                                    </td>
	                                    <td width="24%" data-label="<?php esc_html_e('API Secret', 'wpwautoposter'); ?>">
	                                        <input type="text" name="wpw_auto_poster_options[twitter_keys][0][consumer_secret]" class="wpw-auto-poster-twitter-consumer-secret" value="" class="large-text">
	                                        <p><small><?php esc_html_e('Enter Twitter Consumer Secret.', 'wpwautoposter'); ?></small></p>  
	                                    </td>
	                                    <td width="24%" data-label="<?php esc_html_e('Access Token', 'wpwautoposter'); ?>">
	                                        <input type="text" name="wpw_auto_poster_options[twitter_keys][0][oauth_token]" class="wpw-auto-poster-twitter-oauth-token" value="" class="large-text">
	                                        <p><small><?php esc_html_e('Enter Twitter Access Token.', 'wpwautoposter'); ?></small></p>  
	                                    </td>
	                                    <td width="24%" data-label="<?php esc_html_e('Access Token Secret', 'wpwautoposter'); ?>"	>
	                                        <input type="text" name="wpw_auto_poster_options[twitter_keys][0][oauth_secret]" class="wpw-auto-poster-twitter-oauth-secret" value="" class="large-text">
	                                        <p><small><?php esc_html_e('Enter Twitter Access Token Secret.', 'wpwautoposter'); ?></small></p>
	                                    </td>
                                        <td>
                                             <a href="javascript:void(0);" class="wpw-auto-poster-delete-account wpw-auto-poster-twitter-remove" title="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"/></a>
                                        </td>
	                                </tr>
	                            <?php } ?>
	                            </tbody>
                            </table>
                            </td></tr>

                            <?php $twitter_add_more = apply_filters( 'wpweb_twitter_add_more_button', true ); ?>
                            <?php if( $twitter_add_more ){ ?>
                            <tr>
                                <td colspan="4">
                                    <a class='wpw-auto-poster-add-more-account button' href='javascript:void(0);'><?php esc_html_e('Add Another App', 'wpwautoposter'); ?></a>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php
                            echo apply_filters(
                                'wpweb_fb_settings_submit_button', '<tr valign="top">
								<td colspan="4">
									<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
								</td>
							    </tr>'
                            ); ?>
                        </tbody>
                    </table>

                </div><!-- .inside -->

            </div><!-- #twitter_api -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-twitter-api -->
<!-- end of the twitter api settings meta box -->

<!-- beginning of the twitter template settings meta box -->
<div id="wpw-auto-poster-twitter-template" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="twitter_template" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw_common_verticle_align'><?php esc_html_e('Autopost to Twitter', 'wpwautoposter'); ?></span>
                </h3>

                <div class="inside">

                    <table class="form-table">											
                        <tbody>		

                            <tr valign="top"> 
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[prevent_post_tw_metabox]"><?php esc_html_e('Do not allow individual posts : ', 'wpwautoposter'); ?></label>
                                </th>									
                                <td>
                                    <input name="wpw_auto_poster_options[prevent_post_tw_metabox]" id="wpw_auto_poster_options[prevent_post_tw_metabox]" type="checkbox" value="1" <?php if (isset($wpw_auto_poster_options['prevent_post_tw_metabox'])) {
                                checked('1', $wpw_auto_poster_options['prevent_post_tw_metabox']);
                            } ?> />
                                    <p><small><?php esc_html_e('If you check this box, then it will hide meta settings for twitter from individual posts.', 'wpwautoposter'); ?></small></p>
                                </td>	
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label><?php esc_html_e('Map Autopost Location : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    $types = get_post_types(array('public' => true), 'objects');
                                    $types = is_array($types) ? $types : array();

                                    //Get twitter account details
                                    $tw_account_details = get_option('wpw_auto_poster_tw_account_details', array());

                                    foreach ($types as $type) {

                                        if (!is_object($type))
                                            continue;

                                        if (isset($wpw_auto_poster_options['tw_type_' . $type->name . '_user'])) {
                                            $wpw_auto_poster_tw_type_user = $wpw_auto_poster_options['tw_type_' . $type->name . '_user'];
                                        } else {
                                            $wpw_auto_poster_tw_type_user = '';
                                        }

                                        $wpw_auto_poster_tw_type_user = (array) $wpw_auto_poster_tw_type_user;

                                        if( isset( $type->labels ) ) {
                                            $label = $type->labels->name ? $type->labels->name : $type->name;
                                        }
                                        else {
                                            $label = $type->name;
                                        }

                                        if ($label == 'Media' || $label == 'media' || $type->name == 'elementor_library')
                                            continue; // skip media
                                        ?>		
                                        <div class="wpw-auto-poster-fb-types-wrap">
                                            <div class="wpw-auto-poster-tw-types-label">
                                                <?php
                                                esc_html_e('Autopost', 'wpwautoposter');
                                                echo ' ' . esc_html($label);
                                                esc_html_e(' to Twitter of this user(s)', 'wpwautoposter');
                                                ?>
                                            </div><!--.wpw-auto-poster-tw-types-label-->
                                            <div class="wpw-auto-poster-tw-users-acc">
                                                <select name="wpw_auto_poster_options[<?php echo 'tw_type_' . esc_attr($type->name) . '_user'; ?>][]" id="wpw_auto_poster_options[<?php echo 'tw_type_' . esc_attr($type->name) . '_user'; ?>][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
                                                    <?php
                                                    if (!empty($tw_account_details) && count($tw_account_details) > 0) {
                                                        foreach ($tw_account_details as $tw_key => $tw_value) {
                                                            echo '<option value="' . esc_attr($tw_key) . '" ' . selected(in_array($tw_key, $wpw_auto_poster_tw_type_user), true, false) . '>' . esc_html($tw_value) . '</option>';
                                                        }
                                                    } //end if to check there is user connected to twitter or not
                                                    ?>
                                                </select>
                                            </div><!--.wpw-auto-poster-tw-users-acc-->
                                        </div><!--.wpw-auto-poster-fb-types-wrap-->
    <?php
} //end foreach
?>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_disable_image_tweet]"><?php esc_html_e('Disable Image posting : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <input name="wpw_auto_poster_options[tw_disable_image_tweet]" id="wpw_auto_poster_options[tw_disable_image_tweet]" type="checkbox" value="1" <?php if (isset($wpw_auto_poster_options['tw_disable_image_tweet'])) {
                                checked('1', $wpw_auto_poster_options['tw_disable_image_tweet']);
                            } ?> />
                                    <p><small><?php esc_html_e('Check this box, if you want to disable image posting for twitter.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_tweet_template]"><?php esc_html_e('Posting Template : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <select name="wpw_auto_poster_options[tw_tweet_template]" id="wpw_auto_poster_options[tw_tweet_template]" class="tw_tweet_template">
                                        <?php
                                        $select_template = array("title_link" => "[title] - [link]", "title_fullauthor_link" => "[title] by [full_author] - [link]", "title_nickname_link" => "[title] by @[nickname_author] - [link]", "post_type_title_link" => "New [post_type]: [title] - [link]", "post_type_title_fullauthor_link" => "New [post_type]: [title] by [full_author] - [link]", "post_type_title_nickname_link" => "New [post_type]: [title] by [nickname_author] - [link]", "custom" => "Custom");

                                        $tw_selected_template = isset( $wpw_auto_poster_options['tw_tweet_template'] ) ? $wpw_auto_poster_options['tw_tweet_template'] : '';

                                        foreach( $select_template as $key => $option ) { ?>
                                            <option value="<?php echo $model->wpw_auto_poster_escape_attr($key); ?>" <?php selected($tw_selected_template, $key); ?>>
                                            <?php echo esc_html($option); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <p><small class="wpw-sap-custom-message"><?php esc_html_e('Choose the template you want to use to get your content published on twitter. You can customize this content as per your needs. There are several template tags available for customize the content in custom template. The template tags will then be replaced with the related information.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>
                            
                            <?php
                            if( $tw_selected_template == 'custom' ) {
                                $showing = '';
                                if( $tw_custom_msg_options == 'global_msg' ){
                                    $post_msg_style = ' post_msg_style_hide';
                                    $global_msg_style = '';
                                } else {
                                    $global_msg_style = ' post_msg_style_hide';
                                    $post_msg_style = '';
                                }
                            } else {
                                $showing = ' post_msg_style_hide';
                                $post_msg_style = ' post_msg_style_hide';
                                $global_msg_style = ' post_msg_style_hide';
                            } ?>

                            <tr valign="top" class="custom_template <?php echo esc_attr($showing); ?>">
                                <th scope="row">
                                    <label><?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
                                </th>
                                <td>
                                    <input id="tw_custom_global_msg" type="radio" name="wpw_auto_poster_options[tw_custom_msg_options]" value="global_msg" <?php checked($tw_custom_msg_options, 'global_msg', true);?> class="custom_msg_options" >
                                    <label for="tw_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
                                    
                                    <input id="tw_custom_post_msg" type="radio" name="wpw_auto_poster_options[tw_custom_msg_options]" value="post_msg" <?php checked($tw_custom_msg_options, 'post_msg', true);?> class="custom_msg_options" >
                                    <label for="tw_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
                                </td>   
                            </tr>

                            <tr valign="top" class="wpw_sap_tw_tweet_img global_msg_tr <?php echo esc_attr($global_msg_style); ?>" >
                                <th scope="row">
                                    <label for="wpw_auto_poster_options_tw_tweet_img"><?php esc_html_e('Post Image : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <?php
                                    $tw_tweet_img = isset( $wpw_auto_poster_options['tw_tweet_img'] ) ? $wpw_auto_poster_options['tw_tweet_img'] : ''; ?>

                                    <input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr($tw_tweet_img); ?>" name="wpw_auto_poster_options[tw_tweet_img]" id="wpw_auto_poster_options_tw_tweet_img" class="large-text wpw-auto-poster-img-field">
                                    <input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
                                    <p><small><?php esc_html_e('Here you can upload a default image which will be used for Tweets.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>

                            <tr valign="top" class="custom_template global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[tw_custom_tweet_template]"><?php esc_html_e('Custom Message : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td  class="form-table-td">
                                    <textarea class="large-text" name="wpw_auto_poster_options[tw_custom_tweet_template]"><?php echo ( isset( $wpw_auto_poster_options['tw_custom_tweet_template'] ) ) ? $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['tw_custom_tweet_template']) : ''; ?></textarea>
                                </td>
                            </tr>
                          
                            <tr id="custom_post_type_templates_tw" class="custom_template post_msg_tr <?php echo esc_attr($post_msg_style); ?>" >

                                <th colspan="2">
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
                                            
                                        $wpw_auto_poster_options['tw_global_message_template_'.$type->name] = ( isset( $wpw_auto_poster_options['tw_global_message_template_'.$type->name] ) ) ? $wpw_auto_poster_options['tw_global_message_template_'.$type->name] : '';

                                        $wpw_auto_poster_options['tw_tweet_img_'.$type->name] = ( isset( $wpw_auto_poster_options['tw_tweet_img_'.$type->name] ) ) ? $wpw_auto_poster_options['tw_tweet_img_'.$type->name] : '';
                                        ?>
                                        <table id="tabs-<?php echo esc_attr($type->name); ?>">
                                            <tr valign="top" class="wpw_sap_tw_tweet_img">
                                                <th scope="row">
                                                    <label for="wpw_auto_poster_options_tw_tweet_img_<?php echo esc_attr($type->name); ?>"><?php esc_html_e('Post Image : ', 'wpwautoposter'); ?></label>
                                                </th>
                                                <td>
                                                    <input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['tw_tweet_img_'.$type->name]); ?>" name="wpw_auto_poster_options[tw_tweet_img_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_tw_tweet_img_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field">
                                                    <input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
                                                    <p><small><?php esc_html_e('Here you can upload a default image which will be used for Tweets.', 'wpwautoposter'); ?></small></p>
                                                </td>
                                            </tr>

                                            <tr valign="top">

                                                <th scope="row">
                                                    <label for="wpw_auto_posting_tw_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message ', 'wpwautoposter'); ?>: </label>
                                                </th>

                                                <td class="form-table-td">
                                                    <textarea type="text" name="wpw_auto_poster_options[tw_global_message_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_tw_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['tw_global_message_template_'.$type->name] ); ?></textarea>
                                                </td>   
                                            </tr>
                                        </table>    
                                    <?php } ?>
                                </th>
                            </tr>
                                
                            <tr valign="top" class="custom_template <?php echo esc_attr($showing); ?>">
                                <th scope="row"></th>
                                <td class="global_msg_td">
                                    <p><small class="wpw-sap-custom-message"><?php esc_html_e( 'Here you can enter custom tweet template which will be used for the tweet. Leave it empty to use the post level tweet. ', 'wpwautoposter' ); ?>
                                        <b><?php esc_html_e( 'Tweet can contain up to 280 characters or Unicode glyphs. ', 'wpwautoposter' ); ?></b>
                                        <?php esc_html_e( 'You can use following template tags within the tweet template:', 'wpwautoposter' ); ?>
                                    <?php 
                                        $tw_template_str = '<br /><br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
                                         '<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
                                         '<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
                                         '<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
                                         '<br /><b><code>{post_type}</code></b> - ' . esc_html__(' displays the post type.', 'wpwautoposter') .
                                         '<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter').
                                         '<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter').
                                        '<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter').
                                        '<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter').
                                        '<br /><b><code>{content-digits}</code></b> - ' . sprintf(
                                            esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'),
                                            "<b>", "</b>"
                                        ).
                                        '<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
                                            esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'),
                                            "<b>", "</b>"
                                    );
                                        print $tw_template_str;
                                        ?>
                                    </small></p>
                                </td>
                            </tr>                            

                            <?php
                            echo apply_filters(
                                    'wpweb_fb_settings_submit_button', '<tr valign="top">
																<td colspan="2">
																	<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
                            );
                            ?>
                        </tbody>
                    </table>

                </div><!-- .inside -->

            </div><!-- #twitter_api -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-twitter-template -->
<!-- end of the twitter template settings meta box -->