<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * LinkedIn Posting Class
 *
 * Handles all the functions to post the submitted and approved
 * reviews to a chosen application owner account
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Poster_Li_Posting {
	
	public $linkedinconfig, $linkedin, $message, $model, $logs, $grantaccessToken;
	
	
	public function __construct() {
	
		global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;
		
		$this->message = $wpw_auto_poster_message_stack;
		$this->model = $wpw_auto_poster_model;
		$this->logs	 = $wpw_auto_poster_logs;
		
		//intialize some data
		$this->wpw_auto_poster_li_initialize();
		
		//add action init for making user to logged in linkedin
		add_action( 'init', array( $this, 'wpw_auto_poster_li_user_logged_in' ) );
		
	}
	
	/**
	 * LinekedIn Get Access Tocken
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_li_get_access_token( $app_id ) {
		global $wpw_auto_poster_options;

		$linkedin_auth_options = !empty($wpw_auto_poster_options['linkedin_auth_options']) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';

		//Get stored li app grant data
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

	
		$wpw_auto_poster_linkedin_oauth = get_transient('wpw_auto_poster_linkedin_oauth');

		$access_tocken	= '';
		
		if( isset( $wpw_auto_poster_li_sess_data ) && !empty( $wpw_auto_poster_li_sess_data ) && isset( $wpw_auto_poster_li_sess_data[$app_id] ) ) {
			
			if( isset( $wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_oauth']['linkedin']['access'] ) ){

				if($linkedin_auth_options == 'appmethod'){
					$access_tocken = $wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_oauth']['linkedin']['access'];

				}else{
					$li_access_data	= $wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_oauth']['linkedin']['access'];
		
					$access_tocken	= isset( $li_access_data['access_token'] ) ? $li_access_data['access_token'] : '';	
				}
				
			}
		
		} elseif( isset( $wpw_auto_poster_linkedin_oauth ) ) {

			$li_access_data	= $wpw_auto_poster_linkedin_oauth;
			
			// the company pages not return as previously we are geting it using $li_access_data['access_token'], so two keys [linkedin] and [access] are missing
			if($linkedin_auth_options == 'appmethod') {
				$access_tocken	= isset( $li_access_data['linkedin']['access'] ) ? $li_access_data['linkedin']['access'] : '';
			} else {
				$access_tocken	= isset( $li_access_data['linkedin']['access']['access_token'] ) ? $li_access_data['linkedin']['access']['access_token'] : '';
			}
		}

		return $access_tocken;
	}
	
	/**
	 * Include LinkedIn Class
	 * 
	 * Handles to load Linkedin class
	 * 
	 * @package Social Auto Poster
 	 * @since 1.0.0
	 */
	public function wpw_auto_poster_load_linkedin($app_id = false) {
		
		global $wpw_auto_poster_options;
		
		// Getting linkedin apps
        $li_apps = wpw_auto_poster_get_li_apps();

        // If app id is not passed then take first li app data
        if (empty($app_id)) {
            $li_apps_keys = array_keys($li_apps);
            $app_id = reset($li_apps_keys);
        }

        $linkedin_auth_options = !empty($wpw_auto_poster_options['linkedin_auth_options']) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';
		//linkedin declaration
		if( !empty($app_id) ) {

			if( !class_exists( 'LinkedInOAuth2' ) ) {
				require_once( WPW_AUTO_POSTER_SOCIAL_DIR . '/linkedin/LinkedIn.OAuth2.class.php' );
			}
			
			if($linkedin_auth_options == 'appmethod' && !empty($li_apps[$app_id]) ){

				$call_back_url	= site_url().'/?wpwautoposter=linkedin&wpw_li_app_id='.$app_id;
			
				//linkedin api configuration
				$this->linkedinconfig = array(
										    	'appKey'       => $app_id,
											  	'appSecret'    => $li_apps[$app_id],
											  	'callbackUrl'  => $call_back_url
										  	 );
				
			}
	
			//Get access token
			$access_token	= $this->wpw_auto_poster_li_get_access_token( $app_id );

			//Load linkedin outh2 class
			$this->linkedin = new LinkedInOAuth2( $access_token );

			return true;
			
		} else {
			
			return false;
		}
	}
		
	public function wpw_auto_poster_get_processed_profile_data( $resultData ){
		
		$localArr  = $resultData['firstName']['preferredLocale'];
		$local     = $localArr['language'].'_'.$localArr['country'];
		$user_data = array();

		$user_data['lastName'] = $resultData['lastName']['localized'][$local];
		$user_data['firstName'] = $resultData['firstName']['localized'][$local];
		$user_data['id'] = $resultData['id'];

		return $user_data;
	}
	/**
	 * Make Logged In User to LinekedIn
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_li_user_logged_in() {
		
		global $wpw_auto_poster_options;

		$linkedin_keys = isset( $wpw_auto_poster_options['linkedin_keys'] ) ? $wpw_auto_poster_options['linkedin_keys'] : array();
		$li_auth_options = !empty( $wpw_auto_poster_options['linkedin_auth_options'] ) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';
	
		//check $_GET['wpwautoposter'] equals to linkedin
		if( isset( $_GET['wpwautoposter'] ) && $_GET['wpwautoposter'] == 'linkedin'
			&& !empty( $_GET['code'] ) && !empty( $_GET['state'] ) && isset( $_GET['wpw_li_app_id'] )) {

			if( $li_auth_options == 'appmethod' ) {
		 		$wpw_auto_poster_options['linkedin_auth_options'] = "graph";
                update_option('wpw_auto_poster_options', $wpw_auto_poster_options );
                update_option( 'wpw_auto_poster_li_sess_data', array() );
            }
			
			//record logs for grant extended permission
			$this->logs->wpw_auto_poster_add( 'LinkedIn Grant Extended Permission', true );
			
			//record logs for get parameters set properly
			$this->logs->wpw_auto_poster_add( 'Get Parameters Set Properly.' );
			
			$li_app_id = stripslashes_deep($_GET['wpw_li_app_id']);

			$li_app_secret = '';

			foreach ( $linkedin_keys as $linkedin_key => $linkedin_value ) {

				if (in_array($li_app_id, $linkedin_value)){

					$li_app_secret = $linkedin_value['app_secret'];
				}

			}

			$callbackUrl = site_url().'/?wpwautoposter=linkedin&wpw_li_app_id='.$li_app_id;


			//load linkedin class
			$linkedin	= $this->wpw_auto_poster_load_linkedin( $li_app_id );
		
			//check linkedin loaded or not
			if( !$linkedin ) return false;
			
			//Get Access token
			$arr_access_token	= $this->linkedin->getAccessToken( $li_app_id, $li_app_secret, $callbackUrl);

			// code will excute when user does connect with linked in
			if( !empty( $arr_access_token['access_token'] ) ) { // if user allows access to linkedin
				
				//record logs for get type initiate called
				$this->logs->wpw_auto_poster_add( 'LinkedIn grant initiate called' );
				
				//record logs for get type response called
				$this->logs->wpw_auto_poster_add( 'LinkedIn permission granted by user' );
				
	        	//record logs for get type initiate called
				$this->logs->wpw_auto_poster_add( 'LinkedIn Request token retrieval success when clicked on allow access by user' );
	        	
				// the request went through without an error, gather user's 'access' tokens
				$wpw_auto_poster_linkedin_oauth['linkedin']['access'] = $arr_access_token;
				set_transient('wpw_auto_poster_linkedin_oauth',$wpw_auto_poster_linkedin_oauth );

				// set the user as authorized for future quick reference
				/*$_SESSION['wpw_auto_poster_linkedin_oauth']['linkedin']['authorized'] = TRUE;*/
				
				//Get User Profiles
				$resultdata	= $this->linkedin->getProfile();
	
				if( !empty( $resultdata ) && !empty( $resultdata['id']) ){

					$resultdata = $this->wpw_auto_poster_get_processed_profile_data($resultdata);
					
					//set user data to sesssion for further use
			        $wpw_auto_poster_li_cache = $resultdata;
			        set_transient( 'wpw_auto_poster_li_cache',$wpw_auto_poster_li_cache );
		           	$wpw_auto_poster_li_user_id = isset( $resultdata['id'] ) ? $resultdata['id'] : '';
		           	set_transient( 'wpw_auto_poster_li_user_id' , $wpw_auto_poster_li_user_id );
		           	
		           	//Get company data
		           	$company_data	= $this->wpw_auto_poster_get_company_data( $li_app_id );

		           	
		           	//update company data in session
		           	$wpw_auto_poster_li_companies = $company_data;
		           	set_transient( 'wpw_auto_poster_li_companies',$wpw_auto_poster_li_companies );
		           	
		           	//Get group data
		           	$group_data	= $this->wpw_auto_poster_get_group_data( $li_app_id, $resultdata['id'] );
		           	
		           	//Update group data in session
		           	$wpw_auto_poster_li_groups = $group_data;
		           	set_transient( 'wpw_auto_poster_li_groups' , $wpw_auto_poster_li_groups );
		           	
					// redirect the user back to the demo page
					$this->message->add_session( 'poster-selected-tab', 'linkedin' );
					
					//set user data  to session
					$this->wpw_auto_poster_set_li_data_to_session( $li_app_id );
					
	                // unset session data so there will be no probelm to grant extend another account
					delete_transient( 'wpw_auto_poster_linkedin_oauth' );
	            	delete_transient( 'wpw_auto_poster_li_oauth' );
	                
					//record logs for grant extend successfully
					$this->logs->wpw_auto_poster_add( 'Grant Extended Permission Successfully.' );
					
					$poster_setting_url = add_query_arg( array('page' => 'wpw-auto-poster-settings' ), admin_url() );
				} else{
					$this->logs->wpw_auto_poster_add( 'LinkedIn User data not found' );
				}
				
				wp_redirect( $poster_setting_url );
				exit;
				
		  	} else {
		  		
				//record logs for access token retrieval
				$this->logs->wpw_auto_poster_add( 'LinkedIn error: Access token retrieval failed' );
	        }
			
		}else if( isset($_GET['wpw_auto_poster_li_app_method']) && $_GET['wpw_auto_poster_li_app_method'] == 'appmethod' ){
		
			 if (isset($_GET['access_token']) && $_GET['access_token'] != '' && $_GET['wpw_li_grant'] == 'true') {

			 	if( $li_auth_options != 'appmethod' ) {
			 		$wpw_auto_poster_options['linkedin_auth_options'] = "appmethod";
                    update_option('wpw_auto_poster_options', $wpw_auto_poster_options );
                    update_option( 'wpw_auto_poster_li_sess_data', array() );
                }

			 	if (!empty($_GET['access_token'])) { 
                    $this->grantaccessToken = $_GET['access_token'];
              
					$wpw_auto_poster_linkedin_oauth['linkedin']['access'] = $this->grantaccessToken;
					set_transient('wpw_auto_poster_linkedin_oauth',$wpw_auto_poster_linkedin_oauth );

                    if (!class_exists('LinkedInOAuth2')) {
			           require_once( WPW_AUTO_POSTER_SOCIAL_DIR . '/linkedin/LinkedIn.OAuth2.class.php' );
			        }
						$linkedinAppMethod = new LinkedInOAuth2($this->grantaccessToken);
						$resultdata 	   = $linkedinAppMethod->getProfile();
					
						if( !empty( $resultdata ) && !empty( $resultdata['id']) ){

							$resultdata = $this->wpw_auto_poster_get_processed_profile_data($resultdata);

							//set user data to sesssion for further use
					        $wpw_auto_poster_li_cache = $resultdata;
					        set_transient( 'wpw_auto_poster_li_cache',$wpw_auto_poster_li_cache );
				           	$wpw_auto_poster_li_user_id = isset( $resultdata['id'] ) ? $resultdata['id'] : '';
				           	set_transient( 'wpw_auto_poster_li_user_id' , $wpw_auto_poster_li_user_id );
				           	
				           	//Get company data
				           	$company_data	= $this->wpw_auto_poster_get_company_data( WPW_AUTO_POSTER_LI_APP_ID );

				           	
				           	//update company data in session
				           	$wpw_auto_poster_li_companies = $company_data;
				           	set_transient( 'wpw_auto_poster_li_companies',$wpw_auto_poster_li_companies );
				           	
				           	//Get group data
				           	$group_data	= $this->wpw_auto_poster_get_group_data( WPW_AUTO_POSTER_LI_APP_ID, $resultdata['id'] );
				           	
				           	//Update group data in session
				           	$wpw_auto_poster_li_groups = $group_data;
				           	set_transient( 'wpw_auto_poster_li_groups' , $wpw_auto_poster_li_groups );
				           	
							// redirect the user back to the demo page
							$this->message->add_session( 'poster-selected-tab', 'linkedin' );
							
							//set user data  to session
							$this->wpw_auto_poster_set_li_data_to_session( WPW_AUTO_POSTER_LI_APP_ID );
							
			                // unset session data so there will be no probelm to grant extend another account
							delete_transient( 'wpw_auto_poster_linkedin_oauth' );
			            	delete_transient( 'wpw_auto_poster_li_oauth' );
			                
							//record logs for grant extend successfully
							$this->logs->wpw_auto_poster_add( 'Grant Extended Permission Successfully.' );
							
							$poster_setting_url = add_query_arg( array('page' => 'wpw-auto-poster-settings' ), admin_url() );

						} else{
							$this->logs->wpw_auto_poster_add( 'LinkedIn User data not found' );
						}
						
						wp_redirect( $poster_setting_url );
						exit;

                }else{

                }

			 }
		}
		else{//end if to check $_GET['wpwautoposter'] equals to linkedin

		} 
				
	}
	
	/**
	 * Initializes Some Data to session
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_li_initialize() {
		
		global $wpw_auto_poster_options;
		
		//check user data is not empty and linkedin app id and secret are not empty
		if( !empty( $wpw_auto_poster_options['linkedin_app_id'] ) && !empty( $wpw_auto_poster_options['linkedin_app_secret'] ) ) {
			
			//Set Session From Options Value
			$wpw_auto_poster_li_sess_data	= get_option( 'wpw_auto_poster_li_sess_data' );
			
			if( !empty( $wpw_auto_poster_li_sess_data ) && !isset( $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_user_id'] ) ) { //check user data is not empty
				
				$wpw_auto_poster_li_user_id	= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_user_id'];
				set_transient('wpw_auto_poster_li_user_id',$wpw_auto_poster_li_user_id);

				$wpw_auto_poster_li_cache		= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_cache'];
				set_transient('wpw_auto_poster_li_cache',$wpw_auto_poster_li_cache);

				$wpw_auto_poster_li_oauth		= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_oauth'];
				set_transient('wpw_auto_poster_li_oauth',$wpw_auto_poster_li_oauth);

				$wpw_auto_poster_linkedin_oauth	= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_oauth']; //assign stored oauth token to database
				set_transient('wpw_auto_poster_linkedin_oauth',$wpw_auto_poster_linkedin_oauth);

				$wpw_auto_poster_li_companies	= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_companies']; //assign stored companies to database
				set_transient('wpw_auto_poster_li_companies',$wpw_auto_poster_li_companies);

				$wpw_auto_poster_li_groups		= $wpw_auto_poster_li_sess_data['wpw_auto_poster_li_groups']; //assign stored groups to database
				set_transient('wpw_auto_poster_li_groups',$wpw_auto_poster_li_groups);
			}
		}
	}
	
	/**
	 * Get LinkedIn Login URL
	 * 
	 * Handles to Return LinkedIn URL
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_get_li_login_url($app_id = false) {
		
		global $wpw_auto_poster_options;

		// Scope before used are w_share, r_basicprofile,r_liteprofile,w_member_social,r_emailaddress, rw_company_admin

		$scope	= array( 'w_member_social', 'r_liteprofile', 'w_member_social','r_emailaddress'); // after updated new scope permission https://docs.microsoft.com/en-us/linkedin/shared/references/migrations/marketing-permissions-migration

		// additional scope if have companies pages approved and have permission on his app as below
		if( isset( $wpw_auto_poster_options['li_company']) && !empty( $wpw_auto_poster_options['li_company'] ) ){
			$scope[] = 'rw_organization_admin';
			$scope[] = 'w_organization_social';
		}
		
		     
		//load linkedin class
		$linkedin = $this->wpw_auto_poster_load_linkedin( $app_id );
		
		//check linkedin loaded or not
		if( !$linkedin ) return false;
		
		$callbackUrl = site_url().'/?wpwautoposter=linkedin&wpw_li_app_id='.$app_id;
		
		try {//Prepare login URL
			$preparedurl	= $this->linkedin->getAuthorizeUrl($app_id, $callbackUrl, $scope );
		} catch( Exception $e ) {
			$preparedurl	= '';
        }
        
		return $preparedurl;
	}
	
	
	/**
	 * Get LinkedIn Login URL
	 * 
	 * Handles to Return LinkedIn URL
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_get_li_app_method_login_url() {
		
		global $wpw_auto_poster_options;

		// Scope before used are w_share, r_basicprofile,r_liteprofile,w_member_social,r_emailaddress, rw_company_admin

		$scope	= array( 'w_member_social', 'r_liteprofile', 'w_member_social','r_emailaddress'); // after updated new scope permission https://docs.microsoft.com/en-us/linkedin/shared/references/migrations/marketing-permissions-migration

		// additional scope if have companies pages approved and have permission on his app as below
		if( isset( $wpw_auto_poster_options['li_company']) && !empty( $wpw_auto_poster_options['li_company'] ) ){
			$scope[] = 'rw_organization_admin';
			$scope[] = 'w_organization_social';
		}
		
		if (!class_exists('LinkedInOAuth2')) {
           require_once( WPW_AUTO_POSTER_SOCIAL_DIR . '/linkedin/LinkedIn.OAuth2.class.php' );
        }
        //check linkedin loaded or not
        $helperli = new LinkedInOAuth2();

        $redirect_URL = WPW_AUTO_POSTER_LI_REDIRECT_URL;
        
        try {
            $preparedurl = $helperli->getAuthorizeUrl(WPW_AUTO_POSTER_LI_APP_ID, $redirect_URL, $scope, site_url() );
            	
            
        } catch (Exception $e) {
            $preparedurl = '';
        }
            
        return $preparedurl;
	}

	/**
	 * Post To LinkedIn
	 * 
	 * Handles to Posting to Linkedin User Wall,
	 * Company Page / Group Posting
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_post_to_linkedin( $post, $auto_posting_type ) {
		
		global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options, $ThemifyBuilder;
		
		// Get stored li app grant data
        $wpw_auto_poster_li_sess_data = get_option('wpw_auto_poster_li_sess_data');

		//meta prefix
		$prefix			= WPW_AUTO_POSTER_META_PREFIX;

		$post_type = $post->post_type; // Post type
		
		//Initilize linkedin posting
		$li_posting		= array();

		//Initialize tags and categories
		$tags_arr = array();
        $cats_arr = array();
		
		// Getting all linkedin apps
        $li_apps = wpw_auto_poster_get_li_apps();
		
		//check linkedin authorized session is true or not
		//need to do for linkedin posting code
		if( !empty( $wpw_auto_poster_li_sess_data ) ) {
			
			//posting logs data
			$posting_logs_data	= array();
					
			$unique	= 'false';
			
			//user data
			$userdata	= get_userdata( $post->post_author );
			$first_name	= $userdata->first_name; //user first name
			$last_name	= $userdata->last_name; //user last name
			
			//published status
			$ispublished	= get_post_meta( $post->ID, $prefix . 'li_status', true );


			// Get all selected tags for selected post type for hashtags support
            if(isset($wpw_auto_poster_options['li_post_type_tags']) && !empty($wpw_auto_poster_options['li_post_type_tags'])) {

                $custom_post_tags = $wpw_auto_poster_options['li_post_type_tags'];
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
            if(isset($wpw_auto_poster_options['li_post_type_cats']) && !empty($wpw_auto_poster_options['li_post_type_cats'])) {

                $custom_post_cats = $wpw_auto_poster_options['li_post_type_cats'];
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
			$posttitle		= $post->post_title;
			$post_content 	= $post->post_content;

			// fix html render issue with themify theme builder
			if( empty( $ThemifyBuilder ) ) {
				$post_content 	= apply_filters('the_content',$post_content);
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
			
			$post_content 	= strip_shortcodes($post_content);


            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);

			
			//custom title from metabox
			$customtitle	= get_post_meta( $post->ID, $prefix . 'li_post_title', true );

			// custom title from custom post type message

			if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {
				
				// global custom post msg template for reposter
                $li_global_custom_message_template = ( isset( $wpw_auto_poster_reposter_options["repost_li_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["repost_li_global_message_template_".$post_type] : '';

                $li_global_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_li_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_li_custom_msg_options'] : '';

                // global custom msg template for reposter
                $li_global_template_text = ( isset( $wpw_auto_poster_reposter_options["repost_li_global_message_template"] ) ) ? $wpw_auto_poster_reposter_options["repost_li_global_message_template"] : '';
			}
			else {

				$li_global_custom_message_template = ( isset( $wpw_auto_poster_options["li_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_options["li_global_message_template_".$post_type] : '';

                $li_global_custom_msg_options = isset( $wpw_auto_poster_options['li_custom_msg_options'] ) ? $wpw_auto_poster_options['li_custom_msg_options'] : '';
				
				$li_global_template_text = ( !empty( $wpw_auto_poster_options['li_global_message_template'] ) ) ? $wpw_auto_poster_options['li_global_message_template'] : '';

			}

            if( !empty( $customtitle ) ) {
				// Fetch the title from post setting
                $customtitle = $customtitle;

            } else{
				if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {
					// Fetch the title from general setting
					$customtitle = ( !empty( $wpw_auto_poster_reposter_options['repost_li_global_title_template'] ) ) ? $wpw_auto_poster_reposter_options['repost_li_global_title_template'] : '';				
				}else{
					// Fetch the title from general setting
					$customtitle = ( !empty( $wpw_auto_poster_options['li_global_title_template'] ) ) ? $wpw_auto_poster_options['li_global_title_template'] : '';				
				}
				

			}
            
			//custom title set use it otherwise user posttiel
			$title	= !empty( $customtitle ) ? $customtitle : $posttitle;

			
			//post image
			$postimage		= get_post_meta( $post->ID, $prefix . 'li_post_image', true );
			

			/**************
			 * Image Priority
			 * If metabox image set then take from metabox
			 * If metabox image is not set then take from featured image
			 * If featured image is not set then take from settings page
			 **************/
			
			//get featured image from post / page / custom post type
			$post_featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

            // global custom post img
            $li_custom_post_img = ( isset( $wpw_auto_poster_options["li_post_image_".$post_type] ) ) ? $wpw_auto_poster_options["li_post_image_".$post_type] : '';

			$li_global_custom_msg_options = isset( $wpw_auto_poster_options['li_custom_msg_options'] ) ? $wpw_auto_poster_options['li_custom_msg_options'] : '';

			//check custom image is set in meta and not empty
			if( isset( $postimage['src'] ) && !empty( $postimage['src'] ) ) {
				$postimage = $postimage['src'];
			} elseif ( isset( $post_featured_img[0] ) && !empty( $post_featured_img[0] ) ) {
				//check post featrued image is set the use that image
				$postimage = $post_featured_img[0];
			} else {
				//else get post image from settings page
				$postimage = ( $li_global_custom_msg_options == 'post_msg' && !empty( $li_custom_post_img ) ) ? $li_custom_post_img : $wpw_auto_poster_options['li_post_image'];
			}
			
			$postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage );

			//post link
			$postlink = get_post_meta( $post->ID, $prefix . 'li_post_link', true );
			$postlink = isset( $postlink ) && !empty( $postlink ) ? $postlink : '';
			//if custom link is set or not
			$customlink = !empty( $postlink ) ? 'true' : 'false';
			
			//do url shortner
			$postlink = $this->model->wpw_auto_poster_get_short_post_link( $postlink, $unique, $post->ID, $customlink, 'li' );
			
			// not sure why this code here it should be above $postlink but lets keep it here
			//if post is published on linkedin once then change url to prevent duplication
			if( isset( $ispublished ) && $ispublished == '1' ) {
				$unique = 'true';
			}
			
			//comments
			$description = get_post_meta( $post->ID, $prefix . 'li_post_comment', true );

			$description = !empty( $description ) ? $description : '';
			$description = apply_filters( 'wpw_auto_poster_li_comments', $description, $post );

			if( $li_global_custom_msg_options == 'post_msg' && !empty( $li_global_custom_message_template ) && empty( $description ) ) {

                $description = $li_global_custom_message_template;
            }
            elseif( empty( $description ) && !empty( $li_global_template_text ) ) {

                $description = $li_global_template_text;
            } elseif( empty( $description ) ){
            	//get linkedin posting description
				$description = $post_content;
            }


			// Get post excerpt
			$excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

			// Get post tags
            $tags_arr   = apply_filters('wpw_auto_poster_li_hashtags', $tags_arr);
            $hashtags   = ( !empty( $tags_arr ) ) ? '#'.implode( ' #', $tags_arr ) : '';

            // get post categories
            $cats_arr   = apply_filters('wpw_auto_poster_li_hashcats', $cats_arr);
            $hashcats   = ( !empty( $cats_arr ) ) ? '#'.implode( ' #', $cats_arr ) : '';

			
            $full_author = normalize_whitespace( $first_name.' '.$last_name );
            $nickname_author = get_user_meta( $post->post_author, 'nickname', true);

			$search_arr 		= array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );
			$replace_arr 		= array( $posttitle , $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

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


			$description = $this->model->wpw_auto_poster_stripslashes_deep( $description );

			$description = $this->model->wpw_auto_poster_html_decode( $description );
				
			// replace title with tag support value					
			$search_arr 		= array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );
			$replace_arr 		= array( $posttitle, $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

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
            
			// replace title with tag support value
			$title 				= str_replace( $search_arr, $replace_arr, $title );

			//Get title
			$title 				= $this->model->wpw_auto_poster_html_decode( $title );

			//use 400 character to post to linkedin will use as title
			$description 	= $this->model->wpw_auto_poster_excerpt( $description, 3000 );

			//Get comment
			$comments 			= $this->model->wpw_auto_poster_html_decode( $description );
			$comments			= $this->model->wpw_auto_poster_excerpt( $comments, 700 );
			
			//Linkedin Profile Data from setting //_wpweb_li_post_profile
			$li_post_profiles 	= get_post_meta( $post->ID, $prefix . 'li_post_profile' );

			if( $post_type == 'wpwsapquickshare'){
                $li_post_profiles = get_post_meta($post->ID, $prefix . 'li_post_profile',true);
            }

			/******* Code to posting to selected category Linkdin account ******/

			// get all categories for custom post type
			$categories = wpw_auto_poster_get_post_categories_by_ID( $post_type, $post->ID );
			
			// Get all selected account list from category
			$category_selected_social_acct = get_option( 'wpw_auto_poster_category_posting_acct');
			
			// IF category selected and category social account data found
			if( !empty( $categories ) && !empty( $category_selected_social_acct ) && empty( $li_post_profiles ) ) {
				$li_clear_cnt = true;

				// GET Linkdin user account ids from post selected categories
				foreach ( $categories as $key => $term_id ) {
					
					$cat_id = $term_id;
					// Get TW user account ids form selected category  
					if( isset( $category_selected_social_acct[$cat_id]['li'] ) && !empty( $category_selected_social_acct[$cat_id]['li'] ) ) {
						// clear TW user data once
						if( $li_clear_cnt)
							$li_post_profiles = array();
						$li_post_profiles = array_merge($li_post_profiles, $category_selected_social_acct[$cat_id]['li'] );
						$li_clear_cnt = false;
					}
				}
				if( !empty( $li_post_profiles ) ) {
					$li_post_profiles = array_unique($li_post_profiles);
				}
			}

		
			if( empty( $li_post_profiles ) ) {//If profiles are empty in metabox
				
				$li_post_profiles	= isset( $wpw_auto_poster_options['li_type_'.$post->post_type.'_profile'] ) ? $wpw_auto_poster_options['li_type_'.$post->post_type.'_profile'] : '';
			}
			
			$content = array( 
								'title' 				=> $title,
								'submitted-url'			=> $postlink,
								'comment'				=> $comments,
								'submitted-image-url'	=> $postimage,
								'description'			=> $description
							);

			//posting logs data
			$posting_logs_data = array(	
											'title' 		=> $title,
											'comment' 		=> $comments,
											'link' 			=> $postlink,
											'image' 		=> $postimage,
											'description'	=> $description
										);
			
			//Get all Profiles
			$profile_datas	= $this->wpw_auto_poster_get_profiles_data();
			
			//record logs for linkedin data
			$this->logs->wpw_auto_poster_add( 'LinkedIn post data : ' . var_export( $content, true ) );
			
			//get user profile data
			$user_profile_data	= $this->wpw_auto_poster_get_li_user_data();			
			
			//Initilize all user/company/group data
			$company_data = $group_data = $userwall_data = $display_name_data = $display_id_data = array();
			
			//initial value of posting flag
			$postflg = false;
			
			try {
				if( !empty( $li_post_profiles ) ) {
			
					foreach ( $li_post_profiles as $li_post_profile ) {
						
						//Initilize log user details
						$posting_logs_user_details	= array();
						
						$split_profile	= explode( ':|:', $li_post_profile );
						
						$profile_type	= isset( $split_profile[0] ) ? $split_profile[0] : '';
						$profile_id		= isset( $split_profile[1] ) ? $split_profile[1] : '';
						$li_post_app_id = isset($split_profile[2]) ? $split_profile[2] : ''; // Linkedin App Id
						$li_post_app_sec = isset($li_apps[$li_post_app_id]) ? $li_apps[$li_post_app_id] : ''; // Linkedin App Sec

						$app_access_token = $this->wpw_auto_poster_li_get_access_token( $li_post_app_id);
						

						$linkedin_auth_options = !empty($wpw_auto_poster_options['linkedin_auth_options']) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';							
						// Load linkedin class
						if($linkedin_auth_options == 'appmethod'){
							$linkedin = $this->wpw_auto_poster_load_linkedin( WPW_AUTO_POSTER_LI_APP_ID );
						}else{
							$linkedin = $this->wpw_auto_poster_load_linkedin( $li_post_app_id );	
						}
                    	
      					// Check linkedin class is exis or not
	                    if (!$linkedin) {
	                        $this->logs->wpw_auto_poster_add('Linkedin error: Linkedin is not initialized with ' . $li_post_app_id . ' App.'); // Record logs for linkedin not initialized
	                        if( $post_type == 'wpwsapquickshare'){
                                update_post_meta($post->ID, $prefix . 'li_post_status','error');
                                update_post_meta($post->ID, $prefix . 'li_error', esc_html__('Linkedin is not initialized with ' . $li_post_app_id . ' App.', 'wpwautoposter') );
                            }
	                        continue;
	                    }

	                     // Getting stored linkedin app data
                    	$li_stored_app_data = isset($wpw_auto_poster_li_sess_data[$li_post_app_id]) ? $wpw_auto_poster_li_sess_data[$li_post_app_id] : array();
                    	
                    	// Get user cache data
                    	$user_cache_data = isset($li_stored_app_data['wpw_auto_poster_li_cache']) ? $li_stored_app_data['wpw_auto_poster_li_cache'] : array();

						//Linkedin Log user details
						$posting_logs_user_details['account_id'] 			= $profile_id;
						$posting_logs_user_details['linkedin_app_id']		= $li_post_app_id;
						$posting_logs_user_details['linkedin_app_secret']	= $li_post_app_sec;
						
						if( $profile_type == 'user' && $user_cache_data['id'] == $profile_id ) { // Check facebook main user data
							
							$user_first_name= isset( $user_cache_data['firstName'] ) ? $user_cache_data['firstName'] : '';
							$user_last_name = isset( $user_cache_data['lastName'] ) ? $user_cache_data['lastName'] : '';
							$user_email		= isset( $user_cache_data['email-address'] ) ? $user_cache_data['email-address'] : '';
							$profile_url 	= isset( $user_cache_data['publicProfileUrl'] ) ? $user_cache_data['publicProfileUrl'] : '';
							$display_name	= $user_first_name . ' ' . $user_last_name;
							
							$posting_logs_user_details['display_name']	= $display_name;
							$posting_logs_user_details['first_name']	= $user_first_name;
							$posting_logs_user_details['last_name']		= $user_last_name;
							$posting_logs_user_details['user_name']		= $user_first_name;
							$posting_logs_user_details['user_email']	= $user_email;
							$posting_logs_user_details['profile_url']	= $profile_url;
							
						} else {
							
							//Account Name
							$posting_logs_user_details['display_name'] = isset( $profile_datas[$li_post_profile] ) ? $profile_datas[$li_post_profile] : '';
						}
			
						switch ( $profile_type ) {
							
							case 'user':
								
								if( !empty( $profile_id ) ) {

									//Filter content
									$content 	= apply_filters( 'wpw_auto_poster_li_content', $content, $post, $profile_type );
									
									$response	= $this->linkedin->shareStatus( $content,'urn:li:person:'.$profile_id, $app_access_token );

									//record logs for linkedin users are not selected
									$this->logs->wpw_auto_poster_add( 'Linkedin posted to User ID : ' . $profile_id  . '' );
									
									if( !empty( $response['id'] ) ) {
										$postflg	= true;
									}
								}
								
							break;
							
							case 'group':

								//Filter content and title
								$title 		= apply_filters( 'wpw_auto_poster_li_title', $title, $post, $profile_type );
								$content 	= apply_filters( 'wpw_auto_poster_li_content', $content, $post, $profile_type );

								$response	= $this->linkedin->postToGroup( $profile_id, $title, $description, $content );
								
								//record logs for linkedin users are not selected
								$this->logs->wpw_auto_poster_add( 'Linkedin posted to Group ID : ' . $profile_id  . '' );
								
								$postflg	= true;
								
							break;
							
							case 'company':

								//Filter content and title
								$title 		= apply_filters( 'wpw_auto_poster_li_title', $title, $post, $profile_type );
								$content 	= apply_filters( 'wpw_auto_poster_li_content', $content, $post, $profile_type );

								// $response	= $this->linkedin->postToCompany( $profile_id, $title, $content );

								$response	= $this->linkedin->shareStatus( $content,'urn:li:organization:'.$profile_id, $app_access_token );
								
								if( !empty( $response['id'] ) ) {
									
									$postflg	= true;

									//record logs for linkedin group are not selected
									$this->logs->wpw_auto_poster_add( 'Linkedin posted to Company ID : ' . $profile_id  . '' );
								}
								
							break;
						}
						
						if( $postflg ) {
							
							//posting logs store into database
							$this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'li', $posting_logs_data, $posting_logs_user_details );
							if( $post_type == 'wpwsapquickshare'){
			                    update_post_meta($post->ID, $prefix . 'li_post_status','success');
			                }
			                
							$li_posting['success'] = 1;
							
						} else {
							
							$li_posting['fail'] = 1;
							if( $post_type == 'wpwsapquickshare'){
			                    update_post_meta($post->ID, $prefix . 'li_post_status','error');
			                    update_post_meta($post->ID, $prefix . 'li_error', esc_html__('Posting fail, please try again.', 'wpwautoposter' ) );
			                }
						}
						
					}
				}
			} catch ( Exception $e ) {
				
				//record logs exception generated
				$this->logs->wpw_auto_poster_add( 'LinkedIn error: ' . $e->__toString() );

				if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'li_post_status','error');
                    update_post_meta($post->ID, $prefix . 'li_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ) );
                }

				// display error notice on post page
				sap_add_notice( sprintf( esc_html__('LinkedIn: Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ), 'error');
				return false;
			}
			
		} else {
			
			//record logs when grant extended permission not set
			$this->logs->wpw_auto_poster_add( 'LinkedIn error: Grant extended permissions not set.' );
			// display error notice on post page
			sap_add_notice( esc_html__('LinkedIn: Please give grant extended permission before posting to the LinkedIn.', 'wpwautoposter' ), 'error');
		}
		
		return $li_posting;
	}
	
	/**
	 * Get LinkedIn Profiles
	 * 
	 * Function to get LinkedIn profiles
	 * UserWall/Company/Groups
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_get_profiles_data() {
		
		$profiles	= array();
		
		//Get Users Data
		$users		= $this->wpw_auto_poster_get_li_users();
		
		//Get Company Data
		$companies	= $this->wpw_auto_poster_get_li_companies();

		//Get Groups Data
		$groups		= $this->wpw_auto_poster_get_li_groups();

		if( !empty( $users ) ) {//If User Data is not empty
			
			foreach ( $users as $app_id => $user_value) {
				$user_id	= isset( $user_value['id'] ) ? $user_value['id'] : '';
				$first_name	= isset( $user_value['firstName'] ) ? $user_value['firstName'] : '';
				$last_name	= isset( $user_value['lastName'] ) ? $user_value['lastName'] : '';
			
				if( !empty( $user_id ) ) {
					$profiles[ 'user:|:'. $user_id .':|:'.$app_id ]	= $first_name.' '.$last_name.' '.'( '. $user_id .' )';
				}
			}
		}
		
		if( !empty( $companies ) ) {//If Company Data is not empty
			
			foreach ( $companies as $app_id => $company_details ) {
				
				foreach ($company_details as $company_id => $company_name) {
					$profiles[ 'company:|:'. $company_id .':|:'.$app_id ]	= $company_name;
				}
			}
		}
		
		if( !empty( $groups ) ) {//If Group Data is not empty
			
			foreach ( $groups as $app_id => $group_details ) {
				
				foreach ($group_details as $group_id => $group_name) {
					$profiles[ 'group:|:'. $group_id .':|:'.$app_id ]	= $group_name;
				}
				
			}
		}
		
		return $profiles;
	}
	
	/**
	 * Get LinkedIn User Data
	 *
	 * Function to get LinkedIn User Data
	 *
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_get_li_user_data() {
		
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

		$user_profile_data = array();
		$wpw_auto_poster_li_cache = get_transient( 'wpw_auto_poster_li_cache' );
		if ( isset( $wpw_auto_poster_li_cache ) && !empty( $wpw_auto_poster_li_cache ) ) {
		
			$user_profile_data = $wpw_auto_poster_li_cache;
		}
		
		return $user_profile_data;
	}
	
	/**
	 * Set Session Data of linkedin to session
	 * 
	 * Handles to set user data to session
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_set_li_data_to_session($li_app_id = false) {
		global $wpw_auto_poster_options;
		//fetch user data who is grant the premission
		$liuserdata = $this->wpw_auto_poster_get_li_user_data();
		$linkedin_auth_options = !empty($wpw_auto_poster_options['linkedin_auth_options']) ? $wpw_auto_poster_options['linkedin_auth_options'] : 'graph';
		
		if( isset( $liuserdata['id'] ) && !empty( $liuserdata['id'] ) ) {
			
			//record logs for user id
			$this->logs->wpw_auto_poster_add( 'LinkedIn User ID : '.$liuserdata['id'] );
			
			try {
		        
		        $wpw_auto_poster_li_user_id = get_transient( 'wpw_auto_poster_li_user_id' );
		        $wpw_auto_poster_li_user_id = isset( $wpw_auto_poster_li_user_id )
					? $wpw_auto_poster_li_user_id : $liuserdata['id'];


				$wpw_auto_poster_li_cache = get_transient( 'wpw_auto_poster_li_cache' );
				$wpw_auto_poster_li_cache	= isset( $wpw_auto_poster_li_cache ) 
					? $wpw_auto_poster_li_cache : $liuserdata;
					
				$wpw_auto_poster_li_oauth = get_transient( 'wpw_auto_poster_li_oauth' );
				$wpw_auto_poster_linkedin_oauth = get_transient( 'wpw_auto_poster_linkedin_oauth' );
				$wpw_auto_poster_li_oauth = isset( $wpw_auto_poster_li_oauth ) 
					? $wpw_auto_poster_li_oauth : $wpw_auto_poster_linkedin_oauth;
				
				$wpw_auto_poster_li_companies = get_transient( 'wpw_auto_poster_li_companies' );
				$wpw_auto_poster_li_companies = isset( $wpw_auto_poster_li_companies ) 
					? $wpw_auto_poster_li_companies : '';
				
				$wpw_auto_poster_li_groups = get_transient( 'wpw_auto_poster_li_groups' );
				$wpw_auto_poster_li_groups = isset( $wpw_auto_poster_li_groups )
					? $wpw_auto_poster_li_groups : '';
				
				// start code to manage session from database 			
				$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );
				
				if( empty( $wpw_auto_poster_li_sess_data ) ) {
					$wpw_auto_poster_li_sess_data = array();
				}
				
				// added code to fixed the issue that after grant extend permission data was not store, if there are some string value in option
				if( !empty( $wpw_auto_poster_li_sess_data ) && !is_array( $wpw_auto_poster_li_sess_data ) ) {

					$wpw_auto_poster_li_sess_data = array();
				}

				if( !isset( $wpw_auto_poster_li_sess_data[$li_app_id] ) && $linkedin_auth_options != 'appmethod' ) {				
					
					$sess_data = array(
											'wpw_auto_poster_li_user_id'	=> $wpw_auto_poster_li_user_id,
											'wpw_auto_poster_li_cache'		=> $liuserdata,
											'wpw_auto_poster_li_oauth'		=> $wpw_auto_poster_linkedin_oauth,
											'wpw_auto_poster_li_companies'	=> $wpw_auto_poster_li_companies,
											'wpw_auto_poster_li_groups'		=> $wpw_auto_poster_li_groups
										);
					
					if ( $li_app_id ) {
			      	
			      		// Save Multiple Accounts
                        $wpw_auto_poster_li_sess_data[$li_app_id] = $sess_data;

			      		update_option( 'wpw_auto_poster_li_sess_data', $wpw_auto_poster_li_sess_data );

			      	}

			      	//record logs for session data updated to options
					$this->logs->wpw_auto_poster_add( 'Session Data Updated to Options' );
				} else {

					if ( $linkedin_auth_options == 'appmethod' ) {

						$sess_data = array(
												'wpw_auto_poster_li_user_id'	=> $wpw_auto_poster_li_user_id,
												'wpw_auto_poster_li_cache'		=> $liuserdata,
												'wpw_auto_poster_li_oauth'		=> $wpw_auto_poster_linkedin_oauth,
												'wpw_auto_poster_li_companies'	=> $wpw_auto_poster_li_companies,
												'wpw_auto_poster_li_groups'		=> $wpw_auto_poster_li_groups
											);
						$newdata = array();
						$newdata[$wpw_auto_poster_li_user_id] = $sess_data;

						$final_data = array_merge($wpw_auto_poster_li_sess_data, $newdata);

						update_option( 'wpw_auto_poster_li_sess_data', $final_data );
					}

				}
			} catch( Exception $e ) {

		 	  	$liuserdata = null;
			}
		}
	}
	
	/**
	 * Reset Sessions
	 *
	 * Resetting the Linkedin sessions when the admin clicks on
	 * its link within the settings page.
	 *
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_li_reset_session() {
		
		// Check if linkedin reset user link is clicked and li_reset_user is set to 1 and linkedin app id is there
        if (isset($_GET['li_reset_user']) && $_GET['li_reset_user'] == '1' && !empty($_GET['wpw_li_app'])) {

        	$wpw_li_app_id = stripslashes_deep($_GET['wpw_li_app']);

            // Getting stored li app data
            $wpw_auto_poster_li_sess_data = get_option('wpw_auto_poster_li_sess_data');

            // Unset particular app value data and update the option
            if (isset($wpw_auto_poster_li_sess_data[$wpw_li_app_id])) {
                unset($wpw_auto_poster_li_sess_data[$wpw_li_app_id]);
                update_option('wpw_auto_poster_li_sess_data', $wpw_auto_poster_li_sess_data);
            }

        }

		/******* Code for selected category Linkdin account ******/

		// unset selected Linkdin account option for category 
		$cat_selected_social_acc 	= array();
		$cat_selected_acc 		= get_option( 'wpw_auto_poster_category_posting_acct');
		$cat_selected_social_acc 	= ( !empty( $cat_selected_acc) ) ? $cat_selected_acc : $cat_selected_social_acc;

		 if( !empty( $cat_selected_social_acc ) ) {
		 	foreach ( $cat_selected_social_acc as $cat_id => $cat_social_acc ) {
		 		if( isset( $cat_social_acc['li'] ) ) {
					unset( $cat_selected_acc[ $cat_id ]['li'] );
		 		}
		 	}

			// Update autoposter category FB posting account options
			update_option( 'wpw_auto_poster_category_posting_acct', $cat_selected_acc ); 	
		 }
		
		$wpw_auto_poster_li_user_id = get_transient( 'wpw_auto_poster_li_user_id' );
		if( isset( $wpw_auto_poster_li_user_id ) ) {//destroy userId session
			delete_transient( 'wpw_auto_poster_li_user_id' );
		}
		$wpw_auto_poster_li_cache = get_transient( 'wpw_auto_poster_li_cache' );
		if( isset( $wpw_auto_poster_li_cache ) ) {//destroy cache
			delete_transient( 'wpw_auto_poster_li_cache' );
		}
		$wpw_auto_poster_li_oauth = get_transient( 'wpw_auto_poster_li_oauth' );
		if( isset( $wpw_auto_poster_li_oauth ) ) {//destroy oauth
			delete_transient( 'wpw_auto_poster_li_oauth' );
		}
		$wpw_auto_poster_li_companies = get_transient( 'wpw_auto_poster_li_companies' );
		if( isset( $wpw_auto_poster_li_companies ) ) {//destroy company session
			delete_transient( 'wpw_auto_poster_li_companies' );
		}
		$wpw_auto_poster_li_groups = get_transient( 'wpw_auto_poster_li_groups' );
		if( isset( $wpw_auto_poster_li_groups ) ) {//destroy group session
			delete_transient( 'wpw_auto_poster_li_groups' );
		}
		$wpw_auto_poster_linkedin_oauth = get_transient( 'wpw_auto_poster_linkedin_oauth' );
		if( isset( $wpw_auto_poster_linkedin_oauth ) ) {//destroy linkedin session
			delete_transient( 'wpw_auto_poster_linkedin_oauth' );
		}
	}
	
	/**
	 * LinkedIn Posting
	 * 
	 * Handles to linkedin posting
	 * by post data
	 * 
	 * @package Social Auto Poster
 	 * @since 1.5.0
	 */
	public function wpw_auto_poster_li_posting( $post, $auto_posting_type = '' ) {
		
		global $wpw_auto_poster_options;
		
		$prefix = WPW_AUTO_POSTER_META_PREFIX;

		
		$res = $this->wpw_auto_poster_post_to_linkedin( $post, $auto_posting_type );
		
		if( isset( $res['success'] ) && !empty( $res['success'] ) ) { //check if error should not occured and successfully tweeted
			
			//record logs for posting done on linkedin
			$this->logs->wpw_auto_poster_add( 'LinkedIn posting completed successfully.' );
			
			update_post_meta( $post->ID, $prefix . 'li_status', '1' );

			// get current timestamp and update meta as published date/time
            $current_timestamp = current_time( 'timestamp' );
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            
			return true;
		}
		
		return false;
	}
	
	/** 
	 * Linkedin Get Company Data
	 * 
	 * @package Social Auto Poster
	 * @since 1.5.0
	 */
	public function wpw_auto_poster_get_company_data( $app_id ) {
		
		//Initilize company array
		$company_data	= array();

		// Get stored li app grant data
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

		if( isset($wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_companies'] ) ) {
			
			$company_data	= $wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_companies'];
		
		} else {
			
			//Load linkedin class
			$this->wpw_auto_poster_load_linkedin( $app_id );
			
			if( !empty( $this->linkedin ) ) { //If linkedin object is found
				
				//Get companies data
				$results	= $this->linkedin->getAdminCompanies();

				//Companies data
				$companies	= isset( $results['elements'] ) ? $results['elements'] : array();
				
				if( !empty( $companies ) ) {//If company data is not empty
					foreach ( $companies as $company ) {
						
						//Get company Id
						$company_array_id	= isset( $company['organizationalTarget~']['id'] ) ? $company['organizationalTarget~']['id'] : '';
						//Get company name
						$company_array_name	= isset( $company['organizationalTarget~']['localizedName'] ) ? $company['organizationalTarget~']['localizedName'] : '';
						
						//If company Id not found
						if( !empty( $company_array_id ) ) {
							$company_data[$company_array_id]	= $company_array_name;
						}
					}
				}
			}
		}
		
		return $company_data;
	}
		
	/** 
	 * Linkedin Get Group Data
	 * 
	 * @package Social Auto Poster
	 * @since 1.5.0
	 */
	public function wpw_auto_poster_get_group_data( $app_id, $person_id = '' ) { 
		
		//Initilize group array
		$group_data	= array();

		//Get stored li app grant data
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );
		
		if( isset($wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_groups'] ) ) {
			
			$group_data	= $wpw_auto_poster_li_sess_data[$app_id]['wpw_auto_poster_li_groups'];
		
		} else {
			
			//Load linkedin class
			$this->wpw_auto_poster_load_linkedin( $app_id );
			
			if( !empty( $this->linkedin ) ) { //If linkedin object is found
				
				//Get groups data
				$results	= $this->linkedin->getGroups($person_id);
				
				$groups		= isset( $results['elements'] ) ? $results['elements'] : array();
				
				if( !empty( $groups ) ) {//If groups is not empty
					
					foreach ( $groups as $group ) {
						
						//Get code is owner/member
						$membershipState = isset( $group['status'] ) ? $group['status'] : '';
						
						if( $membershipState == 'OWNER' ) {//If group owner

							if( !empty( $group['group'] ) ){
								
								$group_details = $this->linkedin->getGroup($group['group']);
								if( !empty( $group_details ) && !empty( $group_details['id'] ) ) {
									//Get group Id
									$group_id	= $group_details['id'];

									//Get group name
									$group_name	= isset( $group_details['title'] ) ? $group['title']['value'] : '';
									
									if( !empty( $group_id ) ) {//Group id is not empty
										$group_data[$group_id]	= $group_name;
									}
								}
							}
						}
					}
				}
			}
		}
		
		return $group_data;
	}

	/** 
	 * Linkedin Get All User Data
	 * 
	 * @package Social Auto Poster
	 * @since 1.5.0
	 */
	public function wpw_auto_poster_get_li_users() {
		
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

		//Initilize users array
		$user_profile_data = array();

		if ( isset ( $wpw_auto_poster_li_sess_data ) && !empty( $wpw_auto_poster_li_sess_data ) ) {

			foreach ( $wpw_auto_poster_li_sess_data as $sess_key => $sess_data ){

				if ( isset( $sess_data['wpw_auto_poster_li_cache'] ) && !empty( $sess_data['wpw_auto_poster_li_cache'] ) ) {
			
					$user_profile_data[$sess_key] = $sess_data['wpw_auto_poster_li_cache'];
				}
			}
		}
		return $user_profile_data;
	}

	/** 
	 * Linkedin Get All Company Data
	 * 
	 * @package Social Auto Poster
	 * @since 1.5.0
	 */
	public function wpw_auto_poster_get_li_companies() {
		
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

		//Initilize company array
		$company_data	= array();
		
		if ( isset( $wpw_auto_poster_li_sess_data ) && !empty( $wpw_auto_poster_li_sess_data ) ) {
			foreach ( $wpw_auto_poster_li_sess_data as $sess_key => $sess_data ){

				if ( isset( $sess_data['wpw_auto_poster_li_companies'] ) && !empty( $sess_data['wpw_auto_poster_li_companies'] ) ) {
			
					$company_data[$sess_key] = $sess_data['wpw_auto_poster_li_companies'];
				}
			}
		}

		return $company_data;
	}

	/** 
	 * Linkedin Get All Group Data
	 * 
	 * @package Social Auto Poster
	 * @since 1.5.0
	 */
	public function wpw_auto_poster_get_li_groups() { 
		
		$wpw_auto_poster_li_sess_data = get_option( 'wpw_auto_poster_li_sess_data' );

		//Initilize group array
		$group_data	= array();
		
		if ( isset( $wpw_auto_poster_li_sess_data ) && !empty( $wpw_auto_poster_li_sess_data ) ) {
			foreach ( $wpw_auto_poster_li_sess_data as $sess_key => $sess_data ){

				if ( isset( $sess_data['wpw_auto_poster_li_groups'] ) && !empty( $sess_data['wpw_auto_poster_li_groups'] ) ) {
			
					$group_data[$sess_key] = $sess_data['wpw_auto_poster_li_groups'];
				}
			}
		}

		return $group_data;
	}
}