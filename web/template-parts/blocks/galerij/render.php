<?php
    $fotos = get_field('galerij') ?: [];

    if (!$fotos) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Galerij slider: voeg foto\'s toe.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-galerij'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-galerij__viewport">
        <div class="mk-galerij__track" tabindex="0" role="region" aria-label="<?php esc_attr_e('Fotogalerij', 'mk'); ?>">
            <?php foreach ($fotos as $foto): ?>
                <figure class="mk-galerij__slide">
                    <?php echo mk_image($foto, 'large', [
                        'class'   => 'mk-galerij__image',
                        'loading' => 'lazy',
                        'sizes'   => '(min-width: 900px) 422px, 75vw',
                    ]); ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mk-galerij__nav">
        <div class="mk-galerij__thumb">
            <button type="button" class="mk-galerij__button mk-galerij__button--prev" data-mk-galerij-prev>
                <span class="screen-reader-text"><?php esc_html_e('Vorige foto\'s', 'mk'); ?></span>
            </button>
            <button type="button" class="mk-galerij__button mk-galerij__button--next" data-mk-galerij-next>
                <span class="screen-reader-text"><?php esc_html_e('Volgende foto\'s', 'mk'); ?></span>
            </button>
        </div>
    </div>
</section>
