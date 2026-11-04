<?php
/**
 * Single post.
 */
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<header class="page-hero grain">
		<div class="wrap">
			<div class="crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_category( ', ' ); ?></div>
			<h1><?php the_title(); ?></h1>
			<p><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		</div>
	</header>
	<div class="content-area">
		<div class="wrap layout-side">
			<article class="entry">
				<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); } ?>
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</article>
			<?php get_template_part( 'template-parts/aside' ); ?>
		</div>
	</div>
	<?php
endwhile;
get_template_part( 'template-parts/cta' );
get_footer();
