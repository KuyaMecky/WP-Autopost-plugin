<?php 

 // Exit if accessed directly
if (!defined('ABSPATH'))
exit;

/**
 * Medium category selection fields
 *
 * The html markup for the Reddit accounts in dropdown.
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

$medium_selected_acc = array();
// Getting Reddit all accounts

$medium_accounts = wpw_auto_poster_get_medium_accounts_with_publications();
$wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');


$selected_acc = get_option('wpw_auto_poster_category_posting_acct');
$medium_selected_acc = (isset($selected_acc[$cat_id]['medium']) && !empty($selected_acc[$cat_id]['medium']) ) ? $selected_acc[$cat_id]['medium'] : $medium_selected_acc; ?>


<tr class="form-field term-wpw-auto-poster-tw-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Medium Account(s) : ', 'wpwautoposter'); ?></th>
    <td>
        <select name="wpw_auto_category_poster_options[medium][]" id="wpw_auto_poster_medium_type_post_method" class="wpw_auto_poster_medium_type_post_method" multiple>
            <?php
            if (!empty($medium_accounts) && count($medium_accounts) > 0) {
                foreach ($medium_accounts as $md_key => $md_value) {
                    echo '<option value="' . esc_attr($md_key) . '" ' . selected(in_array($md_key, $medium_selected_acc), true, true) . '>' . esc_attr($md_value) . '</option>';
                }
            } //end if to check there is user connected to google my business or not
            ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>