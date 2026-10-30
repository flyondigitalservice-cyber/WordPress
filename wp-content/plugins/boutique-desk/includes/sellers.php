<?php
if (!defined('ABSPATH')) exit;

/*
 * Category Sellers: outside sellers who list products only in the categories you assign them
 * (Jewellery, Bags, Fashion accessories...), see only their own products, images and order lines,
 * and track their revenue and earnings after your commission.
 */

function bdesk_seller_caps() {
    return array(
        'read' => true,
        'upload_files' => true,
        'bdesk_seller_desk' => true,
        // Own products only: no edit_others_products.
        'edit_products' => true,
        'edit_published_products' => true,
        'publish_products' => true,
        'delete_products' => true,
        'delete_published_products' => true,
        'read_product' => true,
        'assign_product_terms' => true,
    );
}

add_action('admin_init', function () {
    if (get_option('bdesk_seller_role_version') !== BDESK_VERSION) {
        remove_role('bdesk_seller');
        add_role('bdesk_seller', __('Category Seller', 'boutique-desk'), bdesk_seller_caps());
        update_option('bdesk_seller_role_version', BDESK_VERSION);
    }
});
register_deactivation_hook(BDESK_FILE, function () { remove_role('bdesk_seller'); delete_option('bdesk_seller_role_version'); });

function bdesk_is_seller($user = null) {
    $user = $user ?: wp_get_current_user();
    return $user && $user->ID && in_array('bdesk_seller', (array) $user->roles, true) && !user_can($user, 'manage_options');
}
function bdesk_seller_cats($user_id) {
    return array_values(array_filter(array_map('absint', (array) get_user_meta($user_id, 'bdesk_seller_cats', true))));
}
function bdesk_seller_rate($user_id) {
    return min(100, max(0, (float) get_user_meta($user_id, 'bdesk_seller_commission', true)));
}

/* Optional approval: seller products wait as "Pending review" until an admin publishes them. */
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (!empty($allcaps['bdesk_seller_desk']) && empty($allcaps['manage_options']) && get_option('bdesk_seller_approval', '1') === '1') {
        $allcaps['publish_products'] = false;
    }
    return $allcaps;
}, 10, 4);

/* ---------- Seller access in wp-admin ---------- */
add_filter('woocommerce_prevent_admin_access', function ($prevent) { return bdesk_is_seller() ? false : $prevent; }, 20);
add_filter('login_redirect', function ($to, $requested, $user) {
    return ($user instanceof WP_User && bdesk_is_seller($user)) ? admin_url('admin.php?page=bdesk-seller') : $to;
}, 25, 3);

add_action('admin_menu', function () {
    if (current_user_can('manage_woocommerce')) {
        // Admins open any seller's desk from WooCommerce → Sellers; the page stays out of their menu.
        add_submenu_page('options.php', __('Seller Desk', 'boutique-desk'), __('Seller Desk', 'boutique-desk'), 'manage_woocommerce', 'bdesk-seller', 'bdesk_seller_page');
    } else {
        add_menu_page(__('Seller Desk', 'boutique-desk'), __('Seller Desk', 'boutique-desk'), 'bdesk_seller_desk', 'bdesk-seller', 'bdesk_seller_page', 'dashicons-store', 2);
    }
    add_submenu_page('woocommerce', __('Sellers', 'boutique-desk'), __('Sellers', 'boutique-desk'), 'manage_woocommerce', 'bdesk-sellers', 'bdesk_sellers_admin_page');
    if (!bdesk_is_seller()) return;
    global $menu, $submenu;
    $keep = array('bdesk-seller', 'edit.php?post_type=product', 'upload.php', 'profile.php');
    foreach ((array) $menu as $item) if (isset($item[2]) && !in_array($item[2], $keep, true)) remove_menu_page($item[2]);
    if (isset($submenu['edit.php?post_type=product'])) {
        foreach ($submenu['edit.php?post_type=product'] as $i => $sub) {
            if (!in_array($sub[2], array('edit.php?post_type=product', 'post-new.php?post_type=product'), true)) unset($submenu['edit.php?post_type=product'][$i]);
        }
    }
}, 9999);

