<?php
    $titel = get_field('titel');
    $keuze = get_field('keuze') ?: 'nieuwste';
    $knop  = get_field('knop');

    if ($keuze === 'handmatig') {
        $ids = array_filter((array) get_field('projecten'), function($id) {
            return get_post_status($id) === 'publish';
        });
    } else {
        $aantal = max(1, min(6, (int) (get_field('aantal') ?: 2)));
        $ids = get_posts([
            'post_type'      => 'project',
            'posts_per_page' => $aantal,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
    }

    if (!$ids) {
        if ($is_preview) {
            echo '<p class="mk-block-placeholder">' . esc_html__('Projecten: er zijn nog geen (gepubliceerde) projecten.', 'mk') . '</p>';
        }
        return;
    }

    $classes = ['mk-projecten'];
    if (count($ids) >= 3) $classes[] = 'mk-projecten--drie';
    if (!empty($block['className'])) $classes[] = $block['className'];

    $anchor = !empty($block['anchor']) ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <div class="mk-projecten__container">
        <?php if ($titel): ?>
            <h2 class="mk-projecten__titel mk-h3"><?php echo esc_html($titel); ?></h2>
        <?php endif; ?>

        <div class="mk-projecten__grid">
            <?php foreach ($ids as $id): ?>
                <?php get_template_part('template-parts/components/project-kaart', null, ['post_id' => $id, 'kop' => $titel ? 'h3' : 'h2', 'preview' => $is_preview]); ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($knop['url'])): ?>
            <div class="mk-projecten__knop">
                <?php echo mk_link($knop, 'mk-button mk-button--primair', $is_preview); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
