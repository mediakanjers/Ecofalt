// Slider-component ([data-mk-slider]) — gedeeld door o.a. Galerij en Tijdlijn
// - vorige/volgende in het groene blokje, dat met de scrollpositie meeschuift
// - blokje slepen langs de lijn (muis + touch), klikken op de lijn springt erheen
// - slides met de muis slepen (grab); touch swipet native
// - geen klik (Fancybox, links) direct na het slepen
// - stuurt bij elke wijziging 'mk-slider:update' op het root-element:
//   detail = { progress (0-1), scroll (px), max (px) }
document.addEventListener('DOMContentLoaded', function () {
    var DRAG_THRESHOLD = 5;

    document.querySelectorAll('[data-mk-slider]').forEach(function (root) {
        var track = root.querySelector('[data-mk-slider-track]');
        var nav = root.querySelector('[data-mk-slider-nav]');
        var thumb = root.querySelector('[data-mk-slider-thumb]');
        var prev = root.querySelector('[data-mk-slider-prev]');
        var next = root.querySelector('[data-mk-slider-next]');
        if (!track || !nav || !thumb) return;

        var slides = track.querySelectorAll('[data-mk-slide]');

        function maxScroll() {
            return track.scrollWidth - track.clientWidth;
        }

        // Eén stap = breedte van een slide + tussenruimte
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
            var progress = max > 1 ? Math.min(1, Math.max(0, track.scrollLeft / max)) : 0;

            nav.hidden = max <= 1;
            if (!nav.hidden) {
                thumb.style.transform = 'translateX(' + (progress * travel()) + 'px)';
                prev.disabled = track.scrollLeft <= 1;
                next.disabled = track.scrollLeft >= max - 1;
            }

            root.dispatchEvent(new CustomEvent('mk-slider:update', {
                detail: { progress: progress, scroll: track.scrollLeft, max: Math.max(0, max) }
            }));
        }

        // Na het slepen netjes op een slide uitkomen, met een duwtje in de sleeprichting
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

        // Klik onderdrukken direct na het slepen
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
                state = { id: e.pointerId, x: e.clientX, start: track.scrollLeft, moved: false };
            });

            el.addEventListener('pointermove', function (e) {
                if (!state || e.pointerId !== state.id) return;
                var dx = e.clientX - state.x;

                if (!state.moved) {
                    if (Math.abs(dx) < DRAG_THRESHOLD) return;
                    state.moved = true;
                    el.setPointerCapture(e.pointerId);
                    root.classList.add('is-dragging');
                }

                e.preventDefault();
                options.move(dx, state.start);
            });

            function end(e) {
                if (!state || e.pointerId !== state.id) return;
                if (state.moved) {
                    root.classList.remove('is-dragging');
                    suppressNextClick(el);
                    options.end(e.clientX - state.x);
                }
                state = null;
            }

            el.addEventListener('pointerup', end);
            el.addEventListener('pointercancel', end);
        }

        // Slides slepen met de muis (grab)
        draggable(track, {
            mouseOnly: true,
            move: function (dx, startLeft) { track.scrollLeft = startLeft - dx; },
            end: function (dx) { snapToSlide(dx < 0 ? 1 : -1); }
        });

        // Groene blokje slepen langs de lijn
        draggable(thumb, {
            mouseOnly: false,
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

        // Andere scripts (bijv. tijdlijn.js) kunnen zo een update opvragen
        root.addEventListener('mk-slider:refresh', update);
        update();
    });
});
