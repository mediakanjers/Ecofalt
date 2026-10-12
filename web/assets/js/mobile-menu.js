// Mobiel menu — openen/sluiten, Escape, submenu's uitklappen
document.addEventListener('DOMContentLoaded', function () {
    var menu = document.getElementById('mk-mobile-menu');
    if (!menu) return;

    var openButtons = document.querySelectorAll('[aria-controls="mk-mobile-menu"]');
    var closeButton = menu.querySelector('.mk-mobile-menu__close');
    var lastTrigger = null;

    function setOpen(open) {
        menu.classList.toggle('is-active', open);
        menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        menu.inert = !open;
        document.body.classList.toggle('freeze', open);
        openButtons.forEach(function (button) {
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        if (open && closeButton) {
            closeButton.focus();
        } else if (!open && lastTrigger) {
            lastTrigger.focus();
        }
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            lastTrigger = button;
            setOpen(true);
        });
    });

    menu.querySelectorAll('[data-mk-menu-close]').forEach(function (el) {
        el.addEventListener('click', function () {
            setOpen(false);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && menu.classList.contains('is-active')) {
            setOpen(false);
        }
    });

    // Naar desktop-breedte (lg) geschaald: menu dicht
    window.matchMedia('(min-width: 1200px)').addEventListener('change', function (e) {
        if (e.matches && menu.classList.contains('is-active')) {
            setOpen(false);
        }
    });

    // Knop om submenu's uit te klappen
    menu.querySelectorAll('.menu-item-has-children > a').forEach(function (link) {
        var item = link.parentElement;
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'mk-mobile-menu__sub-toggle';
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-label', 'Submenu ' + link.textContent.trim());
        link.after(button);

        button.addEventListener('click', function () {
            var open = item.classList.toggle('is-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
});
