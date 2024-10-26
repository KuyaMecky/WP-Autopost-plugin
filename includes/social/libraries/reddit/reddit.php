<?php
/**
 * Reddit PHP SDK
 * 
 * Provides a SDK for accessing the Reddit APIs
 * Useage: 
 * $reddit = new reddit();
 * $user = $reddit->getUser();
 * Library URL : https://github.com/jcleblanc/reddit-php-sdk 
*/
class Reddit {


    private $client_id;
    private $redirect_uri;
    private $access_token;
    private $token_type;
    private $auth_uri = 'https://www.reddit.com/api/v1/authorize/?';
    private $scopes = array('save','modposts','identity','edit','flair','history','modconfig','modflair','modlog','modposts','modwiki','mysubreddits','privatemessages','read','report','submit','subscribe','vote','wikiedit','wikiread');
    private $auth_mode = 'basic';

    /**
     * Class Constructor
     *
     * Construct the class and simultaneously log a user in.
     * @link https://github.com/reddit/reddit/wiki/API%3A-login
     */
    public function __construct() {

    }
   
    /**
     * Create new story
     *
     * Creates a new story on a particular subreddit
     * @link http://www.reddit.com/dev/api/oauth#POST_api_submit
     * @param string $title The title of the story
     * @param string $link The link that the story should forward to
     * @param string $subreddit The subreddit where the story should be added
     */
    public function createStory($post_data) {
        $urlSubmit = "https://oauth.reddit.com/api/submit";

        $title   = $post_data['title'];
        $link    = $post_data['submitted-url'];
        $content = $post_data['description'];
        $image   = $post_data['submitted-image-url'];
        $subreddit = $post_data['subreddit_name'];
        $posting_type = $post_data['post_type'];


        //data checks and pre-setup
        if ($title == null || $subreddit == null) {
            return null;
        }

        $title = isset($title) ? $title : $content;

        $kind = ($posting_type == null) ? "self" : $posting_type;

        if (isset($posting_type) && $posting_type != '') {
            if ($posting_type == 'image') {
                $postData = sprintf("kind=image&url=%s&sr=%s&title=%s&r=%s", $image, $subreddit, $title, $subreddit
                );
            } else if ($posting_type == 'link') {
                $postData = sprintf("kind=link&url=%s&sr=%s&title=%s&r=%s", $link, $subreddit, $title, $subreddit
                );
            } else if ($posting_type == 'self') {
                $postData = sprintf("kind=self&sr=%s&title=%s&r=%s&text=%s", $subreddit, $title, $subreddit, $content
                );
            } else {
                $postData = sprintf("kind=self&sr=%s&title=%s&r=%s&text=%s", $subreddit, $title, $subreddit, $content
                );
            }
        }
        $this->auth_mode    = 'oauth';
        $this->access_token = $post_data['access_token'];
   

        
        $response = $this->runCurl($urlSubmit, $postData, '', $this->auth_mode,true,'');
        return $response;
    }

    /**
     * cURL request
     *
     * General cURL request function for GET and POST
     * @link URL
     * @param string $url URL to be requested
     * @param string $postVals NVP string to be send with POST request
     */
    //$token = $this->runCurl($auth_token_url, $postvals, null, true,false);
    public function runCurl($url, $postVals = null, $headers = null, $auth = false,$posting = false , $access_token = '') {
        $ch = curl_init($url);


        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10
        );

        // Create common user agent 
        // Get different user agent string from the reference https://developers.whatismybrowser.com/useragents/explore/software_type_specific/web-browser/2
        $user_agents = [
            "Mozilla/5.0 (Windows NT 5.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36",
        ];

        $useragent = $user_agents[array_rand($user_agents)]; // get random user agent to fix bad request issue

        $options[CURLOPT_USERAGENT] = $useragent;


