// Naar boven — knop rechtsonder, verschijnt na ±1 scherm scrollen.
// (De scrollvoortgang toont het weggetje onder de header.)
document.addEventListener('DOMContentLoaded', function () {
    var knop = document.querySelector('.mk-naar-boven');
    if (!knop) return;

    var ticking = false;

    function update() {
        var y = window.scrollY;
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
