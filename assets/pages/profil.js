import '../styles/profil.scss';

document.addEventListener('DOMContentLoaded', () => {

    console.log("Entrée dans le JS");

    /* =====================================================
        GESTION NOTIFICATIONS
    ===================================================== */

        const notifCheckbox = document.querySelector("#notification_client_notification_document");
        const notifForm = document.getElementById('form-notifications');


        if (notifCheckbox && notifForm) {
            console.log(notifCheckbox);

            notifCheckbox.addEventListener('change', (event) => {

                console.log('Change déclenché, valeur =', event.target.checked);

                notifForm.requestSubmit
                    ? notifForm.requestSubmit()
                    : notifForm.submit();
            });
        }




    /* =====================================================
       GESTION DU FORMULAIRE PROFIL
       ===================================================== */
    const editBtn = document.getElementById('btn-edit');
    const saveBtn = document.getElementById('btn-save');
    const fields  = document.querySelectorAll('.profil-champ');

    if (editBtn && saveBtn && fields.length > 0) {

        // Sécurité : champs en readonly au chargement
        fields.forEach(field => field.setAttribute('readonly', true));

        // Bouton modifier
        editBtn.addEventListener('click', () => {

            fields.forEach(field => field.removeAttribute('readonly'));

            saveBtn.disabled = false;
            saveBtn.classList.remove('disabled');

            editBtn.disabled = true;
            editBtn.classList.add('disabled');
        });
    }

    /* =====================================================
       AUTO-DISPARITION DES FLASH MESSAGES (3 secondes)
       ===================================================== */
    const flashes = document.querySelectorAll('.flash-message');

    if (flashes.length > 0) {
        setTimeout(() => {
            flashes.forEach(flash => {
                flash.style.transition = 'opacity 0.4s ease';
                flash.style.opacity = '0';

                setTimeout(() => {
                    flash.remove();
                }, 400);
            });
        }, 3000);
    }
});
