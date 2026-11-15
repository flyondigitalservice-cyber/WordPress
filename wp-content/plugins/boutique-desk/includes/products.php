<?php
if (!defined('ABSPATH')) exit;

/*
 * Product list upgrades (Products -> All Products).
 * Specs are read from the product title, so no attribute set-up is needed.
 * Adds: spec chips, deal %, units sold, a "needs attention" check, one-click sold out,
 * quick views (On sale, Sold out, Needs attention), a condition filter and bulk actions.
 */

function bdesk_products_on() {
    $s = bdesk_settings();
    return !empty($s['product_list']);
}

/* "Renewed Dell Latitude 5410 | 14” | Intel Core i5-10210U 10th Gen | 8GB RAM | 256GB SSD" -> chips */
function bdesk_parse_specs($title) {
    $t = html_entity_decode((string) $title, ENT_QUOTES, 'UTF-8');
    $out = array('condition' => '', 'cpu' => '', 'ram' => '', 'storage' => '', 'screen' => '', 'extra' => array());

    if (preg_match('/^\s*(Renewed|Refurbished|Brand New|New|Used|Open[\s-]?Box)\b/iu', $t, $m)) {
        $out['condition'] = ucwords(strtolower(str_replace('-', ' ', $m[1])));
    }
    if (preg_match('/(\d{1,3})\s*GB\s*(?:DDR\d\w*\s*)?(?:RAM|Memory)/iu', $t, $m)) $out['ram'] = $m[1] . 'GB';
    if (preg_match('/(\d+(?:\.\d+)?)\s*(GB|TB)\s*(?:NVMe\s*|PCIe\s*)?(SSD|HDD|eMMC|SSHD)/iu', $t, $m)) $out['storage'] = $m[1] . strtoupper($m[2]) . ' ' . (strtolower($m[3]) === 'emmc' ? 'eMMC' : strtoupper($m[3]));
    if (preg_match('/(\d{1,2}(?:\.\d)?)\s*(?:[”″"“\']|-?\s*inch)/iu', $t, $m) && (float) $m[1] >= 9 && (float) $m[1] <= 20) $out['screen'] = $m[1] . '"';

    $gen = '';
    if (preg_match('/(\d{1,2})(?:st|nd|rd|th)\s*Gen/iu', $t, $g)) $gen = $g[1] . 'th';
    if (preg_match('/(?:Apple|MacBook(?:\s+(?:Air|Pro))?)\s+(M\d\w*)\b/iu', $t, $m)) {
        $out['cpu'] = 'Apple ' . strtoupper($m[1]);
    } elseif (preg_match('/Core\s*(?:Ultra\s*)?(i[3579]|m[357])(?:[-\s]?(\d{4,5})\w*)?/iu', $t, $m)) {
        $out['cpu'] = strtolower($m[1]);
        if (!$gen && !empty($m[2])) $gen = (strlen($m[2]) === 5 ? substr($m[2], 0, 2) : substr($m[2], 0, 1)) . 'th';
        if ($gen) $out['cpu'] .= ' · ' . $gen;
    } elseif (preg_match('/Ryzen\s*([3579])/iu', $t, $m)) {
        $out['cpu'] = 'Ryzen ' . $m[1];
    } elseif (preg_match('/\b(Celeron|Pentium|Athlon|Snapdragon)\b/iu', $t, $m)) {
        $out['cpu'] = ucfirst(strtolower($m[1]));
    }
    if (preg_match('/touch/iu', $t)) $out['extra'][] = 'Touch';
    if (preg_match('/2-in-1|detachable|convertible|x360/iu', $t)) $out['extra'][] = '2-in-1';
    return $out;
}

/* Product facts needed by the columns. */
function bdesk_product_flags($product) {
    $flags = array();
    if (trim($product->get_name()) === '') $flags[] = __('No title', 'boutique-desk');
    if (!$product->get_image_id()) $flags[] = __('No photo', 'boutique-desk');
    if (trim($product->get_short_description()) === '') $flags[] = __('No short description', 'boutique-desk');
    if ((float) $product->get_price() <= 0 && !$product->is_type('variable')) $flags[] = __('No price', 'boutique-desk');
    return $flags;
}

