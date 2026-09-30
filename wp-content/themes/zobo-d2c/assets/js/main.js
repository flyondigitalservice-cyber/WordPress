/**
 * Zobo theme interactions: mobile nav, sticky header state, scroll reveals.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	root.classList.add( 'js' );

	// Mobile navigation.
	var toggle = document.querySelector( '[data-nav-toggle]' );
	var nav = document.querySelector( '[data-nav]' );

	function closeNav() {
		document.body.classList.remove( 'nav-open' );
		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	}

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = document.body.classList.toggle( 'nav-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );

		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) ) {
				closeNav();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && document.body.classList.contains( 'nav-open' ) ) {
				closeNav();
				toggle.focus();
			}
		} );

		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 960 ) {
				closeNav();
			}
		} );
	}

	// Header border once the page scrolls.
	var header = document.querySelector( '[data-header]' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	// Reveal elements as they enter the viewport.
	var items = document.querySelectorAll( '.reveal' );
	if ( ! ( 'IntersectionObserver' in window ) ) {
		items.forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
	);

	items.forEach( function ( el ) {
		// Stagger siblings slightly.
		var siblings = el.parentElement ? Array.prototype.indexOf.call( el.parentElement.children, el ) : 0;
		el.style.transitionDelay = Math.min( siblings, 5 ) * 70 + 'ms';
		observer.observe( el );
	} );
} )();
