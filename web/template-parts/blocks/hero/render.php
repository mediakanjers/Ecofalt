<?php
    $titel        = get_field('titel');
    $tekst        = get_field('tekst');
    $knop_1       = get_field('knop_1');
    $knop_2       = get_field('knop_2');
    $video_bg     = mk_vimeo(get_field('vimeo_achtergrond'));
    $vimeo        = mk_vimeo(get_field('vimeo'));
    $film_tekst   = get_field('film_knoptekst') ?: __('Bekijk de bedrijfsfilm', 'mk');
    $afbeelding   = get_field('afbeelding');
    $overlay      = get_field('overlay');
    $kaarten      = get_field('kaarten') ?: [];

    $overlay = is_numeric($overlay) ? max(0, min(100, (int) $overlay)) : 70;
    $titel_regels = $titel ? preg_split('/\R/', trim($titel)) : [];

    $classes = ['mk-hero'];
    if ($kaarten) $classes[] = 'mk-hero--met-kaarten';
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="--mk-hero-overlay: <?php echo esc_attr($overlay / 100); ?>;">
    <div class="mk-hero__stage">
        <div class="mk-hero__media" aria-hidden="true">
            <?php if ($afbeelding): ?>
                <?php echo mk_image($afbeelding, 'full', ['class' => 'mk-hero__image', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
            <?php endif; ?>

            <?php // Achtergrondvideo (headerversie) niet in de editor laden — daar volstaat afbeelding/kleur ?>
            <?php if ($video_bg && !$is_preview): ?>
                <iframe class="mk-hero__video"
                    src="<?php echo esc_url(mk_vimeo_embed($video_bg, ['background' => 1, 'autoplay' => 1, 'loop' => 1, 'muted' => 1])); ?>"
                    allow="autoplay; fullscreen; picture-in-picture"
                    tabindex="-1"
                    title=""></iframe>
            <?php endif; ?>

            <div class="mk-hero__overlay"></div>
        </div>

        <div class="mk-hero__container">
            <div class="mk-hero__content mk-on-dark">
                <?php if ($titel): ?>
                    <h1 class="mk-hero__title">
                        <?php // Haak alleen om de eerste regel, de rest volgt eronder ?>
                        <span class="mk-bracket"><?php echo esc_html(array_shift($titel_regels)); ?></span>
                        <?php foreach ($titel_regels as $regel): ?>
                            <br><?php echo esc_html($regel); ?>
                        <?php endforeach; ?>
                    </h1>
                <?php endif; ?>

                <?php if ($tekst): ?>
                    <p class="mk-hero__text mk-intro"><?php echo nl2br(esc_html($tekst)); ?></p>
                <?php endif; ?>

                <?php if ($knop_1 || $knop_2 || $vimeo): ?>
                    <div class="mk-hero__buttons">
                        <?php echo mk_link($knop_1, 'mk-button mk-button--primair', $is_preview); ?>
                        <?php echo mk_link($knop_2, 'mk-button mk-button--secundair', $is_preview); ?>

                        <?php if ($vimeo): ?>
                            <?php if ($is_preview): ?>
                                <span class="mk-button mk-button--ghost"><?php echo esc_html($film_tekst); ?></span>
                            <?php else: ?>
                                <button type="button" class="mk-button mk-button--ghost" data-mk-video="<?php echo esc_url(mk_vimeo_embed($vimeo, ['autoplay' => 1])); ?>">
                                    <?php echo esc_html($film_tekst); ?>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($kaarten): ?>
        <?php // Kaartjes: template-parts/components/icoon-kaart.php ?>
        <div class="mk-hero__cards mk-icoon-kaarten">
            <?php foreach ($kaarten as $kaart): ?>
                <?php get_template_part('template-parts/components/icoon-kaart', null, ['kaart' => $kaart, 'kop' => 'h2', 'preview' => $is_preview]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
