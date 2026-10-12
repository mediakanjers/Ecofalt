<div class="mk-mobile-menu" id="mk-mobile-menu" aria-hidden="true" inert>
    <div class="mk-mobile-menu__backdrop" data-mk-menu-close></div>

    <div class="mk-mobile-menu__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu', 'mk'); ?>">
        <div class="mk-mobile-menu__top">
            <a class="mk-mobile-menu__logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                <?php echo mk_logo('mk-mobile-menu__logo-img'); ?>
            </a>
            <button class="mk-mobile-menu__close" type="button" data-mk-menu-close>
                <span class="screen-reader-text"><?php esc_html_e('Menu sluiten', 'mk'); ?></span>
            </button>
        </div>

        <nav class="mk-mobile-menu__nav" aria-label="<?php esc_attr_e('Hoofdmenu', 'mk'); ?>">
            <?php
                wp_nav_menu([
                    'theme_location' => 'main_menu',
                    'container'      => false,
                    'menu_class'     => 'mk-mobile-menu__menu',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ]);
            ?>
        </nav>

        <div class="mk-mobile-menu__bottom">
            <?php get_template_part('template-parts/contact/contact-links'); ?>
        </div>
    </div>
</div>
