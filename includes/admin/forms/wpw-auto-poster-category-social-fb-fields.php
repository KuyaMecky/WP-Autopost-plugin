<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Facebook category selection fields
 *
 * The html markup for the Facebook accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 2.3.1
 */
$cat_id = "";
if( !empty($_GET['tag_ID']) ) {
	$cat_id = stripslashes_deep($_GET['tag_ID']);
}

$type = 'category';
if( isset($_GET['taxonomy']) ) {
	$taxonomy = get_taxonomy( $_GET['taxonomy'] );
	if( isset($taxonomy->hierarchical) && $taxonomy->hierarchical != '1' ) {
		$type = 'tag';
	}
}

// Getting facebook all accounts
$fb_accounts = wpw_auto_poster_get_fb_accounts('all_app_users_with_name');
$wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data'); // Getting facebook app grant data

$fb_selected_acc = array();
$selected_acc = get_option('wpw_auto_poster_category_posting_acct');
$fb_selected_acc = ( isset($selected_acc[$cat_id]['fb']) && !empty($selected_acc[$cat_id]['fb']) ) ? $selected_acc[$cat_id]['fb'] : $fb_selected_acc;

?>

<tr class="form-field term-wpw-auto-poster-fb-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Facebook Account(s) : ', 'wpwautoposter'); ?></th>
    <td>       
        <select name="wpw_auto_category_poster_options[fb][]" id="wpw_auto_poster_fb_type_post_accout" class="wpw_auto_poster_fb_type_post_method"  multiple>
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

                            <?php foreach ($aval as $aval_key => $aval_data) { 
                                if( !empty( $aval_key ) ){ // added code for hide profile account for selection
                                    $temp_check = explode('|', $aval_key);
                                    if( isset( $temp_check[0]) && $temp_check[0] == $aid){
                                        continue;
                                    }
                                }
                                ?>
                                <option value="<?php echo esc_attr($aval_key); ?>" <?php selected(in_array($aval_key, $fb_selected_acc), true, true); ?>><?php echo esc_html($aval_data); ?></option>
                            <?php } ?>

                        </optgroup>

                    <?php } else {
                        ?>
                        <option value="<?php echo esc_attr($aid); ?>" <?php selected(in_array($aid, $wpw_auto_poster_fb_type_user), true, true); ?> ><?php echo esc_html($aval); ?></option>
                    <?php
                    }
                } // End of foreach
            } // End of main if
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>

    <?php do_action('wpw_auto_poster_after_fb_category_account') ;?>
</tr>