add_action('current_screen', function ($screen) {
    if (!bdesk_is_seller() || !$screen) return;
    if ($screen->id === 'toplevel_page_bdesk-seller' || in_array($screen->base, array('upload', 'media', 'profile', 'async-upload'), true)) return;
    if (in_array($screen->post_type, array('product', 'attachment'), true) && in_array($screen->base, array('edit', 'post'), true)) return;
    wp_safe_redirect(admin_url('admin.php?page=bdesk-seller'));
    exit;
});

add_action('admin_bar_menu', function ($bar) {
    if (!bdesk_is_seller()) return;
    foreach (array('comments', 'new-content', 'updates', 'wp-logo', 'customize') as $node) $bar->remove_node($node);
}, 999);

/* Sellers see only their own products and their own images. */
add_action('pre_get_posts', function ($q) {
    if (!is_admin() || !bdesk_is_seller() || !$q->is_main_query()) return;
    if (in_array($q->get('post_type'), array('product', 'attachment'), true)) $q->set('author', get_current_user_id());
});
add_filter('ajax_query_attachments_args', function ($args) {
    if (bdesk_is_seller()) $args['author'] = get_current_user_id();
    return $args;
});

/* Category box on the product screen lists only the seller's categories (and their sub-categories). */
function bdesk_seller_allowed_cats($user_id) {
    $ids = bdesk_seller_cats($user_id);
    foreach ($ids as $id) {
        $kids = get_term_children($id, 'product_cat');
        if (!is_wp_error($kids)) $ids = array_merge($ids, $kids);
    }
    return array_values(array_unique(array_map('intval', $ids)));
}
add_filter('get_terms_args', function ($args, $taxonomies) {
    if (!is_admin() || !bdesk_is_seller() || !in_array('product_cat', (array) $taxonomies, true)) return $args;
    $allowed = bdesk_seller_allowed_cats(get_current_user_id());
    $args['include'] = $allowed ?: array(0);
    return $args;
}, 10, 2);

/* Whatever is submitted, a seller's product ends up only in that seller's categories. */
add_action('save_post_product', function ($post_id, $post) {
    if (wp_is_post_revision($post_id) || $post->post_status === 'auto-draft') return;
    $author = get_userdata($post->post_author);
    if (!$author || !bdesk_is_seller($author)) return;
    $allowed = bdesk_seller_allowed_cats($author->ID);
    if (!$allowed) return;
    $current = wp_get_object_terms($post_id, 'product_cat', array('fields' => 'ids'));
    $keep = array_values(array_intersect(array_map('intval', is_wp_error($current) ? array() : $current), $allowed));
    if (!$keep) $keep = array($allowed[0]);
    if ($keep != $current) wp_set_object_terms($post_id, $keep, 'product_cat');
}, 20, 2);

add_action('admin_notices', function () {
    if (!bdesk_is_seller()) return;
    $screen = get_current_screen();
    if (!bdesk_seller_cats(get_current_user_id())) {
        echo '<div class="notice notice-warning"><p>' . esc_html__('No categories have been assigned to your seller account yet, so you cannot add products. Please contact the store owner.', 'boutique-desk') . '</p></div>';
    } elseif ($screen && $screen->post_type === 'product' && get_option('bdesk_seller_approval', '1') === '1') {
        echo '<div class="notice notice-info"><p>' . esc_html__('New products are reviewed by the store before they go live. Use "Submit for Review" when your product is ready.', 'boutique-desk') . '</p></div>';
    }
});

/* Remember who sold each line, so reports stay right even if a product changes hands later. */
add_action('woocommerce_checkout_create_order_line_item', function ($item, $key, $values, $order) {
    $pid = $item->get_product_id();
    $author = $pid ? (int) get_post_field('post_author', $pid) : 0;
    if ($author && bdesk_is_seller(get_userdata($author))) $item->add_meta_data('_bdesk_seller', $author, true);
}, 10, 4);

