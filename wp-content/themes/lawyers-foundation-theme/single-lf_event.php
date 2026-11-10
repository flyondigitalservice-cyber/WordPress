<?php
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()) : the_post();
    $id = get_the_ID();
    $m = lf_event_meta($id);
    $past = $m['date'] && $m['date'] < lf_today();
    $count = lf_event_reg_count($id);
    $left = $m['capacity'] ? max(0, $m['capacity'] - $count) : null;
    $uid = get_current_user_id();
    $me = $uid && lf_member_status($uid) ? lf_member($uid) : null;
    $mine = $me && lf_event_has_reg($id, $me['email']);
    ?>
    <section class="page-head"><div class="wrap">
      <p class="crumb"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <a href="<?php echo esc_url(lf_url('events')); ?>">Events</a></p>
      <?php if ($m['type']) : ?><span class="tag tag-gold"><?php echo esc_html($m['type']); ?></span><?php endif; ?>
      <h1><?php the_title(); ?></h1>
    </div></section>
    <section class="section"><div class="wrap event-single">
      <div>
        <?php if (has_post_thumbnail()) : ?><figure class="frame lead-img"><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
        <div class="prose"><?php the_content(); ?></div>
        <p class="more"><a href="<?php echo esc_url(lf_url('events')); ?>">&larr; All events</a></p>
      </div>
      <aside class="card event-box" id="register">
        <dl class="facts">
          <div><dt><?php echo lf_icon('calendar'); ?> Date</dt><dd><?php echo esc_html($m['date'] ? date_i18n('l, j F Y', strtotime($m['date'])) : 'To be announced'); ?></dd></div>
          <?php if ($m['time']) : ?><div><dt><?php echo lf_icon('clock'); ?> Time</dt><dd><?php echo esc_html($m['time']); ?></dd></div><?php endif; ?>
          <?php if ($m['venue'] || $m['city']) : ?><div><dt><?php echo lf_icon('pin'); ?> Venue</dt><dd><?php echo esc_html(trim($m['venue'] . ', ' . $m['city'], ', ')); ?></dd></div><?php endif; ?>
          <?php if ($m['fee']) : ?><div><dt><?php echo lf_icon('id'); ?> Fee</dt><dd><?php echo esc_html($m['fee']); ?></dd></div><?php endif; ?>
          <?php if ($left !== null && !$past) : ?><div><dt><?php echo lf_icon('users'); ?> Seats</dt><dd><?php echo $left ? (int) $left . ' of ' . (int) $m['capacity'] . ' left' : 'Fully booked'; ?></dd></div><?php endif; ?>
        </dl>
        <h2>Register</h2>
        <?php
        echo lf_flash_html('event');
        if ($past) {
            echo '<p>This event has taken place.</p>';
        } elseif ($mine) {
            echo '<div class="notice ok">You are registered for this event.</div>';
        } elseif ($left === 0) {
            echo '<p>All seats are taken.</p>';
        } elseif ($m['members'] && !lf_is_member($uid)) {
            echo '<p>This event is open to approved members only.</p><p><a class="btn btn-navy" href="' . esc_url(lf_url('login')) . '">Sign in</a> <a class="btn btn-ghost" href="' . esc_url(lf_url('join')) . '">Join</a></p>';
        } elseif (isset($_GET['lf_msg']) && $_GET['lf_msg'] === 'reg') {
            // confirmation shown above
        } else {
            echo lf_form_open('event', 'form');
            echo '<input type="hidden" name="event_id" value="' . (int) $id . '">';
            if ($me) {
                echo '<p>Registering as <b>' . esc_html($me['name']) . '</b>.</p>';
            } else {
                echo lf_field('full_name', 'Your name', 'text', 'required') . lf_field('email', 'Email address', 'email', 'required') . lf_field('phone', 'Mobile number', 'tel', 'required');
                echo '<p class="small">Members: <a href="' . esc_url(lf_url('login')) . '">sign in</a> to register in one click.</p>';
            }
            echo '<p><button class="btn btn-gold" type="submit">Register for this event</button></p></form>';
        }
        ?>
      </aside>
    </div></section>
    <?php
endwhile;
get_footer();
