<?php
/**
 * Tool definitions shared by the MCP server and the in-browser WebMCP tools.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Tools {

	/**
	 * Tool definitions (MCP shape: name, title, description, inputSchema, annotations).
	 *
	 * @return array
	 */
	public static function definitions() {
		$read_only = array(
			'readOnlyHint'   => true,
			'openWorldHint'  => false,
			'idempotentHint' => true,
		);

		return array(
			array(
				'name'        => 'search_site',
				'title'       => 'Search Bombay Marketing',
				'description' => 'Search pages, services, guides and blog posts on bombaymarketing.in. Returns titles and URLs.',
				'inputSchema' => array(
					'type'                 => 'object',
					'properties'           => array(
						'query' => array(
							'type'        => 'string',
							'description' => 'Search terms, e.g. "Shopify store setup" or "Blinkit onboarding".',
						),
						'limit' => array(
							'type'        => 'integer',
							'description' => 'Maximum number of results (1-20).',
							'minimum'     => 1,
							'maximum'     => 20,
							'default'     => 10,
						),
					),
					'required'             => array( 'query' ),
					'additionalProperties' => false,
				),
				'annotations' => $read_only,
			),
			array(
				'name'        => 'get_pricing',
				'title'       => 'Get prices',
				'description' => 'List products with public prices (price and currency) and the services that are quoted per project.',
				'inputSchema' => array(
					'type'                 => 'object',
					'properties'           => new stdClass(),
					'additionalProperties' => false,
				),
				'annotations' => $read_only,
			),
			array(
				'name'        => 'get_page',
				'title'       => 'Read a page as Markdown',
				'description' => 'Return the content of a bombaymarketing.in page or post as Markdown. Accepts a full URL or a path such as "/services/".',
				'inputSchema' => array(
					'type'                 => 'object',
					'properties'           => array(
						'url' => array(
							'type'        => 'string',
							'description' => 'Page URL or path on bombaymarketing.in.',
						),
					),
					'required'             => array( 'url' ),
					'additionalProperties' => false,
				),
				'annotations' => $read_only,
			),
			array(
				'name'        => 'get_contact_info',
				'title'       => 'Get contact details',
				'description' => 'Return Bombay Marketing\'s phone number, office address, opening hours and contact page, for requesting a quote.',
				'inputSchema' => array(
					'type'                 => 'object',
					'properties'           => new stdClass(),
					'additionalProperties' => false,
				),
				'annotations' => $read_only,
			),
		);
	}

	/**
	 * Execute a tool server-side.
	 *
	 * @param string $name Tool name.
	 * @param array  $args Arguments.
	 * @return array{text:string,error:bool}
	 */
	public static function call( $name, $args ) {
		$args = is_array( $args ) ? $args : array();

		switch ( $name ) {
			case 'search_site':
				return self::search( isset( $args['query'] ) ? (string) $args['query'] : '', isset( $args['limit'] ) ? (int) $args['limit'] : 10 );
			case 'get_pricing':
				return self::ok( self::pricing_markdown() );
			case 'get_page':
				return self::page( isset( $args['url'] ) ? (string) $args['url'] : '' );
			case 'get_contact_info':
				return self::ok( self::contact_markdown() );
		}

		return array(
			'text'  => 'Unknown tool: ' . $name,
			'error' => true,
		);
	}

	/**
	 * Successful result.
	 *
	 * @param string $text Text.
	 * @return array
	 */
	private static function ok( $text ) {
		return array(
			'text'  => $text,
			'error' => false,
		);
	}

	/**
	 * Search posts and pages.
	 *
	 * @param string $query Query.
	 * @param int    $limit Limit.
	 * @return array
	 */
	private static function search( $query, $limit ) {
		$query = trim( $query );
		if ( '' === $query ) {
			return array(
				'text'  => 'The "query" argument is required.',
				'error' => true,
			);
		}

		$posts = get_posts(
			array(
				's'              => $query,
				'post_type'      => array( 'page', 'post' ),
				'post_status'    => 'publish',
				'posts_per_page' => max( 1, min( 20, $limit ) ),
				'has_password'   => false,
			)
		);

		if ( ! $posts ) {
			return self::ok( 'No results for "' . $query . '".' );
		}

		$lines = array();
		foreach ( $posts as $post ) {
			$excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 30 );
			$lines[] = '- [' . get_the_title( $post ) . '](' . get_permalink( $post ) . ')' . ( $excerpt ? ': ' . $excerpt : '' );
		}

		return self::ok( implode( "\n", $lines ) );
	}

	/**
	 * Read a page as Markdown.
	 *
	 * @param string $url URL or path.
	 * @return array
	 */
	private static function page( $url ) {
		$url = trim( $url );
		if ( '' === $url ) {
			return array(
				'text'  => 'The "url" argument is required.',
				'error' => true,
			);
		}
		if ( '/' === $url[0] ) {
			$url = home_url( $url );
		}

		$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( wp_parse_url( $url, PHP_URL_HOST ) !== $home_host ) {
			return array(
				'text'  => 'Only pages on ' . $home_host . ' can be read.',
				'error' => true,
			);
		}

		$path    = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
		$post_id = '' === $path ? (int) get_option( 'page_on_front' ) : url_to_postid( $url );
		$post    = $post_id ? get_post( $post_id ) : null;

		if ( ! $post || 'publish' !== $post->post_status || post_password_required( $post ) ) {
			return array(
				'text'  => 'No published page found at ' . $url,
				'error' => true,
			);
		}

		return self::ok( BMAR_Plugin::post_markdown( $post ) );
	}

	/**
	 * Pricing as Markdown.
	 *
	 * @return string
	 */
	public static function pricing_markdown() {
		$config = BMAR_Config::get();
		$out    = "## Prices\n\n";
		foreach ( $config['offers'] as $offer ) {
			$out .= '- [' . $offer['name'] . '](' . $offer['url'] . '): ' . BMAR_Config::price_label( $offer ) . '. ' . $offer['description'] . "\n";
		}
		$out .= "\n## Services (quoted per project)\n\n";
		foreach ( $config['services'] as $service ) {
			$out .= '- [' . $service[0] . '](' . $service[1] . ")\n";
		}
		$out .= "\nFor a service quote, see [Contact](" . $config['contact']['contact_url'] . ").\n";
		return $out;
	}

	/**
	 * Contact info as Markdown.
	 *
	 * @return string
	 */
	public static function contact_markdown() {
		$c = BMAR_Config::get()['contact'];
		return "## Contact\n\n"
			. '- Phone: ' . $c['telephone'] . "\n"
			. '- Office: ' . $c['address'] . "\n"
			. '- Hours: ' . $c['opening_hours'] . "\n"
			. '- Contact page: ' . $c['contact_url'] . "\n";
	}
}
