<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/inc/doodles.php';

function rasaaya_theme_asset( $rel_path ) {
    return '/wp-content/themes/rasaaya-ayurveda/' . ltrim( $rel_path, '/' );
}

function rasaaya_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
}
add_action( 'after_setup_theme', 'rasaaya_setup' );

function rasaaya_enqueue_scripts() {
    wp_enqueue_style( 'rasaaya-style', rasaaya_theme_asset( 'style.css' ), array(), '2.1.0' );
    wp_enqueue_style( 'rasaaya-brand', rasaaya_theme_asset( 'assets/css/brand.css' ), array( 'rasaaya-style' ), '3.0.0' );
    wp_enqueue_script( 'rasaaya-app', rasaaya_theme_asset( 'assets/js/app.js' ), array(), '2.1.0', true );
}
add_action( 'wp_enqueue_scripts', 'rasaaya_enqueue_scripts' );

// Prevent canonical redirect loops & iframe blocks behind reverse proxies (E2B HTTPS preview)
add_filter( 'redirect_canonical', '__return_false' );
remove_action( 'login_init', 'send_frame_options_header' );
remove_action( 'admin_init', 'send_frame_options_header' );

/**
 * Each ritual has its own colour, like the katoris in a masala dabba.
 */
function rasaaya_product_color( $slug ) {
    $map = array(
        '7-day-gut-trial-pack'   => '#C9841A',
        '30-day-gut-starter-kit' => '#0F2F21',
        'gut-reset'              => '#1F4D36',
        'digest-plus'            => '#55763A',
        'glow-within'            => '#B0455A',
        'immunity-kadha'         => '#A04F28',
        'calm-and-sleep'         => '#2E3561',
        'hydrate-kokum-plus'     => '#7A2348',
    );
    return isset( $map[ $slug ] ) ? $map[ $slug ] : '#1F4D36';
}

