<header class="mk-header">
    <div class="mk-header__container">
        <a class="mk-header__logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <?php echo mk_logo('mk-header__logo-img'); ?>
        </a>

        <div class="mk-header__top">
            <?php get_template_part('template-parts/contact/contact-links'); ?>
        </div>

        <?php get_template_part('template-parts/header/nav-main'); ?>

        <button class="mk-header__toggle" type="button" aria-controls="mk-mobile-menu" aria-expanded="false">
            <span class="screen-reader-text"><?php esc_html_e('Menu openen', 'mk'); ?></span>
            <span class="mk-header__toggle-line"></span>
            <span class="mk-header__toggle-line"></span>
            <span class="mk-header__toggle-line"></span>
        </button>
    </div>

    <?php // Weggetje onder de header: de mini-wals rijdt mee met de scrollvoortgang (assets/js/header-weg.js) ?>
    <div class="mk-header__weg" aria-hidden="true">
        <div class="mk-header__wals">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/tijdlijn-machine.svg'); ?>" alt="" width="34" height="21" draggable="false">
        </div>
    </div>
</header>
