<?php
    $titel   = get_field('titel');
    $kaarten = get_field('kaarten') ?: [];

    if (!$kaarten) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Kenmerken: voeg kaartjes toe.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-kenmerken'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if ($titel): ?>
        <h2 class="mk-kenmerken__titel mk-h3"><?php echo esc_html($titel); ?></h2>
    <?php endif; ?>

    <?php // Kaartjes: template-parts/components/icoon-kaart.php (zelfde als in de hero) ?>
    <div class="mk-icoon-kaarten">
        <?php foreach ($kaarten as $kaart): ?>
            <?php get_template_part('template-parts/components/icoon-kaart', null, ['kaart' => $kaart, 'kop' => $titel ? 'h3' : 'h2', 'preview' => $is_preview]); ?>
        <?php endforeach; ?>
    </div>
</section>
