<?php
/**
 * Page views. page.php calls lf_render_page() with the page slug; pages without a view show their editor content.
 */
if (!defined('ABSPATH')) exit;

function lf_views() {
    return [
        'about' => 'lf_view_about', 'leadership' => 'lf_view_leadership', 'membership' => 'lf_view_membership',
        'join' => 'lf_view_join', 'member-login' => 'lf_view_login', 'member-dashboard' => 'lf_view_dashboard',
        'members' => 'lf_view_directory', 'legal-aid' => 'lf_view_legal_aid', 'resources' => 'lf_view_resources',
        'news' => 'lf_view_news', 'gallery' => 'lf_view_gallery', 'contact' => 'lf_view_contact',
    ];
}

function lf_render_page($slug) {
    $views = lf_views();
    if (!isset($views[$slug])) return false;
    call_user_func($views[$slug]);
    return true;
}

/** Portal pages are personal: keep them out of page caches and search results. */
add_action('template_redirect', function () {
    if (is_page(['member-dashboard', 'member-login', 'join', 'members'])) {
        nocache_headers();
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
    }
    if (is_page('member-dashboard') && !is_user_logged_in()) { wp_safe_redirect(lf_url('login')); exit; }
    if (is_page(['member-login', 'join']) && is_user_logged_in() && lf_member_status() && $_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['lf_msg'])) { wp_safe_redirect(lf_url('dashboard')); exit; }
}, 5);
add_filter('wp_robots', function ($r) {
    if (is_page(['member-dashboard', 'member-login'])) { $r['noindex'] = true; }
    return $r;
});

/* ---------- Small form helpers ---------- */

function lf_field($name, $label, $type = 'text', $attrs = '', $value = null, $hint = '') {
    $v = $value === null ? lf_post($name) : $value;
    $id = 'f-' . $name . '-' . wp_unique_id();
    $out = '<p class="field"><label for="' . $id . '">' . esc_html($label) . '</label>';
    if ($type === 'textarea') $out .= '<textarea id="' . $id . '" name="' . esc_attr($name) . '" rows="4" ' . $attrs . '>' . esc_textarea($value === null ? (isset($_POST[$name]) ? sanitize_textarea_field(wp_unslash($_POST[$name])) : '') : $value) . '</textarea>';
    else $out .= '<input id="' . $id . '" type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" value="' . ($type === 'password' ? '' : esc_attr($v)) . '" ' . $attrs . '>';
    if ($hint) $out .= '<small>' . esc_html($hint) . '</small>';
    return $out . '</p>';
}

function lf_select($name, $label, $options, $value = null, $placeholder = 'Select') {
    $v = $value === null ? lf_post($name) : $value;
    $id = 'f-' . $name . '-' . wp_unique_id();
    $out = '<p class="field"><label for="' . $id . '">' . esc_html($label) . '</label><select id="' . $id . '" name="' . esc_attr($name) . '"><option value="">' . esc_html($placeholder) . '</option>';
    foreach ($options as $k => $text) {
        $val = is_int($k) ? $text : $k;
        $out .= '<option value="' . esc_attr($val) . '"' . selected($v, $val, false) . '>' . esc_html($text) . '</option>';
    }
    return $out . '</select></p>';
}

function lf_area_checks($selected) {
    $out = '<fieldset class="checks"><legend>Practice areas</legend>';
    foreach (lf_practice_areas() as $a) $out .= '<label><input type="checkbox" name="areas[]" value="' . esc_attr($a) . '"' . checked(in_array($a, (array) $selected, true), true, false) . '> ' . esc_html($a) . '</label>';
    return $out . '</fieldset>';
}

/* ---------- About ---------- */

