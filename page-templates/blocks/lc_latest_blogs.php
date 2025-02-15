<section class="latest_blogs has-green-100-background-color py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between flex-wrap mb-4">
            <h2 class="h1">Boba's Blog</h2>
            <a href="/bobas-blog/" class="button button-primary">All Articles</a>
        </div>
        <div class="blog__grid">
            <?php
            $q = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 4,
            ));

            $is_first = true;
            while ($q->have_posts()) {
                $q->the_post();
            ?>
                <a class="blog__card" href="<?= get_the_permalink() ?>">
                    <?= get_the_post_thumbnail($q->ID, 'large', ['class' => 'blog__image']) ?>
                    <div class="blog__content">
                        <h3><?= get_the_title() ?></h3>
                        <?php
                        if ($is_first) {
                        ?>
                            <div class="blog__excerpt pb-3"><?= wp_trim_words(get_the_content(), 30) ?></div>
                        <?php
                        }
                        ?>
                        <div class="fs-200">Posted on <?= get_the_date('jS F, Y') ?>
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)) {
                                echo ' in ';
                                foreach ($categories as $category) {
                                    echo esc_html($category->name);
                                }
                            }
                            ?>
                        </div>
                    </div>
                </a>
            <?php
                $is_first = false;
            }

            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>