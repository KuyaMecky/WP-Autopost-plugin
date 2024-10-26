<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


/**
 * Reddit category selection fields
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

$reddit_selected_acc = array();
// Getting Reddit all accounts

$reddit_accounts = wpw_auto_poster_get_reddit_accounts_with_subreddits();
$wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');

$selected_acc = get_option('wpw_auto_poster_category_posting_acct');

$reddit_selected_acc = (isset($selected_acc[$cat_id]['reddit']) && !empty($selected_acc[$cat_id]['reddit']) ) ? $selected_acc[$cat_id]['reddit'] : $reddit_selected_acc; ?>

<tr class="form-field term-wpw-auto-poster-tw-wrap">
    <th for="tag-description"><?php esc_html_e('Post To This Reddit Account(s) : ', 'wpwautoposter'); ?></th>
    <td>
        <select name="wpw_auto_category_poster_options[reddit][]" id="wpw_auto_poster_reddit_type_post_account" class="wpw_auto_poster_reddit_type_post_method" multiple>
            <?php if(!empty($reddit_accounts) && is_array($reddit_accounts)) {
                foreach($reddit_accounts as $aval_key => $aval_data) {
                    $main_account_details = explode('|', $aval_data['main-account']);
                    $main_account_name = !empty( $main_account_details[1] ) ? $main_account_details[1] : '';    
            ?>
            <optgroup label="<?php echo esc_attr($main_account_name); ?>" >
                <option value="<?php echo esc_attr($aval_data['main-account']); ?>" <?php selected(in_array($aval_data['main-account'] , $reddit_selected_acc), true, true ); ?> ><?php echo esc_attr($main_account_name); ?></option>
                <?php if (!empty($aval_data['subreddits']) && is_array($aval_data['subreddits'])) { 
                    foreach($aval_data['subreddits'] as $sr_key => $sr_data) { ?>
                        <option value="<?php echo esc_attr($sr_key); ?>" <?php selected(in_array($sr_key, $reddit_selected_acc), true, true ); ?> ><?php echo esc_attr($sr_data); ?></option>
                    <?php }
                } 
                ?>
            </optgroup>    
            <?php
                }
            }        

            ?>      
            <!--<?php
            if (!empty($reddit_accounts) && count($reddit_accounts) > 0) {
                foreach ($reddit_accounts as $tw_key => $tw_value) {
                    echo '<option value="' . esc_attr($tw_key) . '" ' . selected(in_array($tw_key, $reddit_selected_acc), true, true) . '>' . esc_attr($tw_value) . '</option>';
                }
            } //end if to check there is user connected to google my business or not
            ?>-->
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected account(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>