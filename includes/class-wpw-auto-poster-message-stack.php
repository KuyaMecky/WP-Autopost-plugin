<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*
  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('general', 'Error: Error 1', 'error');
  $messageStack->add('general', 'Error: Error 2', 'warning');
  if ($messageStack->size('general') > 0) echo $messageStack->output('general');
*/
class Wpw_Auto_Poster_Message_Stack {
  
    public $messageToStack, $messages;

	// class constructor
    function __construct() {
    	global $pagenow;

    			
		$this->messages = array();
		
		$wpwautoposter_message_stack = get_transient('wpwautoposter_message_stack');

		if( empty( $wpwautoposter_message_stack ) ) {
			$wpwautoposter_message_stack = array( 'messageToStack' => array() );
			set_transient('wpwautoposter_message_stack', $wpwautoposter_message_stack );
		}
		
		$this->messageToStack =& $wpwautoposter_message_stack['messageToStack'];
		  
		for( $i=0, $n=sizeof( $this->messageToStack ); $i<$n; $i++ ) {
			$this->add( $this->messageToStack[$i]['class'], $this->messageToStack[$i]['text'], $this->messageToStack[$i]['type']);
		}
		
		
		$this->messageToStack = array();
    }

    /**
	 * Start Session
	 * Checked URIs for which sessions should be start
	 * @package Social Auto Poster
	 * @since 2.9.0
	 */
    function wpw_auto_poster_check_session_to_start() {

		$should_start_session = true;

		if( ! empty( $_SERVER[ 'REQUEST_URI' ] ) ) {

			$block_urls_list = $this->wpw_auto_poster_get_blocked_urls();

			$uri       = ltrim( $_SERVER[ 'REQUEST_URI' ], '/' );
			$uri       = untrailingslashit( $uri );

			if( in_array( $uri, $block_urls_list ) ) {
				$should_start_session = false;
			}

			if( false !== strpos( $uri, 'feed=' ) ) {
				$should_start_session = false;
			}

			if( is_admin() && false !== strpos( $uri, 'wp-admin/admin-ajax.php' ) ) {
				// Do not want to start sessions in the admin unless we're processing an ajax request
				$should_start_session = false;
			}

			if( false !== strpos( $uri, 'wp_scrape_key' ) ) {
				// while saving the file editor save process
				$should_start_session = false;
			}

			if( false !== strpos( $uri, 'wp-json/wp-site-health/' ) ) {
                // Starting sessions while site health page gives error, so don't start
                $should_start_session = false;
            }

		}

		return apply_filters( 'wpw_auto_poster_start_session', $should_start_session );

	}

	 /**
	 * Retrieve the URI blacklist
	 *
	 * These are the URIs where should not start sessions
	 *
	 * @package Social Auto Poster
	 * @since 2.9.0
	 */
	public function wpw_auto_poster_get_blocked_urls() {

		$block_urls_list = apply_filters( 'wpw_auto_poster_session_start_blocked_urls', array(
			'feed',
			'feed/rss',
			'feed/rss2',
			'feed/rdf',
			'feed/atom',
			'comments/feed'
		) );

		// Look to see if WordPress is in a sub directory or this is a network site that uses sub directories
		$directory = str_replace( network_home_url(), '', get_site_url() );

		if( ! empty( $directory ) ) {
			foreach( $block_urls_list as $path ) {
				$block_urls_list[] = $directory . '/' . $path;
			}
		}

		return $block_urls_list;
	}

	// class methods
    function add( $class, $message, $type = '' ) {
      
		if ( $type == 'error' ) {
			$this->messages[] = array( 'params' => 'class="message_stack_error"', 'class' => $class, 'text' =>  '&nbsp;' . $message );
		} elseif ( $type == 'warning' ) {
			$this->messages[] = array( 'params' => 'class="message_stack_warning"', 'class' => $class, 'text' => '&nbsp;' . $message );
		} elseif ( $type == 'success' ) {
			$this->messages[] = array( 'params' => 'class="message_stack_success"', 'class' => $class, 'text' => '&nbsp;' . $message );
		} else {
			$this->messages[] = array( 'params' => 'class="message_stack_error"', 'class' => $class, 'text' => '' . $message );
		}
    }

    function add_session( $class, $message, $type = '' ) {
		$this->messageToStack[] = array( 'params' => 'class="message_stack_success"','class' => $class, 'text' => '' .$message, 'type' => $type );

		$wpwautoposter_message_stack['messageToStack'] = $this->messageToStack;
		set_transient('wpwautoposter_message_stack', $wpwautoposter_message_stack );

    }

    function remove_session( $class ) {

    	if( empty($class) ) return;
		
		if( $this->messages && is_array($this->messages) ) {
			foreach( $this->messages as $key => $msg ) {
				if( isset($msg['class']) && $msg['class'] == $class ){
					unset( $this->messages[$key] );
				}
			}

			$wpwautoposter_message_stack['messageToStack'] = $this->messages;
			set_transient('wpwautoposter_message_stack', $wpwautoposter_message_stack );
		}
    }

    function reset() {
		$this->messages = array();
    }

    function output( $class ) {
     
		$str = '';
		$output = array();
		for ( $i=0, $n=count( $this->messages ); $i<$n; $i++ ) {
			if ( $this->messages[$i]['class'] == $class ) {
				$output[] = $this->messages[$i];
			}
		}
      
		$len = count( $output );
		for ( $ii=0; $ii<$len; $ii++ ) {
			$str .= '<div ' . $output[$ii]['params'] . '>' . esc_html($output[$ii]['text']) . '</div>';
		}
    
		return $str;
    }

    function size($class) {
      
		$count = 0;

		for ( $i=0, $n=sizeof( $this->messages ); $i<$n; $i++ ) {
			if ( $this->messages[$i]['class'] == $class ) {
				$count++;
			}
		}

      return $count;
    }
}