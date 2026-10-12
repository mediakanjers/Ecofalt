<?php
    $titel    = get_field('titel');
    $tekst    = get_field('tekst');
    $mengsels = get_field('mengsels') ?: [];

    if (!$titel && !$mengsels) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Mengsels: vul een titel en mengsels in.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-mengsels', 'mk-on-dark'];
    // Met tekst bij de mengsels: kaartjes even breed over de volle breedte
    if (array_filter(array_column($mengsels, 'tekst'))) $classes[] = 'mk-mengsels--met-tekst';
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-mengsels__container">
        <?php if ($titel): ?>
            <h2 class="mk-mengsels__titel mk-h3"><?php echo esc_html($titel); ?></h2>
        <?php endif; ?>

        <?php if ($tekst): ?>
            <p class="mk-mengsels__tekst"><?php echo nl2br(esc_html($tekst)); ?></p>
        <?php endif; ?>

        <?php if ($mengsels): ?>
            <ol class="mk-mengsels__lijst">
                <?php foreach ($mengsels as $i => $mengsel):
                    $link  = $mengsel['link'] ?? null;
                    $klik  = !empty($link['url']) && !$is_preview;
                    $tag   = $klik ? 'a' : 'div';
                    $attrs = $klik
                        ? ' href="' . esc_url($link['url']) . '" target="' . esc_attr($link['target'] ?: '_self') . '"' . (($link['target'] ?? '') === '_blank' ? ' rel="noopener noreferrer"' : '')
                        : '';
                    $pijl  = !empty($link['url']);
                ?>
                    <li>
                        <?php // Nummer en kleur van het vierkantje gaan automatisch op volgorde ?>
                        <<?php echo $tag; ?> class="mk-mengsels__kaart<?php echo $pijl ? ' mk-mengsels__kaart--link' : ''; ?>" data-nummer="<?php echo esc_attr(($i % 3) + 1); ?>"<?php echo $attrs; ?>>
                            <span class="mk-mengsels__nummer" aria-hidden="true"><?php echo esc_html($i + 1); ?></span>
                            <span class="mk-mengsels__info">
                                <?php if (!empty($mengsel['titel'])): ?>
                                    <span class="mk-mengsels__kaart-titel"><?php echo esc_html($mengsel['titel']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($mengsel['subtitel'])): ?>
                                    <span class="mk-mengsels__kaart-sub"><?php echo esc_html($mengsel['subtitel']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($mengsel['tekst'])): ?>
                                    <span class="mk-mengsels__kaart-tekst"><?php echo nl2br(esc_html($mengsel['tekst'])); ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if ($pijl): ?>
                                <span class="mk-mengsels__pijl" aria-hidden="true"></span>
                            <?php endif; ?>
                        </<?php echo $tag; ?>>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</section>