/* ---------- Seller statistics ---------- */
function bdesk_seller_order_ids($seller_id, $limit = 500) {
    global $wpdb;
    $products = get_posts(array('post_type' => 'product', 'author' => $seller_id, 'post_status' => 'any', 'fields' => 'ids', 'numberposts' => -1));
    $in = $products ? implode(',', array_map('intval', $products)) : '0';
    $sql = "SELECT DISTINCT oi.order_id FROM {$wpdb->prefix}woocommerce_order_items oi
        JOIN {$wpdb->prefix}woocommerce_order_itemmeta m ON m.order_item_id = oi.order_item_id
        WHERE oi.order_item_type = 'line_item'
        AND ((m.meta_key = '_bdesk_seller' AND m.meta_value = %d) OR (m.meta_key = '_product_id' AND m.meta_value IN ($in)))
        ORDER BY oi.order_id DESC LIMIT %d";
    return array_map('intval', $wpdb->get_col($wpdb->prepare($sql, $seller_id, $limit)));
}

/* The seller's own lines in an order. */
function bdesk_seller_lines($order, $seller_id) {
    $lines = array();
    foreach ($order->get_items() as $item_id => $item) {
        $marked = (int) $item->get_meta('_bdesk_seller');
        $owner = $marked ?: (int) get_post_field('post_author', $item->get_product_id());
        if ($owner !== (int) $seller_id) continue;
        $lines[] = array('item' => $item, 'net' => (float) $item->get_total() - (float) $order->get_total_refunded_for_item($item_id));
    }
    return $lines;
}

function bdesk_seller_stats($seller_id) {
    $tz = wp_timezone();
    $today = (new DateTimeImmutable('today', $tz))->getTimestamp();
    $month = (new DateTimeImmutable('first day of this month midnight', $tz))->getTimestamp();
    $stats = array('orders' => array(), 'month' => 0, 'month_orders' => 0, 'all' => 0, 'pending_ship' => 0, 'units' => 0, 'days' => array(), 'best' => array());
    for ($i = 29; $i >= 0; $i--) $stats['days'][wp_date('Y-m-d', $today - $i * DAY_IN_SECONDS)] = 0;
    foreach (bdesk_seller_order_ids($seller_id) as $oid) {
        $order = wc_get_order($oid);
        if (!$order || $order->get_type() !== 'shop_order') continue;
        $lines = bdesk_seller_lines($order, $seller_id);
        if (!$lines) continue;
        $stats['orders'][] = array('order' => $order, 'lines' => $lines);
        if (!$order->has_status(bdesk_paid_statuses())) continue;
        $sum = 0;
        foreach ($lines as $l) {
            $sum += $l['net'];
            $stats['units'] += $l['item']->get_quantity();
            $pid = $l['item']->get_product_id();
            if (!isset($stats['best'][$pid])) $stats['best'][$pid] = array('name' => $l['item']->get_name(), 'qty' => 0);
            $stats['best'][$pid]['qty'] += $l['item']->get_quantity();
        }
        $ts = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : 0;
        $stats['all'] += $sum;
        if ($ts >= $month) { $stats['month'] += $sum; $stats['month_orders']++; }
        $day = wp_date('Y-m-d', $ts);
        if (isset($stats['days'][$day])) $stats['days'][$day] += $sum;
        if ($order->has_status('processing')) $stats['pending_ship']++;
    }
    uasort($stats['best'], function ($a, $b) { return $b['qty'] <=> $a['qty']; });
    $stats['best'] = array_slice($stats['best'], 0, 5, true);
    return $stats;
}

