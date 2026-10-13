<?php
/**
 * Product videos.
 *
 * Sources merged into the gallery (in this order):
 *  1. Theme "Product Video" meta box: MP4 upload (attachment) or YouTube / Vimeo / Instagram URL + poster.
 *  2. WooCommerce native "Product gallery videos" (WooCommerce ≥ 11, Settings → Advanced → Features).
 *  3. Common third-party plugin meta keys (filterable via `dastaan_product_video_meta_keys`).
 *
 * @package Dastaan
 */

defined( 'ABSPATH' ) || exit;

const DASTAAN_VIDEO_META        = '_dastaan_product_video';        // Attachment ID (mp4/webm).
const DASTAAN_VIDEO_URL_META    = '_dastaan_product_video_url';    // External URL.
const DASTAAN_VIDEO_POSTER_META = '_dastaan_product_video_poster'; // Poster attachment ID.

/**
 * Collect videos for a product.
 *
 * @param WC_Product $product Product.
 * @return array[] { src, embed, poster, title, source }
 */
function dastaan_get_product_videos( $product ) {
	$videos = array();
	$pid    = $product->get_id();

	// 1. Theme meta.
	$att_id    = (int) get_post_meta( $pid, DASTAAN_VIDEO_META, true );
	$url       = (string) get_post_meta( $pid, DASTAAN_VIDEO_URL_META, true );
	$poster_id = (int) get_post_meta( $pid, DASTAAN_VIDEO_POSTER_META, true );
	$poster    = $poster_id ? wp_get_attachment_image_url( $poster_id, 'woocommerce_single' ) : '';
	if ( $att_id && 0 === strpos( (string) get_post_mime_type( $att_id ), 'video/' ) ) {
		$videos[] = array(
			'src'    => wp_get_attachment_url( $att_id ),
			'embed'  => '',
			'poster' => $poster ? $poster : get_the_post_thumbnail_url( $att_id, 'woocommerce_single' ),
			'title'  => get_the_title( $att_id ),
			'source' => 'theme',
		);
	} elseif ( $url ) {
		if ( dastaan_is_video_file( $url ) ) {
			$videos[] = array( 'src' => esc_url_raw( $url ), 'embed' => '', 'poster' => $poster, 'title' => $product->get_name(), 'source' => 'theme' );
		} else {
			$embed = dastaan_video_embed_url( $url );
			if ( $embed ) {
				$videos[] = array( 'src' => '', 'embed' => $embed, 'poster' => $poster, 'title' => $product->get_name(), 'source' => 'theme', 'url' => esc_url_raw( $url ) );
			}
		}
	}

	// 1b. "WC Featured Video Gallery" plugin (Media Library video + optional cover image).
	$wcfv_id = (int) get_post_meta( $pid, '_wcfv_video_id', true );
	if ( $wcfv_id && 0 === strpos( (string) get_post_mime_type( $wcfv_id ), 'video/' ) ) {
		$wcfv_poster = (int) get_post_meta( $pid, '_wcfv_poster_id', true );
		$videos[]    = array(
			'src'    => wp_get_attachment_url( $wcfv_id ),
			'embed'  => '',
			'poster' => $wcfv_poster ? wp_get_attachment_image_url( $wcfv_poster, 'woocommerce_single' ) : '',
			'title'  => $product->get_name(),
			'source' => 'wcfv',
		);
	}

	// 2. WooCommerce native gallery videos.
	if ( class_exists( '\Automattic\WooCommerce\Internal\ProductGallery\ProductMediaGallery' ) ) {
		try {
			$cls = '\Automattic\WooCommerce\Internal\ProductGallery\ProductMediaGallery';
			if ( method_exists( $cls, 'is_feature_enabled' ) && $cls::is_feature_enabled() && method_exists( $cls, 'get_product_media_gallery_items' ) ) {
				$items = $cls::get_product_media_gallery_items( $product, array( 'include_product_image' => false ) );
				foreach ( $items as $item ) {
					if ( isset( $item['media_type'] ) && 'video' === $item['media_type'] && ! empty( $item['id'] ) ) {
						$vid_id  = (int) $item['id'];
						$pst_id  = ! empty( $item['poster_id'] ) ? (int) $item['poster_id'] : 0;
						$videos[] = array(
							'src'    => wp_get_attachment_url( $vid_id ),
							'embed'  => '',
							'poster' => $pst_id ? wp_get_attachment_image_url( $pst_id, 'woocommerce_single' ) : '',
							'title'  => get_the_title( $vid_id ),
							'source' => 'woocommerce',
						);
					}
				}
			}
		} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Internal API changed; silently skip.
		}
	}

	// 3. Third-party plugin keys (URL or attachment ID).
	$keys = apply_filters(
		'dastaan_product_video_meta_keys',
		array( '_video_url', '_product_video_url', '_woo_product_video', 'product_video_url', '_wc_product_video', '_ywcfav_video_url', 'wcpv_video_url' )
	);
	foreach ( $keys as $key ) {
		$val = get_post_meta( $pid, $key, true );
		if ( empty( $val ) || is_array( $val ) ) {
			continue;
		}
		if ( is_numeric( $val ) && 0 === strpos( (string) get_post_mime_type( (int) $val ), 'video/' ) ) {
			$videos[] = array( 'src' => wp_get_attachment_url( (int) $val ), 'embed' => '', 'poster' => '', 'title' => $product->get_name(), 'source' => 'plugin' );
		} elseif ( is_string( $val ) && dastaan_is_video_file( $val ) ) {
			$videos[] = array( 'src' => esc_url_raw( $val ), 'embed' => '', 'poster' => '', 'title' => $product->get_name(), 'source' => 'plugin' );
		} elseif ( is_string( $val ) && dastaan_video_embed_url( $val ) ) {
			$videos[] = array( 'src' => '', 'embed' => dastaan_video_embed_url( $val ), 'poster' => '', 'title' => $product->get_name(), 'source' => 'plugin', 'url' => esc_url_raw( $val ) );
		}
	}

	// De-duplicate by src/embed.
	$seen = array();
	$out  = array();
	foreach ( $videos as $v ) {
		$k = $v['src'] ? $v['src'] : $v['embed'];
		if ( ! $k || isset( $seen[ $k ] ) ) {
			continue;
		}
		$seen[ $k ] = true;
		$out[]      = $v;
	}

	return apply_filters( 'dastaan_product_videos', $out, $product );
}

