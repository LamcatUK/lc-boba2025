<section class="products_by_brand">
    <div class="container-xl">
        <?php
        $brand = get_field('brand');

        $tax_query[] = array(
            'taxonomy' => 'product_brand',
            'field'    => 'term_id',
            'terms'    => $brand,
        );

        $query = new WP_Query(array(
            'post_type'      => 'product',
            'posts_per_page' => 12,
            'tax_query'      => $tax_query,
        ));

        if ($query->have_posts()) {
            echo '<div class="row">';
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                // $cats = wp_get_post_terms(get_the_ID(), 'product_cat', array('fields' => 'names'));
                // $tags = wp_get_post_terms(get_the_ID(), 'product_tag', array('fields' => 'names'));
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
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>No products found.</p>';
        }
        ?>
    </div>
</section>