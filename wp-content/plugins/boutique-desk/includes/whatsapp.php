<?php
if (!defined('ABSPATH')) exit;

/*
 * Automatic WhatsApp messages through Meta's WhatsApp Cloud API (approved templates).
 * - Order received  -> asks the customer to reply YES
 * - Order shipped   -> courier and tracking number
 * - Order delivered -> thank you
 * Messages are queued with Action Scheduler so checkout and the desk never wait on Meta.
 * A webhook records replies on the order and can confirm it when the customer says YES.
 */

function bdesk_wa_on() {
    $s = bdesk_settings();
    return !empty($s['wa_enabled']) && $s['wa_token'] !== '' && $s['wa_phone_id'] !== '';
}

function bdesk_wa_events() {
    return array('confirm', 'shipped', 'delivered');
}

/* Customers in these countries get the Arabic template when one is set. */
function bdesk_wa_is_arabic($order) {
    $s = bdesk_settings();
    $country = $order->get_billing_country() ?: $order->get_shipping_country();
    $list = array_filter(array_map('trim', explode(',', strtoupper($s['wa_ar_countries']))));
    return $country && in_array($country, $list, true);
}

/* array(template name, language code), or null when this event has no template. */
function bdesk_wa_template($event, $order) {
    $s = bdesk_settings();
    if (bdesk_wa_is_arabic($order) && $s['wa_tpl_' . $event . '_ar'] !== '') return array($s['wa_tpl_' . $event . '_ar'], 'ar');
    if ($s['wa_tpl_' . $event] !== '') return array($s['wa_tpl_' . $event], 'en');
    return null;
}

/* "Renewed Dell XPS 15 9510 Laptop | 15.6" OLED | Intel Core i7 ..." -> "Renewed Dell XPS 15 9510 Laptop" */
function bdesk_short_name($name) {
    $name = html_entity_decode(wp_strip_all_tags((string) $name), ENT_QUOTES, 'UTF-8');
    $parts = preg_split('/\s*\|\s*|\s[–—-]\s/u', $name, 2);
    $short = trim($parts[0]);
    return mb_strlen($short) > 70 ? mb_substr($short, 0, 67) . '...' : $short;
}

function bdesk_wa_items($order) {
    $items = array();
    foreach ($order->get_items() as $it) $items[] = ($it->get_quantity() > 1 ? $it->get_quantity() . ' x ' : '') . bdesk_short_name($it->get_name());
    return implode(', ', $items);
}

/* Values for {{1}}, {{2}}... in the template body. Meta rejects empty values and line breaks. */
function bdesk_wa_params($event, $order, $arabic) {
    $name = $order->get_billing_first_name() ?: $order->get_formatted_billing_full_name();
    $total = bdesk_plain_money($order->get_total(), $order);
    $cod = bdesk_is_cod($order);
    if ($event === 'confirm') {
        $p = array($name, $order->get_order_number(), bdesk_wa_items($order), $total, $cod ? ($arabic ? 'الدفع عند الاستلام' : 'cash on delivery') : ($arabic ? 'مدفوع' : 'paid'));
    } elseif ($event === 'shipped') {
        $note = $cod
            ? ($arabic ? 'يرجى تجهيز مبلغ ' . $total . ' للمندوب.' : 'Please keep ' . $total . ' ready for the courier.')
            : ($arabic ? 'طلبك مدفوع بالكامل.' : 'Your order is fully paid.');
        $p = array($name, $order->get_order_number(), $order->get_meta('_bdesk_courier') ?: ($arabic ? 'شركة الشحن' : 'our courier'), $order->get_meta('_bdesk_tracking') ?: '-', $note);
    } else {
        $p = array($name, $order->get_order_number());
    }
    return array_map(function ($v) {
        $v = trim(preg_replace('/\s+/u', ' ', (string) $v));
        return $v === '' ? '-' : mb_substr($v, 0, 900);
    }, $p);
}

/* ---------- Queue and send ---------- */
function bdesk_wa_queue($order, $event, $force = false) {
    if (!bdesk_wa_on() || !in_array($event, bdesk_wa_events(), true)) return false;
    if (!$force && $order->get_meta('_bdesk_wa_' . $event)) return false;
    if (!bdesk_wa_template($event, $order)) return false;
    $digits = bdesk_phone_digits($order);
    if (strlen($digits) < 9) {
        $order->add_order_note(__('WhatsApp not sent: the customer phone number looks incomplete. Call them instead.', 'boutique-desk'));
        return false;
    }
    $order->update_meta_data('_bdesk_wa_' . $event, 'queued');
    $order->save();
    if (function_exists('as_enqueue_async_action')) as_enqueue_async_action('bdesk_wa_send', array($order->get_id(), $event, 1), 'boutique-desk');
    else bdesk_wa_send($order->get_id(), $event, 1);
    return true;
}

