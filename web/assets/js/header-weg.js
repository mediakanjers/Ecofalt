// Weggetje onder de header — de mini-wals rijdt van links naar rechts mee met de
// scrollvoortgang van de pagina, keert om bij terugscrollen en trilt tijdens het rijden.
document.addEventListener('DOMContentLoaded', function () {
    var weg = document.querySelector('.mk-header__weg');
    var wals = weg && weg.querySelector('.mk-header__wals');
    if (!weg || !wals) return;

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var MIN_SCROLL = 400;     // kortere pagina's: geen weg
    var TURN_THRESHOLD = 4;   // pas omkeren na zoveel px de andere kant op

    var lastY = window.scrollY;
    var anchorY = lastY;
    var reversed = false;
    var driveTimer = null;
    var ticking = false;

    function update() {
        var y = window.scrollY;
        var max = document.documentElement.scrollHeight - window.innerHeight;

        weg.hidden = max < MIN_SCROLL;
        if (weg.hidden) {
            ticking = false;
            return;
        }

        var progress = Math.min(1, Math.max(0, y / max));
        var x = progress * (weg.clientWidth - wals.offsetWidth);
        wals.style.setProperty('--mk-wals-x', x + 'px');

        // Richting: verder dezelfde kant op schuift het meetpunt mee, anders pas na de drempel omkeren
        if (reversed ? y <= anchorY : y >= anchorY) {
            anchorY = y;
        } else if (Math.abs(y - anchorY) > TURN_THRESHOLD) {
            reversed = !reversed;
            anchorY = y;
            wals.classList.toggle('is-reversed', reversed);
        }

        // Trillen zolang er gescrold wordt
        if (!reduceMotion && Math.abs(y - lastY) > 0.5) {
            wals.classList.add('is-driving');
            clearTimeout(driveTimer);
            driveTimer = setTimeout(function () {
                wals.classList.remove('is-driving');
            }, 180);
        }

        lastY = y;
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(update);
    }, { passive: true });

    window.addEventListener('resize', update);
    update();
});
