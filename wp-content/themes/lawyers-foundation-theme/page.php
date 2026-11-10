<?php
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()) : the_post();
    if (!lf_render_page(get_post_field('post_name', get_the_ID()))) :
        lf_page_head(get_the_title());
        ?>
        <section class="section"><div class="wrap narrow prose"><?php the_content(); ?></div></section>
        <?php
    endif;
endwhile;
get_footer();
