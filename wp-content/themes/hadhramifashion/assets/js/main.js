/**
 * Hadhrami Fashion — navigation, bag, product options.
 */
( function () {
	'use strict';

	var cfg = window.HF || {};
	var $ = function ( sel, ctx ) { return ( ctx || document ).querySelector( sel ); };
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || document ).querySelectorAll( sel ) ); };

	function esc( str ) {
		return String( str == null ? '' : str ).replace( /[&<>"']/g, function ( c ) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ c ];
		} );
	}

	function money( n ) {
		return ( cfg.currency || '' ) + Math.round( Number( n ) || 0 ).toLocaleString( 'en-IN' );
	}

	/* ------------------------------------------------------------------ Toast */
	var toastTimer;
	function toast( msg ) {
		var el = $( '[data-toast]' );
		if ( ! el ) { return; }
		el.textContent = msg;
		el.classList.add( 'is-on' );
		clearTimeout( toastTimer );
		toastTimer = setTimeout( function () { el.classList.remove( 'is-on' ); }, 2600 );
	}

	/* ------------------------------------------------------------------- Bag */
	// The bag is the WooCommerce cart. The drawer (div.hf-mini-bag) and the count
	// (span.bag-count) are WooCommerce cart fragments, refreshed after every change.
	function applyFragments( res ) {
		if ( ! res || ! res.fragments ) { return; }
		Object.keys( res.fragments ).forEach( function ( sel ) {
			$$( sel ).forEach( function ( el ) {
				var tmp = document.createElement( 'div' );
				tmp.innerHTML = res.fragments[ sel ];
				if ( tmp.firstElementChild ) { el.replaceWith( tmp.firstElementChild ); }
			} );
		} );
		if ( window.jQuery ) { window.jQuery( document.body ).trigger( 'added_to_cart', [ res.fragments, res.cart_hash ] ); }
	}

	function wcAjax( url, data ) {
		return fetch( url, { method: 'POST', credentials: 'same-origin', body: data } ).then( function ( r ) { return r.json(); } );
	}

	window.HFUtil = { esc: esc, money: money, toast: toast, $: $, $$: $$ };

	document.addEventListener( 'click', function ( e ) {
		var rm = e.target.closest( '[data-remove-key]' );
		if ( ! rm || ! cfg.ajaxRemove ) { return; }
		var fd = new FormData();
		fd.append( 'cart_item_key', rm.getAttribute( 'data-remove-key' ) );
		rm.disabled = true;
		wcAjax( cfg.ajaxRemove, fd ).then( applyFragments ).catch( function () { rm.disabled = false; } );
	} );

	/* --------------------------------------------------------- Drawer & nav */
	var scrim = $( '[data-scrim]' );
	var drawer = $( '#bag-drawer' );
	var nav = $( '#primary-nav' );

	function closeAll() {
		if ( drawer ) { drawer.classList.remove( 'is-open' ); drawer.setAttribute( 'aria-hidden', 'true' ); }
		if ( nav ) { nav.classList.remove( 'is-open' ); }
		document.body.classList.remove( 'nav-is-open' );
		if ( scrim ) { scrim.classList.remove( 'is-on' ); }
		$$( '[data-nav-open]' ).forEach( function ( b ) { b.setAttribute( 'aria-expanded', 'false' ); } );
	}
	function openBag() {
		closeAll();
		drawer.classList.add( 'is-open' );
		drawer.setAttribute( 'aria-hidden', 'false' );
		scrim.classList.add( 'is-on' );
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-bag-open]' ) ) { e.preventDefault(); openBag(); }
		if ( e.target.closest( '[data-bag-close],[data-scrim],[data-nav-close]' ) ) { closeAll(); }
		var navBtn = e.target.closest( '[data-nav-open]' );
		if ( navBtn ) {
			nav.classList.add( 'is-open' );
			document.body.classList.add( 'nav-is-open' );
			scrim.classList.add( 'is-on' );
			navBtn.setAttribute( 'aria-expanded', 'true' );
		}
	} );
	document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' ) { closeAll(); } } );

	var header = $( '#site-header' );
	window.addEventListener( 'scroll', function () {
		if ( header ) { header.classList.toggle( 'is-scrolled', window.scrollY > 10 ); }
	}, { passive: true } );

	/* ---------------------------------------------------------------- Reveal */
	if ( 'IntersectionObserver' in window ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( en ) {
				if ( en.isIntersecting ) { en.target.classList.add( 'is-in' ); io.unobserve( en.target ); }
			} );
		}, { rootMargin: '0px 0px -8% 0px' } );
		$$( '.reveal' ).forEach( function ( el ) { io.observe( el ); } );
	} else {
		$$( '.reveal' ).forEach( function ( el ) { el.classList.add( 'is-in' ); } );
	}

	/* ------------------------------------------------------- Product options */
	var product = $( '.product[data-kind]' );
	if ( product ) {
		var kind = product.getAttribute( 'data-kind' );
		var form = $( '[data-add-form]', product );
		var promise = $( '[data-promise-text]', product );

		function currentFit() {
			if ( kind === 'suits' ) { return 'measure'; }
			var r = $( 'input[name="hf_fit"]:checked', form );
			return r ? r.value : 'ready';
		}
		function syncFit() {
			var fit = currentFit();
			$$( '[data-show-fit]', form ).forEach( function ( el ) {
				el.hidden = el.getAttribute( 'data-show-fit' ) !== fit;
			} );
			if ( promise && kind !== 'suits' ) {
				promise.textContent = promise.getAttribute( fit === 'measure' ? 'data-measure' : 'data-ready' );
			}
		}
		form.addEventListener( 'change', syncFit );
		syncFit();

		// Add to bag without leaving the page; falls back to a normal POST.
		form.addEventListener( 'submit', function ( e ) {
			if ( ! cfg.ajaxAdd || ! window.fetch ) { return; }
			e.preventDefault();
			var btn = $( 'button[type=submit]', form );
			var fd = new FormData( form );
			fd.delete( 'add-to-cart' );
			if ( currentFit() !== 'measure' ) {
				$$( 'input[name^="m_"]', form ).forEach( function ( inp ) { fd.delete( inp.name ); } );
			}
			btn.disabled = true;
			wcAjax( cfg.ajaxAdd, fd ).then( function ( res ) {
				btn.disabled = false;
				if ( res && res.error ) {
					form.submit();
					return;
				}
				applyFragments( res );
				toast( cfg.i18n && cfg.i18n.added );
				openBag();
			} ).catch( function () {
				btn.disabled = false;
				form.submit();
			} );
		} );
	}
}() );
