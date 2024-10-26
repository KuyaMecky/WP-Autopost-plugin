<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Facebook Settings
 *
 * The html markup for the Facebook settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_fb_posting;

// model class
$model = $wpw_auto_poster_model;

// facebook posting class
$fbposting = $wpw_auto_poster_fb_posting;

// get all post methods
$wall_post_methods = $model->wpw_auto_poster_get_fb_posting_method();

$facebook_keys = isset($wpw_auto_poster_options['facebook_keys']) ? $wpw_auto_poster_options['facebook_keys'] : array();

$wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data'); // Getting facebook app grant data

$wpw_auto_poster_fb_sess_app_method = get_option('wpw_auto_poster_fb_sess_app_method');

$fb_app_version = (!empty($wpw_auto_poster_options['fb_app_version']) ) ? $wpw_auto_poster_options['fb_app_version'] : '';

$fb_app_versions = array('208' => '2.8 or below', '209' => '2.9 or above');

$fb_wp_pretty_url = (!empty($wpw_auto_poster_options['fb_wp_pretty_url']) ) ? $wpw_auto_poster_options['fb_wp_pretty_url'] : '';

$fb_wp_pretty_url = !empty($fb_wp_pretty_url) ? ' checked="checked"' : '';

$fb_selected_shortner = isset($wpw_auto_poster_options['fb_url_shortener']) ? $wpw_auto_poster_options['fb_url_shortener'] : '';
$fb_wp_pretty_url_css = ( $fb_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css' : ' ba_wp_pretty_url_css_hide';

// get url shortner service list array 
$fb_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$fb_exclude_cats = array();

$facebook_auth_options = !empty($wpw_auto_poster_options['facebook_auth_options']) ? $wpw_auto_poster_options['facebook_auth_options'] : 'graph';
$facebook_rest_type = !empty($wpw_auto_poster_options['facebook_rest_type']) ? $wpw_auto_poster_options['facebook_rest_type'] : 'android';

$fb_custom_msg_options = isset($wpw_auto_poster_options['fb_custom_msg_options']) ? $wpw_auto_poster_options['fb_custom_msg_options'] : 'global_msg';

$graph_style = "";
$rest_style = "";
$app_method_style = "";
if( $facebook_auth_options == 'graph') {
	$app_method_style = "repost_ba_global_message_template_hide";
	$rest_style = "repost_ba_global_message_template_hide";
} else {
	$graph_style = "repost_ba_global_message_template_hide";
	$rest_style = "repost_ba_global_message_template_hide";
}

if ($fb_custom_msg_options == 'global_msg') {
    $post_msg_style = "post_msg_style_hide";
    $global_msg_style = "";
} else {
    $global_msg_style = "global_msg_style_hide";
    $post_msg_style = "";
}

// Getting facebook all accounts
$fb_accounts = wpw_auto_poster_get_fb_accounts('all_app_users_with_name');

$fb_app_method = wpw_auto_poster_get_fb_app_method();

$image_notes_style = ( empty($wpw_auto_poster_options['fb_post_share_type']) || $wpw_auto_poster_options['fb_post_share_type'] != 'image_posting' ) ? 'fb_post_share_type' : '';

$cat_posts_type = !empty( $wpw_auto_poster_options['fb_posting_cats'] ) ? $wpw_auto_poster_options['fb_posting_cats']: 'exclude';
?>

<!-- beginning of the facebook general settings meta box -->
<div id="wpw-auto-poster-facebook-general" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="facebook_general" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw-sap-buffer-app-settings'><?php esc_html_e( 'Facebook General Settings', 'wpwautoposter' ); ?></span>
                </h3>

                <div class="inside">

                    <table class="form-table">											
                        <tbody>				
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[enable_facebook]"><?php esc_html_e('Enable Autoposting : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <input name="wpw_auto_poster_options[enable_facebook]" id="wpw_auto_poster_options[enable_facebook]" type="checkbox" value="1" <?php
                                    if (isset($wpw_auto_poster_options['enable_facebook'])) {
                                        checked('1', $wpw_auto_poster_options['enable_facebook']);
                                    }
                                    ?> />
                                    <p><small><?php esc_html_e('Check this box, if you want to automatically post your new content to Facebook.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[enable_facebook_for]"><?php esc_html_e('Enable Autoposting for : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <ul>
                                        <?php
                                        $all_types = get_post_types(array('public' => true), 'objects');
                                        $all_types = is_array($all_types) ? $all_types : array();

                                        if (!empty($wpw_auto_poster_options['enable_facebook_for'])) {
                                            $prevent_meta = $wpw_auto_poster_options['enable_facebook_for'];
                                        } else {
                                            $prevent_meta = array();
                                        }

                                        if (!empty($wpw_auto_poster_options['fb_post_type_tags'])) {
                                            $fb_post_type_tags = $wpw_auto_poster_options['fb_post_type_tags'];
                                        } else {
                                            $fb_post_type_tags = array();
                                        }

                                        
                                        $static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

                                        if (!empty($wpw_auto_poster_options['fb_post_type_cats'])) {
                                            $fb_post_type_cats = $wpw_auto_poster_options['fb_post_type_cats'];
                                        } else {
                                            $fb_post_type_cats = array();
                                        }

                                        // Get saved categories for fb to exclude from posting
                                        if (!empty($wpw_auto_poster_options['fb_exclude_cats'])) {
                                            $fb_exclude_cats = $wpw_auto_poster_options['fb_exclude_cats'];
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
                                                <input type="checkbox" id="wpw_auto_posting_facebook_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_facebook_for][]" value="<?php echo esc_attr($type->name); ?>" <?php echo esc_attr($selected); ?>/>

                                                <label for="wpw_auto_posting_facebook_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_attr($label); ?></label>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <p><small><?php esc_html_e('Check each of the post types that you want to post automatically to Facebook when they get published.', 'wpwautoposter'); ?></small></p>  
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_post_type_tags][]"><?php esc_html_e('Select Tags for hashtags : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <select name="wpw_auto_poster_options[fb_post_type_tags][]" id="wpw_auto_poster_options[fb_post_type_tags]" class="fb_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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
                                                    if (isset($fb_post_type_tags[$type->name]) && !empty($fb_post_type_tags[$type->name])) {
                                                        $selected = ( in_array($taxonomy->name, $fb_post_type_tags[$type->name]) ) ? 'selected="selected"' : '';
                                                    }
                                                    if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

                                                        echo '<option value="' . esc_attr($type->name) . "|" . esc_attr($taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_attr($taxonomy->label) . '</option>';
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
                                    <label for="wpw_auto_poster_options[fb_post_type_cats][]"><?php esc_html_e('Select Categories for hashtags : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <select name="wpw_auto_poster_options[fb_post_type_cats][]" id="wpw_auto_poster_options[fb_post_type_cats]" class="fb_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
                                                    if (isset($fb_post_type_cats[$type->name]) && !empty($fb_post_type_cats[$type->name])) {
                                                        $selected = ( in_array($taxonomy->name, $fb_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
                                                    }
                                                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

                                                        echo '<option value="' . esc_attr($type->name) . "|" . esc_attr($taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy->label) . '</option>';
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
                                    <label for="wpw_auto_poster_options[fb_exclude_cats][]"><?php esc_html_e('Select Taxonomies : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td class="wpw-auto-poster-select">
                                    <div class="wpw-auto-poster-cats-option">
                                        <input name="wpw_auto_poster_options[fb_posting_cats]" id="fb_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
                                        <label for="fb_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
                                        <input name="wpw_auto_poster_options[fb_posting_cats]" id="fb_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
                                        <label for="fb_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
                                    </div>
                                    <select name="wpw_auto_poster_options[fb_exclude_cats][]" id="wpw_auto_poster_options[fb_exclude_cats]" class="fb_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">

                                        <?php

                                        $fb_exclude_cats_selected_values = !empty($wpw_auto_poster_options['fb_exclude_cats']) ? $wpw_auto_poster_options['fb_exclude_cats'] : array();
                                        $selected = 'selected="selected"';

                                        if(!empty($fb_exclude_cats_selected_values)) {
                                         
                                            foreach ($fb_exclude_cats_selected_values as $post_type => $post_data) {
                                                
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
                                    <label for="wpw_auto_poster_options[fb_app_version]"><?php esc_html_e('App Version : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td>
                                    <select name="wpw_auto_poster_options[fb_app_version]" id="wpw_auto_poster_options[fb_app_version]" class="fb_app_version">
                                        <?php foreach ($fb_app_versions as $key => $version) { ?>
                                            <option value="<?php print esc_attr($key); ?>" <?php selected($fb_app_version, $key); ?>><?php print esc_html($version); ?></option>
                                        <?php } ?>
                                    </select>
                                    <p><small><?php esc_html_e('Select Facebook App version you are using for auto posting. Please make sure you create all Facebook apps with version "2.8 or below" OR "2.9 or above".', 'wpwautoposter'); ?><b><?php esc_html_e(' Recommnended App Version "2.9 or above".', 'wpwautoposter'); ?></b></small></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_url_shortener]"><?php esc_html_e('URL Shortener : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td>
                                    <select name="wpw_auto_poster_options[fb_url_shortener]" id="wpw_auto_poster_options[fb_url_shortener]" class="fb_url_shortener" data-content='fb'>
                                            <?php foreach ($fb_url_shortener as $key => $option) { ?>
                                            <option value="<?php echo $model->wpw_auto_poster_escape_attr($key); ?>" <?php selected($fb_selected_shortner, $key); ?>>
                                            <?php echo esc_html($option); ?>
                                            </option>
                                    <?php } ?>
                                    </select>
                                    <p><small><?php esc_html_e('Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter'); ?></small></p>
                                </td>
                            </tr>

                            <tr id="row-fb-wp-pretty-url" valign="top" class="<?php print esc_attr($fb_wp_pretty_url_css); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_wp_pretty_url]"><?php esc_html_e('Pretty permalink URL : ', 'wpwautoposter'); ?></label> 
                                </th>
                                <td>
                                    <input type="checkbox" name="wpw_auto_poster_options[fb_wp_pretty_url]" id="wpw_auto_poster_options[fb_wp_pretty_url]" class="fb_wp_pretty_url" data-content='fb' value="yes" <?php print esc_attr($fb_wp_pretty_url); ?>>
                                    <p><small><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></small></p>
                                </td>
                            </tr>

                            <?php
                            if ($fb_selected_shortner == 'bitly') {
                                $class = '';
                            } else {
                                $class = ' ba_wp_pretty_url_css_hide';
                            }

                            if ($fb_selected_shortner == 'shorte.st') {
                                $shortest_class = '';
                            } else {
                                $shortest_class = 'ba_wp_pretty_url_css_hide';
                            }
                            ?>

                            <tr valign="top" class="fb_setting_input_bitly <?php echo esc_attr($class); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_bitly_access_token]"><?php esc_html_e('Bit.ly Access Token', 'wpwautoposter'); ?> </label>
                                </th>
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[fb_bitly_access_token]" id="wpw_auto_poster_options[fb_bitly_access_token]" value="<?php echo (isset( $wpw_auto_poster_options['fb_bitly_access_token']  ) ) ? $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['fb_bitly_access_token']) : ''; ?>" class="large-text">
                                </td>
                            </tr>

                            <tr valign="top" class="fb_setting_input_shortest <?php echo esc_attr($shortest_class); ?>">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_shortest_api_token]"><?php esc_html_e('Shorte.st API Token', 'wpwautoposter'); ?> </label>
                                </th>
                                <td>
                                  <?php
                                    $fb_shortest_api_token = isset( $wpw_auto_poster_options['fb_shortest_api_token'] ) ? $wpw_auto_poster_options['fb_shortest_api_token'] : ''; ?>
                                    <input type="text" name="wpw_auto_poster_options[fb_shortest_api_token]" id="wpw_auto_poster_options[fb_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr(  $fb_shortest_api_token ); ?>" class="large-text" />
                                </td>
                            </tr>
                            <?php
                            echo apply_filters(
                                'wpweb_fb_settings_submit_button', '<tr valign="top">
									<td colspan="2">
									<input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
									</td>
								</tr>'
                            ); ?>
                        </tbody>
                    </table>

                </div><!-- .inside -->

            </div><!-- #facebook_general -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-facebook-general -->
<!-- end of the facebook general settings meta box -->

<!-- beginning of the facebook api settings meta box -->
<div id="wpw-auto-poster-facebook-api" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="facebook_api" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw-sap-buffer-app-settings'><?php esc_html_e('Facebook API Settings', 'wpwautoposter'); ?></span>
                </h3>

                <div class="inside">						
                    <table id="facebook-api-options" class="form-table wpw-auto-poster-facebook-api-options wpw-auto-poster-facebook-api-options_inline_width">
                        <tr>
                            <th>
                                <?php
                                esc_html_e('Authentication Type : ', 'wpwautoposter'); ?>
                            </th>
                            <td>
                                <input id="facebook_app_method" type="radio" name="wpw_auto_poster_options[facebook_auth_options]" value="appmethod" <?php checked($facebook_auth_options, 'appmethod', true); ?>><label for="facebook_app_method" class="wpw-auto-poster-label"><?php esc_html_e('Facebook APP Method', 'wpwautoposter'); ?></label>
                            </td>

                            <td>
                                <input id="facebook_graph_api" type="radio" name="wpw_auto_poster_options[facebook_auth_options]" value="graph" <?php checked($facebook_auth_options, 'graph', true); ?>><label for="facebook_graph_api" class="wpw-auto-poster-label"><?php esc_html_e('Facebook Graph API', 'wpwautoposter'); ?></label>
                            </td>
                        </tr>
                    </table>				
                    <table id="facebook-graph-api" class="form-table wpw-auto-poster-facebook-settings <?php print esc_attr($graph_style); ?>">
                        <tbody>				
                            <tr>
                                <td colspan="4">
                                    <p class="wpw-auto-poster-error-box"><?php
                                        printf(
                                            esc_html__('As facebook made some changes recently, graph API have some limitation. Posting will not work without %s app review %s. For more information about Graph API changes, %s click here %s.', 'wpwautoposter'), "<strong>", "</strong>", "<a href='".esc_url('https://docs.Michael Tallada/sap/facebook-problems-and-changes/')."' target='_blank'>", "</a>");
                                        ?></p>
                                </td>
                            </tr>
                            <tr valign="top">									
                                <td colspan="4">
                                    <div class="wpw-auto-poster-error info">
                                        <ul>
                                            <li>
                                                <?php esc_html_e('Before you can start publishing your content to Facebook you need to create a Facebook Application.', 'wpwautoposter'); ?>
                                                <?php printf(esc_html__('You can get a step by step tutorial on how to create a Facebook Application on our %sDocumentation%s.', 'wpwautoposter'), '<a href="'.esc_url('https://docs.Michael Tallada/social-network-integration/facebook/').'" target="_blank">', '</a>'); ?>

                                            </li>
                                        </ul>
                                    </div>  
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label>
                                    <?php esc_html_e('Allowing permissions : ', 'wpwautoposter'); ?>
                                </label></th>
                                <td colspan="3">
                                    <p><?php esc_html_e('Posting content to your chosen Facebook Page or Group requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.', 'wpwautoposter'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4">
                                    <p class="wpw-auto-poster-info-box"><?php
                                        printf(esc_html__('%s Note: %s Please note the Facebook App, Facebook profile or page and the user who authorizes the app MUST belong to the %s same Facebook account %s. So please make sure you are logged in to Facebook as the same user who created the app.', 'wpwautoposter'), "<b>", "</b>", "<b>", "</b>"
                                        );
                                        ?></p>
                                </td>
                            </tr>

                            <tr><td class="no-padding" colspan="5">
                                <table class="wpw-auto-poster-form-table-resposive">
                                    <thead><tr valign="top">
                                    <td scope="row">
                                        <strong><label for="wpw_auto_poster_options[facebook_keys][0][app_id]"><?php esc_html_e('App ID / API Key', 'wpwautoposter'); ?></label></strong>
                                    </td>
                                    <td scope="row">
                                        <strong><label for="wpw_auto_poster_options[facebook_keys][0][app_secret]"><?php esc_html_e('App Secret', 'wpwautoposter'); ?></label></strong>
                                    </td>
                                    <td scope="row">
                                        <strong><label><?php esc_html_e('Valid OAuth redirect URIs', 'wpwautoposter'); ?></label></strong>
                                    </td>
                                    <td scope="row">
                                        <strong><label><?php esc_html_e('Allowing permissions', 'wpwautoposter'); ?></label></strong>
                                    </td>                                    
                                    <td></td>
                                </tr></thead>

                                <tbody>
                                <?php
                                if( !empty($facebook_keys) && $facebook_auth_options == 'graph' ) {
                                    foreach( $facebook_keys as $facebook_key => $facebook_value ) {
                                        if( !isset($facebook_key) ) {
                                            $facebook_key = "0";
                                        }

                                        // Don't disply delete link for first row
                                        $facebook_delete_class = empty($facebook_key) ? '' : ' wpw-auto-poster-display-inline '; ?>

                                        <tr valign="top" class="wpw-auto-poster-facebook-account-details" data-row-id="<?php echo esc_attr($facebook_key); ?>">
                                            <td scope="row" width="25%" data-label="<?php esc_html_e('App ID / API Key', 'wpwautoposter'); ?>">
                                                <input type="text" name="wpw_auto_poster_options[facebook_keys][<?php echo esc_attr($facebook_key); ?>][app_id]" value="<?php echo $model->wpw_auto_poster_escape_attr($facebook_value['app_id']); ?>" class="large-text wpw-auto-poster-facebook-app-id" />
                                                <p><small><?php esc_html_e('Enter Facebook App ID / API Key.', 'wpwautoposter'); ?></small></p>  
                                            </td>
                                            <td scope="row" width="25%" data-label="<?php esc_html_e('App Secret', 'wpwautoposter'); ?>">
                                                <input type="text" name="wpw_auto_poster_options[facebook_keys][<?php echo esc_attr($facebook_key); ?>][app_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr($facebook_value['app_secret']); ?>" class="large-text wpw-auto-poster-facebook-app-secret" />
                                                <p><small><?php esc_html_e('Enter Facebook App Secret.', 'wpwautoposter'); ?></small></p>  
                                            </td>
                                            <td scope="row" width="25%" valign="top" data-label="<?php esc_html_e('Valid OAuth redirect URIs', 'wpwautoposter'); ?>">
                                                <?php
                                                $valid_auto_redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'wpw_fb_grant' => 'true', 'wpw_fb_app_id' => esc_attr(stripslashes($facebook_value['app_id']))), admin_url('admin.php')); ?>
                                                <input class="fb-oauth-url" id="fb-oauth-url-<?php print esc_attr($facebook_value['app_id']); ?>" type="text" value="<?php echo esc_attr($valid_auto_redirect_url); ?>" size="30" readonly/><button type="button" data-appid="<?php print esc_attr($facebook_value['app_id']); ?>" class="button copy-clipboard"><?php esc_html_e('Copy', 'wpwautoposter'); ?></button>
                                                <p><small><?php esc_html_e('Copy and paste it to Valid OAuth redirect URIs in facebook apps.', 'wpwautoposter'); ?></small></p>
                                            </td>
                                            <td scope="row" width="25%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e('Allowing permissions', 'wpwautoposter'); ?>">
                                                <?php
                                                if (!empty($facebook_value['app_id']) && !empty($facebook_value['app_secret']) && !empty($wpw_auto_poster_fb_sess_data[$facebook_value['app_id']])) {

                                                    echo '<p>' . esc_html__('You already granted extended permissions.', 'wpwautoposter') . '</p>';

                                                    echo apply_filters('wpweb_fb_settings_reset_session', sprintf( esc_html__("%s Reset User Session %s", 'wpwautoposter'), "<a href='" . add_query_arg(array('page' => 'wpw-auto-poster-settings', 'fb_reset_user' => '1', 'wpw_fb_app' => $facebook_value['app_id']), admin_url('admin.php')) . "'>", "</a>"
                                                    ));
                                                } elseif (!empty($facebook_value['app_id']) && !empty($facebook_value['app_secret'])) {
                                                    echo '<p><a href="' . esc_url($fbposting->wpw_auto_poster_get_fb_login_url($facebook_value['app_id'])) . '">' . esc_html__('Grant extended permissions', 'wpwautoposter') . '</a></p>';
                                                } ?>
                                            </td>                                    
                                            <td>
                                                <a href="javascript:void(0);" class="wpw-auto-poster-delete-fb-account wpw-auto-poster-facebook-remove <?php echo esc_attr($facebook_delete_class); ?>" title="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"/></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else { ?>
                                    <tr valign="top" class="wpw-auto-poster-facebook-account-details" data-row-id="<?php echo empty($facebook_key) ? '0' : esc_attr($facebook_key); ?>">
                                        <td scope="row" width="30%" data-label="<?php esc_html_e('Facebook App ID/API Key', 'wpwautoposter'); ?>">
                                            <input type="text" name="wpw_auto_poster_options[facebook_keys][0][app_id]" value="" class="large-text wpw-auto-poster-facebook-app-id" />
                                            <p><small><?php esc_html_e('Enter Facebook App ID / API Key.', 'wpwautoposter'); ?></small></p>  
                                        </td>
                                        <td scope="row" width="30%" data-label="<?php esc_html_e('Facebook App Secret', 'wpwautoposter'); ?>">
                                            <input type="text" name="wpw_auto_poster_options[facebook_keys][0][app_secret]" value="" class="large-text wpw-auto-poster-facebook-app-secret" />
                                            <p><small><?php esc_html_e('EnterFacebook App Secret.', 'wpwautoposter'); ?></small></p>  
                                        </td>
                                        <td scope="row" width="40%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e('Allowing permissions', 'wpwautoposter'); ?>"></td>
                                        <td>
                                            <a href="javascript:void(0);" class="wpw-auto-poster-delete-fb-account wpw-auto-poster-facebook-remove" title="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_META_URL); ?>/images/delete-16.png" alt="<?php esc_html_e('Delete', 'wpwautoposter'); ?>"/></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            </table>
                            </td></tr>

                            <?php $graph_api_add_more = apply_filters( 'wpweb_fb_graph_api_add_more_button', true ); ?>
                            <?php if( $graph_api_add_more ){ ?>
                                <tr>
                                    <td colspan="4">
                                        <a class='wpw-auto-poster-add-more-fb-account button' href='javascript:void(0);'><?php esc_html_e('Add more', 'wpwautoposter'); ?></a>
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

                    <table id="facebook-app-method" class="form-table wpw-auto-poster-facebook-settings wpw-auto-poster-facebook-custom-settings <?php print esc_attr($app_method_style); ?> <?php echo!empty($fb_app_method && $facebook_auth_options == 'appmethod') ? 'wpw-auto-poster-facebook-after-custom-app-added' : '' ?>">
                        <tbody>
                            <?php $wpweb_fb_settings_add_account_button = apply_filters( 'wpweb_fb_settings_add_account_button', true ); ?>
                            <?php if( $wpweb_fb_settings_add_account_button ){ ?>
                                <tr valign="top" class="wpw-auto-poster-facebook-account-details-custom-method <?php echo!empty($fb_app_method && $facebook_auth_options == 'appmethod') ? 'wpw-auto-poster-facebook-custom-app-added' : '' ?>" data-row-id="0">
                                    <td scope="row" class="row-btn" colspan="3">
                                <?php
                                echo '<p><a href="' . $fbposting->wpw_auto_poster_get_fb_app_method_login_url() . '">' . esc_html__('Add Facebook Account', 'wpwautoposter') . '</a></p>';
                                ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php
                            if( !empty($fb_app_method) && $facebook_auth_options == 'appmethod' ) { ?>
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
                                            foreach( $fb_app_method as $facebook_app_key => $facebook_app_value ) {
                                                
                                                // Don't disply delete link for first row
                                                if( ! is_array($facebook_app_value) ) continue;

                                                $fb_user_data = $facebook_app_value; ?>

                                                <tr valign="top" class="wpw-auto-poster-facebook-post-data">
                                                    <td scope="row" width="33%" data-label="<?php esc_html_e('User ID', 'wpwautoposter'); ?>"><?php print esc_html($fb_user_data['id']); ?></td>
                                                    
                                                    <td scope="row" width="33%" data-label="<?php esc_html_e('Account Name', 'wpwautoposter'); ?>"><?php print esc_html($fb_user_data['name']); ?></td>
                                                    
                                                    <td scope="row" width="15%" valign="top" class="wpw-grant-reset-data wpw-delete-fb-app-method width-16" data-label="<?php esc_html_e('Action', 'wpwautoposter'); ?>">
                                                        <?php
                                                        echo apply_filters('wpweb_fb_settings_reset_session', sprintf(
                                                            esc_html__("%s Delete Account %s", 'wpwautoposter'), "<a class='wpw-auto-poster-facebook-app-delete-link' href='" . add_query_arg(array('page' => 'wpw-auto-poster-settings', 'fb_reset_user' => '1', 'wpw_fb_app' => $fb_user_data['id'], 'fb_delet_user' => '1#wpw-auto-poster-facebook-api'), admin_url('admin.php')) . "'>", "</a>"
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
                            ); ?>
                        </tbody>
                    </table>

                </div><!-- .inside -->
            </div><!-- #facebook_api -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-facebook-api -->
<!-- end of the facebook api settings meta box -->

<?php if (isset($wpw_auto_poster_options['app_id']) && !empty($wpw_auto_poster_options['app_id']) && isset($wpw_auto_poster_options['app_secret']) && !empty($wpw_auto_poster_options['app_secret'])) { ?>
<?php } ?>


<!-- beginning of the facebook proxy settings meta box -->
<div id="wpw-auto-poster-facebook-proxy" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="facebook_proxy" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>
                <h3 class="hndle">
                    <span class='wpw-sap-buffer-app-settings'><?php esc_html_e('Facebook Proxy Settings', 'wpwautoposter'); ?></span>
                </h3>
                <div class="inside">                    	
                    <table class="form-table">											
                        <tbody>				
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_proxy_enable]"><?php esc_html_e('Enable Proxy : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <input name="wpw_auto_poster_options[fb_proxy_enable]" id="wpw_auto_poster_options[fb_proxy_enable]" type="checkbox" value="1" <?php
                                    if (isset($wpw_auto_poster_options['fb_proxy_enable'])) {
                                        checked('1', $wpw_auto_poster_options['fb_proxy_enable']);
                                    }
                                    ?> />                                    
                                </td>
                            </tr>
                        <tbody> 
                    </table>    
                    <?php
                    if(isset($wpw_auto_poster_options['fb_proxy_enable']) && $wpw_auto_poster_options['fb_proxy_enable']==1){
                        $fb_proxy_setting_hide = '';
                    }else{
                        $fb_proxy_setting_hide = 'none';
                    }
                    ?> 

                    <table id="facebook-proxy-settings" class="form-table wpw-auto-poster-form-table-resposive" style="display:<?php echo $fb_proxy_setting_hide;?>">                            
                        <thead>
                            <tr valign="top">
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[fb_proxy_url]"><?php esc_html_e('Proxy URL', 'wpwautoposter'); ?></label></strong>
                                </td>
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[fb_proxy_username]"><?php esc_html_e('Proxy Username', 'wpwautoposter'); ?></label></strong>
                                </td>
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[fb_proxy_password]"><?php esc_html_e('Proxy Password', 'wpwautoposter'); ?></label></strong>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr valign="top">                               
                                <td>
                                    <input type="url" name="wpw_auto_poster_options[fb_proxy_url]" id="wpw_auto_poster_options[fb_proxy_url]" value="<?php if(isset($wpw_auto_poster_options['fb_proxy_url'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['fb_proxy_url']); }?>" class="large-text wpw-auto-poster-facebook-proxy-url" />                                 
                                    <p><small><?php esc_html_e( 'Enter Proxy URL', 'wpwautoposter' ); ?></small></p>
                                </td>                            
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[fb_proxy_username]" id="wpw_auto_poster_options[fb_proxy_username]" value="<?php if(isset($wpw_auto_poster_options['fb_proxy_username'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['fb_proxy_username']); }?>" class="large-text wpw-auto-poster-facebook-proxy-username" />                                 
                                    <p><small><?php esc_html_e( 'Enter Proxy Username', 'wpwautoposter' ); ?></small></p>
                                </td>                           
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[fb_proxy_password]" id="wpw_auto_poster_options[fb_proxy_password]" value="<?php if(isset($wpw_auto_poster_options['fb_proxy_password'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['fb_proxy_password']); }?>" class="large-text wpw-auto-poster-facebook-proxy-password" />                                 
                                    <p><small><?php esc_html_e( 'Enter Proxy Password', 'wpwautoposter' ); ?></small></p>
                                </td>
                            </tr> 
                        </tbody>
                    </table>

                    <table class="form-table">											
                        <tbody>
                            <?php
                            echo apply_filters(
                                'wpweb_fb_settings_submit_button', '<tr valign="top">
                                    <td>
                                    <input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
                                    </td>
                                </tr>'
                            );?>
                        </tbody>
                    </table>
                </div><!-- .inside -->
            </div><!-- #facebook_proxy -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-facebook-proxy -->
<!-- end of the facebook proxy settings meta box -->

<!-- beginning of the autopost to facebook meta box -->
<div id="wpw-auto-poster-autopost-facebook" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="autopost_facebook" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>

                <h3 class="hndle">
                    <span class='wpw-sap-buffer-app-settings'><?php esc_html_e('Autopost to Facebook', 'wpwautoposter'); ?></span>
                </h3>

                <div class="inside">

                    <table class="form-table">											
                        <tbody>

                            <tr valign="top"> 
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[prevent_post_metabox]"><?php esc_html_e('Do not allow individual posts :', 'wpwautoposter'); ?></label>
                                </th>									
                                <td>
                                    <input name="wpw_auto_poster_options[prevent_post_metabox]" id="wpw_auto_poster_options[prevent_post_metabox]" type="checkbox" value="1" <?php
                                            if (isset($wpw_auto_poster_options['prevent_post_metabox'])) {
                                                checked('1', $wpw_auto_poster_options['prevent_post_metabox']);
                                            }
                                        ?> />
                                    <p><small><?php esc_html_e('If you check this box, then it will hide meta settings from individual posts.', 'wpwautoposter'); ?></small></p>
                                </td>	
                            </tr>

                            <tr valign="top"> 
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_post_share_type]"><?php esc_html_e('Share posting type : ', 'wpwautoposter'); ?></label>
                                </th>									
                                <td>
                                    <?php $fb_post_share_type = isset($wpw_auto_poster_options['fb_post_share_type']) ? $wpw_auto_poster_options['fb_post_share_type'] : ''; ?>

                                    <select name="wpw_auto_poster_options[fb_post_share_type]">
                                        <option value="link_posting" <?php print selected($fb_post_share_type, 'link_posting'); ?>><?php esc_html_e('Link posting', 'wpwautoposter'); ?></option>
                                        <option value="image_posting" <?php print selected($fb_post_share_type, 'image_posting'); ?>><?php esc_html_e('Image posting', 'wpwautoposter'); ?></option>
                                    </select>
                                    <p><small><?php esc_html_e('Select share posting method as link posting or image posting.', 'wpwautoposter'); ?></small></p>
                            <?php
                            $sharepost_desc = '<br><p class="wpw-auto-poster-meta fb-image-notes ' . esc_attr($image_notes_style) . ' "><strong>' . esc_html__('Note:', 'wpwautoposter') . '</strong> ' . sprintf(esc_html__('If you are using image posting then the supported image formats are %sJPEG, BMP, PNG, GIF%s.', 'wpwautoposter'), '<strong>', '</strong>') . '</p>';
                            $sharepost_desc .= '<p class="wpw-auto-poster-meta fb-image-notes ' . esc_attr($image_notes_style) . '">' . sprintf(esc_html__('Recommend uploading image under 1MB.', 'wpwautoposter'), '<strong>', '</strong>') . '</p>';

                            print $sharepost_desc;
                            ?>
                                </td>	
                            </tr>

                            <?php
                            $wpweb_fb_user_accounts = get_transient('wpweb_fb_user_accounts');
                            if (isset($wpweb_fb_user_accounts) && !empty($wpweb_fb_user_accounts)) {
                                $wpw_auto_poster_fb_user = $fbposting->wpw_auto_poster_get_fb_user_data();
                            } else {
                                $wpw_auto_poster_fb_user = array();
                            }

                            if (empty($wpw_auto_poster_fb_user['id'])) {
                                $wpw_auto_poster_fb_user['id'] = 0;
                            }

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

                                        if (isset($wpw_auto_poster_options['fb_type_' . $type->name . '_method'])) {
                                            $wpw_auto_poster_fb_type_method = $wpw_auto_poster_options['fb_type_' . $type->name . '_method'];
                                        } else {
                                            $wpw_auto_poster_fb_type_method = '';
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
                                                    echo ' ' . esc_html($label);
                                                    esc_html_e(' to Facebook', 'wpwautoposter');
                                                    ?>
                                            </div><!--.wpw-auto-poster-fb-types-label-->
                                            <div class="wpw-auto-poster-fb-type">
                                                <select name="wpw_auto_poster_options[fb_type_<?php echo esc_attr($type->name); ?>_method]" id="wpw_auto_poster_fb_type_post_method">

                                                <?php
                                                foreach ($wall_post_methods as $method_key => $method_value) {
                                                    echo '<option value="' . esc_attr($method_key) . '" ' . selected($wpw_auto_poster_fb_type_method, $method_key, false) . '>' . esc_html($method_value) . '</option>';
                                                }
                                                ?>
                                                </select>
                                            </div><!--.wpw-auto-poster-fb-type-->
                                            <div class="wpw-auto-poster-fb-user-label">
                                                <?php esc_html_e('on this Pages / Groups', 'wpwautoposter'); ?>
                                            </div><!--.wpw-auto-poster-fb-user-label-->
                                            <div class="wpw-auto-poster-fb-users-acc">
                                                <?php
                                                if (isset($wpw_auto_poster_options['fb_type_' . $type->name . '_user'])) {
                                                    $wpw_auto_poster_fb_type_user = $wpw_auto_poster_options['fb_type_' . $type->name . '_user'];
                                                } else {
                                                    $wpw_auto_poster_fb_type_user = '';
                                                }

                                                $wpw_auto_poster_fb_type_user = (array) $wpw_auto_poster_fb_type_user;
                                                ?>

                                                <select name="wpw_auto_poster_options[fb_type_<?php echo esc_attr($type->name); ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
                                                    <?php
                                                    if (!empty($fb_accounts) && is_array($fb_accounts)) {

                                                        foreach ($fb_accounts as $aid => $aval) {

                                                            if (is_array($aval)) {
                                                                $fb_app_data = isset($wpw_auto_poster_fb_sess_data[$aid]) ? $wpw_auto_poster_fb_sess_data[$aid] : array();
                                                                $fb_user_data = isset($fb_app_data['wpw_auto_poster_fb_user_cache']) ? $fb_app_data['wpw_auto_poster_fb_user_cache'] : array();
                                                                $fb_opt_label = !empty($fb_user_data['name']) ? $fb_user_data['name'] . ' - ' : '';
                                                                $fb_opt_label = $fb_opt_label . $aid;
                                                                ?>
                                                                <optgroup label="<?php echo esc_attr($fb_opt_label); ?>">

                                                                <?php foreach ($aval as $aval_key => $aval_data) { // added code for hide profile account for selection
                                                                    if( !empty( $aval_key ) ){
                                                                        $temp_check = explode('|', $aval_key);
                                                                        if( isset( $temp_check[0]) && $temp_check[0] == $aid){
                                                                            continue;
                                                                        }
                                                                    }
                                                                    ?>
                                                                        <option value="<?php echo esc_attr($aval_key); ?>" <?php selected(in_array($aval_key, $wpw_auto_poster_fb_type_user), true, true); ?> ><?php echo esc_attr($aval_data); ?></option>
                                                                <?php } ?>

                                                                </optgroup>

            <?php } else { ?>
                                                                <option value="<?php echo esc_attr($aid); ?>" <?php selected(in_array($aid, $wpw_auto_poster_fb_type_user), true, true); ?> ><?php echo esc_html($aval); ?></option>
                                                    <?php
                                                }
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
                                    <label><?php esc_html_e('Posting Format Options : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                    <input id="fb_custom_global_msg" type="radio" name="wpw_auto_poster_options[fb_custom_msg_options]" value="global_msg" <?php checked($fb_custom_msg_options, 'global_msg', true); ?> class="custom_msg_options">
                                    <label for="fb_custom_global_msg" class="wpw-auto-poster-label"><?php esc_html_e('Global', 'wpwautoposter'); ?></label>

                                    <input id="fb_custom_post_msg" type="radio" name="wpw_auto_poster_options[fb_custom_msg_options]" value="post_msg" <?php checked($fb_custom_msg_options, 'post_msg', true); ?> class="custom_msg_options">
                                    <label for="fb_custom_post_msg" class="wpw-auto-poster-label"><?php esc_html_e('Individual Post Type Message', 'wpwautoposter'); ?></label>
                                </td>	
                            </tr>

<?php if ($fb_app_version < 209) { ?>
                                <tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
                                    <th scope="row">
                                        <label for="wpw_auto_poster_options_fb_custom_img"><?php esc_html_e('Post Image:', 'wpwautoposter'); ?></label>
                                    </th>
                                    <td>
                                <?php $fb_custom_img = isset($wpw_auto_poster_options['fb_custom_img']) ? $wpw_auto_poster_options['fb_custom_img'] : ''; ?>

                                        <input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr($fb_custom_img); ?>" name="wpw_auto_poster_options[fb_custom_img]" id="wpw_auto_poster_options_fb_custom_img" class="large-text wpw-auto-poster-img-field">
                                        <input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
                                        <p><small><?php esc_html_e('Here you can upload a default image which will be used for the Facebook wall post.', 'wpwautoposter'); ?></small></p><br>
                                        <p><small><strong><?php esc_html_e('Note:', 'wpwautoposter'); ?></strong><?php esc_html_e('This option only work if your facebook app version is below 2.9. If you\'re using latest facebook app, it wont work.', 'wpwautoposter'); ?> <a href="<?php echo esc_url('https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/')?>" target="_blank"><?php esc_html_e('Learn More.', 'wpwautoposter'); ?></a></small></p>
                                    </td>	
                                </tr>
<?php } ?>

                            <tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">									
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[fb_global_message_template]"><?php esc_html_e('Custom Message : ', 'wpwautoposter'); ?></label>
                                </th>

<?php $fb_global_message_template = ( isset($wpw_auto_poster_options['fb_global_message_template']) ) ? $wpw_auto_poster_options['fb_global_message_template'] : ''; ?>

                                <td  class="form-table-td">
                                    <textarea type="text" name="wpw_auto_poster_options[fb_global_message_template]" id="wpw_auto_poster_options[fb_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr($fb_global_message_template); ?></textarea>
                                </td>	
                            </tr>

                            <tr id="custom_post_type_templates" class="post_msg_tr <?php echo esc_attr($post_msg_style); ?>">
                                <th colspan="2">
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
                                            <li><a href="#tabs-<?php echo esc_attr($type->name); ?>"><?php echo esc_attr($label); ?></a></li>
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

                                        $fb_global_message_template = ( isset($wpw_auto_poster_options['fb_global_message_template_' . $type->name]) ) ? $wpw_auto_poster_options['fb_global_message_template_' . $type->name] : '';
                                        ?>

                                        <table id="tabs-<?php echo esc_attr($type->name); ?>">

                                        <?php
                                        if ($fb_app_version < 209) {

                                            $fb_custom_img = ( isset($wpw_auto_poster_options['fb_custom_img_' . $type->name]) ) ? $wpw_auto_poster_options['fb_custom_img_' . $type->name] : '';
                                            ?>

                                                <tr valign="top">
                                                    <th scope="row">
                                                        <label for="wpw_auto_poster_options_fb_custom_img_<?php echo esc_attr($type->name); ?>"><?php esc_html_e('Post Image:', 'wpwautoposter'); ?></label>
                                                    </th>
                                                    <td>
                                                        <input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr($fb_custom_img); ?>" name="wpw_auto_poster_options[fb_custom_img_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_fb_custom_img_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field">
                                                        <input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e('Add Image', 'wpwautoposter'); ?>" />
                                                        <p><small><?php esc_html_e('Here you can upload a default image which will be used for the Facebook wall post.', 'wpwautoposter'); ?></small></p><br>
                                                        <p><small><strong><?php esc_html_e('Note:', 'wpwautoposter'); ?></strong><?php esc_html_e('This option only work if your facebook app version is below 2.9. If you\'re using latest facebook app, it wont work.', 'wpwautoposter'); ?> <a href="<?php echo esc_url('https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/');?>" target="_blank"><?php esc_html_e('Learn More.', 'wpwautoposter'); ?></a></small></p>
                                                    </td>	
                                                </tr>
                                            <?php } ?>

                                            <tr valign="top">

                                                <th scope="row">
                                                    <label for="wpw_auto_posting_facebook_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message', 'wpwautoposter'); ?>:</label>
                                                </th>

                                                <td class="form-table-td">
                                                    <textarea type="text" name="wpw_auto_poster_options[fb_global_message_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_facebook_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr($fb_global_message_template); ?></textarea>
                                                </td>	
                                            </tr>

                                            <tr valign="top">								
                                                <th scope="row"></th>
                                                <td class="global_msg_td">
                                                    <p><small class="wpw-sap-custom-message"><?php esc_html_e('Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter'); ?>
                                                            <?php
                                                            $fb_template_str = '<br /><br /><b><code>{first_name}</code> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
                                                                    '<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name,', 'wpwautoposter') .
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
                                                                    '<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
                                                                    ) .
                                                                    '<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
                                                            );
                                                            print $fb_template_str;
                                                            ?>
                                                        </small></p>
                                                </td>	
                                            </tr>

                                        </table>
                                            <?php }
                                            ?>
                                </th>
                            </tr>
                            <tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">								
                                <th scope="row"></th>
                                <td class="global_msg_td">
                                    <p><small class="wpw-sap-custom-message"><?php esc_html_e('Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter'); ?>
                                            <?php
                                            $fb_template_str = '<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
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
                                                    '<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
                                                    ) .
                                                    '<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>");
                                            print $fb_template_str;
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

            </div><!-- #autopost_facebook -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #ps-poster-autopost-facebook -->
<!-- end of the autopost to facebook meta box -->