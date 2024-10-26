<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Misc Functions
 *
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

/**
 * Get Settings From Option Page
 *
 * Handles to return all settings value
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_settings() {

    $settings = is_array(get_option('wpw_auto_poster_options')) ? get_option('wpw_auto_poster_options') : array();

    return $settings;
}

/**
 * Initialize some intial setup
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_initialize() {

    global $wpw_auto_poster_options;

    // Facebook Application ID and Secret
    $fb_apps = wpw_auto_poster_get_fb_apps();
    
    if (!empty($_GET['wpw_fb_app_id'])) {

        $fb_app_id = stripslashes_deep($_GET['wpw_fb_app_id']);

    } else {

        $fb_app_keys = array_keys($fb_apps);
        $fb_app_id = reset($fb_app_keys);
        
    }

    $fb_app_secret = isset($fb_apps[$fb_app_id]) ? $fb_apps[$fb_app_id] : '';

    if (!defined('WPW_AUTO_POSTER_FB_APP_ID')) {
        define('WPW_AUTO_POSTER_FB_APP_ID', $fb_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_FB_APP_SECRET')) {
        define('WPW_AUTO_POSTER_FB_APP_SECRET', $fb_app_secret);
    }

    // Defining the session variables
    if (!defined('WPW_AUTO_POSTER_FB_SESS1')) {
        define('WPW_AUTO_POSTER_FB_SESS1', 'fb_' . WPW_AUTO_POSTER_FB_APP_ID . '_code');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS2')) {
        define('WPW_AUTO_POSTER_FB_SESS2', 'fb_' . WPW_AUTO_POSTER_FB_APP_ID . '_access_token');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS3')) {
        define('WPW_AUTO_POSTER_FB_SESS3', 'fb_' . WPW_AUTO_POSTER_FB_APP_ID . '_user_id');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS4')) {
        define('WPW_AUTO_POSTER_FB_SESS4', 'fb_' . WPW_AUTO_POSTER_FB_APP_ID . '_state');
    }

    
    // Defining the session variables if app method
    if (!defined('WPW_AUTO_POSTER_FB_SESS1_APP')) {
        define('WPW_AUTO_POSTER_FB_SESS1_APP', 'fb_' . WPW_AUTO_POSTER_FB_APP_METHOD_ID . '_code');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS2_APP')) {
        define('WPW_AUTO_POSTER_FB_SESS2_APP', 'fb_' . WPW_AUTO_POSTER_FB_APP_METHOD_ID . '_access_token');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS3_APP')) {
        define('WPW_AUTO_POSTER_FB_SESS3_APP', 'fb_' . WPW_AUTO_POSTER_FB_APP_METHOD_ID . '_user_id');
    }
    if (!defined('WPW_AUTO_POSTER_FB_SESS4_APP')) {
        define('WPW_AUTO_POSTER_FB_SESS4_APP', 'fb_' . WPW_AUTO_POSTER_FB_APP_METHOD_ID . '_state');
    }


    // Instagram Application ID and Secret
    $insta_apps = wpw_auto_poster_get_insta_apps();
    
    if (!empty($_GET['wpw_insta_app_id'])) {
        $insta_app_id = stripslashes_deep($_GET['wpw_insta_app_id']);
    } else {
        $insta_app_keys = array_keys($insta_apps);
        $insta_app_id = reset($insta_app_keys);
    }
    $insta_app_secret = isset($insta_apps[$insta_app_id]) ? $insta_apps[$insta_app_id] : '';

    if (!defined('WPW_AUTO_POSTER_INSTA_FB_APP_ID')) {
        define('WPW_AUTO_POSTER_INSTA_FB_APP_ID', $insta_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_APP_SECRET')) {
        define('WPW_AUTO_POSTER_INSTA_FB_APP_SECRET', $insta_app_secret);
    }

    // Defining the session variables
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS1')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS1', 'fb_' . WPW_AUTO_POSTER_INSTA_FB_APP_ID . '_code');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS2')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS2', 'fb_' . WPW_AUTO_POSTER_INSTA_FB_APP_ID . '_access_token');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS3')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS3', 'fb_' . WPW_AUTO_POSTER_INSTA_FB_APP_ID . '_user_id');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS4')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS4', 'fb_' . WPW_AUTO_POSTER_INSTA_FB_APP_ID . '_state');
    }


     // Defining the session variables if app method
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS1_APP')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS1_APP', 'fb_' . WPW_AUTO_POSTER_INSTA_APP_METHOD_ID . '_code');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS2_APP')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS2_APP', 'fb_' . WPW_AUTO_POSTER_INSTA_APP_METHOD_ID . '_access_token');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS3_APP')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS3_APP', 'fb_' . WPW_AUTO_POSTER_INSTA_APP_METHOD_ID . '_user_id');
    }
    if (!defined('WPW_AUTO_POSTER_INSTA_FB_SESS4_APP')) {
        define('WPW_AUTO_POSTER_INSTA_FB_SESS4_APP', 'fb_long_access_token');
    }



    // Twitter Consumer Key and Secret
    $tw_consumer_key = isset($wpw_auto_poster_options['twitter_keys']) && isset($wpw_auto_poster_options['twitter_keys']['0']) ? $wpw_auto_poster_options['twitter_keys']['0']['consumer_key'] : '';
    $tw_consumer_secret = isset($wpw_auto_poster_options['twitter_keys']) && isset($wpw_auto_poster_options['twitter_keys']['0']) ? $wpw_auto_poster_options['twitter_keys']['0']['consumer_secret'] : '';
    $tw_auth_token = isset($wpw_auto_poster_options['twitter_keys']) && isset($wpw_auto_poster_options['twitter_keys']['0']) ? $wpw_auto_poster_options['twitter_keys']['0']['oauth_token'] : '';
    $tw_auth_token_secret = isset($wpw_auto_poster_options['twitter_keys']) && isset($wpw_auto_poster_options['twitter_keys']['0']) ? $wpw_auto_poster_options['twitter_keys']['0']['oauth_secret'] : '';

    if (!defined('WPW_AUTO_POSTER_TW_CONS_KEY')) {
        define('WPW_AUTO_POSTER_TW_CONS_KEY', $tw_consumer_key);
    }
    if (!defined('WPW_AUTO_POSTER_TW_CONS_SECRET')) {
        define('WPW_AUTO_POSTER_TW_CONS_SECRET', $tw_consumer_secret);
    }
    if (!defined('WPW_AUTO_POSTER_TW_AUTH_TOKEN')) {
        define('WPW_AUTO_POSTER_TW_AUTH_TOKEN', $tw_auth_token);
    }
    if (!defined('WPW_AUTO_POSTER_TW_AUTH_SECRET')) {
        define('WPW_AUTO_POSTER_TW_AUTH_SECRET', $tw_auth_token_secret);
    }

    //LinkedIn Consumer Key and Secret
    $li_apps = wpw_auto_poster_get_li_apps();

    if (!empty($_GET['wpw_li_app_id'])) {
        $li_app_id = stripslashes_deep($_GET['wpw_li_app_id']);
    } else {
        $li_app_keys = array_keys($li_apps);
        $li_app_id = reset($li_app_keys);
    }

    $li_app_secret = isset($li_apps[$li_app_id]) ? $li_apps[$li_app_id] : '';

    if (!defined('WPW_AUTO_POSTER_LI_APP_ID')) {
        define('WPW_AUTO_POSTER_LI_APP_ID', $li_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_LI_APP_SECRET')) {
        define('WPW_AUTO_POSTER_LI_APP_SECRET', $li_app_secret);
    }
    if (!defined('WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP')) { //http port value
        define('WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP', '80');
    }
    if (!defined('WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP_SSL')) { //ssl port value
        define('WPW_AUTO_POSTER_LINKEDIN_PORT_HTTP_SSL', '443');
    }

    //Tumblr Consumer Key and Secret

    $tb_apps = wpw_auto_poster_get_tb_apps();
    if (!empty($_GET['wpw_tb_app_id'])) {
        $tb_app_id = stripslashes_deep($_GET['wpw_tb_app_id']);
    } else {
        $tb_app_keys = array_keys($tb_apps);
        $tb_app_id = reset($tb_app_keys);
    }

    $tb_consumer_secret = isset($tb_apps[$tb_app_id]) ? $tb_apps[$tb_app_id] : '';
    if (!defined('WPW_AUTO_POSTER_TB_CONS_KEY')) {
        define('WPW_AUTO_POSTER_TB_CONS_KEY', $tb_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_TB_CONS_SECRET')) {
        define('WPW_AUTO_POSTER_TB_CONS_SECRET', $tb_consumer_secret);
    }

    // Pinterest Application ID and Secret added since 2.6.0
    $pin_apps = wpw_auto_poster_get_pin_apps();

    if (!empty($_GET['wpw_pin_app_id'])) {
        $pin_app_id = stripslashes_deep($_GET['wpw_pin_app_id']);
    } else {
        $pin_app_keys = array_keys($pin_apps);
        $pin_app_id = reset($pin_app_keys);
    }
    $pin_app_secret = isset($pin_apps[$pin_app_id]) ? $pin_apps[$pin_app_id] : '';

    if (!defined('WPW_AUTO_POSTER_PIN_APP_ID')) {
        define('WPW_AUTO_POSTER_PIN_APP_ID', $pin_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_PIN_APP_SECRET')) {
        define('WPW_AUTO_POSTER_PIN_APP_SECRET', $pin_app_secret);
    }
}

/**
 * Get Social Auto poster Screen ID
 *
 * Handles to get social auto poster screen id
 *
 * @package Social Auto Poster
 * @since 1.8.1
 */
