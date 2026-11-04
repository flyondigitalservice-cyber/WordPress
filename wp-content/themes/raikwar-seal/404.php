<?php
/**
 * 404.
 */
get_header();
?>
<section class="notfound grain">
	<div class="wrap">
		<div class="big">404</div>
		<h1>This page isn’t on file.</h1>
		<p style="color:var(--ink-2);font-size:18px">The page may have moved. Try the home page, or message us and we’ll help directly.</p>
		<div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:24px">
			<a class="btn btn--ink" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go to home page</a>
			<a class="btn btn--primary" href="<?php echo esc_url( raikwar_wa() ); ?>" target="_blank" rel="noopener"><?php echo raikwar_icon( 'wa' ); ?>WhatsApp us</a>
		</div>
	</div>
</section>
<?php get_footer(); ?>
