<?php
/**
 * Front-end form handling: membership application, sign-in, profile, event registration, enquiries.
 */
if (!defined('ABSPATH')) exit;

$GLOBALS['lf_flash'] = [];

function lf_flash($form, $type, $msg) { $GLOBALS['lf_flash'][$form] = [$type, $msg]; }

function lf_flash_html($form) {
    $codes = [
        'applied'  => 'Thank you. Your application has been received and is awaiting verification. You are signed in and can follow its progress here.',
        'saved'    => 'Your profile has been updated.',
        'out'      => 'You have been signed out.',
        'reg'      => 'You are registered for this event. A confirmation has been sent to your email address.',
        'sent'     => 'Thank you. Your message has been received and our office will get back to you.',
        'aid'      => 'Thank you. Your request has been received. A volunteer from the legal aid desk will contact you.',
    ];
    $out = '';
    if (isset($_GET['lf_msg'], $codes[$_GET['lf_msg']]) && (!isset($_GET['lf_form']) || $_GET['lf_form'] === $form)) {
        $out .= '<div class="notice ok" role="status">' . esc_html($codes[sanitize_key($_GET['lf_msg'])]) . '</div>';
    }
    if (isset($GLOBALS['lf_flash'][$form])) {
        list($type, $msg) = $GLOBALS['lf_flash'][$form];
        $out .= '<div class="notice ' . esc_attr($type) . '" role="alert">' . wp_kses($msg, ['br' => [], 'a' => ['href' => []]]) . '</div>';
    }
    return $out;
}

function lf_post($key, $default = '') {
    return isset($_POST[$key]) && !is_array($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
}

function lf_form_open($action, $class = 'form') {
    return '<form method="post" class="' . esc_attr($class) . '" action="#form-' . esc_attr($action) . '" id="form-' . esc_attr($action) . '" novalidate>'
        . wp_nonce_field('lf_' . $action, '_lf_nonce', true, false)
        . '<input type="hidden" name="lf_action" value="' . esc_attr($action) . '">'
        . '<p class="hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="lf_website" tabindex="-1" autocomplete="off"></label></p>';
}

/** Limit repeated submissions from one address. */
function lf_throttled($bucket, $max = 8, $window = 600) {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'x';
    $key = 'lf_t_' . md5($bucket . '|' . $ip);
    $n = (int) get_transient($key);
    if ($n >= $max) return true;
    set_transient($key, $n + 1, $window);
    return false;
}

function lf_valid_phone($p) { return (bool) preg_match('/^[0-9+\-\s()]{8,18}$/', $p); }

add_action('template_redirect', function () {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['lf_action'])) return;
    $action = sanitize_key($_POST['lf_action']);
    $handlers = ['join' => 'lf_handle_join', 'login' => 'lf_handle_login', 'profile' => 'lf_handle_profile', 'event' => 'lf_handle_event', 'contact' => 'lf_handle_enquiry', 'aid' => 'lf_handle_enquiry'];
    if (!isset($handlers[$action])) return;
    nocache_headers();
    if (!isset($_POST['_lf_nonce']) || !wp_verify_nonce($_POST['_lf_nonce'], 'lf_' . $action)) {
        lf_flash($action, 'error', 'Your session has expired. Please reload the page and try again.');
        return;
    }
    if (!empty($_POST['lf_website'])) return; // honeypot
    call_user_func($handlers[$action], $action);
});

/* ---------- Membership application ---------- */

