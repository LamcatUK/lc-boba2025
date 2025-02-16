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
