<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Youtube Posting Class
 *
 * Handles all the functions to post the submitted and approved
 * reviews to a chosen application owner account
 *
 * @package Social Auto Poster - You Tube - You Tube
 * @since 1.0.0
 */
class Wpw_Auto_Poster_Yt_Posting {

    public $youtubeconfig, $youtube, $message, $model, $logs, $client;
    
    public function __construct() {

        global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;
        
        $this->message = $wpw_auto_poster_message_stack;
        $this->model = $wpw_auto_poster_model;
        $this->logs  = $wpw_auto_poster_logs;
        
        //add action init for making user to logged in youtube
        add_action( 'init', array( $this, 'wpw_auto_poster_yt_user_logged_in' ) );
        
    }
    
    /**
     * Youtube Get Access Tocken
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_yt_get_access_token( $app_id ) {

        //Get stored li app grant data
        $wpw_auto_poster_yt_sess_data = get_option( 'wpw_auto_poster_yt_sess_data' );

        $access_tocken  = '';
        $wpw_auto_poster_youtube_oauth = get_transient('wpw_auto_poster_youtube_oauth');

        if( isset( $wpw_auto_poster_yt_sess_data ) && !empty( $wpw_auto_poster_yt_sess_data ) && isset( $wpw_auto_poster_yt_sess_data[$app_id]['wpw_auto_poster_yt_oauth']['youtube']['access'] ) ) {
            
            $yt_access_data = $wpw_auto_poster_yt_sess_data[$app_id]['wpw_auto_poster_yt_oauth']['youtube']['access'];
            
            $access_tocken  = isset( $yt_access_data['access_token'] ) ? $yt_access_data['access_token'] : '';

        } elseif( isset( $wpw_auto_poster_youtube_oauth ) ) {
            
            
            $yt_access_data = $wpw_auto_poster_youtube_oauth;
            
            $access_tocken  = isset( $yt_access_data['access_token'] ) ? $yt_access_data['access_token'] : '';
        }

        return $access_tocken;
    }
    
    /**
     * Include Youtube Class
     * 
     * Handles to load Youtube class
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_load_youtube( $app_id = false ) {

        global $wpw_auto_poster_options;

        // Getting youtube apps
        $yt_apps = wpw_auto_poster_get_yt_apps();

        // If app id is not passed then take first yt app data
        if( empty($app_id) ) {
            $yt_apps_keys = array_keys( $yt_apps );
            $app_id = reset( $yt_apps_keys );
        }

        //youtube declaration
        if( !empty( $app_id ) && !empty( $yt_apps[$app_id] ) ) {

            // Include google client libraries
            require_once WPW_AUTO_POSTER_SOCIAL_DIR . '/youtube/autoload.php';

            require_once WPW_AUTO_POSTER_SOCIAL_DIR . '/youtube/Client.php';

            require_once WPW_AUTO_POSTER_SOCIAL_DIR . '/youtube/Service/YouTube.php';
            
            $this->client = new Google_Client(); 

            $this->client->setClientId( $app_id ); 

            $this->client->setClientSecret( $yt_apps[$app_id] ); 

            $this->client->setScopes( esc_url_raw('https://www.googleapis.com/auth/youtube.upload') ); 
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt("force");

            $callbackUrl = site_url().'/?wpwautoposter=youtube&wpw_yt_app_id='.$app_id;
            
            $this->client->setRedirectUri( esc_url_raw($callbackUrl) ); 
            
            //Get access token
            $access_tocken   = $this->wpw_auto_poster_yt_get_access_token( $app_id );
            
            //Load youtube outh2 class
            $this->youtube = new Google_Service_YouTube( $this->client  );
            
            return true;
        } else {
            return false;
        }
    }

    public function wpw_auto_poster_get_processed_profile_data( $app_id ){
        $user_data['id'] = $app_id;

        return $user_data;
    }
    /**
     * Make Logged In User to Youtube
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_yt_user_logged_in() {

        global $wpw_auto_poster_options, $wpw_auto_poster_logs, $wpw_auto_poster_message_stack;

        $this->logs  = $wpw_auto_poster_logs;
        $this->message = $wpw_auto_poster_message_stack;

        $youtube_keys = isset( $wpw_auto_poster_options['youtube_keys'] ) ? $wpw_auto_poster_options['youtube_keys'] : array();


        //check $_GET['wpwautoposter'] equals to youtube
        if( isset( $_GET['wpwautoposter'] ) && $_GET['wpwautoposter'] == 'youtube' && !empty( $_GET['code'] ) && isset( $_GET['wpw_yt_app_id'] )) {

            //record logs for grant extended permission
            $this->logs->wpw_auto_poster_add( 'YouTube Grant Extended Permission', true );

            //record logs for get parameters set properly
            $this->logs->wpw_auto_poster_add( 'Get Parameters Set Properly.' );

            $yt_app_id = stripslashes_deep($_GET['wpw_yt_app_id']);

            $yt_app_secret = '';

            foreach ( $youtube_keys as $youtube_key => $youtube_value ) {

                if ( in_array( $yt_app_id, $youtube_value ) ) {

                    $yt_app_secret = $youtube_value['app_secret'];
                }

            }

            // redirect if same youtube credential use twice so we can handle error in error log
            $wrong_access = add_query_arg( array('page' => 'wpw-auto-poster-settings' ), admin_url().'/?wpwautoposter=youtube&wpw_yt_app_id='.$yt_app_id );
            wp_redirect( $wrong_access );


            //load youtube class
            $youtube   = $this->wpw_auto_poster_load_youtube( $yt_app_id );

            try {

                if( !$youtube ) return false;

                //check youtube loaded or not
                $this->client->authenticate($_GET['code']); 

            //Get Access token
                $access_token  = $this->client->getAccessToken(); 

                if( !empty( $access_token ) ) { // if user allows access to youtube

                //record logs for get type initiate called
                    $this->logs->wpw_auto_poster_add( 'YouTube grant initiate called' );

                //record logs for get type response called
                    $this->logs->wpw_auto_poster_add( 'YouTube permission granted by user' );

                //record logs for get type initiate called
                    $this->logs->wpw_auto_poster_add( 'YouTube Request token retrieval success when clicked on allow access by user' );

                // the request went through without an error, gather user's 'access' tokens
                    $wpw_auto_poster_youtube_oauth['youtube']['access']['access_token'] = $access_token;
                    set_transient( 'wpw_auto_poster_youtube_oauth', $wpw_auto_poster_youtube_oauth );
                     

                // set the user as authorized for future quick reference
                    /*$wpw_auto_poster_youtube_oauth['youtube']['authorized'] = TRUE;
                    set_transient( 'wpw_auto_poster_youtube_oauth', $wpw_auto_poster_youtube_oauth );*/

