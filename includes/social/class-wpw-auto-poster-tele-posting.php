<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Telegram Posting Class
 *
 * Handles all the functions to post the submitted and approved
 * reviews to a chosen application owner account
 *
 * @package Social Auto Poster
 * @since 3.4.0
 */
class Wpw_Auto_Poster_Tele_Posting{

	public $message, $model, $logs;

	public function __construct() {

		global $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_logs;
		
		$this->message = $wpw_auto_poster_message_stack;
		$this->model = $wpw_auto_poster_model;
		$this->logs	 = $wpw_auto_poster_logs;
	}

	/**
	 * Get active chats
	 *
	 * @package Social Auto Poster
 	 * @since 3.4.0
	 */
	public function wpw_auto_poster_get_active_chats( $token ) { 

		if( empty($token) ) return false;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
		curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/getUpdates');
		curl_setopt($ch, CURLOPT_POSTFIELDS, array());
		$response = curl_exec($ch);		
		if(!$response)
		$this->logs->wpw_auto_poster_add( curl_error($ch) );
		curl_close($ch);

		$updates = json_decode($response);

		if( ! $updates ) {
			return array();
		}

		$chats_list = $uniquechats_list = array();

		foreach( $updates->result as $update ) {
			if( !empty($update->message) && empty( $update->message->left_chat_member ) ){
				$chats_list[$update->message->chat->id] = array(
					'id'	=>	$update->message->chat->id,
					'name'	=>	isset($update->message->chat->first_name) ? $update->message->chat->first_name : '',
					'title'	=>	isset($update->message->chat->title) ? $update->message->chat->title : '',
					'type'	=>	isset($update->message->chat->type) ? $update->message->chat->type : ''
				);
			}
			if(!empty($update->my_chat_member) ){
				$chats_list[$update->my_chat_member->chat->id] = array(
					'id'	=>	$update->my_chat_member->chat->id,
					'name'	=>	isset($update->my_chat_member->chat->first_name) ? $update->my_chat_member->chat->first_name : '',
					'title'	=>	isset($update->my_chat_member->chat->title) ? $update->my_chat_member->chat->title : '',
					'type'	=>	isset($update->my_chat_member->chat->type) ? $update->my_chat_member->chat->type : '',
					'status'	=>	isset($update->my_chat_member->new_chat_member->status) ? $update->my_chat_member->new_chat_member->status : '',
				);
			}
			
		}

		// Filter the array to remove entries with status 'left'
		$active_chats_list = array_filter($chats_list, function ($chat) {
			return !isset($chat['status']) || $chat['status'] !== 'left';
		});

		return $active_chats_list;
	}

	/**
	 * Telegram Posting
	 * 
	 * Handles to telegram posting
	 * by post data
	 * 
	 * @package Social Auto Poster
 	 * @since 3.4.0
	 */
	public function wpw_auto_poster_tele_posting( $post, $auto_posting_type = '' ) {
		
		global $wpw_auto_poster_options;
		
		$prefix = WPW_AUTO_POSTER_META_PREFIX;

		$res = $this->wpw_auto_poster_post_to_telegram( $post, $auto_posting_type );
		
		//check if error should not occured and successfully tweeted
		if( isset( $res['success'] ) && !empty( $res['success'] ) ) {
			
			//record logs for posting done on telegram
			$this->logs->wpw_auto_poster_add( 'Telegram posting completed successfully.' );
			
			update_post_meta( $post->ID, $prefix . 'tele_status', '1' );

			// get current timestamp and update meta as published date/time
            $current_timestamp = current_time( 'timestamp' );
            update_post_meta( $post->ID, $prefix . 'published_date', $current_timestamp );
            
			return true;
		}
		
		return false;
	}

