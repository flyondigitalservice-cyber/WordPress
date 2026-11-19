<?php
/**
 * Plugin Name:       Bombay Marketing Agent Ready
 * Description:       Makes bombaymarketing.in agent-ready: Content-Signal in robots.txt, Offer schema for prices, /llms.txt, Markdown content negotiation, WebMCP tools and an MCP server (with Server Card at /.well-known/mcp/server-card.json) and a lightweight contact form ([bm_contact_form]) that needs no form or SMTP plugin.
 * Version:           1.1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Flyon Digital Service
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bombay-agent-ready
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

define( 'BMAR_VERSION', '1.1.0' );
define( 'BMAR_DIR', plugin_dir_path( __FILE__ ) );

require_once BMAR_DIR . 'includes/class-bmar-config.php';
require_once BMAR_DIR . 'includes/class-bmar-markdown.php';
require_once BMAR_DIR . 'includes/class-bmar-tools.php';
require_once BMAR_DIR . 'includes/class-bmar-plugin.php';
require_once BMAR_DIR . 'includes/class-bmar-htaccess.php';
require_once BMAR_DIR . 'includes/class-bmar-contact.php';

BMAR_Plugin::init();
BMAR_Htaccess::init( __FILE__ );
BMAR_Contact::init();
