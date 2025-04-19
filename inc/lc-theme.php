<?php
/**
 * LC Theme Functions
 *
 * This file contains theme-specific functions and customizations for the LC Boba 2025 theme.
 *
 * @package LC_Boba2025
 */

defined( 'ABSPATH' ) || exit;

require_once LC_THEME_DIR . '/inc/lc-utility.php';
require_once LC_THEME_DIR . '/inc/lc-blocks.php';
require_once LC_THEME_DIR . '/inc/post-types.php';
require_once LC_THEME_DIR . '/inc/lc-woocommerce.php';
// require_once LC_THEME_DIR . '/inc/lc-news.php';.


// Remove unwanted SVG filter injection WP.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

add_theme_support( 'woocommerce' );

/**
 * Removes WooCommerce product gallery features such as zoom, lightbox, and slider.
 *
 * This function disables the default WooCommerce product gallery features to allow for custom implementations.
 */
function custom_remove_woocommerce_gallery() {
    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
    remove_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'custom_remove_woocommerce_gallery' );


/**
 * Enqueues styles and scripts for the custom product gallery.
 *
 * This function loads the Splide.js library for creating responsive sliders.
 */
function enqueue_custom_product_gallery() {
    wp_enqueue_style( 'splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css', array(), '4.1.3' );
    wp_enqueue_script( 'splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js', array(), '4.1.3', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_product_gallery' );

/**
 * Enqueues styles and scripts for the Lightbox2 library.
 *
 * This function loads the Lightbox2 CSS and JavaScript files for creating image lightbox effects.
 */
function enqueue_lightbox_scripts() {
    wp_enqueue_style( 'lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3' );
    wp_enqueue_script( 'lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array( 'jquery' ), '2.11.3', true );
}
add_action( 'wp_enqueue_scripts', 'enqueue_lightbox_scripts' );


remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_action(
    'template_redirect',
    function () {
        ob_start();
    }
);

add_action(
    'wp_footer',
    function () {
        $buffer = ob_get_clean();
        $buffer = str_replace( 'class="container"', 'class="container-xl"', $buffer );
        // phpcs:ignore
        echo $buffer;
    },
    9999
);



/**
 * Removes the comment-reply.min.js script from the footer.
 *
 * This function deregisters the 'comment-reply' script to prevent it from being loaded.
 */
function remove_comment_reply_header_hook() {
    wp_deregister_script( 'comment-reply' );
}
add_action( 'init', 'remove_comment_reply_header_hook' );


/**
 * Removes the Comments menu from the WordPress admin dashboard.
 *
 * This function hides the Comments menu to prevent users from accessing it.
 */
function remove_comments_menu() {
    remove_menu_page( 'edit-comments.php' );
}

/**
 * Removes specific page templates from the available options in the WordPress admin.
 *
 * This function unsets certain page templates to prevent them from being used.
 *
 * @param array $page_templates The list of available page templates.
 * @return array The modified list of page templates.
 */
function child_theme_remove_page_template( $page_templates ) {
    unset( $page_templates['page-templates/blank.php'], $page_templates['page-templates/empty.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php'] );
    return $page_templates;
}
add_filter( 'theme_page_templates', 'child_theme_remove_page_template' );


/**
 * Removes post format support from the theme.
 *
 * This function disables support for specific post formats such as 'aside', 'image', 'video', 'quote', and 'link'.
 */
function remove_understrap_post_formats() {
    remove_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
add_action( 'after_setup_theme', 'remove_understrap_post_formats', 11 );


if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page(
        array(
            'page_title' => 'Site-Wide Settings',
            'menu_title' => 'Site-Wide Settings',
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'edit_posts',
        )
    );
}

/**
 * Initializes theme widgets and menus.
 *
 * This function registers navigation menus, unregisters sidebars, and sets up theme support for custom colors and editor color palette.
 */
function widgets_init() {

    register_nav_menus(
        array(
            'primary_nav'  => __( 'Primary Nav', 'lc-boba2025' ),
            'footer_menu1' => __( 'Footer Nav', 'lc-boba2025' ),
        )
    );

    unregister_sidebar( 'hero' );
    unregister_sidebar( 'herocanvas' );
    unregister_sidebar( 'statichero' );
    unregister_sidebar( 'left-sidebar' );
    unregister_sidebar( 'right-sidebar' );
    unregister_sidebar( 'footerfull' );
    unregister_nav_menu( 'primary' );

    add_theme_support( 'disable-custom-colors' );
    add_theme_support(
        'editor-color-palette',
        array(
            array(
                'name'  => 'Green 900',
                'slug'  => 'green-900',
                'color' => '#143223',
            ),
            array(
                'name'  => 'Green 500',
                'slug'  => 'green-500',
                'color' => '#3a5a3d',
            ),
            array(
                'name'  => 'Green 400',
                'slug'  => 'green-400',
                'color' => '#99b964',
            ),
            array(
                'name'  => 'Green 300',
                'slug'  => 'green-300',
                'color' => '#e4e4d9',
            ),
            array(
                'name'  => 'Green 200',
                'slug'  => 'green-200',
                'color' => '#f1f1ec',
            ),
            array(
                'name'  => 'Green 100',
                'slug'  => 'green-100',
                'color' => '#f8f8f5',
            ),
            array(
                'name'  => 'Accent 400',
                'slug'  => 'accent-400',
                'color' => '#8B3F5D',
            ),
        )
    );
}
add_action( 'widgets_init', 'widgets_init', 11 );


/**
 * Registers a custom dashboard widget.
 *
 * This function adds a custom widget to the WordPress dashboard
 * to display information and a contact link for Lamcat.
 */
function register_lc_dashboard_widget() {
    wp_add_dashboard_widget(
        'lc_dashboard_widget',
        'Lamcat',
        'lc_dashboard_widget_display'
    );
}
add_action( 'wp_dashboard_setup', 'register_lc_dashboard_widget' );

/**
 * Displays the custom Lamcat dashboard widget.
 *
 * This function outputs the content for the Lamcat dashboard widget,
 * including an image and a contact button.
 */
function lc_dashboard_widget_display() {
    ?>
    <div style="display: flex; align-items: center; justify-content: space-around;">
        <img style="width: 50%;"
            src="<?= esc_url( get_stylesheet_directory_uri() . '/img/lc-full.jpg' ); ?>">
        <a class="button button-primary" target="_blank" rel="noopener nofollow noreferrer"
            href="mailto:hello@lamcat.co.uk/">Contact</a>
    </div>
    <div>
        <p><strong>Thanks for choosing Lamcat!</strong></p>
        <hr>
        <p>Got a problem with your site, or want to make some changes & need us to take a look for you?</p>
        <p>Use the link above to get in touch and we'll get back to you ASAP.</p>
    </div>
    <?php
}

// phpcs:disable
// add_filter('wpseo_breadcrumb_links', function( $links ) {
//     global $post;
//     if ( is_singular( 'post' ) ) {
//         $t = get_the_category($post->ID);
//         $breadcrumb[] = array(
//             'url' => '/guides/',
//             'text' => 'Guides',
//         );
//         array_splice( $links, 1, -2, $breadcrumb );
//     }
//     return $links;
// }
// );
// phpcs:enable


/**
 * Filters the excerpt more link.
 *
 * This function modifies the excerpt more link to ensure it is not displayed in the admin area
 * and only applies to posts with valid IDs.
 *
 * @param string $post_excerpt The current post excerpt.
 * @return string The modified post excerpt.
 */
function understrap_all_excerpts_get_more_link( $post_excerpt ) {
    if ( is_admin() || ! get_the_ID() ) {
        return $post_excerpt;
    }
    return $post_excerpt;
}



/**
 * Removes shortcodes from search results.
 *
 * This function strips shortcodes from the content when displaying search results.
 *
 * @param string $content The content to filter.
 * @return string The filtered content without shortcodes.
 */
function wpdocs_remove_shortcode_from_index( $content ) {
    if ( is_search() ) {
        $content = strip_shortcodes( $content );
    }
    return $content;
}
add_filter( 'the_content', 'wpdocs_remove_shortcode_from_index' );


/**
 * Enqueues theme styles and scripts.
 *
 * This function loads external libraries such as AOS, Splide.js, and jQuery,
 * and deregisters the default WordPress jQuery to replace it with a CDN version.
 */
function cb_theme_enqueue() {
    $the_theme = wp_get_theme();
    // phpcs:disable
    // wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array('jquery'), null, true);
    // phpcs:enable
    wp_enqueue_style( 'aos-style', 'https://unpkg.com/aos@2.3.1/dist/aos.css', array() );
    wp_enqueue_script( 'aos', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), null, true );
    wp_enqueue_style( 'splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css', array(), null );
    wp_enqueue_script( 'splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js', array(), null, true );
    wp_deregister_script( 'jquery' );
    wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'cb_theme_enqueue' );


// phpcs:disable
// function add_custom_menu_item($items, $args)
// {
//     if ($args->theme_location == 'primary_nav') {
//         $new_item = '<li class="menu-item menu-item-type-post_tyep menu-item-object-page nav-item"><a href="' . esc_url(home_url('/search/')) . '" class="nav-link" title="Search"><span class="icon-search"></span></a></li>';
//         $items .= $new_item;
//     }
//     return $items;
// }
// add_filter('wp_nav_menu_items', 'add_custom_menu_item', 10, 2);
// phpcs:enable


/**
 * Removes unwanted widgets from the WordPress dashboard.
 *
 * This function removes default WordPress dashboard widgets and Yoast SEO widgets
 * to declutter the admin interface.
 */
function remove_dashboard_widgets() {
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // At a Glance.
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // Activity.
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // Quick Draft.
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress News and Events.

    // Yoast SEO Widgets.
    remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' ); // "Yoast SEO Posts Overview".
    remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'normal' ); // "Yoast SEO / Wincher: Top Keyphrases".
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );


/**
 * Formats a scientific name by capitalizing the genus, lowercasing species and variety names,
 * and italicizing the entire name.
 *
 * @param string $name The scientific name to format.
 * @return string The formatted scientific name in italics, or an empty string if no name is provided.
 */
function format_scientific_name( $name ) {
    if ( ! $name ) {
        return ''; // Return empty if no name provided.
    }

    $name  = trim( $name ); // Remove leading/trailing spaces.
    $words = explode( ' ', $name ); // Split into words.

    if ( count( $words ) > 0 ) {
        $words[0] = ucfirst( strtolower( $words[0] ) ); // Capitalize first word (Genus).
    }

    $word_count = count( $words );
    for ( $i = 1; $i < $word_count; $i++ ) {
        // Keep certain Latin abbreviations in lowercase (e.g., "var.", "subsp.").
        if ( ! in_array( strtolower( $words[ $i ] ), array( 'var.', 'subsp.', 'ssp.' ), true ) ) {
            $words[ $i ] = strtolower( $words[ $i ] ); // Lowercase species & variety names.
        }
    }

    return '<em>' . implode( ' ', $words ) . '</em>'; // Italicize and return.
}

/**
 * Fixes active navigation classes for custom post types.
 *
 * This function adjusts the CSS classes for navigation menu items to ensure
 * the correct menu item is highlighted when viewing custom post type archives
 * or single posts.
 *
 * @param array  $classes An array of CSS classes applied to the menu item.
 * @param object $item The current menu item object.
 * @return array The modified array of CSS classes.
 */
function lc_fix_active_nav_classes( $classes, $item ) {
    global $post;

    // Get current post type.
    $current_post_type = get_query_var( 'post_type' ) ? get_query_var( 'post_type' ) : ( isset( $post ) ? get_post_type( $post ) : '' );

    // Get the CPT archive URL.
    $cpt_archive_url = get_post_type_archive_link( 'plant-guide' );

    // Blog Index Page ID (if using a static page for blog).
    $blog_index_id = get_option( 'page_for_posts' );

    // Remove incorrect 'current_page_parent' from Blog when viewing CPT.
    if ( 'plant-guide' === $current_post_type && in_array( 'current_page_parent', $classes, true ) ) {
        $classes = array_diff( $classes, array( 'current_page_parent' ) );
    }

    // If this menu item links to the CPT archive, highlight it when viewing the archive or a single CPT post.
    if ( ( 'plant-guide' === $current_post_type || is_singular( 'plant-guide' ) ) && $cpt_archive_url === $item->url ) {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter( 'nav_menu_css_class', 'lc_fix_active_nav_classes', 10, 2 );

/**
 * Formats photo attribution into a clickable link.
 *
 * This function parses a photo attribution string and converts it into a formatted HTML link
 * with the author's name, license, and URL.
 *
 * @param string $attribution The photo attribution string to format.
 * @return string The formatted HTML link or an empty string if the format is invalid.
 */
function format_photo_attribution( $attribution ) {
    // Regex to handle variations of attribution formats (any text after the author before license).
    $pattern = '/^By (.+?) - .*?, (CC BY(?:-SA)? [^,]+), (https?:\/\/[^\s,]+)$/';

    if ( preg_match( $pattern, $attribution, $matches ) ) {
        $author  = esc_html( $matches[1] ); // Extract author (handles parentheses).
        $license = esc_html( $matches[2] ); // Extract license (handles CC BY & CC BY-SA).
        $url     = esc_url( $matches[3] ); // Extract URL.

        return '<a href="' . $url . '" class="photo-attribution" target="_blank" rel="noopener noreferrer">
                    Photo by ' . $author . ' — ' . $license . '
                </a>';
    }

    return ''; // Return nothing if format doesn't match.
}

add_filter( 'wpcf7_autop_or_not', '__return_false' );



/**
 * Blocks specific video file types from being uploaded.
 *
 * This function removes certain video MIME types from the allowed upload list.
 *
 * @param array $mimes The list of allowed MIME types.
 * @return array The modified list of MIME types.
 */
function block_video_uploads( $mimes ) {
    unset( $mimes['mp4'] ); // Block MP4.
    unset( $mimes['mov'] ); // Block MOV.
    unset( $mimes['avi'] ); // Block AVI.
    unset( $mimes['mkv'] ); // Block MKV.
    unset( $mimes['flv'] ); // Block FLV.
    unset( $mimes['wmv'] ); // Block WMV.
    return $mimes;
}
add_filter( 'upload_mimes', 'block_video_uploads' );

/**
 * Hides the video upload button in the WordPress media library.
 *
 * This function adds custom CSS to the admin head to hide the video upload button
 * from the media library interface.
 */
function hide_video_upload_button() {
    echo '<style>
        .media-frame-content .media-menu-item[data-type="video"] { display: none !important; }
    </style>';
}
add_action( 'admin_head', 'hide_video_upload_button' );


/**
 * Blocks specific video file uploads.
 *
 * This function checks the file type of uploaded files and blocks uploads
 * for certain video formats such as MP4, MOV, AVI, MKV, FLV, and WMV.
 *
 * @param array $file The file being uploaded.
 * @return array The modified file array with an error message if blocked.
 */
function block_video_file_upload( $file ) {
    $filetype      = wp_check_filetype( $file['name'] );
    $blocked_types = array( 'mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv' );

    if ( in_array( $filetype['ext'], $blocked_types, true ) ) {
        $file['error'] = '❌ Video uploads are not allowed.';
    }

    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'block_video_file_upload' );
