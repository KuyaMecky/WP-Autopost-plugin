<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

 class wpw_auto_poster_tw_tinyurl {
 	
   	public $name;
	public $url;

    function __construct() {
    	$this->name = 'tinyurl';
    }
    
    function shorten( $pageurl ) {
		$tiny_url =  wp_remote_fopen( esc_url_raw('http://tinyurl.com/api-create.php?url=' . urlencode( $pageurl ) ) ); 
		if ( $tiny_url ) {
			return $tiny_url;
		} else {
			return $pageurl;	
		}
	}
 }