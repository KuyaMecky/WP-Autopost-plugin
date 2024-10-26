<?php
/**
 * Plugin Name: Social Auto Poster
 * Plugin URI: https://Michael Tallada/
 * Description: Social Auto Poster lets you automatically post all your content to several different social networks.
 * Version: 5.3.15
 * Author: Michael Tallada
 * Domain Path: languages
 * WC tested up to: 9.1.2
 * Tested up to: 6.6
 *
 * @package Social Auto Poster
 * @category Core
 * @author Michael Tallada
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Basic Plugin Definitions
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
if (! defined('WPW_AUTO_POSTER_VERSION')) {
    define('WPW_AUTO_POSTER_VERSION', '5.3.15'); //version of plugin
}

//specify the user's role capabilites who can access this plugins settings in backend
//for more informatioon please check  http://codex.wordpress.org/Roles_and_Capabilities
if (! defined('wpwautoposterlevel')) {
    define('wpwautoposterlevel', 'manage_options'); //administrator role can use this plugin
}

if (! defined('WPW_AUTO_POSTER_DIR')) {
    define('WPW_AUTO_POSTER_DIR', dirname(__FILE__)); // plugin dir
}
if (! defined('WPW_AUTO_POSTER_URL')) {
    define('WPW_AUTO_POSTER_URL', plugin_dir_url(__FILE__)); // plugin url
}
if (! defined('WPW_AUTO_POSTER_IMG_URL')) {
    define('WPW_AUTO_POSTER_IMG_URL', WPW_AUTO_POSTER_URL . 'includes/images'); // plugin image url
}
if (! defined('WPW_AUTO_POSTER_ADMIN')) {
    define('WPW_AUTO_POSTER_ADMIN', WPW_AUTO_POSTER_DIR . '/includes/admin'); // plugin admin dir
}
if (! defined('WPW_AUTO_POSTER_META_DIR')) {
    define('WPW_AUTO_POSTER_META_DIR', WPW_AUTO_POSTER_DIR . '/includes/meta-boxes'); // path to meta boxes
}
if (! defined('WPW_AUTO_POSTER_META_URL')) { // path to meta boxes
    define('WPW_AUTO_POSTER_META_URL', WPW_AUTO_POSTER_URL . 'includes/meta-boxes');
}
if (! defined('WPW_AUTO_POSTER_SOCIAL_DIR')) { // path to meta boxes
    define('WPW_AUTO_POSTER_SOCIAL_DIR', WPW_AUTO_POSTER_DIR . '/includes/social/libraries');
}
if (! defined('WPW_AUTO_POSTER_TITLE_PREFIX')) {
    define('WPW_AUTO_POSTER_TITLE_PREFIX', 'Michael Tallada');
}
if (! defined('WPW_AUTO_POSTER_META_PREFIX')) {
    define('WPW_AUTO_POSTER_META_PREFIX', '_wpweb_'); //metabox prefix
}
if (! defined('WPW_AUTO_POSTER_LOGS_POST_TYPE')) { //social posting logs post type
    define('WPW_AUTO_POSTER_LOGS_POST_TYPE', 'wpwautoposterlogs');
}
if (! defined('WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE')) { //social quick share post type
    define('WPW_AUTO_POSTER_QUICK_SHARE_POST_TYPE', 'wpwsapquickshare');
}
if (! defined('WPW_AUTO_POSTER_LOG_DIR')) {
    define('WPW_AUTO_POSTER_LOG_DIR', ABSPATH . 'sap-logs/');
}
if (! defined('WPW_AUTO_POSTER_PLUGIN_KEY')) {
    define('WPW_AUTO_POSTER_PLUGIN_KEY', 'sap');
}
if (! defined('WPW_AUTO_POSTER_BASENAME')) { // base name
    define('WPW_AUTO_POSTER_BASENAME', basename(WPW_AUTO_POSTER_DIR));
}
if (! defined('WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE')) {
    define('WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE', 30); // Default custom schedule minutes
}

// added since 2.6.0
if (! defined('WPW_AUTO_POSTER_UTM_SOURCE')) { // Google tracking source name
    define('WPW_AUTO_POSTER_UTM_SOURCE', 'SocialAutoPoster');
}

// added since 2.6.0
if (! defined('WPW_AUTO_POSTER_UTM_MEDIUM')) { // Google tracking medium name
    define('WPW_AUTO_POSTER_UTM_MEDIUM', 'Social');
}

// added since 3.2.5
if (! defined('WPW_AUTO_POSTER_FB_APP_REDIRECT_URL')) {
    define('WPW_AUTO_POSTER_FB_APP_REDIRECT_URL', esc_url_raw('https://updater.Michael Tallada/codebase/SAP/fb/index.php')); // FB app redirect url
}

// added since 2.7.6
if (! defined('WPW_AUTO_POSTER_FB_API_VERSION')) {
    define('WPW_AUTO_POSTER_FB_API_VERSION', '2.9'); // FACEBOOK REST API CLASS
}

// added since 2.7.6
if (! defined('WPW_AUTO_POSTER_FB_GRAPH_VERSION')) {
    define('WPW_AUTO_POSTER_FB_GRAPH_VERSION', 'v3.0'); // FACEBOOK CLASS
}



$upload_dir = wp_upload_dir();
$upload_path = isset($upload_dir['basedir']) ? $upload_dir['basedir'] . '/' : ABSPATH;
$upload_url = isset($upload_dir['baseurl']) ? $upload_dir['baseurl'] : site_url();

// SAP upload dir for external images
if (! defined('WPW_AUTO_POSTER_SAP_UPLOADS_DIR')) {
    define('WPW_AUTO_POSTER_SAP_UPLOADS_DIR', $upload_path . 'sap_uploads/'); // external image upload dir
}

// SAP upload dir for external images
if (! defined('WPW_AUTO_POSTER_SAP_UPLOADS_URL')) {
    define('WPW_AUTO_POSTER_SAP_UPLOADS_URL', $upload_url . '/sap_uploads/'); // external image upload dir
}
if (! defined('WPW_AUTO_POSTER_FB_APP_METHOD_ID')) {
    define('WPW_AUTO_POSTER_FB_APP_METHOD_ID', '423068861904227'); // FACEBOOK APP ID
}
if (! defined('WPW_AUTO_POSTER_FB_APP_METHOD_SECRET')) {
    define('WPW_AUTO_POSTER_FB_APP_METHOD_SECRET', '37d585819468978caa6ce5fb944c6515'); // FACEBOOK APP SECRET
}

if (! defined('WPW_AUTO_POSTER_INSTA_APP_METHOD_ID')) {
    define('WPW_AUTO_POSTER_INSTA_APP_METHOD_ID', '423068861904227'); // Insta FACEBOOK APP ID
}
if (! defined('WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET')) {
    define('WPW_AUTO_POSTER_INSTA_APP_METHOD_SECRET', '37d585819468978caa6ce5fb944c6515'); // Insta APP SECRET
}

if (! defined('WPW_AUTO_POSTER_INSTA_APP_REDIRECT_URL')) {
    define('WPW_AUTO_POSTER_INSTA_APP_REDIRECT_URL', esc_url_raw('https://updater.Michael Tallada/codebase/SAP/fb/index-instagram.php')); // FB app redirect url
}

// added since 3.2.5
if (! defined('WPW_AUTO_POSTER_POST_LIMIT')) {
    define('WPW_AUTO_POSTER_POST_LIMIT', 10); // FACEBOOK APP SECRET
}

// added since 3.2.5
if (! defined('WPW_AUTO_POSTER_GMB_APP_CLIENT_ID')) {
    define('WPW_AUTO_POSTER_GMB_APP_CLIENT_ID', '804943316894-vksk1aj1mpkec9k57ocp8pttmno62hvk.apps.googleusercontent.com'); // GMB APP CLIENT ID
}
if (! defined('WPW_AUTO_POSTER_GMB_APP_CLIENT_SECRET')) {
    define('WPW_AUTO_POSTER_GMB_APP_CLIENT_SECRET', 'ns7XgpZEiAp3KalQFkcgH12Z'); //  GMB APP CLIENT SECRET
}
if (! defined('WPW_AUTO_POSTER_GMB_APP_SCOPE')) {
    define('WPW_AUTO_POSTER_GMB_APP_SCOPE', esc_url_raw('https://www.googleapis.com/auth/plus.business.manage')); //GMB APP SCOPE
}
if (! defined('WPW_AUTO_POSTER_GMB_REDIRECT_URL')) {
    define('WPW_AUTO_POSTER_GMB_REDIRECT_URL', esc_url_raw('https://updater.Michael Tallada/codebase/SAP/gmb/success.php')); //GMB Redirect URL
}

//Linked App method
if ( ! defined('WPW_AUTO_POSTER_LI_APP_ID') ){
    define('WPW_AUTO_POSTER_LI_APP_ID', '78atjnjdyfgvd8' ); // LINKEDIN APP ID
}

if ( ! defined('WPW_AUTO_POSTER_LI_APP_SECRET') ) {
    define('WPW_AUTO_POSTER_LI_APP_SECRET', 'DSgb8XJB6jHpgXWq' ); // LINKEDIN APP SECRET
}

if ( ! defined('WPW_AUTO_POSTER_LI_REDIRECT_URL') ) {
    define('WPW_AUTO_POSTER_LI_REDIRECT_URL', esc_url_raw('https://updater.Michael Tallada/codebase/SAP/li/index.php')); // LI app redirect url
}

// added since 3.2.5
if (! defined('WPW_AUTO_POSTER_REDDIT_REDIRECT_URL')) {
    define('WPW_AUTO_POSTER_REDDIT_REDIRECT_URL', esc_url_raw('https://www.Michael Tallada/codebase/SAP/reddit/index.php')); //Reddit Redirect URL
}
if (! defined('WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID')) {
    define('WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID', 'DxYnO7KhcMWXeQ');
}
if (! defined('WPW_AUTO_POSTER_REDDIT_APP_CLIENT_SECRET')) {
     define('WPW_AUTO_POSTER_REDDIT_APP_CLIENT_SECRET', 'wvqfuACX31stEXoi2M7b_Y9ZhnE'); //Client Secret
}
if (! defined('WPW_AUTO_POSTER_REDDIT_APP_SCOPE')) {
    $scopes = array( 'save', 'modposts', 'identity', 'edit', 'flair', 'history', 'modconfig', 'modflair', 'modlog', 'modposts', 'modwiki', 'mysubreddits', 'privatemessages', 'read', 'report', 'submit', 'subscribe', 'vote', 'wikiedit', 'wikiread' );

    $scopes = implode(",", $scopes);

    define('WPW_AUTO_POSTER_REDDIT_APP_SCOPE', $scopes); //Reddit scopes
}

// added since 3.8.2
if (! defined('WPW_AUTO_POSTER_MEDIUM_REDIRECT_URL')) {
    define('WPW_AUTO_POSTER_MEDIUM_REDIRECT_URL', 'https://updater.Michael Tallada/codebase/SAP/medium/index.php'); //Medium Redirect URL
}
if (! defined('WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_ID')) {
    define('WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_ID', '17e320baa51f');
}
if (! defined('WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_SECRET')) {
     define('WPW_AUTO_POSTER_MEDIUM_APP_CLIENT_SECRET', 'b58f15d26149f739005b3806bfc84444ae331ce0'); //Client Secret
}
if (! defined('WPW_AUTO_POSTER_MEDIUM_APP_SCOPE')) {
    $scopes = "basicProfile,listPublications,publishPost";
    define('WPW_AUTO_POSTER_MEDIUM_APP_SCOPE', $scopes); //Medium scopes
}
if ( ! defined( 'WPW_AUTO_POSTER_LICENSE_VALIDATOR' ) ) {
    define( 'WPW_AUTO_POSTER_LICENSE_VALIDATOR', 'https://updater.Michael Tallada/Updates/validator.php' ); // plugin vendor capability
}

/**
 * Re read all options to make it wpml compatible
 *
 * @package Social Auto Poster
 * @since 1.3.0
 */
