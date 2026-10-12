<?php
    // Extra menulocatie naast Hoofdmenu en Footermenu (die komen uit mkbase)
    add_action('after_setup_theme', function() {
        register_nav_menus([
            'voorwaarden_menu' => __('Footermenu voorwaarden', 'mk'),
        ]);
    });
?>
