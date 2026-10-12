<?php
    $email    = get_field('email', 'option');
    $telefoon = get_field('telefoon', 'option');

    if (!$email && !$telefoon) return;
?>

<ul class="mk-contact-links">
    <?php if ($email): ?>
        <li>
            <a class="mk-contact-links__item" href="mailto:<?php echo antispambot(sanitize_email($email)); ?>">
                <?php echo mk_svg('mail'); ?>
                <span><?php echo antispambot(esc_html($email)); ?></span>
            </a>
        </li>
    <?php endif; ?>
    <?php if ($telefoon): ?>
        <li>
            <a class="mk-contact-links__item" href="<?php echo esc_attr(mk_tel_href($telefoon)); ?>">
                <?php echo mk_svg('phone'); ?>
                <span><?php echo esc_html($telefoon); ?></span>
            </a>
        </li>
    <?php endif; ?>
</ul>
