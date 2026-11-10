<?php
if (!defined('ABSPATH')) exit;
get_header();
lf_page_head('Page not found', 'The page you are looking for has moved or does not exist.');
?>
<section class="section"><div class="wrap narrow">
  <p><a class="btn btn-navy" href="<?php echo esc_url(home_url('/')); ?>">Go to the home page</a> <a class="btn btn-ghost" href="<?php echo esc_url(lf_url('events')); ?>">See events</a></p>
</div></section>
<?php get_footer();
