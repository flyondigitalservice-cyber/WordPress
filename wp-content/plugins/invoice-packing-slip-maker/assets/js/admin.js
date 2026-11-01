/* global jQuery, wp, ipsmAdmin */
( function ( $ ) {
	'use strict';

	var cfg = window.ipsmAdmin || { i18n: {}, bulk: {} };

	/* ---------------------------------------------------------------
	 * Order screen: send invoice without reloading the page.
	 * ------------------------------------------------------------- */
	$( document ).on( 'click', '.ipsm-email', function ( e ) {
		var $btn = $( this );
		var msg  = $btn.data( 'confirm' );

		e.preventDefault();
		if ( $btn.hasClass( 'is-busy' ) || ( msg && ! window.confirm( msg ) ) ) {
			return;
		}
		if ( ! cfg.ajaxUrl ) {
			window.location.href = $btn.attr( 'href' ); // No-JS-data fallback.
			return;
		}

		var $text = $btn.find( '.ipsm-email-text' );
		var label = $text.text();
		var $box  = $btn.closest( '.ipsm-mb' );

		$box.find( '.ipsm-mb-error' ).remove();
		$btn.addClass( 'is-busy' );
		$text.text( cfg.i18n.sending );

		$.post( cfg.ajaxUrl, { action: 'ipsm_email_invoice', nonce: cfg.emailNonce, order_id: $btn.data( 'order' ) } )
			.done( function ( res ) {
				if ( res && res.data && res.data.status ) {
					$box.find( '.ipsm-mb-status-wrap' ).html( res.data.status );
				}
				if ( res && res.success ) {
					$btn.removeClass( 'is-busy' ).addClass( 'is-done' );
					$text.text( cfg.i18n.sent );
					setTimeout( function () {
						$btn.removeClass( 'is-done' );
						$text.text( label );
					}, 2500 );
				} else {
					fail( res && res.data && res.data.message );
				}
			} )
			.fail( function ( xhr ) {
				fail( xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message );
			} );

		function fail( message ) {
			$btn.removeClass( 'is-busy' );
			$text.text( label );
			$( '<p class="ipsm-mb-error" role="alert" />' ).text( message || cfg.i18n.failed ).insertAfter( $btn );
		}
	} );

	/* ---------------------------------------------------------------
	 * Orders list: bulk print / download open in a new tab so the
	 * orders list stays where it is.
	 * ------------------------------------------------------------- */
	var lastBulkButton = 'action';
	$( document ).on( 'click', '#doaction, #doaction2', function () {
		lastBulkButton = 'doaction2' === this.id ? 'action2' : 'action';
	} );

	$( document ).on( 'submit', '#posts-filter, #wc-orders-filter', function ( e ) {
		var $form  = $( this );
		var action = $form.find( 'select[name="' + lastBulkButton + '"]' ).val();
		if ( ! action || '-1' === action ) {
			action = $form.find( 'select[name="action"]' ).val();
		}
		var spec = cfg.bulk && cfg.bulk[ action ];
		if ( ! spec || ! cfg.docUrl ) {
			return; // Not ours (or email): normal submit.
		}

		var ids = $form.find( 'input[name="post[]"]:checked, input[name="id[]"]:checked' ).map( function () {
			return this.value;
		} ).get();

		e.preventDefault();
		lastBulkButton = 'action';
		if ( ! ids.length ) {
			window.alert( cfg.i18n.noneChosen );
			return;
		}

		var url = cfg.docUrl + '&type=' + encodeURIComponent( spec[ 0 ] ) + '&output=' + spec[ 1 ] + '&order_ids=' + ids.join( ',' ) + ( 'html' === spec[ 1 ] ? '&autoprint=1' : '' );
		if ( 'html' === spec[ 1 ] ) {
			var win = window.open( url, '_blank' );
			if ( ! win ) {
				window.alert( cfg.i18n.popup );
			}
		} else {
			window.location.href = url; // Downloads don't leave the page.
		}
	} );

	// Packing slip quick button opens in a new tab.
	$( function () {
		$( '.wc-action-button-ipsm-slip' ).attr( { target: '_blank', rel: 'noopener' } );
	} );

	/* ---------------------------------------------------------------
	 * Settings page.
	 * ------------------------------------------------------------- */
	$( function () {
		var $wrap = $( '.ipsm-wrap' );
		if ( ! $wrap.length ) {
			return;
		}
		var $form = $( '#ipsm-settings-form' );

		// Tabs (remembered in the URL hash so a save returns to the same tab).
		function showTab( tab ) {
			var $tab = $wrap.find( '.ipsm-tab[data-tab="' + tab + '"]' );
			if ( ! $tab.length ) {
				return;
			}
			$wrap.find( '.ipsm-tab' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
			$tab.addClass( 'is-active' ).attr( 'aria-selected', 'true' );
			$wrap.find( '.ipsm-panel' ).attr( 'hidden', true ).removeClass( 'is-active' );
			$wrap.find( '.ipsm-panel[data-panel="' + tab + '"]' ).removeAttr( 'hidden' ).addClass( 'is-active' );
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + tab );
			}
			var $referer = $form.find( 'input[name="_wp_http_referer"]' );
			$referer.val( String( $referer.val() ).split( '#' )[ 0 ] + '#' + tab );
		}
		$wrap.on( 'click', '.ipsm-tab', function () {
			showTab( $( this ).data( 'tab' ) );
		} );
		$wrap.on( 'click', '[data-ipsm-goto]', function ( e ) {
			e.preventDefault();
			showTab( $( this ).data( 'ipsm-goto' ) );
			$( 'html, body' ).animate( { scrollTop: $wrap.find( '.ipsm-tabs' ).offset().top - 50 }, 200 );
		} );
		if ( window.location.hash ) {
			showTab( window.location.hash.substring( 1 ) );
		}

		// Unsaved-changes indicator and leave warning.
		var dirty = false;
		$form.on( 'change input', ':input', function () {
			if ( ! dirty ) {
				dirty = true;
				$wrap.find( '.ipsm-unsaved' ).removeAttr( 'hidden' );
			}
		} );
		$form.on( 'submit', function () {
			dirty = false;
		} );
		$( window ).on( 'beforeunload', function () {
			return dirty ? cfg.i18n.leave : undefined;
		} );

		// Live invoice number example.
		function pad( n, len ) {
			n = String( n );
			while ( n.length < len ) {
				n = '0' + n;
			}
			return n;
		}
		function updateExample() {
			var now = new Date();
			var tokens = function ( s ) {
				return String( s || '' )
					.replace( /\{Y\}/g, now.getFullYear() )
					.replace( /\{y\}/g, String( now.getFullYear() ).slice( -2 ) )
					.replace( /\{m\}/g, pad( now.getMonth() + 1, 2 ) );
			};
			var digits = Math.min( 10, Math.max( 0, parseInt( $( '#ipsm-number_padding' ).val(), 10 ) || 0 ) );
			var next   = Math.max( 1, parseInt( $( '#ipsm-next' ).val(), 10 ) || 1 );
			$( '#ipsm-number-example' ).text( tokens( $( '#ipsm-number_prefix' ).val() ) + pad( next, digits ) + tokens( $( '#ipsm-number_suffix' ).val() ) );
		}
		$wrap.on( 'input change', '#ipsm-number_prefix, #ipsm-number_suffix, #ipsm-number_padding, #ipsm-next', updateExample );

		// Colour: picker + preset swatches.
		var $color = $( '#ipsm-accent' );
		function markSwatch( value ) {
			$wrap.find( '.ipsm-swatch' ).each( function () {
				$( this ).toggleClass( 'is-active', String( $( this ).data( 'color' ) ).toLowerCase() === String( value ).toLowerCase() );
			} );
		}
		if ( $.fn.wpColorPicker ) {
			$color.wpColorPicker( {
				change: function ( event, ui ) {
					markSwatch( ui.color.toString() );
					$form.trigger( 'change' );
				}
			} );
		}
		markSwatch( $color.val() );
		$wrap.on( 'click', '.ipsm-swatch', function () {
			var value = $( this ).data( 'color' );
			if ( $.fn.wpColorPicker ) {
				$color.wpColorPicker( 'color', value );
			} else {
				$color.val( value ).trigger( 'change' );
			}
			markSwatch( value );
		} );

		// Logo picker.
		var frame;
		$( '#ipsm-logo-select' ).on( 'click', function ( e ) {
			e.preventDefault();
			if ( ! window.wp || ! wp.media ) {
				return;
			}
			if ( ! frame ) {
				frame = wp.media( {
					title: cfg.i18n.chooseLogo,
					button: { text: cfg.i18n.useLogo },
					library: { type: 'image' },
					multiple: false
				} );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
					$( '#ipsm-logo-id' ).val( att.id ).trigger( 'change' );
					$( '#ipsm-logo-preview' ).empty().append( $( '<img alt="" />' ).attr( 'src', url ) );
					$( '#ipsm-logo-select' ).addClass( 'has-logo' );
					$( '#ipsm-logo-remove' ).removeAttr( 'hidden' );
				} );
			}
			frame.open();
		} );
		$( '#ipsm-logo-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$( '#ipsm-logo-id' ).val( '0' ).trigger( 'change' );
			$( '#ipsm-logo-preview' ).empty();
			$( '#ipsm-logo-select' ).removeClass( 'has-logo' );
			$( this ).attr( 'hidden', true );
		} );

		// Preview: scale the document to fit the side panel, switch documents.
		var $frameWrap = $wrap.find( '.ipsm-preview-frame' );
		var $iframe    = $( '#ipsm-preview-iframe' );
		function fitPreview() {
			if ( ! $iframe.length ) {
				return;
			}
			var scale = $frameWrap.width() / 640;
			$iframe.css( 'transform', 'scale(' + scale + ')' );
			$frameWrap.css( 'height', Math.round( 900 * scale ) );
		}
		$iframe.on( 'load', function () {
			$frameWrap.removeClass( 'is-loading' );
		} );
		$( window ).on( 'resize', fitPreview );
		fitPreview();

		$wrap.on( 'click', '.ipsm-segmented button', function () {
			var $b = $( this );
			$b.addClass( 'is-active' ).siblings().removeClass( 'is-active' );
			$frameWrap.addClass( 'is-loading' );
			$iframe.attr( 'src', $b.data( 'src' ) );
			$( '#ipsm-preview-full' ).attr( 'href', $b.data( 'full' ) );
		} );
	} );
}( jQuery ) );
