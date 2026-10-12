<?php
    // Projectkaartje — gebruikt in het Projecten-blok, het overzicht en "Andere projecten".
    // $args: 'post_id', 'kop' (h2/h3, standaard h3), 'preview' (in de block-editor: geen link)
    $post_id = $args['post_id'] ?? get_the_ID();
    $kop     = in_array($args['kop'] ?? 'h3', ['h2', 'h3', 'h4'], true) ? $args['kop'] : 'h3';
    $preview = !empty($args['preview']);

    $samenvatting = get_field('samenvatting', $post_id);
    $titel        = get_the_title($post_id);
?>
<article class="mk-project-kaart">
    <div class="mk-project-kaart__media">
        <?php if (has_post_thumbnail($post_id)): ?>
            <?php echo get_the_post_thumbnail($post_id, 'large', [
                'class'   => 'mk-project-kaart__afbeelding',
                'loading' => 'lazy',
                'sizes'   => '(min-width: 1200px) 640px, (min-width: 900px) 50vw, 100vw',
                'alt'     => '',
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="mk-project-kaart__body">
        <<?php echo $kop; ?> class="mk-project-kaart__titel">
            <?php if ($preview): ?>
                <?php echo esc_html($titel); ?>
            <?php else: ?>
                <?php // De link beslaat het hele kaartje (::after), "Lees meer" is alleen visueel ?>
                <a class="mk-project-kaart__link" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($titel); ?></a>
            <?php endif; ?>
        </<?php echo $kop; ?>>

        <?php if ($samenvatting): ?>
            <p class="mk-project-kaart__tekst"><?php echo esc_html($samenvatting); ?></p>
        <?php endif; ?>

        <span class="mk-project-kaart__meer mk-link" aria-hidden="true"><?php esc_html_e('Lees meer', 'mk'); ?></span>
    </div>
</article>
