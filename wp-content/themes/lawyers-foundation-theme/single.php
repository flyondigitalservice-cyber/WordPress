<?php
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()) : the_post();
    $press = get_post_type() === 'lf_press';
    ?>
    <section class="page-head"><div class="wrap narrow">
      <p class="crumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <a href="<?php echo esc_url($press ? lf_url('press') : lf_url('news')); ?>"><?php echo $press ? 'Press Releases' : 'News'; ?></a></p>
      <h1><?php the_title(); ?></h1>
      <p class="lede"><?php echo $press ? 'Press release · ' : ''; ?><?php echo esc_html(get_the_date('j F Y')); ?></p>
    </div></section>
    <section class="section"><div class="wrap narrow">
      <?php if (has_post_thumbnail()) : ?><figure class="frame lead-img"><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
      <div class="prose"><?php the_content(); ?></div>
      <?php if ($press) : ?>
        <div class="card press-contact"><h2>Media contact</h2><p>Media &amp; Publications Committee, <?php echo esc_html(lf_org()); ?><br><a href="mailto:<?php echo esc_attr(lf_opt('email')); ?>"><?php echo esc_html(lf_opt('email')); ?></a> · <?php echo esc_html(lf_opt('phone')); ?></p></div>
      <?php endif; ?>
      <p class="more"><a href="<?php echo esc_url($press ? lf_url('press') : lf_url('news')); ?>">&larr; <?php echo $press ? 'All press releases' : 'All news'; ?></a></p>
    </div></section>
    <?php
endwhile;
get_footer();
