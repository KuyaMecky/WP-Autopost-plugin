<?php


// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

require_once __DIR__ . '/libraries/gmb/vendor/autoload.php';    

/**
 * Google My Business cookie method Posting Class
 * 
 * Handles functions to get and posting to user account, pages and groups
 * 
 * @package Social Auto Poster
 * @since 3.0.7
 */
class Wpw_Auto_Poster_GMB_Posting {

    public $message, $model, $logs, $mybusiness;

    public function __construct() {
        global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;

        $this->message = $wpw_auto_poster_message_stack;
        $this->model = $wpw_auto_poster_model;
        $this->logs = $wpw_auto_poster_logs;
        
        $param = array(
            'client_id' => WPW_AUTO_POSTER_GMB_APP_CLIENT_ID,
            'client_secret' => WPW_AUTO_POSTER_GMB_APP_CLIENT_SECRET,
            'redirect_uri' => WPW_AUTO_POSTER_GMB_REDIRECT_URL,
            'scope' => WPW_AUTO_POSTER_GMB_APP_SCOPE
        );
        
        $myBusiness = new Google_my_business($param);
        $this->mybusiness = $myBusiness;
        //initialize the session value when data is saved in database
        add_action('init', array($this, 'wpw_auto_poster_gmb_initialize'));
    }

