<?php
    $foto     = get_field('foto');
    $titel    = get_field('titel');
    $tekst    = get_field('tekst');
    $naam     = get_field('naam');
    $email    = get_field('email');
    $telefoon = get_field('telefoon');

    if (!$titel && !$tekst && !$naam) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Contactpersoon: vul een titel, tekst en contactgegevens in.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-contactpersoon'];
    if ($foto) $classes[] = 'mk-contactpersoon--met-foto';
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
    $tag    = $is_preview ? 'span' : 'a'; // in de editor geen echte links
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-contactpersoon__kaart">
        <?php if ($foto): ?>
            <div class="mk-contactpersoon__foto">
                <?php echo mk_image($foto, 'large', ['loading' => 'lazy', 'sizes' => '(min-width: 600px) 312px, 100vw']); ?>
            </div>
        <?php endif; ?>

        <div class="mk-contactpersoon__inhoud">
            <?php if ($titel): ?>
                <h2 class="mk-contactpersoon__titel mk-h3"><?php echo esc_html($titel); ?></h2>
            <?php endif; ?>

            <?php if ($tekst): ?>
                <p class="mk-contactpersoon__tekst"><?php echo nl2br(esc_html($tekst)); ?></p>
            <?php endif; ?>

            <?php if ($naam || $email || $telefoon): ?>
                <p class="mk-contactpersoon__gegevens">
                    <?php if ($naam): ?>
                        <strong class="mk-contactpersoon__naam"><?php echo esc_html($naam); ?></strong>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <<?php echo $tag; ?> class="mk-contactpersoon__link"<?php echo $is_preview ? '' : ' href="mailto:' . antispambot(sanitize_email($email)) . '"'; ?>><?php echo antispambot(esc_html($email)); ?></<?php echo $tag; ?>>
                    <?php endif; ?>
                    <?php if ($telefoon): ?>
                        <<?php echo $tag; ?> class="mk-contactpersoon__link"<?php echo $is_preview ? '' : ' href="' . esc_attr(mk_tel_href($telefoon)) . '"'; ?>><?php echo esc_html($telefoon); ?></<?php echo $tag; ?>>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>
