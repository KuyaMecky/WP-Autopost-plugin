<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Tumblr Posting Class
 *
 * Handles all the functions to tweet on twitter
 *
 * @package Social Auto Poster
 * @since 1.3.0
 */
class Wpw_Auto_Poster_TB_Posting {

    public $tumblr, $model, $message, $logs;

    public function __construct() {

        global $wpw_auto_poster_model, $wpw_auto_poster_message_stack, $wpw_auto_poster_logs;

        $this->model    = $wpw_auto_poster_model;
        $this->message  = $wpw_auto_poster_message_stack;
        $this->logs     = $wpw_auto_poster_logs;

        //initialize some tumblr data
        $this->wpw_auto_poster_tb_initialize();

        //add action init for making user to logged in tumblr
        add_action( 'init', array( $this, 'wpw_auto_poster_tb_user_logged_in' ), 20 );
    }
    /**
     * Include Facebook Class
     * 
     * Handles to load facebook class
     * 
     * @package Social Auto Poster
     * @since 1.3.0
     */
    public function wpw_auto_poster_load_tumblr($app_id = false) {
       
        global $wpw_auto_poster_options;

        // Getting tumblr apps
        $tb_apps = wpw_auto_poster_get_tb_apps();

        // If app id is not passed then take first tb app data
        if (empty($app_id)) {
            $tb_apps_keys = array_keys($tb_apps);
            $app_id = reset($tb_apps_keys);
        }

    
        //tumblr declaration
        if( !empty($app_id) && !empty($tb_apps[$app_id]) ) {


            if( !class_exists( 'Client' ) ) {

                require_once( WPW_AUTO_POSTER_SOCIAL_DIR . '/tumblr/vendor/autoload.php' );
            }
        
            return true;
            
        } else {
            
            return false;
        }
    }
    /**
     * Make Logged In User to Tumblr
     * 
     * @package Social Auto Poster
     * @since 1.3.0
     */
    public function wpw_auto_poster_tb_user_logged_in() {
        

        
        global $wpw_auto_poster_options;

        $tumblr_keys = isset( $wpw_auto_poster_options['tumblr_keys'] ) ? $wpw_auto_poster_options['tumblr_keys'] : array();

        // code will excute when user does connect with tumblr
        //check $_GET['wpwautoposter'] isset and equals to tumblr
        //check $_GET['authtumb'] isset and quals to 1
        if( isset( $_GET['authtumb'] ) && $_GET['authtumb'] == '1'
            && isset( $_GET['wpwautoposter'] ) && $_GET['wpwautoposter'] == 'tumblr' && isset( $_GET['wpw_tb_app_id'] ) ) { // if user allows access to tumblr
            
            $tb_app_id = stripslashes_deep($_GET['wpw_tb_app_id']);

            $tb_app_secret = '';

            foreach ( $tumblr_keys as $tumblr_key => $tumblr_value ) {

                if (in_array($tb_app_id, $tumblr_value)){

                    $tb_app_secret = $tumblr_value['consumer_secret'];
                }
            }

            //record logs for grant extended permission
            $this->logs->wpw_auto_poster_add( 'Tumblr Grant Extended Permission', true );

            //load tumblr class
            $tumblr = $this->wpw_auto_poster_load_tumblr( $tb_app_id );
            

            //check tumblr loaded or not
            if( !$tumblr ) return false;

            $pageurl = $this->model->wpw_auto_poster_self_url();
            $wpw_auto_poster_tumb_callback_url = add_query_arg( array( 'auth' => 'tumbauth', 'authtumb' => false ), $pageurl ); 
            

            $wpw_auto_poster_tumb_oauth = new Tumblr\API\Client( $tb_app_id , $tb_app_secret );
            $requestHandler = $wpw_auto_poster_tumb_oauth->getRequestHandler();  
            $requestHandler->setBaseUrl('https://www.tumblr.com/');


            //$wpw_auto_poster_tumb_request_token = $wpw_auto_poster_tumb_oauth->getRequestToken($wpw_auto_poster_tumb_callback_url);
            $wpw_auto_poster_tumb_request_token = $requestHandler->request('POST', 'oauth/request_token', array(
                'oauth_callback' => $wpw_auto_poster_tumb_callback_url
            )); 

            $result = (string) $wpw_auto_poster_tumb_request_token->body;
            parse_str($result, $wpw_auto_poster_tumb_request_token);

            $wpw_auto_poster_tumblr = $wpw_auto_poster_tumb_request_token;
            set_transient('wpw_auto_poster_tumblr',$wpw_auto_poster_tumblr);

            //record logs for token is set properly to session
            $this->logs->wpw_auto_poster_add( 'Request token assign to the session' );
            
         

            if( $wpw_auto_poster_tumblr['oauth_callback_confirmed'] ) {


                //record logs for token is generated successfully
                $this->logs->wpw_auto_poster_add( 'Oauth token successfully generated' );
                $url = $wpw_auto_poster_tumb_oauth->getAuthorizeURL( $wpw_auto_poster_tumb_request_token['oauth_token'] ); 
                wp_redirect( $url );
                exit;
            }
        } //end if

        

        // code will excute when user does connect with tumblr
        if ( isset($_GET['auth']) && $_GET['auth'] == 'tumbauth' 
            && isset( $_GET['wpwautoposter'] ) && $_GET['wpwautoposter'] == 'tumblr' && isset( $_GET['wpw_tb_app_id'] ) ) { 
            
           

            $tb_app_id = stripslashes_deep($_GET['wpw_tb_app_id']);

            $tb_app_secret = '';

            foreach ( $tumblr_keys as $tumblr_key => $tumblr_value ) {

                if (in_array($tb_app_id, $tumblr_value)){

                    $tb_app_secret = $tumblr_value['consumer_secret'];
                }

            }

            //load tumblr class
            $tumblr = $this->wpw_auto_poster_load_tumblr( $tb_app_id );

            //check tumblr loaded or not
            if( !$tumblr ) return false;


            //record logs when user is connected with tumblr
            $this->logs->wpw_auto_poster_add( 'User is connected to tumblr successfully' );

            $wpw_auto_poster_tumblr = get_transient('wpw_auto_poster_tumblr');
            
            

            $wpw_auto_poster_tumb_oauth = new Tumblr\API\Client($tb_app_id, $tb_app_secret, $wpw_auto_poster_tumblr['oauth_token'], $wpw_auto_poster_tumblr['oauth_token_secret']);
            $requestHandler = $wpw_auto_poster_tumb_oauth->getRequestHandler();  
            $requestHandler->setBaseUrl('https://www.tumblr.com/');
            //$wpw_auto_poster_tumb_access_token = $wpw_auto_poster_tumb_oauth->getAccessToken($_REQUEST['oauth_verifier']); 
            
         
            $verifier = trim($_GET['oauth_verifier']);
            $wpw_auto_poster_tumb_access_token = $requestHandler->request('POST', 'oauth/access_token', array('oauth_verifier' => $verifier));
            $out = (string) $wpw_auto_poster_tumb_access_token->body;
            $data = array();
            parse_str($out, $wpw_auto_poster_tumb_access_token);



            $wpw_auto_poster_tumblr['oauth_token'] = isset($wpw_auto_poster_tumb_access_token['oauth_token']) ? $wpw_auto_poster_tumb_access_token['oauth_token'] : $wpw_auto_poster_tumblr['oauth_token'];
            $wpw_auto_poster_tumblr['oauth_token_secret'] = isset($wpw_auto_poster_tumb_access_token['oauth_token_secret']) ? $wpw_auto_poster_tumb_access_token['oauth_token_secret'] : $wpw_auto_poster_tumblr['oauth_token_secret'];
            
            $wpw_auto_poster_tumb_oauth = new Tumblr\API\Client($tb_app_id, $tb_app_secret, $wpw_auto_poster_tumblr['oauth_token'], $wpw_auto_poster_tumblr['oauth_token_secret']);

            $wpw_auto_poster_account_info = $wpw_auto_poster_tumb_oauth->getUserInfo();
            
                
            $wpw_auto_poster_account_url = ( isset($wpw_auto_poster_account_info->user->blogs[0]->url) && !empty($wpw_auto_poster_account_info->user->blogs[0]->url) ) ? $wpw_auto_poster_account_info->user->blogs[0]->url : ''; 

            $wpw_auto_poster_tb_user_id = get_transient('wpw_auto_poster_tb_user_id');
            $wpw_auto_poster_tb_user_id = isset( $wpw_auto_poster_tb_user_id )
                ? $wpw_auto_poster_tb_user_id : $wpw_auto_poster_account_info->user->name;

            $wpw_auto_poster_tb_cache = get_transient('wpw_auto_poster_tb_cache');
            $wpw_auto_poster_tb_cache   = isset( $wpw_auto_poster_tb_cache ) 
                ? $wpw_auto_poster_tb_cache : $wpw_auto_poster_account_info->user;

            $wpw_auto_poster_tb_oauth = get_transient('wpw_auto_poster_tb_oauth');
            $wpw_auto_poster_tb_oauth = isset($wpw_auto_poster_tb_oauth) 
                ? $wpw_auto_poster_tb_oauth : $wpw_auto_poster_tumblr;

            //record logs all user authentication data assign to session
            $this->logs->wpw_auto_poster_add( 'User authentication data assign to session successfully' );
                       
            // start code to manage session from database           
            $wpw_auto_poster_tb_sess_data = get_option( 'wpw_auto_poster_tb_sess_data' );

            if( !isset( $wpw_auto_poster_tb_sess_data[$tb_app_id] ) ) {
                
                $sess_data = array(

                                        'wpw_auto_poster_tb_user_id'    => $wpw_auto_poster_account_info->user->name,
                                        'wpw_auto_poster_tb_cache'      => $wpw_auto_poster_account_info->user,
                                        'wpw_auto_poster_tb_oauth'      => $wpw_auto_poster_tumblr
                                    );
            

                if ( $tb_app_id ) {
                    
                    // Save Multiple Accounts
                    $wpw_auto_poster_tb_sess_data[$tb_app_id] = $sess_data;

                    update_option( 'wpw_auto_poster_tb_sess_data', $wpw_auto_poster_tb_sess_data );
                }

                //record logs for session data updated to options
                $this->logs->wpw_auto_poster_add( 'User data updated to options' );
            }

            // unset session data so there will be no probelm to grant extend another account
            delete_transient( 'wpw_auto_poster_tb_oauth' );
            delete_transient( 'wpw_auto_poster_tb_user_id' );
            delete_transient( 'wpw_auto_poster_tb_cache' );
            delete_transient( 'wpw_auto_poster_tumblr' );
            delete_transient( 'wpw_auto_poster_tumblr_new' );

            //set session to set tab selected in settings page
            $this->message->add_session( 'poster-selected-tab', 'tumblr' );

            //record logs for grant extend successfully
            $this->logs->wpw_auto_poster_add( 'Grant Extended Permission Successfully.' );

            // end code to manage session from database
            $pageurl = add_query_arg( array(    
                                                'auth'              => false, 
                                                'wpwautoposter'     => false,
                                                'oauth_verifier'    => false,
                                                'oauth_token'       => false
                                            )
                                            , $this->model->wpw_auto_poster_self_url() );
            wp_redirect($pageurl);
            exit;
            
        } // end if
    }
    /**
     * Initializes Some Data to session
     * 
     * @package Social Auto Poster
     * @since 1.3.0
     * 
     */
    public function wpw_auto_poster_tb_initialize() {

        global $wpw_auto_poster_options;
        
        //check tumblr consumer key and secret not empty
        if( !empty( $wpw_auto_poster_options['tumblr_consumer_key'] ) && !empty( $wpw_auto_poster_options['tumblr_consumer_secret'] ) ) {
            //Set Session From Options Value
            $wpw_auto_poster_tb_sess_data = get_option( 'wpw_auto_poster_tb_sess_data' );

            if( !empty( $wpw_auto_poster_tb_sess_data ) &&  !isset( $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id'] ) ) { //check user data is not empty
                
                if( isset($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id']) ) {

                    $wpw_auto_poster_tb_user_id = $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id'];
                    set_transient('wpw_auto_poster_tb_user_id',$wpw_auto_poster_tb_user_id);
                }
                
                if ( isset($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_cache']) ) {

                    $wpw_auto_poster_tb_cache = $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_cache'];
                    set_transient('wpw_auto_poster_tb_cache',$wpw_auto_poster_tb_cache);
                }
                
                if ( isset($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_oauth']) ) {

                    $wpw_auto_poster_tb_oauth = $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_oauth'];
                    set_transient('wpw_auto_poster_tb_oauth',$wpw_auto_poster_tb_oauth);
                }
                
                if ( isset($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_oauth']) ){

                    $wpw_auto_poster_tumblr = $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_oauth']; //assign stored oauth token to database
                    set_transient('wpw_auto_poster_tumblr',$wpw_auto_poster_tumblr);
                }
            }

        }
    }
    /**
     * Get Tumblr Login URL
     * 
     * Handles to Return Tumblr URL
     * 
     * @package Social Auto Poster
     * @since 1.3.0
     * 
     */

    public function wpw_auto_poster_get_tb_login_url( $app_id = false ) {

        $preparedurl = add_query_arg( array( 'authtumb' => '1', 'wpwautoposter' => 'tumblr', 'wpw_tb_app_id' => $app_id ) ); 
        return $preparedurl;
    }

    /**
     * Post To Tumblr
     * 
     * Handles to Post on Tumblr account
     * 
     * @package Social Auto Poster
     * @since 1.3.0
     */
    public function wpw_auto_poster_post_to_tumblr( $post, $auto_posting_type ) {



        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
        
        // Get stored tb app grant data
        $wpw_auto_poster_tb_sess_data = get_option('wpw_auto_poster_tb_sess_data');

        $special_characters = '/[\'^£{()}#~?><>,|=©]/';


        //check tumblr user id is set in session and not empty
        if( !empty( $wpw_auto_poster_tb_sess_data ) ) {
        
            //posting logs data
            $posting_logs_data = array();

            //Initialize tags and categories
            $tags_arr = array();
            $cats_arr = array();
            
            //record logs for tumblr posting
            $this->logs->wpw_auto_poster_add( 'Tumblr posting to user account begins.' );
    
            //meta prefix
            $prefix = WPW_AUTO_POSTER_META_PREFIX;

            $post_type = $post->post_type; // Post type

            //Get posting type
            $posting_type_meta  = get_post_meta( $post->ID, $prefix . 'tb_posting_type', true );
            $posting_type_global = 'link';

            //  If quick share
            if( $post_type != 'wpwsapquickshare' ){
                $posting_type_global = !empty( $wpw_auto_poster_options['tb_posting_type'] ) ? $wpw_auto_poster_options['tb_posting_type'] : $wpw_auto_poster_options['tb_type_' . $post_type . '_method'];
            }
            
            $posting_type       = !empty( $posting_type_meta ) ? $posting_type_meta : $posting_type_global;


            //Get image url
            $post_img_meta      = get_post_meta( $post->ID, $prefix . 'tb_post_image', true );
            $post_featured_img  = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

            //check custom image is set in meta and not empty
            if( !empty( $post_img_meta['src'] ) ) {
                $post_img = $post_img_meta['src'];
                
            } elseif ( !empty( $post_featured_img[0] ) ) {
                //check post featrued image is set the use that image
                $post_img = $post_featured_img[0];
            } else {
                //else get post image from settings page

                $tb_global_custom_msg_options = isset( $wpw_auto_poster_options['tb_custom_msg_options'] ) ? $wpw_auto_poster_options['tb_custom_msg_options'] : '';
                
                // get individual post type post image from settings page
                $tb_custom_post_img = ( isset( $wpw_auto_poster_options["tb_custom_img_".$post_type] ) ) ? $wpw_auto_poster_options["tb_custom_img_".$post_type] : '';
                
                $post_img = !empty( $wpw_auto_poster_options['tb_custom_img'] ) ? $wpw_auto_poster_options['tb_custom_img'] : '';

                $post_img = ( $tb_global_custom_msg_options == 'post_msg' && !empty( $tb_custom_post_img ) ) ? $tb_custom_post_img : $post_img;
            }
            
            $post_img_path = '';
            $original_post_img = $post_img;

            if( !empty( $post_img ) && preg_match( $special_characters, $post_img ) ){
                if( wpw_auto_poster_direct_filesystem()->is_writable( WPW_AUTO_POSTER_SAP_UPLOADS_DIR ) ) {
                    $file_type = wp_check_filetype($post_img);
                    $temp_file_name = rand(10000, 9999999999).'.'.$file_type['ext'];
                    $post_img_path = wpw_auto_poster_get_image_path($post_img, $temp_file_name);
                    if( !empty( $post_img_path ) ){
                        $post_img = WPW_AUTO_POSTER_SAP_UPLOADS_URL.$temp_file_name;
                    }
                } else{
                    
                    $this->logs->wpw_auto_poster_add( 'Tumblr error: ' . WPW_AUTO_POSTER_SAP_UPLOADS_DIR.' have not writable permission, please give the permission.' );
                    if( $post_type == 'wpwsapquickshare'){
                        update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                        update_post_meta($post->ID, $prefix . 'tb_error', esc_html__(WPW_AUTO_POSTER_SAP_UPLOADS_DIR.' have not writable permission, please give the permission.', 'wpwautoposter' ) );
                    }
                    sap_add_notice( sprintf( esc_html__('Tumblr: Error while posting %s', 'wpwautoposter' ), WPW_AUTO_POSTER_SAP_UPLOADS_DIR.' have not writable permission, please give the permission.' ), 'error');
                    return false;
                }
            }

            $post_img = apply_filters('wpw_auto_poster_social_media_posting_image', $post_img );

            $wpw_auto_poster_tb_sess_data = get_option( 'wpw_auto_poster_tb_sess_data' );
            $unique = 'false';
    
            //user details
            $userdata = get_userdata( $post->post_author );
            $first_name = $userdata->first_name; //user first name
            $last_name = $userdata->last_name; //user last name
    
            //published status
            $ispublished = get_post_meta( $post->ID, $prefix . 'tb_status', true );

            // Get all selected tags for selected post type for hashtags support
            if(isset($wpw_auto_poster_options['tb_post_type_tags']) && !empty($wpw_auto_poster_options['tb_post_type_tags'])) {

                $custom_post_tags = $wpw_auto_poster_options['tb_post_type_tags'];
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
            if(isset($wpw_auto_poster_options['tb_post_type_cats']) && !empty($wpw_auto_poster_options['tb_post_type_cats'])) {

                $custom_post_cats = $wpw_auto_poster_options['tb_post_type_cats'];
                if(isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])){  
                    foreach($custom_post_cats[$post_type] as $key => $category){
                        $term_list = wp_get_post_terms( $post->ID, $category, array("fields" => "names") );
                        foreach($term_list as $term_single) {
                            $cats_arr[] = str_replace( ' ', '' ,$term_single);
                        }
                    }
                    
                }
            }


            if (!isset($wpw_auto_poster_options['prevent_post_tb_metabox'])) { //check if prevent metabox is not enable
                $wpw_auto_poster_tb_user_id = get_post_meta($post->ID, $prefix . 'tb_user_id');
            } 

            if( $post_type == 'wpwsapquickshare'){
                $wpw_auto_poster_tb_user_id = get_post_meta($post->ID, $prefix . 'tb_user_id',true);
            }
            
            // Getting all tumblr apps
            $tb_apps = wpw_auto_poster_get_tb_apps();

            // Tumblr user id on whose account the post will be posted
            $tb_user_ids = '';

            //check there is tumblr user accounts are set and not empty in metabox
            if (isset($wpw_auto_poster_tb_user_id) && !empty($wpw_auto_poster_tb_user_id)) {
                //users from metabox
                $tb_user_ids = $wpw_auto_poster_tb_user_id;

                /* * *** Backward Compatibility Code Starts **** */
                // If user account is selected in meta so creating data accoring to new method ( Will be helpfull when scheduling is done )
                if (!empty($tb_user_ids)) {

                    $tb_first_app_key = !empty($wpw_auto_poster_options['tumblr_keys'][0]['consumer_key']) ? $wpw_auto_poster_options['tumblr_keys'][0]['consumer_key'] : '';

                    if (!empty($tb_first_app_key)) {
                        foreach ($tb_user_ids as $tb_user_key => $tb_user_data) {
                            if (strpos($tb_user_data, '|') === false) {
                                $tb_user_ids[$tb_user_key] = $tb_user_data . '|' . $tb_first_app_key;
                            }
                        }
                    }
                }
                /** *** Backward Compatibility Code Ends **** */
            } //end if


            /******* Code to posting to selected category Tumblr account ******/

            // get all categories for custom post type
            $categories = wpw_auto_poster_get_post_categories_by_ID( $post_type, $post->ID );

            // Get all selected account list from category
            $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');
            // IF category selected and category social account data found
            if( !empty($categories) && !empty($category_selected_social_acct) && empty($tb_user_ids) ) {
                $tb_clear_cnt = true;
                // GET Tumblr user account ids from post selected categories
                foreach( $categories as $key => $term_id ) {

                    $cat_id = $term_id;
                    // Get Tumblr user account ids form selected category  
                    if (isset($category_selected_social_acct[$cat_id]['tb']) && !empty($category_selected_social_acct[$cat_id]['tb'])) {
                        // clear tumblr user data once
                        if ($tb_clear_cnt)
                            $tb_user_ids = array();
                        $tb_user_ids = array_merge($tb_user_ids, $category_selected_social_acct[$cat_id]['tb']);
                        $tb_clear_cnt = false;
                    }
                }
                if( !empty( $tb_user_ids ) ){
                    $tb_user_ids = array_unique($tb_user_ids);
                }
            }

            //check tumblr user accounts are empty in metabox and set in settings page
            if (empty($tb_user_ids) && isset($wpw_auto_poster_options['tb_type_' . $post_type . '_user']) && !empty($wpw_auto_poster_options['tb_type_' . $post_type . '_user'])) {
                //users from settings
                $tb_user_ids = $wpw_auto_poster_options['tb_type_' . $post_type . '_user'];
            } //end if

            //check tumblr user accounts are empty selected for posting
            if (empty($tb_user_ids)) {

                //record logs for tumblr users are not selected
                $this->logs->wpw_auto_poster_add('Tumblr error: user not selected for posting.');

                if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                    update_post_meta($post->ID, $prefix . 'tb_error', esc_html__('User not selected for posting.', 'wpwautoposter' ) );
                }

                // display error notice on post page
                sap_add_notice( esc_html__('Tumblr: You have not selected any user for the posting.', 'wpwautoposter' ), 'error');
                //return false
                return false;
            } //end if to check user ids are empty
            //convert user ids to single array
            $post_to_users = (array) $tb_user_ids;


            //post title
            $posttitle = $post->post_title;
            $customtitle = get_post_meta( $post->ID, $prefix . 'tb_post_title', true );

            if( !empty( $customtitle ) ) {
				// Fetch the title from post setting
                $customtitle = $customtitle;

            } else{
                if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {
                    // Fetch the title from general setting
					$customtitle = ( !empty( $wpw_auto_poster_reposter_options['repost_tb_global_title_template'] ) ) ? $wpw_auto_poster_reposter_options['repost_tb_global_title_template'] : '';				
                }else{
                    // Fetch the title from general setting
				    $customtitle = ( !empty( $wpw_auto_poster_options['tb_global_title_template'] ) ) ? $wpw_auto_poster_options['tb_global_title_template'] : '';				
                }
				

			}

            $title = !empty( $customtitle ) ? $customtitle : $posttitle;
    
            $wpw_auto_poster_tb_custom_link     = get_post_meta( $post->ID, $prefix . 'tb_custom_post_link', true );

            if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {

                // global custom post msg template for reposter
                $tb_global_custom_message_template = ( isset( $wpw_auto_poster_reposter_options["repost_tb_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["repost_tb_global_message_template_".$post_type] : '';

                $tb_global_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_tb_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_tb_custom_msg_options'] : '';

                // global custom msg template for reposter
                $tb_global_message_template = ( isset( $wpw_auto_poster_reposter_options["repost_tb_global_message_template"] ) )? $wpw_auto_poster_reposter_options["repost_tb_global_message_template"] : '';
            }
            else {

                // custom description from custom post type message
                $tb_global_custom_message_template = ( isset( $wpw_auto_poster_options["tb_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_options["tb_global_message_template_".$post_type] : '';

                $tb_global_custom_msg_options = isset( $wpw_auto_poster_options['tb_custom_msg_options'] ) ? $wpw_auto_poster_options['tb_custom_msg_options'] : '';

                // get global tumblr custom message
                $tb_global_message_template = ( isset( $wpw_auto_poster_options["tb_global_message_template"] ) )? $wpw_auto_poster_options["tb_global_message_template"] : '';
            }

            //custom description from meta
            $tb_meta_message_template = get_post_meta( $post->ID, $prefix . 'tb_post_desc', true );

            $post_content = strip_shortcodes($post->post_content);
            
            $post_content = apply_filters('the_content',$post_content);

            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);

            if ( !empty( $tb_meta_message_template ) ){
                //custom description set at tumblr post meta level
                $description = $tb_meta_message_template;

            } elseif( $tb_global_custom_msg_options == 'post_msg' && !empty( $tb_global_custom_message_template ) ) {
                //custom description set at tumblr global settings custom post type message
                $description = $tb_global_custom_message_template;

            } elseif( !empty( $tb_global_message_template ) ) {
                //custom description set at tumblr global settings
                $description = $tb_global_message_template;

            } else {
                //custom description not set at tumblr global settings then take post content
                $description = $post_content;
            }

            $description = $this->model->wpw_auto_poster_stripslashes_deep( $description, true );

            // Get post excerpt
            $excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

            // Get post tags
            $tags_arr = apply_filters('wpw_auto_poster_tb_hashtags', $tags_arr);
            $hashtags   = ( !empty( $tags_arr ) ) ? '#'.implode( ' #', $tags_arr ) : '';

            // get post categories
            $cats_arr = apply_filters('wpw_auto_poster_tb_hashcats', $cats_arr);
            $hashcats   = ( !empty( $cats_arr ) ) ? '#'.implode( ' #', $cats_arr ) : '';
    
            //if post is published on facebook once then change url to prevent duplication
            if( isset( $ispublished ) && $ispublished == '1' ) { 
                $unique = 'true';
            }
            //post link for posting to facebook user wall
            $postlink = isset( $wpw_auto_poster_tb_custom_link ) && !empty( $wpw_auto_poster_tb_custom_link ) ? $wpw_auto_poster_tb_custom_link : '';
            //if custom link is set or not
            $customlink = !empty( $postlink ) ? 'true' : 'false';
            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link( $postlink, $unique, $post->ID, $customlink, 'tb' );
    
            $full_author = normalize_whitespace( $first_name.' '.$last_name );
            $nickname_author = get_user_meta( $post->post_author, 'nickname', true);

            $search_arr = array( '{title}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{hashtags}', '{hashcats}', '{link}', '{excerpt}','{content}' );
            $replace_arr = array( $posttitle, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), $hashtags, $hashcats, $postlink, $excerpt,$post_content );

            $code_matches = array();
    
            // check if template tags contains {content-numbers}
            if( preg_match_all( '/\{(content)(-)(\d*)\}/', $description, $code_matches ) ) {

                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $trim_content = substr( $post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $trim_content;
            }

            $cf_matches = array();
            // check if template tags contains {CF-CustomFieldName}
            if( preg_match_all( '/\{(CF)(-)(\S*)\}/', $description, $cf_matches ) ) {

                foreach ($cf_matches[0] as $key => $value)
                {
                    $cf_tag = $value;

                    $search_arr[] = $cf_tag;
                }

                foreach ($cf_matches[3] as $key => $value)
                {
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
            
            if( isset( $wpw_auto_poster_options['tumblr_content_type'] ) && !empty( $wpw_auto_poster_options['tumblr_content_type'] ) ) { //check tumblr content is set full or snippest
                //it will consider first 200 characters when snippests is selected
                $description = $this->model->wpw_auto_poster_excerpt( $description, 200 );
                $description .= '...';
            } else {
                //else it will consider full content
                $description = $description;
            }

            //decode html from posting content
            $description = $this->model->wpw_auto_poster_html_decode( $description );

            // replace title tag support value
            $search_arr = array( '{title}', '{full_author}', '{nickname_author}', '{post_type}', '{link}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );
            $replace_arr = array( $posttitle, $full_author, $nickname_author, $post_type, $postlink, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname'), $post_content, $excerpt, $hashtags, $hashcats );

            // check if template tags contains {content-numbers}
            if( preg_match_all( '/\{(content)(-)(\d*)\}/', $title, $code_matches ) ) {

                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $trim_content = substr( $post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $trim_content;
            }

            // check if template tags contains {CF-CustomFieldName}
            if( preg_match_all( '/\{(CF)(-)(\S*)\}/', $title, $cf_matches ) ) {

                foreach ($cf_matches[0] as $key => $value)
                {
                    $cf_tag = $value;

                    $search_arr[] = $cf_tag;
                }

                foreach ($cf_matches[3] as $key => $value)
                {
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
            
            $title = str_replace( $search_arr, $replace_arr, $title );


            //Build posting arguments based on Type
            switch ($posting_type) {
                case 'link':

                    //Set all params
                    $tumblrdata = apply_filters( 'wpw_post_meta_tb_posting_args', array(
                        'type'  => 'link',
                        'title' => $title,
                        'url'   => $postlink,
                        'description'   => $description,
                        'thumbnail'     => $post_img,
                        'excerpt'       => !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID ),
                    ), $post );

                    break;

                case 'photo':

                    //Set all params
                    $tumblrdata = apply_filters( 'wpw_post_meta_tb_posting_args', array(
                        'type'      => 'photo',
                        'caption'   => $title,
                        'link'      => $postlink,
                        'source'    => $post_img,
                    ), $post );

                    break;

                case 'text':
                default:

                    //Final posting description
                    $finaldescription = $postlink . '<br /><br />' . $description;
                    $tumblrdata = apply_filters( 'wpw_post_meta_tb_posting_args', array( 'type' => 'text', 'title' => $title,  'body' => $finaldescription ), $post );
                    //$tumblrdata = apply_filters( 'wpw_post_meta_tb_posting_args', array( 'type' => 'text', 'title' => '',  'body' => '' ), $post );

                    break;
            }

            //posting logs data
            $posting_logs_data = $tumblrdata;
            
            if( isset( $posting_logs_data['thumbnail']) ){
                $posting_logs_data['thumbnail'] = $original_post_img;
            } elseif( isset( $posting_logs_data['source']) ){
                $posting_logs_data['source'] = $original_post_img;
            }

            //record logs for tumblr data
            $this->logs->wpw_auto_poster_add( 'Tumblr post data : ' . var_export( $tumblrdata, true ) );
            
            //initial value of posting flag
            $postflg = false;

            //Send post to tumblr account
            

            if ( !empty( $post_to_users ) ) {

                $tumblr_keys = isset( $wpw_auto_poster_options['tumblr_keys'] ) ? $wpw_auto_poster_options['tumblr_keys'] : array();

                foreach ( $post_to_users as $post_to ) {

                    $tb_post_app_arr = explode('|', $post_to);

                    // Tumblr Posting App Id
                    $tb_app_id          = isset($tb_post_app_arr[0]) ? $tb_post_app_arr[0] : '';
                    $tb_posting_account = isset($tb_post_app_arr[1]) ? $tb_post_app_arr[1] : '';
                 
                    
                    try {   

                        if( isset($wpw_auto_poster_tb_sess_data[$tb_app_id]) ) {

                            // Get tumblr user cache data
                            $wpw_auto_poster_tb_cache = $wpw_auto_poster_tb_sess_data[$tb_app_id]['wpw_auto_poster_tb_cache'];

                            // Get tumblr oauth data
                            $wpw_auto_poster_tumblr = $wpw_auto_poster_tb_sess_data[$tb_app_id]['wpw_auto_poster_tb_oauth'];


                            //tumblr account URL
                            $wpw_auto_poster_account_url = ( isset( $wpw_auto_poster_tb_cache->blogs[0]->url ) && !empty( $wpw_auto_poster_tb_cache->blogs[0]->url) ) ? $wpw_auto_poster_tb_cache->blogs[0]->url : '';
                            $wpw_auto_poster_account_url = trim( str_ireplace( 'http://', '', $wpw_auto_poster_account_url ) );
                            $wpw_auto_poster_account_url = trim( str_ireplace( 'https://', '', $wpw_auto_poster_account_url ) );

                            if ( substr( $wpw_auto_poster_account_url, -1 ) == '/' ) {
                                $wpw_auto_poster_account_url = substr( $wpw_auto_poster_account_url, 0, -1 );
                            }

                            //load tumblr class
                            $tumblr = $this->wpw_auto_poster_load_tumblr( $tb_app_id );

                            //check tumblr loaded or not
                            if( !$tumblr ) return false ;

                            foreach ( $tumblr_keys as $tumblr_key => $tumblr_value ) {

                                if (in_array($tb_app_id, $tumblr_value)){

                                    $tb_app_secret = $tumblr_value['consumer_secret'];
                                }
                            }
                            
                            $wpw_auto_poster_tumb_oauth = new Tumblr\API\Client( $tb_app_id, $tb_app_secret, $wpw_auto_poster_tumblr['oauth_token'], $wpw_auto_poster_tumblr['oauth_token_secret']);  
                            $postinfo = $wpw_auto_poster_tumb_oauth->createPost($tb_posting_account , $tumblrdata); 
                            
                            $code = $postinfo->meta->status;
                            //record logs for post posted to tumblr
                            if( isset( $postinfo->id ) && !empty( $postinfo->id ) ) {
                                
                                $user_profile_data  = isset( $wpw_auto_poster_tb_cache ) ? $wpw_auto_poster_tb_cache : '';
                                $user_profile_id    = isset( $user_profile_data->name ) ? $user_profile_data->name : '';
                                
                                //User details
                                $posting_logs_user_details = array(
                                    'account_id'            => $tb_posting_account,
                                    'display_name'          => $tb_posting_account,
                                    'user_name'             => $tb_posting_account,
                                    'tumblr_consumer_key'   => $tb_app_id,
                                    'tumblr_consumer_secret'=> $tb_app_secret,
                                );
                                
                                //posting logs store into database
                                $this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'tb', $posting_logs_data, $posting_logs_user_details );
                                
                                $this->logs->wpw_auto_poster_add( 'Tumblr posted to user account with Response ID ' . $postinfo->id  );
                                if( $post_type == 'wpwsapquickshare'){
                                    update_post_meta($post->ID, $prefix . 'tb_post_status','success');
                                }
                                
                                if( !empty( $post_img_path ) ){
                                    unlink($post_img_path);
                                }

                                $postflg = true;
                                
                            } //end if to check response id is set & not empty
                            else {
                                
                                if( is_array($postinfo->errors) ) {
                                
                                    // added in version 1.5.4   
                                    if( isset( $postinfo->errors[0] ) && is_object( $postinfo->errors[0] ) ){
                                        $this->logs->wpw_auto_poster_add( 'Tumblr error: ' . $postinfo->errors[0]->message );
                                        if( $post_type == 'wpwsapquickshare'){
                                            update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                                            update_post_meta($post->ID, $prefix . 'tb_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $postinfo->errors[0]->message ) );
                                        }
                                        sap_add_notice( sprintf( esc_html__('Tumblr: Error while posting %s', 'wpwautoposter' ), $postinfo->errors[0]->message ), 'error');
                                    } else{
                                        $this->logs->wpw_auto_poster_add( 'Tumblr error: ' . $postinfo->errors[0] );
                                        if( $post_type == 'wpwsapquickshare'){
                                            update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                                            update_post_meta($post->ID, $prefix . 'tb_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $postinfo->errors[0] ) );
                                        }
                                        sap_add_notice( sprintf( esc_html__('Tumblr: Error while posting %s', 'wpwautoposter' ), $postinfo->errors[0] ), 'error');
                                    }
                                } else {
                                    
                                    // added in version 1.5.4   
                                    $this->logs->wpw_auto_poster_add( 'Tumblr error: ' . $postinfo->errors->Unprocessable );
                                    if( $post_type == 'wpwsapquickshare'){
                                        update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                                        update_post_meta($post->ID, $prefix . 'tb_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $postinfo->errors->Unprocessable ) );
                                    }
                                    sap_add_notice( sprintf( esc_html__('Tumblr: Error while posting %s', 'wpwautoposter' ), $postinfo->errors->Unprocessable ), 'error');
                                }

                                if( !empty( $post_img_path ) ){
                                    unlink($post_img_path);
                                }
                                
                                $postflg = false;
                            }

                        } else {

                            $this->logs->wpw_auto_poster_add( 'Tumblr Grant Extended Permission not se for '.$tb_app_id );
                            if( $post_type == 'wpwsapquickshare'){
                                update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                                update_post_meta($post->ID, $prefix . 'tb_error', esc_html__('Grant Extended Permission not se for '.$tb_app_id, 'wpwautoposter' ) );
                            }
                        }
                        
                    } catch ( Exception $e ) {

                        //record logs exception generated
                        $this->logs->wpw_auto_poster_add( 'Tumblr error: ' . $e->__toString() );
                        if( $post_type == 'wpwsapquickshare'){
                            update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                            update_post_meta($post->ID, $prefix . 'tb_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ) );
                        }
                        sap_add_notice( sprintf( esc_html__('Tumblr: Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ), 'error');
                        return false;
                    }
                }
            }

            return $postflg;

        } else {
            //record logs when grant extended permission not set
            $this->logs->wpw_auto_poster_add( 'Tumblr error: Grant extended permissions not set.' );
            if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'tb_post_status','error');
                update_post_meta($post->ID, $prefix . 'tb_error', esc_html__('Please give Grant extended permission before posting to the Tumblr.', 'wpwautoposter' ) );
            }
            sap_add_notice( esc_html__('Tumblr: Please give Grant extended permission before posting to the Tumblr.', 'wpwautoposter' ), 'error');
        }
    }
    /**
     * Reset Sessions
     *
     * Resetting the Tumblr sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster
     * @since 1.3.0
     */
    public function wpw_auto_poster_tb_reset_session() {


        // Check if tumblr reset user link is clicked and tb_reset_user is set to 1 and tumbrl app id is there
        if (isset($_GET['tb_reset_user']) && $_GET['tb_reset_user'] == '1' && !empty($_GET['wpw_tb_app'])) {

            $wpw_tb_app_id = stripslashes_deep($_GET['wpw_tb_app']);

            // Getting stored tb app data
            $wpw_auto_poster_tb_sess_data = get_option('wpw_auto_poster_tb_sess_data');

            // Unset particular app value data and update the option
            if (isset($wpw_auto_poster_tb_sess_data[$wpw_tb_app_id])) {
                unset($wpw_auto_poster_tb_sess_data[$wpw_tb_app_id]);
                update_option('wpw_auto_poster_tb_sess_data', $wpw_auto_poster_tb_sess_data);
            }
        }
    }

    /**
     * Tumblr Posting
     * 
     * Handles to tumblr posting
     * by post data
     * 
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_tb_posting( $post, $auto_posting_type = '' ) {
        
        global $wpw_auto_poster_options;
        
        $prefix = WPW_AUTO_POSTER_META_PREFIX;

                    
        $res = $this->wpw_auto_poster_post_to_tumblr( $post, $auto_posting_type );
        
        if ( $res ) { //check post is publish on tumblr or not
            
            //record logs for posting done on tumblr
            $this->logs->wpw_auto_poster_add( 'Tumblr posting completed successfully.' );
            
            update_post_meta( $post->ID, $prefix . 'tb_status', '1' );

            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time( 'timestamp' );
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            
            return true;
        }
        
        return false;
    }
}

