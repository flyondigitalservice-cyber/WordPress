/* DCE Vibe — header, mobile menu and WhatsApp lead form. */
( function () {
	'use strict';

	var header = document.querySelector( '[data-dce-header]' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	// Mobile drawer.
	var drawer = document.getElementById( 'dce-drawer' );
	var opener = document.querySelector( '[data-dce-open]' );
	function setDrawer( open ) {
		if ( ! drawer ) {
			return;
		}
		drawer.classList.toggle( 'is-open', open );
		drawer.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
		document.body.classList.toggle( 'dce-lock', open );
		if ( opener ) {
			opener.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
		if ( open ) {
			var first = drawer.querySelector( 'a, button' );
			if ( first ) {
				first.focus();
			}
		} else if ( opener ) {
			opener.focus();
		}
	}
	if ( opener ) {
		opener.addEventListener( 'click', function () {
			setDrawer( true );
		} );
	}
	document.querySelectorAll( '[data-dce-close]' ).forEach( function ( el ) {
		el.addEventListener( 'click', function () {
			setDrawer( false );
		} );
	} );
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && drawer && drawer.classList.contains( 'is-open' ) ) {
			setDrawer( false );
		}
	} );
	if ( drawer ) {
		drawer.querySelectorAll( '.menu-item-has-children' ).forEach( function ( li ) {
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'dce-sub-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', 'Show submenu' );
			btn.textContent = '+';
			btn.addEventListener( 'click', function () {
				var open = li.classList.toggle( 'is-expanded' );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				btn.textContent = open ? '−' : '+';
			} );
			li.insertBefore( btn, li.querySelector( '.sub-menu' ) );
		} );
		drawer.querySelectorAll( 'a[href*="#"]' ).forEach( function ( a ) {
			a.addEventListener( 'click', function () {
				setDrawer( false );
			} );
		} );
	}

	// Lead forms: save the lead, then open WhatsApp with everything pre-filled.
	document.querySelectorAll( '.dce-lead-form' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( e ) {
			if ( ! window.DCE || ! window.fetch ) {
				return; // Normal POST fallback.
			}
			e.preventDefault();
			var status = form.querySelector( '.dce-form-status' );
			var data = new FormData( form );
			var name = ( data.get( 'dce_name' ) || '' ).trim();
			var phone = ( data.get( 'dce_phone' ) || '' ).trim();
			if ( ! name || phone.replace( /\D/g, '' ).length < 7 ) {
				status.textContent = DCE.i18n.required;
				status.className = 'dce-form-status is-error';
				( name ? form.querySelector( '[name="dce_phone"]' ) : form.querySelector( '[name="dce_name"]' ) ).focus();
				return;
			}
			var lines = [ 'Hello ' + DCE.brand + ', I would like a free home visit / quote.', 'Name: ' + name, 'Phone: ' + phone ];
			[ [ 'dce_service', 'Interested in' ], [ 'dce_area', 'Area' ], [ 'dce_message', 'Details' ], [ 'dce_source', 'Page' ] ].forEach( function ( f ) {
				var v = ( data.get( f[ 0 ] ) || '' ).trim();
				if ( v ) {
					lines.push( f[ 1 ] + ': ' + v );
				}
			} );
			var waUrl = 'https://wa.me/' + DCE.wa + '?text=' + encodeURIComponent( lines.join( '\n' ) );

			data.set( 'action', 'dce_lead' );
			data.set( 'nonce', DCE.nonce );
			try {
				fetch( DCE.ajax, { method: 'POST', body: data, credentials: 'same-origin', keepalive: true } );
			} catch ( err ) {}

			status.textContent = DCE.i18n.opening;
			status.className = 'dce-form-status is-ok';
			var win = window.open( waUrl, '_blank' );
			if ( win ) {
				win.opener = null;
			} else {
				window.location.href = waUrl;
			}
			form.reset();
		} );
	} );
} )();