/* ---------- SQL pieces shared by the views, filters and counts ---------- */
function bdesk_sql_attention() {
    global $wpdb;
    $p = $wpdb->posts; $m = $wpdb->postmeta;
    return "($p.post_title = '' OR $p.post_excerpt = ''
        OR NOT EXISTS (SELECT 1 FROM $m a WHERE a.post_id = $p.ID AND a.meta_key = '_thumbnail_id' AND a.meta_value + 0 > 0)
        OR NOT EXISTS (SELECT 1 FROM $m b WHERE b.post_id = $p.ID AND b.meta_key = '_price' AND b.meta_value + 0 > 0))";
}
function bdesk_sql_sale() {
    global $wpdb;
    return "EXISTS (SELECT 1 FROM {$wpdb->postmeta} s WHERE s.post_id = {$wpdb->posts}.ID AND s.meta_key = '_sale_price' AND s.meta_value <> '')";
}
function bdesk_count_where($sql) {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status IN ('publish','draft','pending','private','future') AND $sql");
}

/* ---------- Columns ---------- */
add_filter('manage_edit-product_columns', function ($cols) {
    if (!bdesk_products_on()) return $cols;
    $out = array();
    foreach ($cols as $k => $v) {
        if (in_array($k, array('sku', 'product_tag', 'taxonomy-product_brand'), true) && !bdesk_catalog_uses($k)) continue;
        $out[$k] = $v;
        if ($k === 'name') $out['bdesk_specs'] = __('Specs', 'boutique-desk');
        if ($k === 'price') { $out['bdesk_deal'] = __('Deal', 'boutique-desk'); }
    }
    $out['bdesk_sold'] = __('Sold', 'boutique-desk');
    $out['bdesk_check'] = __('Check', 'boutique-desk');
    return $out;
}, 30);

/* Hide the SKU, Tags and Brands columns on stores that never fill them in. */
function bdesk_catalog_uses($col) {
    global $wpdb;
    $key = 'bdesk_uses_' . sanitize_key($col);
    $v = get_transient($key);
    if ($v === false) {
        if ($col === 'sku') $v = $wpdb->get_var("SELECT 1 FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value <> '' LIMIT 1") ? 'yes' : 'no';
        else $v = $wpdb->get_var($wpdb->prepare("SELECT 1 FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s AND count > 0 LIMIT 1", str_replace('taxonomy-', '', $col))) ? 'yes' : 'no';
        set_transient($key, $v, 6 * HOUR_IN_SECONDS);
    }
    return $v === 'yes';
}

add_action('manage_product_posts_custom_column', function ($col, $post_id) {
    if (!bdesk_products_on() || strpos($col, 'bdesk_') !== 0) return;
    $product = wc_get_product($post_id);
    if (!$product) return;
    if ($col === 'bdesk_specs') {
        $sp = bdesk_parse_specs($product->get_name());
        if ($sp['condition']) echo '<span class="bdesk-chip is-cond">' . esc_html($sp['condition']) . '</span>';
        foreach (array('cpu', 'ram', 'storage', 'screen') as $k) if ($sp[$k]) echo '<span class="bdesk-chip">' . esc_html($sp[$k]) . '</span>';
        foreach ($sp['extra'] as $x) echo '<span class="bdesk-chip is-extra">' . esc_html($x) . '</span>';
        if (!$sp['cpu'] && !$sp['ram'] && !$sp['storage']) echo '<span class="description">&mdash;</span>';
    } elseif ($col === 'bdesk_deal') {
        $reg = (float) $product->get_regular_price();
        $now = (float) $product->get_price();
        if ($product->is_on_sale() && $reg > 0 && $now < $reg) {
            echo '<strong class="bdesk-off">-' . esc_html(round(($reg - $now) / $reg * 100)) . '%</strong><br><small>' . esc_html(sprintf(__('saves %s', 'boutique-desk'), html_entity_decode(wp_strip_all_tags(wc_price($reg - $now)), ENT_QUOTES, 'UTF-8'))) . '</small>';
        } elseif ($product->get_sale_price() !== '' && (float) $product->get_sale_price() >= $reg) {
            echo '<span class="bdesk-chip is-warn">' . esc_html__('Sale not lower', 'boutique-desk') . '</span>';
        } else {
            echo '<span class="description">&mdash;</span>';
        }
    } elseif ($col === 'bdesk_sold') {
        $n = (int) $product->get_total_sales();
        echo $n ? '<strong>' . esc_html($n) . '</strong>' : '<span class="description">0</span>';
    } elseif ($col === 'bdesk_check') {
        $flags = bdesk_product_flags($product);
        if (!$flags) echo '<span class="bdesk-chip is-ok">' . esc_html($product->get_status() === 'draft' ? __('Ready to publish', 'boutique-desk') : __('Complete', 'boutique-desk')) . '</span>';
        foreach ($flags as $f) echo '<span class="bdesk-chip is-warn">' . esc_html($f) . '</span>';
    }
}, 10, 2);

