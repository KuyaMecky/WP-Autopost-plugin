<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

global $wpw_auto_poster_tb_posting;

//posting class
$tbposting = $wpw_auto_poster_tb_posting;

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

/**
 * Tab argument
 */
$tbmetatab = array(
	'class' => 'wpw_tumblr', //unique class name of each tabs
	'title' => esc_html__('Tumblr', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//Posting type
$tb_posting_types = array(
	'' => esc_html__('Select', 'wpwautoposter'),
	'text' => esc_html__('Text', 'wpwautoposter'),
	'link' => esc_html__('Link', 'wpwautoposter'),
	'photo' => esc_html__('Photo', 'wpwautoposter')
);

//when tumblr is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $tbmetatab );

// Get stored tb app grant data
$wpw_auto_poster_tb_sess_data = get_option('wpw_auto_poster_tb_sess_data');

$tb_users = wpw_auto_poster_get_tb_accounts();

if( WPW_AUTO_POSTER_TB_CONS_KEY == '' || WPW_AUTO_POSTER_TB_CONS_SECRET == '' ) {
	$poster_meta->addGrantPermission( $prefix . 'tb_warning', array('desc' => esc_html__('Enter your Tumblr Application Details within the Settings page, otherwise the posting to Tumblr won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );

} elseif( empty($wpw_auto_poster_tb_sess_data) ) {

	$poster_meta->addGrantPermission( $prefix . 'tb_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Tumblr.', 'wpwautoposter'), 'url' => $tbposting->wpw_auto_poster_get_tb_login_url(), 'urltext' => esc_html__('Grant extended permissions now.', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'tb_status', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Tumblr wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );

$post_status = get_post_meta( $post_id, $prefix . 'tb_status', true );
$post_label = esc_html__( 'Publish Post On Tumblr : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Tumblr Userwall.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Tumblr : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Tumblr Userwall.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Tumblr : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Tumblr Userwall.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On Tumblr : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to Tumblr Userwall.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Tumblr%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Tumblr.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>' ) . '</p>';

//post to tumblr
$poster_meta->addPublishBox( $prefix . 'post_to_tumblr', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_tumblr') );

//Immediate post to tumblr
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_tumblr', array('name' => esc_html__('Immediate Posting On Tumblr : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Tumblr.', 'tab' => 'wpw_tumblr') );
}

//posting type
$poster_meta->addSelect( $prefix . 'tb_posting_type', $tb_posting_types, array('name' => esc_html__('Posting Type : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Choose posting type which you want to use. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );

//publish status to tumblr
$poster_meta->addTextarea( $prefix . 'tb_post_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom title which will be used on the wall post. Leave it empty to use the post title. You can use following template tags within the custom title : ', 'wpwautoposter') .
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
	), 'tab' => 'wpw_tumblr', 'rows' => '3') );

$poster_meta->addSelect( $prefix . 'tb_user_id', $tb_users, array('name' => esc_html__('Post To This Tumblr Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global settings. Leave it  empty to use the global defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );

//post link
$poster_meta->addText( $prefix . 'tb_custom_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used on the wall post. Leave it empty to use the link to the post. The link must start with http://', 'wpwautoposter'), 'tab' => 'wpw_tumblr') );

//publish status descriptin to tumblr
$poster_meta->addTextarea( $prefix . 'tb_post_desc', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter custom content which will appear underneath the post title in Tumblr. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post content. You can use following template tags within the custom message : ', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the sitename/blogname.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.%s', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_tumblr') );

//post image url
$poster_meta->addImage( $prefix . 'tb_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Tumblr post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_tumblr', 'show_path' => true) );