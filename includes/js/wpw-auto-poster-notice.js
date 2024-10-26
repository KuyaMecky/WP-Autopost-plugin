'use strict';

(function ( $ ) {

	var setCookie = function ( c_name, value, exdays ) {
		var exdate = new Date();
		exdate.setDate( exdate.getDate() + exdays );
		var c_value = encodeURIComponent( value ) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
		document.cookie = c_name + "=" + c_value;
	};

	$( document ).on( 'click.wpw-auto-poster-notice-dismiss', '.wpw-auto-poster-notice-dismiss', function ( e ) {
		e.preventDefault();
		var $el = $( this ).closest('#wpw_auto_poster_license-activation-notice' );
		$el.fadeTo( 100, 0, function () {
			$el.slideUp( 100, function () {
				$el.remove();
			} );
		} );
		setCookie( 'wpwautoposterdeactivationmsg',
			WpwAutoPosterOptions.wpw_auto_poster_version,
			30 );
	} );
})( window.jQuery );