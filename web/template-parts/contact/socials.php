<?php if (have_rows('socials', 'option')): ?>
    <ul class="mk-socials">
        <?php while (have_rows('socials', 'option')): the_row();
            $platform = get_sub_field('platform');
            $url      = get_sub_field('url');
            $icon     = mk_svg($platform, 'socials');
            if (!$icon || !$url) continue;
        ?>
            <li>
                <a class="mk-socials__link mk-socials__link--<?php echo esc_attr($platform); ?>" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo $icon; ?>
                    <span class="screen-reader-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>
