<?php
    // Slider-navigatie: lijn met groen blokje (vorige/volgende). Zie components/_slider.scss en js/slider.js
    // $args: 'prev' en 'next' (schermlezer-teksten)
    $prev = $args['prev'] ?? __('Vorige', 'mk');
    $next = $args['next'] ?? __('Volgende', 'mk');
?>
<div class="mk-slider__nav" data-mk-slider-nav>
    <div class="mk-slider__thumb" data-mk-slider-thumb>
        <button type="button" class="mk-slider__button mk-slider__button--prev" data-mk-slider-prev>
            <span class="screen-reader-text"><?php echo esc_html($prev); ?></span>
        </button>
        <button type="button" class="mk-slider__button mk-slider__button--next" data-mk-slider-next>
            <span class="screen-reader-text"><?php echo esc_html($next); ?></span>
        </button>
    </div>
</div>