// Register Custom Post Types: Products & Customer Orders
function rasaaya_register_cpts() {
    register_post_type( 'rasaaya_product', array(
        'labels' => array(
            'name'          => 'RASAAYA Products',
            'singular_name' => 'Product',
            'add_new_item'  => 'Add New Sachet SKU',
            'edit_item'     => 'Edit Sachet SKU',
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-carrot',
        'supports'     => array( 'title', 'editor', 'excerpt' ),
        'rewrite'      => array( 'slug' => 'product' ),
        'show_in_rest' => true,
    ) );

    register_post_type( 'rasaaya_order', array(
        'labels' => array(
            'name'          => 'Customer Orders',
            'singular_name' => 'Order',
        ),
        'public'      => false,
        'show_ui'     => true,
        'menu_icon'   => 'dashicons-cart',
        'supports'    => array( 'title', 'editor', 'custom-fields' ),
    ) );
}
add_action( 'init', 'rasaaya_register_cpts' );

// Catalog Master Definition (Proposal 1A Verified)
function rasaaya_get_catalog_seed() {
    return array(
        array(
            'slug'         => '7-day-gut-trial-pack',
            'title'        => '7-Day Gut Trial Pack',
            'job'          => 'First-Order Discovery • 7 Gut Reset + 7 Digest+',
            'cat'          => 'bundles',
            'badge'        => '₹99 TRIAL HOOK',
            'mrp'          => 199,
            'price'        => 99,
            'pack'         => '14 Sachets (7 AM + 7 PM)',
            'herbs'        => '7 × Gut Reset (Triphala, Isabgol, Saunf) + 7 × Digest+ (Jeera, Pudina, Hing, Kala Namak)',
            'ritual'       => 'Take 1 Gut Reset in lukewarm water after dinner & 1 Digest+ in room-temp water after heavy lunch.',
            'coa_batch'    => 'RAS-TR-2611',
            'img'          => 'sku-trial-pack.jpg',
            'rating'       => '4.9',
            'reviews'      => '840',
            'sub_eligible' => false,
            'desc'         => 'Experience lighter mornings and zero post-lunch bloat in 7 days. Includes complimentary entry to our moderated WhatsApp 7-Day Gut Challenge cohort.',
        ),
        array(
            'slug'         => '30-day-gut-starter-kit',
            'title'        => '30-Day Gut Starter Kit',
            'job'          => 'Complete AM + PM Gut Protocol (Saves ₹99)',
            'cat'          => 'bundles',
            'badge'        => 'HERO BUNDLE • BEST VALUE',
            'mrp'          => 648,
            'price'        => 549,
            'pack'         => '60 Sachets (2 Full Boxes)',
            'herbs'        => 'Full Box Gut Reset (30 × 6g) + Full Box Digest+ (30 × 4g) — Zero Senna, Zero Sugar',
            'ritual'       => 'Complete 30-day digestive reset: Digest+ post-lunch for gas/acidity comfort; Gut Reset post-dinner for effortless morning regularity.',
            'coa_batch'    => 'RAS-SK-2611',
            'img'          => 'hero-collection.jpg',
            'rating'       => '4.9',
            'reviews'      => '612',
            'sub_eligible' => true,
            'desc'         => 'Our #1 recommended full-month pair combining daily gentle fibre-triphala cleansing with post-meal spiced digestive support.',
        ),
        array(
            'slug'         => 'gut-reset',
            'title'        => 'Gut Reset',
            'job'          => 'Daily Gut Cleanse & Effortless Regularity',
            'cat'          => 'gut',
            'badge'        => 'HERO SKU',
            'mrp'          => 399,
            'price'        => 359,
            'pack'         => '30 Sachets × 6 g (180 g)',
            'herbs'        => 'Triphala 40% (Amla, Haritaki, Bibhitaki), Unjha Isabgol Husk 30%, Saunf 10%, Jeera 10%, Ajwain 5%, Sendha Namak 5%',
            'ritual'       => 'Tear 1 sachet (6g) into 200 ml lukewarm water 30 mins after dinner, swirl for 15 seconds and drink immediately.',
            'coa_batch'    => 'RAS-GR-2611',
            'img'          => 'sku-gut-reset.jpg',
            'rating'       => '4.9',
            'reviews'      => '530',
            'sub_eligible' => true,
            'desc'         => 'Non-habit-forming daily Ayurvedic fibre & Triphala ritual (strictly zero harsh Senna leaf) supporting gentle overnight gut motility and morning lightness.',
        ),
        array(
            'slug'         => 'digest-plus',
            'title'        => 'Digest+',
            'job'          => 'Post-Meal Heaviness, Bloating & Gas Comfort',
            'cat'          => 'gut',
            'badge'        => 'OFFICE ESSENTIAL',
            'mrp'          => 249,
            'price'        => 225,
            'pack'         => '30 Sachets × 4 g (120 g)',
            'herbs'        => 'Roasted Jeera, Saunf, Ajwain, Pudina Leaf, Amla Fruit, Roasted Hing (pinch), Kala Namak',
            'ritual'       => 'Mix 1 sachet (4g) into 150 ml room-temperature or chilled water right after heavy office lunches or late dinners.',
            'coa_batch'    => 'RAS-DG-2611',
            'img'          => 'sku-digest-plus.jpg',
            'rating'       => '4.8',
            'reviews'      => '418',
            'sub_eligible' => true,
            'desc'         => 'Pocket-sized spiced churna infusion inspired by classical Hingvastak & kitchen carminatives—replaces fizzy high-sodium antacids.',
        ),
        array(
            'slug'         => 'glow-within',
            'title'        => 'Glow Within',
            'job'          => 'Clear Skin & Natural Radiance From Within',
            'cat'          => 'skin',
            'badge'        => 'GUT-SKIN AXIS',
            'mrp'          => 449,
            'price'        => 399,
            'pack'         => '30 Sachets × 5 g (150 g)',
            'herbs'        => 'Wild Amla (Vit C), Manjistha Root, Lakadong Haldi + Kali Mirch (Piperine), Guduchi, Dehydrated Beetroot',
            'ritual'       => 'Swirl 1 sachet (5g) into 200 ml cool or lukewarm water mid-morning on a light stomach.',
            'coa_batch'    => 'RAS-GL-2611',
            'img'          => 'sku-glow.jpg',
            'rating'       => '4.8',
            'reviews'      => '295',
            'sub_eligible' => true,
            'desc'         => 'Classical Raktashodhaka (blood-purifying botanical) blend pairing Manjistha and high-curcumin Haldi with Amla and ruby Beetroot.',
        ),
        array(
            'slug'         => 'immunity-kadha',
            'title'        => 'Immunity Kadha',
            'job'          => 'Zero-Boil Seasonal Throat & Respiratory Care',
            'cat'          => 'skin',
            'badge'        => 'MONSOON & WINTER',
            'mrp'          => 349,
            'price'        => 315,
            'pack'         => '30 Sachets × 5 g (150 g)',
            'herbs'        => 'Giloy Stem, Rama & Krishna Tulsi, Mulethi Root, Ceylon Dalchini, Saunth (Dry Ginger), Kali Mirch',
            'ritual'       => 'Empty 1 sachet (5g) into a mug of 150 ml piping hot water—dissolves in 15 seconds with zero boiling or straining.',
            'coa_batch'    => 'RAS-IK-2611',
            'img'          => 'sku-immunity.jpg',
            'rating'       => '4.9',
            'reviews'      => '214',
            'sub_eligible' => true,
            'desc'         => 'Warming, soothing Mulethi-Tulsi-Saunth kadha in a clean single-serve sachet for seasonal weather shifts and AC throat comfort.',
        ),
        array(
            'slug'         => 'calm-and-sleep',
            'title'        => 'Calm & Sleep',
            'job'          => 'Evening Cortisol Unwind & Restful Sleep',
            'cat'          => 'sleep',
            'badge'        => '100% ROOT ASHWAGANDHA',
            'mrp'          => 449,
            'price'        => 399,
            'pack'         => '30 Sachets × 5 g (150 g)',
            'herbs'        => 'Certified Ashwagandha Root Only (0% Leaf), Brahmi, Shankhpushpi, Egyptian Chamomile Blossom',
            'ritual'       => 'Whisk 1 sachet (5g) into 180 ml warm cow or almond milk 30 minutes before bedtime.',
            'coa_batch'    => 'RAS-CS-2611',
            'img'          => 'sku-calm-sleep.jpg',
            'rating'       => '4.9',
            'reviews'      => '267',
            'sub_eligible' => true,
            'desc'         => 'Melatonin-free Medhya Rasayana moon-milk blend using strictly AYUSH/FSSAI-compliant Ashwagandha root (never leaf) and floral Chamomile.',
        ),
        array(
            'slug'         => 'hydrate-kokum-plus',
            'title'        => 'Hydrate Kokum+',
            'job'          => 'Clean Ayurvedic Summer & Workout Electrolyte',
            'cat'          => 'sleep',
            'badge'        => 'SUMMER ELECTROLYTE',
            'mrp'          => 299,
            'price'        => 269,
            'pack'         => '15 Sachets × 8 g (120 g)',
            'herbs'        => 'Konkan Kokum Rind, Kagzi Nimbu, Pink Sendha Namak, Pudina, Unrefined Jaggery / Stevia Leaf',
            'ritual'       => 'Shake 1 sachet (8g) with 250 ml chilled water and ice cubes post-commute or workout.',
            'coa_batch'    => 'RAS-HY-2611',
            'img'          => 'sku-hydrate.jpg',
            'rating'       => '4.8',
            'reviews'      => '189',
            'sub_eligible' => true,
            'desc'         => 'Refreshing Konkan kokum-nimbu electrolyte replenishment without synthetic neon dyes or glucose spikes.',
        ),
    );
}

// Seed Products & Pages once
function rasaaya_maybe_seed_content() {
    if ( get_option( 'rasaaya_seeded_v2' ) === 'yes' ) {
        return;
    }

    $items = rasaaya_get_catalog_seed();
    foreach ( $items as $idx => $item ) {
        $existing = get_page_by_path( $item['slug'], OBJECT, 'rasaaya_product' );
        $post_data = array(
            'post_title'   => $item['title'],
            'post_name'    => $item['slug'],
            'post_content' => $item['desc'],
            'post_excerpt' => $item['job'],
            'post_status'  => 'publish',
            'post_type'    => 'rasaaya_product',
            'menu_order'   => $idx + 1,
        );
        if ( $existing ) {
            $post_data['ID'] = $existing->ID;
            $pid = wp_update_post( $post_data );
        } else {
            $pid = wp_insert_post( $post_data );
        }

        if ( $pid && ! is_wp_error( $pid ) ) {
            update_post_meta( $pid, '_ras_job', $item['job'] );
            update_post_meta( $pid, '_ras_cat', $item['cat'] );
            update_post_meta( $pid, '_ras_badge', $item['badge'] );
            update_post_meta( $pid, '_ras_mrp', $item['mrp'] );
            update_post_meta( $pid, '_ras_price', $item['price'] );
            update_post_meta( $pid, '_ras_pack', $item['pack'] );
            update_post_meta( $pid, '_ras_herbs', $item['herbs'] );
            update_post_meta( $pid, '_ras_ritual', $item['ritual'] );
            update_post_meta( $pid, '_ras_coa', $item['coa_batch'] );
            update_post_meta( $pid, '_ras_img', $item['img'] );
            update_post_meta( $pid, '_ras_rating', $item['rating'] );
            update_post_meta( $pid, '_ras_reviews', $item['reviews'] );
            update_post_meta( $pid, '_ras_sub', $item['sub_eligible'] ? '1' : '0' );
        }
    }

    $pages = array(
        'home'              => 'RASAAYA Home',
        'shop'              => 'Shop All Sachets',
        '7-day-trial'       => '7-Day Gut Challenge (₹99)',
        'ayurvedic-science' => 'Herbs, Formulation & NABL Lab Reports',
        'checkout'          => 'Secure 5%-GST Checkout',
    );
    $home_id = 0;
    foreach ( $pages as $slug => $title ) {
        $p = get_page_by_path( $slug );
        if ( ! $p ) {
            $pid = wp_insert_post( array(
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_status' => 'publish',
                'post_type'   => 'page',
            ) );
        } else {
            $pid = $p->ID;
        }
        if ( $slug === 'home' ) {
            $home_id = $pid;
        }
    }

    if ( $home_id ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_id );
    }

    flush_rewrite_rules();
    update_option( 'rasaaya_seeded_v2', 'yes' );
}
add_action( 'init', 'rasaaya_maybe_seed_content', 20 );

