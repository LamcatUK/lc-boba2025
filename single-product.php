<?php

/**
 * Single Product Template (Bootstrap 5 / Understrap Compatible)
 *
 * Fixes layout issues, restores breadcrumbs, related products, and click-to-lightbox.
 *
 * @package WooCommerce/Templates
 * 
 * @version 1.6.4
 */

defined('ABSPATH') || exit;

get_header('shop'); ?>
<main id="main">

	<div id="product-<?php the_ID(); ?>" <?php wc_product_class('woocommerce-single-product container-xl', get_the_ID()); ?>>

		<section class="breadcrumbs pt-4">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
			}
			?>
		</section>
		<div class="product-container">
			<div class="row g-5">

				<!-- Left Column: Product Images with Lightbox Fix -->
				<div class="col-md-6">
					<?php
					do_action('woocommerce_before_single_product');

					if (post_password_required()) {
						echo get_the_password_form();
						return;
					}

					// Ensure WooCommerce's product gallery scripts are enqueued properly
					if (function_exists('woocommerce_show_product_images')) {
						woocommerce_show_product_images();
					}
					?>
				</div>

				<!-- Right Column: Product Summary -->
				<div class="col-md-6">
					<div class="summary entry-summary">
						<?php
						woocommerce_template_single_title();
						woocommerce_template_single_excerpt();
						woocommerce_template_single_price();
						woocommerce_template_single_add_to_cart();
						woocommerce_template_single_meta(); // Categories

						// Move the full product description here
						$product = wc_get_product(get_the_ID());
						if ($product->get_description()) {
							echo '<div class="woocommerce-product-description mt-4">';
							echo '<h2 class="h4">' . esc_html__('Product Description', 'woocommerce') . '</h2>';
							echo wpautop($product->get_description());
							echo '</div>';
						}
						?>
					</div>
				</div>

			</div>
		</div>

		<!-- Related Products -->

		<?php
		// Get the current product
		$product = wc_get_product(get_the_ID());

		// Get related product IDs
		$related_ids = wc_get_related_products($product->get_id(), 4); // Get up to 4 related products

		if (! empty($related_ids)) { ?>
			<div class="container-xl mt-5 mb-5">
				<h2 class="mb-4">You May Also Like</h2>
				<div class="row">
					<?php
					foreach ($related_ids as $related_id) {
						$related_product = wc_get_product($related_id);
						$permalink = get_permalink($related_id);
					?>
						<div class="product col-md-6 col-lg-3">
							<a href="<?= esc_url($permalink) ?>" class="product__card">
								<?= get_the_post_thumbnail($related_id, 'woocommerce_thumbnail', array('class' => 'product__image')) ?>
								<div class="product__price"><?= $related_product->get_price_html() ?></div>
								<div class="product__detail">
									<h3><?= esc_html(get_the_title($related_id)) ?></h3>
									<div class="mb-3"><?= $related_product->get_short_description() ?></div>
									<div class="reserve">View Details</div>
								</div>
							</a>
						</div>


					<?php
					}
					?>
				</div>
			</div>
		<?php } ?>


		<?php do_action('woocommerce_after_single_product'); ?>

	</div>
</main>
<?php get_footer('shop'); ?>