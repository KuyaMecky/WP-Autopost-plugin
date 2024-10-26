<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pinterest Posting Class
 *
 * Handles all the functions to post pins
 * to a chosen Pinterest Boards.
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */
class Wpw_Auto_Poster_PIN_Posting
{

    public $pinterest, $message, $model, $logs, $_user_cache, $authMethod, $cookie;

    public function __construct()
    {

        global $wpw_auto_poster_message_stack, $wpw_auto_poster_model,
        $wpw_auto_poster_logs, $wpw_auto_poster_options;

        $this->message = $wpw_auto_poster_message_stack;
        $this->model = $wpw_auto_poster_model;
        $this->logs = $wpw_auto_poster_logs;

        $this->authMethod = !empty($wpw_auto_poster_options['pinterest_auth_options']) ? $wpw_auto_poster_options['pinterest_auth_options'] : 'app';

        $this->cookie = false;
        if ($this->authMethod == 'cookie') {
            if (! class_exists('Wpw_Auto_Poster_PIN_Cookie_Posting')) {
                require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-pin-cookie-posting.php');
            }
            $this->cookie = new Wpw_Auto_Poster_PIN_Cookie_Posting();
        }

        //initialize the session value when data is saved in database
        add_action('init', array($this, 'wpw_auto_poster_pin_initialize'));
    }

