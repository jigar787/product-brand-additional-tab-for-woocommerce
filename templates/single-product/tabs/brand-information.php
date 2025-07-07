<?php
/**
 * Brand information tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/brand-information.php.
 *
 * @package WC_Product_Brand_Additional_Tab\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $brands[0] ) ) {
	return;
}
$primary_brand = $brands[0];
$thumbnail_id  = get_term_meta( $primary_brand->term_id, 'thumbnail_id', true );

do_action( 'wc_pbat_before_brand_content', $primary_brand );
?>
<div class="wc-pbat-brand-info">
	<h2>
		<?php echo esc_html( $primary_brand->name ); ?>
	</h2>

	<?php if ( $thumbnail_id ) : ?>
		<div class="wc-pbat-brand-logo">
			<?php echo wp_get_attachment_image( $thumbnail_id, 'medium' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( isset( $primary_brand->description ) ) : ?>
		<div class="wc-pbat-brand-description">
		<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo WC_Product_Brand_Additional_Tab::get_content( $primary_brand );
		?>
		</div>
	<?php endif; ?>
</div>
<?php
do_action( 'wc_pbat_after_brand_content', $primary_brand );