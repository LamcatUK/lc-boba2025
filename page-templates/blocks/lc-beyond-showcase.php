<?php
/**
 * Template for the Beyond Boba Showcase block.
 *
 * @package lc-boba2025
 */

?>
<section class="beyond_showcase has-green-100-background-color py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between flex-wrap mb-4">
            <h2 class="h1">Beyond Boba</h2>
            <a href="/beyond-boba/" class="button button-primary">See More</a>
        </div>
		<div class="row g-5">
			<div class="col-md-6">
				<?= esc_html( get_field( 'content' ) ); ?>
			</div>
			<div class="col-md-6">
                <div id="beyond-boba-carousel" class="splide" data-aos="fadein" data-aos-delay="200">
					<div class="splide__track">
						<ul class="splide__list">
							<?php
							$carousel_images = get_field( 'showcase' );
							if ( ! empty( $carousel_images ) ) {
								foreach ( $carousel_images as $c ) {
									?>
							<li class="splide__slide"><?= wp_get_attachment_image( $c, 'large', false, array( 'alt' => '' ) ); ?></li>
									<?php
								}
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
    </div>
</section>
<?php
global $lc_showcase_script_added;
if ( ! isset( $lc_showcase_script_added ) ) {
    $lc_showcase_script_added = true;
    add_action(
        'wp_footer',
        function () {
            ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const carousel = document.querySelector("#beyond-boba-carousel");

                if (!carousel) return;

                const slides = carousel.querySelectorAll(".splide__slide");

                new Splide("#beyond-boba-carousel", {
                    type: "loop",
                    perPage: 1,
                    pagination: slides.length > 1, // Enable pagination only if more than 1 slide
                    arrows: slides.length > 1, // Enable arrows only if more than 1 slide
                    autoplay: true,
                    interval: 4000,
                }).mount();
            });
        </script>
            <?php
        },
        9999
    );
}
