<?php
    $adres    = get_field('adres', 'option');
    $postcode = get_field('postcode', 'option');
    $email    = get_field('email', 'option');
?>

<div class="mk-footer__container">
    <?php if (has_nav_menu('footer_menu')): ?>
        <div class="mk-footer__col">
            <h2 class="mk-footer__title"><?php esc_html_e('Informatie', 'mk'); ?></h2>
            <?php get_template_part('template-parts/footer/nav-footer', null, ['location' => 'footer_menu']); ?>
        </div>
    <?php endif; ?>

    <?php if ($adres || $postcode): ?>
        <div class="mk-footer__col">
            <h2 class="mk-footer__title"><?php esc_html_e('Contactgegevens', 'mk'); ?></h2>
            <address class="mk-footer__address">
                <?php if ($adres) echo esc_html($adres) . '<br>'; ?>
                <?php if ($postcode) echo esc_html($postcode); ?>
            </address>
        </div>
    <?php endif; ?>

    <?php if ($email): ?>
        <div class="mk-footer__col mk-footer__col--email">
            <a class="mk-contact-links__item" href="mailto:<?php echo antispambot(sanitize_email($email)); ?>">
                <?php echo mk_svg('mail'); ?>
                <span><?php echo antispambot(esc_html($email)); ?></span>
            </a>
        </div>
    <?php endif; ?>

    <div class="mk-footer__col mk-footer__col--socials">
        <?php get_template_part('template-parts/contact/socials'); ?>
    </div>

    <?php if (has_nav_menu('voorwaarden_menu')): ?>
        <div class="mk-footer__col mk-footer__col--voorwaarden">
            <h2 class="mk-footer__title"><?php esc_html_e('Voorwaarden', 'mk'); ?></h2>
            <?php get_template_part('template-parts/footer/nav-footer', null, ['location' => 'voorwaarden_menu']); ?>
        </div>
    <?php endif; ?>
</div>
