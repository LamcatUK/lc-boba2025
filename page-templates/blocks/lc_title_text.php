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

?>
<section class="title_text py-4 <?= $bg ?>" data-aos="fade">
    <div class="container-xl py-4 text-center <?= $ill ?> <?= $illpos ?>">
        <?php
        $d = 0;
        if ($title = get_field('title')) {
            $level = get_field('level') === 'h2' ? 'h2' : 'h1';
            echo "<{$level}>{$title}</{$level}>";
        }
        if (get_field('content') ?? null) {
        ?>
            <?= get_field('content') ?>
        <?php
        }
        if (get_field('cta') ?? null) {
            $l = get_field('cta');
        ?>
            <a href="<?= $l['url'] ?>" target="<?= $l['target'] ?>" class="button button-primary mt-4"><?= $l['title'] ?></a>
        <?php
        }
        ?>
    </div>
</section>