<?php
/**
 * Template part for displaying the page hero section.
 *
 * @package lc-boba2025
 */

?>
<section class="page_hero">
    <div class="container-xl px-0">
        <div class="page_hero__grid">
            <div class="page_hero__title">
                <?php
                $page_title = get_field( 'title' ) ? get_field( 'title' ) : get_the_title();
                echo '<h1>' . esc_html( $page_title ) . '</h1>';
                ?>
            </div>
            <?php
            if ( get_field( 'content' ) ?? null ) {
                ?>
                <div class="page_hero__inner">
                    <?= esc_html( get_field( 'content' ) ); ?>
                </div>
                <?php
            }
            $l = get_field( 'cta_link' ) ?? null;
            if ( $l ) {
                ?>
                <div class="page_hero__cta">
                    <a href="<?= esc_url( $l['url'] ); ?>" target="<?= esc_attr( $l['target'] ); ?>" class="button button-primary mb-3 align-self-start"><?= esc_html( $l['title'] ); ?></a>
                </div>
                <?php
            }
            ?>
            <div class="page_hero__slider">
                <div class="overlay"></div>
                <div id="page-hero-carousel" class="splide" data-aos="fadein" data-aos-delay="200">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                            $carousel_images = get_field( 'carousel' );
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

</section>
<?php
global $lc_page_hero_script_added;
if ( ! isset( $lc_page_hero_script_added ) ) {
    $lc_page_hero_script_added = true;
    add_action(
        'wp_footer',
        function () {
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
        },
        9999
    );
}