function lf_handle_join($action) {
    if (is_user_logged_in()) { lf_flash($action, 'error', 'You are already signed in.'); return; }
    if (lf_throttled('join', 5)) { lf_flash($action, 'error', 'Too many attempts. Please try again in a few minutes.'); return; }

    $name = lf_post('full_name');
    $email = sanitize_email(wp_unslash(isset($_POST['email']) ? $_POST['email'] : ''));
    $phone = lf_post('phone');
    $pass = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';
    $type = sanitize_key(lf_post('member_type'));
    $enrol = strtoupper(lf_post('enrolment_no'));
    $year = (int) lf_post('enrolment_year');
    $court = lf_post('court');
    $locality = lf_post('locality');
    $areas = isset($_POST['areas']) && is_array($_POST['areas']) ? array_values(array_intersect(lf_practice_areas(), array_map('sanitize_text_field', wp_unslash($_POST['areas'])))) : [];

    $err = [];
    if (mb_strlen($name) < 3) $err[] = 'Enter your full name.';
    if (!is_email($email)) $err[] = 'Enter a valid email address.';
    elseif (email_exists($email)) $err[] = 'An account with this email address already exists. <a href="' . esc_url(lf_url('login')) . '">Sign in</a> instead.';
    if (!lf_valid_phone($phone)) $err[] = 'Enter a valid mobile number.';
    if (strlen($pass) < 8) $err[] = 'Choose a password of at least 8 characters.';
    if (!isset(lf_member_types()[$type])) $err[] = 'Select a membership category.';
    if ($type === 'student') {
        if (mb_strlen(lf_post('firm')) < 3) $err[] = 'Enter the name of your law college.';
    } else {
        if (!preg_match('/^[A-Z0-9\/\-\s]{5,25}$/', $enrol)) $err[] = 'Enter your Bar Council enrolment number, for example MAH/1234/2015.';
        if ($year < 1950 || $year > (int) wp_date('Y')) $err[] = 'Enter the year of your enrolment.';
        if (!in_array($court, lf_courts(), true)) $err[] = 'Select the court where you mainly practise.';
    }
    if (!in_array($locality, lf_areas(), true)) $err[] = 'Select your locality.';
    if (empty($_POST['declare'])) $err[] = 'Please confirm the declaration.';
    if ($err) { lf_flash($action, 'error', implode('<br>', $err)); return; }

    $uid = wp_insert_user(['user_login' => $email, 'user_email' => $email, 'user_pass' => $pass, 'display_name' => $name, 'first_name' => $name, 'role' => 'lf_member']);
    if (is_wp_error($uid)) { lf_flash($action, 'error', 'We could not create your account. Please try again or contact the office.'); return; }

    $meta = ['phone' => $phone, 'enrolment_no' => $enrol, 'enrolment_year' => $year ?: '', 'member_type' => $type, 'court' => $court, 'areas' => $areas,
             'locality' => $locality, 'firm' => lf_post('firm'), 'office_address' => sanitize_textarea_field(wp_unslash(isset($_POST['office_address']) ? $_POST['office_address'] : '')),
             'bio' => '', 'in_directory' => empty($_POST['in_directory']) ? '' : 1];
    foreach ($meta as $k => $v) update_user_meta($uid, 'lf_' . $k, $v);
    update_user_meta($uid, 'lf_status', 'pending');

    wp_mail(get_option('admin_email'), 'New membership application: ' . $name,
        "A new membership application is waiting for verification.\n\nName: $name\nEmail: $email\nPhone: $phone\nCategory: " . lf_member_types()[$type][0] . "\nEnrolment: $enrol ($year)\nCourt: $court\n\nReview: " . admin_url('admin.php?page=lf-portal&status=pending'));
    wp_mail($email, 'We have received your application: ' . lf_org(),
        "Dear $name,\n\nThank you for applying for membership of " . lf_org() . ".\n\nOur office will verify your enrolment details. You will receive an email once your membership is approved. Meanwhile you can sign in and follow your application:\n" . lf_url('dashboard') . "\n\n" . lf_org());

    wp_set_current_user($uid);
    wp_set_auth_cookie($uid, true);
    wp_safe_redirect(add_query_arg('lf_msg', 'applied', lf_url('dashboard')));
    exit;
}

/* ---------- Sign in ---------- */

