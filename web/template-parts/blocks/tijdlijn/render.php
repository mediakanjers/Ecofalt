<?php
    $titel = get_field('titel');
    $tekst = get_field('tekst');
    $knop  = get_field('knop');
    $items = get_field('items') ?: [];

    if (!$titel && !$items) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Tijdlijn: vul een titel en mijlpalen in.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-tijdlijn'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor  = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
    // Wals inline, zodat het logo los teruggespiegeld kan worden bij omdraaien
    $machine = file_get_contents(get_stylesheet_directory() . '/assets/images/tijdlijn-machine.svg');
    $machine = str_replace('<svg ', '<svg aria-hidden="true" focusable="false" ', $machine);
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if ($titel || $tekst || $knop): ?>
        <div class="mk-tijdlijn__intro">
            <?php if ($titel): ?>
                <h2 class="mk-tijdlijn__titel"><?php echo esc_html($titel); ?></h2>
            <?php endif; ?>

            <?php if ($tekst): ?>
                <p class="mk-tijdlijn__tekst"><?php echo nl2br(esc_html($tekst)); ?></p>
            <?php endif; ?>

            <?php echo mk_link($knop, 'mk-button mk-button--primair', $is_preview); ?>
        </div>
    <?php endif; ?>

    <?php if ($items): ?>
        <div class="mk-tijdlijn__road mk-slider" data-mk-slider data-mk-slider-free>
            <?php // Vers asfalt: de weg die de wals al gereden heeft (breedte via tijdlijn.js) ?>
            <span class="mk-tijdlijn__asfalt" aria-hidden="true"></span>

            <?php // De wals rijdt mee met de tijdlijn (assets/js/tijdlijn.js) ?>
            <div class="mk-tijdlijn__machine" aria-hidden="true">
                <?php // Binnenste laag draait om bij terugrijden (.is-reversed), rook draait mee ?>
                <div class="mk-tijdlijn__machine-inner">
                    <div class="mk-tijdlijn__machine-body"><?php echo $machine; ?></div>
                    <span class="mk-tijdlijn__smoke"><i></i><i></i><i></i></span>
                </div>

                <?php // Aanwijzing tot de eerste interactie (assets/js/tijdlijn.js) ?>
                <span class="mk-tijdlijn__hint">
                    <span class="mk-tijdlijn__hint-pijlen"></span>
                    <span class="mk-tijdlijn__hint-tekst mk-tijdlijn__hint-tekst--muis"><?php esc_html_e('Pak de wals en rijd door de tijd', 'mk'); ?></span>
                    <span class="mk-tijdlijn__hint-tekst mk-tijdlijn__hint-tekst--touch"><?php esc_html_e('Swipe en rijd door de tijd', 'mk'); ?></span>
                </span>
            </div>

            <div class="mk-slider__viewport">
                <ol class="mk-slider__track mk-tijdlijn__track" data-mk-slider-track tabindex="0" aria-label="<?php esc_attr_e('Tijdlijn', 'mk'); ?>">
                    <?php foreach ($items as $item): ?>
                        <?php // Klik (of Enter) op een mijlpaal: de wals rijdt erheen ?>
                        <li class="mk-tijdlijn__item" data-mk-slide tabindex="0" role="button" aria-label="<?php echo esc_attr(trim(($item['titel'] ?? '') . ': ' . ($item['tekst'] ?? ''), ': ')); ?>">
                            <?php if (!empty($item['titel'])): ?>
                                <h3 class="mk-tijdlijn__item-titel"><?php echo esc_html($item['titel']); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($item['tekst'])): ?>
                                <p class="mk-tijdlijn__item-tekst"><?php echo nl2br(esc_html($item['tekst'])); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>

            <?php get_template_part('template-parts/components/slider-nav', null, ['prev' => __('Eerder in de tijdlijn', 'mk'), 'next' => __('Later in de tijdlijn', 'mk')]); ?>
        </div>
    <?php endif; ?>
</section>
