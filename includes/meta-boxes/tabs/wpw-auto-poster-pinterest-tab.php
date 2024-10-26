<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
 $pinmetatab = array(
	'class' => 'wpw_pinterest', //unique class name of each tabs
	'title' => esc_html__('Pinterest', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when pinterest is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $pinmetatab );

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored pin app grant data
$wpw_auto_poster_pin_sess_data = get_option('wpw_auto_poster_pin_sess_data');

// Get all pinterest account authenticated
$pin_users = wpw_auto_poster_get_pin_accounts('all_accounts');

$pinterest_auth_options = !empty($wpw_auto_poster_options['pinterest_auth_options']) ? $wpw_auto_poster_options['pinterest_auth_options'] : 'app';

if( ! is_ssl() && $pinterest_auth_options == 'app' ) {
	$poster_meta->addGrantPermission($prefix . 'pin_warning', array('desc' => esc_html__('Pinterest requires SSL for posting to boards.', 'wpwautoposter'), 'url' => '', 'urltext' => '', 'tab' => 'wpw_pinterest'));

} elseif( $pinterest_auth_options == 'cookie' ) {
	$allPinData = get_option( 'wpw_auto_poster_pin_sess_data', array() );
	if( empty($allPinData) ) {
		$poster_meta->addGrantPermission($prefix . 'pin_warning', array('desc' => esc_html__('Enter your Pinterest session id within the Settings Page, otherwise the Pinterest posting won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_pinterest'));
	}

// Check pinterest application id and secret must entered in settings page or not
} elseif( WPW_AUTO_POSTER_PIN_APP_ID == '' || WPW_AUTO_POSTER_PIN_APP_SECRET == '' ) {
	$poster_meta->addGrantPermission( $prefix . 'pin_warning', array('desc' => esc_html__('Enter your Pinterest APP ID / Secret within the Settings Page, otherwise the Pinterest posting won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_pinterest') );

} elseif( empty($wpw_auto_poster_pin_sess_data) ) { // Check pinterest user id is set or not
	$poster_meta->addGrantPermission( $prefix . 'pin_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Pinterest.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_pinterest') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'pin_published_on_pin', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Pinterest board post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_pinterest') );

$post_status = get_post_meta( $post_id, $prefix . 'pin_published_on_pin', true );
$post_label = esc_html__( 'Publish Post On Pinterest : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Pinterest board.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Pinterest : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Pinterest board.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Pinterest : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Pinterest board.', 'wpwautoposter' );

} elseif (empty($post_status) && !empty($schedule_option)) {
	$post_label = esc_html__( 'Schedule Post On Pinterest : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Pinterest board.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Pinterest%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Pinterest.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter' ), '<strong>', '</strong>') . '</p>';

//post to pinterest
$poster_meta->addPublishBox( $prefix . 'post_to_pinterest', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_pinterest') );

//Immediate post to pinterest
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_pinterest', array('name' => esc_html__('Immediate Posting On Pinterest : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Pinterest.', 'tab' => 'wpw_pinterest') );
}

//post to this account
$poster_meta->addSelect( $prefix . 'pin_user_id', $pin_users, array('name' => esc_html__('Post To This Pinterest Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_pinterest') );

//custom link to post to pinterest
$poster_meta->addText( $prefix . 'pin_custom_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used for  the board pins. Leave it empty to use the link of the current post.', 'wpwautoposter'), 'tab' => 'wpw_pinterest') );

$poster_meta->addImage( $prefix . 'pin_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Pinterest post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter') . '<br><br><strong>' . esc_html__('Note : ', 'wpwautoposter') . ' </strong>' . esc_html__('You need to select atleast one image, otherwise pinterest posting will not work.', 'wpwautoposter'), 'tab' => 'wpw_pinterest', 'show_path' => true) );

//publish with diffrent post title
$poster_meta->addTextarea( $prefix . 'pin_custom_status_msg', array('default' => '', 'validate_func' => 'escape_html', 'name' => esc_html__('Custom Message:', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom note text. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post title. You can use following template tags within the notes text : ', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the short post description.', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') . 
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag, %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_pinterest') );