    /**
     * Google My Business Login URL link
     * 
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_gmb_app_method_login_url() {
        $state = admin_url('admin.php');
        return $this->mybusiness->gmb_login($state);
    }

    /**
     * Assign Google My Business User's all Data to session
     * 
     * Handles to assign user's google my business data
     * to sessoin & save to database
     * 
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_gmb_initialize() {
        global $wpw_auto_poster_options, $wpw_auto_poster_message_stack;
        $wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');
        $user_accounts = array();
        $gmb_sess_data = array();
        if (isset($_GET['code']) && isset($_GET['wpw_auto_poster_gmb_verification']) && $_GET['wpw_auto_poster_gmb_verification'] == 'true') {
            $access_token = $this->mybusiness->get_access_token(sanitize_text_field($_GET['code']));
            $refresh_token = $access_token['refresh_token'];
            if (!empty($access_token)) {
                $access_token = $this->mybusiness->get_exchange_token($access_token['refresh_token']);

                if ($access_token['access_token'] != '') {
                    $accounts = $this->mybusiness->get_accounts($access_token['access_token']);


                    if (isset($accounts['accounts']) && count($accounts['accounts']) > 0) {
                        $accountID = explode("/", $accounts['accounts'][0]['name']);
                        $user_accounts['auth_accounts'][$accountID[1]] = $accounts['accounts'][0]['name'];
                        $user_accounts['details'][$accountID[1]] = array(
                            'name' => $accounts['accounts'][0]['name'],
                            'display_name' => $accounts['accounts'][0]['accountName'],
                            'accountid' => $accountID[1],
                            'refresh_token' => $refresh_token,
                            'driver' => 'gmb',
                            'account_name' => $accounts['accounts'][0]['accountName'],
                        );
                        $locations = $this->mybusiness->get_locations($accounts['accounts'][0]['name'], $access_token['access_token']);

                       

                        $location_verified_status = false;
                        if (!empty($locations)) {
                            foreach ($locations['locations'] as $key => $value) {
                                if (isset($value['metadata']['hasVoiceOfMerchant']) && $value['metadata']['hasVoiceOfMerchant'] == '1' ) {
                                    $locationID = explode("/", $value['name']);
                                    $user_accounts[$accountID[1]][] = array(
                                        'id' => $locationID[1],
                                        'name' => $value['title'],
                                        'category' => isset($value['primaryCategory']['displayName']) ? $value['primaryCategory']['displayName'] : '',
                                        'refresh_token' => $refresh_token,
                                        'locationname' => $value['name'],
                                    );
                                    $location_verified_status = true;
                                }
                            }
                            if (!$location_verified_status) {
                                $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'wpw_auto_poster_gmb_verification' => 'false#wpw-auto-poster-gmb-api'), admin_url('admin.php'));
                                $this->logs->wpw_auto_poster_add('Google My Business Exception : Your location is not verified for ' . $accounts['accounts'][0]['accountName'] . ' account.');
                                $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'googlemybusiness');
                                wp_redirect($redirect_url);
                                exit;
                            }


                            $gmb_sess_data[$accountID[1]] = array(
                                'wpw_auto_poster_gmb_user_id' => $accountID[1],                                
                                'wpw_auto_poster_gmb_user_accounts' => $user_accounts,
                            );
                            if (!empty($wpw_auto_poster_gmb_sess_data)) {
                                $gmb_sess_data = array_merge($wpw_auto_poster_gmb_sess_data, $gmb_sess_data);
                            } else {
                                $gmb_sess_data = $gmb_sess_data;
                            }

                            update_option('wpw_auto_poster_gmb_sess_data', $gmb_sess_data);
                            
                            $this->logs->wpw_auto_poster_add('Google My Business : Your location is added successfully for ' . $accounts['accounts'][0]['accountName'] . ' account.');
                        }
                        
                        $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'gmb_verification' => 'true#wpw-auto-poster-gmb-api'), admin_url('admin.php'));
                        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'googlemybusiness');
                        wp_redirect($redirect_url);
                        exit;
                    }
                }
            } else {
                $this->logs->wpw_auto_poster_add('Google My Business Exception : Access token is empty.');
                return false;
            }
        }
    }

    /**
     * Code for posting on Google My Business
     * 
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_gmb_posting($post, $auto_posting_type = '') {
        global $wpw_auto_poster_options;
        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $res = $this->wpw_auto_poster_post_to_gmb($post, $auto_posting_type);
        if (isset($res['success']) && !empty($res['success'])) {
            //record logs for posting done on google my business
            $this->logs->wpw_auto_poster_add('Google My Business posting completed successfully.');
            update_post_meta($post->ID, $prefix . 'gmb_published_on_posts', '1');
            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time('timestamp');
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            return true;
        }
        return false;
    }

    /**
     * Post To Google My Business
     * 
     * Handles to Post on Google My Business account
     * 
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_post_to_gmb($post, $auto_posting_type) {
        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
        $wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');
        
        
        

        //metabox field prefix
        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $post_type = $post->post_type; //post type
        $gmb_posting = array();
        //Initialize tags and categories
        $tags_arr = array();
        $cats_arr = array();
        // Getting all location apps
        $gmb_apps = wpw_auto_poster_get_gmb_accounts_location();
        //check google my business authorized session is true or not
        //need to do for google my business posting code
        if (!empty($wpw_auto_poster_gmb_sess_data)) {
            //posting logs data
            $posting_logs_data = array();
            $unique = 'false';
            //user data
            $userdata = get_userdata($post->post_author);
            $first_name = $userdata->first_name; //user first name
            $last_name = $userdata->last_name; //user last name
            //published status
            $ispublished = get_post_meta($post->ID, $prefix . 'gmb_published_on_posts', true);
            // Get all selected tags for selected post type for hashtags support
            if (isset($wpw_auto_poster_options['gmb_post_type_tags']) && !empty($wpw_auto_poster_options['gmb_post_type_tags'])) {
                $custom_post_tags = $wpw_auto_poster_options['gmb_post_type_tags'];
                if (isset($custom_post_tags[$post_type]) && !empty($custom_post_tags[$post_type])) {
                    foreach ($custom_post_tags[$post_type] as $key => $tag) {
                        $term_list = wp_get_post_terms($post->ID, $tag, array("fields" => "names"));
                        foreach ($term_list as $term_single) {
                            $tags_arr[] = str_replace(' ', '', $term_single);
                        }
                    }
                }
            }
            // Get all selected categories for selected post type for hashcats support
            if (isset($wpw_auto_poster_options['gmb_post_type_cats']) && !empty($wpw_auto_poster_options['gmb_post_type_cats'])) {
                $custom_post_cats = $wpw_auto_poster_options['gmb_post_type_cats'];
                if (isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])) {
                    foreach ($custom_post_cats[$post_type] as $key => $category) {
                        $term_list = wp_get_post_terms($post->ID, $category, array("fields" => "names"));
                        foreach ($term_list as $term_single) {
                            $cats_arr[] = str_replace(' ', '', $term_single);
                        }
                    }
                }
            }
            //post title
            $posttitle = $post->post_title;
            $post_content = $post->post_content;
            $post_content = strip_shortcodes($post_content);
            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);
            //custom title from metabox
            $customtitle = $this->model->wpw_auto_poster_stripslashes_deep($posttitle);
            // custom title from custom post type message
            if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                // global custom post msg template for reposter
                $gmb_global_custom_message_template = ( isset($wpw_auto_poster_reposter_options["repost_gmb_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_reposter_options["repost_gmb_global_message_template_" . $post_type] : '';
                $gmb_global_custom_msg_options = isset($wpw_auto_poster_reposter_options['repost_gmb_custom_msg_options']) ? $wpw_auto_poster_reposter_options['repost_gmb_custom_msg_options'] : '';
                // global custom msg template for reposter
                $gmb_global_template_text = ( isset($wpw_auto_poster_reposter_options["repost_gmb_global_message_template"]) ) ? $wpw_auto_poster_reposter_options["repost_gmb_global_message_template"] : '';
            } else {
                $gmb_global_custom_message_template = ( isset($wpw_auto_poster_options["gmb_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_options["gmb_global_message_template_" . $post_type] : '';
                $gmb_global_custom_msg_options = isset($wpw_auto_poster_options['gmb_custom_msg_options']) ? $wpw_auto_poster_options['gmb_custom_msg_options'] : '';
                $gmb_global_template_text = (!empty($wpw_auto_poster_options['gmb_global_message_template']) ) ? $wpw_auto_poster_options['gmb_global_message_template'] : '';
            }
            if (!empty($customtitle)) {
                $customtitle = $customtitle;
            }

            //custom title set use it otherwise user posttiel
            $title = !empty($customtitle) ? $customtitle : $post_content;
            //post image
            $postimage = get_post_meta($post->ID, $prefix . 'gmb_post_image', true);
            $gmb_add_buttons = get_post_meta($post->ID, $prefix . 'gmb_add_buttons', true);
            /*             * ************
             * Image Priority
             * If metabox image set then take from metabox
             * If metabox image is not set then take from featured image
             * If featured image is not set then take from settings page
             * ************ */
            //get featured image from post / page / custom post type
            $post_featured_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            // global custom post img
            $gmb_custom_post_img = ( isset($wpw_auto_poster_options["gmb_post_image_" . $post_type]) ) ? $wpw_auto_poster_options["gmb_post_image_" . $post_type] : '';
            $gmb_global_custom_msg_options = isset($wpw_auto_poster_options['gmb_custom_msg_options']) ? $wpw_auto_poster_options['gmb_custom_msg_options'] : '';
            //check custom image is set in meta and not empty
            if (isset($postimage['src']) && !empty($postimage['src'])) {
                $postimage = $postimage['src'];
            } elseif (isset($post_featured_img[0]) && !empty($post_featured_img[0])) {
                //check post featrued image is set the use that image
                $postimage = $post_featured_img[0];
            } else {
                //else get post image from settings page
                $postimage = ( $gmb_global_custom_msg_options == 'post_msg' && !empty($gmb_custom_post_img) ) ? $gmb_custom_post_img : $wpw_auto_poster_options['gmb_post_image'];
            }
            $postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage);
            $gmb_custom_gmb_add_buttons = 'LEARN_MORE';
            //  If quick share
            if( $post_type != 'wpwsapquickshare' ){
                $gmb_custom_gmb_add_buttons = ( isset($wpw_auto_poster_options["gmb_add_buttons_" . $post_type]) ) ? $wpw_auto_poster_options["gmb_add_buttons_" . $post_type] : '';
            }
            
            //check custom button type is set in meta and not empty
            if (isset($gmb_add_buttons) && $gmb_add_buttons == '') {
                if ($gmb_global_custom_msg_options == 'post_msg' && !empty($gmb_custom_gmb_add_buttons)) {
                    $button_type = $gmb_custom_gmb_add_buttons;
                } else {
                    $button_type = $wpw_auto_poster_options['gmb_add_buttons'];
                }
            } else {
                $button_type = $gmb_add_buttons;
            }
            //post link
            $postlink = get_the_permalink($post->ID);
            $postlink = isset($postlink) ? $postlink : '';

            $qs_link   = get_post_meta( $post->ID, $prefix . 'share_link', true );
            
            if( !empty( $qs_link ) ) {
                $postlink = $qs_link;
            }
            
            //if custom link is set or not
            $customlink = !empty($postlink) ? 'true' : 'false';

            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, $customlink, 'gmb');

            // not sure why this code here it should be above $postlink but lets keep it here
            //if post is published on googlemybusiness once then change url to prevent duplication
            if (isset($ispublished) && $ispublished == '1') {
                $unique = 'true';
            }
            //comments
            $description = get_post_meta($post->ID, $prefix . 'gmb_custom_status_msg', true);
            $description = !empty($description) ? $description : '';
            $description = apply_filters('wpw_auto_poster_gmb_comments', $description, $post);

            if ($gmb_global_custom_msg_options == 'post_msg' && !empty($gmb_global_custom_message_template) && empty($description)) {
                $description = $gmb_global_custom_message_template;
            } elseif (empty($description) && !empty($gmb_global_template_text)) {
                $description = $gmb_global_template_text;
            } elseif (empty($description)) {
                //get gmb posting description
                $description = $posttitle;
            }
            // Get post excerpt
            $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );
            // Get post tags
            $tags_arr = apply_filters('wpw_auto_poster_gmb_hashtags', $tags_arr);
            $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';
            // get post categories
            $cats_arr = apply_filters('wpw_auto_poster_gmb_hashcats', $cats_arr);
            $hashcats = (!empty($cats_arr) ) ? '#' . implode(' #', $cats_arr) : '';
            $full_author = normalize_whitespace( $first_name . ' ' . $last_name );
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
            $description = $this->model->wpw_auto_poster_stripslashes_deep($description);
            $description = $this->model->wpw_auto_poster_html_decode($description);
            // replace title with tag support value                 
            $search_arr = array('{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}', '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}');
            $replace_arr = array($posttitle, $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option('blogname'), get_option('blogname'), $post_content, $excerpt, $hashtags, $hashcats);
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
            //use 400 character to post to googlemybusiness will use as title
            $description = $this->model->wpw_auto_poster_excerpt($description, 400);
            //Get comment
            $comments = $this->model->wpw_auto_poster_html_decode($description);
            $comments = $this->model->wpw_auto_poster_excerpt($comments, 700);
            //Linkedin Profile Data from setting //_wpweb_li_post_profile
            $gmb_post_profiles = get_post_meta($post->ID, $prefix . 'gmb_user_id');
            
            if( $post_type == 'wpwsapquickshare'){
                $gmb_post_profiles = get_post_meta($post->ID, $prefix . 'gmb_user_id',true);
            }

            /*             * ***** Code to posting to selected category Google My Business account ***** */
            // get all categories for custom post type
            $categories = wpw_auto_poster_get_post_categories_by_ID($post_type, $post->ID);
            // Get all selected account list from category
            $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');
            // IF category selected and category social account data found
            if (!empty($categories) && !empty($category_selected_social_acct) && empty($gmb_post_profiles)) {
                $gmb_clear_cnt = true;
                foreach ($categories as $key => $term_id) {
                    $cat_id = $term_id;
                    if (isset($category_selected_social_acct[$cat_id]['gmb']) && !empty($category_selected_social_acct[$cat_id]['gmb'])) {
                        if ($gmb_clear_cnt)
                            $gmb_post_profiles = array();
                        $gmb_post_profiles = array_merge($gmb_post_profiles, $category_selected_social_acct[$cat_id]['gmb']);
                        $gmb_clear_cnt = false;
                    }
                }
                if (!empty($gmb_post_profiles)) {
                    $gmb_post_profiles = array_unique($gmb_post_profiles);
                }
            }

            if (empty($gmb_post_profiles)) {//If profiles are empty in metabox
                $gmb_post_profiles = isset($wpw_auto_poster_options['gmb_type_' . $post->post_type . '_user']) ? $wpw_auto_poster_options['gmb_type_' . $post->post_type . '_user'] : '';
            }

            if (empty($gmb_post_profiles)) {
                //record logs for google my business users are not selected
                $this->logs->wpw_auto_poster_add('Google My Business error: User not selected for posting.');
                sap_add_notice(esc_html__('Google My Business: You have not selected any user for the posting.', 'wpwautoposter'), 'error');
                if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'gmb_post_status','error');
                    update_post_meta($post->ID, $prefix . 'gmb_error', esc_html__('You have not selected any user for the posting.', 'wpwautoposter' ));
                }
                return false;
            } //end if to check user ids are empty
            $content = array(
                'title' => $title,
                'submitted-url' => $postlink,
                'comment' => $comments,
                'submitted-image-url' => $postimage,
                'description' => $description
            );
            //posting logs data
            $posting_logs_data = array(
                'title' => $title,
                'link' => $postlink,
                'image' => $postimage,
                'description' => $description
            );


            $this->logs->wpw_auto_poster_add('Google My Business post data : ' . var_export($content, true));
            //initial value of posting flag
            $postflg = false;

            if (!empty($gmb_post_profiles)) {
                foreach ($gmb_post_profiles as $gmb_post_profile) {
                    //Initilize log user details
                    $posting_logs_user_details = array();
                    $proxy = '';
                    $gmb_users_id = $gmb_post_profile;
                    $gmb_users_id_array = explode("/", $gmb_post_profile);
                   

                    $link_button_text = $button_type;
                    

                    //$allLocations = $wpw_auto_poster_gmb_sess_data[$gmb_users_id_array[1]]['wpw_auto_poster_gmb_user_accounts'][$gmb_users_id_array[1]];

                    $wpw_auto_poster_gmb_sess_accounts  = array_keys($wpw_auto_poster_gmb_sess_data);

                    $posting_account = $this->sap_get_fb_account_from_localtion($gmb_users_id);


                    $allLocations = $wpw_auto_poster_gmb_sess_data[
                            $wpw_auto_poster_gmb_sess_data[$posting_account]['wpw_auto_poster_gmb_user_id']]['wpw_auto_poster_gmb_user_accounts'][$wpw_auto_poster_gmb_sess_data[$posting_account]['wpw_auto_poster_gmb_user_id']];

                  
                    
                    $locIDs = array_column( $allLocations, 'id' );

                    $gmb_array_key = array_search( $gmb_users_id_array[1], $locIDs );
                   
                    $gmb_array = $allLocations[$gmb_array_key];
                    
                    /* To fix compatiblity issue with older version datarecords and new version datarecords */
                    if (is_array($gmb_array) && !isset($gmb_array[0])) {
                        $gmb_array = array('0' => $gmb_array);
                    }
                    

                    /* To fix compatiblity issue with older version datarecords and new version datarecords */                 
                    if (!empty($gmb_array)) {

                        foreach ($gmb_array as $send_gmb) {


                            $posting_logs_user_details['display_name'] = $send_gmb['name'];
                            $posting_logs_user_details['id'] = $send_gmb['id'];
                            $refresh_token = $send_gmb['refresh_token'];                          

                            $access_token = $this->mybusiness->get_exchange_token($refresh_token);
                            if (!empty($access_token) && $access_token['access_token'] != '') {
                                if (isset($gmb_users_id) && !empty($gmb_users_id)) {                                    
                                    $post_data = array(
                                        'topicType' => "STANDARD",
                                        'languageCode' => "en_US",
                                        'summary' => $content['description'],
                                        'callToAction' => array(
                                            'actionType' => $link_button_text,
                                            'url' => $content['submitted-url'],
                                        ),
                                        'media' => array(
                                            'mediaFormat' => 'PHOTO',
                                            'sourceUrl' => $content['submitted-image-url'],
                                        ),
                                       'name' => $content['title'],
                                    );
                                    
                                    $response = $this->mybusiness->post_local_post('accounts/'.$posting_account.'/'.$gmb_users_id . '/localPosts', $access_token['access_token'], $post_data); 

                                    
                                    $state = isset( $response['state'] ) ? $response['state'] : '';

                                    if (!empty($response) && ($state == 'LIVE' || $state == 'PROCESSING' )) {
                                        //posting logs store into database
                                        $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'gmb', $posting_logs_data, $posting_logs_user_details);
                                        if( $post_type == 'wpwsapquickshare'){
                                            update_post_meta($post->ID, $prefix . 'gmb_post_status','success');
                                        }
                                        $postflg = true;
                                        $gmb_posting['success'] = 1;
                                    } else {
                                        if ($response['error']['details'][0]['errorDetails'][0]['field'] == 'photos.additional_photo_urls') {
                                            $postflg = false;
                                            $gmb_posting['fail'] = 0;
                                            $this->logs->wpw_auto_poster_add('Google My Business error: ' . $response['error']['details'][0]['errorDetails'][0]['message']);
                                            if( $post_type == 'wpwsapquickshare'){
                                                update_post_meta($post->ID, $prefix . 'gmb_post_status','error');
                                                update_post_meta($post->ID, $prefix . 'gmb_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $response['error']['details'][0]['errorDetails'][0]['message']) );
                                            }

                                            // display error notice on post page
                                            sap_add_notice(sprintf(esc_html__('Google My Business: Something was wrong while posting %s', 'wpwautoposter'), $response['error']['details'][0]['errorDetails'][0]['message']), 'error');
                                        } else {
                                            $postflg = false;
                                            $gmb_posting['fail'] = 0;
                                            $this->logs->wpw_auto_poster_add('Google My Business error: ' . $response['error']['message']);

                                            if( $post_type == 'wpwsapquickshare'){
                                                update_post_meta($post->ID, $prefix . 'gmb_post_status','error');
                                                update_post_meta($post->ID, $prefix . 'gmb_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $response['error']['message']) );
                                            }

                                            // display error notice on post page
                                            sap_add_notice(sprintf(esc_html__('Google My Business: Something was wrong while posting %s', 'wpwautoposter'), $response['error']['message']), 'error');
                                        }
                                    }
                                }
                            } else {
                                $postflg = false;
                                $gmb_posting['fail'] = 0;
                                $this->logs->wpw_auto_poster_add('Google My Business error: Your Access Token is expire.');
                                if( $post_type == 'wpwsapquickshare'){
                                    update_post_meta($post->ID, $prefix . 'gmb_post_status','error');
                                    update_post_meta($post->ID, $prefix . 'gmb_error', esc_html__('Your Access Token is expire.', 'wpwautoposter') );
                                }
                            }
                        }
                    }
                }
            }
        } else {
            //record logs when grant extended permission not set
            $this->logs->wpw_auto_poster_add('Google My Business error. Session Data not found');
            // display error notice on post page
            sap_add_notice(esc_html__('Google My Business: Please select location before posting to the Google My Business.', 'wpwautoposter'), 'error');
            if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'gmb_post_status','error');
                update_post_meta($post->ID, $prefix . 'gmb_error', esc_html__('Please select location before posting to the Google My Business.', 'wpwautoposter') );
            }
        }
        return $gmb_posting;
    }



    public function sap_get_fb_account_from_localtion( $location_id ){
        $accountid = ''; 
        
        $sap_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');


        if( !empty( $sap_gmb_sess_data )){
            foreach ($sap_gmb_sess_data as $account_id => $accounts_data) {

                if( isset( $accounts_data['sap_gmb_user_accounts'][$account_id] )){
                    foreach ($accounts_data['sap_gmb_user_accounts'][$account_id] as $key => $location) {

                        if($location_id ==  $location['locationname']){
                            $accountid = $account_id;
                        }                        
                    }
                }
            }
        }       

        return $account_id;
        
    }

    /**
     * Reset Sessions
     *
     * Resetting the Google My Business sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_gmb_reset_session() {
        // Check if google my business reset user link is clicked and gmb_reset_user is set to 1 and google my business user id is there
        if (isset($_GET['gmb_reset_user']) && $_GET['gmb_reset_user'] == '1' && !empty($_GET['wpw_gmb_userid'])) {
            $wpw_gmb_app_id = sanitize_text_field($_GET['wpw_gmb_userid']);
            // Getting stored li app data
            $wpw_auto_poster_gmb_sess_data = get_option('wpw_auto_poster_gmb_sess_data');
            // Unset particular app value data and update the option
            if (isset($wpw_auto_poster_gmb_sess_data[$wpw_gmb_app_id])) {
                unset($wpw_auto_poster_gmb_sess_data[$wpw_gmb_app_id]);
                update_option('wpw_auto_poster_gmb_sess_data', $wpw_auto_poster_gmb_sess_data);
            }
        }
        /*         * ***** Code for selected category Google My Business account ***** */
        // unset selected Google My Business account option for category 
        $cat_selected_social_acc = array();
        $cat_selected_acc = get_option('wpw_auto_poster_category_posting_acct');
        $cat_selected_social_acc = (!empty($cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;
        if (!empty($cat_selected_social_acc)) {
            foreach ($cat_selected_social_acc as $cat_id => $cat_social_acc) {
                if (isset($cat_social_acc['gmb'])) {
                    unset($cat_selected_acc[$cat_id]['gmb']);
                }
            }
            // Update autoposter category GMB posting account options
            update_option('wpw_auto_poster_category_posting_acct', $cat_selected_acc);
        }
    }

}

?>