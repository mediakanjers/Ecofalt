// Tijdlijn (mk/tijdlijn) — de wals rijdt mee met de slider (slider.js)
// - positie volgt de voortgang van de tijdlijn: links bij de eerste mijlpaal, rechts bij de laatste
// - de straat schuift langzamer mee (parallax), zodat het voelt alsof de wals rijdt
// - tijdens het rijden trilt de wals en komt er rook uit de uitlaat (CSS: .is-driving)
// - het kaartje waar de voorste wals bij is, wordt actief (groen)
// - bij in beeld komen rijdt de wals van buiten het scherm naar zijn startplek
document.addEventListener('DOMContentLoaded', function () {
    var MACHINE_WIDTH = 370;     // breedte van tijdlijn-machine.svg op schaal 1
    var OVERHANG = 134;          // ontwerp: wals start 134px links van de content
    var FRONT = 0.78;            // voorste wals op 78% van de breedte
    var PARALLAX = 0.35;         // straat schuift 35% van de tijdlijn-scroll mee
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.mk-tijdlijn').forEach(function (tijdlijn) {
        var road = tijdlijn.querySelector('.mk-tijdlijn__road');
        var machine = tijdlijn.querySelector('.mk-tijdlijn__machine');
        var track = tijdlijn.querySelector('[data-mk-slider-track]');
        var items = tijdlijn.querySelectorAll('.mk-tijdlijn__item');
        if (!road || !machine || !track) return;

        var lastX = null;
        var driveTimer = null;
        var parked = false; // wacht buiten beeld op de intro

        // Begin en eind van de rit, afgestemd op de content (eerste kaartje)
        function route() {
            var roadRect = road.getBoundingClientRect();
            var trackRect = track.getBoundingClientRect();
            var width = machine.offsetWidth;
            var scale = width / MACHINE_WIDTH;
            var contentLeft = trackRect.left - roadRect.left + parseFloat(getComputedStyle(track).paddingLeft);
            var contentRight = roadRect.width - contentLeft;

            return {
                start: Math.max(8, contentLeft - OVERHANG * scale),
                end: Math.min(roadRect.width - width - 8, contentRight - width + OVERHANG * scale)
            };
        }

        function progress() {
            var max = track.scrollWidth - track.clientWidth;
            return max > 1 ? Math.min(1, Math.max(0, track.scrollLeft / max)) : 0;
        }

        function setDriving() {
            if (reduceMotion) return;
            machine.classList.add('is-driving');
            clearTimeout(driveTimer);
            driveTimer = setTimeout(function () {
                if (!machine.classList.contains('is-intro')) machine.classList.remove('is-driving');
            }, 220);
        }

        // Kaartje het dichtst bij de voorste wals wordt actief
        function markActive(x) {
            if (!items.length) return;
            var front = road.getBoundingClientRect().left + x + machine.offsetWidth * FRONT;
            var best = null;
            var bestDistance = Infinity;

            items.forEach(function (item) {
                var rect = item.getBoundingClientRect();
                var distance = Math.abs(rect.left + rect.width / 2 - front);
                if (distance < bestDistance) {
                    bestDistance = distance;
                    best = item;
                }
            });

            items.forEach(function (item) {
                item.classList.toggle('is-active', item === best);
            });
        }

        function render() {
            var r = route();
            var x = r.start + progress() * Math.max(0, r.end - r.start);

            road.style.setProperty('--mk-road-shift', (-track.scrollLeft * PARALLAX) + 'px');
            markActive(x);

            if (parked) return;
            if (lastX !== null && Math.abs(x - lastX) > 0.5) setDriving();
            machine.style.setProperty('--mk-machine-x', x + 'px');
            lastX = x;
        }

        tijdlijn.querySelector('[data-mk-slider]').addEventListener('mk-slider:update', render);
        window.addEventListener('resize', render);

        // Intro: wals buiten beeld parkeren en laten inrijden zodra de weg in beeld komt
        if (!reduceMotion && 'IntersectionObserver' in window) {
            parked = true;
            machine.style.transition = 'none';
            machine.style.setProperty('--mk-machine-x', -(machine.offsetWidth + 40) + 'px');
            machine.getBoundingClientRect(); // forceer de startpositie
            machine.style.transition = '';

            var observer = new IntersectionObserver(function (entries) {
                if (!entries[0].isIntersecting) return;
                observer.disconnect();

                parked = false;
                machine.classList.add('is-intro', 'is-driving');
                render();

                machine.addEventListener('transitionend', function done(e) {
                    if (e.propertyName !== 'transform') return;
                    machine.removeEventListener('transitionend', done);
                    machine.classList.remove('is-intro', 'is-driving');
                });
            }, { threshold: 0.4 });

            observer.observe(road);
        }

        render();
    });
});
