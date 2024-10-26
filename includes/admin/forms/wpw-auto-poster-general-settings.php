<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * General Settings
 *
 * The html markup for the general settings box.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */
global $wpw_auto_poster_options, $wpw_auto_poster_logs, $wpw_auto_poster_model;

//logs class
$logs 	= $wpw_auto_poster_logs;
$model	= $wpw_auto_poster_model;

$twice_daily_time1 = empty( $wpw_auto_poster_options['schedule_wallpost_twice_time1'] ) ? '0': $wpw_auto_poster_options['schedule_wallpost_twice_time1'];

$twice_daily_time2 = empty( $wpw_auto_poster_options['schedule_wallpost_twice_time2'] ) ? '0': $wpw_auto_poster_options['schedule_wallpost_twice_time2'];

$schedule_wallpost_option = isset( $wpw_auto_poster_options['schedule_wallpost_option'] ) ? $wpw_auto_poster_options['schedule_wallpost_option'] : '';

$custom_schedule_css	= ( $schedule_wallpost_option == 'daily' ) ? '' : ' wpw-auto-poster-display-none ';

$custom_twice_schedule_css	= ( $schedule_wallpost_option == 'twicedaily' ) ? '' : ' wpw-auto-poster-twice-schedule-time ';

if( $custom_schedule_css == '' ) {
	$random_schedule_css	= empty($wpw_auto_poster_options['enable_random_posting']) ? '' : ' wpw-auto-poster-display-none ';
} else {
	$random_schedule_css	= $custom_schedule_css;
}

if( $custom_twice_schedule_css == '' ) {
	$random_twice_schedule_css	= empty( $wpw_auto_poster_options['enable_twice_random_posting'] ) ? '' : ' wpw-auto-poster-twice-schedule-time ';
} else {
	$random_twice_schedule_css	= $custom_twice_schedule_css;
}

$wpw_aps_limit_cls = empty($schedule_wallpost_option) ? ' wpw-auto-poster-display-none' : '';
$wpw_aps_min_limit_cls = ( empty($schedule_wallpost_option) || $schedule_wallpost_option != 'wpw_custom_mins' ) ? ' wpw-auto-poster-display-none' : '';

$wpw_aps_schedule_order = !empty($wpw_auto_poster_options['schedule_wallpost_order']) ? $wpw_auto_poster_options['schedule_wallpost_order'] : '';

$schedule_posting_order_style = ( !empty($schedule_wallpost_option) ) ? '':'ba_wp_pretty_url_css_hide';

$schedule_exlude_days_style = ( !empty($schedule_wallpost_option) && $schedule_wallpost_option != 'weekly' ) ? '':'ba_wp_pretty_url_css_hide';

// Code to hide and unhide custom minutes box 
$custom_minute_box_style = ( !empty($schedule_wallpost_option) && $schedule_wallpost_option == 'wpw_custom_mins' ) ? 'schedule_wallpost_option':'ba_wp_pretty_url_css_hide';

// Get custom minutes box value 
$schedule_wallpost_custom_minute = ( !empty( $wpw_auto_poster_options['schedule_wallpost_custom_minute'] ) ) ? $wpw_auto_poster_options['schedule_wallpost_custom_minute'] : WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE;

// check if google tracking is enable
$google_tracking_style = ( empty( $wpw_auto_poster_options['enable_google_tracking'] ) || $wpw_auto_poster_options['enable_google_tracking'] != 1 ) ? 'post_msg_style_hide' : '';

// check if google analytics script use with plugin or thirdpart
$google_tracking_script = ( !empty( $wpw_auto_poster_options['google_tracking_script'] ) ) ? $wpw_auto_poster_options['google_tracking_script'] : 'yes';

$google_tracking_script_style = ( $google_tracking_script == 'no' || !empty( $google_tracking_style ) ) ? 'post_msg_style_hide' : '';

$wpw_aps_posting_behaviour = !empty($wpw_auto_poster_options['schedule_wallpost_order_behaviour']) ? $wpw_auto_poster_options['schedule_wallpost_order_behaviour'] : 'DESC';

