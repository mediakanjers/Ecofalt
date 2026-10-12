<?php
    $titel      = get_field('titel');
    $tekst      = get_field('tekst');
    $afbeelding = get_field('afbeelding');
    $tekst_2    = get_field('tekst_2');

    $quote          = get_field('quote_tekst');
    $quote_foto     = get_field('quote_foto');
    $handtekening   = get_field('quote_handtekening');
    $naam           = get_field('quote_naam');
    $functie        = get_field('quote_functie');

    if (!$titel && !$tekst && !$quote) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Verhaal: vul een titel en tekst in.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-verhaal'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-verhaal__kaart">
        <?php if ($titel): ?>
            <h2 class="mk-verhaal__titel mk-h3"><?php echo esc_html($titel); ?></h2>
        <?php endif; ?>

        <?php if ($tekst): ?>
            <div class="mk-verhaal__tekst mk-text"><?php echo wp_kses_post($tekst); ?></div>
        <?php endif; ?>

        <?php if ($afbeelding): ?>
            <figure class="mk-verhaal__afbeelding">
                <?php echo mk_image($afbeelding, 'large', ['loading' => 'lazy', 'sizes' => '(min-width: 900px) 766px, 100vw']); ?>
            </figure>
        <?php endif; ?>

        <?php if ($tekst_2): ?>
            <div class="mk-verhaal__tekst mk-text"><?php echo wp_kses_post($tekst_2); ?></div>
        <?php endif; ?>

        <?php if ($quote): ?>
            <figure class="mk-verhaal__quote<?php echo $quote_foto ? ' mk-verhaal__quote--met-foto' : ''; ?>">
                <?php if ($quote_foto): ?>
                    <div class="mk-verhaal__quote-foto">
                        <?php echo mk_image($quote_foto, 'large', ['loading' => 'lazy', 'sizes' => '(min-width: 900px) 383px, 100vw']); ?>
                    </div>
                <?php endif; ?>

                <div class="mk-verhaal__quote-inhoud">
                    <blockquote class="mk-verhaal__quote-tekst">
                        <?php // *woorden tussen sterretjes* worden groen ?>
                        <p><?php echo preg_replace('/\*(.+?)\*/s', '<span class="mk-verhaal__accent">$1</span>', esc_html($quote)); ?></p>
                    </blockquote>

                    <?php if ($handtekening || $naam || $functie): ?>
                        <figcaption class="mk-verhaal__afzender">
                            <?php if ($handtekening): ?>
                                <?php echo mk_image($handtekening, 'medium', ['class' => 'mk-verhaal__handtekening', 'alt' => $naam ?: '', 'loading' => 'lazy']); ?>
                            <?php endif; ?>
                            <?php if ($naam && !$handtekening): ?>
                                <span class="mk-verhaal__naam"><?php echo esc_html($naam); ?></span>
                            <?php endif; ?>
                            <?php if ($functie): ?>
                                <span class="mk-verhaal__functie"><?php echo esc_html($functie); ?></span>
                            <?php endif; ?>
                        </figcaption>
                    <?php endif; ?>
                </div>
            </figure>
        <?php endif; ?>
    </div>
</section>
