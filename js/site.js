(function (window, document) {
    'use strict';

    var analyticsId = 'G-3DQ871C28K';
    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', analyticsId, { anonymize_ip: true });

    function track(eventName, element) {
        window.gtag('event', eventName, {
            event_category: 'engagement',
            event_label: element.getAttribute('aria-label') || element.textContent.replace(/\s+/g, ' ').trim()
        });
    }

    document.addEventListener('click', function (event) {
        var element = event.target.closest('[data-track]');
        if (element) {
            track(element.getAttribute('data-track'), element);
        }
    });
}(window, document));
