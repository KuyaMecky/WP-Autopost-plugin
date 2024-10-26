<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

require_once __DIR__ . '/libraries/reddit/reddit.php';

/**
 * Reddit method Posting Class
 * 
 * Handles functions to get and posting to user account, pages and groups
 * 
 * @package Social Auto Poster
 * @since 3.5.2
 */

class Wpw_Auto_Poster_Reddit_Posting {

  public $message, $model, $logs, $myreddit;


  public function __construct() {

     global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;

     $this->message = $wpw_auto_poster_message_stack;
     $this->model = $wpw_auto_poster_model;
     $this->logs = $wpw_auto_poster_logs;

     $myReddit = new Reddit();
     $this->myreddit = $myReddit;
        //initialize the session value when data is saved in database
     add_action('init', array($this, 'wpw_auto_poster_reddit_initialize'));

 }

    /**
     * Post To Reddit
     * 
     * Code for posting on Reddit
     * 
     * @package Social Auto Poster
     * @since 3.5.2
     */

    public function wpw_auto_poster_reddit_posting($post, $auto_posting_type = '') {
        global $wpw_auto_poster_options;
        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $res = $this->wpw_auto_poster_post_to_reddit($post, $auto_posting_type);
        if (isset($res['success']) && !empty($res['success'])) {
            //record logs for posting done on reddit my business
            $this->logs->wpw_auto_poster_add('Reddit Posting is completed successfully');
            update_post_meta($post->ID, $prefix . 'reddit_published_on_posts', '1');
            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time('timestamp');
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            return true;
        }
        return false;
    }

    /**
     * Post To Reddit
     * 
     * Handles to Posting on Reddit
     * 
     * @package Social Auto Poster
     * @since 3.5.2
     */

