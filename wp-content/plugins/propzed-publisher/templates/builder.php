<?php
/**
 * Builder brand page: /builder/{slug}/
 *
 * Layout: breadcrumb > logo, name, tagline, facts, contact buttons
 *         > about > status chips > all projects (12 per page) > enquiry > other builders.
 *
 * To customise, copy this file to your (child) theme as
 * propzed-publisher/builder.php.
 *
 * @package PropzedPublisher
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;

$pzp_term    = get_queried_object();
$pzp_builder = pzp_builder_data( $pzp_term );
$pzp_filter  = pzp_builder_status_filter();
$pzp_about   = term_description( $pzp_term );
/* translators: %s: builder name */
$pzp_wa_text = sprintf( __( 'Hi Propzed, I am interested in commercial projects by %s. Please share details.', 'propzed-publisher' ), $pzp_builder['name'] );

// Status chips: only statuses this builder actually has.
$pzp_statuses = array();
foreach ( get_terms( array( 'taxonomy' => 'property_status', 'hide_empty' => true ) ) as $pzp_status ) {
	$pzp_has = get_posts(
		array(
			'post_type'   => 'property',
			'numberposts' => 1,
			'fields'      => 'ids',
			'tax_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'AND',
				array( 'taxonomy' => PZP_BUILDER_TAX, 'terms' => $pzp_term->term_id ),
				array( 'taxonomy' => 'property_status', 'terms' => $pzp_status->term_id ),
			),
		)
	);
	if ( $pzp_has ) {
		$pzp_statuses[] = $pzp_status;
	}
}

$pzp_facts = array_filter(
	array(
		/* translators: %d: number of projects */
		__( 'Projects on Propzed', 'propzed-publisher' ) => (string) $pzp_builder['count'],
		__( 'Cities', 'propzed-publisher' )              => implode( ', ', array_slice( $pzp_builder['cities'], 0, 4 ) ),
		__( 'Property types', 'propzed-publisher' )      => implode( ', ', array_slice( $pzp_builder['types'], 0, 3 ) ),
		__( 'Established', 'propzed-publisher' )         => $pzp_builder['established'],
		__( 'Head office', 'propzed-publisher' )         => $pzp_builder['hq'],
	)
);

$pzp_others = array_filter(
	pzp_get_builders( 13 ),
	static function ( $other ) use ( $pzp_builder ) {
		return $other['id'] !== $pzp_builder['id'];
	}
);
?>

