<?php
/*
 * Reference Link : https://github.com/jonathantorres/medium-sdk-php
*/
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

require_once __DIR__ . '/libraries/medium/vendor/autoload.php';
use JonathanTorres\MediumSdk\Medium;

/**
 * Medium method Posting Class
 *
 * Handles functions to get and posting to user account, pages and groups
 *
 * @package Social Auto Poster
 * @since 3.8.2
 */

class Wpw_Auto_Poster_Medium_Posting {

    public $message, $model, $logs, $mymedium;

    public function __construct() {

        global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;

        $this->message = $wpw_auto_poster_message_stack;
        $this->model = $wpw_auto_poster_model;
        $this->logs = $wpw_auto_poster_logs;

        $param = array(
            'client-id'     => WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_ID,
            'client-secret' => WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_SECRET,
            'redirect-url'  => WPW_AUTO_POSTER_MEDIUM_REDIRECT_URL,
            'state'         => admin_url('admin.php'),
            'scopes'        => WPW_AUTO_POSTER_MEDIUM_APP_SCOPE
        );


        $myMedium = new Medium($param);
        $this->mymedium = $myMedium;

        //initialize the session value when data is saved in database*/
        add_action('init', array($this, 'wpw_auto_poster_medium_initialize'));

    }

    /**
     * Post To Medium
     *
     * Code for posting on Medium
     *
     * @package Social Auto Poster
     * @since 3.8.2
     */

    public function wpw_auto_poster_medium_posting($post, $auto_posting_type = '') {

        global $wpw_auto_poster_options;
        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $res = $this->wpw_auto_poster_post_to_medium($post, $auto_posting_type);
        if (isset($res['success']) && !empty($res['success'])) {

            //record logs for posting done on Medium my business
            $this->logs->wpw_auto_poster_add('Medium Posting completed successfully');
            update_post_meta($post->ID, $prefix . 'medium_published_on_posts', '1');
            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time('timestamp');
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            return true;
        }
        return false;
    }

