<?php
if (!defined('ABSPATH')) exit;

/*
 * Store Editor: adds and edits products, processes orders, prints invoices and slips.
 * Cannot delete anything, change settings, see coupons, analytics, plugins, themes or users.
 */
function bdesk_role_caps() {
    return array(
        'read' => true,
        'upload_files' => true,
        // Products: add and edit, never delete.
        'edit_products' => true,
        'edit_others_products' => true,
        'edit_published_products' => true,
        'edit_private_products' => true,
        'publish_products' => true,
        'read_product' => true,
        'read_private_products' => true,
        'assign_product_terms' => true,
        // Orders: view and update status, never delete.
        'edit_shop_orders' => true,
        'edit_others_shop_orders' => true,
        'edit_published_shop_orders' => true,
        'edit_private_shop_orders' => true,
        'read_shop_order' => true,
        'read_private_shop_orders' => true,
        'publish_shop_orders' => true,
    );
}

function bdesk_install_role() {
    remove_role('bdesk_editor');
    add_role('bdesk_editor', __('Store Editor', 'boutique-desk'), bdesk_role_caps());
}

function bdesk_remove_role() {
    remove_role('bdesk_editor');
}

/* Keep the role in step with the plugin version without needing re-activation. */
add_action('admin_init', function () {
    if (get_option('bdesk_role_version') !== BDESK_VERSION) {
        bdesk_install_role();
        update_option('bdesk_role_version', BDESK_VERSION);
    }
});

function bdesk_is_editor($user = null) {
    $user = $user ?: wp_get_current_user();
    return $user && in_array('bdesk_editor', (array) $user->roles, true) && !user_can($user, 'manage_options');
}

/* Land on the Store Desk after login. */
add_filter('login_redirect', function ($to, $requested, $user) {
    if ($user instanceof WP_User && bdesk_is_editor($user)) return admin_url('admin.php?page=bdesk');
    return $to;
}, 20, 3);

/* WooCommerce sends non-managers away from wp-admin; let Store Editors in. */
add_filter('woocommerce_prevent_admin_access', function ($prevent) {
    return bdesk_is_editor() ? false : $prevent;
});

/* Only the menus a Store Editor needs. */
add_action('admin_menu', function () {
    if (!bdesk_is_editor()) return;
    global $menu, $submenu;
    $keep = array('bdesk', 'edit.php?post_type=product', 'woocommerce', 'upload.php', 'profile.php');
    foreach ((array) $menu as $item) {
        if (isset($item[2]) && !in_array($item[2], $keep, true)) remove_menu_page($item[2]);
    }
    if (isset($submenu['woocommerce'])) {
        foreach ($submenu['woocommerce'] as $i => $sub) {
            if (!in_array($sub[2], array('wc-orders', 'edit.php?post_type=shop_order'), true)) unset($submenu['woocommerce'][$i]);
        }
    }
    if (isset($submenu['edit.php?post_type=product'])) {
        foreach ($submenu['edit.php?post_type=product'] as $i => $sub) {
            if (!in_array($sub[2], array('edit.php?post_type=product', 'post-new.php?post_type=product'), true)) unset($submenu['edit.php?post_type=product'][$i]);
        }
    }
}, 9999);

/* The standard dashboard and blocked screens send the editor to the Store Desk. */
add_action('current_screen', function ($screen) {
    if (!bdesk_is_editor() || !$screen) return;
    $is_order_screen = in_array($screen->id, bdesk_order_screens(), true) || in_array($screen->id, array('edit-shop_order', 'woocommerce_page_wc-orders'), true);
    $is_product_screen = in_array($screen->post_type, array('product', 'attachment'), true);
    if ($is_order_screen || $is_product_screen || in_array($screen->base, array('toplevel_page_bdesk', 'upload', 'media', 'profile'), true)) return;
    wp_safe_redirect(admin_url('admin.php?page=bdesk'));
    exit;
});

/* A quieter admin bar. */
add_action('admin_bar_menu', function ($bar) {
    if (!bdesk_is_editor()) return;
    foreach (array('comments', 'new-content', 'updates', 'wp-logo', 'customize') as $node) $bar->remove_node($node);
}, 999);
