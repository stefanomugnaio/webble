// assets/pages/index.js

import '../styles/index.scss';
import { animate } from 'animejs';   // ✅ API v4

document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#hero-bg');
    if (!container) {
        return;
    }

    const NB_ORBS = 25;

    // Création des bulles lumineuses
    for (let i = 0; i < NB_ORBS; i++) {
        const orb = document.createElement('div');
        orb.classList.add('hero-orb');

        // taille aléatoire
        const size = 80 + Math.random() * 200; // 80 à 280 px
        orb.style.width = `${size}px`;
        orb.style.height = `${size}px`;

        // position de départ aléatoire
        orb.style.left = `${Math.random() * 100}vw`;
        orb.style.top = `${Math.random() * 100}vh`;

        // couleur : dégradé radial avec teinte variable
        const hue = 200 + Math.random() * 80; // bleu / violet / rose
        orb.style.background = `
            radial-gradient(circle at 30% 20%,
                hsla(${hue}, 90%, 70%, 0.9),
                hsla(${hue + 40}, 90%, 60%, 0.0)
            )
        `;

        container.appendChild(orb);
    }

    // Petite fonction utilitaire pour les valeurs aléatoires
    const rand = (min, max) => Math.random() * (max - min) + min;

    // Animation douce des bulles
    animate('.hero-orb', {
        translateX: () => rand(-150, 150),
        translateY: () => rand(-100, 100),
        scale: () => rand(0.8, 1.4),
        opacity: () => rand(0.3, 0.8),
        duration: () => rand(6000, 13000),
        easing: 'easeInOutSine',
        direction: 'alternate',
        loop: true,
        delay: (_, i) => i * 200, // décalage progressif
    });
});
