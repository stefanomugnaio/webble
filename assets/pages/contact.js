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

            grecaptcha.ready(() => {
                grecaptcha.execute('VOTRE_SITE_KEY_RECAPTCHA', { action: 'contact' })
                    .then((token) => {
                        // On met le token dans le champ hidden
                        recaptchaField.value = token;

                        // Puis on envoie réellement le formulaire
                        form.submit();
                    })
                    .catch((error) => {
                        console.error('Erreur reCAPTCHA :', error);
                        // En cas de bug reCAPTCHA, tu peux choisir :
                        // soit bloquer l'envoi, soit laisser passer.
                        // Ici on laisse passer :
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
});
