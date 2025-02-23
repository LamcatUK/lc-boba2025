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
            <div class="col-lg-9">
                <div class="single__content">
                    <h1 class="single__title"><?= get_the_title() ?></h1>
                    <?= get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'single__image']) ?>
                    <?php
                    $count = estimate_reading_time_in_minutes(get_the_content(), 200, true, true) ?? null;
                    if ($count) {
                        echo '<div class="fs-200">' . $count . '</div>';
                    }

                    foreach ($blocks as $block) {
                        echo render_block($block);
                    }
                    ?>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sidebar">
                    <?php
                    $cats = get_the_category();
                    $ids = wp_list_pluck($cats, 'term_id');
                    $r = new WP_Query(array(
                        'category__in' => $ids,
                        'posts_per_page' => 4,
                        'post__not_in' => array(get_the_ID())
                    ));
                    if ($r->have_posts()) {
                    ?>
                        <div class="ff-heading fs-600 has-green-500-color">Related Posts</div>
                        <div class="related">
                            <div class="row g-4">
                                <?php
                                while ($r->have_posts()) {
                                    $r->the_post();
                                ?>
                                    <div class="col-sm-6 col-lg-12">
                                        <a class="related__card"
                                            href="<?= get_the_permalink() ?>">
                                            <?= get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'related__image']) ?>
                                            <div class="related__content">
                                                <h3 class="related__title">
                                                    <?= get_the_title() ?>
                                                </h3>
                                            </div>
                                        </a>
                                    </div>
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