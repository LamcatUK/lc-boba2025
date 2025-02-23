<section class="recent_posts">
    <div class="container-xl">
        <h2>Recent Posts</h2>
        <div class="recent_posts__grid row g-4">
            <?php
            $q = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 4
            ));
            while ($q->have_posts()) {
                $q->the_post();
            ?>
                <div class="col-sm-6 col-lg-3">
                    <a href="<?= get_the_permalink() ?>" class="recent_posts__card">
                        <?= get_the_post_thumbnail(get_the_ID(), 'medium', ['class' => 'recent_posts__image']) ?>
                        <h3><?= get_the_title() ?></h3>
                    </a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</section>