function wpw_auto_poster_get_sap_screen_id() {

    $wpsap_screen_id = sanitize_title(esc_html__('Social Auto Poster', 'wpwautoposter'));
    return apply_filters('wpw_auto_poster_get_sap_screen_id', $wpsap_screen_id);
}

/**
 * Get Social Auto poster Screen ID
 *
 * Handles to get social auto poster screen id
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_fb_apps() {

    global $wpw_auto_poster_options;

    $fb_apps = array();
    $fb_keys = !empty($wpw_auto_poster_options['facebook_keys']) ? $wpw_auto_poster_options['facebook_keys'] : array();

    if (!empty($fb_keys)) {

        foreach ($fb_keys as $fb_key_id => $fb_key_data) {

            if (!empty($fb_key_data['app_id']) && !empty($fb_key_data['app_secret'])) {
                $fb_apps[$fb_key_data['app_id']] = $fb_key_data['app_secret'];
            }
        } // End of for each
    } // End of main if

    return $fb_apps;
}


/**
 * Get Social Auto poster Screen ID
 *
 * Handles to get social auto poster screen id
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_insta_apps() {

    global $wpw_auto_poster_options;

    $insta_apps = array();
    $insta_keys = !empty($wpw_auto_poster_options['insta_facebook_keys']) ? $wpw_auto_poster_options['insta_facebook_keys'] : array();

    if (!empty($insta_keys)) {

        foreach ($insta_keys as $insta_key_id => $insta_key_data) {

            if (!empty($insta_key_data['app_id']) && !empty($insta_key_data['app_secret'])) {
                $insta_apps[$insta_key_data['app_id']] = $insta_key_data['app_secret'];
            }
        } // End of for each
    } // End of main if

    return $insta_apps;
}

/**
 *
 * Handles to return list of apps for linkedin
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_li_apps() {

    global $wpw_auto_poster_options;

    $li_apps = array();
    $li_keys = !empty($wpw_auto_poster_options['linkedin_keys']) ? $wpw_auto_poster_options['linkedin_keys'] : array();

    if (!empty($li_keys)) {

        foreach ($li_keys as $li_key_id => $li_key_data) {

            if (!empty($li_key_data['app_id']) && !empty($li_key_data['app_secret'])) {
                $li_apps[$li_key_data['app_id']] = $li_key_data['app_secret'];
            }
        } // End of for each
    } // End of main if

    return $li_apps;
}

/**
 *
 * Handles to return list of apps for Tumblr
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_tb_apps() {

    global $wpw_auto_poster_options;

    $tb_apps = array();
    $tb_keys = !empty($wpw_auto_poster_options['tumblr_keys']) ? $wpw_auto_poster_options['tumblr_keys'] : array();

    if (!empty($tb_keys)) {

        foreach ($tb_keys as $tb_key_id => $tb_key_data) {

            if (!empty($tb_key_data['consumer_key']) && !empty($tb_key_data['consumer_secret'])) {
                $tb_apps[$tb_key_data['consumer_key']] = $tb_key_data['consumer_secret'];
            }
        } // End of for each
    } // End of main if

    return $tb_apps;
}

/**
 * Get Google My Business Locations lists
 *
 * @package Social Auto Poster
 * @since 2.2.0
 */
