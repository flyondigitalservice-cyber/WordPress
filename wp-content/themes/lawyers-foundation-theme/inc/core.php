<?php
/**
 * Core: settings, helpers, content types, member role.
 */
if (!defined('ABSPATH')) exit;

/* ---------- Settings ---------- */

function lf_defaults() {
    return [
        'org_name'   => 'Lawyers Foundation',
        'org_region' => 'Mumbai & Navi Mumbai',
        'tagline'    => 'Justice. Service. Community.',
        'phone'      => '+91 22 5550 0142',
        'mobile'     => '+91 90000 00142',
        'email'      => 'office@lawyersfoundation.example',
        'address_1'  => 'Office No. 14, 2nd Floor, Justice Chambers, Sample Street, Fort, Mumbai 400001',
        'address_2'  => 'Unit 207, Sample Business Park, Sector 15, CBD Belapur, Navi Mumbai 400614',
        'hours'      => 'Monday to Friday, 10:30 am to 6:00 pm. Saturday, 10:30 am to 2:00 pm.',
        'reg_no'     => 'Registration No. F-00000 (Mumbai) [sample]',
        'upi'        => 'lawyersfoundation@samplebank',
        'bank'       => "Account name: Lawyers Foundation\nAccount no.: 0000 0000 0000\nIFSC: SMPL0000000\nBranch: Fort, Mumbai",
        'fee_advocate'  => '1,000',
        'fee_junior'    => '500',
        'fee_student'   => '300',
        'fee_life'      => '10,000',
        'fee_firm'      => '5,000',
        'facebook'   => '',
        'linkedin'   => '',
        'instagram'  => '',
        'youtube'    => '',
        'x'          => '',
    ];
}

function lf_opt($key, $fallback = '') {
    static $opts = null;
    if ($opts === null) $opts = wp_parse_args((array) get_option('lf_settings', []), lf_defaults());
    return isset($opts[$key]) && $opts[$key] !== '' ? $opts[$key] : $fallback;
}

function lf_org() { return lf_opt('org_name', 'Lawyers Foundation'); }

/* ---------- Lists used across forms ---------- */

function lf_member_types() {
    return [
        'advocate' => ['Advocate Member', 'Per year', 'For advocates enrolled with the Bar Council of Maharashtra & Goa with more than three years at the Bar.'],
        'junior'   => ['Junior Advocate', 'Per year', 'For advocates in their first three years of practice.'],
        'student'  => ['Law Student Associate', 'Per year', 'For students enrolled in a three-year or five-year LL.B. course. Non-voting.'],
        'life'     => ['Life Member', 'One time', 'A single contribution for lifetime membership with full voting rights.'],
        'firm'     => ['Law Firm / Chamber', 'Per year', 'Covers up to five advocates practising from the same firm or chamber.'],
    ];
}

function lf_courts() {
    return [
        'Bombay High Court', 'City Civil & Sessions Court, Mumbai', 'Small Causes Court, Mumbai',
        'Metropolitan Magistrate Courts, Mumbai', 'Family Court, Bandra', 'Dindoshi Sessions Court',
        'Thane District Court', 'Belapur Court, Navi Mumbai', 'Vashi Court, Navi Mumbai', 'Panvel Court',
        'NCLT Mumbai', 'DRT Mumbai', 'Consumer Commissions', 'Labour & Industrial Courts', 'MahaRERA', 'Other',
    ];
}

function lf_practice_areas() {
    return [
        'Civil', 'Criminal', 'Family & Matrimonial', 'Property & Real Estate', 'Corporate & Commercial',
        'Banking & Insolvency', 'Labour & Employment', 'Consumer', 'Taxation', 'Intellectual Property',
        'Arbitration & Mediation', 'Co-operative Societies', 'Writs & Constitutional', 'Cyber & Technology',
    ];
}

