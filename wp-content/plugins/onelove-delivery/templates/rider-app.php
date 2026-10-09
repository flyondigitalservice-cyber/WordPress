<?php
/**
 * Rider app shell. Variables: $logged_in, $allowed, $config.
 *
 * @package OneLoveDelivery
 */

defined( 'ABSPATH' ) || exit;
$ver = OLE_DELIVERY_VERSION;
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0b0b0b">
	<meta name="robots" content="noindex, nofollow">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<title><?php echo esc_html( OLE_Settings::get( 'store_name' ) ); ?> · Rider</title>
	<link rel="manifest" href="<?php echo esc_url( home_url( '/rider/manifest.webmanifest' ) ); ?>">
	<link rel="icon" href="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/img/icon.svg' ); ?>" type="image/svg+xml">
	<link rel="apple-touch-icon" href="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/img/icon.svg' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/css/rider.css?ver=' . $ver ); ?>">
</head>
<body class="ole-rider">

<?php if ( ! $logged_in ) : ?>
	<main class="r-login">
		<div class="r-logo"><?php include OLE_DELIVERY_DIR . 'assets/img/icon.svg'; ?></div>
		<h1>Rider login</h1>
		<p class="r-muted"><?php echo esc_html( OLE_Settings::get( 'store_name' ) ); ?> delivery partners</p>
		<?php
		wp_login_form( array(
			'redirect'       => home_url( '/rider/' ),
			'label_username' => 'Username or email',
			'label_log_in'   => 'Log in',
			'remember'       => true,
			'value_remember' => true,
		) );
		?>
		<p class="r-muted r-small"><a href="<?php echo esc_url( wp_lostpassword_url( home_url( '/rider/' ) ) ); ?>">Forgot password?</a></p>
	</main>

<?php elseif ( ! $allowed ) : ?>
	<main class="r-login">
		<h1>Not a rider account</h1>
		<p class="r-muted">This account is not set up as a Delivery Rider. Ask the store to change your role.</p>
		<p><a class="r-btn r-btn--ghost" href="<?php echo esc_url( $config['logout'] ); ?>">Log out</a></p>
	</main>

<?php else : ?>
	<header class="r-top">
		<div>
			<div class="r-hello">Hi, <span id="r-name">rider</span></div>
			<div class="r-sub" id="r-gps">GPS off</div>
		</div>
		<button type="button" class="r-duty" id="r-duty" aria-pressed="false">
			<span class="r-duty__dot"></span><span class="r-duty__txt">Off duty</span>
		</button>
	</header>

	<section class="r-stats">
		<div><b id="r-active-count">0</b><span>Active</span></div>
		<div class="r-stat--earn"><b id="r-earned">₹0</b><span id="r-trips">0 trips today</span></div>
		<div><b id="r-cash">₹0</b><span>Cash in hand</span></div>
	</section>

	<div class="r-banner" id="r-banner" hidden></div>

	<main class="r-main">
		<h2 class="r-h2">Active deliveries</h2>
		<div id="r-active"></div>

		<details class="r-done">
			<summary>Completed today</summary>
			<div id="r-done"></div>
		</details>
	</main>

	<footer class="r-foot">
		<a href="<?php echo esc_url( $config['logout'] ); ?>">Log out</a>
	</footer>

	<!-- Deliver sheet -->
	<div class="r-sheet" id="r-sheet" hidden>
		<form class="r-sheet__panel" id="r-sheet-form">
			<h3 id="r-sheet-title">Complete delivery</h3>
			<div id="r-sheet-body"></div>
			<div class="r-sheet__actions">
				<button type="button" class="r-btn r-btn--ghost" data-close-sheet>Cancel</button>
				<button type="submit" class="r-btn r-btn--green" id="r-sheet-submit">Confirm</button>
			</div>
		</form>
	</div>

	<div class="r-toast" id="r-toast" role="status" aria-live="polite"></div>

	<script>window.OLE_RIDER = <?php echo wp_json_encode( $config ); ?>;</script>
	<script src="<?php echo esc_url( OLE_DELIVERY_URL . 'assets/js/rider.js?ver=' . $ver ); ?>" defer></script>
<?php endif; ?>

</body>
</html>
