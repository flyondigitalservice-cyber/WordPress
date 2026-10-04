/**
 * FlavorKit front-end interactions. Vanilla JS, no dependencies.
 *
 * - Sticky header state
 * - Mobile menu + cart drawers (focus trap, Esc to close)
 * - Search panel toggle
 * - Reviews slider buttons
 * - Scroll reveal animations
 * - Quantity +/- buttons on product & cart pages
 * - Opens the cart drawer after AJAX add-to-cart
 */
( function () {
	'use strict';

	const body = document.body;
	const overlay = document.querySelector( '.fk-overlay' );
	let activeDrawer = null;
	let lastFocus = null;

	/* ---------- Sticky header ---------- */
	const header = document.querySelector( '.fk-header' );
	if ( header ) {
		const onScroll = () => header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	/* ---------- Drawers ---------- */
	const focusable = ( el ) =>
		el.querySelectorAll( 'a[href], button:not([disabled]), input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])' );

	function openDrawer( drawer, trigger ) {
		if ( ! drawer ) {
			return;
		}
		if ( activeDrawer && activeDrawer !== drawer ) {
			closeDrawer( false );
		}
		lastFocus = trigger || document.activeElement;
		activeDrawer = drawer;
		drawer.classList.add( 'is-open' );
		drawer.setAttribute( 'aria-hidden', 'false' );
		if ( overlay ) {
			overlay.hidden = false;
		}
		body.classList.add( 'fk-lock' );
		document.querySelectorAll( '[aria-controls="' + drawer.id + '"]' ).forEach( ( btn ) => btn.setAttribute( 'aria-expanded', 'true' ) );
		const first = focusable( drawer )[ 0 ];
		if ( first ) {
			window.setTimeout( () => first.focus(), 60 );
		}
	}

	function closeDrawer( restoreFocus = true ) {
		if ( ! activeDrawer ) {
			return;
		}
		const drawer = activeDrawer;
		drawer.classList.remove( 'is-open' );
		drawer.setAttribute( 'aria-hidden', 'true' );
		document.querySelectorAll( '[aria-controls="' + drawer.id + '"]' ).forEach( ( btn ) => btn.setAttribute( 'aria-expanded', 'false' ) );
		activeDrawer = null;
		if ( overlay ) {
			overlay.hidden = true;
		}
		body.classList.remove( 'fk-lock' );
		if ( restoreFocus && lastFocus ) {
			lastFocus.focus();
		}
	}

	document.addEventListener( 'click', ( event ) => {
		const menuBtn = event.target.closest( '.fk-menu-toggle' );
		if ( menuBtn ) {
			openDrawer( document.getElementById( 'fk-mobile-nav' ), menuBtn );
			return;
		}

		const cartBtn = event.target.closest( '.fk-cart-toggle' );
		const cartDrawer = document.getElementById( 'fk-cart-drawer' );
		// Cart & checkout pages: follow the link instead of opening the drawer.
		if ( cartBtn && cartDrawer && ! body.classList.contains( 'woocommerce-cart' ) && ! body.classList.contains( 'woocommerce-checkout' ) ) {
			event.preventDefault();
			openDrawer( cartDrawer, cartBtn );
			return;
		}

		if ( event.target.closest( '.fk-drawer-close' ) || event.target === overlay ) {
			closeDrawer();
			return;
		}

		// Close the mobile menu when an in-page anchor is followed.
		if ( activeDrawer && activeDrawer.id === 'fk-mobile-nav' && event.target.closest( 'a[href*="#"]' ) ) {
			closeDrawer( false );
		}

		const searchBtn = event.target.closest( '.fk-search-toggle' );
		if ( searchBtn ) {
			const panel = document.getElementById( 'fk-search' );
			if ( panel ) {
				const willOpen = panel.hidden;
				panel.hidden = ! willOpen;
				searchBtn.setAttribute( 'aria-expanded', String( willOpen ) );
				if ( willOpen ) {
					const input = panel.querySelector( 'input[type="search"]' );
					if ( input ) {
						input.focus();
					}
				}
			}
		}
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' ) {
			if ( activeDrawer ) {
				closeDrawer();
			}
			const panel = document.getElementById( 'fk-search' );
			if ( panel && ! panel.hidden ) {
				panel.hidden = true;
				const btn = document.querySelector( '.fk-search-toggle' );
				if ( btn ) {
					btn.setAttribute( 'aria-expanded', 'false' );
					btn.focus();
				}
			}
		}

		// Keep keyboard focus inside an open drawer.
		if ( event.key === 'Tab' && activeDrawer ) {
			const items = Array.from( focusable( activeDrawer ) ).filter( ( el ) => el.offsetParent !== null );
			if ( ! items.length ) {
				return;
			}
			const first = items[ 0 ];
			const last = items[ items.length - 1 ];
			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );

	// WooCommerce fires jQuery events after AJAX add-to-cart.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'added_to_cart', () => {
			openDrawer( document.getElementById( 'fk-cart-drawer' ) );
		} );
	}

	/* ---------- Reviews slider ---------- */
	document.querySelectorAll( '.fk-slider-prev, .fk-slider-next' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const track = document.getElementById( btn.getAttribute( 'aria-controls' ) );
			if ( ! track ) {
				return;
			}
			const card = track.firstElementChild;
			const step = card ? card.getBoundingClientRect().width + 22 : track.clientWidth * 0.8;
			const dir = btn.classList.contains( 'fk-slider-prev' ) ? -1 : 1;
			const rtl = document.documentElement.dir === 'rtl' ? -1 : 1;
			track.scrollBy( { left: step * dir * rtl, behavior: 'smooth' } );
		} );
	} );

	/* ---------- Scroll reveal ---------- */
	const reveals = document.querySelectorAll( '.fk-reveal' );
	if ( 'IntersectionObserver' in window ) {
		const io = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						io.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);
		reveals.forEach( ( el ) => io.observe( el ) );
	} else {
		reveals.forEach( ( el ) => el.classList.add( 'is-visible' ) );
	}

	/* ---------- Quantity steppers ---------- */
	function addSteppers( root ) {
		root.querySelectorAll( '.quantity:not(.fk-has-stepper)' ).forEach( ( wrap ) => {
			const input = wrap.querySelector( 'input.qty' );
			if ( ! input || input.type === 'hidden' ) {
				return;
			}
			wrap.classList.add( 'fk-has-stepper' );
			const make = ( label, delta, text ) => {
				const b = document.createElement( 'button' );
				b.type = 'button';
				b.className = 'fk-qty-btn';
				b.setAttribute( 'aria-label', label );
				b.textContent = text;
				b.addEventListener( 'click', () => {
					const step = parseFloat( input.step ) || 1;
					const min = input.min !== '' ? parseFloat( input.min ) : 0;
					const max = input.max !== '' ? parseFloat( input.max ) : Infinity;
					const next = Math.min( max, Math.max( min, ( parseFloat( input.value ) || 0 ) + delta * step ) );
					input.value = next;
					// Native bubbling event also reaches WooCommerce's jQuery listeners.
					input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				} );
				return b;
			};
			wrap.insertBefore( make( 'Decrease quantity', -1, '−' ), input );
			wrap.appendChild( make( 'Increase quantity', 1, '+' ) );
		} );
	}
	addSteppers( document );
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_cart_totals updated_wc_div', () => addSteppers( document ) );
	}
}() );
