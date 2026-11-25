/**
 * Propzed Projects: property assistant chat widget.
 *
 * Guided flow:  goal (buy / lease / invest / sell) -> home or commercial -> type ->
 * location -> budget -> live listings from /wp-json/propzed/v1/properties ->
 * name + mobile -> POST /wp-json/propzed/v1/enquiry (saved under Enquiries).
 * Typed questions are matched to quick answers (stamp duty, RERA, loans, site
 * visits, contact) or used as a keyword search. Everything runs in the browser.
 */
( function () {
	'use strict';

	var C = window.pzsChat;
	if ( ! C || document.getElementById( 'pzs-chat' ) ) {
		return;
	}

	var lead = { deal: '', home: null, type: '', typeName: '', location: '', locationName: '', budget: '', budgetName: '', q: '', topic: '' };
	var els = {};
	var started = false;
	var store = {
		get: function ( k ) {
			try {
				return window.sessionStorage.getItem( k );
			} catch ( e ) {
				return null;
			}
		},
		set: function ( k, v ) {
			try {
				window.sessionStorage.setItem( k, v );
			} catch ( e ) {}
		},
	};

	function decode( s ) {
		var t = document.createElement( 'textarea' );
		t.innerHTML = s || '';
		return t.value;
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
			} else if ( null !== v && undefined !== v ) {
				n.setAttribute( k, v );
			}
		} );
		( children || [] ).forEach( function ( c ) {
			if ( c ) {
				n.appendChild( 'string' === typeof c ? document.createTextNode( c ) : c );
			}
		} );
		return n;
	}

	function wa( text ) {
		return C.whatsapp ? 'https://wa.me/' + C.whatsapp + '?text=' + encodeURIComponent( text ) : '';
	}

	/* UI ------------------------------------------------------------------ */

	function build() {
		var launcher = el( 'button', { type: 'button', id: 'pzs-chat-launcher', class: 'pzs-chat-launcher', 'aria-controls': 'pzs-chat', 'aria-expanded': 'false', onclick: toggle }, [
			el( 'span', { class: 'pzs-chat-launcher__dot', 'aria-hidden': 'true' } ),
			el( 'span', { class: 'pzs-chat-launcher__text', text: 'Ask Propzed' } ),
		] );
		var log = el( 'div', { class: 'pzs-chat__log', role: 'log', 'aria-live': 'polite' } );
		var input = el( 'input', { type: 'text', class: 'pzs-chat__input', placeholder: 'Type a question, e.g. villa in Karjat under 2 Cr', 'aria-label': 'Message', autocomplete: 'off', enterkeyhint: 'send' } );
		var form = el( 'form', { class: 'pzs-chat__form', onsubmit: function ( e ) {
			e.preventDefault();
			var v = input.value.trim();
			if ( v ) {
				input.value = '';
				onText( v );
			}
		} }, [ input, el( 'button', { type: 'submit', class: 'pzs-chat__send', 'aria-label': 'Send', text: '➤' } ) ] );
		var panel = el( 'div', { id: 'pzs-chat', class: 'pzs-chat', role: 'dialog', 'aria-modal': 'false', 'aria-label': C.name, hidden: 'hidden' }, [
			el( 'div', { class: 'pzs-chat__head' }, [
				el( 'div', {}, [ el( 'strong', { text: C.name } ), el( 'small', { text: 'Replies instantly · advisors 10am – 7pm' } ) ] ),
				el( 'div', { class: 'pzs-chat__head-actions' }, [
					C.tel ? el( 'a', { href: C.tel, class: 'pzs-chat__icon', 'aria-label': 'Call', text: '☎' } ) : null,
					el( 'button', { type: 'button', class: 'pzs-chat__icon', 'aria-label': 'Close', text: '✕', onclick: toggle } ),
				] ),
			] ),
			log,
			el( 'div', { class: 'pzs-chat__quick' } ),
			form,
			el( 'p', { class: 'pzs-chat__privacy' }, [ 'Your details go only to the Propzed team.', C.privacy ? el( 'a', { href: C.privacy, text: ' Privacy' } ) : null ] ),
		] );
		document.body.appendChild( panel );
		document.body.appendChild( launcher );
		els = { panel: panel, launcher: launcher, log: log, input: input, quick: panel.querySelector( '.pzs-chat__quick' ) };

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! panel.hidden ) {
				toggle();
			}
		} );

		// Gentle nudge on property pages, once per visit.
		if ( C.context && ! C.context.sold && ! store.get( 'pzs_nudged' ) ) {
			window.setTimeout( function () {
				if ( ! panel.hidden || store.get( 'pzs_nudged' ) ) {
					return;
				}
				store.set( 'pzs_nudged', '1' );
				var tip = el( 'button', { type: 'button', class: 'pzs-chat-nudge', onclick: function () {
					tip.remove();
					toggle();
				} }, [ C.context.inventory ? 'Check live unit availability & price sheet?' : 'Want the price, photos or a site visit?' ] );
				document.body.appendChild( tip );
				window.setTimeout( function () {
					if ( tip.parentNode ) {
						tip.remove();
					}
				}, 12000 );
			}, 25000 );
		}
	}

	function toggle() {
		var open = els.panel.hidden;
		els.panel.hidden = ! open;
		els.launcher.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		document.body.classList.toggle( 'pzs-chat-open', open );
		var nudge = document.querySelector( '.pzs-chat-nudge' );
		if ( nudge ) {
			nudge.remove();
		}
		if ( open ) {
			if ( ! started ) {
				started = true;
				start();
			}
			window.setTimeout( function () {
				els.input.focus();
			}, 50 );
		} else {
			els.launcher.focus();
		}
	}

	function bot( content ) {
		var b = el( 'div', { class: 'pzs-msg is-bot' } );
		( Array.isArray( content ) ? content : [ content ] ).forEach( function ( c ) {
			if ( c ) {
				b.appendChild( 'string' === typeof c ? el( 'p', { text: c } ) : c );
			}
		} );
		els.log.appendChild( b );
		els.log.scrollTop = els.log.scrollHeight;
		return b;
	}

	function me( text ) {
		els.log.appendChild( el( 'div', { class: 'pzs-msg is-me' }, [ el( 'p', { text: text } ) ] ) );
		els.log.scrollTop = els.log.scrollHeight;
	}

	// Quick-reply buttons: [ [label, fn], ... ].
	function quick( options ) {
		els.quick.textContent = '';
		options.forEach( function ( o ) {
			if ( ! o ) {
				return;
			}
			els.quick.appendChild( el( 'button', { type: 'button', class: 'pzs-qr', text: o[ 0 ], onclick: function () {
				els.quick.textContent = '';
				if ( ! o[ 2 ] ) {
					me( o[ 0 ] );
				}
				o[ 1 ]();
			} } ) );
		} );
	}

	function link( text, href, cls ) {
		return el( 'a', { href: href, class: cls || 'pzs-chat__link', target: 0 === href.indexOf( 'https://wa.me' ) ? '_blank' : null, rel: 'noopener', text: text } );
	}

	/* Flow ---------------------------------------------------------------- */

	function start() {
		if ( C.context && ! C.context.sold ) {
			bot( [ 'You are looking at ' + C.context.title + ( C.context.price ? ' (' + C.context.price + ')' : '' ) + '. How can I help?' ] );
			quick( [
				[ 'Book a site visit', function () {
					lead.topic = 'Site visit: ' + C.context.title;
					askName( 'Great. Which name should our advisor ask for?' );
				} ],
				[ 'Get price sheet & details', function () {
					lead.topic = 'Price sheet: ' + C.context.title;
					askName( 'I will have the price sheet, payment plan and photos sent to you. Your name?' );
				} ],
				C.context.inventory ? [ 'Check unit availability', function () {
					var sec = document.getElementById( 'availability' );
					if ( sec ) {
						sec.scrollIntoView( { behavior: 'smooth' } );
					}
					bot( 'The live availability chart is on this page: green units are available. Tap any unit to enquire about it.' );
					mainMenu( true );
				} ] : null,
				[ 'Something else', function () {
					mainMenu();
				} ],
			] );
			return;
		}
		bot( C.hello );
		mainMenu( true );
	}

	function mainMenu( silent ) {
		if ( ! silent ) {
			bot( 'What are you looking for?' );
		}
		quick( [
			[ 'Buy', function () {
				lead.deal = 'Buy';
				askSegment();
			} ],
			[ 'Lease / Rent', function () {
				lead.deal = 'Lease';
				askSegment();
			} ],
			[ 'Invest', function () {
				lead.deal = 'Invest';
				askSegment();
			} ],
			[ 'Sell / list my property', function () {
				bot( [ 'We list verified properties for owners, builders and brokers. Submit yours here, or leave your number and we will call you.', link( 'List your property', C.sell, 'pz-btn pz-btn--gold pzs-chat__btn' ) ] );
				lead.topic = 'Wants to sell / list a property';
				quick( [ [ 'Call me instead', function () {
					askName( 'Sure. Your name?' );
				} ] ] );
			} ],
			[ 'Talk to an advisor', function () {
				lead.topic = 'Wants to talk to an advisor';
				askName( 'Happy to connect you. Your name?' );
			} ],
		] );
	}

	function askSegment() {
		bot( 'A home or a commercial property?' );
		quick( [
			[ 'Home: flat, villa, plot', function () {
				lead.home = true;
				askType();
			} ],
			[ 'Commercial: office, shop, warehouse, hospital…', function () {
				lead.home = false;
				askType();
			} ],
		] );
	}

	function askType() {
		var types = C.types.filter( function ( t ) {
			return t.home === lead.home && ( t.count > 0 || lead.home );
		} );
		bot( 'Which type?' );
		quick( types.map( function ( t ) {
			return [ t.name, function () {
				lead.type = t.slug;
				lead.typeName = t.name;
				askLocation();
			} ];
		} ).concat( [ [ 'Any', function () {
			lead.type = '';
			lead.typeName = lead.home ? 'Home' : 'Commercial property';
			askLocation();
		} ] ] ) );
	}

	function askLocation() {
		bot( 'Preferred location?' );
		var popular = lead.home ? [ 'navi-mumbai', 'mumbai', 'thane', 'raigad', 'panvel', 'karjat', 'khopoli', 'lonavala', 'pune' ] : [ 'mumbai', 'navi-mumbai', 'thane', 'bhiwandi', 'panvel', 'pune', 'bengaluru', 'hyderabad', 'delhi-ncr' ];
		var all = C.locations.slice();
		// Karjat / Khopoli are localities under Raigad: offer them explicitly for homes.
		[ [ 'karjat', 'Karjat' ], [ 'khopoli', 'Khopoli' ], [ 'alibaug', 'Alibaug' ], [ 'lonavala', 'Lonavala' ] ].forEach( function ( x ) {
			if ( ! all.some( function ( l ) {
				return l.slug === x[ 0 ];
			} ) ) {
				all.push( { slug: x[ 0 ], name: x[ 1 ] } );
			}
		} );
		if ( lead.home && popular.indexOf( 'alibaug' ) < 0 ) {
			popular.splice( 4, 0, 'alibaug' );
		}
		var opts = [];
		popular.forEach( function ( slug ) {
			var l = all.filter( function ( x ) {
				return x.slug === slug;
			} )[ 0 ];
			if ( l ) {
				opts.push( [ l.name, function () {
					lead.location = l.slug;
					lead.locationName = l.name;
					askBudget();
				} ] );
			}
		} );
		opts.push( [ 'Anywhere', function () {
			lead.location = '';
			lead.locationName = '';
			askBudget();
		} ] );
		quick( opts );
	}

	function askBudget() {
		var list = C.budgets[ lead.home ? 'home' : 'commercial' ] || {};
		bot( 'Approximate budget?' );
		quick( Object.keys( list ).map( function ( range ) {
			return [ list[ range ], function () {
				lead.budget = range;
				lead.budgetName = list[ range ];
				search();
			} ];
		} ).concat( [ [ 'Not sure yet', function () {
			lead.budget = '';
			lead.budgetName = '';
			search();
		} ] ] ) );
	}

	function query() {
		var p = [ 'per_page=12' ];
		if ( lead.type ) {
			p.push( 'type=' + encodeURIComponent( lead.type ) );
		}
		if ( lead.location ) {
			p.push( 'location=' + encodeURIComponent( lead.location ) );
		}
		if ( lead.budget ) {
			p.push( 'budget=' + encodeURIComponent( lead.budget ) );
		}
		if ( 'Lease' === lead.deal ) {
			p.push( 'status=for-lease' );
		}
		if ( lead.q ) {
			p.push( 'q=' + encodeURIComponent( lead.q ) );
		}
		return p.join( '&' );
	}

	function listingUrl() {
		var p = [];
		if ( lead.type ) {
			p.push( 'pz_type=' + encodeURIComponent( lead.type ) );
		}
		if ( lead.location ) {
			p.push( 'pz_location=' + encodeURIComponent( lead.location ) );
		}
		if ( lead.budget ) {
			p.push( 'pz_budget=' + encodeURIComponent( lead.budget ) );
		}
		if ( lead.q ) {
			p.push( 'pz_q=' + encodeURIComponent( lead.q ) );
		}
		return C.listing + ( p.length ? ( C.listing.indexOf( '?' ) < 0 ? '?' : '&' ) + p.join( '&' ) : '' );
	}

	function search() {
		var wait = bot( 'Searching verified listings…' );
		fetch( C.api + 'properties?' + query(), { headers: { Accept: 'application/json' } } )
			.then( function ( r ) {
				return r.ok ? r.json() : { items: [] };
			} )
			.catch( function () {
				return { items: [] };
			} )
			.then( function ( data ) {
				wait.remove();
				var items = ( data.items || [] ).filter( function ( it ) {
					return ! ( it.statuses || [] ).some( function ( s ) {
						return 'sold' === s.slug || 'leased-out' === s.slug;
					} );
				} );
				// Flats are often listed inside new projects (and the other way round): try the sibling type once.
				var sibling = { apartment: 'residential-project', 'residential-project': 'apartment' }[ lead.type ];
				if ( ! items.length && sibling && ! lead.retried ) {
					lead.retried = true;
					lead.type = sibling;
					search();
					return;
				}
				lead.retried = false;
				var what = [ lead.typeName, lead.locationName ? 'in ' + lead.locationName : '', lead.budgetName ? '(' + lead.budgetName + ')' : '' ].filter( Boolean ).join( ' ' );
				if ( ! items.length ) {
					bot( 'No public listing matches ' + ( what || 'that' ) + ' right now. Our advisors usually have off-market and pre-launch options, often at better prices. Shall I ask one to call you?' );
				} else {
					var list = el( 'div', { class: 'pzs-chat__cards' } );
					items.slice( 0, 3 ).forEach( function ( it ) {
						list.appendChild( el( 'a', { class: 'pzs-chat__card', href: it.link }, [
							it.image ? el( 'img', { src: it.image, alt: '', loading: 'lazy', width: 72, height: 56 } ) : null,
							el( 'span', {}, [
								el( 'strong', { text: decode( it.title ) } ),
								el( 'small', { text: [ it.price_label, it.area_label, decode( it.location_label ) ].filter( Boolean ).join( ' · ' ) } ),
							] ),
						] ) );
					} );
					bot( [ 'Here ' + ( items.length > 1 ? 'are some' : 'is one' ) + ( what ? ' for ' + what : '' ) + ':', list, items.length > 3 ? link( 'See all ' + items.length + ' on the website →', listingUrl() ) : null ] );
				}
				guideFor( lead.location || lead.type );
				lead.topic = [ lead.deal, what ].filter( Boolean ).join( ': ' );
				quick( [
					[ items.length ? 'Get more options & best price' : 'Yes, call me', function () {
						askName( 'Our advisor will share a shortlist, including off-market options. Your name?' );
					} ],
					C.whatsapp ? [ 'Continue on WhatsApp', function () {
						window.open( wa( 'Hi Propzed, I am looking for ' + ( lead.topic || 'a property' ) + '.' ), '_blank', 'noopener' );
					}, true ] : null,
					[ 'Start over', function () {
						lead = { deal: '', home: null, type: '', typeName: '', location: '', locationName: '', budget: '', budgetName: '', q: '', topic: '' };
						mainMenu();
					} ],
				] );
			} );
	}

	function guideFor( word ) {
		if ( ! word || ! C.guides ) {
			return;
		}
		var g = C.guides.filter( function ( x ) {
			return x.slug.indexOf( word ) >= 0;
		} )[ 0 ];
		if ( g ) {
			bot( [ 'Helpful read:', link( g.title, g.url ) ] );
		}
	}

	/* Lead capture --------------------------------------------------------- */

	var awaiting = null;

	function askName( text ) {
		bot( text );
		awaiting = 'name';
		els.quick.textContent = '';
		els.input.placeholder = 'Your name';
		els.input.setAttribute( 'autocomplete', 'name' );
		els.input.focus();
	}

	function onName( v ) {
		lead.name = v.slice( 0, 60 );
		awaiting = 'phone';
		bot( 'Thanks ' + lead.name.split( ' ' )[ 0 ] + '! Your 10-digit mobile number? (also on WhatsApp ideally)' );
		els.input.placeholder = '98XXXXXXXX';
		els.input.setAttribute( 'inputmode', 'tel' );
		els.input.setAttribute( 'autocomplete', 'tel' );
	}

	function onPhone( v ) {
		var digits = v.replace( /\D/g, '' );
		if ( digits.length < 10 || digits.length > 13 ) {
			bot( 'That does not look like a mobile number. Please enter 10 digits, e.g. 9876543210.' );
			return;
		}
		awaiting = null;
		els.input.placeholder = 'Type a question…';
		els.input.removeAttribute( 'inputmode' );
		var wait = bot( 'Sending…' );
		var body = {
			type: C.context && C.context.id ? 'property' : 'requirement',
			name: lead.name,
			phone: digits,
			property_id: C.context && C.context.id ? C.context.id : 0,
			req_deal: lead.deal,
			req_type: lead.typeName,
			req_location: lead.locationName,
			req_budget: lead.budgetName,
			message: 'Sent from the website assistant. ' + ( lead.topic || '' ) + ( lead.q ? ' | Asked: ' + lead.q : '' ) + ' | Page: ' + window.location.href,
		};
		fetch( C.api + 'enquiry', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify( body ) } )
			.then( function ( r ) {
				return r.json().then( function ( j ) {
					return { ok: r.ok, data: j };
				} );
			} )
			.catch( function () {
				return { ok: false, data: {} };
			} )
			.then( function ( res ) {
				wait.remove();
				if ( res.ok ) {
					bot( [ 'Done! An advisor will call you shortly' + ( isOfficeHours() ? '.' : ' (we open at 10am).' ), C.whatsapp ? link( 'Or chat now on WhatsApp', wa( 'Hi Propzed, I am ' + lead.name + '. ' + ( lead.topic || '' ) ), 'pz-btn pz-btn--wa pzs-chat__btn' ) : null ] );
					if ( window.dataLayer ) {
						window.dataLayer.push( { event: 'generate_lead', lead_source: 'propzed_assistant' } );
					}
					if ( 'function' === typeof window.gtag ) {
						window.gtag( 'event', 'generate_lead', { lead_source: 'propzed_assistant' } );
					}
				} else {
					bot( [ ( res.data && res.data.message ) || 'Sorry, that did not go through.', C.tel ? link( 'Call ' + C.phone, C.tel ) : null ] );
				}
				quick( [ [ 'Ask something else', function () {
					mainMenu();
				} ] ] );
			} );
	}

	function isOfficeHours() {
		var h = new Date().getHours();
		return h >= 10 && h < 19;
	}

	/* Free text ------------------------------------------------------------ */

	var ANSWERS = [
		{ re: /stamp|registration|regist/i, guide: 'cost', text: 'In Maharashtra, stamp duty is about 6% of the property value inside Mumbai (BMC) limits and about 7% in most other city limits such as Navi Mumbai, Thane and Pune, with a 1% concession for women buyers. Registration is 1%, capped at ₹30,000. It is charged on the higher of the agreement value and the ready-reckoner rate.' },
		{ re: /\brera\b|maharera/i, guide: 'cost', text: 'Every project with more than 8 units or 500 sq.m of land must be registered on MahaRERA. Check the registration number, the approved plans, the promised possession date and any complaints on maharera.maharashtra.gov.in before you pay a booking amount.' },
		{ re: /\bgst\b/i, guide: 'cost', text: 'GST is 5% (no input credit) on under-construction homes and 1% on affordable homes (carpet up to 60 sq.m in metro areas and value up to ₹45 lakh). Ready-to-move flats with an Occupancy Certificate have no GST.' },
		{ re: /loan|emi|interest|bank/i, guide: 'budget', text: 'Banks usually fund up to 75–90% of the value, depending on the loan size. A useful rule: keep the EMI under about 40% of your take-home pay. Our guides have an EMI calculator, and our advisors can connect you with partner banks.' },
		{ re: /\bna\b|non.?agri|7\/12|satbara|farm ?land|agricultur/i, guide: 'karjat', text: 'In Maharashtra only registered farmers can buy agricultural land, so second-home buyers should buy NA (non-agricultural) plots or villas in approved layouts. Always check the 7/12 extract, the NA order, the title search for 30 years and the access road.' },
	];

	var TYPE_WORDS = [ [ /villa|bungalow|farm ?house/i, 'villa' ], [ /\bplot/i, 'villa-plot' ], [ /flat|apartment|\bbhk\b|\d\s?bhk/i, 'apartment' ], [ /project|new launch|pre.?launch/i, 'residential-project' ], [ /office/i, 'office-space' ], [ /warehouse|godown|logistic/i, 'warehouse' ], [ /hospital|clinic|healthcare/i, 'hospital' ], [ /showroom|shop|retail/i, 'showroom' ], [ /hotel|resort/i, 'hotel' ], [ /\bmall/i, 'mall' ], [ /\bland\b|industrial/i, 'land' ] ];

	function onText( v ) {
		me( v );
		if ( 'name' === awaiting ) {
			return onName( v );
		}
		if ( 'phone' === awaiting ) {
			return onPhone( v );
		}
		els.quick.textContent = '';

		if ( /call|phone|number|contact|talk|speak|advisor|agent/i.test( v ) && v.length < 60 ) {
			bot( [ 'You can reach us directly:', C.tel ? link( 'Call ' + C.phone, C.tel ) : null, C.whatsapp ? link( 'WhatsApp us', wa( 'Hi Propzed, ' + v ) ) : null, 'Or leave your number and we will call you.' ] );
			lead.topic = v;
			quick( [ [ 'Call me back', function () {
				askName( 'Your name?' );
			} ] ] );
			return;
		}
		if ( /site visit|visit|see the (flat|property)|tour/i.test( v ) ) {
			lead.topic = 'Site visit' + ( C.context ? ': ' + C.context.title : '' ) + ' — "' + v + '"';
			return askName( 'Let us fix a site visit. Your name?' );
		}
		if ( /\bsell\b|list my|post my|my property/i.test( v ) ) {
			bot( [ 'You can list your property here:', link( 'List your property', C.sell ) ] );
			return;
		}
		for ( var i = 0; i < ANSWERS.length; i++ ) {
			if ( ANSWERS[ i ].re.test( v ) ) {
				bot( ANSWERS[ i ].text );
				guideFor( ANSWERS[ i ].guide );
				lead.q = '';
				quick( [ [ 'Find me a property', function () {
					mainMenu();
				} ], [ 'Talk to an advisor', function () {
					lead.topic = 'Question: ' + v;
					askName( 'Your name?' );
				} ] ] );
				return;
			}
		}

		// Understand "2 bhk in ulwe under 80 lakh" style searches.
		var found = false;
		TYPE_WORDS.some( function ( tw ) {
			if ( tw[ 0 ].test( v ) ) {
				var t = C.types.filter( function ( x ) {
					return x.slug === tw[ 1 ];
				} )[ 0 ];
				if ( t ) {
					lead.type = t.slug;
					lead.typeName = t.name;
					lead.home = t.home;
					found = true;
				}
				return true;
			}
			return false;
		} );
		var places = C.locations.concat( [ { slug: 'karjat', name: 'Karjat' }, { slug: 'khopoli', name: 'Khopoli' }, { slug: 'alibaug', name: 'Alibaug' }, { slug: 'lonavala', name: 'Lonavala' }, { slug: 'kharghar', name: 'Kharghar' }, { slug: 'ulwe', name: 'Ulwe' }, { slug: 'vashi', name: 'Vashi' }, { slug: 'panvel', name: 'Panvel' }, { slug: 'taloja', name: 'Taloja' }, { slug: 'bkc', name: 'BKC' }, { slug: 'andheri', name: 'Andheri' } ] );
		places.some( function ( l ) {
			var first = l.name.split( /[ &–-]/ )[ 0 ].toLowerCase();
			if ( first.length > 2 && v.toLowerCase().indexOf( first ) >= 0 ) {
				lead.location = l.slug;
				lead.locationName = l.name;
				found = true;
				return true;
			}
			return false;
		} );
		var m = v.match( /(\d+(?:\.\d+)?)\s*(cr|crore|l|lac|lakh|lakhs)\b/i );
		if ( m ) {
			var amount = parseFloat( m[ 1 ] ) * ( /^c/i.test( m[ 2 ] ) ? 1e7 : 1e5 );
			lead.budget = '0-' + Math.round( amount );
			lead.budgetName = 'up to ' + m[ 1 ] + ' ' + m[ 2 ];
			found = true;
		}
		if ( /lease|rent/i.test( v ) ) {
			lead.deal = 'Lease';
		} else if ( /invest/i.test( v ) ) {
			lead.deal = lead.deal || 'Invest';
		} else {
			lead.deal = lead.deal || 'Buy';
		}
		lead.q = found ? '' : v;
		if ( ! lead.typeName ) {
			lead.typeName = 'Property';
		}
		search();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', build );
	} else {
		build();
	}
}() );
