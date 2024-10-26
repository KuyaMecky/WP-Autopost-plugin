<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Tab argument
 */
$redditmetatab = array(
	'class' => 'wpw_reddit', //unique class name of each tabs
	'title' => esc_html__('Reddit', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

$defaulttabon = false;
//when Reddit is on then inactive other tab by default

//initiate tabs in metabox
$poster_meta->addTabs( $redditmetatab );

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

//Posting type
$reddit_posting_types = array(
	''     => esc_html__('Select', 'wpwautoposter'),
	'self' => esc_html__('Text', 'wpwautoposter'),
	'link' => esc_html__('Link', 'wpwautoposter'),
	'image'=> esc_html__('Photo', 'wpwautoposter')
);

// Get stored Reddit app grant data
$wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');

$reddit_accounts	= wpw_auto_poster_get_reddit_accounts();
$subreddit_accounts	= wpw_auto_poster_get_reddit_accounts_with_subreddits();

if( empty($reddit_accounts) ) {
	$poster_meta->addGrantPermission( $prefix . 'reddit_warning', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Reddit.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_reddit') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'reddit_published_on_posts', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Reddit post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_reddit') );

$post_status = get_post_meta( $post_id, $prefix . 'reddit_published_on_posts', true );
$post_label = esc_html__( 'Publish Post On Reddit : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Reddit.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Reddit : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Reddit.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Reddit : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Reddit.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On Reddit : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Reddit.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Reddit%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Reddit.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p classs="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter' ), '<strong>', '</strong>') . '</p>';

//post to Reddit
$poster_meta->addPublishBox( $prefix . 'post_to_reddit', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_reddit') );

//Immediate post to Reddit
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_reddit', array('name' => esc_html__('Immediate Posting On Reddit : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Reddit.', 'tab' => 'wpw_reddit') );
}

//posting type
$poster_meta->addSelect( $prefix . 'reddit_posting_type',$reddit_posting_types, array('name' => esc_html__('Posting Type : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Choose posting type which you want to use. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_reddit') );

//publish status to tumblr
$poster_meta->addTextarea( $prefix . 'reddit_post_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title:', 'wpwautoposter') .
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
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_reddit', 'rows' => '3') );

//post to this account
$poster_meta->addSelect( $prefix . 'reddit_user_id',$subreddit_accounts, array('name' => esc_html__('Post To This Reddit Accounts : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_reddit') );

//post link
$poster_meta->addText( $prefix . 'reddit_custom_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used on the wall post. Leave it empty to use the link to the post. The link must start with http://', 'wpwautoposter'), 'tab' => 'wpw_reddit') );

$poster_meta->addImage( $prefix . 'reddit_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Reddit. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_reddit', 'show_path' => true) );

//publish status descriptin to reddit
$poster_meta->addTextarea( $prefix . 'reddit_post_desc', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter custom content which will appear underneath the post title in Reddit. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post content. You can use following template tags within the custom message:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the sitename/blogname.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_reddit') );