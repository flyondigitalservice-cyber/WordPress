<?php
/**
 * Google Analytics 4, Google Tag Manager, Meta Pixel and site verification tags.
 *
 * Conversion events (fired from assets/js/main.js through window.tasdirTrack):
 *  - whatsapp_click → GA4 "whatsapp_click" + Meta "Contact"
 *  - generate_lead  → GA4 "generate_lead"  + Meta "Lead"
 *  - view_item      → GA4 "view_item"      + Meta "ViewContent" (product pages)
 *  - call_click / email_click → GA4 only
 * Every event is also pushed to dataLayer as "tasdir_<name>" for GTM users.
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Should tracking run for this request?
 *
 * @return bool
 */
function tasdir_should_track() {
	if ( is_customize_preview() || is_preview() ) {
		return false;
	}
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && ! tasdir_opt( 'track_admins' ) ) {
		return false;
	}
	return true;
}

/**
 * Verification meta tags (always output, even for admins).
 */
function tasdir_verification_tags() {
	$tags = array(
		'google-site-verification'  => tasdir_opt( 'google_verify' ),
		'msvalidate.01'             => tasdir_opt( 'bing_verify' ),
		'facebook-domain-verification' => tasdir_opt( 'fb_domain_verify' ),
	);
	foreach ( $tags as $name => $content ) {
		if ( $content ) {
			printf( '<meta name="%1$s" content="%2$s">' . "\n", esc_attr( $name ), esc_attr( $content ) );
		}
	}
	$extra = tasdir_opt( 'head_code' );
	if ( $extra ) {
		echo $extra . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- stored only by users with unfiltered_html.
	}
}
add_action( 'wp_head', 'tasdir_verification_tags', 1 );

/**
 * Tracking scripts in <head>.
 */
function tasdir_tracking_head() {
	$ga4   = tasdir_opt( 'ga4_id' );
	$gtm   = tasdir_opt( 'gtm_id' );
	$pixel = preg_replace( '/\D+/', '', (string) tasdir_opt( 'fb_pixel_id' ) );
	$track = tasdir_should_track();

	// Always define the helper so theme JS never errors.
	?>
<script>
window.dataLayer=window.dataLayer||[];
window.tasdirTrack=function(n,p){p=p||{};try{if(window.gtag){gtag('event',n,p);}window.dataLayer.push(Object.assign({event:'tasdir_'+n},p));var m={whatsapp_click:'Contact',generate_lead:'Lead',view_item:'ViewContent'};if(window.fbq&&m[n]){fbq('track',m[n],p);}}catch(e){}};
</script>
	<?php
	if ( ! $track ) {
		return;
	}

	if ( $gtm ) {
		?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm ); ?>');</script>
		<?php
	}

	if ( $ga4 ) {
		?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4 ); ?>"></script>
<script>function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga4 ); ?>');</script>
		<?php
	}

	if ( $pixel ) {
		?>
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?php echo esc_js( $pixel ); ?>');fbq('track','PageView');</script>
		<?php
	}

	if ( is_singular( 'tasdir_product' ) ) {
		printf(
			"<script>window.addEventListener('DOMContentLoaded',function(){tasdirTrack('view_item',{content_name:%s,content_type:'product',items:[{item_name:%s}]});});</script>\n",
			wp_json_encode( get_the_title(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT ),
			wp_json_encode( get_the_title(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT )
		);
	}
}
add_action( 'wp_head', 'tasdir_tracking_head', 3 );

/**
 * <noscript> fallbacks right after <body>.
 */
function tasdir_tracking_body() {
	if ( ! tasdir_should_track() ) {
		return;
	}
	$gtm   = tasdir_opt( 'gtm_id' );
	$pixel = preg_replace( '/\D+/', '', (string) tasdir_opt( 'fb_pixel_id' ) );
	if ( $gtm ) {
		printf( '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n", esc_attr( $gtm ) );
	}
	if ( $pixel ) {
		printf( '<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=%s&amp;ev=PageView&amp;noscript=1"></noscript>' . "\n", esc_attr( $pixel ) );
	}
}
add_action( 'wp_body_open', 'tasdir_tracking_body' );

/**
 * Warm up connections to tracking hosts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function tasdir_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' !== $relation_type || ! tasdir_should_track() ) {
		return $urls;
	}
	if ( tasdir_opt( 'ga4_id' ) || tasdir_opt( 'gtm_id' ) ) {
		$urls[] = 'https://www.googletagmanager.com';
	}
	if ( tasdir_opt( 'fb_pixel_id' ) ) {
		$urls[] = 'https://connect.facebook.net';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'tasdir_resource_hints', 10, 2 );
