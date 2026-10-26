/**
 * Registers the YouPI Luxury UPI method with the WooCommerce Checkout block.
 * Plain ES5 against WordPress globals, so no build step is needed.
 */
( function ( wc, wp ) {
	if ( ! wc || ! wc.wcBlocksRegistry || ! wc.wcSettings || ! wp || ! wp.element ) {
		return;
	}

	var el = wp.element.createElement;
	var decode = wp.htmlEntities ? wp.htmlEntities.decodeEntities : function ( s ) { return s; };
	var settings = wc.wcSettings.getSetting( 'youpi_luxury_data', {} );
	var title = decode( settings.title || 'Private UPI Transfer' );

	function Content() {
		var paragraphs = decode( settings.description || '' ).split( /\n\s*\n/ );
		return el(
			'div',
			{ className: 'youpi-lux-method' },
			paragraphs.map( function ( text, i ) {
				return el( 'p', { key: i }, text );
			} ),
			el(
				'ul',
				{ className: 'youpi-lux-apps', 'aria-label': 'Supported UPI apps' },
				[ 'Google Pay', 'PhonePe', 'Paytm', 'CRED', 'BHIM' ].map( function ( app ) {
					return el( 'li', { key: app }, app );
				} )
			)
		);
	}

	function Label( props ) {
		var PaymentMethodLabel = props.components && props.components.PaymentMethodLabel;
		return PaymentMethodLabel ? el( PaymentMethodLabel, { text: title } ) : el( 'span', null, title );
	}

	wc.wcBlocksRegistry.registerPaymentMethod( {
		name: 'youpi_luxury',
		label: el( Label ),
		content: el( Content ),
		edit: el( Content ),
		canMakePayment: function () {
			return true;
		},
		ariaLabel: title,
		supports: {
			features: settings.supports || [ 'products' ],
		},
	} );
} )( window.wc, window.wp );
