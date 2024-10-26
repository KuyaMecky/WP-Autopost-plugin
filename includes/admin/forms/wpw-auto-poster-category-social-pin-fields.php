<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Pinterest category selection fields
 *
 * The html markup for the Pinterest accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */

global $wpw_auto_poster_options;

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

// Getting all pinterest account/boards
$pin_accounts = wpw_auto_poster_get_pin_accounts( 'all_app_users_with_boards' );
$wpw_auto_poster_pin_sess_data = get_option( 'wpw_auto_poster_pin_sess_data' ); // Getting pinterest app grant data

$pin_selected_acc = array();
$selected_acc = get_option('wpw_auto_poster_category_posting_acct');
$pin_selected_acc = ( isset($selected_acc[$cat_id]['pin']) && !empty($selected_acc[$cat_id]['pin']) ) ? $selected_acc[$cat_id]['pin'] : $pin_selected_acc;
?>

<tr class="form-field term-wpw-auto-poster-pin-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Pinterest Account(s) : ', 'wpwautoposter'); ?></th>
    <td>       
        <select name="wpw_auto_category_poster_options[pin][]" id="wpw_auto_poster_pin_type_post_method" class="wpw_auto_poster_pin_type_post_method"  multiple>
        <?php
            if( !empty($pin_accounts) && is_array($pin_accounts) ) {
                
                foreach( $pin_accounts as $aid => $aval ) {
                    
                    if( is_array( $aval ) ) {

                        $pin_app_data   = isset( $wpw_auto_poster_pin_sess_data[$aid] ) ? $wpw_auto_poster_pin_sess_data[$aid] : array();

                        $pin_opt_label  = !empty( $pin_app_data['wpw_auto_poster_pin_user_name'] ) ? $pin_app_data['wpw_auto_poster_pin_user_name'] .' - ' : '';
                        $pin_opt_label  = $pin_opt_label . $aid; ?>

                        <optgroup label="<?php echo esc_attr($pin_opt_label); ?>">
                            <?php foreach ( $aval as $aval_key => $aval_data ) { ?>
                                <option value="<?php echo esc_attr($aval_key); ?>" <?php selected( in_array( $aval_key, $pin_selected_acc ), true, true ); ?> ><?php echo esc_attr($aval_data); ?></option>
                            <?php } ?>
                        </optgroup>
                        
            <?php } else {  ?>

                <option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $pin_selected_acc ), true, true ); ?> ><?php echo esc_attr($aval); ?></option>
            <?php }
                
                } // End of foreach
            } // End of main if
        ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>
