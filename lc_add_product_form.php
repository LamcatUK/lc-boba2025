<?php

/**
 * Template Name: Add New Product
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

acf_form_head();
get_header();

?>
<main class="container-xl">
    <h1>Add a New Product</h1>

    <?php
    $args = array(
        'post_id'       => 'new_post',
        'new_post'      => array(
            'post_type'   => 'product',
            'post_status' => 'publish',
        ),
        'submit_value'  => 'Create Product',
        'field_groups'  => array('group_67b21b2436032'), // Replace with your ACF field group ID
    );

    acf_form($args);
    ?>
</main>
<?php

get_footer();
