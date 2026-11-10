<?php
/**
 * wp-admin: Foundation menu (members, settings), event details box, list columns.
 */
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
    $pending = count(get_users(['meta_key' => 'lf_status', 'meta_value' => 'pending', 'fields' => 'ID']));
    $bubble = $pending ? ' <span class="awaiting-mod">' . $pending . '</span>' : '';
    add_menu_page('Foundation Portal', 'Foundation' . $bubble, 'manage_options', 'lf-portal', 'lf_admin_members', 'dashicons-shield', 20);
    add_submenu_page('lf-portal', 'Members', 'Members' . $bubble, 'manage_options', 'lf-portal', 'lf_admin_members');
    add_submenu_page('lf-portal', 'Portal Settings', 'Settings', 'manage_options', 'lf-settings', 'lf_admin_settings');
});

/* ---------- Members screen ---------- */

function lf_admin_members() {
    if (!current_user_can('manage_options')) return;
    $filter = isset($_GET['status']) ? sanitize_key($_GET['status']) : '';
    $args = ['meta_key' => 'lf_status', 'meta_compare' => 'EXISTS', 'orderby' => 'registered', 'order' => 'DESC', 'number' => 500];
    if ($filter) { $args['meta_value'] = $filter; unset($args['meta_compare']); }
    $users = get_users($args);
    $types = lf_member_types();
    $base = admin_url('admin.php?page=lf-portal');
    echo '<div class="wrap"><h1 class="wp-heading-inline">Members</h1> ';
    echo '<a class="page-title-action" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=lf_export_members'), 'lf_export')) . '">Export CSV</a><hr class="wp-header-end">';
    if (isset($_GET['updated'])) echo '<div class="notice notice-success is-dismissible"><p>Member updated.</p></div>';
    echo '<ul class="subsubsub">';
    $links = ['' => 'All', 'pending' => 'Pending verification', 'approved' => 'Approved', 'rejected' => 'Rejected', 'lapsed' => 'Lapsed'];
    $i = 0;
    foreach ($links as $k => $label) {
        echo '<li><a href="' . esc_url($k ? add_query_arg('status', $k, $base) : $base) . '"' . ($filter === $k ? ' class="current"' : '') . '>' . esc_html($label) . '</a>' . (++$i < count($links) ? ' | ' : '') . '</li>';
    }
    echo '</ul><table class="wp-list-table widefat striped"><thead><tr><th>Name</th><th>Enrolment</th><th>Category</th><th>Court / Area</th><th>Contact</th><th>Fee</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
    if (!$users) echo '<tr><td colspan="8">No members found.</td></tr>';
    foreach ($users as $u) {
        $m = lf_member($u->ID);
        $act = function ($status, $label) use ($u) {
            return '<a href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=lf_member_status&user=' . $u->ID . '&to=' . $status), 'lf_member_' . $u->ID)) . '">' . esc_html($label) . '</a>';
        };
        $actions = [];
        if ($m['status'] !== 'approved') $actions[] = $act('approved', 'Approve');
        if ($m['status'] !== 'rejected') $actions[] = $act('rejected', 'Reject');
        if ($m['status'] === 'approved') $actions[] = $act('lapsed', 'Mark lapsed');
        $actions[] = $act($m['fee_paid'] ? 'fee_unpaid' : 'fee_paid', $m['fee_paid'] ? 'Mark fee unpaid' : 'Mark fee paid');
        $actions[] = '<a href="' . esc_url(get_edit_user_link($u->ID)) . '">Edit</a>';
        echo '<tr><td><strong>' . esc_html($m['name']) . '</strong><br><small>' . esc_html($m['member_id'] ?: 'No ID yet') . '</small></td>';
        echo '<td>' . esc_html($m['enrolment_no']) . '<br><small>' . esc_html($m['enrolment_year']) . '</small></td>';
        echo '<td>' . esc_html(isset($types[$m['member_type']]) ? $types[$m['member_type']][0] : '') . '</td>';
        echo '<td>' . esc_html($m['court']) . '<br><small>' . esc_html($m['locality']) . '</small></td>';
        echo '<td><a href="mailto:' . esc_attr($m['email']) . '">' . esc_html($m['email']) . '</a><br>' . esc_html($m['phone']) . '</td>';
        echo '<td>' . ($m['fee_paid'] ? 'Paid' : 'Not paid') . ($m['valid_until'] ? '<br><small>Valid to ' . esc_html($m['valid_until']) . '</small>' : '') . '</td>';
        echo '<td><strong>' . esc_html(ucfirst($m['status'])) . '</strong></td><td>' . implode(' | ', $actions) . '</td></tr>';
    }
    echo '</tbody></table></div>';
}

