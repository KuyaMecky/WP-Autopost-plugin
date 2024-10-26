<?php
/**
 * Plugin Setup Functions
 *
 * @package Social Auto Poster
 * @since 3.8.2
 */


/**
 * Manage plugin default settings on
 * plugin install
 *
 * @package Social Auto Poster
 * @since 3.8.2
 */
function wpw_auto_manage_plugin_install_settings() {

	//get plugin options from database
	$wpw_auto_poster_options = get_option('wpw_auto_poster_options');
	$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check auto poster options is empty or not
	if( empty($wpw_auto_poster_options) ) {
		//set default settings of social auto poster
		wpw_auto_posting_default_settings();
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0');
		update_option('wpw_auto_poster_gmb_notice_dismissed', '1');
	}

	$wpw_auto_poster_options = get_option('wpw_auto_poster_options');
	$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

	$wpw_auto_poster_options = (empty($wpw_auto_poster_options)) ? array() : $wpw_auto_poster_options;
	$wpw_auto_poster_reposter_options = (empty($wpw_auto_poster_reposter_options)) ? array() : $wpw_auto_poster_reposter_options;
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0
	if( $wpw_auto_poster_set_option == '1.0' ) {
		$udpopt = false;
		if (!isset($wpw_auto_poster_options['enable_logs'])) { //check enable logs is not set
			$enable_logs = array('enable_logs' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_logs);
			$udpopt = true;
		}
		//check url shortener facebook
		if( !isset($wpw_auto_poster_options['fb_url_shortener']) ) {
			$fb_url_shortener = array('fb_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_url_shortener);
			$udpopt = true;
		}        //check url facebook bitly user name
		if( !isset($wpw_auto_poster_options['fb_bitly_username']) ) {
			$fb_bitly_username = array('fb_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_bitly_username);
			$udpopt = true;
		}
		//check url facebook bitly api key
		if( !isset($wpw_auto_poster_options['fb_bitly_api_key']) ) {
			$fb_bitly_api_key = array('fb_bitly_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_bitly_api_key);
			$udpopt = true;
		}
		//check url shortener twitter
		if( !isset($wpw_auto_poster_options['tw_url_shortener']) ) {
			$tw_url_shortener = array('tw_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_url_shortener);
			$udpopt = true;
		}        //check url twitter bitly user name
		if( !isset($wpw_auto_poster_options['tw_bitly_username']) ) {
			$tw_bitly_username = array('tw_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_bitly_username);
			$udpopt = true;
		}        //check url twitter bitly api key
		if( !isset($wpw_auto_poster_options['tw_bitly_api_key']) ) {
			$tw_bitly_api_key = array('tw_bitly_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_bitly_api_key);
			$udpopt = true;
		}        //check url shortener linkedin
		if( !isset($wpw_auto_poster_options['li_url_shortener']) ) {
			$li_url_shortener = array('li_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_url_shortener);
			$udpopt = true;
		}
		//check url linkedin bitly user name
		if( !isset($wpw_auto_poster_options['li_bitly_username']) ) {
			$li_bitly_username = array('li_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_bitly_username);
			$udpopt = true;
		}
		//check url linkedin bitly api key
		if( !isset($wpw_auto_poster_options['li_bitly_api_key']) ) {
			$li_bitly_api_key = array('li_bitly_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_bitly_api_key);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.1');
	} //check plugin set option value is 1.0

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.1
	if( $wpw_auto_poster_set_option == '1.0.1' ) {

		$udpopt = false;

		//Tumblr settings
		if( !isset($wpw_auto_poster_options['enable_tumblr']) ) { //check enable tumblr is not set
			$enable_tumblr = array('enable_tumblr' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_tumblr);
			$udpopt = true;
		}
		//check enable tumblr for is not set
		if( !isset($wpw_auto_poster_options['enable_tumblr_for']) ) {
			$enable_tumblr_for = array('enable_tumblr_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_tumblr_for);
			$udpopt = true;
		}
		//check content type of tumblr is not set
		if( !isset($wpw_auto_poster_options['tumblr_content_type']) ) {
			$tumblr_content_type = array('tumblr_content_type' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tumblr_content_type);
			$udpopt = true;
		}
		//check url shortener tumblr
		if( !isset($wpw_auto_poster_options['tb_url_shortener']) ) {
			$tb_url_shortener = array('tb_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_url_shortener);
			$udpopt = true;
		}
		//check url tumblr bitly user name
		if( !isset($wpw_auto_poster_options['tb_bitly_username']) ) {
			$tb_bitly_username = array('tb_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_bitly_username);
			$udpopt = true;
		}
		//check url tumblr bitly api key
		if( !isset($wpw_auto_poster_options['tb_bitly_api_key']) ) {
			$tb_bitly_api_key = array('tb_bitly_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_bitly_api_key);
			$udpopt = true;
		}
		//check consumer key is not set
		if( !isset($wpw_auto_poster_options['tumblr_consumer_key']) ) {
			$tumblr_consumer_key = array('tumblr_consumer_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tumblr_consumer_key);
			$udpopt = true;
		}
		//check consumer secret is not set
		if( !isset($wpw_auto_poster_options['tumblr_consumer_secret']) ) {
			$tumblr_consumer_secret = array('tumblr_consumer_secret' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tumblr_consumer_secret);
			$udpopt = true;
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.2');
	} //check plugin set option value is 1.0.1

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.2
	if( $wpw_auto_poster_set_option == '1.0.2' ) {
		$udpopt = false;
		if( !isset($wpw_auto_poster_options['enable_posting_logs']) ) { //check enable posting logs is not set
			$enable_posting_logs = array('enable_posting_logs' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_posting_logs);
			$udpopt = true;
		}
		if( isset($wpw_auto_poster_options['twitter_consumer_key']) && isset($wpw_auto_poster_options['twitter_consumer_secret']) && isset($wpw_auto_poster_options['twitter_oauth_token']) && isset($wpw_auto_poster_options['twitter_oauth_secret']) ) { //check twitter consumer key is set

			//Twitter Posting Class
			require_once( WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-tw-posting.php' ); // twitter posting class
			$wpw_auto_poster_tw_posting = new Wpw_Auto_Poster_TW_Posting();
			$twitter_keys_data = array(
				'consumer_key' => $wpw_auto_poster_options['twitter_consumer_key'],
				'consumer_secret' => $wpw_auto_poster_options['twitter_consumer_secret'],
				'oauth_token' => $wpw_auto_poster_options['twitter_oauth_token'],
				'oauth_secret' => $wpw_auto_poster_options['twitter_oauth_secret'],
			);
			$twitter_keys = array('twitter_keys' => array($twitter_keys_data));
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $twitter_keys);
			$tw_account_details = array();
			$user_profile_data = $wpw_auto_poster_tw_posting->wpw_auto_poster_get_user_data($twitter_keys_data['consumer_key'], $twitter_keys_data['consumer_secret'], $twitter_keys_data['oauth_token'], $twitter_keys_data['oauth_secret']);
			if (!empty($user_profile_data)) { // Check user data are not empty
				if (isset($user_profile_data->name) && !empty($user_profile_data->name)) { // Check user name is not empty
					$tw_account_details['1'] = $user_profile_data->name;
					$types = get_post_types(array('public' => true), 'objects');
					$types = is_array($types) ? $types : array();
					foreach ($types as $type) {
						if (!is_object($type))
							continue;
						$tw_type_user = array('tw_type_' . $type->name . '_user' => array('1'));
						$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_type_user);
					}
				}
			}
			//Update twitter acoount details
			update_option('wpw_auto_poster_tw_account_details', $tw_account_details);
			$udpopt = true;
		}
		if ($udpopt == true) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.3');
	} //check plugin set option value is 1.0.2

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.3
	if( $wpw_auto_poster_set_option == '1.0.3' ) {
		$udpopt = false;
		if( !isset($wpw_auto_poster_options['schedule_wallpost_option']) ) { //check Schedule WallPost is set or not
			$schedule_wallpost_option = array('schedule_wallpost_option' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_option);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['schedule_wallpost_time']) ) { //check Schedule Time is set or not
			$schedule_wallpost_time = array('schedule_wallpost_time' => '0');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_time);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.4');
	} //check plugin set option value is 1.0.3

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.4
	if( $wpw_auto_poster_set_option == '1.0.4' ) {
		$udpopt = false;
		if( !isset($wpw_auto_poster_options['schedule_wallpost_minute']) ) { //check Schedule Time is set or not
			$schedule_wallpost_minute = array('schedule_wallpost_minute' => '0');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_minute);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.5');
	} //check plugin set option value is 1.0.4

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.5
	if( $wpw_auto_poster_set_option == '1.0.5' ) {
		$udpopt = false;
		//check twitter image is set or not
		if( !isset($wpw_auto_poster_options['tw_tweet_img']) ) {
			$tw_tweet_img = array('tw_tweet_img' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_tweet_img);
			$udpopt = true;
		}
		if( $udpopt == true) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.6');
	} //check plugin set option value is 1.0.5

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.6
	if( $wpw_auto_poster_set_option == '1.0.6' ) {
		$udpopt = false;
		//check Facebook bitly access token is set or not
		if( !isset($wpw_auto_poster_options['fb_bitly_access_token']) ) {
			$fb_bitly_access_token = array('fb_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_bitly_access_token);
			$udpopt = true;
		}
		//check Twitter bitly access token is set or not
		if( !isset($wpw_auto_poster_options['tw_bitly_access_token']) ) {
			$tw_bitly_access_token = array('tw_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_bitly_access_token);
			$udpopt = true;
		}
		//check LinkedIn bitly access token is set or not
		if( !isset($wpw_auto_poster_options['li_bitly_access_token']) ) {
			$li_bitly_access_token = array('li_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_bitly_access_token);
			$udpopt = true;
		}
		//check Tumblr bitly access token is set or not
		if( !isset($wpw_auto_poster_options['tb_bitly_access_token']) ) {
			$tb_bitly_access_token = array('tb_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_bitly_access_token);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.7');
	} //check plugin set option value is 1.0.6

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.7
	if( $wpw_auto_poster_set_option == '1.0.7' ) {
		$udpopt = false;
		//check Facebook shortest api token is set or not
		if( !isset($wpw_auto_poster_options['fb_shortest_api_token']) ) {
			$fb_shortest_api_token = array('fb_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_shortest_api_token);
			$udpopt = true;
		}
		//check Twitter shortest api token is set or not
		if( !isset($wpw_auto_poster_options['tw_shortest_api_token']) ) {
			$tw_shortest_api_token = array('tw_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_shortest_api_token);
			$udpopt = true;
		}
		//check LinkedIn shortest api token is set or not
		if( !isset($wpw_auto_poster_options['li_shortest_api_token']) ) {
			$li_shortest_api_token = array('li_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_shortest_api_token);
			$udpopt = true;
		}
		//check Tumblr shortest api token is set or not
		if( !isset($wpw_auto_poster_options['tb_shortest_api_token']) ) {
			$tb_shortest_api_token = array('tb_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_shortest_api_token);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		//update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.8');
	} //check plugin set option value is 1.0.7

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.0.8
	if( $wpw_auto_poster_set_option == '1.0.8' ) {
		$udpopt = false;
		// check daily posts limit is set or not
		if( !isset($wpw_auto_poster_options['enable_random_posting']) ) {
			$enable_random_posting = array('enable_random_posting' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_random_posting);
			$udpopt = true;
		}
		// check daily posts limit is set or not
		if( !isset($wpw_auto_poster_options['daily_posts_limit']) ) {
			$daily_posts_limit = array('daily_posts_limit' => 10);
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $daily_posts_limit);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.0.9');
	} //check plugin set option value is 1.0.8

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	// Check set option for plugin is set 1.0.9
	if( $wpw_auto_poster_set_option == '1.0.9' ) {

		$udpopt = false;
		// Saving facebook data for multiple account for new version
		if( isset($wpw_auto_poster_options['fb_app_id']) && isset($wpw_auto_poster_options['fb_app_secret']) ) { // Check facebook app id and app secret is set
			// Updating App key and App secret storage
			$facebook_keys_data = array(
				'app_id' => $wpw_auto_poster_options['fb_app_id'],
				'app_secret' => $wpw_auto_poster_options['fb_app_secret'],
			);
			$facebook_keys = array('facebook_keys' => array($facebook_keys_data));
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $facebook_keys);

			// Updating old fb session data to new method
			$wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data');
			if (!empty($wpw_auto_poster_fb_sess_data) && empty($wpw_auto_poster_fb_sess_data[$wpw_auto_poster_options['fb_app_id']])) {
				$new_fb_sess_data[$wpw_auto_poster_options['fb_app_id']] = $wpw_auto_poster_fb_sess_data;
				update_option('wpw_auto_poster_fb_sess_data', $new_fb_sess_data);
			}

			// Updating facebook post to accounts
			// Getting all post types
			$types = get_post_types(array('public' => true), 'objects');
			$types = is_array($types) ? $types : array();

			// Loop of post types
			foreach( $types as $type ) {
				if( !is_object($type) ) continue;

				// Skip media
				if( isset($type->labels) ) {
					$label = $type->labels->name ? $type->labels->name : $type->name;
				} else {
					$label = $type->name;
				}

				if( $label == 'Media' || $label == 'media' ) continue;

				if( isset($wpw_auto_poster_options['fb_type_' . $type->name . '_user']) ) {
					foreach( $wpw_auto_poster_options['fb_type_' . $type->name . '_user'] as $fb_type_key => $fb_type_data ) {
						if( strpos($fb_type_data, '|') === false ) {
							$wpw_auto_poster_options['fb_type_' . $type->name . '_user'][$fb_type_key] = $fb_type_data . '|' . $wpw_auto_poster_options['fb_app_id'];
						}
					}
				}
			}
			$udpopt = true;
		}

		if( $udpopt == true ) { // Check if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		// Update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.0');
	} // Check plugin set option value is 1.0.9

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	if( $wpw_auto_poster_set_option == '1.1.0' ) {

		$udpopt = false;

		//check Facebook google API key is set or not
		if( !isset($wpw_auto_poster_options['fb_google_short_api_key']) ) {
			$fb_google_short_api_key = array('fb_google_short_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_google_short_api_key);
			$udpopt = true;
		}
		//check Twitter google API key is set or not
		if( !isset($wpw_auto_poster_options['tw_google_short_api_key']) ) {
			$tw_google_short_api_key = array('tw_google_short_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_google_short_api_key);
			$udpopt = true;
		}
		//check LinkedIn google API key is set or not
		if( !isset($wpw_auto_poster_options['li_google_short_api_key']) ) {
			$li_google_short_api_key = array('li_google_short_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_google_short_api_key);
			$udpopt = true;
		}
		//check Tumblr google API key is set or not
		if( !isset($wpw_auto_poster_options['tb_google_short_api_key']) ) {
			$tb_google_short_api_key = array('tb_google_short_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_google_short_api_key);
			$udpopt = true;
		}
		if( $udpopt == true ) { // Check if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// Update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.1');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//Change Log file Dir and create directory on activation
	wpw_auto_poster_create_files();

	//check set option for plugin is set 1.1.0
	if( $wpw_auto_poster_set_option == '1.1.1' ) {
		$udpopt = false;
		if( !isset($wpw_auto_poster_options['fb_app_version']) ) {
			$fb_app_version = array('fb_app_version' => '209');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_app_version);
			$udpopt = true;
		}
		if( $udpopt == true ) { // Check if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// Update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.2');
	} // Check plugin set option value is 1.1.1

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.2
	if( $wpw_auto_poster_set_option == '1.1.2' ) {
		$udpopt = false;
		// check daily posts limit is set or not
		if( !isset($wpw_auto_poster_options['schedule_wallpost_order']) ) {
			$order_by = array('schedule_wallpost_order' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $order_by);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.3');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.3
	if( $wpw_auto_poster_set_option == '1.1.3' ) {
		$udpopt = false;
		// check daily posts limit is set or not
		if( !isset($wpw_auto_poster_options['fb_wp_pretty_url']) ) {
			$wp_pretty_url = array(
				'fb_wp_pretty_url' => '',
				'tw_wp_pretty_url' => '',
				'li_wp_pretty_url' => '',
				'tb_wp_pretty_url' => ''
			);
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_pretty_url);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.4');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.4
	if( $wpw_auto_poster_set_option == '1.1.4' ) {
		$udpopt = false;
		// check is custom schdeule time for minute is set or not
		if( !isset($wpw_auto_poster_options['schedule_wallpost_custom_minute']) ) {
			$schedule_wallpost_custom_minute = array('schedule_wallpost_custom_minute' => WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE);
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_custom_minute);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.5');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	// major updates new options added and registered since 2.6.0
	//check set option for plugin is set 1.1.4
	if( $wpw_auto_poster_set_option == '1.1.5' ) {
		$udpopt = false;
		// check is custom schdeule time for minute is set or not
		if( !isset($wpw_auto_poster_options['schedule_wallpost_twice_time1']) ) {
			$schedule_wallpost_twicedaily_settings = array('schedule_wallpost_twice_time1' => '0', 'schedule_wallpost_twice_time2' => '12', 'enable_twice_random_posting' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_twicedaily_settings);
			$udpopt = true;
		}
		//check whether facebook fb_global_message_template exist or not
		if( !isset($wpw_auto_poster_options['fb_global_message_template']) ) {
			$fb_global_message_template = array('fb_global_message_template' => '{title} - {link}');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_global_message_template);
			$udpopt = true;
		}
		/* * * Pinterest Support Options Start ** */
		//check whether pinterest is enabled
		if( !isset($wpw_auto_poster_options['enable_pinterest']) ) {
			$enable_pinterest = array('enable_pinterest' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_pinterest);
			$udpopt = true;
		}
		// check whether pinterest is enabled for post types
		if( !isset($wpw_auto_poster_options['enable_pinterest_for']) ) {
			$enable_pinterest_for = array('enable_pinterest_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_pinterest_for);
			$udpopt = true;
		}
		//check url shortener pinterest
		if( !isset($wpw_auto_poster_options['pin_url_shortener']) ) {
			$pin_url_shortener = array('pin_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_url_shortener);
			$udpopt = true;
		}
		//check pinterest shortest api token is set or not
		if( !isset($wpw_auto_poster_options['pin_shortest_api_token']) ) {
			$pin_shortest_api_token = array('pin_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_shortest_api_token);
			$udpopt = true;
		}
		//check pinterest bitly access token is set or not
		if( !isset($wpw_auto_poster_options['pin_bitly_access_token']) ) {
			$pin_bitly_access_token = array('pin_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_bitly_access_token);
			$udpopt = true;
		}
		//check pinterest google short api key is set or not
		if( !isset($wpw_auto_poster_options['pin_google_short_api_key']) ) {
			$pin_google_short_api_key = array('pin_google_short_api_key' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_google_short_api_key);
			$udpopt = true;
		}
		// check whether pinterest account is configured
		if( !isset($wpw_auto_poster_options['pinterest_keys']) ) {
			$pinterest_keys = array('pinterest_keys' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pinterest_keys);
			$udpopt = true;
		}
		// check pinterest pretty url is set or not
		if( !isset($wpw_auto_poster_options['pin_wp_pretty_url']) ) {
			$pin_wp_pretty_url = array('pin_wp_pretty_url' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_wp_pretty_url);
			$udpopt = true;
		}
		// check whether to show pinterest metabox in post page
		if( !isset($wpw_auto_poster_options['prevent_post_pin_metabox']) ) {
			$prevent_post_pin_metabox = array('prevent_post_pin_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_pin_metabox);
			$udpopt = true;
		}
		// check whether to show pinterest post image is set
		if( !isset($wpw_auto_poster_options['pin_custom_img']) ) {
			$pin_custom_img = array('pin_custom_img' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_custom_img);
			$udpopt = true;
		}
		// check whether to show pinterest post image is set
		if( !isset($wpw_auto_poster_options['pin_custom_template']) ) {
			$pin_custom_template = array('pin_custom_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_custom_template);
			$udpopt = true;
		}
		/* * * Pinterest Support Options End ** */
		// New options for category and tags taxomy selection for each social networks
		if( !isset($wpw_auto_poster_options['fb_post_type_tags']) ) {
			$fb_post_type_tags = array('fb_post_type_tags' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_post_type_tags);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['fb_post_type_cats']) ) {
			$fb_post_type_cats = array('fb_post_type_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tw_post_type_tags']) ) {
			$tw_post_type_tags = array('tw_post_type_tags' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_post_type_tags);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tw_post_type_cats']) ) {
			$tw_post_type_cats = array('tw_post_type_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['li_post_type_tags']) ) {
			$li_post_type_tags = array('li_post_type_tags' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_post_type_tags);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['li_post_type_cats']) ) {
			$li_post_type_cats = array('li_post_type_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tb_post_type_tags']) ) {
			$tb_post_type_tags = array('tb_post_type_tags' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_post_type_tags);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tb_post_type_cats']) ) {
			$tb_post_type_cats = array('tb_post_type_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['pin_post_type_tags']) ) {
			$pin_post_type_tags = array('pin_post_type_tags' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_post_type_tags);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['pin_post_type_cats']) ) {
			$pin_post_type_cats = array('pin_post_type_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_post_type_cats);
			$udpopt = true;
		}
		// code end for category and tags selection
		/* * * New options for exclude category selection for each social networks start ** */
		if( !isset($wpw_auto_poster_options['fb_exclude_cats']) ) {
			$fb_exclude_cats = array('fb_exclude_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_exclude_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tw_exclude_cats']) ) {
			$tw_exclude_cats = array('tw_exclude_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tw_exclude_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['li_exclude_cats']) ) {
			$li_exclude_cats = array('li_exclude_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_exclude_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['tb_exclude_cats']) ) {
			$tb_exclude_cats = array('tb_exclude_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_exclude_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_options['pin_exclude_cats']) ) {
			$pin_exclude_cats = array('pin_exclude_cats' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pin_exclude_cats);
			$udpopt = true;
		}
		// check for google tracking options
		if( !isset($wpw_auto_poster_options['enable_google_tracking']) ) {
			$google_tracking = array('enable_google_tracking' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $google_tracking);
			$udpopt = true;
		}
		// check for Enable Excerpt Content options
		if( !isset($wpw_auto_poster_options['enable_excerpt_content']) ) {
			$enable_excerpt_content = array('enable_excerpt_content' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_excerpt_content);
			$udpopt = true;
		}
		/* * * New options for exclude category selection for each social networks end ** */
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.6');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.6
	if ($wpw_auto_poster_set_option == '1.1.6') {
		$udpopt = false;
		// check is google tracking code script option is exist or not
		if (!isset($wpw_auto_poster_options['google_tracking_script'])) {
			$google_tracking_script = array('google_tracking_script' => 'yes', 'google_tracking_code' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $google_tracking_script);
			$udpopt = true;
		}
		if ($udpopt == true) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.7');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.7
	if( $wpw_auto_poster_set_option == '1.1.7' ) {
		$udpopt = false;
		// check is google tracking code script option is exist or not
		if( !isset($wpw_auto_poster_options['schedule_wallpost_order_behaviour']) ) {
			$wallpost_order_behaviour = array('schedule_wallpost_order_behaviour' => 'DESC');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wallpost_order_behaviour);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.8');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.8
	if( $wpw_auto_poster_set_option == '1.1.8' ) {
		if (empty($wpw_auto_poster_reposter_options)) {
			wpw_auto_posting_reposter_default_settings(); // update default settings for reposter options
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.9');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.9
	if( $wpw_auto_poster_set_option == '1.1.9') {
		$udpopt = false;
		// check is google tracking code script option is exist or not
		if( !isset($wpw_auto_poster_options['li_global_message_template']) ) {
			$li_global_message_template = array('li_global_message_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.10');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.10
	if( $wpw_auto_poster_set_option == '1.1.10' ) {
		$udpopt = false;
		// check is google tracking code script option is exist or not
		if( !isset($wpw_auto_poster_options['fb_post_share_type']) || empty($wpw_auto_poster_options['fb_post_share_type']) ) {
			$fb_post_share_type = array('fb_post_share_type' => 'link_posting');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_post_share_type);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.11');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.11
	if( $wpw_auto_poster_set_option == '1.1.11' ) {
		$udpopt = false;
		if( !isset($wpw_auto_poster_reposter_options['fb_post_ids_exclude']) ) {
			$post_ids_exclude = array(
				'fb_post_ids_exclude' => '',
				'li_post_ids_exclude' => '',
				'pin_post_ids_exclude' => '',
				'tb_post_ids_exclude' => '',
				'tw_post_ids_exclude' => '',
			);
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $post_ids_exclude);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.12');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.12
	if( $wpw_auto_poster_set_option == '1.1.12' ) {
		$udpopt = false;
		// remove code for set rest method as default method for fb
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.13');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.13
	if( $wpw_auto_poster_set_option == '1.1.13' ) {
		$udpopt = false;
		//check whether tumblr tb_global_message_template exist or not
		if( !isset($wpw_auto_poster_options['tb_global_message_template']) ) {
			$tb_global_message_template = array('tb_global_message_template' => '{title} - {link}');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tb_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.14');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.14
	if( $wpw_auto_poster_set_option == '1.1.14' ) {
		$udpopt = false;
		//check whether facebook facebook_rest_type exist or not
		if( !isset($wpw_auto_poster_options['facebook_rest_type']) ) {
			$facebook_rest_type = array('facebook_rest_type' => 'android');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $facebook_rest_type);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.15');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.14
	if( $wpw_auto_poster_set_option == '1.1.15' ) {
		$udpopt = false;
		//check whether enable_posting_for_newpost exist or not
		if( !isset($wpw_auto_poster_options['enable_posting_for_newpost']) ) {
			$posting_for_newpost = array('enable_posting_for_newpost' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $posting_for_newpost);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.16');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.16
	if( $wpw_auto_poster_set_option == '1.1.16' ) {
		$udpopt = false;
		$social_types = array('fb', 'tw', 'li', 'tb', 'ba', 'pin');
		foreach ($social_types as $key => $value) {
			//check url shortener is google then resave it to default
			if (!empty($wpw_auto_poster_options[$value . '_url_shortener']) && $wpw_auto_poster_options[$value . '_url_shortener'] == 'google_shortner') {
				$wpw_auto_poster_options[$value . '_url_shortener'] = 'wordpress';
				if (isset($wpw_auto_poster_options[$value . 'google_short_api_key'])) {
					unset($wpw_auto_poster_options[$value . 'google_short_api_key']);
				}
				$udpopt = true;
			}
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.17');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.17
	if( $wpw_auto_poster_set_option == '1.1.17' ) {
		$udpopt = false;
		//check whether facebook fb_proxy exist or not
		if( !isset($wpw_auto_poster_options['fb_proxy']) ) {
			$fb_proxy = array('fb_proxy' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $fb_proxy);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.18');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.18
	if( $wpw_auto_poster_set_option == '1.1.18' ) {
		$udpopt = false;
		//check whether linkedin li_company exist or not
		if( !isset($wpw_auto_poster_options['li_company']) ) {
			$li_company = array('li_company' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $li_company);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.19');
	}

	//get option for when plugin is activating first time
	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.19
	if( $wpw_auto_poster_set_option == '1.1.19' ) {
		$udpopt = false;
		//check Youtube is enable or not
		if( !isset($wpw_auto_poster_options['enable_youtube']) ) {
			$enable_youtube = array('enable_youtube' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_youtube);
			$udpopt = true;
		}
		//check Youtube for posts or page
		if( !isset($wpw_auto_poster_options['enable_youtube_for']) ) {
			$enable_youtube_for = array('enable_youtube_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_youtube_for);
			$udpopt = true;
		}
		//check Youtube for posts type tag
		if( !isset($wpw_auto_poster_options['yt_post_type_tags']) ) {
			$yt_post_type_tags = array('yt_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_post_type_tags);
			$udpopt = true;
		}
		//check Youtube for posts type category
		if( !isset($wpw_auto_poster_options['yt_post_type_cats']) ) {
			$yt_post_type_cats = array('yt_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_post_type_cats);
			$udpopt = true;
		}
		//check URL Youtube shortner
		if( !isset($wpw_auto_poster_options['yt_url_shortener']) ) {
			$yt_url_shortener = array('yt_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_url_shortener);
			$udpopt = true;
		}
		//check Youtube URl shortner bitly username
		if( !isset($wpw_auto_poster_options['yt_bitly_username']) ) {
			$yt_bitly_username = array('yt_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_bitly_username);
			$udpopt = true;
		}
		//Check bitly access token
		if( !isset($wpw_auto_poster_options['yt_bitly_access_token']) ) {
			$yt_bitly_access_token = array('yt_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_bitly_access_token);
			$udpopt = true;
		}
		//Check youtube shorttest api token
		if( !isset($wpw_auto_poster_options['yt_shortest_api_token']) ) {
			$yt_shortest_api_token = array('yt_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_shortest_api_token);
			$udpopt = true;
		}
		//Check youtube access token and secret array
		if( !isset($wpw_auto_poster_options['yt_keys']) ) {
			$yt_keys = array('yt_keys' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_keys);
			$udpopt = true;
		}
		//Check youtube prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_yt_metabox']) ) {
			$prevent_post_yt_metabox = array('prevent_post_yt_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_yt_metabox);
			$udpopt = true;
		}
		//Check setting for youtube custom msg option
		if( !isset($wpw_auto_poster_options['yt_custom_msg_options']) ) {
			$yt_custom_msg_options = array('yt_custom_msg_options' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_custom_msg_options);
			$udpopt = true;
		}
		//Check youtube custom video exists
		if( !isset($wpw_auto_poster_options['yt_custom_img']) ) {
			$yt_custom_img = array('yt_custom_img' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_custom_img);
			$udpopt = true;
		}
		//Check youtube custom template options
		if( !isset($wpw_auto_poster_options['yt_template']) ) {
			$yt_template = array('yt_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $yt_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		// update plugin version to option
		update_option('wpw_auto_poster_set_option', '1.1.20');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.20
	if( $wpw_auto_poster_set_option == '1.1.20' ) {
		$udpopt = false;
		if( !empty($wpw_auto_poster_options) && $wpw_auto_poster_options['facebook_auth_options'] == 'rest' ) { // comability code to set app method as default method if using rest method
			$facebook_app_method = array('facebook_auth_options' => 'appmethod');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $facebook_app_method);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_fb_sess_data', array()); // rest mobile api method data
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.21');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.21
	if( $wpw_auto_poster_set_option == '1.1.21' ) {

		$udpopt = false;
		$g_udpopt = false;

		$wpw_auto_poster_options = get_option('wpw_auto_poster_options');
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

		if( isset($wpw_auto_poster_options['schedule_wallpost_custom_minute']) && $wpw_auto_poster_options['schedule_wallpost_custom_minute'] < 30 ) {
			$schedule_wallpost_custom_minute = array('schedule_wallpost_custom_minute' => WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE);
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $schedule_wallpost_custom_minute);
			$udpopt = true;
		}
		if( isset($wpw_auto_poster_options['daily_posts_limit']) && $wpw_auto_poster_options['daily_posts_limit'] > 10 ) {
			$wpw_auto_poster_options['daily_posts_limit'] = WPW_AUTO_POSTER_POST_LIMIT;
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['schedule_wallpost_option']) ) { 
			if (( $wpw_auto_poster_reposter_options['schedule_wallpost_option']['days'] == '' || $wpw_auto_poster_reposter_options['schedule_wallpost_option']['days'] == 0 ) && ( $wpw_auto_poster_reposter_options['schedule_wallpost_option']['hours'] == '' || $wpw_auto_poster_reposter_options['schedule_wallpost_option']['hours'] == 0 ) && $wpw_auto_poster_reposter_options['schedule_wallpost_option']['minutes'] < 30) { // check if minutes set less than 30 for posting then set it to 30
				$wpw_auto_poster_reposter_options['schedule_wallpost_option']['days'] = 0;
				$wpw_auto_poster_reposter_options['schedule_wallpost_option']['hours'] = 0;
				$wpw_auto_poster_reposter_options['schedule_wallpost_option']['minutes'] = WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE;
				$g_udpopt = true;
			}
		}

		//Model Class
		require_once( WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-model.php' );
		$wpw_auto_poster_model = new Wpw_Auto_Poster_Model();
		$social_accounts = $wpw_auto_poster_model->wpw_auto_poster_get_social_type_name();

		foreach( $social_accounts as $social_slug => $name ) {
			if( isset($wpw_auto_poster_reposter_options[$social_slug . '_posts_limit']) && $wpw_auto_poster_reposter_options[$social_slug . '_posts_limit'] > 10 ) {
				$wpw_auto_poster_reposter_options[$social_slug . '_posts_limit'] = WPW_AUTO_POSTER_POST_LIMIT;
				$g_udpopt = true;
			}
		}

		if( $udpopt == true ) {
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		if( $g_udpopt == true ) {
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.22');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.23
	if( $wpw_auto_poster_set_option == '1.1.22' ) {

		$udpopt = false;
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

		//add default value to minimum post age textbox
		if( !isset($wpw_auto_poster_reposter_options['minimum_post_age']) ) {
			$minimum_post_age = array('minimum_post_age' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $minimum_post_age);
			$udpopt = true;
		}
		//add default value to maximum post age textbox
		if( !isset($wpw_auto_poster_reposter_options['maximum_post_age']) ) {
			$maximum_post_age = array('maximum_post_age' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $maximum_post_age);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.23');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.25
	if( $wpw_auto_poster_set_option == '1.1.23' ) {
		$udpopt = false;
		if( !empty($wpw_auto_poster_options['prevent_linked_accounts_access']) ) {
			$wpw_auto_poster_options['prevent_linked_accounts_access'] = '';
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.24');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.24
	if( $wpw_auto_poster_set_option == '1.1.24' ) {
		$udpopt = false;
		//check wordpress is enable or not
		if( !isset($wpw_auto_poster_options['enable_wordpress']) ) {
			$enable_wordpress = array('enable_wordpress' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_wordpress);
			$udpopt = true;
		}
		//check wordpress for posts or page
		if( !isset($wpw_auto_poster_options['enable_wordpress_for']) ) {
			$enable_wordpress_for = array('enable_wordpress_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_wordpress_for);
			$udpopt = true;
		}
		//check wordpress for posts type tag
		if( !isset($wpw_auto_poster_options['wp_post_type_tags']) ) {
			$wp_post_type_tags = array('wp_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_post_type_tags);
			$udpopt = true;
		}
		//check wordpress for posts type category
		if( !isset($wpw_auto_poster_options['wp_post_type_cats']) ) {
			$wp_post_type_cats = array('wp_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_post_type_cats);
			$udpopt = true;
		}
		//check URL wordpress shortner
		if( !isset($wpw_auto_poster_options['wp_url_shortener']) ) {
			$wp_url_shortener = array('wp_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_url_shortener);
			$udpopt = true;
		}
		//check wordpress URl shortner bitly username
		if( !isset($wpw_auto_poster_options['wp_bitly_username']) ) {
			$wp_bitly_username = array('wp_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_bitly_username);
			$udpopt = true;
		}
		//Check wordpress bitly access token
		if( !isset($wpw_auto_poster_options['wp_bitly_access_token']) ) {
			$wp_bitly_access_token = array('wp_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_bitly_access_token);
			$udpopt = true;
		}
		//Check wordpress shorttest api token
		if( !isset($wpw_auto_poster_options['wp_shortest_api_token']) ) {
			$wp_shortest_api_token = array('wp_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_shortest_api_token);
			$udpopt = true;
		}
		//Check wordpress prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_wp_metabox']) ) {
			$prevent_post_wp_metabox = array('prevent_post_wp_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_wp_metabox);
			$udpopt = true;
		}
		//Check setting for wordpress custom content option
		if( !isset($wpw_auto_poster_options['wp_custom_msg_options']) ) {
			$wp_custom_msg_options = array('wp_custom_msg_options' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_custom_msg_options);
			$udpopt = true;
		}
		//Check wordpress custom image exists
		if( !isset($wpw_auto_poster_options['wp_post_image']) ) {
			$wp_post_image = array('wp_post_image' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_post_image);
			$udpopt = true;
		}
		//Check wordpress global image
		if( !isset($wpw_auto_poster_options['wp_global_title']) ) {
			$wp_global_title = array('wp_global_title' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_global_title);
			$udpopt = true;
		}
		//Check wordpress custom template options
		if( !isset($wpw_auto_poster_options['wp_global_message_template']) ) {
			$wp_global_message_template = array('wp_global_message_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $wp_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		// Same code for reposter settings
		$udpopt = false;
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

		if( !isset($wpw_auto_poster_reposter_options['enable_wordpress']) ) {
			$wp_enable = array('enable_wordpress' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_enable);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['enable_wordpress_for']) ) {
			$enable_wordpress_for = array('enable_wordpress_for' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $enable_wordpress_for);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_posting_cats']) ) {
			$wp_posting_cats = array('wp_posting_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_posting_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_post_type_cats']) ) {
			$wp_post_type_cats = array('wp_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_post_type_cats']) ) {
			$wp_post_type_cats = array('wp_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_post_ids_exclude']) ) {
			$wp_post_ids_exclude = array('wp_post_ids_exclude' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_post_ids_exclude);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_posts_limit']) ) {
			$wp_posts_limit = array('wp_posts_limit' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_posts_limit);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['repost_wp_custom_msg_options']) ) {
			$repost_wp_custom_msg_options = array('repost_wp_custom_msg_options' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $repost_wp_custom_msg_options);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_post_image']) ) {
			$wp_post_image = array('wp_post_image' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_post_image);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_global_title']) ) {
			$wp_global_title = array('wp_global_title' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_global_title);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['wp_global_message_template']) ) {
			$wp_global_message_template = array('wp_global_message_template' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $wp_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.25');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.25
	if( $wpw_auto_poster_set_option == '1.1.25' ) {

		$udpopt = false;
		$wpw_auto_poster_options = get_option('wpw_auto_poster_options');

		//check Google My Business is enable or not
		if( !isset($wpw_auto_poster_options['enable_googlemybusiness']) ) {
			$enable_googlemybusiness = array('enable_googlemybusiness' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_googlemybusiness);
			$udpopt = true;
		}
		//check Google My Business for posts or page
		if( !isset($wpw_auto_poster_options['enable_googlemybusiness_for']) ) {
			$enable_googlemybusiness_for = array('enable_googlemybusiness_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_googlemybusiness_for);
			$udpopt = true;
		}
		//check Google My Business for posts type tag
		if( !isset($wpw_auto_poster_options['gmb_post_type_tags']) ) {
			$gmb_post_type_tags = array('gmb_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_post_type_tags);
			$udpopt = true;
		}
		//check Google My Business for posts type category
		if( !isset($wpw_auto_poster_options['gmb_post_type_cats']) ) {
			$gmb_post_type_cats = array('gmb_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_post_type_cats);
			$udpopt = true;
		}
		//check URL Google My Business shortner
		if( !isset($wpw_auto_poster_options['gmb_url_shortener']) ) {
			$gmb_url_shortener = array('gmb_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_url_shortener);
			$udpopt = true;
		}
		//check Google My Business URl shortner bitly username
		if( !isset($wpw_auto_poster_options['gmb_bitly_username']) ) {
			$gmb_bitly_username = array('gmb_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_bitly_username);
			$udpopt = true;
		}
		//Check Google My Business bitly access token
		if( !isset($wpw_auto_poster_options['gmb_bitly_access_token']) ) {
			$gmb_bitly_access_token = array('gmb_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_bitly_access_token);
			$udpopt = true;
		}
		//Check google my business shorttest api token
		if( !isset($wpw_auto_poster_options['gmb_shortest_api_token']) ) {
			$gmb_shortest_api_token = array('gmb_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_shortest_api_token);
			$udpopt = true;
		}
		//Check google my business prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_gmb_metabox']) ) {
			$prevent_post_gmb_metabox = array('prevent_post_gmb_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_gmb_metabox);
			$udpopt = true;
		}
		//Check setting for google my business custom msg option
		if( !isset($wpw_auto_poster_options['gmb_custom_msg_options']) ) {
			$gmb_custom_msg_options = array('gmb_custom_msg_options' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_custom_msg_options);
			$udpopt = true;
		}
		//Check google my business custom image exists
		if( !isset($wpw_auto_poster_options['gmb_post_image']) ) {
			$gmb_post_image = array('gmb_post_image' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_post_image);
			$udpopt = true;
		}
		//Check google my business custom template options
		if( !isset($wpw_auto_poster_options['gmb_global_message_template']) ) {
			$gmb_global_message_template = array('gmb_global_message_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_global_message_template);
			$udpopt = true;
		}
		//Check GMB buttons
		if( !isset($wpw_auto_poster_options['gmb_add_buttons']) ) {
			$gmb_add_buttons = array('gmb_add_buttons' => 'LEARN_MORE');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $gmb_add_buttons);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_gmb_sess_data', array());
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.26');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.26
	if( $wpw_auto_poster_set_option == '1.1.26' ) {

		$udpopt = false;

		//Check pinterest auth method
		if( !isset($wpw_auto_poster_options['pinterest_auth_options']) ) {
			$pinterest_auth_options = array('pinterest_auth_options' => 'app');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $pinterest_auth_options);
			$udpopt = true;
		}

		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.27');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.27
	if( $wpw_auto_poster_set_option == '1.1.27' ) {

		$udpopt = false;
		$wpw_auto_poster_options = get_option('wpw_auto_poster_options');

		//check Reddit is enable or not
		if( !isset($wpw_auto_poster_options['enable_reddit']) ) {
			$enable_reddit = array('enable_reddit' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_reddit);
			$udpopt = true;
		}
		//check Reddit for posts or page
		if( !isset($wpw_auto_poster_options['enable_reddit_for']) ) {
			$enable_reddit_for = array('enable_reddit_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$enable_reddit_for);
			$udpopt = true;
		}
		//check Reddit for posts type tag
		if( !isset($wpw_auto_poster_options['reddit_post_type_tags']) ) {
			$reddit_post_type_tags = array('reddit_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_post_type_tags);
			$udpopt = true;
		}
		//check Reddit for posts type category
		if( !isset($wpw_auto_poster_options['reddit_post_type_cats']) ) {
			$reddit_post_type_cats = array('reddit_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_post_type_cats);
			$udpopt = true;
		}
		//check URL Reddit shortner
		if( !isset($wpw_auto_poster_options['reddit_url_shortener']) ) {
			$reddit_url_shortener = array('reddit_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_url_shortener);
			$udpopt = true;
		}
		//check Reddit shortner bitly username
		if( !isset($wpw_auto_poster_options['reddit_bitly_username']) ) {
			$reddit_bitly_username = array('reddit_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_bitly_username);
			$udpopt = true;
		}
		//Check Reddit bitly access token
		if( !isset($wpw_auto_poster_options['reddit_bitly_access_token']) ) {
			$reddit_bitly_access_token = array('reddit_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $reddit_bitly_access_token);
			$udpopt = true;
		}
		//Check Reddit shorttest api token
		if( !isset($wpw_auto_poster_options['reddit_shortest_api_token']) ) {
			$reddit_shortest_api_token = array('reddit_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $reddit_shortest_api_token);
			$udpopt = true;
		}
		//Check Reddit prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_reddit_metabox']) ) {
			$prevent_post_reddit_metabox = array('prevent_post_reddit_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_reddit_metabox);
			$udpopt = true;
		}
		//Check setting for Reddit custom msg option
		if( !isset($wpw_auto_poster_options['reddit_custom_msg_options']) ) {
			$reddit_custom_msg_options = array('reddit_custom_msg_options' => 'global_msg');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_custom_msg_options);
			$udpopt = true;
		}
		//Check Reddit custom image exists
		if( !isset($wpw_auto_poster_options['reddit_post_image']) ) {
			$reddit_post_image = array('reddit_post_image' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_post_image);
			$udpopt = true;
		}
		//Check Reddit custom template options
		if( !isset($wpw_auto_poster_options['reddit_global_message_template']) ) {
			$reddit_global_message_template = array('reddit_global_message_template' => '{title} - {link}');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $reddit_global_message_template);
			$udpopt = true;
		}
		// Same code for reposter settings
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');
		if( !isset($wpw_auto_poster_reposter_options['enable_reddit']) ) {
			$reddit_enable = array('enable_reddit' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_enable);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['enable_reddit_for']) ) {
			$enable_reddit_for = array('enable_reddit_for' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$enable_reddit_for);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_posting_cats']) ) {
			$reddit_posting_cats = array('reddit_posting_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,  $reddit_posting_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_post_type_cats']) ) {
			$reddit_post_type_cats = array('reddit_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_post_ids_exclude']) ) {
			$reddit_post_ids_exclude = array('reddit_post_ids_exclude' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_post_ids_exclude);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_posts_limit']) ) {
			$reddit_posts_limit = array('reddit_posts_limit' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $reddit_posts_limit);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['repost_wp_custom_msg_options']) ) {
			$repost_wp_custom_msg_options = array('repost_wp_custom_msg_options' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $repost_wp_custom_msg_options);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_post_image']) ) {
			$reddit_post_image = array('reddit_post_image' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_post_image);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_global_title']) ) {
			$reddit_global_title = array('reddit_global_title' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_global_title);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['reddit_global_message_template']) ) {
			$reddit_global_message_template = array('reddit_global_message_template' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$reddit_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);

			update_option('wpw_auto_poster_reddit_sess_data', array());
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.28');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');

	//check set option for plugin is set 1.1.28
	if( $wpw_auto_poster_set_option == '1.1.28' ) {
		$udpopt = false;

		//check telegram is enable or not
		if( !isset($wpw_auto_poster_options['enable_telegram']) ) {
			$enable_telegram = array('enable_telegram' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_telegram);
			$udpopt = true;
		}
		//check telegram for posts or page
		if( !isset($wpw_auto_poster_options['enable_telegram_for']) ) {
			$enable_telegram_for = array('enable_telegram_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_telegram_for);
			$udpopt = true;
		}
		//check telegram for posts type tag
		if( !isset($wpw_auto_poster_options['tele_post_type_tags']) ) {
			$tele_post_type_tags = array('tele_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_post_type_tags);
			$udpopt = true;
		}
		//check telegram for posts type category
		if( !isset($wpw_auto_poster_options['tele_post_type_cats']) ) {
			$tele_post_type_cats = array('tele_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_post_type_cats);
			$udpopt = true;
		}
		//check URL telegram shortner
		if( !isset($wpw_auto_poster_options['tele_url_shortener']) ) {
			$tele_url_shortener = array('tele_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_url_shortener);
			$udpopt = true;
		}
		//check telegram URl shortner bitly username
		if( !isset($wpw_auto_poster_options['tele_bitly_username']) ) {
			$tele_bitly_username = array('tele_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_bitly_username);
			$udpopt = true;
		}
		//Check telegram bitly access token
		if( !isset($wpw_auto_poster_options['tele_bitly_access_token']) ) {
			$tele_bitly_access_token = array('tele_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_bitly_access_token);
			$udpopt = true;
		}
		//Check telegram shorttest api token
		if( !isset($wpw_auto_poster_options['tele_shortest_api_token']) ) {
			$tele_shortest_api_token = array('tele_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_shortest_api_token);
			$udpopt = true;
		}
		//Check telegram access token and secret array
		if( !isset($wpw_auto_poster_options['telegram_keys']) ) {
			$tele_keys = array('telegram_keys' => array());
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_keys);
			$udpopt = true;
		}
		//Check telegram prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_tele_metabox']) ) {
			$prevent_post_tele_metabox = array('prevent_post_tele_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_tele_metabox);
			$udpopt = true;
		}
		//Check setting for telegram custom msg option
		if( !isset($wpw_auto_poster_options['tele_custom_msg_options']) ) {
			$tele_custom_msg_options = array('tele_custom_msg_options' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_custom_msg_options);
			$udpopt = true;
		}
		//Check telegram custom image exists
		if( !isset($wpw_auto_poster_options['tele_post_image']) ) {
			$tele_post_image = array('tele_post_image' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_post_image);
			$udpopt = true;
		}
		//Check telegram custom image exists
		if( !isset($wpw_auto_poster_options['tele_post_img_caption']) ) {
			$tele_post_img_caption = array('tele_post_img_caption' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_post_img_caption);
			$udpopt = true;
		}
		//Check telegram custom template options
		if( !isset($wpw_auto_poster_options['tele_global_message_template']) ) {
			$tele_global_message_template = array('tele_global_message_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tele_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		// Same code for reposter settings
		$udpopt = false;
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

		if( !isset($wpw_auto_poster_reposter_options['enable_telegram']) ) {
			$tele_enable = array('enable_telegram' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_enable);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['enable_telegram_for']) ) {
			$enable_telegram_for = array('enable_telegram_for' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $enable_telegram_for);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_posting_cats']) ) {
			$tele_posting_cats = array('tele_posting_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_posting_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_post_type_cats']) ) {
			$tele_post_type_cats = array('tele_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_post_type_cats']) ) {
			$tele_post_type_cats = array('tele_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_post_ids_exclude']) ) {
			$tele_post_ids_exclude = array('tele_post_ids_exclude' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_post_ids_exclude);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_posts_limit']) ) {
			$tele_posts_limit = array('tele_posts_limit' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_posts_limit);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['repost_tele_custom_msg_options']) ) {
			$repost_tele_custom_msg_options = array('repost_tele_custom_msg_options' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $repost_tele_custom_msg_options);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_global_message_template']) ) {
			$tele_global_message_template = array('tele_global_message_template' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_global_message_template);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_post_image']) ) {
			$tele_post_image = array('tele_post_image' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $tele_post_image);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['tele_post_img_caption']) ) {
			$repost_tele_post_img_caption = array('tele_post_img_caption' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $repost_tele_post_img_caption);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.29');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');


	//check set option for plugin is set 1.1.29
	if( $wpw_auto_poster_set_option == '1.1.29' ) {

		$udpopt = false;
		$wpw_auto_poster_options = get_option('wpw_auto_poster_options');

		//check Medium is enable or not
		if( !isset($wpw_auto_poster_options['enable_medium']) ) {
			$enable_medium = array('enable_medium' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$enable_medium);
			$udpopt = true;
		}
		//check Medium posts or page
		if( !isset($wpw_auto_poster_options['enable_medium_for']) ) {
			$enable_medium_for = array('enable_medium_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$enable_medium_for);
			$udpopt = true;
		}
		//check Medium for posts type tag
		if( !isset($wpw_auto_poster_options['medium_post_type_tags']) ) {
			$medium_post_type_tags = array('medium_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_post_type_tags);
			$udpopt = true;
		}
		//check Medium for posts type category
		if( !isset($wpw_auto_poster_options['medium_post_type_cats']) ) {
			$medium_post_type_cats = array('medium_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_post_type_cats);
			$udpopt = true;
		}
		//check URL Medium shortner
		if( !isset($wpw_auto_poster_options['medium_url_shortener']) ) {
			$medium_url_shortener = array('medium_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_url_shortener);
			$udpopt = true;
		}
		//check Medium shortner bitly username
		if( !isset($wpw_auto_poster_options['medium_bitly_username']) ) {
			$medium_bitly_username = array('medium_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_bitly_username);
			$udpopt = true;
		}
		//Check Medium bitly access token
		if( !isset($wpw_auto_poster_options['medium_bitly_access_token']) ) {
			$medium_bitly_access_token = array('medium_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $medium_bitly_access_token);
			$udpopt = true;
		}
		//Check Medium shorttest api token
		if( !isset($wpw_auto_poster_options['medium_shortest_api_token']) ) {
			$medium_shortest_api_token = array('medium_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $medium_shortest_api_token);
			$udpopt = true;
		}
		//Check Medium prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_medium_metabox']) ) {
			$prevent_post_medium_metabox = array('prevent_post_medium_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$prevent_post_medium_metabox);
			$udpopt = true;
		}
		//Check Medium for Reddit custom msg option
		if( !isset($wpw_auto_poster_options['medium_custom_msg_options']) ) {
			$medium_custom_msg_options = array('medium_custom_msg_options' => 'global_msg');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_custom_msg_options);
			$udpopt = true;
		}
		//Check Medium custom image exists
		if( !isset($wpw_auto_poster_options['medium_post_image']) ) {
			$medium_post_image = array('medium_post_image' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_post_image);
			$udpopt = true;
		}
		//Check Medium custom template options
		if( !isset($wpw_auto_poster_options['medium_global_message_template']) ) {
			$medium_global_message_template = array('medium_global_message_template' => '{title} - {link}');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		// Same code for reposter settings
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');
		if( !isset($wpw_auto_poster_reposter_options['enable_medium']) ) {
			$medium_enable = array('enable_medium' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_enable);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['enable_medium_for']) ) {
			$enable_medium_for = array('enable_medium_for' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$enable_medium_for);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_posting_cats']) ) {
			$medium_posting_cats = array('medium_posting_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_posting_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_post_type_cats']) ) {
			$medium_post_type_cats = array('medium_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_post_ids_exclude']) ) {
			$medium_post_ids_exclude = array('medium_post_ids_exclude' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_post_ids_exclude);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_posts_limit']) ) {
			$medium_posts_limit = array('medium_posts_limit' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_posts_limit);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['repost_medium_custom_msg_options']) ) {
			$medium_custom_msg_options = array('repost_medium_custom_msg_options' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_custom_msg_options);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_post_image']) ) {
			$medium_post_image = array('medium_post_image' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_post_image);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_global_title']) ) {
			$medium_global_title = array('medium_global_title' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_global_title);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['medium_global_message_template']) ) {
			$medium_global_message_template = array('medium_global_message_template' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options,$medium_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
		  	update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
	    update_option('wpw_auto_poster_set_option', '1.1.30');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');
	//check set option for plugin is set 1.1.30
	if( $wpw_auto_poster_set_option == '1.1.30' ) {
		
		$udpopt = false;
		$wpw_auto_poster_options = get_option('wpw_auto_poster_options');

		//Add Include and Exclude Taxonomies for setting page For Facebook
		if( !isset($wpw_auto_poster_options['fb_posting_cats']) ) {
			$fb_posting_cats = array('fb_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$fb_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Twitter
		if( !isset($wpw_auto_poster_options['tw_posting_cats']) ) {
			$tw_posting_cats = array('tw_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$tw_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Linkedin
		if( !isset($wpw_auto_poster_options['li_posting_cats']) ) {
			$li_posting_cats = array('li_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$li_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Tumblr
		if( !isset($wpw_auto_poster_options['tb_posting_cats']) ) {
			$tb_posting_cats = array('tb_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$tb_posting_cats);
			$udpopt = true;
		}


		//Add Include and Exclude Taxonomies for setting page For Youtube
		if( !isset($wpw_auto_poster_options['yt_posting_cats']) ) {
			$yt_posting_cats = array('yt_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$yt_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Pinterest
		if( !isset($wpw_auto_poster_options['pin_posting_cats']) ) {
			$pin_posting_cats = array('pin_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$pin_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For GMB
		if( !isset($wpw_auto_poster_options['gmb_posting_cats']) ) {
			$gmb_posting_cats = array('gmb_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$gmb_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Reddit
		if( !isset($wpw_auto_poster_options['reddit_posting_cats']) ) {
			$reddit_posting_cats = array('reddit_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$reddit_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Telegram
		if( !isset($wpw_auto_poster_options['tele_posting_cats']) ) {
			$tele_posting_cats = array('tele_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$tele_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For Medium
		if( !isset($wpw_auto_poster_options['medium_posting_cats']) ) {
			$medium_posting_cats = array('medium_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$medium_posting_cats);
			$udpopt = true;
		}

		//Add Include and Exclude Taxonomies for setting page For WordPress
		if( !isset($wpw_auto_poster_options['wp_posting_cats']) ) {
			$wp_posting_cats = array('wp_posting_cats' => 'exclude');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options,$wp_posting_cats);
			$udpopt = true;
		}

		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.31');
	}

	$wpw_auto_poster_set_option = get_option('wpw_auto_poster_set_option');
	//check set option for plugin is set 1.1.31
	if( $wpw_auto_poster_set_option == '1.1.31' ) {
		$udpopt = false;
		//check instagram is enable or not
		if( !isset($wpw_auto_poster_options['enable_insta']) ) {
			$enable_insta = array('enable_insta' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_insta);
			$udpopt = true;
		}
		//check instagram for posts or page
		if( !isset($wpw_auto_poster_options['enable_insta_for']) ) {
			$enable_insta_for = array('enable_insta_for' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $enable_insta_for);
			$udpopt = true;
		}
		//check instagram for posts type tag
		if( !isset($wpw_auto_poster_options['insta_post_type_tags']) ) {
			$insta_post_type_tags = array('insta_post_type_tags' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_post_type_tags);
			$udpopt = true;
		}
		//check instagram for posts type category
		if( !isset($wpw_auto_poster_options['insta_post_type_cats']) ) {
			$insta_post_type_cats = array('insta_post_type_cats' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_post_type_cats);
			$udpopt = true;
		}
		//check URL instagram shortner
		if( !isset($wpw_auto_poster_options['insta_url_shortener']) ) {
			$insta_url_shortener = array('insta_url_shortener' => 'wordpress');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_url_shortener);
			$udpopt = true;
		}
		//check instagram URl shortner bitly username
		if( !isset($wpw_auto_poster_options['insta_bitly_username']) ) {
			$insta_bitly_username = array('insta_bitly_username' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_bitly_username);
			$udpopt = true;
		}
		//Check instagram bitly access token
		if( !isset($wpw_auto_poster_options['insta_bitly_access_token']) ) {
			$insta_bitly_access_token = array('insta_bitly_access_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_bitly_access_token);
			$udpopt = true;
		}
		//Check instagram shorttest api token
		if( !isset($wpw_auto_poster_options['insta_shortest_api_token']) ) {
			$insta_shortest_api_token = array('insta_shortest_api_token' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_shortest_api_token);
			$udpopt = true;
		}
		//Check instagram prevent for metabox
		if( !isset($wpw_auto_poster_options['prevent_post_insta_metabox']) ) {
			$prevent_post_insta_metabox = array('prevent_post_insta_metabox' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $prevent_post_insta_metabox);
			$udpopt = true;
		}
		//Check instagram custom template options
		if( !isset($wpw_auto_poster_options['insta_global_message_template']) ) {
			$insta_global_message_template = array('insta_global_message_template' => '');
			$wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $insta_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
		}

		// Same code for reposter settings
		$udpopt = false;
		$wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');

		if( !isset($wpw_auto_poster_reposter_options['enable_insta']) ) {
			$insta_enable = array('enable_insta' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $insta_enable);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['enable_insta_for']) ) {
			$enable_insta_for = array('enable_insta_for' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $enable_insta_for);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['insta_posting_cats']) ) {
			$insta_posting_cats = array('insta_posting_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $insta_posting_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['insta_post_type_cats']) ) {
			$insta_post_type_cats = array('insta_post_type_cats' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $insta_post_type_cats);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['insta_post_ids_exclude']) ) {
			$insta_post_ids_exclude = array('insta_post_ids_exclude' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $insta_post_ids_exclude);
			$udpopt = true;
		}
		if( !isset($wpw_auto_poster_reposter_options['insta_global_message_template']) ) {
			$insta_global_message_template = array('insta_global_message_template' => '');
			$wpw_auto_poster_reposter_options = array_merge($wpw_auto_poster_reposter_options, $insta_global_message_template);
			$udpopt = true;
		}
		if( $udpopt == true ) { // if any of the settings need to be updated
			update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
		}
		update_option('wpw_auto_poster_set_option', '1.1.32');
	}
}

/**
 *
 * Check for schedule the cron
 *
 * Set the crone if it's not set
 *
 * @package Social Auto Poster
 * @since 2.6.10
 */
function wpw_auto_poster_check_for_schedule() {

	$utc_timestamp = time(); //
	$local_time = current_time('timestamp'); // to get current local time

	if( !wp_next_scheduled('wpw_auto_poster_reposter_scheduled_cron') ) {
		$scheds = (array) wp_get_schedules();
		$interval = ( isset($scheds['wpw_reposter_custom_schedule']['interval']) ) ? (int) $scheds['wpw_reposter_custom_schedule']['interval'] : 0;
		$utc_timestamp = $local_time + $interval;
		wp_schedule_event($utc_timestamp, 'wpw_reposter_custom_schedule', 'wpw_auto_poster_reposter_scheduled_cron');
	}

	if( !wp_next_scheduled('wpw_auto_poster_clear_log_cron') ) {
		$scheds = (array) wp_get_schedules();
		$interval = ( isset($scheds['weekly']['interval']) ) ? (int) $scheds['weekly']['interval'] : 0;
		$utc_timestamp = $local_time + $interval;
		wp_schedule_event($utc_timestamp, 'weekly', 'wpw_auto_poster_clear_log_cron');
	}

	if( !wp_next_scheduled('wpw_auto_poster_clear_sap_uploads_cron') ) {
		$scheds = (array) wp_get_schedules();
		$interval = ( isset($scheds['weekly']['interval']) ) ? (int) $scheds['weekly']['interval'] : 0;
		$utc_timestamp = $local_time + $interval;
		wp_schedule_event($utc_timestamp, 'weekly', 'wpw_auto_poster_clear_sap_uploads_cron');
	}

	if( !wp_next_scheduled('wpw_auto_poster_scheduled_quick_share') ) {
		wp_schedule_event( time(), 'wpw_quickshare_custom_schedule', 'wpw_auto_poster_scheduled_quick_share');
	}

	$wpw_auto_poster_options = get_option('wpw_auto_poster_options');
	if( empty($wpw_auto_poster_options['schedule_wallpost_option']) ) {
		return false;
	}

	if( !wp_next_scheduled('wpw_auto_poster_scheduled_cron') && !empty($wpw_auto_poster_options['schedule_wallpost_option']) ) {
		$utc_timestamp = time(); //
		$scheds = (array) wp_get_schedules();
		$current_schedule = $wpw_auto_poster_options['schedule_wallpost_option'];
		if (!empty($current_schedule) && $current_schedule == 'daily' && isset($wpw_auto_poster_options['schedule_wallpost_time']) && isset($wpw_auto_poster_options['schedule_wallpost_minute'])) {
			// Schedule other CRON events starting at user defined hour and periodically thereafter
			$schedule_time = mktime($wpw_auto_poster_options['schedule_wallpost_time'], $wpw_auto_poster_options['schedule_wallpost_minute'], 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));
			// get difference
			$diff = ( $schedule_time - $local_time );
			$utc_timestamp = $utc_timestamp + $diff;
			wp_schedule_event($utc_timestamp, 'daily', 'wpw_auto_poster_scheduled_cron');

		} elseif( !empty($wpw_auto_poster_options['schedule_wallpost_option']) && $wpw_auto_poster_options['schedule_wallpost_option'] == 'twicedaily' && empty($wpw_auto_poster_options['enable_twice_random_posting']) ) {			// Added since version 2.5.1

			$utc_timestamp = time();

			// Schedule other CRON events starting at user defined hour and periodically thereafter
			$schedule_time1 = mktime($wpw_auto_poster_options['schedule_wallpost_twice_time1'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

			// get difference
			$diff = ( $schedule_time1 - $local_time );
			$utc_timestamp1 = $utc_timestamp + $diff;
			wp_schedule_event($utc_timestamp1, 'daily', 'wpw_auto_poster_scheduled_cron');
			$schedule_time2 = mktime($wpw_auto_poster_options['schedule_wallpost_twice_time2'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

			// get difference
			$diff = ( $schedule_time2 - $local_time );
			$utc_timestamp2 = $utc_timestamp + $diff;
			wp_schedule_event($utc_timestamp2, 'daily', 'wpw_auto_poster_scheduled_cron');

		} elseif( !empty($wpw_auto_poster_options['schedule_wallpost_option']) && $wpw_auto_poster_options['schedule_wallpost_option'] == 'hourly' ) {			// Added since version 2.0.0

			// logic to get hours rounded, if current time is 3:15 am it will return 4 am.
			// return value in seconds
			$new_time = ceil($local_time / 3600) * 3600;

			// get difference between 3:15 and 4 so it will become 45 min (2700 seconds)
			$diff = ( $new_time - $local_time );

			// add 2700 seconds so cron will start runnig from 4 am.
			$utc_timestamp = $utc_timestamp + $diff;
			wp_schedule_event($utc_timestamp, $wpw_auto_poster_options['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');

		} else {

			$interval = ( isset($scheds[$current_schedule]['interval']) ) ? (int) $scheds[$current_schedule]['interval'] : 0;

			$utc_timestamp = $utc_timestamp + $interval;

			wp_schedule_event($utc_timestamp, $wpw_auto_poster_options['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');
		}
	}
}

/**
 * Remove plugin settings on uninstall
 *
 * @package Social Auto Poster
 * @since 3.8.2
 */
function wpw_auto_manage_plugin_uninstall_settings() {

	//facebook posting class
	$fbposting = new Wpw_Auto_Poster_FB_Posting();

	//linkedin posting class
	$liposting = new Wpw_Auto_Poster_Li_Posting();

	//tumblr posting class
	$tbposting = new Wpw_Auto_Poster_TB_Posting();

	//pinterest posting class
	$pinposting = new Wpw_Auto_Poster_PIN_Posting();

	//pinterest posting class
	$ytposting = new Wpw_Auto_Poster_Yt_Posting();

	//Google My Business posting class
	$gmbposting = new Wpw_Auto_Poster_GMB_Posting();

	//Google My Business posting class
	$redditposting = new Wpw_Auto_Poster_Reddit_Posting();

	//Instagram posting class
	$instaposting = new Wpw_Auto_Poster_INSTA_Posting();

	//facebook session reset
	$fbposting->wpw_auto_poster_fb_reset_session();

	//linkedin session reset
	$liposting->wpw_auto_poster_li_reset_session();

	//tumblr session reset
	$tbposting->wpw_auto_poster_tb_reset_session();

	//pinterest session reset
	$pinposting->wpw_auto_poster_pin_reset_session();

	//youtube session reset
	$ytposting->wpw_auto_poster_yt_reset_session();

	//gmb session reset
	$gmbposting->wpw_auto_poster_gmb_reset_session();

	//Reddit session reset
	$redditposting->wpw_auto_poster_reddit_reset_session();

	//Instagram session reset
	$instaposting->wpw_auto_poster_insta_reset_session();

	//delete auto poster options
	delete_option('wpw_auto_poster_options');

	//delete auto poster reposter options
	delete_option('wpw_auto_poster_reposter_options');

	//deleter facebook session data
	delete_option('wpw_auto_poster_fb_sess_data');

	//delete linkedin session data
	delete_option('wpw_auto_poster_li_sess_data');

	//delete tumblr session data
	delete_option('wpw_auto_poster_tb_sess_data');

	//delete twitter account data
	delete_option('wpw_auto_poster_tw_account_details');

	//delete pinterest session data
	delete_option('wpw_auto_poster_pin_sess_data');

	//delete set option data
	delete_option('wpw_auto_poster_set_option');

	//delete set option data for youtube
	delete_option('wpw_auto_poster_yt_sess_data');

	// Delete WordPress sites
	delete_option('wpw_auto_poster_wordpress_sites');
	delete_option('wpw_auto_poster_wordpress_mapped_posttypes');

	//delete google my business session data
	delete_option('wpw_auto_poster_gmb_sess_data');

	// delete custom post type data
	delete_option('wpw_auto_poster_reddit_sess_data');

	 // Delete telegram data
	$teleindex = get_option( 'wpw_auto_poster_telegram_chat_last_index' );
	for( $i == 0; $i <= $teleindex; $i++ ) {
		delete_option( 'wpw_auto_poster_telegram_chat_' . $i );
	}
	delete_option( 'wpw_auto_poster_telegram_chat_last_index' );

	// delete custom post type data
	$post_types = array( WPW_AUTO_POSTER_LOGS_POST_TYPE );
	foreach( $post_types as $post_type ) {
		$args = array('post_type' => $post_type, 'post_status' => 'any', 'numberposts' => '-1');
		$all_posts = get_posts($args);
		foreach( $all_posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

	//deleter instagram fb session data
	delete_option('wpw_auto_poster_insta_sess_data');
}

/**
 * Default Settings
 *
 * Defining the default values for the plugin options.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_posting_default_settings() {

	global $wpw_auto_poster_options;

	//default values
	$wpw_auto_poster_options = array(

		//General Settings
		'enable_google_tracking' => '',
		'enable_excerpt_content' => '',
		'google_tracking_script' => 'yes',
		'google_tracking_code' => '',
		'delete_options' => '',
		'bitly_username' => '',
		'bitly_api_key' => '',
		'enable_logs' => '',
		'enable_posting_logs' => '',
		'enable_random_posting' => '',
		'schedule_wallpost_option' => '',
		'schedule_wallpost_time' => '0',
		'schedule_wallpost_minute' => '0',
		'daily_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
		'schedule_wallpost_order' => '',
		'autopost_thirdparty_plugins' => 0,
		'schedule_wallpost_custom_minute' => WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE,
		'schedule_wallpost_twice_time1' => '0',
		'schedule_wallpost_twice_time2' => '12',
		'enable_twice_random_posting' => '',
		'enable_posting_for_newpost' => '',

		//Facebook Settings
		'enable_facebook' => '',
		'enable_facebook_for' => '',
		'fb_post_type_tags' => array(),
		'fb_post_type_cats' => array(),
		'fb_app_version' => '209',
		'fb_url_shortener' => 'wordpress',
		'fb_bitly_access_token' => '',
		'fb_shortest_api_token' => '',
		'fb_google_short_api_key' => '',
		'facebook_keys' => array(),
		'fb_posting_cats' => 'exclude',
		'fb_exclude_cats' => array(),
		'fb_wp_pretty_url' => '',
		'prevent_linked_accounts_access' => '',
		'prevent_post_metabox' => '',
		'prevent_post_tw_metabox' => '',
		'prevent_post_li_metabox' => '',
		'prevent_post_tb_metabox' => '',
		'fb_custom_img' => '',
		'custom_status_msg' => esc_html__('New blog post:', 'wpwautoposter') . '  {title} - {link}',
		'fb_global_message_template' => '{title} - {link}',
		'fb_post_share_type' => 'link_posting',
		'facebook_auth_options' => 'appmethod',
		'facebook_rest_type' => 'android',
		'fb_proxy' => '',

		//Twitter Settings
		'enable_twitter' => '',
		'enable_twitter_for' => '',
		'tw_post_type_tags' => array(),
		'tw_post_type_cats' => array(),
		'tw_exclude_cats' => array(),
		'tw_posting_cats' => 'exclude',
		'tw_url_shortener' => 'wordpress',
		'tw_bitly_access_token' => '',
		'tw_shortest_api_token' => '',
		'tw_google_short_api_key' => '',
		'twitter_keys' => '',
		'tw_tweet_img' => '',
		'tw_tweet_template' => 'title_link',
		'tw_custom_tweet_template' => '',
		'tw_wp_pretty_url' => '',

		//LinkedIn Settings
		'enable_linkedin' => '',
		'enable_linkedin_for' => '',
		'li_post_type_tags' => array(),
		'li_post_type_cats' => array(),
		'li_posting_cats' => 'exclude',
		'li_exclude_cats' => array(),
		'li_url_shortener' => 'wordpress',
		'li_bitly_access_token' => '',
		'li_shortest_api_token' => '',
		'li_google_short_api_key' => '',
		'linkedin_app_id' => '',
		'linkedin_app_secret' => '',
		'li_post_image' => '',
		'li_wp_pretty_url' => '',
		'li_company' => '',

		//Tumblr settting
		'enable_tumblr' => '',
		'enable_tumblr_for' => '',
		'tb_post_type_tags' => array(),
		'tb_post_type_cats' => array(),
		'tb_exclude_cats' => array(),
		'tb_posting_cats' => 'exclude',
		'tb_url_shortener' => 'wordpress',
		'tb_bitly_access_token' => '',
		'tb_shortest_api_token' => '',
		'tb_google_short_api_key' => '',
		'tumblr_content_type' => '',
		'tumblr_consumer_key' => '',
		'tumblr_consumer_secret' => '',
		'tb_wp_pretty_url' => '',
		'tb_global_message_template' => '{title} - {link}',

		//Pinterest Settings since 2.6.0
		'enable_pinterest' => '',
		'enable_pinterest_for' => '',
		'pin_post_type_tags' => array(),
		'pin_post_type_cats' => array(),
		'pin_exclude_cats' => array(),
		'pin_posting_cats' => 'exclude',
		'pin_url_shortener' => 'wordpress',
		'pin_bitly_access_token' => '',
		'pin_shortest_api_token' => '',
		'pinterest_auth_options' => '',
		'pin_google_short_api_key' => '',
		'pinterest_keys' => array(),
		'pin_wp_pretty_url' => '',
		'prevent_post_pin_metabox' => '',
		'pin_custom_img' => '',
		'pin_custom_template' => '',

		//Youtube Settings
		'enable_youtube' => '',
		'enable_youtube_for' => '',
		'yt_post_type_tags' => array(),
		'yt_post_type_cats' => array(),
		'yt_exclude_cats' => array(),
		'yt_posting_cats' => 'exclude',
		'yt_url_shortener' => 'wordpress',
		'yt_bitly_access_token' => '',
		'yt_shortest_api_token' => '',
		'yt_google_short_api_key' => '',
		'yt_keys' => array(),
		'yt_wp_pretty_url' => '',
		'prevent_post_yt_metabox' => '',
		'yt_custom_img' => '',
		'yt_template' => '',

		//WordPress Settings
		'enable_wordpress' => '',
		'enable_wordpress_for' => '',
		'wp_post_type_tags' => array(),
		'wp_post_type_cats' => array(),
		'wp_exclude_cats' => array(),
		'wp_posting_cats' => 'exclude',
		'wp_url_shortener' => 'wordpress',
		'wp_bitly_access_token' => '',
		'wp_shortest_api_token' => '',
		'wp_google_short_api_key' => '',
		'wordpress_keys' => array(),
		'wp_wp_pretty_url' => '',
		'prevent_post_wp_metabox' => '',
		'wp_post_image' => '',
		'wp_global_title' => '',
		'wp_global_message_template' => '{content} - {hashtags} {hashcats}',

		//Google My Business Settings
		'enable_googlemybusiness' => '',
		'enable_googlemybusiness_for' => '',
		'gmb_post_type_tags' => array(),
		'gmb_post_type_cats' => array(),
		'gmb_exclude_cats' => array(),
		'gmb_posting_cats' => 'exclude',
		'gmb_add_buttons' => 'LEARN_MORE',
		'gmb_url_shortener' => 'wordpress',
		'gmb_bitly_access_token' => '',
		'gmb_shortest_api_token' => '',
		'gmb_google_short_api_key' => '',
		'gmb_post_image' => '',
		'gmb_wp_pretty_url' => '',
		'prevent_post_gmb_metabox' => '',
		'gmb_global_message_template' => '{title} - {link}',

		//Reddit Settings
		'enable_reddit' => '',
		'enable_reddit_for' => '',
		'reddit_post_type_tags' => array(),
		'reddit_post_type_cats' => array(),
		'reddit_exclude_cats' => array(),
		'reddit_posting_cats' => 'exclude',
		'reddit_url_shortener' => 'wordpress',
		'reddit_bitly_access_token' => '',
		'reddit_shortest_api_token' => '',
		'reddit_google_short_api_key' => '',
		'reddit_post_image' => '',
		'reddit_wp_pretty_url' => '',
		'prevent_post_reddit_metabox' => '',
		'reddit_global_message_template' => '{title} - {link}',

		//Telegram Settings
		'enable_telegram' => '',
		'enable_telegram_for' => '',
		'tele_post_type_tags' => array(),
		'tele_post_type_cats' => array(),
		'tele_exclude_cats' => array(),
		'tele_posting_cats' => 'exclude',
		'tele_url_shortener' => 'wordpress',
		'tele_bitly_access_token' => '',
		'tele_shortest_api_token' => '',
		'teele_google_short_api_key' => '',
		'telegram_keys' => array(),
		'tele_wp_pretty_url' => '',
		'prevent_post_tele_metabox' => '',
		'tele_post_image' => '',
		'tele_post_img_caption' => '',
		'tele_global_message_template' => '{title} - {link}',


		//Medium Settings
		'enable_medium' => '',
		'enable_medium_for' => '',
		'medium_post_type_tags' => array(),
		'medium_post_type_cats' => array(),
		'medium_exclude_cats' => array(),
		'medium_posting_cats' => 'exclude',
		'medium_url_shortener' => 'wordpress',
		'medium_bitly_access_token' => '',
		'medium_shortest_api_token' => '',
		'medium_google_short_api_key' => '',
		'medium_post_image' => '',
		'medium_wp_pretty_url' => '',
		'prevent_post_medium_metabox' => '',
		'medium_global_message_template' => '{title} - {link}',

		//Instagram Settings
		'enable_instagram' => '',
		'enable_instagram_for' => '',
		'insta_post_type_tags' => array(),
		'insta_post_type_cats' => array(),
		'insta_app_version' => '208',
		'insta_url_shortener' => 'wordpress',
		'insta_bitly_access_token' => '',
		'insta_shortest_api_token' => '',
		'insta_google_short_api_key' => '',
		'instagram_keys' => array(),
		'insta_posting_cats' => 'exclude',
		'insta_exclude_cats' => array(),
		'insta_wp_pretty_url' => '',
		'prevent_linked_accounts_access' => '',
		'prevent_post_insta_metabox' => '',
		'insta_custom_img' => '',
		'custom_status_msg' => esc_html__('New blog post:', 'wpwautoposter') . '  {title} - {link}',
		'insta_global_message_template' => '{title} - {link}',
		'insta_auth_options' => 'appmethod',
	);

	// apply filters for default settings
	$wpw_auto_poster_options = apply_filters( 'wpw_auto_poster_default_settings', $wpw_auto_poster_options );
	update_option( 'wpw_auto_poster_options', $wpw_auto_poster_options );
}

/**
 * Default Settings
 *
 * Defining the default values for the plugin reposter options.
 *
 * @package Social Auto Poster
 * @since 2.6.9
 */
function wpw_auto_posting_reposter_default_settings() {

    global $wpw_auto_poster_reposter_options;

    //default values
    $wpw_auto_poster_reposter_options = array(

        //General Settings
        'schedule_posting_order' => '',
        'schedule_posting_order_behaviour' => 'ASC',
        'schedule_wallpost_option' => array('days' => '0', 'hours' => '0', 'minutes' => '0'),
        'daily_posts_limit' => 10,
        'schedule_wallpost_repeat' => 'no',
        'reposter_repeat_times' => '',
        'unique_posting' => '',
        'enable_posting_for_newpost' => '',
        'minimum_post_age' => '0',
        'maximum_post_age' => '0',

        //Facebook Settings
        'enable_facebook' => '',
        'enable_facebook_for' => '',
        'fb_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'fb_posting_cats' => 'include',
        'fb_post_type_tags' => array(),
        'fb_post_type_cats' => array(),
        'fb_last_posted_page' => 1,
        'fb_post_ids_exclude' => '',

        //Twitter Settings
        'enable_twitter' => '',
        'tw_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'enable_twitter_for' => '',
        'tw_posting_cats' => 'include',
        'tw_post_type_cats' => array(),
        'tw_last_posted_page' => 1,
        'tw_post_ids_exclude' => '',

        //LinkedIn Settings
        'enable_linkedin' => '',
        'enable_linkedin_for' => '',
        'li_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'li_posting_cats' => 'include',
        'li_post_type_cats' => array(),
        'li_last_posted_page' => 1,
        'li_post_ids_exclude' => '',

        //Tumblr settting
        'enable_tumblr' => '',
        'enable_tumblr_for' => '',
        'tb_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'tb_posting_cats' => 'include',
        'tb_post_type_cats' => array(),
        'tb_last_posted_page' => 1,
        'tb_post_ids_exclude' => '',

        //Pinterest Settings
        'enable_pinterest' => '',
        'enable_pinterest_for' => '',
        'pin_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'pin_posting_cats' => 'include',
        'pin_post_type_cats' => array(),
        'pin_last_posted_page' => 1,
        'pin_post_ids_exclude' => '',

        //Youtube Settings
        'enable_youtube' => '',
        'enable_youtube_for' => '',
        'yt_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'yt_posting_cats' => 'include',
        'yt_post_type_cats' => array(),
        'yt_last_posted_page' => 1,
        'yt_post_ids_exclude' => '',

        //WordPress Settings
        'enable_wordpress' => '',
        'enable_wordpress_for' => '',
        'wp_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'wp_posting_cats' => 'include',
        'wp_post_type_cats' => array(),
        'wp_last_posted_page' => 1,
        'wp_post_ids_exclude' => '',

        //Google My Business Settings
        'enable_googlemybusiness' => '',
        'enable_googlemybusiness_for' => '',
        'gmb_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'gmb_posting_cats' => 'include',
        'gmb_post_type_cats' => array(),
        'gmb_last_posted_page' => 1,
        'gmb_post_ids_exclude' => '',

        //Reddit Settings
        'enable_reddit' => '',
        'enable_reddit_for' => '',
        'reddit_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'reddit_posting_cats' => 'include',
        'reddit_post_type_cats' => array(),
        'reddit_last_posted_page' => 1,
        'reddit_post_ids_exclude' => '',

        //Telegram Settings
        'enable_telegram' => '',
        'enable_telegram_for' => '',
        'tele_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'tele_posting_cats' => 'include',
        'tele_post_type_cats' => array(),
        'tele_last_posted_page' => 1,
		'tele_post_ids_exclude' => '',

		//Medium Settings
		'enable_medium' => '',
		'enable_medium_for' => '',
		'medium_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
		'medium_posting_cats' => 'include',
		'medium_post_type_cats' => array(),
		'medium_last_posted_page' => 1,
		'medium_post_ids_exclude' => '',

		//Instagram Settings
        'enable_insta' => '',
        'enable_insta_for' => '',
        'insta_posts_limit' => WPW_AUTO_POSTER_POST_LIMIT,
        'insta_posting_cats' => 'include',
        'insta_post_type_tags' => array(),
        'insta_post_type_cats' => array(),
        'insta_last_posted_page' => 1,
        'insta_post_ids_exclude' => '',
    );

    // apply filters for reposter default settings
    $wpw_auto_poster_reposter_options = apply_filters( 'wpw_auto_poster_reposter_default_settings', $wpw_auto_poster_reposter_options );

    $status = update_option( 'wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options );
}
