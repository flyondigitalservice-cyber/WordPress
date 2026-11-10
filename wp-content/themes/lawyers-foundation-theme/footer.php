<?php if (!defined('ABSPATH')) exit; ?>
</main>
<footer class="site-footer no-print">
  <div class="wrap footer-grid">
    <div>
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo lf_logo_mark(); ?><span><b><?php echo esc_html(strtoupper(lf_org())); ?></b><small><?php echo esc_html(lf_opt('org_region')); ?></small></span></a>
      <p>A voluntary professional association of advocates practising in the courts of Mumbai and Navi Mumbai.</p>
      <p class="social">
        <?php foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'x' => 'X'] as $k => $label) {
            if (lf_opt($k)) echo '<a href="' . esc_url(lf_opt($k)) . '" target="_blank" rel="noopener">' . esc_html($label) . '</a>';
        } ?>
      </p>
    </div>
    <div>
      <h2>Foundation</h2>
      <ul>
        <li><a href="<?php echo esc_url(lf_url('about')); ?>">About</a></li>
        <li><a href="<?php echo esc_url(lf_url('leadership')); ?>">Office bearers</a></li>
        <li><a href="<?php echo esc_url(lf_url('events')); ?>">Events</a></li>
        <li><a href="<?php echo esc_url(lf_url('press')); ?>">Press releases</a></li>
        <li><a href="<?php echo esc_url(lf_url('news')); ?>">News &amp; articles</a></li>
        <li><a href="<?php echo esc_url(lf_url('gallery')); ?>">Gallery</a></li>
      </ul>
    </div>
    <div>
      <h2>Members</h2>
      <ul>
        <li><a href="<?php echo esc_url(lf_url('membership')); ?>">Membership</a></li>
        <li><a href="<?php echo esc_url(lf_url('join')); ?>">Apply online</a></li>
        <li><a href="<?php echo esc_url(lf_url('login')); ?>">Member sign in</a></li>
        <li><a href="<?php echo esc_url(lf_url('directory')); ?>">Member directory</a></li>
        <li><a href="<?php echo esc_url(lf_url('legal-aid')); ?>">Legal aid</a></li>
        <li><a href="<?php echo esc_url(lf_url('resources')); ?>">Resources</a></li>
      </ul>
    </div>
    <div>
      <h2>Contact</h2>
      <p><?php echo esc_html(lf_opt('address_1')); ?></p>
      <p><?php echo esc_html(lf_opt('address_2')); ?></p>
      <p><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', lf_opt('phone'))); ?>"><?php echo esc_html(lf_opt('phone')); ?></a><br><a href="mailto:<?php echo esc_attr(lf_opt('email')); ?>"><?php echo esc_html(lf_opt('email')); ?></a></p>
    </div>
  </div>
  <div class="wrap footer-base">
    <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(lf_org()); ?>. <?php echo esc_html(lf_opt('reg_no')); ?></p>
    <p><a href="<?php echo esc_url(lf_url('conduct')); ?>">Code of Conduct</a><a href="<?php echo esc_url(lf_url('privacy')); ?>">Privacy</a><a href="<?php echo esc_url(lf_url('terms')); ?>">Terms</a><a href="<?php echo esc_url(lf_url('contact')); ?>">Contact</a></p>
  </div>
  <div class="wrap"><p class="disclaimer">As required by the Bar Council of India Rules, this website does not advertise or solicit work for any advocate. Its content is for information only and is not legal advice.</p></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
