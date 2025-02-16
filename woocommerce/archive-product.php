<?php

/**
 * Clean WooCommerce Archive Product Template
 * Removes all hooks and uses normal PHP + Bootstrap 5
 */

defined('ABSPATH') || exit;

get_header('shop'); ?>

<div class="container-xl py-5">

	<!-- Page Title -->
	<header class="woocommerce-products-header mb-4">
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	</header>

	<!-- WooCommerce Notices -->
	<div class="woocommerce-notices-wrapper"><?php wc_print_notices(); ?></div>

	<!-- Sorting & Result Count -->
	<div class="row align-items-center mb-4">
		<div class="col-md-6">
			<p class="woocommerce-result-count mb-0">
				<?php woocommerce_result_count(); ?>
			</p>
		</div>
		<div class="col-md-6 text-md-end">
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>

	<!-- Product Grid -->
	<div class="row">
		<?php
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$product = wc_get_product(get_the_ID());
		?>

				<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
					<a href="<?php the_permalink(); ?>" class="product woocommerce-LoopProduct-link woocommerce-loop-product__link">
						<div class="product-image-wrapper">
							<?= woocommerce_get_product_thumbnail() ?>
							<div class="product-price"><?= $product->get_price_html() ?></div>
						</div>
						<div class="product-details">
							<h3 class="product-title h5 mb-2"><?= get_the_title() ?></h3>
							<div><?= $product->get_short_description() ?></div>
						</div>
					</a>
				</div>

			<?php
			}
		} else {
			?>
			<p class="col-12 text-center"><?php esc_html_e('No products found', 'woocommerce'); ?></p>
		<?php
		}
		?>
	</div>

	<!-- Pagination -->
	<div class="mt-5">
		<?php woocommerce_pagination(); ?>
	</div>

</div>

<?php get_footer('shop'); ?>