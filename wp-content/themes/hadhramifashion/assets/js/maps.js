/**
 * Hadhrami Fashion — "Is your door within the two-hour circle?" map.
 *
 * Draws the 2-hour and 24-hour circles around the atelier, lets the visitor search
 * an address, tap the map or use their location, and asks the Hadhrami Delivery
 * plugin (GET /quote) what reaches them and when. Checkout, rider app and live
 * tracking are provided by the plugin itself.
 */
( function () {
	'use strict';

	var cfg = window.HF || {};
	var U = window.HFUtil;
	var $ = U.$, $$ = U.$$, esc = U.esc, money = U.money;
	var STORE = { lat: cfg.store.lat, lng: cfg.store.lng };

	var mapsPromise;
	function loadMaps() {
		if ( mapsPromise ) { return mapsPromise; }
		mapsPromise = new Promise( function ( resolve, reject ) {
			if ( ! cfg.mapsKey ) { reject( new Error( 'no-key' ) ); return; }
			if ( window.google && window.google.maps && window.google.maps.importLibrary ) { resolve( window.google.maps ); return; }
			window.__hfMapsReady = function () { resolve( window.google.maps ); };
			var s = document.createElement( 'script' );
			s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent( cfg.mapsKey ) +
				'&v=weekly&libraries=places&loading=async&callback=__hfMapsReady';
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
		{ featureType: 'road.highway', elementType: 'geometry', stylers: [ { color: '#e5dccb' } ] },
		{ featureType: 'water', elementType: 'geometry', stylers: [ { color: '#cfd6d3' } ] }
	];

	function svgIcon( kind ) {
		var svg = kind === 'store'
			? '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44"><circle cx="22" cy="22" r="20" fill="#14161a" stroke="#c8a86f" stroke-width="2"/><text x="22" y="28" font-family="Georgia,serif" font-size="17" fill="#c8a86f" text-anchor="middle">H</text></svg>'
			: '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="46" viewBox="0 0 34 46"><path d="M17 45S2 28.5 2 17a15 15 0 0 1 30 0c0 11.5-15 28-15 28Z" fill="#5d1e24" stroke="#fff" stroke-width="2"/><circle cx="17" cy="17" r="5.5" fill="#fbf8f2"/></svg>';
		var size = kind === 'store' ? [ 44, 44, 22, 22 ] : [ 34, 46, 17, 45 ];
		return {
			url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent( svg ),
			scaledSize: new google.maps.Size( size[ 0 ], size[ 1 ] ),
			anchor: new google.maps.Point( size[ 2 ], size[ 3 ] )
		};
	}

	function checkIcon() {
		return '<svg class="hf-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m5 12 5 5 9-10"/></svg>';
	}

	function initZone( root ) {
		var mapEl = $( '[data-map="zone"]', root );
		var result = $( '[data-zone-result]', root );
		var ctx = {};

		function show( pick, q ) {
			var ex = q.express, ta = q.tailored;
			var html = '<h4>' + ( q.in_express ? 'Yes — you are inside the two-hour circle.' : q.in_area ? 'You are inside our 24-hour tailoring area.' : 'You are beyond our rider routes.' ) + '</h4>';
			html += '<p class="muted" style="margin:0">' + esc( pick.address || 'Your pinned location' ) + ( q.km != null ? ' · about ' + Number( q.km ).toFixed( 1 ) + ' km by road' : '' ) + '</p><ul>';
			html += '<li class="' + ( ex.available ? '' : 'is-off' ) + '">' + checkIcon() + '<span><strong>Ready-to-wear shirts</strong> — ' +
				( ex.available ? 'order now, at your door ' + esc( ex.due_text ) : esc( ex.reason || 'beyond the 2-hour radius' ) ) + '</span></li>';
			html += '<li class="' + ( ta.available ? '' : 'is-off' ) + '">' + checkIcon() + '<span><strong>Suits & shirts to your measure</strong> — ' +
				( ta.available ? 'delivered ' + esc( ta.due_text ) : 'please call the atelier to arrange a fitting' ) + '</span></li>';
			if ( q.in_area ) {
				html += '<li>' + checkIcon() + '<span><strong>Delivery</strong> — ' + ( q.fee ? money( q.fee ) : 'complimentary' ) +
					( q.free_above ? ', complimentary on bags over ' + money( q.free_above ) : '' ) + '</span></li>';
			}
			result.innerHTML = html + '</ul>';
			result.classList.add( 'is-on' );
		}

		function pick( p ) {
			if ( ctx.map ) {
				if ( ! ctx.dest ) { ctx.dest = new google.maps.Marker( { map: ctx.map, icon: svgIcon( 'dest' ), zIndex: 6 } ); }
				ctx.dest.setPosition( p );
				var b = new google.maps.LatLngBounds();
				b.extend( STORE ); b.extend( p );
				ctx.map.fitBounds( b, 70 );
			}
			if ( ! cfg.quoteUrl ) {
				U.toast( 'Delivery checker is not set up yet.' );
				return;
			}
			fetch( cfg.quoteUrl + ( cfg.quoteUrl.indexOf( '?' ) > -1 ? '&' : '?' ) + 'lat=' + p.lat + '&lng=' + p.lng, { credentials: 'omit', cache: 'no-store' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( q ) {
					if ( ! q || ! q.express ) { throw new Error( ( q && q.message ) || 'Could not check this address.' ); }
					show( p, q );
				} )
				.catch( function ( e ) { U.toast( e.message ); } );
		}

		var locate = $( '[data-locate]', root );
		if ( locate ) {
			locate.addEventListener( 'click', function () {
				if ( ! navigator.geolocation ) { U.toast( 'Location is not available on this device.' ); return; }
				navigator.geolocation.getCurrentPosition( function ( pos ) {
					pick( { lat: pos.coords.latitude, lng: pos.coords.longitude, address: 'Your current location' } );
				}, function () {
					U.toast( 'Please allow location access, or type your address.' );
				}, { enableHighAccuracy: true, timeout: 12000 } );
			} );
		}

		loadMaps().then( function ( gm ) {
			var ph = $( '[data-map-placeholder]', mapEl );
			if ( ph ) { ph.remove(); }
			ctx.map = new gm.Map( mapEl, { center: STORE, zoom: 11, styles: MAP_STYLE, disableDefaultUI: true, zoomControl: true, gestureHandling: 'cooperative', clickableIcons: false } );
			new gm.Marker( { map: ctx.map, position: STORE, icon: svgIcon( 'store' ), title: cfg.store.name, zIndex: 5 } );
			if ( cfg.areaKm ) {
				new gm.Circle( { map: ctx.map, center: STORE, radius: cfg.areaKm * 1000, strokeColor: '#a6834b', strokeOpacity: 0.35, strokeWeight: 1, fillColor: '#a6834b', fillOpacity: 0.04, clickable: false } );
			}
			if ( cfg.expressKm ) {
				new gm.Circle( { map: ctx.map, center: STORE, radius: cfg.expressKm * 1000, strokeColor: '#5d1e24', strokeOpacity: 0.6, strokeWeight: 1.4, fillColor: '#5d1e24', fillOpacity: 0.06, clickable: false } );
			}
			ctx.map.addListener( 'click', function ( e ) { pick( { lat: e.latLng.lat(), lng: e.latLng.lng(), address: '' } ); } );

			return gm.importLibrary( 'places' ).then( function ( places ) {
				if ( ! places.PlaceAutocompleteElement ) { return; }
				var slot = $( '[data-autocomplete-slot]', root );
				var input = $( 'input', slot );
				var pac = new places.PlaceAutocompleteElement( { locationBias: { center: STORE, radius: Math.max( 5000, ( cfg.areaKm || 30 ) * 1500 ) } } );
				pac.id = input.id;
				input.replaceWith( pac );
				var use = function ( place ) {
					place.fetchFields( { fields: [ 'formattedAddress', 'location' ] } ).then( function () {
						if ( place.location ) { pick( { lat: place.location.lat(), lng: place.location.lng(), address: place.formattedAddress || '' } ); }
					} );
				};
				pac.addEventListener( 'gmp-select', function ( ev ) { if ( ev.placePrediction ) { use( ev.placePrediction.toPlace() ); } } );
				pac.addEventListener( 'gmp-placeselect', function ( ev ) { if ( ev.place ) { use( ev.place ); } } );
			} );
		} ).catch( function () {
			var ph = $( '[data-map-placeholder]', mapEl );
			if ( ph ) { ph.innerHTML = '<span>' + esc( cfg.store.name + ' — ' + cfg.store.address ) + '<br><small>Use the location button to check your address.</small></span>'; }
		} );
	}

	$$( '[data-zone]' ).forEach( initZone );
}() );