        if ($postVals != null && $auth === true ) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postVals;
        } elseif ( $postVals != null ) {
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }

        if ($this->auth_mode == 'oauth') {

            
            if($posting) {
                $access_token = '';
                $access_token = $this->access_token;

            } else {

                if(isset($access_token) && $access_token != ''){
                    $access_token = $access_token;
                }

            }
            if (isset($access_token) && $access_token != '') {
                $token = explode(":",$access_token);
                $headers = array("Authorization: {$token[0]} {$token[1]}");

            } else {
                $headers = array("Authorization: Basic " . base64_encode( WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID.":".WPW_AUTO_POSTER_REDDIT_APP_CLIENT_SECRET));
            }
            $options[CURLOPT_HEADER] = false;
            $options[CURLINFO_HEADER_OUT] = false;
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        if ( $auth === true ) {

            $header = array ( 'Authorization' => 'Basic '. base64_encode(WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID.":".WPW_AUTO_POSTER_REDDIT_APP_CLIENT_SECRET) );
            $header_array = array();
            foreach( $header as $k => $v )
            {
                $header_array[] = $k.': '.$v;
            }

            $options[CURLOPT_HTTPHEADER] = $header_array;
            //$options[CURLOPT_SSLVERSION] = 4;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        curl_setopt_array($ch, $options);
        $apiResponse = curl_exec($ch);
        $response = json_decode($apiResponse);

        //check if non-valid JSON is returned
        if ($error = json_last_error()) {
            $response = $apiResponse;
        }
        curl_close($ch);

        return $response;
    }

    /**
     * Get user
     *
     * Get data for the current user
     * @link http://www.reddit.com/dev/api#GET_api_v1_me
     */
    public function getUser($access_token) {
        $this->auth_mode = 'oauth';
        $urlUser = "https://oauth.reddit.com/api/v1/me";
        return $this->runCurl($urlUser, '', '', $this->auth_mode,false,$access_token);
    }

    public function reddit_login($state)
    {
        $params = array(
            'duration'     => 'permanent',
            'response_type'=> 'code',
            'client_id'    => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope'        => implode(",", $this->scopes),
            'state'        => $state
        );

        $http_query = http_build_query($params);
        
        
        return $this->auth_uri . $http_query;
    }

    

    public function get_exchange_token($refresh_token)
    {
        
        if (empty($refresh_token))
        {
            $this->_show_error("Refresh token is missing");
        }


        $redirect_url  = WPW_AUTO_POSTER_REDDIT_REDIRECT_URL;
        $client_id     = WPW_AUTO_POSTER_REDDIT_APP_CLIENT_ID;
        $client_secret = WPW_AUTO_POSTER_REDDIT_APP_CLIENT_SECRET;
        $timestamp = time();
        $auth_token_url = 'https://www.reddit.com/api/v1/access_token';
        $postvals = array('grant_type' => 'refresh_token',
                          'refresh_token' => $refresh_token
                        );

        $token = $this->runCurl($auth_token_url, $postvals, null, true,false,'');

        $token = array(
            'access_token' => $token->access_token,
            'token_type' => $token->token_type,
        );
        return $token;
    }

    /**
     * Get Sub-Reddits from the current account which are subscribed
     *
     * Get data for the sub-reddits
     * http://www.reddit.com/dev/api#GET_api_v1_me
    */

    public function get_subscribed_subreddits( $access_token ) {
        
        $get_subscribed_subreddits_url = 'https://oauth.reddit.com/subreddits/mine/subscriber';
        return $this->runCurl($get_subscribed_subreddits_url, '', '','','', $access_token);
    }

    /**
     * Get Sub-Reddits from the current account in which user is approved user
     *
     * Get data for the sub-reddits
     * http://www.reddit.com/dev/api#GET_api_v1_me
    */

    public function get_contributor_subreddits( $access_token ) {

        $get_contributor_subreddits_url = 'https://oauth.reddit.com/subreddits/mine/contributor';
        return $this->runCurl($get_contributor_subreddits_url, '', '','','', $access_token);

    }    

    /**
     * Get Sub-Reddits from the current account in which user is moderator of that subreddit
     *
     * Get data for the sub-reddits
     * http://www.reddit.com/dev/api#GET_api_v1_me
    */

    public function get_moderator_subreddits( $access_token ) {

        $get_moderator_subreddits_url = 'https://oauth.reddit.com/subreddits/mine/moderator';
        return $this->runCurl($get_moderator_subreddits_url, '', '','','', $access_token);

    }

    /**
     * Get Sub-Reddits from the current account in which subreddits contains hosted video link streams
     *
     * Get data for the sub-reddits
     * http://www.reddit.com/dev/api#GET_api_v1_me
    */

    public function get_streams_subreddits( $access_token ) {

        $get_streams_subreddits_url = 'https://oauth.reddit.com/subreddits/mine/streams';
        return $this->runCurl($get_streams_subreddits_url, '', '','','', $access_token);

    }

    /**
     * Throw exception if there is any error
     *
     */
    private function _show_error($data)
    {
        throw new Exception($data, 500);
    }


}

?>
