/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.scss';

document.addEventListener('DOMContentLoaded', () => {
    initLoader();
    initSmoothReveal();
});

function initLoader() {
    const loader = document.getElementById('site-loader');

    if (!loader) {
        return;
    }

    window.addEventListener('load', () => {
        setTimeout(() => {
            loader.classList.add('loader-hidden');
            document.body.classList.remove('site-loading');

            setTimeout(() => {
                loader.remove();
            }, 700);
        }, 700);
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