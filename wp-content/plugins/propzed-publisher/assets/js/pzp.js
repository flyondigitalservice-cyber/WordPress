/**
 * Propzed Publisher & Builders: small front-end helpers. Vanilla JS, no dependencies.
 *
 *  1. Screen-reader landmarks and labels the theme markup is missing
 *  2. Forms: remember name / phone / email, friendly phone error, no double submit
 *  3. WhatsApp shortcut on every property card
 *  4. Shortlist buttons announce their state, Escape closes the mobile menu
 *  5. Builder page / directory: prefilled enquiry message, live search
 *  6. Publisher area: photo checks, confirm before withdrawing, focus messages
 *
 * Every block is wrapped so that one failure can never stop the theme's own script.
 */
( function () {
	'use strict';

	var data = window.pzpData || {};
	var t = data.i18n || {};

	function each( selector, fn, root ) {
		Array.prototype.forEach.call( ( root || document ).querySelectorAll( selector ), fn );
	}
	function safe( fn ) {
		try { fn(); } catch ( e ) { if ( window.console ) { console.warn( 'pzp:', e ); } }
	}
	function store( key, value ) {
		try {
			if ( undefined === value ) { return JSON.parse( window.localStorage.getItem( key ) || 'null' ); }
			window.localStorage.setItem( key, JSON.stringify( value ) );
		} catch ( e ) { /* Private mode or storage disabled. */ }
		return null;
	}

	function init() {

		/* 1. Landmarks and labels ------------------------------------------------ */
		if ( data.enhance ) {
			safe( function () {
				each( '.pz-topbar', function ( el ) {
					el.setAttribute( 'role', 'region' );
					el.setAttribute( 'aria-label', t.contact || 'Contact' );
				} );
				each( '.pz-mobile-bar', function ( el ) {
					el.setAttribute( 'role', 'navigation' );
					el.setAttribute( 'aria-label', t.quick || 'Quick contact' );
				} );
				each( 'a[target="_blank"]', function ( a ) {
					if ( ! /noopener/.test( a.rel ) ) { a.rel = ( a.rel + ' noopener' ).trim(); }
				} );
			} );
		}

		/* 2. Forms ---------------------------------------------------------------- */
		if ( data.enhance ) {
			safe( function () {
				var saved = store( 'pzpContact' ) || {};
				var map = { pz_name: 'name', pz_seller_name: 'name', pz_phone: 'phone', pz_seller_phone: 'phone', pz_email: 'email', pz_seller_email: 'email' };

				each( 'form.pz-form', function ( form ) {
					// Fill in what the visitor typed last time (never passwords).
					Object.keys( map ).forEach( function ( name ) {
						var input = form.querySelector( '[name="' + name + '"]' );
						if ( input && ! input.value && saved[ map[ name ] ] ) { input.value = saved[ map[ name ] ]; }
					} );

					// Clear message for the phone field instead of the browser's "match the requested format".
					each( 'input[type="tel"]', function ( tel ) {
						tel.setAttribute( 'enterkeyhint', 'next' );
						var check = function () {
							var digits = tel.value.replace( /\D/g, '' ).length;
							tel.setCustomValidity( tel.value && ( digits < 10 || digits > 13 ) ? ( t.phone || '' ) : '' );
						};
						tel.addEventListener( 'input', check );
						tel.addEventListener( 'blur', check );
					}, form );

					form.addEventListener( 'submit', function () {
						var next = {};
						Object.keys( map ).forEach( function ( name ) {
							var input = form.querySelector( '[name="' + name + '"]' );
							if ( input && input.value ) { next[ map[ name ] ] = input.value; }
						} );
						if ( Object.keys( next ).length ) {
							store( 'pzpContact', { name: next.name || saved.name, phone: next.phone || saved.phone, email: next.email || saved.email } );
						}
						// Stop double submissions on slow mobile connections.
						var button = form.querySelector( 'button[type="submit"]' );
						if ( button && ! button.getAttribute( 'aria-busy' ) ) {
							button.setAttribute( 'aria-busy', 'true' );
							button.dataset.pzpLabel = button.textContent;
							window.setTimeout( function () {
								button.disabled = true;
								button.textContent = t.sending || button.textContent;
							}, 0 );
							window.setTimeout( function () {
								button.disabled = false;
								button.removeAttribute( 'aria-busy' );
								button.textContent = button.dataset.pzpLabel;
							}, 15000 );
						}
					} );
				} );

				// After a redirect, move focus to the success / error message so it is announced.
				var notice = document.querySelector( '.pz-notice' );
				if ( notice && /[?&]pz(p)?_msg=/.test( window.location.search ) ) {
					notice.setAttribute( 'tabindex', '-1' );
					notice.focus( { preventScroll: false } );
				}
				// Coming back with the browser's Back button: re-enable buttons.
				window.addEventListener( 'pageshow', function ( event ) {
					if ( event.persisted ) {
						each( 'button[aria-busy="true"]', function ( button ) {
							button.disabled = false;
							button.removeAttribute( 'aria-busy' );
							if ( button.dataset.pzpLabel ) { button.textContent = button.dataset.pzpLabel; }
						} );
					}
				} );
			} );
		}

		/* 3. WhatsApp shortcut on cards -------------------------------------------- */
		if ( data.enhance && data.cardWhatsapp && data.whatsapp ) {
			safe( function () {
				var icon = document.querySelector( '.pz-float-wa svg' );
				var addWa = function ( card ) {
					var fav = card.querySelector( '.pz-fav' );
					var link = card.querySelector( '.pz-card__title a' );
					if ( ! link || card.querySelector( '.pzp-card-wa' ) ) { return; }
					var title = link.textContent.trim();
					var a = document.createElement( 'a' );
					a.className = 'pzp-card-wa';
					a.target = '_blank';
					a.rel = 'noopener';
					a.href = 'https://wa.me/' + data.whatsapp + '?text=' + encodeURIComponent( ( t.waText || '%1$s %2$s' ).replace( '%1$s', title ).replace( '%2$s', link.href ) );
					a.setAttribute( 'aria-label', ( t.waLabel || '%s' ).replace( '%s', title ) );
					if ( icon ) { a.appendChild( icon.cloneNode( true ) ); }
					var label = document.createElement( 'span' );
					label.textContent = t.waCard || 'WhatsApp';
					a.appendChild( label );
					// Right after the shortlist button, so keyboard order is heart, WhatsApp, title.
					if ( fav && fav.parentNode === card ) {
						card.insertBefore( a, fav.nextSibling );
					} else {
						card.appendChild( a );
					}
				};
				each( '.pz-card', addWa );
				// Cards added later by the theme (recently viewed, shortlist).
				if ( window.MutationObserver ) {
					new MutationObserver( function ( records ) {
						records.forEach( function ( record ) {
							Array.prototype.forEach.call( record.addedNodes, function ( node ) {
								if ( 1 !== node.nodeType ) { return; }
								if ( node.matches && node.matches( '.pz-card' ) ) { addWa( node ); }
								if ( node.querySelectorAll ) { each( '.pz-card', addWa, node ); }
							} );
						} );
					} ).observe( document.body, { childList: true, subtree: true } );
				}
			} );
		}

		/* 4. Shortlist state, Escape closes the menu ------------------------------- */
		if ( data.enhance ) {
			safe( function () {
				var sync = function () {
					each( '[data-pz-fav]', function ( button ) {
						button.setAttribute( 'aria-pressed', button.classList.contains( 'is-active' ) ? 'true' : 'false' );
					} );
				};
				sync();
				window.setTimeout( sync, 600 );
				document.addEventListener( 'click', function ( event ) {
					if ( event.target.closest && event.target.closest( '[data-pz-fav]' ) ) { window.setTimeout( sync, 50 ); }
				} );
				document.addEventListener( 'keydown', function ( event ) {
					if ( 'Escape' !== event.key ) { return; }
					var toggle = document.querySelector( '.pz-nav-toggle[aria-expanded="true"]' );
					if ( toggle ) { toggle.click(); toggle.focus(); }
				} );
			} );
		}

		/* 5. Builder page and directory -------------------------------------------- */
		safe( function () {
			var enquire = document.querySelector( '[data-pzp-message]' );
			var message = enquire && enquire.querySelector( 'textarea[name="pz_message"]' );
			if ( message && ! message.value ) { message.value = enquire.getAttribute( 'data-pzp-message' ); }

			var search = document.querySelector( '[data-pzp-bsearch]' );
			var list = document.querySelector( '[data-pzp-blist]' );
			if ( search && list ) {
				var empty = document.querySelector( '[data-pzp-bempty]' );
				search.addEventListener( 'input', function () {
					var q = search.value.trim().toLowerCase();
					var shown = 0;
					each( '.pzp-bcard', function ( card ) {
						var match = ! q || ( card.getAttribute( 'data-pzp-name' ) || '' ).indexOf( q ) !== -1;
						card.hidden = ! match;
						if ( match ) { shown++; }
					}, list );
					if ( empty ) { empty.hidden = shown > 0; }
				} );
			}
		} );

		/* 6. Publisher area --------------------------------------------------------- */
		safe( function () {
			each( '[data-pzp-photos]', function ( input ) {
				var status = document.querySelector( '[data-pzp-photo-status]' );
				input.addEventListener( 'change', function () {
					var files = Array.prototype.slice.call( input.files || [] );
					var big = files.filter( function ( f ) { return f.size > 5 * 1024 * 1024; } ).length;
					var text = ( t.photos || '%d' ).replace( '%d', files.length );
					if ( big ) { text += '. ' + ( t.tooBig || '' ); }
					if ( files.length > 10 ) { text += ' ' + ( t.tooMany || '' ); }
					if ( status ) { status.textContent = text; }
				} );
			} );
			each( 'form[data-pzp-confirm]', function ( form ) {
				form.addEventListener( 'submit', function ( event ) {
					if ( ! window.confirm( form.getAttribute( 'data-pzp-confirm' ) ) ) { event.preventDefault(); }
				} );
			} );
			var focus = document.querySelector( '[data-pzp-focus]' );
			if ( focus && ! document.querySelector( '.pzp-notice' ) ) {
				var first = focus.querySelector( 'input:not([type="hidden"]):not([tabindex="-1"])' );
				if ( first ) { focus.scrollIntoView( { block: 'start' } ); first.focus( { preventScroll: true } ); }
			}
			var pzpNotice = document.querySelector( '.pzp-notice' );
			if ( pzpNotice ) { pzpNotice.focus(); }
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