function bdesk_wa_api($path, $body) {
    $s = bdesk_settings();
    $base = apply_filters('bdesk_wa_api_base', 'https://graph.facebook.com/' . $s['wa_api_version'] . '/');
    $res = wp_remote_post($base . ltrim($path, '/'), array(
        'timeout' => 20,
        'headers' => array('Authorization' => 'Bearer ' . $s['wa_token'], 'Content-Type' => 'application/json'),
        'body' => wp_json_encode($body),
    ));
    if (is_wp_error($res)) return array('code' => 0, 'error' => $res->get_error_message(), 'error_code' => 0, 'id' => '');
    $code = (int) wp_remote_retrieve_response_code($res);
    $json = json_decode(wp_remote_retrieve_body($res), true);
    $ok = $code >= 200 && $code < 300 && !empty($json['messages'][0]['id']);
    return array(
        'code' => $code,
        'id' => $ok ? $json['messages'][0]['id'] : '',
        'error' => $ok ? '' : (isset($json['error']['error_user_msg']) ? $json['error']['error_user_msg'] : (isset($json['error']['message']) ? $json['error']['message'] : 'HTTP ' . $code)),
        'error_code' => isset($json['error']['code']) ? (int) $json['error']['code'] : 0,
    );
}

function bdesk_wa_payload($to, $template, $lang, $params) {
    $parameters = array();
    foreach ($params as $p) $parameters[] = array('type' => 'text', 'text' => $p);
    return array(
        'messaging_product' => 'whatsapp',
        'to' => $to,
        'type' => 'template',
        'template' => array('name' => $template, 'language' => array('code' => $lang), 'components' => $parameters ? array(array('type' => 'body', 'parameters' => $parameters)) : array()),
    );
}

add_action('bdesk_wa_send', 'bdesk_wa_send', 10, 3);
function bdesk_wa_send($order_id, $event, $attempt = 1) {
    $order = wc_get_order($order_id);
    if (!$order || !bdesk_wa_on()) return;
    $tpl = bdesk_wa_template($event, $order);
    $to = bdesk_phone_digits($order);
    if (!$tpl || $to === '') return;
    $arabic = $tpl[1] === 'ar';
    $r = bdesk_wa_api(bdesk_settings()['wa_phone_id'] . '/messages', bdesk_wa_payload($to, $tpl[0], $tpl[1], bdesk_wa_params($event, $order, $arabic)));

    if ($r['id']) {
        $order->update_meta_data('_bdesk_wa_' . $event, 'sent');
        $order->update_meta_data('_bdesk_wa_' . $event . '_at', time());
        $order->save();
        set_transient('bdesk_wa_mid_' . md5($r['id']), $order_id . '|' . $event, 30 * DAY_IN_SECONDS);
        delete_option('bdesk_wa_last_error');
        /* translators: 1: event name, 2: phone number */
        $order->add_order_note(sprintf(__('WhatsApp "%1$s" message sent to +%2$s.', 'boutique-desk'), $event, $to));
        return;
    }

    // Temporary trouble (network, rate limit, Meta outage): try again a little later.
    $temporary = $r['code'] === 0 || $r['code'] === 429 || $r['code'] >= 500 || in_array($r['error_code'], array(130429, 131016, 131056), true);
    if ($temporary && $attempt < 3 && function_exists('as_schedule_single_action')) {
        as_schedule_single_action(time() + ($attempt === 1 ? 2 : 10) * MINUTE_IN_SECONDS, 'bdesk_wa_send', array($order_id, $event, $attempt + 1), 'boutique-desk');
        return;
    }
    $order->update_meta_data('_bdesk_wa_' . $event, 'failed');
    $order->save();
    /* translators: 1: event name, 2: error text from Meta */
    $order->add_order_note(sprintf(__('WhatsApp "%1$s" message FAILED: %2$s. Call or message the customer yourself.', 'boutique-desk'), $event, $r['error']));
    update_option('bdesk_wa_last_error', array('time' => time(), 'code' => $r['error_code'], 'text' => $r['error']), false);
}

