<?php

/**
 * Clean WooCommerce Archive Product Template
 * Removes all hooks and uses normal PHP + Bootstrap 5
 */

defined('ABSPATH') || exit;

get_header();
// get_header('shop');

// Fetch categories and tags before the Bootstrap .row structure
$uncategorised_id = get_term_by('slug', 'uncategorised', 'product_cat')->term_id;

$categories = get_terms(array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => array($uncategorised_id), // Exclude "Uncategorised"
));


$tags = get_terms(array(
	'taxonomy'   => 'product_tag',
	'hide_empty' => true,
));

// Function to generate category checkboxes
function render_category_filters($categories)
{
	$output = '';
	foreach ($categories as $category) {
		// $id = 'cat-' . esc_attr($category->slug);
		$id = 'cat-' . esc_attr($category->slug);
		$output .= '<div class="form-check">
            <input class="form-check-input" type="checkbox" name="product_cat[]" value="' . esc_attr($category->slug) . '" id="' . $id . '">
            <label class="form-check-label" for="' . $id . '">' . esc_html($category->name) . '</label>
        </div>';
	}
	return $output;
}

// Function to generate tag checkboxes
function render_tag_filters($tags)
{
	$output = '';
	foreach ($tags as $tag) {
		$id = 'tag-' . esc_attr($tag->slug);
		$output .= '<div class="form-check">
            <input class="form-check-input" type="checkbox" name="product_tag[]" value="' . esc_attr($tag->slug) . '" id="' . $id . '">
            <label class="form-check-label" for="' . $id . '">' . esc_html($tag->name) . '</label>
        </div>';
	}
	return $output;
}
?>

<main id="main" class="products-archive">
	<div class="container-xl py-5">
		<h1>Boba's Shop</h1>
		<div class="row">

			<div class="col-md-3">
				<!-- Single Filter Form -->
				<form id="product-filter-form">
					<!-- Offcanvas Toggle for Mobile -->
					<div class="d-md-none mb-3">
						<button class="filter-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#productFilterSidebar" aria-controls="productFilterSidebar">
							Filters
						</button>
					</div>

					<!-- The One and Only Filter Container -->
					<div id="product-filter-container" class="d-none d-md-block">
						<div class="filter-group mb-4">
							<div class="h3">By Category</div>
							<?php echo render_category_filters($categories); ?>
						</div>
						<div class="filter-group">
							<div class="h3">By Tags</div>
							<?php echo render_tag_filters($tags); ?>
						</div>
					</div>

					<!-- Offcanvas (Uses the Same Filters on Mobile) -->
					<div class="offcanvas offcanvas-start" tabindex="-1" id="productFilterSidebar" aria-labelledby="productFilterSidebarLabel">
						<div class="offcanvas-header">
							<h5 id="productFilterSidebarLabel">Filter Products</h5>
							<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
						</div>
						<div class="offcanvas-body">
							<!-- This is where the filters will be moved dynamically on mobile -->
							<div id="offcanvas-filter-container"></div>
						</div>
					</div>
				</form>
			</div>



			<!-- Product Results -->
			<div class="col-md-9">
				<p id="product-count" class="product-count">Loading products...</p>
				<div id="active-filters" class="active-filters mb-2"></div>
				<div id="product-results">
					<p class="loading-message">Loading products...</p>
				</div>
			</div>
		</div>
	</div>
</main>
<?php

get_footer('shop'); ?>