<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();
?>

<main id="main" class="blog_index">
	<div class="container-xl py-5">
		<h1>Plant Encyclopaedia</h1>

		<?php
		// Get all plant titles and extract the first letter of each
		global $wpdb;
		$results = $wpdb->get_col("SELECT DISTINCT UPPER(LEFT(post_title, 1)) FROM $wpdb->posts WHERE post_type = 'plant-guide' AND post_status = 'publish' ORDER BY post_title ASC");

		// Define A-Z letters
		$letters = range('A', 'Z');
		?>

		<!-- A-Z Filter -->
		<div class="az-filter mb-4">
			<?php
			foreach ($letters as $letter) {
			?>
				<a class="<?= (isset($_GET['filter']) && $_GET['filter'] === $letter) ? 'active' : (in_array($letter, $results) ? '' : 'inactive') ?>" href="<?= add_query_arg('filter', $letter, get_post_type_archive_link('plant-guide')) ?>"><?= $letter ?></a>
			<?php
			}
			?>
			<a href="<?= esc_url(get_post_type_archive_link('plant-guide')) ?>" class="show-all <?= empty($_GET['filter']) ? 'active' : '' ?>">Show All</a>
		</div>

		<ul class="plant-list">
			<?php

			// Get selected letter from query string
			$selected_letter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';

			if (!empty($selected_letter)) {
				echo '<h3 class="has-green-500-color">Showing plants beginning with ' . $selected_letter . '</h3>';
			} else {
				echo '<h3 class="has-green-500-color">Showing all plants</h3>';
			}

			// error_log($selected_letter);

			$args = array(
				'post_type'      => 'plant-guide',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'suppress_filters' => false, // Ensure filters are applied
			);

			// Custom WHERE filter
			function lc_filter_where($where, $query)
			{
				global $wpdb, $selected_letter;
				if (!empty($selected_letter)) {
					// error_log("Filter Applied: Searching for posts starting with " . $selected_letter);
					$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", $selected_letter . '%');
				}
				return $where;
			}

			// Add Filter
			if (!empty($selected_letter) && preg_match('/^[A-Z]$/', $selected_letter)) {
				// error_log('filter found ' . $selected_letter);
				add_filter('posts_where', 'lc_filter_where', 10, 2);
			}

			// Run the query
			$query = new WP_Query($args);

			// Remove the filter after the query
			remove_filter('posts_where', 'lc_filter_where', 10, 2);

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$common_name = get_the_title();
					$scientific_name = get_field('scientific_name') ?? '';
					$alt_names = get_field('aka'); // Assume this is an ACF repeater or comma-separated list
					$thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail', ['class' => 'plant-thumb']);
			?>
					<li class="plant-item">
						<a href="<?= esc_url(get_permalink()) ?>" class="plant-link">
							<?= $thumbnail ?>
							<span class="plant-name"><?= esc_html($common_name) ?></span>
							<?php if ($scientific_name) { ?>
								<span class="plant-scientific"><?= format_scientific_name($scientific_name) ?></span>
							<?php } ?>
							<?php if ($alt_names) { ?>
								<span class="plant-alternative">Also known as: <?= esc_html(is_array($alt_names) ? implode(', ', $alt_names) : $alt_names) ?></span>
							<?php } ?>
						</a>
					</li>
				<?php
				}
			} else {
				?>
				<p>No plants found for <?= esc_html($selected_letter) ?></p>
			<?php
			}
			wp_reset_postdata();

			?>
		</ul>


		<?= understrap_pagination() ?>
	</div>
</main>

<?php
get_footer();
?>