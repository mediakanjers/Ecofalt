// Tijdlijn (mk/tijdlijn) — de wals rijdt mee met de slider (slider.js)
// - positie volgt de voortgang van de tijdlijn: links bij de eerste mijlpaal, rechts bij de laatste
// - de straat schuift langzamer mee (parallax), zodat het voelt alsof de wals rijdt
// - tijdens het rijden trilt de wals en komt er rook uit de uitlaat (CSS: .is-driving)
// - terug in de tijd: de wals draait om en rijdt de andere kant op (CSS: .is-reversed)
// - het kaartje waar de voorste wals bij is, wordt actief (groen)
// - bij in beeld komen rijdt de wals van buiten het scherm naar zijn startplek
// - de wals zelf is te slepen (muis + touch): zo rijd je door de tijd
// - ballonnetje met aanwijzing tot de eerste interactie (1x per bezoek)
document.addEventListener('DOMContentLoaded', function () {
    var MACHINE_WIDTH = 370;     // breedte van tijdlijn-machine.svg op schaal 1
    var OVERHANG = 134;          // ontwerp: wals start 134px links van de content
    var FRONT = 0.78;            // voorste wals op 78% van de breedte
    var PARALLAX = 0.35;         // straat schuift 35% van de tijdlijn-scroll mee
    var TURN_THRESHOLD = 6;      // pas omdraaien na zoveel px de andere kant op (geen gewiebel)
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.mk-tijdlijn').forEach(function (tijdlijn) {
        var road = tijdlijn.querySelector('.mk-tijdlijn__road');
        var machine = tijdlijn.querySelector('.mk-tijdlijn__machine');
        var track = tijdlijn.querySelector('[data-mk-slider-track]');
        var items = tijdlijn.querySelectorAll('.mk-tijdlijn__item');
        if (!road || !machine || !track) return;

        var lastX = null;
        var driveTimer = null;
        var parked = false;   // wacht buiten beeld op de intro
        var reversed = false; // rijdt naar links (terug in de tijd)
        var anchorX = null;   // punt van waaraf een richtingwissel gemeten wordt

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
            // Voorste wals zit rechts, of links als hij omgedraaid is
            var front = road.getBoundingClientRect().left + x + machine.offsetWidth * (reversed ? 1 - FRONT : FRONT);
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

        // Richting volgen: verder dezelfde kant op verschuift het meetpunt mee,
        // pas na TURN_THRESHOLD px de andere kant op draait de wals om
        function updateDirection(x) {
            if (anchorX === null) {
                anchorX = x;
                return;
            }
            if (reversed ? x <= anchorX : x >= anchorX) {
                anchorX = x;
            } else if (Math.abs(x - anchorX) > TURN_THRESHOLD) {
                reversed = !reversed;
                anchorX = x;
                machine.classList.toggle('is-reversed', reversed);
            }
        }

        // fromResize: alleen herpositioneren, niet omdraaien of laten rijden
        function render(fromResize) {
            var r = route();
            var x = r.start + progress() * Math.max(0, r.end - r.start);

            road.style.setProperty('--mk-road-shift', (-track.scrollLeft * PARALLAX) + 'px');

            if (!parked && fromResize !== true) updateDirection(x);
            if (fromResize === true) anchorX = x;
            markActive(x);

            if (parked) return;
            if (fromResize !== true && lastX !== null && Math.abs(x - lastX) > 0.5) setDriving();
            machine.style.setProperty('--mk-machine-x', x + 'px');
            lastX = x;
        }

        var slider = tijdlijn.querySelector('[data-mk-slider]');

        // AANWIJZING — tonen na de intro, weg bij de eerste interactie, 1x per bezoek
        var hintDone = false;
        try {
            hintDone = sessionStorage.getItem('mk-tijdlijn-hint') === '1';
        } catch (e) {}

        function showHint() {
            if (!hintDone) tijdlijn.classList.add('is-hinting');
        }

        function dismissHint() {
            if (hintDone) return;
            hintDone = true;
            tijdlijn.classList.remove('is-hinting');
            try {
                sessionStorage.setItem('mk-tijdlijn-hint', '1');
            } catch (e) {}
        }

        road.addEventListener('pointerdown', dismissHint);
        track.addEventListener('scroll', function () {
            if (track.scrollLeft > 2) dismissHint();
        }, { passive: true });

        // DE WALS SLEPEN — verschuiving van de wals omrekenen naar scroll van de tijdlijn
        var grab = null;

        machine.addEventListener('pointerdown', function (e) {
            if (e.button !== 0 || parked) return;
            var r = route();
            var max = track.scrollWidth - track.clientWidth;
            if (max <= 1 || r.end <= r.start) return;

            grab = { id: e.pointerId, x: e.clientX, start: track.scrollLeft, ratio: max / (r.end - r.start) };
            machine.setPointerCapture(e.pointerId);
            machine.classList.add('is-grabbed');
            slider.classList.add('is-dragging'); // geen snap tijdens het slepen
            e.preventDefault();
        });

        machine.addEventListener('pointermove', function (e) {
            if (!grab || e.pointerId !== grab.id) return;
            track.scrollLeft = grab.start + (e.clientX - grab.x) * grab.ratio;
        });

        function release(e) {
            if (!grab || e.pointerId !== grab.id) return;
            var dx = e.clientX - grab.x;
            grab = null;
            machine.classList.remove('is-grabbed');
            // Eerst snappen (zet native snap tijdelijk uit), dan pas het slepen afronden
            slider.dispatchEvent(new CustomEvent('mk-slider:snap', { detail: { direction: dx > 0 ? 1 : (dx < 0 ? -1 : 0) } }));
            slider.classList.remove('is-dragging');
        }

        machine.addEventListener('pointerup', release);
        machine.addEventListener('pointercancel', release);

        slider.addEventListener('mk-slider:update', render);
        window.addEventListener('resize', function () {
            render(true);
        });

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
                    if (e.target !== machine || e.propertyName !== 'transform') return;
                    machine.removeEventListener('transitionend', done);
                    machine.classList.remove('is-intro', 'is-driving');
                    showHint();
                });
            }, { threshold: 0.4 });

            observer.observe(road);
        } else {
            showHint();
        }

        render();
    });
});