function lf_view_about() {
    lf_page_head('About the Foundation', 'A voluntary, non-political association of advocates practising in the courts of Mumbai and Navi Mumbai.');
    ?>
    <section class="section"><div class="wrap split">
      <div class="prose">
        <h2>Who we are</h2>
        <p><?php echo esc_html(lf_org()); ?> brings together advocates, law firms and law students from the Bombay High Court, the city civil and sessions courts, the magistrate courts, the tribunals, and the courts at Thane, Belapur, Vashi and Panvel.</p>
        <p>We exist for two reasons. The first is the profession: members learn from each other, find seniors and juniors, share work across courts and stand together on matters that affect the Bar. The second is the public: we run free legal aid desks and legal awareness camps so that people who cannot afford a lawyer still get sound advice.</p>
        <p>The Foundation is run by elected office bearers and working committees. Membership is open to every advocate enrolled with the Bar Council of Maharashtra &amp; Goa who practises in the region, and to law students as associates.</p>
      </div>
      <figure class="frame"><?php echo lf_img('members', 'Members of the Foundation outside a court building'); ?></figure>
    </div></section>
    <section class="section alt"><div class="wrap">
      <div class="trio">
        <div class="statement"><?php echo lf_icon('scale'); ?><h3>Our vision</h3><p>A Bar that is united, well informed and trusted by the people it serves, in every court from Fort to Panvel.</p></div>
        <div class="statement"><?php echo lf_icon('users'); ?><h3>Our mission</h3><p>To support advocates through their careers with learning, mentoring and welfare, and to make legal help reachable for people of limited means.</p></div>
        <div class="statement"><?php echo lf_icon('hand'); ?><h3>Our values</h3><p>Integrity before the court, courtesy to colleagues, service to the public, and transparency in how the Foundation is run.</p></div>
      </div>
    </div></section>
    <section class="section"><div class="wrap">
      <div class="section-head"><h2>What we do</h2></div>
      <div class="grid-3">
        <?php
        $work = [
            ['book', 'Continuing legal education', 'Seminars, workshops and study circles on new legislation, procedure and court practice.'],
            ['users', 'Mentoring for junior advocates', 'Senior members guide juniors on drafting, court craft and building a practice.'],
            ['hand', 'Free legal aid', 'Weekly legal aid desks and outreach camps for women, senior citizens, workers and tenants.'],
            ['mic', 'Representation of the Bar', 'We take up court infrastructure, e-filing and advocate welfare issues with the authorities.'],
            ['id', 'Member services', 'Digital membership card, member directory and a referral network across courts.'],
            ['news', 'Publications and press', 'Press statements, newsletters and articles written by members.'],
        ];
        foreach ($work as $w) echo '<div class="card feature">' . lf_icon($w[0]) . '<h3>' . esc_html($w[1]) . '</h3><p>' . esc_html($w[2]) . '</p></div>';
        ?>
      </div>
      <p class="more"><a class="btn btn-navy" href="<?php echo esc_url(lf_url('leadership')); ?>">Office bearers and committees</a> <a class="btn btn-ghost" href="<?php echo esc_url(lf_url('join')); ?>">Apply for membership</a></p>
    </div></section>
    <?php
}

/* ---------- Leadership ---------- */

function lf_committees() {
    return [
        ['Legal Aid & Pro Bono', 'Runs the legal aid desks and outreach camps and assigns volunteer advocates.'],
        ['Continuing Legal Education', 'Plans seminars, workshops and the study circle calendar.'],
        ['Junior Advocates & Mentoring', 'Pairs juniors with seniors and runs court-craft sessions.'],
        ['Women Advocates', 'Works on safety, facilities and equal opportunity for women at the Bar.'],
        ['Advocate Welfare', 'Looks after medical aid, insurance guidance and help for members in distress.'],
        ['Media & Publications', 'Issues press releases and edits the newsletter and website.'],
        ['Navi Mumbai Chapter', 'Coordinates activities at Belapur, Vashi and Panvel courts.'],
        ['Membership & Verification', 'Verifies enrolment details and approves new applications.'],
    ];
}

function lf_view_leadership() {
    lf_page_head('Office Bearers & Committees', 'The Foundation is run by elected office bearers and working committees of member volunteers.');
    $team = get_posts(['post_type' => 'lf_team', 'numberposts' => 40, 'orderby' => 'menu_order', 'order' => 'ASC']);
    echo '<section class="section"><div class="wrap"><div class="section-head"><h2>Office bearers</h2></div><div class="people">';
    foreach ($team as $p) {
        $initials = strtoupper(mb_substr(preg_replace('/^(Adv\.|Dr\.|Mr\.|Ms\.|Mrs\.)\s*/i', '', $p->post_title), 0, 1));
        echo '<div class="person">' . (has_post_thumbnail($p) ? get_the_post_thumbnail($p, 'medium', ['loading' => 'lazy']) : '<span class="avatar" aria-hidden="true">' . esc_html($initials) . '</span>');
        echo '<h3>' . esc_html($p->post_title) . '</h3><p>' . esc_html($p->post_excerpt) . '</p></div>';
    }
    if (!$team) echo '<p class="empty">The list of office bearers will be published here.</p>';
    echo '</div></div></section>';
    echo '<section class="section alt"><div class="wrap"><div class="section-head"><h2>Committees</h2><p>Every member can volunteer for one committee each term.</p></div><div class="grid-2">';
    foreach (lf_committees() as $c) echo '<div class="card"><h3>' . esc_html($c[0]) . '</h3><p>' . esc_html($c[1]) . '</p></div>';
    echo '</div></div></section>';
}

/* ---------- Membership ---------- */

