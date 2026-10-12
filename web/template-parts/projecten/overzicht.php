<?php
    // Projectenoverzicht — gebruikt door archive-project.php en taxonomy-toepassing.php
    $titel = get_field('projecten_archief_titel', 'option') ?: __('Projecten', 'mk');
    $tekst = get_field('projecten_archief_tekst', 'option');

    $toepassingen = get_terms(['taxonomy' => 'toepassing', 'hide_empty' => true]);
    $actief       = is_tax('toepassing') ? get_queried_object_id() : 0;
?>
<div id="main-content" class="vervolgpagina mk-projecten-overzicht">
    <header class="mk-pagina-kop">
        <?php // Eigen kruimelpad: mk_breadcrumbs() (mkbase) kent geen archiefpagina's ?>
        <nav class="mk-breadcrumbs" aria-label="<?php esc_attr_e('Kruimelpad', 'mk'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'mk'); ?></a>
            <span class="mk-breadcrumbs-sep">›</span>
            <?php if ($actief): ?>
                <a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"><?php echo esc_html($titel); ?></a>
                <span class="mk-breadcrumbs-sep">›</span>
                <strong><?php echo esc_html(single_term_title('', false)); ?></strong>
            <?php else: ?>
                <strong><?php echo esc_html($titel); ?></strong>
            <?php endif; ?>
        </nav>
        <h1 class="mk-pagina-kop__titel"><?php echo esc_html($titel); ?></h1>
        <?php if ($tekst): ?>
            <p class="mk-pagina-kop__tekst mk-intro"><?php echo nl2br(esc_html($tekst)); ?></p>
        <?php endif; ?>
    </header>

    <div class="mk-projecten mk-projecten--drie mk-projecten--overzicht">
        <div class="mk-projecten__container">
            <?php if (!is_wp_error($toepassingen) && count($toepassingen) > 0): ?>
                <nav class="mk-filter" aria-label="<?php esc_attr_e('Filter op toepassing', 'mk'); ?>">
                    <a class="mk-filter__item<?php echo !$actief ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"<?php echo !$actief ? ' aria-current="page"' : ''; ?>>
                        <?php esc_html_e('Alle projecten', 'mk'); ?>
                    </a>
                    <?php foreach ($toepassingen as $term): ?>
                        <a class="mk-filter__item<?php echo $actief === $term->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>"<?php echo $actief === $term->term_id ? ' aria-current="page"' : ''; ?>>
                            <?php echo esc_html($term->name); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php if (have_posts()): ?>
                <div class="mk-projecten__grid">
                    <?php while (have_posts()): the_post(); ?>
                        <?php get_template_part('template-parts/components/project-kaart', null, ['post_id' => get_the_ID(), 'kop' => 'h2']); ?>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination([
                    'mid_size'           => 1,
                    'prev_text'          => '<span class="screen-reader-text">' . __('Vorige pagina', 'mk') . '</span>',
                    'next_text'          => '<span class="screen-reader-text">' . __('Volgende pagina', 'mk') . '</span>',
                    'screen_reader_text' => __('Meer projecten', 'mk'),
                    'class'              => 'mk-paginering',
                ]); ?>
            <?php else: ?>
                <p class="mk-projecten__leeg"><?php esc_html_e('Er zijn (nog) geen projecten gevonden.', 'mk'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
