<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// include wordpress library file
require_once __DIR__ . '/libraries/wordpress-xmlrpc/WordpressClient.php';

/**
 * WordPress Posting Class
 *
 * Handles all the functions to post the submitted and approved
 * reviews to a chosen application owner account
 *
 * @package Social Auto Poster
 * @since 3.5.1
 */
class Wpw_Auto_Poster_Wp_Posting{
	public $model, $logs, $proxy, $message;

	private $client;

	public function __construct() {

		global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;
		
		$this->message = $wpw_auto_poster_message_stack;
		$this->model = $wpw_auto_poster_model;
		$this->logs	 = $wpw_auto_poster_logs;
	}

	/**
	 * Authenticate WordPress website,
	 * and Save it to db.
	 *
	 * @package Social Auto Poster
 	 * @since 3.5.1
	 */
	public function wpw_auto_poster_add_website( $data ) {

		// Create endpoint
		$endpoint = esc_url( $data['url'] ) . '/xmlrpc.php';

		// Create client instance
		$wpClient = new \HieuLe\WordpressXmlrpcClient\WordpressClient( $endpoint, $data['username'], $data['password'] );

		$user = $wpClient->getProfile();

		if( is_array($user) && xmlrpc_is_fault($user) ) {
			return array(
				'faultCode' => 'custom',
				'faultString' => esc_html__( 'The login details you have entered is incorrect.', 'wpwautoposter' ),
			);
		}

		if( ! isset($user['faultCode']) && empty($user['faultString']) ) {
			// Get all stored website
			$wp_sites = get_option( 'wpw_auto_poster_wordpress_sites', array() );

			// Encode password
			$data['password'] = base64_encode( $data['password'] );

			// create a unique key from website url
			$key = str_replace( 'https://', '', $data['url'] );
			$key = str_replace( 'http://', '', $key );
			$key = rtrim( $key, '/' );
			$key = str_replace( '/', '-', $key );
			$key = trim( $key );

			// Store website data
			$wp_sites[$key] = $data;
			update_option( 'wpw_auto_poster_wordpress_sites', $wp_sites );
		}

		return $user;
	}

	/**
	 * Get site post types
	 *
	 * @package Social Auto Poster
 	 * @since 3.5.1
	 */
	public function wpw_auto_poster_get_site_post_types( $data ) {
		// Create endpoint
		$endpoint = esc_url( $data['url'] ) . '/xmlrpc.php';

		// Create client instance
		$wpClient = new \HieuLe\WordpressXmlrpcClient\WordpressClient( $endpoint, $data['username'], $data['password'] );

		$postTypes = $wpClient->getPostTypes( array('public' => true) );

		if( is_array($postTypes) && xmlrpc_is_fault($postTypes) ) {
			return false;
		}

		return $postTypes;
	}

	/**
	 * Remove Website
     *
     * Removing the WordPress Website when the admin clicks on
     * its link within the settings page.
	 *
	 * @package Social Auto Poster
 	 * @since 3.5.1
	 */
	public function wpw_auto_poster_wp_remove_site() {
		global $wpw_auto_poster_options;

		// Check if wordpress remove website link is clicked and remove_wp_website is set to 1 and wordpress website id is there
        if( isset($_GET['remove_wp_website']) && $_GET['remove_wp_website'] == '1' && $_GET['wpw_wp_index'] != '' ) {

        	$wpw_wp_site_id = $this->model->wpw_auto_poster_stripslashes_deep( $_GET['wpw_wp_index'] );

        	// Getting stored WP site data
            $wp_sites = get_option('wpw_auto_poster_wordpress_sites');

            // Unset particular app value data and update the option
            if( isset($wp_sites[$wpw_wp_site_id]) ) {
                unset( $wp_sites[$wpw_wp_site_id] );
                update_option( 'wpw_auto_poster_wordpress_sites', $wp_sites );
            }

            // Users need to flush from stored data
            $reset_wp_sites = !empty($wp_sites[$wpw_wp_site_id]) ? $wp_sites[$wpw_wp_site_id] : array();

            // Get all post type
            $all_post_types = get_post_types(array('public' => true), 'objects');
            $all_post_types = is_array($all_post_types) ? $all_post_types : array();

            // Unset users from settings page
            foreach( $all_post_types as $posttype ) {

                //check postype is not object
                if( !is_object($posttype) ) continue;

                if( isset($posttype->labels) ) {
                    $label = $posttype->labels->name ? $posttype->labels->name : $posttype->name;
                } else {
                    $label = $posttype->name;
                }

                if( $label == 'Media' || $label == 'media' ) continue; // skip media
                    
                // Check if user is set for posting in settings page then unset it
                if (isset($wpw_auto_poster_options['wp_type_' . $posttype->name . '_sites'])) {

                    // Get stored facebook users according to post type
                    $wp_stored_sites = $wpw_auto_poster_options['wp_type_' . $posttype->name . '_sites'];

                    // Flusing the App users and taking remaining
                    $new_stored_sites = array_diff($wp_stored_sites, $reset_wp_sites);

                    // If empty data then unset option else update remaining
                    if (!empty($new_stored_sites)) {
                        $wpw_auto_poster_options['wp_type_' . $posttype->name . '_sites'] = $new_stored_sites;
                    } else {
                        unset($wpw_auto_poster_options['wp_type_' . $posttype->name . '_sites']);
                    }
                } //end if
            } //end foreach
        }
	}

