<?php

/**
 * Clean WooCommerce Archive Product Template
 * Removes all hooks and uses normal PHP + Bootstrap 5
 */

defined('ABSPATH') || exit;

get_header('shop'); ?>

<main id="main" class="products-archive">
	<div class="container-xl py-5">

		<!-- Page Title -->
		<header class="woocommerce-products-header mb-4">
			<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
		</header>

		<!-- WooCommerce Notices -->
		<div class="woocommerce-notices-wrapper"><?php wc_print_notices(); ?></div>

		<div class="row">
			<div class="col-md-3">
				<!-- category and tag filtering -->
				<form id="product-filter-form">
					<div class="row">
						<div class="col-sm-6 col-md-12">
							<!-- Product Categories -->
							<div class="filter-group mb-4">
								<div class="h3">By Category</div>
								<?php
								$categories = get_terms(array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => true,
								));

								foreach ($categories as $category) {
									echo '<div class="form-check">
    <input class="form-check-input" type="checkbox" name="product_cat[]" value="' . esc_attr($category->slug) . '" id="cat-' . esc_attr($category->slug) . '">
    <label class="form-check-label" for="cat-' . esc_attr($category->slug) . '">
        ' . esc_html($category->name) . '
    </label>
</div>';
								}
								?>
							</div>
						</div>
						<div class="col-sm-6 col-md-12">

							<!-- Product Tags -->
							<div class="filter-group">
								<div class="h3">By Tags</div>
								<?php
								$tags = get_terms(array(
									'taxonomy'   => 'product_tag',
									'hide_empty' => true,
								));

								foreach ($tags as $tag) {
									echo '<div class="form-check">
    <input class="form-check-input" type="checkbox" name="product_tag[]" value="' . esc_attr($tag->slug) . '" id="tag-' . esc_attr($tag->slug) . '">
    <label class="form-check-label" for="tag-' . esc_attr($tag->slug) . '">
        ' . esc_html($tag->name) . '
    </label>
</div>';
								}
								?>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-9">

				<!-- Results Count Placeholder -->
				<p id="product-count" class="product-count">Loading products...</p>

				<!-- Product Results Will Be Updated Here -->
				<div id="product-results">
					<p class="loading-message">Loading products...</p>
				</div>
			</div>

		</div>
	</div>
</main>
<?php

get_footer('shop'); ?>