function wpw_auto_poster_get_gmb_accounts_location() {

    // Taking some defaults
    $res_data = array();

    // Get stored fb app grant data
    $wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');
    if (is_array($wpw_auto_poster_gmb_sess_data) && !empty($wpw_auto_poster_gmb_sess_data)) {

        foreach ($wpw_auto_poster_gmb_sess_data as $gmb_sess_key => $gmb_sess_data) {

            /* To fix compatiblity issue with older version datarecords and new version datarecords */
            $old_array = isset( $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] ) ? $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] : array();

            if(is_array($old_array) && !empty( $old_array ) && !isset($old_array[0]) ){
                $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] = array('0' => $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key]);
            }
            /* To fix compatiblity issue with older version datarecords and new version datarecords */
            if( isset( $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] ) && !empty( $gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] ) ){
                foreach ($gmb_sess_data['wpw_auto_poster_gmb_user_accounts'][$gmb_sess_key] as $locations) {
                    $locationname = isset($locations['locationname']) ? $locations['locationname'] : '';

                    $accountid = isset($locations['id']) ? $locations['id'] : '';

                    $accountname = isset($locations['name']) ? $locations['name'] : '';

                    $res_data[$locationname] = $accountid . ' | ' . $accountname;
                }
            }
        }
    }
    return $res_data;
}

/**
 * Get Google My Business Acccounts Lists
 *
 *
 * @package Social Auto Poster
 * @since 2.7.6
 */
function wpw_auto_poster_get_gmb_accounts() {

    // Taking some defaults
    $res_data = array();

    // Get stored gmb data
    $wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');

    // print_r($wpw_auto_poster_gmb_sess_data);
    if (is_array($wpw_auto_poster_gmb_sess_data) && !empty($wpw_auto_poster_gmb_sess_data)) {

        foreach ($wpw_auto_poster_gmb_sess_data as $gmb_key => $gmb_sess_data) {

            if ($gmb_key == $gmb_sess_data['wpw_auto_poster_gmb_user_id']) {
                $res_data[$gmb_key] = $gmb_sess_data['wpw_auto_poster_gmb_user_accounts']['details'][$gmb_key];
            }
        }
    }
    return $res_data;
}


/**
 * Get Reddit Acccounts Lists
 *
 *
 * @package Social Auto Poster
 * @since 3.5.2
 */
function wpw_auto_poster_get_reddit_accounts(){

    // Taking some defaults
    $res_data = array();

    // Get stored reddit data
    $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');

    if( !empty($wpw_auto_poster_reddit_sess_data) && is_array($wpw_auto_poster_reddit_sess_data) ) {
        foreach($wpw_auto_poster_reddit_sess_data as $key => $accounts){
            $res_data[$key] = $accounts['name'];
        }
    }
    return $res_data;
}

/**
 * Get Medium Acccounts Lists
 *
 *
 * @package Social Auto Poster
 * @since 3.8.2
*/

function wpw_auto_poster_get_medium_accounts(){

        // Taking some defaults
    $res_data = array();

        // Get stored reddit data
    $wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');
    if(!empty($wpw_auto_poster_medium_sess_data) && is_array($wpw_auto_poster_medium_sess_data)) {
        foreach($wpw_auto_poster_medium_sess_data as $key => $accounts){

            $account_display = !empty($accounts['display_name']) ? $accounts['display_name'] : '';
            $account_name    = !empty($accounts['name']) ? $accounts['name'] : '';

            $res_data[$key] = array(

             'display_name' => $account_display,
             'name' => $account_name


         );
        }
    }
    return $res_data;
}

/**
 * Get Medium Acccounts with Medium Publications
 *
 *
 * @package Social Auto Poster
 * @since 3.8.2
*/

function wpw_auto_poster_get_medium_accounts_with_publications(){

    // Taking some defaults
    $res_data = array();

    // Get stored reddit data
    $wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');

    if(!empty($wpw_auto_poster_medium_sess_data) && is_array($wpw_auto_poster_medium_sess_data)) {
        foreach($wpw_auto_poster_medium_sess_data as $main_key => $accounts){
            $res_data["main-account"."|".$main_key] = !empty($accounts['name']) ? $accounts['name'] : '';
            if(!empty($accounts['publications']) && is_array($accounts['publications'])) {
                foreach($accounts['publications'] as $key => $my_publications) {
                    $res_data["my-publication"."|".$main_key."|".$my_publications->id] = $accounts['display_name']." | ".$my_publications->name;
                }
            }
        }
    }

    return $res_data;
}

/**
 * Telegram get all chats
 *
 * @package Social Auto Poster
 * @since 3.7.0
 */
function wpw_auto_poster_get_tele_chats() {

    global $wpw_auto_poster_options;

    $tele_apps  = array();
    $tele_keys  = !empty($wpw_auto_poster_options['telegram_keys']) ? $wpw_auto_poster_options['telegram_keys'] : array();

    $chats = array();
    if( !empty($tele_keys) ) {
        foreach( $tele_keys as $key => $teleAcc ) {
            $optKey = 'wpw_auto_poster_telegram_chat_' . $key;

            $chat = get_option( $optKey, array() );
            if( !empty($chat) ) $chats[] = $chat;
        } // End of for each
    } // End of main if

    return $chats;
}

/**
 * Get Reddit Acccounts Lists
 *
 *
 * @package Social Auto Poster
 * @since 3.5.2
 */
