<?php

function parse_phone($phone)
{
    $phone = preg_replace('/\s+/', '', $phone);
    $phone = preg_replace('/\(0\)/', '', $phone);
    $phone = preg_replace('/[\(\)\.]/', '', $phone);
    $phone = preg_replace('/-/', '', $phone);
    $phone = preg_replace('/^0/', '+44', $phone);
    return $phone;
}

function split_lines($content)
{
    $content = preg_replace('/<br \/>/', '<br>&nbsp;<br>', $content);
    return $content;
}

add_shortcode('contact_address', function () {
    $output = get_field('contact_address', 'option');
    return $output;
});

add_shortcode('contact_phone', function () {
    if (get_field('contact_phone', 'option')) {
        return '<a href="tel:' . parse_phone(get_field('contact_phone', 'option')) . '">' . get_field('contact_phone', 'option') . '</a>';
    }
    return;
});
add_shortcode('contact_email', function () {
    if (get_field('contact_email', 'option')) {
        return '<a href="mailto:' . get_field('contact_email', 'option') . '">' . get_field('contact_email', 'option') . '</a>';
    }
    return;
});
add_shortcode('contact_email_icon', function () {
    if (get_field('contact_email', 'option')) {
        return '<a href="mailto:' . get_field('contact_email', 'option') . '"><i class="fas fa-envelope"></i></a>';
    }
    return;
});
add_shortcode('contact_email_icon_text', function () {
    if (get_field('contact_email', 'option')) {
        return '<a href="mailto:' . get_field('contact_email', 'option') . '"><i class="far fa-envelope"></i> ' . get_field('contact_email', 'option') . '</a>';
    }
    return;
});

// Generate individual social icon shortcodes
function lc_social_icon_shortcode($atts)
{
    $atts = shortcode_atts(['type' => ''], $atts);
    if (!$atts['type']) {
        return '';
    }

    $social = get_field('social', 'option');
    $urls = [
        'facebook'  => $social['facebook_url'] ?? '',
        'instagram' => $social['instagram_url'] ?? '',
        'twitter'   => $social['twitter_url'] ?? '',
        'pinterest' => $social['pinterest_url'] ?? '',
        'youtube'   => $social['youtube_url'] ?? '',
        'linkedin'  => $social['linkedin_url'] ?? '',
    ];

    if (!isset($urls[$atts['type']]) || empty($urls[$atts['type']])) {
        return '';
    }

    $url = esc_url($urls[$atts['type']]);
    $icon = esc_attr($atts['type']);

    return '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
}

// Register individual social icon shortcodes
$social_types = ['facebook', 'instagram', 'twitter', 'pinterest', 'youtube', 'linkedin'];
foreach ($social_types as $type) {
    add_shortcode('social_' . $type . '_icon', function () use ($type) {
        return lc_social_icon_shortcode(['type' => $type]);
    });
}

// Generate a single shortcode to output all social icons
add_shortcode('social_icons', function ($atts) {
    $atts = shortcode_atts([
        'class' => '',
    ], $atts, 'social_icons');

    $social = get_field('social', 'option');
    if (!$social) {
        return '';
    }

    $icons = [];
    $social_map = [
        'facebook'  => 'facebook-f',
        'instagram' => 'instagram',
        'twitter'   => 'x-twitter',
        'pinterest' => 'pinterest',
        'youtube'   => 'youtube',
        'linkedin'  => 'linkedin',
    ];

    foreach ($social_map as $key => $icon) {
        if (!empty($social[$key . '_url'])) {
            $url = esc_url($social[$key . '_url']);
            $icons[] = '<a href="' . $url . '" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-brands fa-' . $icon . '"></i></a>';
        }
    }

    return !empty($icons) ? '<div class="social-icons ' . $class . '">' . implode(' ', $icons) . '</div>' : '';
});



/**
 * Grab the specified data like Thumbnail URL of a publicly embeddable video hosted on Vimeo.
 *
 * @param  str $video_id The ID of a Vimeo video.
 * @param  str $data 	  Video data to be fetched
 * @return str            The specified data
 */
function get_vimeo_data_from_id($video_id, $data)
{
    // width can be 100, 200, 295, 640, 960 or 1280
    $request = wp_remote_get('https://vimeo.com/api/oembed.json?url=https://vimeo.com/' . $video_id . '&width=960');

    $response = wp_remote_retrieve_body($request);

    $video_array = json_decode($response, true);

    return $video_array[$data];
}

function lc_gutenberg_admin_styles()
{
    echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1040px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }

            /* stop horizontal overflow flicker */
            .block-editor-page #wpwrap {
                overflow-y: auto !important;
            }
        </style>
    ';
}
add_action('admin_head', 'lc_gutenberg_admin_styles');