add_filter('manage_edit-product_sortable_columns', function ($cols) {
    if (bdesk_products_on()) $cols['bdesk_sold'] = 'bdesk_sold';
    return $cols;
});

/* The date column is rarely needed here; staff can switch it back on in Screen Options. */
add_filter('default_hidden_columns', function ($hidden, $screen) {
    if ($screen && $screen->id === 'edit-product' && bdesk_products_on()) $hidden[] = 'date';
    return $hidden;
}, 10, 2);

/* ---------- Views, filters and ordering ---------- */
add_filter('views_edit-product', function ($views) {
    if (!bdesk_products_on()) return $views;
    global $wpdb;
    $cur = isset($_GET['bdesk_view']) ? sanitize_key($_GET['bdesk_view']) : '';
    $stock = isset($_GET['stock_status']) ? sanitize_key($_GET['stock_status']) : '';
    $link = function ($url, $label, $n, $on) {
        return '<a href="' . esc_url($url) . '"' . ($on ? ' class="current" aria-current="page"' : '') . '>' . esc_html($label) . ' <span class="count">(' . number_format_i18n($n) . ')</span></a>';
    };
    $base = admin_url('edit.php?post_type=product');
    $sold = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wc_product_meta_lookup l JOIN {$wpdb->posts} p ON p.ID = l.product_id WHERE p.post_type = 'product' AND p.post_status IN ('publish','draft','pending','private') AND l.stock_status = 'outofstock'");
    $sale = bdesk_count_where(bdesk_sql_sale());
    $attn = bdesk_count_where(bdesk_sql_attention());
    if ($sale) $views['bdesk_sale'] = $link(add_query_arg('bdesk_view', 'sale', $base), __('On sale', 'boutique-desk'), $sale, $cur === 'sale');
    if ($sold) $views['bdesk_sold'] = $link(add_query_arg('stock_status', 'outofstock', $base), __('Sold out', 'boutique-desk'), $sold, $stock === 'outofstock');
    if ($attn) $views['bdesk_attn'] = $link(add_query_arg('bdesk_view', 'attention', $base), __('Needs attention', 'boutique-desk'), $attn, $cur === 'attention');
    return $views;
});

function bdesk_is_product_list($query) {
    global $pagenow;
    return is_admin() && $pagenow === 'edit.php' && $query->is_main_query() && $query->get('post_type') === 'product' && bdesk_products_on();
}

add_filter('posts_where', function ($where, $query) {
    if (!bdesk_is_product_list($query)) return $where;
    global $wpdb;
    $view = isset($_GET['bdesk_view']) ? sanitize_key($_GET['bdesk_view']) : '';
    if ($view === 'sale') $where .= ' AND ' . bdesk_sql_sale();
    if ($view === 'attention') $where .= ' AND ' . bdesk_sql_attention();
    $cond = isset($_GET['bdesk_cond']) ? sanitize_text_field(wp_unslash($_GET['bdesk_cond'])) : '';
    if (in_array($cond, array('Renewed', 'Refurbished', 'New', 'Used'), true)) {
        $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", $wpdb->esc_like($cond) . '%');
    }
    return $where;
}, 10, 2);

add_action('pre_get_posts', function ($q) {
    if (!bdesk_is_product_list($q)) return;
    if ($q->get('orderby') === 'bdesk_sold') {
        $q->set('meta_key', 'total_sales');
        $q->set('orderby', 'meta_value_num');
    }
});

add_action('restrict_manage_posts', function ($type) {
    if ($type !== 'product' || !bdesk_products_on()) return;
    $cur = isset($_GET['bdesk_cond']) ? sanitize_text_field(wp_unslash($_GET['bdesk_cond'])) : '';
    echo '<select name="bdesk_cond" aria-label="' . esc_attr__('Condition', 'boutique-desk') . '"><option value="">' . esc_html__('Any condition', 'boutique-desk') . '</option>';
    foreach (array('Renewed', 'Refurbished', 'New', 'Used') as $c) echo '<option value="' . esc_attr($c) . '"' . selected($cur, $c, false) . '>' . esc_html($c) . '</option>';
    echo '</select>';
    $view = isset($_GET['bdesk_view']) ? sanitize_key($_GET['bdesk_view']) : '';
    if ($view) echo '<input type="hidden" name="bdesk_view" value="' . esc_attr($view) . '">';
});

/* Show 50 products a page until someone chooses otherwise in Screen Options. */
add_filter('edit_product_per_page', function ($n) {
    return (bdesk_products_on() && (int) $n === 20 && !get_user_option('edit_product_per_page')) ? 50 : $n;
});

