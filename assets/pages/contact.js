import '../styles/contact.scss';

document.addEventListener('DOMContentLoaded', () => {
    console.log("contact.js chargé");

    /* =====================================================
       reCAPTCHA v3 sur le formulaire de contact
       ===================================================== */

    const form = document.getElementById('contact-form');
    const recaptchaField = document.getElementById('contact_recaptchaToken');

    if (form && recaptchaField) {
        form.addEventListener('submit', (event) => {

            // Si reCAPTCHA n'est pas chargé, on laisse passer (fallback)
            if (typeof grecaptcha === 'undefined') {
                console.warn('reCAPTCHA non disponible, envoi sans vérification.');
                return;
            }

            event.preventDefault(); // on bloque l'envoi le temps de récupérer le token

            // On récupère la clé depuis le data- du formulaire, avec fallback sur ta clé actuelle
            const siteKey =
                form.dataset.recaptchaSiteKey ||
                '6LdPoDosAAAAAJCaixO_Oip1kWWn7sIipFd0Z0Iz';

            grecaptcha.ready(() => {
                grecaptcha.execute(siteKey, { action: 'contact' })
                    .then((token) => {
                        // On met le token dans le champ hidden
                        recaptchaField.value = token;

                        // Puis on envoie réellement le formulaire
                        form.submit();
                    })
                    .catch((error) => {
                        console.error('Erreur reCAPTCHA :', error);
                        // En cas de bug reCAPTCHA : on laisse passer
                        form.submit();
                    });
            });
        });
    }

    /* =====================================================
       AUTO-DISPARITION DES FLASH MESSAGES (5 secondes)
       ===================================================== */

    const flashes = document.querySelectorAll('.flash-message');

    flashes.forEach((flash) => {
        setTimeout(() => {
            // petite transition
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';

            setTimeout(() => {
                flash.remove();
            }, 400);
        }, 5000); // 5000 ms = 5 secondes
    });

    /* =====================================================
       NUMÉRO DE CONTRAT (affichage conditionnel)
       ===================================================== */

    const sujetSelect = document.getElementById('contact_sujet');
    const numContratWrapper = document.getElementById('num-contrat-wrapper');
    const numContratInput = document.getElementById('contact_num_contrat');

    // Les sujets pour lesquels le numéro de contrat est requis
    const sujetsAvecContrat = ['technique', 'specifique', 'support', 'password', 'autre'];

    function toggleNumContrat() {
        if (!sujetSelect || !numContratWrapper || !numContratInput) {
            return;
        }

        const value = sujetSelect.value;
        const doitAfficher = sujetsAvecContrat.includes(value);

        // Affiche/masque le bloc
        numContratWrapper.classList.toggle('d-none', !doitAfficher);

        // Rend le champ requis ou non (HTML5)
        if (doitAfficher) {
            numContratInput.setAttribute('required', 'required');
        } else {
            numContratInput.removeAttribute('required');
        }
    }

    if (sujetSelect) {
        // Au chargement (utile si le formulaire est réaffiché avec des erreurs)
        toggleNumContrat();

        // À chaque changement de sujet
        sujetSelect.addEventListener('change', toggleNumContrat);
    }
});
