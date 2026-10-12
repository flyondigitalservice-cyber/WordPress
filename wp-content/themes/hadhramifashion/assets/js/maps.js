/**
 * Hadhrami Fashion — Google Maps delivery engine.
 *
 *  [data-zone]      Address checker: draws the 2-hour and 24-hour circles around
 *                   the atelier and tells the visitor what reaches them, and when.
 *  [data-checkout]  Pin-the-door checkout with live delivery promises and fees.
 *  [data-track]     Customer tracking: rider moving on the map, ETA, timeline.
 *  [data-rider]     Rider page: shares the phone's GPS with the customer.
 *
 * Google APIs used (enable them for the key): Maps JavaScript, Places (New),
 * Geocoding, Routes. Everything degrades gracefully without a key.
 */
( function () {
	'use strict';

	var cfg = window.HF || {};
	var U = window.HFUtil;
	var Bag = window.HFBag;
	var $ = U.$, $$ = U.$$, esc = U.esc, money = U.money;
	var STORE = { lat: cfg.store.lat, lng: cfg.store.lng };

	/* ------------------------------------------------------------ REST */
	function api( path, body ) {
		return fetch( cfg.rest + path, {
			method: body ? 'POST' : 'GET',
			// Public endpoints: no cookie/nonce, so cached pages never send a stale nonce.
			headers: { 'Content-Type': 'application/json' },
			credentials: 'omit',
			body: body ? JSON.stringify( body ) : undefined
		} ).then( function ( r ) {
			return r.json().then( function ( j ) {
				if ( ! r.ok ) { throw new Error( j && j.message ? j.message : 'Request failed' ); }
				return j;
			} );
		} );
	}

	function bagItems() {
		return Bag.read().map( function ( l ) {
			return { id: l.id, qty: l.qty, fit: l.fit, size: l.size, measurements: l.measurements };
		} );
	}

	/* ------------------------------------------------------ Maps loader */
	var mapsPromise;
	function loadMaps() {
		if ( mapsPromise ) { return mapsPromise; }
		mapsPromise = new Promise( function ( resolve, reject ) {
			if ( ! cfg.mapsKey ) { reject( new Error( 'no-key' ) ); return; }
			if ( window.google && window.google.maps ) { resolve( window.google.maps ); return; }
			window.__hfMapsReady = function () { resolve( window.google.maps ); };
			var s = document.createElement( 'script' );
			s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent( cfg.mapsKey ) +
				'&v=weekly&libraries=places,geometry&loading=async&callback=__hfMapsReady';
			s.async = true;
			s.onerror = function () { reject( new Error( 'load-failed' ) ); };
			document.head.appendChild( s );
		} );
		return mapsPromise;
	}

	/* Muted ivory & ink style, matching the site. */
	var MAP_STYLE = [
		{ elementType: 'geometry', stylers: [ { color: '#f3ede1' } ] },
		{ elementType: 'labels.text.fill', stylers: [ { color: '#6f6a60' } ] },
		{ elementType: 'labels.text.stroke', stylers: [ { color: '#fbf8f2' } ] },
		{ featureType: 'poi', stylers: [ { visibility: 'off' } ] },
		{ featureType: 'transit', stylers: [ { visibility: 'off' } ] },
		{ featureType: 'road', elementType: 'geometry', stylers: [ { color: '#ffffff' } ] },
		{ featureType: 'road.arterial', elementType: 'geometry', stylers: [ { color: '#fbf8f2' } ] },
		{ featureType: 'road.highway', elementType: 'geometry', stylers: [ { color: '#e5dccb' } ] },
		{ featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [ { color: '#d9cfbd' } ] },
		{ featureType: 'water', elementType: 'geometry', stylers: [ { color: '#cfd6d3' } ] },
		{ featureType: 'administrative', elementType: 'geometry.stroke', stylers: [ { color: '#d9cfbd' } ] }
	];

	function svgIcon( kind ) {
		var svg;
		if ( kind === 'store' ) {
			svg = '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44"><circle cx="22" cy="22" r="20" fill="#14161a" stroke="#c8a86f" stroke-width="2"/><text x="22" y="28" font-family="Georgia,serif" font-size="17" fill="#c8a86f" text-anchor="middle">H</text></svg>';
			return { url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent( svg ), scaledSize: new google.maps.Size( 44, 44 ), anchor: new google.maps.Point( 22, 22 ) };
		}
		if ( kind === 'rider' ) {
			svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="#a6834b" stroke="#fff" stroke-width="3"/><g fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="13" cy="24" r="3"/><circle cx="27" cy="24" r="3"/><path d="M13 24h6l3-7h3M19 17h4"/></g></svg>';
			return { url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent( svg ), scaledSize: new google.maps.Size( 40, 40 ), anchor: new google.maps.Point( 20, 20 ) };
		}
		svg = '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="46" viewBox="0 0 34 46"><path d="M17 45S2 28.5 2 17a15 15 0 0 1 30 0c0 11.5-15 28-15 28Z" fill="#5d1e24" stroke="#fff" stroke-width="2"/><circle cx="17" cy="17" r="5.5" fill="#fbf8f2"/></svg>';
		return { url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent( svg ), scaledSize: new google.maps.Size( 34, 46 ), anchor: new google.maps.Point( 17, 45 ) };
	}

	function makeMap( el, opts ) {
		var ph = $( '[data-map-placeholder]', el );
		if ( ph ) { ph.remove(); }
		var map = new google.maps.Map( el, Object.assign( {
			center: STORE,
			zoom: 12,
			styles: MAP_STYLE,
			disableDefaultUI: true,
			zoomControl: true,
			gestureHandling: 'cooperative',
			clickableIcons: false
		}, opts || {} ) );
		new google.maps.Marker( { map: map, position: STORE, icon: svgIcon( 'store' ), title: cfg.store.name, zIndex: 5 } );
		return map;
	}

	function drawZones( map ) {
		if ( cfg.tailoredKm ) {
			new google.maps.Circle( { map: map, center: STORE, radius: cfg.tailoredKm * 1000, strokeColor: '#a6834b', strokeOpacity: .35, strokeWeight: 1, fillColor: '#a6834b', fillOpacity: .04, clickable: false } );
		}
		if ( cfg.expressKm ) {
			new google.maps.Circle( { map: map, center: STORE, radius: cfg.expressKm * 1000, strokeColor: '#5d1e24', strokeOpacity: .6, strokeWeight: 1.4, fillColor: '#5d1e24', fillOpacity: .06, clickable: false } );
		}
	}

	function mapUnavailable( el, msg ) {
		var ph = el && $( '[data-map-placeholder]', el );
		if ( ph ) { ph.innerHTML = '<span>' + esc( msg ) + '</span>'; }
	}

	/* --------------------------------------------------- Road distance */
	function roadRoute( origin, dest ) {
		// Routes API (new) first, then the classic Directions service, then null.
		return google.maps.importLibrary( 'routes' ).then( function ( lib ) {
			if ( lib.Route && lib.Route.computeRoutes ) {
				return lib.Route.computeRoutes( {
					origin: origin,
					destination: dest,
					travelMode: 'DRIVING',
					fields: [ 'distanceMeters', 'durationMillis', 'path' ]
				} ).then( function ( res ) {
					var r = res && res.routes && res.routes[ 0 ];
					if ( ! r ) { throw new Error( 'no-route' ); }
					return { km: r.distanceMeters / 1000, mins: Math.round( ( r.durationMillis || 0 ) / 60000 ), path: r.path };
				} );
			}
			throw new Error( 'no-routes-lib' );
		} ).catch( function () {
			return new Promise( function ( resolve ) {
				try {
					new google.maps.DirectionsService().route( { origin: origin, destination: dest, travelMode: 'DRIVING' }, function ( res, status ) {
						if ( status !== 'OK' ) { resolve( null ); return; }
						var leg = res.routes[ 0 ].legs[ 0 ];
						resolve( { km: leg.distance.value / 1000, mins: Math.round( leg.duration.value / 60 ), path: res.routes[ 0 ].overview_path } );
					} );
				} catch ( e ) { resolve( null ); }
			} );
		} );
	}

	function drawRoute( map, holder, path ) {
		if ( holder.line ) { holder.line.setMap( null ); }
		if ( ! path ) { return; }
		holder.line = new google.maps.Polyline( {
			map: map,
			path: path,
			strokeColor: '#14161a',
			strokeOpacity: 0,
			icons: [ { icon: { path: 'M 0,-1 0,1', strokeOpacity: .85, strokeWeight: 2.5, scale: 2 }, offset: '0', repeat: '10px' } ]
		} );
	}

	/* ------------------------------------------------- Address picking */
	function reverseGeocode( latLng ) {
		return new Promise( function ( resolve ) {
			try {
				new google.maps.Geocoder().geocode( { location: latLng }, function ( res, status ) {
					resolve( status === 'OK' && res[ 0 ] ? res[ 0 ].formatted_address : '' );
				} );
			} catch ( e ) { resolve( '' ); }
		} );
	}

	/**
	 * Mount an address autocomplete in `slot`, calling onPick({lat,lng,address}).
	 * Uses the Places (New) web component, falling back to the classic widget.
	 */
	function mountAutocomplete( slot, onPick ) {
		var input = $( 'input', slot );
		var bias = { center: STORE, radius: Math.max( 5000, ( cfg.tailoredKm || 30 ) * 1000 * 1.5 ) };

		return google.maps.importLibrary( 'places' ).then( function ( places ) {
			if ( places.PlaceAutocompleteElement ) {
				var pac = new places.PlaceAutocompleteElement( { locationBias: bias } );
				pac.id = input.id;
				if ( input.placeholder ) { pac.setAttribute( 'placeholder', input.placeholder ); }
				input.replaceWith( pac );

				var handle = function ( place ) {
					return place.fetchFields( { fields: [ 'formattedAddress', 'location', 'displayName' ] } ).then( function () {
						if ( ! place.location ) { return; }
						onPick( { lat: place.location.lat(), lng: place.location.lng(), address: place.formattedAddress || '' } );
					} );
				};
				pac.addEventListener( 'gmp-select', function ( ev ) {
					if ( ev.placePrediction ) { handle( ev.placePrediction.toPlace() ); }
				} );
				pac.addEventListener( 'gmp-placeselect', function ( ev ) {
					if ( ev.place ) { handle( ev.place ); }
				} );
				return;
			}
			throw new Error( 'legacy' );
		} ).catch( function () {
			if ( ! google.maps.places || ! google.maps.places.Autocomplete ) { return; }
			var ac = new google.maps.places.Autocomplete( input, { fields: [ 'geometry', 'formatted_address' ] } );
			ac.setBounds( new google.maps.Circle( bias ).getBounds() );
			ac.addListener( 'place_changed', function () {
				var p = ac.getPlace();
				if ( ! p.geometry ) { return; }
				onPick( { lat: p.geometry.location.lat(), lng: p.geometry.location.lng(), address: p.formatted_address || input.value } );
			} );
		} );
	}

	function locateMe( btn, onPick ) {
		if ( ! btn ) { return; }
		btn.addEventListener( 'click', function () {
			if ( ! navigator.geolocation ) { U.toast( 'Location is not available on this device.' ); return; }
			btn.disabled = true;
			navigator.geolocation.getCurrentPosition( function ( pos ) {
				btn.disabled = false;
				onPick( { lat: pos.coords.latitude, lng: pos.coords.longitude, address: '' } );
			}, function () {
				btn.disabled = false;
				U.toast( 'Please allow location access, or type your address.' );
			}, { enableHighAccuracy: true, timeout: 12000 } );
		} );
	}

	/* ============================================================ ZONE */
	function initZone( root ) {
		var mapEl = $( '[data-map="zone"]', root );
		var result = $( '[data-zone-result]', root );
		var ctx = {};

		function show( pick, quote, route ) {
			var ex = quote.options.express;
			var ta = quote.options.tailored;
			var html = '<h4>' + ( quote.inExpress ? 'Yes — you are inside the two-hour circle.' : quote.inTailored ? 'You are inside our 24-hour tailoring area.' : 'You are beyond our rider routes.' ) + '</h4>';
			html += '<p class="muted" style="margin:0">' + esc( pick.address || 'Your pinned location' ) + ' · ' + quote.distanceKm.toFixed( 1 ) + ' km' + ( route && route.mins ? ' · about ' + route.mins + ' min by road' : '' ) + '</p><ul>';
			html += '<li class="' + ( ex ? '' : 'is-off' ) + '">' + checkIcon() + '<span><strong>Ready-to-wear shirts</strong> — ' + ( ex ? 'order now, at your door ' + esc( ex.etaText ) + ( ex.fee ? ' (' + money( ex.fee ) + ')' : '' ) : 'beyond the 2-hour radius' ) + '</span></li>';
			html += '<li class="' + ( ta ? '' : 'is-off' ) + '">' + checkIcon() + '<span><strong>Suits & shirts to your measure</strong> — ' + ( ta ? 'delivered ' + esc( ta.etaText ) : 'standard delivery in 3–5 days' ) + '</span></li>';
			html += '<li>' + checkIcon() + '<span><strong>Home measurement</strong> — ' + ( quote.inTailored ? 'a tailor can visit you today' : 'call us to arrange a fitting' ) + '</span></li>';
			html += '</ul>';
			result.innerHTML = html;
			result.classList.add( 'is-on' );
		}

		function pick( p ) {
			var routeP = ctx.map ? roadRoute( STORE, { lat: p.lat, lng: p.lng } ) : Promise.resolve( null );
			if ( ctx.map ) {
				if ( ! ctx.dest ) { ctx.dest = new google.maps.Marker( { map: ctx.map, icon: svgIcon( 'dest' ), zIndex: 6 } ); }
				ctx.dest.setPosition( p );
				var b = new google.maps.LatLngBounds();
				b.extend( STORE ); b.extend( p );
				ctx.map.fitBounds( b, 70 );
			}
			var addrP = p.address || ! ctx.map ? Promise.resolve( p.address ) : reverseGeocode( p );
			Promise.all( [ routeP, addrP ] ).then( function ( r ) {
				var route = r[ 0 ];
				p.address = r[ 1 ];
				if ( ctx.map ) { drawRoute( ctx.map, ctx, route && route.path ); }
				return api( 'quote', { lat: p.lat, lng: p.lng, roadKm: route ? route.km : null, items: [] } ).then( function ( q ) { show( p, q, route ); } );
			} ).catch( function ( e ) { U.toast( e.message ); } );
		}

		locateMe( $( '[data-locate]', root ), pick );

		loadMaps().then( function () {
			ctx.map = makeMap( mapEl, { zoom: 11 } );
			drawZones( ctx.map );
			mountAutocomplete( $( '[data-autocomplete-slot]', root ), pick );
			ctx.map.addListener( 'click', function ( e ) { pick( { lat: e.latLng.lat(), lng: e.latLng.lng(), address: '' } ); } );
		} ).catch( function () {
			mapUnavailable( mapEl, cfg.store.name + ' — ' + cfg.store.address );
		} );
	}

	function checkIcon() {
		return '<svg class="hf-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m5 12 5 5 9-10"/></svg>';
	}

	/* ======================================================== CHECKOUT */
	function initCheckout( form ) {
		var mapEl = $( '[data-map="checkout"]', form );
		var tiersEl = $( '[data-tiers]', form );
		var notice = $( '[data-notice]', form );
		var placeBtn = $( '[data-place]', form );
		var measureBox = $( '[data-measure-mode]', form );
		var state = { pick: null, quote: null, tier: null, roadKm: null };
		var ctx = {};

		function setNotice( msg, ok ) {
			notice.textContent = msg || '';
			notice.classList.toggle( 'is-on', !! msg );
			notice.classList.toggle( 'notice--ok', !! ok );
		}

		function renderSummary() {
			var lines = Bag.read();
			$( '[data-summary-lines]', form ).innerHTML = lines.length ? lines.map( function ( l ) {
				return '<div class="line" style="grid-template-columns:56px 1fr auto"><img src="' + esc( l.image ) + '" alt="" style="width:56px" referrerpolicy="no-referrer"><div><div class="line__title">' + esc( l.title ) + '</div><div class="line__meta">' + l.qty + ' × ' + esc( Bag.lineMeta( l ) ) + '</div></div><div class="line__price">' + money( l.price * l.qty ) + '</div></div>';
			} ).join( '' ) : '<p class="empty-bag">' + esc( cfg.i18n.empty ) + ' <a href="' + esc( cfg.shopUrl ) + '">Browse the collection</a></p>';

			var sub = Bag.subtotal();
			var fee = state.quote && state.tier ? state.quote.options[ state.tier ].fee : null;
			$( '[data-summary-subtotal]', form ).textContent = money( sub );
			$( '[data-summary-fee]', form ).textContent = fee === null ? '—' : ( fee ? money( fee ) : 'Complimentary' );
			$( '[data-summary-total]', form ).textContent = money( sub + ( fee || 0 ) );

			var needsMeasure = lines.some( function ( l ) { return l.fit === 'measure'; } );
			measureBox.hidden = ! needsMeasure;
			var anyProvided = lines.some( function ( l ) { return l.fit === 'measure' && l.measurements && Object.keys( l.measurements ).length; } );
			if ( needsMeasure && anyProvided ) { $( '#mm-provided', form ).checked = true; }

			placeBtn.disabled = ! ( lines.length && state.pick && state.tier );
		}

		function renderTiers() {
			var q = state.quote;
			if ( ! q ) { return; }
			var order = [ 'express', 'tailored', 'standard' ];
			if ( ! state.tier || ! q.options[ state.tier ] || ! q.options[ state.tier ].available ) {
				state.tier = order.filter( function ( k ) { return q.options[ k ] && q.options[ k ].available; } )[ 0 ] || null;
			}
			tiersEl.innerHTML = order.filter( function ( k ) { return q.options[ k ]; } ).map( function ( k ) {
				var o = q.options[ k ];
				return '<div class="choice"><input type="radio" name="tier" id="tier-' + k + '" value="' + k + '"' + ( state.tier === k ? ' checked' : '' ) + ( o.available ? '' : ' disabled' ) + '>' +
					'<label for="tier-' + k + '"><strong>' + esc( o.label ) + '</strong><em>' + ( o.fee ? money( o.fee ) : 'Complimentary' ) + '</em>' +
					'<span>' + ( o.available ? 'Arrives ' + esc( o.etaText ) + '. ' + esc( o.note ) : esc( o.reason ) ) + '</span></label></div>';
			} ).join( '' );
			if ( ! q.inTailored ) {
				tiersEl.insertAdjacentHTML( 'beforeend', '<p class="muted" style="font-size:.85rem">' + esc( cfg.i18n.outOfRange ) + '</p>' );
			}
			renderSummary();
		}

		function requote() {
			if ( ! state.pick ) { return; }
			api( 'quote', { lat: state.pick.lat, lng: state.pick.lng, roadKm: state.roadKm, items: bagItems() } ).then( function ( q ) {
				state.quote = q;
				$( '[data-distance] span', form ).textContent = q.distanceKm.toFixed( 1 ) + ' km from the atelier' + ( q.inExpress ? ' · inside the two-hour circle' : '' );
				renderTiers();
			} ).catch( function ( e ) { setNotice( e.message ); } );
		}

		function pick( p, fromDrag ) {
			state.pick = p;
			setNotice( '' );
			var chain = Promise.resolve();
			if ( ctx.map ) {
				if ( ! ctx.dest ) {
					ctx.dest = new google.maps.Marker( { map: ctx.map, icon: svgIcon( 'dest' ), draggable: true, zIndex: 6 } );
					ctx.dest.addListener( 'dragend', function ( e ) { pick( { lat: e.latLng.lat(), lng: e.latLng.lng(), address: '' }, true ); } );
				}
				ctx.dest.setPosition( p );
				if ( ! fromDrag ) {
					var b = new google.maps.LatLngBounds();
					b.extend( STORE ); b.extend( p );
					ctx.map.fitBounds( b, 60 );
				}
				chain = roadRoute( STORE, { lat: p.lat, lng: p.lng } ).then( function ( route ) {
					state.roadKm = route ? route.km : null;
					drawRoute( ctx.map, ctx, route && route.path );
				} );
				if ( ! p.address ) {
					chain = Promise.all( [ chain, reverseGeocode( p ) ] ).then( function ( r ) {
						p.address = r[ 1 ];
						var field = $( '#co-address', form );
						if ( field && 'value' in field && p.address ) { field.value = p.address; }
					} );
				}
			}
			chain.then( requote );
		}

		form.addEventListener( 'change', function ( e ) {
			if ( e.target.name === 'tier' ) { state.tier = e.target.value; renderSummary(); }
		} );
		document.addEventListener( 'hf:bag', function () { renderSummary(); requote(); } );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			setNotice( '' );
			var lines = Bag.read();
			if ( ! lines.length ) { setNotice( cfg.i18n.empty ); return; }
			if ( ! state.pick ) { setNotice( 'Please choose your address on the map.' ); return; }
			var fd = new FormData( form );
			if ( ! String( fd.get( 'name' ) || '' ).trim() || String( fd.get( 'phone' ) || '' ).replace( /\D/g, '' ).length < 7 ) {
				setNotice( 'Please share your name and a phone number our rider can call.' );
				return;
			}
			placeBtn.disabled = true;
			placeBtn.textContent = 'Placing your order…';
			api( 'orders', {
				lat: state.pick.lat,
				lng: state.pick.lng,
				roadKm: state.roadKm,
				address: state.pick.address || ( $( '#co-address', form ) && $( '#co-address', form ).value ) || '',
				addressDetails: fd.get( 'addressDetails' ),
				tier: state.tier,
				measureMode: fd.get( 'measureMode' ),
				notes: fd.get( 'notes' ),
				customer: { name: fd.get( 'name' ), phone: fd.get( 'phone' ), email: fd.get( 'email' ) },
				items: bagItems()
			} ).then( function ( res ) {
				Bag.write( [] );
				setNotice( 'Thank you — order ' + res.number + ' is with the atelier. Arriving ' + res.etaText + '.', true );
				window.location.href = res.trackUrl;
			} ).catch( function ( err ) {
				setNotice( err.message );
				placeBtn.disabled = false;
				placeBtn.textContent = 'Place order';
			} );
		} );

		locateMe( $( '[data-locate]', form ), pick );
		renderSummary();

		loadMaps().then( function () {
			ctx.map = makeMap( mapEl, { zoom: 12 } );
			drawZones( ctx.map );
			mountAutocomplete( $( '[data-autocomplete-slot]', form ), pick );
			ctx.map.addListener( 'click', function ( e ) { pick( { lat: e.latLng.lat(), lng: e.latLng.lng(), address: '' } ); } );
		} ).catch( function () {
			mapUnavailable( mapEl, 'Map unavailable — use the location button to share where you are.' );
		} );
	}

	/* =========================================================== TRACK */
	function smoothMove( marker, to ) {
		var from = marker.getPosition();
		if ( ! from ) { marker.setPosition( to ); return; }
		var start = performance.now(), dur = 1200;
		var fLat = from.lat(), fLng = from.lng();
		( function step( now ) {
			var t = Math.min( 1, ( now - start ) / dur );
			marker.setPosition( { lat: fLat + ( to.lat - fLat ) * t, lng: fLng + ( to.lng - fLng ) * t } );
			if ( t < 1 ) { requestAnimationFrame( step ); }
		}( start ) );
	}

	function initTrack( root ) {
		var key = root.getAttribute( 'data-track' );
		var mapEl = $( '[data-map="track"]', root );
		var ctx = { lastRoute: null };
		var mapReady = loadMaps().then( function () {
			ctx.map = makeMap( mapEl, { zoom: 13 } );
			return ctx.map;
		} ).catch( function () {
			mapUnavailable( mapEl, 'Live map unavailable.' );
			return null;
		} );

		function render( o ) {
			$( '[data-track-number]', root ).textContent = 'Order ' + o.number;
			$( '[data-track-eta]', root ).textContent = o.status === 'delivered' ? 'Delivered. Wear it well.' : o.status === 'cancelled' ? 'This order was cancelled.' : 'Arriving ' + o.etaText;
			$( '[data-track-tier]', root ).textContent = o.tierLabel + ' · ' + o.total;

			var current = o.timeline.filter( function ( s ) { return s.done; } ).length - 1;
			$( '[data-track-timeline]', root ).innerHTML = o.timeline.map( function ( s, i ) {
				return '<li class="' + ( s.done ? 'is-done ' : '' ) + ( i === current && o.status !== 'delivered' ? 'is-current' : '' ) + '">' + esc( s.label ) + ( s.time ? '<time>' + esc( s.time ) + '</time>' : '' ) + '</li>';
			} ).join( '' );

			$( '[data-track-rider]', root ).innerHTML = o.riderName && o.status === 'dispatched'
				? '<div class="rider-card"><svg class="hf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="6" cy="17" r="3"/><circle cx="18" cy="17" r="3"/><path d="M6 17h6l3-7h3M12 10h3"/></svg><div><strong>' + esc( o.riderName ) + '</strong> is on the way' + ( o.rider ? ' <span class="muted">· updated ' + esc( o.rider.ago ) + ' ago</span>' : '' ) + ( o.riderPhone ? '<br><a href="tel:' + esc( o.riderPhone ) + '">Call ' + esc( o.riderPhone ) + '</a>' : '' ) + '</div></div>'
				: '';

			$( '[data-track-items]', root ).innerHTML = '<div class="opt__title"><span>In this delivery</span></div>' + o.items.map( function ( it ) {
				return '<div class="muted" style="font-size:.92rem">' + it.qty + ' × ' + esc( it.title ) + ( it.fit === 'measure' ? ' — made to measure' : '' ) + '</div>';
			} ).join( '' );

			mapReady.then( function ( map ) {
				if ( ! map ) { return; }
				var dest = { lat: o.destination.lat, lng: o.destination.lng };
				if ( ! ctx.dest ) {
					ctx.dest = new google.maps.Marker( { map: map, position: dest, icon: svgIcon( 'dest' ), zIndex: 6 } );
					var b = new google.maps.LatLngBounds();
					b.extend( STORE ); b.extend( dest );
					map.fitBounds( b, 70 );
				}
				var from = STORE;
				if ( o.rider ) {
					from = { lat: o.rider.lat, lng: o.rider.lng };
					if ( ! ctx.rider ) {
						ctx.rider = new google.maps.Marker( { map: map, position: from, icon: svgIcon( 'rider' ), zIndex: 9 } );
					} else {
						smoothMove( ctx.rider, from );
					}
				}
				// Re-route only when the rider has moved meaningfully.
				var moved = ! ctx.lastRoute || google.maps.geometry.spherical.computeDistanceBetween(
					new google.maps.LatLng( ctx.lastRoute ), new google.maps.LatLng( from ) ) > 150;
				if ( o.status !== 'delivered' && moved ) {
					ctx.lastRoute = from;
					roadRoute( from, dest ).then( function ( route ) {
						drawRoute( map, ctx, route && route.path );
						if ( route && route.mins && o.rider && o.status === 'dispatched' ) {
							$( '[data-track-eta]', root ).textContent = 'About ' + route.mins + ' minutes away';
						}
					} );
				}
				if ( o.status === 'delivered' ) { drawRoute( map, ctx, null ); }
			} );
		}

		function poll() {
			api( 'orders/' + encodeURIComponent( key ) ).then( function ( o ) {
				render( o );
				if ( o.status !== 'delivered' && o.status !== 'cancelled' ) {
					setTimeout( poll, o.status === 'dispatched' ? 10000 : 30000 );
				}
			} ).catch( function ( e ) {
				$( '[data-track-eta]', root ).textContent = e.message;
			} );
		}
		poll();
	}

	/* =========================================================== RIDER */
	function initRider( root ) {
		var key = root.getAttribute( 'data-rider' );
		var token = root.getAttribute( 'data-token' );
		var statusEl = $( '[data-rider-status]', root );
		var mapEl = $( '[data-map="rider"]', root );
		var watchId = null, lastSent = 0, lastPos = null, wakeLock = null, ctx = {};

		if ( ! key || ! token ) { return; }

		var mapReady = loadMaps().then( function () { ctx.map = makeMap( mapEl, { zoom: 14, gestureHandling: 'greedy' } ); return ctx.map; } )
			.catch( function () { mapUnavailable( mapEl, 'Map unavailable — location sharing still works.' ); return null; } );

		api( 'orders/' + encodeURIComponent( key ) ).then( function ( o ) {
			$( '[data-rider-number]', root ).textContent = 'Order ' + o.number;
			$( '[data-rider-address]', root ).textContent = o.destination.address;
			$( '[data-rider-nav]', root ).href = 'https://www.google.com/maps/dir/?api=1&travelmode=driving&destination=' + o.destination.lat + ',' + o.destination.lng;
			mapReady.then( function ( map ) {
				if ( ! map ) { return; }
				new google.maps.Marker( { map: map, position: o.destination, icon: svgIcon( 'dest' ) } );
				var b = new google.maps.LatLngBounds();
				b.extend( STORE ); b.extend( o.destination );
				map.fitBounds( b, 50 );
			} );
		} ).catch( function ( e ) { statusEl.textContent = e.message; } );

		function send( pos, force ) {
			var now = Date.now();
			var p = { lat: pos.coords.latitude, lng: pos.coords.longitude };
			var far = ! lastPos || Math.abs( p.lat - lastPos.lat ) + Math.abs( p.lng - lastPos.lng ) > 0.0004;
			if ( ctx.map ) {
				if ( ! ctx.me ) { ctx.me = new google.maps.Marker( { map: ctx.map, icon: svgIcon( 'rider' ), zIndex: 9 } ); }
				ctx.me.setPosition( p );
			}
			if ( ! force && now - lastSent < 8000 && ! far ) { return; }
			lastSent = now; lastPos = p;
			api( 'orders/' + encodeURIComponent( key ) + '/rider', { token: token, lat: p.lat, lng: p.lng, heading: pos.coords.heading } )
				.then( function () { statusEl.textContent = 'Sharing live location · last sent ' + new Date().toLocaleTimeString(); } )
				.catch( function ( e ) { statusEl.textContent = e.message; } );
		}

		$( '[data-rider-start]', root ).addEventListener( 'click', function () {
			if ( ! navigator.geolocation ) { statusEl.textContent = 'GPS is not available on this phone.'; return; }
			if ( watchId !== null ) { return; }
			statusEl.textContent = 'Waiting for GPS…';
			watchId = navigator.geolocation.watchPosition( function ( pos ) { send( pos, lastSent === 0 ); }, function ( err ) {
				statusEl.textContent = 'Location error: ' + err.message;
			}, { enableHighAccuracy: true, maximumAge: 5000, timeout: 20000 } );
			if ( navigator.wakeLock ) { navigator.wakeLock.request( 'screen' ).then( function ( l ) { wakeLock = l; } ).catch( function () {} ); }
			this.textContent = 'Delivery in progress — keep this page open';
			this.disabled = true;
		} );

		$( '[data-rider-done]', root ).addEventListener( 'click', function () {
			if ( ! window.confirm( 'Mark this order as delivered?' ) ) { return; }
			api( 'orders/' + encodeURIComponent( key ) + '/rider', { token: token, action: 'delivered' } ).then( function () {
				if ( watchId !== null ) { navigator.geolocation.clearWatch( watchId ); watchId = null; }
				if ( wakeLock ) { wakeLock.release(); }
				statusEl.textContent = 'Delivered. Thank you!';
			} ).catch( function ( e ) { statusEl.textContent = e.message; } );
		} );
	}

	/* ------------------------------------------------------------ Boot */
	$$( '[data-zone]' ).forEach( initZone );
	$$( '[data-checkout]' ).forEach( initCheckout );
	$$( '[data-track]' ).forEach( initTrack );
	$$( '[data-rider]' ).forEach( initRider );
}() );
