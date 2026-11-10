<?php
/**
 * Builders directory: /builders/
 *
 * To customise, copy this file to your (child) theme as
 * propzed-publisher/builders.php.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pzp_builders = pzp_get_builders();
?>

<section class="pz-page-head">
	<div class="pz-container">
		<nav class="pz-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'propzed-publisher' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'propzed-publisher' ); ?></a> /
			<span aria-current="page"><?php esc_html_e( 'Builders', 'propzed-publisher' ); ?></span>
		</nav>
		<h1><?php esc_html_e( 'Builders & Developers', 'propzed-publisher' ); ?></h1>
		<div class="pz-page-head__desc">
			<p><?php esc_html_e( 'Pick a builder to see all of their commercial projects in one place: offices, showrooms, malls, warehouses and plots, with price, size and connectivity.', 'propzed-publisher' ); ?></p>
		</div>
	</div>
</section>

<div class="pz-container pzp-bbody">
	<?php if ( $pzp_builders ) : ?>

		<?php if ( count( $pzp_builders ) > 8 ) : ?>
			<div class="pzp-bsearch">
				<label for="pzp-bsearch"><?php esc_html_e( 'Find a builder', 'propzed-publisher' ); ?></label>
				<input type="search" id="pzp-bsearch" data-pzp-bsearch placeholder="<?php esc_attr_e( 'Type a builder name…', 'propzed-publisher' ); ?>" autocomplete="off">
			</div>
		<?php endif; ?>

		<h2 class="pz-sr"><?php esc_html_e( 'All builders', 'propzed-publisher' ); ?></h2>
		<p class="pz-results-count" role="status" data-pzp-bcount>
			<?php
			/* translators: %d: number of builders */
			echo esc_html( sprintf( _n( '%d builder', '%d builders', count( $pzp_builders ), 'propzed-publisher' ), count( $pzp_builders ) ) );
			?>
		</p>

		<div class="pzp-bgrid pzp-bgrid--dir" data-pzp-blist>
			<?php
			foreach ( $pzp_builders as $pzp_builder ) {
				echo pzp_builder_card( $pzp_builder ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in pzp_builder_card().
			}
			?>
		</div>
		<p class="pz-empty" data-pzp-bempty hidden><?php esc_html_e( 'No builder matches your search.', 'propzed-publisher' ); ?></p>

	<?php else : ?>
		<div class="pz-empty">
			<h2><?php esc_html_e( 'Builder pages are coming soon', 'propzed-publisher' ); ?></h2>
			<p><?php esc_html_e( 'Meanwhile, browse all commercial properties.', 'propzed-publisher' ); ?></p>
			<a class="pz-btn pz-btn--gold" href="<?php echo esc_url( propzed_listing_url() ); ?>"><?php esc_html_e( 'View properties', 'propzed-publisher' ); ?></a>
		</div>
	<?php endif; ?>

	<div class="pzp-cta-card">
		<div>
			<strong><?php esc_html_e( 'Are you a builder or developer?', 'propzed-publisher' ); ?></strong>
			<p><?php esc_html_e( 'List your commercial projects and get your own builder page. Every listing is verified by our team before it goes live.', 'propzed-publisher' ); ?></p>
		</div>
		<a class="pz-btn pz-btn--navy" href="<?php echo esc_url( pzp_publisher_url( array( 'tab' => 'register' ) ) ); ?>"><?php esc_html_e( 'List your projects', 'propzed-publisher' ); ?></a>
	</div>
</div>

<?php
get_footer();
