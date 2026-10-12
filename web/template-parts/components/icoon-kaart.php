<?php
    // Icoon-kaartje (icoon, titel, tekst, optioneel "Lees meer") — gebruikt in de Hero en het Kenmerken-blok.
    // $args: 'kaart' (ACF-rij met icoon/titel/tekst/link), 'kop' (h2/h3), 'preview' (block-editor)
    $kaart   = $args['kaart'] ?? [];
    $kop     = in_array($args['kop'] ?? 'h2', ['h2', 'h3'], true) ? $args['kop'] : 'h2';
    $preview = !empty($args['preview']);
?>
<div class="mk-icoon-kaart">
    <?php if (!empty($kaart['icoon'])): ?>
        <div class="mk-icoon-kaart__icoon">
            <?php echo mk_image($kaart['icoon'], 'medium', ['alt' => '', 'loading' => 'lazy']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($kaart['titel'])): ?>
        <<?php echo $kop; ?> class="mk-icoon-kaart__titel mk-h4"><?php echo esc_html($kaart['titel']); ?></<?php echo $kop; ?>>
    <?php endif; ?>

    <?php if (!empty($kaart['tekst'])): ?>
        <p class="mk-icoon-kaart__tekst"><?php echo nl2br(esc_html($kaart['tekst'])); ?></p>
    <?php endif; ?>

    <?php if (!empty($kaart['link']['url'])): ?>
        <div class="mk-icoon-kaart__footer">
            <?php echo mk_link($kaart['link'], 'mk-link', $preview, __('Lees meer', 'mk')); ?>
        </div>
    <?php endif; ?>
</div>
