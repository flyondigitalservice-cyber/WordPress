/**
 * Product compare: selection in localStorage, table built from WooCommerce Store API.
 * All dynamic text is inserted with textContent (never innerHTML).
 */
( function () {
	'use strict';

	var cfg = window.bzrCompare;
	if ( ! cfg ) {
		return;
	}
	var KEY = 'bzr_compare';

	function read() {
		try {
			var ids = JSON.parse( window.localStorage.getItem( KEY ) || '[]' );
			return Array.isArray( ids ) ? ids.filter( Number.isInteger ).slice( 0, cfg.max ) : [];
		} catch ( e ) {
			return [];
		}
	}
	function write( ids ) {
		try {
			window.localStorage.setItem( KEY, JSON.stringify( ids ) );
		} catch ( e ) { /* storage disabled: selection simply will not persist */ }
		sync();
	}
	function el( tag, cls, text ) {
		var node = document.createElement( tag );
		if ( cls ) {
			node.className = cls;
		}
		if ( text !== undefined ) {
			node.textContent = text;
		}
		return node;
	}

	function toggle( id ) {
		var ids = read();
		var at = ids.indexOf( id );
		if ( at > -1 ) {
			ids.splice( at, 1 );
		} else if ( ids.length >= cfg.max ) {
			window.alert( cfg.i18n.full );
			return false;
		} else {
			ids.push( id );
		}
		write( ids );
		return true;
	}

	/* ---- Checkboxes on product cards ---- */
	function decorateCards() {
		document.querySelectorAll( '.wc-block-product:not([data-bzr-cmp])' ).forEach( function ( card ) {
			var match = ( card.className.match( /\bpost-(\d+)\b/ ) || [] )[ 1 ];
			card.setAttribute( 'data-bzr-cmp', '1' );
			if ( ! match ) {
				return;
			}
			var label = el( 'label', 'bzr-compare-check' );
			var box = el( 'input' );
			box.type = 'checkbox';
			box.setAttribute( 'data-bzr-compare-id', match );
			label.appendChild( box );
			label.appendChild( el( 'span', '', ' ' + cfg.i18n.compare ) );
			card.appendChild( label );
		} );
	}

	function sync() {
		var ids = read();
		document.querySelectorAll( '[data-bzr-compare-id]' ).forEach( function ( node ) {
			var on = ids.indexOf( parseInt( node.getAttribute( 'data-bzr-compare-id' ), 10 ) ) > -1;
			if ( node.type === 'checkbox' ) {
				node.checked = on;
			} else {
				node.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
				node.classList.toggle( 'is-active', on );
			}
		} );
		var tray = document.querySelector( '[data-bzr-tray]' );
		if ( tray ) {
			tray.hidden = ids.length === 0 || !! document.querySelector( '[data-bzr-compare-page]' );
			tray.querySelector( '[data-bzr-tray-count]' ).textContent = ids.length + ' ' + cfg.i18n.selected;
			tray.querySelector( '[data-bzr-tray-link]' ).href = cfg.page;
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var btn = event.target.closest( 'button[data-bzr-compare-id]' );
		if ( btn ) {
			toggle( parseInt( btn.getAttribute( 'data-bzr-compare-id' ), 10 ) );
			return;
		}
		if ( event.target.closest( '[data-bzr-tray-clear]' ) ) {
			write( [] );
			renderPage();
		}
	} );
	document.addEventListener( 'change', function ( event ) {
		var box = event.target;
		if ( box.matches && box.matches( 'input[type="checkbox"][data-bzr-compare-id]' ) ) {
			if ( ! toggle( parseInt( box.getAttribute( 'data-bzr-compare-id' ), 10 ) ) ) {
				box.checked = false;
			}
		}
	} );

	/* ---- Compare page ---- */
	function money( prices ) {
		if ( ! prices || prices.price === undefined ) {
			return '';
		}
		var minor = parseInt( prices.currency_minor_unit, 10 ) || 0;
		var value = ( parseInt( prices.price, 10 ) / Math.pow( 10, minor ) ).toFixed( minor );
		return ( prices.currency_prefix || '' ) + value + ( prices.currency_suffix || '' );
	}

	function row( head, cells ) {
		var tr = el( 'tr' );
		var th = el( 'th', '', head );
		th.scope = 'row';
		tr.appendChild( th );
		cells.forEach( function ( cell ) {
			var td = el( 'td' );
			if ( cell instanceof Node ) {
				td.appendChild( cell );
			} else {
				td.textContent = cell;
			}
			tr.appendChild( td );
		} );
		return tr;
	}

	function buildTable( products ) {
		var wrap = el( 'div', 'bzr-compare__scroll' );
		var table = el( 'table', 'bzr-compare__table' );
		var body = el( 'tbody' );

		body.appendChild( row( '', products.map( function ( p ) {
			var box = el( 'div', 'bzr-compare__product' );
			if ( p.images && p.images[ 0 ] ) {
				var img = el( 'img' );
				img.src = p.images[ 0 ].thumbnail || p.images[ 0 ].src;
				img.alt = '';
				img.loading = 'lazy';
				box.appendChild( img );
			}
			var link = el( 'a', '', p.name );
			link.href = p.permalink;
			box.appendChild( link );
			var rm = el( 'button', 'bzr-compare__remove', cfg.i18n.remove );
			rm.type = 'button';
			rm.addEventListener( 'click', function () {
				toggle( p.id );
				renderPage();
			} );
			box.appendChild( rm );
			return box;
		} ) ) );

		body.appendChild( row( cfg.i18n.price, products.map( function ( p ) { return money( p.prices ); } ) ) );
		body.appendChild( row( cfg.i18n.rating, products.map( function ( p ) {
			return parseFloat( p.average_rating ) > 0 ? p.average_rating + ' / 5' : '-';
		} ) ) );
		body.appendChild( row( cfg.i18n.stock, products.map( function ( p ) { return p.is_in_stock ? cfg.i18n.inStock : cfg.i18n.outStock; } ) ) );

		// Union of attribute names across products, in first-seen order.
		var names = [];
		products.forEach( function ( p ) {
			( p.attributes || [] ).forEach( function ( a ) {
				if ( names.indexOf( a.name ) === -1 ) {
					names.push( a.name );
				}
			} );
		} );
		names.forEach( function ( name ) {
			body.appendChild( row( name, products.map( function ( p ) {
				var attr = ( p.attributes || [] ).filter( function ( a ) { return a.name === name; } )[ 0 ];
				return attr ? attr.terms.map( function ( t ) { return t.name; } ).join( ', ' ) : '-';
			} ) ) );
		} );

		body.appendChild( row( '', products.map( function ( p ) {
			var a = el( 'a', 'wp-element-button', cfg.i18n.view );
			a.href = p.permalink;
			return a;
		} ) ) );

		table.appendChild( body );
		wrap.appendChild( table );
		return wrap;
	}

	function renderPage() {
		var host = document.querySelector( '[data-bzr-compare-page]' );
		if ( ! host ) {
			return;
		}
		var ids = read();
		host.textContent = '';
		if ( ids.length === 0 ) {
			host.appendChild( el( 'p', '', cfg.i18n.empty ) );
			var browse = el( 'a', 'wp-element-button', cfg.i18n.browse );
			browse.href = cfg.i18n.shop;
			host.appendChild( browse );
			return;
		}
		fetch( cfg.api + '?per_page=' + cfg.max + '&include=' + ids.join( ',' ), { credentials: 'same-origin' } )
			.then( function ( res ) {
				if ( ! res.ok ) {
					throw new Error( 'bad response' );
				}
				return res.json();
			} )
			.then( function ( products ) {
				products.sort( function ( a, b ) { return ids.indexOf( a.id ) - ids.indexOf( b.id ); } );
				host.textContent = '';
				host.appendChild( buildTable( products ) );
			} )
			.catch( function () {
				host.textContent = '';
				host.appendChild( el( 'p', '', cfg.i18n.error ) );
			} );
	}

	var queued = false;
	function schedule() {
		if ( queued ) {
			return;
		}
		queued = true;
		window.requestAnimationFrame( function () {
			queued = false;
			decorateCards();
			sync();
		} );
	}

	function hasNewCards( records ) {
		// Only react to product cards being added, never to our own DOM changes (avoids a feedback loop).
		return records.some( function ( record ) {
			return Array.prototype.some.call( record.addedNodes, function ( node ) {
				return node.nodeType === 1 && ( node.matches( '.wc-block-product' ) || node.querySelector( '.wc-block-product:not([data-bzr-cmp])' ) );
			} );
		} );
	}

	decorateCards();
	sync();
	renderPage();
	new MutationObserver( function ( records ) {
		if ( hasNewCards( records ) ) {
			schedule();
		}
	} ).observe( document.body, { childList: true, subtree: true } );
}() );