/* ---------- Seller Desk ---------- */
function bdesk_seller_page() {
    $seller_id = get_current_user_id();
    if (current_user_can('manage_woocommerce') && !empty($_GET['seller'])) $seller_id = absint($_GET['seller']);
    if (!current_user_can('bdesk_seller_desk') && !current_user_can('manage_woocommerce')) wp_die(esc_html__('You do not have access to this page.', 'boutique-desk'));
    $seller = get_userdata($seller_id);
    if (!$seller) wp_die(esc_html__('Seller not found.', 'boutique-desk'));
    $s = bdesk_seller_stats($seller_id);
    $rate = bdesk_seller_rate($seller_id);
    $cats = array_filter(array_map(function ($id) { $t = get_term($id, 'product_cat'); return ($t && !is_wp_error($t)) ? $t->name : ''; }, bdesk_seller_cats($seller_id)));
    $counts = array();
    foreach (array('publish' => 'Live', 'pending' => 'In review', 'draft' => 'Drafts') as $st => $label) {
        $counts[$label] = count(get_posts(array('post_type' => 'product', 'author' => $seller_id, 'post_status' => $st, 'fields' => 'ids', 'numberposts' => -1)));
    }
    $sold_out = array_filter(array_map('wc_get_product', get_posts(array('post_type' => 'product', 'author' => $seller_id, 'post_status' => 'publish', 'fields' => 'ids', 'numberposts' => 8, 'meta_key' => '_stock_status', 'meta_value' => 'outofstock'))));
    $peak = max(1, max($s['days']));
    ?>
    <div class="wrap bdesk-wrap">
        <div class="bdesk-head">
            <div><h1><?php echo esc_html($seller_id === get_current_user_id() ? 'Seller Desk' : 'Seller Desk: ' . $seller->display_name); ?></h1>
            <p><?php echo $cats ? esc_html('Your categories: ' . implode(', ', $cats)) : esc_html__('No categories assigned yet.', 'boutique-desk'); ?></p></div>
            <div class="bdesk-head-actions">
                <?php if ($seller_id === get_current_user_id() && $cats): ?><a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>">Add product</a><?php endif; ?>
                <a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=product' . ($seller_id !== get_current_user_id() ? '&author=' . $seller_id : ''))); ?>">My products</a>
            </div>
        </div>
        <div class="bdesk-cards">
            <div class="bdesk-card"><span>Revenue this month</span><strong><?php echo wp_kses_post(bdesk_money($s['month'])); ?></strong><small><?php echo esc_html($s['month_orders']); ?> orders</small></div>
            <div class="bdesk-card is-accent"><span>Your earnings this month</span><strong><?php echo wp_kses_post(bdesk_money($s['month'] * (1 - $rate / 100))); ?></strong><small>After <?php echo esc_html($rate + 0); ?>% store commission</small></div>
            <div class="bdesk-card"><span>All-time revenue</span><strong><?php echo wp_kses_post(bdesk_money($s['all'])); ?></strong><small><?php echo esc_html($s['units']); ?> pieces sold</small></div>
            <div class="bdesk-card"><span>Being packed</span><strong><?php echo esc_html($s['pending_ship']); ?></strong><small>Orders not yet shipped</small></div>
            <div class="bdesk-card"><span>Your products</span><strong><?php echo esc_html($counts['Live']); ?></strong><small><?php echo esc_html($counts['In review'] . ' in review · ' . $counts['Drafts'] . ' drafts'); ?></small></div>
        </div>
        <div class="bdesk-grid">
            <section class="bdesk-panel"><h2>Revenue, last 30 days</h2>
                <div class="bdesk-chart" role="img" aria-label="Daily revenue for the last 30 days">
                    <?php foreach ($s['days'] as $d => $v): ?><div class="bdesk-bar" title="<?php echo esc_attr(wp_date('j M', strtotime($d)) . ': ' . html_entity_decode(wp_strip_all_tags(bdesk_money($v)))); ?>"><i style="height:<?php echo esc_attr(max(2, round($v / $peak * 100))); ?>%"></i><em><?php echo esc_html((int) wp_date('j', strtotime($d)) % 5 === 0 ? wp_date('j', strtotime($d)) : ''); ?></em></div><?php endforeach; ?>
                </div>
            </section>
            <section class="bdesk-panel"><h2>Best sellers</h2>
                <?php if ($s['best']): ?><ol class="bdesk-list"><?php foreach ($s['best'] as $b): ?><li><span style="color:#1d1a17"><?php echo esc_html($b['name']); ?></span><span><?php echo esc_html($b['qty']); ?> sold</span></li><?php endforeach; ?></ol><?php else: ?><p class="bdesk-empty">No sales yet.</p><?php endif; ?>
                <?php if ($sold_out): ?><h2 class="bdesk-sub">Sold out</h2><ul class="bdesk-list"><?php foreach ($sold_out as $p): ?><li><a href="<?php echo esc_url(get_edit_post_link($p->get_id())); ?>"><?php echo esc_html($p->get_name()); ?></a><span class="bdesk-tag">Restock</span></li><?php endforeach; ?></ul><?php endif; ?>
            </section>
        </div>
        <section class="bdesk-panel"><h2>Orders for your products</h2>
            <?php if ($s['orders']): ?>
            <table class="widefat striped bdesk-table">
                <thead><tr><th>Order</th><th>Date</th><th>Your items</th><th>Your revenue</th><th>Status</th><th>Ship to</th></tr></thead>
                <tbody><?php foreach (array_slice($s['orders'], 0, 50) as $row): $o = $row['order']; $sum = 0; $items = array();
                    foreach ($row['lines'] as $l) {
                        $sum += $l['net'];
                        $meta = array();
                        foreach ($l['item']->get_formatted_meta_data('') as $m) $meta[] = wp_strip_all_tags($m->display_key . ': ' . $m->display_value);
                        $items[] = $l['item']->get_quantity() . ' × ' . $l['item']->get_name() . ($meta ? ' (' . implode(', ', $meta) . ')' : '');
                    } ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($o->get_order_number()); ?></strong></td>
                        <td><?php echo esc_html(wc_format_datetime($o->get_date_created(), 'j M Y')); ?></td>
                        <td class="bdesk-items"><?php echo esc_html(implode(', ', $items)); ?></td>
                        <td><?php echo wp_kses_post(bdesk_money($sum, $o)); ?></td>
                        <td><?php echo esc_html(wc_get_order_status_name($o->get_status())); ?></td>
                        <td><?php echo esc_html(trim(($o->get_shipping_city() ?: $o->get_billing_city()) . ', ' . ($o->get_shipping_state() ?: $o->get_billing_state()), ', ')); ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
            <?php else: ?><p class="bdesk-empty">No orders for your products yet.</p><?php endif; ?>
        </section>
    </div>
    <?php
}

