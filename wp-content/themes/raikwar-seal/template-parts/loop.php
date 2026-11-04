<?php if ( have_posts() ) : ?>
	<div class="posts">
		<?php while ( have_posts() ) : the_post(); ?>
			<a class="post-card" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); } ?>
				<div class="post-card__body">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<h2><?php the_title(); ?></h2>
					<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<div class="pagination"><?php echo paginate_links(); // phpcs:ignore ?></div>
<?php else : ?>
	<div class="empty">
		<h2>Nothing here yet.</h2>
		<p>New updates on registrations and compliance are coming soon.</p>
		<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a>
	</div>
<?php endif; ?>
