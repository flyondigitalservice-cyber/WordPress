<?php
/**
 * Homepage.
 *
 * @package luxurywatchs
 */

get_header();

$lw_slides = array(
	array(
		'eyebrow' => __( 'New Season • Automatic Collection', 'luxurywatchs' ),
		'title'   => __( 'Time, Crafted<br>in <em>Gold</em>.', 'luxurywatchs' ),
		'text'    => __( 'Sapphire-finish crystals, 21-jewel automatic movements and 100m water resistance — from ₹8,999.', 'luxurywatchs' ),
		'cta'     => __( 'Shop the Collection', 'luxurywatchs' ),
		'url'     => lw_shop_url(),
		'tone'    => 'gold',
	),
	array(
		'eyebrow' => __( 'The Abyss Diver', 'luxurywatchs' ),
		'title'   => __( 'Dive Deeper.<br>Look <em>Sharper</em>.', 'luxurywatchs' ),
		'text'    => __( 'Ceramic bezel, luminous indices and a solid steel bracelet built for monsoon and boardroom alike.', 'luxurywatchs' ),
		'cta'     => __( 'Explore Divers', 'luxurywatchs' ),
		'url'     => lw_cat_url( 'diver' ),
		'tone'    => 'ocean',
	),
	array(
		'eyebrow' => __( 'Festive Gifting Edit', 'luxurywatchs' ),
		'title'   => __( 'Gift a Moment<br>They\'ll <em>Wear</em>.', 'luxurywatchs' ),
		'text'    => __( 'Premium gift box, personalised note and free express delivery on couple sets.', 'luxurywatchs' ),
		'cta'     => __( 'Shop Couple Sets', 'luxurywatchs' ),
		'url'     => lw_cat_url( 'couple' ),
		'tone'    => 'pepsi',
	),
);
?>

<section class="lw-hero" data-slider aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured', 'luxurywatchs' ); ?>">
	<?php foreach ( $lw_slides as $i => $s ) : ?>
		<?php $lw_img = get_theme_mod( 'lw_hero_img_' . ( $i + 1 ) ); ?>
		<div class="lw-hero__slide lw-tone--<?php echo esc_attr( $s['tone'] ); ?><?php echo 0 === $i ? ' is-active' : ''; ?>" data-slide aria-roledescription="slide">
			<?php if ( $lw_img ) : ?>
				<img class="lw-hero__bg" src="<?php echo esc_url( $lw_img ); ?>" alt="" <?php echo 0 === $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
			<?php endif; ?>
			<div class="lw-container lw-hero__inner">
				<div class="lw-hero__copy">
					<p class="lw-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
					<h1 class="lw-hero__title"><?php echo wp_kses( $s['title'], array( 'br' => array(), 'em' => array() ) ); ?></h1>
					<p class="lw-hero__text"><?php echo esc_html( $s['text'] ); ?></p>
					<div class="lw-hero__ctas">
						<a class="lw-btn lw-btn--gold" href="<?php echo esc_url( $s['url'] ); ?>"><?php echo esc_html( $s['cta'] ); ?> <?php echo lw_icon( 'arrow' ); // phpcs:ignore ?></a>
						<a class="lw-btn lw-btn--wa" href="<?php echo esc_url( lw_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?><?php esc_html_e( 'WhatsApp Us', 'luxurywatchs' ); ?></a>
					</div>
					<ul class="lw-hero__proof">
						<li><strong>25,000+</strong><span><?php esc_html_e( 'Happy customers', 'luxurywatchs' ); ?></span></li>
						<li><strong>4.8★</strong><span><?php esc_html_e( 'Average rating', 'luxurywatchs' ); ?></span></li>
						<li><strong>COD</strong><span><?php esc_html_e( 'Pay on delivery', 'luxurywatchs' ); ?></span></li>
					</ul>
				</div>
				<?php if ( ! $lw_img ) : ?>
					<div class="lw-hero__art"><?php echo lw_watch_svg( $s['tone'] ); // phpcs:ignore ?></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
	<div class="lw-hero__nav">
		<button type="button" class="lw-iconbtn" data-prev aria-label="<?php esc_attr_e( 'Previous slide', 'luxurywatchs' ); ?>"><?php echo lw_icon( 'chevron-l' ); // phpcs:ignore ?></button>
		<div class="lw-hero__dots" data-dots></div>
		<button type="button" class="lw-iconbtn" data-next aria-label="<?php esc_attr_e( 'Next slide', 'luxurywatchs' ); ?>"><?php echo lw_icon( 'chevron-r' ); // phpcs:ignore ?></button>
	</div>
</section>

