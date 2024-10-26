<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Insta Page Posting Class
 *
 * Handles all the functions to post the submitted and approved
 * reviews to a chosen Fan Page / Facebook Account.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

// Include the autoloader provided in the SDK
require_once WPW_AUTO_POSTER_SOCIAL_DIR . "/facebook/autoload.php";

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class Wpw_Auto_Poster_INSTA_Posting
{

    public $facebook, $message, $model, $logs, $_insta_user_cache;
    public $fb_app_version = '';
    public $error = "";
    public $helper;
    public $grantaccessToken;
    public $fbPermissions = array('pages_manage_posts','pages_show_list','instagram_basic','instagram_content_publish','business_management');  //Optional permissions
    public $api_url = 'https://graph.facebook.com/v14.0/';


    public function __construct()
    {

        global $wpw_auto_poster_message_stack, $wpw_auto_poster_model,
        $wpw_auto_poster_logs;

        $this->message = $wpw_auto_poster_message_stack;
        $this->model = $wpw_auto_poster_model;
        $this->logs = $wpw_auto_poster_logs;

        //initialize the session value when data is saved in database
        add_action('init', array($this, 'wpw_auto_poster_insta_initialize'));
    }

    /**
     * Include Facebook Class
     *
     * Handles to load facebook class
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_load_insta_fb($app_id = false)
    {

        global $wpw_auto_poster_options;

        // Facebook app version
        $this->fb_app_version = ( !empty($wpw_auto_poster_options['fb_app_version']) ) ? $wpw_auto_poster_options['fb_app_version'] : '';

        // Getting facebook apps
        if (isset($_GET['wpw_auto_poster_instagram_app_method']) && $_GET['wpw_auto_poster_instagram_app_method'] == 'appmethod') {
            $fb_apps = array(
                WPW_AUTO_POSTER_INSTA_APP_METHOD_ID => WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET,
            );
        } elseif (isset($wpw_auto_poster_options['insta_fb_auth_options']) && $wpw_auto_poster_options['insta_fb_auth_options'] == 'appmethod') {
            $fb_apps = array(
                WPW_AUTO_POSTER_INSTA_APP_METHOD_ID => WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET,
            );
        } else {
            $fb_apps = wpw_auto_poster_get_insta_apps();
        }

        // If app id is not passed then take first fb app data
        if (empty($app_id)) {
            $fb_apps_keys = array_keys($fb_apps);
            $app_id = reset($fb_apps_keys);
        }

        // Check facebook application id and application secret is not empty or not
        if (!empty($app_id) && !empty($fb_apps[$app_id])) {
            $this->facebook = new Facebook(array(
                'app_id' => $app_id,
                'app_secret' => $fb_apps[$app_id],
                'cookie' => true,
                'default_graph_version' => WPW_AUTO_POSTER_FB_GRAPH_VERSION,
            ));

            // Get redirect login helper
            $this->helper = $this->facebook->getRedirectLoginHelper();

            return true;
        } else {
            return false;
        }
    }


    /**
     * Include Facebook Class
     *
     * Handles to load facebook class with the use of fix app id and secret
     * Facebook App method
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_load_insta_fb_app_method($app_id = false)
    {

        global $wpw_auto_poster_options;

        // Facebook app version
        $this->fb_app_version = ( !empty($wpw_auto_poster_options['fb_app_version']) ) ? $wpw_auto_poster_options['fb_app_version'] : '';


        // Check facebook application id and application secret is not empty or not
        if (!empty(WPW_AUTO_POSTER_INSTA_APP_METHOD_ID) && !empty(WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET)) {
            $this->facebook = new Facebook(array(
                'app_id' => WPW_AUTO_POSTER_INSTA_APP_METHOD_ID,
                'app_secret' => WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET,
                'cookie' => true,
                'default_graph_version' => WPW_AUTO_POSTER_FB_GRAPH_VERSION,
            ));

            // Get redirect login helper
            $this->helper = $this->facebook->getRedirectLoginHelper();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Assign Instagram User's all Data to session
     *
     * Handles to assign user's Instagram data
     * to sessoin & save to database
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_insta_initialize()
    {

        global $wpw_auto_poster_options;

        //set session to set tab selected in settings page

        if (isset($_GET['code']) && isset($_GET['wpw_instagram_grant']) && $_GET['wpw_instagram_grant']) {
            $insta_fb_auth_options = !empty($wpw_auto_poster_options['insta_fb_auth_options']) ? $wpw_auto_poster_options['insta_fb_auth_options'] : 'graph';

            $wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data');
            
            if (isset($_GET['access_token']) && $_GET['access_token'] != '') {
                if ($insta_fb_auth_options != 'appmethod') {
                    $wpw_auto_poster_options['insta_fb_auth_options'] = "appmethod";
                    update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
                    //update_option('wpw_auto_poster_insta_sess_data', array()); To fixed multiple account issue fix
                } else {
                   // update_option('wpw_auto_poster_insta_sess_data', array()); To fixed multiple account issue fix
                }

                $this->grantaccessToken = stripslashes_deep($_GET['access_token']);
                try {
                    $this->facebook = new Facebook(array(
                        'app_id' => WPW_AUTO_POSTER_INSTA_APP_METHOD_ID,
                        'app_secret' => WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET,
                        'cookie' => true,
                        'default_graph_version' => WPW_AUTO_POSTER_FB_GRAPH_VERSION,
                    ));

                    $profileRequest = $this->facebook->get('/me?fields=name,first_name,last_name', $this->grantaccessToken);
                    $user = $profileRequest->getGraphNode()->asArray();
                    
                    $client =  $this->facebook->getOAuth2Client();
                    $accessTokenLong = $client->getLongLivedAccessToken($this->grantaccessToken);
                } catch (FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                } catch (FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }
                
                if (!empty($user)) {
                    //record logs for user id
                    $this->logs->wpw_auto_poster_add('Facebook User ID : ' . $user['id']);

                    try {
                        // Proceed knowing you have a logged in user who's authenticated.
                        $wpweb_insta_fb_user_cache = $user;
                        set_transient('wpweb_insta_fb_user_cache', $wpweb_insta_fb_user_cache);
                        $this->_insta_user_cache = $wpweb_insta_fb_user_cache;

                        $wpweb_insta_fb_user_id = $user['id'];
                        set_transient('wpweb_insta_fb_user_id', $wpweb_insta_fb_user_id);

                        $wpweb_insta_fb_user_accounts = $this->wpw_auto_poster_fb_fetch_accounts();
                        set_transient('wpweb_insta_fb_user_accounts', $wpweb_insta_fb_user_accounts);

                        $wpweb_insta_user_accounts = $this->wpw_auto_poster_insta_fetch_accounts();
                        set_transient('wpweb_insta_fb_user_accounts', $wpweb_insta_user_accounts);

                        // Start code to manage session from database
                        $wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data');

                        // Checking if the grant extend is already done or not
                        if (!isset($wpw_auto_poster_insta_sess_data[$user['id']])) {
                            $sess_insta_data = array(
                                'wpw_auto_poster_insta_fb_user_cache' => $wpweb_insta_fb_user_cache,
                                'wpw_auto_poster_insta_fb_user_id' => $wpweb_insta_fb_user_id,
                                'wpw_auto_poster_insta_fb_user_accounts' => $wpweb_insta_fb_user_accounts,
                                'wpw_auto_poster_insta_user_accounts' => $wpweb_insta_user_accounts,
                                WPW_AUTO_POSTER_INSTA_FB_SESS1_APP => stripslashes_deep($_GET['code']),
                                WPW_AUTO_POSTER_INSTA_FB_SESS2_APP => stripslashes_deep($_GET['access_token']),
                                'wpw_auto_poster_'.$user['id'].'_long_access_token' => $accessTokenLong->getValue(),
                            );

                            if (!empty($wpw_auto_poster_insta_sess_data)) { 

                                foreach ($wpw_auto_poster_insta_sess_data as $k_app_id => $v_sess_data) {
                                    if ($k_app_id == $v_sess_data['wpw_auto_poster_insta_fb_user_id']) {
                                        unset($wpw_auto_poster_insta_sess_data[$user['id']]);
                                    }
                                }
                            }

                            // Save Multiple Accounts
                            $wpw_auto_poster_insta_sess_data[$user['id']] = $sess_insta_data;

                            // Update session data to options
                            update_option('wpw_auto_poster_insta_sess_data', $wpw_auto_poster_insta_sess_data);

                            // Record logs for session data updated to options
                            $this->logs->wpw_auto_poster_add('Facebook Session Data Updated to Options');
                        }


                        // Record logs for grant extend successfully
                        $this->logs->wpw_auto_poster_add('Facebook Grant Extended Permission Successfully.');
                    } catch (FacebookApiException $e) {
                        //record logs exception generated
                        $this->logs->wpw_auto_poster_add('Facebook Exception : ' . $e->__toString());

                        //user is null
                        $user = null;
                    } //end catch
                }

                $this->message->add_session('poster-selected-tab', 'instagram');

            //redirect to proper page
                wp_redirect(add_query_arg(array('wpw_instagram_grant' => false, 'code' => false, 'state' => false, 'access_token' => false)));
                exit;
            }
        }
    }

    /**
     * Insta Login URL
     *
     * Getting the login URL from Instagram.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_insta_login_url($app_id = false)
    {

        //load facebook class
        $facebook = $this->wpw_auto_poster_load_insta_fb($app_id);

        //check facebook class is exis or not
        if (!$facebook) {
            return false;
        }

        $redirect_URL = add_query_arg(array('page' => 'wpw-auto-poster-settings' ), admin_url('admin.php'));

        $redirect_URL = apply_filters('wpw_auto_poster_fb_redirect_url', $redirect_URL);
        $redirect_URL = add_query_arg(array('wpw_instagram_grant' => 'true', 'wpw_insta_app_id' => $app_id), $redirect_URL);

        $loginUrl = $this->helper->getLoginUrl($redirect_URL, $this->fbPermissions);

        return apply_filters('wpw_auto_poster_get_insta_login_url', $loginUrl, $this);
    }


    /**
     * Instagram Login URL
     *
     * Getting the login URL from Instagram.
     * Facebook App method
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_insta_app_method_login_url()
    {
        global $wpw_auto_poster_options;
        //load facebook class
        
            $facebook = $this->wpw_auto_poster_load_insta_fb_app_method(WPW_AUTO_POSTER_INSTA_APP_METHOD_ID);

            //check facebook class is exis or not
        if (!$this->facebook) {
            return false;
        }

            $redirect_URL = WPW_AUTO_POSTER_INSTA_APP_REDIRECT_URL;

            $loginUrl = $this->helper->getLoginUrl($redirect_URL, $this->fbPermissions);

            $loginUrl = add_query_arg(array('state' => admin_url('admin.php'),'wpw_auto_poster_insta_app_method_redirect' => admin_url('admin.php'),'wpw_instagram_grant' => 'true', 'wpw_insta_app_id' => WPW_AUTO_POSTER_INSTA_APP_METHOD_ID ), $loginUrl);
            return $loginUrl;
    }

    /**
     * User Data
     *
     * Getting the cached user data from the connected
     * Facebook user (back end).
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_insta_user_data()
    {

        if (!empty($this->_insta_user_cache)) {
            return $this->_insta_user_cache;
        }
    }

    public function wpw_auto_poster_insta_get_groups_tokens()
    {
        // Check facebook class is exis or not
        if (empty($this->facebook)) {
            return false;
        }

        $endpoint = esc_url_raw($this->api_url.'me/groups?access_token='.$this->grantaccessToken.'&limit=1000&offset=0&admin_only=true');

        $headers = array( 'Accept: application/json', 'Content-Type: application/json');

        $response = wp_remote_get($endpoint, array( 'sslverify' => false));
        
        if (is_array($response)) {
            $body = $response['body'];

            if (!empty($body)) {
                $page_response = json_decode($body);

                if (isset($page_response->data) && !empty($page_response->data)) {
                    return $page_response->data;
                }
            }
        }

        return false;
    }

    /**
     * Pages Tokens
     *
     * Getting the the tokens from all pages/accounts which
     * are associated with the connected Facebook account
     * so that the admin chan choose to which page/account
     * he wants to post the submitted and approved reviews to.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_insta_get_pages_tokens()
    {

        // Check facebook class is exis or not
        if (empty($this->facebook)) {
            return false;
        }

        $endpoint = esc_url_raw($this->api_url.'me/accounts?access_token='.$this->grantaccessToken.'&limit=1000&offset=0');

        $headers = array( 'Accept: application/json', 'Content-Type: application/json');

        $response = wp_remote_get($endpoint, array( 'sslverify' => false));

        if (is_array($response)) {
            $body = $response['body'];

            if (!empty($body)) {
                $page_response = json_decode($body);

                if (isset($page_response->data) && !empty($page_response->data)) {
                    return $page_response->data;
                }
            }
        }

        return false;
    }

    /**
     * Fetching Accounts
     *
     * Fetching all the associated accounts from the connected
     * Facebook user (site admin).
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_fb_fetch_accounts()
    {

        global $wpw_auto_poster_options;

        if (!isset($wpw_auto_poster_options['prevent_linked_accounts_access'])) {
            $page_tokens = $this->wpw_auto_poster_insta_get_pages_tokens();
            $page_tokens = !empty($page_tokens) ? $page_tokens : array();

            $group_tokens = $this->wpw_auto_poster_insta_get_groups_tokens();
            $group_tokens = !empty($group_tokens) ? $group_tokens : array();
        } else {
            $page_tokens = array();
            $group_tokens = array();
        }

        $api = array();
        
        // Taking user auth tokens
        if (isset($_GET['wpw_auto_poster_instagram_app_method']) && $_GET['wpw_auto_poster_instagram_app_method'] == 'appmethod') {
            $user_auth_tokens = $this->grantaccessToken;
        } else {
            $user_auth_tokens = $this->grantaccessToken->getValue();
        }

        $wpweb_insta_user_id = get_transient('wpweb_insta_user_id');
        $api['auth_accounts'][$wpweb_insta_user_id] = $this->_insta_user_cache['name'] . " (" . $wpweb_insta_user_id . ")";

        $api['auth_tokens'][$wpweb_insta_user_id] = !empty($user_auth_tokens) ? $user_auth_tokens : '';

        if (!isset($wpw_auto_poster_options['prevent_linked_accounts_access'])) {
            if (!empty($page_tokens)) {
                foreach ($page_tokens as $page_key => $ptk) {
                    if (!isset($ptk->id) || !isset($ptk->access_token)) {
                        continue;
                    }

                    $api['auth_tokens'][$ptk->id] = $ptk->access_token;
                    $api['auth_accounts'][$ptk->id] = $ptk->name;
                }
            }

            //Remove this code due to group posting is not working from fb api 2.4.0 ( SAP V-1.8.0 )
            // Creating user group data if user is administrator of that group
            if (!empty($group_tokens)) {
                foreach ($group_tokens as $gtk) {
                    if (isset($gtk->id)) {
                        if (isset($_GET['wpw_auto_poster_instagram_app_method']) && $_GET['wpw_auto_poster_instagram_app_method'] == 'appmethod') {
                            $api['auth_tokens'][$gtk->id] = $this->grantaccessToken;
                        } else {
                            $api['auth_tokens'][$gtk->id] = $this->grantaccessToken->getValue();
                        }

                        $api['auth_accounts'][$gtk->id] = $gtk->name;
                    }
                }
            }
        }
        
        return $api;
    }

    /**
     * Function to get IG user id from fb page id
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */

    public function wpw_auto_poster_insta_fetch_accounts()
    {

        global $wpw_auto_poster_options;
        $ig_user_data = array();

        if (!isset($wpw_auto_poster_options['prevent_linked_accounts_access'])) {
            $page_tokens = $this->wpw_auto_poster_insta_get_pages_tokens();
            $page_tokens = !empty($page_tokens) ? $page_tokens : array();

            $group_tokens = $this->wpw_auto_poster_insta_get_groups_tokens();
            $group_tokens = !empty($group_tokens) ? $group_tokens : array();
        } else {
            $page_tokens = array();
            $group_tokens = array();
        }

       
        $get_current_fb_user_id = get_transient('wpweb_insta_fb_user_id');
        
        // Taking user auth tokens
        if (isset($_GET['wpw_auto_poster_instagram_app_method']) && $_GET['wpw_auto_poster_instagram_app_method'] == 'appmethod') {
            $user_auth_tokens = $this->grantaccessToken;
        } else {
            $user_auth_tokens = $this->grantaccessToken->getValue();
        }

        if (!isset($wpw_auto_poster_options['prevent_linked_accounts_access'])) {
            if (!empty($page_tokens)) {
                foreach ($page_tokens as $page_key => $ptk) {
                    if (!isset($ptk->id) || !isset($ptk->access_token)) {
                        continue;
                    }

                    $api_url = $this->api_url . $ptk->id . '?fields=instagram_business_account&access_token=' . $ptk->access_token;
        
                    $response = wp_remote_get(
                        $api_url,
                        array(
                            'timeout'     => 120,
                            'httpversion' => '1.1',
                            'sslverify'   => false,
                        )
                    );

                    $response_body = wp_remote_retrieve_body($response);
                    $result_body  = json_decode($response_body);

                    $result = $this->model->wpw_auto_poster_object_to_array($result_body);
                  

                    if (array_key_exists('instagram_business_account', $result)) {
                        $ig_user_id = $result['instagram_business_account']['id'];

                        $insta_pages_api = $this->api_url . $ig_user_id . '/?fields=name,username&access_token=' . $ptk->access_token;

                        $response = wp_remote_get(
                            $insta_pages_api,
                            array(
                                'timeout'     => 120,
                                'httpversion' => '1.1',
                                'sslverify'   => false,
                            )
                        );

                        $response_body = wp_remote_retrieve_body($response);

                        $insta_body = json_decode($response_body);

                        $insta_result = $this->model->wpw_auto_poster_object_to_array($insta_body);

                        $ig_user_data[ $ig_user_id ] = $insta_result['username'];
                    }
                }
            }
        }

        
        return $ig_user_data;
    }


    /**
     * Post to User Wall on Instagram
     *
     * Handles to post user wall on instagram
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_insta_post_to_userwall($post, $auto_posting_type)
    {
        
        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
        // Get stored fb app grant data
        
        $wpw_auto_poster_inta_fb_sess_data = get_option('wpw_auto_poster_insta_sess_data');
        

        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $post_type = $post->post_type; //post type
        $instagram_posting = array();

        //Initialize tags and categories
        $tags_arr = array();
        $cats_arr = array();
    

        // Check facebook grant extended permission is set ot not
        if (!empty($wpw_auto_poster_inta_fb_sess_data)) {
           //posting logs data
            $posting_logs_data = array();
            $unique = 'false';

            $userdata = get_userdata($post->post_author);
            $first_name = $userdata->first_name; //user first name
            $last_name = $userdata->last_name; //user last name
           //published status
            $ispublished = get_post_meta($post->ID, $prefix . 'insta_published_on_insta', true);

            if (isset($wpw_auto_poster_options['insta_post_type_tags']) && !empty($wpw_auto_poster_options['insta_post_type_tags'])) {
                $custom_post_tags = $wpw_auto_poster_options['insta_post_type_tags'];

             
                if (isset($custom_post_tags[$post_type]) && !empty($custom_post_tags[$post_type])) {
                    foreach ($custom_post_tags[$post_type] as $key => $tag) {
                        $term_list = wp_get_post_terms($post->ID, $tag, array("fields" => "names"));
                        

                        foreach ($term_list as $term_single) {
                            $tags_arr[] = str_replace(' ', '', $term_single); // replace space with -
                        }
                    }
                }
            }

            if (isset($wpw_auto_poster_options['insta_post_type_cats']) && !empty($wpw_auto_poster_options['insta_post_type_cats'])) {
                $custom_post_cats = $wpw_auto_poster_options['insta_post_type_cats'];
                if (isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])) {
                    foreach ($custom_post_cats[$post_type] as $key => $category) {
                        $term_list = wp_get_post_terms($post->ID, $category, array("fields" => "names"));
                        foreach ($term_list as $term_single) {
                            $cats_arr[] = str_replace(' ', '', $term_single); // replace space with -
                        }
                    }
                }
            }

            $posttitle = $post->post_title;
           // $customtitle=get_post_meta($post->ID, $prefix . 'fb_custom_title', true);
            $post_content = $post->post_content;
            $post_content = strip_shortcodes($post_content);

            if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                // global custom post msg template for reposter
                $insta_global_custom_message_template = ( isset($wpw_auto_poster_reposter_options["repost_insta_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_reposter_options["repost_insta_global_message_template_" . $post_type] : '';
                $insta_global_custom_msg_options = isset($wpw_auto_poster_reposter_options['repost_insta_custom_msg_options']) ? $wpw_auto_poster_reposter_options['repost_insta_custom_msg_options'] : '';
                // global custom msg template for reposter
                $insta_global_template_text = ( isset($wpw_auto_poster_reposter_options["repost_insta_global_message_template"]) ) ? $wpw_auto_poster_reposter_options["repost_insta_global_message_template"] : '';
            } else {
                $insta_global_custom_message_template = ( isset($wpw_auto_poster_options["insta_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_options["insta_global_message_template_" . $post_type] : '';
                $insta_global_custom_msg_options = isset($wpw_auto_poster_options['insta_custom_msg_options']) ? $wpw_auto_poster_options['insta_custom_msg_options'] : '';
                $insta_global_template_text = (!empty($wpw_auto_poster_options['insta_global_message_template']) ) ? $wpw_auto_poster_options['insta_global_message_template'] : '';
            }
            // if (!empty($customtitle)) {
            //     $customtitle = $customtitle;
            // }

            //custom title set use it otherwise user posttiel
            $title =  $posttitle;
            $postimage = get_post_meta($post->ID, $prefix . 'insta_post_image', true);
            $post_featured_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

            $gallery_images = array();    
            $gallery_images_ids = get_post_meta($post->ID, $prefix . 'instagram_post_gallery', true);
            if( !empty( $gallery_images_ids ) ){
                foreach ( $gallery_images_ids as $key => $image_id ) {
                    $gall_img = wp_get_attachment_image_src( $image_id, 'full');
                    $gall_img = $gall_img[0];
                    $gallery_images[] = $gall_img;
                }
            }

            if( !empty($post_featured_img) ) {
                array_unshift( $gallery_images , $post_featured_img[0] );
            }

            if (isset($postimage['src']) && !empty($postimage['src'])) {
                $postimage = $postimage['src'];
            } elseif (isset($post_featured_img[0]) && !empty($post_featured_img[0])) {
                //check post featrued image is set the use that image
                $postimage = $post_featured_img[0];
            } else {
                //else get post image from settings page
                $postimage = ( $insta_global_custom_msg_options == 'post_msg' && !empty($insta_global_custom_msg_options) ) ? $insta_global_custom_msg_options : $wpw_auto_poster_options['insta_custom_img'];
            }
            $postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage);

            $gallery_images = apply_filters('wpw_insta_social_media_posting_gallery_image', $gallery_images);


            if (!empty($postlink)) {
                $postlink = $postlink;
            } else {
                $postlink = get_the_permalink($post->ID);
            }
            //if custom link is set or not
            $customlink = !empty($postlink) ? 'true' : 'false';
            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, $customlink, 'instagram');

            if (isset($ispublished) && $ispublished == '1') {
                $unique = 'true';
            }

            $description =get_post_meta($post->ID, $prefix . 'insta_custom_title', true);
            $description = !empty($description) ? $description : '';
            $description = apply_filters('wpw_auto_poster_insta_comments', $description, $post);
            if ($insta_global_custom_msg_options == 'post_msg' && !empty($insta_global_custom_message_template) && empty($description)) {
                 $description = $insta_global_custom_message_template;
            } elseif (empty($description) && !empty($insta_global_template_text)) {
                 $description = $insta_global_template_text;
            } elseif (empty($description)) {
                 //get medium posting description
                 $description = $post_content;
            }
            
               // Get post excerpt
               $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );
               // Get post tags
               $tags_arr = apply_filters('wpw_auto_poster_instagram_hashtags', $tags_arr);
               $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';
               
   
               // get post categories
               $cats_arr = apply_filters('wpw_auto_poster_instagram_hashcats', $cats_arr);
               $hashcats = (!empty($cats_arr) ) ? '#' . implode(' #', $cats_arr) : '';

               
               $full_author = normalize_whitespace( $first_name . ' ' . $last_name);
               $nickname_author = get_user_meta($post->post_author, 'nickname', true);
               $search_arr = array('{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}', '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}');
               $replace_arr = array($posttitle, $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option('blogname'), get_option('blogname'), $post_content, $excerpt, $hashtags, $hashcats);
               $code_matches = array();
   
               // check if template tags contains {content-numbers}
            if (preg_match_all('/\{(content)(-)(\d*)\}/', $description, $code_matches)) {
                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $post_content = substr($post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $post_content;
            }
               $cf_matches = array();
               // check if template tags contains {CF-CustomFieldName}
            if (preg_match_all('/\{(CF)(-)(\S*)\}/', $description, $cf_matches)) {
                foreach ($cf_matches[0] as $key => $value) {
                    $cf_tag = $value;
                    $search_arr[] = $cf_tag;
                }
                foreach ($cf_matches[3] as $key => $value) {
                    $cf_name = $value;
                    $tag_value = '';
                    if ($cf_name) {
                        $tag_value = get_post_meta($post->ID, $cf_name, true);
                        if (is_array($tag_value)) {
                            $tag_value = '';
                        }
                    }
                    $replace_arr[] = $tag_value;
                }
            }
   
               $description = str_replace($search_arr, $replace_arr, $description);
               
               // replace title with tag support value
               $search_arr = array('{title}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}', '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}');
               $replace_arr = array($posttitle, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option('blogname'), get_option('blogname'), $post_content, $excerpt, $hashtags, $hashcats);
               // check if template tags contains {content-numbers}
            if (preg_match_all('/\{(content)(-)(\d*)\}/', $title, $code_matches)) {
                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $post_content = substr($post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $post_content;
            }
               // check if template tags contains {CF-CustomFieldName}
            if (preg_match_all('/\{(CF)(-)(\S*)\}/', $title, $cf_matches)) {
                foreach ($cf_matches[0] as $key => $value) {
                    $cf_tag = $value;
                    $search_arr[] = $cf_tag;
                }
                foreach ($cf_matches[3] as $key => $value) {
                    $cf_name = $value;
                    $tag_value = '';
                    if ($cf_name) {
                        $tag_value = get_post_meta($post->ID, $cf_name, true);
                        if (is_array($tag_value)) {
                            $tag_value = '';
                        }
                    }
                    $replace_arr[] = $tag_value;
                }
            }
   
                // replace title with tag support value
               $title = str_replace($search_arr, $replace_arr, $title);
               //Get title
               $title = $this->model->wpw_auto_poster_html_decode($title);
               //use 400 character to post to medium will use as title
               //Get comment
               $comments = $this->model->wpw_auto_poster_html_decode($description);
               $comments = $this->model->wpw_auto_poster_excerpt($comments, 700);
        
               $instant_post_profiles = get_post_meta($post->ID, $prefix . 'insta_user_id');
            if ($post_type == 'wpwsapquickshare') {
                $instant_post_profiles = get_post_meta($post->ID, $prefix . 'insta_user_id', true);
            }

               $categories = wpw_auto_poster_get_post_categories_by_ID($post_type, $post->ID);
               $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');

            if (!empty($categories) && !empty($category_selected_social_acct) && empty($instant_post_profiles)) {
                $insta_clear_cnt = true;
                foreach ($categories as $key => $term_id) {
                    $cat_id = $term_id;
                    if (isset($category_selected_social_acct[$cat_id]['insta']) && !empty($category_selected_social_acct[$cat_id]['insta'])) {
                        if ($insta_clear_cnt) {
                            $instant_post_profiles = array();
                        }
                            $instant_post_profiles = array_merge($instant_post_profiles, $category_selected_social_acct[$cat_id]['insta']);
                            $insta_clear_cnt = false;
                    }
                }
                if (!empty($instant_post_profiles)) {
                    $instant_post_profiles = array_unique($instant_post_profiles);
                }
            }

            if (empty($instant_post_profiles)) {//If profiles are empty in metabox
                $instant_post_profiles = isset($wpw_auto_poster_options['insta_type_' .$post->post_type . '_user']) ? $wpw_auto_poster_options['insta_type_' . $post->post_type . '_user'] : '';
            }

            if (empty($instant_post_profiles)) {
                //record logs for reddit users are not selected
                $this->logs->wpw_auto_poster_add('Instagram: User not selected for posting.');
                if ($post_type == 'wpwsapquickshare') {
                    update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                    update_post_meta($post->ID, $prefix . 'insta_post_status', esc_html__('User not selected for posting.', 'wpwautoposter'));
                }
                sap_add_notice(esc_html__('Instagram: You have not selected any user for the posting.', 'wpwautoposter'), 'error');
                return false;
            } //end if to check user ids are empty

          
            $description = substr($description, 0, 2200);

            $post_status = 'public';
             //posting logs data
             $posting_logs_data = array(
                'title' => $title,
                'link' => $postlink,
                'image' => $postimage,
                'description' => $description
             );

            //initial value of posting flag
             $postflg = false;

             if (!empty($instant_post_profiles)) {
                 foreach ($instant_post_profiles as $account_key => $insta_profile) {
                     $insta_account_id    = $insta_profile;
                     $insta_accounts_data = explode("|", $insta_account_id);
                    
                     $insta_account_id      = !empty($insta_accounts_data['0']) ?  $insta_accounts_data['0'] : '';
                     $fb_main_account_id    = !empty($insta_accounts_data['1']) ?  $insta_accounts_data['1'] : '';

                     if (array_key_exists($fb_main_account_id, $wpw_auto_poster_inta_fb_sess_data)) {
                         $insta_account_details = $wpw_auto_poster_inta_fb_sess_data[$fb_main_account_id]['wpw_auto_poster_insta_user_accounts'][$insta_account_id];
                         $posting_logs_user_details['display_name'] = $insta_account_details;
                         $posting_logs_user_details['id'] = $insta_account_id;

                         $long_access_token = $wpw_auto_poster_inta_fb_sess_data[$fb_main_account_id]['wpw_auto_poster_'.$fb_main_account_id.'_long_access_token']; 
                         $posting_content = strip_tags($description);                           
                         $post_data = array(
                            'description'   => $posting_content
                         );

                         $post_data_carosoul = array(
                            'description'   => $posting_content,
                            'image'         => $gallery_images
                         );
                        
                        /* New Multiple Images (Carousel) Logic */
                        $container_ids = array();   
                        if(!empty($gallery_images) && count($gallery_images) >= 2 ){

                            foreach($gallery_images as $carosoul_image){

                                //fetching the container-id while posting
                                $publish_container_api  = 'https://graph.facebook.com/v3.2/' . $insta_account_id . '/media?is_carousel_item=true&caption=' . urlencode($post_data['description']) . '&access_token=' . $long_access_token .'&image_url=' . $carosoul_image;

                                $response = wp_remote_post(
                                    $publish_container_api,
                                    array(
                                        'timeout'     => 120,
                                        'httpversion' => '1.1',
                                        'sslverify'   => false,
                                    )
                                );
                            
                                $response_body = wp_remote_retrieve_body($response);
                                $container    = json_decode($response_body);
                              
                                if ((isset($container->error) && !empty($container->error))) {
                                    $error_message = $container->error->message;
                                    if ($container->error->code == "36003") {
                                        $error_message = "The image's aspect ratio does not fall within our acceptable range. Advise the app user to try again with an image that falls withing a 4:5 to 1.91:1 range.";
                                    }
    
                                    if( !empty( $container->error->message ) ){
                                        $error_message = $container->error->message;
                                    }
    
                                    if ($post_type == 'wpwsapquickshare') {
                                        update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                                        update_post_meta($post->ID, $prefix . 'insta_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $error_message));
                                    }
                                }
    
                                if (!empty($container->id) && isset($container->id)) {
                                    $container_ids[] = $container->id;
                                }    
                            }
                            
                            if(!empty($container_ids)){
                                $container_ids_combine = implode(',',$container_ids);
                                $create_carousel_container_api  = 'https://graph.facebook.com/v3.2/' . $insta_account_id . '/media?media_type=CAROUSEL&caption=' . urlencode($post_data['description']) . '&access_token=' . $long_access_token.'&children='.$container_ids_combine;
                                $response = wp_remote_post(
                                    $create_carousel_container_api,
                                    array(
                                        'timeout'     => 120,
                                        'httpversion' => '1.1',
                                        'sslverify'   => false,
                                    )
                                );
                            
                                $response_body = wp_remote_retrieve_body($response);
                                $create_carousel_container_api_response  = json_decode($response_body);
    
                                if ((isset($create_carousel_container_api_response->error) && !empty($create_carousel_container_api_response->error))) {
                                    if( !empty( $create_carousel_container_api_response->error->message ) ){
                                        $error_message = $create_carousel_container_api_response->error->message;
                                    }
                                }
    
                                if (!empty($create_carousel_container_api_response->id) && isset($create_carousel_container_api_response->id)) {
                                    $create_carousel_container_id = $create_carousel_container_api_response->id;
                                } 
    
                                //Carosaul Container ID
                                if (!empty($create_carousel_container_id) && isset($create_carousel_container_id)) {
                                    
                                    $posting_api        =  'https://graph.facebook.com/v3.2/'.$insta_account_id.'/media_publish?creation_id=' . $create_carousel_container_id . '&access_token=' . $long_access_token;
                                    $response = wp_remote_post(
                                        $posting_api,
                                        array(
                                            'timeout'     => 120,
                                            'httpversion' => '1.1',
                                            'sslverify'   => false,
                                        )
                                    );
                                
                                    $response_body = wp_remote_retrieve_body($response);
                                    $response_data = json_decode($response_body);

                                    if (!empty($response_data) && !empty($response_data->id)) {
                                        $this->logs->wpw_auto_poster_add('Instagram post data : ' . var_export($post_data_carosoul, true));
                                        $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'insta', $posting_logs_data, $posting_logs_user_details);
                                        $instagram_posting['success'] = 1;
                                    } else {
                                        $errorMessage = $response_data->error->error_user_msg;
                                        $this->logs->wpw_auto_poster_add('Instagram: '.$errorMessage);
                                        if ($post_type == 'wpwsapquickshare') {
                                            update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                                            update_post_meta($post->ID, $prefix . 'insta_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $errorMessage));
                                        }
                                        $instagram_posting['fail'] = 0;
                                    }
                                }
                                else {
                                    $this->logs->wpw_auto_poster_add('Instagram: '.$error_message);
                                    $instagram_posting['fail'] = 0;
                                }
                                
                            }

                             /* New Multiple Images (Carousel) Logic */
                        }else{
                            
                            if(!empty($gallery_images)){

                                //Custom code for fetching the container-id while posting
                                $publish_container_api  = 'https://graph.facebook.com/v3.2/' . $insta_account_id . '/media?image_url=' . $gallery_images[0] . '&caption=' . urlencode($post_data['description']) . '&access_token=' . $long_access_token;
                                $response = wp_remote_post(
                                    $publish_container_api,
                                    array(
                                        'timeout'     => 120,
                                        'httpversion' => '1.1',
                                        'sslverify'   => false,
                                    )
                                );
                            
                                $response_body = wp_remote_retrieve_body($response);
                                $container    = json_decode($response_body);

                                if ((isset($container->error) && !empty($container->error))) {
                                    $error_message = $container->error->error_user_msg;
                                    if ($container->error->code == "36003") {
                                        $error_message = "The image's aspect ratio does not fall within our acceptable range. Advise the app user to try again with an image that falls withing a 4:5 to 1.91:1 range.";
                                    }

                                    if( !empty( $container->error->message ) ){
                                        $error_message = $container->error->message;
                                    }

                                    if ($post_type == 'wpwsapquickshare') {
                                        update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                                        update_post_meta($post->ID, $prefix . 'insta_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $error_message));
                                    }
                                }

                                

                                if (!empty($container->id) && isset($container->id)) {
                                    $container_id = $container->id;
                                    $posting_api        =  'https://graph.facebook.com/v3.2/'.$insta_account_id.'/media_publish?creation_id=' . $container_id . '&access_token=' . $long_access_token;
                                    $response = wp_remote_post(
                                        $posting_api,
                                        array(
                                            'timeout'     => 120,
                                            'httpversion' => '1.1',
                                            'sslverify'   => false,
                                        )
                                    );
                                
                                    $response_body = wp_remote_retrieve_body($response);
                                    $response_data = json_decode($response_body);
                            
                                

                                    if (!empty($response_data) && !empty($response_data->id)) {
                                        $this->logs->wpw_auto_poster_add('Instagram post data : ' . var_export($post_data_carosoul, true));
                                        $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'insta', $posting_logs_data, $posting_logs_user_details);
                                        $instagram_posting['success'] = 1;
                                    } else {
                                        $errorMessage = $response_data->error->error_user_msg;
                                        $this->logs->wpw_auto_poster_add('Instagram: '.$errorMessage);
                                        if ($post_type == 'wpwsapquickshare') {
                                            update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                                            update_post_meta($post->ID, $prefix . 'insta_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $errorMessage));
                                        }
                                        $instagram_posting['fail'] = 0;
                                    }
                                }
                                else {
                                    $this->logs->wpw_auto_poster_add('Instagram: '.$error_message);
                                    $instagram_posting['fail'] = 0;
                                }
                            }else{
                                $this->logs->wpw_auto_poster_add('Instagram post data : ' . var_export($post_data_carosoul, true));
                            }    
                        }  
                        
                     }
                 }
             } else {
                 //record logs when grant extended permission not set
                 $this->logs->wpw_auto_poster_add('Insta error. Session Data not found');
                 // display error notice on post page

                 if ($post_type == 'wpwsapquickshare') {
                     update_post_meta($post->ID, $prefix . 'insta_post_status', 'error');
                     update_post_meta($post->ID, $prefix . 'insta_error', esc_html__('Please select account before posting to the Medium.', 'wpwautoposter'));
                 }
                 sap_add_notice(esc_html__('Instagram: Please select account before posting to the Instagram.', 'wpwautoposter'), 'error');
             }
        }
       
        return $instagram_posting;
    }

    
    /**
     * Reset Sessions
     *
     * Resetting the Instagram sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_insta_reset_session()
    {

        global $wpw_auto_poster_options;

        delete_transient('wpweb_insta_fb_user_id');
        delete_transient('wpweb_insta_fb_user_cache');
        delete_transient('wpweb_insta_fb_user_accounts');

        // Check if facebook reset user link is clicked and fb_reset_user is set to 1 and facebook app id is there
        if (isset($_GET['insta_reset_user']) && $_GET['insta_reset_user'] == '1' && !empty($_GET['wpw_insta_fb_app'])) {
            $wpw_fb_app_id = stripslashes_deep($_GET['wpw_insta_fb_app']);

            // Getting stored fb app data
            $wpw_auto_poster_insta_sess_data = get_option('wpw_auto_poster_insta_sess_data');

            // Getting facebook app users
            $app_users = wpw_auto_poster_get_fb_accounts('all_app_users');

            // Users need to flush from stored data
            $reset_app_users = !empty($app_users[$wpw_fb_app_id]) ? $app_users[$wpw_fb_app_id] : array();

            // Unset perticular app value data and update the option
            if (isset($wpw_auto_poster_insta_sess_data[$wpw_fb_app_id])) {
                unset($wpw_auto_poster_insta_sess_data[$wpw_fb_app_id]);
                update_option('wpw_auto_poster_insta_sess_data', $wpw_auto_poster_insta_sess_data);
            }

            // Get all post type
            $all_post_types = get_post_types(array('public' => true), 'objects');
            $all_post_types = is_array($all_post_types) ? $all_post_types : array();

            // Unset users from settings page
            foreach ($all_post_types as $posttype) {
                //check postype is not object
                if (!is_object($posttype)) {
                    continue;
                }

                if (isset($posttype->labels)) {
                    $label = $posttype->labels->name ? $posttype->labels->name : $posttype->name;
                } else {
                    $label = $posttype->name;
                }
                
                if ($label == 'Media' || $label == 'media') {
                    continue; // skip media
                }


                // Check if user is set for posting in settings page then unset it
                if (isset($wpw_auto_poster_options['insta_type_' . $posttype->name . '_user'])) {
                    // Get stored facebook users according to post type
                    $fb_stored_users = $wpw_auto_poster_options['insta_type_' . $posttype->name . '_user'];

                    // Flusing the App users and taking remaining
                    $new_stored_users = array_diff($fb_stored_users, $reset_app_users);

                    // If empty data then unset option else update remaining
                    if (!empty($new_stored_users)) {
                        $wpw_auto_poster_options['insta_type_' . $posttype->name . '_user'] = $new_stored_users;
                    } else {
                        unset($wpw_auto_poster_options['insta_type_' . $posttype->name . '_user']);
                    }
                } //end if
            } //end foreach

            // Update autoposter options to settings
            update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
        } //end if
    }

    /**
     * Instagram Posting
     *
     * Handles to instagram posting
     * by post data
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_insta_posting($post, $auto_posting_type = '')
    {

        global $wpw_auto_poster_options;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //post to user wall on instagram
        $res = $this->wpw_auto_poster_insta_post_to_userwall($post, $auto_posting_type);

       

        if (isset($res['success']) && !empty($res['success'])) { 

            //check post has been posted on instagram or not
            //record logs for posting done on instagram
            $this->logs->wpw_auto_poster_add('Instagram posting completed successfully.');

            update_post_meta($post->ID, $prefix . 'insta_published_on_insta', '1');

            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time('timestamp');
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            return true;
        }

        return false;
    }
}
