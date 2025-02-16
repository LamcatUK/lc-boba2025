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

    // Ensure this is only for new product submissions
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    // Get form data
    $product_name = get_field('product_name', $post_id);
    $short_desc = get_field('short_description', $post_id);
    $long_desc = get_field('long_description', $post_id);
    $price = get_field('price', $post_id);
    $category = get_field('category', $post_id);
    $tags = get_field('tags', $post_id);
    $images = get_field('product_images', $post_id); // Assuming this is a gallery field

    $post_slug = sanitize_title($product_name);

    // Set product title and descriptions
    wp_update_post(array(
        'ID'           => $post_id,
        'post_title'   => $product_name,
        'post_content' => $long_desc,
        'post_excerpt' => $short_desc,
        'post_name'    => $post_slug,
    ));

    // Set product price
    update_post_meta($post_id, '_price', $price);
    update_post_meta($post_id, '_regular_price', $price);

    // Ensure $category is an array
    $category = get_field('category', $post_id);
    if (!empty($category)) {
        if (!is_array($category)) {
            $category = array($category); // Convert single category to array
        }
        wp_set_object_terms($post_id, $category, 'product_cat'); // Assign categories
    }

    // Ensure $tags is processed correctly
    $tags = get_field('tags', $post_id);
    if (!empty($tags)) {
        if (is_array($tags)) {
            wp_set_object_terms($post_id, $tags, 'product_tag'); // Assign multiple tags
        } else {
            wp_set_object_terms($post_id, array_map('trim', explode(',', $tags)), 'product_tag'); // Handle comma-separated string
        }
    }

    // Handle images & videos
    if (!empty($images)) {
        $gallery_ids = array();
        foreach ($images as $image) {
            $attachment_id = is_numeric($image) ? $image : attachment_url_to_postid($image);
            if ($attachment_id) {
                $gallery_ids[] = $attachment_id;
            }
        }
        if (!empty($gallery_ids)) {
            update_post_meta($post_id, '_product_image_gallery', implode(',', $gallery_ids));
            set_post_thumbnail($post_id, $gallery_ids[0]); // Set first image as featured image
        }
    }
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

    error_log("🔥 force_product_tags() running for post ID: $post_id");

    global $wpdb;

    // Get the selected ACF taxonomy tags
    $selected_tags = get_field('tags', $post_id);
    if (!is_array($selected_tags)) {
        $selected_tags = [];
    }

    // Get new tags from the text field
    $new_tags_text = get_field('new_tags', $post_id);
    error_log("🆕 New Tags Field: " . $new_tags_text);

    if (!empty($new_tags_text)) {
        $new_tags_array = array_map('trim', explode(',', $new_tags_text));

        foreach ($new_tags_array as $tag_name) {
            if (empty($tag_name)) continue;

            $existing_term = term_exists($tag_name, 'product_tag');

            if ($existing_term) {
                $tag_id = (int) $existing_term['term_id'];
                error_log("✅ Tag exists: $tag_name (ID: $tag_id)");
            } else {
                $new_term = wp_insert_term($tag_name, 'product_tag');
                if (!is_wp_error($new_term)) {
                    $tag_id = (int) $new_term['term_id'];
                    error_log("🆕 Tag created: $tag_name (ID: $tag_id)");
                } else {
                    error_log("❌ Error creating tag: " . $new_term->get_error_message());
                    continue;
                }
            }

            if (!in_array($tag_id, $selected_tags)) {
                $selected_tags[] = $tag_id;
            }
        }
    }

    $final_tags = array_unique($selected_tags);
    error_log("🎯 Final Tags to Assign: " . print_r($final_tags, true));

    if (!empty($final_tags)) {
        // 🚀 Force WooCommerce to accept these tags AFTER it finishes saving
        add_action('shutdown', function () use ($post_id, $final_tags) {
            error_log("🔄 Running `wp_set_object_terms()` on shutdown for post ID: $post_id");
            wp_set_object_terms($post_id, $final_tags, 'product_tag');
        });

        error_log("✅ Tags scheduled for assignment on post ID: $post_id");
    }
}

// ✅ Ensure this function runs for WooCommerce products
add_action('wp_after_insert_post', 'force_product_tags', 99, 3);
