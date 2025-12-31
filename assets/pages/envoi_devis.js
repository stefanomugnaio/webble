import '../styles/envoi_devis.scss';

document.addEventListener("DOMContentLoaded", () => {

    console.log("JS envoi_devis chargé.");

    // ID réel généré par Symfony
    const check = document.getElementById("devis_contrat_maintenance");

    if (!check) {
        console.error("❌ Checkbox introuvable dans le DOM !");
        return;
    }

    const row = document.getElementById("maintenanceRow");
    const maintenanceAmount = document.getElementById("maintenanceAmount");
    const tvaAmount = document.getElementById("tvaAmount");
    const totalTtc = document.getElementById("totalTtc");

    const siteHT = parseFloat(window.montantSiteHT ?? 0);
    const maintenanceHTNormal = parseFloat(window.maintenanceHT ?? 0);
    const offer = (window.offerLabel ?? "").toLowerCase();
    const tauxTVA = parseFloat(window.tauxTVA ?? 0.20);

    // ====== OPTIONS Domaine + Hébergement (IDs Symfony) ======
    const domaineCheck = document.getElementById("devis_domaine");
    const hebergementCheck = document.getElementById("devis_hebergement");
    const domaineRow = document.getElementById("domaineRow");
    const hebergementRow = document.getElementById("hebergementRow");
    const domaineAmount = document.getElementById("domaineAmount");
    const hebergementAmount = document.getElementById("hebergementAmount");

    const domaineHT = 20;
    const hebergementHT = 30;
    // =========================================================

    // ====== MODAL BOOTSTRAP POUR LES WARNINGS ======
    let warningModal = null;
    const warningModalEl = document.getElementById("optionWarningModal");
    const warningModalMessageEl = document.getElementById("optionWarningModalMessage");

    if (warningModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        warningModal = new bootstrap.Modal(warningModalEl);
    }

    function showWarningModal(message) {
        if (warningModal && warningModalMessageEl) {
            // On veut interpréter le HTML (br, etc.)
            warningModalMessageEl.innerHTML = message;
            warningModal.show();
        } else {
            // Fallback au cas où Bootstrap JS n'est pas chargé
            alert(message.replace(/<br\s*\/?>/gi, "\n"));
        }
    }
    // =================================================

    // Offre Sérieuse → 3 mois gratuits = 9 mois payés
    const maintenanceHT =
        offer.includes("sérieuse") || offer.includes("serieuse")
            ? 30 * 9
            : maintenanceHTNormal;

    function formatEuro(value) {
        return value.toFixed(2).replace(".", ",") + " €";
    }

    function updateRecap() {

        const maintenanceChecked = check.checked;
        const domaineChecked = domaineCheck ? domaineCheck.checked : false;
        const hebergementChecked = hebergementCheck ? hebergementCheck.checked : false;

        // Grise / dégrise la ligne maintenance
        row.classList.toggle("gray-row", !maintenanceChecked);

        const maintenanceValue = maintenanceChecked ? maintenanceHT : 0;
        maintenanceAmount.textContent = formatEuro(maintenanceValue);

        // Option domaine
        const domaineValue = domaineChecked ? domaineHT : 0;
        if (domaineRow) {
            domaineRow.classList.toggle("gray-row", !domaineChecked);
        }
        if (domaineAmount) {
            domaineAmount.textContent = formatEuro(domaineValue);
        }

        // Option hébergement
        const hebergementValue = hebergementChecked ? hebergementHT : 0;
        if (hebergementRow) {
            hebergementRow.classList.toggle("gray-row", !hebergementChecked);
        }
        if (hebergementAmount) {
            hebergementAmount.textContent = formatEuro(hebergementValue);
        }

        const totalHT = siteHT + maintenanceValue + domaineValue + hebergementValue;
        const tva = totalHT * tauxTVA;
        const ttc = totalHT + tva;

        tvaAmount.textContent = formatEuro(tva);
        totalTtc.textContent = formatEuro(ttc);
    }

    // Quand on clique sur la maintenance
    check.addEventListener("change", updateRecap);

    // Quand on clique sur "nom de domaine"
    if (domaineCheck) {
        domaineCheck.addEventListener("change", (event) => {
            if (!event.target.checked) {
                showWarningModal(
                    "Le nom de domaine devra être créé par vos soins avant la mise en ligne de votre site. Sans nom de domaine, votre site ne pourra pas être accessible sur Internet.<br><br>En décochant cette option, vous renoncez à l’accompagnement pour le choix, l’achat et la configuration de ce nom de domaine."
                );
            }
            updateRecap();
        });
    }

    // Quand on clique sur "hébergement"
    if (hebergementCheck) {
        hebergementCheck.addEventListener("change", (event) => {
            if (!event.target.checked) {
                showWarningModal(
                    "Sans hébergement actif, votre site ne pourra pas être mis en ligne : vous devrez donc souscrire et activer vous-même un hébergement fonctionnel avant la fin du développement du site.<br><br>En décochant cette option, la configuration de l’hébergement et la mise en ligne ne seront pas incluses dans la prestation."
                );
            }
            updateRecap();
        });
    }

    // Initialisation (avec les cases cochées par défaut)
    updateRecap();
});
