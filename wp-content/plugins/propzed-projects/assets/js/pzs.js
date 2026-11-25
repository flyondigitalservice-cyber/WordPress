/**
 * Propzed Projects: site-wide helpers.
 *   1. "8 of 120 units left" chip on property cards (data from window.pzsLeft).
 *   2. [propzed_emi] calculator.
 */
( function () {
	'use strict';

	function inr( n ) {
		n = Math.round( n );
		if ( n >= 1e7 ) {
			return '₹ ' + ( n / 1e7 ).toFixed( 2 ).replace( /\.?0+$/, '' ) + ' Cr';
		}
		if ( n >= 1e5 ) {
			return '₹ ' + ( n / 1e5 ).toFixed( 2 ).replace( /\.?0+$/, '' ) + ' L';
		}
		return '₹ ' + n.toLocaleString( 'en-IN' );
	}

	function chips() {
		var left = window.pzsLeft;
		if ( ! left ) {
			return;
		}
		document.querySelectorAll( '.pz-card [data-pz-fav]' ).forEach( function ( btn ) {
			var id = btn.getAttribute( 'data-pz-fav' );
			var card = btn.closest( '.pz-card' );
			if ( ! left[ id ] || ! card || card.querySelector( '.pzs-left' ) ) {
				return;
			}
			var avail = left[ id ][ 0 ];
			var total = left[ id ][ 1 ];
			var chip = document.createElement( 'span' );
			chip.className = 'pzs-left' + ( avail / total <= 0.2 ? ' is-low' : '' );
			chip.textContent = ( window.pzsLeftLabel || '%1$d of %2$d units left' ).replace( '%1$d', avail ).replace( '%2$d', total );
			var meta = card.querySelector( '.pz-card__meta' ) || card.querySelector( '.pz-card__body' );
			if ( meta ) {
				meta.appendChild( chip );
			}
		} );
	}

	function emi( box ) {
		var get = function ( name ) {
			var el = box.querySelector( '[name="' + name + '"]' );
			return el ? parseFloat( el.value ) || 0 : 0;
		};
		var out = function ( key, text ) {
			var el = box.querySelector( '[data-out="' + key + '"]' );
			if ( el ) {
				el.textContent = text;
			}
		};
		function calc() {
			var price = get( 'price' );
			var down = Math.min( 90, Math.max( 0, get( 'down' ) ) );
			var rate = get( 'rate' ) / 1200;
			var months = Math.max( 1, Math.round( get( 'years' ) * 12 ) );
			var loan = price * ( 1 - down / 100 );
			var pay = rate ? ( loan * rate * Math.pow( 1 + rate, months ) ) / ( Math.pow( 1 + rate, months ) - 1 ) : loan / months;
			var duty = price * get( 'duty' ) / 100 + Math.min( 30000, price * 0.01 );
			if ( ! price ) {
				return;
			}
			out( 'emi', inr( pay ) + ' / month' );
			out( 'loan', inr( loan ) );
			out( 'duty', inr( duty ) );
			out( 'cash', inr( price - loan + duty ) );
			out( 'interest', inr( pay * months - loan ) );
			out( 'income', inr( pay / 0.4 ) + ' / month' );
		}
		box.addEventListener( 'input', calc );
		calc();
	}

	function init() {
		chips();
		document.querySelectorAll( '[data-pzs-emi]' ).forEach( emi );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