function lf_view_membership() {
    lf_page_head('Membership', 'Join the advocates of Mumbai and Navi Mumbai in one professional community.');
    ?>
    <section class="section"><div class="wrap">
      <div class="section-head"><h2>Membership categories</h2><p>Fees are collected by the office after your enrolment details are verified.</p></div>
      <div class="plans">
        <?php foreach (lf_member_types() as $k => $t) : ?>
          <div class="plan<?php echo $k === 'advocate' ? ' plan-main' : ''; ?>">
            <h3><?php echo esc_html($t[0]); ?></h3>
            <p class="price"><span>₹</span><?php echo esc_html(lf_opt('fee_' . $k)); ?><small><?php echo esc_html($t[1]); ?></small></p>
            <p><?php echo esc_html($t[2]); ?></p>
            <a class="btn <?php echo $k === 'advocate' ? 'btn-gold' : 'btn-ghost'; ?>" href="<?php echo esc_url(add_query_arg('type', $k, lf_url('join'))); ?>">Apply</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div></section>
    <section class="section alt"><div class="wrap split">
      <div>
        <h2>What members receive</h2>
        <ul class="ticks">
          <?php foreach ([
              'A digital membership card with your membership ID, shown in your dashboard',
              'A listing in the member directory, searchable by court and practice area',
              'Free or reduced-fee entry to seminars, workshops and the annual meet',
              'Members-only notices, circulars and reference material',
              'Referrals from colleagues who need help in another court',
              'Mentoring for junior advocates and law students',
              'A voice in the Foundation: voting rights and committee membership',
              'Welfare support and guidance in times of need',
          ] as $b) echo '<li>' . lf_icon('check') . '<span>' . esc_html($b) . '</span></li>'; ?>
        </ul>
      </div>
      <div>
        <h2>How to join</h2>
        <ol class="steps">
          <li><b>Apply online</b><span>Fill in the application with your enrolment number and court of practice. It takes about three minutes.</span></li>
          <li><b>Verification</b><span>The membership committee checks your enrolment details, usually within three working days.</span></li>
          <li><b>Pay the fee</b><span>Pay by UPI, bank transfer or at the office. Details are shown in your dashboard.</span></li>
          <li><b>Receive your card</b><span>Your membership ID and digital card appear in your dashboard as soon as you are approved.</span></li>
        </ol>
        <p><a class="btn btn-navy" href="<?php echo esc_url(lf_url('join')); ?>">Start your application</a></p>
      </div>
    </div></section>
    <?php
}

/* ---------- Apply ---------- */

function lf_view_join() {
    lf_page_head('Apply for Membership', 'Applications are verified by the membership committee before approval.', 'Apply');
    $types = [];
    foreach (lf_member_types() as $k => $t) $types[$k] = $t[0] . ' (₹' . lf_opt('fee_' . $k) . ', ' . strtolower($t[1]) . ')';
    $type = lf_post('member_type', isset($_GET['type']) ? sanitize_key($_GET['type']) : '');
    $areas = isset($_POST['areas']) && is_array($_POST['areas']) ? array_map('sanitize_text_field', wp_unslash($_POST['areas'])) : [];
    echo '<section class="section"><div class="wrap narrow">';
    if (is_user_logged_in()) {
        echo '<div class="notice ok">You are signed in. <a href="' . esc_url(lf_url('dashboard')) . '">Go to your dashboard</a>.</div></div></section>';
        return;
    }
    echo lf_flash_html('join');
    echo lf_form_open('join', 'form card');
    echo '<h2>Your details</h2><div class="cols">';
    echo lf_field('full_name', 'Full name (as on your Bar Council certificate)', 'text', 'required autocomplete="name"');
    echo lf_field('email', 'Email address', 'email', 'required autocomplete="email"', null, 'This will be your sign-in.');
    echo lf_field('phone', 'Mobile number', 'tel', 'required autocomplete="tel"');
    echo lf_field('password', 'Choose a password', 'password', 'required minlength="8" autocomplete="new-password"', null, 'At least 8 characters.');
    echo '</div><h2>Professional details</h2><div class="cols">';
    echo lf_select('member_type', 'Membership category', $types, $type);
    echo lf_field('enrolment_no', 'Bar Council enrolment number', 'text', 'placeholder="MAH/1234/2015"', null, 'Not required for law students.');
    echo lf_field('enrolment_year', 'Year of enrolment', 'number', 'min="1950" max="' . esc_attr(wp_date('Y')) . '"');
    echo lf_select('court', 'Court where you mainly practise', lf_courts());
    echo lf_select('locality', 'Locality', lf_areas());
    echo lf_field('firm', 'Firm, chamber or law college', 'text');
    echo '</div>';
    echo lf_area_checks($areas);
    echo lf_field('office_address', 'Office address (optional)', 'textarea');
    echo '<p class="check"><label><input type="checkbox" name="in_directory" value="1"' . checked(!$_POST || !empty($_POST['in_directory']), true, false) . '> List me in the member directory once I am approved. Contact details are shown only to signed-in members.</label></p>';
    echo '<p class="check"><label><input type="checkbox" name="declare" value="1"' . checked(!empty($_POST['declare']), true, false) . ' required> I declare that the details above are true and I agree to follow the <a href="' . esc_url(lf_url('conduct')) . '">Code of Conduct</a> and the <a href="' . esc_url(lf_url('privacy')) . '">Privacy Policy</a>.</label></p>';
    echo '<p><button class="btn btn-gold" type="submit">Submit application</button></p>';
    echo '<p class="small">Already applied? <a href="' . esc_url(lf_url('login')) . '">Sign in</a>.</p></form></div></section>';
}