/**
 * Unified media list (images + videos) for the gallery template.
 *
 * @param WC_Product $product Product.
 * @return array[] { type: image|video, id, src, full, srcset, sizes, alt, w, h, thumb, poster, embed }
 */
function dastaan_get_product_media( $product ) {
	$media     = array();
	$image_ids = array();
	if ( $product->get_image_id() ) {
		$image_ids[] = (int) $product->get_image_id();
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gid = (int) $gid;
		if ( $gid && ! in_array( $gid, $image_ids, true ) && 0 === strpos( (string) get_post_mime_type( $gid ), 'image/' ) ) {
			$image_ids[] = $gid;
		}
	}

	foreach ( $image_ids as $id ) {
		$full  = wp_get_attachment_image_src( $id, 'full' );
		$large = wp_get_attachment_image_src( $id, 'woocommerce_single' );
		$thumb = wp_get_attachment_image_src( $id, 'woocommerce_gallery_thumbnail' );
		if ( ! $large ) {
			continue;
		}
		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$media[] = array(
			'type'   => 'image',
			'id'     => $id,
			'src'    => $large[0],
			'w'      => $large[1],
			'h'      => $large[2],
			'srcset' => wp_get_attachment_image_srcset( $id, 'woocommerce_single' ),
			'sizes'  => '(min-width: 1024px) 50vw, 100vw',
			'full'   => $full ? $full[0] : $large[0],
			'full_w' => $full ? $full[1] : $large[1],
			'full_h' => $full ? $full[2] : $large[2],
			'thumb'  => $thumb ? $thumb[0] : $large[0],
			'alt'    => $alt ? $alt : $product->get_name(),
		);
	}

	$videos = dastaan_get_product_videos( $product );
	if ( $videos ) {
		$video_items = array();
		foreach ( $videos as $v ) {
			$video_items[] = array(
				'type'   => 'video',
				'id'     => 0,
				'src'    => $v['src'],
				'embed'  => $v['embed'],
				'poster' => $v['poster'] ? $v['poster'] : ( isset( $media[0] ) ? $media[0]['src'] : '' ),
				'thumb'  => $v['poster'] ? $v['poster'] : ( isset( $media[0] ) ? $media[0]['thumb'] : '' ),
				'alt'    => $v['title'] ? $v['title'] : $product->get_name(),
				'url'    => isset( $v['url'] ) ? $v['url'] : '',
			);
		}
		$pos = dastaan_option( 'product_video_position' );
		if ( 'first' === $pos || empty( $media ) ) {
			$media = array_merge( $video_items, $media );
		} elseif ( 'last' === $pos ) {
			$media = array_merge( $media, $video_items );
		} else {
			array_splice( $media, 1, 0, $video_items );
		}
	}

	if ( empty( $media ) ) {
		$media[] = array(
			'type'   => 'image',
			'id'     => 0,
			'src'    => wc_placeholder_img_src( 'woocommerce_single' ),
			'w'      => 1000,
			'h'      => 1333,
			'srcset' => '',
			'sizes'  => '',
			'full'   => wc_placeholder_img_src( 'full' ),
			'full_w' => 1000,
			'full_h' => 1333,
			'thumb'  => wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' ),
			'alt'    => $product->get_name(),
		);
	}

	return apply_filters( 'dastaan_product_media', $media, $product );
}