function lf_handle_login($action) {
    if (lf_throttled('login', 10)) { lf_flash($action, 'error', 'Too many attempts. Please try again in a few minutes.'); return; }
    $login = lf_post('email');
    $pass = isset($_POST['password']) ? (string) wp_unslash($_POST['password']) : '';
    if (is_email($login)) { $u = get_user_by('email', $login); if ($u) $login = $u->user_login; }
    $user = wp_signon(['user_login' => $login, 'user_password' => $pass, 'remember' => !empty($_POST['remember'])], is_ssl());
    if (is_wp_error($user)) { lf_flash($action, 'error', 'The email address or password is not correct.'); return; }
    $to = user_can($user, 'edit_posts') && !lf_member_status($user->ID) ? admin_url() : lf_url('dashboard');
    wp_safe_redirect($to);
    exit;
}

/* ---------- Profile ---------- */

function lf_handle_profile($action) {
    $uid = get_current_user_id();
    if (!$uid || !lf_member_status($uid)) return;
    $phone = lf_post('phone');
    if (!lf_valid_phone($phone)) { lf_flash($action, 'error', 'Enter a valid mobile number.'); return; }
    $areas = isset($_POST['areas']) && is_array($_POST['areas']) ? array_values(array_intersect(lf_practice_areas(), array_map('sanitize_text_field', wp_unslash($_POST['areas'])))) : [];
    update_user_meta($uid, 'lf_phone', $phone);
    update_user_meta($uid, 'lf_areas', $areas);
    if (in_array(lf_post('court'), lf_courts(), true)) update_user_meta($uid, 'lf_court', lf_post('court'));
    if (in_array(lf_post('locality'), lf_areas(), true)) update_user_meta($uid, 'lf_locality', lf_post('locality'));
    update_user_meta($uid, 'lf_firm', lf_post('firm'));
    update_user_meta($uid, 'lf_office_address', sanitize_textarea_field(wp_unslash(isset($_POST['office_address']) ? $_POST['office_address'] : '')));
    update_user_meta($uid, 'lf_bio', mb_substr(sanitize_textarea_field(wp_unslash(isset($_POST['bio']) ? $_POST['bio'] : '')), 0, 600));
    update_user_meta($uid, 'lf_in_directory', empty($_POST['in_directory']) ? '' : 1);
    wp_safe_redirect(add_query_arg(['lf_msg' => 'saved', 'lf_form' => 'profile'], lf_url('dashboard')) . '#form-profile');
    exit;
}

/* ---------- Event registration ---------- */

function lf_handle_event($action) {
    $event_id = (int) lf_post('event_id');
    $event = get_post($event_id);
    if (!$event || $event->post_type !== 'lf_event' || $event->post_status !== 'publish') return;
    if (lf_throttled('event', 10)) { lf_flash($action, 'error', 'Too many attempts. Please try again in a few minutes.'); return; }
    $m = lf_event_meta($event_id);
    if ($m['date'] && $m['date'] < lf_today()) { lf_flash($action, 'error', 'Registration for this event has closed.'); return; }

    $uid = get_current_user_id();
    if ($uid && lf_member_status($uid)) {
        $me = lf_member($uid);
        $name = $me['name']; $email = $me['email']; $phone = $me['phone'];
    } else {
        $name = lf_post('full_name');
        $email = sanitize_email(wp_unslash(isset($_POST['email']) ? $_POST['email'] : ''));
        $phone = lf_post('phone');
    }
    if ($m['members'] && !lf_is_member($uid)) { lf_flash($action, 'error', 'This event is open to approved members only.'); return; }
    if (mb_strlen($name) < 3 || !is_email($email) || !lf_valid_phone($phone)) { lf_flash($action, 'error', 'Enter your name, a valid email address and a mobile number.'); return; }
    if (lf_event_has_reg($event_id, $email)) { lf_flash($action, 'error', 'This email address is already registered for the event.'); return; }
    if ($m['capacity'] && lf_event_reg_count($event_id) >= $m['capacity']) { lf_flash($action, 'error', 'Sorry, all seats for this event are taken.'); return; }

    $rid = wp_insert_post(['post_type' => 'lf_reg', 'post_status' => 'publish', 'post_title' => $name, 'post_content' => 'Registered for: ' . $event->post_title . ($uid && lf_is_member($uid) ? "\nMember ID: " . get_user_meta($uid, 'lf_member_id', true) : "\nGuest")]);
    if (!$rid || is_wp_error($rid)) { lf_flash($action, 'error', 'We could not save your registration. Please try again.'); return; }
    update_post_meta($rid, '_lf_event_id', $event_id);
    update_post_meta($rid, '_lf_email', strtolower($email));
    update_post_meta($rid, '_lf_phone', $phone);
    update_post_meta($rid, '_lf_user_id', $uid);

    $when = $m['date'] ? date_i18n('l, j F Y', strtotime($m['date'])) . ($m['time'] ? ', ' . $m['time'] : '') : 'To be announced';
    wp_mail($email, 'Registration confirmed: ' . $event->post_title,
        "Dear $name,\n\nYou are registered for:\n\n" . $event->post_title . "\nWhen: $when\nWhere: " . trim($m['venue'] . ', ' . $m['city'], ', ') . ($m['fee'] ? "\nFee: " . $m['fee'] : '') . "\n\n" . get_permalink($event) . "\n\n" . lf_org());
    wp_safe_redirect(add_query_arg('lf_msg', 'reg', get_permalink($event)) . '#register');
    exit;
}