function wpw_auto_poster_get_reddit_accounts_with_subreddits_for_posting(){

    // Taking some defaults
    $res_data = array();

    // Get stored reddit data
    $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');    
    if (!empty($wpw_auto_poster_reddit_sess_data) && is_array($wpw_auto_poster_reddit_sess_data)) {
        foreach ($wpw_auto_poster_reddit_sess_data as $key => $accounts){

            $unique_key = $key . "|" . $accounts['display_name'];
            $res_data[$unique_key] = $accounts['display_name'];

            if (!empty($accounts['subreddit_details']) && is_array($accounts['subreddit_details']) ) {

                foreach ( $accounts['subreddit_details'] as $subreddit_key => $sreddit_details ) {

                    $unique_key_subreddit = $key . "|" . $sreddit_details->data->display_name;
                    $res_data[$unique_key_subreddit] = $sreddit_details->data->display_name;                         
                }

            }

        }
    }

    return $res_data;

}

/**
 * Get Reddit Acccounts Lists - This is method is used just for posting purpose
 *
 *
 * @package Social Auto Poster
 * @since 3.5.2
 */
function  wpw_auto_poster_get_reddit_accounts_with_subreddits() {

    // Taking some defaults
    $res_data = array();

    // Get stored reddit data
    $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');   
   

    if (!empty($wpw_auto_poster_reddit_sess_data) && is_array($wpw_auto_poster_reddit_sess_data)) {
        foreach ($wpw_auto_poster_reddit_sess_data as $key => $accounts){

            $unique_key = $key . "|" . $accounts['display_name'];
            $subreddits_data = array();

            if (!empty($accounts['subreddit_details']) && is_array($accounts['subreddit_details']) ) {

                foreach ( $accounts['subreddit_details'] as $subreddit_key => $sreddit_details ) {

                    $unique_key_subreddit = $key . "|" . $sreddit_details->data->display_name;
                    $subreddits_data[$unique_key_subreddit]  = $sreddit_details->data->display_name;                                           
                }

            }

             $res_data[$key] = array(
                'main-account' => $unique_key,
                'subreddits'   => $subreddits_data             
            );
        }
    }
   
    return $res_data;

}

/**
 *
 * Handles to return list of account for Tumblr
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_tb_accounts() {

    // Taking some defaults
    $res_data = array();

    // Get stored tb app grant data
    $wpw_auto_poster_tb_sess_data = get_option('wpw_auto_poster_tb_sess_data');

    
    

    if (is_array($wpw_auto_poster_tb_sess_data) && !empty($wpw_auto_poster_tb_sess_data)) {

        foreach ($wpw_auto_poster_tb_sess_data as $tb_key => $tb_data) {

            if (is_array($tb_data) && !empty($tb_data)) {

                if (isset($tb_data['wpw_auto_poster_tb_user_id'])) {
                    
                    if ( !empty( $tb_data['wpw_auto_poster_tb_cache']->blogs ) && is_array( $tb_data['wpw_auto_poster_tb_cache']->blogs )  ) {

                        $tumblr_blogs = array();
                        $tumblr_blogs = $tb_data['wpw_auto_poster_tb_cache']->blogs;
                        
                        foreach ( $tumblr_blogs as $tb_blog_key => $tb_blog_data  ) {
                        
                            $res_data[$tb_key . '|' . $tb_blog_data->name] = $tb_blog_data->name;

                        }
                        
                    }
                     
                }
            }
        }
    }

    return $res_data;
}

/**
 * Get Social Auto poster Screen ID
 *
 * Handles to get social auto poster screen id
 *
 * @package Social Auto Poster
 * @since 2.2.0
 */
