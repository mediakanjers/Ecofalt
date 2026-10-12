<?php
    // Foto-slider met Fancybox — gebruikt door het Galerij-blok en de projectpagina.
    // $args: 'fotos' (ID's), 'groep' (Fancybox-groep), 'preview' (block-editor), 'classes', 'anchor'
    $fotos   = $args['fotos'] ?? [];
    $groep   = $args['groep'] ?? 'galerij-' . wp_unique_id();
    $preview = !empty($args['preview']);
    $classes = array_merge(['mk-galerij', 'mk-slider'], (array) ($args['classes'] ?? []));
    $anchor  = !empty($args['anchor']) ? ' id="' . esc_attr($args['anchor']) . '"' : '';

    if (!$fotos) return;

    // Fancybox (uit mkbase, CSS laadt daar al) alleen laden waar een galerij staat
    if (!$preview) {
        wp_enqueue_script('fancybox-js');
    }
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
                    <?php if ($preview): ?>
                        <?php echo $afbeelding; ?>
                    <?php else: ?>
                        <a class="mk-galerij__link"
                            href="<?php echo esc_url(wp_get_attachment_image_url($foto, 'full')); ?>"
                            data-fancybox="<?php echo esc_attr($groep); ?>"
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