add_action('admin_post_lf_member_status', function () {
    $uid = isset($_GET['user']) ? (int) $_GET['user'] : 0;
    if (!current_user_can('manage_options') || !$uid) wp_die('Not allowed.');
    check_admin_referer('lf_member_' . $uid);
    $to = isset($_GET['to']) ? sanitize_key($_GET['to']) : '';
    if ($to === 'fee_paid') update_user_meta($uid, 'lf_fee_paid', 1);
    elseif ($to === 'fee_unpaid') delete_user_meta($uid, 'lf_fee_paid');
    elseif (in_array($to, ['approved', 'rejected', 'lapsed', 'pending'], true)) lf_set_member_status($uid, $to);
    wp_safe_redirect(admin_url('admin.php?page=lf-portal&updated=1'));
    exit;
});

add_action('admin_post_lf_export_members', function () {
    if (!current_user_can('manage_options')) wp_die('Not allowed.');
    check_admin_referer('lf_export');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=members-' . wp_date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Member ID', 'Name', 'Email', 'Phone', 'Enrolment no.', 'Enrolment year', 'Category', 'Court', 'Practice areas', 'Locality', 'Firm', 'Status', 'Fee paid', 'Valid until', 'Applied on']);
    foreach (get_users(['meta_key' => 'lf_status', 'meta_compare' => 'EXISTS']) as $u) {
        $m = lf_member($u->ID);
        $row = [$m['member_id'], $m['name'], $m['email'], $m['phone'], $m['enrolment_no'], $m['enrolment_year'], $m['member_type'], $m['court'], implode('; ', $m['areas']), $m['locality'], $m['firm'], $m['status'], $m['fee_paid'] ? 'yes' : 'no', $m['valid_until'], $m['joined']];
        // Stop spreadsheet apps treating member-entered text as a formula.
        fputcsv($out, array_map(function ($v) { $v = (string) $v; return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v; }, $row));
    }
    fclose($out);
    exit;
});

/* ---------- Membership section on the user edit screen ---------- */

function lf_admin_user_fields($user) {
    if (!current_user_can('manage_options')) return;
    $m = lf_member($user->ID);
    if (!$m['status']) return;
    wp_nonce_field('lf_user_' . $user->ID, 'lf_user_nonce');
    echo '<h2>Foundation membership</h2><table class="form-table" role="presentation">';
    echo '<tr><th><label for="lf_status">Status</label></th><td><select name="lf_status" id="lf_status">';
    foreach (['pending', 'approved', 'rejected', 'lapsed'] as $s) echo '<option value="' . $s . '"' . selected($m['status'], $s, false) . '>' . ucfirst($s) . '</option>';
    echo '</select></td></tr>';
    echo '<tr><th>Membership ID</th><td>' . esc_html($m['member_id'] ?: 'Issued on approval') . '</td></tr>';
    echo '<tr><th><label for="lf_fee_paid">Fee received</label></th><td><input type="checkbox" name="lf_fee_paid" id="lf_fee_paid" value="1"' . checked($m['fee_paid'], true, false) . '></td></tr>';
    echo '<tr><th><label for="lf_valid_until">Valid until</label></th><td><input type="date" name="lf_valid_until" id="lf_valid_until" value="' . esc_attr($m['valid_until']) . '"></td></tr>';
    foreach (['phone' => 'Phone', 'enrolment_no' => 'Enrolment no.', 'enrolment_year' => 'Enrolment year', 'court' => 'Principal court', 'locality' => 'Locality', 'firm' => 'Firm / chamber'] as $k => $label) {
        echo '<tr><th><label for="lf_' . $k . '">' . esc_html($label) . '</label></th><td><input type="text" class="regular-text" name="lf_' . $k . '" id="lf_' . $k . '" value="' . esc_attr($m[$k]) . '"></td></tr>';
    }
    echo '<tr><th>Practice areas</th><td>' . esc_html(implode(', ', $m['areas'])) . '</td></tr></table>';
}
add_action('show_user_profile', 'lf_admin_user_fields');
add_action('edit_user_profile', 'lf_admin_user_fields');

