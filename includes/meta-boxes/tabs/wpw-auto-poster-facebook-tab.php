<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

//Check Post status
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Facebook app version
$fb_app_version = ( !empty($wpw_auto_poster_options['fb_app_version']) ) ? $wpw_auto_poster_options['fb_app_version'] : '';

$facebook_auth_options = !empty($wpw_auto_poster_options['facebook_auth_options']) ? $wpw_auto_poster_options['facebook_auth_options'] : 'graph';

// Check Shedule general options
$schedule_option = !empty( $wpw_auto_poster_options['schedule_wallpost_option'] ) ? $wpw_auto_poster_options['schedule_wallpost_option'] : '';

/**
 * Tab argument
 */
$fbmetatab = array(
	'class' => 'wpw_facebook', //unique class name of each tabs
	'title' => esc_html__('Facebook', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when facebook is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $fbmetatab );

// Get stored fb app grant data
$wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data');

// Get all facebook account authenticated
$fb_users = wpw_auto_poster_get_fb_accounts('all_accounts');

//  code to remove user profile account for the facebook
if( !empty($fb_users) ) {
	foreach( $fb_users as $fb_user_key => $fb_user ) {
		$temp_check = explode('|', $fb_user_key);
		if( isset($temp_check[0]) && isset($temp_check[1]) && $temp_check[0] == $temp_check[1] ) {
			unset($fb_users[$fb_user_key]);
		}
	}
}

// Check facebook application id and secret must entered in settings page or not
if( (WPW_AUTO_POSTER_FB_APP_ID == '' || WPW_AUTO_POSTER_FB_APP_SECRET == '') &&
	$facebook_auth_options == 'graph') {

	$poster_meta->addGrantPermission($prefix . 'fb_warning', array('desc' => esc_html__('Enter your Facebook APP ID / Secret within the Settings Page, otherwise the Facebook posting won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_facebook'));
} elseif( empty($wpw_auto_poster_fb_sess_data) ) { // Check facebook user id is set or not
	$poster_meta->addGrantPermission($prefix . 'fb_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Facebook.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_facebook'));
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'fb_published_on_fb', array('name' => esc_html__('Status  :', 'wpwautoposter'), 'desc' => esc_html__('Status of Facebook wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_facebook') );

$post_status = get_post_meta( $post_id, $prefix . 'fb_published_on_fb', true );
$post_label = esc_html__( 'Publish Post On Facebook:', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Facebook Userwall.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Facebook : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Facebook Userwall.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Facebook : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Facebook Userwall.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On Facebook : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Facebook Userwall.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Facebook%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Facebook.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>' ) . '</p>';

//post to facebook
$poster_meta->addPublishBox( $prefix . 'post_to_facebook', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_facebook') );

//Immediate post to facebook
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_facebook', array('name' => esc_html__('Immediate Posting On Facebook : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Facebook Userwall.', 'tab' => 'wpw_facebook') );
}

//publish with diffrent post title
$poster_meta->addTextarea( $prefix . 'fb_custom_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom message which will be used for the wall post. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post title. You can use following template tags within the message:', 'wpwautoposter') .
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
		esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag. %s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_facebook', 'rows' => 3 )
);

do_action('wpw_auto_poster_after_custom_message_field_fb', $poster_meta, $post_id);

//post to this account
$poster_meta->addSelect( $prefix . 'fb_user_id', $fb_users, array('name' => esc_html__('Post To This Facebook Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_facebook') );

$wall_post_methods = array(
	'' => esc_html__('Default', 'wpwautoposter')
);

$wall_post_methods = array_merge( $wall_post_methods, $model->wpw_auto_poster_get_fb_posting_method() );

//post on wall as a type
$poster_meta->addSelect( $prefix . 'fb_posting_method', $wall_post_methods, array('name' => esc_html__('Post As : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select a Facebook post type. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_facebook') );

$share_posting_type_methods = array(
	'' => esc_html__('Default', 'wpwautoposter'),
	'link_posting' => esc_html__('Link posting', 'wpwautoposter'),
	'image_posting' => esc_html__('Image posting', 'wpwautoposter'),
);

// Fb Share posting type as
$poster_meta->addSelect( $prefix . 'fb_share_posting_type', $share_posting_type_methods, array('name' => esc_html__('Share type : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select a Facebook share post type. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_facebook') );

$sharepost_desc = '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second facebook_note"><strong>' . esc_html__('Note : ', 'wpwautoposter') . '</strong> ' . sprintf(esc_html__('If you are using image posting then the supported image formats are %sJPEG, BMP, PNG, GIF%s.', 'wpwautoposter'), '<strong>', '</strong>') . '</p>';

$sharepost_desc .= '<p class="wpw-auto-poster-meta wpw-auto-poster-meta_second facebook_note">' . sprintf(esc_html__('Recommend uploading image under 1MB.', 'wpwautoposter'), '<strong>', '</strong>') . '</p>';

$poster_meta->addGallery( $prefix . 'fb_post_gallery', array('name' => esc_html__('Image(s) to use : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload multiple images which will be used for the Facebook image posting. Leave it empty to use the featured image. if featured image is not set then it will take default image from the settings page.', 'wpwautoposter') . $sharepost_desc, 'tab' => 'wpw_facebook', 'show_path' => true) );

// Display custom image if fb version below 2.9
if( $fb_app_version < 209 ) {

	//post image url
	$poster_meta->addImage( $prefix . 'fb_post_image', array('name' => esc_html__('Post Image:', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Facebook wall post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter') . '<br><br><strong>' . esc_html__('Note:', 'wpwautoposter') . ' </strong>' . esc_html__('This option only work if your facebook app version is below 2.9. If you\'re using latest facebook app, it wont work.', 'wpwautoposter') . ' <a href="' . esc_url('https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/') . '" target="_blank">' . esc_html__('Learn More.', 'wpwautoposter') . '</a>', 'tab' => 'wpw_facebook', 'show_path' => true) );
}

//publish with diffrent post title
$poster_meta->addText( $prefix . 'fb_custom_status_msg', array('default' => esc_html__('New blog post:', 'wpwautoposter') . ' {title} - {link}', 'validate_func' => 'escape_html', 'name' => esc_html__('Status Update Text:', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom status update text. Leave it empty to  use the default one from the settings page. You can use following template tags within the status text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') . '<br /><code>{hashtags}</code> - ' . esc_html__('displayss the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
		esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
		esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_facebook' )
);

// Display custom post link and description if fb version below 2.11
if( $fb_app_version < 209 ) {
	//custom link to post to facebook
	$poster_meta->addText( $prefix . 'fb_custom_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link:', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used for  the wall post. Leave it empty to use the link of the current post. The link must start with', 'wpwautoposter') . ' http://', 'tab' => 'wpw_facebook') );
}