    /**
     * Post To Medium
     *
     * Code handles logic for posting on Medium
     *
     * @package Social Auto Poster
     * @since 3.8.2
     */
    public function wpw_auto_poster_post_to_medium($post, $auto_posting_type) {

        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
        $wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');

        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $post_type = $post->post_type; //post type
        $post_type_new = $post->post_type;
        $medium_posting = array();

        //Initialize tags and categories
        $tags_arr = array();
        $cats_arr = array();

        // Getting all location apps
        $medium_accounts = wpw_auto_poster_get_medium_accounts_with_publications();

        if(!empty($wpw_auto_poster_medium_sess_data)) {

             //posting logs data
             $posting_logs_data = array();
             $unique = 'false';
             //user data
             $userdata = get_userdata($post->post_author);
             $first_name = $userdata->first_name; //user first name
             $last_name = $userdata->last_name; //user last name
             //published status
             $ispublished = get_post_meta($post->ID, $prefix . 'medium_published_on_posts', true);
             // Get all selected tags for selected post type for hashtags support
             if(isset($wpw_auto_poster_options['medium_post_type_tags']) && !empty($wpw_auto_poster_options['medium_post_type_tags'])) {
                $custom_post_tags = $wpw_auto_poster_options['medium_post_type_tags'];
                foreach($custom_post_tags as $key => $value) {

                    $post_details = explode("|",$value);
                    $post_type  = $post_details[0];
                    $post_tag  = $post_details[1];
                    if (isset($post_type) && !empty($post_type)) {

                        $term_list = wp_get_post_terms($post->ID, $post_tag, array("fields" => "names"));
                        foreach ($term_list as $term_single) {
                            $tags_arr[] = str_replace(' ', '', $term_single);
                        }

                    }

                }
             }

             if(isset($wpw_auto_poster_options['medium_post_type_cats']) && !empty($wpw_auto_poster_options['medium_post_type_cats'])) {

                 $custom_post_cats = $wpw_auto_poster_options['medium_post_type_cats'];
                 foreach($custom_post_cats as $key => $value) {

                   $post_details = explode("|",$value);
                   $post_type  = $post_details[0];
                   $post_cat  = $post_details[1];
                   if (isset($post_type) && !empty($post_type)) {

                     $term_list = wp_get_post_terms($post->ID,$post_cat, array("fields" => "names"));
                     foreach ($term_list as $term_single) {
                         $cats_arr[] = str_replace(' ', '', $term_single);
                     }

                   }

                 }
            }       

            //post title
            $posttitle = $post->post_title;
            $customtitle = get_post_meta( $post->ID, $prefix . 'medium_post_title', true );
            $post_content = $post->post_content;
            $post_content = strip_shortcodes($post_content); 

            //strip html kses and tags
            //decode html entity
            //custom title from metabox
            // custom title from custom post type message

            if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                // global custom post msg template for reposter
                $medium_global_custom_message_template = ( isset($wpw_auto_poster_reposter_options["repost_medium_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_reposter_options["repost_medium_global_message_template_" . $post_type] : '';
                $medium_global_custom_msg_options = isset($wpw_auto_poster_reposter_options['repost_medium_custom_msg_options']) ? $wpw_auto_poster_reposter_options['repost_medium_custom_msg_options'] : '';
                // global custom msg template for reposter
                $medium_global_template_text = ( isset($wpw_auto_poster_reposter_options["repost_medium_global_message_template"]) ) ? $wpw_auto_poster_reposter_options["repost_medium_global_message_template"] : '';
            } else {
                $medium_global_custom_message_template = ( isset($wpw_auto_poster_options["medium_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_options["medium_global_message_template_" . $post_type] : '';
                $medium_global_custom_msg_options = isset($wpw_auto_poster_options['medium_custom_msg_options']) ? $wpw_auto_poster_options['medium_custom_msg_options'] : '';
                $medium_global_template_text = (!empty($wpw_auto_poster_options['medium_global_message_template']) ) ? $wpw_auto_poster_options['medium_global_message_template'] : '';
            }
            if (!empty($customtitle)) {
                $customtitle = $customtitle;
            }else{
                if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                    // Fetch the title from general setting
                    $customtitle = ( !empty( $wpw_auto_poster_reposter_options['repost_medium_global_title_template'] ) ) ? $wpw_auto_poster_reposter_options['repost_medium_global_title_template'] : '';				
                }else{
                    $customtitle = ( !empty( $wpw_auto_poster_options['medium_global_title_template'] ) ) ? $wpw_auto_poster_options['medium_global_title_template'] : '';
                }    
                    
            }

            //custom title set use it otherwise user posttiel
            $title = !empty($customtitle) ? $customtitle : $posttitle;

            if (!empty($postlink)) {
                $postlink = $postlink;
            }else{
                $postlink = get_the_permalink($post->ID);
            }
            //if custom link is set or not
            $customlink = !empty($postlink) ? 'true' : 'false';
            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, $customlink, 'medium');

            // not sure why this code here it should be above $postlink but lets keep it here
            //if post is published on medium once then change url to prevent duplication
            if (isset($ispublished) && $ispublished == '1') {
                $unique = 'true';
            }

             //comments
             $description = get_post_meta($post->ID, $prefix . 'medium_post_desc', true);
             $description = !empty($description) ? $description : '';
             $description = apply_filters('wpw_auto_poster_medium_comments', $description, $post);
             if($medium_global_custom_msg_options == 'post_msg' && !empty($medium_global_custom_message_template) && empty($description)) {
                 $description = $medium_global_custom_message_template;
             } elseif (empty($description) && !empty($medium_global_template_text)) {
                 $description = $medium_global_template_text;
             } elseif (empty($description)) {
                 //get medium posting description
                 $description = $post_content;
             }


              // Get post excerpt
            $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );
            // Get post tags
            $tags_arr = apply_filters('wpw_auto_poster_medium_hashtags', $tags_arr);
            $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';

            // get post categories
            $cats_arr = apply_filters('wpw_auto_poster_medium_hashcats', $cats_arr);
            $hashcats = (!empty($cats_arr) ) ? '#' . implode(' #', $cats_arr) : '';
            $full_author = normalize_whitespace( $first_name . ' ' . $last_name );
            $nickname_author = get_user_meta($post->post_author, 'nickname', true);
            $search_arr = array('{title}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}', '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}');
            $replace_arr = array($posttitle, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option('blogname'), get_option('blogname'), $post_content, $excerpt, $hashtags, $hashcats);
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
            //Medium Profile Data from setting //_wpweb_li_post_profile
            $medium_post_profiles = get_post_meta($post->ID, $prefix . 'medium_user_id');
            if( $post_type_new == 'wpwsapquickshare'){
                $medium_post_profiles = get_post_meta($post->ID, $prefix . 'medium_user_id',true);
            }

            $categories = wpw_auto_poster_get_post_categories_by_ID($post_type, $post->ID);

            $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');
            if (!empty($categories) && !empty($category_selected_social_acct) && empty($medium_post_profiles)) {
                $medium_clear_cnt = true;
                foreach ($categories as $key => $term_id) {
                    $cat_id = $term_id;
                    if (isset($category_selected_social_acct[$cat_id]['medium']) && !empty($category_selected_social_acct[$cat_id]['medium'])) {
                        if ($medium_clear_cnt)
                            $medium_post_profiles = array();
                            $medium_post_profiles = array_merge($medium_post_profiles, $category_selected_social_acct[$cat_id]['medium']);
                            $medium_clear_cnt = false;
                    }
                }
                if (!empty($medium_post_profiles)) {
                    $medium_post_profiles = array_unique($medium_post_profiles);
                }
            }

            if(empty($medium_post_profiles)) {//If profiles are empty in metabox
                $medium_post_profiles = isset($wpw_auto_poster_options['medium_type_' .$post->post_type . '_user']) ? $wpw_auto_poster_options['medium_type_' . $post->post_type . '_user'] : '';
            }


            if (empty($medium_post_profiles)) {
                //record logs for medium users are not selected
                $this->logs->wpw_auto_poster_add('Medium: User not selected for posting.');

                if( $post_type_new == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'medium_post_status','error');
                    update_post_meta($post->ID, $prefix . 'medium_error', esc_html__('User not selected for posting.', 'wpwautoposter' ) );
                }

                sap_add_notice(esc_html__('Medium: You have not selected any user for the posting.', 'wpwautoposter'), 'error');
                return false;
            } //end if to check user ids are empty

            $post_status = 'public';
            $medium_custom_tags = array();
            $posting_tags = get_post_meta($post->ID, $prefix . 'medium_custom_tags',true);
            if(!empty($posting_tags)) {

                $medium_custom_tags = explode(',', trim($posting_tags));
                $posting_logs_data['tags'] = $medium_custom_tags;
            }

            //posting logs data
            $posting_logs_data = array(
                'title' => $title,
                'link' => $postlink,
                'description' => $description,

            );

            //initial value of posting flag
            $postflg = false;
            
            if (!empty($medium_post_profiles)) {

                foreach($medium_post_profiles as $account_key  => $medium_post_profile) {


                    $medium_account_id = $medium_post_profile;
                    $medium_users_id_array = explode("|",$medium_account_id);

                    $main_account_id = !empty($medium_users_id_array['1']) ?  $medium_users_id_array['1'] : '';
                    $account_type    = !empty($medium_users_id_array['0']) ?  $medium_users_id_array['0'] : '';
                    if(array_key_exists($main_account_id, $wpw_auto_poster_medium_sess_data)) {

                        $posting_logs_user_details['display_name'] = $wpw_auto_poster_medium_sess_data[$main_account_id]['display_name'];
                        $posting_logs_user_details['id'] = $main_account_id;
                        $refresh_token = $wpw_auto_poster_medium_sess_data[$main_account_id]['token_details']['refresh_token'];

                        $accessToken = $this->mymedium->exchangeRefreshToken($refresh_token);
                        if(!empty($accessToken)){

                            $medium_custom_tags = array_map('trim', $medium_custom_tags);

                            $post_data = array(
                              'title'			=> $title,
                              'contentFormat' => 'html',
                              'content'		=> $description,
                              'canonicalUrl'	=> $postlink,
                              'publishStatus'	=> $post_status,
                              'tags'        => $medium_custom_tags,
                          );

                            $response = '';
                            if($account_type == 'main-account') {

                                $this->mymedium->setAccessToken( $accessToken );
                                $response = $this->mymedium->createPost($main_account_id, $post_data);

                            } else if($account_type == 'my-publication') {
                                
                                $publication_id = !empty($medium_users_id_array[2]) ? $medium_users_id_array[2] : '';
                                $this->mymedium->setAccessToken( $accessToken );
                                $response = $this->mymedium->createPostUnderPublication($publication_id, $post_data);
                                $posting_logs_user_details['publication_id'] = $publication_id;
                            }


                            if(!empty($response) && !empty($response->data->id)){

                                unset($post_data['contentFormat']);
                                $this->logs->wpw_auto_poster_add('Medium post data : ' . var_export($post_data, true));
                                $posting_logs_user_details['link_to_post'] = $response->data->url;
                                $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'medium', $posting_logs_data, $posting_logs_user_details);
                                $postflg = true;
                                $medium_posting['success'] = 1;

                            } else {

                                $errorMessage = $response->errors[0]->message;
                                $this->logs->wpw_auto_poster_add('Medium: '.$errorMessage);
                                if( $post_type_new == 'wpwsapquickshare'){
                                    update_post_meta($post->ID, $prefix . 'medium_post_status','error');
                                    update_post_meta($post->ID, $prefix . 'medium_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $errorMessage ) );
                                }
                                $postflg = false;
                                $medium_posting['fail'] = 0;
                            }

                           }

                        }

                }


            }

        } else {

            //record logs when grant extended permission not set
            $this->logs->wpw_auto_poster_add('Medium error. Session Data not found');
            // display error notice on post page

            if( $post_type_new == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'medium_post_status','error');
                update_post_meta($post->ID, $prefix . 'medium_error', esc_html__('Please select account before posting to the Medium.', 'wpwautoposter' ) );
            }
            sap_add_notice(esc_html__('Medium: Please select account before posting to the Medium.', 'wpwautoposter'), 'error');

        }

        return $medium_posting;

    }

