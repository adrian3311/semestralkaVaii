// back-to-top.js
// Simple Back-to-Top button. Shows when user scrolls down and smoothly scrolls to top on click.
(function () {
    'use strict';

    function createButton() {
        var btn = document.createElement('button');
        btn.id = 'backToTop';
        btn.title = 'Back to top';
        btn.setAttribute('aria-label', 'Back to top');
        btn.style.position = 'fixed';
        btn.style.right = '18px';
        btn.style.bottom = '24px';
        btn.style.zIndex = '9999';
        btn.style.display = 'none';
        btn.style.alignItems = 'center';
        btn.style.justifyContent = 'center';
        btn.style.width = '44px';
        btn.style.height = '44px';
        btn.style.borderRadius = '50%';
        btn.style.border = 'none';
        btn.style.background = '#fd790d';
        btn.style.color = '#fff';
        btn.style.boxShadow = '0 4px 10px rgba(0,0,0,0.15)';
        btn.style.cursor = 'pointer';
        btn.style.transition = 'opacity 200ms ease, transform 200ms ease';
        btn.style.opacity = '0';
        btn.innerHTML = '↑';

        // accessible focus styles
        btn.addEventListener('focus', function () { btn.style.outline = '3px solid rgba(13,110,253,0.25)'; });
        btn.addEventListener('blur', function () { btn.style.outline = 'none'; });

        document.body.appendChild(btn);
        return btn;
    }

    function show(btn) {
        btn.style.display = 'flex';
        // force reflow to allow transition
        // eslint-disable-next-line no-unused-expressions
        btn.offsetHeight;
        btn.style.opacity = '1';
        btn.style.transform = 'translateY(0)';
    }

    function hide(btn) {
        btn.style.opacity = '0';
        btn.style.transform = 'translateY(8px)';
        // after transition hide from layout
        setTimeout(function () { if (btn.style.opacity === '0') btn.style.display = 'none'; }, 220);
    }

    function init() {
        if (typeof document === 'undefined') return;
        var btn = createButton();
        var threshold = 200; // px scrolled before showing button

        window.addEventListener('scroll', function () {
            try {
                if (window.scrollY > threshold) {
                    show(btn);
                } else {
                    hide(btn);
                }
            } catch (e) {
                // ignore
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            try {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                window.scrollTo(0, 0);
            }
        });

        // keyboard support: press 't' to toggle top (optional)
        document.addEventListener('keydown', function (e) {
            if (e.key === 't' || e.key === 'T') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

