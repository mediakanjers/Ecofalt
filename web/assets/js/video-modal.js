// Videopopup — elke knop met data-mk-video="{embed-url}" opent de video in een <dialog>
document.addEventListener('DOMContentLoaded', function () {
    var triggers = document.querySelectorAll('[data-mk-video]');
    if (!triggers.length) return;

    var dialog = document.createElement('dialog');
    dialog.className = 'mk-video-modal';
    dialog.setAttribute('aria-label', 'Video');
    dialog.innerHTML =
        '<div class="mk-video-modal__frame"></div>' +
        '<button type="button" class="mk-video-modal__close"><span class="screen-reader-text">Video sluiten</span></button>';
    document.body.appendChild(dialog);

    var frame = dialog.querySelector('.mk-video-modal__frame');

    // Bij sluiten de iframe weghalen, zodat de video stopt
    dialog.addEventListener('close', function () {
        frame.innerHTML = '';
        document.body.classList.remove('freeze');
    });

    dialog.querySelector('.mk-video-modal__close').addEventListener('click', function () {
        dialog.close();
    });

    // Klik naast de video sluit de popup
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) dialog.close();
    });

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var iframe = document.createElement('iframe');
            iframe.src = trigger.getAttribute('data-mk-video');
            iframe.allow = 'autoplay; fullscreen; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.title = trigger.textContent.trim();
            frame.appendChild(iframe);

            dialog.showModal();
            document.body.classList.add('freeze');
        });
    });
});
