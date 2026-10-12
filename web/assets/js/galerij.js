// Galerij slider (mk/galerij)
// - vorige/volgende-knoppen in het groene blokje, dat met de scrollpositie meeschuift
// - blokje slepen langs de lijn (muis + touch), klikken op de lijn springt erheen
// - foto's met de muis slepen (grab); touch swipet native
// - klik op een foto opent Fancybox (niet na het slepen)
document.addEventListener('DOMContentLoaded', function () {
    var DRAG_THRESHOLD = 5;

    // Fancybox (uit mkbase) — Nederlandse teksten en rustige instellingen
    if (window.jQuery && jQuery.fancybox) {
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
    }

    document.querySelectorAll('.mk-galerij').forEach(function (galerij) {
        var track = galerij.querySelector('.mk-galerij__track');
        var nav = galerij.querySelector('.mk-galerij__nav');
        var thumb = galerij.querySelector('.mk-galerij__thumb');
        var prev = galerij.querySelector('[data-mk-galerij-prev]');
        var next = galerij.querySelector('[data-mk-galerij-next]');
        if (!track || !nav || !thumb) return;

        var slides = track.querySelectorAll('.mk-galerij__slide');

        function maxScroll() {
            return track.scrollWidth - track.clientWidth;
        }

        // Eén stap = breedte van een foto + tussenruimte
        function step() {
            var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            return slides.length ? slides[0].offsetWidth + gap : track.clientWidth;
        }

        // Lijn = nav zonder de gutter-padding; het blokje reist over (lijn - blokje)
        function travel() {
            var style = getComputedStyle(nav);
            var line = nav.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
            return Math.max(1, line - thumb.offsetWidth);
        }

        function update() {
            var max = maxScroll();
            nav.hidden = max <= 1;
            if (nav.hidden) return;

            var progress = Math.min(1, Math.max(0, track.scrollLeft / max));
            thumb.style.transform = 'translateX(' + (progress * travel()) + 'px)';
            prev.disabled = track.scrollLeft <= 1;
            next.disabled = track.scrollLeft >= max - 1;
        }

        // Na het slepen netjes op een foto uitkomen, met een duwtje in de sleeprichting
        function snapToSlide(direction) {
            if (!slides.length) return;
            var base = slides[0].offsetLeft;
            var projected = track.scrollLeft + direction * step() * 0.3;
            var target = 0;
            var best = Infinity;

            slides.forEach(function (slide) {
                var left = slide.offsetLeft - base;
                var distance = Math.abs(left - projected);
                if (distance < best) {
                    best = distance;
                    target = left;
                }
            });

            track.scrollTo({ left: Math.min(target, maxScroll()), behavior: 'smooth' });
        }

        // Klik onderdrukken direct na het slepen (anders opent Fancybox of klikt een knop)
        function suppressNextClick(el) {
            function block(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            // De click volgt direct op pointerup; daarna weer opruimen, ook als er geen click komt
            el.addEventListener('click', block, { capture: true, once: true });
            setTimeout(function () {
                el.removeEventListener('click', block, { capture: true });
            }, 0);
        }

        // Generieke sleep-afhandeling: pas na DRAG_THRESHOLD pixels wordt het een sleepactie
        function draggable(el, options) {
            var state = null;

            el.addEventListener('pointerdown', function (e) {
                if (e.button !== 0 || (options.mouseOnly && e.pointerType !== 'mouse')) return;
                state = { id: e.pointerId, x: e.clientX, start: options.start(), moved: false };
            });

            el.addEventListener('pointermove', function (e) {
                if (!state || e.pointerId !== state.id) return;
                var dx = e.clientX - state.x;

                if (!state.moved) {
                    if (Math.abs(dx) < DRAG_THRESHOLD) return;
                    state.moved = true;
                    el.setPointerCapture(e.pointerId);
                    galerij.classList.add('is-dragging');
                }

                e.preventDefault();
                options.move(dx, state.start);
            });

            function end(e) {
                if (!state || e.pointerId !== state.id) return;
                if (state.moved) {
                    galerij.classList.remove('is-dragging');
                    suppressNextClick(el);
                    options.end(e.clientX - state.x);
                }
                state = null;
            }

            el.addEventListener('pointerup', end);
            el.addEventListener('pointercancel', end);
        }

        // Foto's slepen met de muis (grab)
        draggable(track, {
            mouseOnly: true,
            start: function () { return track.scrollLeft; },
            move: function (dx, startLeft) { track.scrollLeft = startLeft - dx; },
            end: function (dx) { snapToSlide(dx < 0 ? 1 : -1); }
        });

        // Groene blokje slepen langs de lijn
        draggable(thumb, {
            mouseOnly: false,
            start: function () { return track.scrollLeft; },
            move: function (dx, startLeft) {
                track.scrollLeft = startLeft + (dx / travel()) * maxScroll();
            },
            end: function (dx) { snapToSlide(dx > 0 ? 1 : -1); }
        });

        // Native slepen van afbeeldingen/links uitschakelen
        track.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });

        // Klik op de lijn: blokje (en slider) springt naar die plek
        nav.addEventListener('click', function (e) {
            if (thumb.contains(e.target)) return;
            var style = getComputedStyle(nav);
            var lineLeft = nav.getBoundingClientRect().left + parseFloat(style.paddingLeft);
            var progress = (e.clientX - lineLeft - thumb.offsetWidth / 2) / travel();
            progress = Math.min(1, Math.max(0, progress));
            track.scrollTo({ left: progress * maxScroll(), behavior: 'smooth' });
        });

        prev.addEventListener('click', function () {
            track.scrollBy({ left: -step(), behavior: 'smooth' });
        });

        next.addEventListener('click', function () {
            track.scrollBy({ left: step(), behavior: 'smooth' });
        });

        var ticking = false;
        track.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                update();
                ticking = false;
            });
        }, { passive: true });

        window.addEventListener('resize', update);
        update();
    });
});
