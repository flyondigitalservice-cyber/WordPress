=== Bombay Marketing Agent Ready ===
Requires at least: 6.2
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later

Makes bombaymarketing.in readable and usable by AI agents.

== What it adds ==

1. robots.txt: `Content-Signal: search=yes, ai-input=yes, ai-train=no` inside the `User-agent: *` group.
2. Prices: Product + Offer JSON-LD (INR 5000 / USD 50 plugin bundle) on the homepage and /plugins/, plus `<link rel="pricing">`.
3. /llms.txt: site summary, key pages, pricing, services, contact and recent posts.
4. Markdown: any page requested with `Accept: text/markdown` (or `?format=markdown`) returns `text/markdown`. Responses send `Vary: Accept`.
5. WebMCP: registers search_site, get_pricing, get_page and get_contact_info with `navigator.modelContext.registerTool()` on page load.
6. MCP: a read-only MCP server (Streamable HTTP, JSON responses) at /wp-json/bombay-agent/v1/mcp, described by the Server Card at /.well-known/mcp/server-card.json.

All site facts (summary, prices, services, contact, key pages) live in includes/class-bmar-config.php and can be overridden with the `bmar_config` filter.

== Deployment notes ==

* Activate the plugin, then purge any page cache (LiteSpeed, WP Rocket, Cloudflare, etc.).
* Page caches must vary on the Accept header, or skip caching when Accept contains text/markdown; otherwise cached HTML is served to agents.
* /llms.txt and /.well-known/mcp/server-card.json are served by WordPress. If the web server answers .txt/.json or /.well-known/ itself (common with nginx), route those paths to index.php. A physical robots.txt file in the web root overrides the Content-Signal line; delete it or add the line by hand.

== LiteSpeed Cache (bombaymarketing.in) ==

Markdown responses send `X-LiteSpeed-Cache-Control: no-cache`, so they are never cached. A cached HTML copy can still be served to a Markdown request, though. To bypass the cache for those requests, add this above the LSCACHE block in .htaccess:

    <IfModule LiteSpeed>
    RewriteEngine On
    RewriteCond %{HTTP_ACCEPT} text/markdown [NC]
    RewriteRule .* - [E=Cache-Control:no-cache]
    </IfModule>
