<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quick Share Class
 * Handles generic quick share functionality.
 *
 * @package Social Auto Poster
 * @since 3.9.2
 */
class Wpw_Auto_Posting_QuickShare
{

    public $model, $logs, $fbposting, $twposting, $liposting, $insposting, $pinposting, $ytposting, $gmbposting, $redditposting, $instaposting, $tbposting, $baposting, $wpposting, $teleposting, $mediumposting;

    public function __construct()
    {
        global $wpw_auto_poster_model,$wpw_auto_poster_fb_posting,$wpw_auto_poster_tw_posting,$wpw_auto_poster_li_posting,$wpw_auto_poster_ba_posting,$wpw_auto_poster_tb_posting,$wpw_auto_poster_ins_posting,$wpw_auto_poster_logs,$wpw_auto_poster_pin_posting,$wpw_auto_poster_yt_posting,$wpw_auto_poster_wp_posting,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_tele_posting,$wpw_auto_poster_medium_posting,$wpw_auto_poster_insta_posting;

        $this->logs = $wpw_auto_poster_logs;
        $this->model = $wpw_auto_poster_model;
        $this->fbposting = $wpw_auto_poster_fb_posting;
        $this->twposting = $wpw_auto_poster_tw_posting;
        $this->liposting = $wpw_auto_poster_li_posting;
        $this->tbposting = $wpw_auto_poster_tb_posting;
        $this->baposting = $wpw_auto_poster_ba_posting;
        $this->insposting = $wpw_auto_poster_ins_posting;
        $this->pinposting = $wpw_auto_poster_pin_posting;
        $this->ytposting = $wpw_auto_poster_yt_posting;
        $this->wpposting = $wpw_auto_poster_wp_posting;
        $this->gmbposting = $wpw_auto_poster_gmb_postings;
        $this->redditposting = $wpw_auto_poster_reddit_postings;
        $this->teleposting = $wpw_auto_poster_tele_posting;
        $this->mediumposting = $wpw_auto_poster_medium_posting;
        $this->instaposting = $wpw_auto_poster_insta_posting;
    }

