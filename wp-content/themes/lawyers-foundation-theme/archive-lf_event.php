<?php
if (!defined('ABSPATH')) exit;
get_header();
lf_page_head('Events', 'Seminars, workshops, legal aid camps and gatherings in Mumbai and Navi Mumbai.');
$up = lf_events('upcoming', 30);
$past = lf_events('past', 12);
?>
<section class="section"><div class="wrap">
  <div class="section-head"><h2>Upcoming events</h2></div>
  <div class="ev-list">
    <?php if ($up->have_posts()) { foreach ($up->posts as $p) echo lf_event_card($p); } else echo '<p class="empty">No events are scheduled right now. New dates are announced to members by email.</p>'; ?>
  </div>
</div></section>
<?php if ($past->have_posts()) : ?>
<section class="section alt"><div class="wrap">
  <div class="section-head"><h2>Past events</h2></div>
  <div class="ev-list past"><?php foreach ($past->posts as $p) echo lf_event_card($p); ?></div>
</div></section>
<?php endif;
get_footer();
