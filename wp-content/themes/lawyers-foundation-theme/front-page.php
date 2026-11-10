<?php
if (!defined('ABSPATH')) exit;
get_header();
$hero = lf_img_url('hero', 'full');
$aid = lf_img_url('legal-aid', 'large');
?>
<section class="hero"<?php if ($hero) echo ' style="--hero:url(' . esc_url($hero) . ')"'; ?>>
  <div class="wrap">
    <div class="hero-text">
      <p class="eyebrow"><?php echo esc_html(lf_opt('org_region')); ?></p>
      <h1>One Bar across two cities, <em>standing together.</em></h1>
      <p class="lede">The professional association of advocates practising from the Bombay High Court to the courts of Belapur, Vashi and Panvel. Learn, connect and serve with your colleagues.</p>
      <div class="hero-actions">
        <a class="btn btn-gold" href="<?php echo esc_url(lf_url('join')); ?>">Become a member</a>
        <a class="btn btn-line" href="<?php echo esc_url(lf_url('events')); ?>">Upcoming events</a>
      </div>
    </div>
  </div>
</section>

<section class="quick"><div class="wrap">
  <?php
  $quick = [
      ['id', 'Apply online', 'Membership application with enrolment verification', lf_url('join')],
      ['calendar', 'Events', 'Seminars, workshops and legal aid camps', lf_url('events')],
      ['users', 'Member directory', 'Find colleagues by court and practice area', lf_url('directory')],
      ['hand', 'Legal aid', 'Free first advice from volunteer advocates', lf_url('legal-aid')],
  ];
  foreach ($quick as $q) echo '<a href="' . esc_url($q[3]) . '">' . lf_icon($q[0]) . '<b>' . esc_html($q[1]) . '</b><span>' . esc_html($q[2]) . '</span></a>';
  ?>
</div></section>

<section class="section"><div class="wrap split">
  <figure class="frame"><?php echo lf_img('members', 'Advocates of the Foundation on the steps of a court building'); ?></figure>
  <div>
    <p class="eyebrow">About the Foundation</p>
    <h2>A professional home for every advocate in the region</h2>
    <p><?php echo esc_html(lf_org()); ?> is a voluntary, non-political association of advocates, law firms and law students. We help members grow in practice through learning and mentoring, speak for the Bar on matters that affect it, and take legal help to people who cannot afford it.</p>
    <ul class="ticks">
      <li><?php echo lf_icon('check'); ?><span>Open to advocates enrolled with the Bar Council of Maharashtra &amp; Goa</span></li>
      <li><?php echo lf_icon('check'); ?><span>Chapters for Mumbai and Navi Mumbai</span></li>
      <li><?php echo lf_icon('check'); ?><span>Run by elected office bearers and member committees</span></li>
    </ul>
    <p><a class="btn btn-navy" href="<?php echo esc_url(lf_url('about')); ?>">About us</a> <a class="btn btn-ghost" href="<?php echo esc_url(lf_url('leadership')); ?>">Office bearers</a></p>
  </div>
</div></section>

<section class="section alt"><div class="wrap">
  <div class="section-head row"><div><p class="eyebrow">Calendar</p><h2>Upcoming events</h2></div><a class="more-link" href="<?php echo esc_url(lf_url('events')); ?>">All events <?php echo lf_icon('arrow'); ?></a></div>
  <div class="ev-list">
    <?php
    $up = lf_events('upcoming', 4);
    if ($up->have_posts()) { foreach ($up->posts as $p) echo lf_event_card($p); }
    else echo '<p class="empty">No events are scheduled right now. New dates are announced to members by email.</p>';
    ?>
  </div>
</div></section>

<section class="section"><div class="wrap split">
  <div>
    <p class="eyebrow">Member portal</p>
    <h2>Your membership, online</h2>
    <p>Apply, get verified and manage everything from your dashboard.</p>
    <div class="grid-2 mini">
      <?php
      $feat = [
          ['id', 'Digital membership card', 'Your membership ID and validity, ready to show or print.'],
          ['calendar', 'One-click event registration', 'Members register for seminars and meets without filling a form.'],
          ['users', 'Directory and referrals', 'Reach a colleague in another court when your client needs one.'],
          ['book', 'Notices and circulars', 'Members-only updates from the office in one place.'],
      ];
      foreach ($feat as $f) echo '<div class="feature">' . lf_icon($f[0]) . '<h3>' . esc_html($f[1]) . '</h3><p>' . esc_html($f[2]) . '</p></div>';
      ?>
    </div>
    <p><a class="btn btn-navy" href="<?php echo esc_url(lf_url('membership')); ?>">Membership categories and fees</a> <a class="btn btn-ghost" href="<?php echo esc_url(lf_url('login')); ?>">Member sign in</a></p>
  </div>
  <figure class="frame"><?php echo lf_img('portal', 'An advocate using the member portal on a laptop'); ?></figure>
</div></section>

<section class="aid-band"<?php if ($aid) echo ' style="--aid:url(' . esc_url($aid) . ')"'; ?>>
  <div class="wrap"><div class="aid-text">
    <p class="eyebrow">Legal aid &amp; pro bono</p>
    <h2>Free legal advice for people who cannot afford a lawyer</h2>
    <p>Volunteer advocates of the Foundation run legal aid desks and camps for senior citizens, women, workers and tenants across Mumbai and Navi Mumbai.</p>
    <p><a class="btn btn-gold" href="<?php echo esc_url(lf_url('legal-aid')); ?>">Request legal aid</a> <a class="btn btn-line" href="<?php echo esc_url(lf_url('legal-aid')); ?>">Volunteer</a></p>
  </div></div>
</section>

<section class="section"><div class="wrap">
  <div class="section-head row"><div><p class="eyebrow">Press &amp; news</p><h2>From the Foundation</h2></div><a class="more-link" href="<?php echo esc_url(lf_url('press')); ?>">All press releases <?php echo lf_icon('arrow'); ?></a></div>
  <div class="grid-3">
    <?php
    $latest = get_posts(['post_type' => ['lf_press', 'post'], 'numberposts' => 3, 'post_status' => 'publish']);
    foreach ($latest as $p) echo lf_post_card($p);
    if (!$latest) echo '<p class="empty">News will appear here.</p>';
    ?>
  </div>
</div></section>

<section class="section alt"><div class="wrap split">
  <div>
    <p class="eyebrow">Where our members practise</p>
    <h2>From Fort to Panvel</h2>
    <p>Members appear before courts and tribunals across the region, so help in another court is one call away.</p>
    <p class="tags big"><?php foreach (array_slice(lf_courts(), 0, 15) as $c) echo '<span class="tag">' . esc_html($c) . '</span>'; ?></p>
  </div>
  <figure class="frame"><?php echo lf_img('library', 'A senior advocate mentoring two junior advocates in a law library'); ?></figure>
</div></section>

<section class="join"><div class="wrap">
  <h2>Join your colleagues at the Bar</h2>
  <p>Apply online in about three minutes. Your application is verified by the membership committee.</p>
  <p><a class="btn btn-gold" href="<?php echo esc_url(lf_url('join')); ?>">Apply for membership</a> <a class="btn btn-line" href="<?php echo esc_url(lf_url('contact')); ?>">Contact the office</a></p>
</div></section>
<?php get_footer();