function wpw_auto_poster_loaded_option()
{
    // Re-read settings because read plugin default option to Make it WPML Compatible
    global $wpw_auto_poster_options;
    $wpw_auto_poster_options = get_option('wpw_auto_poster_options');
}
//add action to load plugin
add_action('plugins_loaded', 'wpw_auto_poster_loaded_option');

//admin notice for GMB APP change
add_action('admin_notices', 'wpw_auto_poster_gmb_notice');

/**
 * Function used for wordpress notice for change GMB details and re-configure GMB
 *
 * @package Social Auto Poster
 * @since 4.0.2
 */
function wpw_auto_poster_gmb_notice()
{

    if (empty(get_option('wpw_auto_poster_gmb_notice_dismissed'))) {
        $class = 'notice notice-info wpw-sap-dismiss';
        
        $url = add_query_arg(array('wpw-redirect-to-gmb' => '1', 'wpw_set_gmb_tab' => '1' ), admin_url('admin.php'));


        $message = sprintf(esc_html__('The Google My Business API is updated so, kindly re-configure your Google My Business account again by clicking on %1$sAdd GMB Accounts%2$s button. %3$sDismiss%2$s', 'wpwautoposter'), '<a href="'.$url.'#wpw-auto-poster-tab-googlemybusiness">', '</a>', '<a class="alignright" href="?wpw-auto-poster-gmb-dismissed">');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    }
}

