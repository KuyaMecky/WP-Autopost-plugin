<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Instagram category selection fields
 *
 * The html markup for the Instagram accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 2.3.1
 */

global $wpw_auto_poster_options;

$cat_id = "";
if( !empty($_GET['tag_ID']) ) {
	$cat_id = stripslashes_deep($_GET['tag_ID']);
}

$cat_type = 'category';
if( isset($_GET['taxonomy']) ) {
	$taxonomy = get_taxonomy( $_GET['taxonomy'] );
	if( isset($taxonomy->hierarchical) && $taxonomy->hierarchical != '1' ) {
		$type = 'tag';
	}
}

// Getting instagram all accounts
$insta_accounts = wpw_auto_poster_get_insta_accounts('all_app_users_with_name');
$wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data'); // Getting instagram app grant data

$insta_selected_acc = array();
$selected_acc = get_option('wpw_auto_poster_category_posting_acct');
$insta_selected_acc = ( isset($selected_acc[$cat_id]['insta']) && !empty($selected_acc[$cat_id]['insta']) ) ? $selected_acc[$cat_id]['insta'] : $insta_selected_acc;

?>
<tr class="form-field term-wpw-auto-poster-fb-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Instagram Account(s) : ', 'wpwautoposter'); ?></th>
    <td>       
        <select name="wpw_auto_category_poster_options[insta][]" id="wpw_auto_poster_insta_type_post_method" class="wpw_auto_poster_insta_type_post_method"  multiple>
            <?php
            if (!empty($insta_accounts) && is_array($insta_accounts)) {

                foreach ($insta_accounts as $aid => $aval) {

                    if (is_array($aval)) {
                        
                        $insta_app_data = isset($wpw_auto_poster_insta_sess_data[$aid]) ? $wpw_auto_poster_insta_sess_data[$aid] : array();
                        
                        $insta_user_data = isset($insta_app_data['wpw_auto_poster_insta_fb_user_cache']) ? $insta_app_data['wpw_auto_poster_insta_fb_user_cache'] : array();
                        
                        $insta_opt_label = !empty($insta_user_data['name']) ? $insta_user_data['name'] . ' - ' : '';
                        $insta_opt_label = $insta_opt_label . $aid;

                    ?>
                        <optgroup label="<?php echo esc_attr($insta_opt_label); ?>">

                            <?php foreach ($aval as $aval_key => $aval_data) { 
                                if( !empty( $aval_key ) ){ // added code for hide profile account for selection
                                    $temp_check = explode('|', $aval_key);
                                    if( isset( $temp_check[0]) && $temp_check[0] == $aid){
                                        continue;
                                    }
                                }
                                ?>
                                <option value="<?php echo esc_attr($aval_key); ?>" <?php selected(in_array($aval_key, $insta_selected_acc), true, true); ?>><?php echo esc_html($aval_data); ?></option>
                            <?php } ?>
                        </optgroup>

                    <?php } else { 
                        
                        $all_types = get_post_types(array('public' => true), 'objects');
                        $types = is_array($all_types) ? $all_types : array();

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

                            $wpw_auto_poster_insta_type_user = array();

                            if (isset($wpw_auto_poster_options['insta_type_' . $type->name . '_user']) && !empty($wpw_auto_poster_options['insta_type_' . $type->name . '_user'])) {
                                $wpw_auto_poster_insta_type_user = $wpw_auto_poster_options['insta_type_' . $type->name . '_user'];
                            } 
                        }

                        $insta_app_data = isset($wpw_auto_poster_insta_sess_data[$aid]) ? $wpw_auto_poster_insta_sess_data[$aid] : array();
                        
                        $insta_user_data = isset($insta_app_data['wpw_auto_poster_insta_fb_user_cache']) ? $insta_app_data['wpw_auto_poster_insta_fb_user_cache'] : array();
                        
                        $insta_opt_label = !empty($insta_user_data['name']) ? $insta_user_data['name'] . ' - ' : '';
                        $insta_opt_label = $insta_opt_label . $aid;
                    ?>
                        <optgroup label="<?php echo esc_attr($insta_opt_label); ?>">
                            <?php 
                                foreach ($aval as $aval_key => $aval_data) { 
                                    if( !empty( $aval_key ) ) { 
                                        $temp_check = explode('|', $aval_key);
                                        if( isset( $temp_check[0]) && $temp_check[0] == $aid){
                                            continue;
                                        }
                                    }
                            ?>
                                <option value="<?php echo esc_attr($aval_key); ?>" <?php selected(in_array($aval_key, $wpw_auto_poster_insta_type_user), true, true); ?>><?php echo esc_html($aval_data); ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php 
                    }
                } 
            } 
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $cat_type ); ?></p>
    </td>
    <?php do_action('wpw_auto_poster_after_insta_category_account') ;?>
</tr>