// Galerij slider (mk/galerij) — slider-werking zit in slider.js, hier alleen Fancybox
document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery || !jQuery.fancybox) return;

    // Fancybox (uit mkbase) — Nederlandse teksten en rustige instellingen
    jQuery.extend(jQuery.fancybox.defaults, {
        lang: 'nl',
        i18n: {
            nl: {
                CLOSE: 'Sluiten',
                NEXT: 'Volgende',
                PREV: 'Vorige',
                ERROR: 'De foto kon niet geladen worden.',
                PLAY_START: 'Diavoorstelling starten',
                PLAY_STOP: 'Diavoorstelling pauzeren',
                FULL_SCREEN: 'Volledig scherm',
                THUMBS: 'Miniaturen',
                DOWNLOAD: 'Downloaden',
                SHARE: 'Delen',
                ZOOM: 'Zoomen'
            }
        },
        hash: false,
        loop: true,
        buttons: ['zoom', 'fullScreen', 'thumbs', 'close'],
        animationEffect: 'zoom-in-out',
        transitionEffect: 'slide'
    });
});
