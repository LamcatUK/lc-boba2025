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

    // Set product title and descriptions
    wp_update_post(array(
        'ID'           => $post_id,
        'post_title'   => $product_name,
        'post_content' => $long_desc,
        'post_excerpt' => $short_desc,
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