/* ---------- Admin: seller settings on the user profile ---------- */
function bdesk_seller_profile_fields($user) {
    if (!current_user_can('manage_options') || !in_array('bdesk_seller', (array) $user->roles, true)) return;
    $mine = bdesk_seller_cats($user->ID);
    $terms = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0));
    wp_nonce_field('bdesk_seller_profile', 'bdesk_seller_nonce');
    echo '<h2>Category Seller</h2><table class="form-table" role="presentation"><tr><th>Allowed categories</th><td>';
    foreach ((array) $terms as $t) echo '<label style="display:inline-block;min-width:200px;margin:0 12px 8px 0"><input type="checkbox" name="bdesk_seller_cats[]" value="' . esc_attr($t->term_id) . '"' . checked(in_array($t->term_id, $mine, true), true, false) . '> ' . esc_html($t->name) . '</label>';
    echo '<p class="description">The seller can only add products to these categories (and their sub-categories). Create new categories such as Jewellery or Bags under Products → Categories.</p></td></tr>';
    echo '<tr><th><label for="bdesk-commission">Store commission (%)</label></th><td><input id="bdesk-commission" type="number" min="0" max="100" step="0.5" name="bdesk_seller_commission" value="' . esc_attr(bdesk_seller_rate($user->ID)) . '" class="small-text"> <span class="description">Kept by the store from this seller\'s sales. Their dashboard shows earnings after commission.</span></td></tr></table>';
}
add_action('edit_user_profile', 'bdesk_seller_profile_fields');
add_action('show_user_profile', 'bdesk_seller_profile_fields');
function bdesk_seller_profile_save($user_id) {
    if (!current_user_can('manage_options') || !isset($_POST['bdesk_seller_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bdesk_seller_nonce'])), 'bdesk_seller_profile')) return;
    update_user_meta($user_id, 'bdesk_seller_cats', array_map('absint', (array) ($_POST['bdesk_seller_cats'] ?? array())));
    update_user_meta($user_id, 'bdesk_seller_commission', min(100, max(0, (float) ($_POST['bdesk_seller_commission'] ?? 0))));
}
add_action('edit_user_profile_update', 'bdesk_seller_profile_save');
add_action('personal_options_update', 'bdesk_seller_profile_save');

