<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Bitly Class
 *
 * Handles to make url shortner with bitly
 * 
 * @package Social Auto Poster
 * @since 1.0.0
 */
 class wpw_auto_poster_tw_bitly {
 	
   	public $name,$access_token;

    function __construct($access_token) {
    	global $wpw_auto_poster_logs;
    	$this->name = 'bitly';
    	$this->access_token = $access_token;
    	$this->logs = $wpw_auto_poster_logs;
    }
    
    function shorten( $pageurl ) {
    	
    	/*$request_uri = 'https://api-ssl.bitly.com/v3/shorten?' .
    		'access_token=' . $this->access_token .
			'&longUrl=' . urlencode( $pageurl );
			
		$request_uri = esc_url_raw($request_uri);

		$encoded_data =  wp_remote_fopen( $request_uri ); 
		
		if ( $encoded_data ) {
			$decoded_result = json_decode( $encoded_data );
			if ( $decoded_result && $decoded_result->status_code == 200 && isset( $decoded_result->data ) && isset( $decoded_result->data->url ) ) {
				return $decoded_result->data->url;	
			}
		} */
		
		$post = json_encode( array("long_url" => $pageurl));
				
		$ch = curl_init('https://api-ssl.bitly.com/v4/shorten'); // Initialise cURL
		$authorization = "Authorization: Bearer ".$this->access_token; // Prepare the authorisation token
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields

		$result = curl_exec($ch); // Execute the cURL statement
		curl_close($ch); // Close the cURL connection

		$response = json_decode($result);
;
		if(!empty($response)) {
			return $response->link;
		}
		
		return $pageurl;
	}
 }