$wpw_behaviour_style = ( !empty($wpw_auto_poster_options['schedule_wallpost_order']) && $wpw_auto_poster_options['schedule_wallpost_order'] == 'rand' ) ? 'ba_wp_pretty_url_css_hide' : '';

$all_week_days = wpw_auto_poster_get_week_days();

$excld_selected_days = ( !empty( $wpw_auto_poster_options['schedule_excl_posting_days']) ) ? $wpw_auto_poster_options['schedule_excl_posting_days'] : array();

?>

<!-- beginning of the general settings meta box -->
<div id="wpw-auto-poster-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
								
					<!-- general settings box title -->
					<h3 class="hndle">
						<span class='wpw-sap-buffer-app-settings'><?php esc_html_e( 'General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>				
							
								<?php
									
									// do action for add setting before general settings
									do_action( 'wpw_auto_poster_before_general_setting', $wpw_auto_poster_options );
									
								?>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_posting_logs]"><?php esc_html_e( 'Enable Social Posting Logs : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[enable_posting_logs]" name="wpw_auto_poster_options[enable_posting_logs]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_posting_logs'] ) ) { checked( '1', $wpw_auto_poster_options['enable_posting_logs'] ); } ?> />
										<p><small>
											<?php //esc_html_e( 'Check this box to store your social posting activities into the database which can be viewed from "Social Posting Logs" section.', 'wpwautoposter' ); ?>
											<?php print sprintf(esc_html__( 'Check this box to store your social posting activities into the database which can be viewed from %s"Social Posting Logs"%s section', 'wpwautoposter' ), '<a href="'.esc_url( add_query_arg( 'page', 'wpw-auto-poster-posted-logs',admin_url( '/admin.php' ) ) ).'" target="_blank">','</a>' ); ?>
										</small></p>
									</td>
								</tr>
								<!-- Google campaign tracking option since 2.6.1 -->
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_google_tracking]"><?php esc_html_e( 'Google Analytics Campaign Tracking : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[enable_google_tracking]" name="wpw_auto_poster_options[enable_google_tracking]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_google_tracking'] ) ) { checked( '1', $wpw_auto_poster_options['enable_google_tracking'] ); } ?> />
										<p><small>
											<?php esc_html_e( 'Enable campaign tracking if you want to see how much traffic is generated by this plugin.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>
								<!-- Google campaign tracking script option since 2.6.1 -->
								<tr id="google_tracking_script_row" valign="top" class="<?php print esc_attr($google_tracking_style);?>" >
									<th scope="row">
										<label for="wpw_auto_poster_options[google_tracking_script]"><?php esc_html_e( 'Use Google Analytics:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<span class="sap-ga-option"><input id="wpw_auto_poster_google_tracking" name="wpw_auto_poster_options[google_tracking_script]" type="radio" value="yes" <?php if ( isset( $google_tracking_script ) ) { checked( 'yes', $google_tracking_script ); } ?> />
										<label for="wpw_auto_poster_google_tracking"><?php esc_html_e('Google Analytics from Social Auto Poster ', 'wpwautoposter'); ?></label></span>&nbsp;&nbsp;
										<span class="sap-ga-option"><input id="thirdparty_google_tracking" name="wpw_auto_poster_options[google_tracking_script]" type="radio" value="no" <?php if ( isset( $google_tracking_script ) ) { checked( 'no', $google_tracking_script ); } ?> />
										<label for="thirdparty_google_tracking"><?php esc_html_e('Google Analytics from thirdparty plugin', 'wpwautoposter'); ?></label>
										</span>
										<p><small>
											<?php esc_html_e( 'If you are using any thirdparty plugin which is adding google analytics tracking code then select ', 'wpwautoposter' ); 
												print '<strong>'.esc_html__('thirdparty option.', 'wpwautoposter').'</strong>';
												?>
										</small></p>
									</td>
								</tr>

								<!-- Google campaign tracking option since 2.6.1 -->
								<tr id="google_tracking_code_row" valign="top" class="<?php print esc_attr($google_tracking_script_style); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[google_tracking_code]"><?php esc_html_e( 'Google Analytics Tracking code:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<textarea id="wpw_auto_poster_options[google_tracking_code]" name="wpw_auto_poster_options[google_tracking_code]"><?php if ( isset( $wpw_auto_poster_options['google_tracking_code'] ) ) {  print $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['google_tracking_code']); } ?></textarea>
										<p><small>
											<?php esc_html_e( 'Paste your Google Analytics tracking code here. This will be added into the header template of your theme.', 'wpwautoposter' ); ?>
										</small></p>
										<p><small><strong><?php esc_html_e('Note:', 'wpwautoposter');?></strong>
											<?php esc_html_e( 'Get Google Analytics Tracking code from ', 'wpwautoposter' ); ?>
											<a title="<?php esc_html_e('Google Analytics javascript code', 'wpwautoposter');?>" href="<?php echo esc_url('https://developers.google.com/analytics/devguides/collection/analyticsjs/');?>" target="_blank"><?php esc_html_e('here.', 'wpwautoposter');?></a>
										</small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_posting_for_newpost]"><?php esc_html_e( 'Enable Auto Posting for new post only : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[enable_posting_for_newpost]" name="wpw_auto_poster_options[enable_posting_for_newpost]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_posting_for_newpost'] ) ) { checked( '1', $wpw_auto_poster_options['enable_posting_for_newpost'] ); } ?> />
										<p><small>
											<?php esc_html_e( 'Check this box to enable auto posting for new post only.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>
								<!-- Enable Excerpt Content -->
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_excerpt_content]"><?php esc_html_e( 'Enable Excerpt Content : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[enable_excerpt_content]" name="wpw_auto_poster_options[enable_excerpt_content]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_excerpt_content'] ) ) { checked( '1', $wpw_auto_poster_options['enable_excerpt_content'] ); } ?> />
										<p><small>
											<?php esc_html_e( 'Check this box to enable Excerpt Content. If the Excerpt field is empty, the content will be taken from the main content. Otherwise, the plugin will function as usual.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>

								<tr><td colspan="2">
									<h3><?php esc_html_e('Schedule Settings', 'wpwautoposter'); ?></h3>
								</td></tr>
								
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[schedule_wallpost_option]"><?php esc_html_e( 'Schedule Wall Posts : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<select name="wpw_auto_poster_options[schedule_wallpost_option]" id="wpw_auto_poster_options[schedule_wallpost_option]" class="wpw-auto-poster-schedule-option">
											<?php
												$schedule_wallpost_options = $model->wpw_auto_poster_get_all_schedules();

												foreach ( $schedule_wallpost_options as $key => $option ) {
													?>
													<option value="<?php echo esc_attr($key); ?>" <?php selected( $schedule_wallpost_option, $key ); ?>>
														<?php echo esc_html($option); ?>
													</option>
													<?php
												}
											?>
										</select>
										<span class="wpw-auto-poster-custom-minute-box <?php print esc_attr($custom_minute_box_style);?>" id="wpw-auto-poster-custom-minute-box">
											<input type="number" id="wpw_auto_poster_options[schedule_wallpost_custom_minute]" name="wpw_auto_poster_options[schedule_wallpost_custom_minute]" value="<?php print esc_attr($schedule_wallpost_custom_minute);?>" min="<?php print esc_attr(WPW_AUTO_POSTER_SCHEDULE_CUSTOM_DEFAULT_MINUTE);?>"> 
											<small><?php esc_html_e( 'Minutes', 'wpwautoposter' ); ?></small>
										</span>
										<p><small>
											<?php esc_html_e( 'Select the Schedule wall post option if you want to auto post your content at a desired time i.e. Minutes, Hourly, Twice Daily, Daily or Weekly.', 'wpwautoposter' ); ?>
										</small></p>
										<p class="wpw-auto-poster-info-box width-80 wpw-auto-poster-custom-minute-box <?php print esc_attr($wpw_aps_min_limit_cls);?>" id="wpw-auto-poster-custom-minute-box">
											<?php print sprintf( esc_html__( '%sNote:%s Minimum 30 minutes of schedule allowed to avoid account blocking issue.', 'wpwautoposter' ), '<b>','</b>'); ?>
										</p>
									</td>
								</tr>

								<tr valign="top" class="wpw-auto-poster-custom-schedule-wrap <?php echo esc_attr($custom_schedule_css) ?>">
									<th scope="row">
										<label><?php esc_html_e( 'Posting Type:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_regular_posting" class="wpw-auto-poster-random-posting" name="wpw_auto_poster_options[enable_random_posting]" type="radio" value="" <?php if ( isset( $wpw_auto_poster_options['enable_random_posting'] ) ) { checked( '', $wpw_auto_poster_options['enable_random_posting'] ); } ?> /><label for="wpw_auto_poster_regular_posting" class="wpw-auto-poster-label"><?php esc_html_e('Specific Time', 'wpwautoposter'); ?></label>&nbsp;&nbsp;
                                        <input id="wpw_auto_poster_random_posting" class="wpw-auto-poster-random-posting" name="wpw_auto_poster_options[enable_random_posting]" type="radio" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_random_posting'] ) ) { checked( '1', $wpw_auto_poster_options['enable_random_posting'] ); } ?> /><label for="wpw_auto_poster_random_posting" class="wpw-auto-poster-label"><?php esc_html_e('Randomly', 'wpwautoposter'); ?></label>
										<p><small>
											<?php esc_html_e( 'Select the Posting Type option. i.e. Specific time or Randomly.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>

								<tr valign="top" class="wpw-auto-poster-schedule-time wpw-auto-poster-custom-schedule-wrap <?php echo esc_attr($random_schedule_css); ?> daily">
									<th scope="row">
										<label for="wpw_auto_poster_options[schedule_wallpost_time]"><?php esc_html_e( 'Schedule Time:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<select name="wpw_auto_poster_options[schedule_wallpost_time]" id="wpw_auto_poster_options[schedule_wallpost_time]" class="wpw-auto-poster-hours">
											<?php		
												//Get all schedule time					
												$schedule_time_options = $model->wpw_auto_poster_get_all_schedule_time();
													
												foreach ( $schedule_time_options as $key => $value ) {
													
													?>
													<option value="<?php echo esc_attr($key); ?>" <?php selected( $wpw_auto_poster_options['schedule_wallpost_time'], $key ); ?>>
														<?php echo esc_html($value); ?>
													</option>
													<?php
												}															
											?> 														
										</select>
										<select name="wpw_auto_poster_options[schedule_wallpost_minute]" id="wpw_auto_poster_options[schedule_wallpost_minute]" class="wpw-auto-poster-minutes">
											<?php
												//Get all schedule minutes					
												$schedule_minute_options = $model->wpw_auto_poster_get_all_schedule_minutes();
													
												foreach ( $schedule_minute_options as $key => $value ) {
													
													?>
													<option value="<?php echo esc_attr($key); ?>" <?php selected( $wpw_auto_poster_options['schedule_wallpost_minute'], $key ); ?>>
														<?php echo esc_html($value); ?>
													</option>
													<?php
												}														
											?> 														
										</select>
										<p><small>
											<?php esc_html_e( 'Select the schedule time.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>
								
								<!-- Code for twice daily schedule posting options-->
								<tr valign="top" class="wpw-auto-poster-custom-twice-schedule-wrap <?php echo esc_attr($custom_twice_schedule_css) ?>">
									<th scope="row">
										<label><?php esc_html_e( 'Posting Type:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
                                        <input id="wpw_auto_poster_twice_regular_posting" class="wpw-auto-poster-twice-random-posting" name="wpw_auto_poster_options[enable_twice_random_posting]" type="radio" value="" <?php if ( isset( $wpw_auto_poster_options['enable_twice_random_posting'] ) ) { checked( '', $wpw_auto_poster_options['enable_twice_random_posting'] ); } else{ checked( '', '');} ?> /><label for="wpw_auto_poster_twice_regular_posting" class="wpw-auto-poster-label"><?php esc_html_e('Specific Time', 'wpwautoposter'); ?></label>&nbsp;&nbsp;
                                        <input id="wpw_auto_poster_twice_random_posting" class="wpw-auto-poster-twice-random-posting" name="wpw_auto_poster_options[enable_twice_random_posting]" type="radio" value="1" <?php if ( isset( $wpw_auto_poster_options['enable_twice_random_posting'] ) ) { checked( '1', $wpw_auto_poster_options['enable_twice_random_posting'] ); } ?> /><label for="wpw_auto_poster_twice_random_posting" class="wpw-auto-poster-label"><?php esc_html_e('Randomly', 'wpwautoposter'); ?></label>
										<p><small>
											<?php esc_html_e( 'Select the Posting Type option.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>
								<tr valign="top" class="wpw-auto-poster-schedule-twice-time wpw-auto-poster-custom-twice-schedule-wrap twicedaily <?php print esc_attr($random_twice_schedule_css);?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[schedule_wallpost_twice_time1]"><?php esc_html_e( 'Schedule Time:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<div class="twice-schedule-time-options">
											<select name="wpw_auto_poster_options[schedule_wallpost_twice_time1]" id="wpw_auto_poster_options[schedule_wallpost_twice_time1]" class="wpw-auto-poster-twice-hours">
												<?php		
													//Get all schedule time					
													$schedule_time_options = $model->wpw_auto_poster_get_all_schedule_time();
														
													foreach ( $schedule_time_options as $key => $value ) {
														
														?>
														<option value="<?php echo esc_attr($key); ?>" <?php selected( $twice_daily_time1, $key ); ?>>
															<?php echo esc_html($value); ?>
														</option>
														<?php
													}															
												?> 														
											</select>
											<small>
												<?php esc_html_e( 'First schedule', 'wpwautoposter' ); ?>
											</small>
										</div>
										<div class="twice-schedule-time-options">
											<select name="wpw_auto_poster_options[schedule_wallpost_twice_time2]" id="wpw_auto_poster_options[schedule_wallpost_twice_time2]" class="wpw-auto-poster-twice-hours">
												<?php		
													//Get all schedule time					
													$schedule_time_options = $model->wpw_auto_poster_get_all_schedule_time();
														
													foreach ( $schedule_time_options as $key => $value ) {
														
														?>
														<option value="<?php echo esc_attr($key); ?>" <?php selected( $twice_daily_time2, $key ); ?>>
															<?php echo esc_html($value); ?>
														</option>
														<?php
													}															
												?> 														
											</select>
											<small>
												<?php esc_html_e( 'Second schedule', 'wpwautoposter' ); ?>
											</small>	
										</div>
									</td>
								</tr>
								<tr id="wpw-auto-poster-schedule-days" class="<?php echo esc_attr($schedule_exlude_days_style);?>">
									<th><label><?php esc_html_e('Exclude Posting Days', 'wpwautoposter');?>:</label></th>
									<td>
										<div class="wpw-auto-poster-days-container">
											<?php foreach ( $all_week_days as $dy_key => $day ) {?>
											<label>
												<input type="checkbox" name="wpw_auto_poster_options[schedule_excl_posting_days][]" value="<?php print esc_attr($dy_key);?>" <?php if( in_array( $dy_key, $excld_selected_days )){ echo 'checked';}?>> <?php echo esc_html($day);?>
											</label>
											<?php } ?>
											<p><small>
											<?php esc_html_e( 'Select the days on which you don\'t want to auto post.', 'wpwautoposter' ); ?>
										</small></p>
										</div>
									</td>
								</tr>
								<!-- Schedule posting order -->
								<tr id="wpw-auto-poster-schedule-order-row" valign="top" class="<?php print esc_attr($schedule_posting_order_style);?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[schedule_wallpost_order]"><?php esc_html_e( 'Posting order:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<select name="wpw_auto_poster_options[schedule_wallpost_order]" id="wpw_auto_poster_options[schedule_wallpost_order]" class="wpw-auto-poster-schedule-order">
											<?php   												
												$schedule_posting_orders = $model->wpw_auto_poster_get_all_posting_orders();
																
												foreach ( $schedule_posting_orders as $key => $option ) {											
													?>
													<option value="<?php echo esc_attr($key); ?>" <?php selected( $wpw_aps_schedule_order, $key ); ?>>
														<?php echo esc_attr($option); ?>
													</option>
													<?php
												}														
											?>
										</select>
										
										<select name="wpw_auto_poster_options[schedule_wallpost_order_behaviour]" id="wpw_auto_poster_options[schedule_wallpost_order_behaviour]" class="wpw-auto-poster-schedule-order <?php print esc_attr($wpw_behaviour_style);?>">
											<?php   												
												$schedule_posting_behaviour = array( 'ASC' => 'Ascending', 'DESC' => 'Descending' );
																
												foreach ( $schedule_posting_behaviour as $key => $option ) {											
													?>
													<option value="<?php echo esc_attr($key); ?>" <?php selected( $wpw_aps_posting_behaviour, $key ); ?>>
														<?php echo esc_html($option); ?>
													</option>
													<?php
												}														
											?>
										</select>
										<p><small>
											<?php printf(esc_html__( 'Select posting order and all scheduled post will be posted on %s posting order %s.', 'wpwautoposter' ), "<strong>", "</strong>"
										); ?>
										</small></p>
										<p><small>
											<?php printf(esc_html__( '%s Default %s - All scheduled post will be posted by post ID in descending order.', 'wpwautoposter' ),
											"<strong>", "</strong>"
										); ?>
										</small></p>
									</td>
								</tr>

								<tr valign="top" class="wpw-auto-poster-schedule-limit <?php echo esc_attr($wpw_aps_limit_cls); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[daily_posts_limit]"><?php esc_html_e( 'Maximum Posting per schedule:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[daily_posts_limit]" name="wpw_auto_poster_options[daily_posts_limit]" type="number" value="<?php echo esc_attr($wpw_auto_poster_options['daily_posts_limit']); ?>" min="0" max="<?php print esc_attr(WPW_AUTO_POSTER_POST_LIMIT); ?>" />
										<p><small>
											<?php esc_html_e( 'Enter the maximum number of auto posting that you want to allow per hour, day, week etc. based on selected schedule wall posts option.', 'wpwautoposter' ); ?>
										</small></p>
										<p class="wpw-auto-poster-info-box width-80"><?php print sprintf( esc_html__('%sNote:%s Maximum 10 posts per schedule allowed to avoid account blocking issue.','wpwautoposter' ), '<b>','</b>' ); ?></p>
									</td>
								</tr>
								
								<tr><td colspan="2">
									<h3><?php esc_html_e('Misc Settings', 'wpwautoposter'); ?></h3>
								</td></tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[autopost_thirdparty_plugins]"><?php esc_html_e( 'Allow autopost from thirdparty plugins : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[autopost_thirdparty_plugins]" name="wpw_auto_poster_options[autopost_thirdparty_plugins]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['autopost_thirdparty_plugins'] ) ) { checked( '1', $wpw_auto_poster_options['autopost_thirdparty_plugins'] ); } ?> />
										<p><small>
											<?php esc_html_e( 'Check this box if you want to allow autoposting from any thirdparty plugins which allows to submit data from frontend.', 'wpwautoposter' ); ?>
										</small></p>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[delete_options]"><?php esc_html_e( 'Delete Options : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input id="wpw_auto_poster_options[delete_options]" name="wpw_auto_poster_options[delete_options]" type="checkbox" value="1" <?php if ( isset( $wpw_auto_poster_options['delete_options'] ) ) { checked( '1', $wpw_auto_poster_options['delete_options'] ); } ?> />
										<p><small><?php esc_html_e( 'Check this box if you don\'t want to use Social Auto Poster plugin on your website anymore. This will make sure that all the settings and tables are being deleted from the database when you deactivate the plugin.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_logs]"><?php esc_html_e( 'Debug Log : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
											<p><small>
											<?php esc_html_e( 'You can check posting system log at the file location ', 'wpwautoposter' );
													echo ' <code>'.esc_html(WPW_AUTO_POSTER_LOG_DIR.$logs->wpw_auto_poster_file_name( 'logs' )).'</code>';
											?>
										</small></p>
									</td>
								</tr>

								<?php

									// do action for add setting after general settings
									do_action( 'wpw_auto_poster_after_general_setting', $wpw_auto_poster_options );
								?>

								<?php
									echo apply_filters (
														 'wpweb_fb_settings_submit_button',
														 '<tr valign="top">
																<td colspan="2">
																	<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
														);
								?>
							</tbody>
						</table>
										
					</div><!-- .inside -->
									
			</div><!-- #general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-general -->
<!-- end of the general settings meta box -->