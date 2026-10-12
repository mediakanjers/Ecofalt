// Naar boven — knop rechtsonder, verschijnt na ±1 scherm scrollen.
// De groene ring vult zich met de scrollvoortgang (CSS-variabele --mk-voortgang, 0-100).
document.addEventListener('DOMContentLoaded', function () {
    var knop = document.querySelector('.mk-naar-boven');
    if (!knop) return;

    var ticking = false;

    function update() {
        var max = document.documentElement.scrollHeight - window.innerHeight;
        var y = window.scrollY;
        var voortgang = max > 0 ? Math.min(100, Math.max(0, (y / max) * 100)) : 0;

        knop.style.setProperty('--mk-voortgang', voortgang.toFixed(1));
        knop.classList.toggle('is-zichtbaar', y > window.innerHeight * 0.8);
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    }, { passive: true });

    window.addEventListener('resize', update);

    knop.addEventListener('click', function () {
        // html heeft scroll-behavior: smooth (behalve bij "verminder beweging")
        window.scrollTo({ top: 0 });
    });

    update();
});