function lf_areas() {
    return ['South Mumbai', 'Central Mumbai', 'Western Suburbs', 'Eastern Suburbs', 'Vashi', 'CBD Belapur', 'Nerul', 'Kharghar', 'Panvel', 'Airoli', 'Thane', 'Other'];
}

/* ---------- URLs and images ---------- */

function lf_url($key) {
    $map = [
        'home' => '', 'about' => 'about', 'leadership' => 'leadership', 'membership' => 'membership',
        'join' => 'join', 'login' => 'member-login', 'dashboard' => 'member-dashboard', 'directory' => 'members',
        'legal-aid' => 'legal-aid', 'resources' => 'resources', 'news' => 'news', 'gallery' => 'gallery',
        'contact' => 'contact', 'conduct' => 'code-of-conduct', 'privacy' => 'privacy', 'terms' => 'terms',
        'events' => 'events', 'press' => 'press',
    ];
    $path = isset($map[$key]) ? $map[$key] : $key;
    return home_url($path === '' ? '/' : '/' . $path . '/');
}

/** Image by key: media library attachment named lf-{key}, else the file bundled with the theme. */
function lf_img_url($key, $size = 'large') {
    $cache = get_transient('lf_img_ids');
    if (!is_array($cache)) $cache = [];
    if (!array_key_exists($key, $cache)) {
        $found = get_posts(['post_type' => 'attachment', 'name' => 'lf-' . $key, 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids']);
        $cache[$key] = $found ? (int) $found[0] : 0;
        set_transient('lf_img_ids', $cache, $cache[$key] ? DAY_IN_SECONDS : 5 * MINUTE_IN_SECONDS);
    }
    if ($cache[$key]) {
        $src = wp_get_attachment_image_url($cache[$key], $size);
        if ($src) return $src;
    }
    $file = '/assets/img/lf-' . $key . '.webp';
    return file_exists(get_template_directory() . $file) ? get_template_directory_uri() . $file : '';
}

function lf_img($key, $alt, $class = '') {
    $src = lf_img_url($key);
    if (!$src) return '';
    return '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '" loading="lazy"' . ($class ? ' class="' . esc_attr($class) . '"' : '') . '>';
}

add_action('add_attachment', function () { delete_transient('lf_img_ids'); });

function lf_logo_mark() {
    return '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false"><circle cx="32" cy="32" r="30" fill="#0c1f3f" stroke="#b4912f" stroke-width="2"/><g stroke="#d9c48a" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M32 12v34M24 48h16M20 18h24"/><path d="M20 18l-7 15h14zM44 18l-7 15h14z" stroke-width="1.6"/><path d="M13 33c0 5 14 5 14 0M37 33c0 5 14 5 14 0" stroke-width="1.6"/></g><circle cx="32" cy="11" r="2.2" fill="#b4912f"/></svg>';
}

function lf_icon($name) {
    $p = [
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'pin'      => '<path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.800 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.600 2.900-6 6.5-6s6.5 2.400 6.5 6M16 4.800a3.5 3.5 0 0 1 0 6.400M18 14.300c2.100.7 3.5 2.700 3.5 5.700"/>',
        'scale'    => '<path d="M12 3v18M7 21h10M5 7h14M5 7l-3 7a3 3 0 0 0 6 0zM19 7l-3 7a3 3 0 0 0 6 0z"/>',
        'book'     => '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2zM4 21a2 2 0 0 0 2 2h13v-4"/>',
        'hand'     => '<path d="M12 21C7 17.5 3 14 3 9.5A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 9 2.5c0 4.5-4 8-9 11.5z"/>',
        'id'       => '<rect x="2.5" y="5" width="19" height="14" rx="2"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5 16c.6-1.600 2-2.300 3.5-2.300s2.900.7 3.5 2.300M14.5 10h4M14.5 13.5h4"/>',
        'mic'      => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5.5 11.5a6.5 6.5 0 0 0 13 0M12 18v3M8.5 21h7"/>',
        'phone'    => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 7l8.5 6 8.5-6"/>',
        'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check'    => '<path d="M4.5 12.5l5 5 10-11"/>',
        'news'     => '<path d="M4 5h13v14H6a2 2 0 0 1-2-2zM17 9h3v8a2 2 0 0 1-2 2M7.5 9h6M7.5 12.5h6M7.5 16h4"/>',
    ];
    $d = isset($p[$name]) ? $p[$name] : '';
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}

/* ---------- Content types ---------- */

add_action('init', function () {
    $common = ['public' => true, 'show_in_rest' => true, 'menu_position' => 21];

    register_post_type('lf_event', $common + [
        'labels' => ['name' => 'Events', 'singular_name' => 'Event', 'add_new_item' => 'Add event', 'edit_item' => 'Edit event'],
        'menu_icon' => 'dashicons-calendar-alt', 'has_archive' => 'events',
        'rewrite' => ['slug' => 'events', 'with_front' => false],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);
    register_post_type('lf_press', $common + [
        'labels' => ['name' => 'Press Releases', 'singular_name' => 'Press release', 'add_new_item' => 'Add press release', 'edit_item' => 'Edit press release'],
        'menu_icon' => 'dashicons-megaphone', 'has_archive' => 'press',
        'rewrite' => ['slug' => 'press', 'with_front' => false],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);
    register_post_type('lf_team', [
        'labels' => ['name' => 'Office Bearers', 'singular_name' => 'Office bearer', 'add_new_item' => 'Add office bearer', 'edit_item' => 'Edit office bearer'],
        'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'menu_position' => 22, 'menu_icon' => 'dashicons-businessperson',
        'supports' => ['title', 'excerpt', 'thumbnail', 'page-attributes'],
    ]);
    register_post_type('lf_notice', [
        'labels' => ['name' => 'Member Notices', 'singular_name' => 'Notice', 'add_new_item' => 'Add notice', 'edit_item' => 'Edit notice'],
        'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'menu_position' => 22, 'menu_icon' => 'dashicons-lock',
        'supports' => ['title', 'editor'],
    ]);
    $private = ['public' => false, 'show_ui' => true, 'show_in_menu' => 'lf-portal', 'supports' => ['title', 'editor'], 'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true];
    register_post_type('lf_reg', $private + ['labels' => ['name' => 'Event Registrations', 'singular_name' => 'Registration', 'menu_name' => 'Event Registrations']]);
    register_post_type('lf_enquiry', $private + ['labels' => ['name' => 'Enquiries', 'singular_name' => 'Enquiry', 'menu_name' => 'Enquiries']]);

    if (!get_role('lf_member')) add_role('lf_member', 'Foundation Member', ['read' => true]);
});

/* ---------- Events ---------- */

function lf_event_meta($id) {
    return [
        'date'     => (string) get_post_meta($id, '_lf_date', true),
        'time'     => (string) get_post_meta($id, '_lf_time', true),
        'venue'    => (string) get_post_meta($id, '_lf_venue', true),
        'city'     => (string) get_post_meta($id, '_lf_city', true),
        'type'     => (string) get_post_meta($id, '_lf_type', true),
        'fee'      => (string) get_post_meta($id, '_lf_fee', true),
        'capacity' => (int) get_post_meta($id, '_lf_capacity', true),
        'members'  => (bool) get_post_meta($id, '_lf_members_only', true),
    ];
}

function lf_today() { return wp_date('Y-m-d'); }

function lf_events($when = 'upcoming', $count = 6) {
    $up = $when === 'upcoming';
    return new WP_Query([
        'post_type' => 'lf_event', 'post_status' => 'publish', 'posts_per_page' => $count, 'no_found_rows' => true,
        'meta_key' => '_lf_date', 'orderby' => 'meta_value', 'order' => $up ? 'ASC' : 'DESC',
        'meta_query' => [['key' => '_lf_date', 'value' => lf_today(), 'compare' => $up ? '>=' : '<', 'type' => 'DATE']],
    ]);
}

function lf_event_reg_count($event_id) {
    $q = new WP_Query(['post_type' => 'lf_reg', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_lf_event_id', 'meta_value' => (int) $event_id]);
    return (int) $q->found_posts;
}

function lf_event_has_reg($event_id, $email) {
    $q = new WP_Query(['post_type' => 'lf_reg', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true,
        'meta_query' => [['key' => '_lf_event_id', 'value' => (int) $event_id], ['key' => '_lf_email', 'value' => strtolower($email)]]]);
    return $q->have_posts();
}

function lf_event_card($p) {
    $m = lf_event_meta($p->ID);
    $ts = $m['date'] ? strtotime($m['date']) : 0;
    $out  = '<article class="ev">';
    $out .= '<div class="ev-date">' . ($ts ? '<b>' . esc_html(date_i18n('d', $ts)) . '</b><span>' . esc_html(date_i18n('M Y', $ts)) . '</span>' : '<span>Date to be announced</span>') . '</div>';
    $out .= '<div class="ev-body">';
    if ($m['type']) $out .= '<span class="tag">' . esc_html($m['type']) . '</span>';
    if ($m['members']) $out .= '<span class="tag tag-gold">Members only</span>';
    $out .= '<h3><a href="' . esc_url(get_permalink($p)) . '">' . esc_html(get_the_title($p)) . '</a></h3>';
    $bits = array_filter([$m['time'], trim($m['venue'] . ($m['city'] ? ', ' . $m['city'] : ''), ', ')]);
    if ($bits) $out .= '<p class="ev-meta">' . esc_html(implode(' · ', $bits)) . '</p>';
    $out .= '</div><a class="ev-go" href="' . esc_url(get_permalink($p)) . '">Details ' . lf_icon('arrow') . '</a></article>';
    return $out;
}

/* ---------- Members ---------- */

function lf_member_status($user_id = 0) {
    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) return '';
    $s = get_user_meta($user_id, 'lf_status', true);
    return in_array($s, ['pending', 'approved', 'rejected', 'lapsed'], true) ? $s : '';
}

function lf_is_member($user_id = 0) { return lf_member_status($user_id) === 'approved'; }

function lf_member_fields() {
    return ['phone', 'enrolment_no', 'enrolment_year', 'member_type', 'court', 'areas', 'locality', 'firm', 'office_address', 'bio', 'in_directory'];
}

function lf_member($user_id) {
    $u = get_userdata($user_id);
    if (!$u) return null;
    $d = ['id' => $u->ID, 'name' => $u->display_name, 'email' => $u->user_email, 'status' => lf_member_status($u->ID),
          'member_id' => (string) get_user_meta($u->ID, 'lf_member_id', true), 'valid_until' => (string) get_user_meta($u->ID, 'lf_valid_until', true),
          'fee_paid' => (bool) get_user_meta($u->ID, 'lf_fee_paid', true), 'joined' => $u->user_registered];
    foreach (lf_member_fields() as $f) $d[$f] = get_user_meta($u->ID, 'lf_' . $f, true);
    $d['areas'] = is_array($d['areas']) ? $d['areas'] : [];
    return $d;
}

function lf_set_member_status($user_id, $status) {
    $old = lf_member_status($user_id);
    update_user_meta($user_id, 'lf_status', $status);
    if ($status === 'approved') {
        if (!get_user_meta($user_id, 'lf_member_id', true)) {
            update_user_meta($user_id, 'lf_member_id', 'LF-' . wp_date('Y') . '-' . str_pad((string) $user_id, 4, '0', STR_PAD_LEFT));
        }
        if ($old !== 'approved') {
            $u = get_userdata($user_id);
            $type = get_user_meta($user_id, 'lf_member_type', true);
            if (!get_user_meta($user_id, 'lf_valid_until', true) && $type !== 'life') {
                update_user_meta($user_id, 'lf_valid_until', wp_date('Y-m-d', strtotime('+1 year')));
            }
            wp_mail($u->user_email, 'Your membership of ' . lf_org() . ' is approved',
                "Dear " . $u->display_name . ",\n\nYour membership application has been verified and approved.\n\nMembership ID: " . get_user_meta($user_id, 'lf_member_id', true) .
                "\n\nSign in to see your digital membership card, register for events and find fellow members:\n" . lf_url('login') . "\n\n" . lf_org() . "\n" . lf_opt('org_region'));
        }
    }
}

/** Members never see the WordPress dashboard. */
add_filter('show_admin_bar', function ($show) {
    return current_user_can('edit_posts') ? $show : false;
});
add_action('admin_init', function () {
    if (wp_doing_ajax() || !is_user_logged_in()) return;
    $u = wp_get_current_user();
    if (in_array('lf_member', (array) $u->roles, true) && !current_user_can('edit_posts')) {
        wp_safe_redirect(lf_url('dashboard'));
        exit;
    }
});

/* ---------- Navigation ---------- */

function lf_nav_items() {
    return [
        ['About', lf_url('about'), [['About the Foundation', lf_url('about')], ['Office Bearers & Committees', lf_url('leadership')], ['Code of Conduct', lf_url('conduct')]]],
        ['Membership', lf_url('membership'), [['Categories & Benefits', lf_url('membership')], ['Apply Online', lf_url('join')], ['Member Directory', lf_url('directory')]]],
        ['Events', lf_url('events'), []],
        ['Media', lf_url('press'), [['Press Releases', lf_url('press')], ['News & Articles', lf_url('news')], ['Gallery', lf_url('gallery')]]],
        ['Legal Aid', lf_url('legal-aid'), []],
        ['Resources', lf_url('resources'), []],
        ['Contact', lf_url('contact'), []],
    ];
}

function lf_fallback_menu() {
    echo '<ul class="menu">';
    foreach (lf_nav_items() as $item) {
        echo '<li' . ($item[2] ? ' class="has-sub"' : '') . '><a href="' . esc_url($item[1]) . '">' . esc_html($item[0]) . '</a>';
        if ($item[2]) {
            echo '<ul class="sub-menu">';
            foreach ($item[2] as $s) echo '<li><a href="' . esc_url($s[1]) . '">' . esc_html($s[0]) . '</a></li>';
            echo '</ul>';
        }
        echo '</li>';
    }
    echo '</ul>';
}

/** Page hero used by inner pages. */
function lf_page_head($title, $lede = '', $crumb = '') {
    echo '<section class="page-head"><div class="wrap">';
    echo '<p class="crumb"><a href="' . esc_url(home_url('/')) . '">Home</a> / ' . esc_html($crumb ?: $title) . '</p>';
    echo '<h1>' . esc_html($title) . '</h1>';
    if ($lede) echo '<p class="lede">' . esc_html($lede) . '</p>';
    echo '</div></section>';
}

function lf_post_card($p) {
    $type = get_post_type($p) === 'lf_press' ? 'Press release' : 'News';
    $out  = '<article class="card post-card">';
    if (has_post_thumbnail($p)) $out .= '<a class="thumb" href="' . esc_url(get_permalink($p)) . '">' . get_the_post_thumbnail($p, 'medium_large', ['loading' => 'lazy']) . '</a>';
    $out .= '<p class="meta"><span class="tag">' . esc_html($type) . '</span> ' . esc_html(get_the_date('j F Y', $p)) . '</p>';
    $out .= '<h3><a href="' . esc_url(get_permalink($p)) . '">' . esc_html(get_the_title($p)) . '</a></h3>';
    $out .= '<p>' . esc_html(wp_trim_words(get_the_excerpt($p), 26)) . '</p></article>';
    return $out;
}
