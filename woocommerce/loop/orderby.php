<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<form class="woocommerce-ordering mb-3" method="get">
	<label for="orderby" class="form-label me-2"><?php esc_html_e( 'Sort by:', 'woocommerce' ); ?></label>
	<select name="orderby" class="form-select" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
		<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
	// Keep query string vars intact
	foreach ( $_GET as $key => $val ) {
		if ( 'orderby' === $key || 'submit' === $key ) {
			continue;
		}
		if ( is_array( $val ) ) {
			foreach ( $val as $inner_val ) {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $inner_val ) . '" />';
			}
		} else {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}
	}
	?>
</form>
