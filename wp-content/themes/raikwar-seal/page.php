<?php
/**
 * Page template.
 * Builder-style pages (GreenShift / full-width blocks) render full width;
 * text pages get a reading column with a sticky enquiry card.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$raw        = get_post_field( 'post_content', get_the_ID() );
	$is_builder = (bool) preg_match( '/wp:greenshift|wp:(columns|group|cover|media-text)|gspb_|alignfull|alignwide/', $raw );
	?>
	<header class="page-hero grain">
		<div class="wrap">
			<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></div>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
		</div>
	</header>

	<div class="content-area">
		<div class="wrap">
			<?php if ( $is_builder ) : ?>
				<div class="entry entry--wide"><?php the_content(); ?></div>
			<?php else : ?>
				<div class="layout-side">
					<article class="entry"><?php the_content(); ?></article>
					<?php get_template_part( 'template-parts/aside' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
endwhile;
get_template_part( 'template-parts/cta' );
get_footer();
