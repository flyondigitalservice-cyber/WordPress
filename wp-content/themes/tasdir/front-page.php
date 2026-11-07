<?php
/**
 * Home page (landing page built for lead generation).
 *
 * @package Tasdir
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tasdir_products = new WP_Query(
	array(
		'post_type'      => 'tasdir_product',
		'posts_per_page' => 6,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);
$tasdir_hero_id  = (int) tasdir_opt( 'hero_image' );
?>

<section class="hero">
	<div class="hero__img">
		<?php
		if ( $tasdir_hero_id && wp_get_attachment_image_url( $tasdir_hero_id, 'full' ) ) {
			echo wp_get_attachment_image(
				$tasdir_hero_id,
				'full',
				false,
				array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => '100vw',
					'alt'           => '',
				)
			);
		} else {
			echo tasdir_theme_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'banana-plantation',
				__( 'Tasdir banana export carton in an Indian banana plantation', 'tasdir' ),
				'100vw',
				array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
				)
			);
		}
		?>
	</div>
	<div class="wrap">
		<span class="eyebrow"><?php echo esc_html( tasdir_opt( 'hero_eyebrow' ) ); ?></span>
		<h1><?php echo wp_kses( tasdir_opt( 'hero_title' ), tasdir_kses_title() ); ?></h1>
		<p class="hero__lead"><?php echo esc_html( tasdir_opt( 'hero_text' ) ); ?></p>
		<div class="hero__actions">
			<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Get a quote on WhatsApp', 'tasdir' ); ?></a>
			<a class="btn btn--ghost" href="#products" data-scroll><?php esc_html_e( 'View products', 'tasdir' ); ?><?php tasdir_the_icon( 'arrow' ); ?></a>
		</div>
		<ul class="hero__badges">
			<li><?php tasdir_the_icon( 'check' ); ?><?php esc_html_e( 'Farm-direct sourcing', 'tasdir' ); ?></li>
			<li><?php tasdir_the_icon( 'check' ); ?><?php esc_html_e( 'Export-grade packing', 'tasdir' ); ?></li>
			<li><?php tasdir_the_icon( 'check' ); ?><?php esc_html_e( 'Reefer cold chain', 'tasdir' ); ?></li>
		</ul>
	</div>
</section>

<section class="trust" aria-label="<?php esc_attr_e( 'Why buyers choose Tasdir', 'tasdir' ); ?>">
	<div class="wrap">
		<div class="trust__item"><?php tasdir_the_icon( 'leaf' ); ?><div><strong><?php esc_html_e( 'Farm fresh', 'tasdir' ); ?></strong><span><?php esc_html_e( 'Harvested to order', 'tasdir' ); ?></span></div></div>
		<div class="trust__item"><?php tasdir_the_icon( 'box' ); ?><div><strong><?php esc_html_e( 'Branded cartons', 'tasdir' ); ?></strong><span><?php esc_html_e( 'Ventilated & stackable', 'tasdir' ); ?></span></div></div>
		<div class="trust__item"><?php tasdir_the_icon( 'snow' ); ?><div><strong><?php esc_html_e( 'Cold chain', 'tasdir' ); ?></strong><span><?php esc_html_e( 'Pack-house to port', 'tasdir' ); ?></span></div></div>
		<div class="trust__item"><?php tasdir_the_icon( 'doc' ); ?><div><strong><?php esc_html_e( 'Full documents', 'tasdir' ); ?></strong><span><?php esc_html_e( 'Phyto, COO, invoice', 'tasdir' ); ?></span></div></div>
	</div>
</section>

<section id="products" class="section section--cream">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'Our products', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Premium fruit, export ready', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Hand-graded Indian fruit packed in Tasdir export cartons. Tap “Get price” to receive today’s rate on WhatsApp.', 'tasdir' ); ?></p>
		</div>
		<div class="grid grid--2">
			<?php
			while ( $tasdir_products->have_posts() ) {
				$tasdir_products->the_post();
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap split">
		<div class="split__media">
			<?php echo tasdir_theme_img( 'banana-box-open', __( 'Tasdir 13 kg banana export carton packed with fresh bananas', 'tasdir' ), '(min-width: 980px) 560px, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div>
			<span class="eyebrow"><?php esc_html_e( 'Why Tasdir', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'A reliable export partner from farm to port', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'We handle sourcing, grading, packing, cold chain and paperwork so you receive consistent, market-ready fruit every shipment.', 'tasdir' ); ?></p>
			<ul class="ticks">
				<li><strong><?php esc_html_e( 'Consistent quality', 'tasdir' ); ?></strong> — <?php esc_html_e( 'size, colour and maturity graded by hand.', 'tasdir' ); ?></li>
				<li><strong><?php esc_html_e( 'Your packing, your label', 'tasdir' ); ?></strong> — <?php esc_html_e( 'Tasdir cartons or private label on request.', 'tasdir' ); ?></li>
				<li><strong><?php esc_html_e( 'Transparent pricing', 'tasdir' ); ?></strong> — <?php esc_html_e( 'clear FOB / CIF quotes with no surprises.', 'tasdir' ); ?></li>
				<li><strong><?php esc_html_e( 'Real-time updates', 'tasdir' ); ?></strong> — <?php esc_html_e( 'photos, loading videos and documents on WhatsApp.', 'tasdir' ); ?></li>
			</ul>
			<a class="btn btn--navy" href="#enquiry" data-scroll><?php esc_html_e( 'Request a quotation', 'tasdir' ); ?><?php tasdir_the_icon( 'arrow' ); ?></a>
		</div>
	</div>
</section>

<section class="section section--cream">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'What we deliver', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Built for importers & wholesalers', 'tasdir' ); ?></h2>
		</div>
		<div class="features">
			<div class="feature"><div class="feature__icon"><?php tasdir_the_icon( 'shield' ); ?></div><h3><?php esc_html_e( 'Quality checked', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Pre-shipment inspection and residue-compliant sourcing for your market.', 'tasdir' ); ?></p></div>
			<div class="feature"><div class="feature__icon"><?php tasdir_the_icon( 'box' ); ?></div><h3><?php esc_html_e( 'Export packing', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Ventilated corrugated cartons designed for long reefer voyages.', 'tasdir' ); ?></p></div>
			<div class="feature"><div class="feature__icon"><?php tasdir_the_icon( 'ship' ); ?></div><h3><?php esc_html_e( 'On-time shipping', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Planned harvests and booked reefers to meet your arrival date.', 'tasdir' ); ?></p></div>
			<div class="feature"><div class="feature__icon"><?php tasdir_the_icon( 'tag' ); ?></div><h3><?php esc_html_e( 'Competitive rates', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Direct farm relationships mean better prices for regular buyers.', 'tasdir' ); ?></p></div>
		</div>
	</div>
</section>

<section class="section section--navy" id="process">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'How it works', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'From enquiry to your port in 4 steps', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Simple, documented and fully tracked.', 'tasdir' ); ?></p>
		</div>
		<ol class="steps">
			<li class="step"><h3><?php esc_html_e( 'Enquiry & quote', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Send product, volume and port on WhatsApp — get a quotation fast.', 'tasdir' ); ?></p></li>
			<li class="step"><h3><?php esc_html_e( 'Confirm order', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Proforma invoice with Incoterms, packing and shipment date.', 'tasdir' ); ?></p></li>
			<li class="step"><h3><?php esc_html_e( 'Harvest & pack', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Harvested to order, graded, pre-cooled and packed in Tasdir cartons.', 'tasdir' ); ?></p></li>
			<li class="step"><h3><?php esc_html_e( 'Ship & document', 'tasdir' ); ?></h3><p><?php esc_html_e( 'Reefer loading, tracking and full export documents shared.', 'tasdir' ); ?></p></li>
		</ol>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'Packaging', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Packed to arrive perfect', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Tasdir-branded export cartons with ventilation holes, handling marks and net weight — ready for retail and wholesale.', 'tasdir' ); ?></p>
		</div>
		<div class="gallery">
			<figure><?php echo tasdir_theme_img( 'grapes-vineyard', __( 'Tasdir grapes carton in an Indian vineyard', 'tasdir' ), '(min-width: 640px) 66vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
			<figure><?php echo tasdir_theme_img( 'banana-box', __( 'Tasdir banana export carton, 13 kg net weight', 'tasdir' ), '(min-width: 640px) 33vw, 50vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
			<figure><?php echo tasdir_theme_img( 'grapes-box', __( 'Tasdir open grapes carton with red and green grapes', 'tasdir' ), '(min-width: 640px) 33vw, 50vw' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
		</div>
	</div>
</section>

<?php $tasdir_markets = tasdir_markets(); ?>
<?php if ( $tasdir_markets ) : ?>
<section class="section section--cream">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'Markets', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Shipping Indian fruit worldwide', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Don’t see your country? Ask us — we quote to any port with a reefer service.', 'tasdir' ); ?></p>
		</div>
		<ul class="chips">
			<?php foreach ( $tasdir_markets as $tasdir_m ) : ?>
				<li><?php echo esc_html( $tasdir_m ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php endif; ?>

<section id="enquiry" class="section">
	<div class="wrap enquiry">
		<div class="enquiry__info">
			<span class="eyebrow"><?php esc_html_e( 'Get a quote', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Tell us what you need', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Share your product, quantity and destination. Your enquiry is saved with our export desk and opens in WhatsApp so we can reply instantly.', 'tasdir' ); ?></p>
			<?php get_template_part( 'template-parts/contact-list' ); ?>
		</div>
		<?php get_template_part( 'template-parts/enquiry-form' ); ?>
	</div>
</section>

<section class="section section--cream" id="faq">
	<div class="wrap">
		<div class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'FAQ', 'tasdir' ); ?></span>
			<h2><?php esc_html_e( 'Questions from importers', 'tasdir' ); ?></h2>
		</div>
		<div class="faq">
			<?php foreach ( tasdir_faqs() as $tasdir_q => $tasdir_a ) : ?>
				<details>
					<summary><?php echo esc_html( $tasdir_q ); ?></summary>
					<p><?php echo esc_html( $tasdir_a ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="cta-band">
			<h2><?php esc_html_e( 'Ready for your next shipment?', 'tasdir' ); ?></h2>
			<p><?php esc_html_e( 'Message our export desk now for today’s prices and availability.', 'tasdir' ); ?></p>
			<div class="btns">
				<a class="btn btn--wa" href="<?php echo esc_url( tasdir_wa_url() ); ?>" target="_blank" rel="noopener"><?php tasdir_the_icon( 'whatsapp' ); ?><?php esc_html_e( 'Chat on WhatsApp', 'tasdir' ); ?></a>
				<a class="btn btn--ghost" href="#enquiry" data-scroll><?php esc_html_e( 'Send enquiry form', 'tasdir' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
