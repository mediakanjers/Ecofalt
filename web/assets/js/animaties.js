// Speelse animaties (CSS: components/_animaties.scss)
// - Ecofalt-haak tekent zich en mengsel-cijfers ploppen in zodra ze in beeld komen (1x)
// - Steentjes in de achtergrond bewegen langzamer mee dan de content (diepte)
// Bij "verminder beweging" doet dit niets: alles is dan direct zichtbaar en staat stil.
document.addEventListener('DOMContentLoaded', function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    // IN BEELD — .is-zichtbaar zetten, daarna niet meer volgen
    var doelen = document.querySelectorAll('.mk-bracket, .mk-mengsels__lijst');

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-zichtbaar');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.4 });

        doelen.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        doelen.forEach(function (el) {
            el.classList.add('is-zichtbaar');
        });
    }

    // STEENTJES — per laag een eigen snelheid; translate binnen één tegel zodat het naadloos herhaalt
    var lagen = [
        { el: document.querySelector('.mk-steentjes__laag--groot'), snelheid: 0.18, tegel: 720 },
        { el: document.querySelector('.mk-steentjes__laag--klein'), snelheid: 0.4, tegel: 460 }
    ].filter(function (laag) {
        return laag.el;
    });
    if (!lagen.length) return;

    var ticking = false;
    function beweeg() {
        var y = window.scrollY;
        lagen.forEach(function (laag) {
            laag.el.style.transform = 'translate3d(0, ' + (-(y * laag.snelheid) % laag.tegel) + 'px, 0)';
        });
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(beweeg);
    }, { passive: true });

    beweeg();
});
