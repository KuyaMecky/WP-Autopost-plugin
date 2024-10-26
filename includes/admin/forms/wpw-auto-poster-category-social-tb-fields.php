<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Tumblr category selection fields
 *
 * The html markup for the Tumblr accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 2.6.0
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

// Getting all tumblr
$tb_accounts = wpw_auto_poster_get_tb_accounts();
$wpw_auto_poster_tb_sess_data = get_option( 'wpw_auto_poster_tb_sess_data' ); // Getting tumblr app grant data

$tb_selected_acc = array();
$selected_acc = get_option('wpw_auto_poster_category_posting_acct');
$tb_selected_acc = ( isset($selected_acc[$cat_id]['tb']) && !empty($selected_acc[$cat_id]['tb']) ) ? $selected_acc[$cat_id]['tb'] : $tb_selected_acc;

?>

<tr class="form-field term-wpw-auto-poster-tb-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Tumblr Account(s) : ', 'wpwautoposter'); ?></th>
    <td>       
        <select name="wpw_auto_category_poster_options[tb][]" id="wpw_auto_poster_tb_type_post_method" class="wpw_auto_poster_tb_type_post_method"  multiple>
        <?php
            if( !empty($tb_accounts) && is_array($tb_accounts) ) {
                
                foreach( $tb_accounts as $aid => $aval ) {
                    
                    $tb_app_data   = isset( $wpw_auto_poster_tb_sess_data[$aid] ) ? $wpw_auto_poster_tb_sess_data[$aid] : array(); ?>

                    <option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $tb_selected_acc ), true, true ); ?> ><?php echo esc_attr($aval); ?></option>
                     <?php 
                } // End of foreach
            } // End of main if
        ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>
