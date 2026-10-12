<?php
    $titel        = get_field('titel');
    $tekst        = get_field('tekst');
    $kop          = get_field('kop_toepassingen');
    $toepassingen = get_field('toepassingen') ?: [];
    $knop         = get_field('knop');
    $kop_titel    = get_field('paginatitel') ? 'h1' : 'h2'; // bovenaan een pagina: de H1

    if (!$titel && !$toepassingen) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Toepassingen: vul een titel en toepassingen in.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-toepassingen'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-toepassingen__container">
        <?php if ($titel || $tekst): ?>
            <div class="mk-toepassingen__intro">
                <?php if ($titel): ?>
                    <<?php echo $kop_titel; ?> class="mk-toepassingen__titel mk-<?php echo $kop_titel; ?>"><?php echo esc_html($titel); ?></<?php echo $kop_titel; ?>>
                <?php endif; ?>
                <?php if ($tekst): ?>
                    <p class="mk-toepassingen__tekst"><?php echo nl2br(esc_html($tekst)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($toepassingen): ?>
            <?php if ($kop): ?>
                <h3 class="mk-toepassingen__kop"><?php echo esc_html($kop); ?></h3>
            <?php endif; ?>

            <ul class="mk-toepassingen__lijst">
                <?php foreach ($toepassingen as $toepassing):
                    $link  = $toepassing['link'] ?? null;
                    $klik  = !empty($link['url']) && !$is_preview;
                    $tag   = $klik ? 'a' : 'div';
                    $attrs = $klik
                        ? ' href="' . esc_url($link['url']) . '" target="' . esc_attr($link['target'] ?: '_self') . '"' . (($link['target'] ?? '') === '_blank' ? ' rel="noopener noreferrer"' : '')
                        : '';
                ?>
                    <li>
                        <<?php echo $tag; ?> class="mk-toepassingen__kaart<?php echo $klik ? ' mk-toepassingen__kaart--link' : ''; ?><?php echo !empty($toepassing['tekst']) ? ' mk-toepassingen__kaart--met-tekst' : ''; ?>"<?php echo $attrs; ?>>
                            <?php if (!empty($toepassing['icoon'])): ?>
                                <span class="mk-toepassingen__icoon">
                                    <?php echo mk_image($toepassing['icoon'], 'medium', ['alt' => '', 'loading' => 'lazy']); ?>
                                </span>
                            <?php endif; ?>
                            <span class="mk-toepassingen__kaart-inhoud">
                                <?php if (!empty($toepassing['titel'])): ?>
                                    <span class="mk-toepassingen__kaart-titel"><?php echo esc_html($toepassing['titel']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($toepassing['tekst'])): ?>
                                    <span class="mk-toepassingen__kaart-tekst"><?php echo nl2br(esc_html($toepassing['tekst'])); ?></span>
                                <?php endif; ?>
                            </span>
                        </<?php echo $tag; ?>>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($knop['url'])): ?>
            <div class="mk-toepassingen__knop">
                <?php echo mk_link($knop, 'mk-button mk-button--primair', $is_preview); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
