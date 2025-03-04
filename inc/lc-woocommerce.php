<?php

//add cart to nav if not empty
add_action('wp_head', 'add_cart_icon_to_header_nav');

function add_cart_icon_to_header_nav()
{

    if (WC()->cart->is_empty()) {
        return;
    }

    if (is_page('basket')) {
        return;
    }

?>
    <a id="stickyCart" title="Cart" href="<?= wc_get_cart_url() ?>" class="nav-link">
        <div class="fw-700">My Basket</div>
        <span><i class="fa-solid fa-basket-shopping"></i> <?php echo WC()->cart->get_cart_contents_count(); ?></span>
    </a>
    <?php

}


// remove add to basket button from product previews
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);


// NEW PRODUCT FORM
function create_woocommerce_product($post_id)
{
    if (get_post_type($post_id) !== 'product') {
        return;
    }

    // Prevent running on autosave or revisions
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    // Get ACF fields
    $product_name = get_field('product_name', $post_id);
    $short_desc = get_field('short_description', $post_id);
    $long_desc = get_field('long_description', $post_id);
    $price = get_field('price', $post_id);
    $category = get_field('category', $post_id);
    $tags = get_field('tags', $post_id);
    $images = get_field('product_images', $post_id); // ACF Gallery Field

    // Ensure post slug is clean
    $post_slug = sanitize_title($product_name);

    // Update the post
    wp_update_post([
        'ID'           => $post_id,
        'post_title'   => $product_name,
        'post_content' => $long_desc,
        'post_excerpt' => $short_desc,
        'post_name'    => $post_slug,
    ]);

    // Set product price
    update_post_meta($post_id, '_price', $price);
    update_post_meta($post_id, '_regular_price', $price);

    // Assign categories
    if (!empty($category)) {
        if (!is_array($category)) {
            $category = [$category];
        }
        wp_set_object_terms($post_id, $category, 'product_cat');
    }

    // Assign tags
    if (!empty($tags)) {
        if (is_array($tags)) {
            wp_set_object_terms($post_id, $tags, 'product_tag');
        } else {
            wp_set_object_terms($post_id, array_map('trim', explode(',', $tags)), 'product_tag');
        }
    }

    // Handle images
    if (!empty($images) && is_array($images)) {
        $gallery_ids = [];
        $first_image_set = false;

        foreach ($images as $index => $image) {
            $attachment_id = is_numeric($image) ? $image : attachment_url_to_postid($image);

            if ($attachment_id) {
                if (!$first_image_set) {
                    // ✅ Ensure first image is the thumbnail
                    set_post_thumbnail($post_id, $attachment_id);
                    update_post_meta($post_id, '_thumbnail_id', $attachment_id);
                    $first_image_set = true;
                } else {
                    // ✅ Store remaining images for gallery
                    $gallery_ids[] = $attachment_id;
                }
            }
        }

        if (!empty($gallery_ids)) {
            update_post_meta($post_id, '_product_image_gallery', implode(',', $gallery_ids));
            // Ensure WooCommerce picks up gallery images correctly
            delete_transient('wc_product_images_' . $post_id);
            delete_transient('wc_product_variations_' . $post_id);
        }
    }

    // ✅ Force WooCommerce to refresh the product images
    delete_transient('wc_product_children_' . $post_id);
    wc_delete_product_transients($post_id);
}
add_action('acf/save_post', 'create_woocommerce_product', 20);




function custom_product_success_message($post_id)
{
    if (get_post_type($post_id) !== 'product') {
        return;
    }

    $product_title = get_the_title($post_id);
    wp_redirect(add_query_arg(
        array(
            'product_created' => '1',
            'product_name' => urlencode($product_title),
            'product_id' => $post_id
        ),
        site_url('/add-new-product/')
    ));

    exit;
}
add_action('acf/save_post', 'custom_product_success_message', 20);

function acf_add_bootstrap_classes($field)
{

    // Ensure 'wrapper' exists
    if (isset($field['wrapper']['class'])) {
        $field['wrapper']['class'] .= ' mb-3'; // Adds bottom margin
    } else {
        $field['wrapper']['class'] = 'mb-3';
    }

    // Add Bootstrap form-control class for text inputs
    if (in_array($field['type'], array('text', 'email', 'number', 'url', 'password', 'textarea'))) {
        $field['class'] .= ' form-control';
    }

    // Add Bootstrap form-select class for select fields
    if ($field['type'] === 'select') {
        $field['class'] .= ' form-select';
    }

    // Add Bootstrap form-check class for checkboxes and radios
    if ($field['type'] === 'checkbox' || $field['type'] === 'radio') {
        if (isset($field['wrapper']['class'])) {
            $field['wrapper']['class'] .= ' form-check';
        } else {
            $field['wrapper']['class'] = 'form-check';
        }
        $field['class'] .= ' form-check-input';
    }

    return $field;
}
add_filter('acf/render_field', 'acf_add_bootstrap_classes', 10, 1);


