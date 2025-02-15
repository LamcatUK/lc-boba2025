<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();
$img = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>
<main id="main" class="blog single">
    <?php
    $content = get_the_content();
    $blocks = parse_blocks($content);
    $sidebar = array();
    $after;
    ?>
    <div class="container-xl">
        <section class="breadcrumbs pt-4">
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
            }
            ?>
        </section>
        <div class="row g-4 pb-4">
            <div class="col-lg-9 single__content">
                <h1 class="single__title"><?= get_the_title() ?></h1>
                <?php
                $image_id = get_post_thumbnail_id();
                $image_caption = wp_get_attachment_caption($image_id);
                ?>
                <figure class="plant-image">
                    <?= get_the_post_thumbnail(get_the_ID(), 'full', ['class' => 'single__image']) ?>
                    <?php
                    if ($image_caption) {
                    ?>
                        <figcaption class="plant-caption"><?= format_photo_attribution($image_caption) ?></figcaption>
                    <?php
                    }
                    ?>
                </figure>
                <?php

                $common_name = get_the_title();
                $scientific_name = get_field('scientific_name') ?? '';
                $alt_names = get_field('aka');

                ?>
                <div class="plant-info">
                    <div class="plant-info__row">
                        <div class="plant-info__title">Common Name:</div>
                        <div class="plant-info__name"><?= esc_html($common_name) ?></div>
                    </div>
                    <?php
                    if ($scientific_name) {
                    ?>
                        <div class="plant-info__row">
                            <div class="plant-info__title">Scientific Name:</div>
                            <div class="plant-info__scientific"><?= format_scientific_name($scientific_name) ?></div>
                        </div>
                    <?php
                    }
                    if ($alt_names) {
                    ?>
                        <div class="plant-info__row">
                            <div class="plant-info__title">Also Known As:</div>
                            <div class="plant-info__alternative"><?= esc_html(is_array($alt_names) ? implode(', ', $alt_names) : $alt_names) ?></div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <?php
                foreach ($blocks as $block) {
                    echo render_block($block);
                }
                ?>
            </div>
            <div class="col-lg-3">
                <div class="sidebar">
                    <?php
                    $cats = get_the_category();
                    $ids = wp_list_pluck($cats, 'term_id');
                    $r = new WP_Query(array(
                        'post_type' => 'plant-guide',
                        'category__in' => $ids,
                        'posts_per_page' => 4,
                        'post__not_in' => array(get_the_ID())
                    ));
                    if ($r->have_posts()) {
                    ?>
                        <div class="ff-heading fs-600 has-green-500-color d-none d-lg-block">Related Plants</div>
                        <div class="related">
                            <?php
                            while ($r->have_posts()) {
                                $r->the_post();
                            ?>
                                <a class="related__card"
                                    href="<?= get_the_permalink() ?>">
                                    <?= get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'related__image']) ?>
                                    <div class="related__content">
                                        <h3 class="related__title">
                                            <?= get_the_title() ?>
                                        </h3>
                                    </div>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
?>