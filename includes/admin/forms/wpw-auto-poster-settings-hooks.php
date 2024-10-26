<?php

/*********************** All Hooks Start ***************************/

// add action to add general settings tab 	-  5
// add action to add facebook settings tab 	- 10
// add action to add twitter settings tab 	- 15
// add action to add linkedin settings tab 	- 20
// add action to add tumblr settings tab 	- 25
// add action to add youtube settings tab 	- 30
// add action to add pinterest settings tab	- 35
// add action to add gmb settings tab		- 40
// add action to add reddit settings tab	- 99
// add action to add telegram settings tab - 149
// add action to add wordpress settings tab - 199
// add action to add bufferapp settings tab - 249
// add action to add instagram settings tab - 299
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_general_setting_tab', 5 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_facebook_setting_tab', 10 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_twitter_setting_tab', 15 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_linkedin_setting_tab', 20 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_tumblr_setting_tab', 25 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_pinterest_setting_tab', 35 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_googlemybusiness_setting_tab', 40 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_reddit_setting_tab', 99 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_telegram_setting_tab', 149 );
add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_medium_setting_tab', 199);

add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_instagram_setting_tab',249);

add_action( 'wpw_auto_poster_settings_panel_tab', 'wpw_auto_poster_wordpress_setting_tab',299);

do_action( 'wpw_auto_poster_settings_panel_after_tabs' );
do_action( 'wpw_auto_poster_settings_panel_tab_after_ba' );

// add action to add general settings tab content 	-  5
// add action to add facebook settings tab content 	- 10
// add action to add twitter settings tab content 	- 15
// add action to add linkedin settings tab content 	- 20
// add action to add tumblr settings tab content 	- 25
// add action to add youtube settings tab content 	- 30
// add action to add pinterest settings tab content - 35
// add action to add gmb settings tab content 		- 40
// add action to add reddit settings tab content 	- 99
// add action to add teligram settings tab content - 149
// add action to add wordpress settings tab content - 199
// add action to add bufferapp settings tab content - 249
// add action to add instagram settings tab content - 299
add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_general_setting_tab_content', 5 );
add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_facebook_setting_tab_content', 10 );

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_twitter_setting_tab_content', 15 );
add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_linkedin_setting_tab_content', 20 );

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_tumblr_setting_tab_content', 25 );
add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_pinterest_setting_tab_content', 35 );

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_googlemybusiness_setting_tab_content', 40 );

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_reddit_setting_tab_content', 99 );

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_telegram_setting_tab_content',149);
add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_medium_setting_tab_content',199);

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_instagram_setting_tab_content',249);

add_action( 'wpw_auto_poster_settings_panel_tab_content', 'wpw_auto_poster_wordpress_setting_tab_content',299);


do_action( 'wpw_auto_poster_settings_panel_after_tabs_content' );
do_action( 'wpw_auto_poster_settings_panel_tab_content_after_ba' );

/*********************** All Hooks End ***************************/