/* ---------- Sign in ---------- */

function lf_view_login() {
    lf_page_head('Member Sign In', 'Sign in to see your membership card, register for events and reach fellow members.', 'Sign in');
    echo '<section class="section"><div class="wrap auth">';
    echo '<div>' . lf_flash_html('login');
    if (is_user_logged_in()) {
        echo '<div class="notice ok">You are signed in. <a href="' . esc_url(lf_member_status() ? lf_url('dashboard') : admin_url()) . '">Continue</a> or <a href="' . esc_url(lf_logout_url()) . '">sign out</a>.</div>';
    } else {
        echo lf_form_open('login', 'form card');
        echo lf_field('email', 'Email address', 'email', 'required autocomplete="username"');
        echo lf_field('password', 'Password', 'password', 'required autocomplete="current-password"');
        echo '<p class="check"><label><input type="checkbox" name="remember" value="1"> Keep me signed in on this device</label></p>';
        echo '<p><button class="btn btn-navy" type="submit">Sign in</button></p>';
        echo '<p class="small"><a href="' . esc_url(lf_lostpassword_url()) . '">Forgot your password?</a></p></form>';
    }
    echo '</div><aside class="auth-side">' . lf_img('portal', 'An advocate using the member portal on a laptop') . '<h2>Not a member yet?</h2><p>Membership is open to advocates practising in Mumbai and Navi Mumbai, and to law students as associates.</p><p><a class="btn btn-gold" href="' . esc_url(lf_url('join')) . '">Apply for membership</a></p></aside>';
    echo '</div></section>';
}

/* ---------- Dashboard ---------- */

function lf_member_card($m) {
    $types = lf_member_types();
    $out  = '<div class="idcard"><div class="idcard-top">' . lf_logo_mark() . '<div><b>' . esc_html(strtoupper(lf_org())) . '</b><span>' . esc_html(lf_opt('org_region')) . '</span></div></div>';
    $out .= '<p class="idcard-name">' . esc_html($m['name']) . '</p>';
    $out .= '<p class="idcard-type">' . esc_html(isset($types[$m['member_type']]) ? $types[$m['member_type']][0] : 'Member') . '</p>';
    $out .= '<dl><div><dt>Membership ID</dt><dd>' . esc_html($m['member_id']) . '</dd></div>';
    if ($m['enrolment_no']) $out .= '<div><dt>Enrolment no.</dt><dd>' . esc_html($m['enrolment_no']) . '</dd></div>';
    $out .= '<div><dt>Valid until</dt><dd>' . esc_html($m['member_type'] === 'life' ? 'Lifetime' : ($m['valid_until'] ? date_i18n('j M Y', strtotime($m['valid_until'])) : '—')) . '</dd></div></dl></div>';
    return $out;
}