function lf_admin_user_save($user_id) {
    if (!current_user_can('manage_options') || !isset($_POST['lf_user_nonce']) || !wp_verify_nonce($_POST['lf_user_nonce'], 'lf_user_' . $user_id)) return;
    foreach (['phone', 'enrolment_no', 'enrolment_year', 'court', 'locality', 'firm'] as $k) {
        if (isset($_POST['lf_' . $k])) update_user_meta($user_id, 'lf_' . $k, sanitize_text_field(wp_unslash($_POST['lf_' . $k])));
    }
    if (!empty($_POST['lf_fee_paid'])) update_user_meta($user_id, 'lf_fee_paid', 1); else delete_user_meta($user_id, 'lf_fee_paid');
    $valid = isset($_POST['lf_valid_until']) ? sanitize_text_field(wp_unslash($_POST['lf_valid_until'])) : '';
    update_user_meta($user_id, 'lf_valid_until', preg_match('/^\d{4}-\d{2}-\d{2}$/', $valid) ? $valid : '');
    $status = isset($_POST['lf_status']) ? sanitize_key($_POST['lf_status']) : '';
    if (in_array($status, ['pending', 'approved', 'rejected', 'lapsed'], true)) lf_set_member_status($user_id, $status);
}
add_action('personal_options_update', 'lf_admin_user_save');
add_action('edit_user_profile_update', 'lf_admin_user_save');

/* ---------- Settings ---------- */

function lf_settings_fields() {
    return [
        'Organisation' => ['org_name' => 'Name', 'org_region' => 'Region line', 'tagline' => 'Tagline', 'reg_no' => 'Registration line (footer)'],
        'Contact' => ['phone' => 'Office phone', 'mobile' => 'Mobile / WhatsApp', 'email' => 'Email', 'address_1' => 'Mumbai office address', 'address_2' => 'Navi Mumbai office address', 'hours' => 'Office hours'],
        'Membership fees (₹)' => ['fee_advocate' => 'Advocate Member', 'fee_junior' => 'Junior Advocate', 'fee_student' => 'Law Student Associate', 'fee_life' => 'Life Member', 'fee_firm' => 'Law Firm / Chamber'],
        'Fee payment' => ['upi' => 'UPI ID', 'bank' => 'Bank transfer details'],
        'Social links' => ['facebook' => 'Facebook URL', 'linkedin' => 'LinkedIn URL', 'instagram' => 'Instagram URL', 'youtube' => 'YouTube URL', 'x' => 'X URL'],
    ];
}

add_action('admin_init', function () {
    register_setting('lf_settings', 'lf_settings', ['sanitize_callback' => function ($in) {
        $out = [];
        foreach (lf_defaults() as $k => $def) {
            if (!isset($in[$k])) continue;
            $out[$k] = $k === 'bank' ? sanitize_textarea_field($in[$k]) : sanitize_text_field($in[$k]);
        }
        return $out;
    }]);
});

function lf_admin_settings() {
    if (!current_user_can('manage_options')) return;
    $saved = wp_parse_args((array) get_option('lf_settings', []), lf_defaults());
    echo '<div class="wrap"><h1>Portal Settings</h1><p>These details appear in the header, footer, contact page, membership page and emails. The values supplied with the theme are samples: replace them with your own.</p><form method="post" action="options.php">';
    settings_fields('lf_settings');
    foreach (lf_settings_fields() as $group => $fields) {
        echo '<h2>' . esc_html($group) . '</h2><table class="form-table" role="presentation">';
        foreach ($fields as $k => $label) {
            echo '<tr><th><label for="lf_' . $k . '">' . esc_html($label) . '</label></th><td>';
            if ($k === 'bank') echo '<textarea class="large-text" rows="4" name="lf_settings[bank]" id="lf_bank">' . esc_textarea($saved[$k]) . '</textarea>';
            else echo '<input type="text" class="large-text" name="lf_settings[' . $k . ']" id="lf_' . $k . '" value="' . esc_attr($saved[$k]) . '">';
            echo '</td></tr>';
        }
        echo '</table>';
    }
    submit_button();
    echo '</form></div>';
}

/* ---------- Event details box ---------- */

add_action('add_meta_boxes', function () {
    add_meta_box('lf_event_details', 'Event details', 'lf_event_box', 'lf_event', 'normal', 'high');
    add_meta_box('lf_event_regs', 'Registrations', 'lf_event_regs_box', 'lf_event', 'side');
});

