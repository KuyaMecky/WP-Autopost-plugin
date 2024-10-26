<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

global $wpw_auto_poster_li_posting;

//posting class
$liposting = $wpw_auto_poster_li_posting;

/**
 * Tab argument
 */
 $limetatab = array(
	'class' => 'wpw_linkedin', //unique class name of each tabs
	'title' => esc_html__('LinkedIn', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when linkedin is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $limetatab );

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

// Get stored li app grant data
$wpw_auto_poster_li_sess_data = get_option('wpw_auto_poster_li_sess_data');

if( !is_ssl() ) {
	$poster_meta->addGrantPermission( $prefix . 'li_warning', array('desc' => esc_html__('Linkedin requires SSL for posting.', 'wpwautoposter'), 'url' => '', 'urltext' => '', 'tab' => 'wpw_linkedin') );

} elseif( WPW_AUTO_POSTER_LI_APP_ID == '' || WPW_AUTO_POSTER_LI_APP_SECRET == '' ) {
	$poster_meta->addGrantPermission( $prefix . 'li_warning', array('desc' => esc_html__('Enter your LinkedIn Application Details within the Settings Page, otherwise posting to LinkedIn won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_linkedin') );

} elseif( empty($wpw_auto_poster_li_sess_data) ) {
	$poster_meta->addGrantPermission( $prefix . 'li_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Linkedin.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Grant extended permissions now', 'wpwautoposter'), 'tab' => 'wpw_linkedin') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'li_status', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of LinkedIn wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_linkedin') );

$post_status = get_post_meta( $post_id, $prefix . 'li_status', true );
$post_label = esc_html__( 'Publish Post On LinkedIn : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to your LinkedIn.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On LinkedIn : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to your LinkedIn.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On LinkedIn : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to your LinkedIn.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On LinkedIn : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to your LinkedIn.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to LinkedIn%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to LinkedIn.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note : ', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>' ) . '</p>';

//post to linkedin
$poster_meta->addPublishBox( $prefix . 'post_to_linkedin', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_linkedin') );

//Immediate post to LinkedIn
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_linkedin', array('name' => esc_html__('Immediate Posting On LinkedIn : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to LinkedIn.', 'tab' => 'wpw_linkedin') );
}

//publish status to linkedin
$poster_meta->addTextarea( $prefix . 'li_post_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom title which will be used for the wall post. Leave it empty to use the post title. You can use following template tags within the custom title:', 'wpwautoposter') .
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
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_linkedin', 'rows' => 3) );

$li_profiles = $liposting->wpw_auto_poster_get_profiles_data();

//post to this account
$poster_meta->addSelect( $prefix . 'li_post_profile', $li_profiles, array('name' => esc_html__('Post To This Linkedin Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_linkedin') );

//publish status to linkedin image
$poster_meta->addImage( $prefix . 'li_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the LinkedIn wall post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_linkedin', 'show_path' => true) );

//custom link to post to facebook
$poster_meta->addText( $prefix . 'li_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used for the wall post. Leave it empty to use the link of the current post. The link must start with', 'wpwautoposter') . ' http://', 'tab' => 'wpw_linkedin') );

//comment to linkedin
$poster_meta->addTextarea( $prefix . 'li_post_comment', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can customize the content which will be used by LinkedIn for the wall post. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post content. You can use following template tags within the status text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{post_content}</code></b> - ' . esc_html__('displays the post content,', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
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
	), 'tab' => 'wpw_linkedin') );