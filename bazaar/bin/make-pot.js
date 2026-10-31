#!/usr/bin/env node
/**
 * Regenerate translation templates (.pot) for Bazaar Core and the Bazaar themes.
 *
 * Usage (from the repository root):
 *   npm install --no-save wp-pot   # once
 *   node bazaar/bin/make-pot.js
 */
'use strict';

const path = require( 'path' );
const { WP_Pot } = require( 'wp-pot' );
const fs = require( 'fs' );

const root = path.resolve( __dirname, '../..' );
const targets = [
	{ dir: 'wp-content/plugins/bazaar-core', domain: 'bazaar-core', name: 'Bazaar Core' },
	{ dir: 'wp-content/themes/bazaar-store', domain: 'bazaar-store', name: 'Bazaar Store' },
	{ dir: 'wp-content/themes/bazaar-electronics', domain: 'bazaar-electronics', name: 'Bazaar Electronics' },
	{ dir: 'wp-content/themes/bazaar-boutique', domain: 'bazaar-boutique', name: 'Bazaar Boutique' },
];

for ( const t of targets ) {
	const base = path.join( root, t.dir );
	fs.mkdirSync( path.join( base, 'languages' ), { recursive: true } );
	new WP_Pot( {
		pathsRelativeTo: base,
		php: { textdomain: t.domain, metadataFile: t.dir.includes( 'plugins' ) ? 'bazaar-core.php' : 'style.css' },
		pot: { package: t.name, team: 'Flyon Digital Service', lastTranslator: 'Flyon Digital Service' },
	} )
		.parse( [ base + '/**/*.php', base + '/style.css' ] )
		.writePot( path.join( base, 'languages', t.domain + '.pot' ) );
	console.log( 'Wrote ' + t.dir + '/languages/' + t.domain + '.pot' );
}