function wpw_auto_poster_get_fb_accounts($data_type = false) {

    // Taking some defaults
    $res_data = array();

    // Get stored fb app grant data
    $wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_fb_sess_data');

    if (is_array($wpw_auto_poster_fb_sess_data) && !empty($wpw_auto_poster_fb_sess_data)) {

        foreach ($wpw_auto_poster_fb_sess_data as $fb_sess_key => $fb_sess_data) {

            $fb_sess_acc = isset($fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_accounts']) ? $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_accounts'] : array();
            $fb_sess_token = isset($fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_tokens']) ? $fb_sess_data['wpw_auto_poster_fb_user_accounts']['auth_tokens'] : array();
            // Retrives only App Users
            if ($data_type == 'all_app_users') {
                // Loop of account and merging with page id and app key
                foreach ($fb_sess_acc as $fb_page_id => $fb_page_name) {
                    $res_data[$fb_sess_key][] = $fb_page_id . '|' . $fb_sess_key;
                }
            } elseif ($data_type == 'all_app_users_with_name') {
                // Loop of account and merging with page id and app key
                foreach ($fb_sess_acc as $fb_page_id => $fb_page_name) {
                    $res_data[$fb_sess_key][$fb_page_id . '|' . $fb_sess_key] = $fb_page_name;
                }
            } elseif ($data_type == 'app_users') {
                $res_data[$fb_sess_key] = (!empty($fb_sess_acc) && is_array($fb_sess_acc) ) ? array_keys($fb_sess_acc) : array();
            } elseif ($data_type == 'all_auth_tokens') {
                // Loop of tokens and merging with page id and app key
                foreach ($fb_sess_token as $fb_sess_token_id => $fb_sess_token_data) {
                    $res_data[$fb_sess_token_id . '|' . $fb_sess_key] = $fb_sess_token_data;
                }
            } elseif ($data_type == 'auth_tokens') {
                // Merging the array
                $res_data = $res_data + $fb_sess_token;
            } elseif ($data_type == 'all_accounts') {
                // Loop of account and merging with page id and app key
                foreach ($fb_sess_acc as $fb_page_id => $fb_page_name) {
                    $res_data[$fb_page_id . '|' . $fb_sess_key] = $fb_page_name;
                }
            } else {
                // Merging the array
                $res_data = $res_data + $fb_sess_acc;
            }
        }
    }

    return $res_data;
}


/**
 * Get Social Auto poster Screen ID
 *
 * Handles to get social auto poster screen id
 *
 * @package Social Auto Poster
 * @since 2.2.0
 */
function wpw_auto_poster_get_insta_accounts($data_type = false) {
    $res_data = array();
    $wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data');
    if (is_array($wpw_auto_poster_insta_sess_data) && !empty($wpw_auto_poster_insta_sess_data)) {
        foreach ($wpw_auto_poster_insta_sess_data as $insta_sess_key => $insta_sess_data) {
            $insta_sess_acc = isset($insta_sess_data['wpw_auto_poster_insta_user_accounts']) ? $insta_sess_data['wpw_auto_poster_insta_user_accounts'] : array();
            if ($data_type == 'all_app_users_with_name') {
                foreach ($insta_sess_acc as $insta_page_id => $insta_page_name) {
                    $res_data[$insta_sess_key][$insta_page_id . '|' . $insta_sess_key] = $insta_page_name;
                }
            } elseif ($data_type == 'all_accounts') {
                foreach ($insta_sess_acc as $insta_page_id => $insta_page_name) {
                    $res_data[$insta_page_id . '|' . $insta_sess_key] = $insta_page_name;
                }
            } else {
                $res_data = $res_data + $insta_sess_acc;
            } 
        }
    }
    return $res_data;
}


/**
 *
 * Handles to return list all facebook account user details including name, id, token
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_fb_app_method() {
    // Taking some defaults
    $res_data = array();

    // Get stored fb app grant data
    $wpw_auto_poster_fb_app_data = get_option('wpw_auto_poster_fb_sess_data');

    if (is_array($wpw_auto_poster_fb_app_data) && !empty($wpw_auto_poster_fb_app_data)) {

        foreach ($wpw_auto_poster_fb_app_data as $fb_app_key => $fb_app_data) {

            if ($fb_app_key == $fb_app_data['wpw_auto_poster_fb_user_id']) {
                $res_data[$fb_app_key] = $fb_app_data['wpw_auto_poster_fb_user_cache'];
            }
        }
    }

    return $res_data;
}

/**
 *
 * Handles to return list all linkedin account user details including name, id, token
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_li_app_method() {
    // Taking some defaults
    $res_data = array();

    // Get stored fb app grant data
    $wpw_auto_poster_li_sess_data = get_option('wpw_auto_poster_li_sess_data');

    if (is_array($wpw_auto_poster_li_sess_data) && !empty($wpw_auto_poster_li_sess_data)) {

        foreach ($wpw_auto_poster_li_sess_data as $li_app_key => $li_app_data) {

            if ($li_app_key == $li_app_data['wpw_auto_poster_li_user_id']) {
                $res_data[$li_app_key] = $li_app_data['wpw_auto_poster_li_cache'];
            }
        }
    }

    return $res_data;
}


/**
 *
 * Handles to return list all instagram account user details including user id
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_get_insta_fb_app_method() {
    // Taking some defaults
    $res_data = array();

    // Get stored fb app grant data
    $wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data');

    if (is_array($wpw_auto_poster_insta_sess_data) && !empty($wpw_auto_poster_insta_sess_data)) {

        foreach ($wpw_auto_poster_insta_sess_data as $insta_app_key => $insta_app_data) {

            if ($insta_app_key == $insta_app_data['wpw_auto_poster_insta_fb_user_id']) {
                $res_data[$insta_app_key] = $insta_app_data['wpw_auto_poster_insta_fb_user_cache'];
            }
        }
    }

    return $res_data;
}


/**
 * Check Extra Security
 *
 * Handles to check extra security
 *
 * @package Social Auto Poster
 * @since 2.1.1
 */
function wpw_auto_poster_extra_security($post_id, $post) {

    $extra_security = false;

    if ((!isset($_POST['post_ID']) || $post_id != $_POST['post_ID'])) {
        $extra_security = true;
    }

    $post_type_object = get_post_type_object($post->post_type);

    $wpw_auto_poster_set_option = get_option('wpw_auto_poster_options');

    if (( isset($wpw_auto_poster_set_option['autopost_thirdparty_plugins']) && $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] == 1)) {

        $extra_security = false;
    }

    /**
     * Current user can edit post not working on cron. Added compability of WordPress Automatic Plugin
     *
     * Change code to solved post save capability issue with thirdparty plugin
     */
    if ((!isset($wpw_auto_poster_set_option['autopost_thirdparty_plugins']) || $wpw_auto_poster_set_option['autopost_thirdparty_plugins'] != 1 ) && (!current_user_can($post_type_object->cap->edit_post, $post_id) )) {
        $extra_security = true;
    }

    $extra_security = apply_filters('wpw_auto_poster_extra_security', $extra_security, $post_id);

    return $extra_security;
}

