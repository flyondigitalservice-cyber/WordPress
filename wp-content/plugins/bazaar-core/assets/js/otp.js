/**
 * OTP login form behaviour. Config is provided as window.bzrOtp (see inc/otp.php).
 */
( function () {
	'use strict';

	var cfg = window.bzrOtp;
	var root = document.querySelector( '[data-bzr-otp]' );
	if ( ! cfg || ! root ) {
		return;
	}

	var emailForm = root.querySelector( '[data-step="email"]' );
	var codeForm = root.querySelector( '[data-step="code"]' );
	var status = root.querySelector( '[data-bzr-otp-status]' );
	var sentNote = root.querySelector( '[data-bzr-otp-sent]' );
	var email = '';

	function say( text, isError ) {
		status.textContent = text || '';
		status.classList.toggle( 'is-error', !! isError );
	}

	function post( url, body ) {
		return fetch( url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			credentials: 'same-origin',
			body: JSON.stringify( body ),
		} ).then( function ( res ) {
			return res.json().then( function ( data ) {
				if ( ! res.ok ) {
					throw new Error( data && data.message ? data.message : cfg.i18n.error );
				}
				return data;
			} );
		} );
	}

	function busy( form, on ) {
		form.querySelectorAll( 'button, input' ).forEach( function ( el ) {
			el.disabled = on;
		} );
	}

	emailForm.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		email = emailForm.elements.email.value.trim();
		if ( ! emailForm.elements.email.checkValidity() || ! email ) {
			say( cfg.i18n.error, true );
			return;
		}
		say( cfg.i18n.sending );
		busy( emailForm, true );
		post( cfg.request, { email: email, website: emailForm.elements.website.value } )
			.then( function () {
				emailForm.hidden = true;
				codeForm.hidden = false;
				sentNote.textContent = cfg.i18n.sent + ' ' + email;
				say( '' );
				codeForm.elements.code.focus();
			} )
			.catch( function ( err ) {
				say( err.message, true );
			} )
			.then( function () {
				busy( emailForm, false );
			} );
	} );

	codeForm.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		say( cfg.i18n.verifying );
		busy( codeForm, true );
		post( cfg.verify, { email: email, code: codeForm.elements.code.value } )
			.then( function ( data ) {
				window.location.assign( data.redirect );
			} )
			.catch( function ( err ) {
				say( err.message, true );
				busy( codeForm, false );
				codeForm.elements.code.select();
			} );
	} );

	root.querySelector( '[data-bzr-otp-back]' ).addEventListener( 'click', function () {
		codeForm.hidden = true;
		emailForm.hidden = false;
		codeForm.elements.code.value = '';
		say( '' );
		emailForm.elements.email.focus();
	} );
}() );
