<?php
/**
 * Lightweight contact form: no form plugin, no SMTP plugin.
 *
 * - Shortcode [bm_contact_form] renders a plain HTML form (works without JavaScript).
 * - Every submission is saved as a private "Enquiry" (wp-admin > Enquiries), so nothing
 *   is lost even if an email fails.
 * - A notification email goes out with WordPress's built-in wp_mail(), sent from
 *   an @bombaymarketing.in address (covered by the domain's Hostinger SPF record)
 *   with Reply-To set to the visitor.
 * - Spam protection: honeypot field, minimum fill time and a per-IP rate limit.
 *   No nonce, because LiteSpeed caches the contact page.
 * - While MetForm is still installed, [metform form_id="87"] is rendered with this form,
 *   so the contact page switches over as soon as this file is deployed.
 *
 * @package Bombay_Agent_Ready
 */

defined( 'ABSPATH' ) || exit;

class BMAR_Contact {

	const ACTION     = 'bm_contact';
	const POST_TYPE  = 'bm_enquiry';
	const SHORTCODE  = 'bm_contact_form';
	const MIN_SECS   = 3;
	const RATE_LIMIT = 5;   // Submissions per IP ...
	const RATE_SECS  = 600; // ... per 10 minutes.

	/**
	 * Last wp_mail error message, captured from the wp_mail_failed hook.
	 *
	 * @var string
	 */
	private static $mail_error = '';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_shortcode( self::SHORTCODE, array( __CLASS__, 'render' ) );
		add_filter( 'pre_do_shortcode_tag', array( __CLASS__, 'replace_metform' ), 10, 3 );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'admin_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'admin_column' ), 10, 2 );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( __CLASS__, 'add_meta_box' ) );
	}

	/**
	 * Where notifications go: option `bmar_contact_to` (comma separated) or the site admin email.
	 *
	 * @return string[]
	 */
	private static function recipients() {
		$to = (string) get_option( 'bmar_contact_to', '' );
		if ( '' === trim( $to ) ) {
			$to = (string) get_option( 'admin_email' );
		}
		$list = array_filter( array_map( 'sanitize_email', explode( ',', $to ) ), 'is_email' );
		return apply_filters( 'bmar_contact_recipients', array_values( $list ) );
	}

	/* ---------------------------------------------------------------------
	 * Storage
	 * ------------------------------------------------------------------- */

	/**
	 * Private post type that keeps every enquiry.
	 */
	public static function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'          => 'Enquiries',
					'singular_name' => 'Enquiry',
					'menu_name'     => 'Enquiries',
					'all_items'     => 'All enquiries',
					'edit_item'     => 'Enquiry',
					'search_items'  => 'Search enquiries',
					'not_found'     => 'No enquiries yet.',
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => false,
				'menu_position'   => 26,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => array( 'title' ),
				'capability_type' => 'page',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}

	/**
	 * List table columns.
	 *
	 * @param array $cols Columns.
	 * @return array
	 */
	public static function admin_columns( $cols ) {
		return array(
			'cb'         => $cols['cb'],
			'title'      => 'Name',
			'bm_email'   => 'Email',
			'bm_phone'   => 'Phone',
			'bm_message' => 'Message',
			'bm_mail'    => 'Email alert',
			'date'       => $cols['date'],
		);
	}

	/**
	 * List table column values.
	 *
	 * @param string $col     Column key.
	 * @param int    $post_id Post ID.
	 */
	public static function admin_column( $col, $post_id ) {
		switch ( $col ) {
			case 'bm_email':
				$email = get_post_meta( $post_id, '_bm_email', true );
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				break;
			case 'bm_phone':
				echo esc_html( get_post_meta( $post_id, '_bm_phone', true ) );
				break;
			case 'bm_message':
				echo esc_html( wp_trim_words( get_post_field( 'post_content', $post_id ), 20 ) );
				break;
			case 'bm_mail':
				echo esc_html( get_post_meta( $post_id, '_bm_mail_status', true ) );
				break;
		}
	}

	/**
	 * Read-only details box on the enquiry screen.
	 */
	public static function add_meta_box() {
		add_meta_box(
			'bm_enquiry_details',
			'Enquiry details',
			function ( $post ) {
				$rows = array(
					'Email'       => get_post_meta( $post->ID, '_bm_email', true ),
					'Phone'       => get_post_meta( $post->ID, '_bm_phone', true ),
					'Sent from'   => get_post_meta( $post->ID, '_bm_page', true ),
					'Email alert' => get_post_meta( $post->ID, '_bm_mail_status', true ),
				);
				echo '<table class="form-table">';
				foreach ( $rows as $label => $value ) {
					echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
				}
				echo '<tr><th>Message</th><td>' . nl2br( esc_html( $post->post_content ) ) . '</td></tr></table>';
			},
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/* ---------------------------------------------------------------------
	 * Front end
	 * ------------------------------------------------------------------- */

	/**
	 * Render MetForm form 87 with this form instead (until the page shortcode is swapped).
	 *
	 * @param false|string $out  Short-circuit output.
	 * @param string       $tag  Shortcode tag.
	 * @param array|string $attr Attributes.
	 * @return false|string
	 */
	public static function replace_metform( $out, $tag, $attr ) {
		if ( 'metform' === $tag && is_array( $attr ) && isset( $attr['form_id'] ) && 87 === (int) $attr['form_id']
			&& apply_filters( 'bmar_contact_replace_metform', true ) ) {
			return self::render();
		}
		return $out;
	}

	/**
	 * Shortcode output.
	 *
	 * @return string
	 */
	public static function render() {
		$status = isset( $_GET['bm_sent'] ) ? sanitize_key( wp_unslash( $_GET['bm_sent'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$notice = '';
		if ( 'ok' === $status ) {
			$notice = '<div class="bm-cf-msg ok" role="status">' . esc_html( self::message( 'ok' ) ) . '</div>';
		} elseif ( '' !== $status ) {
			$notice = '<div class="bm-cf-msg err" role="alert">' . esc_html( self::message( $status ) ) . '</div>';
		}

		ob_start();
		?>
<div class="bm-cf" id="bm-contact">
<style>
.bm-cf form{display:flex;flex-direction:column;gap:14px}
.bm-cf label{display:block;font-weight:700;font-size:15px;margin:0 0 6px;color:#141414}
.bm-cf label span{color:#b42318}
.bm-cf input,.bm-cf textarea{width:100%;box-sizing:border-box}
.bm-cf .bm-cf-hp{position:absolute!important;left:-9999px!important;width:1px;height:1px;overflow:hidden}
.bm-cf-msg{padding:14px 16px;border:2px solid #141414;border-radius:12px;font-weight:600;margin:0 0 16px}
.bm-cf-msg.ok{background:#e7f8ec}
.bm-cf-msg.err{background:#fdecea}
.bm-cf button[disabled]{opacity:.6;cursor:wait}
.bm-cf small.bm-cf-note{display:block;font-size:13px;opacity:.75}
</style>
<div class="bm-cf-out" aria-live="polite"><?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
	<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
	<input type="hidden" name="bm_ts" value="">
	<input type="hidden" name="bm_page" value="">
	<div class="bm-cf-hp" aria-hidden="true"><label for="bm-cf-website">Website</label><input id="bm-cf-website" type="text" name="bm_website" tabindex="-1" autocomplete="off"></div>
	<div><label for="bm-cf-name">Full Name <span>*</span></label><input id="bm-cf-name" type="text" name="bm_name" placeholder="Your name" required maxlength="100" autocomplete="name"></div>
	<div><label for="bm-cf-email">Email <span>*</span></label><input id="bm-cf-email" type="email" name="bm_email" placeholder="you@company.com" required maxlength="150" autocomplete="email"></div>
	<div><label for="bm-cf-phone">Phone / WhatsApp</label><input id="bm-cf-phone" type="tel" name="bm_phone" placeholder="+91 98xxx xxxxx" maxlength="30" autocomplete="tel"></div>
	<div><label for="bm-cf-message">Message <span>*</span></label><textarea id="bm-cf-message" name="bm_message" placeholder="Write your message here" required maxlength="5000"></textarea></div>
	<button type="submit">Submit</button>
	<small class="bm-cf-note">We reply within one working day.</small>
</form>
<script>
(function () {
	var root = document.getElementById('bm-contact');
	if (!root || !window.fetch || !window.FormData) { return; }
	var form = root.querySelector('form'), out = root.querySelector('.bm-cf-out'), btn = form.querySelector('button');
	form.bm_ts.value = String(Math.floor(Date.now() / 1000));
	form.bm_page.value = location.href.split('#')[0];
	function show(ok, msg) {
		out.innerHTML = '';
		var d = document.createElement('div');
		d.className = 'bm-cf-msg ' + (ok ? 'ok' : 'err');
		d.setAttribute('role', ok ? 'status' : 'alert');
		d.textContent = msg;
		out.appendChild(d);
		root.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		if (!form.checkValidity()) { form.reportValidity(); return; }
		btn.disabled = true; btn.textContent = 'Sending…';
		var fd = new FormData(form); fd.append('bm_ajax', '1');
		fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (j) { show(!!j.ok, j.message); if (j.ok) { form.reset(); form.bm_ts.value = String(Math.floor(Date.now() / 1000)); } })
			.catch(function () { show(false, <?php echo wp_json_encode( self::message( 'error' ) ); ?>); })
			.then(function () { btn.disabled = false; btn.textContent = 'Submit'; });
	});
})();
</script>
</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Visitor-facing messages.
	 *
	 * @param string $code Status code.
	 * @return string
	 */
	private static function message( $code ) {
		$wa       = 'WhatsApp us on +91 87676 97389';
		$messages = array(
			'ok'      => 'Thank you! Your message has been sent. We will reply within one working day.',
			'invalid' => 'Please enter your name, a valid email address and a message.',
			'slow'    => 'Too many messages in a short time. Please try again in a few minutes or ' . $wa . '.',
			'error'   => 'Sorry, something went wrong. Please try again or ' . $wa . '.',
		);
		return isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['error'];
	}

	/* ---------------------------------------------------------------------
	 * Submission
	 * ------------------------------------------------------------------- */

	/**
	 * Handle a submission (JS fetch or plain POST).
	 */
	public static function handle() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form on a cached page; protected by honeypot, timing and rate limit.
		$p    = wp_unslash( $_POST );
		$ajax = ! empty( $p['bm_ajax'] );

		$name    = isset( $p['bm_name'] ) ? sanitize_text_field( $p['bm_name'] ) : '';
		$email   = isset( $p['bm_email'] ) ? sanitize_email( $p['bm_email'] ) : '';
		$phone   = isset( $p['bm_phone'] ) ? sanitize_text_field( $p['bm_phone'] ) : '';
		$message = isset( $p['bm_message'] ) ? sanitize_textarea_field( $p['bm_message'] ) : '';
		$page    = isset( $p['bm_page'] ) ? esc_url_raw( $p['bm_page'] ) : '';
		$ts      = isset( $p['bm_ts'] ) ? (int) $p['bm_ts'] : 0;
		// phpcs:enable

		if ( '' === $page ) {
			$page = wp_get_referer() ? wp_get_referer() : home_url( '/contact/' );
		}

		// Bots: filled honeypot or submitted faster than a human could. Pretend success.
		if ( ! empty( $p['bm_website'] ) || ( $ts > 0 && ( time() - $ts ) < self::MIN_SECS ) ) {
			self::respond( $ajax, 'ok', $page );
		}

		$name    = mb_substr( $name, 0, 100 );
		$phone   = mb_substr( $phone, 0, 30 );
		$message = mb_substr( $message, 0, 5000 );

		if ( '' === $name || ! is_email( $email ) || '' === trim( $message ) ) {
			self::respond( $ajax, 'invalid', $page );
		}

		$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$key  = 'bm_cf_' . md5( $ip );
		$seen = (int) get_transient( $key );
		if ( $seen >= self::RATE_LIMIT ) {
			self::respond( $ajax, 'slow', $page );
		}
		set_transient( $key, $seen + 1, self::RATE_SECS );

		// 1. Save the enquiry first so it is never lost.
		$post_id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'private',
				'post_title'   => $name,
				'post_content' => $message,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			$post_id = 0;
		} else {
			update_post_meta( $post_id, '_bm_email', $email );
			update_post_meta( $post_id, '_bm_phone', $phone );
			update_post_meta( $post_id, '_bm_page', $page );
		}

		// 2. Email the team.
		$sent = self::send_mail( $name, $email, $phone, $message, $page, $post_id );

		if ( $post_id ) {
			update_post_meta( $post_id, '_bm_mail_status', $sent ? 'Sent ' . current_time( 'Y-m-d H:i' ) : 'FAILED: ' . self::$mail_error );
		}

		self::respond( $ajax, ( $post_id || $sent ) ? 'ok' : 'error', $page );
	}

	/**
	 * Send the notification with wp_mail().
	 *
	 * @return bool
	 */
	private static function send_mail( $name, $email, $phone, $message, $page, $post_id ) {
		$to = self::recipients();
		if ( ! $to ) {
			self::$mail_error = 'No recipient email configured.';
			return false;
		}

		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$host = preg_replace( '/^www\./', '', (string) $host );
		$from = apply_filters( 'bmar_contact_from', 'noreply@' . $host );

		$subject = 'New enquiry from ' . $name . ' - Bombay Marketing website';
		$lines   = array(
			'New enquiry from the website contact form.',
			'',
			'Name:    ' . $name,
			'Email:   ' . $email,
			'Phone:   ' . ( '' !== $phone ? $phone : '-' ),
			'Page:    ' . $page,
			'Time:    ' . current_time( 'd M Y, h:i A' ),
			'',
			'Message:',
			$message,
			'',
			'---',
			'Reply to this email to answer ' . $name . ' directly.',
		);
		if ( $post_id ) {
			$lines[] = 'Saved in WordPress: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		}

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: Bombay Marketing Website <' . $from . '>',
			'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>', '"' ), '', $name ) . ' <' . $email . '>',
		);

		$catch = function ( $error ) {
			self::$mail_error = $error->get_error_message();
		};
		add_action( 'wp_mail_failed', $catch );
		$sent = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
		remove_action( 'wp_mail_failed', $catch );

		if ( ! $sent && '' === self::$mail_error ) {
			self::$mail_error = 'wp_mail() returned false.';
		}
		return (bool) $sent;
	}

	/**
	 * Finish the request: JSON for fetch, redirect back for plain POST.
	 *
	 * @param bool   $ajax Whether the request came from fetch.
	 * @param string $code Status code.
	 * @param string $page Page to return to.
	 */
	private static function respond( $ajax, $code, $page ) {
		if ( $ajax ) {
			wp_send_json(
				array(
					'ok'      => 'ok' === $code,
					'message' => self::message( $code ),
				)
			);
		}
		$url = remove_query_arg( 'bm_sent', $page );
		if ( 0 !== strpos( $url, home_url() ) ) {
			$url = home_url( '/contact/' );
		}
		wp_safe_redirect( add_query_arg( 'bm_sent', $code, $url ) . '#bm-contact', 303 );
		exit;
	}
}