/* ---------- One-click sold out / back in stock ---------- */
function bdesk_set_availability($product, $available) {
    if (!$product || $product->is_type('variable')) return false;
    if ($product->get_manage_stock()) $product->set_stock_quantity($available ? max(1, (int) $product->get_stock_quantity()) : 0);
    $product->set_stock_status($available ? 'instock' : 'outofstock');
    $product->save();
    return true;
}

add_filter('post_row_actions', function ($actions, $post) {
    if ($post->post_type !== 'product' || !bdesk_products_on() || !current_user_can('edit_post', $post->ID)) return $actions;
    $product = wc_get_product($post->ID);
    if (!$product || $product->is_type('variable')) return $actions;
    $out = !$product->is_in_stock();
    $url = wp_nonce_url(admin_url('admin-post.php?action=bdesk_stock&id=' . $post->ID . '&to=' . ($out ? 'in' : 'out')), 'bdesk_stock_' . $post->ID);
    $actions['bdesk_stock'] = '<a href="' . esc_url($url) . '" class="' . ($out ? 'bdesk-act-in' : 'bdesk-act-out') . '">' . esc_html($out ? __('Back in stock', 'boutique-desk') : __('Mark sold out', 'boutique-desk')) . '</a>';
    return $actions;
}, 10, 2);

add_action('admin_post_bdesk_stock', function () {
    $id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    check_admin_referer('bdesk_stock_' . $id);
    if (!current_user_can('edit_post', $id)) wp_die(esc_html__('You do not have permission to do this.', 'boutique-desk'));
    $to = isset($_GET['to']) && $_GET['to'] === 'in' ? 'in' : 'out';
    bdesk_set_availability(wc_get_product($id), $to === 'in');
    wp_safe_redirect(add_query_arg(array('bdesk_bulk' => $to === 'in' ? 'bdesk_in' : 'bdesk_out', 'bdesk_n' => 1), wp_get_referer() ?: admin_url('edit.php?post_type=product')));
    exit;
});

/* ---------- Bulk actions ---------- */
add_filter('bulk_actions-edit-product', function ($actions) {
    if (!bdesk_products_on()) return $actions;
    $actions['bdesk_out'] = __('Mark sold out', 'boutique-desk');
    $actions['bdesk_in'] = __('Mark back in stock', 'boutique-desk');
    $actions['bdesk_unsale'] = __('Remove sale price', 'boutique-desk');
    $actions['bdesk_draft'] = __('Hide from shop (draft)', 'boutique-desk');
    return $actions;
});

add_filter('handle_bulk_actions-edit-product', function ($redirect, $action, $ids) {
    if (!in_array($action, array('bdesk_out', 'bdesk_in', 'bdesk_unsale', 'bdesk_draft'), true)) return $redirect;
    $n = 0;
    foreach ((array) $ids as $id) {
        $id = absint($id);
        if (!current_user_can('edit_post', $id)) continue;
        $p = wc_get_product($id);
        if (!$p) continue;
        if ($action === 'bdesk_out' || $action === 'bdesk_in') {
            if (bdesk_set_availability($p, $action === 'bdesk_in')) $n++;
        } elseif ($action === 'bdesk_unsale') {
            if ($p->is_type('variable') || $p->get_sale_price() === '') continue;
            $p->set_sale_price('');
            $p->set_date_on_sale_from('');
            $p->set_date_on_sale_to('');
            $p->save();
            $n++;
        } else {
            if ($p->get_status() === 'draft') continue;
            $p->set_status('draft');
            $p->save();
            $n++;
        }
    }
    return add_query_arg(array('bdesk_bulk' => $action, 'bdesk_n' => $n), $redirect);
}, 10, 3);

add_action('admin_notices', function () {
    if (empty($_GET['bdesk_bulk'])) return;
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'edit-product') return;
    $n = isset($_GET['bdesk_n']) ? absint($_GET['bdesk_n']) : 0;
    $labels = array(
        'bdesk_out' => _n('%d product marked sold out.', '%d products marked sold out.', $n, 'boutique-desk'),
        'bdesk_in' => _n('%d product is back in stock.', '%d products are back in stock.', $n, 'boutique-desk'),
        'bdesk_unsale' => _n('Sale price removed from %d product.', 'Sale price removed from %d products.', $n, 'boutique-desk'),
        'bdesk_draft' => _n('%d product hidden from the shop.', '%d products hidden from the shop.', $n, 'boutique-desk'),
    );
    $k = sanitize_key($_GET['bdesk_bulk']);
    if (!isset($labels[$k])) return;
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(sprintf($labels[$k], $n)) . '</p></div>';
});
