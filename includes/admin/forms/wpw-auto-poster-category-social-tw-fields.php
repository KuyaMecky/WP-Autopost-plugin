<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Twitter category selection fields
 *
 * The html markup for the Twitter accounts in dropdown.
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

$tw_selected_acc = array();
$tw_account_details = get_option('wpw_auto_poster_tw_account_details', array());
$selected_acc = get_option('wpw_auto_poster_category_posting_acct');

$tw_selected_acc = ( isset($selected_acc[$cat_id]['tw']) && !empty($selected_acc[$cat_id]['tw']) ) ? $selected_acc[$cat_id]['tw'] : $tw_selected_acc;
?>
<tr class="form-field term-wpw-auto-poster-tw-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Twitter Account(s) : ', 'wpwautoposter'); ?></th>
    <td>
        <select name="wpw_auto_category_poster_options[tw][]" id="wpw_auto_poster_tw_type_post_method" class="wpw_auto_poster_tw_type_post_method" multiple>
            <?php
            if (!empty($tw_account_details) && count($tw_account_details) > 0) {

                foreach ($tw_account_details as $tw_key => $tw_value) {
                    echo '<option value="' . esc_attr($tw_key) . '" ' . selected(in_array($tw_key, $tw_selected_acc), true, true) . '>' . esc_attr($tw_value) . '</option>';
                }
            } //end if to check there is user connected to twitter or not
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>