/**
 * Function to redirect user to GM setting page and for dismiss notice
 *
 * @package Social Auto Poster
 * @since 4.0.2
 */
function wpw_auto_poster_gmb_notice_dismissed()
{

    global $wpw_auto_poster_message_stack;

    if (isset($_GET['wpw-redirect-to-gmb']) && $_GET['wpw-redirect-to-gmb'] == '1' && isset($_GET['wpw_set_gmb_tab']) && $_GET['wpw_set_gmb_tab'] == '1') {
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'googlemybusiness');
        $url = add_query_arg(array('page' => 'wpw-auto-poster-settings', 'wpw_auto_poster_gmb_verification' => 'true','wpw_set_gmb_tab' => '1' ), admin_url('admin.php')).'#row-gmb-wp-pretty-url';
        wp_redirect($url);
        exit;
    }

    if (isset($_GET['wpw-auto-poster-gmb-dismissed'])) {
        update_option('wpw_auto_poster_gmb_notice_dismissed', '1');
        $url = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        wp_redirect($url);
        exit;
    }
}
add_action('admin_init', 'wpw_auto_poster_gmb_notice_dismissed');

/**
 * Load Text Domain
 * This gets the plugin ready for translation.
 *
 * @package Social Auto Poster
 * @since 1.7.5
 */
