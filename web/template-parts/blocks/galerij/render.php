<?php
    $fotos = get_field('galerij') ?: [];

    if (!$fotos) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Galerij slider: voeg foto\'s toe.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-galerij', 'mk-slider'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';

    // Fancybox (uit mkbase, CSS laadt daar al) alleen laden waar een galerij staat
    if (!$is_preview) {
        wp_enqueue_script('fancybox-js');
    }
    $fancybox_groep = 'galerij-' . sanitize_html_class($block['id'] ?? wp_unique_id());
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-mk-slider>
    <div class="mk-slider__viewport">
        <div class="mk-slider__track" data-mk-slider-track tabindex="0" role="region" aria-label="<?php esc_attr_e('Fotogalerij', 'mk'); ?>">
            <?php foreach ($fotos as $foto):
                $afbeelding = mk_image($foto, 'large', [
                    'class'     => 'mk-galerij__image',
                    'loading'   => 'lazy',
                    'sizes'     => '(min-width: 900px) 422px, 75vw',
                    'draggable' => 'false',
                ]);
            ?>
                <figure class="mk-galerij__slide" data-mk-slide>
                    <?php if ($is_preview): ?>
                        <?php echo $afbeelding; ?>
                    <?php else: ?>
                        <a class="mk-galerij__link"
                            href="<?php echo esc_url(wp_get_attachment_image_url($foto, 'full')); ?>"
                            data-fancybox="<?php echo esc_attr($fancybox_groep); ?>"
                            data-caption="<?php echo esc_attr(wp_get_attachment_caption($foto)); ?>"
                            draggable="false">
                            <?php echo $afbeelding; ?>
                            <span class="screen-reader-text"><?php esc_html_e('Foto vergroten', 'mk'); ?></span>
                        </a>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>

    <?php get_template_part('template-parts/components/slider-nav', null, ['prev' => __('Vorige foto\'s', 'mk'), 'next' => __('Volgende foto\'s', 'mk')]); ?>
</section>
