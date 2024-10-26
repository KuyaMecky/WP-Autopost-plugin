<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
$gmbmetatab = array(
	'class' => 'wpw_gmb', //unique class name of each tabs
	'title' => esc_html__('Google My Business', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when Google My Business is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $gmbmetatab );

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored Google My Business app grant data
$wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');

// Get all Google My Business account authenticated
$gmb_users = wpw_auto_poster_get_gmb_accounts_location();
$gmb_accounts = wpw_auto_poster_get_gmb_accounts();

if( empty($gmb_accounts) ) {
	$poster_meta->addGrantPermission( $prefix . 'gmb_warning', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Google My Business.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_gmb') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'gmb_published_on_posts', array('name' => esc_html__('Status', 'wpwautoposter'), 'desc' => esc_html__('Status of Google My Business post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_gmb') );

$post_status = get_post_meta( $post_id, $prefix . 'gmb_published_on_posts', true );
$post_label = esc_html__( 'Publish Post On Google My Business : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Google My Business.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Google My Business : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Google My Business.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Google My Business : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Google My Business.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On Google My Business : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Google My Business.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Google My Business%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Google My Business.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter' ), '<strong>', '</strong>') . '</p>';

//post to Google My Business
$poster_meta->addPublishBox( $prefix . 'post_to_gmb', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_gmb') );

//Immediate post to Google My Business
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_gmb', array('name' => esc_html__('Immediate Posting On Google My Business : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Google My Business.', 'tab' => 'wpw_gmb') );
}

//post to this account
$poster_meta->addSelect( $prefix . 'gmb_user_id', $gmb_users, array('name' => esc_html__('Post To This Google My Business Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . '):', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_gmb') );

$poster_meta->addImage( $prefix . 'gmb_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Google My Business post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter') . '<br><br><strong>' . esc_html__('Note:', 'wpwautoposter') . ' </strong>' . sprintf(esc_html__('Minimum image size 250px / 250px height / width is required.', 'wpwautoposter'),"<b>","</b>"), 'tab' => 'wpw_gmb', 'show_path' => true) );

$gmb_add_buttons = $model->wpw_auto_poster_gmb_button_type();
$gmb_add_buttons = array_merge( array('' => esc_html__('Default', 'wpwautoposter')), $gmb_add_buttons );

// GMB share button type
$poster_meta->addSelect( $prefix . 'gmb_add_buttons', $gmb_add_buttons, array('name' => esc_html__('Button type:', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select Google My Business Button Type. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_gmb') );

//publish with diffrent post title
$poster_meta->addTextarea( $prefix . 'gmb_custom_status_msg', array('default' => '', 'validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom note text. Leave it empty to  use the default one from the settings page. You can use following template tags within the notes text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the short post description.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') . 
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b>- ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag, %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_gmb') );