<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Google My Business category selection fields
 *
 * The html markup for the Google My Business accounts in dropdown.
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

$gmb_selected_acc = array();
// Getting Google My Business cookie all accounts
$gmb_locations = wpw_auto_poster_get_gmb_accounts_location();
$wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');

$selected_acc = get_option('wpw_auto_poster_category_posting_acct');

$gmb_selected_acc = ( isset($selected_acc[$cat_id]['gmb']) && !empty($selected_acc[$cat_id]['gmb']) ) ? $selected_acc[$cat_id]['gmb'] : $gmb_selected_acc;
?>
<tr class="form-field term-wpw-auto-poster-tw-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Google My Business Account(s) : ', 'wpwautoposter'); ?></th>
    <td>
        <select name="wpw_auto_category_poster_options[gmb][]" id="wpw_auto_poster_gmb_type_post_method" class="wpw_auto_poster_gmb_type_post_method" multiple>
            <?php
            if (!empty($gmb_locations) && count($gmb_locations) > 0) {
                foreach ($gmb_locations as $tw_key => $tw_value) {
                    echo '<option value="' . esc_attr($tw_key) . '" ' . selected(in_array($tw_key, $gmb_selected_acc), true, true) . '>' . esc_attr($tw_value) . '</option>';
                }
            } //end if to check there is user connected to google my business or not
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>