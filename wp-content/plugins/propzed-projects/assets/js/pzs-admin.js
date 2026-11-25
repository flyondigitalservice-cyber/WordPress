/**
 * Propzed Projects: floor plan & inventory editor (property edit screen).
 *
 * Works on the JSON in #pzs-inventory-json, saved with the property ("Update").
 *   1. Plans: name, unit code prefix, floors ("1-20", "G,1-5"), plan image.
 *   2. Draw: drag on the plan image to add a unit box; drag a box to move it,
 *      drag its corner to resize. Select a box to set label, type, carpet, facing, price.
 *   3. Inventory: building chart of floors x units. Click a unit to edit it, or pick
 *      a quick action ("Mark Booked") and click units to punch them in one go.
 */
( function () {
	'use strict';

	var root = document.getElementById( 'pzs-inv-app' );
	var field = document.getElementById( 'pzs-inventory-json' );
	if ( ! root || ! field ) {
		return;
	}

	var STATUSES = ( window.pzsAdmin && window.pzsAdmin.statuses ) || { available: 'Available', hold: 'On hold', booked: 'Booked', sold: 'Sold' };
	var state;
	try {
		state = JSON.parse( field.value || '{}' );
	} catch ( e ) {
		state = {};
	}
	state.plans = Array.isArray( state.plans ) ? state.plans : [];
	state.units = state.units && 'object' === typeof state.units && ! Array.isArray( state.units ) ? state.units : {};
	state.show_prices = !! state.show_prices;

	var ui = { plan: 0, slot: null, unit: null, quick: '' };

	function uid( p ) {
		return p + Math.random().toString( 36 ).slice( 2, 9 );
	}

	function el( tag, attrs, children ) {
		var n = document.createElement( tag );
		Object.keys( attrs || {} ).forEach( function ( k ) {
			var v = attrs[ k ];
			if ( 'text' === k ) {
				n.textContent = v;
			} else if ( 'class' === k ) {
				n.className = v;
			} else if ( 0 === k.indexOf( 'on' ) && 'function' === typeof v ) {
				n.addEventListener( k.slice( 2 ), v );
			} else if ( null !== v && undefined !== v && false !== v ) {
				n.setAttribute( k, true === v ? '' : v );
			}
		} );
		( children || [] ).forEach( function ( c ) {
			if ( c ) {
				n.appendChild( 'string' === typeof c ? document.createTextNode( c ) : c );
			}
		} );
		return n;
	}

	function floors( spec ) {
		spec = String( spec || '' ).trim();
		if ( ! spec ) {
			return [ '' ];
		}
		var out = [];
		spec.split( ',' ).forEach( function ( tok ) {
			tok = tok.trim();
			var m = tok.match( /^(\d{1,3})\s*-\s*(\d{1,3})$/ );
			if ( m ) {
				for ( var f = +m[ 1 ]; f <= Math.min( +m[ 2 ], +m[ 1 ] + 199 ); f++ ) {
					if ( out.indexOf( String( f ) ) < 0 ) {
						out.push( String( f ) );
					}
				}
			} else if ( /^[A-Za-z0-9]{1,6}$/.test( tok ) && out.indexOf( tok ) < 0 ) {
				out.push( tok );
			}
		} );
		return out.length ? out.slice( 0, 200 ) : [ '' ];
	}

	function code( plan, slot, floor ) {
		var label = String( slot.label || '' );
		if ( '' === floor ) {
			return ( plan.prefix || '' ) + label;
		}
		return ( plan.prefix || '' ) + floor + ( /^\d+$/.test( label ) ? ( label.length < 2 ? '0' + label : label ) : '-' + label );
	}

	function key( plan, slot, floor ) {
		return plan.id + '|' + slot.id + '|' + floor;
	}

	function unit( plan, slot, floor ) {
		return state.units[ key( plan, slot, floor ) ] || { status: 'available' };
	}

	function setUnit( plan, slot, floor, changes ) {
		var k = key( plan, slot, floor );
		var u = Object.assign( { status: 'available', price: '', buyer: '', phone: '', date: '', note: '' }, state.units[ k ] || {}, changes );
		if ( ( 'booked' === u.status || 'sold' === u.status ) && ! u.date ) {
			u.date = new Date().toISOString().slice( 0, 10 );
		}
		state.units[ k ] = u;
	}

	function save() {
		field.value = JSON.stringify( state );
	}

	function plan() {
		return state.plans[ ui.plan ];
	}

	function counts() {
		var c = { total: 0, available: 0, hold: 0, booked: 0, sold: 0 };
		state.plans.forEach( function ( p ) {
			floors( p.floors ).forEach( function ( f ) {
				p.slots.forEach( function ( s ) {
					c.total++;
					c[ unit( p, s, f ).status ]++;
				} );
			} );
		} );
		return c;
	}

	/* ------------------------------------------------------------------ */

	function render() {
		save();
		root.textContent = '';

		// Top bar.
		var c = counts();
		var top = el( 'div', { class: 'pzs-ed__top' }, [
			el( 'div', { class: 'pzs-ed__tabs' }, state.plans.map( function ( p, i ) {
				return el( 'button', { type: 'button', class: 'button' + ( i === ui.plan ? ' button-primary' : '' ), text: p.name || 'Plan ' + ( i + 1 ), onclick: function () {
					ui.plan = i;
					ui.slot = null;
					ui.unit = null;
					render();
				} } );
			} ).concat( [ el( 'button', { type: 'button', class: 'button', text: '+ Add plan / tower / layout', onclick: addPlan } ) ] ) ),
			el( 'label', { class: 'pzs-ed__prices' }, [
				el( 'input', { type: 'checkbox', checked: state.show_prices, onchange: function ( e ) {
					state.show_prices = e.target.checked;
					save();
				} } ),
				' Show unit prices on the website',
			] ),
		] );
		root.appendChild( top );
		if ( c.total ) {
			root.appendChild( el( 'p', { class: 'pzs-ed__counts' }, [
				el( 'strong', { text: c.total + ' units: ' } ),
				el( 'span', { class: 'pzs-tag is-available', text: c.available + ' available' } ),
				el( 'span', { class: 'pzs-tag is-hold', text: c.hold + ' on hold' } ),
				el( 'span', { class: 'pzs-tag is-booked', text: c.booked + ' booked' } ),
				el( 'span', { class: 'pzs-tag is-sold', text: c.sold + ' sold' } ),
			] ) );
		}

		if ( ! state.plans.length ) {
			root.appendChild( el( 'div', { class: 'pzs-ed__empty' }, [
				el( 'p', { text: 'No floor plan yet. Add a plan for each tower (typical floor plan) or one master layout for villas / plots.' } ),
				el( 'button', { type: 'button', class: 'button button-primary', text: 'Add first plan', onclick: addPlan } ),
			] ) );
			return;
		}

		var p = plan();
		root.appendChild( planSettings( p ) );
		var cols = el( 'div', { class: 'pzs-ed__cols' } );
		cols.appendChild( canvas( p ) );
		cols.appendChild( slotPanel( p ) );
		root.appendChild( cols );
		root.appendChild( chart( p ) );
	}

	function addPlan() {
		state.plans.push( { id: uid( 'p' ), name: 'Tower ' + String.fromCharCode( 65 + state.plans.length ), prefix: String.fromCharCode( 65 + state.plans.length ) + '-', floors: '1-10', image: 0, image_url: '', slots: [] } );
		ui.plan = state.plans.length - 1;
		ui.slot = null;
		ui.unit = null;
		render();
	}

	function planSettings( p ) {
		var input = function ( label, prop, attrs, help ) {
			return el( 'label', { class: 'pzs-ed__field' }, [
				el( 'span', { text: label } ),
				el( 'input', Object.assign( { type: 'text', value: p[ prop ] || '', onchange: function ( e ) {
					p[ prop ] = e.target.value;
					render();
				} }, attrs || {} ) ),
				help ? el( 'small', { text: help } ) : null,
			] );
		};
		var box = el( 'div', { class: 'pzs-ed__plan' }, [
			input( 'Plan name', 'name', { placeholder: 'Tower A – typical floor' } ),
			input( 'Unit code prefix', 'prefix', { placeholder: 'A-', style: 'max-width:90px' }, 'A- + floor 12 + unit 03 = A-1203' ),
			input( 'Floors', 'floors', { placeholder: '1-20' }, 'e.g. 1-20 or G,1-5,7-22. Empty = single level (villa / plot layout).' ),
		] );
		var imgBtns = el( 'div', { class: 'pzs-ed__field' }, [
			el( 'span', { text: 'Plan image' } ),
			el( 'div', {}, [
				el( 'button', { type: 'button', class: 'button', text: p.image ? 'Change image' : 'Choose plan image', onclick: function () {
					pickImage( p );
				} } ),
				p.image ? el( 'button', { type: 'button', class: 'button-link pzs-danger', text: ' Remove', onclick: function () {
					p.image = 0;
					p.image_url = '';
					render();
				} } ) : null,
			] ),
		] );
		box.appendChild( imgBtns );

		var gen = el( 'div', { class: 'pzs-ed__field' }, [
			el( 'span', { text: 'No plan image?' } ),
			el( 'div', {}, [
				el( 'input', { type: 'number', min: 1, max: 60, value: 4, style: 'width:60px', id: 'pzs-gen-n' } ),
				el( 'button', { type: 'button', class: 'button', text: 'Add units per floor', onclick: function () {
					var n = Math.max( 1, Math.min( 60, parseInt( document.getElementById( 'pzs-gen-n' ).value, 10 ) || 1 ) );
					var start = p.slots.length;
					for ( var i = 1; i <= n; i++ ) {
						p.slots.push( { id: uid( 's' ), label: String( start + i ), x: '', y: '', w: '', h: '', config: '', carpet: '', facing: '', price: '' } );
					}
					render();
				} } ),
			] ),
			el( 'small', { text: 'Creates units for the building chart; you can draw their boxes on a plan later.' } ),
		] );
		box.appendChild( gen );

		box.appendChild( el( 'button', { type: 'button', class: 'button-link pzs-danger pzs-ed__del', text: 'Delete this plan', onclick: function () {
			if ( window.confirm( 'Delete "' + ( p.name || 'this plan' ) + '" and its units?' ) ) {
				state.plans.splice( ui.plan, 1 );
				ui.plan = 0;
				ui.slot = null;
				render();
			}
		} } ) );
		return box;
	}

	function pickImage( p ) {
		if ( ! window.wp || ! window.wp.media ) {
			return;
		}
		var frame = window.wp.media( { title: 'Floor plan / master layout', library: { type: 'image' }, multiple: false } );
		frame.on( 'select', function () {
			var a = frame.state().get( 'selection' ).first().toJSON();
			p.image = a.id;
			p.image_url = ( a.sizes && a.sizes.large ? a.sizes.large.url : a.url );
			render();
		} );
		frame.open();
	}

	/* Drawing canvas ----------------------------------------------------- */

	function canvas( p ) {
		var wrap = el( 'div', { class: 'pzs-ed__canvas' } );
		if ( ! p.image_url ) {
			wrap.appendChild( el( 'p', { class: 'pzs-ed__noimg', text: 'Choose a plan image to draw unit boxes on it. Tip: export one page of the brochure as JPG/PNG.' } ) );
			return wrap;
		}
		wrap.appendChild( el( 'p', { class: 'description', text: 'Drag on the image to add a unit. Drag a box to move it, its corner to resize.' } ) );
		var stage = el( 'div', { class: 'pzs-ed__stage' } );
		var img = el( 'img', { src: p.image_url, alt: '', draggable: 'false' } );
		stage.appendChild( img );

		p.slots.forEach( function ( s ) {
			if ( '' === s.x || null === s.x || undefined === s.x ) {
				return;
			}
			var b = el( 'div', { class: 'pzs-ed__box' + ( ui.slot === s.id ? ' is-selected' : '' ), 'data-slot': s.id }, [ el( 'span', { text: s.label } ), el( 'i', { class: 'pzs-ed__handle' } ) ] );
			place( b, s );
			stage.appendChild( b );
		} );

		var drag = null;
		var pct = function ( e ) {
			var r = stage.getBoundingClientRect();
			return { x: Math.max( 0, Math.min( 100, ( e.clientX - r.left ) / r.width * 100 ) ), y: Math.max( 0, Math.min( 100, ( e.clientY - r.top ) / r.height * 100 ) ) };
		};
		stage.addEventListener( 'pointerdown', function ( e ) {
			if ( 0 !== e.button ) {
				return;
			}
			e.preventDefault();
			var pos = pct( e );
			var boxEl = e.target.closest( '.pzs-ed__box' );
			if ( boxEl ) {
				var s = findSlot( p, boxEl.getAttribute( 'data-slot' ) );
				ui.slot = s.id;
				drag = { mode: e.target.classList.contains( 'pzs-ed__handle' ) ? 'resize' : 'move', slot: s, el: boxEl, start: pos, orig: { x: s.x, y: s.y, w: s.w, h: s.h } };
				stage.querySelectorAll( '.is-selected' ).forEach( function ( n ) {
					n.classList.remove( 'is-selected' );
				} );
				boxEl.classList.add( 'is-selected' );
			} else {
				var ghost = el( 'div', { class: 'pzs-ed__box is-ghost' } );
				stage.appendChild( ghost );
				drag = { mode: 'draw', start: pos, el: ghost };
			}
			stage.setPointerCapture( e.pointerId );
		} );
		stage.addEventListener( 'pointermove', function ( e ) {
			if ( ! drag ) {
				return;
			}
			var pos = pct( e );
			var dx = pos.x - drag.start.x;
			var dy = pos.y - drag.start.y;
			if ( 'draw' === drag.mode ) {
				place( drag.el, { x: Math.min( pos.x, drag.start.x ), y: Math.min( pos.y, drag.start.y ), w: Math.abs( dx ), h: Math.abs( dy ) } );
			} else if ( 'move' === drag.mode ) {
				drag.slot.x = round( Math.max( 0, Math.min( 100 - drag.orig.w, drag.orig.x + dx ) ) );
				drag.slot.y = round( Math.max( 0, Math.min( 100 - drag.orig.h, drag.orig.y + dy ) ) );
				place( drag.el, drag.slot );
			} else {
				drag.slot.w = round( Math.max( 1, Math.min( 100 - drag.orig.x, drag.orig.w + dx ) ) );
				drag.slot.h = round( Math.max( 1, Math.min( 100 - drag.orig.y, drag.orig.h + dy ) ) );
				place( drag.el, drag.slot );
			}
		} );
		stage.addEventListener( 'pointerup', function ( e ) {
			if ( ! drag ) {
				return;
			}
			var d = drag;
			drag = null;
			if ( 'draw' === d.mode ) {
				var pos = pct( e );
				var w = Math.abs( pos.x - d.start.x );
				var h = Math.abs( pos.y - d.start.y );
				if ( w < 1.5 || h < 1.5 ) {
					d.el.remove();
					return;
				}
				var s = { id: uid( 's' ), label: nextLabel( p ), x: round( Math.min( pos.x, d.start.x ) ), y: round( Math.min( pos.y, d.start.y ) ), w: round( w ), h: round( h ), config: lastConfig( p ), carpet: '', facing: '', price: '' };
				p.slots.push( s );
				ui.slot = s.id;
			}
			render();
		} );
		wrap.appendChild( stage );
		return wrap;
	}

	function round( n ) {
		return Math.round( n * 100 ) / 100;
	}

	function place( node, s ) {
		node.style.left = s.x + '%';
		node.style.top = s.y + '%';
		node.style.width = s.w + '%';
		node.style.height = s.h + '%';
	}

	function findSlot( p, id ) {
		return p.slots.filter( function ( s ) {
			return s.id === id;
		} )[ 0 ];
	}

	function nextLabel( p ) {
		var max = 0;
		p.slots.forEach( function ( s ) {
			var n = parseInt( s.label, 10 );
			if ( ! isNaN( n ) && n > max ) {
				max = n;
			}
		} );
		var next = String( max + 1 );
		return floors( p.floors ).length > 1 && next.length < 2 ? '0' + next : next;
	}

	function lastConfig( p ) {
		return p.slots.length ? p.slots[ p.slots.length - 1 ].config : '';
	}

	/* Selected unit-box panel -------------------------------------------- */

	function slotPanel( p ) {
		var panel = el( 'div', { class: 'pzs-ed__panel' } );
		var s = ui.slot ? findSlot( p, ui.slot ) : null;
		if ( ! s ) {
			panel.appendChild( el( 'p', { class: 'description', text: p.slots.length ? 'Select a unit box (on the plan or the label in the chart header) to edit its details.' : 'No units in this plan yet.' } ) );
			return panel;
		}
		var row = function ( label, prop, attrs ) {
			return el( 'label', { class: 'pzs-ed__field' }, [
				el( 'span', { text: label } ),
				el( 'input', Object.assign( { type: 'text', value: null === s[ prop ] || undefined === s[ prop ] ? '' : s[ prop ], onchange: function ( e ) {
					s[ prop ] = e.target.value;
					render();
				} }, attrs || {} ) ),
			] );
		};
		panel.appendChild( el( 'h4', { text: 'Unit ' + code( p, s, floors( p.floors )[ 0 ] ) + ( floors( p.floors ).length > 1 ? ' (same position on every floor)' : '' ) } ) );
		panel.appendChild( row( 'Label / unit no.', 'label', { placeholder: '03 or Villa 7' } ) );
		panel.appendChild( row( 'Type', 'config', { placeholder: '2 BHK', list: 'pzs-configs' } ) );
		panel.appendChild( row( 'Carpet area', 'carpet', { placeholder: '650 sq.ft' } ) );
		panel.appendChild( row( 'Facing / view', 'facing', { placeholder: 'East, garden view' } ) );
		panel.appendChild( row( 'Base price ₹', 'price', { type: 'number', min: 0, step: 1000, placeholder: '12500000' } ) );
		panel.appendChild( el( 'datalist', { id: 'pzs-configs' }, [ '1 RK', '1 BHK', '1.5 BHK', '2 BHK', '2.5 BHK', '3 BHK', '4 BHK', '3 BHK Villa', '4 BHK Villa', '5 BHK Villa', 'Plot', 'Office', 'Shop' ].map( function ( v ) {
			return el( 'option', { value: v } );
		} ) ) );
		panel.appendChild( el( 'p', {}, [
			el( 'button', { type: 'button', class: 'button-link pzs-danger', text: 'Delete unit box', onclick: function () {
				p.slots = p.slots.filter( function ( x ) {
					return x.id !== s.id;
				} );
				ui.slot = null;
				render();
			} } ),
			' ',
			'' !== s.x && null !== s.x ? el( 'button', { type: 'button', class: 'button-link', text: 'Remove box from plan', onclick: function () {
				s.x = s.y = s.w = s.h = '';
				render();
			} } ) : null,
		] ) );
		return panel;
	}

	/* Building chart + unit editor --------------------------------------- */

	function chart( p ) {
		var wrap = el( 'div', { class: 'pzs-ed__inventory' } );
		if ( ! p.slots.length ) {
			return wrap;
		}
		var fl = floors( p.floors );
		var quick = el( 'select', { onchange: function ( e ) {
			ui.quick = e.target.value;
		} }, [ el( 'option', { value: '', text: 'Click a unit to: edit details' } ) ].concat( Object.keys( STATUSES ).map( function ( k ) {
			return el( 'option', { value: k, text: 'Click a unit to: mark ' + STATUSES[ k ], selected: ui.quick === k } );
		} ) ) );
		wrap.appendChild( el( 'div', { class: 'pzs-ed__invbar' }, [ el( 'h4', { text: 'Inventory: ' + ( p.name || '' ) } ), quick ] ) );

		var table = el( 'div', { class: 'pzs-ed__chart' } );
		var header = el( 'div', { class: 'pzs-ed__row is-head' }, [ el( 'span', { class: 'pzs-ed__floor', text: fl.length > 1 ? 'Floor' : '' } ) ] );
		p.slots.forEach( function ( s ) {
			header.appendChild( el( 'button', { type: 'button', class: 'pzs-ed__colhead' + ( ui.slot === s.id ? ' is-selected' : '' ), text: s.label + ( s.config ? ' · ' + s.config : '' ), onclick: function () {
				ui.slot = s.id;
				render();
			} } ) );
		} );
		table.appendChild( header );

		fl.slice().reverse().forEach( function ( f ) {
			var row = el( 'div', { class: 'pzs-ed__row' }, [ el( 'span', { class: 'pzs-ed__floor', text: f } ) ] );
			p.slots.forEach( function ( s ) {
				var u = unit( p, s, f );
				var k = key( p, s, f );
				row.appendChild( el( 'button', { type: 'button', class: 'pzs-ed__cell is-' + u.status + ( ui.unit === k ? ' is-selected' : '' ), title: code( p, s, f ) + ' · ' + STATUSES[ u.status ] + ( u.buyer ? ' · ' + u.buyer : '' ), text: code( p, s, f ), onclick: function () {
					if ( ui.quick ) {
						setUnit( p, s, f, { status: ui.quick } );
					} else {
						ui.unit = k;
					}
					render();
				} } ) );
			} );
			table.appendChild( row );
		} );
		wrap.appendChild( table );

		if ( ui.unit ) {
			var parts = ui.unit.split( '|' );
			var s = findSlot( p, parts[ 1 ] );
			if ( s && parts[ 0 ] === p.id ) {
				wrap.appendChild( unitPanel( p, s, parts[ 2 ] ) );
			}
		}
		return wrap;
	}

	function unitPanel( p, s, f ) {
		var u = Object.assign( { status: 'available', price: '', buyer: '', phone: '', date: '', note: '' }, unit( p, s, f ) );
		var change = function ( prop ) {
			return function ( e ) {
				var c = {};
				c[ prop ] = e.target.value;
				setUnit( p, s, f, c );
				render();
			};
		};
		var field2 = function ( label, prop, attrs ) {
			return el( 'label', { class: 'pzs-ed__field' }, [ el( 'span', { text: label } ), el( 'input', Object.assign( { type: 'text', value: u[ prop ] || '', onchange: change( prop ) }, attrs || {} ) ) ] );
		};
		return el( 'div', { class: 'pzs-ed__unit' }, [
			el( 'h4', { text: 'Unit ' + code( p, s, f ) + ( s.config ? ' · ' + s.config : '' ) } ),
			el( 'div', { class: 'pzs-ed__unitgrid' }, [
				el( 'label', { class: 'pzs-ed__field' }, [ el( 'span', { text: 'Status' } ), el( 'select', { onchange: change( 'status' ) }, Object.keys( STATUSES ).map( function ( k ) {
					return el( 'option', { value: k, text: STATUSES[ k ], selected: u.status === k } );
				} ) ) ] ),
				field2( 'Price ₹ (this unit)', 'price', { type: 'number', min: 0, step: 1000, placeholder: s.price ? 'Base: ' + s.price : '' } ),
				field2( 'Booking date', 'date', { type: 'date' } ),
				field2( 'Buyer name (private)', 'buyer' ),
				field2( 'Buyer phone (private)', 'phone', { type: 'tel' } ),
				field2( 'Note (private)', 'note' ),
			] ),
			el( 'p', { class: 'description', text: 'Buyer name, phone and note are only visible here in wp-admin. Click "Update" to publish changes.' } ),
		] );
	}

	render();
}() );
