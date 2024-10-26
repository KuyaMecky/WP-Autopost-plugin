<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Upgrade Class
 *
 * Handles generic Upgrade functionality and AJAX requests.
 *
 * @package Social Auto Poster
 * @since 2.9.1
 */
class Wpw_Auto_Poster_Upgrade {		
    
    public $model;

	public function __construct() {
        
        global $wpw_auto_poster_model;

        $this->model = $wpw_auto_poster_model;
	}
    
    /**
	 * Update database from exclude categories to exclude taxnomies.
     * Previously only category support and slug is stored. 
     * Now all taxonomies support and term id saved
	 *
	 * @package Social Auto Poster
	 * @since 2.9.1
	 */
    function sap_v291_upgrades() {
        
        $plugin_version = WPW_AUTO_POSTER_VERSION;

        $sap_v291_upgrades = get_option('sap_v291_upgrades');
        
        if( empty( $sap_v291_upgrades ) && version_compare( $plugin_version, '2.9.3', '<' ) ) {
            
            //get plugin options from database
            $wpw_auto_poster_options = get_option('wpw_auto_poster_options');

            $wpw_auto_poster_reposter_options   = get_option('wpw_auto_poster_reposter_options');

            $social_types = array(
                                'fb',
                                'tw',
                                'li',
                                'tb',
                                'ba',
                                'ins',
                                'pin',
                            );

            foreach ( $social_types as $key => $value) {

                $new_sap_taxonomy_opt = array();
                $new_reposter_taxonomy_opt = array();

                if( !empty( $wpw_auto_poster_options[$value.'_exclude_cats'] ) ){

                    $old_sap_values = $wpw_auto_poster_options[$value.'_exclude_cats'];

                    foreach ( $old_sap_values as $post_type => $slugs_arr ) {
                        if( !empty( $slugs_arr ) ){

                            foreach ( $slugs_arr as $slg_key => $slug ) {
                                
                                $taxonomies = get_taxonomies();

                                if( !empty( $taxonomies ) ) {

                                    foreach ( $taxonomies as $tx_key => $tax ) {

                                        $term = get_term_by('slug', $slug, $tax);

                                        if( !empty( $term ) ){
                                            $new_sap_taxonomy_opt[$post_type][] = $term->term_id;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // for reposter
                if(!empty($wpw_auto_poster_reposter_options[$value.'_post_type_cats']) ){
                    $old_reposter_values = $wpw_auto_poster_reposter_options[$value.'_post_type_cats'];

                    foreach ( $old_reposter_values as $post_type => $slugs_arr ) {
                        if( !empty( $slugs_arr ) ){

                            foreach ( $slugs_arr as $slg_key => $slug ) {
                                
                                $taxonomies = get_taxonomies();

                                if( !empty( $taxonomies ) ) {

                                    foreach ( $taxonomies as $tx_key => $tax ) {

                                        $term = get_term_by('slug', $slug, $tax);

                                        if( !empty( $term ) ){
                                            $new_reposter_taxonomy_opt[$post_type][] = $term->term_id;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if( !empty( $new_sap_taxonomy_opt ) ){
                    $wpw_auto_poster_options[$value.'_exclude_cats'] = $new_sap_taxonomy_opt;
                    update_option('wpw_auto_poster_options', $wpw_auto_poster_options);
                }

                if( !empty( $new_reposter_taxonomy_opt ) ){
                    $wpw_auto_poster_reposter_options[$value.'_post_type_cats'] = $new_reposter_taxonomy_opt;
                    update_option('wpw_auto_poster_reposter_options', $wpw_auto_poster_reposter_options);
                }
            }
            
            update_option('sap_v291_upgrades', 1);
        }
    }

    
    /**
	 * Reposter exclude taxonomies settings become blank when last_posted_page updated in 
     * main reposter poster options
     * Now, those settings are migrated as seprate options so it will not create issue
	 *
	 * @package Social Auto Poster
	 * @since 2.9.2
	 */
    function sap_v292_upgrades() {
        
        $plugin_version = WPW_AUTO_POSTER_VERSION;

        $sap_v292_upgrades = get_option('sap_v292_upgrades');
        
        if( empty( $sap_v292_upgrades ) && version_compare( $plugin_version, '2.9.3', '<' ) ) {
            
            $wpw_auto_poster_reposter_options   = get_option('wpw_auto_poster_reposter_options');
            $all_social_networks = $this->model->wpw_auto_poster_get_social_type_data();

            // Loop all the supported social networks
            foreach( $all_social_networks as $slug => $label ) {
                $last_posted_page = ( !empty( $wpw_auto_poster_reposter_options[$slug.'_last_posted_page']) ) ? $wpw_auto_poster_reposter_options[$slug.'_last_posted_page'] : 1;
                update_option( 'sap_reposter_'.$slug.'_last_posted_page', $last_posted_page);
            }

            update_option('sap_v292_upgrades', 1);
        }
    }

	/**
	 * Adding Hooks
	 *
	 * @package Social Auto Poster
	 * @since 2.9.1
	 */
	public function add_hooks() {			
        
        add_action('wp_loaded', array($this, 'sap_v291_upgrades') );  
        add_action('wp_loaded', array($this, 'sap_v292_upgrades') );
	}
}