/* -------------------------------------------------------------------------
 * Meta box
 * ---------------------------------------------------------------------- */

/**
 * Register meta box.
 */
function dastaan_video_meta_box() {
	add_meta_box( 'dastaan_product_video', __( 'Product Video', 'dastaan' ), 'dastaan_video_meta_box_html', 'product', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'dastaan_video_meta_box' );

/**
 * Meta box HTML.
 *
 * @param WP_Post $post Post.
 */
function dastaan_video_meta_box_html( $post ) {
	wp_nonce_field( 'dastaan_video_save', 'dastaan_video_nonce' );
	$att_id    = (int) get_post_meta( $post->ID, DASTAAN_VIDEO_META, true );
	$url       = (string) get_post_meta( $post->ID, DASTAAN_VIDEO_URL_META, true );
	$poster_id = (int) get_post_meta( $post->ID, DASTAAN_VIDEO_POSTER_META, true );
	$att_name  = $att_id ? basename( (string) get_attached_file( $att_id ) ) : '';
	$poster    = $poster_id ? wp_get_attachment_image_url( $poster_id, 'thumbnail' ) : '';
	?>
	<div class="dastaan-video-box">
		<p><strong><?php esc_html_e( 'Upload MP4 / WebM', 'dastaan' ); ?></strong></p>
		<input type="hidden" name="dastaan_video_id" id="dastaan_video_id" value="<?php echo esc_attr( $att_id ); ?>">
		<p class="dastaan-video-name" style="word-break:break-all;color:#50575e"><?php echo $att_name ? esc_html( $att_name ) : esc_html__( 'No video selected', 'dastaan' ); ?></p>
		<p>
			<button type="button" class="button dastaan-pick-video"><?php esc_html_e( 'Select video', 'dastaan' ); ?></button>
			<button type="button" class="button-link-delete dastaan-clear-video" <?php echo $att_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'dastaan' ); ?></button>
		</p>
		<p><strong><?php esc_html_e( 'OR paste a link', 'dastaan' ); ?></strong><br><span class="description"><?php esc_html_e( 'YouTube, Vimeo, Instagram reel or direct .mp4 URL', 'dastaan' ); ?></span></p>
		<input type="url" class="widefat" name="dastaan_video_url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://">
		<p style="margin-top:12px"><strong><?php esc_html_e( 'Poster image (optional)', 'dastaan' ); ?></strong></p>
		<input type="hidden" name="dastaan_video_poster" id="dastaan_video_poster" value="<?php echo esc_attr( $poster_id ); ?>">
		<div class="dastaan-poster-preview" style="margin-bottom:6px"><?php echo $poster ? '<img src="' . esc_url( $poster ) . '" style="max-width:100%;height:auto;border-radius:4px" alt="">' : ''; ?></div>
		<p>
			<button type="button" class="button dastaan-pick-poster"><?php esc_html_e( 'Select poster', 'dastaan' ); ?></button>
			<button type="button" class="button-link-delete dastaan-clear-poster" <?php echo $poster_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'dastaan' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'The video is placed in the gallery (position set in Customizer → Product Page), opens in the lightbox and is published as VideoObject schema.', 'dastaan' ); ?></p>
	</div>
	<?php
}

/**
 * Save meta.
 *
 * @param int $post_id Post ID.
 */
