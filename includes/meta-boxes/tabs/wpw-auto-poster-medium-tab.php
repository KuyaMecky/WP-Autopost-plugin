<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
$mediummetatab = array(
	'class' => 'wpw_medium', //unique class name of each tabs
	'title' => esc_html__('Medium', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

$defaulttabon = false;
//when Medium is on then inactive other tab by default

//initiate tabs in metabox
$poster_meta->addTabs($mediummetatab);

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored Reddit app grant data
$wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');

$medium_posting_accounts = wpw_auto_poster_get_medium_accounts_with_publications();

$poster_meta->addGrantPermission( $prefix . 'medium_warning_image', array('desc' => esc_html__('You will be not able to post featured image as the Medium API doesn\'t included with "uploadImage" extended permission.', 'wpwautoposter'), 'url' => '', 'urltext' => esc_html__('', 'wpwautoposter'), 'tab' => 'wpw_medium') );

if( empty($medium_posting_accounts) ) {
	$poster_meta->addGrantPermission( $prefix . 'medium_warning', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Medium.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_medium') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'medium_published_on_posts', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Medium post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_medium') );

$post_status = get_post_meta( $post_id, $prefix . 'medium_published_on_posts', true );
$post_label = esc_html__( 'Publish Post On Medium : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Medium.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Medium : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Medium.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Medium : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Medium.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On Medium : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Medium.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Medium%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Medium.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter' ), '<strong>', '</strong>') . '</p>';

//post to Medium
$poster_meta->addPublishBox( $prefix . 'post_to_medium', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_medium'));

//Immediate post to Reddit
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_medium', array('name' => esc_html__('Immediate Posting On Medium:', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Medium.', 'tab' => 'wpw_medium') );
}

//publish status to tumblr
$poster_meta->addTextarea( $prefix . 'medium_post_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_medium', 'rows' => '3') );

	//post to this account
    $poster_meta->addSelect( $prefix . 'medium_user_id',$medium_posting_accounts, array('name' => esc_html__('Post To This Medium Accounts : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_medium') );

	//Supported Tags
	$poster_meta->addText( $prefix . 'medium_custom_tags', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Tags : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter custom tags to send in posting. Add all the tags with (,) seperated values.', 'wpwautoposter'), 'tab' => 'wpw_medium') );

	//post link
	//publish status descriptin to reddit
	$poster_meta->addTextarea( $prefix . 'medium_post_desc', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter custom content which will appear underneath the post title in Medium. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post content. You can use following template tags within the custom message. Note minimum content of 140 characters is required for posting on Medium.', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the sitename/blogname.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_medium') );
?>
