<?php
    // Inline SVG uit assets/images/{map}/{naam}.svg — kleurt mee met currentColor via CSS
    function mk_svg($name, $folder = 'icons') {
        $file = get_stylesheet_directory() . '/assets/images/' . $folder . '/' . sanitize_file_name($name) . '.svg';
        if (!file_exists($file)) return '';

        $svg = preg_replace('/<\?xml.*?\?>/s', '', file_get_contents($file));
        return str_replace('<svg ', '<svg aria-hidden="true" focusable="false" ', trim($svg));
    }

    // Telefoonnummer omzetten naar een bruikbare tel:-link ("+3153 428 22 78" -> "tel:+31534282278")
    function mk_tel_href($number) {
        return 'tel:' . preg_replace('/[^0-9+]/', '', (string) $number);
    }

    // Afbeelding uit de mediabibliotheek (ID of ACF-array). SVG's krijgen geen width/height:
    // WP geeft daar 1x1 mee, waardoor de browser de verhouding vierkant maakt.
    function mk_image($image, $size = 'full', $attr = []) {
        $id = is_array($image) ? ($image['ID'] ?? 0) : (int) $image;
        if (!$id) return '';

        if (get_post_mime_type($id) === 'image/svg+xml') {
            $attr = array_merge([
                'alt'      => get_post_meta($id, '_wp_attachment_image_alt', true),
                'decoding' => 'async',
            ], $attr);

            $html = '<img src="' . esc_url(wp_get_attachment_url($id)) . '"';
            foreach ($attr as $name => $value) {
                $html .= ' ' . $name . '="' . esc_attr($value) . '"';
            }
            return $html . '>';
        }

        return wp_get_attachment_image($id, $size, false, $attr);
    }

    // Logo uit Mediakanjers > Website instellingen
    function mk_logo($class = '') {
        $logo = get_field('logo', 'option');

        if (empty($logo)) {
            return '<span class="' . esc_attr($class) . '">' . esc_html(get_bloginfo('name')) . '</span>';
        }

        return mk_image($logo, 'full', [
            'class'    => $class,
            'alt'      => get_bloginfo('name'),
            'loading'  => 'eager',
            'decoding' => 'async',
        ]);
    }

    // ACF link-veld als <a> — in de block-editor als <span>, anders navigeert de editor-iframe weg
    function mk_link($link, $class, $is_preview = false, $fallback_title = '') {
        if (empty($link['url'])) return '';

        $title = !empty($link['title']) ? $link['title'] : $fallback_title;

        if ($is_preview) {
            return '<span class="' . esc_attr($class) . '">' . esc_html($title) . '</span>';
        }

        $target = !empty($link['target']) ? $link['target'] : '_self';
        $rel    = $target === '_blank' ? ' rel="noopener noreferrer"' : '';

        return '<a class="' . esc_attr($class) . '" href="' . esc_url($link['url']) . '" target="' . esc_attr($target) . '"' . $rel . '>' . esc_html($title) . '</a>';
    }

    // Vimeo-URL ontleden: vimeo.com/123, vimeo.com/123/abc (niet-openbaar), player.vimeo.com/video/123?h=abc
    function mk_vimeo($url) {
        if (!$url || !preg_match('~vimeo\.com/(?:video/|channels/[^/]+/|groups/[^/]+/videos/)?(\d+)(?:/([a-zA-Z0-9]+))?~', $url, $m)) {
            return null;
        }

        $hash = $m[2] ?? '';
        if (!$hash) {
            parse_str((string) wp_parse_url($url, PHP_URL_QUERY), $query);
            $hash = $query['h'] ?? '';
        }

        return ['id' => $m[1], 'hash' => $hash];
    }

    // Embed-URL voor een ontlede Vimeo-video, met extra player-parameters
    function mk_vimeo_embed($video, $params = []) {
        $base = $video['hash'] ? ['h' => $video['hash']] : [];
        return add_query_arg(array_merge($base, ['dnt' => 1], $params), 'https://player.vimeo.com/video/' . $video['id']);
    }

    // ACF: keuzelijst "Formulier" (o.a. Contact-blok) vullen met de formulieren uit Gravity Forms
    add_filter('acf/load_field/key=field_mk_contact_cta_formulier', function($field) {
        $field['choices'] = [];
        if (class_exists('GFAPI')) {
            foreach (GFAPI::get_forms() as $form) {
                $field['choices'][$form['id']] = $form['title'];
            }
        }
        return $field;
    });
?>
