<?php
function acf_blocks()
{
    if (function_exists('acf_register_block_type')) {

        acf_register_block_type(array(
            'name'                => 'lc_products_by_brand', 
            'title'               => __('LC Products by Brand'), 
            'category'            => 'layout',
            'icon'                => 'cover-image', 
            'render_template'    => 'page-templates/blocks/lc_products_by_brand.php', 
            'mode'                => 'edit',
            'supports'            => array('mode' => false),
        ));


        acf_register_block_type(array(
            'name'                => 'lc_latest_products', 
            'title'               => __('LC Latest Products'), 
            'category'            => 'layout',
            'icon'                => 'cover-image', 
            'render_template'    => 'page-templates/blocks/lc_latest_products.php', 
            'mode'                => 'edit',
            'supports'            => array('mode' => false),
        ));

        acf_register_block_type(array(
            'name'                => 'lc_home_hero',
            'title'                => __('LC Home Hero'),
            'category'            => 'layout',
            'icon'                => 'cover',
            'render_template'    => 'page-templates/blocks/lc_home_hero.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_page_hero',
            'title'                => __('LC Page Hero'),
            'category'            => 'layout',
            'icon'                => 'cover',
            'render_template'    => 'page-templates/blocks/lc_page_hero.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_title_text',
            'title'                => __('LC Title Text'),
            'category'            => 'layout',
            'icon'                => 'editor-textcolor',
            'render_template'    => 'page-templates/blocks/lc_title_text.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));

        acf_register_block_type(array(
            'name'                => 'lc_upcoming_events',
            'title'                => __('LC Upcoming Events'),
            'category'            => 'layout',
            'icon'                => 'calendar',
            'render_template'    => 'page-templates/blocks/lc_upcoming_events.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_all_events',
            'title'                => __('LC All Events'),
            'category'            => 'layout',
            'icon'                => 'calendar',
            'render_template'    => 'page-templates/blocks/lc_all_events.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_text_image',
            'title'                => __('LC Text / Image'),
            'category'            => 'layout',
            'icon'                => 'format-image',
            'render_template'    => 'page-templates/blocks/lc_text_image.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_latest_blogs',
            'title'                => __('LC Latest Blogs'),
            'category'            => 'layout',
            'icon'                => 'list-view',
            'render_template'    => 'page-templates/blocks/lc_latest_blogs.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_recent_posts',
            'title'                => __('LC Recent Posts'),
            'category'            => 'layout',
            'icon'                => 'list-view',
            'render_template'    => 'page-templates/blocks/lc_recent_posts.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
        acf_register_block_type(array(
            'name'                => 'lc_contact',
            'title'                => __('LC Contact Form'),
            'category'            => 'layout',
            'icon'                => 'list-view',
            'render_template'    => 'page-templates/blocks/lc_contact_form.php',
            'mode'    => 'edit',
            'supports' => array('mode' => false, 'anchor' => true),
        ));
    }
}
add_action('acf/init', 'acf_blocks');


// Gutenburg core modifications
add_filter('register_block_type_args', 'core_image_block_type_args', 10, 3);
function core_image_block_type_args($args, $name)
{
    if ($name == 'core/paragraph') {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ($name == 'core/heading') {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ($name == 'core/list') {
        $args['render_callback'] = 'modify_core_add_container';
    }
    // if ($name == 'yoast-seo/breadcrumbs') {
    //     $args['render_callback'] = 'modify_core_add_container';
    // }
    return $args;
}

function modify_core_add_container($attributes, $content)
{
    ob_start();
    // $class = $block['className'];
?>
    <div class="container-xl">
        <?= $content ?>
    </div>
<?php
    $content = ob_get_clean();
    return $content;
}
