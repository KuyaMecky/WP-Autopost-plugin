<?php // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$email_address     = isset( $_POST['wpw_auto_poster_email_address'] ) ? $_POST['wpw_auto_poster_email_address'] : get_option( 'wpw_auto_poster_email_address' );
$activation_code   = isset( $_POST['wpw_auto_poster_activation_code'] ) ? $_POST['wpw_auto_poster_activation_code'] : get_option( 'wpw_auto_poster_activation_code' );
$activation_status = get_option( 'wpw_auto_poster_activated' );
if( $activation_status ) {
	$code_length = strlen( $activation_code ) / 2;
	$activation_code = substr( $activation_code, 0, $code_length ) . str_repeat( '*', $code_length );
}
if ( isset( $data['status'] ) && true == $data['status'] ) {
	$class = 'notice';
} else {
	$class = 'error';
} ?>
<div class="wpweb-header">	
	<div class="wpweb-logo"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="Michael TalladaElite"></div>
	<h2><?php echo esc_html__('License', 'wpwautoposter'); ?></h2>
</div>
<div class="wpweb-activation_section">
	<div id="loader">
		  <img src="<?php echo WPW_AUTO_POSTER_URL . 'includes/images/loader.gif'; ?>" alt="Loading..." />
	</div>
	<div class="wpweb-section-wrap">
		<div class="wpweb-section-header">
			<div class="wpweb-header-text">
				<h2 id="license" class="wpweb-title wpweb-icon-important <?php if( ! empty( $activation_status ) ) { echo 'active'; } ?>">
					<?php _e( 'License', 'wpwautoposter' ); ?>					
				</h2>
			</div>
			<div class="wpweb-license-container">
				<form method="post" action="">
					<input type="hidden" name="action" value="your_plugin_save_settings">
					<?php wp_nonce_field( 'your_plugin_save_settings' ); ?>
					<div class="wpweb-fields-container">
						<div class="wpweb-license-container-fieldset">
							<div class="wpweb-field">
								<div class="wp-wb-txt">
									<label for="license_key"><?php _e( 'License Key*', 'wpwautoposter' ); ?></label>
									<input type="text" id="license_key" placeholder="Enter plugin license key"  name="wpw_auto_poster_activation_code" class="wpw_auto_poster_activation_code" value="<?php if ( $activation_status ) { echo esc_attr( $activation_code ); } ?>" <?php if ( $activation_status ) { echo 'readonly'; } ?>/>
								</div>
							</div>

							<div class="wpweb-field">
								<div class="wp-wb-txt">
									<label for="email_address"><?php _e( 'Email Address*', 'woovoucher' ); ?></label>
									<input type="email" id="email_address" placeholder="Enter email address" name="wpw_auto_poster_email_address" class="wpw_auto_poster_email_address" value="<?php if ( $activation_status ) { echo esc_attr( $email_address ); } ?> " <?php if ( $activation_status ) { echo 'readonly'; } ?>/>
								</div>	
							</div>
						</div>
						<?php if ( $activation_status ) { ?>
							<div class="wpweb-license-manage-container">
								<?php
								printf(
									__( 'Congratulations, and thank you for registering your website. To manage your licenses, sign up on %s.', 'wpwautoposter' ), // phpcs:ignore WordPress.Security.EscapeOutput
									'<a href="https://updater.Michael Tallada/login/" target="_blank">' . esc_html__( 'Michael Tallada License Manager', 'wpwautoposter' ) . '</a>'
								);
								?>
							</div>
						<?php } ?>
					</div>
					<?php if ( $activation_status ) {
						$btntxt = 'Deactivate License';
					} else {
						$btntxt = 'Activate License';
					}
					submit_button( $btntxt, 'primary', 'wpw_auto_poster_button' ); ?>
				</form>
				<div class="wpweb-db-reg-howto">
					<h3 class="wpweb-db-reg-howto-heading"><?php esc_html_e( 'How To Find Your Purchase Code', 'wpwautoposter' ); ?></h3>
					<ol class="wpweb-db-reg-howto-list wpweb-db-card-text-small">
						<li>
							<?php
							printf(
								/* translators: "CodeCanyon sign in link" link. */
								__( 'Sign in to your %s. <strong>IMPORTANT:</strong> You must be signed into the same CodeCanyon account that purchased Social Auto Poster. If you are signed in already, look in the top menu bar to ensure it is the right account.', 'wpwautoposter' ), // phpcs:ignore WordPress.Security.EscapeOutput
								'<a href="https://codecanyon.net/sign_in" target="_blank">' . esc_html__( 'CodeCanyon account', 'wpwautoposter' ) . '</a>'
							);
							?>
						</li>
						<li>
							<?php
							printf(
								/* translators: "Generate A Personal Token" link. */
								__( 'Visit the %s. You should see a row for Social Auto Poster.  If you don\'t, please re-check step 1 that you are on the correct account.', 'wpwautoposter' ), // phpcs:ignore WordPress.Security.EscapeOutput
								'<a href="https://codecanyon.net/downloads" target="_blank">' . esc_html__( 'CodeCanyon downloads page', 'wpwautoposter' ) . '</a>'
							);
							?>
						</li>
						<li>
							<?php
								esc_html_e( 'Click the download button in the Social Auto Poster row.', 'wpwautoposter' )
							?>
						</li>
						<li>
							<?php
								esc_html_e( 'Select either License certificate & purchase code (PDF) or License certificate & purchase code (text). This should then download either a text or PDF file.', 'wpwautoposter' )
							?>
						</li>
						<li>
							<?php
								esc_html_e( 'Open up that newly downloaded file and copy the Item Purchase Code.', 'wpwautoposter' )
							?>
						</li>
					</ol>
				</div>
			</div>
		</div>		
	</div>
	
</div>