function lf_event_box($post) {
    $m = lf_event_meta($post->ID);
    wp_nonce_field('lf_event_' . $post->ID, 'lf_event_nonce');
    $row = function ($k, $label, $type = 'text', $hint = '') use ($m) {
        echo '<p><label for="lf_ev_' . $k . '"><strong>' . esc_html($label) . '</strong></label><br><input type="' . $type . '" id="lf_ev_' . $k . '" name="lf_ev[' . $k . ']" value="' . esc_attr($m[$k] ?: '') . '" style="width:100%;max-width:420px">' . ($hint ? '<br><span class="description">' . esc_html($hint) . '</span>' : '') . '</p>';
    };
    $row('date', 'Date', 'date');
    $row('time', 'Time', 'text', 'For example: 5:30 pm to 7:30 pm');
    $row('venue', 'Venue');
    $row('city', 'City', 'text', 'Mumbai, Navi Mumbai or Online');
    $row('type', 'Type', 'text', 'Seminar, Workshop, Legal Aid Camp, Networking, Annual Meet');
    $row('fee', 'Fee', 'text', 'For example: Free for members, ₹300 for guests');
    $row('capacity', 'Seats', 'number', 'Leave empty for no limit');
    echo '<p><label><input type="checkbox" name="lf_ev[members]" value="1"' . checked($m['members'], true, false) . '> Only approved members can register</label></p>';
}

function lf_event_regs_box($post) {
    $n = lf_event_reg_count($post->ID);
    echo '<p><strong>' . (int) $n . '</strong> registered.</p><p><a href="' . esc_url(admin_url('edit.php?post_type=lf_reg&lf_event=' . $post->ID)) . '">View registrations</a></p>';
}

add_action('save_post_lf_event', function ($post_id) {
    if (!isset($_POST['lf_event_nonce']) || !wp_verify_nonce($_POST['lf_event_nonce'], 'lf_event_' . $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    $in = isset($_POST['lf_ev']) ? (array) wp_unslash($_POST['lf_ev']) : [];
    foreach (['date', 'time', 'venue', 'city', 'type', 'fee'] as $k) {
        update_post_meta($post_id, '_lf_' . $k, isset($in[$k]) ? sanitize_text_field($in[$k]) : '');
    }
    update_post_meta($post_id, '_lf_capacity', isset($in['capacity']) ? absint($in['capacity']) : 0);
    update_post_meta($post_id, '_lf_members_only', empty($in['members']) ? '' : 1);
});

/* ---------- List columns ---------- */

add_filter('manage_lf_event_posts_columns', function ($c) {
    return ['cb' => $c['cb'], 'title' => 'Event', 'lf_date' => 'Date', 'lf_where' => 'Venue', 'lf_regs' => 'Registered'];
});
add_action('manage_lf_event_posts_custom_column', function ($col, $id) {
    $m = lf_event_meta($id);
    if ($col === 'lf_date') echo esc_html($m['date'] ? date_i18n('j M Y', strtotime($m['date'])) . ' ' . $m['time'] : '—');
    if ($col === 'lf_where') echo esc_html(trim($m['venue'] . ', ' . $m['city'], ', '));
    if ($col === 'lf_regs') echo (int) lf_event_reg_count($id) . ($m['capacity'] ? ' / ' . (int) $m['capacity'] : '');
}, 10, 2);

add_filter('manage_lf_reg_posts_columns', function ($c) {
    return ['cb' => $c['cb'], 'title' => 'Attendee', 'lf_event' => 'Event', 'lf_contact' => 'Contact', 'date' => 'Registered on'];
});
add_action('manage_lf_reg_posts_custom_column', function ($col, $id) {
    if ($col === 'lf_event') { $e = (int) get_post_meta($id, '_lf_event_id', true); echo $e ? esc_html(get_the_title($e)) : '—'; }
    if ($col === 'lf_contact') echo esc_html(get_post_meta($id, '_lf_email', true) . ' · ' . get_post_meta($id, '_lf_phone', true));
}, 10, 2);

add_filter('manage_lf_enquiry_posts_columns', function ($c) {
    return ['cb' => $c['cb'], 'title' => 'From', 'lf_kind' => 'Type', 'lf_contact' => 'Contact', 'date' => 'Received'];
});
add_action('manage_lf_enquiry_posts_custom_column', function ($col, $id) {
    if ($col === 'lf_kind') echo esc_html(get_post_meta($id, '_lf_kind', true));
    if ($col === 'lf_contact') echo esc_html(get_post_meta($id, '_lf_email', true) . ' · ' . get_post_meta($id, '_lf_phone', true));
}, 10, 2);

add_action('pre_get_posts', function ($q) {
    if (is_admin() && $q->is_main_query() && $q->get('post_type') === 'lf_reg' && !empty($_GET['lf_event'])) {
        $q->set('meta_key', '_lf_event_id');
        $q->set('meta_value', (int) $_GET['lf_event']);
    }
});