                    if( !empty( $access_token ) ){

                        $resultdata = $this->wpw_auto_poster_get_processed_profile_data($yt_app_id);

                    //set user data to sesssion for further use
                        $wpw_auto_poster_yt_cache = $resultdata;
                        set_transient( 'wpw_auto_poster_yt_cache', $wpw_auto_poster_yt_cache );

                        $wpw_auto_poster_yt_user_id = isset( $yt_app_id ) ? $yt_app_id : '';
                        set_transient( 'wpw_auto_poster_yt_user_id', $wpw_auto_poster_yt_user_id );

                    // redirect the user back to the demo page
                        $this->message->add_session( 'poster-selected-tab', 'youtube' );

                    //set user data  to session
                        $this->wpw_auto_poster_set_yt_data_to_session( $yt_app_id );

                    // unset session data so there will be no probelm to grant extend another account
                        delete_transient('wpw_auto_poster_youtube_oauth');
                        delete_transient('wpw_auto_poster_youtube_oauth_authorized');
                        delete_transient('wpw_auto_poster_yt_oauth');

                    //record logs for grant extend successfully
                        $this->logs->wpw_auto_poster_add( 'Grant Extended Permission Successfully.' );

                        $poster_setting_url = add_query_arg( array('page' => 'wpw-auto-poster-settings' ), admin_url() );
                    } else{
                        $this->logs->wpw_auto_poster_add( 'YouTube User data not found' );
                    }

