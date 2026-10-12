<?php
    wp_nav_menu([
        'theme_location' => $args['location'] ?? 'footer_menu',
        'container'      => false,
        'menu_class'     => 'mk-footer__menu',
        'depth'          => 1,
        'fallback_cb'    => false,
    ]);
?>
