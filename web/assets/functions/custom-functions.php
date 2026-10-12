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

    // Logo uit Mediakanjers > Website instellingen
    function mk_logo($class = '') {
        $logo = get_field('logo', 'option');
        $id   = is_array($logo) ? ($logo['ID'] ?? 0) : (int) $logo;

        if (!$id) {
            return '<span class="' . esc_attr($class) . '">' . esc_html(get_bloginfo('name')) . '</span>';
        }

        // SVG zonder width/height: WP geeft daar 1x1 mee, wat de verhouding vierkant maakt
        if (get_post_mime_type($id) === 'image/svg+xml') {
            return '<img class="' . esc_attr($class) . '" src="' . esc_url(wp_get_attachment_url($id)) . '" alt="' . esc_attr(get_bloginfo('name')) . '" decoding="async">';
        }

        return wp_get_attachment_image($id, 'full', false, [
            'class'    => $class,
            'alt'      => get_bloginfo('name'),
            'loading'  => 'eager',
            'decoding' => 'async',
        ]);
    }
?>
