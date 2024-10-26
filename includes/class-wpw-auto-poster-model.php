<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Model Class
 *
 * Handles generic plugin functionality, mostly Social Network related
 * for all the different features for the autoposting.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Poster_Model
{

    public function __construct()
    {
    }

    /**
     * similar to checked() but checks for array
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_checked_array($checked, $current)
    {

        if (is_array($current)) {
            if (in_array($checked, $current)) {
                echo ' checked="checked"';
            }
        } else {
            if ($checked == $current) {
                echo ' checked="checked"';
            }
        }
    }

    /**
     * Get Unserialize the data
     *
     * Handle serialize data and return unserialize data
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_get_unserialize_data($data)
    {
        $undata = unserialize($data);
        return $undata;
    }

    /**
     * Escape Tags & Slashes with URL
     *
     * Handles escapping the slashes and tags
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_escape_url($data)
    {
        return esc_url($data); // esc_url will do stripslashes and esc_attr both so we dont need to call it.
    }

    /**
     * Escape Tags & Slashes
     *
     * Handles escapping the slashes and tags
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_escape_attr($data)
    {
        return esc_attr_e(stripslashes($data));
    }

    /**
     * Stripslashes
     *
     * It will stripslashes from the content
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_stripslashes_deep($data = array(), $flag = false)
    {
        if ($flag != true) {
            $data = $this->wpw_auto_poster_nohtml_kses($data);
        }

        $data = stripslashes_deep($data);
        return $data;
    }

    /**
     * Strip Html Tags
     *
     * It will sanitize text input (strip html tags, and escape characters)
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_nohtml_kses($data = array())
    {

        if (is_array($data)) {
            $data = array_map(array($this, 'wpw_auto_poster_nohtml_kses'), $data);
        } elseif (is_string($data)) {
            $data = wp_filter_nohtml_kses($data);

            /**
             * Issue with JNews theme - tickets#id=4566
             * HTML tag in post content.
             */
            if (defined('JNEWS_THEME_ID')) {
                $data = wp_strip_all_tags($data);
            }
        }
        return $data;
    }

    /**
     * HTML Entity Decode
     *
     * Handles to decode HTML entities
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_html_decode($string)
    {
        return html_entity_decode($string);
    }

    /**
     * Get Full Permalink before post publish
     *
     * @package Social Auto Poster
     * @since 2.3.1
     */
    function wpw_auto_poster_get_permalink_before_publish($post_id)
    {

        require_once ABSPATH . '/wp-admin/includes/post.php';
        list( $permalink, $postname ) = get_sample_permalink($post_id);

        $post_url = str_replace('%postname%', $postname, $permalink);

        return apply_filters('wpw_auto_poster_get_permalink_before_publish', $post_url, $post_id);
    }

    /**
     * Get Shortner Link
     *
     * Handles to return shortner link
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    function wpw_auto_poster_get_short_post_link($link = '', $unique = 'false', $postid = '', $customlink = 'false', $socialtype = 'fb')
    {
        global $wpw_auto_poster_options;

        $shortnertype = $wpw_auto_poster_options[$socialtype . '_url_shortener'];
        $postObj = get_post($postid);

        if ($postObj->post_type != 'wpwsapquickshare') {
            //default link when it is blank
            $link = !empty($link) ? $link : get_permalink($postid);
        }

        if ($unique == 'true' && $customlink == 'true' && !empty($link)) {//unique url && it is custom url
            $link = add_query_arg('wpwautoposter', time(), $link);
        }

        $campaign_name = $this->wpw_auto_poster_get_social_type_name($socialtype);

        // add Google tracking argument for campaign tracking since 2.6.0
        if (!empty($wpw_auto_poster_options['enable_google_tracking']) && $wpw_auto_poster_options['enable_google_tracking'] == 1 && !empty($link)) {
            $link = add_query_arg(array(
                'utm_source' => WPW_AUTO_POSTER_UTM_SOURCE,
                'utm_medium' => WPW_AUTO_POSTER_UTM_MEDIUM,
                'utm_campaign' => $campaign_name
            ), $link);
        }
        if (!empty($link)) {
            switch ($shortnertype) {
                case 'tinyurl':
                    require_once(WPW_AUTO_POSTER_DIR . '/includes/shorteners/tinyurl.php');

                    $tinyurl = new wpw_auto_poster_tw_tinyurl;
                    $link = $tinyurl->shorten($link);
                    break;
                case 'wordpress':
                    if ($customlink != 'true') { //check custom link should not set
                        if (get_option('permalink_structure') != '') {
                            $link = wp_get_shortlink($postid);
                        } else {
                            $link = get_permalink($postid);
                        }

                        // add Google tracking argument for campaign tracking since 2.6.0
                        if (!empty($wpw_auto_poster_options['enable_google_tracking']) && $wpw_auto_poster_options['enable_google_tracking'] == 1 && !empty($link)) {
                            $link = add_query_arg(
                                array(
                                'utm_source' => WPW_AUTO_POSTER_UTM_SOURCE,
                                'utm_medium' => WPW_AUTO_POSTER_UTM_MEDIUM,
                                'utm_campaign' => $campaign_name
                                    ),
                                $link
                            );
                        }

                        $link = apply_filters('wpw_custom_permalink', $link, $postid, $socialtype);

                        if ($unique == 'true') { //unique url
                            $link = add_query_arg('wpwautoposter', time(), $link);
                        }
                    }
                    break;
                case 'bitly':
                    require_once(WPW_AUTO_POSTER_DIR . '/includes/shorteners/bitly.php');

                    //get bitly user name & api key
                    $wpw_auto_poster_bitly_access_token = $wpw_auto_poster_options[$socialtype . '_bitly_access_token'];

                    $bitlyurl = new wpw_auto_poster_tw_bitly($wpw_auto_poster_bitly_access_token);
                    $link = $bitlyurl->shorten($link);
                    break;
                case 'shorte.st':
                    require_once(WPW_AUTO_POSTER_DIR . '/includes/shorteners/shortest.php');

                    $wpw_auto_poster_shortest_api_token = $wpw_auto_poster_options[$socialtype . '_shortest_api_token'];

                    $tinyurl = new wpw_auto_poster_tw_shortest;
                    $link = $tinyurl->shorten($wpw_auto_poster_shortest_api_token, $link);
                    break;
            }
        }

        return apply_filters('wpw_short_post_link', $link, $unique, $postid, $customlink, $socialtype, $shortnertype);
    }

    /**
     * Replace Shortcode In Twitter Template
     *
     * Handles to return template with replace its shortcodes
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_tweet_status($post, $template, $title = '')
    {

        global $wpw_auto_poster_options;

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $ispublished = get_post_meta($post->ID, $prefix . 'tw_status', true);
        $unique = 'false';

        $post_type = $post->post_type; // Post type

        $tags_arr = array();
        $cats_arr = array();

        if (isset($ispublished) && $ispublished == '1') { //if post is published on facebook once then change url to prevent duplication
            //unique link for posting
            $unique = 'true';
        }


        // Get all selected tags for selected post type for hashtags support
        if (isset($wpw_auto_poster_options['tw_post_type_tags']) && !empty($wpw_auto_poster_options['tw_post_type_tags'])) {
            $custom_post_tags = $wpw_auto_poster_options['tw_post_type_tags'];
            if (isset($custom_post_tags[$post_type]) && !empty($custom_post_tags[$post_type])) {
                foreach ($custom_post_tags[$post_type] as $key => $tag) {
                    $term_list = wp_get_post_terms($post->ID, $tag, array("fields" => "names"));
                    foreach ($term_list as $term_single) {
                        $tags_arr[] = str_replace(' ', '', $term_single); // replace space with -
                    }
                }
            }
        }

        // Get all selected categories for selected post type for hashcats support
        if (isset($wpw_auto_poster_options['tw_post_type_cats']) && !empty($wpw_auto_poster_options['tw_post_type_cats'])) {
            $custom_post_cats = $wpw_auto_poster_options['tw_post_type_cats'];
            if (isset($custom_post_cats[$post_type]) && !empty($custom_post_cats[$post_type])) {
                foreach ($custom_post_cats[$post_type] as $key => $category) {
                    $term_list = wp_get_post_terms($post->ID, $category, array("fields" => "names"));
                    foreach ($term_list as $term_single) {
                        $cats_arr[] = str_replace(' ', '', $term_single); // replace space with -
                    }
                }
            }
        }


        $postlink = get_permalink($post->ID);
        $postlink = $this->wpw_auto_poster_get_short_post_link($postlink, $unique, $post->ID, 'false', 'tw');

        $posttitle = $post->post_title;

        $post_content = strip_shortcodes($post->post_content);

        $post_content = apply_filters('the_content', $post_content);

        //strip html kses and tags
        $post_content = $this->wpw_auto_poster_stripslashes_deep($post_content);

        //decode html entity
        $post_content = $this->wpw_auto_poster_html_decode($post_content);

        $userdata = get_userdata($post->post_author);

        $nicename = get_user_meta($post->post_author, 'nickname', true);
        $first_name = get_user_meta($post->post_author, 'first_name', true);
        $last_name = get_user_meta($post->post_author, 'last_name', true);
        $fullauthor = $first_name . ' ' . $last_name;
        $posttype = $post->post_type;

        // Get post excerpt
        $excerpt = !empty( $post->post_excerpt ) ? $post->post_excerpt : $this->wpw_auto_poster_custom_excerpt( $post->ID );

        //strip html kses and tags
        $excerpt = $this->wpw_auto_poster_stripslashes_deep($excerpt);


        // Get post excerpt
        $excerpt = apply_filters('wpw_auto_poster_tweet_status_excerpt', $excerpt, $post);

        // Get post tags
        $tags_arr = apply_filters('wpw_auto_poster_tw_hashtags', $tags_arr);
        $hashtags = (!empty($tags_arr) ) ? '#' . implode(' #', $tags_arr) : '';


        // get post categories
        $cats_arr = apply_filters('wpw_auto_poster_tw_hashcats', $cats_arr);
        $hashcats = (!empty($cats_arr) ) ? '#' . implode(' #', $cats_arr) : '';


        //if title is passed from function parameter then use that title
        $posttitle = !empty($title) ? $title : $posttitle;

        $replacetags = array('[link]', '[title]', '[full_author]', '[nickname_author]', '[post_type]', '[excerpt]', '[hashtags]', '[hashcats]');
        $replaceval = array($postlink, $posttitle, $fullauthor, $nicename, $posttype, $excerpt, $hashtags, $hashcats);
        $status = str_replace($replacetags, $replaceval, $template);

        $replacetags = array('{link}', '{title}', '{full_author}', '{nickname_author}', '{post_type}', '{excerpt}', '{hashtags}', '{hashcats}', '{content}');
        $replaceval = array($postlink, $posttitle, $fullauthor, $nicename, $posttype, $excerpt, $hashtags, $hashcats, $post_content);

        $code_matches = array();

        // check if template tags contains {content-numbers}
        if (preg_match_all('/\{(content)(-)(\d*)\}/', $status, $code_matches)) {
            $trim_tag = $code_matches[0][0];
            $trim_length = $code_matches[3][0];
            $post_content = substr($post_content, 0, $trim_length);
            $replacetags[] = $trim_tag;
            $replaceval[] = $post_content;
        }

        $cf_matches = array();
        // check if template tags contains {CF-CustomFieldName}
        if (preg_match_all('/\{(CF)(-)(\S*)\}/', $status, $cf_matches)) {
            foreach ($cf_matches[0] as $key => $value) {
                $cf_tag = $value;

                $replacetags[] = $cf_tag;
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

                $replaceval[] = $tag_value;
            }
        }

        $status = str_replace($replacetags, $replaceval, $status);

        return $this->wpw_auto_poster_html_decode($status);
    }

    /**
     * Return Template Text from Value
     *
     * Handles to return Template
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_get_tweet_template($tweet_template, $post_type = '')
    {

        global $wpw_auto_poster_options;
        $retval = '';

        switch ($tweet_template) {
            case 'title_fullauthor_link':
                $retval = '[title] by [full_author] - [link]';
                break;

            case 'title_nickname_link':
                $retval = '[title] by @[nickname_author] - [link]';
                break;

            case 'post_type_title_link':
                $retval = '[title] - [link]';
                break;

            case 'post_type_title_fullauthor_link':
                $retval = '[title] by [full_author] - [link]';
                break;

            case 'post_type_title_nickname_link':
                $retval = '[title] by [nickname_author] - [link]';
                break;
            case 'custom':
                if (!empty($post_type)) {
                    $wpw_auto_poster_options["tw_global_message_template_" . $post_type] = ( isset($wpw_auto_poster_options["tw_global_message_template_" . $post_type]) ) ? $wpw_auto_poster_options["tw_global_message_template_" . $post_type] : '';

                    $tw_custom_msg_options = isset($wpw_auto_poster_options['tw_custom_msg_options']) ? $wpw_auto_poster_options['tw_custom_msg_options'] : '';

                    if ($tw_custom_msg_options == 'post_msg' && !empty($wpw_auto_poster_options["tw_global_message_template_" . $post_type])) {
                        $retval = $wpw_auto_poster_options["tw_global_message_template_" . $post_type];
                        break;
                    } else {
                        $retval = $wpw_auto_poster_options['tw_custom_tweet_template'];
                        break;
                    }
                } else {
                    $retval = $wpw_auto_poster_options['tw_custom_tweet_template'];
                    break;
                }

            case 'title_link':
            default:
                $retval = '[title] - [link]';
                break;
        }

        return $retval;
    }

    /**
     * Get Self URL
     *
     * Handles to return current URL
     *
     * @package Social Auto Poster
     * @since 1.0.0
     */
    public function wpw_auto_poster_self_url()
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : (($_SERVER["HTTPS"] == "on") ? "s" : "");
        $str1 = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $str2 = "/";
        $protocol = substr($str1, 0, strpos($str1, $str2)) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }

    /**
     * Short the Content As Per Character Limit
     *
     * Handles to return short content as per character
     * limit
     *
     * @package Social Auto Poster
     * @since 1.0.0
     * */
    public function wpw_auto_poster_excerpt($content, $charlength = 280)
    {

        $excerpt = '';
        $charlength++;

        //check content length is greater then character length
        if (strlen($content) > $charlength) {
            $subex = substr($content, 0, $charlength - 5);
            $exwords = explode(' ', $subex);
            $excut = - ( strlen($exwords[count($exwords) - 1]) );

            if ($excut < 0) {
                $excerpt = substr($subex, 0, $excut);
            } else {
                $excerpt = $subex;
            }
        } else {
            $excerpt = $content;
        }

        //return short content
        return $excerpt;
    }

    /**
     * Short the excerpt from the post content
     *
     * Handles to return short content as per excerpt
     * limit
     *
     * @package Social Auto Poster
     * @since 1.0.0
     * */
    public function wpw_auto_poster_custom_excerpt( $post_id, $charlength = 55 )
    {
        global $wpw_auto_poster_options;
        $post = get_post($post_id);
        $excerpt = '';

        if ( isset( $wpw_auto_poster_options['enable_excerpt_content'] ) && $wpw_auto_poster_options['enable_excerpt_content'] == '1' && !empty( $post_id ) ) {
            $excerpt = get_the_excerpt( $post_id );
            if ( empty( $excerpt ) ) {
                // Get the post content and strip shortcodes and HTML tags
                $content = strip_shortcodes( $post->post_content );
                // Decode HTML entities
                $content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

                // Remove HTML tags
                $content = wp_strip_all_tags( $content );

                // Generate an excerpt with the specified word count
                $excerpt = wp_trim_words( $content, $word_count, '...' );
            }
        }

        // Return content
        return $excerpt;
    }

    /**
     * Insert Social Posting Log
     *
     * Handles to insert social posting log
     *
     * @package Social Auto Poster
     * @since 1.4.0
     * */
    public function wpw_auto_poster_insert_posting_log($post_id, $social_type, $posting_logs_data = array(), $posting_logs_user_details = array())
    {

        global $wpw_auto_poster_options, $current_user;

        // Check Enable Posting Logs is enable from general settings
        // Check post id, social type and posting data are not empty
        if (isset($wpw_auto_poster_options['enable_posting_logs']) && $wpw_auto_poster_options['enable_posting_logs'] == '1' && !empty($post_id) && !empty($social_type) && !empty($posting_logs_data)) {
            $prefix = WPW_AUTO_POSTER_META_PREFIX;

            $userid = '0';
            if (is_user_logged_in()) { // Check user is logged in
                $userid = $current_user->ID;
            }

            //create array arguments for saving the social posting data to database
            $posting_args = array(
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => WPW_AUTO_POSTER_LOGS_POST_TYPE,
                'post_parent' => $post_id,
                'post_author' => $userid
            );

            // insert the social posting data to database
            $postingid = wp_insert_post($posting_args);

            //if social posting basic data is successfully stored then update some more data to database
            if (!empty($postingid)) { //check record is inserted in database
                // update social type
                update_post_meta($postingid, $prefix . 'social_type', $social_type);

                // update social posting data
                update_post_meta($postingid, $prefix . 'posting_logs', $posting_logs_data);

                // update social posting user details
                update_post_meta($postingid, $prefix . 'user_details', $posting_logs_user_details);
            }
        }
    }

    /**
     * Convert Object To Array
     *
     * Converting Object Type Data To Array Type
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_object_to_array($result)
    {
        $array = array();
        foreach ($result as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->wpw_auto_poster_object_to_array($value);
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Get Posting Logs data
     *
     * Handles to get posting logs data
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_posting_logs_data($args = array())
    {

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $postinglogsargs = array(
            'post_type' => WPW_AUTO_POSTER_LOGS_POST_TYPE,
            'post_status' => 'publish'
        );

        $postinglogsargs = wp_parse_args($args, $postinglogsargs);

        //show how many per page records
        if (isset($args['posts_per_page']) && !empty($args['posts_per_page'])) {
            $postinglogsargs['posts_per_page'] = $args['posts_per_page'];
        } else {
            $postinglogsargs['posts_per_page'] = '-1';
        }

        //show per page records
        if (isset($args['paged']) && !empty($args['paged'])) {
            $postinglogsargs['paged'] = $args['paged'];
        }

        //if search using user
        if (isset($args['author'])) {
            $postinglogsargs['author'] = $args['author'];
        }

        //if search using post parent
        if (isset($args['post_parent'])) {
            $postinglogsargs['post_parent'] = $args['post_parent'];
        }

        //if serch using meta query
        if (isset($args['meta_query'])) {
            $postinglogsargs['meta_query'] = $args['meta_query'];
        }

        //if returns only id
        if (isset($args['fields']) && !empty($args['fields'])) {
            $postinglogsargs['fields'] = $args['fields'];
        }

        //if search is called then retrive searching data
        if (isset($args['search'])) {
            $postinglogsargs['s'] = $args['search'];
        }

        //if month is called then retrive monthly data
        if (isset($args['m'])) {
            $postinglogsargs['m'] = $args['m'];
        }

        //get order by records
        $postinglogsargs['order'] = 'DESC';
        $postinglogsargs['orderby'] = 'date';

        //show order by field records
        if (isset($args['order']) && !empty($args['order'])) {
            $postinglogsargs['order'] = $args['order'];
        }

        //show order by order records
        if (isset($args['orderby']) && !empty($args['orderby'])) {
            $postinglogsargs['orderby'] = $args['orderby'];
        }

        $insta_args = array(
            'post_type' => WPW_AUTO_POSTER_LOGS_POST_TYPE,
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(
                array(
                    'key' => $prefix . 'social_type',
                    'value' => 'ins',
                )
            )
        );

        $insta_posts = get_posts($insta_args);
        $postinglogsargs['post__not_in'] = $insta_posts;

        $postinglogsargs = apply_filters('wpw_auto_poster_get_posting_logs_args', $postinglogsargs, $insta_posts);

        //fire query in to table for retriving data
        $result = new WP_Query($postinglogsargs);

        if (isset($args['count']) && $args['count'] == '1') {
            $postinglogslist = $result->post_count;
        } else {
            //retrived data is in object format so assign that data to array for listing
            $postinglogslist = $this->wpw_auto_poster_object_to_array($result->posts);

            // if get list for auto poster list list then return data with data and total array
            if (isset($args['wpw_auto_poster_list']) && $args['wpw_auto_poster_list']) {
                $data_res = array();

                $data_res['data'] = $postinglogslist;

                //To get total count of post using "found_posts" and for users "total_users" parameter
                $data_res['total'] = isset($result->found_posts) ? $result->found_posts : '';

                return $data_res;
            }
        }

        return $postinglogslist;
    }

    /**
     * Get Scheduling data
     *
     * Handles to get scheduling data
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_scheduling_data($args = array())
    {

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //Get all post type names
        $all_types = get_post_types(array('public' => true));

        //Initialize and get all needed post types
        $post_types = array();
        foreach ($all_types as $key => $type) {
            if (in_array($type, array('attachment', 'media'))) {
                continue;
            }
            $post_types[] = $type;
        }

        //Arguments
        $scheduleargs = array(
            'post_type' => $post_types,
            'post_status' => 'publish'
        );

        $scheduleargs = wp_parse_args($args, $scheduleargs);

        //show how many per page records
        if (isset($args['posts_per_page']) && !empty($args['posts_per_page'])) {
            $scheduleargs['posts_per_page'] = $args['posts_per_page'];
        } else {
            $scheduleargs['posts_per_page'] = '-1';
        }

        //show per page records
        if (isset($args['paged']) && !empty($args['paged'])) {
            $scheduleargs['paged'] = $args['paged'];
        }

        //if search using user
        if (isset($args['author'])) {
            $scheduleargs['author'] = $args['author'];
        }

        //if search using post parent
        if (isset($args['post_parent'])) {
            $scheduleargs['post_parent'] = $args['post_parent'];
        }

        //if serch using meta query
        if (isset($args['meta_query'])) {
            $scheduleargs['meta_query'] = $args['meta_query'];
        }

        //if returns only id
        if (isset($args['fields']) && !empty($args['fields'])) {
            $scheduleargs['fields'] = $args['fields'];
        }

        //if search is called then retrive searching data
        if (isset($args['search'])) {
            $scheduleargs['s'] = $args['search'];
        }

        //if month is called then retrive monthly data
        if (isset($args['m'])) {
            $scheduleargs['m'] = $args['m'];
        }

        //get order by records
        $scheduleargs['order'] = 'DESC';
        $scheduleargs['orderby'] = 'date';

        //show order by field records
        if (isset($args['order']) && !empty($args['order'])) {
            $scheduleargs['order'] = $args['order'];
        }

        //show order by order records
        if (isset($args['orderby']) && !empty($args['orderby'])) {
            $scheduleargs['orderby'] = $args['orderby'];
        }

        //fire query in to table for retriving data
        $result = new WP_Query($scheduleargs);

        if (isset($args['count']) && $args['count'] == '1') {
            $schedulinglist = $result->post_count;
        } else {
            //retrived data is in object format so assign that data to array for listing
            $schedulinglist = $this->wpw_auto_poster_object_to_array($result->posts);

            // if get list for auto poster list list then return data with data and total array
            if (isset($args['wpw_auto_poster_list']) && $args['wpw_auto_poster_list']) {
                $data_res = array();

                $data_res['data'] = $schedulinglist;

                //To get total count of post using "found_posts" and for users "total_users" parameter
                $data_res['total'] = isset($result->found_posts) ? $result->found_posts : '';

                return $data_res;
            }
        }

        return $schedulinglist;
    }

    /**
     * Get Social status meta key
     *
     * Handles to get social status meta key
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_social_status_meta_key($selected_tab = 'facebook')
    {

        

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        //Initialize variable
        $status_meta_key = '';

        //Get social status meta key
        if ($selected_tab == 'facebook') {
            $status_meta_key = $prefix . 'fb_published_on_fb';
        } elseif ($selected_tab == 'twitter') {
            $status_meta_key = $prefix . 'tw_status';
        } elseif ($selected_tab == 'linkedin') {
            $status_meta_key = $prefix . 'li_status';
        } elseif ($selected_tab == 'tumblr') {
            $status_meta_key = $prefix . 'tb_status';
        } elseif ($selected_tab == 'bufferapp') {
            $status_meta_key = $prefix . 'ba_status';
        } elseif ($selected_tab == 'instagram') { // added since 2.6.0
            $status_meta_key = $prefix . 'insta_published_on_insta';
        } elseif ($selected_tab == 'pinterest') { // added since 2.6.0
            $status_meta_key = $prefix . 'pin_published_on_pin';
        } elseif ($selected_tab == 'youtube') { // added since 2.6.0
            $status_meta_key = $prefix . 'yt_published_on_yt';
        } elseif ($selected_tab == 'wordpress') { // added since 3.4.1
            $status_meta_key = $prefix . 'wp_status';
        } elseif ($selected_tab == 'googlemybusiness') { // added since 2.6.0
            $status_meta_key = $prefix . 'gmb_published_on_posts';
        } elseif ($selected_tab == 'reddit') { // added since 2.6.0
            $status_meta_key = $prefix . 'reddit_published_on_posts';
        } elseif ($selected_tab == 'telegram') { // added since 2.7.0
            $status_meta_key = $prefix . 'tele_status';
        } elseif ($selected_tab == 'medium') { // added since 2.6.0
            $status_meta_key = $prefix . 'medium_published_on_posts';
        }

       
        return $status_meta_key;
    }

    /**
     * Bulk Deletion
     *
     * Does handle deleting posts from the
     * database table.
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_bulk_delete($args = array())
    {
        global $wpdb;
        if (isset($args['log_id']) && !empty($args['log_id'])) {
            wp_delete_post($args['log_id']);
        }
    }


    /**
     * Bulk Deletion
     *
     * Does handle deleting posts from the
     * database table.
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_quick_bulk_delete($args = array())
    {
        global $wpdb;
        
        if (isset($args['post_id']) && !empty($args['post_id'])) {
            wp_delete_post($args['post_id']);
        }
    }


    /**
     * Get Date Format
     *
     * Handles to return formatted date which format is set in backend
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_date_format($date, $time = false)
    {

        $format = $time ? get_option('date_format') . ' ' . get_option('time_format') : get_option('date_format');
        $date = date_i18n($format, strtotime($date));
        return apply_filters('wpw_auto_poster_date_format', $date);
    }

    /**
     * All Social Types
     *
     * Handles to return all social types
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_social_type_name($social_type = '')
    {

        $social_types = array(
            'fb'    => esc_html__('Facebook', 'wpwautoposter'),
            'tw'    => esc_html__('Twitter', 'wpwautoposter'),
            'li'    => esc_html__('LinkedIn', 'wpwautoposter'),
            'tb'    => esc_html__('Tumblr', 'wpwautoposter'),
            'yt'    => esc_html__('Youtube', 'wpwautoposter'),
            'pin'   => esc_html__('Pinterest', 'wpwautoposter'),
            'gmb'   => esc_html__('Google My Business', 'wpwautoposter'),
            'reddit'=> esc_html__('Reddit', 'wpwautoposter'),
            'tele'  => esc_html__('Telegram', 'wpwautoposter'),
            'medium'    => esc_html__('Medium', 'wpwautoposter'),
             'insta'    => esc_html__('Instagram', 'wpwautoposter'),
            'wp'    => esc_html__('WordPress', 'wpwautoposter'),
           

        );

        $social_types = apply_filters('wpw_auto_poster_handle_social_type', $social_types);

        if (empty($social_type)) {
            return $social_types;
        }
        return !empty($social_type) && isset($social_types[$social_type]) ? $social_types[$social_type] : '';
    }

    /**
     * Get Facebook Posting Method
     *
     * Handles to get facebook posting method
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_fb_posting_method($method_key = '')
    {

        $fb_posting_method = array(
            'feed' => esc_html__('As a Wall Post', 'wpwautoposter'),
            'feed_status' => esc_html__('As a Status Update', 'wpwautoposter'),
        );

        if (empty($method_key)) { // Check method is empty
            return $fb_posting_method;
        }
        return !empty($method_key) && isset($fb_posting_method[$method_key]) ? $fb_posting_method[$method_key] : '';
    }

   

    /**
     * Get Tumblr Posting Method
     *
     * Handles to get tumblr posting method
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_tb_posting_method($method_key = '')
    {

        $tb_posting_method = array(
            'photo' => esc_html__('As Photo', 'wpwautoposter'),
            'text' => esc_html__('As Text', 'wpwautoposter'),
            'link' => esc_html__('As Link', 'wpwautoposter'),
        );

        if (empty($method_key)) { // Check method is empty
            return $tb_posting_method;
        }
        return !empty($method_key) && isset($tb_posting_method[$method_key]) ? $tb_posting_method[$method_key] : '';
    }

     /**
     * Get Reddit Posting Methods
     *
     * Handles to get reddit posting method
     *
     * @package Social Auto Poster
     * @since 3.5.2
     */

    public function wpw_auto_poster_get_reddit_posting_method($method_key = '')
    {

        $reddit_posting_method = array(
           'image' => esc_html__('As Photo', 'wpwautoposter'),
           'self' => esc_html__('As Text', 'wpwautoposter'),
           'link' => esc_html__('As Link', 'wpwautoposter'),
        );

        if (empty($method_key)) { // Check method is empty
            return $reddit_posting_method;
        }
        return !empty($method_key) && isset($reddit_posting_method[$method_key]) ? $reddit_posting_method[$method_key] : '';
    }


    /**
     * Get One Diemention Array
     *
     * Handles to get one diemention array by two diemention array
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */
    public function wpw_auto_poster_get_one_dim_array($multi_dim_array)
    {

        $one_dim_array = array();
        if (!empty($multi_dim_array)) { // Check dim array are not empty
            foreach ($multi_dim_array as $multi_dim_keys) {
                if (!empty($multi_dim_keys)) { // Check dim keys are not empty
                    foreach ($multi_dim_keys as $multi_dim_values) {
                        $one_dim_array[] = $multi_dim_values;
                    }
                }
            }
        }
        return $one_dim_array;
    }

    /**
     * Get All Schedules
     *
     * Handle to get all schedules
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_get_all_schedules()
    {

        $all_schedules = array(
            '' => esc_html__('Instantly', 'wpwautoposter'),
            'wpw_custom_mins' => esc_html__('Minutes', 'wpwautoposter'),
            'hourly' => esc_html__('Hourly', 'wpwautoposter'),
            'twicedaily' => esc_html__('Twice Daily', 'wpwautoposter'),
            'daily' => esc_html__('Daily', 'wpwautoposter'),
            'weekly' => esc_html__('Weekly', 'wpwautoposter')
        );
        return $all_schedules;
    }

    /**
     * Get All schedule posting order
     *
     * Handle to get all schedule posting order
     *
     * @package Social Auto Poster
     * @since 2.4.7
     */
    public function wpw_auto_poster_get_all_posting_orders()
    {

        $ordersby = array(
            '' => esc_html__('Default', 'wpwautoposter'),
            'post_title' => esc_html__('Post title', 'wpwautoposter'),
            'post_date' => esc_html__('Post date', 'wpwautoposter'),
            'post_type' => esc_html__('Post type', 'wpwautoposter'),
            'rand' => esc_html__('Randomly', 'wpwautoposter')
        );
        return $ordersby;
    }

    /**
     * Get All Schedule Time
     *
     * Handle to get all schedule time
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_get_all_schedule_time()
    {

        $all_schedules = array(
            '0' => esc_html__('12 AM', 'wpwautoposter'),
            '1' => esc_html__('1 AM', 'wpwautoposter'),
            '2' => esc_html__('2 AM', 'wpwautoposter'),
            '3' => esc_html__('3 AM', 'wpwautoposter'),
            '4' => esc_html__('4 AM', 'wpwautoposter'),
            '5' => esc_html__('5 AM', 'wpwautoposter'),
            '6' => esc_html__('6 AM', 'wpwautoposter'),
            '7' => esc_html__('7 AM', 'wpwautoposter'),
            '8' => esc_html__('8 AM', 'wpwautoposter'),
            '9' => esc_html__('9 AM', 'wpwautoposter'),
            '10' => esc_html__('10 AM', 'wpwautoposter'),
            '11' => esc_html__('11 AM', 'wpwautoposter'),
            '12' => esc_html__('12 PM', 'wpwautoposter'),
            '13' => esc_html__('1 PM', 'wpwautoposter'),
            '14' => esc_html__('2 PM', 'wpwautoposter'),
            '15' => esc_html__('3 PM', 'wpwautoposter'),
            '16' => esc_html__('4 PM', 'wpwautoposter'),
            '17' => esc_html__('5 PM', 'wpwautoposter'),
            '18' => esc_html__('6 PM', 'wpwautoposter'),
            '19' => esc_html__('7 PM', 'wpwautoposter'),
            '20' => esc_html__('8 PM', 'wpwautoposter'),
            '21' => esc_html__('9 PM', 'wpwautoposter'),
            '22' => esc_html__('10 PM', 'wpwautoposter'),
            '23' => esc_html__('11 PM', 'wpwautoposter'),
        );
        return $all_schedules;
    }

    /**
     * Get All Schedule Minutes
     *
     * Handle to get all schedule minutes
     *
     * @package Social Auto Poster
     * @since 1.5.2
     */
    public function wpw_auto_poster_get_all_schedule_minutes()
    {

        $all_schedules = array(
            '0' => esc_html__('00 Min', 'wpwautoposter'),
            '5' => esc_html__('05 Min', 'wpwautoposter'),
            '10' => esc_html__('10 Min', 'wpwautoposter'),
            '15' => esc_html__('15 Min', 'wpwautoposter'),
            '20' => esc_html__('20 Min', 'wpwautoposter'),
            '25' => esc_html__('25 Min', 'wpwautoposter'),
            '30' => esc_html__('30 Min', 'wpwautoposter'),
            '35' => esc_html__('35 Min', 'wpwautoposter'),
            '40' => esc_html__('40 Min', 'wpwautoposter'),
            '45' => esc_html__('45 Min', 'wpwautoposter'),
            '50' => esc_html__('50 Min', 'wpwautoposter'),
            '55' => esc_html__('55 Min', 'wpwautoposter'),
        );
        return $all_schedules;
    }

    /**
     * Get All Post Data
     *
     * Handle to get all post data which
     * have send mails to followers
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function wpw_auto_poster_get_schedule_post_data()
    {

        $prefix = WPW_AUTO_POSTER_META_PREFIX;

        $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

        $daily_posts_limit = (!empty($wpw_auto_poster_options['daily_posts_limit']) && $wpw_auto_poster_options['daily_posts_limit'] >= 0 ) ? $wpw_auto_poster_options['daily_posts_limit'] : 1;

        // Options to get post data by selected order
        $wpw_aps_schedule_order = !empty($wpw_auto_poster_options['schedule_wallpost_order']) ? $wpw_auto_poster_options['schedule_wallpost_order'] : '';

        $wpw_aps_posting_behaviour = !empty($wpw_auto_poster_options['schedule_wallpost_order_behaviour']) ? $wpw_auto_poster_options['schedule_wallpost_order_behaviour'] : 'DESC';

        //  Added since version 2.0.0
        if ($wpw_auto_poster_options['schedule_wallpost_option'] == 'hourly') {
            $current_time = current_time('timestamp');

            $local_time = date('Y-m-d H:i', $current_time);
            $local_time = strtotime($local_time);

            // get posts which are scheduled but current time is less then post meta time.
            // if post meta time is blank it will return all.
            $postargs = array(
                'post_type' => 'any',
                'posts_per_page' => $daily_posts_limit,
                'meta_query' => array(
                    array(
                        'key' => $prefix . 'schedule_wallpost',
                        'value' => '',
                        'compare' => '!='
                    ),
                    array(
                        'key' => $prefix . 'select_hour',
                        'value' => $local_time,
                        'compare' => '<='
                    ),
                )
            );
        } elseif ($wpw_auto_poster_options['schedule_wallpost_option'] == 'daily') {
            // get posts which are scheduled
            $postargs = array(
                'post_type' => 'any',
                'posts_per_page' => $daily_posts_limit, //  Added since version 2.0.0
                'meta_query' => array(
                    array(
                        'key' => $prefix . 'schedule_wallpost',
                        'value' => '',
                        'compare' => '!='
                    )
                )
            );

            $enable_random_posting = isset($wpw_auto_poster_options['enable_random_posting']) ? $wpw_auto_poster_options['enable_random_posting'] : '';

            //  Added since version 2.0.0
            // If daily > random time is selected then first clear the cron and re schedule it with random time
            if ($enable_random_posting == 1) {
                wp_clear_scheduled_hook('wpw_auto_poster_scheduled_cron');

                $utc_timestamp = time();

                $random = rand(86400, 97200); // Taking seconds between next 24 hours to 27 hours hours

                $utc_timestamp = $utc_timestamp + $random;

                wp_schedule_event($utc_timestamp, 'daily', 'wpw_auto_poster_scheduled_cron');
            }
        } else {
            $postargs = array(
                'post_type' => 'any',
                'posts_per_page' => $daily_posts_limit,
                'meta_query' => array(
                    array(
                        'key' => $prefix . 'schedule_wallpost',
                        'value' => '',
                        'compare' => '!='
                    )
                )
            );
        }

        //  Added since version 2.4.6
        // Get post data based on selected order from the settings
        if (!empty($wpw_aps_schedule_order)) {
            $postargs['orderby'] = $wpw_aps_schedule_order;
        }

        if (!empty($wpw_aps_schedule_order) && !empty($wpw_aps_posting_behaviour)) {
            $postargs['order'] = $wpw_aps_posting_behaviour;
        }

        // apply filter to modify schedule post arguments
        $postargs = apply_filters('wpw_auto_poster_schedule_post_args', $postargs);

        //fire query in to table for retriving data
        $result = new WP_Query($postargs);

        $postslist = $result->posts;


        // Check if post type counter from filter
        if (isset($args['count']) && !empty($args['count'])) {
            return count($postslist);
        }

        return $postslist;
    }

    /**
     * Get All Post Data for posting with reposter schedule
     *
     * Handle to get all post data which
     * have send mails to followers
     *
     * @package Social Auto Poster
     * @since 2.6.9
     */
    public function wpw_auto_poster_reposter_get_schedule_post_data($posting_for, $exclude_cats, $post_type_cats, $unique_posting, $post_limit, $slug, $label)
    {

        if( $slug == 'insta' ) {
            $label = 'instagram';
        }

        $prefix = WPW_AUTO_POSTER_META_PREFIX;
        $postslist = array();

        $wpw_auto_poster_reposter_options = get_option('wpw_auto_poster_reposter_options');


        $repeat_limit = ( empty($wpw_auto_poster_reposter_options['reposter_repeat_times']) ) ? '' : $wpw_auto_poster_reposter_options['reposter_repeat_times'];

        $exclude_ids = (!empty($wpw_auto_poster_reposter_options[$slug . '_post_ids_exclude']) ) ? $wpw_auto_poster_reposter_options[$slug . '_post_ids_exclude'] : '';

        $daily_posts_limit = (!empty($post_limit) && $post_limit >= 0 ) ? $post_limit : 1;


        // Options to get post data by selected order
        $wpw_aps_schedule_order = !empty($wpw_auto_poster_reposter_options['schedule_posting_order']) ? $wpw_auto_poster_reposter_options['schedule_posting_order'] : '';

        $wpw_posting_behaviour = !empty($wpw_auto_poster_reposter_options['schedule_posting_order_behaviour']) ? $wpw_auto_poster_reposter_options['schedule_posting_order_behaviour'] : '';

        $wpw_posting_repeat = ( empty($wpw_auto_poster_reposter_options['schedule_wallpost_repeat']) || $wpw_auto_poster_reposter_options['schedule_wallpost_repeat'] == 'no' ) ? false : true;

        $last_posted_page = get_option('sap_reposter_' . $slug . '_last_posted_page');
        if (empty($last_posted_page)) {
            $last_posted_page = 1;
        }


        if (!empty($posting_for)) {
            $posting_for = array_unique($posting_for);

            $postargs = array(
                'post_type' => $posting_for,
                'posts_per_page' => $daily_posts_limit,
                'meta_query' => array(
                    array(
                        'key' => $prefix . $slug . '_reposter_publish',
                        'compare' => 'NOT EXISTS'
                    )
                )
            );

            if (isset($wpw_auto_poster_reposter_options['maximum_post_age']) && ( $wpw_auto_poster_reposter_options['maximum_post_age'] > 0 )) {
                $currentTime = current_time('timestamp');
                $date_query_maximum = date('Y-m-d', strtotime('-' . $wpw_auto_poster_reposter_options['maximum_post_age'] . ' days', date($currentTime))); // maximum age posts
                $postargs['date_query'][0]['after'] = $date_query_maximum;
            }

            if (( ( isset($wpw_auto_poster_reposter_options['minimum_post_age']) && ( $wpw_auto_poster_reposter_options['minimum_post_age'] > 0 ) ))) {
                $currentTime = current_time('timestamp');
                $date_query_minimum = date('Y-m-d', strtotime('-' . $wpw_auto_poster_reposter_options['minimum_post_age'] . ' days', date($currentTime))); // minimum age posts
                $postargs['date_query'][0]['before'] = $date_query_minimum;
            }

            if (!empty($exclude_ids)) {
                $exclude_ids = explode(',', $exclude_ids);

                $postargs['post__not_in'] = $exclude_ids;
            }

            if ($unique_posting == true) { // check if unique posting enabled
                $status_meta_key = $this->wpw_auto_poster_get_social_status_meta_key($label);
                $postargs['meta_query'][] = array(
                    'key' => $status_meta_key,
                    'compare' => 'NOT EXISTS'
                );
            }
            
            if (!empty($exclude_cats)) {
                $postargs['tax_query'] = array('relation' => 'OR');

                if ($post_type_cats == 'exclude') {
                    $postargs['tax_query'] = array('relation' => 'AND');
                }

                // group all excluded taxonomies by taxonomy name array
                $excluded_cats_by_taxonomies = array();
                foreach ($exclude_cats as $post_type => $cat_slug) {
                    if (!empty($cat_slug)) {
                        foreach ($cat_slug as $cat_key => $cat_id) {
                            $term_object = get_term($cat_id);
                            if (!empty($term_object)) {
                                $tax_name = $term_object->taxonomy;
                                $excluded_cats_by_taxonomies[$tax_name][] = $cat_id;
                            }
                        }
                    }
                }

                if (!empty($excluded_cats_by_taxonomies)) {
                    foreach ($excluded_cats_by_taxonomies as $tax_name => $tax_array) {
                        if ($tax_name) {
                            $operator = ( $post_type_cats == 'exclude' ) ? 'NOT IN' : 'IN';
                            $postargs['tax_query'][] = array(
                                'taxonomy' => $tax_name,
                                'field' => 'term_id',
                                'terms' => $tax_array,
                                'operator' => $operator
                            );
                        }
                    }
                }
            }

            // Get post data based on selected order from the settings
            if (!empty($wpw_aps_schedule_order)) {
                $postargs['orderby'] = $wpw_aps_schedule_order;
            }


            if (!empty($wpw_posting_behaviour)) {
                $postargs['order'] = $wpw_posting_behaviour;
            }

            $postargs = apply_filters('wpw_auto_poster_reposter_post_args', $postargs, $slug, $label);

            //fire query in to table for retriving data
            $result = new WP_Query($postargs);

            // post start from reposter
            if ($wpw_posting_repeat && empty($result->posts)) {
                if (!empty($last_posted_page) && $postargs['posts_per_page'] >= 1) {
                    $postargs['paged'] = $last_posted_page;
                    $last_posted_page = $last_posted_page + 1;
                    update_option('sap_reposter_' . $slug . '_last_posted_page', $last_posted_page);
                }

                // unique posting will not work for repeat reposting
                unset($postargs['meta_query']);

                if (!empty($repeat_limit)) {
                    $postargs['meta_query'] = array(
                        'relation' => 'OR',
                        array(
                            'key' => $prefix . $slug . '_reposter_repeated_time',
                            'compare' => 'NOT EXISTS'
                        ),
                        array(
                            'key' => $prefix . $slug . '_reposter_repeated_time',
                            'value' => $repeat_limit,
                            'compare' => '<'
                        )
                    );
                }

                //fire query in to table for retriving data
                $result = new WP_Query($postargs);
            }

            $postslist = $result->posts;
        }

        return $postslist;
    }

    /**
     * Check Schedule type hourly and current page
     *
     * @package Social Auto Poster
     * @since 1.5.0
     */
    public function is_datetime()
    {

        global $pagenow, $wpw_auto_poster_options;

        //check hourly Schedule set and current page is posts page
        if (in_array($pagenow, array('post.php', 'post-new.php')) && !empty($wpw_auto_poster_options) && $wpw_auto_poster_options['schedule_wallpost_option'] == "hourly") {
            return true;
        }

        return false;
    }

    /**
     * Get all social types
     *
     * Handles to return all social types with prefix and full name
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_get_social_type_data()
    {

        $social_types = array(
            'fb'      => 'facebook',
            'tw'      => 'twitter',
            'li'      => 'linkedin',
            'tb'      => 'tumblr',
            'yt'      => 'youtube',
            'pin'     => 'pinterest',
            'gmb'     => 'googlemybusiness',
            'reddit'  => 'reddit',
            'tele'    => 'telegram',
            'wp'      => 'wordpress',
            'md'      => 'medium',
            'insta'      => 'instagram'
        );

        $social_types = apply_filters('wpw_auto_poster_handle_social_type_data', $social_types);

        return $social_types;
    }

    /**
     * Shortner method  list array
     *
     * Handles to return url shortner method list array
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_get_shortner_list()
    {

        return array(
            'wordpress' => esc_html__('WordPress', 'wpwautoposter'),
            'tinyurl' => esc_html__('TinyURL', 'wpwautoposter'),
            'bitly' => esc_html__('bit.ly', 'wpwautoposter'),
            'shorte.st' => esc_html__('shorte.st', 'wpwautoposter')
        );
    }

    /**
     * Google My Business Button Type
     *
     * Handles to return button list array
     *
     * @package Social Auto Poster
     * @since 2.6.0
     */
    public function wpw_auto_poster_gmb_button_type()
    {

        return array(
            'BOOK' => esc_html__('Book', 'wpwautoposter'),
            'ORDER' => esc_html__('Order online', 'wpwautoposter'),
            'SHOP' => esc_html__('Buy', 'wpwautoposter'),
            'LEARN_MORE' => esc_html__('Learn more', 'wpwautoposter'),
            'SIGN_UP' => esc_html__('Sign up', 'wpwautoposter'),
            'CALL' => esc_html__('Call', 'wpwautoposter')
        );
    }

    /**
     * Get All schedule reposting order
     *
     * Handle to get all reposting order
     *
     * @package Social Auto Poster
     * @since 2.6.9
     */
    public function wpw_auto_poster_get_reposter_posting_orders()
    {

        $ordersby = array(
            'ID' => esc_html__('ID', 'wpwautoposter'),
            'rand' => esc_html__('Randomly', 'wpwautoposter')
        );
        return $ordersby;
    }
}
