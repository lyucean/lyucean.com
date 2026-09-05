(function () {
    var cfg = window.lyPostViews;
    if (!cfg || !cfg.url) {
        return;
    }
    if (document.body.classList.contains('logged-in')) {
        return;
    }

    function send() {
        try {
            if (navigator.sendBeacon) {
                navigator.sendBeacon(cfg.url, new Blob(['{}'], { type: 'application/json' }));
                return;
            }
        } catch (e) {}

        fetch(cfg.url, {
            method: 'POST',
            keepalive: true,
            credentials: 'omit',
            headers: { 'Content-Type': 'application/json' },
            body: '{}'
        }).catch(function () {});
    }

    if (document.readyState === 'complete') {
        send();
    } else {
        window.addEventListener('load', send, { once: true });
    }
})();
