<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
get_header();

$category = get_queried_object(); // Get the current category

$page_for_posts = get_option('page_for_posts');

?>

</section>
<main id="main" class="blog_index">
    <div class="container-xl py-5">
        <h1><?= esc_html($category->name) ?></h1>
        <?php
        if (!empty($category->description)) {
        ?>
            <div class="mb-4 index_intro">
                <?= apply_filters('the_content', $category->description) ?></p>
            </div>
        <?php
        }

        $categories = get_categories([
            'hide_empty' => true, // Only include categories with posts
        ]);

        if (!empty($categories)) {
            echo '<div class="blog_index__categories mb-4">';
            echo '<a href="/bobas-blog/">All</a>'; // Link back to all blog
            foreach ($categories as $cat) {
                $active_class = $cat->term_id === $category->term_id ? 'active' : ''; // Highlight current category
                echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" class="' . $active_class . '">';
                echo esc_html($cat->name);
                echo '</a>';
            }
            echo '</div>';
        } else {
            echo '<p>No categories found with posts.</p>';
        }
        ?>
        <div class="blog_index__grid">
            <?php
            if (have_posts()) {
                $length = 50;
                while (have_posts()) {
                    the_post();
                    $post_categories = get_the_category();
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
                                    if ($post_categories) {
                                        foreach ($post_categories as $post_category) {
                                    ?>
                                            <span class="blog_index__category"><?= esc_html($post_category->name) ?></span>
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
                }
            } else {
                echo '<p>No posts found in this category.</p>';
            }
            ?>
            <?= understrap_pagination() ?>
        </div>
    </div>
</main>
<?php
get_footer();
