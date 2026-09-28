<?php
/**
 * Bulk image + PDF importer.
 *
 * Put files in wp-content/uploads/dgf-import/ (any sub-folders). Each file is
 * matched to a page by its file name or folder name (page slug, page title or
 * common aliases such as "EPDM", "turf", "interlocking"), imported into the
 * Media Library, attached to that page and — for the first image — set as the
 * page's featured (hero) image. PDFs become catalogue buttons on the matching
 * product page and on the Catalogues page.
 *
 * @package DGF
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalise text for matching.
 *
 * @param string $text Text.
 * @return string
 */
function dgf_norm( $text ) {
	$text = strtolower( remove_accents( (string) $text ) );
	$text = preg_replace( '/[^a-z0-9]+/', ' ', $text );
	return trim( preg_replace( '/\s+/', ' ', $text ) );
}

/**
 * Strip numbering and camera noise from a file name.
 *
 * @param string $name File name without extension.
 * @return string
 */
function dgf_clean_name( $name ) {
	$name = dgf_norm( $name );
	$name = preg_replace( '/\b(img|image|photo|pic|dsc|whatsapp image|wa|copy|final|new|edited|scaled|\d{6,}|\d{1,2})\b/', ' ', $name );
	return trim( preg_replace( '/\s+/', ' ', $name ) );
}

/**
 * Match candidates for each page: slug, title, aliases.
 *
 * @return array<string,array{id:int,terms:string[]}>
 */
function dgf_match_table() {
	$table = array();
	foreach ( dgf_pages_data() as $slug => $page ) {
		$post = dgf_get_page( $slug );
		if ( ! $post ) {
			continue;
		}
		$terms   = array( dgf_norm( $slug ), dgf_norm( $page['title'] ), dgf_norm( $post->post_title ) );
		$aliases = get_post_meta( $post->ID, '_dgf_aliases', true );
		$aliases = $aliases ? explode( ',', $aliases ) : ( isset( $page['aliases'] ) ? $page['aliases'] : array() );
		foreach ( $aliases as $alias ) {
			$terms[] = dgf_norm( $alias );
		}
		$table[ $slug ] = array(
			'id'    => (int) $post->ID,
			'terms' => array_values( array_unique( array_filter( $terms ) ) ),
		);
	}
	return $table;
}

/**
 * Best page for a name, or '' when nothing matches.
 *
 * @param string $name  Normalised name.
 * @param array  $table Match table.
 * @return string Slug.
 */
function dgf_best_page( $name, $table ) {
	if ( '' === $name ) {
		return '';
	}
	$best  = '';
	$score = 0;
	$words = explode( ' ', $name );
	foreach ( $table as $slug => $row ) {
		foreach ( $row['terms'] as $i => $term ) {
			$s = 0;
			if ( $name === $term ) {
				$s = 1000 + strlen( $term );
			} elseif ( preg_match( '/(^| )' . preg_quote( $term, '/' ) . '( |$)/', $name ) ) {
				// Whole term found inside the name; longer (more specific) terms win.
				$s = 100 + strlen( $term ) - ( $i > 2 ? 5 : 0 );
			} else {
				// Partial overlap of meaningful words.
				$term_words = array_filter(
					explode( ' ', $term ),
					static function ( $w ) {
						return strlen( $w ) > 3 && ! in_array( $w, array( 'flooring', 'floor', 'floors', 'gym' ), true );
					}
				);
				if ( $term_words ) {
					$hits = count( array_intersect( $term_words, $words ) );
					if ( $hits === count( $term_words ) ) {
						$s = 50 + $hits * 5;
					}
				}
			}
			if ( $s > $score ) {
				$score = $s;
				$best  = $slug;
			}
		}
	}
	return $best;
}

/**
 * Import everything in uploads/dgf-import.
 *
 * @param string $dir Optional folder.
 * @return array{imported:int,featured:int,skipped:int,notes:string[]}
 */