function wpw_auto_poster_plugins_loaded()
{
    
    // Set filter for plugin's languages directory
    $wpw_auto_poster_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
    $wpw_auto_poster_lang_dir = apply_filters('wpw_auto_poster_languages_directory', $wpw_auto_poster_lang_dir);
    
    // Traditional WordPress plugin locale filter
    $locale = apply_filters('plugin_locale', get_locale(), 'wpwautoposter');
    $mofile = sprintf('%1$s-%2$s.mo', 'wpwautoposter', $locale);
    
    // Setup paths to current locale file
    $mofile_local = $wpw_auto_poster_lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/' . WPW_AUTO_POSTER_BASENAME . '/' . $mofile;

    if (file_exists($mofile_global)) { // Look in global /wp-content/languages/social-auto-poster folder
        load_textdomain('wpwautoposter', $mofile_global);
    } elseif (file_exists($mofile_local)) { // Look in local /wp-content/plugins/social-auto-poster/languages/ folder
        load_textdomain('wpwautoposter', $mofile_local);
    } else { // Load the default language files
        load_plugin_textdomain('wpwautoposter', false, $wpw_auto_poster_lang_dir);
    }
}
add_action('plugins_loaded', 'wpw_auto_poster_plugins_loaded');

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
register_activation_hook(__FILE__, 'wpw_auto_poster_install');