<section class="pz-page-head pzp-bhero">
	<div class="pz-container">
		<nav class="pz-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'propzed-publisher' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'propzed-publisher' ); ?></a> /
			<a href="<?php echo esc_url( pzp_builders_url() ); ?>"><?php esc_html_e( 'Builders', 'propzed-publisher' ); ?></a> /
			<span aria-current="page"><?php echo esc_html( $pzp_builder['name'] ); ?></span>
		</nav>

		<div class="pzp-bhero__row">
			<?php echo pzp_builder_logo( $pzp_builder, 'pzp-logo--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div>
				<h1><?php echo esc_html( $pzp_builder['name'] ); ?></h1>
				<p class="pzp-bhero__tagline">
					<?php
					echo esc_html(
						$pzp_builder['tagline']
							? $pzp_builder['tagline']
							/* translators: %s: builder name */
							: sprintf( __( 'Commercial projects by %s: offices, retail, industrial and land listed on Propzed.', 'propzed-publisher' ), $pzp_builder['name'] )
					);
					?>
				</p>
			</div>
		</div>

		<?php if ( $pzp_facts ) : ?>
			<dl class="pzp-stats">
				<?php foreach ( $pzp_facts as $pzp_label => $pzp_value ) : ?>
					<div><dt><?php echo esc_html( $pzp_label ); ?></dt><dd><?php echo esc_html( $pzp_value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<div class="pzp-bhero__cta">
			<a class="pz-btn pz-btn--gold" href="#pz-form"><?php echo propzed_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Get price list & site visit', 'propzed-publisher' ); ?></a>
			<a class="pz-btn pz-btn--wa" href="<?php echo esc_url( propzed_whatsapp_link( $pzp_wa_text ) ); ?>" target="_blank" rel="noopener"><?php echo propzed_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'WhatsApp', 'propzed-publisher' ); ?><span class="pz-sr"> <?php esc_html_e( '(opens in a new tab)', 'propzed-publisher' ); ?></span></a>
			<a class="pz-btn pz-btn--light" href="<?php echo esc_url( propzed_phone_link() ); ?>"><?php echo propzed_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( propzed_option( 'phone' ) ); ?></a>
		</div>
	</div>
</section>

<div class="pz-container pzp-bbody">

	<?php if ( $pzp_about ) : ?>
		<section class="pz-block pzp-about">
			<?php /* translators: %s: builder name */ ?>
			<h2 class="pz-block__title"><?php echo esc_html( sprintf( __( 'About %s', 'propzed-publisher' ), $pzp_builder['name'] ) ); ?></h2>
			<div class="pz-content"><?php echo wp_kses_post( $pzp_about ); ?></div>
			<?php if ( $pzp_builder['website'] ) : ?>
				<p><a class="pz-link-arrow" href="<?php echo esc_url( $pzp_builder['website'] ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Official website', 'propzed-publisher' ); ?><span class="pz-sr"> <?php esc_html_e( '(opens in a new tab)', 'propzed-publisher' ); ?></span> <?php echo propzed_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<section class="pzp-projects" aria-labelledby="pzp-projects-title">
		<div class="pzp-projects__head">
			<h2 class="pz-block__title" id="pzp-projects-title">
				<?php
				/* translators: %s: builder name */
				echo esc_html( sprintf( __( 'Projects by %s', 'propzed-publisher' ), $pzp_builder['name'] ) );
				?>
			</h2>
			<p class="pz-results-count" role="status">
				<?php
				/* translators: %d: number of properties */
				echo esc_html( sprintf( _n( '%d property', '%d properties', $wp_query->found_posts, 'propzed-publisher' ), $wp_query->found_posts ) );
				?>
			</p>
		</div>

		<?php if ( count( $pzp_statuses ) > 1 ) : ?>
			<nav class="pz-chips-row" aria-label="<?php esc_attr_e( 'Filter projects by status', 'propzed-publisher' ); ?>">
				<a class="pz-chip<?php echo $pzp_filter ? '' : ' is-active'; ?>" href="<?php echo esc_url( $pzp_builder['link'] ); ?>"<?php echo $pzp_filter ? '' : ' aria-current="true"'; ?>><?php esc_html_e( 'All', 'propzed-publisher' ); ?></a>
				<?php foreach ( $pzp_statuses as $pzp_status ) : ?>
					<a class="pz-chip<?php echo $pzp_filter === $pzp_status->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'pzp_status', $pzp_status->slug, $pzp_builder['link'] ) ); ?>"<?php echo $pzp_filter === $pzp_status->slug ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $pzp_status->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="pz-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/property-card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => '&larr;<span class="screen-reader-text"> ' . esc_html__( 'Previous page', 'propzed-publisher' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'propzed-publisher' ) . ' </span>&rarr;',
				)
			);
			?>
		<?php else : ?>
			<div class="pz-empty">
				<h3><?php esc_html_e( 'No live projects to show right now', 'propzed-publisher' ); ?></h3>
				<p><?php esc_html_e( 'New inventory is often available before it is listed. Share your requirement and our team will check with the developer.', 'propzed-publisher' ); ?></p>
				<a class="pz-btn pz-btn--gold" href="#pz-form"><?php esc_html_e( 'Ask about upcoming projects', 'propzed-publisher' ); ?></a>
			</div>
		<?php endif; ?>
	</section>

	<section class="pzp-enquire" data-pzp-message="<?php echo esc_attr( $pzp_wa_text ); ?>">
		<div class="pzp-enquire__text">
			<span class="pz-eyebrow"><?php esc_html_e( 'Talk to an advisor', 'propzed-publisher' ); ?></span>
			<?php /* translators: %s: builder name */ ?>
			<h2><?php echo esc_html( sprintf( __( 'Interested in a %s project?', 'propzed-publisher' ), $pzp_builder['name'] ) ); ?></h2>
			<ul class="pz-checklist">
				<li><?php echo propzed_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Current price list and available units', 'propzed-publisher' ); ?></li>
				<li><?php echo propzed_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Site visit arranged at a time that suits you', 'propzed-publisher' ); ?></li>
				<li><?php echo propzed_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Help with approvals, paperwork and negotiation', 'propzed-publisher' ); ?></li>
			</ul>
		</div>
		<div class="pzp-enquire__form">
			<?php
			get_template_part(
				'template-parts/forms/enquiry',
				null,
				array(
					'type'  => 'contact',
					/* translators: %s: builder name */
					'title' => sprintf( __( 'Enquire about %s', 'propzed-publisher' ), $pzp_builder['name'] ),
				)
			);
			?>
		</div>
	</section>

	<?php if ( $pzp_others ) : ?>
		<section class="pz-related-searches">
			<h2 class="pz-block__title"><?php esc_html_e( 'Other Builders', 'propzed-publisher' ); ?></h2>
			<div class="pz-bubbles">
				<?php foreach ( array_slice( $pzp_others, 0, 12 ) as $pzp_other ) : ?>
					<a class="pz-bubble" href="<?php echo esc_url( $pzp_other['link'] ); ?>"><?php echo esc_html( $pzp_other['name'] ); ?> <small><?php echo esc_html( $pzp_other['count'] ); ?></small></a>
				<?php endforeach; ?>
				<a class="pz-bubble" href="<?php echo esc_url( pzp_builders_url() ); ?>"><?php esc_html_e( 'All builders', 'propzed-publisher' ); ?></a>
			</div>
		</section>
	<?php endif; ?>
</div>

<?php
get_footer();
