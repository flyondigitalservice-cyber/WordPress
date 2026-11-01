/* global jQuery, wp */
( function ( $ ) {
	'use strict';

	$( function () {
		// Confirm before emailing a customer.
		$( document ).on( 'click', '.ipsm-email', function ( e ) {
			var msg = $( this ).data( 'confirm' );
			if ( msg && ! window.confirm( msg ) ) {
				e.preventDefault();
			}
		} );

		// Packing slip button in the orders list opens the print view in a new tab.
		$( '.wc-action-button-ipsm-slip' ).attr( 'target', '_blank' );

		// Settings: colour picker.
		if ( $.fn.wpColorPicker ) {
			$( '.ipsm-color' ).wpColorPicker();
		}

		// Settings: logo picker.
		var frame;
		$( '#ipsm-logo-select' ).on( 'click', function ( e ) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( {
				title: 'Choose logo',
				library: { type: 'image' },
				multiple: false
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
				$( '#ipsm-logo-id' ).val( att.id );
				$( '#ipsm-logo-preview' ).html( $( '<img />' ).attr( 'src', url ) );
				$( '#ipsm-logo-remove' ).show();
			} );
			frame.open();
		} );
		$( '#ipsm-logo-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '#ipsm-logo-id' ).val( '0' );
			$( '#ipsm-logo-preview' ).empty();
			$( this ).hide();
		} );
	} );
}( jQuery ) );
