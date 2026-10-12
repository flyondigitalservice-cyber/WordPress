<?php
/**
 * Template Name: Bag & Checkout
 *
 * @package HadhramiFashion
 */

get_header();
?>
<header class="page-hero" style="margin-bottom:clamp(28px,4vw,48px)">
	<div class="wrap">
		<span class="eyebrow"><?php esc_html_e( 'Your Bag', 'hadhramifashion' ); ?></span>
		<h1><?php esc_html_e( 'Where shall we bring it?', 'hadhramifashion' ); ?></h1>
		<p class="lede"><?php esc_html_e( 'Drop the pin on your door. We will show you exactly how soon our rider can be there.', 'hadhramifashion' ); ?></p>
	</div>
</header>

<div class="wrap">
	<form class="checkout" data-checkout novalidate>
		<div>
			<section class="checkout__panel">
				<h3><span class="step">1</span><?php esc_html_e( 'Delivery address', 'hadhramifashion' ); ?></h3>
				<div class="field">
					<label for="co-address"><?php esc_html_e( 'Search your address', 'hadhramifashion' ); ?></label>
					<div class="address-search">
						<div data-autocomplete-slot>
							<input class="input" id="co-address" name="address" type="text" autocomplete="street-address" placeholder="<?php esc_attr_e( 'Building, street or area', 'hadhramifashion' ); ?>">
						</div>
						<button type="button" class="btn-icon" data-locate title="<?php esc_attr_e( 'Use my location', 'hadhramifashion' ); ?>">
							<?php echo hf_icon( 'locate' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="screen-reader-text"><?php esc_html_e( 'Use my location', 'hadhramifashion' ); ?></span>
						</button>
					</div>
				</div>
				<div class="map" data-map="checkout">
					<div class="map-placeholder" data-map-placeholder><span><?php esc_html_e( 'The map appears once the Google Maps key is added in Hadhrami → Atelier Settings.', 'hadhramifashion' ); ?></span></div>
				</div>
				<p class="distance-pill" data-distance><?php echo hf_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span><?php esc_html_e( 'Drag the pin to your exact door.', 'hadhramifashion' ); ?></span></p>
				<div class="field">
					<label for="co-details"><?php esc_html_e( 'Flat, floor, landmark', 'hadhramifashion' ); ?></label>
					<input id="co-details" name="addressDetails" type="text" placeholder="<?php esc_attr_e( 'e.g. Flat 3B, opposite the mosque gate', 'hadhramifashion' ); ?>">
				</div>
			</section>

			<section class="checkout__panel">
				<h3><span class="step">2</span><?php esc_html_e( 'When', 'hadhramifashion' ); ?></h3>
				<div class="tiers" data-tiers>
					<p class="muted"><?php esc_html_e( 'Choose your address to see delivery times.', 'hadhramifashion' ); ?></p>
				</div>
				<div data-measure-mode hidden style="margin-top:1.2rem">
					<div class="opt__title"><span><?php esc_html_e( 'Measurements', 'hadhramifashion' ); ?></span></div>
					<div class="choices">
						<div class="choice">
							<input type="radio" name="measureMode" id="mm-visit" value="visit" checked>
							<label for="mm-visit"><strong><?php esc_html_e( 'Send a tailor to me', 'hadhramifashion' ); ?></strong><span><?php esc_html_e( 'Measured at your door; the 24 hours start then.', 'hadhramifashion' ); ?></span></label>
						</div>
						<div class="choice">
							<input type="radio" name="measureMode" id="mm-provided" value="provided">
							<label for="mm-provided"><strong><?php esc_html_e( 'Use my measurements', 'hadhramifashion' ); ?></strong><span><?php esc_html_e( 'The ones I entered on the product page.', 'hadhramifashion' ); ?></span></label>
						</div>
					</div>
				</div>
			</section>

			<section class="checkout__panel">
				<h3><span class="step">3</span><?php esc_html_e( 'Your details', 'hadhramifashion' ); ?></h3>
				<div class="field-row">
					<div class="field">
						<label for="co-name"><?php esc_html_e( 'Name', 'hadhramifashion' ); ?></label>
						<input id="co-name" name="name" type="text" autocomplete="name" required>
					</div>
					<div class="field">
						<label for="co-phone"><?php esc_html_e( 'Phone', 'hadhramifashion' ); ?></label>
						<input id="co-phone" name="phone" type="tel" autocomplete="tel" required>
					</div>
				</div>
				<div class="field">
					<label for="co-email"><?php esc_html_e( 'Email (for your receipt)', 'hadhramifashion' ); ?></label>
					<input id="co-email" name="email" type="email" autocomplete="email">
				</div>
				<div class="field">
					<label for="co-notes"><?php esc_html_e( 'A note for the atelier', 'hadhramifashion' ); ?></label>
					<textarea id="co-notes" name="notes" rows="3" placeholder="<?php esc_attr_e( 'Occasion, preferred cuff, monogram…', 'hadhramifashion' ); ?>"></textarea>
				</div>
			</section>
		</div>

		<aside class="summary">
			<h3><?php esc_html_e( 'Summary', 'hadhramifashion' ); ?></h3>
			<div data-summary-lines></div>
			<div class="row"><span><?php esc_html_e( 'Subtotal', 'hadhramifashion' ); ?></span><span data-summary-subtotal>—</span></div>
			<div class="row"><span><?php esc_html_e( 'Delivery', 'hadhramifashion' ); ?></span><span data-summary-fee>—</span></div>
			<div class="row row--total"><span><?php esc_html_e( 'Total', 'hadhramifashion' ); ?></span><span data-summary-total>—</span></div>
			<p class="muted" style="font-size:.85rem;margin-top:1rem"><?php esc_html_e( 'Pay on delivery or at your fitting — cash, card or UPI.', 'hadhramifashion' ); ?></p>
			<div class="notice" data-notice role="alert"></div>
			<button class="btn btn--block" type="submit" data-place disabled><?php esc_html_e( 'Place order', 'hadhramifashion' ); ?></button>
		</aside>
	</form>
</div>
<?php
get_footer();
