<?php
    $fotos = get_field('galerij') ?: [];

    if (!$fotos) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Galerij slider: voeg foto\'s toe.', 'mk') . '</p>';
        }
        return;
    }

    // Markup en Fancybox: template-parts/components/foto-slider.php
    get_template_part('template-parts/components/foto-slider', null, [
        'fotos'   => $fotos,
        'groep'   => 'galerij-' . sanitize_html_class($block['id'] ?? wp_unique_id()),
        'preview' => $is_preview,
        'classes' => !empty($block['className']) ? [$block['className']] : [],
        'anchor'  => $block['anchor'] ?? '',
    ]);