/* ---------- Admin: all sellers at a glance ---------- */
function bdesk_sellers_admin_page() {
    if (isset($_POST['bdesk_approval_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bdesk_approval_nonce'])), 'bdesk_approval')) {
        update_option('bdesk_seller_approval', empty($_POST['bdesk_seller_approval']) ? '0' : '1');
        echo '<div class="notice notice-success"><p>Saved.</p></div>';
    }
    $sellers = get_users(array('role' => 'bdesk_seller'));
    $pending = count(get_posts(array('post_type' => 'product', 'post_status' => 'pending', 'fields' => 'ids', 'numberposts' => -1)));
    ?>
    <div class="wrap bdesk-wrap"><h1>Category Sellers</h1>
        <p>Add a seller under Users → Add New with the role <strong>Category Seller</strong>, then open their profile to choose their categories and commission.</p>
        <form method="post" style="margin:12px 0 18px"><?php wp_nonce_field('bdesk_approval', 'bdesk_approval_nonce'); ?>
            <label><input type="checkbox" name="bdesk_seller_approval" value="1" <?php checked(get_option('bdesk_seller_approval', '1'), '1'); ?>> Review seller products before they go live</label>
            <?php submit_button('Save', 'secondary', 'submit', false); ?>
            <?php if ($pending): ?> &nbsp; <a href="<?php echo esc_url(admin_url('edit.php?post_status=pending&post_type=product')); ?>"><?php echo esc_html($pending); ?> product(s) waiting for review</a><?php endif; ?>
        </form>
        <?php if ($sellers): ?>
        <table class="widefat striped bdesk-table"><thead><tr><th>Seller</th><th>Categories</th><th>Live products</th><th>Revenue this month</th><th>Commission</th><th>Payable this month</th><th></th></tr></thead><tbody>
        <?php foreach ($sellers as $u): $s = bdesk_seller_stats($u->ID); $rate = bdesk_seller_rate($u->ID);
            $cats = array_filter(array_map(function ($id) { $t = get_term($id, 'product_cat'); return ($t && !is_wp_error($t)) ? $t->name : ''; }, bdesk_seller_cats($u->ID))); ?>
            <tr><td><strong><?php echo esc_html($u->display_name); ?></strong><br><small><?php echo esc_html($u->user_email); ?></small></td>
                <td><?php echo esc_html($cats ? implode(', ', $cats) : '—'); ?></td>
                <td><?php echo esc_html(count(get_posts(array('post_type' => 'product', 'author' => $u->ID, 'post_status' => 'publish', 'fields' => 'ids', 'numberposts' => -1)))); ?></td>
                <td><?php echo wp_kses_post(bdesk_money($s['month'])); ?></td>
                <td><?php echo esc_html($rate + 0); ?>%</td>
                <td><strong><?php echo wp_kses_post(bdesk_money($s['month'] * (1 - $rate / 100))); ?></strong></td>
                <td><a class="button button-small" href="<?php echo esc_url(admin_url('admin.php?page=bdesk-seller&seller=' . $u->ID)); ?>">View desk</a> <a class="button button-small" href="<?php echo esc_url(get_edit_user_link($u->ID)); ?>">Settings</a></td></tr>
        <?php endforeach; ?></tbody></table>
        <?php else: ?><p class="bdesk-empty">No sellers yet.</p><?php endif; ?>
    </div>
    <?php
}