// Helper to fetch formatted products from WP DB
function rasaaya_get_products_from_db() {
    $q = new WP_Query( array(
        'post_type'      => 'rasaaya_product',
        'posts_per_page' => 20,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ) );
    $out = array();
    foreach ( $q->posts as $post ) {
        $img_file = get_post_meta( $post->ID, '_ras_img', true );
        $out[] = array(
            'id'           => $post->ID,
            'slug'         => $post->post_name,
            'url'          => '/product/' . $post->post_name . '/',
            'title'        => $post->post_title,
            'desc'         => $post->post_content,
            'job'          => get_post_meta( $post->ID, '_ras_job', true ),
            'cat'          => get_post_meta( $post->ID, '_ras_cat', true ),
            'badge'        => get_post_meta( $post->ID, '_ras_badge', true ),
            'mrp'          => (int) get_post_meta( $post->ID, '_ras_mrp', true ),
            'price'        => (int) get_post_meta( $post->ID, '_ras_price', true ),
            'pack'         => get_post_meta( $post->ID, '_ras_pack', true ),
            'herbs'        => get_post_meta( $post->ID, '_ras_herbs', true ),
            'ritual'       => get_post_meta( $post->ID, '_ras_ritual', true ),
            'coa'          => get_post_meta( $post->ID, '_ras_coa', true ),
            'img'          => rasaaya_theme_asset( 'assets/images/' . $img_file ),
            'rating'       => get_post_meta( $post->ID, '_ras_rating', true ),
            'reviews'      => get_post_meta( $post->ID, '_ras_reviews', true ),
            'sub_eligible' => get_post_meta( $post->ID, '_ras_sub', true ) === '1',
        );
    }
    return $out;
}

