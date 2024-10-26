<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * YouTube category selection fields
 *
 * The html markup for the YouTube accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 3.5.1
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

// Getting YouTube all accounts
$wpw_auto_poster_yt_sess_data = get_option('wpw_auto_poster_yt_sess_data');
$yt_keys = isset($wpw_auto_poster_options['yt_keys']) ? $wpw_auto_poster_options['yt_keys'] : array();
$yt_users = array();

if (!empty($wpw_auto_poster_yt_sess_data)) {
    foreach ($wpw_auto_poster_yt_sess_data as $key => $yt_account) {
        $yt_users[$yt_account['wpw_auto_poster_yt_cache']['id']] = trim($yt_account['wpw_auto_poster_yt_cache']['id']);
    }
}

$selected_acc = get_option('wpw_auto_poster_category_posting_acct');

$yt_selected_acc = ( isset($selected_acc[$cat_id]['yt']) && !empty($selected_acc[$cat_id]['yt']) ) ? $selected_acc[$cat_id]['yt'] : $gmb_selected_acc;
?>
<tr class="form-field term-wpw-auto-poster-yt-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This YouTube Account(s) : ', 'wpwautoposter'); ?></th>
    <td>
        <select name="wpw_auto_category_poster_options[yt][]" id="wpw_auto_poster_yt_type_post_method" class="wpw_auto_poster_yt_type_post_method" multiple>
            <?php
            if (!empty($yt_users) && count($yt_users) > 0) {
                foreach( $yt_users as $yt_key => $yt_value) {
                    echo '<option value="' . esc_attr($yt_key) . '" ' . selected(in_array($yt_key, $yt_selected_acc), true, true) . '>' . esc_attr($yt_value) . '</option>';
                }
            } //end if to check there is user connected to google my business or not
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>