// assets/pages/index.js

import '../styles/index.scss';

document.addEventListener('DOMContentLoaded', () => {
    const heroBg = document.getElementById('hero-bg');
    console.log("Test");    // Config des halos
    const orbs = [
        {
            el: document.querySelector('.hero-orb-1'),
            maxX: 120,
            maxY: 70,
            speed: 0.02,           // vitesse d’interpolation
            changeEvery: 4000,     // changement de cible en ms
        },
        {
            el: document.querySelector('.hero-orb-2'),
            maxX: 150,
            maxY: 90,
            speed: 0.018,
            changeEvery: 5000,
        },
        {
            el: document.querySelector('.hero-orb-3'),
            maxX: 100,
            maxY: 60,
            speed: 0.022,
            changeEvery: 4500,
        }
    ];

    // État interne des halos
    const state = orbs.map(cfg => ({
        ...cfg,
        currentX: 0,
        currentY: 0,
        targetX: 0,
        targetY: 0,
        lastChange: performance.now()
    }));

    const start = performance.now();
    const BG_SCALE = 1.1;      // zoom fixe

    function randomBetween(min, max) {
        return min + Math.random() * (max - min);
    }

    function pickNewTarget(orbState) {
        orbState.targetX = randomBetween(-orbState.maxX, orbState.maxX);
        orbState.targetY = randomBetween(-orbState.maxY, orbState.maxY);
        // on décale un peu l’intervalle pour ne pas tout changer en même temps
        orbState.changeEvery = orbState.changeEvery + randomBetween(-800, 800);
    }

    // Initialisation des cibles
    state.forEach(pickNewTarget);

    function animate(time) {
        const t = time - start;

        // léger mouvement du fond, sans zoom variable
        if (heroBg) {
            const offsetX = Math.sin(t * 0.00015) * 15;   // -15px / +15px
            const offsetY = Math.cos(t * 0.00012) * 15;
            heroBg.style.transform = `scale(${BG_SCALE}) translate3d(${offsetX}px, ${offsetY}px, 0)`;
        }

        // Mise à jour de chaque halo
        state.forEach(orb => {
            if (!orb.el) return;

            // Nouvelle cible si le temps est écoulé
            if (time - orb.lastChange > orb.changeEvery) {
                orb.lastChange = time;
                pickNewTarget(orb);
            }

            // Interpolation vers la cible (lerp)
            orb.currentX += (orb.targetX - orb.currentX) * orb.speed;
            orb.currentY += (orb.targetY - orb.currentY) * orb.speed;

            orb.el.style.transform = `translate3d(${orb.currentX}px, ${orb.currentY}px, 0)`;
        });

        requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);
});
