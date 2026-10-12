<?php
    $titel  = get_field('titel');
    $url    = get_field('vimeo');
    $video  = mk_vimeo($url);
    $poster = get_field('poster');

    if (!$video) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Video: vul een Vimeo-link in.', 'mk') . '</p>';
        }
        return;
    }

    // Zonder eigen thumbnail: die van Vimeo
    $vimeo_thumb = $poster ? '' : mk_vimeo_thumbnail($url);

    $classes = ['mk-video'];
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
    $label  = $titel ?: __('Video afspelen', 'mk');
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-video__container">
        <?php if ($titel): ?>
            <h2 class="mk-video__titel"><?php echo esc_html($titel); ?></h2>
        <?php endif; ?>

        <?php // Klik opent de video in de popup (assets/js/video-modal.js) ?>
        <<?php echo $is_preview ? 'div' : 'button type="button"'; ?> class="mk-video__speler"<?php echo $is_preview ? '' : ' data-mk-video="' . esc_url(mk_vimeo_embed($video, ['autoplay' => 1])) . '" aria-label="' . esc_attr(sprintf(__('%s (video afspelen)', 'mk'), $label)) . '"'; ?>>
            <?php if ($poster): ?>
                <?php echo mk_image($poster, 'full', ['class' => 'mk-video__poster', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width: 1440px) 1100px, 100vw']); ?>
            <?php elseif ($vimeo_thumb): ?>
                <img class="mk-video__poster" src="<?php echo esc_url($vimeo_thumb); ?>" alt="" loading="lazy">
            <?php endif; ?>
            <span class="mk-video__play" aria-hidden="true"></span>
        </<?php echo $is_preview ? 'div' : 'button'; ?>>
    </div>
</section>
