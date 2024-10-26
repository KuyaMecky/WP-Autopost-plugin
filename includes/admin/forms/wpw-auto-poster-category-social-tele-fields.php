<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Telegram category selection fields
 *
 * The html markup for the Telegram accounts in dropdown.
 *
 * @package Social Auto Poster
 * @since 3.7.0
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

$tele_selected_chats = array();

// Getting Telegram chats
$tele_all_chat = wpw_auto_poster_get_tele_chats();

$selected_chats = get_option('wpw_auto_poster_category_posting_acct');

$tele_selected_chats = ! empty( $selected_chats[$cat_id]['tele'] ) ? $selected_chats[$cat_id]['tele'] : $tele_selected_chats; ?>

<tr class="form-field term-wpw-auto-poster-tw-wrap">
	<th for="tag-description"><?php esc_html_e('Post To This Telegram Chat(s) : ', 'wpwautoposter'); ?></th>
	<td>
		<select name="wpw_auto_category_poster_options[tele][]" id="wpw_auto_poster_tele_type_post_method" class="wpw_auto_poster_tele_type_post_method" multiple>
			<?php
			if( !empty($tele_all_chat) && count($tele_all_chat) > 0 ) {
				foreach( $tele_all_chat as $key => $bots ) {

					if( empty($bots['chats']) || ! is_array($bots['chats']) ) continue;

					echo '<optgroup label="' . $bots['boat'] . '">';

					foreach( $bots['chats'] as $ckey => $chat ) {

						if( empty($chat['id']) ) continue;

						$chatVal = $bots['token'] . '|' . $chat['id'];

						$chTitle = isset( $chat['title'] ) ? $chat['title'] : '';
						if( empty($chTitle) && !empty($chat['name']) ) {
							$chTitle = $chat['name'];
						}

						echo '<option value="' . esc_attr($chatVal) . '" ' . selected(in_array($chatVal, $tele_selected_chats), true, false) . '>' . esc_html($chTitle) . '</option>';

					}

					echo '</optgroup>';
				}
            }  ?>
        </select>
        <p class="description"><?php printf( esc_html__('Post belongs to this %s will be posted to selected chat(s). This setting overrides the global default, but can be overridden by a post. Leave it it empty to use the global defaults.', 'wpwautoposter'), $type ); ?></p>
    </td>
</tr>