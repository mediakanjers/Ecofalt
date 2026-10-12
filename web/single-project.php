<?php get_header(); ?>

<?php while (have_posts()): the_post();
    $samenvatting = get_field('samenvatting');
    $inhoud       = get_field('inhoud');
    $fotos        = get_field('galerij') ?: [];
    $toepassingen = get_the_terms(get_the_ID(), 'toepassing') ?: [];

    // Projectgegevens: alleen ingevulde regels tonen
    $gegevens = array_filter([
        __('Opdrachtgever', 'mk')        => get_field('opdrachtgever'),
        __('Locatie', 'mk')              => get_field('locatie'),
        __('Toepassing', 'mk')           => implode(', ', wp_list_pluck($toepassingen, 'name')),
        __('Jaar van uitvoering', 'mk')  => get_field('jaar'),
        __('Mengsel', 'mk')              => get_field('mengsel'),
        __('Oppervlakte / lengte', 'mk') => get_field('oppervlakte'),
    ]);

    // Contactblok (Projecten > Instellingen), met nette standaardwaarden
    $cta_titel = get_field('projecten_cta_titel', 'option') ?: __('Ook Ecofalt toepassen?', 'mk');
    $cta_tekst = get_field('projecten_cta_tekst', 'option') ?: __('We denken graag mee over de juiste opbouw en het passende mengsel.', 'mk');
    $cta_knop  = get_field('projecten_cta_knop', 'option');
    if (empty($cta_knop['url']) && ($contact = get_page_by_path('contact'))) {
        $cta_knop = ['url' => get_permalink($contact), 'title' => __('Neem contact op', 'mk'), 'target' => ''];
    }

    // Andere projecten: de 2 nieuwste, behalve dit project
    $andere = get_posts([
        'post_type'      => 'project',
        'posts_per_page' => 2,
        'post__not_in'   => [get_the_ID()],
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
?>
    <div id="main-content" class="vervolgpagina mk-project">
        <header class="mk-pagina-kop">
            <?php if (function_exists('mk_breadcrumbs')) mk_breadcrumbs(); ?>

            <?php if ($toepassingen): ?>
                <ul class="mk-project__labels">
                    <?php foreach ($toepassingen as $term): ?>
                        <li><a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h1 class="mk-pagina-kop__titel"><?php the_title(); ?></h1>

            <?php if ($samenvatting): ?>
                <p class="mk-pagina-kop__tekst mk-intro"><?php echo esc_html($samenvatting); ?></p>
            <?php endif; ?>
        </header>

        <?php if (has_post_thumbnail()): ?>
            <figure class="mk-project__foto">
                <?php the_post_thumbnail('full', [
                    'class'         => 'mk-project__foto-img',
                    'loading'       => 'eager',
                    'fetchpriority' => 'high',
                    'sizes'         => '(min-width: 1440px) 1326px, 100vw',
                ]); ?>
            </figure>
        <?php endif; ?>

        <div class="mk-project__body<?php echo $inhoud ? '' : ' mk-project__body--zonder-tekst'; ?>">
            <?php if ($inhoud): ?>
                <div class="mk-project__inhoud mk-text">
                    <?php echo wp_kses_post($inhoud); ?>
                </div>
            <?php endif; ?>

            <aside class="mk-project__zijkant">
                <?php if ($gegevens): ?>
                    <div class="mk-project__gegevens">
                        <h2 class="mk-project__zijkant-titel"><?php esc_html_e('Projectgegevens', 'mk'); ?></h2>
                        <dl>
                            <?php foreach ($gegevens as $label => $waarde): ?>
                                <div class="mk-project__gegeven">
                                    <dt><?php echo esc_html($label); ?></dt>
                                    <dd><?php echo esc_html($waarde); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    </div>
                <?php endif; ?>

                <div class="mk-project__cta mk-on-dark">
                    <h2 class="mk-project__zijkant-titel"><?php echo esc_html($cta_titel); ?></h2>
                    <p><?php echo esc_html($cta_tekst); ?></p>
                    <?php echo mk_link($cta_knop, 'mk-button mk-button--primair'); ?>
                </div>
            </aside>
        </div>

        <?php if ($fotos): ?>
            <div class="mk-project__fotos">
                <h2 class="mk-project__sectie-titel mk-h3"><?php esc_html_e('Foto\'s', 'mk'); ?></h2>
                <?php get_template_part('template-parts/components/foto-slider', null, [
                    'fotos' => $fotos,
                    'groep' => 'project-' . get_the_ID(),
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if ($andere): ?>
            <section class="mk-projecten">
                <div class="mk-projecten__container">
                    <h2 class="mk-projecten__titel mk-h3"><?php esc_html_e('Andere projecten', 'mk'); ?></h2>
                    <div class="mk-projecten__grid">
                        <?php foreach ($andere as $id): ?>
                            <?php get_template_part('template-parts/components/project-kaart', null, ['post_id' => $id, 'kop' => 'h3']); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="mk-projecten__knop">
                        <a class="mk-link" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>"><?php esc_html_e('Bekijk alle projecten', 'mk'); ?></a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