/**
 * Get all configured Pinterest accounts
 *
 * Handler to get all configured pinterest account on settings page
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_pin_apps() {

    global $wpw_auto_poster_options;

    $pin_apps = array();
    $pin_keys = !empty($wpw_auto_poster_options['pinterest_keys']) ? $wpw_auto_poster_options['pinterest_keys'] : array();

    if (!empty($pin_keys)) {

        foreach ($pin_keys as $pin_key_id => $pin_key_data) {

            if (!empty($pin_key_data['app_id']) && !empty($pin_key_data['app_secret'])) {
                $pin_apps[$pin_key_data['app_id']] = $pin_key_data['app_secret'];
            }
        } // End of for each
    } // End of main if

    return $pin_apps;
}

/**
 * Get Granted Pinterest Account
 *
 * Handles to get all granted pinterest account as per requirement
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_pin_accounts($data_type = false) {

	global $wpw_auto_poster_options;

    // Taking some defaults
	$res_data = array();

	$pinterest_auth_options = !empty($wpw_auto_poster_options['pinterest_auth_options']) ? $wpw_auto_poster_options['pinterest_auth_options'] : 'app';

    // Get stored pin app grant data
	$wpw_auto_poster_pin_sess_data = get_option( 'wpw_auto_poster_pin_sess_data' );

	if( $pinterest_auth_options == 'cookie' ) {
		if( is_array($wpw_auto_poster_pin_sess_data) && !empty($wpw_auto_poster_pin_sess_data) ) {
			foreach( $wpw_auto_poster_pin_sess_data as $username => $pinSite ) {

				if( empty($pinSite['boards']) ) continue;
				foreach( $pinSite['boards'] as $key => $board ) {
					$key = $username . '|' . $board['id'];
					$value = $username . ' - ' . $board['name'];

					$res_data[$key] = $value;
				}
			}
		}
	} else if( $pinterest_auth_options == 'app' ) {
		if( is_array( $wpw_auto_poster_pin_sess_data ) && !empty($wpw_auto_poster_pin_sess_data) ) {
			foreach ( $wpw_auto_poster_pin_sess_data as $pin_sess_key => $pin_sess_data ) {

				$pin_sess_acc_boards    = isset( $pin_sess_data['wpw_auto_poster_pin_user_boards'] )    ? $pin_sess_data['wpw_auto_poster_pin_user_boards'] : array();
				$pin_sess_token     = isset( $pin_sess_data['wpw_auto_poster_pin_token'] )  ? $pin_sess_data['wpw_auto_poster_pin_token'] : array();

				if( $data_type == 'all_app_users_with_boards' ) {
					// Loop of account and merging with board id and app key
					foreach ( $pin_sess_acc_boards as $pin_board_id => $pin_board_name ) {
						$res_data[$pin_sess_key][$pin_board_id .'|'. $pin_sess_key] = $pin_board_name;
					}
				} elseif ( $data_type == 'all_accounts' ) {
					// Loop of account and merging with board id and app key
					foreach ( $pin_sess_acc_boards as $pin_board_id => $pin_board_name ) {
						$res_data[$pin_board_id .'|'. $pin_sess_key] = $pin_board_name;
					}
				} elseif ( $data_type == 'all_auth_tokens' ) {
					$res_data[$pin_sess_key] = $pin_sess_token;
				} else {
					// Merging the array
					$res_data = $res_data + $pin_sess_acc_boards;
				}
			}
		}
	}
	
	return $res_data;
}

/**
 * Get all catgeories list for all post types
 *
 * Handles to fetch categories for all custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_all_categories() {

    $all_types = get_post_types(array('public' => true), 'objects');

    $all_types = is_array($all_types) ? $all_types : array();
    $data = array();

    // If $_POST for post type value is not empty
    if (!empty($all_types)) {

        foreach ($all_types as $type) {

            if (!is_object($type))
                continue;

            if (isset($type->labels)) {
                $label = $type->labels->name ? $type->labels->name : $type->name;
            } else {
                $label = $type->name;
            }

            $post_type = $type->name;
            $categories_array = array();

            if ($label == 'Media' || $label == 'media')
                continue; // skip media

            $all_taxonomies = get_object_taxonomies($post_type, 'objects');

            // Loop on all taxonomies
            foreach ($all_taxonomies as $taxonomy) {

                if (is_object($taxonomy) && !empty($taxonomy->hierarchical)) {

                    $categories = get_terms($taxonomy->name, array('hide_empty' => false)); // Get categories

                    foreach ($categories as $category) {

                        $categories_array[$category->term_id] = $category->name;
                    }
                }
            }
            if (!empty($categories_array)) {

                $data[$post_type]['label'] = $label;
                $data[$post_type]['categories'] = $categories_array;
                unset($categories_array);
            }
        }
    }

    return $data;
}

/**
 * Get all catgeories list for all post types
 *
 * Handles to fetch categories for all custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_all_categories_and_tags() {

    $all_types = get_post_types(array('public' => true), 'objects');

    $all_types = is_array($all_types) ? $all_types : array();
    $data = array();

    $attribute_taxonomy_array = array();

    if (class_exists('WooCommerce') && function_exists('wc_get_attribute_taxonomies')) {

        $attribute_taxonomies = wc_get_attribute_taxonomies();

        foreach ($attribute_taxonomies as $attribute_taxonomy) {

            $attribute_taxonomy_array[] = "pa_" . $attribute_taxonomy->attribute_name;
        }
    }

    $wc_taxonomy_array = array('product_shipping_class', 'product_visibility', 'product_type', 'post_format');

    $taxonomy_array = array_merge($attribute_taxonomy_array, $wc_taxonomy_array);

    // If $_POST for post type value is not empty
    if (!empty($all_types)) {

        foreach ($all_types as $type) {

            if (!is_object($type))
                continue;

            if (isset($type->labels)) {
                $label = $type->labels->name ? $type->labels->name : $type->name;
            } else {
                $label = $type->name;
            }

            $post_type = $type->name;
            $categories_array = array();

            if ($label == 'Media' || $label == 'media')
                continue; // skip media

            $all_taxonomies = get_object_taxonomies($post_type, 'objects');

            // Loop on all taxonomies
            foreach ($all_taxonomies as $taxonomy) {

                if (is_object($taxonomy) && !in_array($taxonomy->name, $taxonomy_array)) {

                    $categories = get_terms($taxonomy->name, array('hide_empty' => false)); // Get categories

                    foreach ($categories as $category) {

                        $categories_array[$category->term_id] = $taxonomy->label . ": " . $category->name;
                    }
                }
            }
            if (!empty($categories_array)) {

                $data[$post_type]['label'] = $label;
                $data[$post_type]['categories'] = $categories_array;
                unset($categories_array);
            }
        }
    }

    return $data;
}

/**
 * Get all static list for all post types - post/ download / product
 *
 * Handles to fetch taxonomy for static post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_static_tag_taxonomy() {

    $result = array(
        'post' => 'post_tag',
        'download' => 'download_tag',
        'product' => 'product_tag'
    );

    return $result;
}

/**
 * Get all selected categories for a post type
 *
 * Handles to fetch selected categories for custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_post_categories($post_type, $postid) {

    $categories = array();
    $taxonomy_names = array();

    $all_taxonomies = get_object_taxonomies($post_type, 'objects');

    $attribute_taxonomy_array = array();

    if (class_exists('WooCommerce') && function_exists('wc_get_attribute_taxonomies')) {

        $attribute_taxonomies = wc_get_attribute_taxonomies();

        foreach ($attribute_taxonomies as $attribute_taxonomy) {

            $attribute_taxonomy_array[] = "pa_" . $attribute_taxonomy->attribute_name;
        }
    }

    $wc_taxonomy_array = array('product_shipping_class', 'product_visibility', 'product_type', 'post_format');

    $taxonomy_array = array_merge($attribute_taxonomy_array, $wc_taxonomy_array);

    if (!empty($all_taxonomies)) {
        // Loop on all taxonomies
        foreach ($all_taxonomies as $taxonomy) {

            if (is_object($taxonomy) && !in_array($taxonomy->name, $taxonomy_array)) {

                $taxonomy_names[] = $taxonomy->name;
            }
        }

        if (!empty($taxonomy_names)) {
            foreach ($taxonomy_names as $key => $taxonomy_name) {

                $term_list = wp_get_post_terms($postid, $taxonomy_name, array("fields" => "ids"));

                foreach ($term_list as $term_single) {

                    $categories[] = $term_single;
                }
            }
        }
    }

    return $categories;
}

/**
 * Get all selected categories term_id for a post type
 *
 * Handles to fetch selected categories term_id for custom post types
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
function wpw_auto_poster_get_post_categories_by_ID($post_type, $postid) {

    $categoriesID = array();
    $all_taxonomies = get_object_taxonomies($post_type, 'objects');

    if( empty($all_taxonomies) ) return $categoriesID;

    // Loop on all taxonomies
    foreach( $all_taxonomies as $taxonomy ) {
        if( ! is_object($taxonomy) || empty($taxonomy->name) ) continue;

        // Get all terms of taxonomy
        $term_list = wp_get_post_terms($postid, $taxonomy->name, array("fields" => "ids"));

        // Check if no terms
        if( empty($term_list) ) continue;

        foreach( $term_list as $term_single ) {
            $categoriesID[] = $term_single;
        }
    }

    return $categoriesID;
}

/**
 * Add notice to session variable
 *
 * @param type $message
 * @param type $notice_type
 *  error – error message displayed with a red border
 *  warning – warning message displayed with a yellow border
 *  success – success message displayed with a green border
 *  info -  info message displayed with a blue border
 *
 * @package Social Auto Poster
 * @since 2.6.2
 */
