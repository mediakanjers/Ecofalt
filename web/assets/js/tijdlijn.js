// Tijdlijn (mk/tijdlijn) — de wals rijdt mee met de slider (slider.js, vrij scrollend)
// - positie volgt de voortgang van de tijdlijn: links bij de eerste mijlpaal, rechts bij de laatste
// - de straat schuift langzamer mee (parallax), zodat het voelt alsof de wals rijdt
// - tijdens het rijden trilt de wals en komt er rook uit de uitlaat (CSS: .is-driving)
// - terug in de tijd: de wals draait om en rijdt de andere kant op (CSS: .is-reversed)
// - het kaartje waar de voorste wals bij is, wordt actief (groen)
// - mijlpalen die de wals nog niet bereikt heeft zijn gedimd en lichten op als hij ze bereikt
// - klik (of Enter) op een mijlpaal: de wals rijdt erheen
// - bij in beeld komen rijdt de wals van buiten het scherm naar zijn startplek
// - de wals zelf is te slepen (muis + touch): zo rijd je door de tijd
// - ballonnetje met aanwijzing na de intro, tot de eerste interactie
document.addEventListener('DOMContentLoaded', function () {
    var MACHINE_WIDTH = 370;     // breedte van tijdlijn-machine.svg op schaal 1
    var OVERHANG = 134;          // ontwerp: wals start 134px links van de content
    var FRONT = 0.78;            // voorste wals op 78% van de breedte (rijrichting rechts)
    var PARALLAX = 0.35;         // straat schuift 35% van de tijdlijn-scroll mee
    var TURN_THRESHOLD = 6;      // pas omdraaien na zoveel px de andere kant op (geen gewiebel)
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.mk-tijdlijn').forEach(function (tijdlijn) {
        var road = tijdlijn.querySelector('.mk-tijdlijn__road');
        var slider = tijdlijn.querySelector('[data-mk-slider]');
        var machine = tijdlijn.querySelector('.mk-tijdlijn__machine');
        var track = tijdlijn.querySelector('[data-mk-slider-track]');
        var items = Array.prototype.slice.call(tijdlijn.querySelectorAll('.mk-tijdlijn__item'));
        if (!road || !slider || !machine || !track) return;

        var lastX = null;
        var driveTimer = null;
        var parked = false;      // wacht buiten beeld op de intro
        var introDone = true;    // mijlpalen pas laten oplichten na de intro
        var reversed = false;    // rijdt naar links (terug in de tijd)
        var anchorX = null;      // punt van waaraf een richtingwissel gemeten wordt
        var reachedIndex = -1;   // hoogste mijlpaal die de wals bereikt heeft

        function maxScroll() {
            return track.scrollWidth - track.clientWidth;
        }

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
            var max = maxScroll();
            return max > 1 ? Math.min(1, Math.max(0, track.scrollLeft / max)) : 0;
        }

        // Positie van de voorste wals in schermcoördinaten
        function frontOnScreen(x) {
            var factor = reversed ? 1 - FRONT : FRONT;
            return road.getBoundingClientRect().left + x + machine.offsetWidth * factor;
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
        function markActive(front) {
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

        // Mijlpalen tot en met de voorste wals zijn bereikt; nieuw bereikte lichten kort op
        function markReached(front, animate) {
            items.forEach(function (item, index) {
                var rect = item.getBoundingClientRect();
                if (index > reachedIndex && rect.left + rect.width / 2 <= front + rect.width / 2) {
                    reachedIndex = index;
                }
            });

            items.forEach(function (item, index) {
                var reached = index <= reachedIndex;
                if (reached && item.classList.contains('is-upcoming') && animate && !reduceMotion) {
                    item.classList.add('is-reached');
                    item.addEventListener('animationend', function done() {
                        item.removeEventListener('animationend', done);
                        item.classList.remove('is-reached');
                    });
                }
                item.classList.toggle('is-upcoming', !reached);
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
            var resize = fromResize === true;
            var r = route();
            var x = r.start + progress() * Math.max(0, r.end - r.start);

            road.style.setProperty('--mk-road-shift', (-track.scrollLeft * PARALLAX) + 'px');

            if (parked) {
                markActive(frontOnScreen(x));
                return;
            }

            if (resize) {
                anchorX = x;
            } else {
                updateDirection(x);
            }

            var front = frontOnScreen(x);
            markActive(front);
            if (introDone) markReached(front, !resize);

            if (!resize && lastX !== null && Math.abs(x - lastX) > 0.5) setDriving();
            machine.style.setProperty('--mk-machine-x', x + 'px');
            lastX = x;
        }

        // KLIK OP EEN MIJLPAAL — tijdlijn zo scrollen dat de voorste wals bij het kaartje staat.
        // Voorste wals (weg-coördinaten) = start + s * k + f * w, kaartje = trackLeft + c0 - s
        // => s = (trackLeft + c0 - start - f * w) / (k + 1)
        function driveTo(item) {
            var max = maxScroll();
            var r = route();
            if (max <= 1 || r.end <= r.start) return;

            var k = (r.end - r.start) / max;
            var trackLeft = track.getBoundingClientRect().left - road.getBoundingClientRect().left;
            var center = item.offsetLeft + item.offsetWidth / 2;
            var width = machine.offsetWidth;

            function solve(factor) {
                return (trackLeft + center - r.start - width * factor) / (k + 1);
            }

            var target = solve(FRONT);
            // Terug in de tijd: de wals draait om, de voorste wals zit dan links
            if (target < track.scrollLeft - 1) target = solve(1 - FRONT);

            track.scrollTo({ left: Math.max(0, Math.min(max, target)), behavior: 'smooth' });
        }

        items.forEach(function (item) {
            item.addEventListener('click', function () {
                driveTo(item);
            });
            item.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    driveTo(item);
                }
            });
        });

        // AANWIJZING — tonen na de intro, weg bij de eerste interactie
        var hintDone = false;

        function showHint() {
            if (!hintDone) tijdlijn.classList.add('is-hinting');
        }

        function dismissHint() {
            if (hintDone) return;
            hintDone = true;
            tijdlijn.classList.remove('is-hinting');
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
            var max = maxScroll();
            if (max <= 1 || r.end <= r.start) return;

            grab = { id: e.pointerId, x: e.clientX, start: track.scrollLeft, ratio: max / (r.end - r.start) };
            machine.setPointerCapture(e.pointerId);
            machine.classList.add('is-grabbed');
            slider.classList.add('is-dragging');
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
            // Eerst snappen (bij een niet-vrije slider), dan pas het slepen afronden
            slider.dispatchEvent(new CustomEvent('mk-slider:snap', { detail: { direction: dx > 0 ? 1 : (dx < 0 ? -1 : 0) } }));
            slider.classList.remove('is-dragging');
        }

        machine.addEventListener('pointerup', release);
        machine.addEventListener('pointercancel', release);

        slider.addEventListener('mk-slider:update', render);
        window.addEventListener('resize', function () {
            render(true);
        });

        // Zonder JS zijn alle mijlpalen gewoon zichtbaar; met JS starten ze gedimd
        items.forEach(function (item) {
            item.classList.add('is-upcoming');
        });

        // INTRO — wals buiten beeld parkeren en laten inrijden zodra de weg in beeld komt
        if (!reduceMotion && 'IntersectionObserver' in window) {
            parked = true;
            introDone = false;
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

                // Einde van de intro; vangnet als transitionend niet komt (bijv. tabblad op de achtergrond)
                var fallback = setTimeout(finishIntro, 2300);
                function finishIntro() {
                    if (introDone) return;
                    clearTimeout(fallback);
                    machine.removeEventListener('transitionend', onEnd);
                    machine.classList.remove('is-intro', 'is-driving');
                    introDone = true;
                    render(); // eerste mijlpaal licht op
                    showHint();
                }
                function onEnd(e) {
                    if (e.target === machine && e.propertyName === 'transform') finishIntro();
                }
                machine.addEventListener('transitionend', onEnd);
            }, { threshold: 0.4 });

            observer.observe(road);
        } else {
            showHint();
        }

        render();
    });
});
