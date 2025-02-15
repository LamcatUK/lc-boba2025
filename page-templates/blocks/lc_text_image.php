<?php
if ($bg = get_field('background')) {
    $bg = 'has-' . $bg . '-background-color';
}

if ($ill = get_field('illustration')) {
    if ($ill != 'none') {
        $ill = 'illustration-' . $ill;

        $illpos = get_field('align');
        $illpos = 'illustration-' . $illpos;
    }
}

$class = $block['className'] ?? 'py-5';

switch (get_field('split')) {
    case 4060:
        $colText = 'col-md-4';
        $colImage = 'col-md-8';
        break;
    case 6040:
        $colText = 'col-md-8';
        $colImage = 'col-md-4';
        break;
    case 7030:
        $colText = 'col-md-9';
        $colImage = 'col-md-3';
        break;
    default:
        $colText = 'col-md-6';
        $colImage = 'col-md-6';
}

$orderText = get_field('order') == 'Text Image' ? 'order-md-1' : 'order-md-2';
$orderImage = get_field('order') == 'Text Image' ? 'order-md-2' : 'order-md-1';

if (isset($block['anchor'])) {
    echo '<a id="' . esc_attr($block['anchor']) . '" class="anchor"></a>';
}

$link = get_field('image_link') ?? null;

?>
<section
    class="text_image <?= $bg ?> py-4">
    <div class="container-xl py-4 <?= $ill ?> <?= $illpos ?>">
        <div class="row gy-5">
            <div
                class="<?= $colText ?> <?= $orderText ?> d-flex flex-column justify-content-center" data-aos="fadein">
                <h2 class="mb-4">
                    <?= get_field('title') ?>
                </h2>
                <div>
                    <?= get_field('content') ?>
                </div>
                <?php
                if (get_field('cta') ?? null) {
                    $l = get_field('cta');
                ?>
                    <a href="<?= $l['url'] ?>" target="<?= $l['target'] ?>" class="button button-primary mt-5"><?= $l['title'] ?></a>
                <?php
                }
                ?>
            </div>
            <div
                class="<?= $colImage ?> <?= $orderImage ?> d-flex justify-content-center align-items-center">
                <div class="text_image__image has-shadow--lg" data-aos="fadein" data-aos-delay="200">
                    <?= wp_get_attachment_image(get_field('image'), 'large', false, ['alt' => '']) ?>
                </div>
            </div>
        </div>
    </div>
</section>