function dastaan_video_meta_save( $post_id ) {
	if ( ! isset( $_POST['dastaan_video_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dastaan_video_nonce'] ) ), 'dastaan_video_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$video_id = isset( $_POST['dastaan_video_id'] ) ? absint( $_POST['dastaan_video_id'] ) : 0;
	if ( $video_id && 0 !== strpos( (string) get_post_mime_type( $video_id ), 'video/' ) ) {
		$video_id = 0; // Only real video attachments.
	}
	$poster_id = isset( $_POST['dastaan_video_poster'] ) ? absint( $_POST['dastaan_video_poster'] ) : 0;
	if ( $poster_id && ! wp_attachment_is_image( $poster_id ) ) {
		$poster_id = 0;
	}
	$url = isset( $_POST['dastaan_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['dastaan_video_url'] ) ) : '';
	if ( $url && ! dastaan_is_video_file( $url ) && ! dastaan_video_embed_url( $url ) ) {
		$url = ''; // Unsupported host.
	}

	foreach ( array( DASTAAN_VIDEO_META => $video_id, DASTAAN_VIDEO_URL_META => $url, DASTAAN_VIDEO_POSTER_META => $poster_id ) as $key => $val ) {
		if ( $val ) {
			update_post_meta( $post_id, $key, $val );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}
add_action( 'save_post_product', 'dastaan_video_meta_save' );

/**
 * Media picker JS for the meta box.
 *
 * @param string $hook Hook.
 */
function dastaan_video_admin_js( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== get_post_type() ) {
		return;
	}
	wp_enqueue_media();
	$js = <<<'JS'
(function(){
	function picker(btnSel, inputSel, type, onPick){
		document.addEventListener('click', function(e){
			var btn = e.target.closest(btnSel); if(!btn) return; e.preventDefault();
			var frame = wp.media({ title: btn.textContent, library: { type: type }, multiple: false, button: { text: btn.textContent } });
			frame.on('select', function(){ var a = frame.state().get('selection').first().toJSON(); document.querySelector(inputSel).value = a.id; onPick(a); });
			frame.open();
		});
	}
	picker('.dastaan-pick-video', '#dastaan_video_id', 'video', function(a){
		document.querySelector('.dastaan-video-name').textContent = a.filename || a.url; document.querySelector('.dastaan-clear-video').style.display='';
	});
	picker('.dastaan-pick-poster', '#dastaan_video_poster', 'image', function(a){
		var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
		document.querySelector('.dastaan-poster-preview').innerHTML = '<img src="'+src+'" style="max-width:100%;height:auto;border-radius:4px" alt="">'; document.querySelector('.dastaan-clear-poster').style.display='';
	});
	document.addEventListener('click', function(e){
		if(e.target.closest('.dastaan-clear-video')){ e.preventDefault(); document.querySelector('#dastaan_video_id').value=''; document.querySelector('.dastaan-video-name').textContent='No video selected'; e.target.style.display='none'; }
		if(e.target.closest('.dastaan-clear-poster')){ e.preventDefault(); document.querySelector('#dastaan_video_poster').value=''; document.querySelector('.dastaan-poster-preview').innerHTML=''; e.target.style.display='none'; }
	});
})();
JS;
	wp_add_inline_script( 'media-editor', $js );
}
add_action( 'admin_enqueue_scripts', 'dastaan_video_admin_js' );

/**
 * Video icon on product cards that have a video (lets shoppers know a reel exists).
 *
 * @param WC_Product $product Product.
 * @return bool
 */
function dastaan_product_has_video( $product ) {
	$pid = $product->get_id();
	if ( get_post_meta( $pid, DASTAAN_VIDEO_META, true ) || get_post_meta( $pid, DASTAAN_VIDEO_URL_META, true ) || get_post_meta( $pid, '_wcfv_video_id', true ) ) {
		return true;
	}
	if ( get_post_meta( $pid, '_wc_video_gallery', true ) ) {
		return true;
	}

	return false;
}

/**
 * The theme gallery already shows "WC Featured Video Gallery" videos; drop that plugin's
 * front-end assets (built for another theme) so nothing is loaded twice.
 */
function dastaan_dequeue_wcfv_assets() {
	wp_dequeue_style( 'wcfv' );
	wp_dequeue_script( 'wcfv' );
}
add_action( 'wp_enqueue_scripts', 'dastaan_dequeue_wcfv_assets', 100 );
