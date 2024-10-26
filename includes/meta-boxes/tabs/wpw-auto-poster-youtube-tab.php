<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
$ytmetatab = array(
	'class' => 'wpw_youtube', //unique class name of each tabs
	'title' => esc_html__('Youtube', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when youtube is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $ytmetatab );

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored fb app grant data
$wpw_auto_poster_yt_sess_data = get_option('wpw_auto_poster_yt_sess_data');
$yt_keys = isset($wpw_auto_poster_options['yt_keys']) ? $wpw_auto_poster_options['yt_keys'] : array();
$yt_users = array();

if( !empty($wpw_auto_poster_yt_sess_data) ) {
	foreach( $wpw_auto_poster_yt_sess_data as $key => $yt_account ) {
		$yt_users[$yt_account['wpw_auto_poster_yt_cache']['id']] = trim( $yt_account['wpw_auto_poster_yt_cache']['id'] );
	}
}

if( empty($yt_keys) || count($yt_keys) < 1 ) {
	$poster_meta->addGrantPermission( $prefix . 'yt_warning', array('desc' => esc_html__('Enter your Youtube Application details within the Settings Page, otherwise posting to Youtube won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_youtube') );

} elseif( empty($wpw_auto_poster_yt_sess_data) ) { // Check youtube set or not
	$poster_meta->addGrantPermission( $prefix . 'yt_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Youtube.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_youtube') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'yt_published_on_yt', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Youtube post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_youtube') );

$post_status = get_post_meta( $post_id, $prefix . 'yt_published_on_yt', true );

$post_label = esc_html__( 'Publish Post On Youtube : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Youtube timeline.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Youtube : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Youtube timeline.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Youtube : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Youtube timeline.', 'wpwautoposter' );

} elseif (empty($post_status) && !empty($schedule_option)) {
	$post_label = esc_html__( 'Schedule Post On Youtube : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Youtube timeline.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Youtube%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Youtube.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta color-c83737"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>' ) . '</p>';

//post to youtube
$poster_meta->addPublishBox( $prefix . 'post_to_youtube', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_youtube') );

//Immediate post to youtube
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_youtube', array('name' => esc_html__('Immediate Posting On Youtube : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Youtube.', 'tab' => 'wpw_youtube') );
}

//post to this account
$poster_meta->addSelect( $prefix . 'yt_user_id', $yt_users, array('name' => esc_html__('Post To This Youtube Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_youtube') );

$poster_meta->addTextarea( $prefix . 'yt_post_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom title which will be used for the YouTube video title. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter') .
		'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
		'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
		'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
		'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
		'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
		'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
		'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
		'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
		'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
		'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
		'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
		'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
		'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
		) .
		'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_youtube', 'rows' => 3) );

// post image 
$poster_meta->addVideo( $prefix . 'yt_post_image', array('name' => esc_html__('Post Video : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default video which will be used for the Youtube post. Leave it empty to use the global video from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_youtube', 'show_path' => true) );

//post message
$poster_meta->addTextarea( $prefix . 'yt_custom_status_msg', array('default' => '', 'validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom caption text. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post content. You can use following template tags within the caption text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{displays_name}</code></b> - ' . esc_html__('displays the displays name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the short post description.', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(esc_html__('displays the post content %s  %s{content-digits}%s - displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<br />", "<code>", "</code>", "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(esc_html__('inserts the contents of the custom field with the specified name.  %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_youtube') );