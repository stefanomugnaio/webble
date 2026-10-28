/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.scss';
import './pages/admin_offre.js';

document.addEventListener('DOMContentLoaded', () => {
    initLoader();
    initSmoothReveal();
});

function initLoader() {
    const loader = document.getElementById('site-loader');

    if (!loader) {
        return;
    }

    const isInitialLoad = document.documentElement.classList.contains('site-first-load');
    const minimumDisplayTime = isInitialLoad ? 700 : 120;

    const hideLoader = () => {
        window.setTimeout(() => {
            loader.classList.add('loader-hidden');
            document.body.classList.remove('site-loading');
        }, minimumDisplayTime);
    };

    const showTransitionLoader = () => {
        document.documentElement.classList.remove('site-first-load');
        loader.classList.remove('loader-hidden');
        document.body.classList.add('site-loading');
    };

    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader, { once: true });
    }

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            loader.classList.add('loader-hidden');
            document.body.classList.remove('site-loading');
        }
    });

    document.addEventListener('click', (event) => {
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const link = event.target.closest('a[href]');

        if (!link || link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        const destination = new URL(link.href, window.location.href);
        const isSamePageAnchor =
            destination.origin === window.location.origin &&
            destination.pathname === window.location.pathname &&
            destination.search === window.location.search &&
            destination.hash;

        if (destination.origin !== window.location.origin || isSamePageAnchor) {
            return;
        }

        showTransitionLoader();
    });

    document.addEventListener('submit', () => {
        showTransitionLoader();
    });
}

function initSmoothReveal() {
    const elements = document.querySelectorAll(`
        main h1,
        main h2,
        main h3,
        main p,
        main img,
        main .card,
        main .btn,
        main .border,
        main form,
        main .form-control,
        main .form-select,
        main textarea,
        main input,
        main label,
        main section,
        main .row,
        main .col,
        main [class*="col-"]
    `);

    if (!elements.length) {
        return;
    }

    elements.forEach((element) => {
        element.classList.add('smooth-reveal');
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('smooth-reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -60px 0px'
    });

    elements.forEach((element) => {
        observer.observe(element);
    });

    setTimeout(() => {
        revealVisibleElements(elements);
    }, 250);
}

function revealVisibleElements(elements) {
    let delay = 0;

    elements.forEach((element) => {
        const rect = element.getBoundingClientRect();

        const isVisible =
            rect.top < window.innerHeight &&
            rect.bottom > 0;

        if (isVisible) {
            setTimeout(() => {
                element.classList.add('smooth-reveal-visible');
            }, delay);

            delay += 70;
        }
    });
}