/**
 * Plugin Setup (On Activation)
 *
 * Does the initial setup, creates tables in the database and
 * stest default values for the plugin options.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_install()
{

    global $wpdb;

    // Cron jobs
    wp_clear_scheduled_hook('wpw_auto_poster_scheduled_cron');

    // Plugin install setup function file
    require_once(WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-setup-functions.php');

    // Manage plugin version wise settings when plugin install and activation
    wpw_auto_manage_plugin_install_settings();

    // Check and set the crone on pugin activate if it's not set since 2.6.10
    wpw_auto_poster_check_for_schedule();

    require_once( WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-misc-functions.php' );

    if( wpw_auto_poster_is_lincense_activated() ) { 
        require_once WPW_AUTO_POSTER_ADMIN . '/wpw-auto-poster-licence-activation.php';
        $wpw_auto_poster_license = new Wpw_Auto_Posting_license();
        $wpw_auto_poster_license->wpw_auto_poster_verify_license();
    }
}

add_action('admin_init', 'wpw_auto_poster_set_quickshare_post');
/**
 * Function to Set quick share schedule
 *
 * @package Social Auto Poster
 * @since 4.0.0
 */
function wpw_auto_poster_set_quickshare_post()
{
    if (!wp_next_scheduled('wpw_auto_poster_scheduled_quick_share')) {
        $local_time = current_time('timestamp');
        wp_schedule_event($local_time, 'wpw_quickshare_custom_schedule', 'wpw_auto_poster_scheduled_quick_share');
    }
}

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
register_deactivation_hook(__FILE__, 'wpw_auto_poster_uninstall');

/**
 * Plugin Setup (On Deactivation)
 *
 * Deletes all the plugin options if the user has
 * set the option to do that.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_uninstall()
{
    
    global $wpdb;
    $wpw_auto_poster_options = get_option('wpw_auto_poster_options');
    
    if (!empty($wpw_auto_poster_options['delete_options']) &&
        $wpw_auto_poster_options['delete_options'] == '1') {
        // Plugin install setup function file
        require_once(WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-setup-functions.php');

        // Manage plugin version wise settings when plugin install and activation
        wpw_auto_manage_plugin_uninstall_settings();
    }
}

/**
 * Create Files/Directories
 *
 * Handle to create files/directories on activation
 *
 * @package Social Auto Poster
 * @since 1.6.2
 */
function wpw_auto_poster_create_files()
{
    
    global $wp_filesystem;

    if (empty($wp_filesystem)) {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }

    $files = array(
        array(
            'base' => WPW_AUTO_POSTER_LOG_DIR,
            'file' => 'index.html',
            'content' => ''
        ),
        array(
            'base' => WPW_AUTO_POSTER_SAP_UPLOADS_DIR,
            'file' => '',
            'content' => ''
        ),
    );

    foreach ($files as $file) {
        if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
            $wp_filesystem->put_contents($file['base'] . $file['file'], '');
        }
    }
}

if (! file_exists(WPW_AUTO_POSTER_SAP_UPLOADS_DIR)) {
    add_action('admin_init', 'wpw_auto_poster_check_sap_upload_dir');
}

/**
 *
 * Handle to check files/directories on admin init for multi site
 *
 * @package Social Auto Poster
 * @since 2.9.11
 */
function wpw_auto_poster_check_sap_upload_dir()
{
    wpw_auto_poster_create_files();
}