	/**
	 * Post to Telegram
	 *
	 * @package Social Auto Poster
 	 * @since 3.4.0
	 */
	public function wpw_auto_poster_post_to_telegram( $post, $auto_posting_type ) {

		global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options, $ThemifyBuilder;

		// Get all stored telegram chats
		$teleChats = wpw_auto_poster_get_tele_chats();

		// Check chats are stored or not
		if( !empty($teleChats) ) {

			// Posting logs data
			$posting_logs_data = array();

			//Initialize tags and categories
			$tags_arr = array();
			$cats_arr = array();

			$unique = 'false';

			//metabox field prefix
			$prefix = WPW_AUTO_POSTER_META_PREFIX;

			// Post type
			$post_type = $post->post_type;

			// user data form post author
			$userdata	= get_userdata($post->post_author);
			$first_name	= $userdata->first_name;
			$last_name	= $userdata->last_name;

			// published status
            $ispublished = get_post_meta( $post->ID, $prefix . 'tele_status', true );

            // Get all selected tags for selected post type for hashtags support
            if( !empty($wpw_auto_poster_options['tele_post_type_tags']) ) {
            	$custom_post_tags = $wpw_auto_poster_options['tele_post_type_tags'];
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
            if( !empty($wpw_auto_poster_options['tele_post_type_cats']) ) {
                $custom_post_cats = $wpw_auto_poster_options['tele_post_type_cats'];
                if( !empty($custom_post_cats[$post_type]) ) {
                    foreach( $custom_post_cats[$post_type] as $key => $category ) {
                        $term_list = wp_get_post_terms( $post->ID, $category, array("fields" => "names") );
                        foreach( $term_list as $term_single ) {
                            $cats_arr[] = str_replace( ' ', '', $term_single );
                        }
                    }
                }
            }

            //post title
			$posttitle		= $post->post_title;
			$post_content 	= $post->post_content;

			$postlink = '';

			// fix html render issue with themify theme builder
			if( empty($ThemifyBuilder) ) {
				$post_content 	= apply_filters('the_content',$post_content);
			}

			$post_content = preg_replace("/([\r\n]{4,}|[\n]{2,}|[\r]{2,})/", "\r\n\r\n", $post_content); // fix extra line issue with gutern burg

			// If gutenburg/block editor used, than remove blocks comments
			if( function_exists('has_blocks') && !empty($ThemifyBuilder) ) {
			    $blocks = parse_blocks( $post_content );
			    if( !empty($blocks) ){
			    	$post_content = '';
			    	foreach( $blocks as $key => $value ) {
			    		if( isset($value['innerHTML']) && !empty(wp_strip_all_tags($value['innerHTML'])) ) {
			    			$post_content .= wp_strip_all_tags( $value['innerHTML'] ) . '\n';
			    		}
			    	}
			    }
			}
			
			$post_content = strip_shortcodes( $post_content );

			//strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep( $post_content );

            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode( $post_content );

            //custom title from metabox
			$customtitle = get_post_meta( $post->ID, $prefix . 'tele_post_title', true );

			// custom title from custom post type message
			if( !empty($auto_posting_type) && $auto_posting_type == 'reposter' ) {
				
				// global custom post msg template for reposter
                $tele_global_custom_message_template = ( isset( $wpw_auto_poster_reposter_options["tele_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["tele_global_message_template_".$post_type] : '';

                $tele_global_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_tele_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_tele_custom_msg_options'] : '';

                // global custom msg template for reposter
                $tele_global_template_text = ( isset( $wpw_auto_poster_reposter_options["tele_global_message_template"] ) ) ? $wpw_auto_poster_reposter_options["tele_global_message_template"] : '';

                if( $tele_global_custom_msg_options == 'global_msg' ) {
                	// global custom post img
           			$tele_custom_post_img = ( isset( $wpw_auto_poster_reposter_options["tele_post_image"] ) ) ? $wpw_auto_poster_reposter_options["tele_post_image"] : '';
                } else {
                	// global custom post img
           			$tele_custom_post_img = ( isset( $wpw_auto_poster_reposter_options["tele_post_image_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["tele_post_image_".$post_type] : '';
                }
                
			} else {

				$tele_global_custom_message_template = ( isset( $wpw_auto_poster_options["tele_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_options["tele_global_message_template_".$post_type] : '';

                $tele_global_custom_msg_options = isset( $wpw_auto_poster_options['tele_custom_msg_options'] ) ? $wpw_auto_poster_options['tele_custom_msg_options'] : '';
				
				$tele_global_template_text = ( !empty( $wpw_auto_poster_options['tele_global_message_template'] ) ) ? $wpw_auto_poster_options['tele_global_message_template'] : '';

				// global custom post img
				if( $tele_global_custom_msg_options == 'global_msg' ) {
           			$tele_custom_post_img = ( isset( $wpw_auto_poster_options["tele_post_image"] ) ) ? $wpw_auto_poster_options["tele_post_image"] : '';
           		} else {
           			$tele_custom_post_img = ( isset( $wpw_auto_poster_options["tele_post_image_".$post_type] ) ) ? $wpw_auto_poster_options["tele_post_image_".$post_type] : '';
           		}
			}
			
			//custom title set use it otherwise user posttiel
			$title = !empty( $customtitle ) ? $customtitle : $post_content;

			//post image
			$postimage = get_post_meta( $post->ID, $prefix . 'tele_post_image', true );

			// send type photo/text
			$sendType = get_post_meta( $post->ID, $prefix . 'tele_post_msgtype', true );

			if( empty($sendType) && $post_type != 'wpwsapquickshare' ) {
				$sendType = ( isset( $wpw_auto_poster_options["tele_type_".$post_type."_msgtype"] ) ) ? $wpw_auto_poster_options["tele_type_".$post_type."_msgtype"] : 'text';
			}
			
			$sendType = empty( $sendType ) ? 'text' : $sendType;

			/**************
			 * Image Priority
			 * If metabox image set then take from metabox
			 * If metabox image is not set then take from featured image
			 * If featured image is not set then take from settings page
			 **************/
			
			//get featured image from post / page / custom post type
			$post_featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

			$tele_global_custom_msg_options = isset( $wpw_auto_poster_options['tele_custom_msg_options'] ) ? $wpw_auto_poster_options['tele_custom_msg_options'] : '';

			//check custom image is set in meta and not empty
			if( isset( $postimage['src'] ) && !empty( $postimage['src'] ) ) {
				$postimage = $postimage['src'];
			} elseif ( isset( $post_featured_img[0] ) && !empty( $post_featured_img[0] ) ) {
				//check post featrued image is set the use that image
				$postimage = $post_featured_img[0];
			} else {
				//else get post image from settings page
				$postimage = ( !empty( $tele_custom_post_img ) ) ? $tele_custom_post_img : $wpw_auto_poster_options['tele_post_image'];
			}

			$postimage = apply_filters('wpw_auto_poster_social_media_posting_image', $postimage );

			//if custom link is set or not
			$customlink = !empty( $postlink ) ? 'true' : 'false';
			
			//do url shortner
			$postlink = $this->model->wpw_auto_poster_get_short_post_link( $postlink, $unique, $post->ID, $customlink, 'tele' );

			// not sure why this code here it should be above $postlink but lets keep it here
			//if post is published on linkedin once then change url to prevent duplication
			if( isset($ispublished) && $ispublished == '1' ) {
				$unique = 'true';
			}

			//comments
			$description = get_post_meta( $post->ID, $prefix . 'tele_post_comment', true );

			//comments
			$description = get_post_meta( $post->ID, $prefix . 'tele_post_comment', true );

			$description = !empty( $description ) ? $description : '';
			$description = apply_filters( 'wpw_auto_poster_tele_comments', $description, $post );

			if( $tele_global_custom_msg_options == 'post_msg' && !empty($tele_global_custom_message_template) && empty($description) ) {
                $description = $tele_global_custom_message_template;
            } elseif( empty($description) && !empty($tele_global_template_text) ) {
                $description = $tele_global_template_text;
            } elseif( empty($description) ){
            	//get telegram posting description
				$description = $posttitle;
            }

            // Get post excerpt
			$excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

			// Get post tags
            $tags_arr   = apply_filters('wpw_auto_poster_tele_hashtags', $tags_arr);
            $hashtags   = ( !empty( $tags_arr ) ) ? '#' . implode( ' #', $tags_arr ) : '';

            // get post categories
            $cats_arr   = apply_filters('wpw_auto_poster_tele_hashcats', $cats_arr);
            $hashcats	= ( !empty( $cats_arr ) ) ? '#' . implode( ' #', $cats_arr ) : '';

            $full_author = normalize_whitespace( $first_name.' '.$last_name );
            $nickname_author = get_user_meta( $post->post_author, 'nickname', true);

			$search_arr = array( '{title}', '{link}', '{full_author}', '{nickname_author}', '{post_type}', '{first_name}' , '{last_name}', '{sitename}', '{site_name}', '{content}', '{excerpt}', '{hashtags}', '{hashcats}' );

			$replace_arr = array( $posttitle , $postlink, $full_author, $nickname_author, $post_type, $first_name, $last_name, get_option( 'blogname'), get_option( 'blogname' ), $post_content, $excerpt, $hashtags, $hashcats );

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
            if( preg_match_all('/\{(CF)(-)(\S*)\}/', $description, $cf_matches) ) {
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

            // Image caption
			$imgCaption  = get_post_meta( $post->ID, $prefix . 'tele_post_img_caption', true );

			if( empty($imgCaption) ) {
				if( !empty($auto_posting_type) && $auto_posting_type == 'reposter' ) {
					if( isset($wpw_auto_poster_reposter_options['repost_tele_custom_msg_options']) ) {
						if( $wpw_auto_poster_reposter_options['repost_tele_custom_msg_options'] == 'global_msg' ) {
							$imgCaption = isset( $wpw_auto_poster_reposter_options['tele_post_img_caption'] ) ? $wpw_auto_poster_reposter_options['tele_post_img_caption'] : '';
						} elseif( isset($wpw_auto_poster_reposter_options['tele_post_img_caption_'.$post_type]) ) {
							$imgCaption = $wpw_auto_poster_reposter_options['tele_post_img_caption_'.$post_type];
						}
					}
				} else {
					if( isset($wpw_auto_poster_options['tele_custom_msg_options']) ) {
						if( $wpw_auto_poster_options['tele_custom_msg_options'] == 'global_msg' ) {
							$imgCaption = isset( $wpw_auto_poster_options['tele_post_img_caption'] ) ? $wpw_auto_poster_options['tele_post_img_caption'] : '';
						} elseif( isset($wpw_auto_poster_options['tele_post_img_caption_'.$post_type]) ) {
							$imgCaption = $wpw_auto_poster_options['tele_post_img_caption_'.$post_type];
						}
					}
				}
			}

			// check if template tags contains {CF-CustomFieldName}
            if( preg_match_all('/\{(CF)(-)(\S*)\}/', $imgCaption, $cf_matches) ) {
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

			$imgCaption = str_replace( $search_arr, $replace_arr, $imgCaption );

            $description = str_replace( $search_arr, $replace_arr, $description );
			$description = $this->model->wpw_auto_poster_stripslashes_deep( $description );
			$description = $this->model->wpw_auto_poster_html_decode( $description );


			// Get post categories
			$categories = wpw_auto_poster_get_post_categories_by_ID( $post_type, $post->ID );

			//Telegram Chats Data from setting //_wpweb_tele_post_profile
			$tele_post_profiles = get_post_meta( $post->ID, $prefix . 'tele_post_profile' );

			if( $post_type == 'wpwsapquickshare'){
                $tele_post_profiles = get_post_meta($post->ID, $prefix . 'tele_post_profile',true);
            }

			/******* Code to posting to selected category Telegram account ******/
			$category_selected_chats = get_option( 'wpw_auto_poster_category_posting_acct' );

			if( !empty($categories) && !empty($category_selected_chats) && empty($tele_post_profiles) ) {
				$tele_clear_cnt = true;
				foreach( $categories as $key => $term_id ) {
					$cat_id = $term_id;
					if( !empty($category_selected_chats[$cat_id]['tele']) ) {

						if( $tele_clear_cnt ) $tele_post_profiles = array();

						$tele_post_profiles = array_merge( $tele_post_profiles, $category_selected_chats[$cat_id]['tele'] );

						$tele_clear_cnt = false;
					}
				}
				if( !empty($tele_post_profiles) ) {
					$tele_post_profiles = array_unique($tele_post_profiles);
				}
			}
			
			// If profiles are empty at cat level too
			if( empty($tele_post_profiles) ) {
				$tele_post_profiles	= isset( $wpw_auto_poster_options['tele_type_' . $post->post_type . '_chats'] ) ? $wpw_auto_poster_options['tele_type_' . $post->post_type . '_chats'] : '';
			}

			$content = array( 'submitted-url' => $postlink );

			//posting logs data
			$posting_logs_data = array( 'link' => $postlink );

			if( $sendType == 'photo' ) {
				$content['submitted-image-url'] = $postimage;
				$posting_logs_data['image'] = $postimage;
				$content['description'] = $imgCaption;
			} else {
				$content['description'] = $description;
				$posting_logs_data['description'] = $description;
			}

			//record logs for linkedin data$postingErr
			$this->logs->wpw_auto_poster_add( 'Telegram post data : ' . var_export( $content, true ) );

			//Initilize linkedin posting
			$tele_posting = array();
			
			if( !empty($tele_post_profiles) ) {
				foreach( $tele_post_profiles as $tele_post_profile ) {

					$profile	= explode( '|', $tele_post_profile );
					$token		= isset( $profile[0] ) ? $profile[0] : '';
					$chatID		= isset( $profile[1] ) ? $profile[1] : '';

					$postingErr = '';
					
					try {
						if( $sendType == 'photo' ) {
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
							curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/sendPhoto');
							$postFields = array(
								'chat_id'		=> $chatID,
								'caption'		=> $imgCaption . " " . $hashtags . " " . $hashcats,
								'parse_mode'	=> 'HTML',
								'photo'			=> $postimage
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
							$response = json_decode( curl_exec($ch) );

							if(!$response)
								$this->logs->wpw_auto_poster_add( 'Telegram CURL ERROR : ' . curl_error($ch) );
							curl_close($ch);
						} else {
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
							curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$token.'/sendMessage');
							$postFields = array(
							'chat_id'		=> $chatID,
								'chat_id'		=> $chatID,
								'text'			=> $description,
								'parse_mode'	=> 'HTML'
							);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
							$response = json_decode( curl_exec($ch) );
							
							if(!$response)
								$this->logs->wpw_auto_poster_add( 'Telegram CURL ERROR : ' . curl_error($ch) );
							curl_close($ch);
						}
					} catch(Exception $e) {
						$postingErr = $e->getResponse()->getDecodedBody();
					}

					//record logs for linkedin data
					if( empty($postingErr) && $response->result->message_id ) {
						$posting_logs_user_details = array(
							'account_id' 		=> $chatID,
							'posting_type'		=> $sendType,
							'telegram_token' 	=> $token,
							'telegram_chatid'	=> $chatID,
							'message_id'		=> $response->result->message_id
						);

						$this->logs->wpw_auto_poster_add( 'Telegram SUCCESS: Posted data on Chat (ID): ' . $chatID . ', Message ID : ' . $response->result->message_id );

						if( $post_type == 'wpwsapquickshare'){
		                    update_post_meta($post->ID, $prefix . 'tele_post_status','success');
		                }

						//posting logs store into database
						$this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'tele', $posting_logs_data, $posting_logs_user_details );
						
						$tele_posting['success'] = 1;
					} else {
						$this->logs->wpw_auto_poster_add( 'Telegram ERROR: Posting on Chat (ID): ' . $chatID . ', ERROR MESSAGE: ' . var_export( $postingErr, true ) );
						if( $post_type == 'wpwsapquickshare'){
		                    update_post_meta($post->ID, $prefix . 'tele_post_status','error');
		                    update_post_meta($post->ID, $prefix . 'tele_error', esc_html__('Something was wrong while posting on Chat (ID): ' . $chatID , 'wpwautoposter' ) );
		                }
						$tele_posting['fail'] = 1;
					}
				}
			}
			
		} else {
			
			//record logs when grant extended permission not set
			$this->logs->wpw_auto_poster_add( 'Telegram error: No chats found to post message.' );
			if( $post_type == 'wpwsapquickshare'){
                update_post_meta($post->ID, $prefix . 'tele_post_status','error');
                update_post_meta($post->ID, $prefix . 'tele_error', esc_html__('Please add and select chats before posting to the Telegram.', 'wpwautoposter' ) );
            }
			// display error notice on post page
			sap_add_notice( esc_html__('Telegram: Please add and select chats before posting to the Telegram.', 'wpwautoposter' ), 'error');
		}

		return $tele_posting;
	}
}