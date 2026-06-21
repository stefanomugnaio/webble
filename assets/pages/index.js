import '../styles/index.scss';

document.addEventListener('DOMContentLoaded', () => {
    /*
     * Éléments visibles directement sur l'accueil.
     * Ils apparaissent un par un au premier affichage.
     */
    const elementsIntroduction = obtenirElementsUniques([
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
     * Éléments animés lors du défilement de la page.
     */
    const elementsDefilement = obtenirElementsUniques([
        ...document.querySelectorAll('h2'),
        ...document.querySelectorAll('.card'),
        ...document.querySelectorAll('.border.rounded-4.bg-white'),
        ...document.querySelectorAll('#process .col-md-4'),
        ...document.querySelectorAll('#contact-home'),
    ]).filter((element) => !elementsIntroduction.includes(element));

    /*
     * Prépare les éléments de l'introduction.
     */
    elementsIntroduction.forEach((element) => {
        element.classList.add('scroll-reveal', 'home-intro-reveal');
        element.style.transitionDelay = '0ms';
    });

    /*
     * Prépare les éléments animés au défilement.
     */
    elementsDefilement.forEach((element, index) => {
        element.classList.add('scroll-reveal');

        // Limite le décalage entre les éléments.
        element.style.transitionDelay = `${Math.min(index * 60, 300)}ms`;
    });

    /*
     * Rend les éléments visibles dès leur entrée dans l'écran.
     */
    const observateurDefilement = new IntersectionObserver((entrees) => {
        entrees.forEach((entree) => {
            if (!entree.isIntersecting) {
                return;
            }

            entree.target.classList.add('scroll-reveal-visible');
            observateurDefilement.unobserve(entree.target);
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -80px 0px',
    });

    elementsDefilement.forEach((element) => {
        observateurDefilement.observe(element);
    });

    /*
     * Prépare la flèche et son masquage au premier scroll.
     */
    const controleurIndicateur = initialiserIndicateurDefilement();

    /*
     * Lance l'introduction après la disparition du chargeur.
     */
    attendreDisparitionChargeur(() => {
        lancerAnimationIntroduction(elementsIntroduction);

        /*
         * La flèche apparaît après les premiers éléments.
         */
        controleurIndicateur?.afficher();
    });

    /*
     * Initialise le halo de la navigation pilule.
     */
    initialiserHaloNavigation();
});

/*
 * Lance l'animation d'accueil avec un délai progressif.
 */
function lancerAnimationIntroduction(elements) {
    elements.forEach((element, index) => {
        element.style.transitionDelay = `${index * 140}ms`;
        element.classList.add('scroll-reveal-visible');
    });
}

/*
 * Attend la disparition du chargeur avant de continuer.
 */
function attendreDisparitionChargeur(rappel) {
    const chargeur = document.getElementById('site-loader');

    if (!chargeur) {
        window.setTimeout(rappel, 150);
        return;
    }

    let animationLancee = false;

    const lancerAnimation = () => {
        if (animationLancee) {
            return;
        }

        animationLancee = true;

        window.setTimeout(() => {
            rappel();
        }, 150);
    };

    if (chargeur.classList.contains('loader-hidden')) {
        lancerAnimation();
        return;
    }

    const observateurChargeur = new MutationObserver(() => {
        if (!chargeur.classList.contains('loader-hidden')) {
            return;
        }

        observateurChargeur.disconnect();
        lancerAnimation();
    });

    observateurChargeur.observe(chargeur, {
        attributes: true,
        attributeFilter: ['class'],
    });

    /*
     * Sécurité si le chargeur ne se ferme pas correctement.
     */
    window.addEventListener('load', () => {
        window.setTimeout(lancerAnimation, 1500);
    }, {
        once: true,
    });
}

/*
 * Gère l'apparition et la disparition de la flèche de défilement.
 */
function initialiserIndicateurDefilement() {
    const indicateur = document.querySelector('[data-indicateur-defilement]');

    if (!indicateur) {
        return null;
    }

    let indicateurMasque = window.scrollY > 5;
    let minuterieAffichage = null;

    /*
     * Masque définitivement la flèche après le premier scroll.
     */
    const masquer = () => {
        indicateurMasque = true;

        window.clearTimeout(minuterieAffichage);

        indicateur.classList.remove('indicateur-defilement-visible');
        indicateur.classList.add('indicateur-defilement-cache');
    };

    /*
     * Affiche doucement la flèche après l'introduction.
     */
    const afficher = () => {
        if (indicateurMasque || window.scrollY > 5) {
            masquer();
            return;
        }

        minuterieAffichage = window.setTimeout(() => {
            if (indicateurMasque || window.scrollY > 5) {
                return;
            }

            indicateur.classList.add('indicateur-defilement-visible');
        }, 900);
    };

    window.addEventListener('scroll', masquer, {
        passive: true,
        once: true,
    });

    return {
        afficher,
    };
}

/*
 * Anime lentement le halo entre les onglets.
 */
function initialiserHaloNavigation() {
    const navigation = document.querySelector('.home-pill-nav');

    if (!navigation) {
        return;
    }

    const halo = navigation.querySelector('.home-pill-halo');
    const onglets = [...navigation.querySelectorAll('.home-pill-link')];

    if (!halo || onglets.length === 0) {
        return;
    }

    const preferenceMouvementReduit = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    );

    let indexOngletActif = 0;
    let minuterieDefilement = null;
    let identifiantRedimensionnement = null;
    let sourisDansNavigation = false;
    let focusDansNavigation = false;

    /*
     * Vérifie que la navigation est affichée.
     */
    const navigationEstVisible = () => (
        navigation.offsetParent !== null
        && navigation.offsetWidth > 0
        && navigation.offsetHeight > 0
    );

    /*
     * Déplace le halo sous l'onglet demandé.
     */
    const positionnerHalo = (onglet, avecTransition = true) => {
        if (!onglet || !navigationEstVisible()) {
            return;
        }

        if (!avecTransition) {
            halo.classList.add('sans-transition');
        }

        halo.style.width = `${onglet.offsetWidth}px`;
        halo.style.height = `${onglet.offsetHeight}px`;
        halo.style.transform = `translate3d(${onglet.offsetLeft}px, ${onglet.offsetTop}px, 0)`;

        halo.classList.add('home-pill-halo-visible');

        onglets.forEach((element) => {
            element.classList.toggle(
                'onglet-halo-actif',
                element === onglet
            );
        });

        if (!avecTransition) {
            window.requestAnimationFrame(() => {
                halo.classList.remove('sans-transition');
            });
        }
    };

    /*
     * Arrête le passage automatique.
     */
    const arreterDefilementAutomatique = () => {
        if (minuterieDefilement === null) {
            return;
        }

        window.clearInterval(minuterieDefilement);
        minuterieDefilement = null;
    };

    /*
     * Démarre un passage lent entre les onglets.
     */
    const demarrerDefilementAutomatique = () => {
        arreterDefilementAutomatique();

        if (
            preferenceMouvementReduit.matches
            || sourisDansNavigation
            || focusDansNavigation
            || !navigationEstVisible()
        ) {
            return;
        }

        // Le halo change d'onglet toutes les quatre secondes.
        minuterieDefilement = window.setInterval(() => {
            indexOngletActif = (indexOngletActif + 1) % onglets.length;
            positionnerHalo(onglets[indexOngletActif]);
        }, 4000);
    };

    /*
     * Active immédiatement l'onglet sélectionné.
     */
    const activerOnglet = (onglet, index) => {
        indexOngletActif = index;
        positionnerHalo(onglet);
    };

    /*
     * Fige l'animation lorsque la souris entre dans le menu.
     */
    navigation.addEventListener('pointerenter', () => {
        sourisDansNavigation = true;
        arreterDefilementAutomatique();
    });

    /*
     * Reprend l'animation lorsque la souris quitte le menu.
     */
    navigation.addEventListener('pointerleave', () => {
        sourisDansNavigation = false;
        demarrerDefilementAutomatique();
    });

    /*
     * Place le halo sous l'onglet survolé ou sélectionné.
     */
    onglets.forEach((onglet, index) => {
        onglet.addEventListener('pointerenter', () => {
            activerOnglet(onglet, index);
        });

        onglet.addEventListener('focus', () => {
            activerOnglet(onglet, index);
        });

        onglet.addEventListener('click', () => {
            activerOnglet(onglet, index);
        });
    });

    /*
     * Le focus clavier fige également l'animation.
     */
    navigation.addEventListener('focusin', () => {
        focusDansNavigation = true;
        arreterDefilementAutomatique();
    });

    navigation.addEventListener('focusout', (evenement) => {
        if (navigation.contains(evenement.relatedTarget)) {
            return;
        }

        focusDansNavigation = false;
        demarrerDefilementAutomatique();
    });

    /*
     * Recalcule la position lors d'un redimensionnement.
     */
    window.addEventListener('resize', () => {
        window.clearTimeout(identifiantRedimensionnement);

        identifiantRedimensionnement = window.setTimeout(() => {
            positionnerHalo(
                onglets[indexOngletActif],
                false
            );

            demarrerDefilementAutomatique();
        }, 150);
    });

    /*
     * Arrête l'animation lorsque la page est masquée.
     */
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            arreterDefilementAutomatique();
            return;
        }

        demarrerDefilementAutomatique();
    });

    /*
     * Applique les préférences d'accessibilité.
     */
    const gererPreferenceMouvement = () => {
        positionnerHalo(
            onglets[indexOngletActif],
            false
        );

        demarrerDefilementAutomatique();
    };

    if (typeof preferenceMouvementReduit.addEventListener === 'function') {
        preferenceMouvementReduit.addEventListener(
            'change',
            gererPreferenceMouvement
        );
    } else {
        preferenceMouvementReduit.addListener(
            gererPreferenceMouvement
        );
    }

    /*
     * Place le halo sur le premier onglet au chargement.
     */
    window.requestAnimationFrame(() => {
        positionnerHalo(onglets[indexOngletActif], false);
        demarrerDefilementAutomatique();
    });
}

/*
 * Supprime les éléments vides et les doublons.
 */
function obtenirElementsUniques(elements) {
    return [...new Set(elements.filter(Boolean))];
}