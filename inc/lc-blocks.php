<?php
/**
 * LC Blocks Registration
 *
 * This file contains the registration of custom ACF blocks and modifications
 * to Gutenberg core blocks for the LC Boba 2025 theme.
 *
 * PHP version 8.3
 *
 * @category WordPress_Theme
 * @package  LC_Boba2025
 * @author   Lamcat - DS
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.lamcat.co.uk
 */

/**
 * Register ACF blocks for the LC Boba 2025 theme.
 *
 * This function registers custom ACF blocks using the Advanced Custom Fields
 * plugin. Each block is defined with its name, title, category, icon, render
 * template, and other properties.
 *
 * @return void
 */
function acf_blocks() {
    if ( function_exists( 'acf_register_block_type' ) ) {

        acf_register_block_type(
            array(
                'name'            => 'lc_beyond_showcase',
                'title'           => __( 'LC Beyond Boba Showcase' ),
                'category'        => 'layout',
                'icon'            => 'cover-image',
                'render_template' => 'page-templates/blocks/lc-beyond-showcase.php',
                'mode'            => 'edit',
                'supports'        => array( 'mode' => false ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_products_by_brand',
                'title'           => __( 'LC Products by Brand' ),
                'category'        => 'layout',
                'icon'            => 'cover-image',
                'render_template' => 'page-templates/blocks/lc_products_by_brand.php',
                'mode'            => 'edit',
                'supports'        => array( 'mode' => false ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_latest_products',
                'title'           => __( 'LC Latest Products' ),
                'category'        => 'layout',
                'icon'            => 'cover-image',
                'render_template' => 'page-templates/blocks/lc_latest_products.php',
                'mode'            => 'edit',
                'supports'        => array( 'mode' => false ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_home_hero',
                'title'           => __( 'LC Home Hero' ),
                'category'        => 'layout',
                'icon'            => 'cover',
                'render_template' => 'page-templates/blocks/lc_home_hero.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_page_hero',
                'title'           => __( 'LC Page Hero' ),
                'category'        => 'layout',
                'icon'            => 'cover',
                'render_template' => 'page-templates/blocks/lc_page_hero.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_title_text',
                'title'           => __( 'LC Title Text' ),
                'category'        => 'layout',
                'icon'            => 'editor-textcolor',
                'render_template' => 'page-templates/blocks/lc_title_text.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_upcoming_events',
                'title'           => __( 'LC Upcoming Events' ),
                'category'        => 'layout',
                'icon'            => 'calendar',
                'render_template' => 'page-templates/blocks/lc_upcoming_events.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_all_events',
                'title'           => __( 'LC All Events' ),
                'category'        => 'layout',
                'icon'            => 'calendar',
                'render_template' => 'page-templates/blocks/lc_all_events.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_text_image',
                'title'           => __( 'LC Text / Image' ),
                'category'        => 'layout',
                'icon'            => 'format-image',
                'render_template' => 'page-templates/blocks/lc_text_image.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_latest_blogs',
                'title'           => __( 'LC Latest Blogs' ),
                'category'        => 'layout',
                'icon'            => 'list-view',
                'render_template' => 'page-templates/blocks/lc_latest_blogs.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_recent_posts',
                'title'           => __( 'LC Recent Posts' ),
                'category'        => 'layout',
                'icon'            => 'list-view',
                'render_template' => 'page-templates/blocks/lc_recent_posts.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );

        acf_register_block_type(
            array(
                'name'            => 'lc_contact',
                'title'           => __( 'LC Contact Form' ),
                'category'        => 'layout',
                'icon'            => 'list-view',
                'render_template' => 'page-templates/blocks/lc_contact_form.php',
                'mode'            => 'edit',
                'supports'        => array(
                    'mode'   => false,
                    'anchor' => true,
                ),
            )
        );
    }
}
add_action( 'acf/init', 'acf_blocks' );


// Gutenburg core modifications.

/**
 * Modify arguments for core block types.
 *
 * This function adds a render callback to specific core block types
 * to wrap their content in a container.
 *
 * @param array  $args Arguments for registering a block type.
 * @param string $name Name of the block type.
 *
 * @return array Modified arguments for the block type.
 */
function core_image_block_type_args( $args, $name ) {
    if ( 'core/paragraph' === $name ) {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ( 'core/heading' === $name ) {
        $args['render_callback'] = 'modify_core_add_container';
    }
    if ( 'core/list' === $name ) {
        $args['render_callback'] = 'modify_core_add_container';
    }
    return $args;
}
add_filter( 'register_block_type_args', 'core_image_block_type_args', 10, 3 );

/**
 * Wraps the content of specific core blocks in a container.
 *
 * This function is used as a render callback to add a container
 * around the content of core blocks like paragraph, heading, and list.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block content.
 *
 * @return string Modified block content wrapped in a container.
 */
function modify_core_add_container( $attributes, $content ) {
    ob_start();
    ?>
    <div class="container-xl">
        <?= wp_kses_post( $content ); ?>
    </div>
    <?php
    $content = ob_get_clean();
    return $content;
}
