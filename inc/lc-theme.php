<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

require_once LC_THEME_DIR . '/inc/lc-utility.php';
require_once LC_THEME_DIR . '/inc/lc-blocks.php';
require_once LC_THEME_DIR . '/inc/post-types.php';
// require_once LC_THEME_DIR . '/inc/lc-news.php';


// Remove unwanted SVG filter injection WP
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

add_theme_support('woocommerce');

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

add_action('template_redirect', function () {
    ob_start();
});

add_action('wp_footer', function () {
    $buffer = ob_get_clean();
    $buffer = str_replace('class="container"', 'class="container-xl"', $buffer);
    echo $buffer;
}, 9999);



// Remove comment-reply.min.js from footer
function remove_comment_reply_header_hook()
{
    wp_deregister_script('comment-reply');
}
add_action('init', 'remove_comment_reply_header_hook');

add_action('admin_menu', 'remove_comments_menu');
function remove_comments_menu()
{
    remove_menu_page('edit-comments.php');
}

add_filter('theme_page_templates', 'child_theme_remove_page_template');
function child_theme_remove_page_template($page_templates)
{
    // unset($page_templates['page-templates/blank.php'],$page_templates['page-templates/empty.php'], $page_templates['page-templates/fullwidthpage.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
    unset($page_templates['page-templates/blank.php'], $page_templates['page-templates/empty.php'], $page_templates['page-templates/left-sidebarpage.php'], $page_templates['page-templates/right-sidebarpage.php'], $page_templates['page-templates/both-sidebarspage.php']);
    return $page_templates;
}
add_action('after_setup_theme', 'remove_understrap_post_formats', 11);
function remove_understrap_post_formats()
{
    remove_theme_support('post-formats', array('aside', 'image', 'video', 'quote', 'link'));
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(
        array(
            'page_title'     => 'Site-Wide Settings',
            'menu_title'    => 'Site-Wide Settings',
            'menu_slug'     => 'theme-general-settings',
            'capability'    => 'edit_posts',
        )
    );
}

function widgets_init()
{

    register_nav_menus(array(
        'primary_nav' => __('Primary Nav', 'lc-boba2025'),
        'footer_menu1' => __('Footer Nav', 'lc-boba2025'),
    ));

    unregister_sidebar('hero');
    unregister_sidebar('herocanvas');
    unregister_sidebar('statichero');
    unregister_sidebar('left-sidebar');
    unregister_sidebar('right-sidebar');
    unregister_sidebar('footerfull');
    unregister_nav_menu('primary');

    add_theme_support('disable-custom-colors');
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
add_action('widgets_init', 'widgets_init', 11);


remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

//Custom Dashboard Widget
add_action('wp_dashboard_setup', 'register_lc_dashboard_widget');
function register_lc_dashboard_widget()
{
    wp_add_dashboard_widget(
        'lc_dashboard_widget',
        'Lamcat',
        'lc_dashboard_widget_display'
    );
}

function lc_dashboard_widget_display()
{
?>
    <div style="display: flex; align-items: center; justify-content: space-around;">
        <img style="width: 50%;"
            src="<?= get_stylesheet_directory_uri() . '/img/lc-full.jpg'; ?>">
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



function understrap_all_excerpts_get_more_link($post_excerpt)
{
    if (is_admin() || ! get_the_ID()) {
        return $post_excerpt;
    }
    return $post_excerpt;
}

//* Remove Yoast SEO breadcrumbs from Revelanssi's search results
add_filter('the_content', 'wpdocs_remove_shortcode_from_index');
function wpdocs_remove_shortcode_from_index($content)
{
    if (is_search()) {
        $content = strip_shortcodes($content);
    }
    return $content;
}


function cb_theme_enqueue()
{
    $the_theme = wp_get_theme();
    // wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
    wp_enqueue_style('aos-style', "https://unpkg.com/aos@2.3.1/dist/aos.css", array());
    wp_enqueue_script('aos', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), null, true);
    wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css', array(), null);
    wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js', array(), null, true);
    // wp_deregister_script('jquery');
    // wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.3.min.js', array(), null, true);
    // wp_enqueue_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array('jquery'), null, true);

}
add_action('wp_enqueue_scripts', 'cb_theme_enqueue');



// function add_custom_menu_item($items, $args)
// {
//     if ($args->theme_location == 'primary_nav') {
//         $new_item = '<li class="menu-item menu-item-type-post_tyep menu-item-object-page nav-item"><a href="' . esc_url(home_url('/search/')) . '" class="nav-link" title="Search"><span class="icon-search"></span></a></li>';
//         $items .= $new_item;
//     }

//     return $items;
// }
// add_filter('wp_nav_menu_items', 'add_custom_menu_item', 10, 2);

function remove_dashboard_widgets()
{
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // At a Glance
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); // Activity
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Quick Draft
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress News and Events

    // Yoast SEO Widgets
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal'); // "Yoast SEO Posts Overview"
    remove_meta_box('wpseo-wincher-dashboard-overview', 'dashboard', 'normal'); // "Yoast SEO / Wincher: Top Keyphrases"

}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');


function format_scientific_name($name)
{
    if (!$name) {
        return ''; // Return empty if no name provided
    }

    $name = trim($name); // Remove leading/trailing spaces
    $words = explode(' ', $name); // Split into words

    if (count($words) > 0) {
        $words[0] = ucfirst(strtolower($words[0])); // Capitalize first word (Genus)
    }

    for ($i = 1; $i < count($words); $i++) {
        // Keep certain Latin abbreviations in lowercase (e.g., "var.", "subsp.")
        if (!in_array(strtolower($words[$i]), ['var.', 'subsp.', 'ssp.'])) {
            $words[$i] = strtolower($words[$i]); // Lowercase species & variety names
        }
    }

    return '<em>' . implode(' ', $words) . '</em>'; // Italicize and return
}

function lc_fix_active_nav_classes($classes, $item)
{
    global $post;

    // Get current post type
    $current_post_type = get_query_var('post_type') ? get_query_var('post_type') : (isset($post) ? get_post_type($post) : '');

    // Get the CPT archive URL
    $cpt_archive_url = get_post_type_archive_link('plant-guide');

    // Blog Index Page ID (if using a static page for blog)
    $blog_index_id = get_option('page_for_posts');

    // Remove incorrect 'current_page_parent' from Blog when viewing CPT
    if ($current_post_type === 'plant-guide' && in_array('current_page_parent', $classes)) {
        $classes = array_diff($classes, ['current_page_parent']);
    }

    // If this menu item links to the CPT archive, highlight it when viewing the archive or a single CPT post
    if (($current_post_type === 'plant-guide' || is_singular('plant-guide')) && $item->url === $cpt_archive_url) {
        $classes[] = 'current-menu-item';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'lc_fix_active_nav_classes', 10, 2);

function format_photo_attribution($attribution)
{
    // Regex to handle variations of attribution formats (any text after the author before license)
    $pattern = '/^By (.+?) - .*?, (CC BY(?:-SA)? [^,]+), (https?:\/\/[^\s,]+)$/';

    if (preg_match($pattern, $attribution, $matches)) {
        $author = esc_html($matches[1]); // Extract author (handles parentheses)
        $license = esc_html($matches[2]); // Extract license (handles CC BY & CC BY-SA)
        $url = esc_url($matches[3]); // Extract URL

        return '<a href="' . $url . '" class="photo-attribution" target="_blank" rel="noopener noreferrer">
                    Photo by ' . $author . ' — ' . $license . '
                </a>';
    }

    return ''; // Return nothing if format doesn't match
}


add_filter('wpcf7_autop_or_not', '__return_false');
