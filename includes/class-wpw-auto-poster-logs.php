<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Logs Class
 *
 * Handles to write logs to the file
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Poster_Logs {
	
	private $_handles;
	private $_path = WPW_AUTO_POSTER_LOG_DIR;
	public $filename;

	public function __construct() {
		$this->_handles = array();
	}


	/**
	 * Write Log File
	 *
	 * Handles to write log file
	 * 
	 * @package Social Auto Poster
 	 * @since 1.0.0
	 **/
	public function wpw_auto_poster_add( $message, $time = false, $handle = 'logs' ) {
		
		global $wpw_auto_poster_options, $wp_filesystem;

		if (empty($wp_filesystem)) {
		    require_once (ABSPATH . '/wp-admin/includes/file.php');
		    WP_Filesystem();
		}

		$this->filename = $this->wpw_auto_poster_file_name();
		
		$logmsg = $wp_filesystem->get_contents($this->_path.$this->filename);
		$logmsg .= "\n";

		//check need to write time to file
		if( $time ) {
			//append time to log message
			$logmsg .= "\n" . date_i18n( 'm-d-Y @ H:i:s - ' );
		} //end if to check time write to logs
		
		//append message to log message
		$logmsg .= $message;
		
		$wp_filesystem->put_contents($this->_path.$this->filename,$logmsg);	
	}


	/**
	 * Clear Logs
	 * 
	 * Handles to clear log entries
	 *
	 * @package Social Auto Poster
 	 * @since 1.0.0
	 **/
	public function wpw_auto_poster_clear( $handle = 'logs' ) {
		global $wp_filesystem;
		
		if (empty($wp_filesystem)) {
		    require_once (ABSPATH . '/wp-admin/includes/file.php');
		    WP_Filesystem();
		}
		
		$this->filename = $this->wpw_auto_poster_file_name();

		$wp_filesystem->put_contents($this->_path.$this->filename,'');
	}


	/**
	 * File Name
	 * 
	 * Handles to return file name with hash
	 *
	 * @package Social Auto Poster
 	 * @since 1.0.0
	 **/
	public function wpw_auto_poster_file_name( $handle = 'logs' ) {
		//return file name from handles
		return $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.txt';
	}
}