    /**
     * Returns authentication url for medium.
     *
     * Handles to return authenctication url of medium user.
     *
     * @package Social Auto Poster
     * @since 3.8.2
     */

    public function wpw_auto_poster_medium_auth_url(){

        $authUrl = $this->mymedium->getAuthenticationUrl();
        return esc_url_raw($authUrl);

    }

    /**
     * Assign Medium all Data to session
     *
     * Handles to assign user's medium data
     * to sessoin & save to database
     *
     * @package Social Auto Poster
     * @since 3.8.2
     */

     public function wpw_auto_poster_medium_initialize(){

        global $wpw_auto_poster_options, $wpw_auto_poster_message_stack;

        $wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');

        $user_accounts    = array();
        $medium_sess_data = array();

        if( isset($_GET['code']) &&  isset($_GET['state']) && isset($_GET['wpw_auto_poster_medium_verification']) && $_GET['wpw_auto_poster_medium_verification'] == 'true' ) {

            $authorizationCode = $_GET['code'];
            $this->mymedium->authenticate( $authorizationCode );
            $refreshToken = $this->mymedium->getRefreshToken();
            $accessToken = $this->mymedium->exchangeRefreshToken( $refreshToken );
            //$this->mymedium->setAccessToken( $accessToken );
            $user =  $this->mymedium->getAuthenticatedUser();
           
            if(!empty($user->data->id)){
                $medium_sess_data[$user->data->id] = array(
                    'name'          => $user->data->name,
                    'display_name'  => $user->data->username,
                );
               

                if(!empty($refreshToken) && !empty($accessToken)) {
                    $medium_sess_data[$user->data->id]['token_details'] = array(
                        'access_token' => $accessToken,
                        'refresh_token' => $refreshToken,
                    );
                }

                $publications = $this->mymedium->publications($user->data->id)->data;
                if(!empty($publications)) {
                    $medium_sess_data[$user->data->id]['publications'] = $publications;
                }
            }

              

            if (!empty($wpw_auto_poster_medium_sess_data)) {
                $medium_sess_data = array_merge($wpw_auto_poster_medium_sess_data, $medium_sess_data);
            } else if (!empty($medium_sess_data) && is_array($medium_sess_data)) {
                $medium_sess_data = $medium_sess_data;
            }

             


            if(!empty($medium_sess_data) && is_array($medium_sess_data)) {
                update_option('wpw_auto_poster_medium_sess_data', $medium_sess_data);
            }

            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'medium_verification' => 'true#wpw-auto-poster-md-api'), admin_url('admin.php'));