/* ---------- When to send ---------- */
add_action('woocommerce_order_status_processing', function ($order_id, $order = null) {
    $order = $order ?: wc_get_order($order_id);
    if (!$order || $order->get_type() !== 'shop_order' || !bdesk_wa_on()) return;
    if (!bdesk_settings()['confirm_orders'] || bdesk_stage($order) !== 'new') return;
    $created = $order->get_date_created();
    if ($created && (time() - $created->getTimestamp()) > 3 * DAY_IN_SECONDS) return; // never message about old orders
    bdesk_wa_queue($order, 'confirm');
}, 20, 2);
add_action('bdesk_order_shipped', function ($order) { bdesk_wa_queue($order, 'shipped'); });
add_action('bdesk_order_delivered', function ($order) { bdesk_wa_queue($order, 'delivered'); });

/* ---------- Webhook: replies and delivery status ---------- */
add_action('rest_api_init', function () {
    register_rest_route('bdesk/v1', '/whatsapp', array(
        array('methods' => 'GET', 'callback' => 'bdesk_wa_verify', 'permission_callback' => '__return_true'),
        array('methods' => 'POST', 'callback' => 'bdesk_wa_receive', 'permission_callback' => '__return_true'),
    ));
});

function bdesk_wa_webhook_url() {
    return rest_url('bdesk/v1/whatsapp');
}

function bdesk_wa_verify($request) {
    $s = bdesk_settings();
    if ($request->get_param('hub_mode') === 'subscribe' && $s['wa_verify_token'] !== '' && hash_equals($s['wa_verify_token'], (string) $request->get_param('hub_verify_token'))) {
        status_header(200);
        header('Content-Type: text/plain');
        echo esc_html((string) $request->get_param('hub_challenge'));
        exit;
    }
    return new WP_REST_Response('Forbidden', 403);
}

function bdesk_wa_yes_words() { return array('yes', 'y', 'yep', 'ok', 'okay', 'confirm', 'confirmed', 'sure', 'نعم', 'ايوه', 'أيوه', 'ايوا', 'اوك', 'أوك', 'تم', 'تمام', 'موافق', 'اكد', 'أكد', 'تأكيد'); }
function bdesk_wa_no_words() { return array('no', 'cancel', 'لا', 'الغاء', 'إلغاء', 'الغي', 'ألغي'); }

function bdesk_wa_reply_kind($text) {
    $t = trim(mb_strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text)));
    $first = preg_split('/\s+/u', $t)[0] ?? '';
    if (in_array($first, bdesk_wa_yes_words(), true)) return 'yes';
    if (in_array($first, bdesk_wa_no_words(), true)) return 'no';
    return 'other';
}

/* The customer's open order waiting for confirmation (newest first), matched on phone number. */
function bdesk_wa_find_order($from) {
    $from = preg_replace('/\D+/', '', $from);
    if ($from === '') return null;
    $open = wc_get_orders(array('limit' => 100, 'type' => 'shop_order', 'status' => bdesk_open_statuses(), 'orderby' => 'date', 'order' => 'DESC'));
    $fallback = null;
    foreach ($open as $o) {
        if (bdesk_phone_digits($o) !== $from) continue;
        if (bdesk_stage($o) === 'new') return $o;
        if (!$fallback) $fallback = $o;
    }
    return $fallback;
}

function bdesk_wa_receive($request) {
    $s = bdesk_settings();
    $body = $request->get_body();
    $sig = (string) $request->get_header('x_hub_signature_256');
    if ($s['wa_app_secret'] === '' || $sig === '' || !hash_equals('sha256=' . hash_hmac('sha256', $body, $s['wa_app_secret']), $sig)) {
        return new WP_REST_Response('Bad signature', 403);
    }
    $data = json_decode($body, true);
    foreach ((array) ($data['entry'] ?? array()) as $entry) {
        foreach ((array) ($entry['changes'] ?? array()) as $change) {
            $v = $change['value'] ?? array();
            foreach ((array) ($v['statuses'] ?? array()) as $st) bdesk_wa_handle_status($st);
            foreach ((array) ($v['messages'] ?? array()) as $msg) bdesk_wa_handle_message($msg);
        }
    }
    return new WP_REST_Response('ok', 200);
}

