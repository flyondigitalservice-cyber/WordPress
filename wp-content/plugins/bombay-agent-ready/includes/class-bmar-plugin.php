<?php
/**
 * Hooks: robots.txt, llms.txt, Markdown negotiation, Offer schema, WebMCP, MCP server and Server Card.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Plugin {

	const REST_NS          = 'bombay-agent/v1';
	const MCP_PROTOCOL     = '2025-06-18';
	const SERVER_CARD_PATH = '.well-known/mcp/server-card.json';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_filter( 'robots_txt', array( __CLASS__, 'robots_txt' ), 99, 2 );
		add_action( 'parse_request', array( __CLASS__, 'serve_virtual_files' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_serve_markdown' ), 0 );
		add_action( 'send_headers', array( __CLASS__, 'send_headers' ) );
		add_action( 'wp_head', array( __CLASS__, 'head_links' ), 2 );
		add_action( 'wp_head', array( __CLASS__, 'offer_schema' ), 20 );
		add_action( 'wp_footer', array( __CLASS__, 'webmcp_script' ), 20 );
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest' ) );
	}

	/* ---------------------------------------------------------------------
	 * 1. Content-Signal in robots.txt
	 * ------------------------------------------------------------------- */

	/**
	 * Add a Content-Signal directive to the virtual robots.txt.
	 *
	 * @param string $output Robots.txt body.
	 * @param bool   $public Whether the site is public.
	 * @return string
	 */
	public static function robots_txt( $output, $public ) {
		if ( ! $public || false !== stripos( $output, 'Content-Signal:' ) ) {
			return $output;
		}

		$signal = BMAR_Config::get()['content_signal'];
		$block  = "# Content signals (https://contentsignals.org/): search = search indexing, ai-input = use in AI answers (RAG/grounding), ai-train = training or fine-tuning AI models.\n"
			. 'Content-Signal: ' . $signal . "\n";

		// Place it inside the first "User-agent: *" group so it applies to all crawlers.
		if ( preg_match( '/^User-agent:\s*\*\s*$/mi', $output, $m, PREG_OFFSET_CAPTURE ) ) {
			$pos = $m[0][1] + strlen( $m[0][0] );
			return substr( $output, 0, $pos ) . "\n" . $block . ltrim( substr( $output, $pos ), "\r\n" );
		}

		return "User-agent: *\n" . $block . "\n" . $output;
	}

	/* ---------------------------------------------------------------------
	 * 3 & 6. /llms.txt and /.well-known/mcp/server-card.json
	 * ------------------------------------------------------------------- */

	/**
	 * Request path relative to the site root, without slashes at either end.
	 *
	 * @return string
	 */
	private static function request_path() {
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
		$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( '' !== $base && '/' !== $base && 0 === strpos( $path, $base ) ) {
			$path = substr( $path, strlen( $base ) );
		}
		return trim( $path, '/' );
	}

	/**
	 * Serve llms.txt and the MCP Server Card before WordPress routes the request.
	 */
	public static function serve_virtual_files() {
		$path = self::request_path();

		if ( 'llms.txt' === $path ) {
			self::send_text( self::llms_txt(), 'text/plain; charset=utf-8' );
		}

		if ( self::SERVER_CARD_PATH === $path || '.well-known/mcp.json' === $path ) {
			header( 'Access-Control-Allow-Origin: *' );
			self::send_text( wp_json_encode( self::server_card(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), 'application/json; charset=utf-8' );
		}
	}

	/**
	 * Send a body and stop.
	 *
	 * @param string $body         Body.
	 * @param string $content_type Content type.
	 */
	private static function send_text( $body, $content_type ) {
		status_header( 200 );
		header( 'Content-Type: ' . $content_type );
		header( 'X-Robots-Tag: noindex' );
		header( 'Cache-Control: public, max-age=3600' );
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'HEAD' === $_SERVER['REQUEST_METHOD'] ) {
			exit;
		}
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Build llms.txt (https://llmstxt.org/).
	 *
	 * @return string
	 */
	public static function llms_txt() {
		$c   = BMAR_Config::get();
		$out = '# ' . $c['name'] . "\n\n> " . $c['summary'] . "\n\n";

		$out .= "Every page is available as Markdown: request it with the header `Accept: text/markdown`.\n";
		$out .= 'An MCP server is available at ' . self::mcp_endpoint() . ' (Server Card: ' . home_url( '/' . self::SERVER_CARD_PATH ) . ").\n\n";

		$out .= "## Key pages\n\n";
		foreach ( $c['key_pages'] as $p ) {
			$out .= '- [' . $p[0] . '](' . $p[1] . '): ' . $p[2] . "\n";
		}

		$out .= "\n## Pricing\n\n";
		foreach ( $c['offers'] as $offer ) {
			$out .= '- [' . $offer['name'] . '](' . $offer['url'] . '): ' . BMAR_Config::price_label( $offer ) . '. ' . $offer['description'] . "\n";
		}
		$out .= '- Services are quoted per project: [Contact](' . $c['contact']['contact_url'] . ")\n";

		$out .= "\n## Services\n\n";
		foreach ( $c['services'] as $s ) {
			$out .= '- [' . $s[0] . '](' . $s[1] . ")\n";
		}

		$out .= "\n## Contact\n\n";
		$out .= '- Phone: ' . $c['contact']['telephone'] . "\n";
		$out .= '- Office: ' . $c['contact']['address'] . "\n";
		$out .= '- Hours: ' . $c['contact']['opening_hours'] . "\n";

		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 15,
				'has_password'   => false,
			)
		);
		if ( $posts ) {
			$out .= "\n## Optional\n\n";
			foreach ( $posts as $post ) {
				$out .= '- [' . html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ) . '](' . get_permalink( $post ) . ")\n";
			}
		}

		/**
		 * Filter the llms.txt body.
		 *
		 * @param string $out Body.
		 */
		return apply_filters( 'bmar_llms_txt', $out );
	}

	/* ---------------------------------------------------------------------
	 * 4. Markdown for Accept: text/markdown
	 * ------------------------------------------------------------------- */

	/**
	 * Whether the client prefers Markdown over HTML.
	 *
	 * @return bool
	 */
	public static function wants_markdown() {
		if ( isset( $_GET['format'] ) && in_array( $_GET['format'], array( 'md', 'markdown' ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( (string) $_SERVER['HTTP_ACCEPT'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( false === strpos( $accept, 'text/markdown' ) && false === strpos( $accept, 'text/x-markdown' ) ) {
			return false;
		}

		$md   = 0.0;
		$html = 0.0;
		foreach ( explode( ',', $accept ) as $range ) {
			$parts = array_map( 'trim', explode( ';', $range ) );
			$type  = array_shift( $parts );
			$q     = 1.0;
			foreach ( $parts as $param ) {
				if ( 0 === strpos( $param, 'q=' ) ) {
					$q = (float) substr( $param, 2 );
				}
			}
			if ( 'text/markdown' === $type || 'text/x-markdown' === $type ) {
				$md = max( $md, $q );
			} elseif ( 'text/html' === $type || 'application/xhtml+xml' === $type ) {
				$html = max( $html, $q );
			}
		}

		return $md > 0 && $md >= $html;
	}

	/**
	 * Vary on Accept so caches keep HTML and Markdown apart.
	 */
	public static function send_headers() {
		header( 'Vary: Accept', false );
	}

	/**
	 * Serve the current page as Markdown when asked.
	 */
	public static function maybe_serve_markdown() {
		if ( ! self::wants_markdown() || is_feed() || is_robots() || is_admin() ) {
			return;
		}

		$queried = get_queried_object();

		if ( is_singular() && $queried instanceof WP_Post ) {
			if ( post_password_required( $queried ) ) {
				return;
			}
			$md = self::post_markdown( $queried );
		} elseif ( is_home() || is_archive() || is_search() ) {
			$md = self::listing_markdown();
		} else {
			return;
		}

		status_header( is_404() ? 404 : 200 );
		header( 'Content-Type: text/markdown; charset=utf-8' );
		header( 'Vary: Accept' );
		header( 'X-Markdown-Tokens: ' . (int) ceil( strlen( $md ) / 4 ) );
		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'HEAD' === $_SERVER['REQUEST_METHOD'] ) {
			exit;
		}
		echo $md; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Render a post's content (including page-builder output) to Markdown.
	 *
	 * @param WP_Post $post Post.
	 * @return string
	 */
	public static function post_markdown( $post ) {
		$html = '';

		// Elementor (and UiCore, which builds on it) keeps the real layout outside post_content.
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->frontend ) ) {
			$doc = isset( \Elementor\Plugin::$instance->documents ) ? \Elementor\Plugin::$instance->documents->get( $post->ID ) : null;
			if ( $doc && method_exists( $doc, 'is_built_with_elementor' ) && $doc->is_built_with_elementor() ) {
				$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post->ID, true );
			}
		}

		if ( '' === trim( (string) $html ) ) {
			$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $post );
			$html = apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			wp_reset_postdata();
		}

		$title = html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' );
		$desc  = html_entity_decode( wp_strip_all_tags( get_the_excerpt( $post ) ), ENT_QUOTES, 'UTF-8' );
		$md    = "---\n"
			. 'title: ' . wp_json_encode( $title, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n"
			. 'url: ' . get_permalink( $post ) . "\n"
			. ( $desc ? 'description: ' . wp_json_encode( $desc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" : '' )
			. 'updated: ' . get_post_modified_time( 'c', true, $post ) . "\n"
			. "---\n\n";

		$body = BMAR_Markdown::from_html( '<html><body><main>' . $html . '</main></body></html>' );
		if ( ! preg_match( '/^# /m', $body ) ) {
			$md .= '# ' . $title . "\n\n";
		}
		$md .= $body;

		// The homepage and pricing page always carry machine-readable prices.
		$is_front   = (int) get_option( 'page_on_front' ) === (int) $post->ID;
		$is_pricing = false;
		foreach ( BMAR_Config::get()['offers'] as $offer ) {
			if ( untrailingslashit( $offer['url'] ) === untrailingslashit( get_permalink( $post ) ) ) {
				$is_pricing = true;
			}
		}
		if ( $is_front || $is_pricing ) {
			$md .= "\n" . BMAR_Tools::pricing_markdown();
		}
		if ( $is_front ) {
			$md .= "\n" . BMAR_Tools::contact_markdown();
		}

		/**
		 * Filter the Markdown for a post.
		 *
		 * @param string  $md   Markdown.
		 * @param WP_Post $post Post.
		 */
		return apply_filters( 'bmar_post_markdown', $md, $post );
	}

	/**
	 * Markdown for blog index, archives and search.
	 *
	 * @return string
	 */
	private static function listing_markdown() {
		global $wp_query;

		$title = wp_strip_all_tags( wp_get_document_title() );
		$md    = '# ' . html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) . "\n\n";
		foreach ( $wp_query->posts as $post ) {
			$excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 40 );
			$md     .= '- [' . html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ) . '](' . get_permalink( $post ) . ')' . ( $excerpt ? ': ' . html_entity_decode( $excerpt, ENT_QUOTES, 'UTF-8' ) : '' ) . "\n";
		}
		$next = get_next_posts_page_link();
		if ( $next && $wp_query->max_num_pages > max( 1, get_query_var( 'paged' ) ) ) {
			$md .= "\n[Next page](" . $next . ")\n";
		}
		return $md;
	}

	/**
	 * Discovery links in <head>.
	 */
	public static function head_links() {
		echo '<link rel="alternate" type="text/plain" title="llms.txt" href="' . esc_url( home_url( '/llms.txt' ) ) . '" />' . "\n";
		echo '<link rel="mcp-server-card" type="application/json" href="' . esc_url( home_url( '/' . self::SERVER_CARD_PATH ) ) . '" />' . "\n";

		if ( is_singular() || is_front_page() || is_home() ) {
			global $wp;
			$url = home_url( add_query_arg( array( 'format' => 'markdown' ), trailingslashit( $wp->request ) ) );
			echo '<link rel="alternate" type="text/markdown" href="' . esc_url( $url ) . '" />' . "\n";
		}

		foreach ( BMAR_Config::get()['offers'] as $offer ) {
			echo '<link rel="pricing" href="' . esc_url( $offer['url'] ) . '" />' . "\n";
			break;
		}
	}

	/* ---------------------------------------------------------------------
	 * 2. Offer schema with price and currency
	 * ------------------------------------------------------------------- */

	/**
	 * Output Product + Offer JSON-LD on the homepage and on each product's page.
	 */
	public static function offer_schema() {
		$c       = BMAR_Config::get();
		$current = untrailingslashit( home_url( '/' . $GLOBALS['wp']->request ) );
		$items   = array();

		foreach ( $c['offers'] as $offer ) {
			if ( ! is_front_page() && untrailingslashit( $offer['url'] ) !== $current ) {
				continue;
			}

			$offers = array();
			foreach ( $offer['prices'] as $p ) {
				$offers[] = array(
					'@type'         => 'Offer',
					'price'         => $p['price'],
					'priceCurrency' => $p['currency'],
					'availability'  => 'https://schema.org/InStock',
					'url'           => $offer['url'],
					'seller'        => array( '@id' => home_url( '/#organization' ) ),
				);
			}

			$items[] = array(
				'@type'       => 'Product',
				'@id'         => trailingslashit( $offer['url'] ) . '#product',
				'name'        => $offer['name'],
				'description' => $offer['description'],
				'url'         => $offer['url'],
				'brand'       => array(
					'@type' => 'Brand',
					'name'  => $c['name'],
				),
				'offers'      => $offers,
			);
		}

		if ( ! $items ) {
			return;
		}

		$data = array(
			'@context' => 'https://schema.org',
			'@graph'   => $items,
		);

		echo '<script type="application/ld+json" id="bmar-offers">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}

	/* ---------------------------------------------------------------------
	 * 5. WebMCP tools for in-browser agents
	 * ------------------------------------------------------------------- */

	/**
	 * Register the shared tools with navigator.modelContext on page load.
	 */
	public static function webmcp_script() {
		$data = array(
			'tools'    => BMAR_Tools::definitions(),
			'endpoint' => rest_url( self::REST_NS . '/tools/' ),
			'pricing'  => BMAR_Tools::pricing_markdown(),
			'contact'  => BMAR_Tools::contact_markdown(),
			'home'     => home_url( '/' ),
		);
		?>
<script id="bmar-webmcp">
(function () {
	var cfg = <?php echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>;

	function text(t, isError) {
		return { content: [ { type: 'text', text: String(t) } ], isError: !!isError };
	}

	function callServer(name, args) {
		return fetch(cfg.endpoint + encodeURIComponent(name), {
			method: 'POST',
			credentials: 'omit',
			headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
			body: JSON.stringify(args || {})
		}).then(function (r) { return r.json(); })
		  .then(function (j) { return text(j.text, j.error); })
		  .catch(function (e) { return text('Request failed: ' + e.message, true); });
	}

	var local = {
		get_pricing: function () { return Promise.resolve(text(cfg.pricing)); },
		get_contact_info: function () { return Promise.resolve(text(cfg.contact)); }
	};

	function register() {
		var mc = navigator.modelContext;
		if (!mc || typeof mc.registerTool !== 'function' || window.__bmarWebMcp) { return; }
		window.__bmarWebMcp = true;
		cfg.tools.forEach(function (t) {
			try {
				mc.registerTool({
					name: t.name,
					title: t.title,
					description: t.description,
					inputSchema: t.inputSchema,
					annotations: t.annotations,
					execute: function (args) {
						return local[t.name] ? local[t.name](args) : callServer(t.name, args);
					}
				});
			} catch (e) {
				if (window.console) { console.warn('WebMCP registerTool failed for ' + t.name, e); }
			}
		});
	}

	register();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', register);
	}
	window.addEventListener('load', register);
})();
</script>
		<?php
	}

	/* ---------------------------------------------------------------------
	 * REST: MCP server (Streamable HTTP, JSON responses) + WebMCP tool proxy
	 * ------------------------------------------------------------------- */

	/**
	 * MCP endpoint URL.
	 *
	 * @return string
	 */
	public static function mcp_endpoint() {
		return rest_url( self::REST_NS . '/mcp' );
	}

	/**
	 * Register REST routes.
	 */
	public static function register_rest() {
		register_rest_route(
			self::REST_NS,
			'/mcp',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'mcp_handle' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => 'GET',
					'callback'            => function () {
						return new WP_REST_Response( array( 'error' => 'This MCP server does not offer an SSE stream. Send JSON-RPC requests with POST.' ), 405, array( 'Allow' => 'POST' ) );
					},
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			self::REST_NS,
			'/tools/(?P<name>[a-z_]+)',
			array(
				'methods'             => 'POST',
				'callback'            => function ( WP_REST_Request $request ) {
					$args = $request->get_json_params();
					return rest_ensure_response( BMAR_Tools::call( $request['name'], is_array( $args ) ? $args : array() ) );
				},
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handle an MCP JSON-RPC message (or batch).
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function mcp_handle( WP_REST_Request $request ) {
		$body = json_decode( $request->get_body(), true );

		if ( ! is_array( $body ) ) {
			return new WP_REST_Response( self::rpc_error( null, -32700, 'Parse error' ), 400 );
		}

		$is_batch = isset( $body[0] );
		$messages = $is_batch ? $body : array( $body );
		$replies  = array();

		foreach ( $messages as $msg ) {
			$reply = self::mcp_message( is_array( $msg ) ? $msg : array() );
			if ( null !== $reply ) {
				$replies[] = $reply;
			}
		}

		if ( ! $replies ) {
			return new WP_REST_Response( null, 202 );
		}

		$response = new WP_REST_Response( $is_batch ? $replies : $replies[0], 200 );
		$response->header( 'MCP-Protocol-Version', self::MCP_PROTOCOL );
		return $response;
	}

	/**
	 * Handle one JSON-RPC message. Returns null for notifications.
	 *
	 * @param array $msg Message.
	 * @return array|null
	 */
	private static function mcp_message( $msg ) {
		$id     = array_key_exists( 'id', $msg ) ? $msg['id'] : null;
		$method = isset( $msg['method'] ) ? (string) $msg['method'] : '';
		$params = isset( $msg['params'] ) && is_array( $msg['params'] ) ? $msg['params'] : array();

		if ( null === $id ) {
			return null; // Notification (e.g. notifications/initialized).
		}

		switch ( $method ) {
			case 'initialize':
				$requested = isset( $params['protocolVersion'] ) ? (string) $params['protocolVersion'] : self::MCP_PROTOCOL;
				$supported = array( '2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05' );
				return self::rpc_result(
					$id,
					array(
						'protocolVersion' => in_array( $requested, $supported, true ) ? $requested : self::MCP_PROTOCOL,
						'capabilities'    => self::capabilities(),
						'serverInfo'      => self::server_info(),
						'instructions'    => 'Read-only tools for Bombay Marketing (bombaymarketing.in): search the site, read pages as Markdown, get prices and contact details.',
					)
				);

			case 'ping':
				return self::rpc_result( $id, new stdClass() );

			case 'tools/list':
				return self::rpc_result( $id, array( 'tools' => BMAR_Tools::definitions() ) );

			case 'tools/call':
				$name   = isset( $params['name'] ) ? (string) $params['name'] : '';
				$known  = wp_list_pluck( BMAR_Tools::definitions(), 'name' );
				if ( ! in_array( $name, $known, true ) ) {
					return self::rpc_error( $id, -32602, 'Unknown tool: ' . $name );
				}
				$result = BMAR_Tools::call( $name, isset( $params['arguments'] ) ? $params['arguments'] : array() );
				return self::rpc_result(
					$id,
					array(
						'content' => array(
							array(
								'type' => 'text',
								'text' => $result['text'],
							),
						),
						'isError' => $result['error'],
					)
				);
		}

		return self::rpc_error( $id, -32601, 'Method not found: ' . $method );
	}

	/**
	 * JSON-RPC result.
	 *
	 * @param mixed $id     Id.
	 * @param mixed $result Result.
	 * @return array
	 */
	private static function rpc_result( $id, $result ) {
		return array(
			'jsonrpc' => '2.0',
			'id'      => $id,
			'result'  => $result,
		);
	}

	/**
	 * JSON-RPC error.
	 *
	 * @param mixed  $id      Id.
	 * @param int    $code    Code.
	 * @param string $message Message.
	 * @return array
	 */
	private static function rpc_error( $id, $code, $message ) {
		return array(
			'jsonrpc' => '2.0',
			'id'      => $id,
			'error'   => array(
				'code'    => $code,
				'message' => $message,
			),
		);
	}

	/**
	 * Server identity.
	 *
	 * @return array
	 */
	private static function server_info() {
		return array(
			'name'    => 'in.bombaymarketing/site',
			'title'   => 'Bombay Marketing',
			'version' => BMAR_VERSION,
		);
	}

	/**
	 * Server capabilities.
	 *
	 * @return array
	 */
	private static function capabilities() {
		return array(
			'tools' => array( 'listChanged' => false ),
		);
	}

	/**
	 * MCP Server Card (SEP-1649).
	 *
	 * @return array
	 */
	public static function server_card() {
		$info = self::server_info();
		$c    = BMAR_Config::get();

		$card = array(
			'$schema'          => 'https://static.modelcontextprotocol.io/schemas/mcp-server-card/v1.json',
			'version'          => '1.0',
			'protocolVersion'  => self::MCP_PROTOCOL,
			'name'             => $info['name'],
			'title'            => $info['title'],
			'description'      => $c['summary'],
			'serverInfo'       => $info,
			'websiteUrl'       => home_url( '/' ),
			'documentationUrl' => home_url( '/llms.txt' ),
			'iconUrl'          => get_site_icon_url( 512 ) ? get_site_icon_url( 512 ) : null,
			'endpoint'         => self::mcp_endpoint(),
			'transport'        => array(
				'type'     => 'streamable-http',
				'endpoint' => self::mcp_endpoint(),
			),
			'remotes'          => array(
				array(
					'type' => 'streamable-http',
					'url'  => self::mcp_endpoint(),
				),
			),
			'capabilities'     => self::capabilities(),
			'authentication'   => array(
				'required' => false,
				'schemes'  => array(),
			),
			'tools'            => array_map(
				function ( $t ) {
					return array(
						'name'        => $t['name'],
						'title'       => $t['title'],
						'description' => $t['description'],
					);
				},
				BMAR_Tools::definitions()
			),
		);

		return array_filter(
			$card,
			function ( $v ) {
				return null !== $v;
			}
		);
	}
}
