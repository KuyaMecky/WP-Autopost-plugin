<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
$telemetatab = array(
	'class' => 'wpw_telegram', //unique class name of each tabs
	'title' => esc_html__('Telegram', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when Telegram is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $telemetatab );

//Check Post status
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored li app grant data
$teleChats = wpw_auto_poster_get_tele_chats();

if( empty($teleChats) ) {
	$poster_meta->addGrantPermission($prefix . 'tele_warning', array('desc' => esc_html__('Enter your Telegram Boat Details within the Settings Page, otherwise posting to Telegram won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_telegram'));
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'tele_status', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Telegram wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_telegram') );

$post_status = get_post_meta( $post_id, $prefix.'tele_status', true );
$post_label  = esc_html__( 'Publish Post On Telegram : ', 'wpwautoposter' );
$post_desc   = esc_html__( 'Publish this Post to your Telegram.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Telegram : ', 'wpwautoposter' );
	$post_desc  = esc_html__( 'Re-publish this Post to your Telegram.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Telegram : ', 'wpwautoposter' );
	$post_desc  = esc_html__( 'Re-schedule this Post to your Telegram.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label  = esc_html__( 'Schedule Post On Telegram : ', 'wpwautoposter' );
	$post_desc   = esc_html__( 'Schedule this Post to your Telegram.', 'wpwautoposter' );
}

$post_desc .= '<br>'.sprintf( esc_html__( 'If you have enabled %sEnable auto posting to Telegram%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Telegram.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>'.esc_html__( 'Note : ', 'wpwautoposter' ).'</strong> '. sprintf( esc_html__( 'This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>','</strong>' ).'</p>';

		//post to linkedin
$poster_meta->addPublishBox( $prefix . 'post_to_telegram', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_telegram') );

//Immediate post to LinkedIn
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_telegram', array('name' => esc_html__('Immediate Posting On Telegram:', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Telegram.', 'tab' => 'wpw_telegram') );
}

// Telegram chats
$tele_bots = wpw_auto_poster_get_tele_chats();

$tele_chats = array();
if( !empty($tele_bots) ) {
	foreach( $tele_bots as $bkey => $bot ) {

		if( empty($bot['chats']) || ! is_array($bot['chats']) ) continue;

		foreach( $bot['chats'] as $ckey => $chat ) {
			if( empty($chat['id']) ) continue;

			$chTitle = isset( $chat['title'] ) ? $chat['title'] : '';
			if( empty($chTitle) && !empty($chat['name']) ) {
				$chTitle = $chat['name'];
			}

			$tele_chats[$bot['token'] . '|' . $ckey] = $bot['boat'] . ' | ' . $chTitle;
		}
	}
}

//post to this account
$poster_meta->addSelect( $prefix . 'tele_post_profile', $tele_chats, array('name' => esc_html__('Post To This Telegram Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_telegram')  );

$teleTypes = array(
	'' => esc_html__( 'Default', 'wpwautoposter' ),
	'text' => esc_html__( 'Text Message', 'wpwautoposter' ),
	'photo' => esc_html__( 'Image Post', 'wpwautoposter' ),
);
$poster_meta->addSelect( $prefix . 'tele_post_msgtype', $teleTypes, array('name' => esc_html__('Posting Type : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select posting type, This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_telegram') );

//publish status to linkedin image
$poster_meta->addImage( $prefix . 'tele_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Telegram wall post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_telegram', 'show_path' => true) );

//custom link to post to facebook
$poster_meta->addText( $prefix . 'tele_post_img_caption', array('validate_func' => 'escape_html', 'name' => esc_html__('Image Caption : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a image caption which will be used for the chat image. You can use following template tags within the caption message:', 'wpwautoposter') . 

	'<br /><br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__(' displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter').
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter').
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter').
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter').
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>").
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>"),
	'tab' => 'wpw_telegram') );

//comment to linkedin
$poster_meta->addTextarea( $prefix . 'tele_post_comment', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can customize the content which will be used by Telegram for the wall post. You can use following template tags within the status text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter').
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter').
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter').
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter').
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
		esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'),
		"<b>", "</b>"
	).
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
		esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_telegram') );