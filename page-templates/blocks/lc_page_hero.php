<section class="page_hero">
    <div class="container-xl px-0">
        <div class="row g-4">
            <div class="col-md-6 d-flex flex-column align-items-start justify-content-center" data-aos="fade">
                <?php
                $title = get_field('title') ?: get_the_title();

                echo '<h1>' . $title . '</h1>';

                if (get_field('content') ?? null) {
                ?>
                    <div class="mb-3"><?= get_field('content') ?></div>
                <?php
                }

                $l = get_field('cta_link') ?? null;
                if ($l) {
                ?>
                    <a href="<?= $l['url'] ?>" target="<?= $l['target'] ?>" class="button button-primary mb-3 align-self-start"><?= $l['title'] ?></a>
                <?php
                }
                ?>
            </div>
            <div class="col-md-6">
                <div id="page-hero-carousel" class="splide" data-aos="fadein" data-aos-delay="200">
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
global $lc_page_hero_script_added;
if (!isset($lc_page_hero_script_added)) {
    $lc_page_hero_script_added = true;
    add_action('wp_footer', function () {
?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const carousel = document.querySelector("#page-hero-carousel");

                if (!carousel) return;

                const slides = carousel.querySelectorAll(".splide__slide");

                new Splide("#page-hero-carousel", {
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
    }, 9999);
}
