/**
 * Propzed Projects: interactive floor plan + building chart.
 *
 * Reads the JSON printed by pzs_render_inventory(). For each plan:
 *   - plan image with clickable unit hotspots coloured by status (for the chosen floor)
 *   - building chart (floors x units) that doubles as the floor picker
 *   - unit card with type, carpet, facing, price and Enquire / WhatsApp buttons
 * Only public data is in the JSON: no buyer names or phone numbers.
 */
( function () {
	'use strict';

	var cfg = window.pzsInv || { i18n: {} };
	var t = function ( key ) {
		return ( cfg.i18n && cfg.i18n[ key ] ) || key;
	};

	function el( tag, attrs, children ) {
		var node = document.createElement( tag );
		Object.keys( attrs || {} ).forEach( function ( k ) {
			if ( 'text' === k ) {
				node.textContent = attrs[ k ];
			} else if ( 'class' === k ) {
				node.className = attrs[ k ];
			} else if ( null !== attrs[ k ] && undefined !== attrs[ k ] ) {
				node.setAttribute( k, attrs[ k ] );
			}
		} );
		( children || [] ).forEach( function ( c ) {
			if ( c ) {
				node.appendChild( 'string' === typeof c ? document.createTextNode( c ) : c );
			}
		} );
		return node;
	}

	function Viewer( section, data ) {
		this.section = section;
		this.data = data;
		this.app = section.querySelector( '[data-pzs-inv]' );
		this.planIndex = 0;
		this.config = '';
		this.onlyAvail = false;
		this.selected = null; // { slot, floor }.
		this.render();
	}

	Viewer.prototype.plan = function () {
		return this.data.plans[ this.planIndex ];
	};

	Viewer.prototype.unit = function ( slotId, floor ) {
		return this.plan().units[ slotId + '|' + floor ] || { code: '', status: 'available', price: '' };
	};

	Viewer.prototype.slot = function ( slotId ) {
		var s = this.plan().slots.filter( function ( x ) {
			return x.id === slotId;
		} );
		return s[ 0 ];
	};

	Viewer.prototype.matches = function ( slot ) {
		return ! this.config || slot.config === this.config;
	};

	// Default floor: the highest one that still has an available unit.
	Viewer.prototype.defaultFloor = function () {
		var plan = this.plan();
		var self = this;
		for ( var i = plan.floors.length - 1; i >= 0; i-- ) {
			var f = plan.floors[ i ];
			var free = plan.slots.some( function ( s ) {
				return 'available' === self.unit( s.id, f ).status;
			} );
			if ( free ) {
				return f;
			}
		}
		return plan.floors[ plan.floors.length - 1 ];
	};

	Viewer.prototype.render = function () {
		var self = this;
		var data = this.data;
		if ( undefined === this.floor || this.plan().floors.indexOf( this.floor ) < 0 ) {
			this.floor = this.defaultFloor();
		}
		this.rows = {};
		this.cells = {};
		this.app.textContent = '';

		// Plan tabs.
		if ( data.plans.length > 1 ) {
			var tabs = el( 'div', { class: 'pzs-inv__tabs', role: 'tablist' } );
			data.plans.forEach( function ( p, i ) {
				var b = el( 'button', { type: 'button', role: 'tab', 'aria-selected': i === self.planIndex ? 'true' : 'false', text: p.name || 'Plan ' + ( i + 1 ) } );
				b.addEventListener( 'click', function () {
					self.planIndex = i;
					self.config = '';
					self.selected = null;
					self.render();
				} );
				tabs.appendChild( b );
			} );
			this.app.appendChild( tabs );
		}

		// Filters + legend.
		var plan = this.plan();
		var configs = [];
		plan.slots.forEach( function ( s ) {
			if ( s.config && configs.indexOf( s.config ) < 0 ) {
				configs.push( s.config );
			}
		} );
		var bar = el( 'div', { class: 'pzs-inv__bar' } );
		if ( configs.length > 1 ) {
			var chips = el( 'div', { class: 'pzs-inv__chips', role: 'group', 'aria-label': 'Filter' } );
			[ '' ].concat( configs.sort() ).forEach( function ( c ) {
				var b = el( 'button', { type: 'button', class: 'pzs-chip', 'aria-pressed': c === self.config ? 'true' : 'false', text: c || t( 'allTypes' ) } );
				b.addEventListener( 'click', function () {
					self.config = c;
					self.render();
				} );
				chips.appendChild( b );
			} );
			bar.appendChild( chips );
		}
		var only = el( 'label', { class: 'pzs-inv__only' } );
		var cb = el( 'input', { type: 'checkbox' } );
		cb.checked = this.onlyAvail;
		cb.addEventListener( 'change', function () {
			self.onlyAvail = cb.checked;
			self.paint();
		} );
		only.appendChild( cb );
		only.appendChild( document.createTextNode( ' ' + t( 'onlyAvail' ) ) );
		bar.appendChild( only );
		var legend = el( 'ul', { class: 'pzs-legend' } );
		[ 'available', 'hold', 'booked' ].forEach( function ( s ) {
			legend.appendChild( el( 'li', { class: 'is-' + s, text: t( s ) } ) );
		} );
		bar.appendChild( legend );
		this.app.appendChild( bar );

		var body = el( 'div', { class: 'pzs-inv__body' + ( plan.image ? '' : ' is-chart-only' ) } );

		// Plan image with hotspots.
		this.spots = {};
		if ( plan.image ) {
			var wrap = el( 'div', { class: 'pzs-inv__plan' } );
			var stage = el( 'div', { class: 'pzs-inv__stage' } );
			stage.style.aspectRatio = plan.image.w && plan.image.h ? plan.image.w + ' / ' + plan.image.h : '';
			stage.appendChild( el( 'img', { src: plan.image.url, alt: plan.image.alt || '', loading: 'lazy', width: plan.image.w || null, height: plan.image.h || null } ) );
			plan.slots.forEach( function ( s ) {
				if ( ! s.box ) {
					return;
				}
				var b = el( 'button', { type: 'button', class: 'pzs-spot' } );
				b.style.left = s.box[ 0 ] + '%';
				b.style.top = s.box[ 1 ] + '%';
				b.style.width = s.box[ 2 ] + '%';
				b.style.height = s.box[ 3 ] + '%';
				b.appendChild( el( 'span', { text: s.label } ) );
				b.addEventListener( 'click', function () {
					self.select( s.id, self.floor );
				} );
				stage.appendChild( b );
				self.spots[ s.id ] = b;
			} );
			wrap.appendChild( stage );
			this.floorLabel = el( 'p', { class: 'pzs-inv__floor' } );
			wrap.appendChild( this.floorLabel );
			body.appendChild( wrap );
		}

		// Building chart.
		var side = el( 'div', { class: 'pzs-inv__side' } );
		var multi = plan.floors.length > 1 || ! plan.image;
		if ( multi ) {
			var chart = el( 'div', { class: 'pzs-inv__chart', role: 'grid', 'aria-label': t( 'building' ) } );
			plan.floors.slice().reverse().forEach( function ( f ) {
				var row = el( 'div', { class: 'pzs-row', role: 'row' } );
				var head = el( 'button', { type: 'button', class: 'pzs-row__floor', role: 'rowheader', text: '' === f ? '' : f } );
				head.addEventListener( 'click', function () {
					self.floor = f;
					self.paint();
				} );
				row.appendChild( head );
				plan.slots.forEach( function ( s ) {
					var u = self.unit( s.id, f );
					var c = el( 'button', { type: 'button', role: 'gridcell', class: 'pzs-cell', title: u.code + ( s.config ? ' · ' + s.config : '' ), 'aria-label': u.code + ', ' + ( s.config || '' ) + ', ' + t( u.status ) } );
					c.appendChild( el( 'span', { text: s.label } ) );
					c.addEventListener( 'click', function () {
						self.floor = f;
						self.select( s.id, f );
					} );
					row.appendChild( c );
					self.cells[ s.id + '|' + f ] = c;
				} );
				self.rows[ f ] = row;
				chart.appendChild( row );
			} );
			side.appendChild( chart );
		}
		this.detail = el( 'div', { class: 'pzs-inv__detail', 'aria-live': 'polite' }, [ el( 'p', { class: 'pzs-inv__hint', text: t( 'pick' ) } ) ] );
		side.appendChild( this.detail );
		body.appendChild( side );
		this.app.appendChild( body );

		this.paint();
		if ( this.selected ) {
			this.select( this.selected.slot, this.selected.floor );
		}
	};

	// Colour hotspots for the current floor and the chart cells.
	Viewer.prototype.paint = function () {
		var self = this;
		var plan = this.plan();
		plan.slots.forEach( function ( s ) {
			var spot = self.spots[ s.id ];
			if ( spot ) {
				var u = self.unit( s.id, self.floor );
				spot.className = 'pzs-spot is-' + u.status + ( self.matches( s ) ? '' : ' is-dim' ) + ( self.onlyAvail && 'available' !== u.status ? ' is-hidden' : '' );
				spot.setAttribute( 'aria-label', u.code + ', ' + ( s.config || '' ) + ', ' + t( u.status ) );
			}
			plan.floors.forEach( function ( f ) {
				var c = self.cells[ s.id + '|' + f ];
				if ( c ) {
					var st = self.unit( s.id, f ).status;
					c.className = 'pzs-cell is-' + st + ( self.matches( s ) ? '' : ' is-dim' ) + ( self.onlyAvail && 'available' !== st ? ' is-hidden' : '' );
				}
			} );
		} );
		Object.keys( this.rows ).forEach( function ( f ) {
			self.rows[ f ].classList.toggle( 'is-current', f === String( self.floor ) );
		} );
		if ( this.floorLabel ) {
			this.floorLabel.textContent = ( plan.name ? plan.name + ' · ' : '' ) + ( '' === this.floor ? '' : t( 'floor' ) + ' ' + this.floor );
		}
		this.section.querySelectorAll( '.is-selected' ).forEach( function ( n ) {
			n.classList.remove( 'is-selected' );
		} );
		if ( this.selected ) {
			var spot = this.spots[ this.selected.slot ];
			if ( spot && this.selected.floor === this.floor ) {
				spot.classList.add( 'is-selected' );
			}
			var cell = this.cells[ this.selected.slot + '|' + this.selected.floor ];
			if ( cell ) {
				cell.classList.add( 'is-selected' );
			}
		}
	};

	Viewer.prototype.select = function ( slotId, floor ) {
		var self = this;
		var s = this.slot( slotId );
		var u = this.unit( slotId, floor );
		this.selected = { slot: slotId, floor: floor };
		this.floor = floor;
		this.paint();

		var rows = [];
		var add = function ( label, value ) {
			if ( value ) {
				rows.push( el( 'div', {}, [ el( 'dt', { text: label } ), el( 'dd', { text: value } ) ] ) );
			}
		};
		add( t( 'floor' ), '' === floor ? '' : String( floor ) );
		add( t( 'carpet' ), s.carpet );
		add( t( 'facing' ), s.facing );
		if ( 'available' === u.status ) {
			add( t( 'price' ), u.price || t( 'onRequest' ) );
		}

		var card = el( 'div', { class: 'pzs-unit is-' + u.status }, [
			el( 'div', { class: 'pzs-unit__head' }, [
				el( 'strong', { text: u.code } ),
				s.config ? el( 'span', { class: 'pzs-unit__type', text: s.config } ) : null,
				el( 'span', { class: 'pzs-unit__status', text: t( u.status ) } ),
			] ),
			el( 'dl', { class: 'pzs-unit__facts' }, rows ),
		] );

		if ( 'available' === u.status ) {
			var msg = ( t( 'msg' ) || '' ).replace( '%1$s', u.code ).replace( '%2$s', this.data.title );
			var actions = el( 'div', { class: 'pzs-unit__actions' } );
			var ask = el( 'button', { type: 'button', class: 'pz-btn pz-btn--gold', text: t( 'enquire' ) } );
			ask.addEventListener( 'click', function () {
				self.enquire( msg );
			} );
			actions.appendChild( ask );
			if ( cfg.whatsapp ) {
				actions.appendChild( el( 'a', { class: 'pz-btn pz-btn--wa', href: 'https://wa.me/' + cfg.whatsapp + '?text=' + encodeURIComponent( msg + ' ' + this.data.url ), target: '_blank', rel: 'noopener', text: t( 'whatsapp' ) } ) );
			}
			card.appendChild( actions );
		} else {
			card.appendChild( el( 'p', { class: 'pzs-unit__note', text: 'hold' === u.status ? t( 'held' ) : t( 'taken' ) } ) );
		}
		this.detail.textContent = '';
		this.detail.appendChild( card );
	};

	// Fill the theme's sidebar enquiry form, or go to the property page.
	Viewer.prototype.enquire = function ( msg ) {
		var form = document.getElementById( 'pz-form' );
		var box = form && form.querySelector( '[name="pz_message"]' );
		var pid = form && form.querySelector( '[name="pz_property_id"]' );
		if ( box && pid && String( pid.value ) === String( this.data.id ) ) {
			box.value = msg;
			form.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			var name = form.querySelector( '[name="pz_name"]' );
			window.setTimeout( function () {
				( name || box ).focus( { preventScroll: true } );
			}, 400 );
			return;
		}
		window.location.href = this.data.url + '#pz-form';
	};

	function init() {
		document.querySelectorAll( '.pzs-inv' ).forEach( function ( section ) {
			var raw = section.querySelector( '.pzs-inv__data' );
			if ( ! raw || section.dataset.ready ) {
				return;
			}
			section.dataset.ready = '1';
			var data;
			try {
				data = JSON.parse( raw.textContent );
			} catch ( e ) {
				return;
			}
			// The section is printed inside the description; show it as its own box after the Overview.
			var block = section.parentElement && section.parentElement.closest( '.pz-block' );
			if ( block && block !== section ) {
				block.parentNode.insertBefore( section, block.nextSibling );
			}
			new Viewer( section, data ); // eslint-disable-line no-new
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