// disable full-screen editor view by default
if (is_admin()) {
    function lc_disable_editor_fullscreen_by_default()
    {
        $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
        wp_add_inline_script('wp-blocks', $script);
    }
    add_action('enqueue_block_editor_assets', 'lc_disable_editor_fullscreen_by_default');
}



function get_the_top_ancestor_id()
{
    global $post;
    if ($post->post_parent) {
        $ancestors = array_reverse(get_post_ancestors($post->ID));
        return $ancestors[0];
    } else {
        return $post->ID;
    }
}

function lc_json_encode($string)
{
    // $value = json_encode($string);
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $string);
    $result = json_encode($result);
    return $result;
}

function lc_time_to_8601($string)
{
    $time = explode(':', $string);
    $output = 'PT' . $time[0] . 'H' . $time[1] . 'M' . $time[2] . 'S';
    return $output;
}


function lcdump($var)
{
    // ob_start();
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    return; // ob_get_clean();
}

function lcslugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces[] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function lc_social_share($id)
{
    ob_start();
    $url = get_the_permalink($id);

?>
    <div class="text-larger text--yellow mb-5">
        <div class="h4 text-dark">Share</div>
        <a target='_blank' href='https://twitter.com/share?url=<?= $url ?>' class="mr-2"><i class='fab fa-twitter'></i></a>
        <a target='_blank' href='http://www.linkedin.com/shareArticle?url=<?= $url ?>' class="mr-2"><i class='fab fa-linkedin-in'></i></a>
        <a target='_blank' href='http://www.facebook.com/sharer.php?u=<?= $url ?>'><i class='fab fa-facebook-f'></i></a>
    </div>
    <?php

    $out = ob_get_clean();
    return $out;
}


function enable_strict_transport_security_hsts_header()
{
    header('Strict-Transport-Security: max-age=31536000');
}
add_action('send_headers', 'enable_strict_transport_security_hsts_header');


function lc_list($field)
{
    ob_start();
    $field = strip_tags($field, '<br />');
    $bullets = preg_split("/\r\n|\n|\r/", $field);
    foreach ($bullets as $b) {
        if ($b == '') {
            continue;
        }
    ?>
        <li><?= $b ?></li>
<?php
    }
    return ob_get_clean();
}

/**
 * formatBytes
 *
 * Returns img tag with srcset.
 *
 * @param	string $size
 * @param	int    $precision
 * @return	string
 */
function formatBytes($size, $precision = 2)
{
    if ($size <= 0) {
        return '0 B'; // Return 0 bytes as a default value
    }

    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}


/**
 * lc_featured_image
 *
 * Returns img tag with srcset.
 *
 * @param	string $id The post ID.
 * @return	string
 */
function lc_featured_image($id)
{
    $tag = get_the_post_thumbnail(
        $id,
        'full',
        array(
            'srcset' => wp_get_attachment_image_url(get_post_thumbnail_id(), 'medium') . ' 480w, ' .
                wp_get_attachment_image_url(get_post_thumbnail_id(), 'large') . ' 640w, ' .
                wp_get_attachment_image_url(get_post_thumbnail_id(), 'full') . ' 960w'
        )
    );
    return $tag;
}

// REMOVE TAG AND COMMENT SUPPORT

// Disable Tags Dashboard WP
add_action('admin_menu', 'my_remove_sub_menus');

function my_remove_sub_menus()
{
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
}
// Remove tags support from posts
function myprefix_unregister_tags()
{
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
add_action('init', 'myprefix_unregister_tags');

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

function remove_comments()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'remove_comments');


function estimate_reading_time_in_minutes($content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false)
{
    // In case if content is build with gutenberg parse blocks
    if ($with_gutenberg) {
        $blocks = parse_blocks($content);
        $contentHtml = '';

        foreach ($blocks as $block) {
            $contentHtml .= render_block($block);
        }

        $content = $contentHtml;
    }

    // Remove HTML tags from string
    $content = wp_strip_all_tags($content);

    // When content is empty return 0
    if (!$content) {
        return 0;
    }

    // Count words containing string
    $words_count = str_word_count($content);

    // Calculate time for read all words and round
    $minutes = ceil($words_count / $words_per_minute);

    if ($formatted) {
        $minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise($minutes, 'minute') . '</p>';
    }

    return $minutes;
}

function pluralise($quantity, $singular, $plural = null)
{
    if ($quantity == 1 || !strlen($singular)) return $singular;
    if ($plural !== null) return $plural;

    $last_letter = strtolower($singular[strlen($singular) - 1]);
    switch ($last_letter) {
        case 'y':
            return substr($singular, 0, -1) . 'ies';
        case 's':
            return $singular . 'es';
        default:
            return $singular . 's';
    }
}

