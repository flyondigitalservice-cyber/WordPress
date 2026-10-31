/**
 * Bazaar core JS. Vanilla, no dependencies, event delegation, no globals.
 * - Swatches: sync buttons <-> the hidden WooCommerce <select>.
 * - Dialogs: open/close <dialog> elements (size guide).
 */
( function () {
	'use strict';

	/* ---- Dialogs ---- */
	document.addEventListener( 'click', function ( event ) {
		var opener = event.target.closest( '[data-bzr-dialog]' );
		if ( opener ) {
			var dialog = document.getElementById( opener.getAttribute( 'data-bzr-dialog' ) );
			if ( dialog && typeof dialog.showModal === 'function' ) {
				dialog.showModal();
			}
			return;
		}

		var closer = event.target.closest( '[data-bzr-dialog-close]' );
		if ( closer ) {
			var parent = closer.closest( 'dialog' );
			if ( parent ) {
				parent.close();
			}
			return;
		}

		// Click on the backdrop closes the dialog.
		if ( event.target.matches && event.target.matches( 'dialog.bzr-dialog' ) ) {
			event.target.close();
		}
	} );

	/* ---- Swatches ---- */
	function selectFor( group ) {
		var wrap = group.previousElementSibling;
		return wrap ? wrap.querySelector( 'select' ) : null;
	}

	function syncGroup( group ) {
		var select = selectFor( group );
		if ( ! select ) {
			return;
		}
		group.querySelectorAll( '.bzr-swatch' ).forEach( function ( button ) {
			var value = button.getAttribute( 'data-value' );
			var option = Array.prototype.find.call( select.options, function ( o ) {
				return o.value === value;
			} );
			var disabled = ! option || option.disabled;
			button.setAttribute( 'aria-checked', select.value === value ? 'true' : 'false' );
			button.setAttribute( 'aria-disabled', disabled ? 'true' : 'false' );
		} );
	}

	function syncAll() {
		document.querySelectorAll( '.bzr-swatches' ).forEach( syncGroup );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.bzr-swatch' );
		if ( ! button ) {
			return;
		}
		var group = button.closest( '.bzr-swatches' );
		var select = selectFor( group );
		if ( ! select || button.getAttribute( 'aria-disabled' ) === 'true' ) {
			return;
		}
		var value = button.getAttribute( 'data-value' );
		// Clicking the active swatch again clears the choice.
		select.value = select.value === value ? '' : value;
		select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		syncGroup( group );
	} );

	// Arrow-key navigation inside a swatch group (radiogroup pattern).
	document.addEventListener( 'keydown', function ( event ) {
		var button = event.target.closest && event.target.closest( '.bzr-swatch' );
		if ( ! button || [ 'ArrowRight', 'ArrowLeft', 'ArrowDown', 'ArrowUp' ].indexOf( event.key ) === -1 ) {
			return;
		}
		var all = Array.prototype.slice.call( button.parentNode.querySelectorAll( '.bzr-swatch' ) );
		var rtl = document.documentElement.dir === 'rtl';
		var forward = event.key === 'ArrowDown' || event.key === ( rtl ? 'ArrowLeft' : 'ArrowRight' );
		var next = all[ ( all.indexOf( button ) + ( forward ? 1 : -1 ) + all.length ) % all.length ];
		next.focus();
		event.preventDefault();
	} );

	// WooCommerce updates option availability after a selection and on reset.
	if ( window.jQuery ) {
		window.jQuery( document ).on(
			'woocommerce_update_variation_values reset_data found_variation',
			'.variations_form',
			syncAll
		);
	}
	document.addEventListener( 'change', function ( event ) {
		if ( event.target.closest && event.target.closest( '.bzr-swatches__select' ) ) {
			syncAll();
		}
	} );
	document.addEventListener( 'DOMContentLoaded', syncAll );
}() );

/* ---- Sticky add-to-cart bar ---- */
( function () {
	'use strict';
	var bar = document.querySelector( '[data-bzr-sticky-atc]' );
	var form = document.querySelector( 'form.cart' );
	if ( ! bar || ! form || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}
	new IntersectionObserver( function ( entries ) {
		// Show the bar once the real form has scrolled out of view above the viewport.
		var entry = entries[ 0 ];
		bar.hidden = entry.isIntersecting || entry.boundingClientRect.top > 0;
	} ).observe( form );

	bar.querySelector( '[data-bzr-sticky-target]' ).addEventListener( 'click', function () {
		form.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		var focusable = form.querySelector( 'button, select, input' );
		if ( focusable ) {
			focusable.focus( { preventScroll: true } );
		}
	} );
}() );

/* ---- Deal countdown ---- */
( function () {
	'use strict';
	var nodes = document.querySelectorAll( '[data-bzr-countdown]' );
	if ( ! nodes.length ) {
		return;
	}
	function pad( n ) {
		return n < 10 ? '0' + n : String( n );
	}
	function tick() {
		var now = Math.floor( Date.now() / 1000 );
		nodes.forEach( function ( node ) {
			var left = parseInt( node.getAttribute( 'data-bzr-countdown' ), 10 ) - now;
			if ( left <= 0 ) {
				node.hidden = true;
				return;
			}
			var d = Math.floor( left / 86400 );
			var h = Math.floor( ( left % 86400 ) / 3600 );
			var m = Math.floor( ( left % 3600 ) / 60 );
			var text = ( d > 0 ? d + 'd ' : '' ) + pad( h ) + ':' + pad( m ) + ':' + pad( left % 60 );
			node.querySelector( '[data-bzr-countdown-text]' ).textContent = text;
		} );
	}
	tick();
	window.setInterval( tick, 1000 );
}() );