    /**
     * Save Quick Share
     *
     * @package Social Auto Poster
     * @since 3.9.2
     */
    public function wpw_auto_poster_save_quick_share()
    {

        if (isset($_POST['quick_share_save']) && wp_verify_nonce($_POST['quick_share_save'], 'wpw_auto_poster_save_quick_share')) {
            // Check title and message must be added
            if (empty(trim($_POST['qs_message']))) {
                // redirect URL
                $redirect_url = add_query_arg(array(
                    'page' => 'wpw-auto-poster-quick-share',
                    'message' => 3
                ), admin_url('admin.php'));
                set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                wp_redirect($redirect_url);
                exit;
            }

            // check if any of social media enabled or not
            if (empty($_POST['enable_socials'])) {
                // redirect URL
                $redirect_url = add_query_arg(array(
                    'page' => 'wpw-auto-poster-quick-share',
                    'message' => 4
                ), admin_url('admin.php'));
                set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                wp_redirect($redirect_url);
                exit;
            }

            if (isset($_POST['enable_socials']) && !empty($_POST['enable_socials'])) {
                $selected_networks = stripslashes_deep($_POST['enable_socials']);
                $empty_networks = array();
                foreach ($selected_networks as $key => $selected_network) {
                    if (empty($_POST['qs_accounts_'.$selected_network])) {
                        $empty_networks[] = $selected_network;
                    }
                }
                if (!empty($empty_networks)) {
                    // redirect URL
                    $redirect_url = add_query_arg(array(
                        'page' => 'wpw-auto-poster-quick-share',
                        'message' => 9,
                        'network' => implode(',', $empty_networks)
                    ), admin_url('admin.php'));
                    set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                    wp_redirect($redirect_url);
                    exit;
                }
            }

            if (!empty($_POST['qs_link']) && filter_var($_POST['qs_link'], FILTER_VALIDATE_URL) === false) {
                // redirect URL
                $redirect_url = add_query_arg(array(
                    'page' => 'wpw-auto-poster-quick-share',
                    'message' => 10,
                    'network' => implode(',', $empty_networks)
                ), admin_url('admin.php'));
                set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                wp_redirect($redirect_url);
                exit;
            }

            // posting type
            $fb_posting_type = isset($_POST['_wpweb_fb_share_posting_type']) ? $_POST['_wpweb_fb_share_posting_type'] : '';

            // check for link posting
            if (in_array('fb', $_POST['enable_socials']) && $fb_posting_type == 'link_posting'
             && empty($_POST['qs_link'])) {
                // redirect URL
                $redirect_url = add_query_arg(array(
                    'page' => 'wpw-auto-poster-quick-share',
                    'message' => 5
                ), admin_url('admin.php'));
                set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                wp_redirect($redirect_url);
                exit;
            } elseif (in_array('fb', $_POST['enable_socials']) && $fb_posting_type == 'image_posting'
             && empty($_POST['qs_image']['id'])) {
                // redirect URL
                $redirect_url = add_query_arg(array(
                    'page' => 'wpw-auto-poster-quick-share',
                    'message' => 6
                ), admin_url('admin.php'));
                set_transient('wpw_auto_poster_quick_share_post_data', $_POST);
                wp_redirect($redirect_url);
                exit;
            }

            $message        = isset($_POST['qs_message']) ? sanitize_textarea_field($_POST['qs_message']) : '';
            $image['src']   = isset($_POST['qs_image']['url']) ? esc_url_raw($_POST['qs_image']['url']) : '';
            $video['src']   = isset($_POST['qs_video']['url']) ? esc_url_raw($_POST['qs_video']['url']) : '';
            $link           = isset($_POST['qs_link']) ? sanitize_text_field($_POST['qs_link']) : '';

            // Geather the details
            $postArgs = array(
                'post_title' => sanitize_textarea_field($_POST['qs_message']),
                'post_type' => WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE,
                'post_content' => sanitize_textarea_field($_POST['qs_message']),
                'post_status' => 'publish',
            );

            // insert a post
            $post_id = wp_insert_post($postArgs);

            // Check if post inserted
            if ($post_id) {
                // Get the prefix
                $prefix = WPW_AUTO_POSTER_META_PREFIX;

                // Update post meta
                if (isset($_POST['qs_image']['url'])) {
                    update_post_meta($post_id, $prefix . 'share_image', stripslashes_deep($_POST['qs_image']['url']));
                }
                if (isset($_POST['qs_link'])) {
                    update_post_meta($post_id, $prefix . 'share_link', sanitize_text_field($_POST['qs_link']));
                }
                if (isset($_POST['qs_schedule'])) {
                    update_post_meta($post_id, $prefix . 'share_schedule', sanitize_text_field(strtotime($_POST['qs_schedule'])));
                }
                if (isset($_POST['qs_accounts_fb'])) {
                    update_post_meta($post_id, $prefix.'fb_user_id', stripslashes_deep($_POST['qs_accounts_fb']));
                }

                if (isset($_POST['qs_accounts_tw'])) {
                    update_post_meta($post_id, $prefix.'tw_user_id', stripslashes_deep($_POST['qs_accounts_tw']));
                }

                if (isset($_POST['qs_accounts_li'])) {
                    update_post_meta($post_id, $prefix.'li_post_profile', stripslashes_deep($_POST['qs_accounts_li']));
                }

                if (isset($_POST['qs_accounts_tb'])) {
                    update_post_meta($post_id, $prefix.'tb_user_id', stripslashes_deep($_POST['qs_accounts_tb']));
                }

                if (isset($_POST['qs_accounts_yt'])) {
                    update_post_meta($post_id, $prefix.'yt_user_id', stripslashes_deep($_POST['qs_accounts_yt']));
                }

                if (isset($_POST['qs_accounts_pin'])) {
                    update_post_meta($post_id, $prefix.'pin_user_id', stripslashes_deep($_POST['qs_accounts_pin']));
                }

                if (isset($_POST['qs_accounts_gmb'])) {
                    update_post_meta($post_id, $prefix.'gmb_user_id', stripslashes_deep($_POST['qs_accounts_gmb']));
                }

                if (isset($_POST['qs_accounts_reddit'])) {
                    update_post_meta($post_id, $prefix.'reddit_user_id', stripslashes_deep($_POST['qs_accounts_reddit']));
                }

                if (isset($_POST['qs_accounts_tele'])) {
                    update_post_meta($post_id, $prefix.'tele_post_profile', stripslashes_deep($_POST['qs_accounts_tele']));
                }

                if (isset($_POST['qs_accounts_medium'])) {
                    update_post_meta($post_id, $prefix.'medium_user_id', stripslashes_deep($_POST['qs_accounts_medium']));
                }

                if (isset($_POST['qs_accounts_insta'])) {
                    update_post_meta($post_id, $prefix.'insta_user_id', stripslashes_deep($_POST['qs_accounts_insta']));
                }

                if (isset($_POST['qs_accounts_wp'])) {
                    update_post_meta($post_id, $prefix.'wp_post_sites', stripslashes_deep($_POST['qs_accounts_wp']));
                }


                if (!empty($message)) {
                    update_post_meta($post_id, $prefix . 'fb_custom_title', $message);
                    update_post_meta($post_id, $prefix . 'tw_template', $message);
                    update_post_meta($post_id, $prefix . 'li_post_comment', $message);
                    update_post_meta($post_id, $prefix . 'tb_post_desc', $message);
                    update_post_meta($post_id, $prefix . 'yt_custom_status_msg', $message);
                    update_post_meta($post_id, $prefix . 'pin_custom_status_msg', $message);
                    update_post_meta($post_id, $prefix . 'gmb_custom_status_msg', $message);
                    update_post_meta($post_id, $prefix . 'reddit_post_desc', $message);
                    update_post_meta($post_id, $prefix . 'tele_post_comment', $message);
                    update_post_meta($post_id, $prefix . 'medium_post_desc', $message);
                    update_post_meta($post_id, $prefix . 'insta_post_desc', $message);
                    update_post_meta($post_id, $prefix . 'wp_post_content', $message);
                }

                if (!empty($image)) {
                    update_post_meta($post_id, $prefix . 'fb_post_image', $image);
                    update_post_meta($post_id, $prefix . 'tw_image', $image);
                    update_post_meta($post_id, $prefix . 'li_post_image', $image);
                    update_post_meta($post_id, $prefix . 'tb_post_image', $image);
                    update_post_meta($post_id, $prefix . 'pin_post_image', $image);
                    update_post_meta($post_id, $prefix . 'gmb_post_image', $image);
                    update_post_meta($post_id, $prefix . 'reddit_post_image', $image);
                    update_post_meta($post_id, $prefix . 'tele_post_image', $image);
                    update_post_meta($post_id, $prefix . 'insta_post_image', $image);
                    update_post_meta($post_id, $prefix . 'wp_post_image', $image);
                }
                if (!empty($video)) {
                    update_post_meta($post_id, $prefix . 'yt_post_image', $video);
                }

                if (!empty($link)) {
                    update_post_meta($post_id, $prefix . 'fb_custom_post_link', $link);
                    update_post_meta($post_id, $prefix . 'li_post_link', $link);
                    update_post_meta($post_id, $prefix . 'tb_custom_post_link', $link);
                    update_post_meta($post_id, $prefix . 'pin_custom_post_link', $link);
                    update_post_meta($post_id, $prefix . 'gmb_custom_post_link', $link);
                    update_post_meta($post_id, $prefix . 'reddit_custom_post_link', $link);
                }

                // code to save posting type meta with custom post type
                if (isset($_POST['_wpweb_fb_share_posting_type']) && in_array('fb', $_POST['enable_socials'])) {
                    update_post_meta($post_id, $prefix . 'fb_share_posting_type', sanitize_text_field($_POST['_wpweb_fb_share_posting_type']));
                }
                if (isset($_POST['_wpweb_tb_posting_type']) && in_array('tb', $_POST['enable_socials'])) {
                    update_post_meta($post_id, $prefix . 'tb_posting_type', sanitize_text_field($_POST['_wpweb_tb_posting_type']));
                }
                if (isset($_POST['_wpweb_gmb_add_buttons']) && in_array('gmb', $_POST['enable_socials'])) {
                    update_post_meta($post_id, $prefix . 'gmb_add_buttons', sanitize_text_field($_POST['_wpweb_gmb_add_buttons']));
                }
                if (isset($_POST['_wpweb_reddit_posting_type']) && in_array('reddit', $_POST['enable_socials'])) {
                    update_post_meta($post_id, $prefix . 'reddit_posting_type', sanitize_text_field($_POST['_wpweb_reddit_posting_type']));
                }
                if (isset($_POST['_wpweb_tele_post_msgtype']) && in_array('tele', $_POST['enable_socials'])) {
                    update_post_meta($post_id, $prefix . 'tele_post_msgtype', sanitize_text_field($_POST['_wpweb_tele_post_msgtype']));
                }


                // set post thumbnail image
                if (!empty($_POST['qs_image']['id'])) {
                    set_post_thumbnail($post_id, $_POST['qs_image']['id']);
                }

                // Get all social profiles
                $AllNetworks = $this->model->wpw_auto_poster_get_social_type_data();
                

                // Get enable networks
                $enableNetworks = isset($_POST['enable_socials']) ? $_POST['enable_socials'] : array();

                

                $postedstr = $schedulepoststr = $schedule_post_to = array();


                $post = get_post($post_id, 'OBJECT');
                
                foreach ($AllNetworks as $key => $AllNetwork) {
                    if (isset($key) && $key == 'md') {
                        $key = 'medium';
                    }
                    if (in_array($key, $enableNetworks)) {
                        update_post_meta($post_id, $prefix . 'share_on_' . $key, 'yes');

                        // Facebook Quick Posting
                        if (isset($key) && $key == 'fb') {
                            if (empty($_POST['qs_schedule'])) {
                                $fb_res = $this->fbposting->wpw_auto_poster_fb_posting($post);
                                if ($fb_res) {
                                    $postedstr[] = 'fb';
                                }
                            }
                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'facebook';
                                    
                                $schedulepoststr[] = 'fb';
                                    //Update gmb status to scheduled
                                update_post_meta($post_id, $prefix . 'fb_published_on_fb', 2);
                            }
                        }


                        // Twitter Quick Posting
                        if (isset($key) && $key == 'tw') {
                            if (empty($_POST['qs_schedule'])) {
                                $tb_res = $this->twposting->wpw_auto_poster_tw_posting($post);
                                if ($tb_res) {
                                    $postedstr[] = 'tw';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'twitter';
                                $schedulepoststr[] = 'tw';
                                update_post_meta($post_id, $prefix . 'tw_status', 2);
                            }
                        }


                        // Linkedin Quick Posting
                        if (isset($key) && $key == 'li') {
                            if (empty($_POST['qs_schedule'])) {
                                $li_res = $this->liposting->wpw_auto_poster_li_posting($post);
                                if ($li_res) {
                                    $postedstr[] = 'li';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'linkedin';
                                $schedulepoststr[] = 'li';
                                update_post_meta($post_id, $prefix . 'li_status', 2);
                            }
                        }

                        // Tumblr Quick Posting
                        if (isset($key) && $key == 'tb') {
                            if (empty($_POST['qs_schedule'])) {
                                $tb_res = $this->tbposting->wpw_auto_poster_tb_posting($post);
                                if ($tb_res) {
                                    $postedstr[] = 'tb';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'tumblr';
                                $schedulepoststr[] = 'tb';
                                update_post_meta($post_id, $prefix . 'tb_status', 2);
                            }
                        }

                        //Youtube Quick Posting
                        if (isset($key) && $key == 'yt') {
                            if (empty($_POST['qs_schedule'])) {
                                $yt_res = $this->ytposting->wpw_auto_poster_yt_posting($post);
                                if ($yt_res) {
                                    $postedstr[] = 'yt';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'youtube';
                                $schedulepoststr[] = 'yt';
                                update_post_meta($post_id, $prefix . 'yt_published_on_yt', 2);
                            }
                        }

                        //Pinterest Quick Posting
                        if (isset($key) && $key == 'pin') {
                            if (empty($_POST['qs_schedule'])) {
                                $pin_res = $this->pinposting->wpw_auto_poster_pin_posting($post);
                                if ($pin_res) {
                                    $postedstr[] = 'pin';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'pinterest';
                                $schedulepoststr[] = 'pin';
                                update_post_meta($post_id, $prefix . 'pin_published_on_pin', 2);
                            }
                        }

                        //Google My Business Quick Posting
                        if (isset($key) && $key == 'gmb') {
                            if (empty($_POST['qs_schedule'])) {
                                $gmb_res = $this->gmbposting->wpw_auto_poster_gmb_posting($post);
                                if ($gmb_res) {
                                    $postedstr[] = 'gmb';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'googlemybusiness';
                                $schedulepoststr[] = 'gmb';
                                update_post_meta($post_id, $prefix . 'gmb_published_on_posts', 2);
                            }
                        }

                        //Reddit Quick Posting
                        if (isset($key) && $key == 'reddit') {
                            if (empty($_POST['qs_schedule'])) {
                                $reddit_res = $this->redditposting->wpw_auto_poster_reddit_posting($post);
                                if ($reddit_res) {
                                    $postedstr[] = 'reddit';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'reddit';
                                $schedulepoststr[] = 'reddit';
                                update_post_meta($post_id, $prefix . 'reddit_published_on_posts', 2);
                            }
                        }

                        //Telegram Quick Posting
                        if (isset($key) && $key == 'tele') {
                            if (empty($_POST['qs_schedule'])) {
                                $tele_res = $this->teleposting->wpw_auto_poster_tele_posting($post);
                                if ($tele_res) {
                                    $postedstr[] = 'tele';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'telegram';
                                $schedulepoststr[] = 'tele';
                                update_post_meta($post_id, $prefix . 'tele_status', 2);
                            }
                        }

                        //Medium Quick Posting
                        if (isset($key) && $key == 'medium') {
                            if (empty($_POST['qs_schedule'])) {
                                $medium_res = $this->mediumposting->wpw_auto_poster_medium_posting($post);
                                if ($medium_res) {
                                    $postedstr[] = 'medium';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'medium';
                                $schedulepoststr[] = 'medium';
                                update_post_meta($post_id, $prefix . 'medium_published_on_posts', 2);
                            }
                        }

                        //Instagram Quick Posting
                        if (isset($key) && $key == 'insta') {
                            if (empty($_POST['qs_schedule'])) {
                                $inta_res = $this->instaposting->wpw_auto_poster_insta_posting($post);
                                if ($inta_res) {
                                    $postedstr[] = 'insta';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'instagram';
                                $schedulepoststr[] = 'insta';
                                update_post_meta($post_id, $prefix . 'insta_published_on_insta', 2);
                            }
                        }




                        //Medium Quick Posting
                        if (isset($key) && $key == 'wp') {
                            if (empty($_POST['qs_schedule'])) {
                                $wp_res = $this->wpposting->wpw_auto_poster_wp_posting($post);
                                if ($wp_res) {
                                    $postedstr[] = 'wp';
                                }
                            }

                            if (!empty($_POST['qs_schedule'])) {
                                $schedule_post_to[] = 'wordpress';
                                $schedulepoststr[] = 'wp';
                                update_post_meta($post_id, $prefix . 'wp_status', 2);
                            }
                        }
                    }
                }
                    //update schedule wallpost
                if (!empty($schedule_post_to)) {
                    update_post_meta($post_id, $prefix . 'schedule_wallpost', $schedule_post_to);
                    update_post_meta($post_id, $prefix . 'quick_schedule', 2);
                }

                

                // update enable metas array too
                update_post_meta($post_id, $prefix . 'enable_socials', $enableNetworks);

                // redirect URL
                if (isset($postedstr) && !empty($postedstr)) {
                    $redirect_url = add_query_arg(array(
                        'page' => 'wpw-auto-poster-quick-share',
                        'message' => 7,
                        'wpwautoposteron' => $postedstr
                    ), admin_url('admin.php'));
                } elseif (isset($schedulepoststr) && !empty($schedulepoststr)) {
                    $redirect_url = add_query_arg(array(
                        'page' => 'wpw-auto-poster-quick-share',
                        'message' => 7,
                        'wpwautoposterscheduleon' => $schedulepoststr
                    ), admin_url('admin.php'));
                } else {
                    $redirect_url = add_query_arg(array(
                        'page' => 'wpw-auto-poster-quick-share',
                        'message' => 7,
                    ), admin_url('admin.php'));
                }

                wp_redirect($redirect_url);
                exit;
            }
        }
    }


    /**
     * Cron Job For Quick Share
     *
     * Handle to call schedule cron for
     * send wallpost to followers
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_scheduled_cron_quick_share()
    {
        global $wpw_auto_poster_options,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_medium_posting;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $current_day = current_time('w'); // get current day of week
        // get days which are excluded for posting
         
        // Get all post data which have send wall post
        $postargs = array(
                'post_type' => 'wpwsapquickshare',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => $prefix . 'schedule_wallpost',
                        'value' => '',
                        'compare' => '!='
                    )
                )
            );

        $result = new WP_Query($postargs);
        $postslist = $result->posts;

        
        

        if (!empty($postslist)) { // Check post data are not empty
            foreach ($postslist as $post_data) {
                $postid = $post_data->ID;

                //get schedule wallpost
                $get_schedule = get_post_meta($postid, $prefix . 'schedule_wallpost', true);

                $this->logs->wpw_auto_poster_add('Start schedule Posting', true);

                // Get times for schedule and current
                $schedule_time  = get_post_meta($postid, $prefix . 'share_schedule', true);
                $current_time   = current_time('timestamp');

                if (!empty($get_schedule) && $schedule_time <= $current_time) {
                    if (in_array('facebook', $get_schedule)) { // Check facebook
                        $this->logs->wpw_auto_poster_add('Facebook Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to user wall on facebook
                        $res = $this->fbposting->wpw_auto_poster_fb_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('facebook', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('twitter', $get_schedule)) { // Check twitter
                        $this->logs->wpw_auto_poster_add('Twitter Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to twitter
                        $res = $this->twposting->wpw_auto_poster_tw_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('twitter', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('linkedin', $get_schedule)) { // Check linkedin
                        $this->logs->wpw_auto_poster_add('Linkedin Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to linkedin
                        $res = $this->liposting->wpw_auto_poster_li_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('linkedin', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('tumblr', $get_schedule)) { // Check tumblr
                        $this->logs->wpw_auto_poster_add('Tumblr Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to tumblr
                        $res = $this->tbposting->wpw_auto_poster_tb_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('tumblr', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }


                    
                    if (in_array('instagram', $get_schedule)) { // Check instagram
                        $this->logs->wpw_auto_poster_add('Instagram Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to user timeline on instagram
                        $res=$this->instaposting->wpw_auto_poster_insta_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('instagram', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('youtube', $get_schedule) && !empty($this->ytposting)) { // Check youtube
                        $this->logs->wpw_auto_poster_add('Youtube Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to user timeline on youtube
                        $res = $this->ytposting->wpw_auto_poster_yt_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('youtube', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('pinterest', $get_schedule)) { // Check pinterest
                        $this->logs->wpw_auto_poster_add('Pinterest Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to user board/pins on pinterest
                        $res = $this->pinposting->wpw_auto_poster_pin_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('pinterest', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('wordpress', $get_schedule)) { // Check WordPress
                        $this->logs->wpw_auto_poster_add('WordPress Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to google my business
                        $res = $this->wpposting->wpw_auto_poster_wp_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('wordpress', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('googlemybusiness', $get_schedule)) { // Check google my business
                        $this->logs->wpw_auto_poster_add('Google My Business Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to google my business
                        $res = $wpw_auto_poster_gmb_postings->wpw_auto_poster_gmb_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('googlemybusiness', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                    if (in_array('reddit', $get_schedule)) { // Check google my business
                        $this->logs->wpw_auto_poster_add('Reddit Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to google my business
                        $res = $wpw_auto_poster_reddit_postings->wpw_auto_poster_reddit_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('reddit', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }

                    if (in_array('telegram', $get_schedule) && !empty($this->teleposting)) { // Check for telegram
                        $this->logs->wpw_auto_poster_add('Telegram Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to google my business
                        $res = $this->teleposting->wpw_auto_poster_tele_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('telegram', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }

                    if (in_array('medium', $get_schedule)) { // Medium
                        $this->logs->wpw_auto_poster_add('Medium Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to google my business
                        $res = $wpw_auto_poster_medium_posting->wpw_auto_poster_medium_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('medium', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }
                }

                //delete schedule wallpost
                if (empty($get_schedule)) {
                    delete_post_meta($postid, $prefix . 'schedule_wallpost');
                    delete_post_meta($postid, $prefix . 'quick_schedule');
                } else {
                    update_post_meta($postid, $prefix . 'schedule_wallpost', $get_schedule);
                }
            }
        }
    }

    /**
     * Get quick share posts
     *
     * @package Social Auto Poster
     * @since 3.9.2
     */
    public function wpw_auto_poster_get_quick_share_posts($args = array())
    {

        // Get page
        $paged = isset($args['paged']) ? $args['paged'] : '1';
        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        // create posts args to get posts
        $postArgs = array(
            'post_type' => WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'any',
        );

        // Get quick posts
        $quickPosts = new WP_Query($postArgs);
        
        return $quickPosts;
    }


    public function wpw_auto_poster_quick_schedules($schedules)
    {
        
        if (! isset($schedules["wpw_quickshare_custom_schedule"])) {
            $schedules["wpw_quickshare_custom_schedule"] = array(
                'interval' => 1800,
                'display'  => esc_html__('Once every 30 minutes', 'wpwautoposter')
            );
        }

        return $schedules;
    }

    /**
     * Adding Hooks
     *
     * @package Social Auto Poster
     * @since 3.9.2
     */
    public function add_hooks()
    {
        add_action('admin_init', array($this, 'wpw_auto_poster_save_quick_share'));

        //add action to call schedule cron for send wall post
        add_action('wpw_auto_poster_scheduled_quick_share', array($this, 'wpw_auto_poster_scheduled_cron_quick_share'));
        

        add_filter('cron_schedules', array($this,'wpw_auto_poster_quick_schedules'));
    }
}