    /**
     * Include Pinterest Class
     *
     * Handles to load pinterest class
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_load_pinterest($app_id = false)
    {

        global $wpw_auto_poster_options;

        // Getting pinterest apps
        $pin_apps = wpw_auto_poster_get_pin_apps();

        // If app id is not passed then take first pinterest app data
        if (empty($app_id)) {
            $pin_apps_keys = array_keys($pin_apps);
            $app_id = reset($pin_apps_keys);
        }

        // Check whether application id and application secret is set or not
        if (!empty($app_id) && !empty($pin_apps[$app_id])) {
            require_once(WPW_AUTO_POSTER_SOCIAL_DIR . '/pinterest/autoload.php');
            $this->pinterest = new DirkGroenen\Pinterest\Pinterest($app_id, $pin_apps[$app_id]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pinterest Login URL
     *
     * Getting the login URL from Pinterest.
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_get_pinterest_login_url($app_id = false)
    {
        $pinterest = $this->wpw_auto_poster_load_pinterest($app_id);
        if (!$pinterest) {
            return false;
        }
        $redirect_URL = admin_url('admin.php');
        
        $this->pinterest->auth->setState('pinterestapp#'.$app_id);
        $loginUrl = $this->pinterest->auth->getLoginUrl($redirect_URL, array('pins:write
','pins:read', 'boards:read','boards:write','user_accounts:read'));
        return $loginUrl;
    }

    /**
     * Assign Pinterest User's all Data to session
     *
     * Handles to assign user's pinterest data
     * to sessoin & save to database
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_pin_initialize()
    {

        global $wpw_auto_poster_options;

        if (( isset($_GET['code']) && isset($_REQUEST['state']) && strpos($_REQUEST['state'], 'pinterestapp') !== false )) {
            //record logs for grant extended permission
            $this->logs->wpw_auto_poster_add('Pinterest Grant Extended Permission', true);

            //record logs for get parameters set properly
            $this->logs->wpw_auto_poster_add('Get Parameters Set Properly.');
            $code       = stripslashes_deep($_GET['code']);
            $state      = stripslashes_deep($_GET['state']);
            $app = explode('#', $state);
            $pin_app_id = stripslashes_deep($app[1]);
            $redirect_URL = admin_url('admin.php');

            try {
                //load pinterest class
                $pinterest = $this->wpw_auto_poster_load_pinterest($pin_app_id);
            } catch (Exception $e) {
                //record logs exception generated
                $this->logs->wpw_auto_poster_add('Pinterest error: ' . $e->getMessage());
                $pinterest = null;
            }

            //check pinterest class is exis or not
            if (!$pinterest) {
                return false;
            }

            // Pinterest
            try {
                $token = $this->pinterest->auth->getOAuthToken($code, $redirect_URL);
            } catch (Exception $e) {

                //record logs exception generated
                $this->logs->wpw_auto_poster_add('Pinterest error: ' . $e->getMessage());
            }

            $me = false;
            if (!empty($token->access_token)) {

                $pin_access_token = $token->access_token;

                try {
                    $grant = $this->pinterest->auth->setOAuthToken($token->access_token);

                    $me = $this->pinterest->users->me(
                        array(
                            'fields' => 'username,first_name,last_name'
                        )
                    );

                    //record logs for user id
                    $this->logs->wpw_auto_poster_add('Pinterest User ID : ' . $me->id);
                } catch (Exception $e) {
                    //record logs exception generated
                    $this->logs->wpw_auto_poster_add('Pinterest error: ' . $e->getMessage());
                }
            }

            //check user is logged in pinterest or not
            if ($me) {

                try {
                    // Proceed knowing you have a logged in user who's authenticated.
                    $wpweb_pin_user_id = $me->username;
                    $wpweb_pin_user_name = $me->username;
                    $wpweb_pin_user_url = $me->website_url;

                    $boards = $this->pinterest->users->getMeBoards(array(
                            'fields' => 'name,url'
                        ));

                    $i = 0;

                    // For record
                    $boardList = $selectBoard = array();

                    foreach ($boards->items as $boardu) {
                        
                        $board_user = $boardu['owner']['username'];

                        $boardList[$boardu['id']] = $boardu['name'];
                        $selectBoard[$boardu['id']] = $boardu['name'];
                    }

                    $wpweb_pin_user_boards = $boardList;
                    // For record
                    $wpweb_pin_user_boards_select = $selectBoard;

                    // Start code to manage session from database
                    $wpw_auto_poster_pin_sess_data = get_option('wpw_auto_poster_pin_sess_data');
                    // Checking if the grant extend is already done or not
                    if (!isset($wpw_auto_poster_pin_sess_data[$pin_app_id])) {
                        $sess_data = array(
                            'wpw_auto_poster_pin_user_id' => $wpweb_pin_user_id,
                            'wpw_auto_poster_pin_user_name' => $wpweb_pin_user_name,
                            'wpw_auto_poster_pin_user_boards' => $wpweb_pin_user_boards,
                            'wpw_auto_poster_pin_token' => $pin_access_token,
                        );
                        if ($pin_app_id) {
                            // Save Multiple Accounts
                            $wpw_auto_poster_pin_sess_data[$pin_app_id] = $sess_data;

                            // Update session data to options
                            update_option('wpw_auto_poster_pin_sess_data', $wpw_auto_poster_pin_sess_data);

                            // Record logs for session data updated to options
                            $this->logs->wpw_auto_poster_add('Session Data Updated to Options');
                        } else {
                            // Record logs when app id is not found
                            $this->logs->wpw_auto_poster_add("Pinterest error: The App Id {$pin_app_id} does not exist.");
                        }
                    }// end code to manage session from database
                    // Record logs for grant extend successfully
                    $this->logs->wpw_auto_poster_add('Grant Extended Permission Successfully.');
                } catch (Exception $e) {
                    //record logs exception generated
                    $this->logs->wpw_auto_poster_add('Pinterest error: ' . $e->__toString());

                    //user is null
                    $me = null;
                } //end catch
            } //end if to check user is not empty
            //set tab selected
            $this->message->add_session('poster-selected-tab', 'pinterest');

            //redirect to proper page
            wp_redirect(add_query_arg(array('page' => 'wpw-auto-poster-settings' ), admin_url('admin.php')));
            
            exit;
        } //end if to check page is set and wpw_fb_grant is set & its true & code is set & state is set in $_GET
    }

    /**
     * Reset Sessions
     *
     * Resetting the Pinterest sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_pin_reset_session()
    {

        global $wpw_auto_poster_options;

        // Check if pinterest reset user link is clicked and pin_reset_user is set to 1 and pinterest app id is there
        if (isset($_GET['pin_reset_user']) && $_GET['pin_reset_user'] == '1' && !empty($_GET['wpw_pin_app'])) {
            $wpw_pin_app_id = stripslashes_deep($_GET['wpw_pin_app']);

            // Getting stored pin app data
            $wpw_auto_poster_pin_sess_data = get_option('wpw_auto_poster_pin_sess_data');

            // Getting pinterest app users
            $app_users = wpw_auto_poster_get_pin_accounts('all_app_users_with_boards');

            // Users need to flush from stored data
            $reset_app_users = !empty($app_users[$wpw_pin_app_id]) ? $app_users[$wpw_pin_app_id] : array();

            // Unset perticular app value data and update the option
            if (isset($wpw_auto_poster_pin_sess_data[$wpw_pin_app_id])) {
                unset($wpw_auto_poster_pin_sess_data[$wpw_pin_app_id]);
                update_option('wpw_auto_poster_pin_sess_data', $wpw_auto_poster_pin_sess_data);
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
                if (isset($wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'])) {
                    // Get stored pinterest users according to post type
                    $pin_stored_users = $wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'];

                    // Flusing the App users and taking remaining
                    $new_stored_users = array_diff($pin_stored_users, $reset_app_users);

                    // If empty data then unset option else update remaining
                    if (!empty($new_stored_users)) {
                        $wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'] = $new_stored_users;
                    } else {
                        unset($wpw_auto_poster_options['pin_type_' . $posttype->name . '_user']);
                    }
                } //end if
            } //end foreach
            // Update autoposter options to settings
            update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
        } //end if
    }

    /**
     * Reset Cookie Sessions
     *
     * Resetting the Pinterest sessions when the admin clicks on
     * its link within the settings page.
     *
     * @package Social Auto Poster
     * @since 3.5.1
     */
    public function wpw_auto_poster_pin_reset_cookie_account()
    {

        global $wpw_auto_poster_options;

        if (isset($_GET['remove_pin_cookie_acc']) && $_GET['remove_pin_cookie_acc'] == '1' && !empty($_GET['wpw_pin_cookie_index'])) {
            $username = esc_attr($_GET['wpw_pin_cookie_index']);

            $allPinData = get_option('wpw_auto_poster_pin_sess_data', array());

            if (isset($allPinData[$username])) {
                unset($allPinData[$username]);
                update_option('wpw_auto_poster_pin_sess_data', $allPinData);
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
                if (isset($wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'])) {
                    // Get stored pinterest users according to post type
                    $pin_stored_users = $wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'];
                    foreach ($pin_stored_users as $key => $pin_user) {
                        $user = explode('|', $pin_user);

                        if (isset($user[0]) && $user[0] == $username) {
                            unset($pin_stored_users[$key]);
                        }
                    }
                    $wpw_auto_poster_options['pin_type_' . $posttype->name . '_user'] = $pin_stored_users;
                } //end if
            } //end foreach

            // Update autoposter options to settings
            update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
        }
    }

    /**
     * Pinterest Posting
     *
     * Handles to pinterest posting
     * by post data
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_pin_posting($post, $auto_posting_type = '')
    {

        global $wpw_auto_poster_options;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //post to user board on pinterest
        $res = $this->wpw_auto_poster_pin_post_to_userwall($post, $auto_posting_type);

        if (!empty($res)) { //check post has been posted on pinterest or not
            //record logs for posting done on pinterest
            $this->logs->wpw_auto_poster_add('Pinterest posting completed successfully.');

            update_post_meta($post->ID, $prefix . 'pin_published_on_pin', '1');

            // get current timestamp and update meta as published date/time
            $current_timestamp = current_time('timestamp');
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            
            return true;
        }

        return false;
    }

    /**
     * Post to User board on Pinterest
     *
     * Handles to post content on Pinterest user board
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_pin_post_to_userwall($post, $auto_posting_type)
    {

        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;

        // Get stored pin app grant data
        $wpw_auto_poster_pin_sess_data = get_option('wpw_auto_poster_pin_sess_data');

        // Check pinterest grant extended permission is set ot not
        if (!empty($wpw_auto_poster_pin_sess_data)) {
            // Posting logs data
            $posting_logs_data = array();

            //Initialize tags and categories
            $tags_arr = array();
            $cats_arr = array();

            //metabox field prefix
            $prefix = WPW_AUTO_POSTER_META_PREFIX;

            $post_type = $post->post_type; // Post type
            $unique = 'false'; // Unique
            $userdata = get_userdata($post->post_author); //user data form post author
            $first_name = $userdata->first_name; //user first name
            $last_name = $userdata->last_name; //user last name
            //published status
            $ispublished = get_post_meta($post->ID, $prefix . 'pin_published_on_pin', true);

            $full_author = normalize_whitespace( $first_name.' '.$last_name ); // user full name
            $nickname_author = get_user_meta($post->post_author, 'nickname', true); // user nickname

            // Get all selected tags for selected post type for hashtags support
            if (isset($wpw_auto_poster_options['pin_post_type_tags']) && !empty($wpw_auto_poster_options['pin_post_type_tags'])) {
                $custom_post_tags = $wpw_auto_poster_options['pin_post_type_tags'];
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
            if (isset($wpw_auto_poster_options['pin_post_type_cats']) && !empty($wpw_auto_poster_options['pin_post_type_cats'])) {
                $custom_post_cats = $wpw_auto_poster_options['pin_post_type_cats'];
                if (isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])) {
                    foreach ($custom_post_cats[$post_type] as $key => $category) {
                        $term_list = wp_get_post_terms($post->ID, $category, array("fields" => "names"));
                        foreach ($term_list as $term_single) {
                            $cats_arr[] = str_replace(' ', '', $term_single);
                        }
                    }
                }
            }

            if (!isset($wpw_auto_poster_options['prevent_post_pin_metabox'])) {//check if prevent metabox is not enable
                $wpw_auto_poster_pin_user_id = get_post_meta($post->ID, $prefix . 'pin_user_id');
                $wpw_auto_poster_custom_link = get_post_meta($post->ID, $prefix . 'pin_custom_post_link', true);
                $wpw_auto_poster_custom_note = get_post_meta($post->ID, $prefix . 'pin_custom_status_msg', true);
                $wpw_auto_poster_custom_img = get_post_meta($post->ID, $prefix . 'pin_post_image', true);
            } //end if
            // Getting all pinterest apps

            if ($post_type == 'wpwsapquickshare') {
                $wpw_auto_poster_pin_user_id = get_post_meta($post->ID, $prefix . 'pin_user_id', true);
            }

            if (!empty($auto_posting_type) && $auto_posting_type == 'reposter') {
                // global custom post msg template for reposter
                $pin_global_custom_message_template = ( isset($wpw_auto_poster_reposter_options["repost_pin_global_message_template_".$post_type]) ) ? $wpw_auto_poster_reposter_options["repost_pin_global_message_template_".$post_type] : '';

                $pin_global_custom_msg_options = isset($wpw_auto_poster_reposter_options['repost_pin_custom_msg_options']) ? $wpw_auto_poster_reposter_options['repost_pin_custom_msg_options'] : '';

                // global custom msg template for reposter
                $pin_global_message_template = ( isset($wpw_auto_poster_reposter_options["repost_pin_global_message_template"]) ) ? $wpw_auto_poster_reposter_options["repost_pin_global_message_template"] : '';
            } else {
                // custom post type message from instagram global setting
                $pin_global_custom_message_template = ( isset($wpw_auto_poster_options["pin_custom_template_".$post_type]) ) ? $wpw_auto_poster_options["pin_custom_template_".$post_type] : '';

                $pin_global_custom_msg_options = isset($wpw_auto_poster_options['pin_custom_msg_options']) ? $wpw_auto_poster_options['pin_custom_msg_options'] : '';

                $pin_global_message_template = $wpw_auto_poster_options["pin_custom_template"];
            }

            if (isset($wpw_auto_poster_custom_note) && $wpw_auto_poster_custom_note) {
                //custom status message to post on pinterest
                $custom_msg = $wpw_auto_poster_custom_note;
            } elseif ($pin_global_custom_msg_options == 'post_msg' && !empty($pin_global_custom_message_template)) {
                //custom message set at pinterest settings custom post type message
                $custom_msg = $pin_global_custom_message_template;
            } else {
                //custom message set at pinterest settings
                $custom_msg = $pin_global_message_template;
            }

            //remove html entity from custom message
            $custom_msg = $this->model->wpw_auto_poster_html_decode($custom_msg);

            //get post title
            $title = $post->post_title;
            //remove html entity from title
            $title = $this->model->wpw_auto_poster_html_decode($title);

             //post link for posting to user board pin
            $postlink = isset($wpw_auto_poster_custom_link) && !empty($wpw_auto_poster_custom_link) ? $wpw_auto_poster_custom_link : '';
           
            //if custom link is set or not
            $customlink = !empty($postlink) ? 'true' : 'false';
            //do url shortner
            $postlink = $this->model->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, $customlink, 'pin');

            //Check if not
            if (empty($postlink)) {
                $postlink = $this->model->wpw_auto_poster_get_permalink_before_publish($post->ID);
            }

            if (isset($ispublished) && !empty($ispublished)) {
                $unique = 'true';
            }

            $post_content = strip_shortcodes($post->post_content);
            $post_content = apply_filters('the_content', $post_content);
            
            //strip html kses and tags
            $post_content = $this->model->wpw_auto_poster_stripslashes_deep($post_content);
            //decode html entity
            $post_content = $this->model->wpw_auto_poster_html_decode($post_content);
            // Taking the limited content to avoid the exception
            $post_content = $this->model->wpw_auto_poster_excerpt($post_content, 9500);

            // Get post excerpt
            $excerpt = !empty($post->post_excerpt) ? $this->model->wpw_auto_poster_html_decode($this->model->wpw_auto_poster_stripslashes_deep($post->post_excerpt)) : $this->model->wpw_auto_poster_custom_excerpt( $post->ID );

            // Get post tags
            $tags_arr = apply_filters('wpw_auto_poster_pin_hashtags', $tags_arr);
            $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';


            // get post categories
            $cats_arr = apply_filters('wpw_auto_poster_pin_hashcats', $cats_arr);
            $hashcats = (!empty($cats_arr) ) ? '#' . implode(' #', $cats_arr) : '';

            // check if custom message is empty if yes than set caption as post title
            if (!empty($custom_msg)) {
                $search_arr = array('{first_name}', '{last_name}', '{title}', '{full_author}', '{nickname_author}', '{post_type}', '{link}', '{excerpt}', '{sitename}', '%title%', '%link%', '{hashtags}', '{hashcats}','{content}');
                $replace_arr = array($first_name, $last_name, $title, $full_author, $nickname_author, $post_type, $postlink, $excerpt, get_option('blogname'), $title, $postlink, $hashtags, $hashcats,$post_content);

                $code_matches = array();
    
                // check if template tags contains {content-numbers}
                if (preg_match_all('/\{(content)(-)(\d*)\}/', $custom_msg, $code_matches)) {
                    $trim_tag = $code_matches[0][0];
                    $trim_length = $code_matches[3][0];
                    $post_content = substr($post_content, 0, $trim_length);
                    $search_arr[] = $trim_tag;
                    $replace_arr[] = $post_content;
                }

                $cf_matches = array();
                // check if template tags contains {CF-CustomFieldName}
                if (preg_match_all('/\{(CF)(-)(\S*)\}/', $custom_msg, $cf_matches)) {
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

                $notes = str_replace($search_arr, $replace_arr, $custom_msg);
            } else {
                $notes = $title;
            }

            /* * ************
             * Image Priority
             * If metabox image set then take from metabox
             * If metabox image is not set then take from featured image
             * If featured image is not set then take from settings page
             * ************ */

            //get featured image from post / page / custom post type
            $post_featured_img['src'] = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            $post_featured_img['path'] = get_attached_file(get_post_thumbnail_id($post->ID), 'full');

            $img_src = '';
            //check custom image is set in meta and not empty
            if (!empty($wpw_auto_poster_custom_img['src'])) {
                $img_src = $wpw_auto_poster_custom_img['src'];

                $img_src = apply_filters('wpw_auto_poster_social_media_posting_image', $img_src);

                if (empty($wpw_auto_poster_custom_img['id'])) {
                    $img_path = wpw_auto_poster_get_image_path($img_src);
                } else {
                    $img_path = get_attached_file($wpw_auto_poster_custom_img['id'], 'full');
                }
            } elseif (isset($post_featured_img) && !empty($post_featured_img['src']) && !empty($post_featured_img['path'])) {
                //check post featrued image is set the use that image
                $img_src = $post_featured_img['src'][0];

                $img_src = apply_filters('wpw_auto_poster_social_media_posting_image', $img_src);

                if (strpos($img_src, site_url()) !== false) {
                    $img_path = $post_featured_img['path'];
                } else {
                    $img_path = wpw_auto_poster_get_image_path($img_src);
                }
            } else {
                $pin_global_custom_msg_options = isset($wpw_auto_poster_options['pin_custom_msg_options']) ? $wpw_auto_poster_options['pin_custom_msg_options'] : '';

                if ($pin_global_custom_msg_options == 'post_msg' && isset($wpw_auto_poster_options['pin_custom_img_'.$post_type]) && !empty($wpw_auto_poster_options['pin_custom_img_'.$post_type])) {
                    //else get individual post type post image from settings page
                    $img_src = $wpw_auto_poster_options['pin_custom_img_'.$post_type];
                } elseif (isset($wpw_auto_poster_options['pin_custom_img']) && !empty($wpw_auto_poster_options['pin_custom_img'])) {
                    //else get post image from settings page
                    $img_src = $wpw_auto_poster_options['pin_custom_img'];
                }

                if (!empty($img_src)) {
                    $img_src = apply_filters('wpw_auto_poster_social_media_posting_image', $img_src);
                    
                    $site_url = site_url();

                    if (strpos($img_src, site_url()) !== false) {
                        $imagePath = str_replace($site_url, "", $img_src);
                        $img_path = '..' . $imagePath;
                    } else {
                        $img_path = wpw_auto_poster_get_image_path($img_src);
                    }
                }
            }

            $pin_apps = wpw_auto_poster_get_pin_apps();

            // Getting all stored pinterest auth token
            $pin_auth_token = wpw_auto_poster_get_pin_accounts('all_auth_tokens');

            // Pinterest user id on whose wall the post will be posted
            $pin_user_ids = '';

            //check there is pinterest user ids are set and not empty in metabox
            if (!empty($wpw_auto_poster_pin_user_id)) {
                //users from metabox
                $pin_user_ids = $wpw_auto_poster_pin_user_id;
            } else {
                /* * *** Backward Compatibility Code Starts **** */
                // If user account is selected in meta so creating data accoring to new method ( Will be helpfull when scheduling is done )
                $pin_first_app_key = !empty($wpw_auto_poster_options['pinterest_keys'][0]['app_id']) ? $wpw_auto_poster_options['pinterest_keys'][0]['app_id'] : '';

                if (!empty($pin_first_app_key)) {
                    foreach ($pin_user_ids as $pin_user_key => $pin_user_data) {
                        if (strpos($pin_user_data, '|') === false) {
                            $pin_user_ids[$pin_user_key] = $pin_user_data . '|' . $pin_first_app_key;
                        }
                    }
                }
                /* * *** Backward Compatibility Code Ends **** */
            } //end if
            /******* Code to posting to selected category Pinterest account ******/

            // get all categories for custom post type
            $categories = wpw_auto_poster_get_post_categories_by_ID($post_type, $post->ID);
             
            // Get all selected account list from category
            $category_selected_social_acct = get_option('wpw_auto_poster_category_posting_acct');

            // IF category selected and category social account data found
            if (!empty($categories) && !empty($category_selected_social_acct) && empty($pin_user_ids)) {
                $pin_clear_cnt = true;
                // GET Pinterest user account ids from post selected categories
                foreach ($categories as $key => $term_id) {
                    $cat_id = $term_id;

                    // Get Pinterest user account ids form selected category
                    if (isset($category_selected_social_acct[$cat_id]['pin']) && !empty($category_selected_social_acct[$cat_id]['pin'])) {
                        // clear pinterest user data once
                        if ($pin_clear_cnt) {
                            $pin_user_ids = array();
                        }
                        $pin_user_ids = array_merge($pin_user_ids, $category_selected_social_acct[$cat_id]['pin']);
                        $pin_clear_cnt = false;
                    }
                }
                if (!empty($pin_user_ids)) {
                    $pin_user_ids = array_unique($pin_user_ids);
                }
            }

            //check pinterest user ids are empty in metabox and set in settings page
            if (empty($pin_user_ids) && isset($wpw_auto_poster_options['pin_type_' . $post_type . '_user']) && !empty($wpw_auto_poster_options['pin_type_' . $post_type . '_user'])) {
                //users from settings
                $pin_user_ids = $wpw_auto_poster_options['pin_type_' . $post_type . '_user'];
            } //end if

            //check pinterest user ids are empty selected for posting
            if (empty($pin_user_ids)) {
                //record logs for pinterest users are not selected
                $this->logs->wpw_auto_poster_add('Pinterest error: user not selected for posting.');
                if ($post_type == 'wpwsapquickshare') {
                    update_post_meta($post->ID, $prefix . 'pin_post_status', 'error');
                    update_post_meta($post->ID, $prefix . 'pin_error', esc_html__('User not selected for posting.', 'wpwautoposter'));
                }
                // display error notice on post page
                sap_add_notice(esc_html__('Pinterest: You have not selected any user for the posting.', 'wpwautoposter'), 'error');
                //return false
                return false;
            } //end if to check user ids are empty

            //convert user ids to single array
            $post_to_users = (array) $pin_user_ids;

            //posting logs data
            $posting_logs_data = array(
                'notes' => $notes,
                'image' => $img_src,
                'link' => $postlink,
            );

            $send = array(
                'note' => mb_substr($notes, 0, 499),
                'link' => $postlink
            );

            if (isset($img_path) && !empty($img_path)) {
                $send['image'] = $img_path;
            }

            //initial value of posting flag
            $postflg = false;

            if (!empty($post_to_users)) {
                $posting_logs_user_details = array();
                foreach ($post_to_users as $post_to) {
                    if ($this->authMethod == 'cookie') {
                        $allPinData = get_option('wpw_auto_poster_pin_sess_data', array());

                        $pinData = explode('|', $post_to);

                        $username   = isset($pinData[0]) ? $pinData[0] : '';
                        $boardID    = isset($pinData[1]) ? $pinData[1] : '';

                        // Get session id
                        $sessID = !empty($allPinData[$username]['sessid']) ? $allPinData[$username]['sessid'] : '';

                        if (!empty($username) && !empty($boardID) && !empty($sessID)) {
                            $posted = $this->cookie->wpw_auto_poster_post_pin($sessID, $boardID, $send);

                            $bordName = isset($allPinData[$username]['boards'][$boardID]['name']) ? $allPinData[$username]['boards'][$boardID]['name'] : '';

                            $bordURL = isset($allPinData[$username]['boards'][$boardID]['url']) ? $allPinData[$username]['boards'][$boardID]['url'] : '';

                            // User details
                            $posting_logs_user_details = array();
                            $posting_logs_user_details['board_id'] = $boardID;
                            $posting_logs_user_details['board_url'] = $bordURL;
                            if (!empty($bordName)) {
                                $posting_logs_user_details['display_name'] = $username . " - " . $bordName;

                                $send['posting_at'] = $username . " - " . $bordName;
                            }

                            $postflg = false;
                            if (isset($posted['status']) && $posted['status'] == 'success') {
                                $postflg = true;
                                $posting_logs_user_details['pin_id'] = isset($posted['pindata']['id']) ? $posted['pindata']['id'] : '';
                                $send['pin_id'] = isset($posted['pindata']['id']) ? $posted['pindata']['id'] : '';
                            } elseif (isset($posted['status']) && $posted['status'] == 'error') {
                                $send['error'] = isset($posted['message']) ? $posted['message'] : '';
                                sap_add_notice(sprintf(esc_html__('Pinterest: Something was wrong while posting %s', 'wpwautoposter'), $send['error']), 'error');
                            }

                            //posting logs store into database
                            $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'pin', $posting_logs_data, $posting_logs_user_details);

                            //record logs for data posted on pinterest boards
                            $this->logs->wpw_auto_poster_add('Pinterest post data : ' . var_export($send, true));
                        }
                    } else {
                        $post_account_details = wpw_auto_poster_get_pin_accounts('all_app_users_with_boards');

                        $pin_post_app_arr = explode('|', $post_to);
                        // Pinterest Posting board Id
                        $pin_post_to_board_id = isset($pin_post_app_arr[0]) ? $pin_post_app_arr[0] : '';
                        // Pinterest App Id
                        $pin_post_app_id = isset($pin_post_app_arr[1]) ? $pin_post_app_arr[1] : '';
                        //$send['board'] = $pin_post_to_board_id;
                        $boardID = explode('|', $post_to);

                        $send['board_id'] = $boardID[0]; // with latest api change need to pass username/boarname instead of board id
                        $send['media_source'] = array(
                            'source_type' => 'image_url',
                            'url' => $img_src
                        );
                        $send['title'] = $send['note'];
                        unset($send['image']);
                        unset($send['note']);

                        //check there is auth token is set for pinterest user
                        if (isset($pin_auth_token[$pin_post_app_id])) {
                            $auth_token = $pin_auth_token[$pin_post_app_id];
                        }

                        if (!empty($wpw_auto_poster_pin_sess_data[$pin_post_app_id])) {

                            $account_name = $wpw_auto_poster_pin_sess_data[$pin_post_app_id]['wpw_auto_poster_pin_user_name'];

                            if (!empty($wpw_auto_poster_pin_sess_data[$pin_post_app_id]['wpw_auto_poster_pin_user_boards'])) {
                                $board_name = $wpw_auto_poster_pin_sess_data[$pin_post_app_id]['wpw_auto_poster_pin_user_boards'][$pin_post_to_board_id];
                            }
                        }

                        // User details
                        if (!empty($account_name) && !empty($board_name)) {
                            $posting_logs_user_details = array(
                                'display_name' => $account_name." - ".$board_name,
                            );
                        }

                        try {
                            $pinterest = $this->wpw_auto_poster_load_pinterest($pin_post_app_id);
                            if (!$pinterest) {
                                return false;
                            }
                            $this->pinterest->auth->setOAuthToken($auth_token);

                            $pub = $this->pinterest->pins->create($send);

                            if ($post_type == 'wpwsapquickshare') {
                                update_post_meta($post->ID, $prefix . 'pin_post_status', 'success');
                            }

                            //posting logs store into database
                            $this->model->wpw_auto_poster_insert_posting_log($post->ID, 'pin', $posting_logs_data, $posting_logs_user_details);
                            //record logs for data posted on pinterest boards
                            $this->logs->wpw_auto_poster_add('Pinterest post data : ' . ' Pin Id: '. $pub->id . ' | ' . $pub->title);
                            $postflg = true;
                        } catch (\Exception $e) {
                            $this->logs->wpw_auto_poster_add('Pinterest error: Posting exception for ' . $post_to . ' : ' . $e->getMessage());

                            if ($post_type == 'wpwsapquickshare') {
                                update_post_meta($post->ID, $prefix . 'pin_post_status', 'error');
                                update_post_meta($post->ID, $prefix . 'pin_error', sprintf(esc_html__('Something was wrong while posting %s', 'wpwautoposter'), $e->getMessage()));
                            }
                            sap_add_notice(sprintf(esc_html__('Pinterest: Something was wrong while posting %s', 'wpwautoposter'), $e->getMessage()), 'error');
                            $postflg = false;
                        }
                    }
                }
            }

            return $postflg;
        } else {
            //record logs when grant extended permission not set
            $this->logs->wpw_auto_poster_add('Pinterest error: Grant extended permissions not set.');
            if ($post_type == 'wpwsapquickshare') {
                update_post_meta($post->ID, $prefix . 'pin_post_status', 'error');
                update_post_meta($post->ID, $prefix . 'pin_error', esc_html__('Grant extended permissions not set.', 'wpwautoposter'));
            }
            sap_add_notice(esc_html__('Pinterest: Please give Grant extended permission before posting to the Pinterest.', 'wpwautoposter'), 'error');
        } //end else
    }
}