function sap_add_notice($message, $notice_type = 'success') {

    $wpwautoposter_notices = get_transient('sap_notices');

    // get existing notices
    $notices = !empty($wpwautoposter_notices) ? $wpwautoposter_notices : array();

    // add new notice
    $notices = array($notice_type => array($message));

    set_transient('sap_notices', $notices );
}

/**
 * Get Settings From Option Page
 *
 * Handles to return all reposter settings value
 *
 * @package Social Auto Poster
 * @since 2.6.9
 */
function wpw_auto_poster_reposter_settings() {

    $settings = is_array(get_option('wpw_auto_poster_reposter_options')) ? get_option('wpw_auto_poster_reposter_options') : array();

    return $settings;
}

/**
 * Get post link
 *
 * @package Social Auto Poster
 * @since 2.6.9
 */
function wpw_auto_poster_get_post_link($social_type, $user_details) {

    $post_link = '';

    global $wpw_auto_poster_li_posting;

    //linkedin posting class
    $liposting = $wpw_auto_poster_li_posting;

    switch ($social_type) {

        case 'fb':

        $account_data = explode("|", $user_details['account_id']);
        $profile_id = !empty($account_data[0]) ? $account_data[0] : '';
        $post_link = 'https://www.facebook.com/' . $profile_id;

        break;

        case 'tw':

        $username = ( isset($user_details['user_name'])) ? $user_details['user_name'] : '';
        $post_link = 'https://twitter.com/' . $username;

        break;

        case 'li':

        if (isset($user_details['profile_url'])) {

            $post_link = $user_details['profile_url'];
            $post_link .= '/recent-activity/';
        } else {
            $posting_id = !empty($user_details['account_id']) ? $user_details['account_id'] : '';
            $li_profile_data = $liposting->wpw_auto_poster_get_profiles_data();

            if (!empty($li_profile_data)) {

                foreach ($li_profile_data as $key => $value) {

                    $profileData = explode(":|:", $key);
                    $profile_type = $profileData[0];
                    $profile_id = $profileData[1];
                    if ($posting_id == $profile_id) {
                        $post_link = 'https://www.linkedin.com/' . $profile_type . '/' . $profile_id;
                    }
                }
            }
        }

        break;

        case 'tb':

        $username = ( isset($user_details['user_name'])) ? $user_details['user_name'] : '';
        $post_link = 'https://www.tumblr.com/blog/' . $username;

        break;

        case 'ba':

        $profile_id = ( isset($user_details['account_id']) ) ? $user_details['account_id'] : '';
        $post_link = 'https://buffer.com/app/' . $profile_id;

        break;

        case 'ins':

        $display_name = ( isset($user_details['display_name']) ) ? $user_details['display_name'] : '';
        $post_link = 'https://www.instagram.com/' . $display_name;

        break;

        case 'pin':
        if( !empty($user_details['pin_id']) ) {
            $post_link = 'https://www.pinterest.com/pin/' . trim($user_details['pin_id']);
        } elseif( !empty($user_details['board_url']) ) {
            $post_link = 'https://www.pinterest.com/' . trim($user_details['board_url']);
        } else{
            $account_data = explode("-", $user_details['display_name']);
            $board_name = $account_data[1];
            $post_link = 'https://www.pinterest.com/' . trim($board_name);
        }
        break;

        case 'wp':
        $post_link = isset($user_details['post_link']) ? $user_details['post_link'] : '';
        break;

        case 'gmb':
        $post_link = $user_details['id'];
        break;

        case 'reddit':
        $post_link = "https://www.reddit.com/user/".$user_details['display_name']."/posts";
        break;

        case 'medium':
            if( !empty($user_details['link_to_post']) ) {
                $post_link = $user_details['link_to_post'];
            }
        break;


    }

    return esc_url($post_link);
}

/**
 * Get external image path for posting
 *
 *
 * @package Social Auto Poster
 * @since 2.6.9
 */
