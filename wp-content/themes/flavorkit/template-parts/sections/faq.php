<?php
/**
 * FAQ accordion (native <details>, with FAQPage schema).
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

$fk_items = flavorkit_lines( flavorkit_mod( 'faq_items' ), 2 );
if ( ! $fk_items ) {
	return;
}

$fk_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
?>
<section class="fk-section fk-faq" id="faq">
	<div class="fk-container fk-faq-grid">
		<div class="fk-faq-intro">
			<?php flavorkit_section_heading( __( 'FAQ', 'flavorkit' ), flavorkit_mod( 'faq_title' ), '', 'left' ); ?>
			<?php if ( flavorkit_whatsapp_url() ) : ?>
				<a class="fk-btn fk-btn-light" href="<?php echo esc_url( flavorkit_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo flavorkit_icon( 'whatsapp', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Ask us on WhatsApp', 'flavorkit' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="fk-faq-list">
			<?php foreach ( $fk_items as $fk_i => $fk_item ) : ?>
				<?php
				$fk_schema['mainEntity'][] = array(
					'@type'          => 'Question',
					'name'           => $fk_item[0],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $fk_item[1],
					),
				);
				?>
				<details class="fk-faq-item fk-reveal"<?php echo 0 === $fk_i ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $fk_item[0] ); ?><span class="fk-faq-icon" aria-hidden="true"><?php echo flavorkit_icon( 'plus', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></summary>
					<div class="fk-faq-answer"><p><?php echo esc_html( $fk_item[1] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $fk_schema ); ?></script>
</section>
