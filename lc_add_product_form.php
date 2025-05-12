<?php
/**
 * Template Name: Add New Product
 *
 * @package lc-boba2025
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Restrict access to logged-in users.
if ( ! is_user_logged_in() ) {
    get_header();
    ?>
    <main>
        <div class="container-xl py-5">
            <h1>Access Restricted</h1>
            <p>You need to be logged in to access this page. Please <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">log in</a>.</p>
        </div>
    </main>
    <?php
    get_footer();
    exit;
}

acf_form_head();
get_header();

// Get query parameters safely.
$product_created = filter_input( INPUT_GET, 'product_created', FILTER_SANITIZE_NUMBER_INT );
$product_name    = filter_input( INPUT_GET, 'product_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$product_id      = filter_input( INPUT_GET, 'product_id', FILTER_VALIDATE_INT );

if ( $product_created && $product_name ) {
    $product_url = get_permalink( $product_id );
    $form_url    = remove_query_arg( array( 'product_created', 'product_name', 'product_id' ) );
    ?>
    <main class="bg--green-200">
        <div class="container-xl py-5">
            <div id="message" class="updated notice notice-success index_intro">
                <h1>Success!</h1>
                <div class="mb-4">Your product "<strong><?php echo esc_html( urldecode( $product_name ) ); ?></strong>" has been created.</div>
                <div class="d-flex flex-wrap gap-4">
                    <a href="<?php echo esc_url( $product_url ); ?>" target="_blank" class="button button-primary mb-3 w-100 w-sm-auto">View Product</a>
                    <a href="<?php echo esc_url( $form_url ); ?>" class="button button-primary mb-3 w-100 w-sm-auto">Add Another Product</a></p>
                </div>
            </div>
        </div>
    </main>
    <?php
} else {
    ?>
    <main>
        <div class="container-xl py-5">
            <h1>Add a New Product</h1>

            <?php
            $args = array(
                'post_id'            => 'new_post',
                'new_post'           => array(
                    'post_type'   => 'product',
                    'post_status' => 'publish',
                ),
                'submit_value'       => 'Create Product',
                'field_groups'       => array( 'group_67b21b2436032' ), // Replace with your ACF field group ID.
                'form_attributes'    => array(
                    'class'      => 'needs-validation', // Enables Bootstrap validation styles.
                    'novalidate' => '', // Disables default browser validation.
                ),
                'html_before_fields' => '<div class="row g-3">', // Bootstrap row with gutters.
                'html_after_fields'  => '</div>',
                'label_placement'    => 'top', // Ensures proper alignment.
            );

            acf_form( $args );
            ?>
        </div>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var priceField = document.querySelector('.acf-field[data-name="price"] .acf-input-wrap input[type="number"]');

            if (priceField) {
                var wrapper = document.createElement('div');
                wrapper.classList.add('input-group');

                var prepend = document.createElement('span');
                prepend.classList.add('input-group-text');
                prepend.textContent = '£';

                // Wrap input in Bootstrap 5 input group
                priceField.parentNode.insertBefore(wrapper, priceField);
                wrapper.appendChild(prepend);
                wrapper.appendChild(priceField);
            }

            var priceInput = document.querySelector('.acf-field[data-name="price"] input[type="text"]');

            if (priceInput) {
                priceInput.addEventListener("input", function(event) {
                    var value = this.value;

                    // Allow only numbers and a single decimal
                    value = value.replace(/[^0-9.]/g, "");

                    // Prevent multiple decimal points
                    var parts = value.split(".");
                    if (parts.length > 2) {
                        value = parts[0] + "." + parts[1]; // Keep first decimal only
                    }

                    // Limit decimal places to two
                    if (value.includes(".")) {
                        var decimalIndex = value.indexOf(".");
                        var integerPart = value.substring(0, decimalIndex);
                        var decimalPart = value.substring(decimalIndex + 1, decimalIndex + 3); // Max 2 decimals
                        value = integerPart + "." + decimalPart;
                    }

                    this.value = value;
                });

                // Format on blur (e.g., '10' → '10.00')
                priceInput.addEventListener("blur", function() {
                    if (this.value !== "" && !this.value.includes(".")) {
                        this.value = parseFloat(this.value).toFixed(2);
                    } else if (this.value.endsWith(".")) {
                        this.value = this.value.slice(0, -1); // Remove trailing dot
                    }
                });
            }
        });
    </script>
    <?php
}
get_footer();
