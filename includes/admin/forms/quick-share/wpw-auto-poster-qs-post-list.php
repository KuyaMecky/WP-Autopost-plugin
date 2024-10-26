<?php
// Exit if accessed directly
if( !defined('ABSPATH') ) exit;

/**
 * Manage quick posts schedules
 * The html markup for the quick post schedules list
 *
 * @package Social Auto Poster
 * @since 3.9.2
 */

global $wpw_auto_poster_quick_share;
$quickShare		= $wpw_auto_poster_quick_share;
$instant_post	= $quickShare->wpw_auto_poster_get_quick_share_posts();
$format			= get_option( 'date_format' ) . ' ' . get_option('time_format');
$prefix			= WPW_AUTO_POSTER_META_PREFIX;

$activeTab = !empty( $_GET['tab'] ) ? $_GET['tab'] : 'published'; ?>

<div class="sap-tabing-main-wrapper">
	<div class="sap-tab-nav-wrap">
		<ul>
			 <li class="tab-switcher" data-tab-index="0" tabindex="0">
			 	<a href="javascript:void(0);" class="nav-tab <?php if( $activeTab == 'published' ) echo 'nav-tab-active'; ?>" attr-tab="published" id="published"><?php esc_html_e( 'Published', 'wpwautoposter' ); ?></a>
			 </li>
			 
			<li class="tab-switcher" data-tab-index="1" tabindex="0">
				<a href="javascript:void(0);" class="nav-tab <?php if( $activeTab == 'scheduled' ) echo 'nav-tab-active'; ?>" attr-tab="scheduled" id="scheduled"><?php esc_html_e( 'Scheduled', 'wpwautoposter' ); ?></a>
			</li>
		</ul>
	</div>

<div class="sap-tab-content-wrap" id="allTabsContainer">
	<div class="tab-container" data-tab-index="0" id="published" <?php if( $activeTab != 'published' ) echo 'style="display:none;"'; ?>>
		<div class="wpw-auto-poster-qs-list-wrap wpw-auto-poster-card">
			<div class="wpw-auto-poster-table-wrap">
				<?php if (!empty($instant_post) > 0) { ?>
					<select id='searchByGender' class="searchByGender">
						<option value=''><?php esc_html_e( 'Bulk Action', 'wpwautoposter' ); ?></option>
						<option value='delete'><?php esc_html_e( 'Delete', 'wpwautoposter' ); ?></option>
					</select>
				<?php } ?>
				<table class="wpw-auto-poster-datatable wpw-auto-poster-table">
					<thead>
						<tr>
							<th class="chk" data-sortable="false"><input type="checkbox" class="quickpost-select-all" /></th>
							<th><?php esc_html_e( 'Message', 'wpwautoposter' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wpwautoposter' ); ?></th>
							<th data-sortable="false"><?php esc_html_e( 'Action', 'wpwautoposter' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th class="chk" data-sortable="false"><input type="checkbox" class="quickpost-select-all" /></th>
							<th><?php esc_html_e( 'Message', 'wpwautoposter' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wpwautoposter' ); ?></th>
							<th data-sortable="false"><?php esc_html_e( 'Action', 'wpwautoposter' ); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if( $instant_post->have_posts() ):
							while( $instant_post->have_posts() ): 
								$instant_post->the_post(); 
								$current_id = get_the_ID(); 
								$post_title = get_the_title();
								$get_published = get_post_meta($current_id,'_wpweb_quick_schedule');
								if( strlen( wp_strip_all_tags($post_title) ) > 250 ){
									$listing_content = substr(esc_html($post_title), 0, 250) . '...';
								}else{
									$listing_content = esc_html($post_title);
								}
								if(isset($get_published) && empty($get_published)){ 
									?>
									<tr>
										<td class="chk">
											<input type="checkbox" name="post_id[]" value="<?php the_ID(); ?>" />
										</td>
										<td>
											<?php echo '<a href="?page=wpw-auto-poster-quick-share&action=preview&id='.get_the_ID().'">'. $listing_content .'</a>'; ?>
										</td>
										<td><?php echo get_the_date( $format ); ?></td>
										<td><?php echo apply_filters ( 'wpweb_auto_poster_delete_action', sprintf('<a class="wpw-auto-poster-post-title-delete wpw-auto-poster-logs-delete" href="?page=%s&action=%s&post_id[]=%s" title="'.esc_html__('Delete', 'wpwautoposter').'"><span class="dashicons dashicons-dismiss"></span></a>','wpw-auto-poster-quick-share','delete', $current_id ) ); ?></td>
									</tr>
									<?php
								}
							endwhile;
						endif;
						wp_reset_postdata(); ?>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="tab-container" data-tab-index="1" id="scheduled" <?php if( $activeTab != 'scheduled' ) echo 'style="display:none;"'; ?>>
		<div class="wpw-auto-poster-qs-list-wrap wpw-auto-poster-card">
			<div class="wpw-auto-poster-table-wrap">
				<?php if (!empty($instant_post) > 0) { ?>
					<select id='searchByGender' class="searchByGender">
						<option value=''><?php esc_html_e( 'Bulk Action', 'wpwautoposter' ); ?></option>
						<option value='delete'><?php esc_html_e( 'Delete', 'wpwautoposter' ); ?></option>
					</select>
				<?php } ?>
				<table class="wpw-auto-poster-schedule-datatable wpw-auto-poster-table">
					<thead>
						<tr>
							<th class="chk" data-sortable="false"><input type="checkbox" class="quickpost-select-all" /></th>
							<th class="qs-schedule-message"><?php esc_html_e( 'Message', 'wpwautoposter' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wpwautoposter' ); ?></th>
							<th data-sortable="false"><?php esc_html_e( 'Action', 'wpwautoposter' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th class="chk" data-sortable="false"><input type="checkbox" class="quickpost-select-all" /></th>
							<th><?php esc_html_e( 'Message', 'wpwautoposter' ); ?></th>
							<th><?php esc_html_e( 'Date', 'wpwautoposter' ); ?></th>
							<th data-sortable="false"><?php esc_html_e( 'Action', 'wpwautoposter' ); ?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if( $instant_post->have_posts() ):
							while( $instant_post->have_posts() ): 
								$instant_post->the_post(); 
								$current_id = get_the_ID();
								$post_title = get_the_title();
								$get_schedule = get_post_meta($current_id,'_wpweb_quick_schedule',true);
								$scheduled_date_time = get_post_meta($current_id,$prefix.'share_schedule',true);
								if(isset($get_schedule) && !empty($get_schedule)){
									?>
									<tr>
										<td class="chk">
											<input type="checkbox" name="post_id[]" value="<?php the_ID(); ?>" />
										</td>
										<td>
											<?php echo '<a href="?page=wpw-auto-poster-quick-share&tab=scheduled&action=preview&id='.get_the_ID().'">'. substr($post_title, 0, 250) .'...</a>'; ?>
										</td>
										<td><?php echo date( $format, $scheduled_date_time ); ?></td>
										<td><?php echo apply_filters ( 'wpweb_auto_poster_delete_action', sprintf('<a class="wpw-auto-poster-post-title-delete wpw-auto-poster-logs-delete" href="?page=%s&action=%s&post_id[]=%s" title="'.esc_html__('Delete', 'wpwautoposter').'"><span class="dashicons dashicons-dismiss"></span></a>','wpw-auto-poster-quick-share','delete',$current_id ) ); ?>
										</td>
									</tr>
									<?php
								}
							endwhile;
						endif;
						wp_reset_postdata(); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</div>


</div>