function lf_view_dashboard() {
    $uid = get_current_user_id();
    $m = lf_member($uid);
    lf_page_head('My Dashboard', 'Welcome, ' . ($m ? $m['name'] : '') . '.', 'Dashboard');
    echo '<section class="section"><div class="wrap">';
    echo lf_flash_html('dashboard');
    if (!$m || !$m['status']) {
        echo '<div class="notice">This account is not a membership account. <a href="' . esc_url(admin_url()) . '">Go to the site admin</a> or <a href="' . esc_url(lf_logout_url()) . '">sign out</a>.</div></div></section>';
        return;
    }
    $types = lf_member_types();
    echo '<div class="dash"><div class="dash-main">';

    if ($m['status'] === 'pending') {
        echo '<div class="notice"><b>Application under verification.</b> The membership committee is checking your enrolment details. You will receive an email when your membership is approved.</div>';
    } elseif ($m['status'] === 'rejected') {
        echo '<div class="notice error"><b>Application not approved.</b> Please contact the office at ' . esc_html(lf_opt('email')) . ' if you think this is a mistake.</div>';
    } elseif ($m['status'] === 'lapsed') {
        echo '<div class="notice error"><b>Membership lapsed.</b> Renew your fee to restore your card and directory listing.</div>';
    }

    if ($m['status'] === 'approved') echo lf_member_card($m) . '<p class="small no-print"><button class="link" type="button" onclick="window.print()">Print or save card</button></p>';

    if (!$m['fee_paid'] && $m['status'] !== 'rejected') {
        $fee = lf_opt('fee_' . $m['member_type']);
        echo '<div class="card pay"><h2>Membership fee</h2><p>Your category: <b>' . esc_html(isset($types[$m['member_type']]) ? $types[$m['member_type']][0] : '') . '</b>. Fee payable: <b>₹' . esc_html($fee) . '</b>.</p>';
        echo '<p>Pay by UPI to <b>' . esc_html(lf_opt('upi')) . '</b>, by bank transfer, or at the office. Mention your name and enrolment number in the payment note. The office marks your fee as received within two working days.</p>';
        echo '<pre>' . esc_html(lf_opt('bank')) . '</pre></div>';
    }

    // Registered events
    $regs = get_posts(['post_type' => 'lf_reg', 'numberposts' => 20, 'post_status' => 'any', 'meta_key' => '_lf_user_id', 'meta_value' => $uid]);
    echo '<div class="card"><h2>My event registrations</h2>';
    if ($regs) {
        echo '<ul class="list">';
        foreach ($regs as $r) {
            $e = get_post((int) get_post_meta($r->ID, '_lf_event_id', true));
            if (!$e) continue;
            $em = lf_event_meta($e->ID);
            echo '<li><a href="' . esc_url(get_permalink($e)) . '">' . esc_html($e->post_title) . '</a><span>' . esc_html($em['date'] ? date_i18n('j M Y', strtotime($em['date'])) : '') . '</span></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>You have not registered for any event yet. <a href="' . esc_url(lf_url('events')) . '">See upcoming events</a>.</p>';
    }
    echo '</div>';

    // Notices
    echo '<div class="card"><h2>Notices for members</h2>';
    if ($m['status'] === 'approved') {
        $notices = get_posts(['post_type' => 'lf_notice', 'numberposts' => 6]);
        if ($notices) {
            foreach ($notices as $n) echo '<details class="notice-item"><summary>' . esc_html($n->post_title) . ' <span>' . esc_html(get_the_date('j M Y', $n)) . '</span></summary><div class="prose">' . wp_kses_post(wpautop($n->post_content)) . '</div></details>';
        } else echo '<p>There are no notices at the moment.</p>';
    } else {
        echo '<p>Notices and circulars are visible once your membership is approved.</p>';
    }
    echo '</div>';

    // Profile
    echo lf_flash_html('profile');
    echo lf_form_open('profile', 'form card');
    echo '<h2>My profile</h2><div class="cols">';
    echo lf_field('phone', 'Mobile number', 'tel', 'required', $_POST ? null : $m['phone']);
    echo lf_field('firm', 'Firm, chamber or law college', 'text', '', $_POST ? null : $m['firm']);
    echo lf_select('court', 'Court where you mainly practise', lf_courts(), $_POST ? null : $m['court']);
    echo lf_select('locality', 'Locality', lf_areas(), $_POST ? null : $m['locality']);
    echo '</div>';
    echo lf_area_checks($m['areas']);
    echo lf_field('office_address', 'Office address', 'textarea', '', $m['office_address']);
    echo lf_field('bio', 'About your practice (shown in the directory)', 'textarea', 'maxlength="600"', $m['bio']);
    echo '<p class="check"><label><input type="checkbox" name="in_directory" value="1"' . checked((bool) $m['in_directory'], true, false) . '> List me in the member directory</label></p>';
    echo '<p><button class="btn btn-navy" type="submit">Save profile</button></p></form>';

    echo '</div><aside class="dash-side"><div class="card"><h2>Account</h2><dl class="facts">';
    echo '<div><dt>Status</dt><dd><span class="pill pill-' . esc_attr($m['status']) . '">' . esc_html(ucfirst($m['status'])) . '</span></dd></div>';
    echo '<div><dt>Category</dt><dd>' . esc_html(isset($types[$m['member_type']]) ? $types[$m['member_type']][0] : '—') . '</dd></div>';
    echo '<div><dt>Fee</dt><dd>' . ($m['fee_paid'] ? 'Received' : 'Not yet received') . '</dd></div>';
    echo '<div><dt>Email</dt><dd>' . esc_html($m['email']) . '</dd></div></dl>';
    echo '<p><a class="btn btn-ghost" href="' . esc_url(lf_logout_url()) . '">Sign out</a></p></div>';
    echo '<div class="card"><h2>Quick links</h2><ul class="links"><li><a href="' . esc_url(lf_url('events')) . '">Upcoming events</a></li><li><a href="' . esc_url(lf_url('directory')) . '">Member directory</a></li><li><a href="' . esc_url(lf_url('resources')) . '">Resources</a></li><li><a href="' . esc_url(lf_lostpassword_url()) . '">Change password</a></li></ul></div></aside></div>';
    echo '</div></section>';
}

/* ---------- Directory ---------- */

function lf_view_directory() {
    lf_page_head('Member Directory', 'Find a colleague by name, court or practice area.');
    $s = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
    $court = isset($_GET['court']) ? sanitize_text_field(wp_unslash($_GET['court'])) : '';
    $area = isset($_GET['area']) ? sanitize_text_field(wp_unslash($_GET['area'])) : '';
    $page = max(1, isset($_GET['pg']) ? (int) $_GET['pg'] : 1);
    $per = 24;
    $mq = [['key' => 'lf_status', 'value' => 'approved'], ['key' => 'lf_in_directory', 'value' => '1']];
    if (in_array($court, lf_courts(), true)) $mq[] = ['key' => 'lf_court', 'value' => $court];
    if (in_array($area, lf_practice_areas(), true)) $mq[] = ['key' => 'lf_areas', 'value' => '"' . $area . '"', 'compare' => 'LIKE'];
    $args = ['meta_query' => $mq, 'orderby' => 'display_name', 'order' => 'ASC', 'number' => $per, 'offset' => ($page - 1) * $per, 'count_total' => true];
    if ($s !== '') { $args['search'] = '*' . $s . '*'; $args['search_columns'] = ['display_name']; }
    $query = new WP_User_Query($args);
    $full = lf_is_member() || current_user_can('manage_options');

    echo '<section class="section"><div class="wrap">';
    echo '<form class="filter" method="get" action="' . esc_url(lf_url('directory')) . '"><p class="field"><label for="dq">Name</label><input id="dq" type="search" name="q" value="' . esc_attr($s) . '"></p>';
    echo lf_select('court', 'Court', lf_courts(), $court, 'All courts') . lf_select('area', 'Practice area', lf_practice_areas(), $area, 'All areas');
    echo '<p><button class="btn btn-navy" type="submit">Search</button></p></form>';
    if (!$full) echo '<div class="notice">Phone numbers and email addresses are shown only to approved members. <a href="' . esc_url(lf_url('login')) . '">Sign in</a> or <a href="' . esc_url(lf_url('join')) . '">apply for membership</a>.</div>';

    $users = $query->get_results();
    if (!$users) {
        echo '<p class="empty">No members match your search yet.</p>';
    } else {
        echo '<p class="small">' . (int) $query->get_total() . ' member' . ($query->get_total() == 1 ? '' : 's') . ' found</p><div class="grid-3 members">';
        foreach ($users as $u) {
            $m = lf_member($u->ID);
            echo '<article class="card member"><span class="avatar" aria-hidden="true">' . esc_html(strtoupper(mb_substr(preg_replace('/^(Adv\.|Dr\.)\s*/i', '', $m['name']), 0, 1))) . '</span><h3>' . esc_html($m['name']) . '</h3>';
            echo '<p class="meta">' . esc_html(implode(' · ', array_filter([$m['court'], $m['locality']]))) . '</p>';
            if ($m['areas']) { echo '<p class="tags">'; foreach (array_slice($m['areas'], 0, 4) as $a) echo '<span class="tag">' . esc_html($a) . '</span>'; echo '</p>'; }
            if ($m['firm']) echo '<p>' . esc_html($m['firm']) . '</p>';
            if ($full) {
                if ($m['bio']) echo '<p>' . esc_html($m['bio']) . '</p>';
                echo '<p class="contact"><a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $m['phone'])) . '">' . lf_icon('phone') . esc_html($m['phone']) . '</a><a href="mailto:' . esc_attr($m['email']) . '">' . lf_icon('mail') . esc_html($m['email']) . '</a></p>';
            }
            echo '</article>';
        }
        echo '</div>';
        $pages = (int) ceil($query->get_total() / $per);
        if ($pages > 1) {
            echo '<nav class="pager" aria-label="Pages">';
            for ($i = 1; $i <= $pages; $i++) echo '<a' . ($i === $page ? ' class="current" aria-current="page"' : '') . ' href="' . esc_url(add_query_arg(array_filter(['q' => $s, 'court' => $court, 'area' => $area, 'pg' => $i]), lf_url('directory'))) . '">' . $i . '</a>';
            echo '</nav>';
        }
    }
    echo '</div></section>';
}

/* ---------- Legal aid ---------- */

function lf_view_legal_aid() {
    lf_page_head('Legal Aid & Pro Bono', 'Free first advice from volunteer advocates for people who cannot afford a lawyer.', 'Legal Aid');
    ?>
    <section class="section"><div class="wrap split">
      <div class="prose">
        <h2>How the legal aid desk works</h2>
        <p>Volunteer advocates of the Foundation give free first advice on family disputes, tenancy and housing society matters, wages and employment, consumer complaints, senior citizen maintenance and police complaints.</p>
        <ol class="steps">
          <li><b>Send a request</b><span>Use the form on this page or visit the desk during office hours.</span></li>
          <li><b>We call you</b><span>A volunteer advocate calls to understand the matter and tells you which papers to bring.</span></li>
          <li><b>Advice and next steps</b><span>You receive advice in plain language. Where a case must be filed, we guide you to the District Legal Services Authority or a volunteer advocate.</span></li>
        </ol>
        <p class="small">The desk gives general guidance. It does not create an advocate-client relationship until an advocate formally accepts your matter. In an emergency, call the police on 112 or the NALSA legal aid helpline on 15100.</p>
      </div>
      <figure class="frame"><?php echo lf_img('legal-aid', 'Volunteer advocates advising people at a legal aid camp'); ?></figure>
    </div></section>
    <section class="section alt"><div class="wrap split">
      <div>
        <h2>Request legal aid</h2>
        <?php
        echo lf_flash_html('aid');
        echo lf_form_open('aid', 'form card');
        echo '<div class="cols">' . lf_field('full_name', 'Your name', 'text', 'required') . lf_field('phone', 'Mobile number', 'tel', 'required');
        echo lf_field('email', 'Email address (optional)', 'email');
        echo lf_select('matter', 'Type of matter', ['Family & matrimonial', 'Tenancy or housing society', 'Wages or employment', 'Consumer complaint', 'Senior citizen', 'Police or criminal complaint', 'Property', 'Other']);
        echo lf_select('locality', 'Your locality', lf_areas()) . '</div>';
        echo lf_field('message', 'Describe your problem briefly', 'textarea', 'required', null, 'Do not include bank details or passwords.');
        echo '<p class="check"><label><input type="checkbox" name="consent" value="1" required> I agree that a volunteer advocate may contact me about this request.</label></p>';
        echo '<p><button class="btn btn-gold" type="submit">Send request</button></p></form>';
        ?>
      </div>
      <div>
        <h2>For advocates: volunteer</h2>
        <p>Members can volunteer two hours a month at the legal aid desk or join an outreach camp. Law student associates assist with intake and research.</p>
        <ul class="ticks">
          <li><?php echo lf_icon('check'); ?><span>Weekly desk at the Mumbai and Navi Mumbai offices</span></li>
          <li><?php echo lf_icon('check'); ?><span>Outreach camps in schools, housing societies and community halls</span></li>
          <li><?php echo lf_icon('check'); ?><span>Certificate of pro bono service at the annual meet</span></li>
        </ul>
        <p><a class="btn btn-navy" href="<?php echo esc_url(is_user_logged_in() ? lf_url('contact') : lf_url('join')); ?>"><?php echo is_user_logged_in() ? 'Tell us you want to volunteer' : 'Become a member to volunteer'; ?></a></p>
      </div>
    </div></section>
    <?php
}

/* ---------- Resources ---------- */

function lf_view_resources() {
    lf_page_head('Resources', 'Official court, tribunal and Bar Council links used every day by advocates in the region.');
    $groups = [
        'Courts & cause lists' => [
            ['Bombay High Court', 'https://bombayhighcourt.nic.in/', 'Cause lists, orders, case status and notices.'],
            ['eCourts Services', 'https://services.ecourts.gov.in/', 'Case status and orders for district and taluka courts, including Thane, Belapur, Vashi and Panvel.'],
            ['eFiling (eCourts)', 'https://filing.ecourts.gov.in/', 'Online filing for High Courts and district courts.'],
            ['Supreme Court of India', 'https://www.sci.gov.in/', 'Cause lists, judgments and e-filing.'],
            ['National Judicial Data Grid', 'https://njdg.ecourts.gov.in/', 'Pendency and disposal statistics.'],
        ],
        'Tribunals & regulators' => [
            ['MahaRERA', 'https://maharera.maharashtra.gov.in/', 'Project registrations, complaints and orders.'],
            ['NCLT', 'https://nclt.gov.in/', 'Cause lists and orders of the Mumbai benches.'],
            ['e-Jagriti (Consumer Commissions)', 'https://e-jagriti.gov.in/', 'Online filing and case status for consumer commissions.'],
        ],
        'Bar & legal services' => [
            ['Bar Council of Maharashtra & Goa', 'https://www.barcouncilmahgoa.org/', 'Enrolment, certificate of practice and welfare schemes.'],
            ['Bar Council of India', 'https://www.barcouncilofindia.org/', 'Rules, AIBE and professional standards.'],
            ['NALSA', 'https://nalsa.gov.in/', 'Free legal services and Lok Adalat schedules.'],
        ],
        'Legislation' => [
            ['India Code', 'https://www.indiacode.nic.in/', 'Central and state Acts with amendments.'],
            ['e-Gazette', 'https://egazette.gov.in/', 'Official notifications of the Government of India.'],
        ],
    ];
    echo '<section class="section"><div class="wrap">';
    foreach ($groups as $title => $links) {
        echo '<h2 class="group-title">' . esc_html($title) . '</h2><div class="grid-3">';
        foreach ($links as $l) echo '<a class="card resource" href="' . esc_url($l[1]) . '" target="_blank" rel="noopener"><h3>' . esc_html($l[0]) . ' ' . lf_icon('arrow') . '</h3><p>' . esc_html($l[2]) . '</p></a>';
        echo '</div>';
    }
    echo '<p class="small">These links open official websites that the Foundation does not control.</p></div></section>';
}

/* ---------- News ---------- */

function lf_view_news() {
    lf_page_head('News & Articles', 'Updates from the Foundation and articles by members.', 'News');
    $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    $q = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 9, 'paged' => $paged]);
    echo '<section class="section"><div class="wrap">';
    if ($q->have_posts()) {
        echo '<div class="grid-3">';
        foreach ($q->posts as $p) echo lf_post_card($p);
        echo '</div>';
        $links = paginate_links(['total' => $q->max_num_pages, 'current' => $paged, 'type' => 'plain']);
        if ($links) echo '<nav class="pager">' . $links . '</nav>';
    } else echo '<p class="empty">News will appear here.</p>';
    echo '</div></section>';
}

