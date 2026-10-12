<?php
    $titel       = get_field('titel');
    $tekst       = get_field('tekst');
    $formulier   = (int) get_field('formulier');
    $afbeelding  = get_field('afbeelding');
    $achtergrond = get_field('achtergrond');
    $stijl       = get_field('stijl') === 'licht' ? 'licht' : 'groen';
    $licht       = $stijl === 'licht';

    // Groen: band met gecentreerde tekst. Licht: paginakop (h1, links uitgelijnd), zonder band
    $classes = ['mk-contact-cta', 'mk-contact-cta--' . $stijl];
    if (!$licht) $classes[] = 'mk-on-dark';
    if ($licht) $achtergrond = null;
    if ($afbeelding) $classes[] = 'mk-contact-cta--met-foto';
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if ($achtergrond): ?>
        <div class="mk-contact-cta__achtergrond" aria-hidden="true">
            <?php echo mk_image($achtergrond, 'large', ['alt' => '', 'loading' => 'lazy']); ?>
        </div>
    <?php endif; ?>

    <div class="mk-contact-cta__container">
        <?php if ($titel || $tekst): ?>
            <div class="mk-contact-cta__intro">
                <?php if ($titel): ?>
                    <?php $kop = $licht ? 'h1' : 'h2'; ?>
                    <<?php echo $kop; ?> class="mk-contact-cta__titel mk-h2"><?php echo esc_html($titel); ?></<?php echo $kop; ?>>
                <?php endif; ?>
                <?php if ($tekst): ?>
                    <p class="mk-contact-cta__tekst"><?php echo nl2br(esc_html($tekst)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mk-contact-cta__inhoud">
            <div class="mk-contact-cta__formulier">
                <?php if ($formulier && function_exists('gravity_form')): ?>
                    <?php if ($is_preview): ?>
                        <?php // Gravity Forms laadt zijn scripts niet in de editor: hier alleen een melding ?>
                        <p class="mk-contact-cta__melding">
                            <?php
                                $form = class_exists('GFAPI') ? GFAPI::get_form($formulier) : null;
                                /* translators: %s: naam van het formulier */
                                printf(esc_html__('Formulier: %s (zichtbaar op de website)', 'mk'), esc_html($form['title'] ?? '#' . $formulier));
                            ?>
                        </p>
                    <?php else: ?>
                        <?php gravity_form($formulier, false, false, false, null, true); ?>
                    <?php endif; ?>
                <?php elseif ($is_preview): ?>
                    <p class="mk-contact-cta__melding"><?php esc_html_e('Kies een formulier in de blokinstellingen.', 'mk'); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($afbeelding): ?>
                <div class="mk-contact-cta__foto">
                    <?php echo mk_image($afbeelding, 'large', [
                        'loading' => 'lazy',
                        'sizes'   => '(min-width: 900px) 422px, 100vw',
                    ]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