                    wp_redirect( $poster_setting_url );
                    exit;

                } else {

                //record logs for access token retrieval
                    $this->logs->wpw_auto_poster_add( 'YouTube error: Access token retrieval failed' );
                }
            }  catch (Google_Exception $e) {
                $this->logs->wpw_auto_poster_add( 'Youtube error: Incorrect YouTube App ID/API Key or Secret.'  );
                // display error notice on post page
                sap_add_notice( sprintf( esc_html__('Youtube error: Incorrect YouTube App ID/API Key or Secret.', 'wpwautoposter' ), $e->getMessage() ), 'error');
                return false;
            }
            


            // code will excute when user does connect with linked in
            
            
        } //end if to check $_GET['wpwautoposter'] equals to youtube

    }
    
    /**
     * Get Youtube Login URL
     * 
     * Handles to Return Youtube URL
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_yt_login_url($app_id = false) {

        //load youtube class
        $youtube = $this->wpw_auto_poster_load_youtube( $app_id );
        
        //check youtube loaded or not
        if( !$youtube ) return false;
        
        $callbackUrl = site_url().'/?wpwautoposter=youtube&wpw_yt_app_id='.$app_id;
        
        try {//Prepare login URL
            $preparedurl    = $this->client->createAuthUrl();
        } catch( Exception $e ) {
            $preparedurl    = '';
        }
        return $preparedurl;
    }
    
    
    /**
     * Post To youtube
     * 
     * Handles to Posting to youtube User Wall,
     * Company Page / Group Posting
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_post_to_youtube( $post, $auto_posting_type ) {


        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options, $ThemifyBuilder, $wpw_auto_poster_logs,$wpw_auto_poster_model;

        $this->logs = $wpw_auto_poster_logs;
        $this->model = $wpw_auto_poster_model;
        
        // Get stored li app grant data
        $wpw_auto_poster_yt_sess_data = get_option('wpw_auto_poster_yt_sess_data');

        //meta prefix
        $prefix         = WPW_AUTO_POSTER_META_PREFIX;

        $post_type = $post->post_type; // Post type
        
        //Initilize youtube posting
        $yt_posting     = array();

        //Initialize tags and categories
        $tags_arr = array();
        $cats_arr = array();
        
        // Getting all youtube apps
        $yt_apps = wpw_auto_poster_get_yt_apps();
        
        //check youtube authorized session is true or not
        //need to do for youtube posting code

        if( !empty( $wpw_auto_poster_yt_sess_data ) ) {

            //posting logs data
            $posting_logs_data  = array();

            $unique = 'false';
            
            //user data
            $userdata   = get_userdata( $post->post_author );
            $first_name = $userdata->first_name; //user first name
            $last_name  = $userdata->last_name; //user last name
            
            //published status
            $ispublished    = get_post_meta( $post->ID, $prefix . 'yt_status', true );

            // Get all selected tags for selected post type for hashtags support
            if(isset($wpw_auto_poster_options['yt_post_type_tags']) && !empty($wpw_auto_poster_options['yt_post_type_tags'])) {

                $custom_post_tags = $wpw_auto_poster_options['yt_post_type_tags'];
                if(isset($custom_post_tags[$post_type]) && !empty($custom_post_tags[$post_type])){  
                    foreach($custom_post_tags[$post_type] as $key => $tag){
                        $term_list = wp_get_post_terms( $post->ID, $tag, array("fields" => "names") );
                        foreach($term_list as $term_single) {
                            $tags_arr[] = str_replace( ' ', '' ,$term_single);
                        }
                    }
                }
            }

            // Get all selected categories for selected post type for hashcats support
            if(isset($wpw_auto_poster_options['yt_post_type_cats']) && !empty($wpw_auto_poster_options['yt_post_type_cats'])) {

                $custom_post_cats = $wpw_auto_poster_options['yt_post_type_cats'];
                if(isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])){  
                    foreach($custom_post_cats[$post_type] as $key => $category){
                        $term_list = wp_get_post_terms( $post->ID, $category, array("fields" => "names") );
                        foreach($term_list as $term_single) {
                            $cats_arr[] = str_replace( ' ', '' ,$term_single);
                        }
                    }
                    
                }
            }

            
            //post title
            $posttitle      = $post->post_title;
            $post_content   = $post->post_content;

            // fix html render issue with themify theme builder
            if( empty( $ThemifyBuilder ) ) {
                $post_content   = apply_filters('the_content',$post_content);
            }

            // If gutenburg/block editor used, than remove blocks comments
            if( function_exists( 'has_blocks') && !empty( $ThemifyBuilder ) ) {
                $blocks = parse_blocks( $post_content );
                if( !empty( $blocks) ){

                    $post_content = '';

                    foreach ( $blocks as $key => $value) {
                        if( isset( $value['innerHTML'] ) && !empty( wp_strip_all_tags($value['innerHTML']) ) ) {
                            $post_content .= wp_strip_all_tags($value['innerHTML']).'\n';
                        }
                    }
                }
            }
            
            $post_content   = strip_shortcodes($post_content);

            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);

            
            //custom title from metabox
            $customtitle    = get_post_meta( $post->ID, $prefix . 'yt_post_title', true );

            // custom title from custom post type message

            if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {

                // global custom post msg template for reposter
                $yt_global_custom_message_template = ( isset( $wpw_auto_poster_reposter_options["repost_yt_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["repost_yt_global_message_template_".$post_type] : '';

                $yt_global_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_yt_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_yt_custom_msg_options'] : '';

                // global custom msg template for reposter
                $yt_global_template_text = ( isset( $wpw_auto_poster_reposter_options["repost_yt_global_message_template"] ) ) ? $wpw_auto_poster_reposter_options["repost_yt_global_message_template"] : '';
            }
            else {

                $yt_global_custom_message_template = ( isset( $wpw_auto_poster_options["yt_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_options["yt_global_message_template_".$post_type] : '';

                $yt_global_custom_msg_options = isset( $wpw_auto_poster_options['yt_custom_msg_options'] ) ? $wpw_auto_poster_options['yt_custom_msg_options'] : '';
                
                $yt_global_template_text = ( !empty( $wpw_auto_poster_options['yt_global_message_template'] ) ) ? $wpw_auto_poster_options['yt_global_message_template'] : '';

            }

            if( !empty( $customtitle ) ) {
                $customtitle = $customtitle;
            }else{
                if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {
                    $customtitle = ( !empty( $wpw_auto_poster_reposter_options['repost_yt_global_title_template'] ) ) ? $wpw_auto_poster_reposter_options['repost_yt_global_title_template'] : '';				
                }else{
                    $customtitle = ( !empty( $wpw_auto_poster_options['yt_global_title_template'] ) ) ? $wpw_auto_poster_options['yt_global_title_template'] : '';				
                }
                
            }

            //custom title set use it otherwise user posttiel
            $title  = !empty( $customtitle ) ? $customtitle : $posttitle;
            
            //post video
            $postimage      = get_post_meta( $post->ID, $prefix . 'yt_post_image', true );
            
            /**************
             * Image Priority
             * If metabox image set then take from metabox
             * If metabox image is not set then take from featured image
             * If featured image is not set then take from settings page
             **************/
            //global custom post video
            $yt_custom_post_video = ( isset( $wpw_auto_poster_options["yt_custom_img"] ) ) ? $wpw_auto_poster_options["yt_custom_img"] : '';

            // global custom post img
            $yt_custom_post_img = ( isset( $wpw_auto_poster_options["yt_custom_img_".$post_type] ) ) ? $wpw_auto_poster_options["yt_custom_img_".$post_type] : '';


            $yt_global_custom_msg_options = isset( $wpw_auto_poster_options['yt_custom_msg_options'] ) ? $wpw_auto_poster_options['yt_custom_msg_options'] : '';


            if( isset( $postimage['src'] ) && !empty( $postimage['src'] ) ) {
                $postimage = $postimage['src'];
            } elseif (isset($yt_custom_post_video['src']) && !empty($yt_custom_post_video['src']) ) {
                //check post featrued image is set the use that image
                $postimage = $yt_custom_post_video['src'];
            } else {
                //else get post image from settings page
                $postimage = ( $yt_global_custom_msg_options == 'post_msg' && !empty( $yt_custom_post_img ) ) ? $yt_custom_post_img : $wpw_auto_poster_options['yt_custom_img'];
            }


            if(empty($postimage)){
                
                $tmp_post_type = ( $post_type == 'wpwsapquickshare' ) ? 'quickshare': $post_type;

                $this->logs->wpw_auto_poster_add( 'Upload atleast one video in ' . var_export( $tmp_post_type, true ) .' or else upload in YouTube global setting.' );
                if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                    update_post_meta($post->ID, $prefix . 'yt_error', esc_html__('Upload atleast one video for posting.', 'wpwautoposter' ) );
                }
                sap_add_notice( sprintf( esc_html__('Upload atleast one video in %s or else upload in YouTube global setting.', 'wpwautoposter' ), var_export( $tmp_post_type, true ) ), 'error');
                return false;
            }

            $postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage );

            
            //post link
            $postlink = get_post_meta( $post->ID, $prefix . 'yt_post_link', true );
            $postlink = isset( $postlink ) && !empty( $postlink ) ? $postlink : '';

            //if custom link is set or not
            $customlink = !empty( $postlink ) ? 'true' : 'false';
            
            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link( $postlink, $unique, $post->ID, $customlink, 'yt' );
            
            // not sure why this code here it should be above $postlink but lets keep it here
            //if post is published on youtube once then change url to prevent duplication
            if( isset( $ispublished ) && $ispublished == '1' ) {
                $unique = 'true';
            }
            
            //comments
            $description = get_post_meta( $post->ID, $prefix . 'yt_custom_status_msg', true );

            $description = !empty( $description ) ? $description : '';

            if( $yt_global_custom_msg_options == 'post_msg' && !empty( $yt_global_custom_message_template ) && empty( $description ) ) {

                $description = $yt_global_custom_message_template;
            }
            elseif( empty( $description ) && !empty( $yt_global_template_text ) ) {

                $description = $yt_global_template_text;
            } elseif( empty( $description ) ){

                //get youtube posting description
                $description = $post_content;
            }

            // Get post excerpt
            $excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

            // Get post tags
            $tags_arr   = apply_filters('wpw_auto_poster_yt_hashtags', $tags_arr);
            $hashtags   = ( !empty( $tags_arr ) ) ? '#'.implode( ' #', $tags_arr ) : '';

            // get post categories
            $cats_arr   = apply_filters('wpw_auto_poster_yt_hashcats', $cats_arr);
            $hashcats   = ( !empty( $cats_arr ) ) ? '#'.implode( ' #', $cats_arr ) : '';

            
            $full_author = normalize_whitespace( $first_name.' '.$last_name );
            $nickname_author = get_user_meta( $post->post_author, 'nickname', true);

            $search_arr         = array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );
            $replace_arr        = array( $posttitle , $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

            $code_matches = array();

            // check if template tags contains {content-numbers}
            if( preg_match_all( '/\{(content)(-)(\d*)\}/', $description, $code_matches ) ) {
                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $post_content = substr( $post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $post_content;
            }

            $cf_matches = array();
            // check if template tags contains {CF-CustomFieldName}
            if( preg_match_all( '/\{(CF)(-)(\S*)\}/', $description, $cf_matches ) ) {

                foreach ($cf_matches[0] as $key => $value) {
                    $cf_tag = $value;
                    $search_arr[] = $cf_tag;
                }

                foreach ($cf_matches[3] as $key => $value) {
                    $cf_name = $value;
                    $tag_value = '';
                    
                    if( $cf_name ) {
                        $tag_value = get_post_meta($post->ID, $cf_name, true);

                        if( is_array( $tag_value ) ) {
                            $tag_value = '';
                        }
                    }

                    $replace_arr[] = $tag_value;
                }
            }
            
            $description = str_replace( $search_arr, $replace_arr, $description );
            $description = $this->model->wpw_auto_poster_stripslashes_deep( $description );
            $description = $this->model->wpw_auto_poster_html_decode( $description );

            // replace title with tag support value                 
            $search_arr         = array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );
            $replace_arr        = array( $posttitle, $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

            // check if template tags contains {content-numbers}
            if( preg_match_all( '/\{(content)(-)(\d*)\}/', $title, $code_matches ) ) {
                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $post_content = substr( $post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $post_content;
            }

            // check if template tags contains {CF-CustomFieldName}
            if( preg_match_all( '/\{(CF)(-)(\S*)\}/', $title, $cf_matches ) ) {

                foreach ($cf_matches[0] as $key => $value) {
                    $cf_tag = $value;
                    $search_arr[] = $cf_tag;
                }

                foreach ($cf_matches[3] as $key => $value) {
                    $cf_name = $value;
                    $tag_value = '';
                    
                    if( $cf_name ) {
                        $tag_value = get_post_meta($post->ID, $cf_name, true);

                        if( is_array( $tag_value ) ) {
                            $tag_value = '';
                        }
                    }

                    $replace_arr[] = $tag_value;
                }
            }
            
            // replace title with tag support value
            $title              = str_replace( $search_arr, $replace_arr, $title );

            //Get title
            $title              = $this->model->wpw_auto_poster_html_decode( $title );

            //use 400 character to post to youtube will use as title
            $description    = $this->model->wpw_auto_poster_excerpt( $description, 400 );
            
            //youtube Profile Data from setting //_wpweb_yt_post_profile
            $yt_post_profiles   = get_post_meta( $post->ID, $prefix . 'yt_user_id' );

            if( $post_type == 'wpwsapquickshare'){
                $yt_post_profiles = get_post_meta($post->ID, $prefix . 'yt_user_id',true);
            }
            
            /******* Code to posting to selected category Youtube account ******/
            // get all categories for custom post type
            $categories = wpw_auto_poster_get_post_categories_by_ID( $post_type, $post->ID );
            
            // Get all selected account list from category
            $category_selected_social_acct = get_option( 'wpw_auto_poster_category_posting_acct');
            
            // IF category selected and category social account data found
            if( !empty( $categories ) && !empty( $category_selected_social_acct ) && empty( $yt_post_profiles ) ) {
                $yt_clear_cnt = true;

                // GET Linkdin user account ids from post selected categories
                foreach( $categories as $key => $term_id ) {

                    $cat_id = $term_id;
                    // Get TW user account ids form selected category  
                    if( isset( $category_selected_social_acct[$cat_id]['yt'] ) && !empty( $category_selected_social_acct[$cat_id]['yt'] ) ) {
                        // clear TW user data once
                        if( $yt_clear_cnt)
                            $yt_post_profiles = array();
                        $yt_post_profiles = array_merge($yt_post_profiles, $category_selected_social_acct[$cat_id]['yt'] );
                        $yt_clear_cnt = false;
                    }
                }
                if( !empty( $yt_post_profiles ) ) {
                    $yt_post_profiles = array_unique($yt_post_profiles);
                }
            }

            if( empty( $yt_post_profiles ) ) {//If profiles are empty in metabox
                $yt_post_profiles   = isset( $wpw_auto_poster_options['yt_type_'.$post->post_type.'_user'] ) ? $wpw_auto_poster_options['yt_type_'.$post->post_type.'_user'] : '';
            }
            

            //check youtube user ids are empty selected for posting
            if( empty( $yt_post_profiles ) ) {

            //record logs for youtube users are not selected
                $this->logs->wpw_auto_poster_add( 'YouTube error: user not selected for posting.' );
                if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                    update_post_meta($post->ID, $prefix . 'yt_error', esc_html__('User not selected for posting.', 'wpwautoposter' ) );
                }

                sap_add_notice( esc_html__('YouTube: You have not selected any user for the posting.', 'wpwautoposter' ), 'error');
            //return false
                return false;

            } //end if to check user ids are empty

            $content = array( 
                'title'                 => $title,
                'video'                 => $postimage,
                'description'           => $description
            );


            
            //posting logs data
            $posting_logs_data = array( 
                'title'         => $title,
                'link'          => $postlink,
                'image'         => $postimage,
                'description'   => $description
            );

            //Get all Profiles
            $profile_datas  = $this->wpw_auto_poster_get_profiles_data();

            //record logs for youtube data
            $this->logs->wpw_auto_poster_add( 'Youtube post data : ' . var_export( $content, true ) );

            //get user profile data
            $user_profile_data  = $this->wpw_auto_poster_get_yt_user_data();            

            //Initilize all user/company/group data
            $company_data = $group_data = $userwall_data = $display_name_data = $display_id_data = array();

            //initial value of posting flag
            $postflg = false;

            try {

                if( !empty( $yt_post_profiles ) ) {

                    foreach ( $yt_post_profiles as $yt_post_profile ) {

                        //Initilize log user details
                        $posting_logs_user_details  = array();

                        $profile_id     = $yt_post_profile;
                        $yt_post_app_id = $yt_post_profile; // Youtube App Id


                        $app_access_token = $this->wpw_auto_poster_yt_get_access_token( $yt_post_app_id);


                        // Load youtube class
                        $youtube = $this->wpw_auto_poster_load_youtube( $yt_post_app_id );

                        // Check youtube class is exis or not
                        if (!$youtube) {
                            $this->logs->wpw_auto_poster_add('Youtube error: Youtube is not initialized with ' . $yt_post_app_id . ' App.'); // Record logs for youtube not initialized
                            if( $post_type == 'wpwsapquickshare'){
                                update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                                update_post_meta($post->ID, $prefix . 'yt_error', esc_html__('Youtube is not initialized with ' . $yt_post_app_id . ' App.', 'wpwautoposter' ) );
                            }
                            continue;
                        }

                         // Getting stored youtube app data
                        $yt_stored_app_data = isset($wpw_auto_poster_yt_sess_data[$yt_post_app_id]) ? $wpw_auto_poster_yt_sess_data[$yt_post_app_id] : array();

                        // Get user cache data
                        $user_cache_data = isset($yt_stored_app_data['wpw_auto_poster_yt_cache']) ? $yt_stored_app_data['wpw_auto_poster_yt_cache'] : array();


                        //Youtube Log user details
                        $posting_logs_user_details['account_id']		= $profile_id;
                        $posting_logs_user_details['youtube_app_id']	= $yt_post_app_id;


                        if( !empty( $profile_id ) && !empty( $app_access_token ) ) {
                            $app_access_token = json_decode($app_access_token);
                            $this->client->refreshToken($app_access_token->refresh_token);
                            $app_access_token = $this->client->getAccessToken();

                            if (strpos($postimage, site_url()) !== false) {
                                $postimage = str_replace(site_url(),"",$postimage);
                                $postimage = '..'.$postimage;

                            } else {
                                $postimage = wpw_auto_poster_get_image_path( $postimage );
                            }

                            $this->client->setAccessToken($app_access_token);
                            
                            $snippet = new Google_Service_YouTube_VideoSnippet();
                            $snippet->setTitle($content['title']); 
                            $snippet->setDescription($content['description']); 

                            $video_status = new Google_Service_YouTube_VideoStatus(); 
                            $video_status->privacyStatus = "public";
                            
                            $videoObj = new Google_Service_YouTube_Video(); 

                            $videoObj->setSnippet($snippet); 

                            $videoObj->setStatus($video_status);

                            $chunkSizeBytes = 1 * 1024 * 1024;

                            $this->client->setDefer(true); 

                            $request = $this->youtube->videos->insert("status,snippet", $videoObj);

                            $mediaObj = new Google_Http_MediaFileUpload( 
                                $this->client, 
                                $request, 
                                'video/*', 
                                null,
                                true,
                                $chunkSizeBytes
                            );


                            $mediaObj->setFileSize(filesize($postimage));

                            $status = false; 

                            $handle = fopen($postimage, "rb"); 

                            while( !$status && !feof($handle) ) {
                              $chunk = fread($handle, $chunkSizeBytes); 
                              $status = $mediaObj->nextChunk($chunk); 
                          }
                          fclose($handle);

                          $this->client->setDefer(false);

                          if( !empty( $status ) && isset( $status->id ) && !empty( $status->id ) ) {
                            $postflg    = true;
                             if( $post_type == 'wpwsapquickshare'){
                                update_post_meta($post->ID, $prefix . 'yt_post_status','success');
                            }
                                //record logs for youtube users are not selected
                            $this->logs->wpw_auto_poster_add( 'Youtube posted to User ID : ' . $profile_id  . ' Media Id: '.$status->id );
                             
                            $this->logs->wpw_auto_poster_add( 'Youtube video url : ' . esc_url("http://www.youtube.com/watch?v=") . $status->id );
                        }
                    }


                    if( $postflg ) {
                        //posting logs store into database
                        $this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'yt', $posting_logs_data, $posting_logs_user_details );

                        $yt_posting['success'] = 1;

                    } else {

                        $yt_posting['fail'] = 1;
                        if( $post_type == 'wpwsapquickshare'){
                            update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                            update_post_meta($post->ID, $prefix . 'yt_error', esc_html__('Post not published, please try again.', 'wpwautoposter' ) );
                        }
                    }

                }
            }
        } catch ( Google_ServiceException $e ) {

                //record logs exception generated
            $this->logs->wpw_auto_poster_add( 'Youtube error: ' . $e->getMessage() );
            if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                update_post_meta($post->ID, $prefix . 'yt_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $e->getMessage() ) );
            }
                // display error notice on post page
            sap_add_notice( sprintf( esc_html__('Youtube: Something was wrong while posting %s', 'wpwautoposter' ), $e->getMessage() ), 'error');
            return false;
        }
        catch ( Google_Exception $e ) {
                //record logs exception generated
            $this->logs->wpw_auto_poster_add( 'Youtube error: ' . $e->getMessage() );
            if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'yt_post_status','error');
                update_post_meta($post->ID, $prefix . 'yt_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $e->getMessage() ) );
            }
                // display error notice on post page
            sap_add_notice( sprintf( esc_html__('Youtube: Something was wrong while posting %s', 'wpwautoposter' ), $e->getMessage() ), 'error');
            return false;
        }

    } else {

            //record logs when grant extended permission not set
        $this->logs->wpw_auto_poster_add( 'Youtube error: Grant extended permissions not set.' );
        if( $post_type == 'wpwsapquickshare'){
            update_post_meta($post->ID, $prefix . 'yt_post_status','error');
            update_post_meta($post->ID, $prefix . 'yt_error', esc_html__('Please give grant extended permission before posting to the YouTube.', 'wpwautoposter' ) );
        }
            // display error notice on post page
        sap_add_notice( esc_html__('YouTube: Please give grant extended permission before posting to the YouTube.', 'wpwautoposter' ), 'error');
    }

    return $yt_posting;
}

    /**
     * Get YouTube Profiles
     * 
     * Function to get YouTube profiles
     * UserWall
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_profiles_data() {

        $profiles   = array();
        
        //Get Users Data
        $users = $this->wpw_auto_poster_get_yt_users();
        

        if( !empty( $users ) ) {//If User Data is not empty

            foreach( $users as $app_id => $user_value) {
                $user_id    = isset( $user_value['id'] ) ? $user_value['id'] : '';            
                if( !empty( $user_id ) ) {
                    $profiles[ $user_id ] = $user_id;
                }
            }
        }
        
        
        return $profiles;
    }
    
    /**
     * Get youtube User Data
     *
     * Function to get youtube User Data
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_yt_user_data() {

        $wpw_auto_poster_yt_sess_data = get_option( 'wpw_auto_poster_yt_sess_data' );

        $user_profile_data = array();
        $wpw_auto_poster_yt_cache = get_transient('wpw_auto_poster_yt_cache');
        if ( isset( $wpw_auto_poster_yt_cache ) && !empty( $wpw_auto_poster_yt_cache ) ) {

            $user_profile_data = $wpw_auto_poster_yt_cache;
        }
        
        return $user_profile_data;
    }
    
    /**
     * Set Session Data of youtube to session
     * 
     * Handles to set user data to session
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_set_yt_data_to_session($yt_app_id = false) {
        global $wpw_auto_poster_logs;

        $this->logs = $wpw_auto_poster_logs;

        //fetch user data who is grant the premission
        $ytuserdata = $this->wpw_auto_poster_get_yt_user_data();
        
        if( isset( $ytuserdata['id'] ) && !empty( $ytuserdata['id'] ) ) {

            //record logs for user id
            $this->logs->wpw_auto_poster_add( 'YouTube User ID : '.$ytuserdata['id'] );
            
            try {


                $wpw_auto_poster_yt_user_id = get_transient( 'wpw_auto_poster_yt_user_id' );

                $wpw_auto_poster_yt_user_id = isset( $wpw_auto_poster_yt_user_id )
                ? $wpw_auto_poster_yt_user_id : $ytuserdata['id'];
                set_transient('wpw_auto_poster_yt_user_id',$wpw_auto_poster_yt_user_id);

                $wpw_auto_poster_yt_cache = get_transient( 'wpw_auto_poster_yt_cache' );
                $wpw_auto_poster_yt_cache   = isset( $wpw_auto_poster_yt_cache ) 
                ? $wpw_auto_poster_yt_cache : $ytuserdata;
                set_transient('wpw_auto_poster_yt_cache',$wpw_auto_poster_yt_cache);


                $wpw_auto_poster_yt_oauth = get_transient( 'wpw_auto_poster_yt_oauth' );

                $wpw_auto_poster_youtube_oauth = get_transient( 'wpw_auto_poster_youtube_oauth' );

                $wpw_auto_poster_yt_oauth = isset( $wpw_auto_poster_yt_oauth ) 
                ? $wpw_auto_poster_yt_oauth : $wpw_auto_poster_youtube_oauth;
                set_transient('wpw_auto_poster_yt_oauth',$wpw_auto_poster_yt_oauth);
                
                // start code to manage session from database           
                $wpw_auto_poster_yt_sess_data = get_option( 'wpw_auto_poster_yt_sess_data' );

                if( !isset( $wpw_auto_poster_yt_sess_data[$yt_app_id] ) ) {             

                    $sess_data = array(
                        'wpw_auto_poster_yt_user_id'    => $wpw_auto_poster_yt_user_id,
                        'wpw_auto_poster_yt_cache'      => $ytuserdata,
                        'wpw_auto_poster_yt_oauth'      => $wpw_auto_poster_youtube_oauth,
                    );

                    
                    
                    if ( $yt_app_id ) {

                        // Save Multiple Accounts
                        $wpw_auto_poster_yt_sess_data[$yt_app_id] = $sess_data;

                        update_option( 'wpw_auto_poster_yt_sess_data', $wpw_auto_poster_yt_sess_data );

                    }
                    
                    //record logs for session data updated to options
                    $this->logs->wpw_auto_poster_add( 'Session Data Updated to Options' );
                }
            } catch( Exception $e ) {

                $ytuserdata = null;
            }
        }
    }
    
    /**
     * Reset Sessions
     *
     * Resetting the YouTube sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    public function wpw_auto_poster_yt_reset_session() {

        // Check if YouTube reset user link is clicked and yt_reset_user is set to 1 and YouTube app id is there
        if (isset($_GET['yt_reset_user']) && $_GET['yt_reset_user'] == '1' && !empty($_GET['wpw_yt_app'])) {

            $wpw_yt_app_id = stripslashes_deep($_GET['wpw_yt_app']);

            // Getting stored li app data
            $wpw_auto_poster_yt_sess_data = get_option('wpw_auto_poster_yt_sess_data');

            // Unset particular app value data and update the option
            if (isset($wpw_auto_poster_yt_sess_data[$wpw_yt_app_id])) {
                unset($wpw_auto_poster_yt_sess_data[$wpw_yt_app_id]);
                update_option('wpw_auto_poster_yt_sess_data', $wpw_auto_poster_yt_sess_data);
            }

        }

        /******* Code for selected category Youtube account ******/

        // unset selected Youtube account option for category 
        $cat_selected_social_acc    = array();
        $cat_selected_acc       = get_option( 'wpw_auto_poster_category_posting_acct');
        $cat_selected_social_acc    = ( !empty( $cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;

        if( !empty( $cat_selected_social_acc ) ) {
            foreach ( $cat_selected_social_acc as $cat_id => $cat_social_acc ) {
                if( isset( $cat_social_acc['yt'] ) ) {
                    unset( $cat_selected_acc[ $cat_id ]['yt'] );
                }
            }

            // Update autoposter category FB posting account options
            update_option( 'wpw_auto_poster_category_posting_acct', $cat_selected_acc );    
        }
        
        $wpw_auto_poster_yt_user_id = get_transient('wpw_auto_poster_yt_user_id');
        if( isset( $wpw_auto_poster_yt_user_id ) ) {//destroy userId session
            delete_transient( 'wpw_auto_poster_yt_user_id' );
        }

        $wpw_auto_poster_yt_cache = get_transient('wpw_auto_poster_yt_cache');
        if( isset( $wpw_auto_poster_yt_cache ) ) {//destroy cache
            delete_transient( 'wpw_auto_poster_yt_cache' );
        }

        $wpw_auto_poster_yt_oauth = get_transient('wpw_auto_poster_yt_oauth');
        if( isset( $wpw_auto_poster_yt_oauth ) ) {//destroy oauth
            delete_transient( 'wpw_auto_poster_yt_oauth' );
        }

        $wpw_auto_poster_youtube_oauth = get_transient('wpw_auto_poster_youtube_oauth');
        if( isset( $wpw_auto_poster_youtube_oauth ) ) {//destroy YouTube session
            delete_transient( 'wpw_auto_poster_youtube_oauth' );
        }


        $wpw_auto_poster_youtube_oauth_authorized = get_transient('wpw_auto_poster_youtube_oauth_authorized');
        if( isset( $wpw_auto_poster_youtube_oauth_authorized ) ) {//destroy YouTube session
            delete_transient( 'wpw_auto_poster_youtube_oauth_authorized' );
        }


        
    }
    
    /**
     * youtube Posting
     * 
     * Handles to YouTube posting
     * by post data
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.5.0
     */
    public function wpw_auto_poster_yt_posting( $post, $auto_posting_type = '' ) {

        global $wpw_auto_poster_options, $wpw_auto_poster_logs;
        
        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $this->logs = $wpw_auto_poster_logs;

        $res = $this->wpw_auto_poster_post_to_youtube( $post, $auto_posting_type );
        
        if( isset( $res['success'] ) && !empty( $res['success'] ) ) { //check if error should not occured and successfully tweeted

            //record logs for posting done on YouTube
            $this->logs->wpw_auto_poster_add( 'YouTube posting completed successfully.' );
            
            update_post_meta( $post->ID, $prefix . 'yt_published_on_yt', '1' );

            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time( 'timestamp' );
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            
            return true;
        }
        
        return false;
    }
    
    
    /** 
     * youtube Get All User Data
     * 
     * @package Social Auto Poster - You Tube
     * @since 1.5.0
     */
    public function wpw_auto_poster_get_yt_users() {

        $wpw_auto_poster_yt_sess_data = get_option( 'wpw_auto_poster_yt_sess_data' );

        //Initilize users array
        $user_profile_data = array();

        if ( isset ( $wpw_auto_poster_yt_sess_data ) && !empty( $wpw_auto_poster_yt_sess_data ) ) {
            foreach ( $wpw_auto_poster_yt_sess_data as $sess_key => $sess_data ){

                if ( isset( $sess_data['wpw_auto_poster_yt_cache'] ) && !empty( $sess_data['wpw_auto_poster_yt_cache'] ) ) {

                    $user_profile_data[$sess_key] = $sess_data['wpw_auto_poster_yt_cache'];
                }
            }
        }
        return $user_profile_data;
    }

}