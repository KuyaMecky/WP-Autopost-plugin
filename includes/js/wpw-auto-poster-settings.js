'use strict';

jQuery(document).ready(function ($) {

    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
    if( typeof postboxes !== 'undefined' ) {
       postboxes.add_postbox_toggles('toplevel_page_wpw-auto-poster-settings');
    }
    if( $('body.toplevel_page_wpw-auto-poster-settings').length || $('body.social-auto-poster_page_wpw-auto-poster-reposter').length ){
        $( "#custom_post_type_templates" ).tabs();
        $( "#custom_post_type_templates_tw" ).tabs();
        $( "#custom_post_type_templates_li" ).tabs();
        $( "#custom_post_type_templates_tb" ).tabs();
        $( "#custom_post_type_templates_ba" ).tabs();
        $( "#custom_post_type_templates_pin").tabs();
        $( "#custom_post_type_templates_wp" ).tabs();
        $( "#custom_post_type_templates_gmb").tabs();
        $( "#custom_post_type_templates_rd" ).tabs();
        $( "#custom_post_type_templates_tele").tabs();
        $( "#custom_post_type_templates_md" ).tabs();
        $( "#custom_post_type_templates_insta").tabs();


        if( $( "#custom_post_type_templates_ins" ).length ){
            $( "#custom_post_type_templates_ins" ).tabs();
        }
        if( $( "#custom_post_type_templates_yt" ).length ){
            $( "#custom_post_type_templates_yt" ).tabs();
        }
        if( $( "#custom_post_type_templates_insta" ).length ){
            $( "#custom_post_type_templates_insta" ).tabs();
        }

        $('.wpw-auto-poster-cats-tags-select').select2({
            placeholder: WpwAutoPosterSettings.option_label,
            width : '40%'
        });
        
        $('.ajax-taxonomy-search').select2({
            placeholder: WpwAutoPosterSettings.option_label,
            width : '40%',
            minimumInputLength: 3,
            ajax: { 
                url: WpwAutoPosterSettings.ajaxurl,
                method: 'POST',
                dataType: 'json',
                delay: 500,
                data: function (params) {
                 return {
                   searchTerm: params.term,
                   'action':'wpw_auto_poster_taxonomy_ajax_search',
                   
                 };  
                },
                processResults: function (response) {
                  return {
                     results: response.results
                  };
                },
                cache: true
               }
        });

    	

        $('.wpw-auto-poster-users-acc-select').select2({
            placeholder : WpwAutoPosterSettings.accounts_placeholder,
            width       : '100%'
        });

        $('.wpw-auto-poster-wp-sites-select').select2({
            placeholder : WpwAutoPosterSettings.accounts_placeholder,
            width       : '100%'
        });

        $('.wpw-auto-poster-tele-chats-select').select2({
            placeholder : WpwAutoPosterSettings.accounts_placeholder,
            width       : '100%'
        });
    }

    //twitter template
    $(document).on('change', '.tw_tweet_template', function () {
        if ($('.tw_tweet_template').val() == 'custom') {
            $('.custom_template').slideDown('slow');
            sap_tw_custom_msg_template_toggle();
        } else {
            $('.custom_template').slideUp('slow');
        }
        woo_vou_toggle_tweet_image();
    });

    //url shortener

    $(document).on('change','.gmb_url_shortener, .fb_url_shortener, .tw_url_shortener, .li_url_shortener, .tb_url_shortener, .dc_url_shortener, .ff_url_shortener, .ba_url_shortener, .ins_url_shortener, .pin_url_shortener, .yt_url_shortener, .wp_url_shortener, .rd_url_shortener, .tele_url_shortener,.md_url_shortener,.insta_url_shortener', function () {

        var container = $(this).attr('data-content');
        
        //check shortner value is bitly
        if ($(this).val() == 'bitly') {
            $('.' + container + '_setting_input_bitly').slideDown('fast');
        } else {
            $('.' + container + '_setting_input_bitly').hide();
        }

        //check shortner value is shorte.st
        if ($(this).val() == 'shorte.st') {
            $('.' + container + '_setting_input_shortest').slideDown('fast');
        } else {
            $('.' + container + '_setting_input_shortest').hide();
        }
        
        if( $(this).val() === 'wordpress' ) {
            $('#row-'+ container +'-wp-pretty-url').show();
        } else{
            $('#row-'+ container +'-wp-pretty-url').hide();
        }
    });

    if( !$('body.social-auto-poster_page_wpw-auto-poster-manage-schedules').length ){
        //  When user clicks on tab, this code will be executed
        $(document).on("click", ".nav-tab-wrapper a", function () {
            //  First remove class "active" from currently active tab
            $(".nav-tab-wrapper a").removeClass('nav-tab-active');

            //  Now add class "active" to the selected/clicked tab
            $(this).addClass("nav-tab-active");

            //  Hide all tab content
            $(".wpw-auto-poster-tab-content").hide();

            //  Here we get the href value of the selected tab
            var selected_tab = $(this).attr("href");

            //  Show the selected tab content

            $(selected_tab).show();
            var tab_title = $(this).attr("attr-tab");
            $(".wpw-auto-poster-tab-content").removeClass('wpw-auto-poster-selected-tab');
            $('#wpw_auto_poster_selected_tab').val(tab_title);

            //  At the end, we add return false so that the click on the link is not executed
            return false;
        });
    }

    //Image uploader
    $(document).on("click", ".wpw-auto-poster-uploader-button", function () {

        var imgfield, imgidfield;
        imgfield = $(this).prev('input').attr('id');
        imgidfield = $(this).prev().prev('input').attr('id');

        var main_button = $(this);

        // check for media uploader
        if( typeof wp == "undefined" || WpwAutoPosterSettings.new_media_ui != '1' ) {

            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

            window.original_send_to_editor = window.send_to_editor;
            window.send_to_editor = function (html) {

                if( imgfield ) {
                    var mediaurl = $('img', html).attr('src');
                    $('#' + imgfield).val(mediaurl);
                    tb_remove();
                    imgfield = '';
                } else {
                    window.original_send_to_editor(html);
                }
            };
            return false;

        } else {

            var file_frame;

            //new media uploader
            var button = $(this);

            // If the media frame already exists, reopen it.
            if( file_frame ) {
                file_frame.open();
                return;
            }

            if( $(this).hasClass('youtube') ){
                
                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    frame: 'post',
                    state: 'insert',
                    multiple: false,  // Set to true to allow multiple files to be selected
                    library: {
                    type: [ 'video']
                    },
                });
            } else{

                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    frame: 'post',
                    state: 'insert',
                    multiple: false,  // Set to true to allow multiple files to be selected
                    library: {
                    type: [ 'image']
                    },
                });
            }

            file_frame.on('menu:render:default', function (view) {
                // Store our views in an object.
                var views = {};

                // Unset default menu items
                view.unset('library-separator');
                view.unset('gallery');
                view.unset('featured-image');
                view.unset('embed');

                // Initialize the views in our view object.
                view.set(views);
            });

            // When an image is selected, run a callback.
            file_frame.on('insert', function () {
                // Get selected size from media uploader
                var selected_size = $('.attachment-display-settings .size').val();

                var selection = file_frame.state().get('selection');
                selection.each(function (attachment, index) {
                    attachment = attachment.toJSON();

                    // Selected attachment url from media uploader
                    if( typeof selected_size !== 'undefined' ){
                        var attachment_id = attachment.id;
                        var attachment_url = attachment.sizes[selected_size].url;
                    }else{
                        var attachment_id = attachment.id;
                        var attachment_url = attachment.url;
                    }

                    if( ( attachment.type == 'image' && !main_button.hasClass('youtube')) || ( attachment.type == 'video' && main_button.hasClass('youtube') )  ) {
                        if (index == 0) {
                            // place first attachment in field
                            $('#' + imgidfield).val(attachment_id);
                            $('#' + imgfield).val(attachment_url);

                        } else {
                            $('#' + imgidfield).val(attachment_id);
                            $('#' + imgfield).val(attachment_url);
                        }
                    }
                });
            });

            // Finally, open the modal
            file_frame.open();
        }
    });

    //Image uploader
    $(document).on("click", ".wpw-auto-poster-quick-uploader-button", function () {

        var imgfield, imgidfield;
       	var wrapObj		= $(this).parent();
        var imgfield	= $(this).prev('input').attr('id');
        var imgidfield	= $(this).prev().prev('input').attr('id');

        var sap_media_type = 'image';
        if($(this).hasClass('video')){
            sap_media_type = 'video';
        }

        var main_button = $(this);

        // check for media uploader
        if( typeof wp == "undefined" || WpwAutoPosterSettings.new_media_ui != '1' ) {

            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

            window.original_send_to_editor = window.send_to_editor;
            window.send_to_editor = function (html) {

                if( imgfield ) {
                    var mediaurl = $('img', html).attr('src');
                    $('#' + imgfield).val(mediaurl);
                    tb_remove();
                    imgfield = '';
                } else {
                    window.original_send_to_editor(html);
                }
            };
            return false;

        } else {

            var file_frame;

            //new media uploader
            var button = $(this);

            // If the media frame already exists, reopen it.
            if( file_frame ) {
                file_frame.open();
                return;
            }

        	// Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                frame: 'post',
                state: 'insert',
                multiple: false,  // Set to true to allow multiple files to be selected
                library: {
                type: [ sap_media_type]
                },
            });

            file_frame.on('menu:render:default', function (view) {
                // Store our views in an object.
                var views = {};

                // Unset default menu items
                view.unset('library-separator');
                view.unset('gallery');
                view.unset('featured-image');
                view.unset('embed');

                // Initialize the views in our view object.
                view.set(views);
            });

            // When an image is selected, run a callback.
            file_frame.on('insert', function () {
                // Get selected size from media uploader
                var selected_size = $('.attachment-display-settings .size').val();

                var selection = file_frame.state().get('selection');
                selection.each(function (attachment, index) {
                    attachment = attachment.toJSON();

                    // Selected attachment url from media uploader
                    if( typeof selected_size !== 'undefined' ){
                        var attachment_id = attachment.id;
                        var attachment_url = attachment.sizes[selected_size].url;
                    }else{
                        var attachment_id = attachment.id;
                        var attachment_url = attachment.url;
                    }

                    $('#' + imgidfield).val(attachment_id);
                    $('#' + imgfield).val(attachment_url);

                    console.log(attachment);

                    // remove preview image first if there
                    wrapObj.find( '.preview-img' ).remove();
                    wrapObj.find( '.remove-preview-image' ).remove();

                    // append preview image or video
                    if( attachment.type == 'video' ) {
						var video = $('<video />', {
							class: 'preview-img',
							src: attachment_url,
							type: 'video/mp4',
							controls: false
						} );

						video.appendTo( wrapObj );
    					
                    } else {
                        var img = $('<img class="preview-img">');
                        img.attr('src', attachment_url);
                        img.appendTo( wrapObj );
                    }

					// append remove image btn
					var rmbtn = $( '<span class="remove-preview-image"><i class="dashicons dashicons-no-alt"></i></span>' );
					rmbtn.appendTo( wrapObj );
				} );
			});

            // Finally, open the modal
            file_frame.open();
        }
    });

    // remove preview image
    $( document ).on( 'click', '.wpw-auto-poster-uploader .remove-preview-image', function() {
        var wrapObj = $(this).parent();

        wrapObj.find( '.preview-img' ).remove();
        wrapObj.find( 'input[type="hidden"]' ).val('');
        $(this).remove();

        return false;
    } );

    //reset confirmation
    $(document).on("click", ".wpw-auto-poster-reset-button", function () {

        var ans;
        ans = confirm(WpwAutoPosterSettings.confirmmsg);

        if (ans) {
            return true;
        } else {
            return false;
        }

    });

    //posted logs delete confirmation
    $(document).on("click", ".wpw-auto-poster-logs-delete", function () {

        var ans;
        ans = confirm(WpwAutoPosterSettings.deleteconfirmmsg);

        if (ans) {
            return true;
        } else {
            return false;
        }

    });

    //add more account details for facebook
    $(document).on('click', '.wpw-auto-poster-add-more-fb-account', function () {
        var $first = $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last');
        var last_row_id = parseInt($first.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = $first.clone();

        clone_row.insertAfter($first).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last .wpw-auto-poster-facebook-app-id').attr('name', 'wpw_auto_poster_options[facebook_keys][' + last_row_id + '][app_id]').val('');
        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last .wpw-auto-poster-facebook-app-secret').attr('name', 'wpw_auto_poster_options[facebook_keys][' + last_row_id + '][app_secret]').val('');
        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last .fb-oauth-url').val('');
        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last .copy-clipboard').remove();        
        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last .wpw-auto-poster-facebook-remove').show();
        $(this).parents('.wpw-auto-poster-facebook-settings').find('.wpw-auto-poster-facebook-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });


    //delete account details for facebook
    $(document).on('click', '.wpw-auto-poster-delete-fb-account', function () {
        var jQueryparent = $(this).parents('.wpw-auto-poster-facebook-account-details');
        jQueryparent.remove();

        return false;
    });


    // copy Valid oauth url to clipboard
    $( document).on('click', '.copy-clipboard', function(){

        var app_id = $(this).siblings();
        if( app_id != ""){
            app_id.select();
            document.execCommand("Copy");
            $( this ).parent().append( '<div class="wpw-auto-poster-fade-message">'+ WpwAutoPosterSettings.copy_message +'</div>' );
            $( ".wpw-auto-poster-fade-message" ).fadeOut( 3000, function() {
                $( '.wpw-auto-poster-fade-message' ).remove();
            });
        }
    });

    //add more account details for twitter
    $(document).on('click', '.wpw-auto-poster-add-more-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;
        jQueryfirst.clone().insertAfter(jQueryfirst).show();
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last .wpw-auto-poster-twitter-consumer-key').attr('name', 'wpw_auto_poster_options[twitter_keys][' + last_row_id + '][consumer_key]').val('');
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last .wpw-auto-poster-twitter-consumer-secret').attr('name', 'wpw_auto_poster_options[twitter_keys][' + last_row_id + '][consumer_secret]').val('');
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last .wpw-auto-poster-twitter-oauth-token').attr('name', 'wpw_auto_poster_options[twitter_keys][' + last_row_id + '][oauth_token]').val('');
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last .wpw-auto-poster-twitter-oauth-secret').attr('name', 'wpw_auto_poster_options[twitter_keys][' + last_row_id + '][oauth_secret]').val('');
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last .wpw-auto-poster-twitter-remove').show();
        $(this).parents('.wpw-auto-poster-twitter-settings').find('.wpw-auto-poster-twitter-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    //delete account details for twitter
    $(document).on('click', '.wpw-auto-poster-delete-account', function () {

        var jQueryparent = $(this).parents('.wpw-auto-poster-twitter-account-details');
        jQueryparent.remove();

        return false;
    });

    //add more account details for instagram
    $(document).on('click', '.wpw-auto-poster-add-more-ins-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-instagram-settings').find('.wpw-auto-poster-instagram-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-instagram-settings').find('.wpw-auto-poster-instagram-account-details:last .wpw-auto-poster-instagram-username').attr({name: 'wpw_auto_poster_options[instagram_keys][' + last_row_id + '][username]', readonly: false}).val('');
        $(this).parents('.wpw-auto-poster-instagram-settings').find('.wpw-auto-poster-instagram-account-details:last .wpw-auto-poster-instagram-password').attr({name: 'wpw_auto_poster_options[instagram_keys][' + last_row_id + '][password]', readonly: false}).val('');
        $(this).parents('.wpw-auto-poster-instagram-settings').find('.wpw-auto-poster-instagram-account-details:last .wpw-auto-poster-instagram-remove').show();
        $(this).parents('.wpw-auto-poster-instagram-settings').find('.wpw-auto-poster-instagram-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    //delete account details for instagram
    $(document).on('click', '.wpw-auto-poster-delete-ins-account', function () {

        var jQueryparent = $(this).parents('.wpw-auto-poster-instagram-account-details');
        jQueryparent.remove();

        return false;
    });

    //delete account details for instagram
    $(document).on('click', '.wpw-auto-poster-delete-yt-account', function () {
        var jQueryparent = $(this).parents('.wpw-auto-poster-youtube-account-details');
        jQueryparent.remove();

        return false;
    });

    //add more account details for youtube
    $(document).on('click', '.wpw-auto-poster-add-more-yt-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last .wpw-auto-poster-youtube-app-id').attr({name: 'wpw_auto_poster_options[yt_keys][' + last_row_id + '][app_id]', readonly: false}).val('');
        $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last .wpw-auto-poster-youtube-app-secret').attr({name: 'wpw_auto_poster_options[yt_keys][' + last_row_id + '][app_secret]', readonly: false}).val('');
        $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last .yt-oauth-url').val('');
        $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last .wpw-auto-poster-delete-yt-account').show();
        $(this).parents('.wpw-auto-poster-youtube-settings').find('.wpw-auto-poster-youtube-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    // copy Valid oauth url to clipboard
    $( document).on('click', '.yt-copy-clipboard', function(){
        var app_id = $(this).siblings();
        if( app_id != ""){
            app_id.select();
            document.execCommand("Copy");
            $( this ).parent().append( '<div class="wpw-auto-poster-fade-message">'+ WpwAutoPosterSettings.copy_message +'</div>' );
            $( ".wpw-auto-poster-fade-message" ).fadeOut( 3000, function() {
                $( '.wpw-auto-poster-fade-message' ).remove();
            });
        }
    });

    //add more account details for pinterest
    $(document).on('click', '.wpw-auto-poster-add-more-pin-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-pinterest-settings').find('.wpw-auto-poster-pinterest-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-pinterest-settings').find('.wpw-auto-poster-pinterest-account-details:last .wpw-auto-poster-pinterest-app-id').attr('name', 'wpw_auto_poster_options[pinterest_keys][' + last_row_id + '][app_id]').val('');
        $(this).parents('.wpw-auto-poster-pinterest-settings').find('.wpw-auto-poster-pinterest-account-details:last .wpw-auto-poster-pinterest-app-secret').attr('name', 'wpw_auto_poster_options[pinterest_keys][' + last_row_id + '][app_secret]').val('');
        $(this).parents('.wpw-auto-poster-pinterest-settings').find('.wpw-auto-poster-pinterest-account-details:last .wpw-auto-poster-pinterest-remove').show();
        $(this).parents('.wpw-auto-poster-pinterest-settings').find('.wpw-auto-poster-pinterest-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    //delete account details for pinterest
    $(document).on('click', '.wpw-auto-poster-delete-pin-account', function () {
        var jQueryparent = $(this).parents('.wpw-auto-poster-pinterest-account-details');
        jQueryparent.remove();

        return false;
    });

    //add more account details for linkedin
    $(document).on('click', '.wpw-auto-poster-add-more-li-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last .wpw-auto-poster-linkedin-app-id').attr('name', 'wpw_auto_poster_options[linkedin_keys][' + last_row_id + '][app_id]').val('');
        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last .wpw-auto-poster-linkedin-app-secret').attr('name', 'wpw_auto_poster_options[linkedin_keys][' + last_row_id + '][app_secret]').val('');
        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last .li-oauth-url').val('');
        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last .copy-clipboard').remove(); 
        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last .wpw-auto-poster-linkedin-remove').show();
        $(this).parents('.wpw-auto-poster-linkedin-settings').find('.wpw-auto-poster-linkedin-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    //delete account details for linkedin
    $(document).on('click', '.wpw-auto-poster-delete-li-account', function () {
        var jQueryparent = $(this).parents('.wpw-auto-poster-linkedin-account-details');
        jQueryparent.remove();
        return false;
    });

    //add more account details for telegram
    $(document).on('click', '.wpw-auto-poster-add-more-tele-account', function () {

        var jQueryfirst = $(this).parents('.wpw-auto-poster-telegram-settings').find('.wpw-auto-poster-telegram-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();

        $(this).parents('.wpw-auto-poster-telegram-settings').find('.wpw-auto-poster-telegram-account-details:last .wpw-auto-poster-telegram-boat').attr('name', 'wpw_auto_poster_options[telegram_keys][' + last_row_id + '][boat]').val('');
        $(this).parents('.wpw-auto-poster-telegram-settings').find('.wpw-auto-poster-telegram-account-details:last .wpw-auto-poster-telegram-token').attr('name', 'wpw_auto_poster_options[telegram_keys][' + last_row_id + '][token]').val('');
        $(this).parents('.wpw-auto-poster-telegram-settings').find('.wpw-auto-poster-telegram-account-details:last .wpw-auto-poster-telegram-remove').show();
        $(this).parents('.wpw-auto-poster-telegram-settings').find('.wpw-auto-poster-telegram-account-details:last').attr('data-row-id', last_row_id);
        return false;
    } );

    //delete account details for telegram
    $(document).on('click', '.wpw-auto-poster-delete-tele-account', function () {
        var jQueryparent = $(this).parents('.wpw-auto-poster-telegram-account-details');
        jQueryparent.remove();
        return false;
    });

    // copy Valid oauth url to clipboard for linkedin
    $( document).on('click', '.copy-clipboard', function(){
        var app_id = $(this).data('appid');
        copy_board = $('#li-oauth-url-'+app_id);
        var oauth_url = copy_board.val();
        if( oauth_url != ""){
            copy_board.select();
            document.execCommand("Copy");
            $( this ).parent().append( '<div class="wpw-auto-poster-fade-message">'+ WpwAutoPosterSettings.copy_message +'</div>' );
            $( ".wpw-auto-poster-fade-message" ).fadeOut( 3000, function() {
                $( '.wpw-auto-poster-fade-message' ).remove();
            });
        }
    });

    //add more account details for tumblr
    $(document).on('click', '.wpw-auto-poster-add-more-tb-account', function () {
        var jQueryfirst = $(this).parents('.wpw-auto-poster-tumblr-settings').find('.wpw-auto-poster-tumblr-account-details:last');
        var last_row_id = parseInt(jQueryfirst.attr('data-row-id'));
        last_row_id = last_row_id + 1;

        var clone_row = jQueryfirst.clone();

        clone_row.insertAfter(jQueryfirst).show();
        clone_row.find('.wpw-grant-reset-data').html('');

        $(this).parents('.wpw-auto-poster-tumblr-settings').find('.wpw-auto-poster-tumblr-account-details:last .wpw-auto-poster-tumblr-app-id').attr('name', 'wpw_auto_poster_options[tumblr_keys][' + last_row_id + '][consumer_key]').val('');
        $(this).parents('.wpw-auto-poster-tumblr-settings').find('.wpw-auto-poster-tumblr-account-details:last .wpw-auto-poster-tumblr-app-secret').attr('name', 'wpw_auto_poster_options[tumblr_keys][' + last_row_id + '][consumer_secret]').val('');
        $(this).parents('.wpw-auto-poster-tumblr-settings').find('.wpw-auto-poster-tumblr-account-details:last .wpw-auto-poster-tumblr-remove').show();
        $(this).parents('.wpw-auto-poster-tumblr-settings').find('.wpw-auto-poster-tumblr-account-details:last').attr('data-row-id', last_row_id);
        return false;
    });

    //delete account details for tumblr
    $(document).on('click', '.wpw-auto-poster-delete-tb-account', function () {

        var jQueryparent = $(this).parents('.wpw-auto-poster-tumblr-account-details');
        jQueryparent.remove();

        return false;
    });


    //on click of view details from posted logs list
    $(document).on("click", ".wpw-auto-poster-meta-view-details", function () {

        var popupcontent = $(this).parent().find('.wpw-auto-poster-popup-content');
        popupcontent.show();
        $(this).parent().find('.wpw-auto-poster-popup-overlay').show();
        $('html, body').animate({scrollTop: popupcontent.offset().top - 80}, 500);

    });

    //on click of close button or overlay
    $(document).on("click", ".wpw-auto-poster-popup-overlay, .wpw-auto-poster-close-button", function () {

        $('.wpw-auto-poster-popup-content').hide();
        $('.wpw-auto-poster-popup-overlay').hide();
    });

    // apply chosen for posting logs
    $(".wpw-auto-poster-form select").each(function () {
        $(this).css('width', '300px').chosen({search_contains: true});
    });

    $(document).on('change', '.wpw-auto-poster-schedule-option', function () {
        var schedule = $(this).val();

        $('.wpw-auto-poster-custom-schedule-wrap').hide();
        if (schedule == 'daily') {

            $('.wpw-auto-poster-custom-schedule-wrap').show();
        }       

        if ($('#wpw_auto_poster_random_posting').is(':checked')) {
            $('.wpw-auto-poster-schedule-time').hide();
        }

        // Show / hide schedule limit option
        $('.wpw-auto-poster-schedule-limit').show();
        if ( schedule == '' || schedule == 'weekly' ) {
            $('.wpw-auto-poster-schedule-limit').hide();
            $('#wpw-auto-poster-schedule-order-row').hide();
            $('#wpw-auto-poster-schedule-days').hide();
        } else{
            $('#wpw-auto-poster-schedule-order-row').show();
            $('#wpw-auto-poster-schedule-days').show();
        }

        // Code to hide and unhide custom minutes box 
        if( schedule == 'wpw_custom_mins' ){
            $('#wpw-auto-poster-custom-minute-box').show();
            $('.wpw-auto-poster-custom-minute-box').show();
        } else{
            $('#wpw-auto-poster-custom-minute-box').hide();
            $('.wpw-auto-poster-custom-minute-box').hide();
        }

        /**Twice daily*/
        $('.wpw-auto-poster-custom-twice-schedule-wrap').hide();
        if (schedule == 'twicedaily') {
            $('.wpw-auto-poster-custom-twice-schedule-wrap').show();
        }

        if ($('#wpw_auto_poster_twice_random_posting').is(':checked')) {
            $('.wpw-auto-poster-schedule-twice-time').hide();
        }

    });

    // Posting type radio button
    $(document).on('click', '.wpw-auto-poster-random-posting', function () {
        if ($(this).val() == 1) {
            $('.wpw-auto-poster-schedule-time').hide();
        } else {
            $('.wpw-auto-poster-schedule-time').show();
        }
    });

    // Posting type twice daily radio button
    $(document).on('click', '.wpw-auto-poster-twice-random-posting', function () {
        if ($(this).val() == 1) {
            $('.wpw-auto-poster-schedule-twice-time').hide();
        } else {
            $('.wpw-auto-poster-schedule-twice-time').show();
        }
    });

    $(document).on('change', '#wpw_auto_poster_li_type_post_method', function () {

        $(this).parent().parent().find('.wpw-auto-poster-li-posting-wrap').hide();
        var posting_type = $(this).val();
        $(this).parent().parent().find('.wpw-auto-poster-li-' + posting_type + '-posting').show();
    });
    
    // function to toggle Tweet image
    function woo_vou_toggle_tweet_image() {
        if( $("input[name='wpw_auto_poster_options[tw_disable_image_tweet]']").is(':checked') ) {
            $(".wpw_sap_tw_tweet_img").hide();            
        } else {
            $(".wpw_sap_tw_tweet_img").show();
            if($('.tw_tweet_template').val() == 'custom') {
                sap_tw_custom_msg_template_toggle();
            }
        }
    }

    // Setting page onload show/hide Tweet image if Disable Image posting checked
    woo_vou_toggle_tweet_image();    
    $(document).on('click', "input[name='wpw_auto_poster_options[tw_disable_image_tweet]']", function() {
        woo_vou_toggle_tweet_image();
    });

    // AJAX on page load to get categories based on post type selected
    wpw_auto_post_load_cat('no');

    // AJAX when post type is changed to get categories based on post type selected
    $(document).on('change', '#wpw_auto_poster_post_type', function () {
    	wpw_auto_post_load_cat('yes');
    });

    // Function to fetch categories from post type
    function wpw_auto_post_load_cat(open){
    	// Get post type value
    	var post_type_val = $('#wpw_auto_poster_post_type').val();

    	// If post type value is not empty
    	if($.trim(post_type_val)){
    		// Create data
			var data = {
				action			: 'wpw_auto_poster_get_category',
				post_type_val	: post_type_val,
				sel_category_id	: WpwAutoPosterSettings.sel_category_id
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {

				$('#wpw_auto_poster_cat_id').html(response); // Append response to select box
        		$('#wpw_auto_poster_cat_id').trigger("chosen:updated"); // Trigger change event for adding data in chosen select
        		if(open == 'yes') { // If we need to open the select box
        			$('#wpw_auto_poster_cat_id').trigger('chosen:open'); // Trigger event to open chosen select
        		}
			});
    	}
    }

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_facebook_for][]']", function () {
        getCheckedPostType('facebook','fb');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_twitter_for][]']", function () {
        getCheckedPostType('twitter','tw');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_linkedin_for][]']", function () {
        getCheckedPostType('linkedin','li');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_tumblr_for][]']", function () {
        getCheckedPostType('tumblr','tb');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_bufferapp_for][]']", function () {
        getCheckedPostType('bufferapp','ba');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_instagram_for][]']", function () {
        getCheckedPostType('instagram','ins');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_youtube_for][]']", function () {
        getCheckedPostType('youtube','yt');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_pinterest_for][]']", function () {
        getCheckedPostType('pinterest','pin');
    });

    $(document).on('change', "input[name='wpw_auto_poster_options[enable_wordpress_for][]']", function () {
        getCheckedPostType('wordpress','wp');
    });
    
    $(document).on('change', "input[name='wpw_auto_poster_options[enable_googlemybusiness_for][]']", function () {
        getCheckedPostType('googlemybusiness','gmb');
    });
    $(document).on('change', "input[name='wpw_auto_poster_options[enable_reddit_for][]']", function () {
        getCheckedPostType('reddit','rd');
    });
     $(document).on('change', "input[name='wpw_auto_poster_options[enable_telegram_for][]']", function () {
        getCheckedPostType('telegram','tele');
    });
    $(document).on('change', "input[name='wpw_auto_poster_options[enable_medium_for][]']", function () {
       getCheckedPostType('medium','md');
    });
    $(document).on('change', "input[name='wpw_auto_poster_options[enable_insta_for][]']", function () {
       getCheckedPostType('insta','insta');
    });
   

    $(document).on('change', "select[name='wpw_auto_poster_reposter_options[schedule_posting_order]']", function () {
        if( $(this).val() == 'rand' ){
            $("select[name='wpw_auto_poster_reposter_options[schedule_posting_order_behaviour]']").hide();
        } else{
            $("select[name='wpw_auto_poster_reposter_options[schedule_posting_order_behaviour]']").show();
        }
    });

    $(document).on('change', "select[name='wpw_auto_poster_options[schedule_wallpost_order]']", function () {

        if( $(this).val() == 'rand' ){
            $("select[name='wpw_auto_poster_options[schedule_wallpost_order_behaviour]']").hide();
        } else{
            $("select[name='wpw_auto_poster_options[schedule_wallpost_order_behaviour]']").show();
        }
    });

    function getCheckedPostType (type, slug) {

       
        
        var post_type = [];
        var checkCount = $( "input[name='wpw_auto_poster_options[enable_"+type+"_for][]']:checked" ).length;
        $("input[name='wpw_auto_poster_options[enable_"+type+"_for][]']:checked").each(function (i) {
            post_type[i] = $(this).val();
        });

        var selected_tags = $("."+slug+"_post_type_tags").select2("val");
        var selected_cats = $("."+slug+"_post_type_cats").select2("val");

       

        // Create data
        var data = {
            action          : 'wpw_auto_poster_get_taxonomies',
            post_type_val   : post_type,
            selected_tags   : selected_tags,
            selected_cats   : selected_cats,
            social_type     : slug
        };

        $('.wpw-ajax-loader').css("visibility", "visible");
        $.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {

            $('.wpw-ajax-loader').css("visibility", "hidden");
            var result = JSON.parse(response);
            if(result) {
                // Append response to categories and tags select box respectively
                $('.'+slug+'_post_type_cats').html(result['data']['categories']); 
                $('.'+slug+'_post_type_tags').html(result['data']['tags']);
                console.log(result);
            } else {
                // Clear select box if result is empty
                $('.'+slug+'_post_type_cats').html('');
                $('.'+slug+'_post_type_tags').html('');
            }
        });
    }

    
        
    $('#wpw_auto_graph_start_date').datepicker({
    	maxDate: 'today',
    	changeMonth: true,
		changeYear: true,
    	onSelect: function( selectedDate ) {
        	$( "#wpw_auto_graph_end_date" ).datepicker( "option", "minDate", selectedDate );
      	}
    });
    
    $('#wpw_auto_graph_end_date').datepicker({
    	maxDate: 'today',
    	changeMonth: true,
		changeYear: true,
    	onSelect: function( selectedDate ) {
        	$( "#wpw_auto_graph_start_date" ).datepicker( "option", "maxDate", selectedDate );
      	}
    });

    //Filtering Graph Data Process
    $(document).on('click', '.wpw_auto_graph_filter', function () {
		get_poster_logs_json_graph();
    });

    //Filtering Graph Data Process
    $(document).on('change', 'input[type=radio][name=wpw_auto_filter_type], #wpw_auto_graph_social_type', function (){

    	if (this.value == 'custom') {
    		$('.wp-auto-custom-wrap').show();
    	}else{
    		var filter_type = $("input[type=radio][name=wpw_auto_filter_type]:checked").val();
			if( filter_type != 'custom' ){
				$('.wp-auto-custom-wrap').hide();
			}
    		get_poster_logs_json_graph();
    	}
    });

    //Onload logs report page only display
    if( $('#wpw-auto-logs-graph').length ){
           get_poster_logs_json_graph();        
    }

    //Build Graph
    function get_poster_logs_json_graph() {

    	$('.wpw-auto-loader-wrap').show();

    	var social_type = start_date = end_date = '';
    	var filter_type = $("input[type=radio][name=wpw_auto_filter_type]:checked").val();
    	var social_type = $('#wpw_auto_graph_social_type').val();

    	if( filter_type == 'custom'){
	    	//Filter data
	    	var start_date  = $('#wpw_auto_graph_start_date').val();
	    	var end_date    = $('#wpw_auto_graph_end_date').val();
    	}

    	var data = {
					action 		: 'wpw_auto_poster_logs_graph',
					social_type : social_type,
					start_date  : start_date,
					end_date    : end_date,
					filter_type : filter_type,
                    wpw_setting_nonce: WpwAutoPosterSettings.wpw_setting_nonce,
				   };

		//Ajax send
		$.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {

			var graph_data = $.parseJSON(response);

			if(graph_data){

				google.charts.load('current', {'packages':['corechart']});
	    		google.charts.setOnLoadCallback( function (){

		    		var data = google.visualization.arrayToDataTable(graph_data);

		        	var options = {
						    title: WpwAutoPosterSettings.report_title,
                            titlePosition: 'center',
						    curveType: 'function',
						    legend: { position: 'right' },
						    width: 1150,
						    height: 600,
						    vAxis: {
						    	  format: '#,###',
						    	  minValue: 4,
						          viewWindow:{
						            min:0
						          }
						        }
						}

					var chart = new google.visualization.LineChart(document.getElementById('wpw-auto-logs-graph'));
					chart.draw(data, options);
	    		});
			}else{
				alert('no data available');
			}
			$('.wpw-auto-loader-wrap').hide();
		});
    }

    // code to handle hide and shot Use Google Analytics with radio 
    $( document).on( 'change', 'input[name="wpw_auto_poster_options[enable_google_tracking]"]', function(){
        if( $(this).is(":checked") ){
            $('#google_tracking_script_row').show();
            if( $('input[name="wpw_auto_poster_options[google_tracking_script]"]:checked').val() == 'yes' ){
                $('#google_tracking_code_row').show();
            }
        } else{
            $('#google_tracking_script_row').hide();
            $('#google_tracking_code_row').hide();
        }
    });

    // code to handle hide and shot Use Google Analytics textarea 
    $(document).on( 'change', 'input[name="wpw_auto_poster_options[google_tracking_script]"]', function() {
        if( $(this).val() == 'yes'){
            $('#google_tracking_code_row').show();
        } else{
            $('#google_tracking_code_row').hide();
        }
    });

    // Filter by Date for Scheduled/Published post in Manage Schedule
 
    if( $('#wpw_auto_start_date').length ) {
        $('#wpw_auto_start_date').datetimepicker({
            dateFormat: WpwAutoPosterAdmin.date_format,
            timeFormat: WpwAutoPosterAdmin.time_format,
            showMinute : false,
            ampm: false,
            stepMinute:60,
            stepHour: 1,
            currentText: 'Now',
            showOn : 'focus',
            onSelect: function(selected) {
                $("#wpw_auto_end_date").datetimepicker("option", "minDate", selected);
            }
        });
    }
    if( $('#wpw_auto_end_date').length ) {
        $('#wpw_auto_end_date').datetimepicker({
            dateFormat: WpwAutoPosterAdmin.date_format,
            minDate: $('#wpw_auto_start_date').datetimepicker('getDate'),
            timeFormat: WpwAutoPosterAdmin.time_format,
            showMinute : false,
            ampm: false,
            stepMinute:60,
            stepHour: 1,
            currentText: 'Now',
           
        });
    }

    $(document).on('keypress', '.wpw-auto-datepicker', function (e){
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
        return false;
        }
    });

    showDateFilter ( $("#wpw_auto_poster_social_status option:selected").val() ) ;

    $(document).on('change', '#wpw_auto_poster_social_status', function (){

        showDateFilter ( this.value ) ;

    });

    $(document).on( 'change', 'input[name="wpw_auto_poster_reposter_options[schedule_wallpost_repeat]"]', function(){
        if( $(this).val() == 'yes' ) {
            $('td.repeat-times').show();
            $(this).closest('td').css('width','59%');
        } else{
            $('td.repeat-times').hide();
            $(this).closest('td').css('width','');
        }
    });

    function showDateFilter ( social_status ) {

        $('.wp-auto-date-filter').hide();

        if (social_status == '2' || social_status == '1') {
            $('.wp-auto-date-filter').show();
        }
    }

    $(document).on('change', 'input[name="wpw_auto_poster_options[facebook_auth_options]"]', 
        function(){
            if( $(this).val() == 'graph'){
                $('#facebook-graph-api').show();
                $('#facebook-app-method').hide();
            }
            else{
                $('#facebook-app-method').show();
                $('#facebook-graph-api').hide();
            }
        }
    );

    $(document).on('change', 'input[name="wpw_auto_poster_options[linkedin_auth_options]"]', 
        function(){
            if( $(this).val() == 'graph'){
                $('#linkedin-graph-api').show();
                $('#linkedin-app-method').hide();
            }
            else{
                $('#linkedin-app-method').show();
                $('#linkedin-graph-api').hide();
            }
        }
    );
        
    $(document).on('change', 'input[name="wpw_auto_poster_options[insta_fb_auth_options]"]', 
        function(){
            if( $(this).val() == 'graph'){
                $('#insta-fb-graph-api').show();
                $('#insta-fb-app-method').hide();
            }
            else{
                $('#insta-fb-app-method').show();
                $('#insta-fb-graph-api').hide();
            }
        }
    );

    $(document).on('change', 'input[name="wpw_auto_poster_options[pinterest_auth_options]"]', 
        function(){
            if( $(this).val() == 'app'){
                $('#pinterest-app-api').show();
                $('#pinterest-cookie-api').hide();
            }
            else{
                $('#pinterest-cookie-api').show();
                $('#pinterest-app-api').hide();
            }
        }
    );
    
    $(document).on('change', 'input[name="wpw_auto_poster_options[reddit_custom_msg_options]"],input[name="wpw_auto_poster_options[fb_custom_msg_options]"], input[name="wpw_auto_poster_options[li_custom_msg_options]"], input[name="wpw_auto_poster_options[ba_custom_msg_options]"], input[name="wpw_auto_poster_options[ins_custom_msg_options]"], input[name="wpw_auto_poster_options[pin_custom_msg_options]"], input[name="wpw_auto_poster_options[yt_custom_msg_options]"], input[name="wpw_auto_poster_options[wp_custom_msg_options]"], input[name="wpw_auto_poster_options[gmb_custom_msg_options]"], input[name="wpw_auto_poster_options[tele_custom_msg_options]"],input[name="wpw_auto_poster_options[medium_custom_msg_options]"],input[name="wpw_auto_poster_options[insta_custom_msg_options]"]', 
        function(){
            if( $(this).val() == 'global_msg'){
                $(this).parents('tr').nextAll('tr.global_msg_tr').show();
                $(this).parents('tr').nextAll('tr.post_msg_tr').hide();
            } else{
                $(this).parents('tr').nextAll('tr.global_msg_tr').hide();
                $(this).parents('tr').nextAll('tr.post_msg_tr').show();
            }
        }
    );


    $(document).on('change', 'input[name="wpw_auto_poster_options[tb_custom_msg_options]"]', 
        function(){

            sap_tb_custom_msg_template_toggle();
            $('.tb_posting_type').trigger('change');
        }
    );

    function sap_tb_custom_msg_template_toggle() {

        var $this = 'input[name="wpw_auto_poster_options[tb_custom_msg_options]"]';

        if( $('input[name="wpw_auto_poster_options[tb_custom_msg_options]"]:checked').val() == 'global_msg') {

            $($this).parents('tr').nextAll('tr.post_msg_tr').hide();
            $($this).parents('tr').nextAll('tr.global_msg_tr').show();

        } else{
            $($this).parents('tr').nextAll('tr.global_msg_tr').hide();
            $($this).parents('tr').nextAll('tr.post_msg_tr').show();
        }
    }
    
    $(document).on('change', 'input[name="wpw_auto_poster_options[tw_custom_msg_options]"]', 
        function(){

            sap_tw_custom_msg_template_toggle();
            woo_vou_toggle_tweet_image();
        }
    );

    function sap_tw_custom_msg_template_toggle() {

        var $this = 'input[name="wpw_auto_poster_options[tw_custom_msg_options]"]';

        if( $('input[name="wpw_auto_poster_options[tw_custom_msg_options]"]:checked').val() == 'global_msg') {

            $($this).parents('tr').nextAll('tr.global_msg_tr').show();
            $($this).parents('tr').nextAll('tr.post_msg_tr').hide();
        } else{
            $($this).parents('tr').nextAll('tr.global_msg_tr').hide();
            $($this).parents('tr').nextAll('tr.post_msg_tr').show();
        }
    }
    
    // reposter admin social setting: show / hide custom msg templates
    $(document).on('change', 'input[name="wpw_auto_poster_reposter_options[repost_insta_custom_msg_options]"],input[name="wpw_auto_poster_reposter_options[repost_reddit_custom_msg_options]"],input[name="wpw_auto_poster_reposter_options[repost_fb_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_li_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_ba_custom_msg_options]"] , input[name="wpw_auto_poster_reposter_options[repost_ins_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_pin_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_tw_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_tb_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_yt_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_wp_custom_msg_options]"],input[name="wpw_auto_poster_reposter_options[repost_gmb_custom_msg_options]"], input[name="wpw_auto_poster_reposter_options[repost_tele_custom_msg_options]"],input[name="wpw_auto_poster_reposter_options[repost_medium_custom_msg_options]"]', 
        function(){
            if( $(this).val() == 'global_msg'){
                $(this).parents('tr').nextAll('tr.global_msg_tr').show();
                $(this).parents('tr').nextAll('tr.post_msg_tr').hide();
            } else{
                $(this).parents('tr').nextAll('tr.global_msg_tr').hide();
                $(this).parents('tr').nextAll('tr.post_msg_tr').show();
            }
        }
    );

    /**
     * For tumblr
     * 
     * hide post image setting if posting type text is selected
     */
    if( $('.tb_posting_type').length > 0 ) {
        var posting_type = $('.tb_posting_type').val();

        var format_option = $('input[name="wpw_auto_poster_options[tb_custom_msg_options]"]:checked').val()+'_tr';

        if( posting_type == 'text' ) {
            $('.wpw_sap_tb_post_img').hide();
            $('.custom_message_template.'+format_option).show();

        } else if( posting_type == 'link' ) {
            $('.wpw_sap_tb_post_img.'+format_option).show();
            $('.custom_message_template.'+format_option).show();

        } else { 
            $('.wpw_sap_tb_post_img.'+format_option).show();
            $('.custom_message_template').hide();
        }
    }

    $( document ).on( 'change', '.tb_posting_type', function() {
        var posting_type = $('.tb_posting_type').val();

        var format_option = $('input[name="wpw_auto_poster_options[tb_custom_msg_options]"]:checked').val()+'_tr';

        if( posting_type == 'text' ) {
            $('.wpw_sap_tb_post_img').hide();
            $('.custom_message_template.'+format_option).show();
        } else if( posting_type == 'link' ) {
            $('.wpw_sap_tb_post_img.'+format_option).show();
            $('.custom_message_template.'+format_option).show();
        } else { 
            $('.wpw_sap_tb_post_img.'+format_option).show();
            $('.custom_message_template').hide();
        }
    });

    $(document).on('change', 'select[name="wpw_auto_poster_options[fb_post_share_type]"]', function(){
        if( $(this).val() != 'image_posting' ){
            $('.fb-image-notes').hide();            
        } else{
            $('.fb-image-notes').show();
        }
    });

	// Add WordPress account
	$( document ).on( 'click', '#add-wordpress-website', function() {
		var link = $(this);

		$('#wp-website-add-result').removeClass('success').removeClass('error').html('');

		$( '.wpw-has-error' ).removeClass( 'wpw-has-error' );

		var wp_name     = $('#wpw_auto_poster_wordpress_site_name').val();
		var wp_url      = $('#wpw_auto_poster_wordpress_site_url').val();
		var wp_username = $('#wpw_auto_poster_wordpress_site_username').val();
		var wp_password = $('#wpw_auto_poster_wordpress_site_password').val();

		var errFlag = false;

		if( ! wp_name ) {
			$('#wpw_auto_poster_wordpress_site_name').addClass( 'wpw-has-error' );
			errFlag = true;
		}
		if( ! wp_url ) {
			$('#wpw_auto_poster_wordpress_site_url').addClass( 'wpw-has-error' );
			errFlag = true;
		}
		if( ! wp_username ) {
			$('#wpw_auto_poster_wordpress_site_username').addClass( 'wpw-has-error' );
			errFlag = true;
		}
		if( ! wp_password ) {
			$('#wpw_auto_poster_wordpress_site_password').addClass( 'wpw-has-error' );
			errFlag = true;
		}

		if( errFlag ) return;

		link.attr( 'disabled', 'true' );
		link.addClass( 'active' );
		link.next('.wpw-validate-token-loader').show();

		var data = {
			action: 'wpw_auto_poster_wordpress_add_sites',
			wp_name: wp_name,
			wp_url: wp_url,
			wp_username: wp_username,
			wp_password: wp_password,
            wpw_setting_nonce: WpwAutoPosterSettings.wpw_setting_nonce,
		};

		$.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {
			if( response.type == 'success' ){
				$('#wp-website-add-result').addClass('success');
				$('#wp-website-add-result').html(response.message);

				sessionStorage.setItem( "wpw_auto_poster_setting_set_active_tab", 'wordpress' );
				window.location.hash = '#wpw-auto-poster-wordpress-websites';
				window.location.reload( true );
			} else{
				$('#wp-website-add-result').addClass('error');
				$('#wp-website-add-result').html(response.message);
			}

			link.next('.wpw-validate-token-loader').hide();
			link.removeAttr('disabled');
		} );
	} );

	// Add WordPress account
	$( document ).on( 'click', '#add-pinterest-cookie-acc', function() {
		var link = $(this);

		$('#pin-cookie-add-result').removeClass('success').removeClass('error').html('');

		$( '.wpw-has-error' ).removeClass( 'wpw-has-error' );

		var pin_sessid    = $('#wpw_auto_poster_pinterest_acc_sessid').val();

		var errFlag = false;

		if( ! pin_sessid ) {
			$('#wpw_auto_poster_pinterest_acc_sessid').addClass( 'wpw-has-error' );
			errFlag = true;
		}

		if( errFlag ) return;

		link.attr( 'disabled', 'true' );
		link.addClass( 'active' );
		link.next('.wpw-validate-token-loader').show();

		var data = {
			action: 'wpw_auto_poster_pinterest_add_cookie_acc',
			pin_sessid: pin_sessid,
            wpw_setting_nonce: WpwAutoPosterSettings.wpw_setting_nonce,
		};

		$.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {
			if( response.status == 'success' ){
				$('#pin-cookie-add-result').addClass('success');
				$('#pin-cookie-add-result').html(response.message);

				sessionStorage.setItem( "wpw_auto_poster_setting_set_active_tab", 'pinterest' );
				window.location.hash = '#wpw-auto-poster-pinterest-api';
				window.location.reload( true );
			} else{
				$('#pin-cookie-add-result').addClass('error');
				$('#pin-cookie-add-result').html(response.message);
			}

			link.next('.wpw-validate-token-loader').hide();
			link.removeAttr('disabled');
		} );
	} );

    // WordPress popup for map post types
    $( document ).on( 'click', '.wordpress-map-post-types', function(e) {
        e.preventDefault();

        var thisObj = $( this );
        var wrapObj = $( '.wpw-auto-poster-popup-content.wp-map-post-types-popup' );

        // Remove all selected values
        wrapObj.find('select.post-types').prop( 'selectedIndex', 0 )

        var postType = thisObj.attr( 'data-post-type' );
        var dataVal = thisObj.attr( 'data-val' );

        dataVal = $.parseJSON( dataVal );
        $.each(dataVal, function(val, text) {
            var tmp = text.split(":");

            var key = '';
            var type = '';
            if( tmp[0] ) key = tmp[0];
            if( tmp[1] ) type = tmp[1];

            $( 'select[data-site-key="' + key + '"]' ).val( type );
        } );

        wrapObj.find( '.mapped-post' ).val( postType );
        $( '.wp-map-submit-btn' ).prop( 'disabled', true );

        wrapObj.fadeIn();
        wrapObj.next( '.wpw-auto-poster-popup-overlay' ).fadeIn();

        return false;
    } );

    var activeTab = sessionStorage.getItem( "wpw_auto_poster_setting_set_active_tab" );
    if( activeTab ){
        $( 'a.nav-tab.nav-tab-active' ).removeClass('nav-tab-active');
        $( 'a[href="#wpw-auto-poster-tab-' + activeTab + '"]' ).addClass('nav-tab-active');

        $( '.wpw-auto-poster-tab-content' ).removeClass( 'wpw-auto-poster-selected-tab' );
        $( '#wpw-auto-poster-tab-' + activeTab ).addClass( 'wpw-auto-poster-selected-tab' );

        sessionStorage.removeItem( "wpw_auto_poster_setting_set_active_tab" );
    }

    // save wordpress map post types
    $( document ).on( 'click', '.wp-map-submit-btn', function(e) {

        var thisObj = $( this );
        var wrapObj = thisObj.parents( '.wpw-auto-poster-popup' );

        var mapTypes = '';
        var keyVal = {}; // this is only use to show data in dropdown after save in js
        wrapObj.find( 'select.post-types' ).each( function() {
            if( $(this).val() == '' ) return;

            if( mapTypes != '' ) mapTypes += '|';

            var siteKey = $(this).attr('data-site-key');
            var value = $(this).val();

            mapTypes += siteKey + ':' + $(this).val();
            keyVal[siteKey + ':' + value] = $(this).parents( '.wp-map-pt-row' ).find( '.wpmptr-name span' ).text() + ' - ' + value;
        } );

        var postType = wrapObj.find( 'input.mapped-post' ).val();

        var data = {
            action: 'wpw_auto_poster_map_wordpress_post_type',
            mapTypes: mapTypes,
            postType: postType,
            wpw_setting_nonce: WpwAutoPosterSettings.wpw_setting_nonce,
        };

        thisObj.prop( 'disabled', true );
        wrapObj.find( 'img.ajax-loader' ).show();

        $.post( WpwAutoPosterSettings.ajaxurl, data, function(response) {
            
            var res = $.parseJSON( response );
            var selectBox = '.wpw-auto-poster-wp-sites select[name="wpw_auto_poster_options[wp_type_' + postType + '_sites][]"]';

            if( res.status == 'success' ) {
                $( selectBox ).html('');

                var flag = false;
                var jsonArr = [];
                $.each(keyVal, function(val, text) {
                    $('<option/>').val(val).html(text).appendTo( selectBox );
                    flag = true;
                    jsonArr.push( val );
                } );
                
                if( flag ) {
                    $(selectBox).parents( '.wpw-auto-poster-wp-sites' ).show();
                    $(selectBox).select2('destroy').find('option').prop('selected', 'selected').end().select2();
                } else {
                    $(selectBox).parents( '.wpw-auto-poster-wp-sites' ).hide();
                }

                console.log( jsonArr );

                // Remove all selected values
                wrapObj.find('select.post-types').prop( 'selectedIndex', 0 )
                $( '.wordpress-map-post-types[data-post-type="'+postType+'"]' ).attr( 'data-val', JSON.stringify(jsonArr) );

                // Close the popup
                $( '.wpw-auto-poster-popup-content.wp-map-post-types-popup' ).fadeOut();
                $( '.wpw-auto-poster-popup-overlay' ).fadeOut();
            }

            thisObj.prop( 'disabled', false );
            wrapObj.find( 'img.ajax-loader' ).hide();
        } );

        return false;
    } );

    $( document ).on( 'change', 'select.post-types', function() {
        $( '.wp-map-submit-btn' ).prop( 'disabled', false );
    } );

    // telegram hide/show fields based on selected
    $( document ).on( 'change', '.wpw-auto-poster-tele-msgtype', function() {
		var thisObj = $( this );
		var thisVal = thisObj.val();
		var thisType = thisObj.attr( 'data-type' );

		if( thisVal == 'photo' ) {
			$( 'input[name="wpw_auto_poster_options[tele_post_image_'+thisType+']"]' ).parent().parent().show();
			$( 'input[name="wpw_auto_poster_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().show();
			$( 'textarea[name="wpw_auto_poster_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().hide();
		} else {
			$( 'input[name="wpw_auto_poster_options[tele_post_image_'+thisType+']"]' ).parent().parent().hide();
			$( 'input[name="wpw_auto_poster_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().hide();
			$( 'textarea[name="wpw_auto_poster_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().show();
		}
    } );

    // Check for all on page load
    if( $('.wpw-auto-poster-tele-msgtype').length > 0 ) {
    	$('.wpw-auto-poster-tele-msgtype').each( function() {
    		var thisObj = $( this );
			var thisVal = thisObj.val();
			var thisType = thisObj.attr( 'data-type' );

			if( thisVal == 'photo' ) {
				$( 'input[name="wpw_auto_poster_options[tele_post_image_'+thisType+']"]' ).parent().parent().show();
				$( 'input[name="wpw_auto_poster_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().show();
				$( 'textarea[name="wpw_auto_poster_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().hide();
                
                $( 'input[name="wpw_auto_poster_reposter_options[tele_post_image_'+thisType+']"]' ).parent().parent().show();
                $( 'input[name="wpw_auto_poster_reposter_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().show();
                $( 'textarea[name="wpw_auto_poster_reposter_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().hide();
			} else {
				$( 'input[name="wpw_auto_poster_options[tele_post_image_'+thisType+']"]' ).parent().parent().hide();
				$( 'input[name="wpw_auto_poster_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().hide();
				$( 'textarea[name="wpw_auto_poster_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().show();
                
                $( 'input[name="wpw_auto_poster_reposter_options[tele_post_image_'+thisType+']"]' ).parent().parent().hide();
                $( 'input[name="wpw_auto_poster_reposter_options[tele_post_img_caption_'+thisType+']"]' ).parent().parent().hide();
                $( 'textarea[name="wpw_auto_poster_reposter_options[tele_global_message_template_'+thisType+']"]' ).parent().parent().show();
			}
    	} );
    }

    $(document).on('click', 'input[name="wpw_auto_poster_options[fb_proxy_enable]"]', 
        function(){
            if($(this).is(':checked')){
                $('#facebook-proxy-settings').show();
            }
            else{
                $('#facebook-proxy-settings').hide();
            }
        }
    );

    $(document).on('click', 'input[name="wpw_auto_poster_options[pin_proxy_enable]"]', 
        function(){
            if($(this).is(':checked')){
                $('#pinterest-proxy-settings').show();
            }
            else{
                $('#pinterest-proxy-settings').hide();
            }
        }
    );
});