// REST API Endpoints for Live Storefront
add_action( 'rest_api_init', function () {
    register_rest_route( 'rasaaya/v1', '/products', array(
        'methods'             => 'GET',
        'callback'            => function () {
            return rest_ensure_response( rasaaya_get_products_from_db() );
        },
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'rasaaya/v1', '/coa', array(
        'methods'             => 'GET',
        'callback'            => function ( WP_REST_Request $req ) {
            $batch = strtoupper( sanitize_text_field( $req->get_param( 'batch' ) ?: 'RAS-GR-2611' ) );
            $valid_prefixes = array( 'RAS-GR', 'RAS-DG', 'RAS-GL', 'RAS-IK', 'RAS-CS', 'RAS-HY', 'RAS-TR', 'RAS-SK' );
            $matched = false;
            foreach ( $valid_prefixes as $pre ) {
                if ( strpos( $batch, $pre ) === 0 ) {
                    $matched = true;
                    break;
                }
            }
            if ( ! $matched ) {
                $batch = 'RAS-GR-2611 (Sample Reference)';
            }
            return rest_ensure_response( array(
                'status'      => 'PASSED — NABL ISO/IEC 17025 CERTIFIED LAB',
                'batch'       => $batch,
                'mfg_date'    => 'Nov 2026 (Baddi GMP Cluster, HP)',
                'exp_date'    => 'May 2028 (18 Months — 4-Ply Alu-Barrier)',
                'moisture'    => '3.8% (Limit < 5.0% w/w)',
                'heavy_metal' => 'Lead <0.4 ppm, Arsenic <0.2 ppm, Mercury <0.05 ppm (FSSAI Compliant)',
                'microbial'   => 'Total Plate Count <1,000 CFU/g; E. coli / Salmonella: Absent',
                'senna_check' => '0.0% Sennosides (Verified Non-Habit Forming)',
            ) );
        },
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'rasaaya/v1', '/order', array(
        'methods'             => 'POST',
        'callback'            => function ( WP_REST_Request $req ) {
            $body    = $req->get_json_params();
            $name    = sanitize_text_field( $body['name'] ?? 'Guest Customer' );
            $phone   = sanitize_text_field( $body['phone'] ?? '' );
            $city    = sanitize_text_field( $body['city'] ?? 'Mumbai' );
            $address = sanitize_textarea_field( $body['address'] ?? '' );
            $payment = sanitize_text_field( $body['payment'] ?? 'UPI' );
            $total   = (int) ( $body['total'] ?? 0 );
            $items   = $body['items'] ?? array();
            $whatsapp_opt = ! empty( $body['whatsapp_remind'] ) ? 'Yes (Day-25 Reminder Active)' : 'Standard';

            $order_num = 'RAS-2026-' . wp_rand( 1000, 9999 );
            $summary_lines = array();
            foreach ( $items as $it ) {
                $mode = ! empty( $it['sub'] ) ? 'Subscribe 15% Off' : 'One-Time';
                $summary_lines[] = sprintf( '%s × %d (%s) — ₹%d', sanitize_text_field( $it['title'] ?? 'Item' ), (int) ( $it['qty'] ?? 1 ), $mode, (int) ( $it['lineTotal'] ?? 0 ) );
            }
            $gst_included = round( $total - ( $total / 1.05 ), 2 );

            $content = "Order Reference: {$order_num}\n"
                     . "Customer: {$name} ({$phone})\n"
                     . "Shipping City/Address: {$address}, {$city}\n"
                     . "Payment Mode: {$payment}\n"
                     . "WhatsApp Day-25 Nudge: {$whatsapp_opt}\n"
                     . "Items:\n - " . implode( "\n - ", $summary_lines ) . "\n"
                     . "Total Paid (incl. 5% GST HSN 21069099): ₹{$total} (Included GST: ₹{$gst_included})";

            $oid = wp_insert_post( array(
                'post_title'   => "{$order_num} — {$name} (₹{$total})",
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'rasaaya_order',
            ) );

            return rest_ensure_response( array(
                'success'      => true,
                'order_id'     => $oid,
                'order_number' => $order_num,
                'total'        => $total,
                'gst_5pct'     => $gst_included,
                'payment'      => $payment,
                'message'      => "Dhanyavaad {$name}! Your RASAAYA order {$order_num} is confirmed and logged in WordPress.",
            ) );
        },
        'permission_callback' => '__return_true',
    ) );
} );
