<section class="home_hero">
    <div class="container-xl px-0">
        <div class="row g-4">
            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center text-center" data-aos="fade">
                <img src="<?= get_stylesheet_directory_uri() ?>/img/boba-full.svg" alt="Boba's Plants" class="home_hero__logo d-block mx-auto mb-5 mt-3">
                <?php
                $l = get_field('cta_link') ?? null;
                if ($l) {
                ?>
                    <a href="<?= $l['url'] ?>" target="<?= $l['target'] ?>" class="button button-primary mb-3"><?= $l['title'] ?></a>
                <?php
                }
                ?>
            </div>
            <div class="col-md-6">
                <div id="home-hero-carousel" class="splide" data-aos="fadein" data-aos-delay="200">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                            $carousel_images = get_field('carousel');
                            if (!empty($carousel_images)) {
                                foreach ($carousel_images as $c) {
                            ?>
                                    <li class="splide__slide"><?= wp_get_attachment_image($c, 'large', false, ['alt' => '']) ?></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
</section>
<?php
global $lc_home_hero_script_added;
if (!isset($lc_home_hero_script_added)) {
    $lc_home_hero_script_added = true;
    add_action('wp_footer', function () {
?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (!document.querySelector("#home-hero-carousel")) return;

                new Splide("#home-hero-carousel", {
                    type: "loop", // Enables infinite looping
                    perPage: 1, // Show one slide at a time
                    pagination: true, // Show pagination dots
                    arrows: true, // Show previous/next arrows
                    autoplay: true, // Enable auto-play
                    interval: 4000, // 4s per slide
                }).mount();
            });
        </script>
<?php
    }, 9999);
}