<section class="lw-trust" aria-label="<?php esc_attr_e( 'Why shop with us', 'luxurywatchs' ); ?>">
	<div class="lw-container lw-trust__grid">
		<div><?php echo lw_icon( 'truck' ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Free Shipping', 'luxurywatchs' ); ?></strong><span><?php esc_html_e( 'All over India', 'luxurywatchs' ); ?></span></p></div>
		<div><?php echo lw_icon( 'cash' ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Cash on Delivery', 'luxurywatchs' ); ?></strong><span><?php esc_html_e( 'UPI & cards too', 'luxurywatchs' ); ?></span></p></div>
		<div><?php echo lw_icon( 'shield' ); // phpcs:ignore ?><p><strong><?php esc_html_e( '6-Month Warranty', 'luxurywatchs' ); ?></strong><span><?php esc_html_e( 'On selected models', 'luxurywatchs' ); ?></span></p></div>
		<div><?php echo lw_icon( 'headset' ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Live Video Check', 'luxurywatchs' ); ?></strong><span><?php esc_html_e( 'Before dispatch', 'luxurywatchs' ); ?></span></p></div>
	</div>
</section>

<section class="lw-section" id="styles">
	<div class="lw-container">
		<header class="lw-sechead">
			<p class="lw-eyebrow"><?php esc_html_e( 'Find your fit', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'Shop by Style', 'luxurywatchs' ); ?></h2>
		</header>
		<div class="lw-cats">
			<?php
			$lw_tones = array( 'carbon', 'ocean', 'pepsi', 'ivory', 'carbon', 'steel', 'gold', 'navy' );
			foreach ( lw_style_categories() as $i => $c ) :
				$img = lw_cat_image( $c['slug'] );
				?>
				<a class="lw-cat lw-tone--<?php echo esc_attr( $lw_tones[ $i ] ); ?>" href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>">
					<span class="lw-cat__media">
						<?php if ( $img ) : ?>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy" width="600" height="600">
						<?php else : ?>
							<?php echo lw_watch_svg( $lw_tones[ $i ] ); // phpcs:ignore ?>
						<?php endif; ?>
					</span>
					<span class="lw-cat__name"><?php echo esc_html( $c['name'] ); ?></span>
					<span class="lw-cat__tag"><?php echo esc_html( $c['tag'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="lw-section lw-section--alt">
	<div class="lw-container">
		<header class="lw-sechead lw-sechead--row">
			<div>
				<p class="lw-eyebrow"><?php esc_html_e( 'Most loved', 'luxurywatchs' ); ?></p>
				<h2 class="lw-h2"><?php esc_html_e( 'Bestsellers', 'luxurywatchs' ); ?></h2>
			</div>
			<a class="lw-link" href="<?php echo esc_url( lw_shop_url() ); ?>"><?php esc_html_e( 'View all', 'luxurywatchs' ); ?> <?php echo lw_icon( 'arrow' ); // phpcs:ignore ?></a>
		</header>
		<div class="lw-products"><?php lw_product_grid( array( 'type' => 'best', 'limit' => 8 ) ); ?></div>
	</div>
</section>

<section class="lw-section" id="collections">
	<div class="lw-container">
		<header class="lw-sechead lw-sechead--row">
			<div>
				<p class="lw-eyebrow"><?php esc_html_e( 'Designed in-house', 'luxurywatchs' ); ?></p>
				<h2 class="lw-h2"><?php esc_html_e( 'Signature Collections', 'luxurywatchs' ); ?></h2>
			</div>
			<div class="lw-rail-nav">
				<button type="button" class="lw-iconbtn" data-rail-prev="collections" aria-label="<?php esc_attr_e( 'Scroll left', 'luxurywatchs' ); ?>"><?php echo lw_icon( 'chevron-l' ); // phpcs:ignore ?></button>
				<button type="button" class="lw-iconbtn" data-rail-next="collections" aria-label="<?php esc_attr_e( 'Scroll right', 'luxurywatchs' ); ?>"><?php echo lw_icon( 'chevron-r' ); // phpcs:ignore ?></button>
			</div>
		</header>
		<div class="lw-rail" data-rail="collections">
			<?php foreach ( lw_collections() as $c ) : ?>
				<?php $img = lw_cat_image( $c['slug'] ); ?>
				<a class="lw-coll lw-tone--<?php echo esc_attr( $c['tone'] ); ?>" href="<?php echo esc_url( lw_cat_url( $c['slug'] ) ); ?>">
					<span class="lw-coll__media">
						<?php if ( $img ) : ?>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy" width="600" height="600">
						<?php else : ?>
							<?php echo lw_watch_svg( $c['tone'] ); // phpcs:ignore ?>
						<?php endif; ?>
					</span>
					<span class="lw-coll__name"><?php echo esc_html( $c['name'] ); ?></span>
					<span class="lw-coll__line"><?php echo esc_html( $c['line'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="lw-promo">
	<div class="lw-container lw-promo__inner">
		<div class="lw-promo__copy">
			<p class="lw-eyebrow"><?php esc_html_e( 'Limited time', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'The Festive Vault Sale', 'luxurywatchs' ); ?></h2>
			<p><?php printf( esc_html__( 'Up to 40%% off automatics + extra 10%% with code %s. Ends soon.', 'luxurywatchs' ), '<b class="lw-code">' . esc_html( get_theme_mod( 'lw_offer_code', 'LUXE10' ) ) . '</b>' ); ?></p>
			<div class="lw-countdown" data-countdown aria-live="polite">
				<div><b data-d>00</b><span><?php esc_html_e( 'Days', 'luxurywatchs' ); ?></span></div>
				<div><b data-h>00</b><span><?php esc_html_e( 'Hrs', 'luxurywatchs' ); ?></span></div>
				<div><b data-m>00</b><span><?php esc_html_e( 'Min', 'luxurywatchs' ); ?></span></div>
				<div><b data-s>00</b><span><?php esc_html_e( 'Sec', 'luxurywatchs' ); ?></span></div>
			</div>
			<a class="lw-btn lw-btn--gold" href="<?php echo esc_url( lw_cat_url( 'sale' ) ); ?>"><?php esc_html_e( 'Shop the Sale', 'luxurywatchs' ); ?> <?php echo lw_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="lw-promo__art"><?php echo lw_watch_svg( 'gold' ); // phpcs:ignore ?></div>
	</div>
</section>

<section class="lw-section">
	<div class="lw-container">
		<header class="lw-sechead">
			<p class="lw-eyebrow"><?php esc_html_e( 'Every budget, one standard', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'Shop by Price', 'luxurywatchs' ); ?></h2>
		</header>
		<div class="lw-prices">
			<?php foreach ( lw_price_bands() as $b ) : ?>
				<a class="lw-price-tile" href="<?php echo esc_url( lw_price_url( $b['min'], $b['max'] ) ); ?>">
					<span><?php echo esc_html( $b['label'] ); ?></span>
					<strong>₹<?php echo esc_html( $b['amount'] ); ?></strong>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="lw-section lw-section--alt">
	<div class="lw-container">
		<header class="lw-sechead lw-sechead--row">
			<div>
				<p class="lw-eyebrow"><?php esc_html_e( 'Just landed', 'luxurywatchs' ); ?></p>
				<h2 class="lw-h2"><?php esc_html_e( 'New Arrivals', 'luxurywatchs' ); ?></h2>
			</div>
			<a class="lw-link" href="<?php echo esc_url( lw_shop_url() ); ?>"><?php esc_html_e( 'View all', 'luxurywatchs' ); ?> <?php echo lw_icon( 'arrow' ); // phpcs:ignore ?></a>
		</header>
		<div class="lw-products"><?php lw_product_grid( array( 'type' => 'new', 'limit' => 4 ) ); ?></div>
	</div>
</section>

<section class="lw-section">
	<div class="lw-container lw-why">
		<div class="lw-why__intro">
			<p class="lw-eyebrow"><?php esc_html_e( 'The Signature promise', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'Luxury feel. Honest price. Zero risk.', 'luxurywatchs' ); ?></h2>
			<p><?php esc_html_e( 'Every watch is hand-inspected, regulated and photographed in our studio before it ships. What you see is exactly what arrives at your door.', 'luxurywatchs' ); ?></p>
			<a class="lw-btn lw-btn--ghost" href="<?php echo esc_url( lw_whatsapp_url( 'Hi! Can I get a live video of a watch before ordering?' ) ); ?>" target="_blank" rel="noopener"><?php echo lw_whatsapp_icon(); // phpcs:ignore ?><?php esc_html_e( 'Request a live video', 'luxurywatchs' ); ?></a>
		</div>
		<ul class="lw-why__list">
			<li><?php echo lw_icon( 'gear' ); // phpcs:ignore ?><h3><?php esc_html_e( 'Automatic movements', 'luxurywatchs' ); ?></h3><p><?php esc_html_e( 'Smooth-sweep 21-jewel automatics with 40-hour power reserve.', 'luxurywatchs' ); ?></p></li>
			<li><?php echo lw_icon( 'shield' ); // phpcs:ignore ?><h3><?php esc_html_e( '316L steel & sapphire finish', 'luxurywatchs' ); ?></h3><p><?php esc_html_e( 'Scratch-resistant crystal, solid links and screw-down crowns.', 'luxurywatchs' ); ?></p></li>
			<li><?php echo lw_icon( 'box' ); // phpcs:ignore ?><h3><?php esc_html_e( 'Premium gift box', 'luxurywatchs' ); ?></h3><p><?php esc_html_e( 'Premium box and cleaning cloth with every order.', 'luxurywatchs' ); ?></p></li>
			<li><?php echo lw_icon( 'headset' ); // phpcs:ignore ?><h3><?php esc_html_e( 'Real humans on WhatsApp', 'luxurywatchs' ); ?></h3><p><?php esc_html_e( 'Sizing help, strap adjustment guides and after-sales support.', 'luxurywatchs' ); ?></p></li>
		</ul>
	</div>
</section>

<section class="lw-section lw-section--alt">
	<div class="lw-container">
		<header class="lw-sechead">
			<p class="lw-eyebrow"><?php esc_html_e( '4.8 / 5 from 3,200+ verified reviews', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'Loved Across India', 'luxurywatchs' ); ?></h2>
		</header>
		<div class="lw-rail lw-reviews" data-rail="reviews">
			<?php
			$lw_reviews = array(
				array( 'Rohit S.', 'Mumbai', 'Ordered the Abyss diver on COD, delivered in 3 days. Finish and weight feel premium — my colleagues keep asking where I got it.' ),
				array( 'Priya K.', 'Bengaluru', 'Gifted the couple set for our anniversary. The packaging alone made it special. Excellent support on WhatsApp.' ),
				array( 'Arjun M.', 'Delhi', 'Grand Prix chrono looks stunning. All subdials work, strap was resized free. Will buy again.' ),
				array( 'Sneha R.', 'Hyderabad', 'Was nervous ordering online but they sent a live video before shipping. 100% trustworthy.' ),
				array( 'Vikram P.', 'Ahmedabad', 'Aurum two-tone is a showstopper at weddings. Great value for the price.' ),
			);
			foreach ( $lw_reviews as $r ) :
				?>
				<figure class="lw-review">
					<div class="lw-stars" aria-label="<?php esc_attr_e( '5 stars', 'luxurywatchs' ); ?>"><?php echo str_repeat( lw_icon( 'star' ), 5 ); // phpcs:ignore ?></div>
					<blockquote>“<?php echo esc_html( $r[2] ); ?>”</blockquote>
					<figcaption><strong><?php echo esc_html( $r[0] ); ?></strong> · <?php echo esc_html( $r[1] ); ?> <span class="lw-verified"><?php esc_html_e( 'Verified buyer', 'luxurywatchs' ); ?></span></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="lw-section">
	<div class="lw-container lw-faq-wrap">
		<header class="lw-sechead">
			<p class="lw-eyebrow"><?php esc_html_e( 'Good to know', 'luxurywatchs' ); ?></p>
			<h2 class="lw-h2"><?php esc_html_e( 'Frequently Asked Questions', 'luxurywatchs' ); ?></h2>
		</header>
		<div class="lw-faq">
			<?php
			$lw_faq = array(
				array( __( 'Is Cash on Delivery available?', 'luxurywatchs' ), __( 'Yes. COD is available on all pin codes we serve across India. You can also pay via UPI, cards or net banking.', 'luxurywatchs' ) ),
				array( __( 'How long does delivery take?', 'luxurywatchs' ), __( 'Orders ship within 24 hours. Metro cities receive them in 2–4 days, the rest of India in 4–7 days. Shipping is always free.', 'luxurywatchs' ) ),
				array( __( 'What warranty do I get?', 'luxurywatchs' ), __( 'Selected models carry a 6-month warranty on the movement — this is mentioned on the product page. Ask us on WhatsApp if you are unsure about a model.', 'luxurywatchs' ) ),
				array( __( 'Do you offer refunds or returns?', 'luxurywatchs' ), __( 'No. All sales are final and we do not offer refunds or returns. Ask us for a live video on WhatsApp before you order so you know exactly what you are getting.', 'luxurywatchs' ) ),
				array( __( 'Will the strap fit my wrist?', 'luxurywatchs' ), __( 'Bracelets come with removable links. Share your wrist size before dispatch and we will size it for free.', 'luxurywatchs' ) ),
			);
			foreach ( $lw_faq as $q ) :
				?>
				<details class="lw-faq__item">
					<summary><?php echo esc_html( $q[0] ); ?></summary>
					<p><?php echo esc_html( $q[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
	<script type="application/ld+json">
	<?php
	$lw_faq_schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map(
			static function ( $q ) {
				return array(
					'@type'          => 'Question',
					'name'           => $q[0],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $q[1],
					),
				);
			},
			$lw_faq
		),
	);
	echo wp_json_encode( $lw_faq_schema );
	?>
	</script>
</section>

<?php
get_footer();
