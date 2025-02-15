<?php
function lc_register_events_cpt()
{
    $labels = array(
        'name'               => __('Events'),
        'singular_name'      => __('Event'),
        'menu_name'          => __('Events'),
        'name_admin_bar'     => __('Event'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Event'),
        'edit_item'          => __('Edit Event'),
        'new_item'           => __('New Event'),
        'view_item'          => __('View Event'),
        'search_items'       => __('Search Events'),
        'not_found'          => __('No events found'),
        'not_found_in_trash' => __('No events found in trash'),
    );

    $args = array(
        'label'              => __('Events'),
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-calendar-alt', // WordPress event icon
        'supports'           => array('title', 'custom-fields'),
        'has_archive'        => false, // Disable archive page
        'rewrite'            => false, // No frontend URLs
        'exclude_from_search' => true, // Prevent appearing in search
        'publicly_queryable' => false, // Prevent direct access
    );

    register_post_type('event', $args);
}
add_action('init', 'lc_register_events_cpt');

// Modify columns in the Events admin table
function lc_events_admin_columns($columns)
{
    unset($columns['date']); // Remove the publish date
    $columns['event_start_date'] = __('Start Date');
    $columns['event_end_date'] = __('End Date');
    return $columns;
}
add_filter('manage_event_posts_columns', 'lc_events_admin_columns');

// Populate custom columns with ACF data
function lc_events_custom_column_content($column, $post_id)
{
    if ($column == 'event_start_date') {
        echo get_field('event_start_date', $post_id) ?: '—';
    }
    if ($column == 'event_end_date') {
        echo get_field('event_end_date', $post_id) ?: '—';
    }
}
add_action('manage_event_posts_custom_column', 'lc_events_custom_column_content', 10, 2);

function lc_events_sort_by_start_date($query)
{
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'event') {
        $orderby = $query->get('orderby');

        // If no custom sorting is selected, sort by event_start_date
        if (!$orderby) {
            $query->set('meta_key', 'event_start_date');
            $query->set('orderby', 'meta_value');
            $query->set('order', 'ASC'); // Soonest event first
        }
    }
}
add_action('pre_get_posts', 'lc_events_sort_by_start_date');


function lc_events_sortable_columns($columns)
{
    $columns['event_start_date'] = 'event_start_date';
    $columns['event_end_date'] = 'event_end_date';
    return $columns;
}
add_filter('manage_edit-event_sortable_columns', 'lc_events_sortable_columns');

function lc_events_orderby_admin($query)
{
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'event') {
        $orderby = $query->get('orderby');

        if ($orderby == 'event_start_date') {
            $query->set('meta_key', 'event_start_date');
            $query->set('orderby', 'meta_value');
        } elseif ($orderby == 'event_end_date') {
            $query->set('meta_key', 'event_end_date');
            $query->set('orderby', 'meta_value');
        }
    }
}
add_action('pre_get_posts', 'lc_events_orderby_admin');

function lc_redirect_single_event_pages()
{
    if (is_singular('event')) {
        wp_redirect(site_url('/events/'), 301);
        exit;
    }
}
add_action('template_redirect', 'lc_redirect_single_event_pages');

function lc_exclude_events_from_queries($query)
{
    if (!is_admin() && $query->is_main_query() && $query->get('post_type') === 'event') {
        $query->set('post_type', 'post'); // Change to regular posts or prevent output
    }
}
add_action('pre_get_posts', 'lc_exclude_events_from_queries');

function lc_remove_events_from_sitemap($post_types)
{
    unset($post_types['event']);
    return $post_types;
}
add_filter('wpseo_sitemaps_post_types', 'lc_remove_events_from_sitemap');


function lc_register_plants_cpt()
{
    $labels = array(
        'name'               => __('Plants'),
        'singular_name'      => __('Plant'),
        'menu_name'          => __('Plant Guide'),
        'name_admin_bar'     => __('Plant Guide'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New Plant'),
        'edit_item'          => __('Edit Plant'),
        'new_item'           => __('New Plant'),
        'view_item'          => __('View Plant'),
        'search_items'       => __('Search Plants'),
        'not_found'          => __('No plants found'),
        'not_found_in_trash' => __('No plants found in trash'),
    );

    $args = array(
        'label'              => __('Plants'),
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-palmtree',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'revisions'), // Ensure 'editor' is included
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'plant-guide', 'with_front' => false),
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'show_in_rest'       => true, // Enables Gutenberg
    );

    register_post_type('plant-guide', $args);
}
add_action('init', 'lc_register_plants_cpt');
