<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

global $pagenow;

/**
 * Tab argument
 */
$wpmetatab = array(
	'class' => 'wpw_wordpress', //unique class name of each tabs
	'title' => esc_html__('WordPress', 'wpwautoposter'), //  title of tab
	'active' => $defaulttabon //it will by default make tab active on page load
);

//when WordPress is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs($wpmetatab);

//Check Post id
$post_id = !empty( $_GET['post'] ) ? stripslashes_deep( $_GET['post'] ) : '';

$post_type = !empty( $_GET['post_type'] ) ? esc_attr($_GET['post_type']) : get_post_type( $post_id );
if( empty($post_type) && $pagenow == 'post-new.php' ) {
	$post_type = 'post';
}

// Get stored li app grant data
$wpAllSites = get_option( 'wpw_auto_poster_wordpress_sites', array() );
$mapSites = get_option( 'wpw_auto_poster_wordpress_mapped_posttypes', array() );
$mapSites = isset($mapSites[$post_type]) ? $mapSites[$post_type] : array();

if( empty($wpAllSites) ) {
	$poster_meta->addGrantPermission( $prefix . 'wp_warning', array('desc' => esc_html__('Enter your WordPress websites within the Settings Page, otherwise posting to WordPress won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_wordpress') );
}

//add label to show status
$poster_meta->addTweetStatus( $prefix . 'wp_status', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of WordPress website post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_wordpress') );

$post_status = get_post_meta( $post_id, $prefix . 'wp_status', true );
$post_label = esc_html__( 'Publish Post On WordPress : ', 'wpwautoposter' );
$post_desc = esc_html__( 'Publish this Post to your WordPress.', 'wpwautoposter' );

if( $post_status == 1 && empty($schedule_option) ) {
	$post_label = esc_html__( 'Re-publish Post On WordPress : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-publish this Post to your WordPress.', 'wpwautoposter' );

} elseif( ($post_status == 2) || ($post_status == 1 && !empty($schedule_option)) ) {
	$post_label = esc_html__( 'Re-schedule Post On WordPress : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Re-schedule this Post to your WordPress.', 'wpwautoposter' );

} elseif( empty($post_status) && !empty($schedule_option) ) {
	$post_label = esc_html__( 'Schedule Post On WordPress : ', 'wpwautoposter' );
	$post_desc = esc_html__( 'Schedule this Post to your WordPress.', 'wpwautoposter' );
}

$post_desc .= '<br>' . sprintf( esc_html__('If you have enabled %sEnable auto posting to WordPress%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to WordPress.', 'wpwautoposter'), '<strong>', '</strong>' );

$post_desc .= '<br><p class="wpw-auto-poster-meta wpw-auto-poster-meta_second"><strong>' . esc_html__( 'Note:', 'wpwautoposter ') . '</strong> ' . sprintf( esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter' ), '<strong>', '</strong>') . '</p>';

//post to linkedin
$poster_meta->addPublishBox( $prefix . 'post_to_wordpress', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_wordpress') );

//Immediate post to LinkedIn
if( !empty($schedule_option) ) {
	$poster_meta->addPublishBox( $prefix . 'immediate_post_to_wordpress', array('name' => esc_html__('Immediate Posting On WordPress:', 'wpwautoposter'), 'desc' => 'Immediately publish this post to WordPress.', 'tab' => 'wpw_wordpress') );
}

$wpSites = array();
if( !empty($mapSites) ) {
	foreach( $mapSites as $site ) {

		$siteArr = explode(':', $site);
		$siteKey = isset($siteArr[0]) ? $siteArr[0] : '';
		$siteType = isset($siteArr[1]) ? $siteArr[1] : '';

		$siteName = isset($wpAllSites[$siteKey]['name']) ? $wpAllSites[$siteKey]['name'] : '';

		$siteName .= ' - ' . $siteType;
		$wpSites[$site] = stripslashes($siteName);
	}
}

//post to this account
$poster_meta->addSelect( $prefix . 'wp_post_sites', $wpSites, array('name' => esc_html__('Post To This WordPress Website', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select websites to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_wordpress') );

//publish status to wordpress image
$poster_meta->addText( $prefix . 'wp_post_title', array('name' => esc_html__('Post Title : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter custom post title which will be used for the WordPress post. Leave it empty to use from settings page. if there is also blank, then it will take default post title from here.', 'wpwautoposter'), 'tab' => 'wpw_wordpress', 'show_path' => true) );

//publish status to wordpress image
$poster_meta->addImage( $prefix . 'wp_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the WordPress post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_wordpress', 'show_path' => true) );

//comment to wordpress
$poster_meta->addTextarea( $prefix . 'wp_post_content', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Content : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can customize the content which will be used by WordPress for the post. You can use following template tags within the status text:', 'wpwautoposter') .
	'<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
	'<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
	'<br /><b><code>{title}</code></b> - ' . esc_html__('displays the post title.', 'wpwautoposter') .
	'<br /><b><code>{post_content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
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
	), 'tab' => 'wpw_wordpress') );