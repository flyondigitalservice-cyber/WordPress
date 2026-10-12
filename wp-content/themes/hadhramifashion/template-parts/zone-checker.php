<?php
/**
 * Google Maps delivery-zone checker.
 *
 * @package HadhramiFashion
 */

?>
<div class="zone" data-zone>
	<div>
		<span class="eyebrow"><?php esc_html_e( 'Where we deliver', 'hadhramifashion' ); ?></span>
		<h2><?php esc_html_e( 'Is your door within the two-hour circle?', 'hadhramifashion' ); ?></h2>
		<p class="lede">
			<?php
			printf(
				/* translators: 1: express km, 2: tailored km */
				esc_html__( 'Our riders leave the atelier with pressed shirts for any address within %1$s km, and deliver suits cut to your measure anywhere within %2$s km the next day.', 'hadhramifashion' ),
				esc_html( hf_opt( 'express_radius_km' ) ),
				esc_html( hf_opt( 'tailored_radius_km' ) )
			);
			?>
		</p>

		<div class="field">
			<label for="zone-address"><?php esc_html_e( 'Your address', 'hadhramifashion' ); ?></label>
			<div class="address-search">
				<div data-autocomplete-slot>
					<input class="input" id="zone-address" type="text" autocomplete="street-address" placeholder="<?php esc_attr_e( 'Start typing your street or area…', 'hadhramifashion' ); ?>">
				</div>
				<button type="button" class="btn-icon" data-locate title="<?php esc_attr_e( 'Use my location', 'hadhramifashion' ); ?>">
					<?php echo hf_icon( 'locate' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Use my location', 'hadhramifashion' ); ?></span>
				</button>
			</div>
		</div>

		<div class="zone__result" data-zone-result aria-live="polite"></div>
	</div>
	<div class="zone__map" data-map="zone">
		<div class="map-placeholder" data-map-placeholder>
			<span>
				<?php echo hf_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><br>
				<?php echo esc_html( hf_opt( 'store_name' ) ); ?><br>
				<small><?php echo esc_html( hf_opt( 'store_address' ) ); ?></small>
			</span>
		</div>
	</div>
</div>
