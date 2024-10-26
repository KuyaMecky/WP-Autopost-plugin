<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Meta Box Class
 *
 * Handles admin side plugin functionality.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

//include the main class file
require_once ( WPW_AUTO_POSTER_META_DIR . '/meta-box-class.php' );

class Wpw_Auto_Poster_Social_Meta_Box extends Wpw_Auto_Poster_Meta_Box {
	
	public function __construct( $config = array() ) {
		
		if( !empty( $config ) ) {
			
			parent::__construct( $config );
		
			// Must enqueue for all pages as we need js for the media upload, too.
			add_action( 'admin_enqueue_scripts', array( $this, 'wpw_auto_poster_load_scripts_styles' ) );
		}
	}
	
	public function wpw_auto_poster_reset_tweet_template() {
		
		global $wpw_auto_poster_options;
		
		$result = array();

		// Verify nonce
        if ( !isset( $_POST['wpw_metabox_nonce'] ) || !wp_verify_nonce( $_POST['wpw_metabox_nonce'], 'wpw_auto_poster_verify_metabox_nonce' ) ) {
            $result['error'] = 'Invalid nonce';
			$result['success']	= '0';
			echo json_encode( $result );
			exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $result['error'] = 'Invalid user';
				$result['success']	= '0';
				echo json_encode( $result );
				exit;
            }
        }
		
		$postid = isset( $_POST['postid'] ) ? sanitize_text_field($_POST['postid']) : '';
		$metaname = isset( $_POST['meta'] ) ? sanitize_text_field($_POST['meta']) : '';
		$posttitle = isset( $_POST['title'] ) ? sanitize_text_field($_POST['title']) : '';
		
		$post = get_post( $postid );
		
		if( class_exists( 'Wpw_Auto_Poster_Model' ) ) {
			
			$model = new Wpw_Auto_Poster_Model();
			
			$templatetags = '';
			if( method_exists( 'Wpw_Auto_Poster_Model', 'wpw_auto_poster_get_tweet_template' ) ) {
				$templatetags = $model->wpw_auto_poster_get_tweet_template( $wpw_auto_poster_options['tw_tweet_template'] );
			}
			update_post_meta( $postid, $metaname, $templatetags );
			if( class_exists( 'Wpw_Auto_Poster_Model' ) && method_exists( 'Wpw_Auto_Poster_Model', 'wpw_auto_poster_tweet_status' ) ) {
	  			$model = new Wpw_Auto_Poster_Model();
	  			$template = $model->wpw_auto_poster_tweet_status( $post, $templatetags, $posttitle );
	  		}
  			
		}
  		$result['template'] = $template;
		$result['newtemp'] = $templatetags;
		$result['success']	= '1';
		
