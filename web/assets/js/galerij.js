// Galerij slider (mk/galerij) — vorige/volgende en het groene blokje dat met de scrollpositie meeschuift
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mk-galerij').forEach(function (galerij) {
        var track = galerij.querySelector('.mk-galerij__track');
        var nav = galerij.querySelector('.mk-galerij__nav');
        var thumb = galerij.querySelector('.mk-galerij__thumb');
        var prev = galerij.querySelector('[data-mk-galerij-prev]');
        var next = galerij.querySelector('[data-mk-galerij-next]');
        if (!track || !nav || !thumb) return;

        // Eén stap = breedte van een foto + tussenruimte
        function step() {
            var slide = track.querySelector('.mk-galerij__slide');
            var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            return slide ? slide.offsetWidth + gap : track.clientWidth;
        }

        function update() {
            var max = track.scrollWidth - track.clientWidth;
            nav.hidden = max <= 1;
            if (nav.hidden) return;

            // Lijnbreedte = nav zonder de gutter-padding
            var style = getComputedStyle(nav);
            var line = nav.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
            var progress = Math.min(1, Math.max(0, track.scrollLeft / max));

            thumb.style.transform = 'translateX(' + (progress * (line - thumb.offsetWidth)) + 'px)';
            prev.disabled = track.scrollLeft <= 1;
            next.disabled = track.scrollLeft >= max - 1;
        }

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