function get_all_block_names_from_content($id)
{
    // Parse blocks from the content
    $content = get_post_field('post_content', $id);
    $blocks = parse_blocks($content);
    $block_names = [];

    // Recursively find all block names
    foreach ($blocks as $block) {
        if (isset($block['blockName']) && !empty($block['blockName'])) {
            $block_names[] = $block['blockName'];
        }
        if (isset($block['innerBlocks']) && !empty($block['innerBlocks'])) {
            $inner_block_names = get_all_block_names_from_content(serialize_blocks($block['innerBlocks']));
            $block_names = array_merge($block_names, $inner_block_names);
        }
    }

    // Remove duplicates and reindex
    return array_values(array_unique($block_names));
}

// Function to display pages in a hierarchical list
function display_page_hierarchy($parent_id = 0)
{

    $posts_page_id = get_option('page_for_posts');

    // Get the pages with the specified parent, sorted by title
    $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'parent' => $parent_id,
        'sort_column' => 'post_title',  // Sort by post title for alphabetical order
        'sort_order' => 'ASC',          // Sort ascending (A-Z)
        'exclude' => $posts_page_id     // Exclude the posts page (blog index)
    );

    $pages = get_pages($args);

    $output = '';

    if (!empty($pages)) {
        $output .= '<ul>';
        foreach ($pages as $page) {
            // check index status
            $noindex = get_post_meta($page->ID, '_yoast_wpseo_meta-robots-noindex', true);

            if ($noindex != '1') {
                $output .= '<li><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a>';

                // Recursively display child pages, also sorted by title
                $output .= display_page_hierarchy($page->ID); // Get nested child pages

                $output .= '</li>';
            }
        }
        $output .= '</ul>';
    }

    return $output;
}

// Register the shortcode to display the hierarchical page list
function register_page_list_shortcode()
{
    // Start output buffering
    ob_start();

    // Display the hierarchical list
    echo display_page_hierarchy();

    // Return the buffered content
    return ob_get_clean();
}
add_shortcode('page_list', 'register_page_list_shortcode');


function get_gutenberg_h2_headings_from_page()
{
    global $post;

    // Get the raw post content
    $content = $post->post_content;

    // Parse the blocks in the content
    $blocks = parse_blocks($content);

    $h2_headings = [];
    $stack = $blocks; // Use a stack to process blocks iteratively

    while (!empty($stack)) {
        $block = array_shift($stack); // Get the blocks in order

        // Check if it's a heading block
        if ($block['blockName'] === 'core/heading') {
            // Check the level attribute (assume default is 2 if not set)
            $level = $block['attrs']['level'] ?? 2;

            if ($level === 2) {
                // Extract the rendered HTML
                $html = $block['innerHTML'] ?? '';

                // Extract the ID using regex
                if (preg_match('/<h2[^>]*id=["\']([^"\']+)["\']/', $html, $matches)) {
                    $id = $matches[1];
                } else {
                    $id = null;
                }

                // Extract the content
                $content = strip_tags($html);

                // Add the H2 heading to the array
                $h2_headings[] = [
                    'content' => $content,
                    'id'      => $id,
                ];
            }
        }

        // Add inner blocks to the stack
        if (!empty($block['innerBlocks'])) {
            $stack = array_merge($stack, $block['innerBlocks']);
        }
    }

    return $h2_headings;
}


add_filter('wp_handle_upload', 'custom_image_optimizer');

function custom_image_optimizer($upload)
{
    // Check if it's an image
    $file_type = wp_check_filetype($upload['file']);
    if (!in_array($file_type['type'], ['image/jpeg', 'image/png'])) {
        return $upload; // Skip non-image uploads
    }

    $image = wp_get_image_editor($upload['file']);
    if (!is_wp_error($image)) {
        // Resize (optional) - Set max width/height
        $image->resize(1200, 1200, false); // Resize but keep aspect ratio
        // Compress (set quality)
        $image->set_quality(80);
        // Save the optimized image
        $image->save($upload['file']);
    }

    return $upload;
}


// CUSTOM LOGIN URL
/*
add_action('init', 'custom_login_url');
function custom_login_url() {
    $custom_slug = 'my-custom-login'; // Change this to your desired slug

    // Check if the current request is for the default wp-login.php
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        wp_redirect(home_url('/' . $custom_slug));
        exit;
    }

    // Load the default login functionality for the custom slug
    if (strpos($_SERVER['REQUEST_URI'], $custom_slug) !== false) {
        require_once ABSPATH . 'wp-login.php';
        exit;
    }
}

// Prevent direct access to wp-admin for non-logged-in users
add_action('template_redirect', 'redirect_non_logged_users');
function redirect_non_logged_users() {
    if (is_admin() && !is_user_logged_in()) {
        wp_redirect(home_url('/my-custom-login'));
        exit;
    }
}
    */