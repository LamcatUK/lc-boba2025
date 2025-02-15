<section class="latest_products py-4" data-aos="fade">
    <div class="container-xl py-4">
        <div class="text-center mb-5">
            <h2><?= get_field('title') ?></h2>
            <?= get_field('intro') ?>
        </div>
        <div class="row g-4">
            <?php
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'publish',
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product = wc_get_product(get_the_ID());
            ?>
                    <div class="product col-md-6 col-lg-3">
                        <a href="<?= esc_url(get_permalink()) ?>" class="product__card">
                            <?= get_the_post_thumbnail(get_the_ID(), 'woocommerce_thumbnail', array('class' => 'product__image')) ?>
                            <div class="product__price"><?= $product->get_price_html() ?></div>
                            <div class="product__detail">
                                <h3><?= esc_html(get_the_title()) ?></h3>
                                <div class="mb-3"><?= $product->get_short_description() ?></div>
                                <div class="reserve">View Details</div>
                            </div>
                        </a>
                    </div>
            <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
        <div class="text-center">
            <a href="/shop/" class="button button-primary mt-4">All Products</a>
        </div>
    </div>
</section>