function wpw_auto_poster_get_image_path($image_src, $filename = '') {
    global $wp_filesystem;

    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }

    if ( strpos( $image_src, '?' ) != false ) {
        $url = explode( '?', esc_url_raw( $image_src ) );
        $image_src = isset( $url[0] ) ? $url[0] : $image_src;
    }

    $image_path = '';
    $image = '';

    //Check Folder created if not then first creatiing it
    if (!file_exists(WPW_AUTO_POSTER_SAP_UPLOADS_DIR)) {
        wp_mkdir_p(WPW_AUTO_POSTER_SAP_UPLOADS_DIR);
    }

    $response = wp_remote_get($image_src);

    if (isset($response['body']) && !empty($response['body'])) {
        $image = $response['body'];
    }

    if (empty($filename)) {
        $filename = basename($image_src);
    }

    // Validate file extension
    $filetype = wp_check_filetype_and_ext($image_src, $filename);

    // Apply filter to allowed extensions
    $allowed_extensions = apply_filters('wpw_auto_poster_allowed_img_extensions', array('jpg', 'jpeg', 'png', 'gif'));

    if ($filetype['ext'] && $filetype['type'] && in_array($filetype['ext'], $allowed_extensions)) {
        if (!empty($image)) {
            $isUploaded = $wp_filesystem->put_contents(WPW_AUTO_POSTER_SAP_UPLOADS_DIR . $filename, $image);

            if ($isUploaded !== false) {
                $image_path = WPW_AUTO_POSTER_SAP_UPLOADS_DIR . $filename;
            }
        }
    } else {
        // Handle invalid file type
        return new WP_Error('invalid_file_type', __('Invalid file type.', 'wpwautoposter'));
    }

    return $image_path;
}

/**
 * Instanciate the filesystem class
 *
 * @package Social Auto Poster
 * @since 3.7.0
 * @return object WP_Filesystem_Direct instance
 */
function wpw_auto_poster_direct_filesystem() {

    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    return new WP_Filesystem_Direct(new StdClass());
}

/**
 * Get list of days of week
 *
 * @package Social Auto Poster
 * @since 2.8.8
 */
function wpw_auto_poster_get_week_days() {

    return array(
        '1' => esc_html__('Monday', 'wpwautoposter'),
        '2' => esc_html__('Tuesday', 'wpwautoposter'),
        '3' => esc_html__('Wednesday', 'wpwautoposter'),
        '4' => esc_html__('Thursday', 'wpwautoposter'),
        '5' => esc_html__('Friday', 'wpwautoposter'),
        '6' => esc_html__('Saturday', 'wpwautoposter'),
        '0' => esc_html__('Sunday', 'wpwautoposter'),
    );
}

/**
 * Initialize some intial setup
 *
 * @package Social Auto Poster You Tube
 * @since 1.0.0
 */
function wpw_auto_poster_yt_initialization() {

    global $wpw_auto_poster_options;

    //Youtube Consumer Key and Secret

    $yt_apps = wpw_auto_poster_get_yt_apps();

    if (!empty($_GET['wpw_yt_app_id'])) {
        $yt_app_id = stripslashes_deep($_GET['wpw_yt_app_id']);
    } else {
        $yt_app_keys = array_keys($yt_apps);
        $yt_app_id = reset($yt_app_keys);
    }
    $yt_app_secret = isset($yt_apps[$yt_app_id]) ? $yt_apps[$yt_app_id] : '';

    if (!defined('WPW_AUTO_POSTER_YT_APP_ID')) {
        define('WPW_AUTO_POSTER_YT_APP_ID', $yt_app_id);
    }
    if (!defined('WPW_AUTO_POSTER_YT_APP_SECRET')) {
        define('WPW_AUTO_POSTER_YT_APP_SECRET', $yt_app_secret);
    }
    if (!defined('WPW_AUTO_POSTER_YOUTUBE_PORT_HTTP')) { //http port value
        define('WPW_AUTO_POSTER_YOUTUBE_PORT_HTTP', '80');
    }
    if (!defined('WPW_AUTO_POSTER_YOUTUBE_PORT_HTTP_SSL')) { //ssl port value
        define('WPW_AUTO_POSTER_YOUTUBE_PORT_HTTP_SSL', '443');
    }
}

/**
 * Get all youtube apps
 *
 * @package Social Auto Poster You Tube
 * @since 1.0.0
 */
function wpw_auto_poster_get_yt_apps() {

    global $wpw_auto_poster_options;

    $yt_apps = array();
    $yt_keys = !empty($wpw_auto_poster_options['yt_keys']) ? $wpw_auto_poster_options['yt_keys'] : array();

    if( !empty($yt_keys) ) {
        foreach( $yt_keys as $yt_key_id => $yt_key_data ) {
            if (!empty($yt_key_data['app_id']) && !empty($yt_key_data['app_secret'])) {
                $yt_apps[$yt_key_data['app_id']] = $yt_key_data['app_secret'];
            }
        } // End of for each
    } // End of main if

    return $yt_apps;
}

/**
 * Get current post type name
 *
 * @package Social Auto Poster You Tube
 * @since 4.0.5
 */
function wpw_auto_poster_get_current_post_type() {
    
    global $post, $typenow, $current_screen;
    
    if ($post && $post->post_type) return $post->post_type;
    
    elseif($typenow) return $typenow;
    
    elseif($current_screen && $current_screen->post_type) return $current_screen->post_type;
    
    elseif(isset($_REQUEST['post_type'])) return sanitize_key($_REQUEST['post_type']);
    
    return null;
    
}

/**
 * Social Auto Poster license check
 *
 * @package Social Auto Poster
 * @since 5.2.1
 */
function wpw_auto_poster_is_lincense_activated() {
    update_option( 'wpw_auto_poster_activation_code', '123123123123' );
    update_option( 'wpw_auto_poster_email_address', 'no@email.com' );
    update_option( 'wpw_auto_poster_activated', base64_encode("123123123123%no@email.com") );
    $status = false;
    $activated = get_option( 'wpw_auto_poster_activated' );
    if( $activated ) {
        $activated = base64_decode( $activated );
        $data = explode( '%', $activated );
        $license_code = get_option( 'wpw_auto_poster_activation_code' );
        $email = get_option( 'wpw_auto_poster_email_address' );
        if( ! empty( $data ) && $data[0] === $license_code && $data[1] === $email ) {
            $status = true;
        }
    }
    return $status;
}