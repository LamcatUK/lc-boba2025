<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>
<div id="footer-top"></div>
<footer class="footer py-4">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-md-4 col-lg-3">
                <img src="<?= get_stylesheet_directory_uri() ?>/img/boba-full.svg" alt="Boba's Plants" class="footer__logo">
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 pt-4">
                <div class="mx-4 mx-md-auto">
                    <div class="fw-700 pb-2">Links</div>
                    <?= wp_nav_menu(array('theme_location' => 'footer_menu1', 'container_class' => 'footer__menu')) ?>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3 pt-4 text-center text-sm-start">
                <div class="fw-700 pb-2">Contact</div>
                <?= do_shortcode('[contact_email_icon_text]') ?>
                <div class="fw-700 pb-2 pt-4">Connect</div>
                <div class="footer__socials">
                    <?= do_shortcode('[social_icons]') ?>
                </div>

            </div>
            <div class="col-lg-3 pt-4 text-center">
                <img src="<?= get_stylesheet_directory_uri() ?>/img/surrey-hills-enterprises.png" alt="Surrey Hills Enterprises" class="mb-3">
                <div class="fw-700">Boba's Plants is a proud member of Surrey Hills Enterprises</div>
            </div>
        </div>
    </div>
</footer>
<div class="colophon py-2">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-md-8">
                <?= get_field('footer_disclaimer', 'option') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 text-center text-md-start">
                &copy; <?= date('Y') ?> Mugosa Limited, trading as Boba's Plants. Registered in England and Wales, Company No. 07845902
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="/privacy-policy/">Privacy</a> and <a href="/cookies-policy/">Cookies</a> | Site by <a href="https://www.lamcat.co.uk/" target="_blank">Lamcat</a>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>

</html>