function bdesk_wa_handle_status($st) {
    $mid = isset($st['id']) ? (string) $st['id'] : '';
    $map = $mid ? get_transient('bdesk_wa_mid_' . md5($mid)) : false;
    if (!$map) return;
    list($order_id, $event) = explode('|', $map);
    $order = wc_get_order((int) $order_id);
    if (!$order) return;
    $status = isset($st['status']) ? sanitize_key($st['status']) : '';
    if (in_array($status, array('delivered', 'read'), true)) {
        $order->update_meta_data('_bdesk_wa_' . $event, $status);
        $order->save();
    } elseif ($status === 'failed') {
        $err = isset($st['errors'][0]['title']) ? $st['errors'][0]['title'] : 'undeliverable';
        $order->update_meta_data('_bdesk_wa_' . $event, 'failed');
        $order->save();
        /* translators: 1: event name, 2: reason from WhatsApp */
        $order->add_order_note(sprintf(__('WhatsApp "%1$s" message could not be delivered (%2$s). Call the customer instead.', 'boutique-desk'), $event, sanitize_text_field($err)));
    }
}

function bdesk_wa_handle_message($msg) {
    $id = isset($msg['id']) ? (string) $msg['id'] : '';
    if ($id && get_transient('bdesk_wa_seen_' . md5($id))) return; // Meta retries deliveries
    if ($id) set_transient('bdesk_wa_seen_' . md5($id), 1, DAY_IN_SECONDS);
    $type = isset($msg['type']) ? $msg['type'] : '';
    $text = $type === 'text' ? ($msg['text']['body'] ?? '') : ($type === 'button' ? ($msg['button']['text'] ?? '') : ($type === 'interactive' ? ($msg['interactive']['button_reply']['title'] ?? '') : ''));
    $text = sanitize_text_field((string) $text);
    $order = bdesk_wa_find_order(isset($msg['from']) ? $msg['from'] : '');
    if (!$order) return;
    $kind = $text === '' ? 'other' : bdesk_wa_reply_kind($text);
    $order->update_meta_data('_bdesk_wa_reply', $kind);
    $order->update_meta_data('_bdesk_wa_reply_text', mb_substr($text !== '' ? $text : '(' . $type . ')', 0, 200));
    $order->save();
    /* translators: %s: the customer's WhatsApp reply */
    $order->add_order_note(sprintf(__('WhatsApp reply from customer: "%s"', 'boutique-desk'), $text !== '' ? $text : '(' . $type . ')'));
    if ($kind === 'yes' && bdesk_settings()['wa_auto_confirm'] && bdesk_stage($order) === 'new') {
        bdesk_do_step($order, 'confirm');
        $order->add_order_note(__('Confirmed automatically because the customer replied YES on WhatsApp.', 'boutique-desk'));
    }
}

/* ---------- Settings page extras: webhook details, wording for Meta, test message ---------- */
add_action('admin_post_bdesk_wa_test', function () {
    check_admin_referer('bdesk_wa_test');
    if (!current_user_can('manage_woocommerce')) wp_die(esc_html__('You do not have permission to do this.', 'boutique-desk'));
    $phone = isset($_POST['phone']) ? preg_replace('/\D+/', '', sanitize_text_field(wp_unslash($_POST['phone']))) : '';
    $ar = !empty($_POST['arabic']);
    $s = bdesk_settings();
    $tpl = $ar ? $s['wa_tpl_confirm_ar'] : $s['wa_tpl_confirm'];
    if (strlen($phone) < 9) $msg = array('error', __('Type the full number with the country code, for example 971501234567.', 'boutique-desk'));
    elseif ($s['wa_token'] === '' || $s['wa_phone_id'] === '') $msg = array('error', __('Save the access token and phone number ID first.', 'boutique-desk'));
    elseif ($tpl === '') $msg = array('error', __('No confirm template name is set for that language.', 'boutique-desk'));
    else {
        $params = $ar ? array('أحمد', '1001', 'Dell Latitude 5420', 'AED 999.00', 'الدفع عند الاستلام') : array('Ahmed', '1001', 'Dell Latitude 5420', 'AED 999.00', 'cash on delivery');
        $r = bdesk_wa_api($s['wa_phone_id'] . '/messages', bdesk_wa_payload($phone, $tpl, $ar ? 'ar' : 'en', $params));
        $msg = $r['id'] ? array('success', sprintf(__('Test message sent to +%s. Check that phone.', 'boutique-desk'), $phone)) : array('error', sprintf(__('Meta refused the message: %s', 'boutique-desk'), $r['error'] . ($r['error_code'] ? ' (code ' . $r['error_code'] . ')' : '')));
    }
    set_transient('bdesk_wa_test_' . get_current_user_id(), $msg, 120);
    wp_safe_redirect(admin_url('admin.php?page=bdesk-settings#bdesk-wa'));
    exit;
});

