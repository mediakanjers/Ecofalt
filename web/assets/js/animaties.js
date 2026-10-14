// Speelse animaties (CSS: components/_animaties.scss)
// - Ecofalt-haak tekent zich en mengsel-cijfers ploppen in zodra ze in beeld komen (1x)
// - Steentjes in de achtergrond bewegen langzamer mee dan de content (diepte)
// - Hero: video/foto zoomt licht uit en blijft iets achter bij het naar beneden scrollen
// - Foto's in projecten en kolommen bewegen een fractie langzamer dan de pagina
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

    // HERO — media zoomt uit (1.1 -> 1) en zakt weg (25% van de scroll) zolang de hero in beeld is
    var heroStage = document.querySelector('.mk-hero__stage');
    var heroMedia = heroStage && heroStage.querySelector('.mk-hero__media');

    function beweegHero() {
        if (!heroMedia) return;
        var y = window.scrollY;
        var hoogte = heroStage.offsetHeight || 1;
        if (y > hoogte + heroStage.offsetTop) return; // uit beeld: niets te doen
        var p = Math.min(1, y / hoogte);
        heroMedia.style.transform = 'translate3d(0, ' + (y * 0.25).toFixed(1) + 'px, 0) scale(' + (1.1 - 0.1 * p).toFixed(4) + ')';
    }

    // FOTO'S — verschuiving (max ±16px) naar de afstand tot het midden van het scherm.
    // Meten op de ouder (die beweegt zelf niet), alleen foto's die in beeld zijn bijwerken.
    var FOTO_FACTOR = 0.06;
    var FOTO_MAX = 16;
    var fotos = document.querySelectorAll('.mk-project-kaart__afbeelding, .mk-kolommen__afbeelding, .mk-project__foto-img');
    var fotosInBeeld = new Set();

    if ('IntersectionObserver' in window && fotos.length) {
        var fotoObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    fotosInBeeld.add(entry.target);
                } else {
                    fotosInBeeld.delete(entry.target);
                }
            });
            beweegFotos();
        }, { rootMargin: '100px 0px' });

        fotos.forEach(function (foto) {
            fotoObserver.observe(foto.parentElement);
            foto.parentElement._mkFoto = foto;
        });
    }

    function beweegFotos() {
        var midden = window.innerHeight / 2;
        fotosInBeeld.forEach(function (ouder) {
            var rect = ouder.getBoundingClientRect();
            var afstand = rect.top + rect.height / 2 - midden;
            var schuif = Math.max(-FOTO_MAX, Math.min(FOTO_MAX, -afstand * FOTO_FACTOR));
            ouder._mkFoto.style.setProperty('--mk-parallax', schuif.toFixed(1) + 'px');
        });
    }

    var scrollTicking = false;
    function beweegAlles() {
        beweegHero();
        beweegFotos();
        scrollTicking = false;
    }

    window.addEventListener('scroll', function () {
        if (scrollTicking) return;
        scrollTicking = true;
        requestAnimationFrame(beweegAlles);
    }, { passive: true });

    window.addEventListener('resize', beweegAlles);
    beweegAlles();

    // STEENTJES — rustig: 10% van de scrollsnelheid; translate binnen één tegel zodat het naadloos herhaalt
    var lagen = [
        { el: document.querySelector('.mk-steentjes__laag--groot'), snelheid: 0.1, tegel: 900 }
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