function force_product_tags($post_id, $post, $update)
{
    // Only run for WooCommerce products, and prevent admin interference
    if ($post->post_type !== 'product' || is_admin()) return;

    // error_log("🔥 force_product_tags() running for post ID: $post_id");

    global $wpdb;

    // Get the selected ACF taxonomy tags
    $selected_tags = get_field('tags', $post_id);
    if (!is_array($selected_tags)) {
        $selected_tags = [];
    }

    // Get new tags from the text field
    $new_tags_text = get_field('new_tags', $post_id);
    // error_log("🆕 New Tags Field: " . $new_tags_text);

    if (!empty($new_tags_text)) {
        $new_tags_array = array_map('trim', explode(',', $new_tags_text));

        foreach ($new_tags_array as $tag_name) {
            if (empty($tag_name)) continue;

            $existing_term = term_exists($tag_name, 'product_tag');

            if ($existing_term) {
                $tag_id = (int) $existing_term['term_id'];
                // error_log("✅ Tag exists: $tag_name (ID: $tag_id)");
            } else {
                $new_term = wp_insert_term($tag_name, 'product_tag');
                if (!is_wp_error($new_term)) {
                    $tag_id = (int) $new_term['term_id'];
                    // error_log("🆕 Tag created: $tag_name (ID: $tag_id)");
                } else {
                    // error_log("❌ Error creating tag: " . $new_term->get_error_message());
                    continue;
                }
            }

            if (!in_array($tag_id, $selected_tags)) {
                $selected_tags[] = $tag_id;
            }
        }
    }

    $final_tags = array_unique($selected_tags);
    // error_log("🎯 Final Tags to Assign: " . print_r($final_tags, true));

    if (!empty($final_tags)) {
        // 🚀 Force WooCommerce to accept these tags AFTER it finishes saving
        add_action('shutdown', function () use ($post_id, $final_tags) {
            // error_log("🔄 Running `wp_set_object_terms()` on shutdown for post ID: $post_id");
            wp_set_object_terms($post_id, $final_tags, 'product_tag');
        });

        // error_log("✅ Tags scheduled for assignment on post ID: $post_id");
    }
}

// ✅ Ensure this function runs for WooCommerce products
add_action('wp_after_insert_post', 'force_product_tags', 99, 3);


// filtering 
function enqueue_product_filter_scripts()
{
    wp_enqueue_script('product-filter', get_stylesheet_directory_uri() . '/js/filter-products.js', array('jquery'), null, true);
    // wp_localize_script('product-filter', 'ajaxurl', admin_url('admin-ajax.php'));
    wp_localize_script('product-filter', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_product_filter_scripts');


function filter_products_ajax()
{
    $tax_query = array('relation' => 'AND');

    if (!empty($_POST['product_cat'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $_POST['product_cat'],
        );
    }

    if (!empty($_POST['product_tag'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $_POST['product_tag'],
            'operator' => 'AND',
        );
    }

    $query = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'tax_query'      => $tax_query,
    ));

    if ($query->have_posts()) {
        echo '<div class="row">';
        while ($query->have_posts()) {
            $query->the_post();
            // wc_get_template_part('content', 'product');
            $product = wc_get_product(get_the_ID());
            // $cats = wp_get_post_terms(get_the_ID(), 'product_cat', array('fields' => 'names'));
            // $tags = wp_get_post_terms(get_the_ID(), 'product_tag', array('fields' => 'names'));
    ?>
            <div class="col-12 col-sm-6 col-md-4 mb-4">
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
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No products found.</p>';
    }
    wp_die();
}
add_action('wp_ajax_filter_products', 'filter_products_ajax');
add_action('wp_ajax_nopriv_filter_products', 'filter_products_ajax');


function update_available_filters()
{
    $selected_tags = isset($_POST['selected_tags']) && $_POST['selected_tags'] !== "none"
        ? explode(',', sanitize_text_field($_POST['selected_tags']))
        : [];

    $selected_categories = isset($_POST['selected_categories']) && $_POST['selected_categories'] !== "none"
        ? explode(',', sanitize_text_field($_POST['selected_categories']))
        : [];

    // error_log("Received Filters - Categories: " . print_r($selected_categories, true));
    // error_log("Received Filters - Tags: " . print_r($selected_tags, true));

    $available_cats = [];
    $available_tags = [];

    $tax_query = ['relation' => 'AND'];

    if (!empty($selected_tags)) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $selected_tags,
        ];
    }

    if (!empty($selected_categories)) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $selected_categories,
        ];
    }

    if (empty($selected_tags) && empty($selected_categories)) {
        // error_log("No filters selected, returning all categories and tags.");
        $query = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);
    } else {
        // error_log("Running Filtered Query...");
        $query = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => $tax_query,
        ]);
    }

    if ($query->have_posts()) {
        foreach ($query->posts as $post_id) {
            $categories = wp_get_post_terms($post_id, 'product_cat', ['fields' => 'slugs']);
            $tags = wp_get_post_terms($post_id, 'product_tag', ['fields' => 'slugs']);

            $available_cats = array_merge($available_cats, $categories);
            $available_tags = array_merge($available_tags, $tags);
        }
    }

    $available_cats = array_values(array_unique($available_cats));
    $available_tags = array_values(array_unique($available_tags));

    // error_log("Filtered Categories: " . print_r($available_cats, true));
    // error_log("Filtered Tags: " . print_r($available_tags, true));

    wp_send_json([
        'categories' => $available_cats,
        'tags'       => $available_tags,
    ]);
}
add_action('wp_ajax_update_filters', 'update_available_filters');
add_action('wp_ajax_nopriv_update_filters', 'update_available_filters');