function bdesk_wa_template_text() {
    $store = bdesk_settings()['store_name'];
    return array(
        'confirm' => array(
            'en' => 'Hello {{1}}, thank you for ordering from ' . $store . '. Order #{{2}}: {{3}}. Total: {{4}} ({{5}}). Please reply YES to confirm your order and delivery address, and we will ship it right away.',
            'ar' => 'مرحباً {{1}}، شكراً لطلبك من ' . $store . '. الطلب رقم {{2}}: {{3}}. الإجمالي: {{4}} ({{5}}). يرجى الرد بكلمة نعم لتأكيد الطلب وعنوان التوصيل وسنقوم بشحنه فوراً.',
        ),
        'shipped' => array(
            'en' => 'Hello {{1}}, your order #{{2}} has been shipped with {{3}}. Tracking number: {{4}}. {{5}} Thank you for shopping with ' . $store . '.',
            'ar' => 'مرحباً {{1}}، تم شحن طلبك رقم {{2}} مع {{3}}. رقم التتبع: {{4}}. {{5}} شكراً لتسوقك من ' . $store . '.',
        ),
        'delivered' => array(
            'en' => 'Hello {{1}}, your order #{{2}} has been delivered. Thank you for shopping with ' . $store . '. If anything is not right, reply to this message and we will help.',
            'ar' => 'مرحباً {{1}}، تم توصيل طلبك رقم {{2}}. شكراً لتسوقك من ' . $store . '. إذا كان هناك أي مشكلة، يرجى الرد على هذه الرسالة وسنساعدك.',
        ),
    );
}

function bdesk_wa_settings_extra() {
    $s = bdesk_settings();
    $notice = get_transient('bdesk_wa_test_' . get_current_user_id());
    if ($notice) delete_transient('bdesk_wa_test_' . get_current_user_id());
    echo '<div id="bdesk-wa" class="bdesk-panel" style="margin-top:24px"><h2>WhatsApp: connect to Meta</h2>';
    if ($notice) echo '<div class="notice notice-' . esc_attr($notice[0]) . ' inline"><p>' . esc_html($notice[1]) . '</p></div>';
    echo '<p><strong>Webhook URL</strong> (Meta app &rarr; WhatsApp &rarr; Configuration &rarr; Webhook, subscribe to <em>messages</em>)<br><code>' . esc_html(bdesk_wa_webhook_url()) . '</code></p>';
    echo '<p><strong>Verify token</strong> (paste this into the same Meta screen)<br><code>' . esc_html($s['wa_verify_token']) . '</code></p>';
    echo '<h3>Send a test message</h3><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="bdesk_wa_test">' . wp_nonce_field('bdesk_wa_test', '_wpnonce', true, false);
    echo '<input type="text" name="phone" class="regular-text" placeholder="Your number with country code, e.g. 971501234567"> <label><input type="checkbox" name="arabic" value="1"> Arabic template</label> ';
    echo '<button class="button">Send test</button></form>';
    echo '<h3>Template wording to submit in Meta</h3><p>In WhatsApp Manager &rarr; Message templates, create each one as category <strong>Utility</strong>, with exactly this text and the same name you saved above (Meta rejects templates that start or end with a {{number}}, so keep the opening and closing words). The {{numbers}} are filled in by the plugin.</p>';
    foreach (bdesk_wa_template_text() as $event => $langs) {
        echo '<p><strong>' . esc_html($s['wa_tpl_' . $event]) . '</strong> (English)<br><code style="white-space:pre-wrap;display:block;padding:8px">' . esc_html($langs['en']) . '</code></p>';
        echo '<p><strong>' . esc_html($s['wa_tpl_' . $event . '_ar']) . '</strong> (Arabic)<br><code style="white-space:pre-wrap;display:block;padding:8px" dir="rtl">' . esc_html($langs['ar']) . '</code></p>';
    }
    echo '</div>';
}

/* Lazily create the webhook verify token. */
add_action('admin_init', function () {
    $s = bdesk_settings();
    if ($s['wa_verify_token'] === '') {
        $raw = (array) get_option('bdesk_settings', array());
        $raw['wa_verify_token'] = wp_generate_password(24, false);
        update_option('bdesk_settings', $raw);
    }
});
