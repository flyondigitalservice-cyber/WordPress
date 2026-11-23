<?php
get_header();
?>
<section class="sec-wrap">
    <div class="container">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <h1 style="font-size:34px;color:#0A2217;margin-bottom:16px;"><?php the_title(); ?></h1>
            <div><?php the_content(); ?></div>
        <?php endwhile; endif; ?>
    </div>
</section>
<?php get_footer(); ?>
