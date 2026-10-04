<?php
/**
 * Front page: renders the sections chosen in Customizer → FlavorKit → Homepage sections.
 *
 * @package FlavorKit
 */

defined( 'ABSPATH' ) || exit;

get_header();

$fk_available = array_keys( flavorkit_sections() );
$fk_order     = array_map( 'trim', explode( ',', (string) flavorkit_mod( 'sections_order' ) ) );
?>
<main id="primary" class="fk-main">
	<?php
	foreach ( $fk_order as $fk_section ) {
		if ( in_array( $fk_section, $fk_available, true ) ) {
			get_template_part( 'template-parts/sections/' . $fk_section );
		}
	}

	// If a static front page has its own block content, show it after the sections.
	if ( 'page' === get_option( 'show_on_front' ) ) {
		while ( have_posts() ) {
			the_post();
			if ( trim( get_the_content() ) ) {
				echo '<section class="fk-section"><div class="fk-container fk-entry">';
				the_content();
				echo '</div></section>';
			}
		}
	}
	?>
</main>
<?php
get_footer();