		echo json_encode( $result );
		exit;
		
	}
	
	public function wpw_auto_poster_update_tweet_template() {
		
		$result = array();

		// Verify nonce
        if ( !isset( $_POST['wpw_metabox_nonce'] ) || !wp_verify_nonce( $_POST['wpw_metabox_nonce'], 'wpw_auto_poster_verify_metabox_nonce' ) ) {
            $result['error'] = 'Invalid nonce';
			$result['success']	= '0';
			echo json_encode( $result );
			exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $result['error'] = 'Invalid user';
				$result['success']	= '0';
				echo json_encode( $result );
				exit;
            }
        }
		
		$postid = isset( $_POST['postid'] ) ? sanitize_text_field($_POST['postid']) : '';
		$metaname = isset( $_POST['meta'] ) ? sanitize_text_field($_POST['meta']) : '';
		$template =  isset( $_POST['temp'] ) ? sanitize_text_field($_POST['temp']) : '';
		$posttitle = isset( $_POST['title'] ) ? sanitize_text_field($_POST['title']) : '';
		
		$post = get_post( $postid );
		update_post_meta( $postid, $metaname, $template );
		
		if( class_exists( 'Wpw_Auto_Poster_Model' ) && method_exists( 'Wpw_Auto_Poster_Model', 'wpw_auto_poster_tweet_status' ) ) {
  			$model = new Wpw_Auto_Poster_Model();
  			$template = $model->wpw_auto_poster_tweet_status( $post, $template, $posttitle );
  		}
  		
		$result['template'] = $template;
		$result['newtemp'] = sanitize_text_field($_POST['newtemp']);
		$result['success']	= '1';
		
		echo json_encode( $result );
		exit;
	}
	
	public function wpw_auto_poster_load_scripts_styles() {
		
		// Get Plugin Path
		$plugin_path = esc_url(WPW_AUTO_POSTER_META_URL);
					
		// Check for which post type we need to load the styles and scripts	
		if( $this->_meta_box['pages'] == 'all' ) {
			$pages = get_post_types( array( 'public' => true ), 'names' );
		} else {
			$pages = $this->_meta_box['pages'];
		}
		
		/**
		 * only load styles and js when needed
		 * since 1.8
		 */
		global $typenow;
    
		if ( in_array( $typenow, $pages ) && $this->is_edit_page() ) {
			
			// Register & Enqueue Extend Meta Box Style
			wp_register_style( 'wpw-auto-poster-meta-box', $plugin_path . '/css/wpw-auto-poster-meta-box.css', array(), WPW_AUTO_POSTER_VERSION );
      		wp_enqueue_style( 'wpw-auto-poster-meta-box' );
      		
			// Register & Enqueue Extend Meta Box Scripts
			wp_register_script( 'wpw-auto-poster-meta-box-script', $plugin_path . '/js/wpw-auto-poster-meta-box.js', array( 'jquery' ), WPW_AUTO_POSTER_VERSION, true );
			wp_enqueue_script( 'wpw-auto-poster-meta-box-script' );
			wp_localize_script( 'wpw-auto-poster-meta-box-script', 'WPSAPMeta', array(	
																					'invalid_url' => esc_html__( 'Please enter valid url.', 'wpwautoposter' ),
																					'wpw_metabox_nonce' => wp_create_nonce( 'wpw_auto_poster_verify_metabox_nonce' ),
																				) );
		}
	}

	/**
	 * Add Facebook Grant Permission Field
	 * @package Social Auto Poster
	 */
	public function addGrantPermission( $id, $args, $repeater = false ) {
		
		$new_field = array( 'type' => 'grantpermission','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'FB Grant Permission' );
		$new_field = array_merge( $new_field, $args );
    	
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
		
	}
	
	/**
	 * 
	 * Show Facebook Grant Permission Field
	 * 
	 * @param string $field 
	 * @param string|mixed $meta 
	 * @since 1.0.0
	 * @access public
	 */
	
	public function show_field_grantpermission( $field, $meta ) {
		
		echo "<div class='wpw-auto-poster-error'>
				<p>".esc_html($field['desc'])."</p>
				<p><a href='" . esc_url($field['url']) . "'>". esc_html($field['urltext']) . "</a></p>
			</div>";
		
	}
	
	/**
	 * Add Label to meta box
	 * 
	 * @package Social Auto Poster
	 * 
	 */
	public function addTweetStatus($id, $args, $repeater=false){
	
		$new_field = array( 'type' => 'tweetstatus','id'=> $id,'default' => '0','std' => '','desc' => '','style' =>'','name' => 'Label' );
		$new_field = array_merge( $new_field, $args );
    	
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
	}
	
	/**
	 * Show Field Tweet Status Label.
	 *
	 * @param string $field 
	 * @param string $meta 
	 * @since 1.0.0
	 * @access public
	 */
	public function show_field_tweetstatus( $field, $meta) {  
		
		global $post;

		$this->show_field_begin( $field, $meta );

		$metatext 	= esc_html__( 'Unpublished','wpwautoposter' );			
		if( $meta == 1 ) {
			$metatext 	= esc_html__( 'Published','wpwautoposter' );		
		} elseif ( $meta == 2 ) {
			$metatext 	= esc_html__( 'Scheduled','wpwautoposter' );			
		}

		$postid 	= isset($post->ID) ? $post->ID : '';
		
		echo "<label for='".esc_attr($field['id'])."' id='".esc_attr($field['id'])."' class='wpw-lbl-".esc_attr($field['id'])."'>".esc_html($metatext)."</label>";
		
		if( $meta ) {
			echo "<input type='button' id='wpw-auto-poster-rstatus' class='wpw-auto-poster-rstatus button button-secondary' name='wpw_auto_poster_reset_status' value='".esc_html__('Reset Status', 'wpwautoposter')."' aria-label='".esc_attr($field['id'])."' aria-data-id='".esc_attr($postid)."' aria-type='".esc_attr($field['tab'])."' />";
			echo "<span class='wpw-auto-poster-loader spinner'></span>";
		}
		
		$this->show_field_end( $field, $meta );
	} 
	
	/**
	 * Add Publishbox Field to meta box
	 * @package Social Auto Poster
	 * @since 1.0.0
	 * @access public
	 * @param $id string  field id, i.e. the meta key
	 * @param $args mixed|array
	 * 		'name' => // field name/label string optional
	 *    	'desc' => // field description, string optional
	 *    	'std' => // default value, string optional
	 *    	'validate_func' => // validate function, string optional
	 * @param $repeater bool  is this a field inside a repeatr? true|false(default) 
	 */
	public function addPublishBox( $id, $args, $repeater=false ) {
   
		$new_field = array( 'type' => 'publishbox', 'id'=> $id, 'std' => '', 'desc' => '', 'style' =>'', 'name' => 'Publish Checkbox Field' );
		$new_field = array_merge( $new_field, $args );
    
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
	}

	/**
	 * Show PublishBox Checkbox Field.
	 *
	 * @param string $field 
	 * @param string $meta 
	 * @since 1.0.0
	 * @access public
	 */
	public function show_field_publishbox( $field, $meta ) {
  		
		$prefix = WPW_AUTO_POSTER_META_PREFIX;
		$checked_publishbox = apply_filters('wpw_auto_poster_checked_publishbox', array() );
		
		$publishbox_key 	= ( isset($field['id']) ) ? str_replace( $prefix.'post_to_', '', $field['id']) : '';
		
		$meta = apply_filters( 'wpw_auto_poster_checked_publishbox_meta', $meta );

		$checked_publishbox = ( ( in_array($publishbox_key, $checked_publishbox) ) && $meta == 'on' ) ? 1 : 0;
		
		$this->show_field_begin($field, $meta);
		echo "<input type='checkbox' class='rw-checkbox' name='".esc_attr($field['id'])."' id='".esc_attr($field['id'])."' ".checked( 1, $checked_publishbox, false )." /><p class='wpw-auto-poster-meta'>".$field['desc']."</p></td>";
	}

	/**
	 * Add Tweet Mode to meta box
	 * @package Social Auto Poster
	 */
	
	public function addTweetMode($id, $args, $repeater=false){
	
		$new_field = array( 'type' => 'tweetmode','id'=> $id,'default' => '0','std' => '','desc' => '','style' =>'','name' => 'Label' );
		$new_field = array_merge( $new_field, $args );
    	
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
	}
  	
	/**
	 * Show Field Tweet Status Label.
	 *
	 * @param string $field 
	 * @param string $meta 
	 * @since 1.0.0
	 * @access public
	 */
	public function show_field_tweetmode( $field, $meta) {  
		
		global $post;
		
		$this->show_field_begin( $field, $meta );
		$meta = $meta == '' ? $field['default'] : $meta;
		$metatxt = $meta == '1' ? esc_html__( 'Manual','wpwautoposter' ) : esc_html__( 'Automatic','wpwautoposter' );
		
		$class = '';
		if($meta == '1') { $stylemode = "stylemode"; }
		else {
			$stylemode = "post_msg_style_hide";
			$class = 'tweet-mode-full-width';
		}
		
		echo "<label for='".esc_attr($field['id'])."' id='".esc_attr($field['id'])."' class='wpw-auto-poster-tweet-mode ".esc_attr($class)."'>".esc_attr($metatxt)."</label>";
		echo "<input type='hidden' name='".esc_attr($field['id'])."' id='".esc_attr($field['id'])."' value='".esc_attr($meta)."'>";
		
		
		echo "<a href='javascript:void(0);' id='".esc_attr($post->ID)."' class='wpw-auto-poster-reset-tweet-template ".esc_attr($stylemode)."' >".esc_html__( 'Reset','wpwautoposter' )."</a>";
		echo "<img class='wpw-auto-poster-tweet-template-loader tweet-mode-loader' src='".esc_url(WPW_AUTO_POSTER_META_URL)."/images/ajax-loader.gif' />";
		
		$this->show_field_end( $field, $meta );
	} 
	
	/**
	 * Add Tweet Template Textarea Field to meta box
	 * @package Social Auto Poster
	 * @since 1.0.0
	 * @access public
	 * @param $id string  field id, i.e. the meta key
	 * @param $args mixed|array
	 *    	'name' => // field name/label string optional
	 *    	'desc' => // field description, string optional
	 *    	'std' => // default value, string optional
	 *    	'style' =>   // custom style for field, string optional
	 *    	'validate_func' => // validate function, string optional
	 * @param $repeater bool  is this a field inside a repeatr? true|false(default) 
	 */
	public function addTweetTemplate( $id, $args, $repeater=false ) {
    
		$new_field = array( 'type' => 'tweettemplate', 'id'=> $id, 'std' => '', 'desc' => '', 'style' =>'', 'name' => 'Tweet Template Field' );
		$new_field = array_merge( $new_field, $args );
    
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
	}
	  
	/**
	 * Show Field Tweet Template
	 * 
	 * @param string $field 
	 * @param string $meta 
	 * @since 1.0.0
	 * @access public
	 */
  	public function show_field_tweettemplate( $field, $meta ) {
  		
  		global $post;
  		
  		$this->show_field_begin( $field, $meta );
  		$meta = $meta == '' ? $field['default'] : $meta; //check if post is new created then it will consider from default
  		
  		echo "<div class='wpw-auto-poster-tweet-template'>";
  		echo "<span>".esc_html($meta)."</span>";
  		echo "</div>";
  		echo "<div class='wpw-auto-poster-tweet-edit-template'>";
	  		echo "<textarea class='wpw-auto-poster-meta-textarea large-text' id='".esc_attr($field['id'])."' name='".esc_attr($field['id'])."' cols='60' rows='3'>".esc_attr($meta)."</textarea>"; //{$meta}
			echo "<input type='button' id='".esc_attr($post->ID)."' class='wpw-auto-poster-tweet-template-update button' value='".esc_html__( 'Update','wpwautoposter' )."' />";
			echo "<input type='button' id='".esc_attr($field['id'])."' class='wpw-auto-poster-tweet-template-cancel button' value='".esc_html__( 'Cancel','wpwautoposter' )."' />";
			echo "<img class='wpw-auto-poster-tweet-template-loader' src='".esc_url(WPW_AUTO_POSTER_META_URL)."/images/ajax-loader.gif' />";
		echo "</div>";
		$this->show_field_end( $field, $meta );
  	}
  	
	/**
	 * Add Label to meta box (generic function)
	 * @package Social Auto Poster
	 * 
	 */
	public function addTweetPreview($id, $args, $repeater=false){
	
		$new_field = array( 'type' => 'tweetpreview','id'=> $id,'default' => '[title] - [link]','std' => '','desc' => '','style' =>'','name' => 'Label' );
		$new_field = array_merge( $new_field, $args );
    	
		if( false === $repeater ) {
			$this->_fields[] = $new_field;
		} else {
			return $new_field;
		}
	}
	
  	/**
  	 * Show Field Tweet Preview
  	 * 
  	 * @param string $field 
	 * @param string $meta 
	 * @since 1.0.0
	 * @access public
  	 */
  	public function show_field_tweetpreview( $field, $meta ) {
  		
  		global $post;
  		$this->show_field_begin( $field, $meta );
  		$meta = $meta == false ? $field['default'] : $meta; //check if post is new created then it will consider from default
  		if( class_exists( 'Wpw_Auto_Poster_Model' ) && method_exists( 'Wpw_Auto_Poster_Model', 'wpw_auto_poster_tweet_status' ) ) {
  			$model = new Wpw_Auto_Poster_Model();
  			$meta = $model->wpw_auto_poster_tweet_status( $post, $meta );
  		}
  		$tw_tweet_exceed_message = sprintf( esc_html__('Twitter only allow %1$s280 characters%2$s limit for the tweet. If the tweet message exceeds the limit it will be automatically truncated.', 'wpwautoposter'), '<strong>', '</strong>');

  		echo "<label for='".esc_attr($field['id'])."' id='".esc_attr($field['id'])."' class='wpw-auto-poster-tweet-preview'>".esc_html($meta)."</label>";
  		$count = strlen( $meta );
  		$count_class = ( $count > 280 ) ? 'red-color' : '';

  		echo "<div id='".esc_attr($field['id'])."_count' class='wpw-auto-poster-tweet-preview-count ".esc_attr($count_class)."'>".esc_attr($count)."</div>";
  		if( $count > 280 ) {
  			echo '<div class="tweet-template-warning-message" id="tweet-warning-message">'.esc_html($tw_tweet_exceed_message).'</div>';
  		}
		$this->show_field_end( $field, $meta );
  		
  	}
  	
  	/**
	 * Function to reset the post social update status
	 *
	 * @package Social Auto Poster
	 * @since 1.6
	 */
  	function wpw_auto_poster_reset_post_social_status() {

		$prefix = WPW_AUTO_POSTER_META_PREFIX;

  		$result		= array();

		// Verify nonce
        if ( !isset( $_POST['wpw_metabox_nonce'] ) || !wp_verify_nonce( $_POST['wpw_metabox_nonce'], 'wpw_auto_poster_verify_metabox_nonce' ) ) {
            $result['error'] = 'Invalid nonce';
			$result['status']	= 'error';
			echo json_encode( $result );
			exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $result['error'] = 'Invalid user';
				$result['status']	= 'error';
				echo json_encode( $result );
				exit;
            }
        }

  		$post_id 	= ( !empty($_POST['postid']) && is_numeric($_POST['postid']) ) ? trim(stripslashes_deep($_POST['postid']) ) : '';
  		$meta		= (!empty($_POST['meta'])) ? trim( stripslashes_deep($_POST['meta']) ) : '';
  		$social_type= (!empty($_POST['social_type'])) ? trim( stripslashes_deep($_POST['social_type']) ) : '';

  		// Updating the meta
  		if( $post_id && $meta ) {

  			delete_post_meta( $post_id, $meta );

			// Remove network from scheduled schedule wall post
			$schedules = get_post_meta( $post_id, $prefix.'schedule_wallpost', true );
			if( !empty( $schedules ) ) {
				if(($key = array_search($social_type, $schedules)) !== false) {
				    unset($schedules[$key]);
				}
				
				if( empty( $schedules ) ){
					delete_post_meta( $post_id, $prefix.'schedule_wallpost' );
				} else {
					update_post_meta( $post_id, $prefix.'schedule_wallpost', $schedules );
				}
			}
			else { // remove post meta if no social media for schedule

               delete_post_meta( $post_id, $prefix.'schedule_wallpost' );        
            }

  			$result['status'] = 'success';
  			
  		} else {
  			$result['status'] = 'error';
  		}
  		
  		echo json_encode( $result );
  		
  		die();
  	}
  	
  	/**
	 * Adding Hooks
	 *
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
  	public function add_hooks(){
  		
  		//Ajax for saving tweet template
		add_action( 'wp_ajax_wpw_auto_poster_update_tweet_template', array( $this, 'wpw_auto_poster_update_tweet_template') );		
		add_action( 'wp_ajax_wpw_auto_poster_reset_tweet_template', array( $this, 'wpw_auto_poster_reset_tweet_template') );
		
		// Ajax for reset the post social publish status
		add_action( 'wp_ajax_wpw_auto_poster_reset_post_social_status', array( $this, 'wpw_auto_poster_reset_post_social_status') );
  	}
}