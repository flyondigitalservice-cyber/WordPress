<?php
/**
 * Customer live-tracking page. Variables: $d (delivery row or null), $config.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;
$ver   = OLE_DELIVERY_VERSION;
$store = OLE_Settings::get( 'store_name' );
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0b0b0b">
	<meta name="robots" content="noindex, nofollow">
	<title>Track your order · <?php echo esc_html( $store ); ?></title>
	<link rel="icon" href="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/img/icon.svg' ); ?>" type="image/svg+xml">
	<link rel="stylesheet" href="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/css/track.css?ver=' . $ver ); ?>">
</head>
<body class="ole-track">
	<header class="t-top">
		<a class="t-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( strtoupper( $store ) ); ?></a>
		<span class="t-live" id="t-live"><span></span>LIVE</span>
	</header>

<?php if ( ! $d ) : ?>
	<main class="t-panel t-panel--solo">
		<h1>Tracking link not found</h1>
		<p class="t-muted">Please use the link from your order email or WhatsApp message.</p>
		<a class="t-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to shop</a>
	</main>
<?php else : ?>
	<div class="t-map" id="t-map" aria-label="Live map">
		<div class="t-map__placeholder" id="t-map-ph">
			<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 4C21 4 12 13 12 24c0 15 20 36 20 36s20-21 20-36C52 13 43 4 32 4zm0 28a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" fill="currentColor"/></svg>
		</div>
	</div>

	<main class="t-panel">
		<div class="t-grip" aria-hidden="true"></div>
		<p class="t-order">Order <b id="t-order">#…</b></p>
		<h1 class="t-status" id="t-status">Loading…</h1>
		<p class="t-eta" id="t-eta"></p>

		<div class="t-otp" id="t-otp" hidden>
			<span>Share this OTP with the rider</span>
			<b id="t-otp-code">----</b>
		</div>

		<div class="t-cod" id="t-cod" hidden></div>

		<ol class="t-steps" id="t-steps">
			<li data-step="placed"><span class="t-dot"></span><div><b>Order confirmed</b><small></small></div></li>
			<li data-step="assigned"><span class="t-dot"></span><div><b>Rider assigned</b><small></small></div></li>
			<li data-step="picked"><span class="t-dot"></span><div><b>Out for delivery</b><small></small></div></li>
			<li data-step="delivered"><span class="t-dot"></span><div><b>Delivered</b><small></small></div></li>
		</ol>

		<div class="t-rider" id="t-rider" hidden>
			<div class="t-rider__avatar" id="t-rider-initial" aria-hidden="true">R</div>
			<div>
				<b id="t-rider-name">Your rider</b>
				<small>Delivery partner</small>
			</div>
			<a class="t-call" id="t-rider-call" href="#" aria-label="Call rider">
				<svg viewBox="0 0 24 24"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
			</a>
		</div>

		<p class="t-help">Need help? <a id="t-store-call" href="#">Call <?php echo esc_html( $store ); ?></a></p>
	</main>

	<script>window.OLE_TRACK = <?php echo wp_json_encode( $config ); ?>;</script>
	<script src="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/js/maps-loader.js?ver=' . $ver ); ?>" defer></script>
	<script src="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/js/track.js?ver=' . $ver ); ?>" defer></script>
<?php endif; ?>
</body>
</html>
