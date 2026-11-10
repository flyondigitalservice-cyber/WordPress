<?php
if (!defined('ABSPATH')) exit;
get_header();
if (is_post_type_archive('lf_press')) lf_page_head('Press Releases', 'Official statements and announcements from the Foundation. Journalists can reach the media committee through the contact page.', 'Press');
elseif (is_search()) lf_page_head('Search results', 'Results for “' . get_search_query() . '”');
elseif (is_archive()) lf_page_head(wp_strip_all_tags(get_the_archive_title()));
else lf_page_head('News & Articles', 'Updates from the Foundation and articles by members.', 'News');
?>
<section class="section"><div class="wrap">
  <?php if (have_posts()) : ?>
    <div class="grid-3"><?php while (have_posts()) : the_post(); echo lf_post_card(get_post()); endwhile; ?></div>
    <nav class="pager"><?php echo paginate_links(['type' => 'plain']); ?></nav>
  <?php else : ?>
    <p class="empty">Nothing has been published here yet.</p>
  <?php endif; ?>
</div></section>
<?php get_footer();