	/**
	 * WordPress Posting
	 * 
	 * Handles to wordpress posting
	 * by post data
	 * 
	 * @package Social Auto Poster
 	 * @since 3.5.1
	 */
	public function wpw_auto_poster_wp_posting( $post, $auto_posting_type = '' ) {
		global $wpw_auto_poster_options;

		$prefix = WPW_AUTO_POSTER_META_PREFIX;

		$res = $this->wpw_auto_poster_post_to_wordpress( $post, $auto_posting_type );

		//check if error should not occured and successfully tweeted
		if( isset( $res['success'] ) && !empty( $res['success'] ) ) {
			
			update_post_meta( $post->ID, $prefix . 'wp_status', '1' );

			// get current timestamp and update meta as published date/time
			$current_timestamp = current_time( 'timestamp' );
			update_post_meta( $post->ID, $prefix . 'published_date', $current_timestamp );

			return true;
		}
		return false;
	}

	/**
	 * Post to WordPress
	 *
	 * @package Social Auto Poster
 	 * @since 3.5.1
	 */
	public function wpw_auto_poster_post_to_wordpress( $post, $auto_posting_type ) {
		global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options, $ThemifyBuilder;

		// Get all stored telegram chats
		$wpAllSites = get_option( 'wpw_auto_poster_wordpress_sites', array() );

		//Initilize wordpress posting
		$wp_posting = array();

		// Check chats are stored or not
		if( !empty($wpAllSites) ) {

			// Posting logs data
			$posting_logs_data = array();

			//Initialize tags and categories
			$tags_arr = array();
			$cats_arr = array();

			//metabox field prefix
			$prefix = WPW_AUTO_POSTER_META_PREFIX;

			// published status
            $ispublished = get_post_meta( $post->ID, $prefix . 'wp_status', true );

            // Post type
			$post_type = $post->post_type;

            // Get all selected tags for selected post type for hashtags support
            if( !empty($wpw_auto_poster_options['wp_post_type_tags']) ) {
            	$custom_post_tags = $wpw_auto_poster_options['wp_post_type_tags'];
                if( !empty($custom_post_tags[$post_type]) ) {  
                    foreach( $custom_post_tags[$post_type] as $key => $tag ) {
                        $term_list = wp_get_post_terms( $post->ID, $tag, array("fields" => "names") );
                        foreach( $term_list as $term_single ) {
                            $tags_arr[] = str_replace( ' ', '', $term_single );
                        }
                    }
                }
            }

            // Get all selected categories for selected post type for hashcats support
            if( !empty($wpw_auto_poster_options['wp_post_type_cats']) ) {
                $custom_post_cats = $wpw_auto_poster_options['wp_post_type_cats'];
                if( !empty($custom_post_cats[$post_type]) ) {
                    foreach( $custom_post_cats[$post_type] as $key => $category ) {
                        $term_list = wp_get_post_terms( $post->ID, $category, array("fields" => "names") );
                        foreach( $term_list as $term_single ) {
                            $cats_arr[] = str_replace( ' ', '', $term_single );
                        }
                    }
                }
            }

            // user data form post author
			$userdata	= get_userdata($post->post_author);
			$first_name	= $userdata->first_name;
			$last_name	= $userdata->last_name;

			$post_title = $post->post_title;
			$post_content = $post->post_content;

			$custom_title = get_post_meta( $post->ID, $prefix . 'wp_post_title', true );
			$custom_content = get_post_meta( $post->ID, $prefix . 'wp_post_content', true );

			$custom_image = get_post_meta( $post->ID, $prefix . 'wp_post_image', true );
			if( !empty($custom_image['src']) ) {
				$custom_image = $custom_image['src'];
			} elseif( has_post_thumbnail($post) ) {
				$custom_image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
				$custom_image = !empty( $custom_image[0] ) ? $custom_image[0] : '';
			}

			// Get settings from resposter settings page
			if( !empty($auto_posting_type) && $auto_posting_type == 'reposter' ){

				$custom_cnt_options = isset( $wpw_auto_poster_reposter_options['wp_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['wp_custom_msg_options'] : 'global_msg';

				if( $custom_cnt_options == 'global_msg' ) {
					$setting_custom_title = isset( $wpw_auto_poster_reposter_options['wp_global_title'] ) ? $wpw_auto_poster_reposter_options['wp_global_title'] : '';

					$setting_custom_image = isset( $wpw_auto_poster_reposter_options['wp_post_image'] ) ? $wpw_auto_poster_reposter_options['wp_post_image'] : '';

					$setting_custom_content = isset( $wpw_auto_poster_reposter_options['wp_global_message_template'] ) ? $wpw_auto_poster_reposter_options['wp_global_message_template'] : '';

				} else {
					$setting_custom_title = isset( $wpw_auto_poster_reposter_options['wp_post_title_'.$post_type] ) ? $wpw_auto_poster_reposter_options['wp_post_title_'.$post_type] : '';

					$setting_custom_image = isset( $wpw_auto_poster_reposter_options['wp_post_image_'.$post_type] ) ? $wpw_auto_poster_reposter_options['wp_post_image_'.$post_type] : '';

					$setting_custom_content = isset( $wpw_auto_poster_reposter_options['wp_post_cnt_template_'.$post_type] ) ? $wpw_auto_poster_reposter_options['wp_post_cnt_template_'.$post_type] : '';
				}

			// Get settings from normal settings page
			} else {
				$custom_cnt_options = isset( $wpw_auto_poster_options['wp_custom_msg_options'] ) ? $wpw_auto_poster_options['wp_custom_msg_options'] : 'global_msg';

				if( $custom_cnt_options == 'global_msg' ) {
					$setting_custom_title = isset( $wpw_auto_poster_options['wp_global_title'] ) ? $wpw_auto_poster_options['wp_global_title'] : '';

					$setting_custom_image = isset( $wpw_auto_poster_options['wp_post_image'] ) ? $wpw_auto_poster_options['wp_post_image'] : '';

					$setting_custom_content = isset( $wpw_auto_poster_options['wp_global_message_template'] ) ? $wpw_auto_poster_options['wp_global_message_template'] : '';

				} else {
					$setting_custom_title = isset( $wpw_auto_poster_options['wp_post_title_'.$post_type] ) ? $wpw_auto_poster_options['wp_post_title_'.$post_type] : '';

					$setting_custom_image = isset( $wpw_auto_poster_options['wp_post_image_'.$post_type] ) ? $wpw_auto_poster_options['wp_post_image_'.$post_type] : '';

					$setting_custom_content = isset( $wpw_auto_poster_options['wp_post_cnt_template_'.$post_type] ) ? $wpw_auto_poster_options['wp_post_cnt_template_'.$post_type] : '';
				}
			}

			// Get post excerpt
			$excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

			$full_author = normalize_whitespace( $first_name.' '.$last_name );
            $nickname_author = get_user_meta( $post->post_author, 'nickname', true);

            // Check metabox title is empty than take from settings page
            $custom_title = !empty( $custom_title ) ? $custom_title : $setting_custom_title;

            // Check if still empty than take post title
            $custom_title = !empty( $custom_title ) ? $custom_title : $post_title;

            // custom image
            $custom_image = !empty( $custom_image ) ? $custom_image : $setting_custom_image;

            // custom content
			$custom_content = !empty( $custom_content ) ? $custom_content : $setting_custom_content;

            // Get post tags
            $tags_arr   = apply_filters('wpw_auto_poster_wp_hashtags', $tags_arr);
            $hashtags   = ( !empty( $tags_arr ) ) ? '#' . implode( ' #', $tags_arr ) : '';

            // get post categories
            $cats_arr   = apply_filters('wpw_auto_poster_wp_hashcats', $cats_arr);
            $hashcats	= ( !empty( $cats_arr ) ) ? '#' . implode( ' #', $cats_arr ) : '';

            //do url shortner
			$postlink = $this->model->wpw_auto_poster_get_short_post_link( get_permalink($post), false, $post->ID, true, 'wp' );

			$search_arr = array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );

			$replace_arr = array( $post_title , $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

			$code_matches = array();

			// check if template tags contains {content-numbers}
            if( preg_match_all( '/\{(content)(-)(\d*)\}/', $custom_content, $code_matches ) ) {
                $trim_tag = $code_matches[0][0];
                $trim_length = $code_matches[3][0];
                $post_content = substr( $post_content, 0, $trim_length);
                $search_arr[] = $trim_tag;
                $replace_arr[] = $post_content;
            }

            $cf_matches = array();
            // check if template tags contains {CF-CustomFieldName}
            if( preg_match_all('/\{(CF)(-)(\S*)\}/', $custom_content, $cf_matches) ) {
                foreach( $cf_matches[0] as $key => $value ) {
                    $cf_tag = $value;
                    $search_arr[] = $cf_tag;
                }

                foreach( $cf_matches[3] as $key => $value ) {
                    $cf_name = $value;
                    $tag_value = '';
                    
                    if( $cf_name ) {
                        $tag_value = get_post_meta($post->ID, $cf_name, true);
                        if( is_array($tag_value) ) {
                            $tag_value = '';
                        }
                    }
                    $replace_arr[] = $tag_value;
                }
            }

			$custom_content = str_replace( $search_arr, $replace_arr, $custom_content );
			$custom_content = $this->model->wpw_auto_poster_stripslashes_deep( $custom_content );
			$custom_content = $this->model->wpw_auto_poster_html_decode( $custom_content );

			// WordPress sites Data from setting
			$wp_post_sites = get_post_meta( $post->ID, $prefix . 'wp_post_sites' );

			if( $post_type == 'wpwsapquickshare'){
                $wp_post_sites = get_post_meta($post->ID, $prefix . 'wp_post_sites',true);
            }

			//If profiles are empty in metabox
			if( empty($wp_post_sites) ) {

				$mappedSites = get_option( 'wpw_auto_poster_wordpress_mapped_posttypes' );

				$wp_post_sites	= isset( $mappedSites[$post->post_type] ) ? $mappedSites[$post->post_type] : '';
			}

			/** log data **/
			$posting_logData = array(
				'title' => $custom_title,
				'description' => $custom_content,
				'submitted-image-url' => $custom_image,
				'post_type' => $post_type,
				'submitted-url' => $postlink,
			);

			// record logs for wordpress posting data
			$this->logs->wpw_auto_poster_add( 'WordPress post data : ' . var_export($posting_logData, true) );

			try {
				if( !empty($wp_post_sites) ) {
					foreach( $wp_post_sites as $wp_post_site ) {

						$wp_post_site_arr = explode( ':', $wp_post_site );
						$wp_site_id = isset( $wp_post_site_arr[0] ) ? $wp_post_site_arr[0] : '';
						$wp_post_type = isset( $wp_post_site_arr[1] ) ? $wp_post_site_arr[1] : $post_type;

						$wp_site = isset( $wpAllSites[$wp_site_id] ) ? $wpAllSites[$wp_site_id] : array();

						if( empty($wp_site['url']) || empty($wp_site['username']) ||
							empty($wp_site['password']) ) {
							$wp_posting['fail'] = 1;
							continue;
						}

						// Create endpoint
						$endpoint = esc_url( $wp_site['url'] ) . '/xmlrpc.php';

						// Create client instance
						$wpClient = new \HieuLe\WordpressXmlrpcClient\WordpressClient( $endpoint, $wp_site['username'], base64_decode($wp_site['password']) );
						
						$args = array(
							'post_type' 	 => $wp_post_type,
							'post_status' 	 => $post->post_status,
							'post_title' 	 => $custom_title,
							'post_content' 	 => $custom_content,
						);

						// Creating post
						$added_post = $wpClient->newPost( $custom_title, $custom_content, $args );
						

						if( !isset($added_post['faultString']) ) {

							$WpPost = $wpClient->getPost( $added_post );
							$post_link = isset( $WpPost['link'] ) ? $WpPost['link'] : '';

							$wp_posting['success'] = 1;
							$addPostImg = '';
							if( !empty($custom_image) ) {
								$addPostImg = $this->wpw_auto_poster_set_featured_image_to_posted_post( $wpClient, $added_post, $custom_image );
							}

							if( empty($addPostImg) && 'null' == strtolower($addPostImg) ) {
								$addPostImg == '';
							}

							// Log the response
							$postingLog = array(
								'id' => $added_post,
								'posted_posttype' => $wp_post_type,
								'posted_site' => $wp_site['url'],
								'attach-img' => $addPostImg,
							);

							// record logs for wordpress data
							$this->logs->wpw_auto_poster_add( 'WordPress posting request on: ' . $wp_site['name'] . ' - ' . var_export($postingLog, true) );

							$posting_siteData = array(
								'site_name'		=> $wp_site['name'],
								'username'		=> $wp_site['username'],
								'site_url'		=> $wp_site['url'],
								'post_title'	=> $custom_title,
								'posted_posttype'	=> $wp_post_type,
								'post_link' => $post_link
							);

							//posting logs store into database
							$this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'wp', $posting_logData, $posting_siteData );

						} else {
							
							$wp_posting['success'] = 1;
							if( $post_type == 'wpwsapquickshare'){
				                update_post_meta($post->ID, $prefix . 'wp_post_status','success');
				            }

							// record logs for wordpress data
							$this->logs->wpw_auto_poster_add( 'WordPress API Response: ' . var_export($added_post, true) );
						}
					}

					// record logs for posting done on wordpress
					$this->logs->wpw_auto_poster_add( 'WordPress posting completed successfully.' );
				}
			} catch ( Exception $e ) {
				
				//record logs exception generated
				$this->logs->wpw_auto_poster_add( 'WordPress error: ' . $e->__toString() );
				if( $post_type == 'wpwsapquickshare'){
                    update_post_meta($post->ID, $prefix . 'wp_post_status','error');
                    update_post_meta($post->ID, $prefix . 'wp_error', sprintf( esc_html__('Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ) );
                }
				// display error notice on post page
				sap_add_notice( sprintf( esc_html__('WordPress: Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ), 'error');
				return false;
			}
		} else {
			
			//record logs when grant extended permission not set
			$this->logs->wpw_auto_poster_add( 'WordPress error: No websites found to post.' );

			if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'wp_post_status','error');
                update_post_meta($post->ID, $prefix . 'wp_error', esc_html__('Please add and select websites before posting to the WordPress.', 'wpwautoposter' ) );
            }
			// display error notice on post page
			sap_add_notice( esc_html__('WordPress: Please add and select websites before posting to the WordPress.', 'wpwautoposter' ), 'error');
		}

		return $wp_posting;
	}

	/**
	 * Upload post image to posted post
	 */
	public function wpw_auto_poster_set_featured_image_to_posted_post( $wpClient, $post_id, $path ) {

		$post_result = $wpClient->getPost( $post_id );

		if( empty($post_result['post_id']) ) {
			return false;
		}

		if(!empty($path['src'])){
			$image 		= (!empty($path['src'])) ? file_get_contents($path) : '';
			$name 		= (!empty($path['src'])) ? basename($path) : '';
			$mime 		= 'image/jpg';
			$bits 		= $image;
			$overwrite 	= true;
			$postId 	= $post_result['post_id'];

			$attachment_result = $wpClient->uploadFile($name, $mime, $bits, $overwrite, $postId);

			if( !empty($attachment_result['id']) ) {
				$edit_args['post_thumbnail'] = $attachment_result['id'];
				$edit_args['custom_fields'] = array(
					'key' => '_thumbnail_id',
					'value' => $attachment_result['id']
				);

				// Edit posts to set featured image and category
				return $wpClient->editPost($post_result['post_id'], $edit_args);
			}
		}
		return false;
	}
}