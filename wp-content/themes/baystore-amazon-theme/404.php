<?php
/**
 * Baystore Amazon Lean — 404 Page
 *
 * @package Baystore_Amazon
 */

get_header();
?>
<main class="bs-container" style="padding:48px 16px;text-align:center;">
	<div style="background:#fff;border-radius:8px;padding:42px 24px;border:1px solid #e3e6e6;max-width:600px;margin:0 auto;">
		<h1 style="margin:0 0 10px;font-size:28px;font-weight:800;">Looking for something on Baystore.in?</h1>
		<p style="color:#565959;font-size:14px;margin-bottom:20px;">We couldn&apos;t find that page. Explore Today&apos;s Deals or return to the homepage.</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bs-btn-yellow">Back to Baystore Home</a>
	</div>
</main>
<?php
get_footer();
