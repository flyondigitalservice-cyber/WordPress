( function () {
	var header = document.querySelector( '.site-header' );
	var toggle = header && header.querySelector( '.nav-toggle' );
	if ( ! toggle ) {
		return;
	}
	toggle.addEventListener( 'click', function () {
		var open = header.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	} );
} )();
