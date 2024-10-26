<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

$opttemplate = isset( $wpw_auto_poster_options['tw_tweet_template'] ) ? $wpw_auto_poster_options['tw_tweet_template'] : 'title_link';

$post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : get_post_type($post_id);

//tweet default tempalte 
$defaulttemplate = $model->wpw_auto_poster_get_tweet_template( $opttemplate, $post_type );

/**
 * Tab argument
 */
$twmetatab = array(
	'class' => 'wpw_twitter', //unique class name of each tabs
	'title' => esc_html__('Twitter', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when twitter is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs( $twmetatab );

if( WPW_AUTO_POSTER_TW_CONS_KEY == '' || WPW_AUTO_POSTER_TW_CONS_SECRET == '' || 
	WPW_AUTO_POSTER_TW_AUTH_TOKEN == '' || WPW_AUTO_POSTER_TW_AUTH_SECRET == '' ) {

	$poster_meta->addGrantPermission( $prefix . 'tw_warning', array('desc' => esc_html__('Enter your Twitter Application Details within the Settings Page, otherwise posting to Twitter won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_twitter') );
}

//Get twitter account details
$tw_users = get_option( 'wpw_auto_poster_tw_account_details', array() );

//add label to show status
$poster_meta->addTweetStatus($prefix . 'tw_status', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Twitter wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_twitter'));

$post_status = get_post_meta($post_id, $prefix . 'tw_status', true);

$post_label = esc_html__( 'Publish Post On Twitter : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to Twitter.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On Twitter : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to Twitter.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On Twitter : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to Twitter.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__('Schedule Post On Twitter : ', 'wpwautoposter');
	$post_desc = esc_html__('Schedule this Post to Twitter.', 'wpwautoposter');
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to Twitter%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Twitter.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note:', 'wpwautoposter' ) . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>' ) . '</p>';

//post to twitter
$poster_meta->addPublishBox( $prefix . 'post_to_twitter', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_twitter') );

//Immediate post to twitter
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_twitter', array('name' => esc_html__('Immediate Posting On Twitter : ', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Twitter.', 'tab' => 'wpw_twitter') );
}

//post to this account 
$poster_meta->addSelect( $prefix . 'tw_user_id', $tw_users, array('name' => esc_html__('Post To This Twitter Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_twitter') );

//tweet mode
$poster_meta->addTweetMode( $prefix . 'tw_tweet_mode', array('name' => esc_html__('Mode : ', 'wpwautoposter'), 'desc' => esc_html__('Tweet Template Mode.', 'wpwautoposter'), 'tab' => 'wpw_twitter') );

if( empty($wpw_auto_poster_options['tw_disable_image_tweet']) ) {
	//tweet image url
	$poster_meta->addImage( $prefix . 'tw_image', array('name' => esc_html__('Tweet Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Tweet Image. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_twitter', 'show_path' => true) );
}

//tweet template, do not change the order for tweet template and tweet preview field
$poster_meta->addTweetTemplate( $prefix . 'tw_template', array('default' => $defaulttemplate, 'validate_func' => 'escape_html', 'name' => esc_html__('Tweet Template : ', 'wpwautoposter'), 'desc' => __('Here you can enter custom tweet template which will be used for the tweet. Leave it empty to use the post level tweet. <b> Tweet can contain up to 280 characters or Unicode glyphs. </b> You can use following template tags within the tweet template', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{link}</code></b> - ' . esc_html__('displays the post link.', 'wpwautoposter') .
	'<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
	'<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
	'<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
	'<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
	'<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
	'<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
	'<br /><b><code>{content-digits}</code></b> - ' . sprintf(
			esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content.', 'wpwautoposter'), "<b>", "</b>"
	) .
	'<br /><b><code>{CF-CustomFieldName}</code></b> - ' . sprintf(
			esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.', 'wpwautoposter'), "<b>", "</b>"
	), 'tab' => 'wpw_twitter') );

//add label to show preview, do not change the order for tweet template and tweet preview field
$poster_meta->addTweetPreview( $prefix . 'tw_template', array('default' => $defaulttemplate, 'validate_func' => 'escape_html', 'name' => esc_html__('Preview : ', 'wpwautoposter'), 'tab' => 'wpw_twitter') );