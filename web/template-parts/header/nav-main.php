<nav class="mk-nav-main" aria-label="<?php esc_attr_e('Hoofdmenu', 'mk'); ?>">
    <?php
        wp_nav_menu([
            'theme_location' => 'main_menu',
            'container'      => false,
            'menu_class'     => 'mk-nav-main__menu',
            'depth'          => 2,
            'fallback_cb'    => false,
        ]);
    ?>
</nav>
