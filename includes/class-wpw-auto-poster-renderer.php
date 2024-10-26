<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Renderer Class
 *
 * To handles some small HTML content for front end
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
class Wpw_Auto_Poster_Renderer {

	public $model;

	public function __construct() {

		global $wpw_auto_poster_model;

		$this->model = $wpw_auto_poster_model;
	}

	/**
	 * Add Popup For View Posting Details
	 *
	 * Handels to view posting details with popup
	 *
	 * @package Social Auto Poster
	 * @since 1.4.0
	 */
	public function wpw_auto_poster_view_posting_popup( $postid ) {

		$prefix = WPW_AUTO_POSTER_META_PREFIX;

		//get posting details from meta
	 	$posting_logs = get_post_meta( $postid, $prefix.'posting_logs', true );
	 	

	 	//get posting date/time
	 	$format 			   = get_option( 'date_format' ).' '.get_option('time_format') ;
	 	$publication_timestamp = get_the_date($format, $postid);

	 	//get posting user details from meta
	 	$user_details = get_post_meta( $postid, $prefix.'user_details', true );


	 	// get posting social type
		$social_type = get_post_meta( $postid, $prefix . 'social_type', true );


		// get posting link
		$post_link = wpw_auto_poster_get_post_link( $social_type, $user_details );

	 	$html = '';

		$html .= '<div class="wpw-auto-poster-popup-content">

					<div class="wpw-auto-poster-header">
						<div class="wpw-auto-poster-header-title">'.esc_html__( 'Social Posting Logs', 'wpwautoposter' ).'</div>
						<div class="wpw-auto-poster-popup-close"><a href="javascript:void(0);" class="wpw-auto-poster-close-button">&times;</a></div>
					</div>';

		$html .= '		<div class="wpw-auto-poster-popup wpw-auto-poster-posted-logs">

							<table class="form-table" border="1">
								<thead>
									<tr>
										<th scope="row" class="wpw-auto-poster-label">'.esc_html__( 'Label', 'wpwautoposter' ).'</th>
										<th scope="row">'.esc_html__( 'Content', 'wpwautoposter' ).'</th>
									</tr>
								</thead>
								<tbody>';

									if( !empty($posting_logs) &&  count($posting_logs) > 0 ) {

										if( $social_type == 'tele' ) {
											$html .= '<tr>
														<td>'.esc_html__( 'Chat ID', 'wpwautoposter' ).'</td>
														<td>'.$user_details['telegram_chatid'].'</td>
												</tr>';

											if( isset($user_details['posting_type']) ) {
												$html .= '<tr>
														<td>'.esc_html__( 'Message Type', 'wpwautoposter' ).'</td>
														<td>'.$user_details['posting_type'].'</td>
												</tr>';
											}
										}

										foreach( $posting_logs as $posting_log_key => $posting_log_value  ) {

											// Check fb_type is exist then display its name
											$posting_log_value = $posting_log_key == 'fb_type' ? $this->model->wpw_auto_poster_get_fb_posting_method( $posting_log_value ) : $posting_log_value;

											// Check fb_type is exist then change label
											$posting_log_key = $posting_log_key == 'fb_type' ? esc_html__( 'Posting Method', 'wpwautoposter' ) : $posting_log_key;

											if( $social_type == 'wp' && $posting_log_key == 'submitted-image-url' && is_array($posting_log_value) ) {
												$posting_log_value = $posting_log_value['src'];
											}

											if( $social_type != 'yt' || $posting_log_key != 'image' ) {
											$html .= '<tr>
													<td>'.ucwords( $posting_log_key ).'</td>
													<td>'.( $posting_log_key == 'image' || $posting_log_key == 'source'? '<div class="wpw-img-prev"><img src="'.esc_url($posting_log_value).'" >' : $posting_log_value ).'</td>
												</tr>';
											}
										}

										if( $social_type == 'wp' ) {
											$html .= '<tr>
													<td>'.esc_html__( 'Posted at Website', 'wpwautoposter' ).'</td>
													<td>'.$user_details['site_url'].'</td>
												</tr>';
										}


										if( isset( $user_details['display_name'] ) && !empty( $user_details['display_name'] ) && $social_type != 'reddit' ) { // Check display name

												$html .= '<tr>
														<td>'.esc_html__( 'Account Name', 'wpwautoposter' ).'</td>
														<td>'.esc_html($user_details['display_name']).'</td>
													</tr>';

										} else {
											
											$subreddit_name = !empty( $user_details['subreddit_name'] ) ? $user_details['subreddit_name'] : '';
											if (!empty($subreddit_name)) {
												$account_name = !empty($user_details['display_name']) ? $user_details['display_name']." -> " . $subreddit_name : ''; 
											} else {
												$account_name = !empty($user_details['display_name']) ? $user_details['display_name'] : $user_details['display_name'];
											}

											if(!empty($user_details['account_id'])) {

												$account_name = $user_details['account_id'];
											}

											$html .= '<tr>
														<td>'.esc_html__( 'Account Name', 'wpwautoposter' ).'</td>
														<td>'.esc_html($account_name).'</td>
													</tr>';

										}

										$html .= '<tr>
														<td>'.esc_html__( 'Date/Time', 'wpwautoposter' ).'</td>
														<td>'.esc_html($publication_timestamp).'</td>
												</tr>';


										if( $social_type != 'yt' && $social_type != 'tele' && !empty($post_link) ) {
											$html .= '<tr>
															<td>'.esc_html__( 'Link to Post', 'wpwautoposter' ).'</td>
															<td>'.esc_html($post_link).'</td>
													</tr>';
										}


										} else {
											$html .= '<tr>
													<td colspan="2">'.esc_html($postid).esc_html__( 'No posting logs yet.','wpwautoposter' ).'</td>
												</tr>';
										}
								$html .= '</tbody>
							</table>
					</div><!--.wpw-auto-poster-popup-->

				</div><!--.wpw-auto-poster-popup-content-->
				<div class="wpw-auto-poster-popup-overlay"></div>';

		return $html;

	}
}