function dgf_import_images( $dir = '' ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$result = array(
		'imported' => 0,
		'featured' => 0,
		'skipped'  => 0,
		'notes'    => array(),
	);
	if ( '' === $dir ) {
		$upload = wp_upload_dir();
		$dir    = trailingslashit( $upload['basedir'] ) . 'dgf-import';
	}
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
		$result['notes'][] = __( 'Import folder created — add your files and run the import again.', 'dgf' );
		return $result;
	}

	$table = dgf_match_table();
	$files = array();
	$it    = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $it as $file ) {
		if ( $file->isFile() ) {
			$files[] = $file->getPathname();
		}
	}
	natcasesort( $files );

	foreach ( $files as $path ) {
		$ext      = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		$relative = ltrim( substr( $path, strlen( $dir ) ), '/\\' );
		$is_image = in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp', 'gif', 'avif' ), true );
		$is_pdf   = 'pdf' === $ext;

		if ( in_array( $ext, array( 'heic', 'heif' ), true ) ) {
			++$result['skipped'];
			$result['notes'][] = sprintf( __( '%s: HEIC is not supported by browsers — export it as JPG first.', 'dgf' ), $relative );
			continue;
		}
		if ( ! $is_image && ! $is_pdf ) {
			continue;
		}

		$source_key = md5( $relative . '|' . filesize( $path ) );
		$already    = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'meta_key'       => '_dgf_imported', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $source_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		);
		if ( $already ) {
			continue;
		}

		// A file called "logo…" becomes the site logo (Customize → Site Identity) if none is set.
		if ( $is_image && false !== strpos( dgf_norm( pathinfo( $path, PATHINFO_FILENAME ) ), 'logo' ) ) {
			$tmp = wp_tempnam( basename( $path ) );
			if ( $tmp && copy( $path, $tmp ) ) {
				$logo_id = media_handle_sideload( array( 'name' => sanitize_file_name( basename( $path ) ), 'tmp_name' => $tmp ), 0, dgf_opt( 'brand_name' ) . ' logo' );
				if ( ! is_wp_error( $logo_id ) ) {
					update_post_meta( $logo_id, '_dgf_imported', $source_key );
					update_post_meta( $logo_id, '_wp_attachment_image_alt', dgf_opt( 'brand_name' ) );
					++$result['imported'];
					if ( ! get_theme_mod( 'custom_logo' ) ) {
						set_theme_mod( 'custom_logo', $logo_id );
						$result['notes'][] = sprintf( __( '%s set as the site logo.', 'dgf' ), $relative );
					}
				} else {
					wp_delete_file( $tmp );
				}
			}
			continue;
		}

		// Match: file name first, then parent folders from the closest outwards.
		$slug     = dgf_best_page( dgf_clean_name( pathinfo( $path, PATHINFO_FILENAME ) ), $table );
		$segments = array_reverse( array_slice( explode( '/', str_replace( '\\', '/', $relative ) ), 0, -1 ) );
		foreach ( $segments as $segment ) {
			if ( $slug ) {
				break;
			}
			$slug = dgf_best_page( dgf_clean_name( $segment ), $table );
		}
		if ( ! $slug && $is_pdf ) {
			$slug = 'catalogues';
		}
		if ( ! $slug ) {
			$slug = 'projects';
			$result['notes'][] = sprintf( __( '%s → Projects (no page name matched).', 'dgf' ), $relative );
		}
		if ( ! isset( $table[ $slug ] ) ) {
			++$result['skipped'];
			continue;
		}
		$page_id = $table[ $slug ]['id'];

		// media_handle_sideload() moves the file, so work on a temp copy.
		$tmp = wp_tempnam( basename( $path ) );
		if ( ! $tmp || ! copy( $path, $tmp ) ) {
			++$result['skipped'];
			continue;
		}
		$title = ucwords( trim( preg_replace( '/[-_]+/', ' ', pathinfo( $path, PATHINFO_FILENAME ) ) ) );
		$id    = media_handle_sideload(
			array(
				'name'     => sanitize_file_name( ( $is_pdf ? '' : $slug . '-' ) . basename( $path ) ),
				'tmp_name' => $tmp,
			),
			$page_id,
			$is_pdf ? $title : get_the_title( $page_id )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			++$result['skipped'];
			$result['notes'][] = $relative . ': ' . $id->get_error_message();
			continue;
		}
		update_post_meta( $id, '_dgf_imported', $source_key );
		++$result['imported'];

		if ( $is_image ) {
			update_post_meta( $id, '_wp_attachment_image_alt', sprintf( '%s — %s', wp_strip_all_tags( get_the_title( $page_id ) ), dgf_opt( 'brand_name' ) ) );
			if ( ! has_post_thumbnail( $page_id ) ) {
				set_post_thumbnail( $page_id, $id );
				++$result['featured'];
			}
		}
	}
	return $result;
}
