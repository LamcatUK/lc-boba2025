<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();

$page_for_posts = get_option('page_for_posts');

?>
<main id="main" class="blog_index">
    <div class="container-xl pt-5 my-auto">
        <h1>Boba's Blog</h1>
        <div class="mb-4 index_intro">
            <?= apply_filters('the_content', get_the_content(null, false, $page_for_posts)) ?>
        </div>
        <?php
        $categories = get_categories([
            'hide_empty' => true, // Only include categories with posts
        ]);

        if (!empty($categories)) {
            echo '<div class="blog_index__categories mb-4">';
            echo '<a href="/bobas-blog/" class="active">All</a>';
            foreach ($categories as $category) {
                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">';
                echo esc_html($category->name);
                echo '</a>';
            }
            echo '</div>';
        } else {
            echo '<p>No categories found with posts.</p>';
        }

        ?>
        <div class="blog_index__grid">
            <?php
            $length = 50;
            while (have_posts()) {
                the_post();
                $categories = get_the_category();
            ?>
                <a href="<?= get_the_permalink() ?>"
                    class="blog_index__card">
                    <?= get_the_post_thumbnail(get_the_ID(), 'large', ['class' => 'blog_index__image']) ?>
                    <div class="blog_index__inner">
                        <h2><?= get_the_title() ?></h2>
                        <p><?= wp_trim_words(get_the_content(), $length) ?>
                        </p>
                        <div class="blog_index__meta">
                            <div>
                                <?= get_the_date() ?>
                            </div>
                            |
                            <div class="blog_index__categories">
                                <?php
                                if ($categories) {
                                    foreach ($categories as $category) {
                                ?>
                                        <span class="blog_index__category"><?= esc_html($category->name) ?></span>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php
                /*
                if ($c != '') {
                    $l = get_field('cta_link','option');
                ?>
                    <section class="cta my-2">
                        <div class="container-xl px-5 py-4 d-flex justify-content-between align-items-center gap-4 flex-wrap">
                            <h2 class="h4 mb-0 mx-auto ms-md-0"><?=get_field('cta_title','option')?></h2>
                            <p><?=get_field('cta_content','option')?></p>
                            <a href="<?=$l['url']?>" class="button button-primary align-self-center mx-auto me-md-0"><?=$l['title']?></a>
                        </div>
                    </section>
            <?php
                }
                */
                $length = 40;
            }
            ?>
            <?= understrap_pagination() ?>
        </div>
    </div>
</main>
<?php
get_footer();