/**
 * Add plugin action links
 *
 * Adds a settings, support and docs link to the plugin list.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_add_settings_link($links)
{
    $plugin_links = array(
        '<a href="' . add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')) . '">' . esc_html__('Settings', 'wpwautoposter') . '</a>',
        '<a href="' . esc_url('https://support.Michael Tallada/') . '">' . esc_html__('Support', 'wpwautoposter') . '</a>',
        '<a href="' . esc_url('https://docs.Michael Tallada/social-auto-poster/') . '">' . esc_html__('Docs', 'wpwautoposter') . '</a>'
    );
    return array_merge($plugin_links, $links);
}
//add plugin settings, support and docs link to plugin listing page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wpw_auto_poster_add_settings_link');


/**
 * Start Session
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
function wpw_auto_poster_sessionset()
{
    global $wpdb, $wpw_auto_poster_message_stack, $pagenow;

    // added code condition to fix wordpress site health rest api issue
    if ($pagenow != 'site-health.php' && (!isset($_GET['page']) || $_GET['page'] != 'health-check')) {
        if ($wpw_auto_poster_message_stack->wpw_auto_poster_check_session_to_start()) {
            if (! session_id() && ! headers_sent()) {
                session_cache_limiter(''); // fix header response issue for caching
                session_start();
            }
        }
    }
    $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));

    // Reset Facebook User Data
    if (isset($_GET['fb_reset_user']) && $_GET['fb_reset_user'] == '1' && !empty($_GET['wpw_fb_app'])) {
        $fbposting = new Wpw_Auto_Poster_FB_Posting();
        $fbposting->wpw_auto_poster_fb_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'facebook');
        wp_redirect($settingspage);
        exit;
    }

    // Reset LinkedIn User Data
    if (isset($_GET['li_reset_user']) && $_GET['li_reset_user'] == '1') {
        $liposting = new Wpw_Auto_Poster_Li_Posting();
        $liposting->wpw_auto_poster_li_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'linkedin');
        wp_redirect($settingspage);
        exit;
    }

    //Reset Twitter User Data
    if (isset($_GET['tb_reset_user']) && $_GET['tb_reset_user'] == '1') { // if user reset session to tumblr
        $tbposting = new Wpw_Auto_Poster_TB_Posting();
        $tbposting->wpw_auto_poster_tb_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'tumblr');
        wp_redirect($settingspage);
        exit;
    }
    
    // Reset Pinterest User Data
    if (isset($_GET['pin_reset_user']) && $_GET['pin_reset_user'] == '1' && !empty($_GET['wpw_pin_app'])) {
        $pinposting = new Wpw_Auto_Poster_PIN_Posting();
        $pinposting->wpw_auto_poster_pin_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'pinterest');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Pinterest Cookie User Data
    if (isset($_GET['remove_pin_cookie_acc']) && $_GET['remove_pin_cookie_acc'] == '1' && !empty($_GET['wpw_pin_cookie_index'])) {
        $pinposting = new Wpw_Auto_Poster_PIN_Posting();
        $pinposting->wpw_auto_poster_pin_reset_cookie_account();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'pinterest');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Youtube User Data
    if (isset($_GET['yt_reset_user']) && $_GET['yt_reset_user'] == '1' && !empty($_GET['wpw_yt_app'])) {
        $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        $ytposting = new Wpw_Auto_Poster_Yt_Posting();
        $ytposting->wpw_auto_poster_yt_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'youtube');
        wp_redirect($settingspage);
        exit;
    }

    // Reset WordPress User Data
    if (isset($_GET['remove_wp_website']) && $_GET['remove_wp_website'] == '1' && $_GET['wpw_wp_index'] != '') {
        $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        // Add hash
        $settingspage .= '#wpw-auto-poster-tab-wordpress';
        $wpposting = new Wpw_Auto_Poster_Wp_Posting();
        $wpposting->wpw_auto_poster_wp_remove_site();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'wordpress');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Google My Business User Data
    if (isset($_GET['gmb_reset_user']) && $_GET['gmb_reset_user'] == '1' && !empty($_GET['wpw_gmb_userid'])) {
        $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        $gmbposting = new Wpw_Auto_Poster_GMB_Posting();
        $gmbposting->wpw_auto_poster_gmb_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'googlemybusiness');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Reddit User Data
    if (isset($_GET['reddit_reset_user']) && $_GET['reddit_reset_user'] == '1' && !empty($_GET['wpw_reddit_userid'])) {
        $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        $redditposting = new Wpw_Auto_Poster_Reddit_Posting();
        $redditposting->wpw_auto_poster_reddit_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'reddit');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Medium User Data
    if (isset($_GET['medium_reset_user']) && $_GET['medium_reset_user'] == '1' && !empty($_GET['wpw_medium_userid'])) {
        $settingspage = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
        $mediumposting = new Wpw_Auto_Poster_Medium_Posting();
        $mediumposting->wpw_auto_poster_medium_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'medium');
        wp_redirect($settingspage);
        exit;
    }

    // Reset Facebook User Data
    if (isset($_GET['insta_reset_user']) && $_GET['insta_reset_user'] == '1' && !empty($_GET['wpw_insta_fb_app'])) {
        $instaposting = new Wpw_Auto_Poster_Insta_Posting();
        $instaposting->wpw_auto_poster_insta_reset_session();
        $wpw_auto_poster_message_stack->add_session('poster-selected-tab', 'instagram');
        wp_redirect($settingspage);
        exit;
    }
}

global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options, $wpw_auto_poster_message_stack, $wpw_auto_poster_model, $wpw_auto_poster_fb_posting, $wpw_auto_poster_tw_posting, $wpw_auto_poster_li_posting, $wpw_auto_poster_tb_posting, $wpw_auto_poster_scripts, $wpw_auto_poster_render, $wpw_auto_poster_admin, $wpw_auto_poster_logs, $wpw_auto_poster_social_meta_box, $wpw_auto_poster_pin_posting, $wpw_auto_poster_upgrade, $wpw_auto_poster_wp_posting, $wpw_auto_poster_gmb_postings, $wpw_auto_poster_reddit_postings, $wpw_auto_poster_tele_posting,$wpw_auto_poster_medium_posting,$wpw_auto_poster_insta_posting, $wpw_auto_poster_lic;

/**
 * Include different files needed for our plugin.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
require_once(WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-misc-functions.php'); // plugin options class
$wpw_auto_poster_options = wpw_auto_poster_settings();
$wpw_auto_poster_reposter_options = wpw_auto_poster_reposter_settings();
wpw_auto_poster_initialize();

if ( wpw_auto_poster_is_lincense_activated() ) {
    // Register Post Types
    require_once(WPW_AUTO_POSTER_DIR . '/includes/wpw-auto-poster-post-types.php');

    // Settings functions
    require_once(WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-settings-functions.php');

    // Logs Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-logs.php');
    $wpw_auto_poster_logs = new Wpw_Auto_Poster_Logs();

    // Message Stack Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-message-stack.php');
    $wpw_auto_poster_message_stack = new Wpw_Auto_Poster_Message_Stack();

    // Model Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-model.php');
    $wpw_auto_poster_model = new Wpw_Auto_Poster_Model();

    // Facebook Posting Class ( fan page posting class )
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-fb-posting.php');
    $wpw_auto_poster_fb_posting = new Wpw_Auto_Poster_FB_Posting();

    // Twitter Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-tw-posting.php');
    $wpw_auto_poster_tw_posting = new Wpw_Auto_Poster_TW_Posting();

    // Linkein Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-li-posting.php');
    $wpw_auto_poster_li_posting = new Wpw_Auto_Poster_Li_Posting();

    // Tumblr Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-tb-posting.php');
    $wpw_auto_poster_tb_posting = new Wpw_Auto_Poster_TB_Posting();

    // You Tube Posting Class since 1.0.0
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-yt-posting.php');
    $wpw_auto_poster_yt_posting = new Wpw_Auto_Poster_YT_Posting();

    // Pinterest Posting Class since 2.6.0
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-pin-posting.php');
    $wpw_auto_poster_pin_posting = new Wpw_Auto_Poster_PIN_Posting();

    // WordPress Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-wp-posting.php');
    $wpw_auto_poster_wp_posting = new Wpw_Auto_Poster_Wp_Posting();

    // Google My Business Posting Class since version 1.1.22
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-gmb-posting.php');
    $wpw_auto_poster_gmb_postings = new Wpw_Auto_Poster_GMB_Posting();

    // Reddit posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-rd-posting.php');
    $wpw_auto_poster_reddit_postings = new Wpw_Auto_Poster_Reddit_Posting();

    // Instagram Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-instagram-posting.php');
    $wpw_auto_poster_insta_posting = new Wpw_Auto_Poster_INSTA_Posting();

    // load telegram library if php version >= 7.0.0
    if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
        // Telegram Posting Class
        require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-tele-posting.php');
        $wpw_auto_poster_tele_posting = new Wpw_Auto_Poster_Tele_Posting();
    } else {
        $wpw_auto_poster_tele_posting = '';
    }

    // Medium Posting Class
    require_once(WPW_AUTO_POSTER_DIR . '/includes/social/class-wpw-auto-poster-md-posting.php');
    $wpw_auto_poster_medium_posting = new Wpw_Auto_Poster_Medium_Posting();

    // Metabox File to add metabox
    require_once(WPW_AUTO_POSTER_META_DIR . '/wpw-auto-poster-meta-box.php');

    // Including the Scripts and Styles Files
    require_once(WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-scripts.php');
    $wpw_auto_poster_scripts = new Wpw_Auto_Posting_Scripts();
    $wpw_auto_poster_scripts->add_hooks();

    // Render Class to handles most of HTML designs for plugin
    require_once(WPW_AUTO_POSTER_DIR . '/includes/class-wpw-auto-poster-renderer.php');
    $wpw_auto_poster_render = new Wpw_Auto_Poster_Renderer();

    // Admin Class to handles all admin functionalities
    require_once(WPW_AUTO_POSTER_ADMIN . '/class-wpw-auto-poster-admin.php');
    $wpw_auto_poster_admin = new Wpw_Auto_Posting_AdminPages();
    $wpw_auto_poster_admin->add_hooks();

    // Quickshare Class to handles all quick share functionalities
    require_once(WPW_AUTO_POSTER_ADMIN . '/class-wpw-auto-poster-quick-share.php');
    $wpw_auto_poster_quick_share = new Wpw_Auto_Posting_QuickShare();
    $wpw_auto_poster_quick_share->add_hooks();

    // Database upgrade class
    require_once(WPW_AUTO_POSTER_ADMIN . '/class-wpw-auto-poster-upgrade.php');
    $wpw_auto_poster_upgrade = new Wpw_Auto_Poster_Upgrade();
    $wpw_auto_poster_upgrade->add_hooks();

    // Metabox class to manage post metaboxes
    require_once(WPW_AUTO_POSTER_META_DIR . '/class-wpw-auto-poster-meta.php');
    $wpw_auto_poster_social_meta_box = new Wpw_Auto_Poster_Social_Meta_Box();
    $wpw_auto_poster_social_meta_box->add_hooks();

    // session set
    add_action('init', 'wpw_auto_poster_sessionset', 15);
}

require_once WPW_AUTO_POSTER_ADMIN . '/wpw-auto-poster-licence-activation.php';
$wpw_auto_poster_lic = new Wpw_Auto_Posting_license();
$wpw_auto_poster_lic->add_hooks();

/**
* Declaring extension (in)compatibility
* you can declare whether it's compatible with HPOS or not.
* 
* @package WooCommerce Social Login
* @since Version 1.6.4
*/
add_action( 'before_woocommerce_init', function() {

    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
    
} );