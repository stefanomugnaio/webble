import '../styles/index.scss';

document.addEventListener('DOMContentLoaded', () => {
    /*
     * Éléments visibles directement sur l'accueil.
     * Ceux-là apparaissent un par un au premier affichage.
     */
    const introElements = getUniqueElements([
        document.querySelector('.navbar-brand'),
        document.querySelector('.navbar-toggler'),
        document.querySelector('.menu-toggle'),
        document.querySelector('.menu-burger'),

        document.querySelector('section.margin-top-responsive h1'),
        document.querySelector('section.margin-top-responsive .col-lg-6 p'),
        ...document.querySelectorAll('section.margin-top-responsive .col-lg-6 .btn'),
        document.querySelector('.hero-image'),
        document.querySelector('.home-pill-nav'),
    ]);

    /*
     * Éléments du reste de la page.
     * Ceux-là restent animés au scroll comme avant.
     */
    const scrollElements = getUniqueElements([
        ...document.querySelectorAll('h2'),
        ...document.querySelectorAll('.card'),
        ...document.querySelectorAll('.border.rounded-4.bg-white'),
        ...document.querySelectorAll('#process .col-md-4'),
        ...document.querySelectorAll('#contact-home'),
    ]).filter(element => !introElements.includes(element));

    /*
     * Préparation des éléments d'intro.
     */
    introElements.forEach((element) => {
        element.classList.add('scroll-reveal', 'home-intro-reveal');
        element.style.transitionDelay = '0ms';
    });

    /*
     * Préparation des éléments au scroll.
     * Même logique qu'avant.
     */
    scrollElements.forEach((element, index) => {
        element.classList.add('scroll-reveal');

        // petit décalage pour éviter que tout apparaisse exactement en même temps
        element.style.transitionDelay = `${Math.min(index * 60, 300)}ms`;
    });

    /*
     * Animation au scroll.
     * On ne touche pas au fonctionnement existant.
     */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('scroll-reveal-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -80px 0px'
    });

    scrollElements.forEach(element => {
        observer.observe(element);
    });

    /*
     * Animation d'accueil un par un.
     * On attend que le loader soit parti pour que l'animation soit visible.
     */
    waitForLoaderThen(() => {
        playIntroAnimation(introElements);
    });
});

/*
 * Lance l'animation d'accueil avec un délai entre chaque élément.
 */
function playIntroAnimation(elements) {
    elements.forEach((element, index) => {
        element.style.transitionDelay = `${index * 140}ms`;
        element.classList.add('scroll-reveal-visible');
    });
}

/*
 * Attend la disparition du loader.
 * Si aucun loader n'existe, l'animation démarre directement.
 */
function waitForLoaderThen(callback) {
    const loader = document.getElementById('site-loader');

    if (!loader) {
        setTimeout(callback, 150);
        return;
    }

    let hasStarted = false;

    const startAnimation = () => {
        if (hasStarted) {
            return;
        }

        hasStarted = true;

        setTimeout(() => {
            callback();
        }, 150);
    };

    if (loader.classList.contains('loader-hidden')) {
        startAnimation();
        return;
    }

    const observer = new MutationObserver(() => {
        if (loader.classList.contains('loader-hidden')) {
            observer.disconnect();
            startAnimation();
        }
    });

    observer.observe(loader, {
        attributes: true,
        attributeFilter: ['class']
    });

    /*
     * Sécurité : si pour une raison quelconque le loader ne change pas de classe,
     * on lance quand même l'animation après le chargement complet.
     */
    window.addEventListener('load', () => {
        setTimeout(startAnimation, 1500);
    }, { once: true });
}

/*
 * Supprime les éléments vides et les doublons.
 */
function getUniqueElements(elements) {
    return [...new Set(elements.filter(Boolean))];
}