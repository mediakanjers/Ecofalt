<?php
    // =============================================================================
    // Projecten — posttype zonder Gutenberg (titel + uitgelichte afbeelding, rest via ACF)
    // Overzicht: /projecten/ (archive-project.php), filter per toepassing: /projecten/toepassing/{slug}/
    // =============================================================================

    add_action('init', function() {
        // Toepassing (Fietspaden, Wegen, ...) — om op het overzicht te filteren.
        // Vóór het posttype registreren: anders vangt /projecten/{slug}/ de URL /projecten/toepassing/... af
        register_taxonomy('toepassing', ['project'], [
            'labels' => [
                'name'          => __('Toepassingen', 'mk'),
                'singular_name' => __('Toepassing', 'mk'),
                'menu_name'     => __('Toepassingen', 'mk'),
                'all_items'     => __('Alle toepassingen', 'mk'),
                'edit_item'     => __('Toepassing bewerken', 'mk'),
                'add_new_item'  => __('Nieuwe toepassing', 'mk'),
            ],
            'public'            => true,
            'hierarchical'      => true, // vinkjes in de zijbalk van een project
            'show_admin_column' => true,
            'show_in_rest'      => false,
            'rewrite'           => ['slug' => 'projecten/toepassing', 'with_front' => false],
        ]);

        register_post_type('project', [
            'labels' => [
                'name'               => __('Projecten', 'mk'),
                'singular_name'      => __('Project', 'mk'),
                'menu_name'          => __('Projecten', 'mk'),
                'add_new'            => __('Nieuw project', 'mk'),
                'add_new_item'       => __('Nieuw project toevoegen', 'mk'),
                'edit_item'          => __('Project bewerken', 'mk'),
                'new_item'           => __('Nieuw project', 'mk'),
                'view_item'          => __('Project bekijken', 'mk'),
                'search_items'       => __('Projecten zoeken', 'mk'),
                'not_found'          => __('Geen projecten gevonden', 'mk'),
                'not_found_in_trash' => __('Geen projecten in de prullenbak', 'mk'),
                'all_items'          => __('Alle projecten', 'mk'),
                'featured_image'     => __('Projectfoto', 'mk'),
                'set_featured_image' => __('Projectfoto instellen', 'mk'),
            ],
            'public'       => true,
            'has_archive'  => 'projecten',
            'rewrite'      => ['slug' => 'projecten', 'with_front' => false],
            'supports'     => ['title', 'thumbnail'],
            'show_in_rest' => false, // geen block editor
            'menu_icon'    => 'dashicons-location-alt',
            'menu_position'=> 21,
        ]);

        // Permalinks eenmalig verversen na (wijziging van) deze registratie
        if (get_option('mk_projecten_rewrite') !== '2') {
            flush_rewrite_rules(false);
            update_option('mk_projecten_rewrite', '2');
        }
    });

    // Overzicht: 12 projecten per pagina, nieuwste eerst
    add_action('pre_get_posts', function($query) {
        if (is_admin() || !$query->is_main_query()) return;
        if ($query->is_post_type_archive('project') || $query->is_tax('toepassing')) {
            $query->set('posts_per_page', 12);
        }
    });

    // Instellingen voor het overzicht en de projectpagina (onder het menu Projecten)
    add_action('acf/init', function() {
        if (!function_exists('acf_add_options_sub_page')) return;
        acf_add_options_sub_page([
            'page_title'  => __('Projecten — instellingen', 'mk'),
            'menu_title'  => __('Instellingen', 'mk'),
            'menu_slug'   => 'mk-projecten-instellingen',
            'parent_slug' => 'edit.php?post_type=project',
        ]);
    });
?>
