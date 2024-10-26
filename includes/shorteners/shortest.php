<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

 class wpw_auto_poster_tw_shortest {
 	
   	public $name;
	public $url;

    function __construct() {
    	$this->name = 'shortest';
    }
    
    function shorten( $api_token, $pageurl ) {
		
    	$shortest =  json_decode( wp_remote_fopen( esc_url_raw('https://api.shorte.st/s/'.$api_token.'/' . urlencode( $pageurl ) ) ) ); 
		
    	if ( $shortest ) {
			return $shortest->{'shortenedUrl'};
		} else {
			return $pageurl;	
		}
	}
 }