/* ---------- Gallery ---------- */

function lf_view_gallery() {
    lf_page_head('Gallery', 'Moments from seminars, legal aid camps and gatherings of the Foundation.');
    echo '<section class="section"><div class="wrap">';
    $content = get_post_field('post_content', get_queried_object_id());
    if (trim($content)) {
        echo '<div class="prose gallery-content">' . apply_filters('the_content', $content) . '</div>';
    } else {
        echo '<div class="gallery">';
        foreach (['members' => 'Members outside the court', 'library' => 'A mentoring session in the library', 'legal-aid' => 'A legal aid camp', 'portal' => 'The member portal', 'hero' => 'View over the creek from the Navi Mumbai office'] as $k => $cap) {
            $img = lf_img($k, $cap);
            if ($img) echo '<figure>' . $img . '<figcaption>' . esc_html($cap) . '</figcaption></figure>';
        }
        echo '</div><p class="small">To replace these pictures, edit the Gallery page and add a gallery block.</p>';
    }
    echo '</div></section>';
}

/* ---------- Contact ---------- */

function lf_view_contact() {
    lf_page_head('Contact', 'Reach the Foundation office in Mumbai or Navi Mumbai.');
    echo '<section class="section"><div class="wrap split"><div>';
    echo '<h2>Write to us</h2>' . lf_flash_html('contact') . lf_form_open('contact', 'form card');
    echo '<div class="cols">' . lf_field('full_name', 'Your name', 'text', 'required autocomplete="name"') . lf_field('email', 'Email address', 'email', 'required autocomplete="email"');
    echo lf_field('phone', 'Mobile number', 'tel', 'required autocomplete="tel"');
    echo lf_select('subject', 'Subject', ['Membership', 'Events', 'Press & media', 'Legal aid', 'Volunteering', 'Sponsorship', 'Other']) . '</div>';
    echo lf_field('message', 'Message', 'textarea', 'required');
    echo '<p><button class="btn btn-navy" type="submit">Send message</button></p></form></div>';
    echo '<div class="contact-info"><h2>Offices</h2>';
    echo '<div class="card"><h3>' . lf_icon('pin') . ' Mumbai</h3><p>' . esc_html(lf_opt('address_1')) . '</p></div>';
    echo '<div class="card"><h3>' . lf_icon('pin') . ' Navi Mumbai</h3><p>' . esc_html(lf_opt('address_2')) . '</p></div>';
    echo '<div class="card"><h3>' . lf_icon('phone') . ' Call or write</h3><p><a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', lf_opt('phone'))) . '">' . esc_html(lf_opt('phone')) . '</a><br><a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', lf_opt('mobile'))) . '">' . esc_html(lf_opt('mobile')) . '</a><br><a href="mailto:' . esc_attr(lf_opt('email')) . '">' . esc_html(lf_opt('email')) . '</a></p><p class="small">' . esc_html(lf_opt('hours')) . '</p></div>';
    echo '<p class="small">Press enquiries: choose “Press &amp; media” as the subject and the media committee will reply.</p></div></div></section>';
}
