<section class="contact_form">
    <div class="container-xl">
        <h1>Contact Us</h1>
        <div class="row">
            <div class="col-md-6">
                <div><?= apply_filters('the_content', get_field('content')) ?></div>
            </div>
            <div class="col-md-6">
                <?php
                $id = get_field('form_id') ?? null;
                echo do_shortcode('[contact-form-7 id="' . $id . '"]');
                ?>
            </div>
        </div>
    </div>
</section>