/* ---------- Contact and legal aid enquiries ---------- */

function lf_handle_enquiry($action) {
    if (lf_throttled('enquiry', 5)) { lf_flash($action, 'error', 'Too many attempts. Please try again in a few minutes.'); return; }
    $name = lf_post('full_name');
    $email = sanitize_email(wp_unslash(isset($_POST['email']) ? $_POST['email'] : ''));
    $phone = lf_post('phone');
    $msg = sanitize_textarea_field(wp_unslash(isset($_POST['message']) ? $_POST['message'] : ''));
    $err = [];
    if (mb_strlen($name) < 3) $err[] = 'Enter your name.';
    if (!lf_valid_phone($phone)) $err[] = 'Enter a valid mobile number.';
    if ($action === 'contact' && !is_email($email)) $err[] = 'Enter a valid email address.';
    if ($email && !is_email($email)) $err[] = 'The email address does not look right.';
    if (mb_strlen($msg) < 10) $err[] = 'Tell us a little more in the message.';
    if ($action === 'aid' && empty($_POST['consent'])) $err[] = 'Please confirm that we may contact you about this request.';
    if ($err) { lf_flash($action, 'error', implode('<br>', array_unique($err))); return; }

    $kind = $action === 'aid' ? 'Legal aid request' : 'Contact enquiry';
    $extra = $action === 'aid'
        ? "Matter type: " . lf_post('matter') . "\nLocality: " . lf_post('locality') . "\n"
        : "Subject: " . lf_post('subject') . "\n";
    $body = $extra . "Phone: $phone\nEmail: $email\n\n" . mb_substr($msg, 0, 4000);
    $id = wp_insert_post(['post_type' => 'lf_enquiry', 'post_status' => 'private', 'post_title' => $name, 'post_content' => $body]);
    if ($id && !is_wp_error($id)) {
        update_post_meta($id, '_lf_kind', $kind);
        update_post_meta($id, '_lf_email', $email);
        update_post_meta($id, '_lf_phone', $phone);
    }
    wp_mail(get_option('admin_email'), $kind . ' from ' . $name, $body, $email ? ['Reply-To: ' . $email] : []);
    wp_safe_redirect(add_query_arg('lf_msg', $action === 'aid' ? 'aid' : 'sent', lf_url($action === 'aid' ? 'legal-aid' : 'contact')) . '#form-' . $action);
    exit;
}

/* ---------- Sign out link ---------- */

function lf_logout_url() { return wp_logout_url(add_query_arg('lf_msg', 'out', lf_url('login'))); }

function lf_lostpassword_url() { return wp_lostpassword_url(lf_url('login')); }
