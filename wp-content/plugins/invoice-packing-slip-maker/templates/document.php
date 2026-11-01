<?php
/**
 * Page wrapper for printable documents.
 *
 * Override by copying to yourtheme/invoice-packing-slip-maker/document.php.
 *
 * @package Invoice_Packing_Slip_Maker
 *
 * @var IPSM_Document[] $docs
 * @var bool            $toolbar
 * @var string          $pdf_url
 * @var string          $type
 */

defined( 'ABSPATH' ) || exit;

$accent   = IPSM_Settings::get( 'accent_color' );
$is_label = IPSM_Document::TYPE_LABEL === $type;
list( $page_w, $page_h ) = IPSM_PDF::page_size( $type );
$page_css = round( $page_w / 72, 2 ) . 'in ' . round( $page_h / 72, 2 ) . 'in';
// A5 (and other small pages) get a compact layout with smaller fonts.
$is_compact = $page_w > 400 && $page_w < 500;
$title    = 1 === count( $docs )
	? $docs[0]->title() . ' ' . ( IPSM_Document::TYPE_INVOICE === $type ? $docs[0]->invoice_number() : '#' . $docs[0]->order->get_order_number() )
	: IPSM_Document::type_label( $type );
?><!DOCTYPE html>
<html <?php echo function_exists( 'get_language_attributes' ) ? get_language_attributes() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="robots" content="noindex, nofollow" />
<title><?php echo esc_html( $title ); ?></title>
<style>
	@page { size: <?php echo esc_html( $page_css ); ?>; margin: <?php echo $is_label ? '0.15in' : '0.5in'; ?>; }
	* { box-sizing: border-box; }
	html, body { margin: 0; padding: 0; }
	body { font-family: "DejaVu Sans", "Helvetica Neue", Helvetica, Arial, sans-serif; color: #111827; font-size: 12px; line-height: 1.45; background: #e5e7eb; }
	.ipsm-toolbar { position: sticky; top: 0; z-index: 10; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; padding: 12px 16px; background: #111827; }
	.ipsm-toolbar a, .ipsm-toolbar button { font: 600 13px/1 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; padding: 10px 16px; border-radius: 6px; border: 0; cursor: pointer; text-decoration: none; background: #fff; color: #111827; }
	.ipsm-toolbar .primary { background: <?php echo esc_html( $accent ); ?>; color: #fff; outline: 1px solid rgba(255,255,255,.35); }
	.ipsm-page { background: #fff; width: <?php echo esc_html( round( $page_w / 72, 2 ) ); ?>in; max-width: calc(100% - 32px); min-height: <?php echo esc_html( round( $page_h / 72, 2 ) ); ?>in; margin: 24px auto; padding: <?php echo $is_label ? '0.2in' : '0.55in'; ?>; box-shadow: 0 2px 12px rgba(0,0,0,.12); position: relative; }
	.ipsm-page + .ipsm-page { page-break-before: always; break-before: page; }
	.ipsm-muted { color: #6b7280; }
	.ipsm-accent { color: <?php echo esc_html( $accent ); ?>; }
	.ipsm-header { display: table; width: 100%; margin-bottom: 22px; }
	.ipsm-header > div { display: table-cell; vertical-align: top; }
	.ipsm-header .ipsm-title { text-align: right; font-size: 28px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
	.ipsm-logo img { max-width: 220px; max-height: 80px; }
	.ipsm-shopname { font-size: 20px; font-weight: 700; }
	.ipsm-cols { display: table; width: 100%; margin-bottom: 22px; }
	.ipsm-cols > div { display: table-cell; vertical-align: top; width: 50%; padding-right: 16px; }
	.ipsm-meta { margin-left: auto; border-collapse: collapse; }
	.ipsm-meta th { text-align: right; font-weight: 400; color: #6b7280; padding: 1px 10px 1px 0; white-space: nowrap; }
	.ipsm-meta td { text-align: right; font-weight: 700; padding: 1px 0; }
	h3.ipsm-h { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; margin: 0 0 6px; color: <?php echo esc_html( $accent ); ?>; }
	.ipsm-ship-big { font-size: 15px; line-height: 1.5; }
	.ipsm-ship-big strong { font-size: 17px; }
	table.ipsm-items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
	table.ipsm-items thead th { background: <?php echo esc_html( $accent ); ?>; color: #fff; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .06em; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
	table.ipsm-items td { padding: 8px; vertical-align: top; border-bottom: 1px solid #e5e7eb; }
	table.ipsm-items tbody tr:nth-child(even) td { background: #f9fafb; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
	table.ipsm-items .num { text-align: right; white-space: nowrap; }
	.ipsm-item-name { font-weight: 700; }
	.ipsm-item-meta { font-size: 10.5px; color: #6b7280; }
	.ipsm-check { width: 14px; height: 14px; border: 1.5px solid #9ca3af; display: inline-block; border-radius: 2px; }
	.ipsm-qty-big { font-size: 15px; font-weight: 700; }
	table.ipsm-totals { margin-left: auto; border-collapse: collapse; min-width: 280px; }
	table.ipsm-totals th { text-align: right; font-weight: 400; padding: 4px 16px 4px 0; }
	table.ipsm-totals td { text-align: right; font-weight: 700; padding: 4px 8px 4px 0; }
	table.ipsm-totals tr.ipsm-grand th, table.ipsm-totals tr.ipsm-grand td { background: <?php echo esc_html( $accent ); ?>; color: #fff; font-size: 14px; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
	table.ipsm-totals small { font-weight: 400; display: block; font-size: 10px; }
	.ipsm-notes { margin-top: 24px; }
	.ipsm-notes p { margin: 0 0 12px; white-space: pre-line; }
	.ipsm-footer { margin-top: 36px; text-align: center; color: #6b7280; font-size: 11px; white-space: pre-line; border-top: 1px solid #e5e7eb; padding-top: 12px; }
	/* Shipping label */
	.ipsm-label { border: 2px solid #000; height: 100%; min-height: <?php echo esc_html( round( $page_h / 72 - 0.4, 2 ) ); ?>in; display: flex; flex-direction: column; color: #000; }
	.ipsm-label section { padding: 10px 12px; }
	.ipsm-label .from { font-size: 10.5px; line-height: 1.35; border-bottom: 3px solid #000; }
	.ipsm-label .to { flex: 1; font-size: 17px; font-weight: 700; line-height: 1.35; }
	.ipsm-label .to .name { font-size: 21px; }
	.ipsm-label .to .phone { font-weight: 400; font-size: 14px; margin-top: 6px; }
	.ipsm-label .lbl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
	.ipsm-label .bottom { border-top: 3px solid #000; display: flex; justify-content: space-between; align-items: flex-end; }
	.ipsm-label .order-no { font-size: 30px; font-weight: 800; line-height: 1; }
	.ipsm-label .info { text-align: right; font-size: 11px; }
	.ipsm-label .barcode { border-top: 3px solid #000; text-align: center; padding: 8px 12px 6px; }
	.ipsm-label .barcode svg { display: block; margin: 0 auto; }
	.ipsm-label .barcode .code { font-size: 11px; letter-spacing: .1em; margin-top: 3px; }
	<?php if ( $is_compact ) : ?>
	/* A5: compact layout with small fonts. */
	body { font-size: 9px; line-height: 1.4; }
	.ipsm-page:not(.ipsm-type-shipping-label) { padding: 0.4in; }
	.ipsm-header { margin-bottom: 14px; }
	.ipsm-header .ipsm-title { font-size: 19px; }
	.ipsm-shopname { font-size: 15px; }
	.ipsm-logo img { max-width: 160px; max-height: 56px; }
	.ipsm-cols { margin-bottom: 14px; }
	h3.ipsm-h { font-size: 7.5px; margin-bottom: 4px; }
	.ipsm-ship-big { font-size: 11px; }
	.ipsm-ship-big strong { font-size: 12.5px; }
	table.ipsm-items { margin-bottom: 10px; }
	table.ipsm-items thead th { font-size: 7.5px; padding: 5px 6px; }
	table.ipsm-items td { padding: 5px 6px; }
	.ipsm-item-meta { font-size: 8px; }
	.ipsm-check { width: 10px; height: 10px; }
	.ipsm-qty-big { font-size: 11px; }
	table.ipsm-totals { min-width: 200px; }
	table.ipsm-totals th, table.ipsm-totals td { padding: 3px 8px 3px 0; }
	table.ipsm-totals tr.ipsm-grand th, table.ipsm-totals tr.ipsm-grand td { font-size: 11px; padding: 6px; }
	table.ipsm-totals small { font-size: 8px; }
	.ipsm-notes { margin-top: 16px; }
	.ipsm-notes p { margin-bottom: 8px; }
	.ipsm-footer { margin-top: 22px; font-size: 8.5px; padding-top: 8px; }
	.ipsm-label section { padding: 9px 12px; }
	.ipsm-label .from { font-size: 9.5px; }
	.ipsm-label .to { font-size: 15px; }
	.ipsm-label .to .name { font-size: 19px; }
	.ipsm-label .to .phone { font-size: 12px; }
	.ipsm-label .order-no { font-size: 26px; }
	.ipsm-label .info { font-size: 10px; }
	<?php endif; ?>
	@media print {
		body { background: #fff; }
		.ipsm-toolbar { display: none !important; }
		.ipsm-page { width: auto; max-width: none; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
		.ipsm-label { min-height: calc(100vh - 4px); }
	}
	@media (max-width: 640px) {
		.ipsm-page { padding: 20px; }
		.ipsm-cols > div { display: block; width: 100%; margin-bottom: 16px; }
	}
</style>
</head>
<body>
<?php if ( $toolbar ) : ?>
	<div class="ipsm-toolbar">
		<button type="button" class="primary" onclick="window.print()"><?php esc_html_e( 'Print', 'invoice-packing-slip-maker' ); ?></button>
		<?php if ( $pdf_url ) : ?>
			<a href="<?php echo esc_url( $pdf_url ); ?>"><?php esc_html_e( 'Download PDF', 'invoice-packing-slip-maker' ); ?></a>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php foreach ( $docs as $doc ) : ?>
	<div class="ipsm-page ipsm-type-<?php echo esc_attr( $doc->type ); ?>">
		<?php IPSM_Renderer::render_body( $doc ); ?>
	</div>
<?php endforeach; ?>
<?php if ( $toolbar && ! empty( $_GET['autoprint'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
	<script>window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 300); });</script>
<?php endif; ?>
</body>
</html>