    public function wpw_auto_poster_post_to_reddit($post, $auto_posting_type) {

        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
        $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');    

        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $post_type = $post->post_type; //post type
        $reddit_posting = array();
        
        //Initialize tags and categories
        $tags_arr = array();
        $cats_arr = array();

        // Getting all location apps
        $reddit_accounts = wpw_auto_poster_get_reddit_accounts_with_subreddits_for_posting();

        if (!empty($wpw_auto_poster_reddit_sess_data)) {

            //posting logs data
            $posting_logs_data = array();
            $unique = 'false';
            //user data
            $userdata = get_userdata($post->post_author);
            $first_name = $userdata->first_name; //user first name
            $last_name = $userdata->last_name; //user last name
            //published status
            $ispublished = get_post_meta($post->ID, $prefix . 'reddit_published_on_posts', true);
            // Get all selected tags for selected post type for hashtags support        
            
            if (isset($wpw_auto_poster_options['reddit_post_type_tags']) && !empty($wpw_auto_poster_options['reddit_post_type_tags'])) {
                $custom_post_tags = $wpw_auto_poster_options['reddit_post_type_tags'];

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
            if (isset($wpw_auto_poster_options['reddit_post_type_cats']) && !empty($wpw_auto_poster_options['reddit_post_type_cats'])) {
                $custom_post_cats = $wpw_auto_poster_options['reddit_post_type_cats'];
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
            $customtitle = get_post_meta( $post->ID, $prefix . 'reddit_post_title', true );
            $post_content = $post->post_content;
            $post_content = strip_shortcodes($post_content);
            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);
            //custom title from metabox
            // custom title from custom post type message    

            if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                // global custom post msg template for reposter
                $reddit_global_custom_message_template = ( isset($wpw_auto_poster_reposter_options["repost_reddit_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_reposter_options["repost_reddit_global_message_template_" . $post_type] : '';
                $reddit_global_custom_msg_options = isset($wpw_auto_poster_reposter_options['repost_reddit_custom_msg_options']) ? $wpw_auto_poster_reposter_options['repost_reddit_custom_msg_options'] : '';
                // global custom msg template for reposter
                $reddit_global_template_text = ( isset($wpw_auto_poster_reposter_options["repost_reddit_global_message_template"]) ) ? $wpw_auto_poster_reposter_options["repost_reddit_global_message_template"] : '';
            } else {
                $reddit_global_custom_message_template = ( isset($wpw_auto_poster_options["reddit_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_options["reddit_global_message_template_" . $post_type] : '';
                $reddit_global_custom_msg_options = isset($wpw_auto_poster_options['reddit_custom_msg_options']) ? $wpw_auto_poster_options['reddit_custom_msg_options'] : '';
                $reddit_global_template_text = (!empty($wpw_auto_poster_options['reddit_global_message_template']) ) ? $wpw_auto_poster_options['reddit_global_message_template'] : '';
            }
            if (!empty($customtitle)) {
                $customtitle = $customtitle;
            }else{
                if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                    // Fetch the title from general setting
					$customtitle = ( !empty( $wpw_auto_poster_reposter_options['repost_reddit_global_title_template'] ) ) ? $wpw_auto_poster_reposter_options['repost_reddit_global_title_template'] : '';				
                }else{
                    $customtitle = ( !empty( $wpw_auto_poster_options['rd_global_title_template'] ) ) ? $wpw_auto_poster_options['rd_global_title_template'] : '';
                }
            }

            //custom title set use it otherwise user posttiel
            $title = !empty($customtitle) ? $customtitle : $posttitle;
            //post image
            $postimage = get_post_meta($post->ID, $prefix . 'reddit_post_image', true);

            /**************
             * Image Priority
             * If metabox image set then take from metabox
             * If metabox image is not set then take from featured image
             * If featured image is not set then take from settings page
             * ************ */
            //get featured image from post / page / custom post type

            $post_featured_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            // global custom post img
            $reddit_custom_post_img = ( isset($wpw_auto_poster_options["reddit_post_image_" . $post_type]) ) ? $wpw_auto_poster_options["reddit_post_image_" . $post_type] : '';
            $reddit_global_custom_msg_options = isset($wpw_auto_poster_options['reddit_custom_msg_options']) ? $wpw_auto_poster_options['reddit_custom_msg_options'] : '';
            //check custom image is set in meta and not empty
            if (isset($postimage['src']) && !empty($postimage['src'])) {
                $postimage = $postimage['src'];
            } elseif (isset($post_featured_img[0]) && !empty($post_featured_img[0])) {
                //check post featrued image is set the use that image
                $postimage = $post_featured_img[0];
            } else {
                //else get post image from settings page
                $postimage = ( $reddit_global_custom_msg_options == 'post_msg' && !empty($reddit_custom_post_img) ) ? $reddit_custom_post_img : $wpw_auto_poster_options['reddit_post_image'];
            }
            $postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage);

            //post link
            $wpw_auto_poster_rd_custom_link     = get_post_meta( $post->ID, $prefix . 'reddit_custom_post_link', true );

            $postlink = isset( $wpw_auto_poster_rd_custom_link ) && !empty( $wpw_auto_poster_rd_custom_link ) ? $wpw_auto_poster_rd_custom_link : '';

            if (!empty($postlink)) {
                $postlink = $postlink;
            }else{
                $postlink = get_the_permalink($post->ID);
            }
            
            //if custom link is set or not
            $customlink = !empty($postlink) ? 'true' : 'false';

            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, $customlink, 'reddit');

            // not sure why this code here it should be above $postlink but lets keep it here
            //if post is published on reddit once then change url to prevent duplication
            if (isset($ispublished) && $ispublished == '1') {
                $unique = 'true';
            }

            //comments
            $description = get_post_meta($post->ID, $prefix . 'reddit_post_desc', true);
            $description = !empty($description) ? $description : '';
            $description = apply_filters('wpw_auto_poster_reddit_comments', $description, $post);

            if($reddit_global_custom_msg_options == 'post_msg' && !empty($reddit_global_custom_message_template) && empty($description)) {
                $description = $reddit_global_custom_message_template;
            } elseif (empty($description) && !empty($reddit_global_template_text)) {
                $description = $reddit_global_template_text;
            } elseif (empty($description)) {
                //get reddit posting description
                $description = $posttitle;
            }

            // Get post excerpt
            $excerpt = !empty($post->post_excerpt) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );
            // Get post tags
            $tags_arr = apply_filters('wpw_auto_poster_reddit_hashtags', $tags_arr);
            $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';

            // get post categories
            $cats_arr = apply_filters('wpw_auto_poster_reddit_hashcats', $cats_arr);
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
            //use 400 character to post to reddit will use as title
            $description = $this->model->wpw_auto_poster_excerpt($description, 400);
            //Get comment
            $comments = $this->model->wpw_auto_poster_html_decode($description);
            $comments = $this->model->wpw_auto_poster_excerpt($comments, 700);
            //Linkedin Profile Data from setting //_wpweb_li_post_profile
            $reddit_post_profiles = get_post_meta($post->ID, $prefix . 'reddit_user_id');
            if( $post_type == 'wpwsapquickshare'){
                $reddit_post_profiles = get_post_meta($post->ID, $prefix . 'reddit_user_id',true);
            }
            $reddit_posting_type_meta  = get_post_meta($post->ID, $prefix . 'reddit_posting_type',true);
            $posting_type_global = 'link';

            //  If quick share
            if( $post_type != 'wpwsapquickshare' ){
                $posting_type_global= !empty( $wpw_auto_poster_options['reddit_posting_type'] ) ? $wpw_auto_poster_options['reddit_posting_type'] : $wpw_auto_poster_options['reddit_type_' . $post_type . '_method'];
            }

            $posting_type  = !empty($reddit_posting_type_meta) ? $reddit_posting_type_meta : $posting_type_global;

            $categories = wpw_auto_poster_get_post_categories_by_ID($post_type, $post->ID);

            $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');

            if (!empty($categories) && !empty($category_selected_social_acct) && empty($reddit_post_profiles)) {
                $reddit_clear_cnt = true;
                foreach ($categories as $key => $term_id) {
                    $cat_id = $term_id;
                    if (isset($category_selected_social_acct[$cat_id]['reddit']) && !empty($category_selected_social_acct[$cat_id]['reddit'])) {
                        if ($reddit_clear_cnt)
                            $reddit_post_profiles = array();
                        $reddit_post_profiles = array_merge($reddit_post_profiles, $category_selected_social_acct[$cat_id]['reddit']);
                        $reddit_clear_cnt = false;
                    }
                }
                if (!empty($reddit_post_profiles)) {
                    $reddit_post_profiles = array_unique($reddit_post_profiles);
                }
            }

            if(empty($reddit_post_profiles)) {//If profiles are empty in metabox
                $reddit_post_profiles = isset($wpw_auto_poster_options['reddit_type_' .$post->post_type . '_user']) ? $wpw_auto_poster_options['reddit_type_' . $post->post_type . '_user'] : '';
            }


            if (empty($reddit_post_profiles)) {
                //record logs for reddit users are not selected
                $this->logs->wpw_auto_poster_add('Reddit: User not selected for posting.');
                if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                    update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('User not selected for posting.', 'wpwautoposter' ) );
                }
                sap_add_notice(esc_html__('Reddit: You have not selected any user for the posting.', 'wpwautoposter'), 'error');
                return false;
            } //end if to check user ids are empty  

            
                    //posting logs data
            $posting_logs_data = array(
                'title' => $title,
                'link' => $postlink,
                'image' => $postimage,
                'description' => $description
            );


                    //initial value of posting flag
            $postflg = false;  

            if (!empty($reddit_post_profiles)) {

                foreach ($reddit_post_profiles as $reddit_post_profile) {

                    $reddit_user_id = $reddit_post_profile;
                    $reddit_users_id_array = explode("|",$reddit_user_id);
                    $posting_access_token = '';   

                      

                    if(array_key_exists($reddit_users_id_array[0], $wpw_auto_poster_reddit_sess_data)) {

                        $posting_logs_user_details['display_name'] = $wpw_auto_poster_reddit_sess_data[$reddit_users_id_array[0]]['name'];
                        $posting_logs_user_details['id'] = $reddit_users_id_array[0];
                        if (!empty($reddit_users_id_array[1]) && $wpw_auto_poster_reddit_sess_data[$reddit_users_id_array[0]]['name'] != $reddit_users_id_array[1]) {
                            $posting_logs_user_details['subreddit_name'] = !empty($reddit_users_id_array[1]) ? $reddit_users_id_array[1] : '';    
                        }
                        
    

                        $refresh_token = $wpw_auto_poster_reddit_sess_data[$reddit_users_id_array[0]]['token_details']['refresh_token'];

                        $old_token_time = $wpw_auto_poster_reddit_sess_data[$reddit_users_id_array[0]]['token_details']['authorized_timestamp'];

                        $old_time = strtotime(date('H:i:s',$old_token_time));
                        $current_time = strtotime(date('H:i:s'),time());

                        $difference = round(abs($current_time - $old_time) / 60);


                        $newTokenData = $this->myreddit->get_exchange_token($refresh_token);
                        $access_token = $newTokenData['access_token'];
                        $token_type   = $newTokenData['token_type'];

                        $content = array(
                            'title'         => $title,
                            'submitted-url' => $postlink,
                            'comment'       => $comments,
                            'description'   => $description,
                            'submitted-image-url' => $postimage,
                            'user_name'     => $reddit_users_id_array[1] 
                        );  

                        if (!empty($access_token)) {
                           if(isset($reddit_user_id) && !empty( $reddit_user_id)) {
                            $post_data = array(
                                'title'               => $content['title'],
                                'submitted-url'       => $content['submitted-url'],
                                'comment'             => $content['comment'],
                                'submitted-image-url' => $content['submitted-image-url'],
                                'description'         => $content['description'],
                                'post_type'           => $posting_type,
                                'subreddit_name'      => $reddit_users_id_array[1],
                                'access_token'        => $token_type.":".$access_token
                            );

                            $response = $this->myreddit->createStory($post_data);
                                 
                               
                            if(!empty($response) && $response->success == '1'){
                                $this->logs->wpw_auto_poster_add('Reddit post data : ' . var_export($content, true));
                                $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'reddit', $posting_logs_data, $posting_logs_user_details);
                                $postflg = true;
                                $reddit_posting['success'] = 1;
                                if( $post_type == 'wpwsapquickshare'){
                                    update_post_meta($post->ID, $prefix . 'reddit_post_status','success');
                                }

                            } else {

                                /*
                                 *
                                 * Handling Validations if Posting not working in any of the below     scenarios
                                */
                                $msg = isset( $response->jquery[22][3][0] ) ? $response->jquery[22][3][0] : '';
                                $msg_subreddit_unable  = isset( $response->jquery[14][3][0] ) ? $response->jquery[14][3][0] : '';
                                $allowed_only_text_post = isset( $response->jquery[20][3][0] ) ? $response->jquery[20][3][0] : '';
                                $account_name = !empty( $reddit_users_id_array[1] ) ? $reddit_users_id_array[1] : '';

                                if(!empty($response) && $response->success == '' && $msg == 'that link has already been submitted'){
                                    $this->logs->wpw_auto_poster_add('Reddit: That link has already been submitted.');
                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('The link has already been submitted.', 'wpwautoposter' ) );
                                    }
                                    sap_add_notice(esc_html__('Reddit: That link has already been submitted.', 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;

                                } else if(!empty($response) && $response->success == '' && $msg == 'This community doesn\'t allow links to be posted more than once, and this link has already been shared') {
                                    
                                    $this->logs->wpw_auto_poster_add('Reddit: This community doesn\'t allow links to be posted more than once, and this link has already been shared');
                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('The link has already been submitted.', 'wpwautoposter' ) );
                                    }
                                    sap_add_notice(esc_html__('Reddit: This community doesn\'t allow links to be posted more than once, and this link has already been shared', 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;

                                } else if ( !empty($response) && $response->success == '' && $msg_subreddit_unable == 'you aren\'t allowed to post there.' ) {

                                    
                                    $this->logs->wpw_auto_poster_add('Reddit : You aren\'t allowed to post on '.$account_name);

                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('Reddit : You aren\'t allowed to post on ' . $account_name, 'wpwautoposter' ) );
                                    }

                                    sap_add_notice(esc_html__('Reddit : You aren\'t allowed to post on ' . $account_name, 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;

                                } else if ( !empty($response) && $response->success == '' && $msg_subreddit_unable == 'that subreddit does not allow image posts' ) {

                                    $this->logs->wpw_auto_poster_add('Reddit : photo posting is not allowed on '.$account_name);

                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('Reddit : photo posting is not allowed on ' . $account_name, 'wpwautoposter' ) );
                                    }

                                    sap_add_notice(esc_html__('Reddit : photo posting is not allowed on ' . $account_name, 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;

                                } else if ( !empty($response) && $response->success == '' && $msg_subreddit_unable == 'Reddit doesn\'t allow links from tinyurl.com: link shorteners are smelly' ) {

                                    
                                    $this->logs->wpw_auto_poster_add('Reddit doesn\'t allow links from tinyurl.com are not supported for '.$account_name);

                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('Reddit doesn\'t allow links from tinyurl.com are not supported for ' . $account_name, 'wpwautoposter' ) );
                                    }

                                    sap_add_notice(esc_html__('Reddit doesn\'t allow links from tinyurl.com are not supported for ' . $account_name, 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;

                                } else if ( !empty($response) && $response->success == '' && $allowed_only_text_post == 'that subreddit only allows text posts' ) {

                                    $this->logs->wpw_auto_poster_add('Reddit : only text posting is allowed for  ' . $account_name);

                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('Reddit : only text posting is allowed for ' . $account_name, 'wpwautoposter' ) );
                                    }

                                    sap_add_notice(esc_html__('Reddit : only text posting is allowed for ' . $account_name, 'wpwautoposter'), 'error');
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;


                                } else {

                                    $this->logs->wpw_auto_poster_add('Reddit: Something went wrong.');
                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'reddit_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'reddit_error', esc_html__('Post not published, please try again.', 'wpwautoposter' ) );
                                    }
                                    $postflg = false;
                                    $reddit_posting['fail'] = 0;
                                }
                            }

                        }

                    }  

                } 
            }

        }

    }  else {
            //record logs when grant extended permission not set
        $this->logs->wpw_auto_poster_add('Reddit error. Session Data not found');
            // display error notice on post page
        sap_add_notice(esc_html__('Reddit: Please select location before posting to the Reddit.', 'wpwautoposter'), 'error');
    }


    return $reddit_posting;

}    

    /**
     * Reddit Login URL link
     * 
     * @package Social Auto Poster
     * @since 3.5.2
    */

    public function wpw_auto_poster_get_rd_app_method_login_url() {

        $state = admin_url('admin.php');

        $params = array(
            'duration'     => 'permanent',
            'response_type'=> 'code',
            'client_id'    => WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID,
            'redirect_uri' => WPW_AUTO_POSTER_REDDIT_REDIRECT_URL,
            'scope'        => WPW_AUTO_POSTER_REDDIT_APP_SCOPE,
            'state'        => $state
        );

        $http_query = http_build_query($params);
        
        $auth_uri = 'https://www.reddit.com/api/v1/authorize/?';
        return $auth_uri . $http_query;

        //return $this->myreddit->reddit_login($state);
    }

    /**
     * Assign Reddit all Data to session
     * 
     * Handles to assign user's Reddit data
     * to sessoin & save to database
     * 
     * @package Social Auto Poster
     * @since 3.5.2
     */

    /**
     * Assign Reddit Reset user session
     * 
     * Handles to reset account for specific user account
     * 
     * @package Social Auto Poster
     * @since 3.5.2
     */

    public function wpw_auto_poster_reddit_reset_session() {

         // Check if Reddit reset user link is clicked and reddit_reset_user is set to 1 and  reddit user id is there

     if (isset($_GET['reddit_reset_user']) && $_GET['reddit_reset_user'] == '1' && !empty($_GET['wpw_reddit_userid'])) {
        $wpw_reddit_app_id = sanitize_text_field($_GET['wpw_reddit_userid']);
            // Getting stored li app data
        $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');
            // Unset particular app value data and update the option
        if (isset($wpw_auto_poster_reddit_sess_data[$wpw_reddit_app_id])) {
            unset($wpw_auto_poster_reddit_sess_data[$wpw_reddit_app_id]);
            update_option('wpw_auto_poster_reddit_sess_data', $wpw_auto_poster_reddit_sess_data);
        }
    }

    /******* Code for selected category Reddit ***** */
        // unset selected Reddit account option for category 
    $cat_selected_social_acc = array();
    $cat_selected_acc = get_option('wpw_auto_poster_category_posting_acct');
    $cat_selected_social_acc = (!empty($cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;
    if (!empty($cat_selected_social_acc)) {
        foreach ($cat_selected_social_acc as $cat_id => $cat_social_acc) {
            if (isset($cat_social_acc['reddit'])) {
                unset($cat_selected_acc[$cat_id]['reddit']);
            }
        }
            // Update autoposter category Reddit posting account options
        update_option('wpw_auto_poster_category_posting_acct', $cat_selected_acc);
    }

}    

public function wpw_auto_poster_reddit_initialize() {

    global $wpw_auto_poster_options, $wpw_auto_poster_message_stack;
    $wpw_auto_poster_reddit_sess_data = get_option('wpw_auto_poster_reddit_sess_data');
    $user_accounts    = array();
    $reddit_sess_data = array();
    if (isset($_GET['code']) && isset($_GET['wpw_auto_poster_reddit_verification']) && $_GET['wpw_auto_poster_reddit_verification'] == 'true') {

        $code = $_GET["code"];
        $redirect_url = WPW_AUTO_POSTER_REDDIT_REDIRECT_URL;
        $auth_token_url = 'https://www.reddit.com/api/v1/access_token';
        $postvals = sprintf("code=%s&redirect_uri=%s&grant_type=authorization_code", $code, $redirect_url);

        $token = $this->myreddit->runCurl($auth_token_url, $postvals, null,true,false,'');
                /*
                  Save settion details to access user details to fetch user details
                */  
                $access_token = '';  
                if (isset($token->access_token) && $token->access_token != '') {
                    $access_token = "{$token->token_type}:{$token->access_token}";
                }

                $subreddit_data          = array();
                $user_details            = $this->myreddit->getUser( $access_token );

                $subscribed_subreddits   = $this->myreddit->get_subscribed_subreddits( $access_token );
                $contribution_subreddits = $this->myreddit->get_contributor_subreddits( $access_token );
                $moderation_subreddits   = $this->myreddit->get_moderator_subreddits( $access_token );
                $stream_subreddit        =  $this->myreddit->get_streams_subreddits( $access_token );  

                // Code to get subreddit data
                if (!empty($subscribed_subreddits) && !empty($subscribed_subreddits->data->children) && is_array($subscribed_subreddits->data->children)) {
                   $subreddit_data = $subscribed_subreddits->data->children;  
                } if (!empty($subreddit_data) && !empty($contribution_subreddits) && !empty($contribution_subreddits->data->children) && is_array($contribution_subreddits->data->children)) {
                    $subreddit_data = array_merge( $subreddit_data , $contribution_subreddits->data->children );
                } if (!empty($subreddit_data) && !empty($moderation_subreddits) && !empty($moderation_subreddits->data->children) && is_array($moderation_subreddits->data->children)) {
                    $subreddit_data = array_merge( $subreddit_data , $moderation_subreddits->data->children );
                } if (!empty($subreddit_data) && !empty($stream_subreddit) && !empty($stream_subreddit->data->children) &&  is_array($stream_subreddit->data->children)) {
                    $subreddit_data = array_merge( $subreddit_data , $stream_subreddits->data->children );
                }
                
                // removing existing main account from subreddit
                foreach ( $subreddit_data as $key => $subreddit_acc ) {
                    if( $subreddit_acc->data->display_name == $user_details->subreddit->display_name ){
                        unset($subreddit_data[$key]);
                    }
                }

                
                $reddit_sess_data[$user_details->id] = array(
                    'name'          => $user_details->name,
                    'display_name'  => $user_details->subreddit->display_name,
                );

                $reddit_sess_data[$user_details->id]['token_details'] = array(
                    'authorized_timestamp' => time(),
                    'access_token' => $token->access_token,
                    'token_type' => $token->token_type,
                    'expire_timestamp' => $token->expires_in,
                    'refresh_token' => $token->refresh_token,
                );

                $reddit_sess_data[$user_details->id]['subreddit_details'] = $subreddit_data; 

                if (!empty($wpw_auto_poster_reddit_sess_data)) {
                 $reddit_sess_data = array_merge($wpw_auto_poster_reddit_sess_data, $reddit_sess_data);
             } else {
                 $reddit_sess_data = $reddit_sess_data;
             }

             if(!empty($reddit_sess_data) && is_array($reddit_sess_data)) {
                update_option('wpw_auto_poster_reddit_sess_data',$reddit_sess_data);
             }
             $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'reddit_verification' => 'true#wpw-auto-poster-rd-api'), admin_url('admin.php'));
             $this->logs->wpw_auto_poster_add('Reddit account is added successfully for ' .$reddit_sess_data[$user_details->id]['name']);
             $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'reddit');
             wp_redirect($redirect_url);

             exit;

         } 
     }   

 }	

