<?php
/**
 * Pinterest Class 
 *
 * Manage Pinterest cookie APIs
 */
class Wpw_Auto_Poster_PIN_Cookie_Posting{
	public function __construct(){

	}

	/**
	 * Get account details
	 */
	public function wpw_auto_poster_add_account( $sessID ) {

		$apiURL = 'https://www.pinterest.com/resource/HomefeedBadgingResource/get/';

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $apiURL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_ENCODING, '' );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 ); // Good leeway for redirections.
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_REFERER, 'https://pinterest.com/login/' );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array("X-Requested-With:XMLHttpRequest", "Accept:application/json") );
		curl_setopt( $ch, CURLOPT_COOKIE, '_pinterest_sess="'.$sessID.'"' );

		$response = curl_exec( $ch );
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		$data = json_decode( $response, true );
		if( $httpCode == '200' )  {
			
			$userData = isset( $data['client_context']['user'] ) ? $data['client_context']['user'] : array();

			$user = array();
			if( !empty($userData['username']) ) {
				$user['username'] = $userData['username'];
				$user['sessid'] = $sessID;
				$user['id'] = isset( $userData['id'] ) ? $userData['id'] : '';
				$user['email'] = isset( $userData['email'] ) ? $userData['email'] : '';
				$user['full_name'] = isset( $userData['full_name'] ) ? $userData['full_name'] : '';

				// Get account boards
				$user['boards'] = $this->wpw_auto_poster_get_pin_boards( $userData['username'] );

				$allPinData = get_option( 'wpw_auto_poster_pin_sess_data', array() );
				$allPinData[$user['username']] = $user;
				update_option( 'wpw_auto_poster_pin_sess_data', $allPinData );

				$respose['status'] = 'success';
				$respose['message'] = esc_html__( 'Account has been added successfully.', 'wpwautoposter' );
			} else {
				$respose['status'] = 'error';
				$respose['message'] = esc_html__( 'Userdata does not found.', 'wpwautoposter' );
			}
		} else {
			$respose['status'] = 'error';
			$respose['message'] = isset( $data['resource_response']['error']['message'] ) ? $data['resource_response']['error']['message'] : esc_html__( 'Somethig goes wrong, please try letter.', 'wpwautoposter' );
		}

		return $respose;
	}

	/**
	 * Get Boards
	 */
	public function wpw_auto_poster_get_pin_boards( $username ) {
		$apiURL = 'https://www.pinterest.com/resource/BoardsResource/get/?data=';

		$URL_Data = array(
			"options" =>  array( "username" =>  $username ),
		);

		$apiURL .= urlencode( json_encode($URL_Data) );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $apiURL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_ENCODING, '' );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 ); // Good leeway for redirections.
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array("X-Requested-With:XMLHttpRequest", "Accept:application/json") );

		$response = curl_exec( $ch );

		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		$boardsArr = array();
		if( $httpCode == '200' ) {
			$data = json_decode( $response, true );

			$boards = !empty( $data['resource_response']['data'] ) ? $data['resource_response']['data'] : array();

			foreach( $boards as $key => $board ) {
				$boardsArr[$board['id']] = array(
					'id'    =>  $board['id'],
					'name'  =>  $board['name'],
					'url'   =>  ltrim( $board['url'], '/' ),
				);
			}
		}

		return $boardsArr;
	}

	/**
	 * Post Pinterest Pin
	 */
	public function wpw_auto_poster_post_pin( $sessID, $boardId, $data = array() ) {
		global $wpw_auto_poster_options;

		$apiURL = 'https://www.pinterest.com/resource/PinResource/create/';

		$imageURL = isset( $data['image'] ) ? $data['image'] : '';
		$imageURL = str_replace( '../', '', $imageURL );
		$imageURL = str_replace( ABSPATH, '', $imageURL );

		$siteURL = site_url( '/' );
		if( strpos($imageURL, $siteURL) === false ){
			$imageURL = $siteURL . $imageURL;
		}

		$pinData = array(
			"options"   => array(
				"board_id"		=> $boardId,
				"title"			=> '',
				"description"	=> isset( $data['note'] ) ? $data['note'] : '',
				"link"			=> isset( $data['link'] ) ? $data['link'] : '',
				"image_url"		=> $imageURL,
				"method"		=> "uploaded",
			),
			"context" => array()
		);

		$postField = array( 
			'data'  =>	json_encode( $pinData )
		);

		$fields = http_build_query( $postField );

		// generated csrf token dynamically
		$csrftoken = bin2hex( random_bytes(32) ); 

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $apiURL );

		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "X-CSRFToken: {$csrftoken}") );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_ENCODING, '' );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );

		curl_setopt( $ch,CURLOPT_COOKIE,'csrftoken='.$csrftoken.'; _pinterest_sess="'.$sessID.'"; c_dpr=1' );

		// Set Proxy URL
		if(isset($wpw_auto_poster_options['pin_proxy_enable']) && $wpw_auto_poster_options['pin_proxy_enable'] && isset($wpw_auto_poster_options['pin_proxy_url']) && $wpw_auto_poster_options['pin_proxy_url']) {

            curl_setopt( $ch, CURLOPT_PROXY, $wpw_auto_poster_options['pin_proxy_url']); 
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);    
            if(isset($wpw_auto_poster_options['pin_proxy_username']) && $wpw_auto_poster_options['pin_proxy_username'] && isset($wpw_auto_poster_options['pin_proxy_password']) && $wpw_auto_poster_options['pin_proxy_password']){
                curl_setopt( $ch, CURLOPT_PROXYUSERPWD, ($wpw_auto_poster_options['pin_proxy_username'].":".$wpw_auto_poster_options['pin_proxy_password']));  
            }
        }

		$response = curl_exec( $ch );

		// Get response code
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		curl_close( $ch );

		$data = json_decode( $response, true );

		$status = isset( $data['resource_response']['status'] ) ? $data['resource_response']['status'] : '';
		if( $httpCode == '200' && $status == 'success' ) {
			$pinData = isset( $data['resource_response']['data'] ) ? $data['resource_response']['data'] : '';
			$respose['status'] = 'success';
			$respose['pindata'] = $pinData;
		} else {
			$respose['status'] = 'error';
			$respose['message'] = isset( $data['resource_response']['error']['message'] ) ? $data['resource_response']['error']['message'] : esc_html__( 'Somethig goes wrong, please try letter.', 'wpwautoposter' );
		}

		return $respose;
	}

}