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
	var KEY = 'hf_bag_v1';
	var Bag = {
		read: function () {
			try { return JSON.parse( localStorage.getItem( KEY ) ) || []; } catch ( e ) { return []; }
		},
		write: function ( lines ) {
			try { localStorage.setItem( KEY, JSON.stringify( lines ) ); } catch ( e ) {}
			Bag.render();
			document.dispatchEvent( new CustomEvent( 'hf:bag' ) );
		},
		add: function ( line ) {
			var lines = Bag.read();
			var sig = Bag.sig( line );
			var found = lines.filter( function ( l ) { return Bag.sig( l ) === sig; } )[ 0 ];
			if ( found ) {
				found.qty = Math.min( 10, found.qty + 1 );
			} else {
				line.qty = 1;
				lines.push( line );
			}
			Bag.write( lines );
		},
		sig: function ( l ) {
			return [ l.id, l.fit, l.size, JSON.stringify( l.measurements || {} ) ].join( '|' );
		},
		count: function () {
			return Bag.read().reduce( function ( n, l ) { return n + l.qty; }, 0 );
		},
		subtotal: function () {
			return Bag.read().reduce( function ( n, l ) { return n + l.price * l.qty; }, 0 );
		},
		lineMeta: function ( l ) {
			if ( l.fit === 'measure' ) {
				var hasM = l.measurements && Object.keys( l.measurements ).length;
				return 'Made to measure · ' + ( hasM ? 'measurements provided' : 'tailor visit' );
			}
			return 'Ready to wear' + ( l.size ? ' · ' + l.size : '' ) + ( l.express ? ' · 2-hour eligible' : '' );
		},
		render: function () {
			var lines = Bag.read();
			$$( '[data-bag-count]' ).forEach( function ( el ) {
				var c = Bag.count();
				el.textContent = c;
				el.setAttribute( 'data-count', c );
			} );
			var box = $( '[data-bag-lines]' );
			if ( box ) {
				box.innerHTML = lines.length ? lines.map( function ( l, i ) {
					return '<div class="line">' +
						'<img src="' + esc( l.image ) + '" alt="" referrerpolicy="no-referrer">' +
						'<div><a class="line__title" href="' + esc( l.url ) + '">' + esc( l.title ) + '</a>' +
						'<div class="line__meta">' + esc( Bag.lineMeta( l ) ) + '</div>' +
						'<div class="line__qty"><button type="button" data-qty="-1" data-i="' + i + '" aria-label="Less">−</button><span>' + l.qty + '</span><button type="button" data-qty="1" data-i="' + i + '" aria-label="More">+</button></div>' +
						'<button type="button" class="line__remove" data-remove="' + i + '">Remove</button></div>' +
						'<div class="line__price">' + money( l.price * l.qty ) + '</div>' +
					'</div>';
				} ).join( '' ) : '<p class="empty-bag">' + esc( cfg.i18n && cfg.i18n.empty ) + '</p>';
			}
			var sub = $( '[data-bag-subtotal]' );
			if ( sub ) { sub.textContent = money( Bag.subtotal() ); }
		}
	};
	window.HFBag = Bag;
	window.HFUtil = { esc: esc, money: money, toast: toast, $: $, $$: $$ };

	document.addEventListener( 'click', function ( e ) {
		var t = e.target.closest( '[data-qty],[data-remove]' );
		if ( ! t ) { return; }
		var lines = Bag.read();
		if ( t.hasAttribute( 'data-remove' ) ) {
			lines.splice( Number( t.getAttribute( 'data-remove' ) ), 1 );
		} else {
			var l = lines[ Number( t.getAttribute( 'data-i' ) ) ];
			if ( l ) {
				l.qty += Number( t.getAttribute( 'data-qty' ) );
				if ( l.qty < 1 ) { lines.splice( lines.indexOf( l ), 1 ); }
				if ( l.qty > 10 ) { l.qty = 10; }
			}
		}
		Bag.write( lines );
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
		Bag.render();
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
	var product = $( '[data-product]' );
	if ( product ) {
		var data = JSON.parse( product.getAttribute( 'data-product' ) );
		var form = $( '[data-add-form]', product );
		var promise = $( '[data-promise-text]', product );

		function currentFit() {
			if ( data.kind === 'suits' ) { return 'measure'; }
			var r = $( 'input[name="fit"]:checked', form );
			return r ? r.value : 'ready';
		}
		function syncFit() {
			var fit = currentFit();
			$$( '[data-show-fit]', form ).forEach( function ( el ) {
				el.hidden = el.getAttribute( 'data-show-fit' ) !== fit;
			} );
			if ( promise && data.kind !== 'suits' ) {
				promise.textContent = promise.getAttribute( fit === 'measure' ? 'data-measure' : 'data-ready' );
			}
		}
		form.addEventListener( 'change', syncFit );
		syncFit();

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			var fit = currentFit();
			var measurements = {};
			if ( fit === 'measure' ) {
				$$( 'input[name^="m_"]', form ).forEach( function ( inp ) {
					if ( inp.value.trim() ) { measurements[ inp.name.slice( 2 ) ] = inp.value.trim(); }
				} );
			}
			var size = '';
			if ( fit === 'ready' ) {
				var s = $( 'input[name="size"]:checked', form );
				var c = $( 'input[name="cut"]:checked', form );
				size = ( s ? s.value + '″' : '' ) + ( c ? ' ' + c.value : '' );
			}
			Bag.add( {
				id: data.id,
				title: data.title,
				price: data.price,
				kind: data.kind,
				express: data.express && fit === 'ready',
				image: data.image,
				url: data.url,
				fit: fit,
				size: size,
				measurements: measurements
			} );
			toast( cfg.i18n && cfg.i18n.added );
			openBag();
		} );
	}

	Bag.render();
}() );