            $this->logs->wpw_auto_poster_add('Medium account is added successfully for ' .$medium_sess_data[$user->data->id]['name']);

            $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'medium');

            wp_redirect($redirect_url);
            exit;
        }

     }

     /**
     * Assign Medium Reset user session
     *
     * Handles to reset account for specific user account
     *
     * @package Social Auto Poster
     * @since 3.8.2
     */

    public function wpw_auto_poster_medium_reset_session(){

       // Check if Medium reset user link is clicked and medium _reset_user is set to 1 and medium user id is there
        if (isset($_GET['medium_reset_user']) && $_GET['medium_reset_user'] == '1' && !empty($_GET['wpw_medium_userid'])) {
            $wpw_medium_app_id = sanitize_text_field($_GET['wpw_medium_userid']);
                // Getting stored li app data
            $wpw_auto_poster_medium_sess_data = get_option('wpw_auto_poster_medium_sess_data');
                // Unset particular app value data and update the option
            if (isset($wpw_auto_poster_medium_sess_data[$wpw_medium_app_id])) {
                unset($wpw_auto_poster_medium_sess_data[$wpw_medium_app_id]);
                update_option('wpw_auto_poster_medium_sess_data',$wpw_auto_poster_medium_sess_data);
            }
        }

        /******* Code for selected category Medium ***** */
            // unset selected Medium account option for category
        $cat_selected_social_acc = array();
        $cat_selected_acc = get_option('wpw_auto_poster_category_posting_acct');
        $cat_selected_social_acc = (!empty($cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;
        if (!empty($cat_selected_social_acc)) {
            foreach ($cat_selected_social_acc as $cat_id => $cat_social_acc) {
                if (isset($cat_social_acc['medium'])) {
                    unset($cat_selected_acc[$cat_id]['medium']);
                }
            }
                // Update autoposter category Medium posting account options
            update_option('wpw_auto_poster_category_posting_acct', $cat_selected_acc);
        }

    }

}
