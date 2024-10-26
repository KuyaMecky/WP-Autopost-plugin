<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Posting_AdminPages
{

    public $scripts, $model, $render, $message, $logs, $fbposting, $twposting, $liposting, $insposting, $admin, $pinposting, $ytposting, $gmbposting, $redditposting, $instaposting, $tbposting, $baposting, $wpposting, $teleposting, $mediumposting;

    public function __construct()
    {

        global $wpw_auto_poster_scripts, $wpw_auto_poster_model, $wpw_auto_poster_render, $wpw_auto_poster_message_stack,
        $wpw_auto_poster_fb_posting, $wpw_auto_poster_tw_posting, $wpw_auto_poster_li_posting, $wpw_auto_poster_ba_posting,
        $wpw_auto_poster_tb_posting, $wpw_auto_poster_ins_posting, $wpw_auto_poster_logs, $wpw_auto_poster_admin, $wpw_auto_poster_pin_posting, $wpw_auto_poster_yt_posting, $wpw_auto_poster_wp_posting,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings, $wpw_auto_poster_tele_posting,$wpw_auto_poster_medium_posting,$wpw_auto_poster_insta_posting;

        $this->scripts = $wpw_auto_poster_scripts;
        $this->model = $wpw_auto_poster_model;
        $this->render = $wpw_auto_poster_render;
        $this->message = $wpw_auto_poster_message_stack;
        $this->logs = $wpw_auto_poster_logs;
        $this->admin = $wpw_auto_poster_admin;

        //social posting class objects
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
     * Post to Social Medias
     *
     * Handles to post to social media
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_social_posting($post, $scheduled = false)
    {

        global $wpw_auto_poster_options, $postedstr, $schedulepoststr,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_medium_posting;

        $post_co = get_post_field('post_content', $post->ID);

        // get all supported network list array
        $all_social_networks = $this->model->wpw_auto_poster_get_social_type_name();

        $prefix = esc_attr(WPW_AUTO_POSTER_META_PREFIX);

        $postedstr = $schedulepoststr = array();

        $postid = $post->ID;

        $post_type = $post->post_type; // Post type
        // get selected categories ids for a post
        $post_catgeories = wpw_auto_poster_get_post_categories($post_type, $postid);

        /** Code to exclude posting for selected category start * */
        $main_exclude_arr = array(); // define main category exclude array for a post.
        // Initially set exclude flag to false at the begining
        $main_exclude_arr['fb'] = $main_exclude_arr['tw'] = $main_exclude_arr['li'] = $main_exclude_arr['tb'] = $main_exclude_arr['ba'] = $main_exclude_arr['ins'] = $main_exclude_arr['pin'] = $main_exclude_arr['yt'] = $main_exclude_arr['wp'] = $main_exclude_arr['gmb'] = $main_exclude_arr['reddit'] = $main_exclude_arr['tele'] = $main_exclude_arr['md'] = $main_exclude_arr['insta'] = false;


        // Loop all the supported social networks
        foreach ($all_social_networks as $slug => $label) {
            if (isset($wpw_auto_poster_options[$slug.'_posting_cats']) && $wpw_auto_poster_options[$slug.'_posting_cats'] == 'exclude') {
                // get selected categories to exclude for each social network
                $exclude_cats = !empty($wpw_auto_poster_options[$slug . '_exclude_cats']) ? $wpw_auto_poster_options[$slug . '_exclude_cats'] : array();

                // Loop through all the categories of a particualr post.
                foreach ($post_catgeories as $category) {
                    // Check if excluded category is selected for the current post type.
                    if (!empty($exclude_cats[$post_type])) {
                        // If atleast one excluded category matches with the post categories than make flag as true
                        if (in_array($category, $exclude_cats[$post_type])) {
                            // make social network exclude flag true, if atleast one excluded category matches
                            $main_exclude_arr[$slug] = true;
                            break;
                        }
                    }
                }
            } elseif (isset($wpw_auto_poster_options[$slug.'_posting_cats']) && $wpw_auto_poster_options[$slug.'_posting_cats'] == 'include') {
                // get selected categories to exclude for each social network
                $include_cats = !empty($wpw_auto_poster_options[$slug . '_exclude_cats']) ? $wpw_auto_poster_options[$slug . '_exclude_cats'] : array();
                $under_loop_break = array();

                // Loop through all the categories of a particualr post.
                foreach ($post_catgeories as $category) {
                    if (isset($under_loop_break[$slug]) && $under_loop_break[$slug] === false) {
                        break;
                    }

                    // Check if excluded category is selected for the current post type.
                    if (!empty($include_cats[$post_type])) {
                        // If atleast one excluded category matches with the post categories than make flag as true
                        if (in_array($category, $include_cats[$post_type])) {
                            // make social network exclude flag true, if atleast one excluded category matches
                            $main_exclude_arr[$slug] = false;
                            $under_loop_break[$slug] = false;
                            break;
                        } else {
                            $main_exclude_arr[$slug] = true;
                            $under_loop_break[$slug] = true;
                            continue;
                        }
                    }
                }
            }
        }

       
        /** Code to exclude posting for selected category end * */
        //Facebook Posting
        $facebookarr = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();

        //get post published on facebook
        $fb_published = get_post_meta($postid, $prefix . 'fb_published_on_fb', true);

        $schedule_post_to = get_post_meta($postid, $prefix . 'schedule_wallpost', true);
        $schedule_post_to = !empty($schedule_post_to) ? $schedule_post_to : array();

        $post_to_facebook = get_post_meta($postid, $prefix . 'post_to_facebook', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_facebook']) && (!isset($fb_published) || $fb_published == false ) && in_array($post->post_type, $facebookarr) ) || ( isset($_POST[$prefix . 'post_to_facebook']) && $_POST[$prefix . 'post_to_facebook'] == 'on' ) || ( $scheduled === true && $post_to_facebook == 'on' )) {
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_facebook'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['fb'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_facebook'])) { // Check schedule option is "Instantly"
                    //record logs for facebook posting
                    $this->logs->wpw_auto_poster_add('Facebook Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to user wall on facebook
                    $fb_result = $this->fbposting->wpw_auto_poster_fb_posting($post);
                    if ($fb_result) {
                        $postedstr[] = 'fb';
                    }
                }
                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('facebook', $schedule_post_to)) {
                        $schedule_post_to[] = 'facebook';
                    }
                    $schedulepoststr[] = 'fb';

                    //Update facebook status to scheduled
                    update_post_meta($postid, $prefix . 'fb_published_on_fb', 2);
                }
            }
        }

        //Twitter Posting
        $twitterarr = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();

        $tw_published = get_post_meta($postid, $prefix . 'tw_status', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_twitter']) && (!isset($tw_published) || $tw_published == false ) && in_array($post->post_type, $twitterarr) ) || ( isset($_POST[$prefix . 'post_to_twitter']) && $_POST[$prefix . 'post_to_twitter'] == 'on' )) {
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish  since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_twitter'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['tw'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_twitter'])) { // Check schedule option is "Instantly"
                    //record logs for twitter posting
                    $this->logs->wpw_auto_poster_add('Twitter Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to twitter
                    $tw_result = $this->twposting->wpw_auto_poster_tw_posting($post);
                    if ($tw_result) {
                        $postedstr[] = 'tw';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('twitter', $schedule_post_to)) {
                        $schedule_post_to[] = 'twitter';
                    }
                    $schedulepoststr[] = 'tw';
                    //Update twitter status to scheduled
                    update_post_meta($postid, $prefix . 'tw_status', 2);
                }
            }
        }

        //LinkedIn Posting
        $linkedinarr = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();

        $li_published = get_post_meta($postid, $prefix . 'li_status', true);


            //Check If post is already published and there is disable from metabox but it has checked in backend
            //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_linkedin']) && (!isset($li_published) || $li_published == false ) && in_array($post->post_type, $linkedinarr) ) || ( isset($_POST[$prefix . 'post_to_linkedin']) && $_POST[$prefix . 'post_to_linkedin'] == 'on' )) {
            $can_post = true;

                // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                    // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_linkedin'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['li'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_linkedin'])) { // Check schedule option is "Instantly"
                    //record logs for linkedin posting
                    $this->logs->wpw_auto_poster_add('LinkedIn Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to linkedin
                    $li_result = $this->liposting->wpw_auto_poster_li_posting($post);
                    if ($li_result) {
                        $postedstr[] = 'li';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('linkedin', $schedule_post_to)) {
                        $schedule_post_to[] = 'linkedin';
                    }
                    $schedulepoststr[] = 'li';
                        //Update linkedin status to scheduled
                    update_post_meta($postid, $prefix . 'li_status', 2);
                }
            }
        }


        //Tumblr Posting
        $tumblrarr = !empty($wpw_auto_poster_options['enable_tumblr_for']) ? $wpw_auto_poster_options['enable_tumblr_for'] : array();

        $tb_published = get_post_meta($postid, $prefix . 'tb_status', true);

            //Check If post is already published and there is disable from metabox but it has checked in backend
            //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_tumblr']) && (!isset($tb_published) || $tb_published == false ) && in_array($post->post_type, $tumblrarr) ) || ( isset($_POST[$prefix . 'post_to_tumblr']) && !empty($_POST[$prefix . 'post_to_tumblr']) )) {
            $can_post = true;

                // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                    // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_tumblr'])) {
                    $can_post = false;
                }
            }

        

            if (!$main_exclude_arr['tb'] && $can_post) {
                
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_tumblr'])) { // Check schedule option is "Instantly"
                    //record logs for Tumblr posting
                    $this->logs->wpw_auto_poster_add('Tumblr Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to tumblr
                    $tb_result = $this->tbposting->wpw_auto_poster_tb_posting($post);
                    if ($tb_result) {
                        $postedstr[] = 'tb';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('tumblr', $schedule_post_to)) {
                        $schedule_post_to[] = 'tumblr';
                    }
                    $schedulepoststr[] = 'tb';
                        //Update tumblr status to scheduled
                    update_post_meta($postid, $prefix . 'tb_status', 2);
                }
            }
        }

        // WordPress Posting
        $wordpressarr = !empty($wpw_auto_poster_options['enable_wordpress_for']) ? $wpw_auto_poster_options['enable_wordpress_for'] : array();

        $wp_published = get_post_meta($postid, $prefix . 'wp_status', true);

            //Check If post is already published and there is disable from metabox but it has checked in backend
            //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_wordpress']) && (!isset($wp_published) || $wp_published == false ) && in_array($post->post_type, $wordpressarr) ) || ( isset($_POST[$prefix . 'post_to_wordpress']) && $_POST[$prefix . 'post_to_wordpress'] == 'on' )) {
            $can_post = true;

                // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                    // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_wordpress'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['wp'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_wordpress'])) { // Check schedule option is "Instantly"
                    //record logs for linkedin posting
                    $this->logs->wpw_auto_poster_add('WordPress Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to wordpress
                    $wp_result = $this->wpposting->wpw_auto_poster_wp_posting($post);
                    if ($wp_result) {
                        $postedstr[] = 'wp';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('wordpress', $schedule_post_to)) {
                        $schedule_post_to[] = 'wordpress';
                    }
                    $schedulepoststr[] = 'wp';

                        //Update linkedin status to scheduled
                    update_post_meta($postid, $prefix . 'wp_status', 2);
                }
            }
        }

        //Telegram Posting
        $telegramarr = !empty($wpw_auto_poster_options['enable_telegram_for']) ? $wpw_auto_poster_options['enable_telegram_for'] : array();

        $tele_published = get_post_meta($postid, $prefix . 'tele_status', true);

            //Check If post is already published and there is disable from metabox but it has checked in backend
            //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_telegram']) && (!isset($tele_published) || $tele_published == false ) && in_array($post->post_type, $telegramarr) ) || ( isset($_POST[$prefix . 'post_to_telegram']) && $_POST[$prefix . 'post_to_telegram'] == 'on' )) {
            $can_post = true;

                // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                        // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_telegram'])) {
                    $can_post  = false;
                }
            }

            if (!$main_exclude_arr['tele'] && $can_post && !empty($this->teleposting)) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_telegram'])) { // Check schedule option is "Instantly"
                    //record logs for linkedin posting
                    $this->logs->wpw_auto_poster_add('Telegram Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to linkedin
                    $tele_result = $this->teleposting->wpw_auto_poster_tele_posting($post);
                    if ($tele_result) {
                        $postedstr[] = 'tele';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('telegram', $schedule_post_to)) {
                        $schedule_post_to[] = 'telegram';
                    }
                    $schedulepoststr[] = 'tele';
                        //Update linkedin status to scheduled
                    update_post_meta($postid, $prefix . 'tele_status', 2);
                }
            }
        }

        //bufferapp Posting
        $bufferapparr = !empty($wpw_auto_poster_options['enable_bufferapp_for']) ? $wpw_auto_poster_options['enable_bufferapp_for'] : array();

        $ba_published = get_post_meta($postid, $prefix . 'ba_status', true);

        if ((!empty($wpw_auto_poster_options['enable_bufferapp']) && (!isset($ba_published) || $ba_published == false ) && in_array($post->post_type, $bufferapparr) ) || ( isset($_POST[$prefix . 'post_to_bufferapp']) && !empty($_POST[$prefix . 'post_to_bufferapp']) )) { //if tumblr is seleectd then post to bufferapp account
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_bufferapp'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['ba'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_bufferapp'])) { // Check schedule option is "Instantly"
                    //record logs for BufferApp posting
                    $this->logs->wpw_auto_poster_add('BufferApp Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to bufferapp
                    $ba_result = $this->baposting->wpw_auto_poster_ba_posting($post);
                    if ($ba_result) {
                        $postedstr[] = 'ba';
                    }
                }
                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    $wpw_auto_poster_ba_user_id = get_transient('wpw_auto_poster_ba_user_id');
                    if (!empty($wpw_auto_poster_ba_user_id)) {
                        if (!in_array('bufferapp', $schedule_post_to)) {
                            $schedule_post_to[] = 'bufferapp';
                        }
                        $schedulepoststr[] = 'ba';
                        //Update bufferapp status to scheduled
                        update_post_meta($postid, $prefix . 'ba_status', 2);
                    }
                }
            }
        }


    //Youtube Posting
        $ytarr = !empty($wpw_auto_poster_options['enable_youtube_for']) ? $wpw_auto_poster_options['enable_youtube_for'] : array();
        $yt_published = get_post_meta($postid, $prefix . 'yt_published_on_yt', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_youtube']) && (!isset($yt_published) || $yt_published == false ) && in_array($post->post_type, $ytarr) ) || ( isset($_POST[$prefix . 'post_to_youtube']) && $_POST[$prefix . 'post_to_youtube'] == 'on' )) {
            $can_post = true;


            // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_youtube'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['yt'] && $can_post && !empty($this->ytposting)) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_youtube'])) { // Check schedule option is "Instantly"
                    $this->logs->wpw_auto_poster_add('Youtube Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to youtube
                    $yt_result = $this->ytposting->wpw_auto_poster_yt_posting($post);
                    if ($yt_result) {
                        $postedstr[] = 'yt';
                    }
                }
                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('youtube', $schedule_post_to)) {
                        $schedule_post_to[] = 'youtube';
                    }
                    $schedulepoststr[] = 'yt';
                    //Update youtube status to scheduled
                    update_post_meta($postid, $prefix . 'yt_published_on_yt', 2);
                }
            }
        }

        //Pinterest Posting
        $pinterestarr = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();

        //get post published on pinterest
        $pin_published = get_post_meta($postid, $prefix . 'pin_published_on_pin', true);

        $post_to_pinterest = get_post_meta($postid, $prefix . 'post_to_pinterest', true);

        //Check If post is already published and there is disable from metabox but it has checked in backend
        //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_pinterest']) && (!isset($pin_published) || $pin_published == false ) && in_array($post->post_type, $pinterestarr) ) || ( isset($_POST[$prefix . 'post_to_pinterest']) && $_POST[$prefix . 'post_to_pinterest'] == 'on' ) || ( $scheduled === true && $post_to_pinterest == 'on' )) {
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish  since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_pinterest'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['pin'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_pinterest'])) { // Check schedule option is "Instantly"
                    //record logs for pinterest posting
                    $this->logs->wpw_auto_poster_add('Pinterest Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to user wall on pinterest
                    $pin_result = $this->pinposting->wpw_auto_poster_pin_posting($post);
                    if ($pin_result) {
                        $postedstr[] = 'pin';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('pinterest', $schedule_post_to)) {
                        $schedule_post_to[] = 'pinterest';
                    }
                    $schedulepoststr[] = 'pin';
                    //Update pinterest status to scheduled
                    update_post_meta($postid, $prefix . 'pin_published_on_pin', 2);
                }
            }
        }

    //Google My Business Posting
        $gmbarr = !empty($wpw_auto_poster_options['enable_googlemybusiness_for']) ? $wpw_auto_poster_options['enable_googlemybusiness_for'] : array();

        $gmb_published = get_post_meta($postid, $prefix . 'gmb_published_on_posts', true);

        if ((!empty($wpw_auto_poster_options['enable_googlemybusiness']) && (!isset($gmb_published) || $gmb_published == false ) && in_array($post->post_type, $gmbarr) ) || ( isset($_POST[$prefix . 'post_to_gmb']) && $_POST[$prefix . 'post_to_gmb'] == 'on' )) {
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_gmb'])) {
                    $can_post = false;
                }
            }


            if (!$main_exclude_arr['gmb'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_gmb'])) { // Check schedule option is "Instantly"
                    //record logs for gmb posting
                    $this->logs->wpw_auto_poster_add('Google My Business Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to google my business
                    $gmb_result = $wpw_auto_poster_gmb_postings->wpw_auto_poster_gmb_posting($post);
                    if ($gmb_result) {
                        $postedstr[] = 'gmb';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('googlemybusiness', $schedule_post_to)) {
                        $schedule_post_to[] = 'googlemybusiness';
                    }
                    $schedulepoststr[] = 'gmb';
                    //Update gmb status to scheduled
                    update_post_meta($postid, $prefix . 'gmb_published_on_posts', 2);
                }
            }
        }

    //Reddit Posting Posting
        $redditarr = !empty($wpw_auto_poster_options['enable_reddit_for']) ? $wpw_auto_poster_options['enable_reddit_for'] : array();
        $reddit_published = get_post_meta($postid, $prefix . 'reddit_published_on_posts', true);
        if ((!empty($wpw_auto_poster_options['enable_reddit']) && (!isset($reddit_published) || $reddit_published == false ) && in_array($post->post_type, $redditarr) ) || ( isset($_POST[$prefix . 'post_to_reddit']) && $_POST[$prefix . 'post_to_reddit'] == 'on' )) {
            $can_post = true;

            //check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update

                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_reddit'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['reddit'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_reddit'])) { // Check schedule option is "Instantly"
                    //record logs for gmb posting
                    $this->logs->wpw_auto_poster_add('Reddit Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to reddit
                    $reddit_result = $wpw_auto_poster_reddit_postings->wpw_auto_poster_reddit_posting($post);
                    if ($reddit_result) {
                        $postedstr[] = 'reddit';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('reddit', $schedule_post_to)) {
                        $schedule_post_to[] = 'reddit';
                    }
                    $schedulepoststr[] = 'reddit';
                    //Update gmb status to scheduled
                    update_post_meta($postid, $prefix . 'reddit_published_on_posts', 2);
                }
            }
        }

    //Medium Posting Posting
        $mediumarr = !empty($wpw_auto_poster_options['enable_medium_for']) ? $wpw_auto_poster_options['enable_medium_for'] : array();
        $medium_published = get_post_meta($postid, $prefix . 'medium_published_on_posts', true);
        if ((!empty($wpw_auto_poster_options['enable_medium']) && (!isset($medium_published) || $medium_published == false ) && in_array($post->post_type, $mediumarr) ) || ( isset($_POST[$prefix . 'post_to_medium']) && $_POST[$prefix . 'post_to_medium'] == 'on' )) {
            $can_post = true;

            //check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_medium'])) {
                    $can_post = false;
                }
            }

            if (!$main_exclude_arr['md'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_medium'])) { // Check schedule option is "Instantly"
                    //record logs for gmb posting

                    $this->logs->wpw_auto_poster_add('Medium Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                    //post to medium
                    $medium_result = $wpw_auto_poster_medium_posting->wpw_auto_poster_medium_posting($post);
                    if ($medium_result) {
                        $postedstr[] = 'medium';
                    }
                }

                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('medium', $schedule_post_to)) {
                        $schedule_post_to[] = 'medium';
                    }
                    $schedulepoststr[] = 'medium';
                    //Update medium status to scheduled
                    update_post_meta($postid, $prefix . 'medium_published_on_posts', 2);
                }
            }
        }

    //Instagram Posting
        $instagramarr = !empty($wpw_auto_poster_options['enable_insta_for']) ? $wpw_auto_poster_options['enable_insta_for'] : array();

   
        $insta_published = get_post_meta($postid, $prefix . 'insta_published_on_insta', true);

        $post_to_instagram = get_post_meta($postid, $prefix . 'post_to_instagram', true);

    //Check If post is already published and there is disable from metabox but it has checked in backend
    //then it will post to social site when the post is going to published first time when created new
        if ((!empty($wpw_auto_poster_options['enable_insta']) && (!isset($insta_published) || $insta_published == false ) && in_array($post->post_type, $instagramarr) ) || ( isset($_POST[$prefix . 'post_to_instagram']) && $_POST[$prefix . 'post_to_instagram'] == 'on' ) || ( $scheduled === true && $post_to_instagram == 'on' )) {
            $can_post = true;

            // check if only new post publish option is ticked then old post will not publish since 2.8.6
            if (!empty($wpw_auto_poster_options['enable_posting_for_newpost']) && $wpw_auto_poster_options['enable_posting_for_newpost'] == 1) {
                // check is post publish first time or update
                if (!empty($_POST['original_post_status']) && $_POST['original_post_status'] == 'publish' && $post->post_status == 'publish' && !isset($_POST[$prefix . 'post_to_instagram'])) {
                    $can_post = false;
                }
            }

        

            if (!$main_exclude_arr['insta'] && $can_post) {
                if (empty($wpw_auto_poster_options['schedule_wallpost_option']) || isset($_POST[$prefix . 'immediate_post_to_instagram'])) { // Check schedule option is "Instantly"
                    //record logs for facebook posting
                    $this->logs->wpw_auto_poster_add('Instagram Instant Posting | ' . $post->post_type . ' | ' . $postid, true);

                
                    //post to user wall on facebook
                    $insta_result = $this->instaposting->wpw_auto_poster_insta_posting($post);
                
                    if ($insta_result) {
                        $postedstr[] = 'insta';
                    }
                }
                if (!empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
                    if (!in_array('instagram', $schedule_post_to)) {
                        $schedule_post_to[] = 'instagram';
                    }
                    $schedulepoststr[] = 'insta';

                    //Update instagram status to scheduled
                    update_post_meta($postid, $prefix . 'insta_published_on_insta', 2);
                }
            }
        }

        //update schedule wallpost
        if (!empty($schedule_post_to)) {
            update_post_meta($postid, $prefix . 'schedule_wallpost', $schedule_post_to);
        }
    }

    /**
     * Post to Social Medias
     *
     * Handles to post to social media
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_post_to_social_media($postid, $post)
    {

        global $wpw_auto_poster_options;

        /**
         * Issue with siteorigin plugin - Ticket #4606
         * Duplicate posting.
         */
        static $avoid_duplicate_post = 1;

        if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
            if ($avoid_duplicate_post > 1) {
                return $postid;
            }
        }

        //If post type is autopostlog then return auto posting
        if ($post->post_type == WPW_AUTO_POSTER_LOGS_POST_TYPE) {
            return $postid;
        }

        $post_type_object = get_post_type_object($post->post_type);

        if (( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) // Check Autosave
                || ( wpw_auto_poster_extra_security($postid, $post) == true ) // check extra securiry
                || ( $post->post_status != 'publish' && $post->post_status != 'future' ) // allow only publish and future post status
                || ( isset($_GET['_locale']) && count($_GET) == 1 )
            ) {
            return $postid;
        }


        // code to stop instant posting if wordpress post status is future
        if ($post->post_status == 'future' && $wpw_auto_poster_options['schedule_wallpost_option'] == "") {
            return $postid;
        }


        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        // Update Hour for Individual Post in Hourly Posting
        $wpw_auto_poster_select_hour = isset($_POST[$prefix . 'select_hour']) ? stripslashes_deep($_POST[$prefix . 'select_hour']) : '';
        $wpw_auto_poster_select_hour = (!empty($wpw_auto_poster_select_hour) ) ? strtotime($wpw_auto_poster_select_hour) : '';

        if (!empty($wpw_auto_poster_select_hour)) {
            update_post_meta($postid, $prefix . 'select_hour', $wpw_auto_poster_select_hour);
        } else {
            if (!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['schedule_wallpost_option'] == "hourly") {
                $next_scheduled_cron = wp_next_scheduled('wpw_auto_poster_scheduled_cron');
                update_post_meta($postid, $prefix . 'select_hour', $next_scheduled_cron);
            }
        }


        // apply filters for verify send wall posr after post create/update
        $has_send_wall_post = apply_filters('wpw_auto_poster_verify_send_wall_post', true, $post, $wpw_auto_poster_options);

        if ($has_send_wall_post) { // Verified for send wall post
            //posting to all social medias
            $this->wpw_auto_poster_social_posting($post);
        }

        if (empty($wpw_auto_poster_options['schedule_wallpost_option'])) {
            $avoid_duplicate_post++;
        }

        //redirect to custom url after saving post
        add_filter('redirect_post_location', array($this, 'wpw_auto_poster_redirect_save_post'));
    }

    /**
     * Add Schedule posting with social media
     *
     * Handles to work posting on social media when
     * someone set schedule for particular post
     * at that time it will automatic posted on social medias
     * whichever is selected in settings page
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_schedule_posting($postid)
    {

        global $wpw_auto_poster_options;

        $post = get_post($postid);

        if ($post->post_type == 'revision') {
            return; // Imp Line //  if revision dont do anything.
        }
        if ($post->post_status != 'publish') {
            return;
        }

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        // apply filters for verify send wall post after post create/update
        $has_send_wall_post = apply_filters('wpw_auto_poster_verify_send_wall_post', true, $post, $wpw_auto_poster_options);

        if ($has_send_wall_post) { // Verified for send wall post
            //posting to all social medias
            $this->wpw_auto_poster_social_posting($post, true);
        }
    }

    /**
     * Redirect After Save Post
     *
     * Handles to redirect after saving post
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_redirect_save_post($loc)
    {

        global $postedstr, $schedulepoststr;

        if (!empty($postedstr)) {
            return add_query_arg('wpwautoposteron', $postedstr, $loc);
        } elseif (!empty($schedulepoststr)) {
            return add_query_arg('wpwautoposterscheduleon', $schedulepoststr, $loc);
        } else {
            return $loc;
        }
    }

    /**
     * Admin Notices
     *
     * Handles to show admin notices after successfully
     * posted to social networks
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_admin_notices()
    {

        if (isset($_GET['wpwautoposteron']) || isset($_GET['wpwautoposterscheduleon'])) {
            $postedon = isset($_GET['wpwautoposteron']) ? stripslashes_deep($_GET['wpwautoposteron']) : '';
            $scheduledon = isset($_GET['wpwautoposterscheduleon']) ? stripslashes_deep($_GET['wpwautoposterscheduleon']) : '';

            $reparr = array('fb', 'tw', 'li', 'tb', 'yt', 'pin', 'gmb','reddit', 'tele', 'wp','medium','insta');

            $reparr = apply_filters('wpw_auto_poster_admin_notices_social_keys', $reparr);

            $replcarr = array(
                esc_html__('Facebook', 'wpwautoposter'),
                esc_html__('Twitter', 'wpwautoposter'),
                esc_html__('LinkedIn', 'wpwautoposter'),
                esc_html__('Tumblr', 'wpwautoposter'),
                esc_html__('Youtube', 'wpwautoposter'),
                esc_html__('Pinterest', 'wpwautoposter'),
                esc_html__('Google My Business', 'wpwautoposter'),
                esc_html__('Reddit', 'wpwautoposter'),
                esc_html__('Telegram', 'wpwautoposter'),
                esc_html__('WordPress', 'wpwautoposter'),
                esc_html__('Medium', 'wpwautoposter'),
                esc_html__('Instagram', 'wpwautoposter')
            );

            $replcarr = apply_filters('wpw_auto_poster_admin_notices_social_values', $replcarr);

            if (!empty($scheduledon)) {
                $scheduledon = str_replace($reparr, $replcarr, $scheduledon);
                $scheduledon = implode(',', $scheduledon);
                $msg = sprintf(esc_html__('Post scheduled with %1$s', 'wpwautoposter'), $scheduledon);
            } else {
                $postedon = str_replace($reparr, $replcarr, $postedon);
                $postedon = implode(',', $postedon);
                $msg = sprintf(esc_html__('Post published on %1$s', 'wpwautoposter'), $postedon);
            }

            echo "<div class='updated notice notice-success is-dismissible'><p>" . esc_html($msg) . "</p>
            <button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
        }

        // get all notices from transient
        $wpwautoposter_notices = get_transient('sap_notices');
        $all_notices = !empty($wpwautoposter_notices) ? $wpwautoposter_notices : array();

        // Display notices if there is any
        if (!empty($all_notices)) {
            foreach ($all_notices as $notice_type => $messages) {
                foreach ($messages as $message) {
                    echo "<div class='notice notice notice-$notice_type is-dismissible'>
                    <p>" . esc_html($message) . "</p>
                    <button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button>
                    </div>";
                }
            }
            delete_transient('sap_notices');
        }
    }

    /**
     * Bulk Delete
     *
     * Handles bulk delete functinalities of posted logs
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_posted_logs_bulk_delete()
    {

        if (( ( isset($_GET['action']) && $_GET['action'] == 'delete') || ( isset($_GET['action2']) && $_GET['action2'] == 'delete' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-posted-logs' && isset($_GET['logid']) && !empty($_GET['logid'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-posted-logs'), admin_url('admin.php'));

            //get bulk product array from $_GET
            $action_on_id = stripslashes_deep($_GET['logid']);

            if (count($action_on_id) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_id as $posted_log_id) {
                    //parameters for delete function
                    $args = array(
                        'log_id' => $posted_log_id
                    );

                    //call delete function from model class to delete records
                    $this->model->wpw_auto_poster_bulk_delete($args);
                }
                $redirect_url = add_query_arg(array('message' => '3'), $redirect_url);
            }

            //if bulk delete is performed successfully then redirect
            wp_redirect($redirect_url);
            exit;
        }


        if (( ( isset($_GET['action']) && $_GET['action'] == 'delete') || ( isset($_GET['action2']) && $_GET['action2'] == 'delete' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-quick-share' && isset($_GET['post_id']) && !empty($_GET['post_id'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-quick-share'), admin_url('admin.php'));

            //get bulk product array from $_GET
            $action_on_id = stripslashes_deep($_GET['post_id']);

            if (count($action_on_id) > 0) {
             //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_id as $posted_log_id) {
                        //parameters for delete function
                    $args = array(
                        'post_id' => $posted_log_id
                    );

                        //call delete function from model class to delete records
                    $this->model->wpw_auto_poster_quick_bulk_delete($args);
                }
                
                $redirect_url = add_query_arg(array('message' => '8'), $redirect_url);
            }

            //if bulk delete is performed successfully then redirect
            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Search2 Ajax for Taxonomies
     *
     * Functionalities
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */

    function wpw_auto_poster_taxonomy_ajax_search()
    {

        $post_type_categories    = wpw_auto_poster_get_all_categories_and_tags();
       
        $search_term             = !empty($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
        $filter_data['results']  = array();
        $all_data_count = 0;
      
        if (!empty($post_type_categories) && is_array($post_type_categories) && !empty($search_term)) {
            foreach ($post_type_categories as $key => $categories_data) {
                $child_count      = 0;
                $child_term_array = array();
                if (!empty($categories_data['categories']) && is_array($categories_data['categories'])) {
                    foreach ($categories_data['categories'] as $key_child => $child_cat_data) {
                        $term_data      = explode(":", $child_cat_data);
                        $available_term = $term_data[1];
                        
                        if (stripos($available_term, $search_term) !== false) {
                            $child_term_array[$child_count] = array(
                                'id'   => $key."|".$key_child,
                                'text' => $child_cat_data
                            );
                            $child_count++;
                        }
                    }
                }
                
                if (!empty($child_term_array) && is_array($child_term_array)) {
                    $filter_data['results'][$all_data_count] = array(
                        'text'     => $categories_data['label'],
                        'children' => $child_term_array
                       
                    );
                    $all_data_count++;
                }
            }
        }
        echo json_encode($filter_data);
        die();
    }

    /**
     * Quick Bulk Delete
     *
     * Handles bulk delete functinalities of posted logs
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_quick_delete_multiple()
    {

        // Verify nonce
        if ( !isset( $_POST['wpw_admin_setting_nonce'] ) || !wp_verify_nonce( $_POST['wpw_admin_setting_nonce'], 'wpw_auto_poster_verify_admin_setting_nonce' ) ) {
            die();
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                die();
            }
        }

        if (( ( isset($_POST['action_remove']) && $_POST['action_remove'] == 'delete') ) && isset($_POST['page']) && $_POST['page'] == 'wpw-auto-poster-posted-logs' && isset($_POST['id']) && !empty($_POST['id'])) {
         //check action and page and also logid
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-quick-share'), admin_url('admin.php'));

            //get bulk product array from $_GET
            $action_on_id = stripslashes_deep($_POST['id']);


            if (count($action_on_id) > 0) {
             //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_id as $posted_log_id) {
                        //parameters for delete function
                    $args = array(
                        'post_id' => $posted_log_id
                    );
                     
                        //call delete function from model class to delete records
                    $this->model->wpw_auto_poster_quick_bulk_delete($args);
                }
                
                $redirect_url = add_query_arg(array('message' => '8'), $redirect_url);
            }

            //if bulk delete is performed successfully then redirect
            echo '1';
            die();
        }
    }

    /**
     * Bulk Scheduling
     *
     * Handles bulk scheduling functinalities of manage schedule
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    function wpw_auto_poster_scheduling_bulk_process()
    {

        global $wpw_auto_poster_options;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //Get admin url
        $admin_url = admin_url('admin.php');

        //Get all supported social network
        $all_social_networks = $this->model->wpw_auto_poster_get_social_type_name();

        //Get selected tab
        $selected_tab = !empty($_GET['tab']) ? stripslashes_deep($_GET['tab']) : 'facebook';

        //Get social network slug
        $social_network = ucfirst($selected_tab);
        $social_slug = array_search($social_network, $all_social_networks);

        //Get social meta key
        $status_meta_key = $this->model->wpw_auto_poster_get_social_status_meta_key($selected_tab);

        //Code for Scheduling posts
        if (( ( isset($_GET['action']) && $_GET['action'] == 'schedule') || ( isset($_GET['action2']) && $_GET['action2'] == 'schedule' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-manage-schedules' && isset($_GET['schedule']) && !empty($_GET['schedule'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-manage-schedules', 'tab' => $selected_tab), $admin_url);

            //get bulk posts array from $_GET
            $action_on_ids = stripslashes_deep($_GET['schedule']);

            // Update Hour for Individual Post in Hourly Posting
            if (isset($_GET['select_hour'])) {
                $wpw_select_hour = stripslashes_deep($_GET['select_hour']);
            } elseif (isset($_GET['bulk_select_hour'])) {
                $wpw_select_hour = stripslashes_deep($_GET['bulk_select_hour']);
            }


            $wpw_select_hour = (!empty($wpw_select_hour) ) ? strtotime($wpw_select_hour) : '';

            if (count($action_on_ids) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_ids as $post_id) {
                    $main_exclude_arr[$social_slug] = false;

                    // Add network to scheduled schedule wall post
                    $schedules = get_post_meta($post_id, $prefix . 'schedule_wallpost', true);

                    $post_type = get_post_type($post_id); // get post type
                    $post_catgeories = wpw_auto_poster_get_post_categories($post_type, $post_id); // get post categories
                    // get excluded catgeories for the selected tab
                    $exclude_cats = !empty($wpw_auto_poster_options[$social_slug . '_exclude_cats']) ? $wpw_auto_poster_options[$social_slug . '_exclude_cats'] : array();

                    if (!empty($post_catgeories)) {
                        // Loop through all the categories of a particualr post.
                        foreach ($post_catgeories as $category) {
                            // Check if excluded category is selected for the current post type.
                            if (!empty($exclude_cats[$post_type])) {
                                // If atleast one excluded category matches with the post categories than make flag as true
                                if (in_array($category, $exclude_cats[$post_type])) {
                                    // make social network exclude flag true, if atleast one excluded category matches
                                    $main_exclude_arr[$social_slug] = true;
                                    continue;
                                }
                            }
                        }
                    }

                    $schedules = !empty($schedules) ? $schedules : array();
                    $schedules[] = $selected_tab;

                    // check if selected social tab has any excluded categories selected
                    if (!$main_exclude_arr[$social_slug]) {
                        update_post_meta($post_id, $prefix . 'schedule_wallpost', array_unique($schedules));

                        //Update scheduled meta
                        update_post_meta($post_id, $status_meta_key, 2);

                        //Update select hour meta
                        update_post_meta($post_id, $prefix . 'select_hour', $wpw_select_hour);
                    }
                }

                if (!$main_exclude_arr[$social_slug]) {
                    $redirect_url = add_query_arg(array('message' => '1'), $redirect_url);
                }
            }

            //if there is no checboxes are checked then redirect to listing page
            wp_redirect($redirect_url);
            exit;
        }

        //Code for Unscheduling posts
        if (( ( isset($_GET['action']) && $_GET['action'] == 'unschedule') || ( isset($_GET['action2']) && $_GET['action2'] == 'unschedule' ) ) && isset($_GET['page']) && $_GET['page'] == 'wpw-auto-poster-manage-schedules' && isset($_GET['schedule']) && !empty($_GET['schedule'])) { //check action and page and also logid
            // get redirect url
            $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-manage-schedules', 'tab' => $selected_tab), $admin_url);

            //get bulk posts array from $_GET
            $action_on_ids = stripslashes_deep($_GET['schedule']);

            if (count($action_on_ids) > 0) { //check there is some checkboxes are checked or not
                //if there is multiple checkboxes are checked then call delete in loop
                foreach ($action_on_ids as $post_id) {
                    // Add network to scheduled schedule wall post
                    $schedules = get_post_meta($post_id, $prefix . 'schedule_wallpost', true);
                    if (!empty($schedules)) {
                        if (($key = array_search($selected_tab, $schedules)) !== false) {
                            unset($schedules[$key]);
                        }
                        if (!empty($schedules)) {
                            update_post_meta($post_id, $prefix . 'schedule_wallpost', $schedules);
                        } else { // remove post meta if no social media for schedule
                            delete_post_meta($post_id, $prefix . 'schedule_wallpost');
                        }
                    } else { // remove post meta if no social media for schedule
                        delete_post_meta($post_id, $prefix . 'schedule_wallpost');
                    }

                    //Remove status meta
                    delete_post_meta($post_id, $status_meta_key);
                }

                $redirect_url = add_query_arg(array('message' => '2'), $redirect_url);
            }
            //if there is no checboxes are checked then redirect to listing page
            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Validate Setting
     *
     * Handles to add validate schedule settings
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_validate_setting($new_data, $old_data)
    {
        if ((!empty($new_data['schedule_wallpost_option']) && $new_data['schedule_wallpost_option'] != $old_data['schedule_wallpost_option'] ) || ( $new_data['schedule_wallpost_option'] == 'wpw_custom_mins' && !empty($new_data['schedule_wallpost_custom_minute']) && $new_data['schedule_wallpost_custom_minute'] != $old_data['schedule_wallpost_custom_minute'] ) || ( $new_data['schedule_wallpost_option'] == 'twicedaily' && ( $new_data['enable_twice_random_posting'] != $old_data['enable_twice_random_posting'] || ( $new_data['schedule_wallpost_twice_time1'] != $old_data['schedule_wallpost_twice_time1'] || $new_data['schedule_wallpost_twice_time2'] != $old_data['schedule_wallpost_twice_time2'] ) ) ) || ( $new_data['schedule_wallpost_option'] == 'daily' && ( $new_data['schedule_wallpost_option'] == $old_data['schedule_wallpost_option'] ) )) { // Check Schedule WallPost is not "Instance"
            // first clear the schedule
            wp_clear_scheduled_hook('wpw_auto_poster_scheduled_cron');

            if (!wp_next_scheduled('wpw_auto_poster_scheduled_cron')) {
                $utc_timestamp = time(); //

                $local_time = current_time('timestamp'); // to get current local time

                if ($new_data['schedule_wallpost_option'] == 'daily' && isset($new_data['schedule_wallpost_time']) && isset($new_data['schedule_wallpost_minute'])) {
                    // Schedule other CRON events starting at user defined hour and periodically thereafter
                    $schedule_time = mktime($new_data['schedule_wallpost_time'], $new_data['schedule_wallpost_minute'], 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time - $local_time );
                    $utc_timestamp = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp, 'daily', 'wpw_auto_poster_scheduled_cron');
                } elseif ($new_data['schedule_wallpost_option'] == 'twicedaily' && empty($new_data['enable_twice_random_posting'])) {                 // Added since version 2.5.1
                    $utc_timestamp = time();

                    // Schedule other CRON events starting at user defined hour and periodically thereafter
                    $schedule_time1 = mktime($new_data['schedule_wallpost_twice_time1'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time1 - $local_time );
                    $utc_timestamp1 = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp1, 'daily', 'wpw_auto_poster_scheduled_cron');

                    $schedule_time2 = mktime($new_data['schedule_wallpost_twice_time2'], 0, 0, date('m', $local_time), date('d', $local_time), date('Y', $local_time));

                    // get difference
                    $diff = ( $schedule_time2 - $local_time );
                    $utc_timestamp2 = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp2, 'daily', 'wpw_auto_poster_scheduled_cron');
                } elseif ($new_data['schedule_wallpost_option'] == 'hourly') {                 // Added since version 2.0.0
            // logic to get hours rounded, if current time is 3:15 am it will return 4 am.
            // return value in seconds
                    $new_time = ceil($local_time / 3600) * 3600;

            // get difference between 3:15 and 4 so it will become 45 min (2700 seconds)
                    $diff = ( $new_time - $local_time );

            // add 2700 seconds so cron will start runnig from 4 am.
                    $utc_timestamp = $utc_timestamp + $diff;

                    wp_schedule_event($utc_timestamp, $new_data['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');
                } else {
                    $scheds = (array) wp_get_schedules();
                    $current_schedule = $new_data['schedule_wallpost_option'];
                    $interval = ( isset($scheds[$current_schedule]['interval']) ) ? (int) $scheds[$current_schedule]['interval'] : 0;

                    $utc_timestamp = $utc_timestamp + $interval;

                    wp_schedule_event($utc_timestamp, $new_data['schedule_wallpost_option'], 'wpw_auto_poster_scheduled_cron');
                }
            }
        }


        if ($new_data['facebook_auth_options'] == 'appmethod' && $old_data['facebook_auth_options'] != 'appmethod') {
            update_option('wpw_auto_poster_fb_sess_data', array());
        }

        if ($new_data['linkedin_auth_options'] == 'appmethod' && $old_data['linkedin_auth_options'] != 'appmethod') {
            update_option('wpw_auto_poster_li_sess_data', array());
        }

        if ($new_data['linkedin_auth_options'] == 'graph' && $old_data['linkedin_auth_options'] == 'appmethod') {
            update_option('wpw_auto_poster_li_sess_data', array());
        }
        

        if (isset($new_data['schedule_wallpost_custom_minute']) && $new_data['schedule_wallpost_custom_minute'] < 30) {
            $new_data['schedule_wallpost_custom_minute'] = 30;
        }

        if (isset($new_data['daily_posts_limit']) && $new_data['daily_posts_limit'] > 10) {
            $new_data['daily_posts_limit'] = 10;
        }

        if (isset($new_data['daily_posts_limit']) && empty($new_data['daily_posts_limit'])) {
            $new_data['daily_posts_limit'] = 1;
        }

        return $new_data;
    }

    /**
     * Validate Setting
     *
     * Handles to set schedule based on settings
     *
     * @package Social Auto Poster
     * @since 2.6.9
     */
    public function wpw_auto_poster_reposter_validate_setting($new_data, $old_data)
    {
        if (( isset($new_data['schedule_wallpost_option']) && isset($old_data['schedule_wallpost_option']) && is_array($new_data['schedule_wallpost_option']) && is_array($old_data['schedule_wallpost_option']))) { // Check Schedule WallPost is not "Instance"
            // first clear the schedule
            $schedule = $new_data['schedule_wallpost_option'];
            $old_schedule = $old_data['schedule_wallpost_option'];

            if ($schedule['days'] != $old_schedule['days'] || $schedule['hours'] != $old_schedule['hours'] || $schedule['minutes'] != $old_schedule['minutes']) {
                wp_clear_scheduled_hook('wpw_auto_poster_reposter_scheduled_cron');

                if (!wp_next_scheduled('wpw_auto_poster_reposter_scheduled_cron')) {
                    $utc_timestamp = time(); //

                    $local_time = current_time('timestamp'); // to get current local time

                    $scheds = (array) wp_get_schedules();

                    $interval = ( isset($scheds['wpw_reposter_custom_schedule']['interval']) ) ? (int) $scheds['wpw_reposter_custom_schedule']['interval'] : 0;

                    $utc_timestamp = $utc_timestamp + $interval;

                    wp_schedule_event($utc_timestamp, 'wpw_reposter_custom_schedule', 'wpw_auto_poster_reposter_scheduled_cron');
                }
            }
        }

        if ( ( ( isset( $new_data['schedule_wallpost_option']['days'] ) && empty( $new_data['schedule_wallpost_option']['days'] ) ) && ( isset( $new_data['schedule_wallpost_option']['hours'] ) && empty( $new_data['schedule_wallpost_option']['hours'] ) ) ) && ( isset( $new_data['schedule_wallpost_option']['minutes'] ) && $new_data['schedule_wallpost_option']['minutes'] < 30 ) ) {
            $new_data['schedule_wallpost_option']['minutes'] = 30;
        }

        $social_accounts = $this->model->wpw_auto_poster_get_social_type_name();

        foreach ($social_accounts as $social_slug => $name) {
            if (isset($new_data[$social_slug . '_posts_limit']) && $new_data[$social_slug . '_posts_limit'] != '' && $new_data[$social_slug . '_posts_limit'] > 10) {
                $new_data[$social_slug . '_posts_limit'] = 10;
            }

            if (isset($new_data[$social_slug . '_posts_limit']) && empty($new_data[$social_slug . '_posts_limit'])) {
                $new_data[$social_slug . '_posts_limit'] = 1;
            }
        }

        return $new_data;
    }

    /**
     * Add Custom Schedule
     *
     * Handle to add custom schedule
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_add_custom_scheduled($schedules)
    {
        global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;

        // custom minutes value from input box
        $schedule_wallpost_custom_minute = (!empty($wpw_auto_poster_options['schedule_wallpost_custom_minute']) ) ? $wpw_auto_poster_options['schedule_wallpost_custom_minute'] : WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE;


        $schedule_reposter_schedule = (!empty($wpw_auto_poster_reposter_options['schedule_wallpost_option']) ) ? $wpw_auto_poster_reposter_options['schedule_wallpost_option'] : '';

        // custom scheduler value from reposter schedule input box
        // check on update options
        if (isset($_POST['wpw_auto_poster_reposter_options']['schedule_wallpost_option']) && !empty($_POST['wpw_auto_poster_reposter_options']['schedule_wallpost_option'])) {
            $schedule_reposter_schedule = stripslashes_deep($_POST['wpw_auto_poster_reposter_options']['schedule_wallpost_option']);
        }

        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => esc_html__('Once Weekly', 'wpwautoposter')
        );

        // check on update options
        if (isset($_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute']) && !empty($_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute'])) {
            $schedule_wallpost_custom_minute = stripslashes_deep($_POST['wpw_auto_poster_options']['schedule_wallpost_custom_minute']);
        }

        // code to set custom mins given to the input box for schedule cron
        $schedules["wpw_custom_mins"] = array(
            'interval' => $schedule_wallpost_custom_minute * 60,
            'display' => esc_html__($schedule_wallpost_custom_minute . ' minutes', 'wpwautoposter'));

        // code to set custom mins given to the input box for schedule cron since 2.6.6
        if (!empty($schedule_reposter_schedule)) {
            $days = $schedule_reposter_schedule['days'];
            $hours = $schedule_reposter_schedule['hours'];
            $minutes = $schedule_reposter_schedule['minutes'];
            $schedule_name = "Every ";
            if (!empty($days)) {
                $schedule_name .= $days . esc_html__(" days", 'wpwautoposter');
            }
            if (!empty($hours)) {
                $schedule_name .= ' ' . $hours . esc_html__(" Hours", 'wpwautoposter');
            }
            if (!empty($minutes)) {
                $schedule_name .= ' ' . $minutes . esc_html__(" Minutes", 'wpwautoposter');
            }

            $days = (!empty($days) ) ? $days * 86400 : 0; // days to sec
            $hours = (!empty($hours) ) ? $hours * 3600 : 0; // hours to sec
            $minutes = (!empty($minutes) ) ? $minutes * 60 : 0; // minutes to sec

            $total_seconds = $days + $hours + $minutes; // total in seconds

            $schedules["wpw_reposter_custom_schedule"] = array(
                'interval' => $total_seconds,
                'display' => $schedule_name
            );
        }

        return $schedules;
    }

    /**
     * Cron Job For Send WallPost to Followers
     *
     * Handle to call schedule cron for
     * send wallpost to followers
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_scheduled_cron()
    {
        global $wpw_auto_poster_options,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_medium_posting;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $current_day = current_time('w'); // get current day of week
        // get days which are excluded for posting
        $excld_selected_days = (!empty($wpw_auto_poster_options['schedule_excl_posting_days']) ) ? $wpw_auto_poster_options['schedule_excl_posting_days'] : array();

        $schedule_option = $wpw_auto_poster_options['schedule_wallpost_option'];

        // exclude specific day for schedule posting
        if (!empty($excld_selected_days) && in_array($current_day, $excld_selected_days) && $schedule_option != 'weekly') {
            return false;
        }

        // Get all post data which have send wall post
        $posts_data = $this->model->wpw_auto_poster_get_schedule_post_data();

        if (!empty($posts_data)) { // Check post data are not empty
            foreach ($posts_data as $post_data) {
                $postid = $post_data->ID;

                //get schedule wallpost
                $get_schedule = get_post_meta($postid, $prefix . 'schedule_wallpost', true);
                $this->logs->wpw_auto_poster_add('Start schedule Posting', true);

                if (!empty($get_schedule)) {
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

                    if (in_array('bufferapp', $get_schedule)) { // Check bufferapp
                        $this->logs->wpw_auto_poster_add('BufferApp Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to bufferapp
                        $res = $this->baposting->wpw_auto_poster_ba_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('bufferapp', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }

                    if (in_array('instagram', $get_schedule) && !empty($this->instaposting)) { // Check instagram
                        $this->logs->wpw_auto_poster_add('Instagram Schedule Posting | ' . $post_data->post_type . ' | ' . $postid, true);

                        //post to user timeline on instagram
                        $res = $this->instaposting->wpw_auto_poster_insta_posting($post_data);

                        // check if published post successfully
                        if ($res) {
                            $key = array_search('instagram', $get_schedule);
                            unset($get_schedule[$key]);
                        }
                    }

                    if (in_array('instagram', $get_schedule) && empty($this->insposting)) { // Check instagram
                        $key = array_search('instagram', $get_schedule);
                        unset($get_schedule[$key]);
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

                    if (in_array('youtube', $get_schedule) && empty($this->ytposting)) { // Check youtube
                        $key = array_search('youtube', $get_schedule);
                        unset($get_schedule[$key]);
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

                    if (in_array('telegram', $get_schedule) && !empty($this->teleposting)) { // Check google my business
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
                } else {
                    update_post_meta($postid, $prefix . 'schedule_wallpost', $get_schedule);
                }
            }
        }
    }

    /**
     * Cron Job For Send WallPost to social account with Reposter options
     *
     * Handle to call schedule cron for
     * send wallpost to social accounts for reposter option
     *
     * @package Social Auto Poster
     * @since 2.6.9
     */
    public function wpw_auto_poster_reposter_scheduled_cron()
    {

        global $wpw_auto_poster_reposter_options, $wpw_auto_poster_logs,$wpw_auto_poster_gmb_postings,$wpw_auto_poster_reddit_postings,$wpw_auto_poster_medium_posting,$wpw_auto_poster_insta_posting;

        $reposter_options = get_option('wpw_auto_poster_reposter_options');

        $current_day = current_time('w'); // get current day of week
        // get days which are excluded for posting
        $excld_selected_days = (!empty($reposter_options['schedule_excl_posting_days']) ) ? $reposter_options['schedule_excl_posting_days'] : array();

        // exclude specific day for reposter
        if (!empty($excld_selected_days) && in_array($current_day, $excld_selected_days)) {
            return false;
        }

        $wpw_posting_repeat = ( empty($wpw_auto_poster_reposter_options['schedule_wallpost_repeat']) || $wpw_auto_poster_reposter_options['schedule_wallpost_repeat'] == 'no' ) ? false : true;

        $repeat_limit = ( empty($reposter_options['reposter_repeat_times']) ) ? '' : $reposter_options['reposter_repeat_times'];

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $all_social_networks = $this->model->wpw_auto_poster_get_social_type_data();

        // Loop all the supported social networks
        foreach ($all_social_networks as $slug => $label) {

            if( $label == 'instagram' ) {
                $label = $slug;   
            }

            // skip if reposter is not enabled for social media
            if (!isset($wpw_auto_poster_reposter_options['enable_' . $label]) || empty($wpw_auto_poster_reposter_options['enable_' . $label])) {
                continue;
            }


            $posting_for = array();

            if (!empty($wpw_auto_poster_reposter_options['enable_' . $label . '_for']) && !empty($wpw_auto_poster_reposter_options['enable_' . $label])) {
                $posting_for = $wpw_auto_poster_reposter_options['enable_' . $label . '_for'];
            }

            // skip if no post type is selected for auto posting
            if (empty($posting_for)) {
                continue;
            }

            $unique_posting = (!empty($wpw_auto_poster_reposter_options['unique_posting']) && $wpw_auto_poster_reposter_options['unique_posting'] == 1 ) ? true : false;

            // get selected categories to exclude for each social network
            $exclude_cats = !empty($wpw_auto_poster_reposter_options[$slug . '_post_type_cats']) ? $wpw_auto_poster_reposter_options[$slug . '_post_type_cats'] : array();

            // exclude or include selected category?
            $post_type_cats = !empty($wpw_auto_poster_reposter_options[$slug . '_posting_cats']) ? $wpw_auto_poster_reposter_options[$slug . '_posting_cats'] : 'include';

            // limit per schedule
            $post_limit = !empty($wpw_auto_poster_reposter_options[$slug . '_posts_limit']) ? $wpw_auto_poster_reposter_options[$slug . '_posts_limit'] : '1';

            // Get all post data which have send wall post
            $posts_data = $this->model->wpw_auto_poster_reposter_get_schedule_post_data($posting_for, $exclude_cats, $post_type_cats, $unique_posting, $post_limit, $slug, $label);

            // repeat reposter if no posts found for posting and repeat loop true
            if (empty($posts_data) && $wpw_posting_repeat == true) {
                update_option('sap_reposter_' . $slug . '_last_posted_page', 1);

                // Get all post data which have send wall post
                $posts_data = $this->model->wpw_auto_poster_reposter_get_schedule_post_data($posting_for, $exclude_cats, $post_type_cats, $unique_posting, $post_limit, $slug, $label);
            }


            if (!empty($posts_data)) { // Check post data are not empty
                foreach ($posts_data as $post_data) {
                    $postid = $post_data->ID;
                    $post_type = $post_data->post_type; // Post type
                    // add log
                    $wpw_auto_poster_logs->wpw_auto_poster_add('Start Reposter for :' . $label, true);

                    //post to user wall on facebook
                    if ($slug == 'fb') {
                        $res = $this->fbposting->wpw_auto_poster_fb_posting($post_data, 'reposter');
                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);
                            if (!empty($repeat_limit)) { // update repeated time in meta
                                $fb_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $fb_repeated_time = ( empty($fb_repeated_time) ) ? 0 : $fb_repeated_time;
                                $fb_repeated_time = $fb_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $fb_repeated_time);
                            }
                        }
                    } elseif ($slug == 'tw') { //post to twitter
                        $res = $this->twposting->wpw_auto_poster_tw_posting($post_data, 'reposter');
                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);
                            if (!empty($repeat_limit)) {
                                $tw_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $tw_repeated_time = ( empty($tw_repeated_time) ) ? 0 : $tw_repeated_time;
                                $tw_repeated_time = $tw_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $tw_repeated_time);
                            }
                        }
                    } elseif ($slug == 'li') { //post to linkedin
                        $res = $this->liposting->wpw_auto_poster_li_posting($post_data, 'reposter');

                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);
                            if (!empty($repeat_limit)) {
                                $li_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $li_repeated_time = ( empty($li_repeated_time) ) ? 0 : $li_repeated_time;
                                $li_repeated_time = $li_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $li_repeated_time);
                            }
                        }
                    } elseif ($slug == 'tb') { //post to tumblr
                        $res = $this->tbposting->wpw_auto_poster_tb_posting($post_data, 'reposter');
                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);
                            if (!empty($repeat_limit)) {
                                $tb_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $tb_repeated_time = ( empty($tb_repeated_time) ) ? 0 : $tb_repeated_time;
                                $tb_repeated_time = $tb_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $tb_repeated_time);
                            }
                        }
                    } elseif ($slug == 'ba') { //post to bufferapp
                        $res = $this->baposting->wpw_auto_poster_ba_posting($post_data, 'reposter');
                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);

                            if (!empty($repeat_limit)) {
                                $ba_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $ba_repeated_time = ( empty($ba_repeated_time) ) ? 0 : $ba_repeated_time;
                                $ba_repeated_time = $ba_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $ba_repeated_time);
                            }
                        }
                    } /*elseif ($slug == 'ins' && !empty($this->insposting)) { //post to user timeline on instagram
                        $res = $this->insposting->wpw_auto_poster_insta_posting($post_data, 'reposter');
                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);

                            if (!empty($repeat_limit)) {

                                $ins_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $ins_repeated_time = ( empty($ins_repeated_time) ) ? 0 : $ins_repeated_time;
                                $ins_repeated_time = $ins_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $ins_repeated_time);
                            }
                        }
                    }*/ elseif ($slug == 'yt' && !empty($this->ytposting)) { //post to user timeline on youtube
                        $res = $this->ytposting->wpw_auto_poster_yt_posting($post_data, 'reposter');

                        // check if published post successfully
                        if ($res) {
                            update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);

                            if (!empty($repeat_limit)) {
                                $yt_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

                                $yt_repeated_time = ( empty($yt_repeated_time) ) ? 0 : $yt_repeated_time;
                                $yt_repeated_time = $yt_repeated_time + 1;

                                update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $yt_repeated_time);
                            }
                        }
} elseif ($slug == 'pin') { //post to user board/pins on pinterest
    $res = $this->pinposting->wpw_auto_poster_pin_posting($post_data, 'reposter');
    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);

        if (!empty($repeat_limit)) {
            $pin_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

            $pin_repeated_time = ( empty($pin_repeated_time) ) ? 0 : $pin_repeated_time;
            $pin_repeated_time = $pin_repeated_time + 1;

            update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $pin_repeated_time);
        }
    }
} elseif ($slug == 'wp') { //post to wordpress
    $res = $this->wpposting->wpw_auto_poster_wp_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix . $slug . '_reposter_publish', 1);
        if (!empty($repeat_limit)) {
            $wp_repeated_time = get_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', true);

            $wp_repeated_time = ( empty($wp_repeated_time) ) ? 0 : $wp_repeated_time;
            $wp_repeated_time = $wp_repeated_time + 1;

            update_post_meta($postid, $prefix . $slug . '_reposter_repeated_time', $wp_repeated_time);
        }
    }
} elseif ($slug == 'gmb') { //post to google my business
    $res = $wpw_auto_poster_gmb_postings->wpw_auto_poster_gmb_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix.$slug.'_reposter_publish', 1);
        if (!empty($repeat_limit)) {
            $gmb_repeated_time = get_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', true);

            $gmb_repeated_time = ( empty($gmb_repeated_time) ) ? 0 : $gmb_repeated_time;
            $gmb_repeated_time = $gmb_repeated_time + 1;

            update_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', $gmb_repeated_time);
        }
    }
} elseif ($slug == 'reddit') { //post to reddit
    $res = $wpw_auto_poster_reddit_postings->wpw_auto_poster_reddit_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix.$slug.'_reposter_publish', 1);
        if (!empty($repeat_limit)) {
            $reddit_repeated_time = get_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', true);

            $reddit_repeated_time = ( empty($reddit_repeated_time) ) ? 0 : $reddit_repeated_time;
            $reddit_repeated_time = $reddit_repeated_time + 1;

            update_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', $reddit_repeated_time);
        }
    }
} elseif ($slug == 'tele' && !empty($this->teleposting)) { //post to telegram
    $res = $this->teleposting->wpw_auto_poster_tele_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix.$slug.'_reposter_publish', 1);
        if (!empty($repeat_limit)) {
            $tele_repeated_time = get_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', true);

            $tele_repeated_time = ( empty($tele_repeated_time) ) ? 0 : $tele_repeated_time;
            $tele_repeated_time = $tele_repeated_time + 1;

            update_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', $tele_repeated_time);
        }
    }
} elseif ($slug == 'md') { //post to medium
    $res = $wpw_auto_poster_medium_posting->wpw_auto_poster_medium_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix.$slug.'_reposter_publish', 1);
        $tele_repeated_time = '';
        if (!empty($repeat_limit)) {
            $medium_repeated_time = get_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', true);

            $medium_repeated_time = ( empty($tele_repeated_time) ) ? 0 : $tele_repeated_time;
            $medium_repeated_time = $medium_repeated_time + 1;

            update_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', $tele_repeated_time);
        }
    }
} elseif ($slug == 'insta') { //post to medium
    $res = $wpw_auto_poster_insta_posting->wpw_auto_poster_insta_posting($post_data, 'reposter');

    // check if published post successfully
    if ($res) {
        update_post_meta($postid, $prefix.$slug.'_reposter_publish', 1);
        if (!empty($repeat_limit)) {
            $insta_repeated_time = get_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', true);
            $insta_repeated_time = ( empty($insta_repeated_time) ) ? 0 : $insta_repeated_time;
            $insta_repeated_time = $insta_repeated_time + 1;
            update_post_meta($postid, $prefix .$slug.'_reposter_repeated_time', $insta_repeated_time);
        }
    }
}


                    $wpw_auto_poster_logs->wpw_auto_poster_add('End Reposter');
                }
            }
        }

        exit;
    }

    /**
     * Manage WPML compability
     * Remove status of posting on social data
     *
     * so, when user update data,
     * it's going for post data on socials
     *
     * @package Social Auto Poster
     * @since 1.8.3
     */
    public function wpw_auto_poster_wpml_dup_remove_status_meta($master_post_id, $lang, $post_array, $id)
    {

        if (!empty($id)) {
            global $wpw_auto_poster_options;

            $post_type = isset($post_array['post_type']) ? $post_array['post_type'] : '';

            $fb_enable_post_type = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();
            $tw_enable_post_type = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();
            $li_enable_post_type = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();
            $tb_enable_post_type = !empty($wpw_auto_poster_options['enable_tumblr_for']) ? $wpw_auto_poster_options['enable_tumblr_for'] : array();
            $ba_enable_post_type = !empty($wpw_auto_poster_options['enable_bufferapp_for']) ? $wpw_auto_poster_options['enable_bufferapp_for'] : array();
            $ins_enable_post_type = !empty($wpw_auto_poster_options['enable_instagram_for']) ? $wpw_auto_poster_options['enable_instagram_for'] : array();
            $pin_enable_post_type = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();
            $yt_enable_post_type = !empty($wpw_auto_poster_options['enable_youtube_for']) ? $wpw_auto_poster_options['enable_youtube_for'] : array();
            $gmb_enable_post_type = !empty($wpw_auto_poster_options['enable_googlemybusiness_for']) ? $wpw_auto_poster_options['enable_googlemybusiness_for'] : array();
            $tele_enable_post_type = !empty($wpw_auto_poster_options['enable_telegram_for']) ? $wpw_auto_poster_options['enable_telegram_for'] : array();

            if (in_array($post_type, $fb_enable_post_type)) {
                update_post_meta($id, '_wpweb_fb_published_on_fb', false);
            }

            if (in_array($post_type, $tw_enable_post_type)) {
                update_post_meta($id, '_wpweb_tw_status', false);
            }

            if (in_array($post_type, $li_enable_post_type)) {
                update_post_meta($id, '_wpweb_li_status', false);
            }

            if (in_array($post_type, $tb_enable_post_type)) {
                update_post_meta($id, '_wpweb_tb_status', false);
            }

            if (in_array($post_type, $ba_enable_post_type)) {
                update_post_meta($id, '_wpweb_ba_status', false);
            }

            if (in_array($post_type, $ins_enable_post_type)) {
                update_post_meta($id, '_wpweb_insta_published_on_insta', false);
            }

            if (in_array($post_type, $pin_enable_post_type)) {
                update_post_meta($id, '_wpweb_pin_published_on_pin', false);
            }

            if (in_array($post_type, $yt_enable_post_type)) {
                update_post_meta($id, '_wpweb_yt_published_on_yt', false);
            }

            if (in_array($post_type, $gmb_enable_post_type)) {
                update_post_meta($id, '_wpweb_gmb_published_on_posts', false);
            }

            if (in_array($post_type, $tele_enable_post_type)) {
                update_post_meta($id, '_wpweb_tele_status', false);
            }
        }

        return;
    }

    /**
     * Select Hour for Individual Post When Globally Hourly Posting Selected
     *
     * Handle to add meta in publish box
     *
     * @package Social Auto Poster
     * @since 1.8.4
     */
    public function wpw_auto_poster_publish_meta()
    {

        global $post;

        $args = array('public' => true);
        $post_types = get_post_types($args);

        $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

        if ($wpw_auto_poster_options['schedule_wallpost_option'] == 'hourly' && in_array($post->post_type, $post_types)) {
            $prefix = WPW_AUTO_POSTER_META_PREFIX;

            // wordpress date format
            $date_format = apply_filters('wpw_auto_poster_display_date_format', 'Y-m-d');

            $wpw_auto_poster_select_hour = get_post_meta($post->ID, $prefix . 'select_hour', true);

            if (!empty($wpw_auto_poster_select_hour) && strlen($wpw_auto_poster_select_hour) <= 2) {
                $time = $wpw_auto_poster_select_hour;
                $wpw_auto_poster_select_hour = date($date_format, current_time('timestamp'));
                $wpw_auto_poster_select_hour = $wpw_auto_poster_select_hour . ' ' . $time . ':00';
                $wpw_auto_poster_select_hour = date($date_format . ' H:i', strtotime($wpw_auto_poster_select_hour));
            } elseif (!empty($wpw_auto_poster_select_hour)) {
                $wpw_auto_poster_select_hour = date($date_format . ' ' . 'H:i', $wpw_auto_poster_select_hour);

                // Convert old date to timestamp
                $poster_select_hour_timestamp = strtotime($wpw_auto_poster_select_hour);

                // Get a date using the timestamp formats.
                $current_timestamp = current_time( 'timestamp' );
                
                // Check if old time is less from current then add next cron time
                if( $poster_select_hour_timestamp <  $current_timestamp ) {
                    
                    $next_cron = wp_next_scheduled('wpw_auto_poster_scheduled_cron');
                    $wpw_auto_poster_select_hour = get_date_from_gmt(date('Y-m-d H:i:s', $next_cron), 'Y-m-d H:i');
                }

            } else {
                $next_cron = wp_next_scheduled('wpw_auto_poster_scheduled_cron');
                $wpw_auto_poster_select_hour = get_date_from_gmt(date('Y-m-d H:i:s', $next_cron), 'Y-m-d H:i');
            }
            ?>

            <div class="misc-pub-section misc-pub-schedule-date">
                <label for="<?php echo $prefix . 'select_hour'; ?>"><span class="wpw-auto-poster-schedule-icon"><img src="<?php print esc_url(WPW_AUTO_POSTER_IMG_URL) . '/calendar.png'; ?>"></span>
                    <span class="wpw-auto-poster-schedule-label">
                        <?php esc_html_e('Schedule: ', 'wpwautoposter'); ?>
                    </span>
                </label>
                <span class="wpw-auto-poster-schedule-label">
                    <input type="text" name="<?php echo $prefix . 'select_hour'; ?>" id="<?php echo $prefix . 'select_hour'; ?>" class="wpw-auto-poster-schedule-date" value="<?php print esc_attr($wpw_auto_poster_select_hour); ?>">
                    <span class="clear-date" title="<?php esc_html_e('Clear date', 'wpwautoposter'); ?>">X</span>
                </span>
                </div><?php
        }
    }

        /**
         * Add custom metabox for schedule when hourly option is selected
         *
         * This metabox only added when gutenber is enabled
         *
         * @package Social Auto Poster
         * @since 2.9.3
         */
    public function wpw_auto_poster_schedule_meta_boxex($post_type, $post)
    {

        if (function_exists('get_current_screen')) {
            $current_screen = get_current_screen();
            $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

            if (isset($current_screen->is_block_editor) && $current_screen->is_block_editor == '1' && isset($wpw_auto_poster_options['schedule_wallpost_option']) && $wpw_auto_poster_options['schedule_wallpost_option'] == 'hourly') {
                $pages = array('post', 'page');

                // Loop through array
                foreach ($pages as $page) {
                    //add metabox
                    add_meta_box(
                        'wpw_auto_poster_schedule_meta',
                        esc_html__('Social Auto Poster - Schedule'),
                        array($this, 'wpw_auto_poster_publish_meta'),
                        $page,
                        'side',
                        'high'
                    );
                }
            }
        }
    }

        /**
         * Add FB account list field to add or edit category form
         *
         * Handle to display FB account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_fb_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-fb-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Twitter account list field to add or edit category form
         *
         * Handle to display Twitter account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_tw_acc_fields()
    {
        print '<table class="form-table form-field">';
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-tw-fields.php');
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Linkdin account list field to add or edit category form
         *
         * Handle to display Linkdin account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_li_acc_fields()
    {
        print '<table class="form-table form-field">';
        // Linkdin account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-li-fields.php');
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Instagram account list field to add or edit category form
         *
         * Handle to display Instagram account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_ins_acc_fields()
    {
        do_action('wpw_auto_poster_category_ins_form');
    }

        /**
         * Add Pinterest account list field to add or edit category form
         *
         * Handle to display Pinterest account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_pin_acc_fields()
    {
        print '<table class="form-table form-field">';
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-pin-fields.php');
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Tumblr account list field to add or edit category form
         *
         * Handle to display Tumblr account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_tb_acc_fields()
    {
        print '<table class="form-table form-field">';
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-tb-fields.php');
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add GMB account list field to add or edit category form
         *
         * Handle to display GMB account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_yt_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-yt-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add GMB account list field to add or edit category form
         *
         * Handle to display GMB account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_gmb_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-gmb-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Reddit account list field to add or edit category form
         *
         * Handle to display Reddit account list to add category form
         *
         * @package Social Auto Poster
         * @since 3.5.2
         */
    function wpw_auto_poster_add_category_reddit_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-reddit-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Telegram account list field to add or edit category form
         *
         * Handle to display Telegram account list to add category form
         *
         * @package Social Auto Poster
         * @since 3.7.0
         */
    public function wpw_auto_poster_add_category_tele_acc_fields()
    {
        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-tele-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Medium account list field to add or edit category form
         *
         * Handle to display Medium account list to add category form
         *
         * @package Social Auto Poster
         * @since 3.7.0
        */
    public function wpw_auto_poster_add_category_medium_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-md-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add Insta account list field to add or edit category form
         *
         * Handle to display Insta account list to add category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_add_category_insta_acc_fields()
    {

        print '<table class="form-table form-field">';
        // FB account list
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-insta-fields.php');

        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

        /**
         * Add hook to category add edit form
         *
         * Handle to display social account list to add and edit category form
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_hook_taxonomy()
    {

        global $wpw_auto_poster_options;

        $fb_selected_post = !empty($wpw_auto_poster_options['enable_facebook_for']) ? $wpw_auto_poster_options['enable_facebook_for'] : array();
        $tw_selected_post = !empty($wpw_auto_poster_options['enable_twitter_for']) ? $wpw_auto_poster_options['enable_twitter_for'] : array();
        $li_selected_post = !empty($wpw_auto_poster_options['enable_linkedin_for']) ? $wpw_auto_poster_options['enable_linkedin_for'] : array();
        $ins_selected_post = !empty($wpw_auto_poster_options['enable_instagram_for']) ? $wpw_auto_poster_options['enable_instagram_for'] : array();
        $pin_selected_post = !empty($wpw_auto_poster_options['enable_pinterest_for']) ? $wpw_auto_poster_options['enable_pinterest_for'] : array();
        $tb_selected_post = !empty($wpw_auto_poster_options['enable_tumblr_for']) ? $wpw_auto_poster_options['enable_tumblr_for'] : array();
        $yt_selected_post = !empty($wpw_auto_poster_options['enable_youtube_for']) ? $wpw_auto_poster_options['enable_youtube_for'] : array();
        $gmb_selected_post = !empty($wpw_auto_poster_options['enable_googlemybusiness_for']) ? $wpw_auto_poster_options['enable_googlemybusiness_for'] : array();
        $reddit_selected_post = !empty($wpw_auto_poster_options['enable_reddit_for']) ? $wpw_auto_poster_options['enable_reddit_for'] : array();
        $tele_selected_post = !empty($wpw_auto_poster_options['enable_telegram_for']) ? $wpw_auto_poster_options['enable_telegram_for'] : array();
        $medium_selected_post = !empty($wpw_auto_poster_options['enable_medium_for']) ? $wpw_auto_poster_options['enable_medium_for'] : array();
        $insta_selected_post = !empty($wpw_auto_poster_options['enable_insta_for']) ? $wpw_auto_poster_options['enable_insta_for'] : array();


        $fb_exclude_cats = !empty($wpw_auto_poster_options['fb_exclude_cats']) ? $wpw_auto_poster_options['fb_exclude_cats'] : array();
        $tw_exclude_cats = !empty($wpw_auto_poster_options['tw_exclude_cats']) ? $wpw_auto_poster_options['tw_exclude_cats'] : array();
        $li_exclude_cats = !empty($wpw_auto_poster_options['li_exclude_cats']) ? $wpw_auto_poster_options['li_exclude_cats'] : array();
        $ins_exclude_cats = !empty($wpw_auto_poster_options['ins_exclude_cats']) ? $wpw_auto_poster_options['ins_exclude_cats'] : array();
        $pin_exclude_cats = !empty($wpw_auto_poster_options['pin_exclude_cats']) ? $wpw_auto_poster_options['pin_exclude_cats'] : array();
        $tb_exclude_cats = !empty($wpw_auto_poster_options['tb_exclude_cats']) ? $wpw_auto_poster_options['tb_exclude_cats'] : array();
        $yt_exclude_cats = !empty($wpw_auto_poster_options['yt_exclude_cats']) ? $wpw_auto_poster_options['yt_exclude_cats'] : array();
        $gmb_exclude_cats = !empty($wpw_auto_poster_options['gmb_exclude_cats']) ? $wpw_auto_poster_options['gmb_exclude_cats'] : array();
        $reddit_exclude_cats = !empty($wpw_auto_poster_options['reddit_exclude_cats']) ? $wpw_auto_poster_options['reddit_exclude_cats'] : array();
        $tele_exclude_cats = !empty($wpw_auto_poster_options['tele_exclude_cats']) ? $wpw_auto_poster_options['tele_exclude_cats'] : array();
        $medium_exclude_cats = !empty($wpw_auto_poster_options['medium_exclude_cats']) ? $wpw_auto_poster_options['medium_exclude_cats'] : array();
        $insta_exclude_cats = !empty($wpw_auto_poster_options['insta_exclude_cats']) ? $wpw_auto_poster_options['insta_exclude_cats'] : array();
            
        $cat_id = "";

        if (!empty($_GET['tag_ID'])) {
            $cat_id = stripslashes_deep($_GET['tag_ID']);
            $taxonomy = stripslashes_deep($_GET['taxonomy']);

            $term = get_term_by('id', $cat_id, $taxonomy, ARRAY_A);
            $cat_slug = $term['slug'];
        }

        // code to add category hook to each post types
        $all_post_types = get_post_types(array('public' => true), 'objects');
        $all_post_types = is_array($all_post_types) ? $all_post_types : array();

        if (!empty($all_post_types)) {
            foreach ($all_post_types as $type) {
                $tax_obj = get_taxonomies(array('object_type' => array($type->name)), 'objects');

                // FB account list field to only selcted post types
                if (in_array($type->name, $fb_selected_post)) {
                    // add or edit category form hook for FB acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_fb_acc_fields'));

                        $edit_display = true;

                        if (!empty($cat_id)) {
                            // check if the category excluded for facebook
                            if (!empty($fb_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $fb_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display facebook edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_fb_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Twitter account list field to only selcted post types
                if (in_array($type->name, $tw_selected_post)) {
                    // add or edit category form hook for TW acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_tw_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for Twitter
                            if (!empty($tw_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $tw_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display facebook edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_tw_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Linkdin account list field to only selcted post types
                if (in_array($type->name, $li_selected_post)) {
                    // add or edit category form hook for Linkedin acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_li_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for Linkedin
                            if (!empty($li_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $li_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display Linkedin edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_li_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Tumblr account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $tb_selected_post)) {
                    // add or edit category form hook for Tumblr acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_tb_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for pinterest
                            if (!empty($tb_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $tb_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display facebook edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_tb_acc_fields'), 999);
                            }
                        }
                    }
                }

                // YouTube account list field to only selcted post types added since 3.5.1
                if (in_array($type->name, $yt_selected_post)) {
                    // add or edit category form hook for YT acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_yt_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for YouTube
                            if (!empty($yt_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $yt_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_yt_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Pinterest account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $pin_selected_post)) {
                    // add or edit category form hook for Pinterest acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_pin_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for pinterest
                            if (!empty($pin_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $pin_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display facebook edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_pin_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Instagram account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $ins_selected_post)) {
                    // add or edit category form hook for Instagram acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_ins_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for instagram
                            if (!empty($ins_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $ins_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display instagram edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_ins_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Google My Business account list field to only selcted post types added since 2.6.0
                if (in_array($type->name, $gmb_selected_post)) {
                    // add or edit category form hook for GMB acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_gmb_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for google my business
                            if (!empty($gmb_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $gmb_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_gmb_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Reddit account list field to only selcted post types added since 3.5.2
                if (in_array($type->name, $reddit_selected_post)) {
                    // add or edit category form hook for GMB acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_reddit_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for google my business
                            if (!empty($reddit_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $reddit_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_reddit_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Telegram account list field to only selcted post types added since 3.7.0
                if (in_array($type->name, $tele_selected_post)) {
                    // add or edit category form hook for Tele acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_tele_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for google my business
                            if (!empty($tele_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $tele_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_tele_acc_fields'), 999);
                            }
                        }
                    }
                }

                //Medium account list field to only selcted post types added since 3.7.0
                if (in_array($type->name, $medium_selected_post)) {
                    // add or edit category form hook for Tele acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_medium_acc_fields'));

                        $edit_display = true;
                        if (!empty($cat_id)) {
                            // check if the category excluded for google my business
                            if (!empty($medium_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $medium_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_medium_acc_fields'), 999);
                            }
                        }
                    }
                }

                // Instagram account list field to only selcted post types
                if (in_array($type->name, $insta_selected_post)) {
                    // add or edit category form hook for Instagram acct list
                    foreach ($tax_obj as $key => $value) {
                        // Add social account list fields to each category add form
                        add_action($key . '_add_form_fields', array($this, 'wpw_auto_poster_add_category_insta_acc_fields'));

                        $edit_display = true;

                        if (!empty($cat_id)) {
                            // check if the category excluded for instagram
                            if (!empty($insta_exclude_cats[$type->name])) {
                                if (in_array($cat_slug, $insta_exclude_cats[$type->name])) {
                                    $edit_display = false;
                                }
                            }

                            // display instagram edit category account selection if not exclude
                            if ($edit_display) {
                                // Add social account list fields to each category edit form
                                add_action($key . '_edit_form_fields', array($this, 'wpw_auto_poster_add_category_insta_acc_fields'), 999);
                            }
                        }
                    }
                }
            }
        }
    }

        /**
         * Save posting to social account for each category
         *
         * Handle to save social account for category
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    function wpw_auto_poster_category_fields_save($term_id, $tt_id, $taxonomy)
    {

        if (!isset($_POST['wpw_auto_category_posting'])) {
            return false;
        }

        $old_cat_posting_acct = get_option('wpw_auto_poster_category_posting_acct');

        $selected_social_accounts = isset($_POST['wpw_auto_category_poster_options']) ? stripslashes_deep($_POST['wpw_auto_category_poster_options']) : array();

        // clear old social account for term id
        if (!empty($term_id) && isset($old_cat_posting_acct[$term_id])) {
            unset($old_cat_posting_acct[$term_id]);
        }

        if (!empty($term_id) && !empty($selected_social_accounts)) {
            foreach ($selected_social_accounts as $social_acc_name => $social_acc_ids) {
                // update option for each account
                if (!empty($social_acc_ids)) {
                    $old_cat_posting_acct[$term_id][$social_acc_name] = $social_acc_ids;
                }
            }
        }

        update_option('wpw_auto_poster_category_posting_acct', $old_cat_posting_acct);
    }

        /**
         * Function to post wordpress pretty url if settings selected
         *
         * @package Social Auto Poster
         * @since 1.5.6
         */
    public function wpw_auto_poster_is_wp_pretty_url($link, $postid, $socialtype)
    {

        global $wpw_auto_poster_options;

        $is_pretty = (!empty($wpw_auto_poster_options[$socialtype . '_wp_pretty_url']) ) ? $wpw_auto_poster_options[$socialtype . '_wp_pretty_url'] : '';

        if ($is_pretty == 'yes') {
            $link = get_permalink($postid);
        }

        return $link;
    }

        /**
         * Handles to fetch categories from post type
         *
         * @package Social Auto Poster
         * @since 2.6.0
         */
    public function wpw_auto_poster_get_category()
    {

        // If $_POST for post type value is not empty
        if (!empty($_POST['post_type_val'])) {
            // Get all taxonomies defined for that post type
            $all_taxonomies = get_object_taxonomies(stripslashes_deep($_POST['post_type_val']), 'objects');

            // Loop on all taxonomies
            foreach ($all_taxonomies as $taxonomy) {

                /**
                 * If taxonomy is object and it is hierarchical, than it is our category
                 * NOTE: If taxonomy is not hierarchical than it is tag and we should not consider this
                 * And we will only consider first category found in our taxonomy list
                 */
                if (is_object($taxonomy) && !empty($taxonomy->hierarchical)) {
                    $categories = get_terms($taxonomy->name, array('hide_empty' => false)); // Get categories for taxonomy
                    // Start creating html from categories
                    $html = '<option value="">' . esc_html__('Select Category', 'wpwautoposter') . '</option>';
                    foreach ($categories as $category) {
                        $html .= '<option value="' . esc_attr($category->term_id) . '"';
                        // If category is already selected and current id is same as the selected one
                        if (!empty($_POST['sel_category_id']) && $_POST['sel_category_id'] == $category->term_id) {
                            $html .= " selected='selected'";
                        }
                        $html .= '>' . esc_html($category->name) . '</option>';
                    }

                    // Echo html
                    echo $html;
                    exit;
                }
            }
        }
    }

        /**
         * Fetch taxonomies from custom post type
         *
         * @package Social Auto Poster
         * @since 2.3.1
         */
    public function wpw_auto_poster_get_taxonomies()
    {

        global $wpw_auto_poster_options;

        $social_prefix = isset($_POST['social_type']) ? stripslashes_deep($_POST['social_type']) : '';

        $static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

        $post_type_tags = array();
        $post_type_cats = array();
        $selected = $cathtml = $taghtml = '';

        /*                     * *** Custom post type TAG taxonomy code ***** */
        // Check if any taxonomy tag is selected or not
        if (!empty($_POST['selected_tags'])) {
            $pre_selected_tags = stripslashes_deep($_POST['selected_tags']);

            foreach ($pre_selected_tags as $pre_selected_tag) {
                $tagData = explode("|", $pre_selected_tag);
                $post_type = $tagData[0];
                $post_tag = $tagData[1];
                $selected_tags[$post_type][] = $post_tag;
            }

            $post_type_tags = $selected_tags;
        }

        /*                     * *** Custom post type CATEGORY taxonomy code ***** */
        // Check if any taxonomy category is selected or not
        if (!empty($_POST['selected_cats'])) {
            $pre_selected_cats = stripslashes_deep($_POST['selected_cats']);

            foreach ($pre_selected_cats as $pre_selected_cat) {
                $tagData = explode("|", $pre_selected_cat);
                $post_type = $tagData[0];
                $post_cat = $tagData[1];
                $selected_cats[$post_type][] = $post_cat;
            }

            $post_type_cats = $selected_cats;
        }

        // If $_POST for post type value is not empty
        if (!empty($_POST['post_type_val'])) {
            foreach ($_POST['post_type_val'] as $post_type) {
                $html_tag = $html_cat = '';
                // Get all taxonomies defined for that post type
                $all_taxonomies = get_object_taxonomies($post_type, 'objects');

                // Loop on all taxonomies
                foreach ($all_taxonomies as $taxonomy) {
                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {
                        $selected = "";

                        if (isset($post_type_cats[$post_type]) && !empty($post_type_cats[$post_type])) {
                            $selected = ( in_array($taxonomy->name, $post_type_cats[$post_type]) ) ? 'selected="selected"' : '';
                        }

                        $html_cat .= '<option value="' . esc_attr($post_type) . "|" . esc_attr($taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy->label) . '</option>';
                    } elseif (is_object($taxonomy) && $taxonomy->hierarchical != 1) {
                        if (!empty($static_post_type_arr[$post_type]) && $static_post_type_arr[$post_type] != $taxonomy->name) {
                            continue;
                        }
                        $selected = "";

                        if (isset($post_type_tags[$post_type]) && !empty($post_type_tags[$post_type])) {
                            $selected = ( in_array($taxonomy->name, $post_type_tags[$post_type]) ) ? 'selected="selected"' : '';
                        }
                        $html_tag .= '<option value="' . esc_attr($post_type) . "|" . esc_attr($taxonomy->name) . '" ' . esc_attr($selected) . '>' . esc_html($taxonomy->label) . '</option>';
                    }
                }

                if (isset($html_cat) && !empty($html_cat)) {
                    $cathtml .= '<optgroup label=' . ucfirst(esc_attr($post_type)) . '>' . $html_cat . '</optgroup>';
                }
                if (isset($html_tag) && !empty($html_tag)) {
                    $taghtml .= '<optgroup label=' . ucfirst(esc_attr($post_type)) . '>' . $html_tag . '</optgroup>';
                }

                // Unset all values
                unset($html_cat);
                unset($html_tag);

                $response['data'] = array('categories' => $cathtml, 'tags' => $taghtml);
            }
            echo json_encode($response);
            unset($response['data']);
            exit;
        }
    }

        /**
         * Handles to logs report graph process
         *
         * @package Social Auto Poster
         * @since 2.6.0
         */
    public function wpw_auto_poster_logs_graph_process()
    {

        $prepare = $final_array = array();

        // Verify nonce
        if ( !isset( $_POST['wpw_setting_nonce'] ) || !wp_verify_nonce( $_POST['wpw_setting_nonce'], 'wpw_auto_poster_setting_nonce' ) ) {
            $result['error'] = 'Invalid nonce';
			$result['success']	= '0';
			echo json_encode( $result );
			exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $result['error'] = 'Invalid nonce';
                $result['success']	= '0';
                echo json_encode( $result );
                exit;
            }
        }

        $social_types_list = $this->model->wpw_auto_poster_get_social_type_name();
        if (!empty($_REQUEST['social_type'])) {
            $final_array[] = array(esc_html__('Month', 'wpwautoposter'), $social_types_list[$_REQUEST['social_type']]);
        } else {
            $final_array[] = array(
                esc_html__('Month', 'wpwautoposter'),
                esc_html__('Facebook', 'wpwautoposter'),
                esc_html__('Twitter', 'wpwautoposter'),
                esc_html__('LinkedIn', 'wpwautoposter'),
                esc_html__('Tumblr', 'wpwautoposter'),
                esc_html__('YouTube', 'wpwautoposter'),
                esc_html__('Pinterest', 'wpwautoposter'),
                esc_html__('Google My Business', 'wpwautoposter'),
                esc_html__('Reddit', 'wpwautoposter'),
                esc_html__('Telegram', 'wpwautoposter'),
                esc_html__('Medium', 'wpwautoposter'),
                esc_html__('WordPress', 'wpwautoposter'),
                esc_html__('Instagram', 'wpwautoposter'),
            );

            $final_array = apply_filters('wpw_auto_poster_handle_posting_reports', $final_array);
        }


        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //Default Argument
        $args = array(
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
            'wpw_auto_poster_list' => true
        );

        //searched by social type
        if (!empty($_REQUEST['social_type'])) {
            $args['meta_query'] = array(
                array(
                    'key' => $prefix . 'social_type',
                    'value' => $_REQUEST['social_type'],
                )
            );
        }

        if (!empty($_REQUEST['filter_type']) && $_REQUEST['filter_type'] == 'custom') {
            //Check Start date and set it in query
            if (!empty($_REQUEST['start_date'])) {
                $args['date_query'][]['after'] = date('Y-m-d', strtotime('-1 day', strtotime($_REQUEST['start_date'])));
            }

            //Check End date and set it in query
            if (!empty($_REQUEST['end_date'])) {
                $args['date_query'][]['before'] = date('Y-m-d', strtotime('+1 day', strtotime($_REQUEST['end_date'])));
            }

            //Check Start date and End date if empty then month set
            if (empty($_REQUEST['start_date']) && empty($_REQUEST['end_date'])) {
                $args['m'] = date('Ym');
            }
        } elseif (!empty($_REQUEST['filter_type']) && $_REQUEST['filter_type'] == 'current_year') {
            //Set Current year
            $args['date_query'][]['year'] = date('Y');
        } elseif (!empty($_REQUEST['filter_type']) && $_REQUEST['filter_type'] == 'last_7days') {
            //Set Current Week
            $args['date_query'][]['year'] = date('Y');
            $args['date_query'][]['week'] = date('W');
        } else {
            //Default set current month
            $args['m'] = date('Ym');
        }

        //Get result based on argument
        $results = $this->model->wpw_auto_poster_get_posting_logs_data($args);

        //Check data exist
        if (!empty($results['data'])) {
            //modify data
            foreach ($results['data'] as $key => $value) {
                $post_id = $value['ID'];
                $post_date = date('d-M-Y', strtotime($value['post_date']));
                $social_type = get_post_meta($post_id, $prefix . 'social_type', true);

                //Check post network type
                if (!empty($prepare[$post_date][$social_type])) {
                    $prepare[$post_date][$social_type] = $prepare[$post_date][$social_type] + 1;
                } else {
                    $prepare[$post_date][$social_type] = 1;
                }
            }

            //Finalize prepared data
            foreach ($prepare as $key => $value) {
                $facebook   = !empty($value['fb']) ? $value['fb'] : 0;
                $twitter    = !empty($value['tw']) ? $value['tw'] : 0;
                $linkedin   = !empty($value['li']) ? $value['li'] : 0;
                $tumbler    = !empty($value['tb']) ? $value['tb'] : 0;
                //$instagram    = !empty($value['ins']) ? $value['ins'] : 0;
                $youtube    = !empty($value['yt']) ? $value['yt'] : 0;
                $pinterest  = !empty($value['pin']) ? $value['pin'] : 0;
                $googlemybusiness = !empty($value['gmb']) ? $value['gmb'] : 0;
                $reddit     = !empty($value['reddit']) ? $value['reddit'] : 0;
                $telegram   = !empty($value['tele']) ? $value['tele'] : 0;
                $medium = !empty($value['medium']) ? $value['medium'] : 0;
                $wordpress = !empty($value['wp']) ? $value['wp'] : 0;
                $instagram = !empty($value['insta']) ? $value['insta'] : 0;



                if (!empty($_REQUEST['social_type'])) {
                    $final_array[] = array($key, $value[$_REQUEST['social_type']]);
                } else {
                    $networks_report = apply_filters('wpw_auto_poster_social_reports_values', array($key, $facebook, $twitter, $linkedin, $tumbler, $youtube, $pinterest, $googlemybusiness, $reddit, $telegram,$medium,$wordpress,$instagram), $value);

                    $final_array[] = $networks_report;
                }
            }
        } else {
            if (!empty($_REQUEST['social_type'])) {
                $final_array[] = array( date('d-M-Y'), 0 );
            } else {
                if (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) {
                    $final_array[] = apply_filters('wpw_auto_poster_report_default_option_values', array(date('d-M-Y', strtotime($_REQUEST['start_date'])),0,0,0,0,0,0,0,0,0));
                }

                if (isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])) {
                    $final_array[] = apply_filters('wpw_auto_poster_report_default_option_values', array(date('d-M-Y', strtotime($_REQUEST['end_date'])),0,0,0,0,0,0,0,0,0));
                }

                if (empty($_REQUEST['start_date']) && empty($_REQUEST['end_date'])) {
                    $final_array[] = apply_filters('wpw_auto_poster_report_default_option_values', array(date('d-M-Y'),0,0,0,0,0,0,0,0,0,0));
                }
            }
        }

        echo json_encode($final_array);
        exit();
    }


    /**
     * Cron function for clear log
     *
     * Handle to clear system log file when exectuting the cron
     *
     * @package Social Auto Poster
     * @since 2.7.9
     */
    public function wpw_auto_poster_clear_log_cron()
    {
        global $wpw_auto_poster_logs;

        $wpw_auto_poster_logs->wpw_auto_poster_clear('logs');
    }

    /**
     * Cron function for clearing sap_uploads folder
     *
     * Handle to clear sap_uploads folder content when executing the cron
     *
     * @package Social Auto Poster
     * @since 2.7.9
     */
    public function wpw_auto_poster_clear_sap_uploads_cron()
    {

        // get folder whose content is to be deleted
        $path = WPW_AUTO_POSTER_SAP_UPLOADS_DIR;

        // get all file names
        $files = glob($path . '*');

        // if files exists in the folder
        if (!empty($files)) {
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        }
    }

    /**
     * Display notice if sap_uploads directory not exists
     *
     * @package Social Auto Poster
     * @since 2.8.2
     */
    public function wpw_auto_poster_upload_directory_notice()
    {
        $upload_dir = wp_upload_dir();
        $upload_path = isset($upload_dir['basedir']) ? $upload_dir['basedir'] . '/sap_uploads/' : ABSPATH;
        ?>
        <div class="error fade notice is-dismissible" id="wpw-auto-poster-upgrade-notice">
            <p><?php echo sprintf(esc_html__('Error: Could not create directory %s', 'wpwautoposter'), '<code>' . esc_html($upload_path) . '</code>'); ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wpwautoposter'); ?></span></button>
        </div>
        <?php
    }

    /**
     * Handle to check img url is relative or full image url
     *
     * @package Social Auto Poster
     * @since 2.8.2
     */
    public function wpw_auto_poster_custom_relative_image_src($img_src)
    {

        if (!empty($img_src)) {
            if (is_array($img_src)) {
                $temp_srcs = $img_src;

                foreach ($temp_srcs as $key => $src) {
                    if (( strpos($src, 'http://') !== 0 ) && ( strpos($src, 'https://') !== 0 )) {
                        $img_src[$key] = site_url($src);
                    }
                }
            } else {
                if (( strpos($img_src, 'http://') !== 0 ) && ( strpos($img_src, 'https://') !== 0 )) {
                    $img_src = site_url($img_src);
                }
            }
        }
        return $img_src;
    }

    /**
     * Handle to display notice for tumblr database upgrade
     *
     * @package Social Auto Poster
     * @since 3.1.4
     */
    public function wpw_auto_poster_database_upgrade_admin_notices()
    {
        $wpw_auto_poster_tumblr_upgrade_db = get_option('wpw_auto_poster_tumblr_upgrade_db');
        if (empty($wpw_auto_poster_tumblr_upgrade_db)) {
            $data_upgrade_url = add_query_arg(array('wpw_auto_sap_tumlr_upgrade' => '1'), admin_url('admin.php'));
            $button = '<a href="' . esc_attr($data_upgrade_url) . '"><b>' . esc_html__('here', 'wpwautoposter') . '</b></a>';
            ?>
            <div class="error fade notice is-dismissible" id="wpw-auto-poster-upgrade-notice">
                <p><?php echo sprintf(esc_html__('%sSocial Auto Poster data update:%s We need to update your database to the latest version. Please %sclick%s %s to start the update.', 'wpwautoposter'), '<b>', '</b>', '<b>', '</b>', $button); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Handle to upgrade database data for tumblr multi account feature option
     *
     * @package Social Auto Poster
     * @since 3.1.4
     */
    public function wpw_auto_poster_upgrade_tumblr_config_data()
    {

        if (isset($_GET['wpw_auto_sap_tumlr_upgrade']) && $_GET['wpw_auto_sap_tumlr_upgrade'] == '1') {
            $wpw_auto_poster_tumblr_upgrade_db = get_option('wpw_auto_poster_tumblr_upgrade_db');
            $wpw_auto_poster_tb_sess_data = get_option('wpw_auto_poster_tb_sess_data');
            $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

            if (empty($wpw_auto_poster_tumblr_upgrade_db)) {
                $tb_sess_data = array();
                $tumblr_keys_data = array();


                if (isset($wpw_auto_poster_options['tumblr_consumer_key']) && !empty($wpw_auto_poster_options['tumblr_consumer_key']) && isset($wpw_auto_poster_options['tumblr_consumer_secret']) && !empty($wpw_auto_poster_options['tumblr_consumer_secret'])) {
                    $tb_app_id = $wpw_auto_poster_options['tumblr_consumer_key'];
                    $tb_app_secret = $wpw_auto_poster_options['tumblr_consumer_secret'];


                    if (!empty($wpw_auto_poster_tb_sess_data) && !isset($wpw_auto_poster_tb_sess_data[$tb_app_id])) {
                        $tb_sess_data[$tb_app_id] = $wpw_auto_poster_tb_sess_data;

                        if (isset($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id']) && !empty($wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id'])) {
                            $tb_user_id = $wpw_auto_poster_tb_sess_data['wpw_auto_poster_tb_user_id'];
                        }

                        //check enable tumblr for is not set
                        if (isset($wpw_auto_poster_options['enable_tumblr_for']) && !empty($wpw_auto_poster_options['enable_tumblr_for'])) {
                            $enable_tumblr_for = $wpw_auto_poster_options['enable_tumblr_for'];

                            if (!empty($enable_tumblr_for)) {
                                foreach ($enable_tumblr_for as $type) {
                                    $key = 'tb_type_' . $type . '_user';

                                    $post_typ_user = $tb_app_id . '|' . $tb_user_id;

                                    $data = array(
                                        '0' => $post_typ_user,
                                    );

                                    $post_keys = array($key => $data);

                                    if (!isset($wpw_auto_poster_options[$key])) {
                                        $wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $post_keys);
                                    }
                                }
                            }
                        }

                        update_option('wpw_auto_poster_tb_sess_data', $tb_sess_data);
                        update_option('wpw_auto_poster_tumblr_upgrade_db', '1');
                    }


                    if (!isset($wpw_auto_poster_options['tumblr_keys']) && empty($wpw_auto_poster_options['tumblr_keys'])) {
                        $tumblr_keys_data[] = array(
                            'consumer_key' => $tb_app_id,
                            'consumer_secret' => $tb_app_secret,
                        );

                        $tumblr_keys = array('tumblr_keys' => $tumblr_keys_data);

                        $wpw_auto_poster_options = array_merge($wpw_auto_poster_options, $tumblr_keys);

                        update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
                        update_option('wpw_auto_poster_tumblr_upgrade_db', '1');
                    }

                    sap_add_notice(esc_html__('Database updated successfully.', 'wpwautoposter'), 'success');
                    $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
                    wp_redirect($redirect_url);
                    exit;
                } else {
                    update_option('wpw_auto_poster_tumblr_upgrade_db', '1');
                    sap_add_notice(esc_html__('Database updated successfully.', 'wpwautoposter'), 'success');

                    $redirect_url = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }
    }

    /**
     *
     */
    function wpw_auto_poster_yt_settings_panel_tab()
    {
        add_action('wpw_auto_poster_settings_panel_tab', array($this, 'wpw_auto_poster_youtube_setting_tab'), 30);
    }

    /**
     * Display Youtube Setting Tab
     *
     * Handle to display you tube setting tab
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_youtube_setting_tab($selected_tab)
    {

        $selectedtab = !empty($selected_tab) && $selected_tab == 'youtube' ? ' nav-tab-active' : '';
        ?>
        <a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-youtube" attr-tab="youtube">
            <img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL); ?>/youtube_set.png" width="24" height="24" alt="yt" title="<?php esc_html_e('You Tube', 'wpwautoposter'); ?>" />
        </a>
        <?php
    }

    /**
     * Add hook youtube setting option to social auto poster settings
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_yt_settings_panel_tab_content()
    {

        add_action('wpw_auto_poster_settings_panel_tab_content', array($this, 'wpw_auto_poster_youtube_setting_tab_content'), 30);
    }

    /**
     * Display Youtube Setting Tab Content
     *
     * Handle to display youtube setting tab content
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_youtube_setting_tab_content($selected_tab)
    {

        $selectedtabcontent = !empty($selected_tab) && $selected_tab == 'youtube' ? ' wpw-auto-poster-selected-tab' : '';
        ?>
        <div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-youtube">

            <?php
            // Youtube Settings
            include(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-youtube.php');
            ?>

        </div><!--#wpw-auto-poster-tab-youtube-->
        <?php
    }

    /**
     * Action Hook callback function
     *
     * Add youtube tab to reposter settings
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_reposter_yt_settings_panel_tab()
    {
        add_action('wpw_auto_poster_reposter_settings_panel_tab', array($this, 'wpw_auto_poster_reposter_youtube_setting_tab'), 30);
    }

    /**
     * Display Youtube Setting Tab
     *
     * Handle to display Youtube setting tab
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_reposter_youtube_setting_tab($selected_tab)
    {

        $selectedtab = !empty($selected_tab) && $selected_tab == 'youtube' ? ' nav-tab-active' : '';
        ?>
        <a class="nav-tab <?php echo esc_attr($selectedtab); ?>" href="#wpw-auto-poster-tab-youtube" attr-tab="youtube">
            <img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL); ?>/youtube_set.png" width="24" height="24" alt="yt" title="<?php esc_html_e('Youtube', 'wpwautoposter'); ?>" />
        </a>
        <?php
    }

    /**
     * Action Hook callback function
     *
     * Add Youtube Setting tab content
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_reposter_yt_settings_panel_tab_content()
    {
        add_action('wpw_auto_poster_reposter_settings_panel_tab_content', array($this, 'wpw_auto_poster_reposter_youtube_setting_tab_content'), 30);
    }

    /**
     * Display Youtube Setting Tab Content
     *
     * Handle to display youtube setting tab content
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_reposter_youtube_setting_tab_content($selected_tab)
    {

        $selectedtabcontent = !empty($selected_tab) && $selected_tab == 'youtube' ? ' wpw-auto-poster-selected-tab' : '';
        ?>
        <div class="wpw-auto-poster-tab-content <?php echo esc_attr($selectedtabcontent); ?>" id="wpw-auto-poster-tab-youtube">

            <?php
            // Youtube Settings
            include(WPW_AUTO_POSTER_ADMIN . '/forms/reposter/wpw-auto-poster-reposter-youtube.php');
            ?>

        </div><!--#wpw-auto-poster-reposter-tab-youtube-->
        <?php
    }

    /**
     * Filter Hook callback function
     *
     * Handle to show youtube posting logs
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_yt_hide_posting_logs($post_args, $ytbe_posts)
    {
        unset($post_args['post__not_in']);
        return $post_args;
    }

    /**
     * Set Youtube key for posting report option.
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_yt_social_key($social_keys)
    {
        $social_keys[] = 'yt';
        return $social_keys;
    }

    /**
     * Hook callback function
     *
     * Form for category account selection for youtube
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_category_yt_form_fields()
    {
        print '<table class="form-table">';
        include_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-category-social-yt-fields.php');
        print '<input type="hidden" name="wpw_auto_category_posting" value="1">';
        print '</table>';
    }

    /**
     * Filter Hook callback function
     *
     * Handle to set youtube default values for posting chart report
     *
     * @package Social Auto Poster - You Tube
     * @since 1.0.0
     */
    function wpw_auto_poster_report_yt_default_yt_option_values($default_arr)
    {
        $default_arr[] = 0;
        return $default_arr;
    }

    /**
     * Verify WordPress site details
     * and store it to database
     *
     * @package Social Auto Poster - WordPress
     * @since 3.5.1
     */
    public function wpw_auto_poster_wordpress_add_sites()
    {

        global $wpw_auto_poster_options, $wpw_auto_poster_wp_posting, $wpw_auto_poster_message_stack;

        // Verify nonce
        if ( !isset( $_POST['wpw_setting_nonce'] ) || !wp_verify_nonce( $_POST['wpw_setting_nonce'], 'wpw_auto_poster_setting_nonce' ) ) {
            $response = array(
                'type' => 'error',
                'message' => 'Invalid nonce'
                );
            wp_send_json($response);
            exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $response = array(
                    'type' => 'error',
                    'message' => 'Invalid user'
                    );
                wp_send_json($response);
                exit;
            }
        }

        // Check if any values are blank just return
        if (empty($_POST['wp_name']) || empty($_POST['wp_url']) ||
            empty($_POST['wp_username']) || empty($_POST['wp_password'])) {
            $response = array(
                'type' => 'error',
                'message' => esc_html__('Please enter value in all the fields.', 'wpwautoposter')
            );

            $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'instagram');
            wp_send_json($response);
            exit;
        }

        // default response
        $response = array(
        'type' => 'error',
        'message' => esc_html__('There is some issue while authenticating website.', 'wpwautoposter')
        );

        // Create array of wp details
        $wpDetails = array(
        'name' => esc_attr($_POST['wp_name']),
        'url' => esc_attr($_POST['wp_url']),
        'username' => esc_attr($_POST['wp_username']),
        'password' => esc_attr($_POST['wp_password'])
        );

        $websiteDetails = $wpw_auto_poster_wp_posting->wpw_auto_poster_add_website($wpDetails);

        if (isset($websiteDetails['faultCode']) || !empty($websiteDetails['faultString'])) {
            $response = array(
            'type' => 'error',
            'message' => $websiteDetails['faultString']
            );
        } else {
            $response = array(
            'type' => 'success',
            'message' => esc_html__('Website authenticated and added successfully!', 'wpwautoposter')
            );
        }

        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'wordpress');
        wp_send_json($response);
        exit;
    }

    /**
     * Map WordPress websites with post type
     *
     * @package Social Auto Poster - WordPress
     * @since 3.5.1
     */
    public function wpw_auto_poster_map_wordpress_post_type()
    {

        // Verify nonce
        if ( !isset( $_POST['wpw_setting_nonce'] ) || !wp_verify_nonce( $_POST['wpw_setting_nonce'], 'wpw_auto_poster_setting_nonce' ) ) {
            $response = array(
                'type' => 'error',
                'message' => 'Invalid nonce'
                );
            wp_send_json($response);
            exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $response = array(
                    'type' => 'error',
                    'message' => 'Invalid user'
                    );
                wp_send_json($response);
                exit;
            }
        }

        $postType = isset($_POST['postType']) ? sanitize_text_field($_POST['postType']) : '';
        $mapTypes = isset($_POST['mapTypes']) ? sanitize_text_field($_POST['mapTypes']) : '';

        // Check if empty post type
        if (empty($postType)) {
            $response = array(
                'status' => 'error',
                'msg' => esc_html__('Somethig is wrong, please refresh the page and try again.', 'wpwautoposter')
            );

            echo json_encode($response);
            exit;
        }

        $storedTypes = get_option('wpw_auto_poster_wordpress_mapped_posttypes', array());

        $mapTypes = !empty($mapTypes) ? array_map('sanitize_text_field', explode('|', $mapTypes)) : '';
        $storedTypes[$postType] = $mapTypes;
        update_option('wpw_auto_poster_wordpress_mapped_posttypes', $storedTypes);

        $response = array(
            'status' => 'success',
        );

        echo json_encode($response);
        exit;
    }

    /**
     * Add cookie method accounts
     */
    public function wpw_auto_poster_pinterest_add_cookie_acc()
    {

        global $wpw_auto_poster_options;

        // Verify nonce
        if ( !isset( $_POST['wpw_setting_nonce'] ) || !wp_verify_nonce( $_POST['wpw_setting_nonce'], 'wpw_auto_poster_setting_nonce' ) ) {
            $response = array(
                'type' => 'error',
                'message' => 'Invalid nonce'
                );
            wp_send_json($response);
            exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $response = array(
                    'type' => 'error',
                    'message' => 'Invalid user'
                    );
                wp_send_json($response);
                exit;
            }
        }

        // Reset pinterest data if method is different before
        $authMethod = !empty($wpw_auto_poster_options['pinterest_auth_options']) ? $wpw_auto_poster_options['pinterest_auth_options'] : 'app';
        if ($authMethod == 'app') {
            $wpw_auto_poster_options['pinterest_auth_options'] = 'cookie';
            update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
            delete_option('wpw_auto_poster_pin_sess_data');
        }

        if (! class_exists('Wpw_Auto_Poster_PIN_Cookie_Posting')) {
            require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-pin-cookie-posting.php');
        }

        $pinCookie = new Wpw_Auto_Poster_PIN_Cookie_Posting();

        $response = array();
        if (!empty($_POST['pin_sessid'])) {
            $sessID     = esc_attr($_POST['pin_sessid']);
            $siteName   = isset($_POST['pin_name']) ? esc_attr($_POST['pin_name']) : '';
            $response = $pinCookie->wpw_auto_poster_add_account($sessID, $siteName);
        }

        wp_send_json($response);
        exit;
    }

    public function wpw_auto_poster_init_test()
    {
        //$this->wpw_auto_poster_pinterest_add_cookie_acc();
    }

    /**
     * Adding Hooks
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function add_hooks()
    {

        // if the user can edit plugin options, let the fun begin!
        add_action('admin_menu', 'wpw_auto_poster_add_settings_page');

        add_action('admin_init', 'wpw_auto_poster_init');
        add_action('admin_init', array($this, 'wpw_auto_poster_init_test'));

        //post to social media when post or page or custom post type will be published
        add_action('save_post', array($this, 'wpw_auto_poster_post_to_social_media'), 15, 2);

        //add for schedule posting
        add_action('publish_future_post', array($this, 'wpw_auto_poster_schedule_posting'));

        //show admin notices
        add_action('admin_notices', array($this, 'wpw_auto_poster_admin_notices'));

        //show admin notices
        add_action('admin_notices', array($this, 'wpw_auto_poster_database_upgrade_admin_notices'));

        //add admin init for bult delete functionality
        add_action('admin_init', array($this, 'wpw_auto_poster_posted_logs_bulk_delete'));

        //add admin init for bulk scheduling functionality
        add_action('admin_init', array($this, 'wpw_auto_poster_scheduling_bulk_process'));

        // add filter to add validate settings
        add_filter('wpw_auto_poster_validate_settings', array($this, 'wpw_auto_poster_validate_setting'), 10, 2);

        // add filter to add validate settings for reposter
        add_filter('wpw_auto_poster_reposter_validate_settings', array($this, 'wpw_auto_poster_reposter_validate_setting'), 10, 2);

        //add filter to add custom schedule
        add_filter('cron_schedules', array($this, 'wpw_auto_poster_add_custom_scheduled'));

        //add action to call schedule cron for send wall post
        add_action('wpw_auto_poster_scheduled_cron', array($this, 'wpw_auto_poster_scheduled_cron'));

        //add action to call schedule cron for send wall post with reposter
        add_action('wpw_auto_poster_reposter_scheduled_cron', array($this, 'wpw_auto_poster_reposter_scheduled_cron'));
        //add_action('init', array($this, 'wpw_auto_poster_reposter_scheduled_cron'));
        

        //add action to call schedule cron for clear system log file
        add_action('wpw_auto_poster_clear_log_cron', array($this, 'wpw_auto_poster_clear_log_cron'));

        //add action to call schedule cron for clearing sap_uploads folder
        add_action('wpw_auto_poster_clear_sap_uploads_cron', array($this, 'wpw_auto_poster_clear_sap_uploads_cron'));

        //Remove post meta for status from wpml
        add_action('icl_make_duplicate', array($this, 'wpw_auto_poster_wpml_dup_remove_status_meta'), 10, 4);

        //Add meta in publish box
        add_action('post_submitbox_misc_actions', array($this, 'wpw_auto_poster_publish_meta'));

        // Add schedule metabox to gutenber editor
        add_action('add_meta_boxes', array($this, 'wpw_auto_poster_schedule_meta_boxex'), 10, 2);

        //Add action to add hook for all taxonomy add or edit form
        add_action('wp_loaded', array($this, 'wpw_auto_poster_hook_taxonomy'));

        //Add action to save posting social accounts for category
        add_action('created_term', array($this, 'wpw_auto_poster_category_fields_save'), 10, 3);

        //Add action to save posting social accounts for category
        add_action('edit_term', array($this, 'wpw_auto_poster_category_fields_save'), 10, 3);

        // Add filter to post pretty url instead wordpress default
        add_filter('wpw_custom_permalink', array($this, 'wpw_auto_poster_is_wp_pretty_url'), 10, 3);

        // Add action to for select2 ajax search for categories / Taxonomies
        add_action('wp_ajax_wpw_auto_poster_taxonomy_ajax_search', array($this, 'wpw_auto_poster_taxonomy_ajax_search'));
        add_action('wp_ajax_nopriv_wpw_auto_poster_taxonomy_ajax_search', array($this,'wpw_auto_poster_taxonomy_ajax_search'));

        // Add action to fecth categories from post type
        add_action('wp_ajax_wpw_auto_poster_quick_delete_multiple', array($this, 'wpw_auto_poster_quick_delete_multiple'));

        // Add action to fecth categories from post type
        add_action('wp_ajax_wpw_auto_poster_get_category', array($this, 'wpw_auto_poster_get_category'));
        add_action('wp_ajax_nopriv_wpw_auto_poster_get_category', array($this, 'wpw_auto_poster_get_category'));

        // Add action to fecth categories from custom post type
        add_action('wp_ajax_wpw_auto_poster_get_taxonomies', array($this, 'wpw_auto_poster_get_taxonomies'));
        add_action('wp_ajax_nopriv_wpw_auto_poster_get_taxonomies', array($this, 'wpw_auto_poster_get_taxonomies'));

        // Add action to fecth Graph data
        add_action('wp_ajax_wpw_auto_poster_logs_graph', array($this, 'wpw_auto_poster_logs_graph_process'));

        // check if sap uploads directory not exist on upload directory
        if (!file_exists(WPW_AUTO_POSTER_SAP_UPLOADS_DIR)) {
            add_action('admin_notices', array($this, 'wpw_auto_poster_upload_directory_notice'));
        }

        // Filter code for allowed relative image url instead of full image url
        add_filter('wpw_auto_poster_social_media_posting_image', array($this, 'wpw_auto_poster_custom_relative_image_src'));

        add_action('admin_init', array($this, 'wpw_auto_poster_upgrade_tumblr_config_data'));

        // social auto poster youtube settings
        add_action('wpw_auto_poster_settings_panel_tab_after_ba', array($this, 'wpw_auto_poster_yt_settings_panel_tab'));

        add_action('wpw_auto_poster_settings_panel_tab_content_after_ba', array($this, 'wpw_auto_poster_yt_settings_panel_tab_content'));

        // social auto poster reposter youtube settings
        add_action('wpw_auto_poster_reposter_settings_panel_tab_after_ba', array($this, 'wpw_auto_poster_reposter_yt_settings_panel_tab'));

        add_action('wpw_auto_poster_reposter_settings_panel_tab_content_after_ba', array($this, 'wpw_auto_poster_reposter_yt_settings_panel_tab_content'));

        /* Hide youtube posting logs */
        add_filter('wpw_auto_poster_get_posting_logs_args', array($this, 'wpw_auto_poster_yt_hide_posting_logs'), 10, 2);

        add_filter('wpw_auto_poster_admin_notices_social_keys', array($this, 'wpw_auto_poster_yt_social_key'));

        add_action('wpw_auto_poster_category_yt_form', array($this, 'wpw_auto_poster_category_yt_form_fields'));

        add_filter('wpw_auto_poster_report_default_option_values', array($this, 'wpw_auto_poster_report_yt_default_yt_option_values'));

        add_action('wp_ajax_wpw_auto_poster_wordpress_add_sites', array($this, 'wpw_auto_poster_wordpress_add_sites'));

        add_action('wp_ajax_wpw_auto_poster_map_wordpress_post_type', array($this, 'wpw_auto_poster_map_wordpress_post_type'));

        add_action('wp_ajax_wpw_auto_poster_pinterest_add_cookie_acc', array($this, 'wpw_auto_poster_pinterest_add_cookie_acc'));
    }
}