<?php

/**
 * Clean WooCommerce Archive Product Template
 * Removes all hooks and uses normal PHP + Bootstrap 5
 */

defined('ABSPATH') || exit;

get_header('shop'); ?>

<main id="main">
    <div class="container-xl py-5">

        <!-- Page Title -->
        <header class="woocommerce-products-header mb-4">
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        </header>

        <!-- WooCommerce Notices -->
        <div class="woocommerce-notices-wrapper"><?php wc_print_notices(); ?></div>

        <!-- category and tag filtering -->
        <form id="product-filter-form" class="product-filter-form">
            <div class="filter-group">
                <label for="product-category">Category:</label>
                <select name="product_cat" id="product-category">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                    ));

                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Tags:</label>
                <div id="product-tags">
                    <?php
                    $tags = get_terms(array(
                        'taxonomy'   => 'product_tag',
                        'hide_empty' => true,
                    ));

                    foreach ($tags as $tag) {
                        echo '<label><input type="checkbox" name="product_tags[]" value="' . esc_attr($tag->slug) . '"> ' . esc_html($tag->name) . '</label><br>';
                    }
                    ?>
                </div>
            </div>
        </form>

        <!-- Product List Wrapper (Will Be Updated via AJAX) -->
        <div id="product-results">
            <?php get_template_part('template-parts/product-list'); // Load initial product list 
            ?>
        </div>




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

        <?php
        if (!is_admin() && is_post_type_archive('product')) {
            add_action('pre_get_posts', function ($query) {
                if ($query->is_main_query() && !is_admin()) {
                    $tax_query = array();

                    if (!empty($_GET['product_cat'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => sanitize_text_field($_GET['product_cat']),
                        );
                    }

                    if (!empty($_GET['product_tag'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'product_tag',
                            'field'    => 'slug',
                            'terms'    => sanitize_text_field($_GET['product_tag']),
                        );
                    }

                    if (!empty($tax_query)) {
                        $query->set('tax_query', $tax_query);
                    }
                }
            });
        }

        ?>


        <!-- Product Grid -->
        <div class="row">
            <?php
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    $product = wc_get_product(get_the_ID());

                    $cats = wp_get_post_terms(get_the_ID(), 'product_cat', array('fields' => 'names'));
                    $tags = wp_get_post_terms(get_the_ID(), 'product_tag', array('fields' => 'names'));


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
                                <div>Tags:<?= implode(',', $tags) ?></div>
                                <div>Cats:<?= implode(',', $cats) ?></div>
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
</main>
<?php
add_action('wp_footer', function () {
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var categorySelect = document.querySelector("#product-category");
            var tagSelect = document.querySelector("#product-tag");

            if (categorySelect) {
                categorySelect.addEventListener("change", function() {
                    var category = this.value;

                    // AJAX Request to get filtered tags
                    fetch(window.ajaxurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: new URLSearchParams({
                                action: "filter_tags",
                                product_cat: category,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            tagSelect.innerHTML = '<option value="">All Tags</option>';
                            data.forEach(tag => {
                                tagSelect.innerHTML += `<option value="${tag.slug}">${tag.name}</option>`;
                            });
                        })
                        .catch(error => console.error("Error fetching tags:", error));
                });
            }
        });
    </script>
<?php
});
get_footer('shop'); ?>