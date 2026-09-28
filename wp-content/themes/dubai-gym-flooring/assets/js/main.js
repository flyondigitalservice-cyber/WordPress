/**
 * Dubai Gym Flooring — front-end behaviour (no dependencies).
 */
( function () {
	'use strict';

	var doc = document;
	var cfg = window.DGF || {};
	doc.documentElement.classList.add( 'dgf-js' );

	function track( name, data ) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push( Object.assign( { event: name }, data || {} ) );
	}

	/* Header shadow on scroll. */
	var header = doc.querySelector( '[data-dgf-header]' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	/* Mobile navigation. */
	var burger = doc.querySelector( '[data-dgf-burger]' );
	var nav = doc.querySelector( '[data-dgf-nav]' );
	function closeNav() {
		if ( ! nav ) {
			return;
		}
		nav.classList.remove( 'is-open' );
		doc.body.classList.remove( 'dgf-nav-open' );
		if ( burger ) {
			burger.setAttribute( 'aria-expanded', 'false' );
		}
	}
	if ( burger && nav ) {
		burger.addEventListener( 'click', function () {
			var open = ! nav.classList.contains( 'is-open' );
			nav.classList.toggle( 'is-open', open );
			doc.body.classList.toggle( 'dgf-nav-open', open );
			burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		doc.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeNav();
			}
		} );
		nav.querySelectorAll( '.menu-item-has-children' ).forEach( function ( item, i ) {
			var btn = doc.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'dgf-sub-toggle';
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.setAttribute( 'aria-label', 'Show sub-menu' );
			btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
			var sub = item.querySelector( '.sub-menu' );
			if ( sub ) {
				sub.id = sub.id || 'dgf-sub-' + i;
				btn.setAttribute( 'aria-controls', sub.id );
			}
			btn.addEventListener( 'click', function () {
				var open = item.classList.toggle( 'is-open' );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
			item.insertBefore( btn, sub );
		} );
	}

	/* Track WhatsApp clicks (GA4 / GTM via dataLayer). */
	doc.addEventListener( 'click', function ( e ) {
		var link = e.target.closest && e.target.closest( 'a[href*="wa.me/"]' );
		if ( link ) {
			track( 'whatsapp_click', { link_url: link.href, page_path: location.pathname } );
		}
	} );

	/* Lead forms: save the lead, then continue to WhatsApp. */
	doc.querySelectorAll( 'form[data-dgf-lead]' ).forEach( function ( form ) {
		var status = form.querySelector( '.dgf-form__status' );
		form.addEventListener( 'submit', function ( e ) {
			var name = form.elements.name;
			var phone = form.elements.phone;
			var digits = ( phone.value || '' ).replace( /\D+/g, '' );
			var bad = false;
			[ name, phone ].forEach( function ( field ) {
				field.removeAttribute( 'aria-invalid' );
			} );
			if ( ! name.value.trim() ) {
				name.setAttribute( 'aria-invalid', 'true' );
				bad = true;
			}
			if ( digits.length < 7 ) {
				phone.setAttribute( 'aria-invalid', 'true' );
				bad = true;
			}
			if ( bad ) {
				e.preventDefault();
				status.textContent = ( cfg.i18n && cfg.i18n.error ) || 'Please fill in your name and phone number.';
				( name.value.trim() ? phone : name ).focus();
				return;
			}
			if ( ! window.fetch || ! cfg.leadEndpoint ) {
				return; // Normal POST fallback → admin-post.php → WhatsApp.
			}
			e.preventDefault();
			form.classList.add( 'is-sending' );
			status.textContent = ( cfg.i18n && cfg.i18n.sending ) || 'Opening WhatsApp…';

			var data = {};
			new FormData( form ).forEach( function ( value, key ) {
				data[ key ] = value;
			} );

			fetch( cfg.leadEndpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				credentials: 'same-origin',
				body: JSON.stringify( data )
			} )
				.then( function ( res ) {
					return res.json();
				} )
				.then( function ( json ) {
					form.classList.remove( 'is-sending' );
					if ( json && json.ok ) {
						track( 'generate_lead', { form_location: location.pathname, product: data.product, location: data.location } );
						status.textContent = json.message || ( cfg.i18n && cfg.i18n.saved ) || '';
						if ( json.whatsapp ) {
							window.location.href = json.whatsapp;
						} else {
							form.reset();
						}
					} else {
						status.textContent = ( json && json.message ) || ( cfg.i18n && cfg.i18n.error );
						if ( json && json.whatsapp ) {
							window.location.href = json.whatsapp;
						}
					}
				} )
				.catch( function () {
					// Network/API problem: fall back to a normal form POST so the lead is never lost.
					form.classList.remove( 'is-sending' );
					form.removeAttribute( 'data-dgf-lead' );
					HTMLFormElement.prototype.submit.call( form );
				} );
		} );
	} );

	/* Gallery lightbox. */
	var box = doc.querySelector( '[data-dgf-lightbox-root]' );
	if ( box ) {
		var img = box.querySelector( 'img' );
		var close = function () {
			box.hidden = true;
			img.removeAttribute( 'src' );
		};
		doc.addEventListener( 'click', function ( e ) {
			var item = e.target.closest && e.target.closest( '[data-dgf-lightbox]' );
			if ( item ) {
				e.preventDefault();
				var thumb = item.querySelector( 'img' );
				img.src = item.href;
				img.alt = thumb ? thumb.alt : '';
				box.hidden = false;
				box.querySelector( 'button' ).focus();
			} else if ( e.target === box || ( e.target.closest && e.target.closest( '.dgf-lightbox__close' ) ) ) {
				close();
			}
		} );
		doc.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! box.hidden ) {
				close();
			}
		} );
	}

	/* Scroll reveal. */
	if ( 'IntersectionObserver' in window && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		var targets = doc.querySelectorAll( '.dgf-content .dgf-section > *, .dgf-card, .dgf-catalogue, .dgf-gallery__item, .dgf-cta__inner' );
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				// Reveal on entry, and never leave anything hidden above the viewport (fast scroll, anchor jumps).
				if ( entry.isIntersecting || entry.boundingClientRect.top < 0 ) {
					entry.target.classList.add( 'is-in' );
					io.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 } );
		targets.forEach( function ( el ) {
			el.classList.add( 'dgf-reveal' );
			io.observe( el );
		} );
		window.addEventListener( 'beforeprint', function () {
			targets.forEach( function ( el ) {
				el.classList.add( 'is-in' );
			} );
		} );
	}
}() );
